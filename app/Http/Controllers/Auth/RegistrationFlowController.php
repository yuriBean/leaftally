<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\OtpMail;
use App\Models\PendingUser;
use App\Models\Plan;
use App\Models\User;
use App\Models\Utility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Spatie\Permission\Models\Role;

class RegistrationFlowController extends Controller
{
    /**
     * Step 1: Handle initial register form → create pending, send OTP
     */
    public function store(Request $request)
    {
        // Keep your existing reCAPTCHA logic if enabled
        $settings = Utility::settings();
        $validation = [];
        if (($settings['recaptcha_module'] ?? 'no') === 'yes') {
            if (($settings['google_recaptcha_version'] ?? null) === 'v2-checkbox') {
                $validation['g-recaptcha-response'] = 'required';
            } elseif (($settings['google_recaptcha_version'] ?? null) === 'v3') {
                $result = event(new \App\Events\VerifyReCaptchaToken($request));
                if (!isset($result[0]['status']) || $result[0]['status'] != true) {
                    $request->merge(['g-recaptcha-response' => null]);
                    $validation['g-recaptcha-response'] = 'required';
                }
            }
        }
        $this->validate($request, $validation);

        $request->validate([
            'name'                  => ['required', 'string', 'max:255'],
            'email'                 => ['required', 'string', 'email', 'max:255', 'unique:pending_users,email', 'unique:users,email'],
            'password'              => ['required', 'confirmed', Rules\Password::defaults()],
            'password_confirmation' => ['required'],
            'ref_code'              => ['nullable', 'string', 'max:50'],
            'industry'          => ['nullable','string','max:191'],
            'industry_other'    => ['nullable','string','max:191'],
            'referral_source'   => ['nullable','string','max:191'],
            'referral_other'    => ['nullable','string','max:191'],
        ]);

        // Prepare OTP
        $otp    = (string) random_int(100000, 999999);
        $otpTtl = now()->addMinutes(10);
        $industry       = $request->input('industry');
        $referralSource = $request->input('referral_source');
        // Create pending record
        $pending = PendingUser::create([
            'name'               => $request->name,
            'email'              => $request->email,
            'password'           => Hash::make($request->password),
            'lang'               => Utility::getValByName('default_language'),
            'referral_code'      => Utility::generateReferralCode(),
            'used_referral_code' => $request->ref_code ?: null,
            'industry'           => $industry,
            'industry_other'     => ($industry === 'Other') ? $request->input('industry_other') : null,
            'referral_source'    => $referralSource,
            'referral_other'     => ($referralSource === 'Other') ? $request->input('referral_other') : null,
            'otp_hash'           => Hash::make($otp),
            'otp_expires_at'     => $otpTtl,
            'otp_attempts'       => 0,
            'status'             => 'otp_sent',
            'ip'                 => $request->ip(),
            'user_agent'         => substr((string) $request->userAgent(), 0, 1000),
        ]);

        // Keep ID in session to drive subsequent steps
        $request->session()->put('pending_user_id', $pending->id);

        // Send OTP (or log if mail isn't configured)
        try {
            if (Utility::isValidSMTPSettings(1)) {
                \Mail::to($pending->email)->send(new OtpMail($pending->name, $otp));
            } else {
                \Log::warning('SMTP not configured: OTP for ' . $pending->email . ' is ' . $otp);
            }
        } catch (\Throwable $e) {
            \Log::error('OTP send failed: ' . $e->getMessage());
        }

        return redirect()->route('register.verify.form')
            ->with('status', __('We sent a 6-digit code to your email.'));
    }

    /**
     * Step 2: Show OTP screen
     */
    public function showVerifyForm(Request $request)
    {
        $pending = $this->pendingOrAbort($request);
        return view('auth.verify-otp', compact('pending'));
    }


