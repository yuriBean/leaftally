<?php

namespace App\Http\Controllers;

use App\Models\CreditNote;
use App\Models\Invoice;
use App\Models\Utility;
use Illuminate\Http\Request;

class CreditNoteController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /** Safely fetch an invoice including soft-deleted ones */
    protected function findInvoiceWithTrashed($id)
    {
        try {
            return Invoice::withTrashed()->find($id);
        } catch (\Throwable $e) {
            return Invoice::find($id);
        }
    }

    /** True if the model is soft-deleted */
    protected function isTrashed($model): bool
    {
        return $model && method_exists($model, 'trashed') && $model->trashed();
    }

    public function index()
    {
        if (\Auth::user()->can('manage credit note')) {
            // Show active invoices list (standard behavior)
            $invoices = Invoice::where('created_by', \Auth::user()->creatorId())->get();

            return view('creditNote.index', compact('invoices'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function create($invoice_id)
    {
        if (!\Auth::user()->can('create credit note')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $invoice = $this->findInvoiceWithTrashed($invoice_id);
        if (!$invoice || $this->isTrashed($invoice)) {
            return redirect()->back()->with('error', __('Invoice not found or has been archived.'));
        }

        $invoiceDue = $invoice;
        return view('creditNote.create', compact('invoiceDue', 'invoice_id'));
    }

    public function store(Request $request, $invoice_id)
    {
        if (!\Auth::user()->can('create credit note')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $validator = \Validator::make($request->all(), [
            'amount' => 'required|numeric',
            'date'   => 'required',
        ]);
        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->getMessageBag()->first());
        }

        $invoice = $this->findInvoiceWithTrashed($invoice_id);
        if (!$invoice || $this->isTrashed($invoice)) {
            return redirect()->back()->with('error', __('Invoice not found or has been archived.'));
        }

        $credit              = new CreditNote();
        $credit->invoice     = $invoice_id;
        $credit->customer    = $invoice->customer_id;
        $credit->date        = $request->date;
        $credit->amount      = $request->amount;
        $credit->description = $request->description;
        $credit->save();

        Utility::updateUserBalance('customer', $invoice->customer_id, $request->amount, 'debit');

        return redirect()->back()->with('success', __('Credit Note successfully created.'));
    }

    public function edit($invoice_id, $creditNote_id)
    {
        if (!\Auth::user()->can('edit credit note')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $creditNote = CreditNote::find($creditNote_id);
        return view('creditNote.edit', compact('creditNote'));
    }

    public function update(Request $request, $invoice_id, $creditNote_id)
    {
        if (!\Auth::user()->can('edit credit note')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $validator = \Validator::make($request->all(), [
            'amount' => 'required|numeric',
            'date'   => 'required',
        ]);
        if ($validator->fails()) {
            return redirect()->route('creditnote.index')->with('error', $validator->getMessageBag()->first());
        }

        $invoice = $this->findInvoiceWithTrashed($invoice_id);
        if (!$invoice) {
            return redirect()->back()->with('error', __('Invoice not found.'));
        }

        $credit = CreditNote::find($creditNote_id);
        // rollback previous effect
        Utility::updateUserBalance('customer', $invoice->customer_id, $credit->amount, 'debit');

        $credit->date        = $request->date;
        $credit->amount      = $request->amount;
        $credit->description = $request->description;
        $credit->save();

        // apply new effect
        Utility::updateUserBalance('customer', $invoice->customer_id, $request->amount, 'credit');

        return redirect()->back()->with('success', __('Credit Note successfully updated.'));
    }

    public function destroy($invoice_id, $creditNote_id)
    {
        if (!\Auth::user()->can('delete credit note')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $creditNote = CreditNote::find($creditNote_id);
        if ($creditNote) {
            $creditNote->delete();
            Utility::updateUserBalance('customer', $creditNote->customer, $creditNote->amount, 'credit');
        }

        return redirect()->back()->with('success', __('Credit Note successfully deleted.'));
    }

    public function customCreate()
    {
        if (!\Auth::user()->can('create credit note')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        // Only active invoices in the dropdown
        $invoices = Invoice::where('created_by', \Auth::user()->creatorId())->get()->pluck('invoice_id', 'id');
        return view('creditNote.custom_create', compact('invoices'));
    }

    public function customStore(Request $request)
    {
        if (!\Auth::user()->can('create credit note')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $validator = \Validator::make($request->all(), [
            'invoice' => 'required|numeric',
            'amount'  => 'required|numeric',
            'date'    => 'required',
        ]);
        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->getMessageBag()->first());
        }

        $invoice = $this->findInvoiceWithTrashed($request->invoice);
        if (!$invoice || $this->isTrashed($invoice)) {
            return redirect()->back()->with('error', __('Invoice not found or has been archived.'));
        }

        $credit              = new CreditNote();
        $credit->invoice     = $invoice->id;
        $credit->customer    = $invoice->customer_id;
        $credit->date        = $request->date;
        $credit->amount      = $request->amount;
        $credit->description = $request->description;
        $credit->save();

        Utility::updateUserBalance('customer', $invoice->customer_id, $request->amount, 'debit');

        return redirect()->back()->with('success', __('Credit Note successfully created.'));
    }

    public function getinvoice(Request $request)
    {
        $invoiceId = $request->id;
        $invoice   = $this->findInvoiceWithTrashed($invoiceId);

        echo json_encode($invoice ? $invoice->getDue() : 0);
    }
}
