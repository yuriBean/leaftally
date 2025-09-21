<?php

namespace App\Exports;

use App\Models\Bill;
use App\Models\Utility;
use Illuminate\Support\Arr;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class BillExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithTitle
{
    /** @var int[]|null */
    protected ?array $onlyIds;

    /**
     * @param int[]|null $onlyIds  When provided, export only these bill IDs
     */
    public function __construct(?array $onlyIds = null)
    {
        // Normalize and keep integers only
        $this->onlyIds = $onlyIds && count($onlyIds)
            ? array_values(array_unique(array_map('intval', $onlyIds)))
            : null;
    }

    public function collection()
    {
        // Owner vs Vendor portal
        $query = !\Auth::guard('vender')->check()
            ? Bill::where('created_by', \Auth::user()->id)
            : Bill::where('vender_id', \Auth::guard('vender')->user()->id)->where('status', '!=', '0');

        if ($this->onlyIds) {
            $query->whereIn('id', $this->onlyIds);
        }

        // Eager-load CORRECT relations (no static helpers):
        return $query->with(['items', 'vender', 'category'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function map($bill): array
    {
        $statusLabels = [
            0 => 'Draft',
            1 => 'Sent',
            2 => 'Unpaid',
            3 => 'Partially Paid',
            4 => 'Paid',
        ];
        $status = $statusLabels[$bill->status] ?? 'Unknown';

        $billNumber = !\Auth::guard('vender')->check()
            ? \Auth::user()->billNumberFormat($bill->bill_id)
            : \App\Models\Vender::billNumberFormat($bill->bill_id);

        $vendorName   = optional($bill->vender)->name ?? '';
        $categoryName = optional($bill->category)->name ?? '';

        // Totals (match your Bill model logic: discounts reduce tax base)
        $subtotal = 0.0;
        $totalTax = 0.0;

        foreach ($bill->items as $item) {
            $lineBase = ($item->price * $item->quantity) - ($item->discount ?? 0);
            $subtotal += $lineBase;

            if ($item->tax) {
                $taxes = Utility::tax($item->tax);
                foreach ($taxes as $tax) {
                    // taxRate(rate, price, qty, discount)
                    $totalTax += Utility::taxRate($tax->rate, $item->price, $item->quantity, $item->discount);
                }
            }
        }

        return [
            $billNumber,
            $vendorName,
            $bill->bill_date ? date('Y-m-d', strtotime($bill->bill_date)) : '',
            $bill->due_date ? date('Y-m-d', strtotime($bill->due_date)) : '',
            $bill->order_number ?? '',
            $status,
            $bill->send_date ? date('Y-m-d', strtotime($bill->send_date)) : '',
            $categoryName,
            number_format($subtotal, 2),
            number_format($totalTax, 2),
            number_format($subtotal + $totalTax, 2),
            date('Y-m-d', strtotime($bill->created_at)),
        ];
    }

    public function headings(): array
    {
        return [
            'Bill Number',
            'Vendor Name',
            'Bill Date',
            'Due Date',
            'Order Number',
            'Status',
            'Send Date',
            'Category',
            'Subtotal',
            'Tax Amount',
            'Total Amount',
            'Created Date',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 12, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => [
                    'fillType'   => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF007C38'],
                ],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 15, // Bill Number
            'B' => 25, // Vendor Name
            'C' => 12, // Bill Date
            'D' => 12, // Due Date
            'E' => 15, // Order Number
            'F' => 12, // Status
            'G' => 12, // Send Date
            'H' => 15, // Category
            'I' => 12, // Subtotal
            'J' => 12, // Tax
            'K' => 12, // Total
            'L' => 12, // Created Date
        ];
    }

    public function title(): string
    {
        $companyName = \Auth::user()->name ?? 'Company';
        return "Bills - {$companyName}";
    }
}
