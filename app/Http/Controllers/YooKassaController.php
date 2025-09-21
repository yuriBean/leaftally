<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoicePayment;
use App\Models\Order;
use App\Models\Plan;
use App\Models\Retainer;
use App\Models\RetainerPayment;
use App\Models\User;
use App\Models\UserCoupon;
use App\Models\Utility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use YooKassa\Client;

class YooKassaController extends Controller
{
    public function planPayWithYooKassa(Request $request)
    {
        $payment_setting = Utility::getAdminPaymentSetting();
        $yookassa_shop_id = $payment_setting['yookassa_shop_id'];
        $yookassa_secret_key = $payment_setting['yookassa_secret'];
        $currency = isset($payment_setting['currency']) ? $payment_setting['currency'] : 'USD';


        $planID = \Illuminate\Support\Facades\Crypt::decrypt($request->plan_id);
        $authuser = Auth::user();
        $plan = Plan::find($planID);
        if ($plan) {

            $get_amount = $plan->price;

            if (!empty($request->coupon)) {
                $coupons = Coupon::where('code', strtoupper($request->coupon))->where('is_active', '1')->first();
                if (!empty($coupons)) {
                    $usedCoupun = $coupons->used_coupon();
                    $discount_value = ($plan->price / 100) * $coupons->discount;
                    $get_amount = $plan->price - $discount_value;
                    $orderID = strtoupper(str_replace('.', '', uniqid('', true)));
                    $userCoupon = new UserCoupon();
                    $userCoupon->user = $authuser->id;
                    $userCoupon->coupon = $coupons->id;
                    $userCoupon->order = $orderID;
                    $userCoupon->save();
                    if ($coupons->limit == $usedCoupun) {
                        return redirect()->back()->with('error', __('This coupon code has expired.'));
                    }
                } else {
                    return redirect()->back()->with('error', __('This coupon code is invalid or has expired.'));
                }
            }

            try {

                if (is_int((int)$yookassa_shop_id)) {
                    $client = new Client();
                    $client->setAuth((int)$yookassa_shop_id, $yookassa_secret_key);
                    $orderID = strtoupper(str_replace('.', '', uniqid('', true)));
                    $product = !empty($plan->name) ? $plan->name : 'Life time';
                    $payment = $client->createPayment(
                        array(
                            'amount' => array(
                                'value' => $get_amount,
                                'currency' => $currency,
                            ),
                            'confirmation' => array(
                                'type' => 'redirect',
                                'return_url' => route('plan.get.yookassa.status', [$plan->id, 'order_id' => $orderID, 'price' => $get_amount]),
                            ),
                            'capture' => true,
                            'description' => 'Заказ №1',
                        ),
                        uniqid('', true)
                    );

                    $authuser = Auth::user();
                    $authuser->plan = $plan->id;
                    $authuser->save();


                    if (!empty($authuser->payment_subscription_id) && $authuser->payment_subscription_id != '') {
                        try {
                            $authuser->cancel_subscription($authuser->id);
                        } catch (\Exception $exception) {
                            Log::debug($exception->getMessage());
                        }
                    }

                    Order::create(
                        [
                            'order_id' => $orderID,
                            'name' => null,
                            'email' => null,
                            'card_number' => null,
                            'card_exp_month' => null,
                            'card_exp_year' => null,
                            'plan_name' => $plan->name,
                            'plan_id' => $plan->id,
                            'price' => $get_amount == null ? 0 : $get_amount,
                            'price_currency' => $currency,
                            'txn_id' => '',
                            'payment_type' => __('YooKassa'),
                            'payment_status' => 'succeeded',
                            'receipt' => null,
                            'user_id' => $authuser->id,
                        ]
                    );


                    Session::put('payment_id', $payment['id']);

                    if ($payment['confirmation']['confirmation_url'] != null) {
                        return redirect($payment['confirmation']['confirmation_url']);
                    } else {
                        return redirect()->route('plans.index')->with('error', 'Something went wrong, Please try again');
                    }

                } else {
                    return redirect()->back()->with('error', 'Please Enter  Valid Shop Id Key');
                }
            } catch (\Throwable $th) {

                return redirect()->back()->with('error', $th);
            }
        }
    }

