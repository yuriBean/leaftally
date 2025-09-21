<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Retainer;
use App\Models\RetainerPayment;
use App\Models\InvoicePayment;
use App\Models\Order;
use App\Models\Plan;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserCoupon;
use App\Models\Utility;
use App\Models\Vender;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Session;
use Stripe;

class StripePaymentController extends Controller
{
    public $stripe_secret;
    public $settings;
    public $currancy;

    public function index()
    {
        $objUser = \Auth::user();

        if ($objUser->type == 'super admin') {
            $orders = Order::select([
                'orders.*',
                'users.name as user_name',
            ])->join('users', 'orders.user_id', '=', 'users.id')->orderBy('orders.created_at', 'DESC')->with('total_coupon_used.coupon_detail')->with(['total_coupon_used.coupon_detail'])->get();

            $userOrders = Order::select('*')
                ->whereIn('id', function ($query) {
                    $query->selectRaw('MAX(id)')
                        ->from('orders')
                        ->groupBy('user_id');
                })
                ->orderBy('created_at', 'desc')
                ->get();

            return view('order.index', compact('orders', 'userOrders'));
        } else {
            $orders = Order::select([
                'orders.*',
                'users.name as user_name',
            ])->join('users', 'orders.user_id', '=', 'users.id')->orderBy('orders.created_at', 'DESC')->where('users.id', '=', $objUser->id)->with('total_coupon_used.coupon_detail')->with(['total_coupon_used.coupon_detail'])->get();

            return view('order.index', compact('orders'));
        }
    }


    public function refund(Request $request, $id, $user_id)
    {
        Order::where('id', $request->id)->update(['is_refund' => 1]);

        $user = User::find($user_id);

        $assignPlan = $user->assignPlan(1);

        return redirect()->back()->with('success', __('We successfully planned a refund and assigned a free plan.'));
    }


