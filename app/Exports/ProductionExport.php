<?php

namespace App\Exports;

use App\Models\ProductionOrder;
use App\Models\ProductionConsumption;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ProductionExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithTitle
{
    protected ?array $ids;

    public function __construct(?array $ids = null)
    {
        $this->ids = $ids;
    }

    public function collection()
    {
        $query = ProductionOrder::query()
            ->where('created_by', \Auth::user()->creatorId())
            ->with('bom')
            ->orderBy('created_at', 'desc');

        if ($this->ids && count($this->ids) > 0) {
            $query->whereIn('id', $this->ids);
        }

        return $query->get();
    }

    public function map($row): array
    {
        $status = match ((string)$row->status) {
            '0' => 'Draft',
            '1' => 'In Process',
            '2' => 'Finished',
            '3' => 'Cancelled',
            default => 'Draft',
        };

        $rawTotal = (float) ProductionConsumption::where('production_order_id', $row->id)->sum('total_cost');
        $mfgCost  = (float) ($row->manufacturing_cost ?? 0);
        $total    = $rawTotal + $mfgCost;

        return [
            $row->code,
            optional($row->bom)->name ?? '',
            $status,
            $row->planned_date ? date('Y-m-d', strtotime($row->planned_date)) : '',
            $row->started_at ? date('Y-m-d', strtotime($row->started_at)) : '',
            $row->finished_at ? date('Y-m-d', strtotime($row->finished_at)) : '',
            number_format((float)$row->multiplier, 4, '.', ''),
            number_format($rawTotal, 2, '.', ''),
            number_format($mfgCost, 2, '.', ''),
            number_format($total, 2, '.', ''),
            $row->notes ?? '',
            date('Y-m-d', strtotime($row->created_at)),
        ];
    }

    public function headings(): array
    {
        return [
            'Code',
            'BOM',
            'Status',
            'Planned Date',
            'Started',
            'Finished',
            'Batch Multiplier',
            'Raw Cost',
            'Manufacturing Cost',
            'Total Cost',
            'Notes',
            'Created Date',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 12, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF007C38']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 16,
            'B' => 26,
            'C' => 14,
            'D' => 14,
            'E' => 14,
            'F' => 14,
            'G' => 16,
            'H' => 14,
            'I' => 18,
            'J' => 14,
            'K' => 30,
            'L' => 14,
        ];
    }

    public function title(): string
    {
        $companyName = \Auth::user()->name ?? 'Company';
        return "Production Orders - {$companyName}";
    }
}
