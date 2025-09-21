<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pending_users', function (Blueprint $table) {
            $table->id();
            // Basic registration data
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password'); // hashed
            $table->string('lang')->nullable();
            $table->string('referral_code')->nullable();      // generated for new user record
            $table->string('used_referral_code')->nullable(); // incoming code from form, if any

            // OTP
            $table->string('otp_hash');              // hashed 6-digit
            $table->timestamp('otp_expires_at');
            $table->unsignedTinyInteger('otp_attempts')->default(0);
            $table->timestamp('otp_verified_at')->nullable();

            // Flow state
            $table->enum('status', ['otp_sent','verified','checkout_started','paid','abandoned'])
                  ->default('otp_sent');

            // Plan + checkout
            $table->unsignedBigInteger('selected_plan_id')->nullable()->index();
            $table->string('stripe_session_id')->nullable()->index();

            // Meta
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
