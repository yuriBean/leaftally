<?php

namespace App\Exports;

use App\Models\Vender;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class VenderExport implements FromCollection, WithHeadings
{
    protected ?array $ids;

    public function __construct(?array $ids = null)
    {
        $this->ids = $ids ? array_values($ids) : null;
    }

    public function collection()
    {
        $q = Vender::query();

        if (Auth::user()->type == 'company') {
            $q->where('created_by', Auth::user()->id);
        }

        if (!empty($this->ids)) {
            $q->whereIn('id', $this->ids);
        }

        $data = $q->get();

        if ($data->isNotEmpty()) {
            foreach ($data as $k => $vendor) {
                unset(
                    $vendor->id,
                    $vendor->user_name,
                    $vendor->avatar,
                    $vendor->password,
                    $vendor->is_enable_login,
                    $vendor->lang,
                    $vendor->created_at,
                    $vendor->updated_at,
                    $vendor->created_by,
                    $vendor->last_login_at,
                    $vendor->is_active,
                    $vendor->email_verified_at,
                    $vendor->remember_token
                );
                $data[$k]['vender_id'] = Auth::user()->venderNumberFormat($vendor->vender_id);
                $data[$k]['balance']   = Auth::user()->priceFormat($vendor->balance);
            }
        }
        return $data;
    }

    public function headings(): array
    {
        return [
            'Vendor ID',
            'Name',
            'Email',
            'Tax Number',
            'Contact',
            'Billing Name',
            'Billing Country',
            'Billing State',
            'Billing City',
            'Billing Phone',
            'Billing Zip',
            'Billing Address',
            'Shipping Name',
            'Shipping Country',
            'Shipping State',
            'Shipping City',
            'Shipping Phone',
            'Shipping Zip',
            'Shipping Address',
            'Balance',
        ];
    }
}
