<?php

namespace App\Exports;

use App\Models\Tax;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class TaxExport implements FromCollection, WithHeadings, WithMapping
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
        $query = Tax::query()
            ->where('created_by', \Auth::user()->creatorId())
            ->select(['id', 'name', 'rate', 'created_at']);

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
            'Rate (%)',
            'Created At',
        ];
    }

    public function map($tax): array
    {
        return [
            $tax->id,
            $tax->name,
            (float) $tax->rate,
            optional($tax->created_at)->format('Y-m-d H:i:s'),
        ];
    }
}
