<?php

namespace App\Exports;

use App\Models\ProductServiceUnit;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ProductUnitExport implements FromCollection, WithHeadings, WithMapping
{
    /**
     * @var array<int>
     */
    protected $ids;

    /**
     * @param array<int>|null $ids  Export only these IDs if provided
     */
    public function __construct(?array $ids = null)
    {
        $this->ids = $ids ?: [];
    }

    public function collection()
    {
        $q = ProductServiceUnit::query()
            ->where('created_by', \Auth::user()->creatorId())
            ->select(['id', 'name', 'created_at'])
            ->orderBy('id', 'asc');

        if (!empty($this->ids)) {
            $q->whereIn('id', $this->ids);
        }

        return $q->get();
    }

    public function headings(): array
    {
        return ['ID', 'Unit', 'Created At'];
    }

    public function map($row): array
    {
        return [
            $row->id,
            $row->name,
            optional($row->created_at)->format('Y-m-d H:i:s'),
        ];
    }
}
