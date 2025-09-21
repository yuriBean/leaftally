<?php

namespace App\Http\Controllers;

use App\Exports\TaxExport;
use App\Models\BillProduct;
use App\Models\InvoiceProduct;
use App\Models\ProposalProduct;
use App\Models\Tax;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class TaxController extends Controller
{
    public function index()
    {
        if (\Auth::user()->can('manage constant tax')) {
            $taxes = Tax::where('created_by', \Auth::user()->creatorId())->get();
            return view('taxes.index')->with('taxes', $taxes);
        }
        return redirect()->back()->with('error', __('Permission denied.'));
    }

    public function create()
    {
        if (\Auth::user()->can('create constant tax')) {
            return view('taxes.create');
        }
        return response()->json(['error' => __('Permission denied.')], 401);
    }

    public function store(Request $request)
    {
        if (!\Auth::user()->can('create constant tax')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $validator = \Validator::make($request->all(), [
            'name' => 'required|max:20',
            'rate' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->getMessageBag()->first());
        }

        $tax             = new Tax();
        $tax->name       = $request->name;
        $tax->rate       = $request->rate;
        $tax->created_by = \Auth::user()->creatorId();
        $tax->save();

        return redirect()->route('taxes.index')->with('success', __('Tax rate successfully created.'));
    }

    public function show(Tax $tax)
    {
        return redirect()->route('taxes.index');
    }

    public function edit(Tax $tax)
    {
        if (\Auth::user()->can('edit constant tax')) {
            if ($tax->created_by == \Auth::user()->creatorId()) {
                return view('taxes.edit', compact('tax'));
            }
            return response()->json(['error' => __('Permission denied.')], 401);
        }
        return response()->json(['error' => __('Permission denied.')], 401);
    }

    public function update(Request $request, Tax $tax)
    {
        if (!\Auth::user()->can('edit constant tax')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        if ($tax->created_by != \Auth::user()->creatorId()) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $validator = \Validator::make($request->all(), [
            'name' => 'required|max:20',
            'rate' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->getMessageBag()->first());
        }

        $tax->name = $request->name;
        $tax->rate = $request->rate;
        $tax->save();

        return redirect()->route('taxes.index')->with('success', __('Tax rate successfully updated.'));
    }

    public function destroy(Tax $tax)
    {
        if (!\Auth::user()->can('delete constant tax')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        if ($tax->created_by != \Auth::user()->creatorId()) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $inProposal = ProposalProduct::whereRaw("find_in_set('$tax->id',tax)")->first();
        $inBill     = BillProduct::whereRaw("find_in_set('$tax->id',tax)")->first();
        $inInvoice  = InvoiceProduct::whereRaw("find_in_set('$tax->id',tax)")->first();

        if (!empty($inProposal) || !empty($inBill) || !empty($inInvoice)) {
            return redirect()->back()->with('error', __('this tax is already assign to proposal or bill or invoice so please move or remove this tax related data.'));
        }

        $tax->delete();
        return redirect()->route('taxes.index')->with('success', __('Tax rate successfully deleted.'));
    }

    /**
     * BULK DELETE
     */
    public function bulkDestroy(Request $request)
    {
        if (!\Auth::user()->can('delete constant tax')) {
            return $request->expectsJson()
                ? response()->json(['message' => __('Permission denied.')], 403)
                : redirect()->back()->with('error', __('Permission denied.'));
        }

        $ids = $request->input('ids', []);
        if (is_string($ids)) {
            $ids = array_filter(array_map('trim', explode(',', $ids)));
        }
        $ids = array_values(array_unique(array_map('intval', (array) $ids)));

        if (empty($ids)) {
            return $request->expectsJson()
                ? response()->json(['message' => __('No records selected.')], 422)
                : redirect()->back()->with('error', __('No records selected.'));
        }

        $deleted = 0;
        $blocked = [];

        $taxes = Tax::where('created_by', \Auth::user()->creatorId())
            ->whereIn('id', $ids)
            ->get();

        foreach ($taxes as $tax) {
            $usedInProposal = ProposalProduct::whereRaw("find_in_set('$tax->id',tax)")->exists();
            $usedInBill     = BillProduct::whereRaw("find_in_set('$tax->id',tax)")->exists();
            $usedInInvoice  = InvoiceProduct::whereRaw("find_in_set('$tax->id',tax)")->exists();

            if ($usedInProposal || $usedInBill || $usedInInvoice) {
                $blocked[] = ['id' => $tax->id, 'name' => $tax->name];
                continue;
            }

            $tax->delete();
            $deleted++;
        }

        $msgParts = [];
        if ($deleted > 0) {
            $msgParts[] = trans_choice(':count tax deleted.|:count taxes deleted.', $deleted, ['count' => $deleted]);
        }
        if (count($blocked) > 0) {
            $msgParts[] = __('Some taxes could not be deleted because they are in use.');
        }
        $message = implode(' ', $msgParts) ?: __('No changes made.');

        if ($request->expectsJson()) {
            return response()->json([
                'message' => $message,
                'deleted' => $deleted,
                'blocked' => $blocked,
            ]);
        }

        return redirect()->back()->with($deleted ? 'success' : 'error', $message);
    }

    /**
     * EXPORT ALL
     */
    public function export()
    {
        if (!\Auth::user()->can('manage constant tax')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $companyName = \Auth::user()->name ?? 'Company';
        $companyName = preg_replace('/[^A-Za-z0-9\-_]/', '_', $companyName);
        $dateString  = date('Y-m-d_H-i-s');
        $filename    = "taxes_{$companyName}_{$dateString}.xlsx";

        return Excel::download(new TaxExport(), $filename);
    }

    /**
     * EXPORT SELECTED
     */
    public function exportSelected(Request $request)
    {
        if (!\Auth::user()->can('manage constant tax')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $ids = $request->input('ids', []);
        if (is_string($ids)) {
            $ids = array_filter(array_map('trim', explode(',', $ids)));
        }
        $ids = array_values(array_unique(array_map('intval', (array) $ids)));

        if (empty($ids)) {
            return redirect()->back()->with('error', __('No records selected.'));
        }

        $companyName = \Auth::user()->name ?? 'Company';
        $companyName = preg_replace('/[^A-Za-z0-9\-_]/', '_', $companyName);
        $dateString  = date('Y-m-d_H-i-s');
        $filename    = "taxes_selected_{$companyName}_{$dateString}.xlsx";

        return Excel::download(new TaxExport($ids), $filename);
    }
}
