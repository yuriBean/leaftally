<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\ContractType;
use App\Models\ContractAttachment;
use App\Models\ContractComment;
use App\Models\ContractNote;
use App\Models\User;
use App\Models\UserDefualtView;
use App\Models\Utility;
use App\Models\Customer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Http\Request;
use App\Exports\ContractExport;
use Maatwebsite\Excel\Facades\Excel;

class ContractController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if ((\Auth::user()->can('manage contract')) || (\Auth::user()->can('manage customer contract'))) {
            if (\Auth::user()->type == 'company') {
                $contracts   = Contract::where('created_by', '=', \Auth::user()->creatorId())->get();
                $curr_month  = Contract::where('created_by', '=', \Auth::user()->creatorId())->whereMonth('start_date', '=', date('m'))->get();
                $curr_week   = Contract::where('created_by', '=', \Auth::user()->creatorId())->whereBetween('start_date', [\Carbon\Carbon::now()->startOfWeek(), \Carbon\Carbon::now()->endOfWeek()])->get();
                $last_30days = Contract::where('created_by', '=', \Auth::user()->creatorId())->whereDate('start_date', '>', \Carbon\Carbon::now()->subDays(30))->get();
            } else {
                $contracts   = Contract::where('customer', '=', \Auth::user()->id)->get();
                $curr_month  = Contract::where('customer', '=', \Auth::user()->id)->whereMonth('start_date', '=', date('m'))->get();
                $curr_week   = Contract::where('customer', '=', \Auth::user()->id)->whereBetween('start_date', [\Carbon\Carbon::now()->startOfWeek(), \Carbon\Carbon::now()->endOfWeek()])->get();
                $last_30days = Contract::where('customer', '=', \Auth::user()->id)->whereDate('start_date', '>', \Carbon\Carbon::now()->subDays(30))->get();
            }

            $cnt_contract                = [];
            $cnt_contract['total']       = \App\Models\Contract::getContractSummary($contracts);
            $cnt_contract['this_month']  = \App\Models\Contract::getContractSummary($curr_month);
            $cnt_contract['this_week']   = \App\Models\Contract::getContractSummary($curr_week);
            $cnt_contract['last_30days'] = \App\Models\Contract::getContractSummary($last_30days);

