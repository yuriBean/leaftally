<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    TransactionController, ReportController, EmployeeController, PayrollController, UsersLogController,
    BudgetController, GoalController, AssetController, CustomFieldController, ProductServiceController,
    CustomerController, VenderController, PaymentController, RevenueController
};

Route::group(['middleware' => ['verified']], function () {

    Route::group(['middleware' => ['auth','XSS','revalidate']], function () {
        Route::get('report/transaction', [TransactionController::class, 'index'])->name('transaction.index');
    });

    Route::group(['middleware' => ['auth','XSS','revalidate']], function () {
        Route::get('report/income-summary', [ReportController::class, 'incomeSummary'])->name('report.income.summary');
        Route::get('report/expense-summary', [ReportController::class, 'expenseSummary'])->name('report.expense.summary');
        Route::get('report/income-vs-expense-summary', [ReportController::class, 'incomeVsExpenseSummary'])->name('report.income.vs.expense.summary');
        Route::get('report/tax-summary', [ReportController::class, 'taxSummary'])->name('report.tax.summary');
        Route::get('report/profit-loss/{view?}/{collapseView?}', [ReportController::class, 'profitLoss'])->name('report.profit.loss');
        Route::get('report/invoice-summary', [ReportController::class, 'invoiceSummary'])->name('report.invoice.summary');

        Route::get('reports-monthly-cashflow', [ReportController::class, 'monthlyCashflow'])->name('report.monthly.cashflow')->middleware(['auth','XSS']);
        Route::get('reports-quarterly-cashflow', [ReportController::class, 'quarterlyCashflow'])->name('report.quarterly.cashflow')->middleware(['auth','XSS']);

        Route::get('report/bill-summary', [ReportController::class, 'billSummary'])->name('report.bill.summary');
        Route::get('report/product-stock-report', [ReportController::class, 'productStock'])->name('report.product.stock.report');
        Route::get('report/invoice-report', [ReportController::class, 'invoiceReport'])->name('report.invoice');
        Route::get('report/account-statement-report', [ReportController::class, 'accountStatement'])->name('report.account.statement');
        Route::get('report/balance-sheet/{view?}/{collapseview?}', [ReportController::class, 'balanceSheet'])->name('report.balance.sheet');
        Route::get('report/ledger', [ReportController::class, 'ledgerSummary'])->name('report.ledger');
        Route::get('report/trial-balance/{view?}', [ReportController::class, 'trialBalanceSummary'])->name('trial.balance');

        Route::post('export/trial-balance', [ReportController::class, 'trialBalanceExport'])->name('trial.balance.export');
        Route::get('report/filter-chart', [ReportController::class, 'getFilteredChartData'])->name('filter.chart.data');
        Route::post('export/profit-loss', [ReportController::class, 'profitLossExport'])->name('profit.loss.export');
        Route::post('export/balance-sheet', [ReportController::class, 'balanceSheetExport'])->name('balance.sheet.export');
    });

    Route::post('import/productservice', [ProductServiceController::class, 'import'])->name('productservice.import');
    Route::get('export/customer', [CustomerController::class, 'export'])->name('customer.export');
    Route::get('import/customer/file', [CustomerController::class, 'importFile'])->name('customer.file.import');
    Route::post('import/customer', [CustomerController::class, 'import'])->name('customer.import');
    Route::get('export/vender', [VenderController::class, 'export'])->name('vender.export');
    Route::get('import/vender/file', [VenderController::class, 'importFile'])->name('vender.file.import');
    Route::post('import/vender', [VenderController::class, 'import'])->name('vender.import');
    Route::get('export/transaction', [\App\Http\Controllers\TransactionController::class, 'export'])->name('transaction.export');
    Route::get('export/accountstatement', [ReportController::class, 'export'])->name('accountstatement.export');
    Route::get('export/productstock', [ReportController::class, 'stock_export'])->name('productstock.export');
    Route::get('export/payment/{date}', [PaymentController::class, 'export'])->name('payment.export');

    Route::resource('budget', BudgetController::class)->middleware(['auth','XSS','revalidate','feature:budgeting_enabled']);
        Route::post('goal/bulk-destroy', [GoalController::class, 'bulkDestroy'])
        ->name('goal.bulk-destroy');

    Route::get('goal/export', [GoalController::class, 'export'])
        ->name('goal.export');

    Route::post('goal/export-selected', [GoalController::class, 'exportSelected'])
        ->name('goal.export-selected');
    Route::resource('goal', GoalController::class)->middleware(['auth','XSS','revalidate']);
    Route::post('account-assets/bulk-destroy', [AssetController::class, 'bulkDestroy'])
    ->name('account-assets.bulk-destroy');

Route::get('account-assets/export', [AssetController::class, 'export'])
    ->name('account-assets.export');

Route::post('account-assets/export-selected', [AssetController::class, 'exportSelected'])
    ->name('account-assets.export-selected');
    Route::resource('account-assets', AssetController::class)->middleware(['auth','XSS','revalidate']);
    Route::post('custom-field/bulk-destroy', [CustomFieldController::class, 'bulkDestroy'])
        ->name('custom-field.bulk-destroy');

    Route::get('custom-field/export', [CustomFieldController::class, 'export'])
        ->name('custom-field.export');

    Route::post('custom-field/export-selected', [CustomFieldController::class, 'exportSelected'])
        ->name('custom-field.export-selected');
    Route::resource('custom-field', CustomFieldController::class)->middleware(['auth','XSS','revalidate']);

    Route::resource('userlogs', UsersLogController::class)->middleware(['auth','XSS','revalidate','feature:user_access_management'])->name('index', 'userlogs.index');
});

 Route::group(['middleware' => ['auth','XSS','revalidate','feature:payroll']], function () {
    Route::get('export/employees', [EmployeeController::class, 'export'])->name('employees.export');
    Route::get('export/payroll', [PayrollController::class, 'export'])->name('payroll.export')->middleware(['feature:payroll_enabled']);
    Route::get('export/generated_payroll_export', [PayrollController::class, 'generated_payroll_export'])->name('active.payroll.export')->middleware(['feature:payroll_enabled']);
    Route::get('export/payroll_slip_export', [PayrollController::class, 'payroll_slip_export'])->name('payrollSlip.export')->middleware(['feature:payroll_enabled']);
 });
