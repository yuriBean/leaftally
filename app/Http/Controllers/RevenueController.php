<?php

namespace App\Http\Controllers;

use App\Exports\RevenueExport;
use App\Models\BankAccount;
use App\Models\Customer;
use App\Models\InvoicePayment;
use App\Models\ProductServiceCategory;
use App\Models\Revenue;
use App\Models\Transaction;
use App\Models\TransactionLines;
use App\Models\Utility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use App\Support\TrashedSelect;

class RevenueController extends Controller
{
    public function index(Request $request)
    {
        if (\Auth::user()->can('manage revenue')) {

            $customer = TrashedSelect::activeOptions(Customer::class, \Auth::user()->creatorId())->prepend('Select Customer', '');
            $account  = TrashedSelect::activeOptions(BankAccount::class, \Auth::user()->creatorId(), 'holder_name')->prepend('Select Account', '');
            $category = ProductServiceCategory::where('created_by', \Auth::user()->creatorId())
                ->where('type', 'income')->get()->pluck('name', 'id')->prepend('Select Category', '');

            $query = Revenue::where('created_by', \Auth::user()->creatorId());

            if (str_contains((string)$request->date, ' to ')) {
                $date_range = explode(' to ', $request->date);
                $query->whereBetween('date', $date_range);
            } elseif (!empty($request->date)) {
                $query->where('date', $request->date);
            }

            if (!empty($request->customer)) {
                $query->where('customer_id', $request->customer);
            }
            if (!empty($request->account)) {
                $query->where('account_id', $request->account);
            }
            if (!empty($request->category)) {
                $query->where('category_id', $request->category);
            }
            if (!empty($request->payment)) {
                $query->where('payment_method', $request->payment);
            }

            $revenues = $query->get();

            return view('revenue.index', compact('revenues', 'customer', 'account', 'category'));
        }

        return redirect()->back()->with('error', __('Permission denied.'));
    }

    public function create()
    {
        if (\Auth::user()->can('create revenue')) {
            $customers  = TrashedSelect::activeOptions(Customer::class, \Auth::user()->creatorId())->prepend('--', 0);
            $categories = ProductServiceCategory::where('created_by', \Auth::user()->creatorId())
                ->where('type', 'income')->get()->pluck('name', 'id');
            $accounts   = TrashedSelect::activeOptions(BankAccount::class, \Auth::user()->creatorId(), 'holder_name');

            return view('revenue.create', compact('customers', 'categories', 'accounts'));
        }

        return response()->json(['error' => __('Permission denied.')], 401);
    }

    public function store(Request $request)
    {
        if (\Auth::user()->can('create revenue')) {

            $validator = \Validator::make(
                $request->all(),
                [
                    'date'       => 'required',
                    'amount'     => 'required',
                    'account_id' => 'required',
                    'category_id'=> 'required',
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first());
            }

            $revenue                 = new Revenue();
            $revenue->date           = $request->date;
            $revenue->amount         = $request->amount;
            $revenue->account_id     = $request->account_id;
            $revenue->customer_id    = $request->customer_id;
            $revenue->category_id    = $request->category_id;
            $revenue->payment_method = 0;
            $revenue->reference      = $request->reference;
            $revenue->description    = $request->description;

            if (!empty($request->add_receipt)) {
                $image_size = $request->file('add_receipt')->getSize();

                $result = Utility::updateStorageLimit(\Auth::user()->creatorId(), $image_size);
                if ($result == 1) {
                    $fileName = time() . "_" . $request->add_receipt->getClientOriginalName();
                    $revenue->add_receipt = $fileName;

                    $dir  = 'uploads/revenue';
                    $path = Utility::upload_file($request, 'add_receipt', $fileName, $dir, []);
                    if ($path['flag'] == 0) {
                        return redirect()->back()->with('error', __($path['msg']));
                    }
                } else {
                    return redirect()->back()->with('error', $result);
                }
            }

            $revenue->created_by = \Auth::user()->creatorId();
            $revenue->save();

            $category            = ProductServiceCategory::where('id', $request->category_id)->first();
            $revenue->payment_id = $revenue->id;
            $revenue->type       = 'Revenue';
            $revenue->category   = $category->name;
            $revenue->user_id    = $revenue->customer_id;
            $revenue->user_type  = 'Customer';
            $revenue->account    = $request->account_id;
            Transaction::addTransaction($revenue);

            $customer         = Customer::find($request->customer_id);
            $payment          = new InvoicePayment();
            $payment->name    = !empty($customer) ? $customer['name'] : '';
            $payment->date    = \Auth::user()->dateFormat($request->date);
            $payment->amount  = \Auth::user()->priceFormat($request->amount);
            $payment->invoice = '';

            if (!empty($customer)) {
                Utility::userBalance('customer', $customer->id, $revenue->amount, 'credit');
            }
            Utility::bankAccountBalance($request->account_id, $revenue->amount, 'credit');

            $accountId = TrashedSelect::findWithTrashed(BankAccount::class, $revenue->account_id);
            if ($accountId) {
                $data = [
                    'account_id'         => $accountId->chart_account_id,
                    'transaction_type'   => 'Credit',
                    'transaction_amount' => $revenue->amount,
                    'reference'          => 'Revenue',
                    'reference_id'       => $revenue->id,
                    'reference_sub_id'   => 0,
                    'date'               => $revenue->date,
                ];
                Utility::addTransactionLines($data);
            }

            $uArr = [
                'payment_name'     => $payment->name,
                'payment_amount'   => $payment->amount,
                'invoice_number'   => $revenue->type,
                'payment_date'     => $payment->date,
                'payment_dueAmount'=> '-',
            ];
            try {
                if (!empty($customer)) {
                    Utility::sendEmailTemplate('new_invoice_payment', [$customer->id => $customer->email], $uArr);
                }
            } catch (\Exception $e) {
                $smtp_error = __('E-Mail has been not sent due to SMTP configuration');
            }

            $setting  = Utility::settings(\Auth::user()->creatorId());
            $customer = Customer::find($request->customer_id);
            if ($customer && isset($setting['revenue_notification']) && $setting['revenue_notification'] == 1) {
                $uArr = [
                    'payment_name'   => $payment->name,
                    'payment_amount' => $payment->amount,
                    'payment_date'   => $payment->date,
                    'user_name'      => \Auth::user()->name,
                ];
                Utility::send_twilio_msg($customer->contact, 'new_revenue', $uArr);
            }

            $module  = 'New Revenue';
            $webhook = Utility::webhookSetting($module);
            if ($webhook) {
                $parameter = json_encode($revenue);
                $status    = Utility::WebhookCall($webhook['url'], $parameter, $webhook['method']);
                if ($status == true) {
                    return redirect()->route('revenue.index')->with('success', __('Revenue successfully created.'));
                }
                return redirect()->back()->with('error', __('Webhook call failed.'));
            }

            return redirect()->route('revenue.index')->with('success', __('Revenue successfully created.') . ((isset($smtp_error)) ? '<br> <span class="text-danger">' . $smtp_error . '</span>' : ''));
        }

        return redirect()->back()->with('error', __('Permission denied.'));
    }

