<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    PaypalController, StripePaymentController, PaystackPaymentController, FlutterwavePaymentController,
    RazorpayPaymentController, PaytmPaymentController, MercadoPaymentController, MolliePaymentController,
    SkrillPaymentController, CoingatePaymentController, PaymentWallPaymentController,
    IyziPayController, SspayController, PaytabController, BenefitPaymentController, CashfreeController,
    AamarpayController, PaytrController, YooKassaController, XenditPaymentController, MidtransController,
    PayHereController, NepalstePaymnetController, FedapayController, CinetPayController, TapPaymentController,
    AuthorizeNetController, KhaltiPaymentController, OzowPaymentController, PaiementProController, DashboardController,BankController
};

// ----------------------- Inside verified: Plan gateways & orders -----------------------
Route::group(['middleware' => ['verified']], function () {

    // Paystack
    Route::post('/plan-pay-with-paystack', [PaystackPaymentController::class, 'planPayWithPaystack'])->name('plan.pay.with.paystack')->middleware(['auth','XSS']);
    Route::get('/plan/paystack/{pay_id}/{plan_id}', [PaystackPaymentController::class, 'getPaymentStatus'])->name('plan.paystack')->middleware(['auth','XSS']);

    // Flutterwave
    Route::post('/plan-pay-with-flaterwave', [FlutterwavePaymentController::class, 'planPayWithFlutterwave'])->name('plan.pay.with.flaterwave')->middleware(['auth','XSS']);
    Route::get('/plan/flaterwave/{txref}/{plan_id}', [FlutterwavePaymentController::class, 'getPaymentStatus'])->name('plan.flaterwave')->middleware(['auth','XSS']);

    // Razorpay
    Route::post('/plan-pay-with-razorpay', [RazorpayPaymentController::class, 'planPayWithRazorpay'])->name('plan.pay.with.razorpay')->middleware(['auth','XSS']);
    Route::get('/plan/razorpay/{txref}/{plan_id}', [RazorpayPaymentController::class, 'getPaymentStatus'])->name('plan.razorpay')->middleware(['auth','XSS']);

    // Paytm
    Route::post('/plan-pay-with-paytm', [PaytmPaymentController::class, 'planPayWithPaytm'])->name('plan.pay.with.paytm')->middleware(['auth','XSS']);
    Route::post('/plan/paytm/{plan}/{coupon?}', [PaytmPaymentController::class, 'getPaymentStatus'])->name('plan.paytm')->middleware(['auth','XSS']);

    // Mercado
    Route::post('/plan-pay-with-mercado', [MercadoPaymentController::class, 'planPayWithMercado'])->name('plan.pay.with.mercado')->middleware(['auth','XSS']);
    Route::get('/plan/mercado/{plan}', [MercadoPaymentController::class, 'getPaymentStatus'])->name('plan.mercado')->middleware(['auth','XSS']);

    // Mollie
    Route::post('/plan-pay-with-mollie', [MolliePaymentController::class, 'planPayWithMollie'])->name('plan.pay.with.mollie')->middleware(['auth','XSS']);
    Route::get('/plan/mollie/{plan}', [MolliePaymentController::class, 'getPaymentStatus'])->name('plan.mollie')->middleware(['auth','XSS']);

    // Skrill
    Route::post('/plan-pay-with-skrill', [SkrillPaymentController::class, 'planPayWithSkrill'])->name('plan.pay.with.skrill')->middleware(['auth','XSS']);
    Route::get('/plan/skrill/{plan}', [SkrillPaymentController::class, 'getPaymentStatus'])->name('plan.skrill')->middleware(['auth','XSS']);

    // Coingate
    Route::post('/plan-pay-with-coingate', [CoingatePaymentController::class, 'planPayWithCoingate'])->name('plan.pay.with.coingate')->middleware(['auth','XSS']);
    Route::get('/plan/coingate/{plan}/{coupons_id}', [CoingatePaymentController::class, 'getPaymentStatus'])->name('plan.coingate')->middleware(['auth','XSS']);

    // Iyzipay (plan)
    Route::post('iyzipay/prepare', [IyziPayController::class, 'initiatePayment'])->name('iyzipay.payment.init');
    Route::post('iyzipay/callback/plan/{id}/{amount}/{coupan_code?}', [IyzipayController::class, 'iyzipayCallback'])->name('iyzipay.payment.callback');

    // SSPay
    Route::post('/sspay', [SspayController::class, 'SspayPaymentPrepare'])->name('plan.sspaypayment');
    Route::get('sspay-payment-plan/{plan_id}/{amount}/{couponCode}', [SspayController::class, 'SspayPlanGetPayment'])->middleware(['auth'])->name('plan.sspay.callback');

    // Paytab
    Route::post('plan-pay-with-paytab', [PaytabController::class, 'planPayWithpaytab'])->middleware(['auth'])->name('plan.pay.with.paytab');
    Route::any('paytab-success/plan', [PaytabController::class, 'PaytabGetPayment'])->middleware(['auth'])->name('plan.paytab.success');

    // Benefit
    Route::any('/payment/benefit', [BenefitPaymentController::class, 'planPayWithbenefit'])->name('plan.pay.with.benefit');
    Route::any('call_back', [BenefitPaymentController::class, 'benefitPlanGetPayment'])->name('plan.benefit.call_back');

    // Cashfree
    Route::post('plan/cashfree/payments/', [CashfreeController::class, 'plancashfreePayment'])->name('plan.pay.with.cashfree');
    Route::any('cashfree/payments/success', [CashfreeController::class, 'cashfreePaymentSuccess'])->name('cashfreePayment.success');

    // Aamarpay
    Route::post('/aamarpay/payment', [AamarpayController::class, 'aamarpaywithplan'])->name('pay.aamarpay.payment');
    Route::any('/aamarpay/success/{data}', [AamarpayController::class, 'aamarpaysuccess'])->name('pay.aamarpay.success');

    // PayTR
    Route::post('/paytr/payment', [PaytrController::class, 'PlanpayWithPaytr'])->name('pay.paytr.payment');
    Route::any('/paytr/success', [PaytrController::class, 'paytrsuccess'])->name('pay.paytr.success');

    // YooKassa
    Route::post('/plan/yookassa/payment', [YooKassaController::class, 'planPayWithYooKassa'])->name('plan.pay.with.yookassa');
    Route::get('/plan/yookassa/{plan}', [YooKassaController::class, 'planGetYooKassaStatus'])->name('plan.get.yookassa.status');

    // Xendit
    Route::any('/xendit/payment', [XenditPaymentController::class, 'planPayWithXendit'])->name('plan.xendit.payment');
    Route::any('/xendit/payment/status', [XenditPaymentController::class, 'planGetXenditStatus'])->name('plan.xendit.status');

    // Midtrans
    Route::any('/midtrans', [MidtransController::class, 'planPayWithMidtrans'])->name('plan.get.midtrans');
    Route::any('/midtrans/callback', [MidtransController::class, 'planGetMidtransStatus'])->name('plan.get.midtrans.status');

    // Orders via Stripe + refunds
    Route::group(['middleware' => ['auth','XSS','revalidate']], function () {
        Route::get('order', [StripePaymentController::class, 'index'])->name('order.index');
        Route::get('/refund/{id}/{user_id}', [StripePaymentController::class, 'refund'])->name('order.refund');
        Route::get('/stripe/{code}', [StripePaymentController::class, 'stripe'])->name('stripe');
        Route::post('/stripe', [StripePaymentController::class, 'stripePost'])->name('stripe.post');
    });

    // PayPal plan
    Route::post('plan-pay-with-paypal', [PaypalController::class, 'planPayWithPaypal'])->name('plan.pay.with.paypal')->middleware(['auth','XSS','revalidate']);
    Route::get('{id}/plan-get-payment-status/{amount}', [PaypalController::class, 'planGetPaymentStatus'])->name('plan.get.payment.status')->middleware(['auth','XSS','revalidate']);

    // PaiementPro plan
    Route::any('plan-paiementpro-payment', [PaiementProController::class, 'planPayWithPaiementpro'])->name('plan.pay.with.paiementpro')->middleware(['auth','XSS']);
    Route::any('/plan-paiementpro-status/{plan_id}', [PaiementProController::class, 'planGetPaiementproStatus'])->name('plan.paiementpro.status')->middleware(['auth','XSS']);

    // Nepalste plan
    Route::post('plan-nepalste-payment/', [NepalstePaymnetController::class, 'planPayWithNepalste'])->name('plan.pay.with.nepalste')->middleware(['auth','XSS']);
    Route::get('plan-nepalste-status/', [NepalstePaymnetController::class, 'planGetNepalsteStatus'])->name('plan.nepalste.status')->middleware(['auth','XSS']);
    Route::get('plan-nepalste-cancel/', [NepalstePaymnetController::class, 'planGetNepalsteCancel'])->name('plan.nepalste.cancel')->middleware(['auth','XSS']);

    // Cinetpay plan
    Route::any('plan-cinetpay-payment', [CinetPayController::class, 'planPayWithCinetpay'])->name('plan.pay.with.cinetpay')->middleware(['auth','XSS']);
    Route::any('plan-cinetpay-return', [CinetPayController::class, 'planCinetPayReturn'])->name('plan.cinetpay.return')->middleware(['auth']);
    Route::any('plan-cinetpay-notify', [CinetPayController::class, 'planCinetPayNotify'])->name('plan.cinetpay.notify')->middleware(['auth','XSS']);

    // FedaPay plan
    Route::any('plan-fedapay-payment', [FedapayController::class, 'planPayWithFedapay'])->name('plan.pay.with.fedapay')->middleware(['auth','XSS']);
    Route::any('plan-fedapay-status', [FedapayController::class, 'planGetFedapayStatus'])->name('plan.fedapay.status')->middleware(['auth','XSS']);

    // PayHere plan
    Route::any('plan-payhere-payment', [PayHereController::class, 'planPayWithPayHere'])->name('plan.pay.with.payhere')->middleware(['auth','XSS']);
    Route::any('plan-payhere-status', [PayHereController::class, 'planGetPayHereStatus'])->name('plan.payhere.status')->middleware(['auth','XSS']);

    // Tap plan
    Route::post('plan-pay-with-tap', [TapPaymentController::class, 'planPayWithTap'])->name('plan.pay.with.tap');
    Route::any('plan-get-tap-status/{plan_id}', [TapPaymentController::class, 'planGetTapStatus'])->name('plan.get.tap.status');

    // Authorize.Net plan
    Route::post('plan-pay-with-authorizenet', [AuthorizeNetController::class, 'planPayWithAuthorizeNet'])->name('plan.pay.with.authorizenet');
    Route::any('plan-get-authorizenet-status', [AuthorizeNetController::class, 'planGetAuthorizeNetStatus'])->name('plan.get.authorizenet.status');

    // Khalti plan
    Route::post('plan-pay-with-khalti', [KhaltiPaymentController::class, 'planPayWithKhalti'])->name('plan.pay.with.khalti');
    Route::any('plan-get-khalti-status', [KhaltiPaymentController::class, 'planGetKhaltiStatus'])->name('plan.get.khalti.status');

    // Ozow plan
    Route::post('plan-pay-with-ozow', [OzowPaymentController::class, 'planPayWithozow'])->name('plan.pay.with.ozow');
    Route::any('plan-get-ozow-status', [OzowPaymentController::class, 'planGetozowStatus'])->name('plan.get.ozow.status');

    // PaymentWall (plan & invoice helper pages)
    Route::post('/paymentwalls', [PaymentWallPaymentController::class, 'paymentwall'])->name('plan.paymentwallpayment')->middleware(['XSS']);
    Route::post('/plan-pay-with-paymentwall/{plan}', [PaymentWallPaymentController::class, 'planPayWithPaymentWall'])->name('plan.pay.with.paymentwall')->middleware(['XSS']);
    Route::get('/plan/{flag}', [PaymentWallPaymentController::class, 'planeerror'])->name('error.plan.show');

    Route::post('/paymentwall', [PaymentWallPaymentController::class, 'invoicepaymentwall'])->name('invoice.paymentwallpayment')->middleware(['XSS']);
    Route::post('/invoice-pay-with-paymentwall/{plan}', [PaymentWallPaymentController::class, 'planeerror'])->name('invoice.pay.with.paymentwall')->middleware(['XSS']);
    Route::get('/invoices/{flag}/{invoice}', [PaymentWallPaymentController::class, 'invoiceerror'])->name('error.invoice.show');

    Route::get('/retainer/{flag}/{retainer}', [PaymentWallPaymentController::class, 'retainererror'])->name('error.retainer.show')->middleware(['XSS']);
});

