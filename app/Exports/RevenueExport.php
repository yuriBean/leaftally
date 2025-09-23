<?php

namespace App\Exports;

use App\Models\Revenue;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class RevenueExport implements FromCollection, WithHeadings
{
    protected array $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters ?? [];
    }

    public function collection()
    {
        $creatorId = \Auth::user()->creatorId();

        $query = Revenue::with(['bankAccount', 'customer', 'category'])
            ->where('created_by', $creatorId);

        if (!empty($this->filters['ids']) && is_array($this->filters['ids'])) {
            $ids = array_values(array_unique(array_map('intval', $this->filters['ids'])));
            if (!empty($ids)) {
                $query->whereIn('id', $ids);
            }
        } else {
            $date = $this->filters['date'] ?? null;
            if (!empty($date)) {
                if (str_contains($date, ' to ')) {
                    [$from, $to] = explode(' to ', $date, 2);
                    $query->whereBetween('date', [trim($from), trim($to)]);
                } else {
                    $query->where('date', $date);
                }
            }
        }

        return $query->orderBy('date', 'desc')->get()->map(function ($r) {
            $account = $r->bankAccount
                ? trim(($r->bankAccount->bank_name ?? '') . ' ' . ($r->bankAccount->holder_name ?? ''))
                : '';
            $customer = $r->customer->name ?? '';
            $category = $r->category->name ?? '';

            return [
                $r->id,
                $r->date,
                (float) $r->amount,
                $account,
                $customer,
                $category,
                $r->reference ?? '',
                $r->description ?? '',
            ];
        });
    }

    public function headings(): array
    {
        return [
            "Revenue Id",
            "Date",
            "Amount",
            "Account",
            "Customer",
            "Category",
            "Reference",
            "Description",
        ];
    }
}
