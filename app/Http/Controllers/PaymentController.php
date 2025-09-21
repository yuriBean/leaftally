<?php

namespace App\Http\Controllers;

use App\Exports\PaymentExport;
use App\Models\BankAccount;
use App\Models\BillAccount;
use App\Models\BillPayment;
use App\Models\Payment;
use App\Models\ProductServiceCategory;
use App\Models\Transaction;
use App\Models\TransactionLines;
use App\Models\Utility;
use App\Models\Vender;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Support\TrashedSelect;
use App\Exports\PaymentSelectedExport; 

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        if (!\Auth::user()->can('manage payment')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        // Filters: only active (non-trashed) options
        $vender   = TrashedSelect::activeOptions(Vender::class, \Auth::user()->creatorId())->prepend('Select Vender', '');
        $account  = TrashedSelect::activeOptions(BankAccount::class, \Auth::user()->creatorId(), 'holder_name')->prepend('Select Account', '');
        $category = ProductServiceCategory::where('created_by', \Auth::user()->creatorId())
            ->where('type', 'expense')->get()->pluck('name', 'id')->prepend('Select Category', '');

        $query = Payment::where('created_by', \Auth::user()->creatorId());

        if (str_contains((string)$request->date, ' to ')) {
            $date_range = explode(' to ', $request->date);
            $query->whereBetween('date', $date_range);
        } elseif (!empty($request->date)) {
            $query->where('date', $request->date);
        }

        if (!empty($request->vender)) {
            $query->where('vender_id', $request->vender);
        }
        if (!empty($request->account)) {
            $query->where('account_id', $request->account);
        }
        if (!empty($request->category)) {
            $query->where('category_id', $request->category);
        }

        $payments = $query->get();

        return view('payment.index', compact('payments', 'account', 'category', 'vender'));
    }

    public function create()
    {
        if (!\Auth::user()->can('create payment')) {
            return response()->json(['error' => __('Permission denied.')], 401);
        }

        $venders    = TrashedSelect::activeOptions(Vender::class, \Auth::user()->creatorId())->prepend('--', 0);
        $categories = ProductServiceCategory::where('created_by', \Auth::user()->creatorId())
            ->whereNotIn('type', ['product & service', 'income'])->get()->pluck('name', 'id');

        // No closure: use holder_name as the label
        $accounts = TrashedSelect::activeOptions(BankAccount::class, \Auth::user()->creatorId(), 'holder_name');

        return view('payment.create', compact('venders', 'categories', 'accounts'));
    }

    public function store(Request $request)
    {
        if (!\Auth::user()->can('create payment')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $validator = \Validator::make(
            $request->all(),
            [
                'date'        => 'required',
                'amount'      => 'required',
                'account_id'  => 'required',
                'category_id' => 'required',
            ]
        );
        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->getMessageBag()->first());
        }

        $payment                 = new Payment();
        $payment->date           = $request->date;
        $payment->amount         = $request->amount;
        $payment->account_id     = $request->account_id;
        $payment->vender_id      = $request->vender_id;
        $payment->category_id    = $request->category_id;
        $payment->payment_method = 0;
        $payment->reference      = $request->reference;
        $payment->description    = $request->description;

        if (!empty($request->add_receipt)) {
            $image_size = $request->file('add_receipt')->getSize();
            $result     = Utility::updateStorageLimit(\Auth::user()->creatorId(), $image_size);

            if ($result == 1) {
                $fileName               = time() . "_" . $request->add_receipt->getClientOriginalName();
                $payment->add_receipt   = $fileName;
                $dir                    = 'uploads/payment';
                $path                   = Utility::upload_file($request, 'add_receipt', $fileName, $dir, []);
                if ($path['flag'] == 0) {
                    return redirect()->back()->with('error', __($path['msg']));
                }
            } else {
                return redirect()->back()->with('error', $result);
            }
        }

        $payment->created_by = \Auth::user()->creatorId();
        $payment->save();

        // Transaction line (works even if bank account later gets soft-deleted)
        $bank = TrashedSelect::findWithTrashed(BankAccount::class, $payment->account_id);
        if ($bank) {
            $data = [
                'account_id'         => $bank->chart_account_id,
                'transaction_type'   => 'Debit',
                'transaction_amount' => $payment->amount,
                'reference'          => 'Payment',
                'reference_id'       => $payment->id,
                'reference_sub_id'   => 0,
                'date'               => $payment->date,
            ];
            Utility::addTransactionLines($data);

            // BillAccount row
            $ba                    = new BillAccount();
            $ba->chart_account_id  = $bank->chart_account_id;
            $ba->price             = $request->amount;
            $ba->description       = $request->description;
            $ba->type              = 'Payment';
            $ba->ref_id            = $payment->id;
            $ba->save();
        }

        $category            = ProductServiceCategory::find($request->category_id);
        $payment->payment_id = $payment->id;
        $payment->type       = 'Payment';
        $payment->category   = $category ? $category->name : '';
        $payment->user_id    = $payment->vender_id;
        $payment->user_type  = 'Vender';
        $payment->account    = $request->account_id;
        Transaction::addTransaction($payment);

        $vender        = Vender::find($request->vender_id);
        $bp            = new BillPayment();
        $bp->name      = $vender->name ?? '';
        $bp->method    = '-';
        $bp->date      = \Auth::user()->dateFormat($request->date);
        $bp->amount    = \Auth::user()->priceFormat($request->amount);
        $bp->bill      = '';

        if ($vender) {
            Utility::userBalance('vendor', $vender->id, $request->amount, 'debit');
        }
        Utility::bankAccountBalance($request->account_id, $request->amount, 'debit');

        $uArr = [
            'payment_name'   => $bp->name,
            'payment_bill'   => $bp->bill,
            'payment_amount' => $bp->amount,
            'payment_date'   => $bp->date,
            'payment_method' => $bp->method,
        ];
        try {
            if ($vender) {
                Utility::sendEmailTemplate('new_bill_payment', [$vender->id => $vender->email], $uArr);
            }
        } catch (\Exception $e) {
            $smtp_error = __('E-Mail has been not sent due to SMTP configuration');
        }

        // Twilio
        $setting = Utility::settings(\Auth::user()->creatorId());
        if ($vender && ($setting['payment_notification'] ?? 0) == 1) {
            Utility::send_twilio_msg($vender->contact, 'new_payment', [
                'payment_name'   => $bp->name,
                'payment_amount' => $bp->amount,
                'payment_date'   => $bp->date,
                'type'           => 'Payment',
            ]);
        }

        // webhook
        $webhook = Utility::webhookSetting('New Payment');
        if ($webhook) {
            $parameter = json_encode($payment);
            $status    = Utility::WebhookCall($webhook['url'], $parameter, $webhook['method']);
            if ($status) {
                return redirect()->route('payment.index')
                    ->with('success', __('Payment successfully created.') . ((isset($smtp_error)) ? '<br> <span class="text-danger">' . $smtp_error . '</span>' : ''));
            }
            return redirect()->back()->with('error', __('Webhook call failed.'));
        }

        return redirect()->route('payment.index')
            ->with('success', __('Payment successfully created.') . ((isset($smtp_error)) ? '<br> <span class="text-danger">' . $smtp_error . '</span>' : ''));
    }

    public function edit(Payment $payment)
    {
        if (!\Auth::user()->can('edit payment')) {
            return response()->json(['error' => __('Permission denied.')], 401);
        }

        // Include selected (even if soft-deleted)
        $venders = TrashedSelect::optionsWithUsed(
            Vender::class,
            \Auth::user()->creatorId(),
            [$payment->vender_id]
        )->prepend('--', 0);

        $categories = ProductServiceCategory::where('created_by', \Auth::user()->creatorId())
            ->whereNotIn('type', ['product & service', 'income'])->get()->pluck('name', 'id');

        // No closure: use holder_name as label
        $accounts = TrashedSelect::optionsWithUsed(
            BankAccount::class,
            \Auth::user()->creatorId(),
            [$payment->account_id],
            'holder_name'
        );

        return view('payment.edit', compact('venders', 'categories', 'accounts', 'payment'));
    }

    public function update(Request $request, Payment $payment)
    {
        if (!\Auth::user()->can('edit payment')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $validator = \Validator::make(
            $request->all(),
            [
                'date'        => 'required',
                'amount'      => 'required',
                'account_id'  => 'required',
                'vender_id'   => 'required',
                'category_id' => 'required',
            ]
        );
        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->getMessageBag()->first());
        }

        $vender = Vender::find($request->vender_id);

        if ($vender) {
            Utility::userBalance('vendor', $payment->vender_id, $payment->amount, 'credit');
        }
        Utility::bankAccountBalance($payment->account_id, $payment->amount, 'credit');

        if ($vender) {
            Utility::userBalance('vendor', $vender->id, $request->amount, 'debit');
        }
        Utility::bankAccountBalance($request->account_id, $request->amount, 'debit');

        $payment->date           = $request->date;
        $payment->amount         = $request->amount;
        $payment->account_id     = $request->account_id;
        $payment->vender_id      = $request->vender_id;
        $payment->category_id    = $request->category_id;
        $payment->payment_method = 0;
        $payment->reference      = $request->reference;
        $payment->description    = $request->description;

        if (!empty($request->add_receipt)) {
            $file_path  = 'uploads/payment/' . $payment->add_receipt;
            $image_size = $request->file('add_receipt')->getSize();

            $result = Utility::updateStorageLimit(\Auth::user()->creatorId(), $image_size);

            if ($result == 1) {
                Utility::changeStorageLimit(\Auth::user()->creatorId(), $file_path);
                $fileName             = time() . "_" . $request->add_receipt->getClientOriginalName();
                $payment->add_receipt = $fileName;

                $dir  = 'uploads/payment';
                $path = Utility::upload_file($request, 'add_receipt', $fileName, $dir, []);
                if ($path['flag'] == 0) {
                    return redirect()->back()->with('error', __($path['msg']));
                }
            } else {
                return redirect()->back()->with('error', $result);
            }
        }

        $payment->save();

        $bank = TrashedSelect::findWithTrashed(BankAccount::class, $payment->account_id);
        if ($bank) {
            $data = [
                'account_id'         => $bank->chart_account_id,
                'transaction_type'   => 'Debit',
                'transaction_amount' => $payment->amount,
                'reference'          => 'Payment',
                'reference_id'       => $payment->id,
                'reference_sub_id'   => 0,
                'date'               => $payment->date,
            ];
            Utility::addTransactionLines($data);
        }

        $category = ProductServiceCategory::find($request->category_id);
        $payment->category   = $category ? $category->name : '';
        $payment->payment_id = $payment->id;
        $payment->type       = 'Payment';
        $payment->account    = $request->account_id;
        Transaction::editTransaction($payment);

        return redirect()->route('payment.index')->with('success', __('Payment successfully updated.') . ((isset($result) && $result != 1) ? '<br> <span class="text-danger">' . $result . '</span>' : ''));
    }

    public function destroy(Payment $payment)
    {
        if (!\Auth::user()->can('delete payment')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        if ($payment->created_by != \Auth::user()->creatorId()) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $file_path = 'uploads/payment/' . $payment->add_receipt;
        $result    = Utility::changeStorageLimit(\Auth::user()->creatorId(), $file_path);

        TransactionLines::where('reference_id', $payment->id)->where('reference', 'Payment')->delete();

        $payment->delete();
        $type = 'Payment';
        $user = 'Vender';
        Transaction::destroyTransaction($payment->id, $type, $user);

        if ($payment->vender_id != 0) {
            Utility::userBalance('vendor', $payment->vender_id, $payment->amount, 'credit');
        }
        Utility::bankAccountBalance($payment->account_id, $payment->amount, 'credit');

        return redirect()->route('payment.index')->with('success', __('Payment successfully deleted.'));
    }

    public function export($date = null)
    {
        $companyName = \Auth::user()->name ?? 'Company';
        $companyName = preg_replace('/[^A-Za-z0-9\-_]/', '_', $companyName);
        $dateString  = date('Y-m-d_H-i-s');
        $dateFilter  = $date ? "_{$date}" : '';
        $filename    = "payments_{$companyName}{$dateFilter}_{$dateString}.xlsx";

        return Excel::download(new PaymentExport($date), $filename);
    }
    public function exportSelected(Request $request)
{
    $this->authorize('manage payment');

    $ids = (array) $request->input('ids', []);
    if (empty($ids)) {
        return back()->with('error', __('Please select at least one payment.'));
    }

    $creatorId = \Auth::user()->creatorId();
    $selected  = Payment::where('created_by', $creatorId)
        ->whereIn('id', $ids)
        ->pluck('id')
        ->all();

    if (empty($selected)) {
        return back()->with('error', __('No matching payments found.'));
    }

    $companyName = \Auth::user()->name ?? 'Company';
    $companyName = preg_replace('/[^A-Za-z0-9\-_]/', '_', $companyName);
    $dateString  = date('Y-m-d_H-i-s');
    $filename    = "payments_selected_{$companyName}_{$dateString}.xlsx";

    return Excel::download(new PaymentSelectedExport($selected), $filename);
}

// NEW: bulk delete selected rows
public function bulkDestroy(Request $request)
{
    $this->authorize('delete payment');

    $ids = (array) $request->input('ids', []);
    if (empty($ids)) {
        return back()->with('error', __('Please select at least one payment.'));
    }

    $creatorId = \Auth::user()->creatorId();

    DB::transaction(function () use ($ids, $creatorId) {
        $payments = Payment::where('created_by', $creatorId)
            ->whereIn('id', $ids)
            ->get();

        foreach ($payments as $payment) {
            // mirror single destroy() logic
            if (!empty($payment->add_receipt)) {
                $file_path = 'uploads/payment/' . $payment->add_receipt;
                Utility::changeStorageLimit($creatorId, $file_path);
                if (file_exists($file_path)) {
                    \File::delete($file_path);
                }
            }

            // Remove accounting traces
            TransactionLines::where('reference_id', $payment->id)
                ->where('reference', 'Payment')
                ->delete();

            $type = 'Payment';
            $user = 'Vender';
            Transaction::destroyTransaction($payment->id, $type, $user);

            if ($payment->vender_id != 0) {
                Utility::userBalance('vendor', $payment->vender_id, $payment->amount, 'credit');
            }
            Utility::bankAccountBalance($payment->account_id, $payment->amount, 'credit');

            $payment->delete();
        }
    });

    return back()->with('success', __('Selected payments deleted.'));
}

}
