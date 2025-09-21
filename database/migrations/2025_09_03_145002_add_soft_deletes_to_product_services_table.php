<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('product_services', function (Blueprint $table) {
            if (!Schema::hasColumn('product_services', 'deleted_at')) {
                $table->softDeletes()->after('created_by');
            }
        });
    }

    public function down(): void
    {
        Schema::table('product_services', function (Blueprint $table) {
            if (Schema::hasColumn('product_services', 'deleted_at')) {
                $table->dropSoftDeletes();
            }
        });
    }
};
