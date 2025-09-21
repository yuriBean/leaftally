<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductionConsumption extends Model
{
    protected $fillable = [
        'production_order_id','product_id','qty_required','qty_issued','unit_cost','total_cost'
    ];

    public function order() {
        return $this->belongsTo(ProductionOrder::class,'production_order_id');
    }

    public function product() {
        return $this->hasOne(ProductService::class,'id','product_id')->withTrashed();
    }
}
