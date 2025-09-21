<?php

namespace App\Exports;

use App\Models\ProductService;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class CurrentStockExport implements FromCollection, WithHeadings, WithMapping
{
    /** @var array<int,string|int>|null */
    protected ?array $productIds;

    public function __construct(?array $productIds = null)
    {
        // keep raw values (don’t cast to int if you use UUIDs)
        $this->productIds = $productIds ? array_values($productIds) : null;
    }

    public function collection()
    {
        // Use creatorId() so employees export the company’s catalog too
        $q = ProductService::query()
            ->where('created_by', Auth::user()->creatorId())
            ->with(['unit:id,name', 'category:id,name'])
            ->orderBy('name');

        if (!empty($this->productIds)) {
            $q->whereIn('id', $this->productIds);
        }

        // fetch only columns we actually need
        return $q->get([
            'id', 'name', 'sku', 'type', 'quantity', 'unit_id', 'category_id', 'updated_at',
        ]);
    }

    /** @param \App\Models\ProductService $row */
    public function map($row): array
    {
        return [
            $row->id,
            $row->name,
            $row->sku,
            $row->type,
            (int) $row->quantity,
            optional($row->unit)->name,
            optional($row->category)->name,
            optional($row->updated_at)?->format('Y-m-d H:i'),
        ];
    }

    public function headings(): array
    {
        return [
            'ID',
            'Product',
            'SKU',
            'Type',
            'Current Quantity',
            'Unit',
            'Category',
            'Last Updated',
        ];
    }
}