// ----------------------- Outside verified: invoice/retainer gateways -----------------------
Route::post('{id}/invoice-with-banktransfer', [\App\Http\Controllers\BankTransferController::class, 'invoicePayWithbank'])->name('invoice.with.banktransfer')->middleware(['XSS','revalidate']);
Route::post('{id}/retainer-with-banktransfer', [\App\Http\Controllers\BankTransferController::class, 'retainerPayWithbank'])->name('retainer.with.banktransfer')->middleware(['XSS','revalidate']);

Route::post('retainer/{id}/payment', [\App\Http\Controllers\StripePaymentController::class, 'addretainerpayment'])->name('retainer.payment')->middleware(['XSS']);
Route::post('/retainer/paytm/{retainer}/{amount}', [PaytmPaymentController::class, 'getRetainerPaymentStatus'])->name('retainer.paytm')->middleware(['XSS']);
Route::get('/retainer/mollie/{invoice}/{amount}', [MolliePaymentController::class, 'getRetainerPaymentStatus'])->name('retainer.mollie')->middleware(['XSS','revalidate']);
Route::get('/retainer/skrill/{retainer}/{amount}', [SkrillPaymentController::class, 'getRetainerPaymentStatus'])->name('retainer.skrill')->middleware(['XSS','revalidate']);
Route::get('/retainer/coingate/{retainer}/{amount}', [CoingatePaymentController::class, 'getRetainerPaymentStatus'])->name('retainer.coingate')->middleware(['XSS','revalidate']);
Route::post('/paymentwall', [PaymentWallPaymentController::class, 'retainerpaymentwall'])->name('retainer.paymentwallpayment')->middleware(['XSS']);
Route::post('/retainer-pay-with-paymentwall/{plan}', [PaymentWallPaymentController::class, 'retainerPayWithPaymentwall'])->name('retainer.pay.with.paymentwall')->middleware(['XSS']);

