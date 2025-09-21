<?php

namespace App\Exports;

use App\Models\DeductionType;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class DeductionTypeExport implements FromCollection, WithHeadings, WithMapping
{
    /**
     * @var array<int>
     */
    protected $ids;

    /**
     * @param array<int>|null $ids
     */
    public function __construct(?array $ids = null)
    {
        $this->ids = $ids ?: [];
    }

    public function collection(): Collection
    {
        $q = DeductionType::query()
            ->select(['id', 'name', 'created_at'])
            ->orderBy('name', 'asc');

        if (!empty($this->ids)) {
            $q->whereIn('id', $this->ids);
        }

        return $q->get();
    }

    public function headings(): array
    {
        return ['ID', 'Name', 'Created At'];
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
