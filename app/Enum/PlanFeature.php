<?php
namespace App\Enum;

final class PlanFeature
{
    public const USER_ACCESS = 'user_access_management';
    public const PAYROLL     = 'payroll_enabled';
    public const BUDGETING   = 'budgeting_enabled';
    public const TAX         = 'tax_management_enabled';
    public const AUDIT       = 'audit_trail_enabled';
    public const MANUFACTURING = 'manufacturing_enabled';

    // Quotas (map to *_quota columns on plans)
    public const QUOTAS = [
        self::INVOICE       => 'invoice_quota',
        self::PAYROLL       => 'payroll_quota',
        self::PRODUCT_MGT   => 'product_quota',
        self::EXPENSE       => 'expense_quota',
        self::CLIENT_MGT    => 'client_quota',
        self::VENDOR_MGT    => 'vendor_quota',
        self::MANUFACTURING => 'manufacturing_quota',
    ];
}
