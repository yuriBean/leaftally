<?php

namespace App\Exports;

use App\Models\Goal;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class GoalExport implements FromCollection, WithHeadings, WithMapping
{
    protected $ids;

    public function __construct(?array $ids = null)
    {
        $this->ids = $ids ?: [];
    }

    public function collection()
    {
        $query = Goal::query()
            ->where('created_by', \Auth::user()->creatorId())
            ->select(['id','name','type','from','to','amount','is_display','created_at']);

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
            'From',
            'To',
            'Amount',
            'Dashboard Display',
            'Created At',
        ];
    }

    public function map($goal): array
    {
        $types = Goal::$goalType;

        return [
            $goal->id,
            $goal->name,
            $types[$goal->type] ?? $goal->type,
            $goal->from,
            $goal->to,
            $goal->amount,
            $goal->is_display ? 'Yes' : 'No',
            optional($goal->created_at)->format('Y-m-d H:i:s'),
        ];
    }
}
