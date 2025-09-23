<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'symbol'
    ];

    public static function getCurrenciesForDropdown()
    {
        return self::orderBy('name')->get()->pluck('name', 'code')->toArray();
    }

    public static function getSymbolByCode($code)
    {
        $currency = self::where('code', $code)->first();
        return $currency ? $currency->symbol : '$';
    }

    public static function getByCode($code)
    {
        return self::where('code', $code)->first();
    }
}