// Toyyibpay (plan + invoice + retainer)
Route::post('/toyyibpay', [\App\Http\Controllers\ToyyibpayController::class, 'charge'])->name('plan.toyyibpaypayment');
Route::get('/plan-pay-with-toyyibpay/{id}/{status}/{coupon}', [\App\Http\Controllers\ToyyibpayController::class, 'status'])->name('plan.status');
Route::post('{id}/invoice-with-toyyibpay', [\App\Http\Controllers\ToyyibpayController::class, 'invoicepaywithtoyyibpay'])->name('invoice.with.toyyibpay');
Route::get('{id}/invoice-toyyibpay-status/{amount}', [\App\Http\Controllers\ToyyibpayController::class, 'invoicetoyyibpaystatus'])->name('invoice.toyyibpay.status');
Route::post('{id}/pay-with-toyyibpay', [\App\Http\Controllers\ToyyibpayController::class, 'retainerpaywithtoyyibpay'])->name('pay.with.toyyibpay')->middleware(['XSS','revalidate']);
Route::get('{id}/{amount}/get-retainer-payment-status', [\App\Http\Controllers\ToyyibpayController::class, 'retaineroyyibpaystatus'])->name('retainer.toyyibpay')->middleware(['XSS','revalidate']);

// PayFast (plan + invoice + retainer)
Route::post('payfast-plan', [\App\Http\Controllers\PayFastController::class, 'index'])->name('payfast.payment');
Route::get('payfast-plan/{success}', [\App\Http\Controllers\PayFastController::class, 'success'])->name('payfast.payment.success');
Route::post('invoice-with-payfast', [\App\Http\Controllers\PayFastController::class, 'invoicePayWithPayFast'])->name('invoice.with.payfast');
Route::get('invoice-payfast-status/{success}', [\App\Http\Controllers\PayFastController::class, 'invoicepayfaststatus'])->name('invoice.payfast.status');
Route::post('retainer-with-payfast', [\App\Http\Controllers\PayFastController::class, 'retainerPayWithPayFast'])->name('retainer.with.payfast');
Route::get('retainer-payfast-status/{success}', [\App\Http\Controllers\PayFastController::class, 'retainerpayfaststatus'])->name('retainer.payfast.status');

