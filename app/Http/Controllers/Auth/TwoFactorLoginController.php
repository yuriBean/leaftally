<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class TwoFactorLoginController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();

        // If not logged in or 2FA not enabled, go home
        if (!$user || $user->two_factor_secret === null) {
            return redirect()->intended(\App\Providers\RouteServiceProvider::HOME);
        }

        // Respect "remember device" cookie
        $cookieName = "2fa_remember_{$user->id}";
        if ($request->cookie($cookieName) === '1') {
            $request->session()->put('2fa_passed', true);
            return redirect()->intended(\App\Providers\RouteServiceProvider::HOME);
        }

        // If already passed in this session, go home
        if ($request->session()->get('2fa_passed') === true) {
            return redirect()->intended(\App\Providers\RouteServiceProvider::HOME);
        }

        return view('auth.twofactor.challenge');
    }

    public function confirm(Request $request)
    {
        $request->validate([
            'code'            => ['nullable','digits:6'],
            'recovery_code'   => ['nullable','string'],
            'remember_device' => ['nullable','boolean'],
        ]);

        $user = $request->user();
        if (!$user || $user->two_factor_secret === null) {
            return redirect()->intended(\App\Providers\RouteServiceProvider::HOME);
        }

        // Try TOTP
        $ok = false;
        if ($request->filled('code')) {
            $ok = $this->verifyTotp(
                Crypt::decryptString($user->two_factor_secret),
                $request->input('code')
            );
        }

        // Fallback to recovery codes
        if (!$ok && $request->filled('recovery_code') && $user->two_factor_recovery_codes) {
            try {
                $codes = json_decode(Crypt::decryptString($user->two_factor_recovery_codes), true) ?: [];
            } catch (\Throwable $e) {
                $codes = [];
            }
            $needle = strtoupper(trim($request->input('recovery_code')));
            if (in_array($needle, $codes, true)) {
                // one-time use: remove it
                $codes = array_values(array_filter($codes, fn ($c) => $c !== $needle));
                $user->two_factor_recovery_codes = Crypt::encryptString(json_encode($codes));
                $user->save();
                $ok = true;
            }
        }

        if (!$ok) {
            return back()->withErrors([
                'code' => __('Invalid code. Check the 6-digit code or use a valid recovery code.'),
            ])->withInput();
        }

        // Passed
        $request->session()->put('2fa_passed', true);

        // Remember device 7 days
        if ($request->boolean('remember_device')) {
            $cookieName = "2fa_remember_{$user->id}";
            $minutes    = 60 * 24 * 7; // 7 days
            $isSecure   = (bool) config('session.secure', false);

            cookie()->queue(cookie(
                $cookieName,
                '1',
                $minutes,
                '/',                        // path
                config('session.domain'),   // domain or null
                $isSecure,                  // secure
                true,                       // httpOnly
                false,                      // raw
                'Lax'                       // sameSite
            ));
        }

        return redirect()->intended(\App\Providers\RouteServiceProvider::HOME);
    }

    // ---- TOTP helpers (fully implemented) ----

    private function base32Decode(string $b32): string
    {
        $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $b32 = strtoupper(str_replace('=', '', $b32));
        $bits = '';
        for ($i = 0, $len = strlen($b32); $i < $len; $i++) {
            $val = strpos($alphabet, $b32[$i]);
            if ($val === false) {
                continue;
            }
            $bits .= str_pad(decbin($val), 5, '0', STR_PAD_LEFT);
        }
        $bin = '';
        foreach (str_split($bits, 8) as $chunk) {
            if (strlen($chunk) === 8) {
                $bin .= chr(bindec($chunk));
            }
        }
        return $bin;
    }

    private function hotp(string $secret, int $counter, int $digits = 6): string
    {
        $key        = $this->base32Decode($secret);
        $binCounter = pack('N2', 0, $counter);
        $hash       = hash_hmac('sha1', $binCounter, $key, true);

        $offset    = ord(substr($hash, -1)) & 0x0F;
        $segment   = substr($hash, $offset, 4);
        $truncated = unpack('N', $segment)[1] & 0x7FFFFFFF;

        $code = $truncated % (10 ** $digits);
        return str_pad((string) $code, $digits, '0', STR_PAD_LEFT);
    }

    private function totp(string $secret, ?int $time = null, int $period = 30, int $digits = 6): string
    {
        $time    = $time ?? time();
        $counter = (int) floor($time / $period);
        return $this->hotp($secret, $counter, $digits);
    }

    private function verifyTotp(string $secret, string $code, int $window = 1): bool
    {
        $now = time();
        $code = trim($code);

        // basic format check to avoid non-numeric issues
        if ($code === '' || !ctype_digit($code) || strlen($code) !== 6) {
            return false;
        }

        for ($i = -$window; $i <= $window; $i++) {
            $candidate = $this->totp($secret, $now + ($i * 30));
            if (hash_equals($candidate, $code)) {
                return true;
            }
        }
        return false; // <-- ensure a boolean is always returned
    }
}