    public function stripe($code)
    {
        try {
            $admin_payment_setting = Utility::getAdminPaymentSetting();

            if (\Auth::user()->type == 'company') {
                if (!empty($admin_payment_setting) && collect($admin_payment_setting)->contains('on')) {
                    $plan_id               = \Illuminate\Support\Facades\Crypt::decrypt($code);
                    $plan                  = Plan::find($plan_id);

                    if ($plan) {
                        return view('stripe', compact('plan', 'admin_payment_setting'));
                    } else {
                        return redirect()->back()->with('error', __('Plan is deleted.'));
                    }
                } else {
                    return redirect()->back()->with('error', __('The admin has not set the payment method.'));
                }
            } else {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Not Found');
        }
    }


    public function stripePost(Request $request)
    {
        $admin = Utility::getAdminPaymentSetting();
        $objUser = \Auth::user();
        $planID  = \Illuminate\Support\Facades\Crypt::decrypt($request->plan_id);
        $plan    = Plan::find($planID);

        $admin_payment_setting = Utility::getAdminPaymentSetting();
        $admin = Utility::getAdminPaymentSetting();

        if ($plan) {
            try {
                $price = $plan->price;
                if (!empty($request->coupon)) {
                    $coupons = Coupon::where('code', strtoupper($request->coupon))->where('is_active', '1')->first();
                    if (!empty($coupons)) {
                        $usedCoupun     = $coupons->used_coupon();
                        $discount_value = ($plan->price / 100) * $coupons->discount;
                        $price          = $plan->price - $discount_value;

                        if ($coupons->limit == $usedCoupun) {
                            return redirect()->back()->with('error', __('This coupon code has expired.'));
                        }
                    } else {
                        return redirect()->back()->with('error', __('This coupon code is invalid or has expired.'));
                    }
                }

                $orderID = strtoupper(str_replace('.', '', uniqid('', true)));


                if ($price > 0.0) {
                    Stripe\Stripe::setApiKey($admin_payment_setting['stripe_secret']);

                    $data = Stripe\Charge::create([
                        "amount" => 100 * $price,
                        "currency" => !empty($admin_payment_setting['currency']) ? $admin_payment_setting['currency'] : '',
                        "source" => $request->stripeToken,
                        "description" => " Plan - " . $plan->name,
                        "metadata" => ["order_id" => $orderID],
                        "shipping" => [
                            "name" => $request->name,
                            'address' => [
                                "line1" => "123 Default Street",
                                "city" => "aaaa",
                                "state" => "bbbbbb",
                                "postal_code" => "111111",
                                "country" => "IN",
                            ]
                        ],
                    ]);
                } else {
                    $data['amount_refunded'] = 0;
                    $data['failure_code']    = '';
                    $data['paid']            = 1;
                    $data['captured']        = 1;
                    $data['status']          = 'succeeded';
                }


                if ($data['amount_refunded'] == 0 && empty($data['failure_code']) && $data['paid'] == 1 && $data['captured'] == 1) {

                    Order::create(
                        [
                            'order_id' => $orderID,
                            'name' => $request->name,
                            'card_number' => isset($data['payment_method_details']['card']['last4']) ? $data['payment_method_details']['card']['last4'] : '',
                            'card_exp_month' => isset($data['payment_method_details']['card']['exp_month']) ? $data['payment_method_details']['card']['exp_month'] : '',
                            'card_exp_year' => isset($data['payment_method_details']['card']['exp_year']) ? $data['payment_method_details']['card']['exp_year'] : '',
                            'plan_name' => $plan->name,
                            'plan_id' => $plan->id,
                            'price' => $price,
                            'price_currency' => $admin['currency'],
                            'txn_id' => isset($data['balance_transaction']) ? $data['balance_transaction'] : '',
                            'payment_type' => __('STRIPE'),
                            'payment_status' => isset($data['status']) ? $data['status'] : 'succeeded',
                            'receipt' => isset($data['receipt_url']) ? $data['receipt_url'] : 'free coupon',
                            'user_id' => $objUser->id,
                        ]
                    );

                    if (!empty($request->coupon)) {
                        $userCoupon         = new UserCoupon();
                        $userCoupon->user   = $objUser->id;
                        $userCoupon->coupon = $coupons->id;
                        $userCoupon->order  = $orderID;
                        $userCoupon->save();

                        $usedCoupun = $coupons->used_coupon();
                        if ($coupons->limit <= $usedCoupun) {
                            $coupons->is_active = 0;
                            $coupons->save();
                        }
                    }
                    if ($data['status'] == 'succeeded') {

                        $assignPlan = $objUser->assignPlan($plan->id);
                        if ($assignPlan['is_success']) {
                            return redirect()->route('plans.index')->with('success', __('Plan successfully activated.'));
                        } else {
                            return redirect()->route('plans.index')->with('error', __($assignPlan['error']));
                        }
                    } else {
                        return redirect()->route('plans.index')->with('error', __('Your payment has failed.'));
                    }
                } else {
                    return redirect()->route('plans.index')->with('error', __('Free plans are not available.'));
                }
            } catch (\Exception $e) {
                return redirect()->route('plans.index')->with('error', __($e->getMessage()));
            }
        } else {
            return redirect()->route('plans.index')->with('error', __('Plan is deleted.'));
        }
    }


    //invoice
    public function addPayment(Request $request, $id)
    {
        // dd($request->all());
        $invoice = Invoice::find($id);
        if (Auth::check()) {
            $user_id = \Auth::user()->creatorId();
        }
        if (Auth::check()) {
            $company_payment_setting = Utility::getCompanyPaymentSettingWithOutAuth($invoice->created_by);
            $settings = DB::table('settings')->where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('value', 'name');
            $objUser = \Auth::user();
        } else {
            $user = User::where('id', $invoice->created_by)->first();
            $company_payment_setting = Utility::getCompanyPaymentSettingWithOutAuth($invoice->created_by);
            $settings = Utility::settingById($invoice->created_by);
            $objUser = $user;
        }

        $setting = Utility::settingsById($invoice->created_by);
        if ($invoice) {
            if ($request->amount > $invoice->getDue()) {
                return redirect()->back()->with('error', __('Invalid amount.'));
            } else {
                $orderID = strtoupper(str_replace('.', '', uniqid('', true)));
                $price   = $request->amount;
                Stripe\Stripe::setApiKey($company_payment_setting['stripe_secret']);

                $data = Stripe\Charge::create([
                    "amount" => 100 * $price,
                    "currency" => !empty($setting['site_currency']) ? $setting['site_currency'] : 'INR',
                    "source" => $request->stripeToken,
                    "description" => 'Invoice ' . Utility::invoiceNumberFormat($settings, $invoice->invoice_id),
                    "metadata" => ["order_id" => $orderID],
                    "shipping" => [
                        "name" => $request->name,
                        'address' => [
                            "line1" => "123 Default Street",
                            "city" => "aaaa",
                            "state" => "bbbbbb",
                            "postal_code" => "111111",
                            "country" => "IN",
                        ]
                    ],
                ]);


                if ($data['amount_refunded'] == 0 && empty($data['failure_code']) && $data['paid'] == 1 && $data['captured'] == 1) {
                    $payments = InvoicePayment::create(
                        [

                            'invoice_id' => $invoice->id,
                            'date' => date('Y-m-d'),
                            'amount' => $price,
                            'account_id' => 0,
                            'payment_method' => 0,
                            'order_id' => $orderID,
                            'currency' => $data['currency'],
                            'txn_id' => $data['balance_transaction'],
                            'payment_type' => __('STRIPE'),
                            'receipt' => null,
                            'add_receipt' => $data['receipt_url'],
                            'reference' => '',
                            'description' => 'Invoice ' . Utility::invoiceNumberFormat($settings, $invoice->invoice_id),
                        ]
                    );

                  
                    if ($invoice->getDue() <= 0) {
                        $invoice->status = 4;
                    } elseif (($invoice->getDue() - $payments->amount) == 0) {
                        $invoice->status = 4;
                    } elseif ($invoice->getDue() > 0) {
                        $invoice->status = 3;
                    } else {
                        $invoice->status = 2;
                    }
                    $invoice->save();

                    $invoicePayment              = new Transaction();
                    $invoicePayment->user_id     = $invoice->customer_id;
                    $invoicePayment->user_type   = 'Customer';
                    $invoicePayment->type        = 'STRIPE';
                    $invoicePayment->created_by  = $objUser->id;
                    $invoicePayment->payment_id  = $invoicePayment->id;
                    $invoicePayment->category    = 'Invoice';
                    $invoicePayment->amount      = $price;
                    $invoicePayment->date        = date('Y-m-d');
                    $invoicePayment->created_by  = $objUser->creatorId();
                    $invoicePayment->payment_id  = $payments->id;
                    $invoicePayment->description = 'Invoice ' . Utility::invoiceNumberFormat($settings, $invoice->invoice_id);
                    $invoicePayment->account     = 0;

                    Transaction::addTransaction($invoicePayment);

                    Utility::updateUserBalance('customer', $invoice->customer_id, $request->amount, 'debit');

                    Utility::bankAccountBalance($request->account_id, $request->amount, 'credit');


                    // Twilio Notification

                    $setting  = Utility::settingsById($objUser->creatorId());
                    $customer = Customer::find($invoice->customer_id);
                    if (isset($setting['payment_notification']) && $setting['payment_notification'] == 1) {
                        $uArr = [
                            'invoice_id' => $invoice->id,
                            'payment_name' => $customer->name,
                            'payment_amount' => $price,
                            'payment_date' => $objUser->dateFormat($request->date),
                            'type' => 'STRIPE',
                            'user_name' => $objUser->name,
                        ];

                        Utility::send_twilio_msg($customer->contact, 'new_payment', $uArr, $invoice->created_by);
                    }

                    // webhook
                    $module = 'New Payment';
                    $webhook =  Utility::webhookSetting($module, $invoice->created_by);

                    if ($webhook) {
                        $parameter = json_encode($invoice);

                        // 1 parameter is  URL , 2 parameter is data , 3 parameter is method
                        $status = Utility::WebhookCall($webhook['url'], $parameter, $webhook['method']);

                    }

                    if (Auth::check()) {
                        return redirect()->route('invoice.show', \Crypt::encrypt($invoice->id))->with('success', __('Payment successfully added'));
                    } else {
                        return redirect()->back()->with('success', __(' Payment successfully added.'));
                    }
                } else {
                    if (Auth::check()) {
                        return redirect()->route('invoice.show', \Crypt::encrypt($invoice->id))->with('error', __('Transaction has been ' . $status));
                    } else {
                        return redirect()->back()->with('success', __('Transaction succesfull'));
                    }
                }
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function addretainerpayment(Request $request, $id)
    {
        $retainer = Retainer::find($id);
        if (Auth::check()) {
            $user_id = \Auth::user()->creatorId();
        }
        if (Auth::check()) {
            $company_payment_setting = Utility::getCompanyPaymentSettingWithOutAuth($retainer->created_by);
            $settings = DB::table('settings')->where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('value', 'name');
            $objUser = \Auth::user();
        } else {
            $user = User::where('id', $retainer->created_by)->first();
            $company_payment_setting = Utility::getCompanyPaymentSettingWithOutAuth($retainer->created_by);
            $settings = Utility::settingById($retainer->created_by);
            $objUser = $user;
        }

        $setting = Utility::settingsById($retainer->created_by);


        if ($retainer) {
            if ($request->amount > $retainer->getDue()) {
                return redirect()->back()->with('error', __('Invalid amount.'));
            } else {


                $orderID = strtoupper(str_replace('.', '', uniqid('', true)));
                $price   = $request->amount;
                Stripe\Stripe::setApiKey($company_payment_setting['stripe_secret']);

                $data = Stripe\Charge::create([
                    "amount" => 100 * $price,
                    "currency" => $setting['site_currency'] ?? '',
                    "source" => $request->stripeToken,
                    "description" => 'Retainer ' . Utility::retainerNumberFormat($settings, $retainer->retainer_id),
                    "metadata" => ["order_id" => $orderID],
                    "shipping" => [
                        "name" => $request->name,
                        'address' => [
                            "line1" => "123 Default Street",
                            "city" => "aaaa",
                            "state" => "bbbbbb",
                            "postal_code" => "111111",
                            "country" => "IN",
                        ]
                    ],
                ]);



                if ($data['amount_refunded'] == 0 && empty($data['failure_code']) && $data['paid'] == 1 && $data['captured'] == 1) {
                    $payments = RetainerPayment::create(
                        [

                            'retainer_id' => $retainer->id,
                            'date' => date('Y-m-d'),
                            'amount' => $price,
                            'account_id' => 0,
                            'payment_method' => 0,
                            'order_id' => $orderID,
                            'currency' => $data['currency'],
                            'txn_id' => $data['balance_transaction'],
                            'payment_type' => __('STRIPE'),
                            'receipt' => null,
                            'add_receipt' => $data['receipt_url'],
                            'reference' => '',
                            'description' => 'Retainer ' . Utility::retainerNumberFormat($settings, $retainer->retainer_id),
                        ]

                    );

                    if ($retainer->getDue() <= 0) {
                        $retainer->status = 4;
                    } elseif (($retainer->getDue() - $payments->amount) == 0) {
                        $retainer->status = 4;
                    } elseif ($retainer->getDue() > 0) {
                        $retainer->status = 3;
                    } else {
                        $retainer->status = 2;
                    }

                    $retainer->save();

                    $retainerPayment              = new Transaction();
                    $retainerPayment->user_id     = $retainer->customer_id;
                    $retainerPayment->user_type   = 'Customer';
                    $retainerPayment->type        = 'STRIPE';
                    $retainerPayment->created_by  = $objUser->id;
                    $retainerPayment->payment_id  = $retainerPayment->id;
                    $retainerPayment->category    = 'Retainer';
                    $retainerPayment->amount      = $price;
                    $retainerPayment->date        = date('Y-m-d');
                    $retainerPayment->created_by  = $objUser->creatorId();
                    $retainerPayment->payment_id  = $payments->id;
                    $retainerPayment->description = 'Retainer ' . Utility::retainerNumberFormat($settings, $retainer->retainer_id);
                    $retainerPayment->account     = 0;
                    Transaction::addTransaction($retainerPayment);



                    Utility::userBalance('customer', $retainer->customer_id, $request->amount, 'debit');

                    Utility::bankAccountBalance($request->account_id, $request->amount, 'credit');

                    //Twilio Notification
                    $setting  = Utility::settingsById($objUser->creatorId());
                    $customer = Customer::find($retainer->customer_id);
                    if (isset($setting['payment_notification']) && $setting['payment_notification'] == 1) {
                        $uArr = [
                            'retainer_id' => $retainer->id,
                            'payment_name' => $customer->name,
                            'payment_amount' => $price,
                            'payment_date' => $objUser->dateFormat($request->date),
                            'type' => 'STRIPE',
                            'user_name' => $objUser->name,
                        ];


                        Utility::send_twilio_msg($customer->contact, 'new_payment', $uArr, $retainer->created_by);
                    }

                    // webhook
                    $module = 'New Payment';

                    $webhook =  Utility::webhookSetting($module, $retainer->created_by);

                    if ($webhook) {

                        $parameter = json_encode($retainer);

                        // 1 parameter is  URL , 2 parameter is data , 3 parameter is method

                        $status = Utility::WebhookCall($webhook['url'], $parameter, $webhook['method']);
                        // if ($status == true) {
                        //     return redirect()->route('payment.index')->with('success', __('Payment successfully created.') . ((isset($smtp_error)) ? '<br> <span class="text-danger">' . $smtp_error . '</span>' : ''));
                        // } else {
                        //     return redirect()->back()->with('error', __('Webhook call failed.'));
                        // }
                    }

                    if (Auth::check()) {
                        return redirect()->route('retainer.show', \Crypt::encrypt($retainer->id))->with('success', __('Payment successfully added'));
                    } else {
                        return redirect()->back()->with('success', __(' Payment successfully added.'));
                    }
                } else {
                    if (Auth::check()) {
                        return redirect()->route('customer.retainer.show', \Crypt::encrypt($retainer->id))->with('error', __('Transaction has been ' . $status));
                    } else {
                        return redirect()->back()->with('success', __('Transaction succesfull'));
                    }
                }
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function invoicePayWithStripe(Request $request)
    {
        $amount = $request->amount;
        $validatorArray = [
            'amount' => 'required',
            'invoice_id' => 'required',
        ];
        $validator      = \Validator::make(
            $request->all(),
            $validatorArray
        )->setAttributeNames(
            ['invoice_id' => 'Invoice']
        );
        if ($validator->fails()) {
            return Utility::error_res($validator->errors()->first());
        }

        $invoice = Invoice::find($request->invoice_id);
        if (Auth::check()) {
            $settings = Utility::settings();
        } else {
            $settings = Utility::settingById($invoice->created_by);
        }

        $this->paymentSetting($invoice->created_by);

        $amount = number_format((float)$request->amount, 2, '.', '');

        $invoice_getdue = number_format((float)$invoice->getDue(), 2, '.', '');

        if ($invoice_getdue < $amount) {
            return Utility::error_res('not correct amount');
        }

        try {
            $stripe_formatted_price = in_array(
                $this->currancy,
                [
                    'MGA',
                    'BIF',
                    'CLP',
                    'PYG',
                    'DJF',
                    'RWF',
                    'GNF',
                    'UGX',
                    'JPY',
                    'VND',
                    'VUV',
                    'XAF',
                    'KMF',
                    'KRW',
                    'XOF',
                    'XPF',
                ]
            ) ? number_format($amount, 2, '.', '') : number_format($amount, 2, '.', '') * 100;

            $return_url_parameters = function ($return_type) {
                return '&return_type=' . $return_type . '&payment_processor=stripe';
            };

            /* Initiate Stripe */
            \Stripe\Stripe::setApiKey($this->stripe_secret);


            $stripe_session = \Stripe\Checkout\Session::create(
                [
                    'payment_method_types' => ['card'],
                    'line_items' => [
                        [
                            'name' => $settings['company_name'] . " - " . Utility::invoiceNumberFormat($invoice->invoice_id),
                            'description' => 'payment for Invoice',
                            'amount' => $stripe_formatted_price,
                            'currency' => Utility::getValByName('site_currency'),
                            'quantity' => 1,
                        ],
                    ],
                    'metadata' => [
                        'invoice_id' => $request->invoice_id,
                    ],
                    'success_url' => route(
                        'invoice.stripe',
                        [
                            'invoice_id' => encrypt($request->invoice_id),
                            'TXNAMOUNT' => $amount,
                            $return_url_parameters('success'),
                        ]
                    ),
                    'cancel_url' => route(
                        'invoice.stripe',
                        [
                            'invoice_id' => encrypt($request->invoice_id),
                            'TXNAMOUNT' => $amount,
                            $return_url_parameters('cancel'),
                        ]
                    ),
                ]
            );
            Utility::updateUserBalance('customer', $invoice->customer_id, $request->amount, 'debit');

            Utility::bankAccountBalance($request->account_id, $request->amount, 'credit');

            $stripe_session = $stripe_session ?? false;

            try {
                return new \RedirectResponse($stripe_session->url);
            } catch (\Exception $e) {
                \Log::debug($e->getMessage());
                return redirect()->route('pay.invoice', \Illuminate\Support\Facades\Crypt::encrypt($invoice->id))->with('error', __('Transaction has been failed!'));
            }
        } catch (\Exception $e) {
            \Log::debug($e->getMessage());
            return redirect()->route('pay.invoice', \Illuminate\Support\Facades\Crypt::encrypt($invoice->id))->with('error', __('Transaction has been failed!'));
        }
    }
}
