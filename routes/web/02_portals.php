<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    VenderController, CustomerController, RetainerController, InvoiceController, ProposalController,
    PaypalController, StripePaymentController, PaystackPaymentController, FlutterwavePaymentController,
    RazorpayPaymentController, PaytmPaymentController, MercadoPaymentController, MolliePaymentController,
    SkrillPaymentController, CoingatePaymentController, PaymentWallPaymentController,ContractController
};
use App\Http\Controllers\Auth\AuthenticatedSessionController;

Route::middleware(['auth', 'XSS', 'revalidate', '2fa'])->group(function () {
Route::prefix('customer')->as('customer.')->group(function () {
    Route::get('login/{lang}', [AuthenticatedSessionController::class, 'showCustomerLoginLang'])->name('login.lang');
    Route::get('login', [AuthenticatedSessionController::class, 'showCustomerLoginForm'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'customerLogin'])->name('login.store');

    Route::get('/password/resets/{lang?}', [AuthenticatedSessionController::class, 'showCustomerLinkRequestForm'])->name('change.langPass');
    Route::post('/password/email', [AuthenticatedSessionController::class, 'postCustomerEmail'])->name('password.email');

    Route::get('reset-password/{token}', [AuthenticatedSessionController::class, 'getCustomerPassword'])->name('reset.password');
    Route::get('reset-password', [AuthenticatedSessionController::class, 'updateCustomerPassword'])->name('password.reset');

    Route::get('retainer', [RetainerController::class, 'customerRetainer'])->name('retainer')->middleware(['auth:customer']);
    Route::get('retainer/{id}/show', [RetainerController::class, 'customerRetainerShow'])->name('retainer.show')->middleware(['auth:customer']);
    Route::get('retainer/{id}/send', [RetainerController::class, 'customerRetainerSend'])->name('retainer.send')->middleware(['auth:customer']);
    Route::post('retainer/{id}/send/mail', [RetainerController::class, 'customerRetainerSendMail'])->name('retainer.send.mail')->middleware(['auth:customer']);

    Route::get('dashboard', [CustomerController::class, 'dashboard'])->name('dashboard')->middleware(['auth:customer']);

    Route::get('invoice', [InvoiceController::class, 'customerInvoice'])->name('invoice')->middleware(['auth:customer']);
    Route::get('/invoice/pay/{invoice}', [InvoiceController::class, 'payinvoice'])->name('pay.invoice');

    Route::get('proposal', [ProposalController::class, 'customerProposal'])->name('proposal')->middleware(['auth:customer']);
    Route::get('proposal/{id}/show', [ProposalController::class, 'customerProposalShow'])->name('proposal.show')->middleware(['auth:customer']);

    Route::get('invoicesend//{id}', [InvoiceController::class, 'customerInvoiceSend'])->name('invoice.send')->middleware(['auth:customer']);
    Route::post('invoice/{id}/send/mail', [InvoiceController::class, 'customerInvoiceSendMail'])->name('invoice.send.mail')->middleware(['auth:customer']);
    Route::get('invoice/{id}/show', [InvoiceController::class, 'customerInvoiceShow'])->name('invoice.show')->middleware(['auth:customer']);

    Route::get('payment', [CustomerController::class, 'payment'])->name('payment')->middleware(['auth:customer']);
    Route::get('transaction', [CustomerController::class, 'transaction'])->name('transaction')->middleware(['auth:customer']);
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    Route::get('profile', [CustomerController::class, 'profile'])->name('profile')->middleware(['auth:customer']);
    Route::post('update-profile', [CustomerController::class, 'editprofile'])->name('update.profile')->middleware(['auth:customer']);
    Route::post('billing-info', [CustomerController::class, 'editBilling'])->name('update.billing.info')->middleware(['auth:customer']);
    Route::post('shipping-info', [CustomerController::class, 'editShipping'])->name('update.shipping.info')->middleware(['auth:customer']);
    Route::post('change.password', [CustomerController::class, 'updatePassword'])->name('update.password')->middleware(['auth:customer']);
    Route::get('change-language/{lang}', [CustomerController::class, 'changeLanquage'])->name('change.language')->middleware(['auth:customer']);

    Route::resource('contract', ContractController::class)->middleware(['auth:customer']);
    Route::post('contract/{id}/description', [ContractController::class, 'descriptionStore'])->name('contract.description.store')->middleware(['auth:customer']);
    Route::post('contract/{id}/file', [ContractController::class, 'fileUpload'])->name('contract.file.upload')->middleware(['auth:customer']);
    Route::post('/contract/{id}/comment', [ContractController::class, 'commentStore'])->name('comment.store')->middleware(['auth:customer']);
    Route::post('/contract/{id}/note', [ContractController::class, 'noteStore'])->name('contract.note.store')->middleware(['auth:customer']);
    Route::get('contract/pdf/{id}', [ContractController::class, 'pdffromcontract'])->name('contract.download.pdf')->middleware(['auth:customer']);
    Route::get('contract/{id}/get_contract', [ContractController::class, 'printContract'])->name('get.contract')->middleware(['auth:customer']);
    Route::get('/signature/{id}', [ContractController::class, 'signature'])->name('signature')->middleware(['auth:customer']);
    Route::post('/signaturestore', [ContractController::class, 'signatureStore'])->name('signaturestore')->middleware(['auth:customer']);
    Route::delete('/contract/{id}/file/delete/{fid}', [ContractController::class, 'fileDelete'])->name('contract.file.delete')->middleware(['auth:customer']);
    Route::get('/contract/{id}/comment', [ContractController::class, 'commentDestroy'])->name('comment.destroy')->middleware(['auth:customer']);
    Route::get('/contract/{id}/note', [ContractController::class, 'noteDestroy'])->name('contract.note.destroy')->middleware(['auth:customer']);
    Route::post('/contract_status_edit/{id}', [ContractController::class, 'contract_status_edit'])->name('contract.status')->middleware(['auth:customer']);

    Route::post('/paymentwall', [PaymentWallPaymentController::class, 'invoicepaymentwall'])->name('invoice.paymentwallpayment');

    Route::post('{id}/invoice-with-paypal', [PaypalController::class, 'customerPayWithPaypal'])->name('invoice.with.paypal');
    Route::get('{id}/get-payment-status/{amount}', [PaypalController::class, 'customerGetPaymentStatus'])->name('get.payment.status');

    Route::post('{id}/pay-with-paypal', [PaypalController::class, 'customerretainerPayWithPaypal'])->name('pay.with.paypal');
    Route::get('{id}/{amount}/get-retainer-payment-status', [PaypalController::class, 'customerGetRetainerPaymentStatus'])->name('get.retainer.payment.status');

    Route::post('invoice/{id}/payment', [StripePaymentController::class, 'addpayment'])->name('invoice.payment');

    Route::post('/retainer-pay-with-paystack', [PaystackPaymentController::class, 'RetainerPayWithPaystack'])->name('retainer.pay.with.paystack')->middleware(['XSS:customer']);
    Route::get('/retainer/paystack/{retainer_id}/{amount}/{pay_id}', [PaystackPaymentController::class, 'getRetainerPaymentStatus'])->name('retainer.paystack')->middleware(['XSS:customer']);

    Route::post('/invoice-pay-with-paystack', [PaystackPaymentController::class, 'invoicePayWithPaystack'])->name('invoice.pay.with.paystack');
    Route::get('/invoice/paystack/{invoice_id}/{amount}/{pay_id}', [PaystackPaymentController::class, 'getInvoicePaymentStatus'])->name('invoice.paystack');

    Route::post('/retainer-pay-with-flaterwave', [FlutterwavePaymentController::class, 'retainerPayWithFlutterwave'])->name('retainer.pay.with.flaterwave');
    Route::get('/retainer/flaterwave/{txref}/{retainer_id}', [FlutterwavePaymentController::class, 'getRetainerPaymentStatus'])->name('retainer.flaterwave');

    Route::post('/invoice-pay-with-flaterwave', [FlutterwavePaymentController::class, 'invoicePayWithFlutterwave'])->name('invoice.pay.with.flaterwave');
    Route::get('/invoice/flaterwave/{txref}/{invoice_id}', [FlutterwavePaymentController::class, 'getInvoicePaymentStatus'])->name('invoice.flaterwave');

    Route::post('/retainer-pay-with-razorpay', [RazorpayPaymentController::class, 'retainerPayWithRazorpay'])->name('retainer.pay.with.razorpay');
    Route::get('/retainer/razorpay/{amount}/{retainer_id}', [RazorpayPaymentController::class, 'getRetainerPaymentStatus'])->name('retainer.razorpay');

    Route::post('/invoice-pay-with-razorpay', [RazorpayPaymentController::class, 'invoicePayWithRazorpay'])->name('invoice.pay.with.razorpay');
    Route::get('/invoice/razorpay/{amount}/{invoice_id}', [RazorpayPaymentController::class, 'getInvoicePaymentStatus'])->name('invoice.razorpay');

    Route::post('/retainer-pay-with-paytm', [PaytmPaymentController::class, 'retainerPayWithPaytm'])->name('retainer.pay.with.paytm')->middleware(['XSS:customer']);
    Route::post('/invoice-pay-with-paytm', [PaytmPaymentController::class, 'invoicePayWithPaytm'])->name('invoice.pay.with.paytm')->middleware(['XSS:customer']);
    Route::post('/invoice/paytm/{invoice}/{amount}', [PaytmPaymentController::class, 'getInvoicePaymentStatus'])->name('invoice.paytm')->middleware(['XSS:customer']);

    Route::post('/retainer-pay-with-mercado', [MercadoPaymentController::class, 'retainerPayWithMercado'])->name('retainer.pay.with.mercado')->middleware(['XSS:customer']);
    Route::any('/retainer/mercado/{retainer}', [MercadoPaymentController::class, 'getRetainerPaymentStatus'])->name('retainer.mercado');

    Route::post('/invoice-pay-with-mercado', [MercadoPaymentController::class, 'invoicePayWithMercado'])->name('invoice.pay.with.mercado');
    Route::any('/invoice/mercado/{invoice}', [MercadoPaymentController::class, 'getInvoicePaymentStatus'])->name('invoice.mercado');

    Route::post('/retainer-pay-with-mollie', [MolliePaymentController::class, 'retainerPayWithMollie'])->name('retainer.pay.with.mollie');
    Route::post('/invoice-pay-with-mollie', [MolliePaymentController::class, 'invoicePayWithMollie'])->name('invoice.pay.with.mollie');
    Route::get('/invoice/mollie/{invoice}/{amount}', [MolliePaymentController::class, 'getInvoicePaymentStatus'])->name('invoice.mollie');

    Route::post('/retainer-pay-with-skrill', [SkrillPaymentController::class, 'retainerPayWithSkrill'])->name('retainer.pay.with.skrill');
    Route::get('/retainer/skrill/{retainer}/{amount}', [SkrillPaymentController::class, 'getRetainerPaymentStatus'])->name('retainer.skrill');

    Route::post('/invoice-pay-with-skrill', [SkrillPaymentController::class, 'invoicePayWithSkrill'])->name('invoice.pay.with.skrill');
    Route::get('/invoice/skrill/{invoice}/{amount}', [SkrillPaymentController::class, 'getInvoicePaymentStatus'])->name('invoice.skrill');

    Route::post('/retainer-pay-with-coingate', [CoingatePaymentController::class, 'retainerPayWithCoingate'])->name('retainer.pay.with.coingate');
    Route::get('/retainer/coingate/{retainer}/{amount}', [CoingatePaymentController::class, 'getRetainerPaymentStatus'])->name('retainer.coingate');
});

Route::prefix('vender')->as('vender.')->group(function () {
    Route::get('login/{lang}', [AuthenticatedSessionController::class, 'showVenderLoginLang'])->name('login.lang');
    Route::get('login', [AuthenticatedSessionController::class, 'showVenderLoginForm'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'VenderLogin'])->name('login.store');

    Route::get('/password/resets/{lang?}', [AuthenticatedSessionController::class, 'showVendorLinkRequestForm'])->name('change.langPass');
    Route::post('/password/email', [AuthenticatedSessionController::class, 'postVendorEmail'])->name('password.email');
    Route::get('reset-password/{token}', [AuthenticatedSessionController::class, 'getVendorPassword'])->name('reset.password');
    Route::post('reset-password', [AuthenticatedSessionController::class, 'updateVendorPassword'])->name('password.reset');

    Route::get('dashboard', [VenderController::class, 'dashboard'])->name('dashboard')->middleware(['auth:vender']);
    Route::get('bill', [\App\Http\Controllers\BillController::class, 'VenderBill'])->name('bill')->middleware(['auth:vender']);
    Route::get('bill/{id}/show', [\App\Http\Controllers\BillController::class, 'venderBillShow'])->name('bill.show')->middleware(['auth:vender']);

    Route::get('bill/{id}/send', [\App\Http\Controllers\BillController::class, 'venderBillSend'])->name('bill.send')->middleware(['auth:vender']);
    Route::post('bill/{id}/send/mail', [\App\Http\Controllers\BillController::class, 'venderBillSendMail'])->name('bill.send.mail')->middleware(['auth:vender']);

    Route::get('payment', [VenderController::class, 'payment'])->name('payment')->middleware(['auth:vender']);
    Route::get('transaction', [VenderController::class, 'transaction'])->name('transaction')->middleware(['auth:vender']);

    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    Route::get('profile', [VenderController::class, 'profile'])->name('profile')->middleware(['auth:vender']);
    Route::post('update-profile', [VenderController::class, 'editprofile'])->name('update.profile')->middleware(['auth:vender']);
    Route::post('billing-info', [VenderController::class, 'editBilling'])->name('update.billing.info')->middleware(['auth:vender']);
    Route::post('shipping-info', [VenderController::class, 'editShipping'])->name('update.shipping.info')->middleware(['auth:vender']);

    Route::post('change.password', [VenderController::class, 'updatePassword'])->name('update.password')->middleware(['auth:vender']);
    Route::get('change-language/{lang}', [VenderController::class, 'changeLanquage'])->name('change.language')->middleware(['auth:vender']);
});
});
