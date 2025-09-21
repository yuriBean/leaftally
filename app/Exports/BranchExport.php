<?php

namespace App\Exports;

use App\Models\Branch;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class BranchExport implements FromCollection, WithHeadings, WithMapping
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
        $q = Branch::query()->select(['id', 'name', 'created_at'])->orderBy('name', 'asc');
        if (!empty($this->ids)) {
            $q->whereIn('id', $this->ids);
        }
        return $q->get();
    }

    public function headings(): array
    {
        return ['ID', 'Name', 'Created At'];
    }

    /**
     * @param \App\Models\Branch $row
     */
    public function map($row): array
    {
        return [
            $row->id,
            $row->name,
            optional($row->created_at)->format('Y-m-d H:i:s'),
        ];
    }
}