    public function edit(Revenue $revenue)
    {
        if (\Auth::user()->can('edit revenue')) {
            $customers = TrashedSelect::optionsWithUsed(
                Customer::class,
                \Auth::user()->creatorId(),
                [$revenue->customer_id]
            )->prepend('--', 0);

            $categories = ProductServiceCategory::where('created_by', \Auth::user()->creatorId())
                ->where('type', 'income')->get()->pluck('name', 'id');

            $accounts = TrashedSelect::optionsWithUsed(
                BankAccount::class,
                \Auth::user()->creatorId(),
                [$revenue->account_id],
                'holder_name'
            );

            return view('revenue.edit', compact('customers', 'categories', 'accounts', 'revenue'));
        }

        return response()->json(['error' => __('Permission denied.')], 401);
    }

    public function update(Request $request, Revenue $revenue)
    {
        if (\Auth::user()->can('edit revenue')) {

            $validator = \Validator::make(
                $request->all(),
                [
                    'date'       => 'required',
                    'amount'     => 'required',
                    'account_id' => 'required',
                    'category_id'=> 'required',
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first());
            }

            $customer = Customer::where('id', $request->customer_id)->first();
            if (!empty($customer)) {
                Utility::userBalance('customer', $revenue->customer_id, $revenue->amount, 'debit');
            }
            Utility::bankAccountBalance($revenue->account_id, $revenue->amount, 'debit');

            if (!empty($customer)) {
                Utility::userBalance('customer', $customer->id, $request->amount, 'credit');
            }
            Utility::bankAccountBalance($request->account_id, $request->amount, 'credit');

            $revenue->date           = $request->date;
            $revenue->amount         = $request->amount;
            $revenue->account_id     = $request->account_id;
            $revenue->customer_id    = $request->customer_id;
            $revenue->category_id    = $request->category_id;
            $revenue->payment_method = 0;
            $revenue->reference      = $request->reference;
            $revenue->description    = $request->description;

            if (!empty($request->add_receipt)) {
                if ($revenue->add_receipt) {
                    $file_path  = 'uploads/revenue/' . $revenue->add_receipt;
                    $image_size = $request->file('add_receipt')->getSize();

                    $result = Utility::updateStorageLimit(\Auth::user()->creatorId(), $image_size);
                    if ($result == 1) {
                        Utility::changeStorageLimit(\Auth::user()->creatorId(), $file_path);
                        $fileName = time() . "_" . $request->add_receipt->getClientOriginalName();
                        $revenue->add_receipt = $fileName;

                        $path = storage_path('uploads/revenue/' . $revenue->add_receipt);
                        if (file_exists($path)) {
                            \File::delete($path);
                        }

                        $dir  = 'uploads/revenue';
                        $path = Utility::upload_file($request, 'add_receipt', $fileName, $dir, []);
                        if ($path['flag'] == 0) {
                            return redirect()->back()->with('error', __($path['msg']));
                        }
                    } else {
                        return redirect()->back()->with('error', $result);
                    }
                }
            }

            $revenue->save();

            $category            = ProductServiceCategory::where('id', $request->category_id)->first();
            $revenue->category   = $category->name;
            $revenue->payment_id = $revenue->id;
            $revenue->type       = 'Revenue';
            $revenue->account    = $request->account_id;
            Transaction::editTransaction($revenue);

            $accountId = TrashedSelect::findWithTrashed(BankAccount::class, $revenue->account_id);
            if ($accountId) {
                $data = [
                    'account_id'         => $accountId->chart_account_id,
                    'transaction_type'   => 'Credit',
                    'transaction_amount' => $revenue->amount,
                    'reference'          => 'Revenue',
                    'reference_id'       => $revenue->id,
                    'reference_sub_id'   => 0,
                    'date'               => $revenue->date,
                ];
                Utility::addTransactionLines($data);
            }

            return redirect()->route('revenue.index')->with('success', __('Revenue successfully updated.'));
        }

        return redirect()->back()->with('error', __('Permission denied.'));
    }

