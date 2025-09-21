<?php

namespace App\Exports;

use App\Models\ProductServiceCategory;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ProductCategoryExport implements FromCollection, WithHeadings, WithMapping
{
    /**
     * @var array<int>
     */
    protected $ids;

    /**
     * @param array<int>|null $ids  If provided, export only these IDs
     */
    public function __construct(?array $ids = null)
    {
        $this->ids = $ids ?: [];
    }

    public function collection()
    {
        $query = ProductServiceCategory::query()
            ->with('chartAccount')
            ->where('created_by', \Auth::user()->creatorId())
            ->select(['id', 'name', 'type', 'chart_account_id', 'color', 'created_at']);

        if (!empty($this->ids)) {
            $query->whereIn('id', $this->ids);
        }

        return $query->orderBy('id', 'asc')->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Type',
            'Account',
            'Color',
            'Created At',
        ];
    }

    public function map($row): array
    {
        $types = \App\Models\ProductServiceCategory::$catTypes;
        $typeLabel = $types[$row->type] ?? (string)$row->type;

        return [
            $row->id,
            $row->name,
            $typeLabel,
            optional($row->chartAccount)->name ?? '-',
            $row->color,
            optional($row->created_at)->format('Y-m-d H:i:s'),
        ];
    }
}
