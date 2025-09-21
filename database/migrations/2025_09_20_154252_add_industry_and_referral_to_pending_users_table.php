<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('pending_users', function (Blueprint $table) {
            $table->string('industry')->nullable()->after('name');
            $table->string('industry_other')->nullable()->after('industry');
            $table->string('referral_source')->nullable()->after('industry_other');
            $table->string('referral_other')->nullable()->after('referral_source');
        });
    }

    public function down()
    {
        Schema::table('pending_users', function (Blueprint $table) {
            $table->dropColumn(['industry', 'industry_other', 'referral_source', 'referral_other']);
        });
    }
};
