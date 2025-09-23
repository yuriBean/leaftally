<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Utility;
use App\Models\User;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('users', 'referral_code')) {

            Schema::table('users', function (Blueprint $table) {
                $table->integer('referral_code')->default(0)->after('requested_plan');
                $table->integer('used_referral_code')->default(0)->after('referral_code');
            });

            Schema::table('users', function (Blueprint $table) {
                $users = User::where('type','company')->get();

                foreach ($users as $user) {
                    if (empty($user->referral_code) || ($user->referral_code) === null || ($user->referral_code) === '' || ($user->referral_code === 0)) {
                        $user->referral_code = Utility::generateReferralCode();
                        $user->save();
                    }
                }
            });
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
        });
    }
};
