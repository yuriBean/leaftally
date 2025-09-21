<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    DashboardController, BillController, ProposalController, RetainerController, InvoiceController,
    UserController, NotificationTemplatesController, SystemController, AiTemplateController,
    BomController, ProductionController
};
use App\Http\Controllers\Auth\{
    AuthenticatedSessionController, RegisteredUserController, EmailVerificationPromptController,
    EmailVerificationNotificationController, VerifyEmailController
};

// ==================== Email verification & auth (public) ====================
Route::get('/verify-email', [EmailVerificationPromptController::class, '__invoke'])->name('verification.notice')->middleware('auth');
Route::get('/verify-email/{lang?}', [EmailVerificationPromptController::class, 'showVerifyForm'])->name('verification.notice.lang');
Route::get('/verify/{id}/{hash}', [VerifyEmailController::class, '__invoke'])->name('verification.verify')->middleware('auth');
Route::post('/email/verification-notification', [EmailVerificationNotificationController::class, 'store'])->name('verification.send');
Route::middleware('guest')->group(function () {
Route::get('/register/{ref?}/{lang?}', [RegisteredUserController::class, 'showRegistrationForm'])->name('register');
Route::post('register', [RegisteredUserController::class, 'store'])->name('register.store');

Route::get('/login/{lang?}', [AuthenticatedSessionController::class, 'showLoginForm'])->name('login');
});
Route::get('/password/resets/{lang?}', [AuthenticatedSessionController::class, 'showLinkRequestForm'])->name('langPass');

// ==================== Root / dashboard (kept exactly) ====================
Route::middleware(['auth', 'XSS', 'revalidate', '2fa'])->group(function () {
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// ==================== Public pay links & PDFs ====================
Route::get('/bill/pay/{bill}', [BillController::class, 'paybill'])->name('pay.billpay');
Route::get('/proposal/pay/{proposal}', [ProposalController::class, 'payproposal'])->name('pay.proposalpay');
Route::get('/retainer/pay/{retainer}', [RetainerController::class, 'payretainer'])->name('pay.retainerpay');
Route::get('/invoice/pay/{invoice}', [InvoiceController::class, 'payinvoice'])->name('pay.invoice');

Route::get('bill/pdf/{id}', [BillController::class, 'bill'])->name('bill.pdf');
Route::get('proposal/pdf/{id}', [ProposalController::class, 'proposal'])->name('proposal.pdf');
Route::get('retainer/pdf/{id}', [RetainerController::class, 'retainer'])->name('retainer.pdf');
Route::get('invoice/pdf/{id}', [InvoiceController::class, 'invoice'])->name('invoice.pdf');

// ==================== Quick exports (public endpoints as in source) ====================
Route::get('export/Proposal', [ProposalController::class, 'export'])->name('proposal.export');
Route::get('export/invoice', [InvoiceController::class, 'export'])->name('invoice.export');
Route::get('export/Bill', [BillController::class, 'export'])->name('Bill.export');
Route::get('export/retainer', [RetainerController::class, 'export'])->name('retainer.export');

// ==================== Company helpers ====================
Route::get('company-info/{id}', [UserController::class, 'CompnayInfo'])->name('company.info');
Route::post('user-unable', [UserController::class, 'UserUnable'])->name('user.unable');
Route::get('user-login/{id}', [UserController::class, 'LoginManage'])->name('users.login');

// ==================== Manufacturing (BOM & Production) ====================
Route::get('/boms/generate-code', [BomController::class, 'generateCode'])->name('boms.generateCode');
Route::get('/boms', [BomController::class, 'index'])->name('bom.index');
Route::get('/boms/create', [BomController::class, 'create'])->name('bom.create');
Route::post('/boms', [BomController::class, 'store'])->name('bom.store');
Route::get('/boms/{bom}', [BomController::class, 'show'])->name('bom.show');
Route::get('/boms/{bom}/edit', [BomController::class, 'edit'])->name('bom.edit');
Route::put('/boms/{bom}', [BomController::class, 'update'])->name('bom.update');
Route::delete('/boms/{bom}', [BomController::class, 'destroy'])->name('bom.destroy');
Route::post('/boms/{bom}/duplicate', [BomController::class, 'duplicate'])->name('bom.duplicate');
Route::get('/boms/{bom}/details', [BomController::class, 'details'])->name('bom.details');
// BOM bulk actions
Route::get('/bom/export', [BomController::class, 'export'])->name('bom.export');
Route::post('/bom/export-selected', [BomController::class, 'exportSelected'])->name('bom.export-selected');
Route::post('/bom/bulk-destroy', [BomController::class, 'bulkDestroy'])->name('bom.bulk-destroy');


Route::resource('production', ProductionController::class);
Route::post('production/{production}/transition', [ProductionController::class, 'transition'])->name('production.transition');
    Route::get('production/export', [ProductionController::class, 'export'])
        ->name('production.export');

    Route::post('production/export-selected', [ProductionController::class, 'exportSelected'])
        ->name('production.export-selected');

    Route::post('production/bulk-destroy', [ProductionController::class, 'bulkDestroy'])
        ->name('production.bulk-destroy');

// ==================== Notification templates ====================
Route::resource('notification-templates', NotificationTemplatesController::class)->middleware(['auth','XSS'])->except('index');
Route::get('notification-templates/{id?}/{lang?}', [NotificationTemplatesController::class, 'index'])->name('notification-templates.index')->middleware(['XSS']);
Route::get('notification_template_lang/{id}/{lang?}', [NotificationTemplatesController::class, 'manageNotificationLang'])->name('manage.notification.language')->middleware(['auth','XSS']);
});
// ==================== Cookie & AI tools ====================
Route::any('/cookie-consent', [SystemController::class, 'CookieConsent'])->name('cookie-consent');
Route::post('cookie-setting', [SystemController::class, 'saveCookieSettings'])->name('cookie.setting');
Route::post('chatgptkey', [SystemController::class, 'chatgptkey'])->name('settings.chatgptkey');

Route::get('generate/{template_name}', [AiTemplateController::class, 'create'])->name('generate');
Route::post('generate/keywords/{id}', [AiTemplateController::class, 'getKeywords'])->name('generate.keywords');
Route::post('generate/response', [AiTemplateController::class, 'AiGenerate'])->name('generate.response');

Route::get('grammar/{template}', [AiTemplateController::class, 'grammar'])->name('grammar');
Route::post('grammar/response', [AiTemplateController::class, 'grammarProcess'])->name('grammar.response');

// ==================== Cache utilities ====================
Route::get('/config-cache', function () {
    \Artisan::call('cache:clear');
    \Artisan::call('route:clear');
    \Artisan::call('view:clear');
    \Artisan::call('optimize:clear');
    return redirect()->back()->with('success', 'Clear Cache successfully.');
});
