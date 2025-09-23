<?php

namespace App\Exports;

use App\Models\Employee;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class EmployeeExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $ids;

    public function __construct(?array $ids = null)
    {
        $this->ids = $ids ? array_values(array_filter($ids, fn ($v) => !is_null($v))) : null;
    }

    public function collection()
    {
        $query = Employee::query()
            ->with(['branch:id,name', 'department:id,name', 'designation:id,name', 'salaryType:id,name'])
            ->where('created_by', \Auth::user()->creatorId());

        if (!empty($this->ids)) {
            $query->whereIn('id', $this->ids);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'Employee ID',
            'Name',
            'Contact',
            'Email',
            'Branch',
            'Department',
            'Designation',
            'Date of Joining',
            'Account Holder',
            'Account Number',
            'Bank Name',
            'Bank Identifier',
            'Branch Location',
            'Tax Payer ID',
        ];
    }

    public function map($employee): array
    {
        $doj = $employee->company_doj ?? $employee->date_of_joining ?? null;
        $dojFormatted = $doj ? optional(\Carbon\Carbon::parse($doj))->format('Y-m-d') : '';

        return [
            $employee->employee_id,
            $employee->name,
            $employee->phone ?? $employee->contact ?? '',
            $employee->email,
            optional($employee->branch)->name ?: '',
            optional($employee->department)->name ?: '',
            optional($employee->designation)->name ?: '',
            $dojFormatted,
            $employee->account_holder_name ?? '',
            $employee->account_number ?? '',
            $employee->bank_name ?? '',
            $employee->bank_identifier_code ?? '',
            $employee->branch_location ?? '',
            $employee->tax_payer_id ?? '',
        ];
    }
}