            return view('contract.index', compact('contracts', 'cnt_contract'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $contractTypes = ContractType::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('name', 'id');
        $contractTypes->prepend('Select Contract Types', '');
        $customers = Customer::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
        $customers->prepend('Select Customer', '');

        return view('contract.create', compact('contractTypes', 'customers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (\Auth::user()->can('create contract')) {
            $rules = [
                'customer'   => 'required',
                'subject'    => 'required',
                'type'       => 'required',
                'value'      => 'required',
                'start_date' => 'required',
                'end_date'   => 'required',
                'edit_status'=> 'Pending',
            ];

            $validator = \Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return redirect()->route('contract.index')->with('error', $validator->getMessageBag()->first());
            }

            $contract              = new Contract();
            $contract->customer    = $request->customer;
            $contract->subject     = $request->subject;
            $contract->type        = $request->type;
            $contract->value       = $request->value;
            $contract->start_date  = $request->start_date;
            $contract->end_date    = $request->end_date;
            $contract->description = $request->description;
            $contract->created_by  = \Auth::user()->creatorId();
            $contract->save();

            return redirect()->route('contract.index')->with('success', __('Contract successfully created.'));
        }

        return redirect()->back()->with('error', __('Permission denied.'));
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $contract = Contract::find($id);
        if (\Auth::user()->can('show contract')) {
            return view('contract.view', compact('contract'));
        }

        return redirect()->back()->with('error', 'permission Denied');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Contract $contract)
    {
        $contractTypes = ContractType::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('name', 'id');
        $customers     = Customer::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');

        return view('contract.edit', compact('contractTypes', 'customers', 'contract'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Contract $contract)
    {
        if (\Auth::user()->can('edit contract')) {
            $rules = [
                'customer'   => 'required',
                'subject'    => 'required',
                'type'       => 'required',
                'value'      => 'required',
                'start_date' => 'required',
                'end_date'   => 'required',
            ];

            $validator = \Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return redirect()->route('contract.index')->with('error', $validator->getMessageBag()->first());
            }

            $contract->customer    = $request->customer;
            $contract->subject     = $request->subject;
            $contract->type        = $request->type;
            $contract->value       = $request->value;
            $contract->start_date  = $request->start_date;
            $contract->end_date    = $request->end_date;
            $contract->description = $request->description;
            $contract->save();

            return redirect()->route('contract.index')->with('success', __('Contract successfully updated.'));
        }

        return redirect()->back()->with('error', __('Permission denied.'));
    }

    /**
     * Remove a single resource from storage.
     */
    public function destroy($id)
    {
        if (!\Auth::user()->can('delete contract')) {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }

        $contract = Contract::find($id);
        if (!$contract) {
            return redirect()->back()->with('error', __('Contract not found.'));
        }

        $attachments = $contract->ContractAttachment()->get();
        foreach ($attachments as $attachment) {
            if (\Storage::exists('contract_attachment/' . $attachment->files)) {
                @unlink(storage_path('contract_attachment/' . $attachment->files));
            }
            $attachment->delete();
        }

        $contract->ContractComment()->get()->each->delete();
        $contract->ContractNote()->get()->each->delete();
        $contract->delete();

        return redirect()->route('contract.index')->with('success', __('Contract successfully deleted!'));
    }

    /**
     * Duplicate form.
     */
    public function duplicate(Contract $contract, $id)
    {
        $contract      = Contract::find($id);
        $contractTypes = ContractType::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('name', 'id');
        $customers     = Customer::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');

        return view('contract.duplicate', compact('contractTypes', 'customers', 'contract'));
    }

    /**
     * Duplicate submit.
     */
    public function duplicatecontract(Request $request)
    {
        if (!\Auth::user()->can('create contract')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $rules = [
            'customer'   => 'required',
            'subject'    => 'required',
            'type'       => 'required',
            'value'      => 'required',
            'start_date' => 'required',
            'end_date'   => 'required',
        ];

        $validator = \Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->route('contract.index')->with('error', $validator->getMessageBag()->first());
        }

        $contract              = new Contract();
        $contract->customer    = $request->customer;
        $contract->subject     = $request->subject;
        $contract->type        = $request->type;
        $contract->value       = $request->value;
        $contract->start_date  = $request->start_date;
        $contract->end_date    = $request->end_date;
        $contract->description = $request->description;
        $contract->created_by  = \Auth::user()->creatorId();
        $contract->save();

        return redirect()->route('contract.index')->with('success', __('Duplicate Contract successfully created.'));
    }

    public function descriptionStore($id, Request $request)
    {
        if (!\Auth::user()->can('contract description')) {
            return redirect()->back()->with('error', __('Permission denied'));
        }

        $contract        = Contract::find($id);
        $contract->notes = $request->notes;
        $contract->save();

        return redirect()->back()->with('success', __('Contract Description successfully saved.'));
    }

    public function fileUpload($id, Request $request)
    {
        if (\Auth::guard('customer')->check()) {
            $user_type = 'customer';
        } else {
            $user_type = 'company';
        }

        $contract = Contract::find($id);
        if (!(\Auth::user()->can('upload attachment'))) {
            return response()->json(['is_success' => false, 'error' => __('Permission Denied.')], 401);
        }

        $request->validate(['file' => 'required']);

        $image_size = $request->file('file')->getSize();
        $result     = Utility::updateStorageLimit(\Auth::user()->creatorId(), $image_size);

        if ($result == 1) {
            $dir   = 'contract_attachment/';
            $files = $request->file->getClientOriginalName();
            $path  = Utility::upload_file($request, 'file', $files, $dir, []);

            if ($path['flag'] == 1) {
                $file = $path['url'];
            } else {
                return redirect()->back()->with('error', __($path['msg']));
            }
        } else {
            return redirect()->back()->with('error', $result);
        }

        $file = ContractAttachment::create([
            'contract_id' => $request->contract_id,
            'created_by'  => \Auth::user()->id,
            'files'       => $files,
            'type'        => $user_type,
        ]);

        $return               = [];
        $return['is_success'] = true;
        $return['download']   = route('contract.file.download', [$contract->id, $file->id]);
        $return['delete']     = route('contract.file.delete',   [$contract->id, $file->id]);

        return response()->json($return);
    }

    public function fileDownload($id, $file_id)
    {
        if (\Auth::user()->type != 'company') {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }

        $contract = Contract::find($id);
        if ($contract->created_by != \Auth::user()->creatorId()) {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }

        $file = ContractAttachment::find($file_id);
        if (!$file) {
            return redirect()->back()->with('error', __('File is not exist.'));
        }

        $file_path = storage_path('contract_attachment/' . $file->files);
        return \Response::download($file_path, $file->files, ['Content-Length: ' . @filesize($file_path)]);
    }

    public function fileDelete($id, $file_id)
    {
        $contract = Contract::find($id);
        $file     = ContractAttachment::find($file_id);
        if (!$file) {
            return response()->json(['is_success' => false, 'error' => __('File is not exist.')], 200);
        }

        $file_path = 'contract_attachment/' . $file->files;
        $result    = Utility::changeStorageLimit(\Auth::user()->creatorId(), $file_path);

        $path = storage_path('contract_attachment/' . $file->files);
        if (file_exists($path)) {
            \File::delete($path);
        }
        $file->delete();

        return redirect()->back()->with('success', __('Attachment successfully deleted!'));
    }

    public function commentStore(Request $request, $id)
    {
        if (\Auth::guard('customer')->check()) {
            $user_type = 'customer';
        } else {
            $user_type = 'company';
        }

        $contract = Contract::find($id);
        if ($contract->edit_status != 'accept') {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }

        $comment              = new ContractComment();
        $comment->comment     = $request->comment;
        $comment->contract_id = $id;
        $comment->created_by  = \Auth::user()->id;
        $comment->type        = $user_type;
        $comment->save();

        return redirect()->back()->with('success', __('Comment Added Successfully!'));
    }

    public function commentDestroy($id)
    {
        $contract = ContractComment::find($id);
        if (!((\Auth::user()->can('delete comment')) || (\Auth::user()->type != 'company'))) {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
        $contract->delete();
        return redirect()->back()->with('success', __('Comment successfully deleted!'));
    }

    public function noteStore($id, Request $request)
    {
        $rules = ['note' => 'required'];
        $validator = \Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->route('contract.index')->with('error', $validator->getMessageBag()->first());
        }

        if (\Auth::guard('customer')->check()) {
            $user_type = 'customer';
        } else {
            $user_type = 'company';
        }

        $note               = new ContractNote();
        $note->contract_id  = $id;
        $note->note         = $request->note;
        $note->created_by   = \Auth::user()->id;
        $note->type         = $user_type;
        $note->save();

        return redirect()->back()->with('success', __('Note successfully saved.'));
    }

    public function noteDestroy($id)
    {
        $contract = ContractNote::find($id);
        if (!((\Auth::user()->can('delete notes')) || (\Auth::user()->type != 'company'))) {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
        $contract->delete();
        return redirect()->back()->with('success', __('Notes successfully deleted!'));
    }

    public function pdffromcontract($contract_id)
    {
        $id       = Crypt::decrypt($contract_id);
        $contract = Contract::findOrFail($id);
        $settings = Utility::settings();

        if (\Auth::check()) {
            $usr = \Auth::user();
        } else {
            $usr = Customer::where('id', $contract->created_by)->first();
        }

        if ($contract) {
            $color      = '#' . Utility::getInvoiceColor($settings);
            $font_color = Utility::getFontColor($color);
            return view('contract.templates.' . $settings['contract_template'], compact('contract', 'color', 'settings', 'font_color', 'usr'));
        }

        return redirect()->back()->with('error', __('Permission Denied.'));
    }

    public function printContract($id)
    {
        $contract = Contract::findOrFail($id);
        $settings = Utility::settings();

        return view('contract.contract_view', compact('contract', 'settings'));
    }

    public function signature($id)
    {
        $contract = Contract::find($id);
        return view('contract.signature', compact('contract'));
    }

    public function signatureStore(Request $request)
    {
        $contract = Contract::find($request->contract_id);

        if (\Auth::user()->type == 'company') {
            $contract->company_signature = $request->company_signature;
        } else {
            $contract->customer_signature = $request->customer_signature;
        }

        $contract->save();

        return response()->json(['Success' => true, 'message' => __('Contract Signed successfully')], 200);
    }

    public function sendmailContract($id, Request $request)
    {
        $contract  = Contract::find($id);

        $uArr = [
            'email'                => $contract->clients->email,
            'contract_subject'     => $contract->subject,
            'contract_customer'    => $contract->clients->name,
            'contract_type'        => $contract->types->name,
            'contract_value'       => $contract->value,
            'contract_start_date'  => $contract->start_date,
            'contract_end_date'    => $contract->end_date,
        ];

        $resp = Utility::sendEmailTemplate('new_contract', [$contract->clients->customer_id => $contract->clients->email], $uArr);

        return redirect()->route('contract.show', $contract->id)
            ->with('success', __('Email Send successfully!') . (($resp['is_success'] == false && !empty($resp['error'])) ? '<br> <span class="text-danger">' . $resp['error'] . '</span>' : ''));
    }

    public function contract_status_edit(Request $request, $id)
    {
        $contract = Contract::find($id);
        $contract->edit_status = $request->edit_status;
        $contract->save();
    }

    /**
     * -----------------------------
     * BULK / EXPORT Actions
     * -----------------------------
     */

    public function bulkDestroy(Request $request)
    {
        if (!\Auth::user()->can('delete contract')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $ids = $request->input('ids', []);
        if (!is_array($ids) || count($ids) === 0) {
            return redirect()->back()->with('error', __('No items selected.'));
        }

        $user = \Auth::user();
        $query = Contract::whereIn('id', $ids);
        $query = ($user->type === 'company')
            ? $query->where('created_by', $user->creatorId())
            : $query->where('customer', $user->id);

        $contracts = $query->get();
        $deleted   = 0;

        foreach ($contracts as $contract) {
            // Delete attachments
            $attachments = $contract->ContractAttachment()->get();
            foreach ($attachments as $attachment) {
                if (\Storage::exists('contract_attachment/' . $attachment->files)) {
                    @unlink(storage_path('contract_attachment/' . $attachment->files));
                }
                $attachment->delete();
            }

            // Delete comments and notes
            $contract->ContractComment()->get()->each->delete();
            $contract->ContractNote()->get()->each->delete();

            // Delete contract
            $contract->delete();
            $deleted++;
        }

        return redirect()->back()->with('success', __(':count contract(s) deleted.', ['count' => $deleted]));
    }

    public function export()
    {
        if (!(\Auth::user()->can('manage contract') || \Auth::user()->can('manage customer contract'))) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $company  = preg_replace('/[^A-Za-z0-9\-_]/', '_', (\Auth::user()->name ?? 'User'));
        $filename = "contracts_{$company}_" . date('Y-m-d_H-i-s') . ".xlsx";

        return Excel::download(new ContractExport(), $filename);
    }

    public function exportSelected(Request $request)
    {
        if (!(\Auth::user()->can('manage contract') || \Auth::user()->can('manage customer contract'))) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $ids = $request->input('ids', []);
        if (!is_array($ids) || count($ids) === 0) {
            return redirect()->back()->with('error', __('No items selected.'));
        }

        $company  = preg_replace('/[^A-Za-z0-9\-_]/', '_', (\Auth::user()->name ?? 'User'));
        $filename = "contracts_selected_{$company}_" . date('Y-m-d_H-i-s') . ".xlsx";

        return Excel::download(new ContractExport($ids), $filename);
    }
}
