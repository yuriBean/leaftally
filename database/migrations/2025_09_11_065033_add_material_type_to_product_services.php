<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_services', function (Blueprint $table) {
            $table->string('material_type', 16)->nullable()->after('type')->index();
        });

        DB::table('product_services')
            ->where('type', 'Product')
            ->whereNull('material_type')
            ->update(['material_type' => 'finished']);
    }

    public function down(): void
    {
        Schema::table('product_services', function (Blueprint $table) {
            $table->dropColumn('material_type');
        });
    }
};
