<?php

namespace App\Exports;

use App\Models\Customer;
use App\Models\Proposal;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ProposalExport implements FromCollection, WithHeadings
{
    /** @var array<int,int|string>|null */
    protected ?array $ids;

    public function __construct(?array $ids = null)
    {
        $this->ids = $ids ? array_values($ids) : null;
    }

    public function collection()
    {
        // Base query: company/staff vs customer portal
        if (!Auth::guard('customer')->check()) {
            $q = Proposal::query()->where('created_by', Auth::user()->creatorId());
        } else {
            $customerId = Auth::guard('customer')->user()->id;
            $q = Proposal::query()->where('customer_id', $customerId)->where('status', '!=', '0');
        }

        if (!empty($this->ids)) {
            $q->whereIn('id', $this->ids);
        }

        $rows = $q->get();

        // Build friendly export data
        $out = collect();
        foreach ($rows as $p) {
            $statusText = [
                0 => 'Draft',
                1 => 'Open',
                2 => 'Accepted',
                3 => 'Declined',
                4 => 'Close',
            ][$p->status] ?? '';

            $out->push([
                'Proposal_No'           => !Auth::guard('customer')->check()
                                            ? Auth::user()->proposalNumberFormat($p->proposal_id)
                                            : Customer::proposalNumberFormat($p->proposal_id),
                'Customer_Name'         => optional($p->customer)->name,
                'Issue_Date'            => $p->issue_date,
                'Send_Date'             => $p->send_date,
                'Category'              => optional($p->category)->name,
                'Status'                => $statusText,
                'Discount_Apply'        => $p->discount_apply ? 'Yes' : 'No',
                'Converted_Invoice_ID'  => $p->converted_invoice_id,
                'Is_Convert'            => $p->is_convert ? 'Yes' : 'No',
            ]);
        }

        return $out;
    }

    public function headings(): array
    {
        return [
            'Proposal_No',
            'Customer_Name',
            'Issue_Date',
            'Send_Date',
            'Category',
            'Status',
            'Discount_Apply',
            'Converted_Invoice_ID',
            'Is_Convert',
        ];
    }
}
