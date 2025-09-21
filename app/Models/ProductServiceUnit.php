<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductServiceUnit extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'created_by',
    ];
}
