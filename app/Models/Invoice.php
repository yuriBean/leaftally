<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = [
        'invoice_id',
        'customer_id',
        'issue_date',
        'due_date',
        'ref_number',
        'status',
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

    public function tax()
    {
        return $this->hasOne('App\Models\Tax', 'id', 'tax_id')->withTrashed();
    }

    public function items()
    {
        return $this->hasMany('App\Models\InvoiceProduct', 'invoice_id', 'id');
    }

    public function payments()
    {
        return $this->hasMany('App\Models\InvoicePayment', 'invoice_id', 'id');
    }

    public function bankpayment()
    {
        return $this->hasMany('App\Models\BankTransfer', 'invoice_id', 'id')
            ->where('type','=','invoice')
            ->where('status','!=','Approved');
    }

    public function customer()
    {
        return $this->hasOne('App\Models\Customer', 'id', 'customer_id')->withTrashed();
    }

    public function getSubTotal()
    {
        $subTotal = 0;
        foreach ($this->items as $product) {
            $subTotal += ($product->price * $product->quantity);
        }
        return $subTotal;
    }

    public function getTotalTax()
    {
        $totalTax = 0;
        foreach($this->items as $product)
        {
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
        return ($this->getTotal() - $due) - $this->invoiceTotalCreditNote();
    }

    public static function change_status($invoice_id, $status)
    {
        $invoice         = Invoice::find($invoice_id);
        $invoice->status = $status;
        $invoice->update();
    }

    public function category()
    {
        return $this->hasOne('App\Models\ProductServiceCategory', 'id', 'category_id')->withTrashed();
    }

    public function creditNote()
    {
        return $this->hasMany('App\Models\CreditNote', 'invoice', 'id');
    }

    public function invoiceTotalCreditNote()
    {
        return $this->hasMany('App\Models\CreditNote', 'invoice', 'id')->sum('amount');
    }

    public function lastPayments()
    {
        return $this->hasOne('App\Models\InvoicePayment', 'id', 'invoice_id');
    }

    public function taxes()
    {
        return $this->hasOne('App\Models\Tax', 'id', 'tax')->withTrashed();
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

    public static function Invoicecategory($category)
    {
        $categoryArr  = explode(',', $category);
        $categoryRate = 0;
        foreach ($categoryArr as $cat) {
            $c    = ProductServiceCategory::withTrashed()->find($cat);
            $categoryRate = $c ? $c->name : '';
        }
        return $categoryRate;
    }
}
