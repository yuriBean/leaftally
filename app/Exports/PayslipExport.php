<?php

namespace App\Exports;

use App\Models\PaySlip;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PayslipExport implements FromCollection, WithHeadings
{
    /**
     * The incoming request (or data holder) passed from the controller.
     * We expect it to contain ids[].
     *
     * @var mixed
     */
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        $request = $this->data;

        // Selected payslip ids only (buttons are disabled if none selected)
        $ids = array_filter((array) $request->input('ids', []));
        if (empty($ids)) {
            // nothing to export – return empty collection
            return collect([]);
        }

        // Pull only selected payslips for this tenant; eager load employee relation
        $payslips = PaySlip::with(['employees', 'employee']) // support either relation name
            ->where('created_by', \Auth::user()->creatorId())
            ->whereIn('id', $ids)
            ->get();

        $rows = [];
        foreach ($payslips as $payslip) {
            // support both $payslip->employees and $payslip->employee relation names
            $emp = $payslip->employees ?? $payslip->employee ?? null;

            $rows[] = [
                // "Employee No"
                $emp ? \Auth::user()->employeeIdFormat($emp->employee_id) : '',
                // "Name"
                $emp->name ?? '',
                // "Salary"
                \Auth::user()->priceFormat($payslip->basic_salary),
                // "Net Salary"
                \Auth::user()->priceFormat($payslip->net_payble),
                // "Status"
                $payslip->status == 0 ? 'UnPaid' : 'Paid',
                // "Account Holder Name"
                $emp->account_holder_name ?? '',
                // "Account Number"
                $emp->account_number ?? '',
                // "Bank Name"
                $emp->bank_name ?? '',
                // "Bank Identifier Code"
                $emp->bank_identifier_code ?? '',
                // "Branch Location"
                $emp->branch_location ?? '',
                // "Tax Payer Id"
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
