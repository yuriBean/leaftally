<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('orders', 'is_refund')) {

            Schema::table('orders', function (Blueprint $table) {
                $table->integer('is_refund')->default(0)->after('user_id');     
            });
        }
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
        });
    }
};
