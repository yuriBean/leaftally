<?php

namespace App\Exports;

use App\Models\ContractType;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ContractTypeExport implements FromCollection, WithHeadings, WithMapping
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

    public function collection()
    {
        $q = ContractType::query()
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
