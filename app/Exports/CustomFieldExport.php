<?php

namespace App\Exports;

use App\Models\CustomField;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class CustomFieldExport implements FromCollection, WithHeadings, WithMapping
{
    protected $ids;

    public function __construct(?array $ids = null)
    {
        $this->ids = $ids ?: [];
    }

    public function collection(): Collection
    {
        $q = CustomField::query()
            ->where('created_by', \Auth::user()->creatorId())
            ->select(['id', 'name', 'type', 'module', 'options', 'created_at'])
            ->orderBy('id', 'asc');

        if (!empty($this->ids)) {
            $q->whereIn('id', $this->ids);
        }

        return $q->get();
    }

    public function headings(): array
    {
        return ['ID', 'Name', 'Type', 'Module', 'Options', 'Created At'];
    }

    public function map($row): array
    {
        return [
            $row->id,
            $row->name,
            $row->type,
            $row->module,
            (string) ($row->options ?? ''),
            optional($row->created_at)->format('Y-m-d H:i:s'),
        ];
    }
}
