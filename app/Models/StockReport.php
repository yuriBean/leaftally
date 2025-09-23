<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'quantity',
        'type',
        'type_id',
        'description',
    ];

    public function product()
    {
        return $this->hasOne('App\Models\ProductService', 'id', 'product_id')->withTrashed();
    }

    public static function products($product)
    {
        $ids  = explode(',', $product);
        $name = '';
        foreach ($ids as $pid) {
            $p = ProductService::withTrashed()->find($pid);
            $name = $p ? $p->name : '';
        }
        return $name;
    }
}