// Iyzipay (invoice + retainer)
Route::post('{id}/invoice-with-iyzipay', [IyzipayController::class, 'invoicePayWithIyziPay'])->name('invoice.with.iyzipay')->middleware(['XSS','revalidate']);
Route::post('invoice/iyzipay/callback/{id}/{amount}', [IyzipayController::class, 'iyzipaypaymentCallback'])->name('iyzipay.callback')->middleware(['XSS','revalidate']);
Route::post('{id}/retainer-with-iyzipay', [IyzipayController::class, 'retainerPayWithIyziPay'])->name('retainer.with.iyzipay')->middleware(['XSS','revalidate']);
Route::post('retainer/iyzipay/callback/{id}/{amount}', [IyzipayController::class, 'retaineriyzipaypaymentCallback'])->name('retainer.iyzipay.callback')->middleware(['XSS','revalidate']);

// SSPay (invoice + retainer)
Route::post('/invoice-pay-with-sspay', [SspayController::class, 'invoicepaywithsspaypay'])->name('invoice.pay.with.sspay');
Route::get('/invoice/sspay/{invoice}/{amount}', [SspayController::class, 'getInvoicePaymentStatus'])->name('invoice.sspay');
Route::post('/retainer-pay-with-sspay', [SspayController::class, 'retainerpaywithsspaypay'])->name('retainer.pay.with.sspay');
Route::get('/retainer/sspay/{retainer}/{amount}', [SspayController::class, 'getRetainerPaymentStatus'])->name('retainer.sspay');

