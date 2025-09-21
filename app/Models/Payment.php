<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'date',
        'amount',
        'account_id',
        'vender_id',
        'description',
        'category_id',
        'payment_method',
        'reference',
        'created_by',
    ];

    public function category()
    {
        // Include archived categories
        return $this->hasOne('App\Models\ProductServiceCategory', 'id', 'category_id')->withTrashed();
    }

    public function vender()
    {
        // show even if vendor was soft-deleted
        return $this->hasOne('App\Models\Vender', 'id', 'vender_id')->withTrashed();
    }

    public function bankAccount()
    {
        return $this->hasOne('App\Models\BankAccount', 'id', 'account_id');
    }

    public static function accounts($account)
    {
        $categoryArr  = explode(',', $account);
        $unitRate = 0;
        foreach ($categoryArr as $accId) {
            if ($accId == 0) {
                $unitRate = '';
            } else {
                $acc  = BankAccount::find($accId);
                $unitRate = ($acc->bank_name.'  '.$acc->holder_name);
            }
        }
        return $unitRate;
    }

    public static function vendors($vendor)
    {
        $categoryArr  = explode(',', $vendor);
        $unitRate = 0;
        foreach ($categoryArr as $vid) {
            if ($vid == 0) {
                $unitRate = '';
            } else {
                $v   = Vender::withTrashed()->find($vid);
                $unitRate = $v ? $v->name : '';
            }
        }
        return $unitRate;
    }

    public static function categories($category)
    {
        $categoryArr  = explode(',', $category);
        $unitRate = 0;
        foreach ($categoryArr as $catId) {
            if ($catId == 0) {
                $unitRate = '';
            } else {
                $cat = ProductServiceCategory::withTrashed()->find($catId);
                $unitRate = $cat ? $cat->name : '-';
            }
        }
        return $unitRate;
    }
}
