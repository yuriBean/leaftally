<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('customers', 'is_enable_login')) {

            Schema::table('customers', function (Blueprint $table) {
                $table->integer('is_enable_login')->default(1)->after('is_active');                                
            });
        }
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
        });
    }
};
