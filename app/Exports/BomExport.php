<?php

namespace App\Exports;

use App\Models\Bom;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class BomExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithTitle
{
    protected $ids;

    public function __construct(?array $ids = null)
    {
        $this->ids = $ids;
    }

    public function collection()
    {
        $q = Bom::where('created_by', Auth::user()->creatorId())
            ->withCount(['inputs', 'outputs'])
            ->orderBy('created_at', 'desc');

        if (!empty($this->ids)) {
            $q->whereIn('id', $this->ids);
        }

        return $q->get();
    }

    public function map($bom): array
    {
        return [
            $bom->code,
            $bom->name,
            $bom->is_active ? 'Yes' : 'No',
            (int) ($bom->inputs_count ?? $bom->inputs()->count()),
            (int) ($bom->outputs_count ?? $bom->outputs()->count()),
            $bom->created_at ? $bom->created_at->format('Y-m-d') : '',
        ];
    }

    public function headings(): array
    {
        return [
            'Code',
            'Name',
            'Active',
            'Inputs',
            'Outputs',
            'Created Date',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 12, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF007C38'],
                ],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 16,
            'B' => 30,
            'C' => 10,
            'D' => 10,
            'E' => 10,
            'F' => 14,
        ];
    }

    public function title(): string
    {
        $companyName = Auth::user()->name ?? 'Company';
        return "BOMs - {$companyName}";
    }
}
