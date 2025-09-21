<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class ProductService extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'sku',
        'sale_price',
        'purchase_price',
        'tax_id',
        'category_id',
        'unit_id',
        'type',
        'sale_chartaccount_id',
        'expense_chartaccount_id',
        'created_by',
        'material_type', 
        'reorder_level',
    ];

    protected $casts = [
        'deleted_at' => 'datetime',
        'material_type' => 'string',
        'reorder_level' => 'integer'
    ];

    protected $appends = ['is_archived'];

    public function getIsArchivedAttribute(): bool
    {
        return !is_null($this->deleted_at);
    }

    public function getMaterialTypeLabelAttribute(): string
    {
        return match ($this->material_type) {
            'raw'      => __('Raw material'),
            'finished' => __('Finished product'),
            'both'     => __('Both'),
            default    => '-',
        };
    }
    public function scopeMine($q)
    {
        return $q->where('created_by', Auth::user()->creatorId());
    }

    public function scopeActive($q)
    {
        return $q->whereNull('deleted_at');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function taxes()
    {
        return $this->hasOne('App\Models\Tax', 'id', 'tax_id')->withTrashed();
    }

    public function unit()
    {
        return $this->hasOne('App\Models\ProductServiceUnit', 'id', 'unit_id')->withTrashed();
    }

    public function category()
    {
        return $this->hasOne('App\Models\ProductServiceCategory', 'id', 'category_id')->withTrashed();
    }

    public function tax($taxes)
    {
        $taxArr = explode(',', $taxes);
        $taxes  = [];
        foreach ($taxArr as $tax) {
            $taxes[] = Tax::withTrashed()->find($tax);
        }
        return $taxes;
    }

    public function taxRate($taxes)
    {
        $taxArr  = explode(',', $taxes);
        $taxRate = 0;
        foreach ($taxArr as $tax) {
            $tax     = Tax::withTrashed()->find($tax);
            $taxRate += $tax ? $tax->rate : 0;
        }
        return $taxRate;
    }

    public static function taxData($taxes)
    {
        $taxArr = explode(',', $taxes);
        $taxes = [];
        foreach ($taxArr as $tax) {
            $taxesData = Tax::withTrashed()->find($tax);
            $taxes[]   = !empty($taxesData) ? $taxesData->name : '';
        }
        return implode(',', $taxes);
    }

    public static function Taxe($taxe)
    {
        $categoryArr  = explode(',', $taxe);
        $taxeRate = 0;
        foreach ($categoryArr as $taxe) {
            $taxe    = Tax::withTrashed()->find($taxe);
            $taxeRate = isset($taxe) ? $taxe->name : '';
        }
        return $taxeRate;
    }

    public static function productserviceunit($unit)
    {
        $categoryArr  = explode(',', $unit);
        $unitRate = 0;
        foreach ($categoryArr as $unit) {
            $unit    = ProductServiceUnit::withTrashed()->find($unit);
            $unitRate = isset($unit) ? $unit->name : '';
        }
        return $unitRate;
    }

    public static function productcategory($category)
    {
        $categoryArr  = explode(',', $category);
        $categoryRate = 0;
        foreach ($categoryArr as $category) {
            $category    = ProductServiceCategory::withTrashed()->find($category);
            $categoryRate = isset($category) ? $category->name : '';
        }
        return $categoryRate;
    }

    public static function getallproducts()
    {
        return ProductService::select('product_services.*', 'c.name as categoryname')
            ->where('product_services.type', '=', 'product')
            ->leftjoin('product_service_categories as c', 'c.id', '=', 'product_services.category_id')
            ->where('product_services.created_by', '=', Auth::user()->creatorId())
            ->orderBy('product_services.id', 'DESC');
    }
    public function getIsLowStockAttribute(): bool
{
    if ($this->type !== 'Product') return false;
    if ($this->reorder_level === null) return false;
    return (int)$this->quantity < (int)$this->reorder_level;
}
}
