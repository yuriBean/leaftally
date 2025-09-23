<?php

namespace App\Exports;

use App\Models\StockReport;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ProductStockExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        $data = [];
        $data = StockReport::where('created_by' , \Auth::user()->id)->get();
        if (!empty($data)) {
            foreach ($data as $k => $Stock) {
                $product = StockReport::products($Stock->product_id);

                unset($Stock->created_by,$Stock->updated_at,$Stock->type_id);
                $data[$k]["product_id"]        = $product;
            }
        }    
        return $data;
    }

    public function headings(): array
    {
        return [
            "Stock Id",
            "Product Name",
            "Quantity",
            "Type",
            "Description",
            "Date",
        ];
    }
}
