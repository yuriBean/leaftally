<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pending_users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('lang')->nullable();
            $table->string('referral_code')->nullable();
            $table->string('used_referral_code')->nullable();

            $table->string('otp_hash');
            $table->timestamp('otp_expires_at');
            $table->unsignedTinyInteger('otp_attempts')->default(0);
            $table->timestamp('otp_verified_at')->nullable();

            $table->enum('status', ['otp_sent','verified','checkout_started','paid','abandoned'])
                  ->default('otp_sent');

            $table->unsignedBigInteger('selected_plan_id')->nullable()->index();
            $table->string('stripe_session_id')->nullable()->index();

            $table->string('ip')->nullable();
            $table->text('user_agent')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pending_users');
    }
};
