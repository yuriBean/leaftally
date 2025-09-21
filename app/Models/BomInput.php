<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BomInput extends Model
{
    protected $fillable = ['bom_id','product_id','qty_per_batch','scrap_pct'];

    public function bom() {
        return $this->belongsTo(Bom::class);
    }

    public function product() {
        return $this->hasOne(ProductService::class,'id','product_id')->withTrashed();
    }
}
