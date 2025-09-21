<?php

namespace App\Exports;

use App\Models\Contract;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ContractExport implements FromCollection, WithHeadings
{
    /** @var array<int> */
    protected $ids;

    /**
     * @param array<int>|null $ids  If provided, export only these IDs (Export Selected).
     *                              If null/empty, export all visible to the user (Export All).
     */
    public function __construct(?array $ids = null)
    {
        $this->ids = $ids ?? [];
    }

    public function collection(): Collection
    {
        $user = \Auth::user();

        $q = Contract::query();

        if ($user->type === 'company') {
            $q->where('created_by', $user->creatorId());
        } else {
            // customers see only their own contracts
            $q->where('customer', $user->id);
        }

        if (!empty($this->ids)) {
            $q->whereIn('id', $this->ids);
        }

        $contracts = $q->with(['clients', 'types'])->get();

        return $contracts->map(function (Contract $c) {
            return [
                'ID'          => $c->id,
                'Contract #'  => \Auth::user()->contractNumberFormat($c->id),
                'Subject'     => (string) $c->subject,
                'Customer'    => optional($c->clients)->name ?? '',
                'Type'        => optional($c->types)->name ?? '',
                'Value'       => (float) $c->value,
                'Start Date'  => $c->start_date,
                'End Date'    => $c->end_date,
                'Status'      => ucfirst((string) $c->edit_status),
                'Description' => (string) ($c->description ?? ''),
            ];
        });
    }

    public function headings(): array
    {
        return [
            'ID',
            'Contract #',
            'Subject',
            'Customer',
            'Type',
            'Value',
            'Start Date',
            'End Date',
            'Status',
            'Description',
        ];
    }
}
