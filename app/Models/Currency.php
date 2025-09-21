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

    /**
     * Get all currencies for dropdown
     */
    public static function getCurrenciesForDropdown()
    {
        return self::orderBy('name')->get()->pluck('name', 'code')->toArray();
    }

    /**
     * Get currency symbol by code
     */
    public static function getSymbolByCode($code)
    {
        $currency = self::where('code', $code)->first();
        return $currency ? $currency->symbol : '$';
    }

    /**
     * Get currency by code
     */
    public static function getByCode($code)
    {
        return self::where('code', $code)->first();
    }
}
