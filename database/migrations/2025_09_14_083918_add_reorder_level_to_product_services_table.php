<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('product_services', function (Blueprint $table) {
            // integer, nullable, for products only (services can stay null)
            $table->unsignedInteger('reorder_level')->nullable()->after('quantity');
        });
    }

    public function down(): void
    {
        Schema::table('product_services', function (Blueprint $table) {
            $table->dropColumn('reorder_level');
        });
    }
};
