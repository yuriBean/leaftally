<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Template extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'template';
    protected $fillable = [
        'template_name',
        'prompt',
        'field_json',
        'is_tone'
    ];
}
