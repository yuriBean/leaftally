<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductionOutput extends Model
{
    protected $fillable = [
        'production_order_id','product_id','qty_planned','qty_good','qty_scrap','cost_allocated'
    ];

    public function order() {
        return $this->belongsTo(ProductionOrder::class,'production_order_id');
    }

    public function product() {
        return $this->hasOne(ProductService::class,'id','product_id')->withTrashed();
    }
}
