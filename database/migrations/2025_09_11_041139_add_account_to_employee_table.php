<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('employees', 'account')) {

        Schema::table('employees', function (Blueprint $table) {
            $table->integer('account')->nullable()->after('tax_payer_id');                                
        });
    }
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
        });
    }
};