// Paytab (invoice + retainer)
Route::post('pay-with-paytab/{id}', [PaytabController::class, 'invoicePayWithpaytab'])->name('invoice.pay.with.paytab');
Route::any('paytab-success/{invoice}/{amount}', [PaytabController::class, 'PaytabGetPaymentStatus'])->name('invoice.paytab');
Route::post('/retainer-pay-with-paytab/{id}', [PaytabController::class, 'retainerpaywithpaytab'])->name('retainer.pay.with.paytab');
Route::get('retainer-paytab-success/{retainer}/{amount}', [PaytabController::class, 'getRetainerPaymentStatus'])->name('retainer.paytab');

// Benefit (invoice + retainer)
Route::post('pay-with-benefit/{id}', [BenefitPaymentController::class, 'invoicePayWithbenefit'])->name('invoice.pay.with.benefit');
Route::any('benefit-success/{invoice}/{amount}', [BenefitPaymentController::class, 'benefitGetPaymentStatus'])->name('invoice.benefit');
Route::post('/retainer-pay-with-benefit/{id}', [BenefitPaymentController::class, 'retainerpaywithbenefit'])->name('retainer.pay.with.benefit');
Route::get('retainer-benefit-success/{retainer}/{amount}', [BenefitPaymentController::class, 'getRetainerPaymentStatus'])->name('retainer.benefit');

// Cashfree (invoice + retainer)
Route::post('{id}/cashfree/payments/invoice', [CashfreeController::class, 'invoicepayWithCashfree'])->name('invoice.pay.with.cashfree');
Route::any('invoice-cashfree-success', [CashfreeController::class, 'invoiceCashfreePaymentSuccess'])->name('invoice.cashfreePayment.success');
Route::post('{id}/cashfree/payments/retainer', [CashfreeController::class, 'retainerpayWithCashfree'])->name('retainer.pay.with.cashfree');
Route::any('retainer-cashfree-success', [CashfreeController::class, 'retainerCashfreePaymentSuccess'])->name('retainer.cashfreePayment.success');

