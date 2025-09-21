<?php

namespace App\Exports;

use App\Models\payroll;
use Maatwebsite\Excel\Concerns\FromCollection;

class PayrollSlipExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
      protected $date;

    public function __construct($date = null)
    {
        $this->date = $date;
    }

      public function collection()
    {
        return Payroll::with('employee')->whereNotNull('payroll_month')
            ->get()
            ->map(function ($payroll) {
                return [
                    'Employee ID'    => $payroll->employee->employee_id ?? '',
                    'Name'           => $payroll->employee->name ?? '',
                    'Email'          => $payroll->employee->email ?? '',
                    'Basic Salary'   => $payroll->basic_salary,
                    'Net Salary'     => $payroll->net_salary,
                    'Payroll Month'  => $payroll->payroll_month,
                    'Payment Month'  => $payroll->payment_date, // This should be 'payment_date' not 'payment_month'
                    'Status'         => ucfirst($payroll->status),
                ];
            });
    }

    public function headings(): array
    {
        return [
            'Employee ID',
            'Name',
            'Email',
            'Basic Salary',
            'Net Salary',
            'Payroll Month',
            'Payment Month',
            'Status',
        ];
    }
}
