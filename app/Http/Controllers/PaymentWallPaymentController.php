<?php

namespace App\Http\Controllers;

use App\Models\Utility;
use App\Models\Plan;
use App\Models\Retainer;
use App\Models\RetainerPayment;
use App\Models\User;
use App\Models\Coupon;
use App\Models\UserCoupon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use App\Models\Invoice;
use App\Models\InvoicePayment;
use Illuminate\Http\Request;

class PaymentWallPaymentController extends Controller
{
    public $secret_key;
    public $public_key;
    public $is_enabled;

    public function paymentwall(Request $request)
    {
        $data = $request->all();

        $admin_payment_setting = Utility::getAdminPaymentSetting();

        return view('plan.paymentwall', compact('data', 'admin_payment_setting'));
    }
    public function paymentConfig($user)
    {
        if (Auth::check()) {
            $user = Auth::user();
        }
        if ($user->type == 'company') {
            $payment_setting = Utility::getAdminPaymentSetting();
        } else {
            $payment_setting = Utility::getCompanyPaymentSetting($user);
        }

        $this->secret_key = isset($payment_setting['paymentwall_private_key ']) ? $payment_setting['paymentwall_private_key  '] : '';
        $this->public_key = isset($payment_setting['paymentwall_public_key']) ? $payment_setting['paymentwall_public_key'] : '';
        $this->is_enabled = isset($payment_setting['is_paymentwall_enabled']) ? $payment_setting['is_paymentwall_enabled'] : 'off';

        return $this;
    }