// Aamarpay (invoice + retainer)
Route::post('{id}/aamarpay/payment', [AamarpayController::class, 'invoicepayWithAamarpay'])->name('invoice.pay.aamarpay.payment');
Route::any('aamarpay/success/invoice/{data}', [AamarpayController::class, 'invoiceAamarpaysuccess'])->name('invoice.pay.aamarpay.success');
Route::post('{id}/aamarpay/payment/retainer', [AamarpayController::class, 'retainerpayWithAamarpay'])->name('retainer.pay.aamarpay.payment');
Route::any('aamarpay/success/retainer/{data}', [AamarpayController::class, 'retainerAamarpaysuccess'])->name('retainer.pay.aamarpay.success');

// PayTR (invoice + retainer)
Route::post('{id}/paytr/payment', [PaytrController::class, 'invoicepayWithPaytr'])->name('invoice.pay.paytr.payment');
Route::any('paytr/success/invoice', [PaytrController::class, 'invoicePaytrsuccess'])->name('invoice.pay.paytr.success');
Route::post('{id}/paytr/payment/retainer', [PaytrController::class, 'retainerpayWithPaytr'])->name('retainer.pay.paytr.payment');
Route::any('paytr/success/retainer', [PaytrController::class, 'retainerPaytrsuccess'])->name('retainer.pay.paytr.success');

// YooKassa (invoice + retainer)
Route::post('invoice-with-yookassa/{id}', [YooKassaController::class, 'invoicePayWithYookassa'])->name('invoice.with.yookassa');
Route::any('invoice-yookassa-status/', [YooKassaController::class, 'getInvociePaymentStatus'])->name('invoice.yookassa.status');
Route::post('retainer-with-yookassa/{id}', [YooKassaController::class, 'retainerPayWithYookassa'])->name('retainer.with.yookassa');
Route::any('retainer-yookassa-status/', [YooKassaController::class, 'getRetainerPaymentStatus'])->name('retainer.yookassa.status');

// Xendit (invoice + retainer)
Route::any('/invoice-with-xendit', [XenditPaymentController::class, 'invoicePayWithXendit'])->name('invoice.with.xendit');
Route::any('/invoice-xendit-status', [XenditPaymentController::class, 'getInvociePaymentStatus'])->name('invoice.xendit.status');
Route::any('/retainer-with-xendit', [XenditPaymentController::class, 'retainerPayWithXendit'])->name('retainer.with.xendit');
Route::any('/retainer-xendit-status', [XenditPaymentController::class, 'getRetainerPaymentStatus'])->name('retainer.xendit.status');

// Midtrans (invoice + retainer)
Route::any('invoice-with-midtrans/', [MidtransController::class, 'invoicePayWithMidtrans'])->name('invoice.with.midtrans');
Route::any('invoice-midtrans-status/', [MidtransController::class, 'getInvociePaymentStatus'])->name('invoice.midtrans.status');
Route::any('retainer-with-midtrans/', [MidtransController::class, 'retainerPayWithMidtrans'])->name('retainer.with.midtrans');
Route::any('retainer-midtrans-status/', [MidtransController::class, 'getRetainerPaymentStatus'])->name('retainer.midtrans.status');

// ----------------------- Invoice payments sections -----------------------

// PaiementPro (invoice)
Route::any('invoice-paiementpro-payment/{id}', [PaiementProController::class, 'invoicePayWithPaiementpro'])->name('invoice.with.paiementpro')->middleware(['XSS']);
Route::any('/invoice-paiementpro-status/{invoice_id}', [PaiementProController::class, 'invoiceGetPaiementproStatus'])->name('invoice.paiementpro.status')->middleware(['XSS']);

// Nepalste (invoice)
Route::post('invoice-nepalste-payment/{id}', [NepalstePaymnetController::class, 'invoicePayWithNepalste'])->name('invoice.with.nepalste')->middleware(['XSS']);
Route::get('invoice-nepalste-status/{id}/{amt?}', [NepalstePaymnetController::class, 'invoiceGetNepalsteStatus'])->name('invoice.nepalste.status')->middleware(['XSS']);
Route::get('invoice-nepalste-cancel/', [NepalstePaymnetController::class, 'invoiceGetNepalsteCancel'])->name('invoice.nepalste.cancel')->middleware(['XSS']);

