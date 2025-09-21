<?php

namespace App\Exports;

use App\Models\Customer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CustomerExport implements FromCollection, WithHeadings
{
    /** @var array<int,string|int>|null */
    protected ?array $ids;

    /**
     * Pass selected IDs if you want only those exported; leave null to export all.
     *
     * @param array<int,string|int>|null $ids
     */
    public function __construct(?array $ids = null)
    {
        $this->ids = $ids ? array_values($ids) : null;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection(): Collection
    {
        $q = Customer::query();

        // Match your original scoping
        if (Auth::user()->type == 'company') {
            $q->where('created_by', Auth::user()->id);
        }

        if (!empty($this->ids)) {
            $q->whereIn('id', $this->ids);
        }

        $rows = $q->get();

        // Build rows in the same order as headings
        return $rows->map(function (Customer $c) {
            return [
                // "Customer ID"
                Auth::user()->customerNumberFormat($c->customer_id),
                // "Name"
                $c->name,
                // "Email"
                $c->email,
                // "Tax Number"  (your heading had "Tex Number" – corrected here; adjust if you need the typo)
                $c->tax_number,
                // "Contact"
                $c->contact,
                // "Billing Name"
                $c->billing_name,
                // "Billing Country"
                $c->billing_country,
                // "Billing State"
                $c->billing_state,
                // "Billing City"
                $c->billing_city,
                // "Billing Phone"
                $c->billing_phone,
                // "Billing Zip"
                $c->billing_zip,
                // "Billing Address"
                $c->billing_address,
                // "Shipping Name"
                $c->shipping_name,
                // "Shipping Country"
                $c->shipping_country,
                // "Shipping State"
                $c->shipping_state,
                // "Shipping City"
                $c->shipping_city,
                // "Shipping Phone"
                $c->shipping_phone,
                // "Shipping Zip"
                $c->shipping_zip,
                // "Shipping Address"
                $c->shipping_address,
                // "Balance"
                Auth::user()->priceFormat($c->balance),
            ];
        });
    }

    public function headings(): array
    {
        return [
            "Customer ID",
            "Name",
            "Email",
            "Tax Number",
            "Contact",
            "Billing Name",
            "Billing Country",
            "Billing State",
            "Billing City",
            "Billing Phone",
            "Billing Zip",
            "Billing Address",
            "Shipping Name",
            "Shipping Country",
            "Shipping State",
            "Shipping City",
            "Shipping Phone",
            "Shipping Zip",
            "Shipping Address",
            "Balance",
        ];
    }
}