    public function planPayWithPaymentWall(Request $request, $plan_id)
    {
        // dd($plan_id);
        $planID    = \Illuminate\Support\Facades\Crypt::decrypt($plan_id);
        $admin = Utility::getAdminPaymentSetting();

        $plan      = Plan::find($planID);
        $user   = Auth::user();
        $coupon_id = '';
        // dd($plan);
        if ($plan) {
            /* Check for code usage */
            $plan->discounted_price = false;
            $price                  = $plan->price;
            //  dd($price);
            if (isset($request->coupon) && !empty($request->coupon)) {
                $request->coupon = trim($request->coupon);
                $coupons         = Coupon::where('code', strtoupper($request->coupon))->where('is_active', '1')->first();

                if (!empty($coupons)) {
                    $usedCoupun             = $coupons->used_coupon();
                    $discount_value         = ($price / 100) * $coupons->discount;
                    $plan->discounted_price = $price - $discount_value;

                    // if($usedCoupun >= $coupons->limit)
                    // {
                    //     return redirect()->back()->with('error', __('This coupon code has expired.'));
                    // }

                    if ($coupons->limit == $usedCoupun) {
                        return redirect()->back()->with('error', __('This coupon code has expired.'));
                    }

                    $price     = $price - $discount_value;
                    $coupon_id = $coupons->id;
                } else {
                    return redirect()->back()->with('error', __('This coupon code is invalid or has expired.'));
                }
            }
            if ($price <= 0) {
                return Utility::error_res(__('Free plans are not available.'));
            } else {
                //  dd('222222');
                $orderID = time();
                \Paymentwall_Config::getInstance()->set(array(
                    'private_key' => 'sdrsefrszdef'
                ));
                $parameters = $request->all();
                $chargeInfo = array(
                    'email' => $parameters['email'],
                    'history[registration_date]' => '1489655092',
                    'amount' => $price,
                    'currency' => $admin['currency'] ? $admin['currency'] : 'USD',
                    'token' => $parameters['brick_token'],
                    'fingerprint' => $parameters['brick_fingerprint'],
                    'description' => 'Order #123'
                );
                $charge = new \Paymentwall_Charge();
                $charge->create($chargeInfo);
                $responseData = json_decode($charge->getRawResponseData(), true);
                $response = $charge->getPublicData();
                //  dd($response);
                if ($charge->isSuccessful() and empty($responseData['secure'])) {
                    if ($charge->isCaptured()) {
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

                        $user->is_plan_purchased = 1;
                        if ($user->is_trial_done == 1) {
                            $user->is_trial_done = 2;
                            $user->save();
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
                        $order->price          = isset($result['data']['amount']) ? ($result['data']['amount'] / 100) : 0;
                        $order->price_currency = $admin['currency'] ? $admin['currency'] : 'USD';
                        $order->txn_id         = isset($result['data']['id']) ? $result['data']['id'] : $pay_id;
                        $order->payment_type   = 'Paystack';
                        $order->payment_status = 'success';
                        $order->receipt        = '';
                        $order->user_id        = $user->id;
                        $order->save();
                        $assignPlan = $authuser->assignPlan($plan->id);
                        if ($assignPlan['is_success']) {
                            $res['msg'] = __("Plan successfully upgraded.");
                            $res['flag'] = 1;
                            return $res;
                        }
                    } elseif ($charge->isUnderReview()) {
                        // decide on risk charge
                    }
                } elseif (!empty($responseData['secure'])) {
                    $response = json_encode(array('secure' => $responseData['secure']));
                } else {
                    // dd('fddrfxde');
                    $errors = json_decode($response, true);
                    $res['flag'] = 2;
                    return $res;
                }
                echo $response;
            }
        }
    }
    public function planeerror(Request $request, $flag)
    {
        if ($flag == 1) {
            return redirect()->route("plans.index")->with('error', __('Transaction has been Successfull! '));
        } else {

            return redirect()->route("plans.index")->with('error', __('Transaction has been failed! '));
        }
    }

    public function invoicepaymentwall(Request $request)
    {

        $data = $request->all();
        $company_payment_setting = Utility::getCompanyPayment();

        return view('invoice.paymentwall', compact('data', 'company_payment_setting'));
    }

    public function retainerpaymentwall(Request $request)
    {
        $data = $request->all();
        $company_payment_setting = Utility::getCompanyPayment();

        return view('retainer.paymentwall', compact('data', 'company_payment_setting'));
    }

    public function invoiceerror(Request $request, $flag, $invoice_id)
    {

        if ($flag == 1) {
            return redirect()->route('invoice.show', encrypt($invoice_id))->with('error', __('Payment successfully added. '));
        } else {
            return redirect()->route("invoice.show", encrypt($invoice_id))->with('error', __('Transaction has been failed! '));

            // return redirect()->back()->with('error', __('Transaction has been failed! '));
        }
    }

    public function retainererror(Request $request, $flag, $retainer_id)
    {

        if ($flag == 1) {
            return redirect()->route('customer.retainer.show', encrypt($retainer_id))->with('error', __('Payment successfully added. '));
        } else {
            return redirect()->route("customer.retainer.show", encrypt($retainer_id))->with('error', __('Transaction has been failed! '));

            // return redirect()->back()->with('error', __('Transaction has been failed! '));
        }
    }

    public function retainerPayWithPaymentwall(Request $request, $retainerID)
    {

        $retainerID = \Crypt::decrypt($retainerID);
        $retainer   = Retainer::find($retainerID);
        $setting = Utility::settingsById($retainer->created_by);


        if (\Auth::check()) {
            $user = \Auth::user();
        } else {
            $user = User::where('id', $retainer->created_by)->first();
        }

        if ($retainer) {
            $price = $request->amount;

            if ($price < 0) {

                $res_data['email']       = $user->email;
                $res_data['total_price'] = $request->amount;
                $res_data['currency']    = $setting['site_currency'];
                $res_data['flag']        = 1;
                $res_data['retainer_id']  = $retainer->id;

                // return $res_data;

            } else {

                $authuser = Auth::user();
                \Paymentwall_Config::getInstance()->set(array(
                    'private_key' => 'sdrsefrszdef'
                ));
                $parameters = $request->all();

                $chargeInfo = array(
                    'email' => $parameters['email'],
                    'history[registration_date]' => '1489655092',
                    'amount' => $price,
                    'currency' => $setting['site_currency'],
                    'token' => $parameters['brick_token'],
                    'fingerprint' => $parameters['brick_fingerprint'],
                    'description' => 'Order #123'
                );
                $charge = new \Paymentwall_Charge();

                $charge->create($chargeInfo);
                $responseData = json_decode($charge->getRawResponseData(), true);
                $response = $charge->getPublicData();

                if ($charge->isSuccessful() && empty($responseData['secure'])) {
                    if ($charge->isCaptured()) {
                        $retainer_payment                 = new RetainerPayment();
                        $retainer_payment->transaction_id = app('App\Http\Controllers\RetainerController')->transactionNumber();
                        $retainer_payment->retainer_id     = $retainer->id;
                        $retainer_payment->amount         = isset($retainer_data['total_price']) ? $retainer_data['total_price'] : 0;
                        $retainer_payment->date           = date('Y-m-d');
                        $retainer_payment->payment_id     = 0;
                        $retainer_payment->payment_type   = 'Paystack';
                        $retainer_payment->notes          = '';
                        $retainer_payment->client_id      = $user->id;
                        $retainer_payment->save();

                        if (($retainer->getDue() - $retainer_payment->amount) == 0) {
                            Retainer::change_status($retainer->id, 3);
                        } else {
                            Retainer::change_status($retainer->id, 2);
                        }

                        $assignPlan = $authuser->assignPlan($retainer->id);
                        if ($assignPlan['is_success']) {
                            $res['msg'] = __("Invoice successfully .");
                            $res['flag'] = 1;
                            return $res;
                        }
                    } elseif ($charge->isUnderReview()) {
                        // decide on risk charge
                    }
                } elseif (!empty($responseData['secure'])) {
                    $response = json_encode(array('secure' => $responseData['secure']));
                } else {
                    $errors = json_decode($response, true);
                    $res['retainer'] = $retainerID;
                    $res['flag'] = 2;
                    return $res;
                }
                echo $response;
            }
        }
    }


    public function invoicePayWithPaymentwall(Request $request, $invoiceID)
    {

        $invoiceID = \Crypt::decrypt($invoiceID);

        // $res['msg'] = __("error");
        // $res['invoice']=$invoiceID;
        // return $res;
        $invoice   = Invoice::find($invoiceID);
        $setting = Utility::settingsById($invoice->created_by);
        if (\Auth::check()) {
            $user = \Auth::user();
        } else {
            $user = User::where('id', $invoice->created_by)->first();
        }

        if ($invoice) {
            $price = $request->amount;

            if ($price < 0) {
                $res_data['email']       = $user->email;
                $res_data['total_price'] = $request->amount;
                $res_data['currency']    = $setting['site_currency'];
                $res_data['flag']        = 1;
                $res_data['invoice_id']  = $invoice->id;

                // return $res_data;

            } else {
                $authuser = Auth::user();
                \Paymentwall_Config::getInstance()->set(array(
                    'private_key' => 'sdrsefrszdef'
                ));
                $parameters = $request->all();
                $chargeInfo = array(
                    'email' => $parameters['email'],
                    'history[registration_date]' => '1489655092',
                    'amount' => $price,
                    'currency' => $setting['site_currency'],
                    'token' => $parameters['brick_token'],
                    'fingerprint' => $parameters['brick_fingerprint'],
                    'description' => 'Order #123'
                );
                $charge = new \Paymentwall_Charge();
                $charge->create($chargeInfo);
                $responseData = json_decode($charge->getRawResponseData(), true);
                $response = $charge->getPublicData();

                if ($charge->isSuccessful() and empty($responseData['secure'])) {
                    if ($charge->isCaptured()) {
                        $invoice_payment                 = new InvoicePayment();
                        $invoice_payment->transaction_id = app('App\Http\Controllers\InvoiceController')->transactionNumber();
                        $invoice_payment->invoice_id     = $invoice->id;
                        $invoice_payment->amount         = isset($invoice_data['total_price']) ? $invoice_data['total_price'] : 0;
                        $invoice_payment->date           = date('Y-m-d');
                        $invoice_payment->payment_id     = 0;
                        $invoice_payment->payment_type   = 'Paystack';
                        $invoice_payment->notes          = '';
                        $invoice_payment->client_id      = $user->id;
                        $invoice_payment->save();

                        Utility::updateUserBalance('customer', $invoice->customer_id, $request->amount, 'debit');

                        Utility::bankAccountBalance($request->account_id, $request->amount, 'credit');

                        if (($invoice->getDue() - $invoice_payment->amount) == 0) {
                            Invoice::change_status($invoice->id, 3);
                        } else {
                            Invoice::change_status($invoice->id, 2);
                        }

                        $assignPlan = $authuser->assignPlan($invoice->id);
                        if ($assignPlan['is_success']) {
                            $res['msg'] = __("Invoice successfully .");
                            $res['flag'] = 1;
                            return $res;
                        }
                    } elseif ($charge->isUnderReview()) {
                        // decide on risk charge
                    }
                } elseif (!empty($responseData['secure'])) {
                    $response = json_encode(array('secure' => $responseData['secure']));
                } else {
                    $errors = json_decode($response, true);
                    $res['invoice'] = $invoiceID;
                    $res['flag'] = 2;
                    return $res;
                }
                echo $response;
            }
        }
    }
}