// Cinetpay (invoice)
Route::any('invoice-cinetpay-payment/{id}', [CinetPayController::class, 'invoicePayWithCinetPay'])->name('invoice.with.cinetpay')->middleware(['XSS']);
Route::any('invoice-cinetpay-return/{id}/{amt?}', [CinetPayController::class, 'invoiceCinetPayReturn'])->name('invoice.cinetpay.return')->middleware(['XSS']);
Route::any('invoice-cinetpay-notify/{id?}', [CinetPayController::class, 'invoiceCinetPayNotify'])->name('invoice.cinetpay.notify')->middleware(['XSS']);

// Fedapay (invoice)
Route::any('invoice-fedapay-payment/{id}', [FedapayController::class, 'invoicePayWithFedapay'])->name('invoice.with.fedapay')->middleware(['XSS']);
Route::any('invoice-fedapay-status/{id}/{amt?}', [FedapayController::class, 'invoiceGetFedapayStatus'])->name('invoice.fedapay.status')->middleware(['XSS']);

// PayHere (invoice)
Route::any('invoice-payhere-payment/{id}', [PayHereController::class, 'invoicePayWithPayHere'])->name('invoice.with.payhere')->middleware(['XSS']);
Route::any('invoice-payhere-status/{id}/{amt?}', [PayHereController::class, 'invoiceGetPayHereStatus'])->name('invoice.payhere.status')->middleware(['XSS']);

// Tap (invoice)
Route::any('invoice-tap-payment', [TapPaymentController::class, 'invoicePayWithTap'])->name('invoice.with.tap')->middleware(['XSS']);
Route::any('invoice-tap-status', [TapPaymentController::class, 'invoiceGetTapStatus'])->name('invoice.tap.status')->middleware(['XSS']);

// Authorize.Net (invoice)
Route::any('/invoice-authorizenet-payment', [AuthorizeNetController::class, 'invoicePayWithAuthorizeNet'])->name('invoice.with.authorizenet');
Route::any('/invoice-get-authorizenet-status', [AuthorizeNetController::class, 'getInvoicePaymentStatus'])->name('invoice.get.authorizenet.status');

// Khalti (invoice)
Route::any('/invoice-khalti-payment', [KhaltiPaymentController::class, 'invoicePayWithKhalti'])->name('invoice.with.khalti');
Route::any('/invoice-get-khalti-status', [KhaltiPaymentController::class, 'getInvoicePaymentStatus'])->name('invoice.get.khalti.status');

// Ozow (invoice)
Route::any('/invoice-ozow-payment', [OzowPaymentController::class, 'invoicePayWithozow'])->name('invoice.with.ozow');
Route::any('/invoice-get-ozow-status/{id}', [OzowPaymentController::class, 'getInvoicePaymentStatus'])->name('invoice.get.ozow.status');

// ----------------------- Retainer payments sections -----------------------

// PaiementPro (retainer)
Route::any('retainer-paiementpro-payment/{id}', [PaiementProController::class, 'retainerPayWithPaiementpro'])->name('retainer.with.paiementpro')->middleware(['XSS']);
Route::any('/retainer-paiementpro-status/{retainer_id}', [PaiementProController::class, 'retainerGetPaiementproStatus'])->name('retainer.paiementpro.status')->middleware(['XSS']);

// Nepalste (retainer)
Route::post('retainer-nepalste-payment/{id}', [NepalstePaymnetController::class, 'retainerPayWithNepalste'])->name('retainer.with.nepalste')->middleware(['XSS']);
Route::get('retainer-nepalste-status/{id}/{amt?}', [NepalstePaymnetController::class, 'retainerGetNepalsteStatus'])->name('retainer.nepalste.status')->middleware(['XSS']);
Route::get('retainer-nepalste-cancel/', [NepalstePaymnetController::class, 'retainerGetNepalsteCancel'])->name('retainer.nepalste.cancel')->middleware(['XSS']);

