<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Plan extends Model
{
    protected $fillable = [
        'name',
        'price',
        'duration',
        'description',
        'image',
        'storage_limit',

        'max_users',
        'max_customers',
        'max_venders',
        'max_employees',

        'features',
        'enable_chatgpt',
        'trial',
        'trial_days',
        'is_disable',

        'user_access_management',
        'payroll_enabled',
        'budgeting_enabled',
        'tax_management_enabled',
        'audit_trail_enabled',
        'manufacturing_enabled',

        'inventory_enabled',

        'invoice_enabled',
        'invoice_quota',
        'product_management_enabled',
        'product_quota',
        'expense_tracking_enabled',
        'expense_quota',
        'client_management_enabled',
        'client_quota',
        'vendor_management_enabled',
        'vendor_quota',

        'payroll_quota',
        'manufacturing_quota',
    ];

    public static $arrDuration = [
        'lifetime' => 'Lifetime',
        'month'    => 'Per Month',
        'year'     => 'Per Year',
    ];

    public function status()
    {
        return [
            __('Lifetime'),
            __('Per Month'),
            __('Per Year'),
        ];
    }

    public static function total_plan()
    {
        return static::count();
    }

    public static function most_purchese_plan()
    {
        return User::select(\DB::raw('count(*) as total'))
            ->where('type', 'company')
            ->whereNotNull('plan')
            ->groupBy('plan')
            ->first();
    }

    protected $casts = [
        'features' => 'array',

        'user_access_management'    => 'boolean',
        'payroll_enabled'           => 'boolean',
        'budgeting_enabled'         => 'boolean',
        'tax_management_enabled'    => 'boolean',
        'audit_trail_enabled'       => 'boolean',
        'manufacturing_enabled'     => 'boolean',

        'invoice_enabled'           => 'boolean',
        'product_management_enabled'=> 'boolean',
        'expense_tracking_enabled'  => 'boolean',
        'client_management_enabled' => 'boolean',
        'vendor_management_enabled' => 'boolean',
        'inventory_enabled'         => 'boolean',

        'trial'                     => 'boolean',
        'is_disable'                => 'boolean',
    ];
}
