<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\Customer;
use App\Models\Deal;
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
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Session;
use net\authorize\api\contract\v1 as AnetAPI;
use net\authorize\api\controller as AnetController;

class AuthorizeNetController extends Controller
{

    public $secret_key;
    public $is_enabled;

    public function planPayWithAuthorizeNet(Request $request)
    {
        $payment_setting = Utility::getAdminPaymentSetting();
        $currency = isset($payment_setting['currency']) ? $payment_setting['currency'] : 'USD';
        // if ($currency != 'USD') {
        //     return redirect()->route('plans.index')->with('error', __('Your currency is not USD'));
        // }
        $planID    = \Illuminate\Support\Facades\Crypt::decrypt($request->plan_id);
        $plan      = Plan::find($planID);
        $authuser  = Auth::user();
        $coupon_id = '';

        $net               = $plan->price;
        $get_amount        = intval($net);

        $coupon_code       = null;
        $discount_value    = null;
        $coupons           = Coupon::where('code', strtoupper($request->coupon))->where('is_active', '1')->first();
        if ($planID) {
            try {

                if ($coupons) {
                    $coupon_code = $coupons->code;
                    $usedCoupun = $coupons->used_coupon();
                    if ($coupons->limit == $usedCoupun) {
                        $res_data['error'] = __('This coupon code has expired.');
                    } else {
                        $discount_value = ($get_amount / 100) * $coupons->discount;
                        $get_amount = $get_amount - $discount_value;

                        if ($get_amount < 0) {
                            $get_amount = $plan->price;
                        }
                        $coupon_id = $coupons->id;
                    }

                    if ($get_amount <= 0) {
                        $authuser = Auth::user();
                        $authuser->plan = $plan->id;
                        $authuser->save();
                        $assignPlan = $authuser->assignPlan($plan->id, $request->frequency);
                        if ($assignPlan['is_success'] == true && !empty($plan)) {
                            if (!empty($authuser->payment_subscription_id) && $authuser->payment_subscription_id != '') {
                                try {
                                    $authuser->cancel_subscription($authuser->id);
                                } catch (\Exception $exception) {
                                    \Log::debug($exception->getMessage());
                                }
                            }
                            $orderID               = strtoupper(str_replace('.', '', uniqid('', true)));
                            $userCoupon            = new UserCoupon();
                            $userCoupon->user      = $authuser->id;
                            $userCoupon->coupon    = $coupons->id;
                            $userCoupon->order     = $orderID;
                            $userCoupon->save();
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
                                    'price' => $price == null ? 0 : $price,
                                    'price_currency' => $currency,
                                    'txn_id' => '',
                                    'payment_type' => 'Razorpay',
                                    'payment_status' => 'success',
                                    'receipt' => null,
                                    'user_id' => $authuser->id,
                                ]
                            );
                            $assignPlan = $authuser->assignPlan($plan->id);

                            $res['msg']  = __("Plan successfully upgraded.");
                            $res['flag'] = 2;

                            return $res;
                        } else {
                            return Utility::error_res(__('Plan fail to upgrade.'));
                        }
                    }
                }
                $data = [
                    'id' =>  $plan->id,
                    'amount' =>  $get_amount,
                    'coupon_code' =>  $request->coupon_code,
                ];
                $data  =    json_encode($data);
                    return view('AuthorizeNet.request', compact('plan', 'get_amount', 'data', 'currency'));
            } catch (\Exception $e) {
                return redirect()->route('plans.index')->with('error', __($e->getMessage()));
            }
        } else {
            return Utility::error_res(__('Plan is deleted.'));
        }
    }

    public function planGetAuthorizeNetStatus(Request $request)
    {
        $input          = $request->all();
        $admin_settings = Utility::getAdminPaymentSetting();
        $data           = json_decode($input['data'], true);
        $amount         =  $data['amount'];
        $plan           = Plan::find($data['id']);
        $authuser       = Auth::user();
        $orderID        = strtoupper(str_replace('.', '', uniqid('', true)));
        $admin_currancy = !empty($admin_settings['defult_currancy']) ? $admin_settings['defult_currancy'] : 'USD';
        try {
            $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
            $merchantAuthentication->setName($admin_settings['authorizenet_merchant_login_id']);
            $merchantAuthentication->setTransactionKey($admin_settings['authorizenet_merchant_transaction_key']);
            $refId                  = 'ref' . time();
            // Create the payment data for a credit card
            $creditCard = new AnetAPI\CreditCardType();
            $creditCard->setCardNumber($input['cardNumber']);
            $creditCard->setExpirationDate($input['year'] . '-' . $input['month']);
            $creditCard->setCardCode($input['cvv']);

            $paymentOne             = new AnetAPI\PaymentType();
            $paymentOne->setCreditCard($creditCard);
            // Create a TransactionRequestType object and add the previous objects to it
            $transactionRequestType = new AnetAPI\TransactionRequestType();
            $transactionRequestType->setTransactionType("authCaptureTransaction");
            $transactionRequestType->setAmount($amount);
            $transactionRequestType->setPayment($paymentOne);
            // Assemble the complete transaction request
            $requestNet             = new AnetAPI\CreateTransactionRequest();
            $requestNet->setMerchantAuthentication($merchantAuthentication);
            $requestNet->setRefId($refId);
            $requestNet->setTransactionRequest($transactionRequestType);
        } catch (\Exception $e) {
            return redirect()->route('plans.index')->with('error', __('something Went wrong!'));
        }
        $controller = new AnetController\CreateTransactionController($requestNet);
        if (!empty($admin_settings['authorizenet_mode']) && $admin_settings['authorizenet_mode'] == 'live') {

            $response   = $controller->executeWithApiResponse(\net\authorize\api\constants\ANetEnvironment::PRODUCTION); // change SANDBOX to PRODUCTION in live mode

        } else {

            $response   = $controller->executeWithApiResponse(\net\authorize\api\constants\ANetEnvironment::SANDBOX); // change SANDBOX to PRODUCTION in live mode
        }

        if ($response != null) {
            if ($response->getMessages()->getResultCode() == "Ok") {
                $tresponse = $response->getTransactionResponse();
                if ($tresponse != null && $tresponse->getMessages() != null) {
                    Order::create(
                        [
                            'order_id' => $orderID,
                            'name' => $authuser->name ?? '',
                            'email' => $authuser->email ?? '',
                            'card_number' => null,
                            'card_exp_month' => null,
                            'card_exp_year' => null,
                            'plan_name' => $plan->name,
                            'plan_id' => $plan->id,
                            'price' => $amount == null ? 0 : $amount,
                            'price_currency' => $admin_currancy,
                            'txn_id' => '',
                            'payment_type' => __('Authorizenet'),
                            'payment_status' => 'Success',
                            'receipt' => null,
                            'user_id' => $authuser->id,
                        ]
                    );

                    if (isset($input['coupon_code']) && $input['coupon_code']) {
                        $coupons = Coupon::where('id', $request->coupon_id)->where('is_active', '1')->first();
                        if (!empty($coupons)) {
                            $userCoupon = new UserCoupon();
                            $userCoupon->user = $authuser->id;
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
                    $assignPlan         = $authuser->assignPlan($plan->id);

                    if ($assignPlan['is_success']) {
                        return redirect()->route('plans.index')->with('success', __('Plan activated Successfully!'));
                    } else {
                        return redirect()->route('plans.index')->with('error', __($assignPlan['error']));
                    }
                    if ($tresponse->getErrors() != null) {
                        return redirect()->route('plans.index')->with('error', __('Transaction Failed!'));
                    }
                }
            } else {
                $tresponse      = $response->getTransactionResponse();
                if ($tresponse != null && $tresponse->getErrors() != null) {
                    return redirect()->route('plans.index')->with('error', __('Transaction Failed!'));
                } else {
                    return redirect()->route('plans.index')->with('error', __('No reponse returned!'));
                }
            }
        } else {
            return redirect()->route('plans.index')->with('error', __('No reponse returned!'));
        }
    }

    public function invoicePayWithAuthorizeNet(Request $request)
    {
        $invoice_id = \Illuminate\Support\Facades\Crypt::decrypt($request->invoice_id);
        $invoice = Invoice::find($invoice_id);
        $getAmount = $request->amount;
        if (\Auth::check()) {
            $user = \Auth::user();
        } else {
            $user = User::where('id', $invoice->created_by)->first();
        }

        $authuser = User::where('id', $user->id)->first();
        $payment_setting = Utility::getCompanyPaymentSetting($user->id);
        $merchant_login_id = $payment_setting['authorizenet_merchant_login_id'];
        $merchant_transaction_key = $payment_setting['authorizenet_merchant_transaction_key'];
        $currency = isset($payment_setting['currency']) ? $payment_setting['currency'] : 'USD';
        $get_amount = round($request->amount);
        $orderID = strtoupper(str_replace('.', '', uniqid('', true)));

        if ($invoice) {
            $invoiceID = $request->invoice_id;
            $get_amount = $request->amount;
            $type = $request->type;
            $authuser = User::where('id', $user->id)->first();
            $data = [
                'invoiceID'     =>  $invoiceID,
                'user_id'       =>  $user,
                'get_amount'    =>  $get_amount,
                'type'          =>  $type,
                'authuser'      =>  $authuser,
            ];

            $data  =    json_encode($data);

            return view('AuthorizeNet.invoice', compact('invoice', 'get_amount', 'authuser', 'data', 'currency'));
        } else {
            return redirect()->back()->with('error', 'Invoice not found.');
        }
    }

    public function getInvoicePaymentStatus(Request $request)
    {
        $input          = $request->all();
        $data           = json_decode($input['data'], true);
        $invoice_id     =   Crypt::decrypt($data['invoiceID']);
        $amount         =   $data['get_amount'];
        $type           =   $data['type'];
        $invoice        = Invoice::find($invoice_id);

        if (Auth::check()) {
            $user = Auth::user();
        } else {
            $user = User::where('id', $invoice->created_by)->first();
        }
        $orderID = strtoupper(str_replace('.', '', uniqid('', true)));
        $settings= Utility::settingsById($invoice->created_by);
        $company_payment_setting = Utility::getCompanyPaymentSetting($user->id);
        $get_amount = $request->get_amount;

            $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
            $merchantAuthentication->setName($company_payment_setting['authorizenet_merchant_login_id']);
            $merchantAuthentication->setTransactionKey($company_payment_setting['authorizenet_merchant_transaction_key']);
            $refId                  = 'ref' . time();
            // Create the payment data for a credit card
            $creditCard = new AnetAPI\CreditCardType();
            $creditCard->setCardNumber($input['cardNumber']);
            $creditCard->setExpirationDate($input['year'] . '-' . $input['month']);
            $creditCard->setCardCode($input['cvv']);

            $paymentOne             = new AnetAPI\PaymentType();
            $paymentOne->setCreditCard($creditCard);
            // Create a TransactionRequestType object and add the previous objects to it
            $transactionRequestType = new AnetAPI\TransactionRequestType();
            $transactionRequestType->setTransactionType("authCaptureTransaction");
            $transactionRequestType->setAmount($amount);
            $transactionRequestType->setPayment($paymentOne);
            // Assemble the complete transaction request
            $requestNet             = new AnetAPI\CreateTransactionRequest();
            $requestNet->setMerchantAuthentication($merchantAuthentication);
            $requestNet->setRefId($refId);
            $requestNet->setTransactionRequest($transactionRequestType);


            $controller = new AnetController\CreateTransactionController($requestNet);
            if (!empty($company_payment_setting['authorizenet_mode']) && $company_payment_setting['authorizenet_mode'] == 'live') {

                $response   = $controller->executeWithApiResponse(\net\authorize\api\constants\ANetEnvironment::PRODUCTION); // change SANDBOX to PRODUCTION in live mode

            } else {

                $response   = $controller->executeWithApiResponse(\net\authorize\api\constants\ANetEnvironment::SANDBOX); // change SANDBOX to PRODUCTION in live mode
            }

        if ($response != null) {
            if ($response->getMessages()->getResultCode() == "Ok") {
                $tresponse = $response->getTransactionResponse();
                if ($tresponse != null && $tresponse->getMessages() != null) {

                    if (!empty($invoice_id)) {
                        $invoice        =  Invoice::find($invoice_id);

                        $invoice_payment                 = new InvoicePayment();
                        $invoice_payment->invoice_id     = $invoice_id;
                        $invoice_payment->date           = Date('Y-m-d');
                        $invoice_payment->amount         = $amount;
                        $invoice_payment->account_id         = 0;
                        $invoice_payment->payment_method         = 0;
                        $invoice_payment->order_id      =$orderID;
                        $invoice_payment->payment_type   = 'AuthorizeNet';
                        $invoice_payment->receipt     = '';
                        $invoice_payment->reference     = '';
                        $invoice_payment->description     = 'Invoice ' . Utility::invoiceNumberFormat($settings, $invoice->invoice_id);
                        $invoice_payment->save();

                        if($invoice->getDue() <= 0)
                        {
                            $invoice->status = 4;
                            $invoice->save();
                        }
                        elseif(($invoice->getDue() - $invoice_payment->amount) == 0)
                        {
                            $invoice->status = 4;
                            $invoice->save();
                        }
                        else
                        {
                            $invoice->status = 3;
                            $invoice->save();
                        }

                        //for customer balance update
                        Utility::updateUserBalance('customer', $invoice->customer_id, $request->amount, 'debit');

                        //For Notification
                        $setting  = Utility::settingsById($invoice->created_by);
                        $customer = Customer::find($invoice->customer_id);
                        $notificationArr = [
                                'payment_price' => $request->amount,
                                'invoice_payment_type' => 'Aamarpay',
                                'customer_name' => $customer->name,
                            ];
                        //Slack Notification
                        if(isset($settings['payment_notification']) && $settings['payment_notification'] ==1)
                        {
                            Utility::send_slack_msg('new_invoice_payment', $notificationArr,$invoice->created_by);
                        }
                        //Telegram Notification
                        if(isset($settings['telegram_payment_notification']) && $settings['telegram_payment_notification'] == 1)
                        {
                            Utility::send_telegram_msg('new_invoice_payment', $notificationArr,$invoice->created_by);
                        }
                        //Twilio Notification
                        if(isset($settings['twilio_payment_notification']) && $settings['twilio_payment_notification'] ==1)
                        {
                            Utility::send_twilio_msg($customer->contact,'new_invoice_payment', $notificationArr,$invoice->created_by);
                        }
                        //webhook
                        $module ='New Invoice Payment';
                        $webhook=  Utility::webhookSetting($module,$invoice->created_by);
                        if($webhook)
                        {
                            $parameter = json_encode($invoice_payment);
                            $status = Utility::WebhookCall($webhook['url'],$parameter,$webhook['method']);
                            if($status == true)
                            {
                                return redirect()->route('invoice.link.copy', \Crypt::encrypt($invoice->id))->with('error', __('Transaction has been failed.'));
                            }
                            else
                            {
                                return redirect()->back()->with('error', __('Webhook call failed.'));
                            }
                        }

                        return redirect()->route('pay.invoice', Crypt::encrypt($invoice_id))->with('success', __('Invoice paid Successfully!'));
                    } else {
                        return redirect()->route('pay.invoice', Crypt::encrypt($invoice_id))->with('error', __('Oops something went wrong.'));
                    }
                }
            } else {
                $tresponse      = $response->getTransactionResponse();
                if ($tresponse != null && $tresponse->getErrors() != null) {
                    return redirect()->route('pay.invoice', Crypt::encrypt($invoice_id))->with('error', __('Transaction Failed!'));
                } else {
                    return redirect()->route('pay.invoice', Crypt::encrypt($invoice_id))->with('error', __('No reponse returned!'));
                }
            }
        } else {
            return redirect()->route('pay.invoice', Crypt::encrypt($invoice_id))->with('error', __('No reponse returned!'));
        }
    }

    public function retainerPayWithAuthorizeNet(Request $request)
    {
        $retainer_id = Crypt::decrypt($request->retainer_id);
        $retainer = Retainer::find($retainer_id);
        $getAmount = $request->amount;
        if (Auth::check()) {
            $user = Auth::user();
        } else {
            $user = User::where('id', $retainer->created_by)->first();
        }

        $authuser = User::where('id', $user->id)->first();
        $payment_setting = Utility::getCompanyPaymentSetting($user->id);
        $merchant_login_id = $payment_setting['authorizenet_merchant_login_id'];
        $merchant_transaction_key = $payment_setting['authorizenet_merchant_transaction_key'];
        $currency = isset($payment_setting['currency']) ? $payment_setting['currency'] : 'USD';
        $get_amount = round($request->amount);
        $orderID = strtoupper(str_replace('.', '', uniqid('', true)));

        if ($retainer) {
            $retainerID = $request->retainer_id;
            $get_amount = $request->amount;
            $type = $request->type;
            $authuser = User::where('id', $user->id)->first();
            $data = [
                'retainerID'     =>  $retainerID,
                'user_id'       =>  $user,
                'get_amount'    =>  $get_amount,
                'type'          =>  $type,
                'authuser'      =>  $authuser,
            ];

            $data  =    json_encode($data);

                return view('AuthorizeNet.retainer', compact('retainer', 'get_amount', 'authuser', 'data', 'currency'));
        } else {
            return redirect()->back()->with('error', 'Retainer not found.');
        }
    }

    public function getRetainerPaymentStatus(Request $request)
    {
        $input          = $request->all();
        $data           = json_decode($input['data'], true);
        $retainer_id     =   \Illuminate\Support\Facades\Crypt::decrypt($data['retainerID']);
        $amount         =   $data['get_amount'];
        $type           =   $data['type'];
        $retainer        = Invoice::find($retainer_id);

        if (Auth::check()) {
            $user = Auth::user();
        } else {
            $user = User::where('id', $retainer->created_by)->first();
        }
        $orderID = strtoupper(str_replace('.', '', uniqid('', true)));
        $settings= Utility::settingsById($retainer->created_by);
        $company_payment_setting = Utility::getCompanyPaymentSetting($user->id);
        $get_amount = $request->get_amount;

            $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
            $merchantAuthentication->setName($company_payment_setting['authorizenet_merchant_login_id']);
            $merchantAuthentication->setTransactionKey($company_payment_setting['authorizenet_merchant_transaction_key']);
            $refId                  = 'ref' . time();
            // Create the payment data for a credit card
            $creditCard = new AnetAPI\CreditCardType();
            $creditCard->setCardNumber($input['cardNumber']);
            $creditCard->setExpirationDate($input['year'] . '-' . $input['month']);
            $creditCard->setCardCode($input['cvv']);

            $paymentOne             = new AnetAPI\PaymentType();
            $paymentOne->setCreditCard($creditCard);
            // Create a TransactionRequestType object and add the previous objects to it
            $transactionRequestType = new AnetAPI\TransactionRequestType();
            $transactionRequestType->setTransactionType("authCaptureTransaction");
            $transactionRequestType->setAmount($amount);
            $transactionRequestType->setPayment($paymentOne);
            // Assemble the complete transaction request
            $requestNet             = new AnetAPI\CreateTransactionRequest();
            $requestNet->setMerchantAuthentication($merchantAuthentication);
            $requestNet->setRefId($refId);
            $requestNet->setTransactionRequest($transactionRequestType);


        $controller = new AnetController\CreateTransactionController($requestNet);
        if (!empty($company_payment_setting['authorizenet_mode']) && $company_payment_setting['authorizenet_mode'] == 'live') {

            $response   = $controller->executeWithApiResponse(\net\authorize\api\constants\ANetEnvironment::PRODUCTION); // change SANDBOX to PRODUCTION in live mode

        } else {

            $response   = $controller->executeWithApiResponse(\net\authorize\api\constants\ANetEnvironment::SANDBOX); // change SANDBOX to PRODUCTION in live mode
        }
        if ($response != null) {
            if ($response->getMessages()->getResultCode() == "Ok") {
                $tresponse = $response->getTransactionResponse();
                if ($tresponse != null && $tresponse->getMessages() != null) {

                    if (!empty($retainer_id)) {
                        $retainer        =  Retainer::find($retainer_id);

                        $retainer_payment                 = new RetainerPayment();
                        $retainer_payment->retainer_id     = $retainer_id;
                        $retainer_payment->date           = Date('Y-m-d');
                        $retainer_payment->amount         = $amount;
                        $retainer_payment->account_id         = 0;
                        $retainer_payment->payment_method         = 0;
                        $retainer_payment->order_id      =$orderID;
                        $retainer_payment->payment_type   = 'Tap';
                        $retainer_payment->receipt     = '';
                        $retainer_payment->reference     = '';
                        $retainer_payment->description     = 'Retainer ' . Utility::retainerNumberFormat($settings, $retainer->retainer_id);
                        $retainer_payment->save();

                        if($retainer->getDue() <= 0)
                        {
                            $retainer->status = 4;
                            $retainer->save();
                        }
                        elseif(($retainer->getDue() - $retainer_payment->amount) == 0)
                        {
                            $retainer->status = 4;
                            $retainer->save();
                        }
                        else
                        {
                            $retainer->status = 3;
                            $retainer->save();
                        }
                        //for customer balance update
                        Utility::updateUserBalance('customer', $retainer->customer_id, $request->amount, 'debit');

                        //For Notification
                        $setting  = Utility::settingsById($retainer->created_by);
                        $customer = Customer::find($retainer->customer_id);
                        $notificationArr = [
                                'payment_price' => $request->amount,
                                'retainer_payment_type' => 'Aamarpay',
                                'customer_name' => $customer->name,
                            ];
                        //Slack Notification
                        if(isset($settings['payment_notification']) && $settings['payment_notification'] ==1)
                        {
                            Utility::send_slack_msg('new_retainer_payment', $notificationArr,$retainer->created_by);
                        }
                        //Telegram Notification
                        if(isset($settings['telegram_payment_notification']) && $settings['telegram_payment_notification'] == 1)
                        {
                            Utility::send_telegram_msg('new_retainer_payment', $notificationArr,$retainer->created_by);
                        }
                        //Twilio Notification
                        if(isset($settings['twilio_payment_notification']) && $settings['twilio_payment_notification'] ==1)
                        {
                            Utility::send_twilio_msg($customer->contact,'new_retainer_payment', $notificationArr,$retainer->created_by);
                        }
                        //webhook
                        $module ='New retainer Payment';
                        $webhook=  Utility::webhookSetting($module,$retainer->created_by);
                        if($webhook)
                        {
                            $parameter = json_encode($retainer_payment);
                            $status = Utility::WebhookCall($webhook['url'],$parameter,$webhook['method']);
                            if($status == true)
                            {
                                return redirect()->route('retainer.link.copy', \Crypt::encrypt($retainer->id))->with('error', __('Transaction has been failed.'));
                            }
                            else
                            {
                                return redirect()->back()->with('error', __('Webhook call failed.'));
                            }
                        }

                        return redirect()->route('pay.retainerpay', \Crypt::encrypt($retainer_id))->with('success', __('Retainer paid Successfully!'));
                    } else {
                        return redirect()->route('pay.retainerpay', \Crypt::encrypt($retainer_id))->with('error', __('Oops something went wrong.'));
                    }
                }
            } else {
                $tresponse      = $response->getTransactionResponse();
                if ($tresponse != null && $tresponse->getErrors() != null) {
                    return redirect()->route('pay.retainerpay', \Crypt::encrypt($retainer_id))->with('error', __('Transaction Failed!'));
                } else {
                    return redirect()->route('pay.retainerpay', \Crypt::encrypt($retainer_id))->with('error', __('No reponse returned!'));
                }
            }
        } else {
            return redirect()->route('pay.retainerpay', Crypt::encrypt($retainer_id))->with('error', __('No reponse returned!'));
        }
    }
}
