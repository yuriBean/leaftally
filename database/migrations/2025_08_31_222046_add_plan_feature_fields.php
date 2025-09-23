<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            if (!Schema::hasColumn('plans', 'user_access_management')) {
                $table->boolean('user_access_management')->default(false);
            }

            if (!Schema::hasColumn('plans', 'payroll_enabled')) {
                $table->boolean('payroll_enabled')->default(false);
            }
            if (!Schema::hasColumn('plans', 'payroll_quota')) {
                $table->integer('payroll_quota')->default(-1);
            }

            if (!Schema::hasColumn('plans', 'budgeting_enabled')) {
                $table->boolean('budgeting_enabled')->default(false);
            }

            if (!Schema::hasColumn('plans', 'tax_management_enabled')) {
                $table->boolean('tax_management_enabled')->default(false);
            }

            if (!Schema::hasColumn('plans', 'audit_trail_enabled')) {
                $table->boolean('audit_trail_enabled')->default(false);
            }

            if (!Schema::hasColumn('plans', 'manufacturing_enabled')) {
                $table->boolean('manufacturing_enabled')->default(false);
            }
            if (!Schema::hasColumn('plans', 'manufacturing_quota')) {
                $table->integer('manufacturing_quota')->default(-1);
            }

            if (!Schema::hasColumn('plans', 'invoice_enabled')) {
                $table->boolean('invoice_enabled')->default(true);
            }
            if (!Schema::hasColumn('plans', 'invoice_quota')) {
                $table->integer('invoice_quota')->default(-1);
            }

            if (!Schema::hasColumn('plans', 'product_management_enabled')) {
                $table->boolean('product_management_enabled')->default(true);
            }
            if (!Schema::hasColumn('plans', 'product_quota')) {
                $table->integer('product_quota')->default(-1);
            }

            if (!Schema::hasColumn('plans', 'expense_tracking_enabled')) {
                $table->boolean('expense_tracking_enabled')->default(true);
            }
            if (!Schema::hasColumn('plans', 'expense_quota')) {
                $table->integer('expense_quota')->default(-1);
            }

            if (!Schema::hasColumn('plans', 'client_management_enabled')) {
                $table->boolean('client_management_enabled')->default(true);
            }
            if (!Schema::hasColumn('plans', 'client_quota')) {
                $table->integer('client_quota')->default(-1);
            }

            if (!Schema::hasColumn('plans', 'vendor_management_enabled')) {
                $table->boolean('vendor_management_enabled')->default(true);
            }
            if (!Schema::hasColumn('plans', 'vendor_quota')) {
                $table->integer('vendor_quota')->default(-1);
            }

            if (!Schema::hasColumn('plans', 'inventory_enabled')) {
                $table->boolean('inventory_enabled')->default(true);
            }
        });
    }

    public function down(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $cols = [
                'user_access_management',
                'payroll_enabled', 'payroll_quota',
                'budgeting_enabled',
                'tax_management_enabled',
                'audit_trail_enabled',
                'manufacturing_enabled', 'manufacturing_quota',
                'invoice_enabled', 'invoice_quota',
                'product_management_enabled', 'product_quota',
                'expense_tracking_enabled', 'expense_quota',
                'client_management_enabled', 'client_quota',
                'vendor_management_enabled', 'vendor_quota',
                'inventory_enabled',
            ];
            foreach ($cols as $c) {
                if (Schema::hasColumn('plans', $c)) {
                    $table->dropColumn($c);
                }
            }
        });
    }
};
