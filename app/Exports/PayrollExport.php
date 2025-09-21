<?php

namespace App\Exports;

use App\Models\payroll;
use Maatwebsite\Excel\Concerns\FromCollection;

class PayrollExport implements FromCollection
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
        // You can filter by $this->date if needed
        return Payroll::with('employee')
            ->get()
            ->map(function ($payroll) {
                return [
                    'Employee ID' => $payroll->employee->employee_id ?? '',
                    'Name'        => $payroll->employee->name ?? '',
                    'Email'       => $payroll->employee->email ?? '',
                    'Basic Salary'=> $payroll->basic_salary,
                    'Net Salary'  => $payroll->net_salary,
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
        ];
    }
}
