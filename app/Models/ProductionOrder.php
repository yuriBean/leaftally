<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductionOrder extends Model
{
    protected $fillable = [
        'bom_id','code','status','multiplier','planned_date','manufacturing_cost',
        'notes','started_at','finished_at','created_by'
    ];

    public static $statuses = [
        'Draft', 'In Process', 'Finished', 'Cancelled'
    ];

    public function bom() {
        return $this->belongsTo(Bom::class);
    }
    public function consumptions() {
        return $this->hasMany(ProductionConsumption::class);
    }
    public function outputs() {
        return $this->hasMany(ProductionOutput::class);
    }

    public function scopeMine($q) {
        return $q->where('created_by', \Auth::user()->creatorId());
    }
}
