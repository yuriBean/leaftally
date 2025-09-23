<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoicePayment;
use App\Models\Retainer;
use App\Models\RetainerPayment;
use App\Models\Order;
use App\Models\User;
use App\Models\Plan;
use App\Models\UserCoupon;
use App\Models\Utility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MolliePaymentController extends Controller
{

    public $api_key;
    public $profile_id;
    public $partner_id;
    public $is_enabled;
    public $invoiceData;

    public function paymentConfig()
    {
        if (\Auth::user()->type == 'company') {
            $payment_setting = Utility::getAdminPaymentSetting();
        } else {
            $payment_setting = Utility::getCompanyPaymentSetting(!empty($this->invoiceData) ? $this->invoiceData->created_by : 0);
        }

        $this->api_key    = isset($payment_setting['mollie_api_key']) ? $payment_setting['mollie_api_key'] : '';
        $this->profile_id = isset($payment_setting['mollie_profile_id']) ? $payment_setting['mollie_profile_id'] : '';
        $this->partner_id = isset($payment_setting['mollie_partner_id']) ? $payment_setting['mollie_partner_id'] : '';
        $this->is_enabled = isset($payment_setting['is_mollie_enabled']) ? $payment_setting['is_mollie_enabled'] : 'off';

        return $this;
    }

    public function planPayWithMollie(Request $request)
    {
        $payment    = $this->paymentConfig();
        $planID     = \Illuminate\Support\Facades\Crypt::decrypt($request->plan_id);
        $plan       = Plan::find($planID);
        $authuser   = Auth::user();
        $coupons_id = '';
        $admin = Utility::getAdminPaymentSetting();

        if ($plan) {
            $price = $plan->price;
            if (isset($request->coupon) && !empty($request->coupon)) {
                $request->coupon = trim($request->coupon);
                $coupons         = Coupon::where('code', strtoupper($request->coupon))->where('is_active', '1')->first();
                if (!empty($coupons)) {
                    $usedCoupun             = $coupons->used_coupon();
                    $discount_value         = ($price / 100) * $coupons->discount;
                    $plan->discounted_price = $price - $discount_value;
                    $coupons_id             = $coupons->id;
                    if ($usedCoupun >= $coupons->limit) {
                        return redirect()->back()->with('error', __('This coupon code has expired.'));
                    }
                    $price = $price - $discount_value;
                } else {
                    return redirect()->back()->with('error', __('This coupon code is invalid or has expired.'));
                }
            }

            if ($price <= 0) {
                return redirect()->route('plans.index')->with('error', __('Free plans are not available.'));
            }
            $errormsg = "";
            try {
                $mollie = new \Mollie\Api\MollieApiClient();
                $mollie->setApiKey($this->api_key);

                $payment = $mollie->payments->create(
                    [
                        "amount" => [
                            "currency" => $admin['currency'] ? $admin['currency'] : 'USD',
                            "value" => number_format($price, 2),
                        ],

                        "description" => "payment for product",
                        "redirectUrl" => route(
                            'plan.mollie',
                            [
                                $request->plan_id,
                                'coupon_id=' . $coupons_id,
                            ]
                        ),
                    ]
                );

                session()->put('mollie_payment_id', $payment->id);
            } catch (\Exception $e) {
                $errormsg = $e->getMessage();
                return redirect()->back()->with('error', 'The amount contains an invalid value.');
            }
            return redirect($payment->getCheckoutUrl())->with('payment_id', $payment->id);
        } else {
            return redirect()->back()->with('error', 'Plan is deleted.');
        }
    }

    public function getPaymentStatus(Request $request, $plan)
    {
        $payment = $this->paymentConfig();

        $planID  = \Illuminate\Support\Facades\Crypt::decrypt($plan);
        $plan    = Plan::find($planID);
        $user    = Auth::user();
        $admin = Utility::getAdminPaymentSetting();

        $orderID = time();
        if ($plan) {
            $price = $plan->price;
            try {
                $mollie = new \Mollie\Api\MollieApiClient();
                $mollie->setApiKey($this->api_key);

                if (session()->has('mollie_payment_id')) {
                    $payment = $mollie->payments->get(session()->get('mollie_payment_id'));

                    if ($payment->isPaid()) {

                        if ($request->has('coupon_id') && $request->coupon_id != '') {
                            $coupons = Coupon::find($request->coupon_id);

                            if (!empty($coupons)) {
                                $userCoupon         = new UserCoupon();
                                $userCoupon->user   = $user->id;
                                $userCoupon->coupon = $coupons->id;
                                $userCoupon->order  = $orderID;
                                $userCoupon->save();

                                $usedCoupun = $coupons->used_coupon();
                                if ($coupons->limit <= $usedCoupun) {
                                    $coupons->is_active = 0;
                                    $coupons->save();
                                }
                            }
                        }
                        Utility::referralTransaction($plan);
                        $order                 = new Order();
                        $order->order_id       = $orderID;
                        $order->name           = $user->name;
                        $order->card_number    = '';
                        $order->card_exp_month = '';
                        $order->card_exp_year  = '';
                        $order->plan_name      = $plan->name;
                        $order->plan_id        = $plan->id;
                        $order->price          = $price == null ? 0 : $price;
                        $order->price_currency = $admin['currency'] ? $admin['currency'] : 'USD';
                        $order->txn_id         = isset($request->TXNID) ? $request->TXNID : '';
                        $order->payment_type   = __('Mollie');
                        $order->payment_status = 'success';
                        $order->receipt        = '';
                        $order->user_id        = $user->id;
                        $order->save();

                        $assignPlan = $user->assignPlan($plan->id, $request->payment_frequency);

                        if ($assignPlan['is_success']) {
                            return redirect()->route('plans.index')->with('success', __('Plan activated Successfully!'));
                        } else {
                            return redirect()->route('plans.index')->with('error', __($assignPlan['error']));
                        }
                    } else {
                        return redirect()->route('plans.index')->with('error', __('Transaction has been failed! '));
                    }
                } else {
                    return redirect()->route('plans.index')->with('error', __('Transaction has been failed! '));
                }
            } catch (\Exception $e) {
                return redirect()->route('plans.index')->with('error', __('Plan not found!'));
            }
        }
    }

    public function retainerPayWithMollie(Request $request)
    {

        $retainerID = \Illuminate\Support\Facades\Crypt::decrypt($request->retainer_id);
        $retainer   = Retainer::find($retainerID);
        $setting = Utility::settingsById($invoice->created_by);
        if ($retainer) {
            $price = $request->amount;
            if ($price > 0) {
                if (Auth::check()) {
                    $payment = $this->paymentConfig();
                    $settings = DB::table('settings')->where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('value', 'name');
                } else {
                    $payment_setting = Utility::getNonAuthCompanyPaymentSetting($retainer->created_by);
                    $this->api_key    = isset($payment_setting['mollie_api_key']) ? $payment_setting['mollie_api_key'] : '';
                    $this->profile_id = isset($payment_setting['mollie_profile_id']) ? $payment_setting['mollie_profile_id'] : '';
                    $this->partner_id = isset($payment_setting['mollie_partner_id']) ? $payment_setting['mollie_partner_id'] : '';
                    $this->is_enabled = isset($payment_setting['is_mollie_enabled']) ? $payment_setting['is_mollie_enabled'] : 'off';
                    $settings = Utility::settingsById($retainer->created_by);
                }

                $payment_setting = Utility::getNonAuthCompanyPaymentSetting($retainer->created_by);
                $this->api_key    = isset($payment_setting['mollie_api_key']) ? $payment_setting['mollie_api_key'] : '';
                $this->profile_id = isset($payment_setting['mollie_profile_id']) ? $payment_setting['mollie_profile_id'] : '';
                $this->partner_id = isset($payment_setting['mollie_partner_id']) ? $payment_setting['mollie_partner_id'] : '';
                $this->is_enabled = isset($payment_setting['is_mollie_enabled']) ? $payment_setting['is_mollie_enabled'] : 'off';
                $mollie = new \Mollie\Api\MollieApiClient();
                $mollie->setApiKey($this->api_key);

                $payment = $mollie->payments->create(
                    [
                        "amount" => [
                            "currency" => $setting['site_currency'],
                            "value" => number_format($price, 2),
                        ],
                        "description" => "payment for product",
                        "redirectUrl" => route(
                            'retainer.mollie',
                            [
                                $request->retainer_id,
                                $price,
                            ]
                        ),
                    ]
                );

                session()->put('mollie_payment_id', $payment->id);

                return redirect($payment->getCheckoutUrl())->with('payment_id', $payment->id);
            } else {
                $res['msg']  = __("Enter valid amount.");
                $res['flag'] = 2;

                return $res;
            }
        } else {
            return redirect()->back()->with('error', 'Invoice is deleted.');
        }
    }

    public function getRetainerPaymentStatus(Request $request, $retainer_id, $amount)
    {

        $retainerID = \Illuminate\Support\Facades\Crypt::decrypt($retainer_id);
        $retainer   = Retainer::find($retainerID);
        if (Auth::check()) {
            $objUser = \Auth::user();
            $payment = $this->paymentConfig();
            $settings = DB::table('settings')->where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('value', 'name');
        } else {
            $user = User::where('id', $retainer->created_by)->first();
            $payment_setting = Utility::getNonAuthCompanyPaymentSetting($retainer->created_by);
            $this->api_key    = isset($payment_setting['mollie_api_key']) ? $payment_setting['mollie_api_key'] : '';
            $this->profile_id = isset($payment_setting['mollie_profile_id']) ? $payment_setting['mollie_profile_id'] : '';
            $this->partner_id = isset($payment_setting['mollie_partner_id']) ? $payment_setting['mollie_partner_id'] : '';
            $this->is_enabled = isset($payment_setting['is_mollie_enabled']) ? $payment_setting['is_mollie_enabled'] : 'off';
            $settings = Utility::settingsById($retainer->created_by);
            $objUser = $user;
        }

        $orderID  = strtoupper(str_replace('.', '', uniqid('', true)));

        $result    = array();
        if ($retainer) {

            try {
                $payment_setting = Utility::getNonAuthCompanyPaymentSetting($retainer->created_by);
                $this->api_key    = isset($payment_setting['mollie_api_key']) ? $payment_setting['mollie_api_key'] : '';
                $mollie = new \Mollie\Api\MollieApiClient();

                $mollie->setApiKey($this->api_key);

                if (session()->has('mollie_payment_id')) {
                    $payment = $mollie->payments->get(session()->get('mollie_payment_id'));

                    if ($payment->isPaid()) {

                        $payments = RetainerPayment::create(
                            [
                                'retainer_id' => $retainer->id,
                                'date' => date('Y-m-d'),
                                'amount' => $amount,
                                'payment_method' => 1,
                                'order_id' => $orderID,
                                'payment_type' => __('Mollie'),
                                'receipt' => '',
                                'description' => __('Retainer') . ' ' . Utility::retainerNumberFormat($settings, $retainer->retainer_id),
                            ]
                        );

                        $retainer = Retainer::find($retainer->id);

                        if ($retainer->getDue() <= 0.0) {
                            Retainer::change_status($retainer->id, 4);
                        } elseif ($retainer->getDue() > 0) {
                            Retainer::change_status($retainer->id, 3);
                        } else {
                            Retainer::change_status($retainer->id, 2);
                        }

                        Utility::updateUserBalance('customer', $retainer->customer_id, $request->amount, 'debit');

                        Utility::bankAccountBalance($request->account_id, $request->amount, 'credit');

                        $setting  = Utility::settingsById($objUser->creatorId());
                        $customer = Customer::find($retainer->customer_id);
                        if (isset($setting['payment_notification']) && $setting['payment_notification'] == 1) {
                            $uArr = [
                                'invoice_id' => $payments->id,
                                'payment_name' => $customer->name,
                                'payment_amount' => $amount,
                                'payment_date' => $objUser->dateFormat($request->date),
                                'type' => 'Mollie',
                                'user_name' => $objUser->name,
                            ];

                            Utility::send_twilio_msg($customer->contact, 'new_payment', $uArr, $retainer->created_by);
                        }

                        $module = 'New Payment';

                        $webhook =  Utility::webhookSetting($module, $retainer->created_by);

                        if ($webhook) {

                            $parameter = json_encode($retainer);

                            $status = Utility::WebhookCall($webhook['url'], $parameter, $webhook['method']);

                        }

                        if (Auth::check()) {
                            return redirect()->route('retainer.show', \Crypt::encrypt($retainer->id))->with('success', __('Payment successfully added.'));
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
                } else {
                    return redirect()->back()->with('error', __('Transaction has been failed! '));
                }
            } catch (\Exception $e) {
                if (Auth::check()) {
                    return redirect()->route('customer.retainer.show', \Crypt::encrypt($retainer->id))->with('error', __('Transaction has been failed.'));
                } else {
                    return redirect()->back()->with('success', __('Transaction has been complted.'));
                }
            }
        }
    }

    public function invoicePayWithMollie(Request $request)
    {

        $invoiceID = \Illuminate\Support\Facades\Crypt::decrypt($request->invoice_id);
        $invoice   = Invoice::find($invoiceID);

        if ($invoice) {
            $price = $request->amount;
            if ($price > 0) {
                if (Auth::check()) {
                    $payment = $this->paymentConfig();
                    $settings = DB::table('settings')->where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('value', 'name');
                } else {
                    $payment_setting = Utility::getNonAuthCompanyPaymentSetting($invoice->created_by);
                    $this->api_key    = isset($payment_setting['mollie_api_key']) ? $payment_setting['mollie_api_key'] : '';
                    $this->profile_id = isset($payment_setting['mollie_profile_id']) ? $payment_setting['mollie_profile_id'] : '';
                    $this->partner_id = isset($payment_setting['mollie_partner_id']) ? $payment_setting['mollie_partner_id'] : '';
                    $this->is_enabled = isset($payment_setting['is_mollie_enabled']) ? $payment_setting['is_mollie_enabled'] : 'off';
                    $settings = Utility::settingsById($invoice->created_by);
                }
                $mollie = new \Mollie\Api\MollieApiClient();
                $mollie->setApiKey($this->api_key);
                $setting = Utility::settingsById($invoice->created_by);

                $payment = $mollie->payments->create(
                    [
                        "amount" => [
                            "currency" => $setting['site_currency'],
                            "value" => number_format($price, 2),
                        ],
                        "description" => "payment for product",
                        "redirectUrl" => route(
                            'customer.invoice.mollie',
                            [
                                $request->invoice_id,
                                $price,
                            ]
                        ),
                    ]
                );

                session()->put('mollie_payment_id', $payment->id);

                return redirect($payment->getCheckoutUrl())->with('payment_id', $payment->id);
            } else {
                $res['msg']  = __("Enter valid amount.");
                $res['flag'] = 2;

                return $res;
            }
        } else {
            return redirect()->back()->with('error', 'Invoice is deleted.');
        }
    }

    public function getInvoicePaymentStatus(Request $request, $invoice_id, $amount)
    {
        $invoiceID = \Illuminate\Support\Facades\Crypt::decrypt($invoice_id);
        $invoice   = Invoice::find($invoiceID);
        if (Auth::check()) {
            $payment = $this->paymentConfig();
            $settings = DB::table('settings')->where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('value', 'name');
            $objUser = \Auth::user();
        } else {
            $user = User::where('id', $invoice->created_by)->first();
            $payment_setting = Utility::getNonAuthCompanyPaymentSetting($invoice->created_by);
            $this->api_key    = isset($payment_setting['mollie_api_key']) ? $payment_setting['mollie_api_key'] : '';
            $this->profile_id = isset($payment_setting['mollie_profile_id']) ? $payment_setting['mollie_profile_id'] : '';
            $this->partner_id = isset($payment_setting['mollie_partner_id']) ? $payment_setting['mollie_partner_id'] : '';
            $this->is_enabled = isset($payment_setting['is_mollie_enabled']) ? $payment_setting['is_mollie_enabled'] : 'off';
            $settings = Utility::settingsById($invoice->created_by);
            $objUser = $user;
        }

        $orderID  = strtoupper(str_replace('.', '', uniqid('', true)));

        $result    = array();
        if ($invoice) {
            try {
                $mollie = new \Mollie\Api\MollieApiClient();
                $mollie->setApiKey($this->api_key);

                if (session()->has('mollie_payment_id')) {
                    $payment = $mollie->payments->get(session()->get('mollie_payment_id'));

                    if ($payment->isPaid()) {

                        $payments = InvoicePayment::create(
                            [
                                'invoice_id' => $invoice->id,
                                'date' => date('Y-m-d'),
                                'amount' => $amount,
                                'payment_method' => 1,
                                'order_id' => $orderID,
                                'payment_type' => __('Mollie'),
                                'receipt' => '',
                                'description' => __('Invoice') . ' ' . Utility::invoiceNumberFormat($settings, $invoice->invoice_id),
                            ]
                        );

                        $invoice = Invoice::find($invoice->id);

                        if ($invoice->getDue() <= 0.0) {
                            Invoice::change_status($invoice->id, 4);
                        } elseif ($invoice->getDue() > 0) {
                            Invoice::change_status($invoice->id, 3);
                        } else {
                            Invoice::change_status($invoice->id, 2);
                        }

                        Utility::updateUserBalance('customer', $invoice->customer_id, $request->amount, 'debit');

                        Utility::bankAccountBalance($request->account_id, $request->amount, 'credit');

                        $setting  = Utility::settingsById($objUser->creatorId());
                        $customer = Customer::find($invoice->customer_id);
                        if (isset($setting['payment_notification']) && $setting['payment_notification'] == 1) {
                            $uArr = [
                                'invoice_id' => $payments->id,
                                'payment_name' => $customer->name,
                                'payment_amount' => $amount,
                                'payment_date' => $objUser->dateFormat($request->date),
                                'type' => 'Mollie',
                                'user_name' => $objUser->name,
                            ];

                            Utility::send_twilio_msg($customer->contact, 'new_payment', $uArr, $invoice->created_by);
                        }

                        $module = 'New Payment';

                        $webhook =  Utility::webhookSetting($module, $invoice->created_by);

                        if ($webhook) {

                            $parameter = json_encode($invoice);

                            $status = Utility::WebhookCall($webhook['url'], $parameter, $webhook['method']);

                        }

                        if (Auth::check()) {
                            return redirect()->route('invoice.show', \Crypt::encrypt($invoice->id))->with('success', __('Payment successfully added.'));
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
                } else {
                    return redirect()->back()->with('error', __('Transaction has been failed! '));
                }
            } catch (\Exception $e) {
                if (Auth::check()) {
                    return redirect()->route('invoice.show', \Crypt::encrypt($invoice->id))->with('error', __('Transaction has been failed.'));
                } else {
                    return redirect()->back()->with('success', __('Transaction has been complted.'));
                }
            }
        }
    }
}
