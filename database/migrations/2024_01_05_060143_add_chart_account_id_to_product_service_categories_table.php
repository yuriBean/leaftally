<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('product_service_categories', 'chart_account_id')) {
            Schema::table('product_service_categories', function (Blueprint $table) {
                $table->integer('chart_account_id')->default(0)->after('type');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_service_categories', function (Blueprint $table) {
            //
        });
    }
};