    public function planGetYooKassaStatus(Request $request, $planId)
    {
        $payment_setting = Utility::getAdminPaymentSetting();
        $yookassa_shop_id = $payment_setting['yookassa_shop_id'];
        $yookassa_secret_key = $payment_setting['yookassa_secret'];
        $currency = isset($payment_setting['currency']) ? $payment_setting['currency'] : 'USD';

        if (is_int((int)$yookassa_shop_id)) {
            $client = new Client();
            $client->setAuth((int)$yookassa_shop_id, $yookassa_secret_key);
            $paymentId = Session::get('payment_id');
            Session::forget('payment_id');
            if ($paymentId == null) {
                return redirect()->back()->with('error', __('Transaction Unsuccesfull'));
            }

            $payment = $client->getPaymentInfo($paymentId);

            if (isset($payment) && $payment->status == "succeeded") {

                $plan = Plan::find($planId);
                $user = auth()->user();
                $orderID = strtoupper(str_replace('.', '', uniqid('', true)));
                try {
                    $Order                 = Order::where('order_id', $request->order_id)->first();
                    $Order->payment_status = 'succeeded';
                    $Order->save();

                    $assignPlan = $user->assignPlan($plan->id);
                    $coupons = Coupon::find($request->coupon_id);

                    if (!empty($request->coupon_id)) {
                        if (!empty($coupons)) {
                            $userCoupon = new UserCoupon();
                            $userCoupon->user = $user->id;
                            $userCoupon->coupon = $coupons->id;
                            $userCoupon->order = $orderID;
                            $userCoupon->save();
                            $usedCoupun = $coupons->used_coupon();
                            if ($coupons->limit <= $usedCoupun) {
                                $coupons->is_active = 0;
                                $coupons->save();
                            }
                        }
                    }

                    if ($assignPlan['is_success']) {
                        return redirect()->route('plans.index')->with('success', __('Plan activated Successfully.'));
                    } else {
                        return redirect()->route('plans.index')->with('error', __($assignPlan['error']));
                    }
                } catch (\Exception $e) {
                    return redirect()->route('plans.index')->with('error', __($e->getMessage()));
                }
            } else {
                return redirect()->back()->with('error', 'Please Enter  Valid Shop Id Key');
            }
        }
    }

    public function invoicePayWithYookassa(Request $request)
    {
        $invoice_id = decrypt($request->invoice_id);

        $invoice = Invoice::find($invoice_id);

        $payment_setting = Utility::getCompanyPaymentSetting($invoice->created_by);

        $yookassa_shop_id = $payment_setting['yookassa_shop_id'];
        $yookassa_secret_key = $payment_setting['yookassa_secret'];
        $currency = isset($payment_setting['site_currency']) ? $payment_setting['site_currency'] : 'RUB';
        $get_amount = $request->amount;

        try {
            if ($invoice) {


                if (is_int((int)$yookassa_shop_id)) {
                    $client = new Client();
                    $client->setAuth((int)$yookassa_shop_id, $yookassa_secret_key);
                    $orderID = strtoupper(str_replace('.', '', uniqid('', true)));
                    $payment = $client->createPayment(
                        array(
                            'amount' => array(
                                'value' => $get_amount,
                                'currency' => $currency,
                            ),
                            'confirmation' => array(
                                'type' => 'redirect',
                                'return_url' => route('invoice.yookassa.status', [
                                    'invoice_id' => $invoice->id,
                                    'amount' => $get_amount
                                ]),
                            ),
                            'capture' => true,
                            'description' => 'Заказ №1',
                        ),
                        uniqid('', true)
                    );

                    Session::put('invoice_payment_id', $payment['id']);

                    if ($payment['confirmation']['confirmation_url'] != null) {
                        return redirect($payment['confirmation']['confirmation_url']);
                    } else {
                        return redirect()->route('plans.index')->with('error', 'Something went wrong, Please try again');
                    }
                } else {
                    return redirect()->back()->with('error', 'Please Enter  Valid Shop Id Key');
                }
            } else {
                return redirect()->back()->with('error', 'Invoice not found.');
            }
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', __($e));
        }
    }

