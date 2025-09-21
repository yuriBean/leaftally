<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    InvoiceController, CreditNoteController, DebitNoteController, BillController, RevenueController,
    PaymentController, RetainerController, ProposalController, CouponController, TaxController
};

Route::group(['middleware' => ['auth','2fa']], function () {

    // ========== INVOICES ==========
    Route::group(['middleware' => ['auth','XSS','revalidate']], function () {
        Route::get('invoice/{id}/duplicate', [InvoiceController::class, 'duplicate'])->name('invoice.duplicate');
        Route::get('invoice/{id}/shipping/print', [InvoiceController::class, 'shippingDisplay'])->name('invoice.shipping.print');
        Route::get('invoice/{id}/payment/reminder', [InvoiceController::class, 'paymentReminder'])->name('invoice.payment.reminder');

        Route::get('invoice/index', [InvoiceController::class, 'index'])->name('invoice.index');
        Route::post('invoice/product/destroy', [InvoiceController::class, 'productDestroy'])->name('invoice.product.destroy');
        Route::post('invoice/product', [InvoiceController::class, 'product'])->name('invoice.product');
        Route::post('invoice/customer', [InvoiceController::class, 'customer'])->name('invoice.customer');

        Route::get('invoice/{id}/sent', [InvoiceController::class, 'sent'])->name('invoice.sent');
        Route::get('invoice/{id}/resent', [InvoiceController::class, 'resent'])->name('invoice.resent');

        Route::get('invoice/{id}/payment', [InvoiceController::class, 'payment'])->name('invoice.payment');
        Route::post('invoice/{id}/payment', [InvoiceController::class, 'createPayment'])->name('invoice.payment.store');
        Route::post('invoice/{id}/payment/{pid}/destroy', [InvoiceController::class, 'paymentDestroy'])->name('invoice.payment.destroy');

        Route::get('invoice/items', [InvoiceController::class, 'items'])->name('invoice.items');
        Route::delete('invoices/bulk-destroy', [InvoiceController::class, 'bulkDestroy'])
    ->name('invoice.bulk-destroy');

    Route::post('invoices/export-selected', [InvoiceController::class, 'exportSelected'])
    ->name('invoice.export-selected');
        Route::resource('invoice', InvoiceController::class)->except('index','create');
        Route::get('invoice/create/{cid}', [InvoiceController::class, 'create'])->name('invoice.create');
    });
    Route::get('/invoices/preview/{template}/{color}', [InvoiceController::class, 'previewInvoice'])->name('invoice.preview');
    Route::post('/invoices/template/setting', [InvoiceController::class, 'saveTemplateSettings'])->name('invoice.template.setting');

    // ========== CREDIT NOTES ==========
    Route::group(['middleware' => ['auth','XSS','revalidate','2fa']], function () {
        Route::get('credit-note', [CreditNoteController::class, 'index'])->name('credit.note');
        Route::get('custom-credit-note', [CreditNoteController::class, 'customCreate'])->name('invoice.custom.credit.note');
        Route::post('custom-credit-note', [CreditNoteController::class, 'customStore'])->name('invoice.custom.credit.note.store');
        Route::get('credit-note/bill', [CreditNoteController::class, 'getinvoice'])->name('invoice.get');
        Route::get('invoice/{id}/credit-note', [CreditNoteController::class, 'create'])->name('invoice.credit.note');
        Route::post('invoice/{id}/credit-note', [CreditNoteController::class, 'store'])->name('invoice.credit.note.store');
        Route::get('invoice/{id}/credit-note/edit/{cn_id}', [CreditNoteController::class, 'edit'])->name('invoice.edit.credit.note');
        Route::post('invoice/{id}/credit-note/edit/{cn_id}', [CreditNoteController::class, 'update'])->name('invoice.update.credit.note');
        Route::delete('invoice/{id}/credit-note/delete/{cn_id}', [CreditNoteController::class, 'destroy'])->name('invoice.delete.credit.note');
    });

    // ========== DEBIT NOTES ==========
    Route::group(['middleware' => ['auth','XSS','revalidate','2fa']], function () {
        Route::get('debit-note', [DebitNoteController::class, 'index'])->name('debit.note');
        Route::get('custom-debit-note', [DebitNoteController::class, 'customCreate'])->name('bill.custom.debit.note');
        Route::post('custom-debit-note', [DebitNoteController::class, 'customStore'])->name('bill.custom.debit.note.store');
        Route::get('debit-note/bill', [DebitNoteController::class, 'getbill'])->name('bill.get');
        Route::get('bill/{id}/debit-note', [DebitNoteController::class, 'create'])->name('bill.debit.note');
        Route::post('bill/{id}/debit-note', [DebitNoteController::class, 'store'])->name('bill.debit.note.store');
        Route::get('bill/{id}/debit-note/edit/{cn_id}', [DebitNoteController::class, 'edit'])->name('bill.edit.debit.note');
        Route::post('bill/{id}/debit-note/edit/{cn_id}', [DebitNoteController::class, 'update'])->name('bill.update.debit.note');
        Route::delete('bill/{id}/debit-note/delete/{cn_id}', [DebitNoteController::class, 'destroy'])->name('bill.delete.debit.note');
    });

    // Bill preview/template
    Route::get('/bill/preview/{template}/{color}', [BillController::class, 'previewBill'])->name('bill.preview');
    Route::post('/bill/template/setting', [BillController::class, 'saveBillTemplateSettings'])->name('bill.template.setting');

    // Duplicate taxes resource as in source (kept)
    Route::post('taxes/bulk-destroy', [TaxController::class, 'bulkDestroy'])
        ->name('taxes.bulk-destroy');

    // Exports
    Route::get('taxes/export', [TaxController::class, 'export'])
        ->name('taxes.export');

    Route::post('taxes/export-selected', [TaxController::class, 'exportSelected'])
        ->name('taxes.export-selected');
    Route::resource('taxes', TaxController::class)->middleware(['auth','XSS','revalidate','feature:tax_management_enabled']);

    // ========== Revenues & Payments index/resources ==========
    Route::get('revenue/index', [RevenueController::class, 'index'])->name('revenue.index')->middleware(['auth','XSS','revalidate']);
        Route::delete('revenue/bulk-destroy', [RevenueController::class, 'bulkDestroy'])
        ->name('revenue.bulk-destroy');
    Route::resource('revenue', RevenueController::class)->middleware(['auth','XSS','revalidate'])->except('index');
        Route::get('export/revenue', [RevenueController::class, 'export'])
        ->name('revenue.export');

    Route::post('export/revenue/selected', [RevenueController::class, 'exportSelected'])
        ->name('revenue.export-selected');



    Route::group(['middleware' => ['auth','XSS','revalidate','2fa']], function () {
        Route::post('bills/export-selected', [BillController::class, 'exportSelected'])
    ->name('bill.export-selected');

Route::delete('bills/bulk-destroy', [BillController::class, 'bulkDestroy'])
    ->name('bill.bulk-destroy');
        Route::get('bill/{id}/duplicate', [BillController::class, 'duplicate'])->name('bill.duplicate');
        Route::get('bill/{id}/shipping/print', [BillController::class, 'shippingDisplay'])->name('bill.shipping.print');
        Route::get('bill/index', [BillController::class, 'index'])->name('bill.index');
        Route::post('bill/product/destroy', [BillController::class, 'productDestroy'])->name('bill.product.destroy');
        Route::post('bill/product', [BillController::class, 'product'])->name('bill.product');
        Route::post('bill/vender', [BillController::class, 'vender'])->name('bill.vender');
        Route::get('bill/{id}/sent', [BillController::class, 'sent'])->name('bill.sent');
        Route::get('bill/{id}/resent', [BillController::class, 'resent'])->name('bill.resent');
        Route::get('bill/{id}/payment', [BillController::class, 'payment'])->name('bill.payment');
        Route::post('bill/{id}/payment', [BillController::class, 'createPayment'])->name('add.bill.payment');
        Route::post('bill/{id}/payment/{pid}/destroy', [BillController::class, 'paymentDestroy'])->name('bill.payment.destroy');
        Route::get('bill/items', [BillController::class, 'items'])->name('bill.items');
        Route::resource('bill', BillController::class)->except('index','create');
        Route::get('bill/create/{cid}', [BillController::class, 'create'])->name('bill.create');
    });

    Route::get('payment/index', [PaymentController::class, 'index'])->name('payment.index')->middleware(['auth','XSS','revalidate']);
    Route::get('export/payments/{date?}', [PaymentController::class, 'export'])
    ->name('payment.export');

    Route::post('export/payments/selected', [PaymentController::class, 'exportSelected'])
    ->name('payment.export-selected');

    Route::match(['POST', 'DELETE'], 'payment/bulk-destroy', [PaymentController::class, 'bulkDestroy'])
    ->name('payment.bulk-destroy');
    Route::resource('payment', PaymentController::class)->except('index')->middleware(['auth','XSS','revalidate']);

    // ========== Retainers (verified) ==========
    Route::post('retainer/product', [RetainerController::class, 'product'])->name('retainer.product')->middleware(['auth','XSS']);
    Route::get('retainer/{id}/sent', [RetainerController::class, 'sent'])->name('retainer.sent')->middleware(['auth']);
    Route::get('retainer/{id}/status/change', [RetainerController::class, 'statusChange'])->name('retainer.status.change')->middleware(['auth']);
    Route::get('retainer/{id}/resent', [RetainerController::class, 'resent'])->name('retainer.resent')->middleware(['auth']);
    Route::get('retainer/{id}/duplicate', [RetainerController::class, 'duplicate'])->name('retainer.duplicate')->middleware(['auth']);
    Route::get('retainer/{id}/payment', [RetainerController::class, 'payment'])->name('retainer.payment')->middleware(['auth']);
    Route::post('retainer/{id}/payment/create', [RetainerController::class, 'createPayment'])->name('retainer.payment.create')->middleware(['auth']);
    Route::get('retainer/{id}/payment/reminder', [RetainerController::class, 'paymentReminder'])->name('retainer.payment.reminder')->middleware(['auth']);
    Route::post('retainer/{id}/payment/{pid}/destroy', [RetainerController::class, 'paymentDestroy'])->name('retainer.payment.destroy')->middleware(['auth']);
    Route::get('retainer/{id}/convert', [RetainerController::class, 'convert'])->name('retainer.convert')->middleware(['auth']);
    Route::post('retainer/product/destroy', [RetainerController::class, 'productDestroy'])->name('retainer.product.destroy')->middleware(['auth']);
    Route::get('retainer/items/', [RetainerController::class, 'items'])->name('retainer.items')->middleware(['auth']);
    Route::delete('retainers/bulk-destroy', [\App\Http\Controllers\RetainerController::class, 'bulkDestroy'])
    ->name('retainer.bulk-destroy');

Route::post('retainers/export-selected', [\App\Http\Controllers\RetainerController::class, 'exportSelected'])
    ->name('retainer.export-selected');
    Route::resource('retainer', RetainerController::class)->except('create')->middleware(['auth','XSS']);
    Route::get('retainer/create/{cid}', [RetainerController::class, 'create'])->name('retainer.create')->middleware(['auth','XSS']);
    Route::post('/retainer/template/setting', [RetainerController::class, 'saveRetainerTemplateSettings'])->name('retainer.template.setting')->middleware(['auth','XSS']);
    Route::get('/retainer/preview/{template}/{color}', [RetainerController::class, 'previewRetainer'])->name('retainer.preview')->middleware(['auth','XSS']);

    // ========== Proposals ==========
    Route::group(['middleware' => ['auth','XSS','revalidate','2fa']], function () {
        Route::get('proposal/{id}/status/change', [ProposalController::class, 'statusChange'])->name('proposal.status.change');
        Route::get('proposal/{id}/convert', [ProposalController::class, 'convert'])->name('proposal.convert');
        Route::get('proposal/{id}/duplicate', [ProposalController::class, 'duplicate'])->name('proposal.duplicate');
        Route::post('proposal/product/destroy', [ProposalController::class, 'productDestroy'])->name('proposal.product.destroy');
        Route::post('proposal/customer', [ProposalController::class, 'customer'])->name('proposal.customer');
        Route::post('proposal/product', [ProposalController::class, 'product'])->name('proposal.product');
        Route::get('proposal/items', [ProposalController::class, 'items'])->name('proposal.items');
        Route::get('proposal/{id}/sent', [ProposalController::class, 'sent'])->name('proposal.sent');
        Route::get('proposal/{id}/resent', [ProposalController::class, 'resent'])->name('proposal.resent');
        Route::get('proposal/{id}/convertinvoice', [ProposalController::class, 'convertInvoice'])->name('proposal.convertinvoice');
        Route::get('proposal/export', [ProposalController::class, 'export'])->name('proposal.export');
        Route::post('proposal/export-selected', [ProposalController::class, 'exportSelected'])->name('proposal.export-selected');
        Route::delete('proposal/bulk-destroy', [ProposalController::class, 'bulkDestroy'])->name('proposal.bulk-destroy');
        Route::resource('proposal', ProposalController::class)->except('create');
        Route::get('proposal/create/{cid}', [ProposalController::class, 'create'])->name('proposal.create');
    });
    Route::get('/proposal/preview/{template}/{color}', [ProposalController::class, 'previewProposal'])->name('proposal.preview');
    Route::post('/proposal/template/setting', [ProposalController::class, 'saveProposalTemplateSettings'])->name('proposal.template.setting');



    // ========== Coupons ==========
    Route::get('/apply-coupon', [CouponController::class, 'applyCoupon'])->name('apply.coupon')->middleware(['auth','XSS']);
    Route::resource('coupons', CouponController::class)->middleware(['auth','XSS','revalidate']);
});
