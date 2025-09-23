<?php

namespace App\Exports;

use App\Models\Customer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CustomerExport implements FromCollection, WithHeadings
{
    protected ?array $ids;

    public function __construct(?array $ids = null)
    {
        $this->ids = $ids ? array_values($ids) : null;
    }

    public function collection(): Collection
    {
        $q = Customer::query();

        if (Auth::user()->type == 'company') {
            $q->where('created_by', Auth::user()->id);
        }

        if (!empty($this->ids)) {
            $q->whereIn('id', $this->ids);
        }

        $rows = $q->get();

        return $rows->map(function (Customer $c) {
            return [
                Auth::user()->customerNumberFormat($c->customer_id),
                $c->name,
                $c->email,
                $c->tax_number,
                $c->contact,
                $c->billing_name,
                $c->billing_country,
                $c->billing_state,
                $c->billing_city,
                $c->billing_phone,
                $c->billing_zip,
                $c->billing_address,
                $c->shipping_name,
                $c->shipping_country,
                $c->shipping_state,
                $c->shipping_city,
                $c->shipping_phone,
                $c->shipping_zip,
                $c->shipping_address,
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