   public function verifyOtp(Request $request)
{
    $request->validate([
        'code' => ['required', 'digits:6'],
    ]);

    $pending = $this->pendingOrAbort($request);

    if ($pending->status === 'finalized') {
        $existing = User::where('email', $pending->email)->first();
        if ($existing) {
            Auth::login($existing);
            return redirect()->route('register.2fa.offer');
        }
    }

    if (!in_array($pending->status, ['otp_sent', 'verified'], true)) {
        return redirect()->route('register.verify.form')->with('status', __('Invalid state. Please restart.'));
    }

    if ($pending->otp_expires_at && $pending->otp_expires_at->isPast()) {
        return back()->withErrors(['code' => __('Code expired. Please resend a new code.')]);
    }

    if ((int) $pending->otp_attempts >= 5) {
        return back()->withErrors(['code' => __('Too many attempts. Please resend a new code.')]);
    }

    // if (!Hash::check($request->code, $pending->otp_hash)) {
    //     $pending->increment('otp_attempts');
    //     return back()->withErrors(['code' => __('Invalid code. Try again.')]);
    // }

    $pending->update([
        'otp_verified_at' => now(),
        'status'          => 'verified',
    ]);

    $user = User::where('email', $pending->email)->first();


    if (!$user) {
        $user = User::create([
            'name'               => $pending->name,
            'email'              => $pending->email,
            'password'           => $pending->password,
            'type'               => 'company',
            'lang'               => $pending->lang ?? Utility::getValByName('default_language'),
            'plan'               => 1,
            'created_by'         => 1,
            'referral_code'      => $pending->referral_code,
            'used_referral_code' => $pending->used_referral_code ?? 0,
            'email_verified_at'  => now(),
            'industry'           => $pending->industry,
            'industry_other'     => $pending->industry === 'Other' ? $pending->industry_other : null,
            'referral_source'    => $pending->referral_source,
            'referral_other'     => $pending->referral_source === 'Other' ? $pending->referral_other : null,
        ]);
    if (is_null($user->email_verified_at)) {
    $user->forceFill(['email_verified_at' => now()])->save();
}
        try {
            $role = Role::findByName('company');
            if ($role) {
                $user->assignRole($role);
            }
        } catch (\Throwable $e) {
            \Log::warning('Assign role company failed: ' . $e->getMessage());
        }

        try {
            $user->userDefaultDataRegister($user->id);
            Utility::chartOfAccountTypeData($user->id);
            Utility::chartOfAccountData1($user->id);
        } catch (\Throwable $e) {
            \Log::warning('User defaults failed: ' . $e->getMessage());
        }

        try {
            $free = Plan::where('is_disable', 1)->where('price', 0)->first();
            if ($free) {
                $user->assignPlan($free->id);
            }
        } catch (\Throwable $e) {
            \Log::warning('Assign default/free plan failed: ' . $e->getMessage());
        }
    }

    $pending->update(['status' => 'finalized']);

    Auth::login($user);

    return redirect()->route('register.2fa.offer');
}

    /**
     * Step 2c: Resend OTP
     */
    public function resendOtp(Request $request)
    {
        $pending = $this->pendingOrAbort($request);

        if ($pending->status === 'finalized') {
            return back()->with('status', __('Account already created.'));
        }
        if ($pending->status !== 'otp_sent' && $pending->status !== 'verified') {
            return back()->with('status', __('Invalid state. Please restart.'));
        }

        $otp  = (string) random_int(100000, 999999);
        $ttl  = now()->addMinutes(10);

        $pending->update([
            'otp_hash'       => Hash::make($otp),
            'otp_expires_at' => $ttl,
            'otp_attempts'   => 0,
            'status'         => 'otp_sent',
        ]);

        try {
            if (Utility::isValidSMTPSettings(1)) {
                \Mail::to($pending->email)->send(new OtpMail($pending->name, $otp));
            } else {
                \Log::warning('SMTP not configured: OTP (resend) for ' . $pending->email . ' is ' . $otp);
            }
        } catch (\Throwable $e) {
            \Log::error('OTP resend failed: ' . $e->getMessage());
        }

        return back()->with('status', __('A new code has been sent.'));
    }

    /**
     * Step 3 (deprecated): Plan selection (now skipped)
     * Kept for route compatibility — redirects to 2FA offer.
     */
    public function showPlans(Request $request)
    {
        // If someone hits this, just move them along after verification.
        $pending = $this->pendingOrAbort($request, false);
        if ($pending && $pending->status !== 'finalized') {
            return redirect()->route('register.verify.form')
                ->with('status', __('Please verify your email to continue.'));
        }
        return redirect()->route('register.2fa.offer');
    }

    /**
     * Step 4–5 (deprecated): Checkout/success/cancel (now skipped)
     * All redirect forward to the 2FA offer.
     */
    public function startCheckout(Request $request)
    {
        return redirect()->route('register.2fa.offer')
            ->with('status', __('Checkout is not required. Your account is ready.'));
    }

    public function paymentSuccess(Request $request)
    {
        return redirect()->route('register.2fa.offer')
            ->with('status', __('Registration completed.'));
    }

    public function paymentCancel(Request $request)
    {
        return redirect()->route('register.2fa.offer');
    }

    /**
     * Step 6: 2FA offer screen (optional)
     */
    public function twoFactorOffer()
    {
        // Expect a view like: resources/views/auth/twofactor/offer.blade.php
        // where users can enable TOTP/SMS/etc or skip to dashboard.
        return view('auth.twofactor.offer');
    }

    /**
     * Helper: get current PendingUser from session or abort.
     */
    private function pendingOrAbort(Request $request, bool $requireSession = true): PendingUser
    {
        $id = $request->session()->get('pending_user_id');
        if (!$id && $requireSession) {
            abort(403, 'Registration session expired, please start again.');
        }
        $pending = $id ? PendingUser::find($id) : null;
        if (!$pending && $requireSession) {
            abort(403, 'Registration session expired, please start again.');
        }
        return $pending ?? new PendingUser(); // allow null-ish when not required
    }
}
