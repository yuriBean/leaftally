<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
     BankTransferController,
    PlanController, ExpenseController, PlanRequestController, ReferralProgramController,
    AllowanceController,AllowanceOptionController,BranchController,
    DepartmentController,DeductionOptionController,DesignationController,DocumentController,
    DucumentUploadController,OtherPaymentController,
    EmployeeController,LoanController,LoanOptionController,OvertimeController,PaySlipController,PayslipTypeController,
    SaturationDeductionController,SetSalaryController,CommissionController,
    ReportController
};




Route::group(['middleware' => ['verified']], function () {

    Route::resource('banktransfer', BankTransferController::class)->middleware(['auth','XSS','revalidate']);
    Route::post('plan-pay-with-bank', [BankTransferController::class, 'planPayWithbank'])->middleware('XSS','auth')->name('plan.pay.with.bank');
    Route::get('/change_status/{id}/{response}', [BankTransferController::class, 'ChangeStatus'])->name('change.status')->middleware(['auth','XSS','revalidate']);

    Route::get('action-status/{id}/{response}', [BankTransferController::class, 'invoicechangestatus'])->name('action.status')->middleware(['XSS','revalidate']);
    Route::get('invoice-payment-show/{id}', [BankTransferController::class, 'invoicpaymenteshow'])->name('invoice.payment.show')->middleware(['XSS']);
    Route::delete('invoice-delete/{id}', [BankTransferController::class, 'invoicedestroy'])->name('invoice.delete');

    Route::get('retainer-payment-show/{id}', [BankTransferController::class, 'retainerpaymenteshow'])->name('retainer.payment.show')->middleware(['XSS']);
    Route::get('retainer-change-status/{id}/{response}', [BankTransferController::class, 'retainerchangestatus'])->name('retainer.change.status')->middleware(['XSS','revalidate']);
    Route::delete('retainer-delete/{id}', [BankTransferController::class, 'retainerdestroy'])->name('retainer.delete');

    // ===== Plans & Expenses =====
    Route::resource('plans', PlanController::class)->middleware(['auth','XSS','revalidate']);
    Route::get('plan/plan-trial/{id}', [PlanController::class, 'PlanTrial'])->name('plan.trial');
    Route::post('plan-disable', [PlanController::class, 'planDisable'])->name('plan.disable')->middleware(['auth','XSS','revalidate']);

    Route::resource('expenses', ExpenseController::class)->middleware(['auth','XSS','revalidate','feature:expense_tracking_enabled']);

    // ===== Plan Request Module & Referral Program =====
    Route::get('plan_request', [PlanRequestController::class, 'index'])->name('plan_request.index')->middleware(['auth','XSS']);
    Route::get('request_frequency/{id}', [PlanRequestController::class, 'requestView'])->name('request.view')->middleware(['auth','XSS']);
    Route::get('request_send/{id}', [PlanRequestController::class, 'userRequest'])->name('send.request')->middleware(['auth','XSS']);
    Route::get('request_response/{id}/{response}', [PlanRequestController::class, 'acceptRequest'])->name('response.request')->middleware(['auth','XSS']);
    Route::get('request_cancel/{id}', [PlanRequestController::class, 'cancelRequest'])->name('request.cancel')->middleware(['auth','XSS']);

    Route::get('referral-program/company', [ReferralProgramController::class, 'companyIndex'])->name('referral-program.company')->middleware(['auth','XSS']);
    Route::resource('referral-program', ReferralProgramController::class)->middleware(['auth','XSS']);
    Route::get('request-amount-sent/{id}', [ReferralProgramController::class, 'requestedAmountSent'])->name('request.amount.sent');
    Route::get('request-amount-cancel/{id}', [ReferralProgramController::class, 'requestCancel'])->name('request.amount.cancel');
    Route::post('request-amount-store/{id}', [ReferralProgramController::class, 'requestedAmountStore'])->name('request.amount.store');
    Route::get('request-amount/{id}/{status}', [ReferralProgramController::class, 'requestedAmount'])->name('amount.request');
});


 Route::group(['middleware' => ['auth','XSS','revalidate','feature:payroll','2fa']], function () {
    Route::resource('allowance', AllowanceController::class);
Route::get('allowances/create/{eid}', [AllowanceController::class, 'allowanceCreate'])->name('allowances.create');
Route::resource('allowanceoption', AllowanceOptionController::class);
Route::resource('branch', BranchController::class);
Route::resource('branch', BranchController::class);
Route::resource('deductionoption', DeductionOptionController::class);
Route::resource('department', DepartmentController::class);
Route::resource('designation', DesignationController::class);
Route::resource('document', DocumentController::class);
Route::resource('document-upload', DucumentUploadController::class);
 Route::post('employee/json', [EmployeeController::class, 'json'])->name('employee.json');
    Route::post('branch/employee/json', [EmployeeController::class, 'employeeJson'])->name('branch.employee.json');
    Route::get('employee-profile', [EmployeeController::class, 'profile'])->name('employee.profile');
    Route::get('show-employee-profile/{id}', [EmployeeController::class, 'profileShow'])->name('show.employee.profile');

    Route::get('lastlogin', [EmployeeController::class, 'lastLogin'])->name('lastlogin');

Route::post('export/employee-selected', [EmployeeController::class, 'exportSelected'])
    ->name('employee.export-selected')
    ;

Route::delete('employee/bulk-destroy', [EmployeeController::class, 'bulkDestroy'])
    ->name('employee.bulk-destroy')
    ;
    Route::resource('employee', EmployeeController::class);

    Route::post('employee/getdepartment', [EmployeeController::class, 'getDepartment'])->name('employee.getdepartment');
    Route::post('branch/employee/json', [EmployeeController::class, 'employeeJson'])->name('branch.employee.json');
        Route::get('export/employee', [EmployeeController::class, 'export'])->name('employee.export');
    Route::get('import/employee/file', [EmployeeController::class, 'importFile'])->name('employee.file.import');
    Route::post('employee/import', [EmployeeController::class, 'fileImport'])->name('employee.import');
    Route::get('import/employee/modal', [EmployeeController::class, 'fileImportModal'])->name('employee.import.modal');
    Route::post('import/employee', [EmployeeController::class, 'employeeImportdata'])->name('employee.import.data');
    Route::post('setting/joiningletter/{lang?}', [SystemController::class, 'joiningletterupdate'])->name('joiningletter.update');
    Route::get('setting/joiningletter/', [SystemController::class, 'companyIndex'])->name('get.joiningletter.language')->middleware(['XSS']);
    Route::get('employee/pdf/{id}', [EmployeeController::class, 'joiningletterPdf'])->name('joiningletter.download.pdf');
    Route::get('employee/doc/{id}', [EmployeeController::class, 'joiningletterDoc'])->name('joininglatter.download.doc');
    Route::get('employee/exppdf/{id}', [EmployeeController::class, 'ExpCertificatePdf'])->name('exp.download.pdf');
    Route::get('employee/expdoc/{id}', [EmployeeController::class, 'ExpCertificateDoc'])->name('exp.download.doc');
    Route::get('employee/nocpdf/{id}', [EmployeeController::class, 'NocPdf'])->name('noc.download.pdf');
    Route::get('employee/nocdoc/{id}', [EmployeeController::class, 'NocDoc'])->name('noc.download.doc');
    Route::resource('loan', LoanController::class);
    Route::get('loans/create/{eid}', [LoanController::class, 'loanCreate'])->name('loans.create');
    Route::resource('loanoption', LoanOptionController::class);
    Route::resource('otherpayment', OtherPaymentController::class);
    Route::get('otherpayments/create/{eid}', [OtherPaymentController::class, 'otherpaymentCreate'])->name('otherpayments.create');
    Route::resource('overtime', OvertimeController::class);
     Route::get('overtimes/create/{eid}', [OvertimeController::class, 'overtimeCreate'])->name('overtimes.create');

        Route::get('payslip/paysalary/{id}/{date}', [PaySlipController::class, 'paysalary'])->name('payslip.paysalary');
        Route::post('payslip/ytd', [PaySlipController::class, 'ytdTotals'])
    ->name('payslip.ytd');
    Route::get('payslip/bulk_pay_create/{date}', [PaySlipController::class, 'bulk_pay_create'])->name('payslip.bulk_pay_create');
    Route::post('payslip/bulkpayment/{date}', [PaySlipController::class, 'bulkpayment'])->name('payslip.bulkpayment');
    Route::post('payslip/search_json', [PaySlipController::class, 'search_json'])->name('payslip.search_json');
    Route::get('payslip/employeepayslip', [PaySlipController::class, 'employeepayslip'])->name('payslip.employeepayslip');
    Route::get('payslip/showemployee/{id}', [PaySlipController::class, 'showemployee'])->name('payslip.showemployee');
    Route::get('payslip/editemployee/{id}', [PaySlipController::class, 'editemployee'])->name('payslip.editemployee');
    Route::post('payslip/editemployee/{id}', [PaySlipController::class, 'updateEmployee'])->name('payslip.updateemployee');
 
    Route::get('payslip/send/{id}/{m}', [PaySlipController::class, 'send'])->name('payslip.send');
    Route::get('payslip/delete/{id}', [PaySlipController::class, 'destroy'])->name('payslip.delete');
    Route::resource('payslip', PaySlipController::class);
     Route::post('export/payslip', [PaySlipController::class, 'export'])->name('payslip.export');
     Route::resource('paysliptype', PayslipTypeController::class);

    Route::resource('saturationdeduction', SaturationDeductionController::class);
    Route::get('saturationdeductions/create/{eid}', [SaturationDeductionController::class, 'saturationdeductionCreate'])->name('saturationdeductions.create');
    Route::get('employee/salary/{eid}', [SetSalaryController::class, 'employeeBasicSalary'])->name('employee.basic.salary');
      Route::get('employee/salary/{eid}', [SetSalaryController::class, 'employeeBasicSalary'])->name('employee.basic.salary');
    Route::post('employee/update/sallary/{id}', [SetSalaryController::class, 'employeeUpdateSalary'])->name('employee.salary.update');
    Route::get('salary/employeeSalary', [SetSalaryController::class, 'employeeSalary'])->name('employeesalary');
    Route::resource('setsalary', SetSalaryController::class);
    Route::resource('commission', CommissionController::class);
    Route::get('commissions/create/{eid}', [CommissionController::class, 'commissionCreate'])->name('commissions.create');
       Route::get('reports-payroll', [ReportController::class, 'payroll'])->name('report.payroll')->middleware(['auth', 'XSS']);
    Route::post('reports-payroll/getdepartment', [ReportController::class, 'getPayrollDepartment'])->name('report.payroll.getdepartment')->middleware(['auth', 'XSS']);
    Route::post('reports-payroll/getemployee', [ReportController::class, 'getPayrollEmployee'])->name('report.payroll.getemployee')->middleware(['auth', 'XSS']);

 });

   Route::get('payslip/pdf/{id}/{m}', [PaySlipController::class, 'pdf'])->name('payslip.pdf');
    Route::get('payslip/payslipPdf/{id}', [PaySlipController::class, 'payslipPdf'])->name('payslip.payslipPdf');