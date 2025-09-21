<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tables = [
            'taxes',
            'tax_rates',
            'product_service_units',
            'product_service_categories',
            'contract_types',
            'custom_fields',
            'banks',
            'branches',
            'deduction_types',
            'designations',
            'bonus_types',
            'allowance_types',
        ];

        foreach ($tables as $name) {
            if (Schema::hasTable($name) && !Schema::hasColumn($name, 'deleted_at')) {
                Schema::table($name, function (Blueprint $table) {
                    // place after updated_at if present
                    $table->softDeletes()->after('updated_at');
                });
            }
        }
    }

    public function down(): void
    {
        $tables = [
            'taxes',
            'tax_rates',
            'product_service_units',
            'product_service_categories',
            'contract_types',
            'custom_fields',
            'banks',
            'branches',
            'deduction_types',
            'designations',
            'bonus_types',
            'allowance_types',
        ];

        foreach ($tables as $name) {
            if (Schema::hasTable($name) && Schema::hasColumn($name, 'deleted_at')) {
                Schema::table($name, function (Blueprint $table) {
                    // drop the soft deletes column
                    $table->dropSoftDeletes();
                });
            }
        }
    }
};
