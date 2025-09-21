<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Revenue extends Model
{
    protected $fillable = [
        'date',
        'amount',
        'account_id',
        'customer_id',
        'category_id',
        'recurring',
        'payment_method',
        'reference',
        'description',
        'created_by',
    ];

    public function category()
    {
        return $this->hasOne('App\Models\ProductServiceCategory', 'id', 'category_id')->withTrashed();
    }

    public function customer()
    {
        return $this->hasOne('App\Models\Customer', 'id', 'customer_id')->withTrashed();
    }

    public function bankAccount()
    {
        return $this->hasOne('App\Models\BankAccount', 'id', 'account_id')->withTrashed();
    }

    public static function accounts($account)
    {
        $categoryArr  = explode(',', $account);
        $unitRate = 0;
        foreach ($categoryArr as $accId) {
            if ($accId == 0) {
                $unitRate = '';
            } else {
                $acc = BankAccount::find($accId);
                $unitRate = ($acc->bank_name.'  '.$acc->holder_name);
            }
        }

        return $unitRate;
    }

    public static function customers($customer)
    {
        $categoryArr  = explode(',', $customer);
        $unitRate = 0;
        foreach ($categoryArr as $cid) {
            if ($cid == 0) {
                $unitRate = '';
            } else {
                $c = Customer::withTrashed()->find($cid);
                $unitRate = $c ? $c->name : '';
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
                $c = ProductServiceCategory::withTrashed()->find($catId);
                $unitRate = $c ? $c->name : '';
            }
        }

        return $unitRate;
    }
}
