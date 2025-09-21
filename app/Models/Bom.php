<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bom extends Model
{
    protected $fillable = [
        'code','name','yield_pct','is_active','notes','created_by'
    ];

    public function inputs() {
        return $this->hasMany(BomInput::class);
    }

    public function outputs() {
        return $this->hasMany(BomOutput::class);
    }

    public function scopeMine($q) {
        return $q->where('created_by', \Auth::user()->creatorId());
    }
}
