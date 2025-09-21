<?php
namespace App\Exports;

use App\Models\Customer;
use App\Models\Retainer;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Collection;

class RetainerExport implements FromCollection , WithHeadings
{
    /** @var array<int>|null */
    protected $ids;

    // NEW: allow optional selected IDs
    public function __construct(?array $ids = null)
    {
        $this->ids = $ids ? array_filter($ids) : null;
    }

    public function collection()
    {
        $data = collect();

        if (!\Auth::guard('customer')->check()) {
            // BUGFIX: use creatorId() (multi-tenant), not user->id
            $q = Retainer::where('created_by', \Auth::user()->creatorId());
        } else {
            // BUGFIX: check() returns bool; get the customer id
            $customerId = \Auth::guard('customer')->user()->id;
            $q = Retainer::where('customer_id', $customerId)->where('status', '!=', '0');
        }

        // NEW: if selected IDs provided, filter to them
        if (!empty($this->ids)) {
            $q->whereIn('id', $this->ids);
        }

        $rows = $q->get();

        foreach ($rows as $k => $retainer) {
            $customer = Retainer::customers($retainer->customer_id);
            $category = Retainer::RetainerCategory($retainer->category_id);

            $status = match ((int) $retainer->status) {
                0 => 'Draft',
                1 => 'Sent',
                2 => 'Unpaid',
                3 => 'Partially Paid', // minor spelling polish
                4 => 'Paid',
                default => (string) $retainer->status,
            };

            // Keep your original number formatting behavior
            if (!\Auth::guard('customer')->check()) {
                $rid = \Auth::user()->retainerNumberFormat($retainer->retainer_id);
            } else {
                $rid = Customer::retainerNumberFormat($retainer->retainer_id);
            }

            $data->push([
                "Retainer_Id"          => $rid,
                "Customer_name"        => $customer,
                "issue Date"           => $retainer->issue_date,
                "Due Date"             => $retainer->due_date,
                "Send Date"            => $retainer->send_date,
                "Category Id"          => $category,
                "status"               => $status,
                "discount_apply"       => $retainer->discount_apply,
                "converted_invoice_id" => $retainer->converted_invoice_id,
                "is_convert"           => $retainer->is_convert,
            ]);
        }

        return $data;
    }

    public function headings(): array
    {
        return [
            "Retainer_Id",
            "Customer_name",
            "issue Date",
            "Due Date",
            "Send Date",
            "Category Id",
            "status",
            "discount_apply",
            "converted_invoice_id",
            "is_convert",
        ];
    }
}
