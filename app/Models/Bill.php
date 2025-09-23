<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bill extends Model
{
    protected $fillable = [
        'vender_id',
        'currency',
        'bill_date',
        'due_date',
        'bill_id',
        'order_number',
        'category_id',
        'created_by',
    ];

    public static $statues = [
        'Draft',
        'Sent',
        'Unpaid',
        'Partialy Paid',
        'Paid',
    ];

    public function vender()
    {
        return $this->belongsTo('App\Models\Vender', 'vender_id', 'id')->withTrashed();
    }

    public function tax()
    {
        return $this->hasOne('App\Models\Tax', 'id', 'tax_id')->withTrashed();
    }

    public function items()
    {
        return $this->hasMany('App\Models\BillProduct', 'bill_id', 'id');
    }

    public function payments()
    {
        return $this->hasMany('App\Models\BillPayment', 'bill_id', 'id');
    }

    public function accounts()
    {
        return $this->hasMany('App\Models\BillAccount', 'ref_id', 'id');
    }

    public function getSubTotal()
    {
        $subTotal = 0;

        foreach ($this->items as $product) {
            $subTotal += ($product->price * $product->quantity);
        }

        $accountTotal = 0;
        foreach ($this->accounts as $account) {
            $accountTotal += $account->price;
        }

        return $subTotal + $accountTotal;
    }

    public function getTotalTax()
    {
        $totalTax = 0;
        foreach ($this->items as $product) {
            $taxes = Utility::totalTaxRate($product->tax);
            $totalTax += ($taxes / 100) * ($product->price * $product->quantity - $product->discount);
        }

        return $totalTax;
    }

    public function getTotalDiscount()
    {
        $totalDiscount = 0;
        foreach ($this->items as $product) {
            $totalDiscount += $product->discount;
        }

        return $totalDiscount;
    }

    public function getTotal()
    {
        return ($this->getSubTotal() - $this->getTotalDiscount()) + $this->getTotalTax();
    }

    public function getDue()
    {
        $due = 0;
        foreach ($this->payments as $payment) {
            $due += $payment->amount;
        }

        return ($this->getTotal() - $due) - ($this->billTotalDebitNote());
    }

    public function category()
    {
        return $this->hasOne('App\Models\ProductServiceCategory', 'id', 'category_id')->withTrashed();
    }

    public function debitNote()
    {
        return $this->hasMany('App\Models\DebitNote', 'bill', 'id');
    }

    public function billTotalDebitNote()
    {
        return $this->hasMany('App\Models\DebitNote', 'bill', 'id')->sum('amount');
    }

    public function lastPayments()
    {
        return $this->hasOne('App\Models\BillPayment', 'id', 'bill_id');
    }

    public function taxes()
    {
        return $this->hasOne('App\Models\Tax', 'id', 'tax')->withTrashed();
    }

    public static function vendor($venders)
    {
        $categoryArr = explode(',', $venders);
        $unitRate = 0;
        foreach ($categoryArr as $venderId) {
            if ($venderId == 0) {
                $unitRate = '';
            } else {
                $v = Vender::withTrashed()->find($venderId);
                $unitRate = ($v) ? $v->name : ' ';
            }
        }

        return $unitRate;
    }

    public static function ProposalCategory($category)
    {
        $categoryArr = explode(',', $category);
        $categoryRate = 0;
        foreach ($categoryArr as $catId) {
            $c = ProductServiceCategory::withTrashed()->find($catId);
            $categoryRate = $c->name ?? '-';
        }

        return $categoryRate;
    }

    public function getAccountTotal()
    {
        $accountTotal = 0;
        foreach ($this->accounts as $account) {
            $accountTotal += $account->price;
        }

        return $accountTotal;
    }
}
