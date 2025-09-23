<?php

namespace App\Exports;

use App\Models\Asset;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AssetExport implements FromCollection, WithHeadings
{
    protected $ids;

    public function __construct(?array $ids = null)
    {
        $this->ids = $ids ?? [];
    }

    public function collection(): Collection
    {
        $q = Asset::query()->where('created_by', \Auth::user()->creatorId());
        if (!empty($this->ids)) {
            $q->whereIn('id', $this->ids);
        }

        return $q->get()->map(function (Asset $a) {
            return [
                'id'                  => $a->id,
                'name'                => $a->name,
                'purchase_date'       => $a->purchase_date,
                'supported_date'      => $a->supported_date,
                'amount'              => (float)$a->amount,
                'depreciation_rate'   => (float)($a->depreciation_rate ?? 0),
                'current_book_value'  => (float)$a->getCurrentBookValue(),
                'description'         => (string)($a->description ?? ''),
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Asset ID',
            'Name',
            'Purchase Date',
            'Supported Date',
            'Amount',
            'Depreciation %',
            'Current Book Value',
            'Description',
        ];
    }
}