    public function getInvociePaymentStatus(Request $request)
    {
        $get_amount = $request->amount;

        $invoice = Invoice::find($request->invoice_id);
        $user = User::where('id', $invoice->created_by)->first();

        $payment_setting = Utility::getCompanyPaymentSetting($user->id);
        $yookassa_shop_id = $payment_setting['yookassa_shop_id'];
        $yookassa_secret_key = $payment_setting['yookassa_secret'];
        $setting = Utility::settingsById($invoice->created_by);

        if (Auth::check()) {
            $settings = \DB::table('settings')->where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('value', 'name');
            $objUser     = \Auth::user();
            $payment_setting = Utility::getCompanyPaymentSettingWithOutAuth($invoice->created_by);
        } else {
            $user = User::where('id', $invoice->created_by)->first();
            $settings = Utility::settingById($invoice->created_by);
            $payment_setting = Utility::getCompanyPaymentSettingWithOutAuth($invoice->created_by);
            $objUser = $user;
        }

        if ($invoice) {
            try {
                if (is_int((int)$yookassa_shop_id)) {
                    $client = new Client();
                    $client->setAuth((int)$yookassa_shop_id, $yookassa_secret_key);
                    $paymentId = Session::get('invoice_payment_id');

                    if ($paymentId == null) {
                        return redirect()->back()->with('error', __('Transaction Unsuccesfull'));
                    }
                    $payment = $client->getPaymentInfo($paymentId);

                    Session::forget('invoice_payment_id');

                    if (isset($payment) && $payment->status == "succeeded") {

                        $user = auth()->user();
                        try {
                            $order_id = strtoupper(str_replace('.', '', uniqid('', true)));
                            $payments = InvoicePayment::create(
                                [

                                    'invoice_id' => $invoice->id,
                                    'date' => date('Y-m-d'),
                                    'amount' => $get_amount,
                                    'account_id' => 0,
                                    'payment_method' => 0,
                                    'order_id' => $order_id,
                                    'currency' => $setting['site_currency'],
                                    'txn_id' => '',
                                    'payment_type' => __('Yookassa'),
                                    'receipt' => '',
                                    'reference' => '',
                                    'description' => 'Invoice ' . Utility::invoiceNumberFormat($settings, $invoice->invoice_id),
                                ]
                            );

                            if ($invoice->getDue() <= 0) {
                                $invoice->status = 4;
                                $invoice->save();
                            } elseif (($invoice->getDue() - $payments->amount) == 0) {
                                $invoice->status = 4;
                                $invoice->save();
                            } elseif ($invoice->getDue() > 0) {
                                $invoice->status = 3;
                                $invoice->save();
                            } else {
                                $invoice->status = 2;
                                $invoice->save();
                            }

                            $invoicePayment              = new \App\Models\Transaction();
                            $invoicePayment->user_id     = $invoice->customer_id;
                            $invoicePayment->user_type   = 'Customer';
                            $invoicePayment->type        = 'Yookassa';
                            $invoicePayment->created_by  = \Auth::check() ? \Auth::user()->id : $invoice->customer_id;
                            $invoicePayment->payment_id  = $invoicePayment->id;
                            $invoicePayment->category    = 'Invoice';
                            $invoicePayment->amount      = $get_amount;
                            $invoicePayment->date        = date('Y-m-d');
                            $invoicePayment->created_by  = \Auth::check() ? \Auth::user()->creatorId() : $invoice->created_by;
                            $invoicePayment->payment_id  = $payments->id;
                            $invoicePayment->description = 'Invoice ' . Utility::invoiceNumberFormat($settings, $invoice->invoice_id);
                            $invoicePayment->account     = 0;

                            \App\Models\Transaction::addTransaction($invoicePayment);

                            Utility::updateUserBalance('customer', $invoice->customer_id, $request->amount, 'debit');

                            Utility::bankAccountBalance($request->account_id, $request->amount, 'credit');

                            //Twilio Notification
                            $setting  = Utility::settingsById($objUser->creatorId());
                            $customer = Customer::find($invoice->customer_id);
                            if (isset($setting['payment_notification']) && $setting['payment_notification'] == 1) {
                                $uArr = [
                                    'invoice_id' => $payments->id,
                                    'payment_name' => $customer->name,
                                    'payment_amount' => $get_amount,
                                    'payment_date' => $objUser->dateFormat($request->date),
                                    'type' => 'Paypal',
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
                                return redirect()->back()->with('success', __(' Payment successfully added.'));
                            } else {
                                return redirect()->back()->with('success', __(' Transaction fail.'));
                            }
                        } catch (\Exception $e) {
                            return redirect()->back()->with('error', __(' Transaction fail.'));
                        }
                    } else {
                        return redirect()->back()->with('error', 'Please Enter  Valid Shop Id Key');
                    }
                }
            } catch (\Exception $e) {
                if (Auth::check()) {
                    return redirect()->back()->with('error', __(' Transaction fail.'));
                } else {
                    return redirect()->route('invoice.show', encrypt($request->invoice_id))->with('success', $e->getMessage());
                    return redirect()->back()->with('success', __(' Payment successfully added.'));
                }
            }
        } else {
            if (Auth::check()) {
                return redirect()->back()->with('error', __('Invoice not found.'));
            } else {
                return redirect()->back()->with('error', __('Invoice not found.'));
            }
        }
    }

    public function retainerPayWithYookassa(Request $request)
    {
        $retainer_id = decrypt($request->retainer_id);

        $retainer = Retainer::find($retainer_id);

        $payment_setting = Utility::getCompanyPaymentSetting($retainer->created_by);

        $yookassa_shop_id = $payment_setting['yookassa_shop_id'];
        $yookassa_secret_key = $payment_setting['yookassa_secret'];
        $currency = isset($payment_setting['site_currency']) ? $payment_setting['site_currency'] : 'RUB';
        $get_amount = $request->amount;

        try {
            if ($retainer) {


                if (is_int((int)$yookassa_shop_id)) {
                    $client = new Client();
                    $client->setAuth((int)$yookassa_shop_id, $yookassa_secret_key);
                    $orderID = strtoupper(str_replace('.', '', uniqid('', true)));
                    $payment = $client->createPayment(
                        array(
                            'amount' => array(
                                'value' => $get_amount,
                                'currency' => $currency,
                            ),
                            'confirmation' => array(
                                'type' => 'redirect',
                                'return_url' => route('retainer.yookassa.status', [
                                    'retainer_id' => $retainer->id,
                                    'amount' => $get_amount
                                ]),
                            ),
                            'capture' => true,
                            'description' => 'Заказ №1',
                        ),
                        uniqid('', true)
                    );

                    Session::put('retainer_payment_id', $payment['id']);

                    if ($payment['confirmation']['confirmation_url'] != null) {
                        return redirect($payment['confirmation']['confirmation_url']);
                    } else {
                        return redirect()->route('plans.index')->with('error', 'Something went wrong, Please try again');
                    }
                } else {
                    return redirect()->back()->with('error', 'Please Enter  Valid Shop Id Key');
                }
            } else {
                return redirect()->back()->with('error', 'Retainer not found.');
            }
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', __($e));
        }
    }

    public function getRetainerPaymentStatus(Request $request)
    {
        $get_amount = $request->amount;

        $retainer = Retainer::find($request->retainer_id);
        $user = User::where('id', $retainer->created_by)->first();

        $payment_setting = Utility::getCompanyPaymentSetting($user->id);
        $yookassa_shop_id = $payment_setting['yookassa_shop_id'];
        $yookassa_secret_key = $payment_setting['yookassa_secret'];
        $setting = Utility::settingsById($retainer->created_by);

        if (Auth::check()) {
            $settings = \DB::table('settings')->where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('value', 'name');
            $objUser     = \Auth::user();
            $payment_setting = Utility::getCompanyPaymentSettingWithOutAuth($retainer->created_by);
         
        } else {
            $user = User::where('id', $retainer->created_by)->first();
            $settings = Utility::settingById($retainer->created_by);
            $payment_setting = Utility::getCompanyPaymentSettingWithOutAuth($retainer->created_by);
         
            $objUser = $user;
        }

        if ($retainer) {
            try {
                if (is_int((int)$yookassa_shop_id)) {
                    $client = new Client();
                    $client->setAuth((int)$yookassa_shop_id, $yookassa_secret_key);
                    $paymentId = Session::get('retainer_payment_id');

                    if ($paymentId == null) {
                        return redirect()->back()->with('error', __('Transaction Unsuccesfull'));
                    }
                    $payment = $client->getPaymentInfo($paymentId);

                    Session::forget('retainer_payment_id');

                    if (isset($payment) && $payment->status == "succeeded") {

                        $user = auth()->user();
                        try {
                            $order_id = strtoupper(str_replace('.', '', uniqid('', true)));
                            $payments = RetainerPayment::create(
                                [
                
                                    'retainer_id' => $retainer->id,
                                    'date' => date('Y-m-d'),
                                    'amount' => $get_amount,
                                    'account_id' => 0,
                                    'payment_method' => 0,
                                    'order_id' => $order_id,
                                    'currency' => $setting['site_currency'],
                                    'txn_id' => '',
                                    'payment_type' => __('Yookassa'),
                                    'receipt' => '',
                                    'reference' => '',
                                    'description' => 'Retainer ' . Utility::retainerNumberFormat($settings, $retainer->retainer_id),
                                ]
                            );
                
                            if ($retainer->getDue() <= 0) {
                                $retainer->status = 4;
                                $retainer->save();
                            } elseif (($retainer->getDue() - $payments->amount) == 0) {
                                $retainer->status = 4;
                                $retainer->save();
                            } else {
                                $retainer->status = 3;
                                $retainer->save();
                            }
                
                            $retainerPayment              = new \App\Models\Transaction();
                            $retainerPayment->user_id     = $retainer->customer_id;
                            $retainerPayment->user_type   = 'Customer';
                            $retainerPayment->type        = 'Yookassa';
                            $retainerPayment->created_by  = \Auth::check() ? \Auth::user()->id : $retainer->customer_id;
                            $retainerPayment->payment_id  = $retainerPayment->id;
                            $retainerPayment->category    = 'Retainer';
                            $retainerPayment->amount      = $get_amount;
                            $retainerPayment->date        = date('Y-m-d');
                            $retainerPayment->created_by  = \Auth::check() ? \Auth::user()->creatorId() : $retainer->created_by;
                            $retainerPayment->payment_id  = $payments->id;
                            $retainerPayment->description = 'Retainer ' . Utility::retainerNumberFormat($settings, $retainer->retainer_id);
                            $retainerPayment->account     = 0;
                
                            \App\Models\Transaction::addTransaction($retainerPayment);

                            Utility::updateUserBalance('customer', $retainer->customer_id, $request->amount, 'debit');

                            Utility::bankAccountBalance($request->account_id, $request->amount, 'credit');

                            //Twilio Notification
                            $setting  = Utility::settingsById($objUser->creatorId());
                            $customer = Customer::find($retainer->customer_id);
                            if (isset($setting['payment_notification']) && $setting['payment_notification'] == 1) {
                                $uArr = [
                                    'retainer_id' => $payments->id,
                                    'payment_name' => $customer->name,
                                    'payment_amount' => $get_amount,
                                    'payment_date' => $objUser->dateFormat($request->date),
                                    'type' => 'Paypal',
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

                            }


                            if (Auth::check()) {
                             
                                return redirect()->back()->with('success', __(' Payment successfully added.'));
                            } else {
                                return redirect()->back()->with('success', __(' Transaction fail.'));
                            }
                        } catch (\Exception $e) {
                            return redirect()->back()->with('error', __(' Transaction fail.'));
                        }
                    } else {
                        return redirect()->back()->with('error', 'Please Enter  Valid Shop Id Key');
                    }
                }
            } catch (\Exception $e) {
                if (Auth::check()) {
                    return redirect()->back()->with('error', __(' Transaction fail.'));
                } else {
                    return redirect()->back()->with('success', __(' Payment successfully added.'));
                }
            }
        } else {
            if (Auth::check()) {
                return redirect()->back()->with('error', __('Retainer not found.'));
            } else {
                return redirect()->back()->with('error', __('Retainer not found.'));
            }
        }
    }
}
