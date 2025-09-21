<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BomOutput extends Model
{
    protected $fillable = ['bom_id','product_id','qty_per_batch','is_primary'];

    public function bom() {
        return $this->belongsTo(Bom::class);
    }

    public function product() {
        return $this->hasOne(ProductService::class,'id','product_id')->withTrashed();
    }
}
