<?php

namespace App\Exports;

use App\Models\ProductService;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ProductServiceExport implements FromCollection, WithHeadings
{
    /** @var array<int,int|string>|null */
    protected ?array $ids;

    public function __construct(?array $ids = null)
    {
        $this->ids = $ids ? array_values($ids) : null;
    }

    public function collection()
    {
        // Start with the same scope you had
        $q = ProductService::query();
        if (Auth::user()->type == 'company') {
            $q->where('created_by', Auth::user()->id);
        }
        if (!empty($this->ids)) {
            $q->whereIn('id', $this->ids);
        }

        $data = $q->get();

        if ($data->isNotEmpty()) {
            foreach ($data as $k => $ProductService) {
                $taxe     = ProductService::Taxe($ProductService->tax_id);
                $unit     = ProductService::productserviceunit($ProductService->unit_id);
                $category = ProductService::productcategory($ProductService->category_id);

                unset(
                    $ProductService->created_by,
                    $ProductService->sku,
                    $ProductService->sale_chartaccount_id,
                    $ProductService->expense_chartaccount_id,
                    $ProductService->created_at,
                    $ProductService->updated_at,
                );

                // Overwrite display fields to friendly text
                $data[$k]['tax_id']      = $taxe;
                $data[$k]['unit_id']     = $unit;
                $data[$k]['category_id'] = $category;
            }
        }

        return $data;
    }

    public function headings(): array
    {
        return [
            "ID",
            "Name",
            "Sale Price",
            "Purchase Price",
            "Quantity",
            "Tax",
            "Category",
            "Unit",
            "Type",
            "Description",
        ];
    }
}
