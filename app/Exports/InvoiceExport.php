<?php

namespace App\Exports;

use App\Models\Invoice;
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

class InvoiceExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithTitle
{
    protected ?array $onlyIds;

    public function __construct(?array $onlyIds = null)
    {
        $this->onlyIds = $onlyIds && count($onlyIds) ? array_map('intval', $onlyIds) : null;
    }

    public function collection()
    {
        $query = \Auth::guard('customer')->check()
            ? Invoice::where('customer_id', \Auth::guard('customer')->user()->id)
                ->where('status', '!=', '0')
            : Invoice::where('created_by', \Auth::user()->id);

        if ($this->onlyIds) {
            $query->whereIn('id', $this->onlyIds);
        }

        return $query->with(['items', 'customer', 'category'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function map($invoice): array
    {
        $statusLabels = [
            0 => 'Draft',
            1 => 'Sent',
            2 => 'Unpaid',
            3 => 'Partially Paid',
            4 => 'Paid',
        ];
        $status = $statusLabels[$invoice->status] ?? 'Unknown';

        $invoiceNumber = !\Auth::guard('customer')->check()
            ? \Auth::user()->invoiceNumberFormat($invoice->invoice_id)
            : \App\Models\Customer::invoiceNumberFormat($invoice->invoice_id);

        $customerName = optional($invoice->customer)->name ?? '';
        $categoryName = optional($invoice->category)->name ?? '';

        $subtotal = 0.0;
        $totalTax = 0.0;
        foreach ($invoice->items as $item) {
            $lineBase = ($item->price * $item->quantity) - ($item->discount ?? 0);
            $subtotal += $lineBase;
            if ($item->tax) {
                $taxes = \App\Models\Utility::tax($item->tax);
                foreach ($taxes as $tax) {
                    $totalTax += \App\Models\Utility::taxRate($tax->rate, $item->price, $item->quantity);
                }
            }
        }

        return [
            $invoiceNumber,
            $customerName,
            $invoice->issue_date ? date('Y-m-d', strtotime($invoice->issue_date)) : '',
            $invoice->due_date ? date('Y-m-d', strtotime($invoice->due_date)) : '',
            $invoice->send_date ? date('Y-m-d', strtotime($invoice->send_date)) : '',
            $categoryName,
            $invoice->ref_number ?? '',
            $status,
            number_format($subtotal, 2),
            number_format($totalTax, 2),
            number_format($subtotal + $totalTax, 2),
            date('Y-m-d', strtotime($invoice->created_at)),
        ];
    }

    public function headings(): array
    {
        return [
            'Invoice Number',
            'Customer Name',
            'Issue Date',
            'Due Date',
            'Send Date',
            'Category',
            'Reference Number',
            'Status',
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
            'A' => 15, 'B' => 25, 'C' => 12, 'D' => 12, 'E' => 12,
            'F' => 15, 'G' => 15, 'H' => 12, 'I' => 12, 'J' => 12,
            'K' => 12, 'L' => 12,
        ];
    }

    public function title(): string
    {
        $companyName = Auth::user()->name ?? 'Company';
        return 'Invoices - ' . $companyName;
    }
}