// Cinetpay (retainer)
Route::any('retainer-cinetpay-payment/{id}', [CinetPayController::class, 'retainerPayWithCinetpay'])->name('retainer.with.cinetpay')->middleware(['XSS']);
Route::any('retainer-cinetpay-return/{id}/{amt?}', [CinetPayController::class, 'retainerCinetPayReturn'])->name('retainer.cinetpay.return')->middleware(['XSS']);
Route::any('retainer-cinetpay-notify/{id?}', [CinetPayController::class, 'retainerCinetPayNotify'])->name('retainer.cinetpay.notify')->middleware(['XSS']);

// FedaPay (retainer)
Route::any('retainer-fedapay-payment/{id}', [FedapayController::class, 'retainerPayWithFedapay'])->name('retainer.with.fedapay')->middleware(['XSS']);
Route::any('retainer-fedapay-status/{id}/{amt?}', [FedapayController::class, 'retainerGetFedapayStatus'])->name('retainer.fedapay.status')->middleware(['XSS']);

// PayHere (retainer)
Route::any('retainer-payhere-payment/{id}', [PayHereController::class, 'retainerPayWithPayHere'])->name('retainer.with.payhere')->middleware(['XSS']);
Route::any('retainer-payhere-status/{id}/{amt?}', [PayHereController::class, 'retainerGetPayHereStatus'])->name('retainer.payhere.status')->middleware(['XSS']);

// Tap (retainer)
Route::any('retainer-tap-payment/', [TapPaymentController::class, 'retainerPayWithTap'])->name('retainer.with.tap')->middleware(['XSS']);
Route::any('retainer-tap-status/', [TapPaymentController::class, 'retainerGetTapStatus'])->name('retainer.tap.status')->middleware(['XSS']);

// Authorize.Net (retainer)
Route::any('/retainer-authorizenet-payment', [AuthorizeNetController::class, 'retainerPayWithAuthorizeNet'])->name('retainer.with.authorizenet');
Route::any('/retainer-get-authorizenet-status', [AuthorizeNetController::class, 'getRetainerPaymentStatus'])->name('retainer.get.authorizenet.status');

// Khalti (retainer)
Route::any('/retainer-khalti-payment', [KhaltiPaymentController::class, 'retainerPayWithKhalti'])->name('retainer.with.khalti');
Route::any('/retainer-get-khalti-status', [KhaltiPaymentController::class, 'getRetainerPaymentStatus'])->name('retainer.get.khalti.status');

// Ozow (retainer)
Route::any('/retainer-ozow-payment', [OzowPaymentController::class, 'retainerPayWithozow'])->name('retainer.with.ozow');
Route::any('/retainer-get-ozow-status/{id}', [OzowPaymentController::class, 'getRetainerPaymentStatus'])->name('retainer.get.ozow.status');

// Duplicate PayPal retainer status (as in source)
Route::get('{id}/{amount}/get-retainer-payment-status', [PaypalController::class, 'customerGetRetainerPaymentStatus'])->name('get.retainer.payment.status')->middleware(['XSS','revalidate']);

// Duplicate dashboard customize (as in source)
Route::post('/dashboard/customize', [DashboardController::class, 'saveCustomization'])->name('dashboard.customize');


    // Exports

   Route::post('branches/bulk-destroy', [BranchController::class, 'bulkDestroy'])
        ->name('branches.bulk-destroy');

    // Export (all / selected)
    Route::get('branches/export', [BranchController::class, 'export'])
        ->name('branches.export');
    Route::post('branches/export-selected', [BranchController::class, 'exportSelected'])
        ->name('branches.export-selected');
Route::resource('branches', BranchController::class)->middleware('auth');
    Route::post('banks/bulk-destroy', [BankController::class, 'bulkDestroy'])
        ->name('banks.bulk-destroy');

    // Export (all / selected)
    Route::get('banks/export', [BankController::class, 'export'])
        ->name('banks.export');
    Route::post('banks/export-selected', [BankController::class, 'exportSelected'])
        ->name('banks.export-selected');
Route::resource('banks', BankController::class)->middleware('auth');
