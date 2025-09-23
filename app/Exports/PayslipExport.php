<?php

namespace App\Exports;

use App\Models\PaySlip;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PayslipExport implements FromCollection, WithHeadings
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        $request = $this->data;

        $ids = array_filter((array) $request->input('ids', []));
        if (empty($ids)) {
            return collect([]);
        }

        $payslips = PaySlip::with(['employees', 'employee'])
            ->where('created_by', \Auth::user()->creatorId())
            ->whereIn('id', $ids)
            ->get();

        $rows = [];
        foreach ($payslips as $payslip) {
            $emp = $payslip->employees ?? $payslip->employee ?? null;

            $rows[] = [
                $emp ? \Auth::user()->employeeIdFormat($emp->employee_id) : '',
                $emp->name ?? '',
                \Auth::user()->priceFormat($payslip->basic_salary),
                \Auth::user()->priceFormat($payslip->net_payble),
                $payslip->status == 0 ? 'UnPaid' : 'Paid',
                $emp->account_holder_name ?? '',
                $emp->account_number ?? '',
                $emp->bank_name ?? '',
                $emp->bank_identifier_code ?? '',
                $emp->branch_location ?? '',
                $emp->tax_payer_id ?? '',
            ];
        }

        return collect($rows);
    }

    public function headings(): array
    {
        return [
            'Employee No',
            'Name',
            'Salary',
            'Net Salary',
            'Status',
            'Account Holder Name',
            'Account Number',
            'Bank Name',
            'Bank Identifier Code',
            'Branch Location',
            'Tax Payer Id',
        ];
    }
}