    public function destroy(Revenue $revenue)
    {
        if (\Auth::user()->can('delete revenue')) {
            if (!empty($revenue->add_receipt)) {
                $file_path = 'uploads/revenue/' . $revenue->add_receipt;
                $result = Utility::changeStorageLimit(\Auth::user()->creatorId(), $file_path);

                if (file_exists($file_path)) {
                    \File::delete($file_path);
                }
            }

            if ($revenue->created_by == \Auth::user()->creatorId()) {
                TransactionLines::where('reference_id', $revenue->id)->where('reference', 'Revenue')->delete();
                $revenue->delete();

                $type = 'Revenue';
                $user = 'Customer';
                Transaction::destroyTransaction($revenue->id, $type, $user);

                if ($revenue->customer_id != 0) {
                    Utility::userBalance('customer', $revenue->customer_id, $revenue->amount, 'debit');
                }
                Utility::bankAccountBalance($revenue->account_id, $revenue->amount, 'debit');

                return redirect()->route('revenue.index')->with('success', __('Revenue successfully deleted.'));
            }

            return redirect()->back()->with('error', __('Permission denied.'));
        }

        return redirect()->back()->with('error', __('Permission denied.'));
    }

    public function export(Request $request)
    {
        if (!\Auth::user()->can('manage revenue')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $companyName = \Auth::user()->name ?? 'Company';
        $companyName = preg_replace('/[^A-Za-z0-9\-_]/', '_', $companyName);
        $date        =date('Y-m-d_H-i-s');
        $filename    = "revenues_{$companyName}_" . date('Y-m-d_H-i-s') . ".xlsx";

        return Excel::download(new RevenueExport(['date' => $date]), $filename);
    }

    public function exportSelected(Request $request)
    {
        if (!\Auth::user()->can('manage revenue')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $idsParam = $request->input('ids', []);
        $ids = collect(is_array($idsParam) ? $idsParam : explode(',', (string) $idsParam))
            ->filter()
            ->map(fn ($v) => (int) $v)
            ->unique()
            ->values()
            ->all();

        if (empty($ids)) {
            return redirect()->back()->with('error', __('Please select at least one revenue.'));
        }

        $companyName = \Auth::user()->name ?? 'Company';
        $companyName = preg_replace('/[^A-Za-z0-9\-_]/', '_', $companyName);
        $filename    = "revenues_selected_{$companyName}_" . date('Y-m-d_H-i-s') . ".xlsx";

        return Excel::download(new RevenueExport(['ids' => $ids]), $filename);
    }

    public function bulkDestroy(Request $request)
    {
        if (!\Auth::user()->can('delete revenue')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $idsParam = $request->input('ids', []);
        $ids = collect(is_array($idsParam) ? $idsParam : explode(',', (string) $idsParam))
            ->filter()
            ->map(fn ($v) => (int) $v)
            ->unique()
            ->values()
            ->all();

        if (empty($ids)) {
            return redirect()->back()->with('error', __('Please select at least one revenue.'));
        }

        $revenues = Revenue::whereIn('id', $ids)
            ->where('created_by', \Auth::user()->creatorId())
            ->get();

        $deleted = 0;

        foreach ($revenues as $rev) {
            if (!empty($rev->add_receipt)) {
                $file_path = 'uploads/revenue/' . $rev->add_receipt;
                try {
                    Utility::changeStorageLimit(\Auth::user()->creatorId(), $file_path);
                } catch (\Throwable $e) {}
                if (file_exists($file_path)) {
                    @unlink($file_path);
                }
            }

            if (!empty($rev->customer_id)) {
                Utility::userBalance('customer', $rev->customer_id, $rev->amount, 'debit');
            }
            Utility::bankAccountBalance($rev->account_id, $rev->amount, 'debit');

            TransactionLines::where('reference_id', $rev->id)->where('reference', 'Revenue')->delete();
            Transaction::destroyTransaction($rev->id, 'Revenue', 'Customer');

            $rev->delete();
            $deleted++;
        }

        return redirect()->back()->with(
            'success',
            trans_choice(':count revenue deleted.|:count revenues deleted.', $deleted, ['count' => $deleted])
        );
    }
}
