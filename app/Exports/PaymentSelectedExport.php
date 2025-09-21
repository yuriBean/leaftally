<?php

namespace App\Exports;

use App\Models\Payment;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PaymentSelectedExport implements FromCollection, WithHeadings
{
    protected array $ids;

    public function __construct(array $ids)
    {
        $this->ids = $ids;
    }

    public function collection()
    {
        $rows = Payment::whereIn('id', $this->ids)->get();

        foreach ($rows as $k => $payment) {
            $account  = Payment::accounts($payment->account_id);
            $vendor   = Payment::vendors($payment->vender_id);
            $category = Payment::categories($payment->category_id);

            unset(
                $payment->created_by,
                $payment->updated_at,
                $payment->created_at,
                $payment->payment_method,
                $payment->add_receipt
            );

            $rows[$k]['account_id']  = $account;
            $rows[$k]['vender_id']   = $vendor;
            $rows[$k]['category_id'] = $category;
        }

        return $rows;
    }

    public function headings(): array
    {
        return [
            "Payment Id",
            "Date",
            "Amount",
            "Account",
            "Vendor",
            "Description",
            "Category",
            "Reference",
        ];
    }
}
