<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Plan extends Model
{
    protected $fillable = [
        // Basics
        'name',
        'price',
        'duration',
        'description',
        'image',
        'storage_limit',

        // Limits
        'max_users',
        'max_customers',
        'max_venders',
        'max_employees',

        // Legacy / extra
        'features',
        'enable_chatgpt',
        'trial',
        'trial_days',
        'is_disable',

        // Feature toggles (editable)
        'user_access_management',
        'payroll_enabled',
        'budgeting_enabled',
        'tax_management_enabled',
        'audit_trail_enabled',
        'manufacturing_enabled',     // NEW

        // Derived / enforced flags
        'inventory_enabled',

        // Always-on modules + quotas
        'invoice_enabled',           // enforced true
        'invoice_quota',
        'product_management_enabled',// enforced true
        'product_quota',
        'expense_tracking_enabled',  // Bills mgmt (enforced true)
        'expense_quota',
        'client_management_enabled', // enforced true
        'client_quota',
        'vendor_management_enabled', // enforced true
        'vendor_quota',

        // Quotas for editable modules
        'payroll_quota',
        'manufacturing_quota',       // NEW
    ];

    public static $arrDuration = [
        'lifetime' => 'Lifetime',
        'month'    => 'Per Month',
        'year'     => 'Per Year',
    ];

    public function status()
    {
        // legacy helper kept for compatibility with existing views
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
        // returns the first row of grouped counts by plan for company users
        return User::select(\DB::raw('count(*) as total'))
            ->where('type', 'company')
            ->whereNotNull('plan')
            ->groupBy('plan')
            ->first();
    }

    protected $casts = [
        // JSON
        'features' => 'array',

        // Booleans (editable)
        'user_access_management'    => 'boolean',
        'payroll_enabled'           => 'boolean',
        'budgeting_enabled'         => 'boolean',
        'tax_management_enabled'    => 'boolean',
        'audit_trail_enabled'       => 'boolean',
        'manufacturing_enabled'     => 'boolean',

        // Booleans (always-on / derived)
        'invoice_enabled'           => 'boolean',
        'product_management_enabled'=> 'boolean',
        'expense_tracking_enabled'  => 'boolean', // Bills
        'client_management_enabled' => 'boolean',
        'vendor_management_enabled' => 'boolean',
        'inventory_enabled'         => 'boolean',

        // Misc flags
        'trial'                     => 'boolean',
        'is_disable'                => 'boolean',
    ];
}
