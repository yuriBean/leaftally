<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    ConstantsController, CustomerController, VenderController, BankAccountController, TransferController,
    ChartOfAccountController, ChartOfAccountTypeController, JournalEntryController
};

Route::group(['middleware' => ['verified']], function () {

    // Customers
    Route::group(['middleware' => ['auth','XSS','revalidate']], function () {
        Route::get('constants', [ConstantsController::class, 'index'])->name('constants.index');
        Route::get('customer/{id}/show', [CustomerController::class, 'show'])->name('customer.show');
        Route::ANY('customer/{id}/statement', [CustomerController::class, 'statement'])->name('customer.statement');
        Route::any('customer-reset-password/{id}', [CustomerController::class, 'customerPassword'])->name('customer.reset');
        Route::post('customer-reset-password/{id}', [CustomerController::class, 'customerPasswordReset'])->name('customer.password.update');
        Route::delete('/customers/bulk-destroy', [CustomerController::class, 'bulkDestroy'])
        ->name('customer.bulk-destroy');
        Route::resource('customer', CustomerController::class)->except('show');
        
        Route::post('customers/export-selected', [CustomerController::class, 'exportSelected'])
    ->name('customer.export-selected');
    });

    // Vendors (company-side)
    Route::group(['middleware' => ['auth','XSS','revalidate','feature:vendor_management_enabled']], function () {
        Route::get('vender/{id}/show', [VenderController::class, 'show'])->name('vender.show');
        Route::ANY('vender/{id}/statement', [VenderController::class, 'statement'])->name('vender.statement');
        Route::any('vender-reset-password/{id}', [VenderController::class, 'venderPassword'])->name('vender.reset');
        Route::post('vender-reset-password/{id}', [VenderController::class, 'vendorPasswordReset'])->name('vender.password.update');
           Route::delete('vender/bulk-destroy', [VenderController::class, 'bulkDestroy'])
        ->name('vender.bulk-destroy');
        Route::resource('vender', VenderController::class)->except('show');
        Route::post('vender/export-selected', [VenderController::class, 'exportSelected'])->name('vender.export-selected');

    });

    // Bank Accounts & Transfers
    Route::group(['middleware' => ['auth','XSS','revalidate']], function () {
        Route::resource('bank-account', BankAccountController::class);
    });
    Route::group(['middleware' => ['auth','XSS','revalidate']], function () {
        Route::get('transfer/index', [TransferController::class, 'index'])->name('transfer.index');
        Route::resource('transfer', TransferController::class)->except('index');
    });

    // Bookkeeping core
    Route::group(['middleware' => ['auth','XSS','revalidate']], function () {
        Route::resource('chart-of-account', ChartOfAccountController::class);
    });
    Route::group(['middleware' => ['auth','XSS','revalidate']], function () {
        Route::resource('chart-of-account-type', ChartOfAccountTypeController::class);
    });
    Route::group(['middleware' => ['auth','XSS','revalidate']], function () {
        Route::post('journal-entry/account/destroy', [JournalEntryController::class, 'accountDestroy'])->name('journal.account.destroy');
        Route::delete('journal-entry/journal/destroy/{item_id}', [JournalEntryController::class, 'journalDestroy'])->name('journal.destroy');
        Route::resource('journal-entry', JournalEntryController::class);
    });
    Route::post('customer_short', [CustomerController::class,'customer_short'])->name('customer_short');
    Route::post('vender_short', [VenderController::class,'vender_short'])->name('vender_short');

    // Helper
    Route::post('chart-of-account/subtype', [ChartOfAccountController::class, 'getSubType'])->name('charofAccount.subType');
});
