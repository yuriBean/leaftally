<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    DashboardController, UserController, RoleController, PermissionController,
    ContractTypeController, ContractController, EmailTemplateController, LanguageController,
    SystemController, WebhookController, InvoiceController
};

Route::group(['middleware' => ['auth','2fa']], function () {

    Route::get('invoice/{id}/show', [InvoiceController::class, 'customerInvoiceShow'])->name('customer.invoice.show')->middleware(['auth:customer','XSS','revalidate']);

    Route::get('users/{id}/login-with-company', [UserController::class, 'LoginWithCompany'])->name('login.with.company');
    Route::get('login-with-company/exit', [UserController::class, 'ExitCompany'])->name('exit.company');

    Route::group(['middleware' => ['auth','XSS','revalidate']], function () {
        Route::post('contract/bulk-destroy', [ContractController::class, 'bulkDestroy'])
    ->name('contract.bulk-destroy');

    Route::get('contract/export', [ContractController::class, 'export'])
        ->name('contract.export');

    Route::post('contract/export-selected', [ContractController::class, 'exportSelected'])
        ->name('contract.export-selected');
        Route::post('contractType/bulk-destroy', [ContractTypeController::class, 'bulkDestroy'])
        ->name('contractType.bulk-destroy');

    Route::get('contractType/export', [ContractTypeController::class, 'export'])
        ->name('contractType.export');

    Route::post('contractType/export-selected', [ContractTypeController::class, 'exportSelected'])
        ->name('contractType.export-selected');
        Route::resource('contractType', ContractTypeController::class)->middleware(['auth','XSS']);
    });

    Route::group(['middleware' => ['auth','XSS','revalidate']], function () {
        Route::resource('contract', ContractController::class)->middleware(['auth','XSS']);
        Route::get('contract/duplicate/{id}', [ContractController::class, 'duplicate'])->name('contract.duplicate')->middleware(['auth','XSS']);
        Route::put('contract/duplicatecontract/{id}', [ContractController::class, 'duplicatecontract'])->name('contract.duplicatecontract')->middleware(['auth','XSS']);
        Route::post('contract/{id}/description', [ContractController::class, 'descriptionStore'])->name('contract.description.store')->middleware(['auth','XSS']);
        Route::post('contract/{id}/file', [ContractController::class, 'fileUpload'])->name('contract.file.upload')->middleware(['auth','XSS']);
        Route::get('/contract/{id}/file/{fid}', [ContractController::class, 'fileDownload'])->name('contract.file.download')->middleware(['auth','XSS']);
        Route::delete('/contract/{id}/file/delete/{fid}', [ContractController::class, 'fileDelete'])->name('contract.file.delete')->middleware(['auth','XSS']);
        Route::post('/contract/{id}/comment', [ContractController::class, 'commentStore'])->name('comment.store')->middleware(['auth','XSS']);
        Route::get('/contract/{id}/comment', [ContractController::class, 'commentDestroy'])->name('comment.destroy')->middleware(['auth','XSS']);
        Route::post('/contract/{id}/note', [ContractController::class, 'noteStore'])->name('contract.note.store')->middleware(['auth','XSS']);
        Route::get('contract/{id}/note', [ContractController::class, 'noteDestroy'])->name('contract.note.destroy')->middleware(['auth','XSS']);
        Route::get('contract/pdf/{id}', [ContractController::class, 'pdffromcontract'])->name('contract.download.pdf')->middleware(['auth','XSS']);
        Route::get('contract/{id}/get_contract', [ContractController::class, 'printContract'])->name('get.contract')->middleware(['auth','XSS']);
        Route::get('/signature/{id}', [ContractController::class, 'signature'])->name('signature')->middleware(['auth','XSS']);
        Route::post('/signaturestore', [ContractController::class, 'signatureStore'])->name('signaturestore')->middleware(['auth','XSS']);
        Route::get('/contract/{id}/mail', [ContractController::class, 'sendmailContract'])->name('send.mail.contract')->middleware(['auth','XSS']);
    });

    Route::get('email_template_lang/{id}/{lang?}', [EmailTemplateController::class, 'manageEmailLang'])->name('manage.email.language')->middleware(['auth','XSS']);
    Route::put('email_template_store/{pid}', [EmailTemplateController::class, 'storeEmailLang'])->name('store.email.language')->middleware(['auth']);
    Route::post('email_template_status', [EmailTemplateController::class, 'updateStatus'])->name('status.email.language')->middleware(['auth']);
    Route::resource('email_template', EmailTemplateController::class)->middleware(['auth']);

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard')->middleware(['auth','XSS','revalidate']);
    Route::get('user/{id}/plan', [UserController::class, 'upgradePlan'])->name('plan.upgrade')->middleware(['XSS','revalidate']);
    Route::get('user/{id}/plan/{pid}', [UserController::class, 'activePlan'])->name('plan.active')->middleware(['XSS','revalidate']);
    Route::get('profile', [UserController::class, 'profile'])->name('profile')->middleware(['XSS','revalidate']);
    Route::post('edit-profile', [UserController::class, 'editprofile'])->name('update.account')->middleware(['XSS','revalidate']);

    Route::resource('users', UserController::class)->middleware(['auth','XSS','revalidate','feature:user_access_management']);
    Route::post('change-password', [UserController::class, 'updatePassword'])->name('update.password');
    Route::any('user-reset-password/{id}', [UserController::class, 'userPassword'])->name('users.reset');
    Route::post('user-reset-password/{id}', [UserController::class, 'userPasswordReset'])->name('user.password.update');
    Route::get('change-language/{lang}', [UserController::class, 'changeMode'])->name('change.mode');

    Route::resource('roles', RoleController::class)->middleware(['auth','XSS','revalidate','feature:user_access_management']);
    Route::resource('permissions', PermissionController::class)->middleware(['auth','XSS','revalidate','feature:user_access_management']);

    Route::group(['middleware' => ['auth','XSS','revalidate']], function () {
        Route::get('change-language/{lang}', [LanguageController::class, 'changeLanquage'])->name('change.language');
        Route::get('manage-language/{lang}', [LanguageController::class, 'manageLanguage'])->name('manage.language');
        Route::post('store-language-data/{lang}', [LanguageController::class, 'storeLanguageData'])->name('store.language.data');
        Route::get('create-language', [LanguageController::class, 'createLanguage'])->name('create.language');
        Route::post('store-language', [LanguageController::class, 'storeLanguage'])->name('store.language');
        Route::delete('/lang/{lang}', [LanguageController::class, 'destroyLang'])->name('lang.destroy');
        Route::post('disable-language', [LanguageController::class, 'disableLang'])->name('disablelanguage')->middleware(['auth','XSS']);
    });

    Route::group(['middleware' => ['auth','XSS','revalidate']], function () {
        Route::resource('settings', SystemController::class);
        Route::post('email-settings', [SystemController::class, 'saveEmailSettings'])->name('email.settings');
        Route::post('company-settings', [SystemController::class, 'saveCompanySettings'])->name('company.settings');
        Route::post('stripe-settings', [SystemController::class, 'savePaymentSettings'])->name('payment.settings');
        Route::post('system-settings', [SystemController::class, 'saveSystemSettings'])->name('system.settings');
        Route::post('recaptcha-settings', [SystemController::class, 'recaptchaSettingStore'])->name('recaptcha.settings.store');
        Route::post('storage-settings', [SystemController::class, 'storageSettingStore'])->name('storage.setting.store');
        Route::get('company-setting', [SystemController::class, 'companyIndex'])->name('company.setting');
        Route::post('business-setting', [SystemController::class, 'saveBusinessSettings'])->name('business.setting');
        Route::any('twilio-settings', [SystemController::class, 'saveTwilioSettings'])->name('twilio.settings');
        Route::post('company-payment-setting', [SystemController::class, 'saveCompanyPaymentSettings'])->name('company.payment.settings');
        Route::post('test', [SystemController::class, 'testMail'])->name('test.mail');
        Route::post('test-mail', [SystemController::class, 'testSendMail'])->name('test.send.mail');
        Route::post('setting/seo', [SystemController::class, 'SeoSettings'])->name('seo.settings');

        Route::resource('webhook', WebhookController::class);
        Route::post('company-email-settings', [SystemController::class, 'saveCompanyEmailSetting'])->name('company.email.settings');
    });
});
