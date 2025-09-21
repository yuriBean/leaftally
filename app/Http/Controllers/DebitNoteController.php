<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\DebitNote;
use App\Models\Utility;
use Illuminate\Http\Request;

class DebitNoteController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    protected function findBillWithTrashed($id)
    {
        try {
            return Bill::withTrashed()->find($id);
        } catch (\Throwable $e) {
            return Bill::find($id);
        }
    }

    protected function isTrashed($model): bool
    {
        return $model && method_exists($model, 'trashed') && $model->trashed();
    }

    public function index()
    {
        if (\Auth::user()->can('manage debit note')) {
            // Active bills list for the page
            $bills = Bill::where('created_by', \Auth::user()->creatorId())->get();

            return view('debitNote.index', compact('bills'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function create($bill_id)
    {
        if (!\Auth::user()->can('create debit note')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $bill = $this->findBillWithTrashed($bill_id);
        if (!$bill || $this->isTrashed($bill)) {
            return redirect()->back()->with('error', __('Bill not found or has been archived.'));
        }

        $billDue = $bill;
        return view('debitNote.create', compact('billDue', 'bill_id'));
    }

    public function store(Request $request, $bill_id)
    {
        if (!\Auth::user()->can('create debit note')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $validator = \Validator::make($request->all(), [
            'amount' => 'required|numeric',
            'date'   => 'required',
        ]);
        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->getMessageBag()->first());
        }

        $bill = $this->findBillWithTrashed($bill_id);
        if (!$bill || $this->isTrashed($bill)) {
            return redirect()->back()->with('error', __('Bill not found or has been archived.'));
        }

        if ($request->amount > $bill->getDue()) {
            return redirect()->back()->with('error', 'Maximum ' . \Auth::user()->priceFormat($bill->getDue()) . ' credit limit of this bill.');
        }

        $debit              = new DebitNote();
        $debit->bill        = $bill->id;
        $debit->vendor      = $bill->vender_id;
        $debit->date        = $request->date;
        $debit->amount      = $request->amount;
        $debit->description = $request->description;
        $debit->save();

        Utility::updateUserBalance('vendor', $bill->vender_id, $request->amount, 'credit');

        return redirect()->back()->with('success', __('Debit Note successfully created.'));
    }

    public function edit($bill_id, $debitNote_id)
    {
        if (!\Auth::user()->can('edit debit note')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $debitNote = DebitNote::find($debitNote_id);
        return view('debitNote.edit', compact('debitNote'));
    }

    public function update(Request $request, $bill_id, $debitNote_id)
    {
        if (!\Auth::user()->can('edit debit note')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $validator = \Validator::make($request->all(), [
            'amount' => 'required|numeric',
            'date'   => 'required',
        ]);
        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->getMessageBag()->first());
        }

        $bill = $this->findBillWithTrashed($bill_id);
        if (!$bill) {
            return redirect()->back()->with('error', __('Bill not found.'));
        }

        if ($request->amount > $bill->getDue()) {
            return redirect()->back()->with('error', 'Maximum ' . \Auth::user()->priceFormat($bill->getDue()) . ' credit limit of this bill.');
        }

        $debit = DebitNote::find($debitNote_id);

        // rollback previous effect
        Utility::updateUserBalance('vendor', $bill->vender_id, $debit->amount, 'debit');

        $debit->date        = $request->date;
        $debit->amount      = $request->amount;
        $debit->description = $request->description;
        $debit->save();

        // apply new effect
        Utility::updateUserBalance('vendor', $bill->vender_id, $request->amount, 'credit');

        return redirect()->back()->with('success', __('Debit Note successfully updated.'));
    }

    public function destroy($bill_id, $debitNote_id)
    {
        if (!\Auth::user()->can('delete debit note')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $debitNote = DebitNote::find($debitNote_id);
        if ($debitNote) {
            $debitNote->delete();
            Utility::updateUserBalance('vendor', $debitNote->vendor, $debitNote->amount, 'debit');
        }

        return redirect()->back()->with('success', __('Debit Note successfully deleted.'));
    }

    public function customCreate()
    {
        if (!\Auth::user()->can('create debit note')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        // Only active bills in dropdown
        $bills = Bill::where('created_by', \Auth::user()->creatorId())->get()->pluck('bill_id', 'id');
        return view('debitNote.custom_create', compact('bills'));
    }

    public function customStore(Request $request)
    {
        if (!\Auth::user()->can('create debit note')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $validator = \Validator::make($request->all(), [
            'bill'   => 'required|numeric',
            'amount' => 'required|numeric',
            'date'   => 'required',
        ]);
        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->getMessageBag()->first());
        }

        $bill = $this->findBillWithTrashed($request->bill);
        if (!$bill || $this->isTrashed($bill)) {
            return redirect()->back()->with('error', __('Bill not found or has been archived.'));
        }

        if ($request->amount > $bill->getDue()) {
            return redirect()->back()->with('error', 'Maximum ' . \Auth::user()->priceFormat($bill->getDue()) . ' credit limit of this bill.');
        }

        $debit              = new DebitNote();
        $debit->bill        = $bill->id;
        $debit->vendor      = $bill->vender_id;
        $debit->date        = $request->date;
        $debit->amount      = $request->amount;
        $debit->description = $request->description;
        $debit->save();

        Utility::updateUserBalance('vendor', $bill->vender_id, $request->amount, 'credit');

        return redirect()->back()->with('success', __('Debit Note successfully created.'));
    }

    public function getbill(Request $request)
    {
        $bill = $this->findBillWithTrashed($request->bill_id);
        echo json_encode($bill ? $bill->getDue() : 0);
    }
}
