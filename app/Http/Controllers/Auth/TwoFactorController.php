<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;

class TwoFactorController extends Controller
{
    public function showSetup(Request $request)
    {
        $user = $request->user();

        if (!empty($user->two_factor_secret)) {
            return redirect()
                ->route('profile')
                ->with('status', __('Two-factor is already enabled.'));
        }

        $secret = $request->session()->get('2fa_secret');
        if (!$secret) {
            $secret = $this->generateSecret();
            $request->session()->put('2fa_secret', $secret);
        }

        $issuer  = rawurlencode(config('app.name', 'App'));
        $label   = rawurlencode($user->email);
        $otpauth = "otpauth://totp/{$issuer}:{$label}?secret={$secret}&issuer={$issuer}&digits=6&period=30";

        $qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=180x180&data=' . urlencode($otpauth);

        return view('auth.twofactor.setup', [
            'secret'  => $secret,
            'qrUrl'   => $qrUrl,
            'otpauth' => $otpauth,
        ]);
    }

    public function enable(Request $request)
    {
        $request->validate([
            'code' => ['required', 'digits:6'],
        ]);

        $user   = $request->user();
        $secret = $request->session()->get('2fa_secret');

        if (!$secret) {
            return back()->withErrors([
                'code' => __('Secret not found. Reload and try again.'),
            ]);
        }

        if (!$this->verifyTotp($secret, $request->code)) {
            return back()->withErrors([
                'code' => __('Invalid code. Make sure your phone time is correct and try again.'),
            ]);
        }

        $user->two_factor_secret          = Crypt::encryptString($secret);
        $user->two_factor_confirmed_at    = now();
        $user->two_factor_recovery_codes  = Crypt::encryptString(json_encode($this->generateRecoveryCodes()));
        $user->save();

        $request->session()->forget('2fa_secret');

        return redirect()
            ->route('profile')
            ->with('success', __('Two-factor authentication enabled.'));
    }

    public function disable(Request $request)
    {
        $user = $request->user();

        $user->two_factor_secret         = null;
        $user->two_factor_recovery_codes = null;
        $user->two_factor_confirmed_at   = null;
        $user->save();

        $request->session()->forget('2fa_secret');

        return back()->with('status', __('Two-factor disabled.'));
    }

    public function regenerateRecoveryCodes(Request $request)
    {
        $user = $request->user();

        if (empty($user->two_factor_secret)) {
            return back()->withErrors(['codes' => __('Enable two-factor first.')]);
        }

        $user->two_factor_recovery_codes = Crypt::encryptString(json_encode($this->generateRecoveryCodes()));
        $user->save();

        return back()->with('status', __('New recovery codes generated.'));
    }

    private function generateSecret(int $length = 16): string
    {
        $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $secret = '';
        for ($i = 0; $i < $length; $i++) {
            $secret .= $alphabet[random_int(0, strlen($alphabet) - 1)];
        }
        return $secret;
    }

    private function base32Decode(string $b32): string
    {
        $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $b32 = strtoupper(str_replace('=', '', $b32));
        $bits = '';
        $out  = '';

        $len = strlen($b32);
        for ($i = 0; $i < $len; $i++) {
            $val = strpos($alphabet, $b32[$i]);
            if ($val === false) {
                continue;
            }
            $bits .= str_pad(decbin($val), 5, '0', STR_PAD_LEFT);
        }

        foreach (str_split($bits, 8) as $chunk) {
            if (strlen($chunk) === 8) {
                $out .= chr(bindec($chunk));
            }
        }

        return $out;
    }

    private function hotp(string $secret, int $counter, int $digits = 6): string
    {
        $key        = $this->base32Decode($secret);
        $binCounter = pack('N2', 0, $counter);
        $hash       = hash_hmac('sha1', $binCounter, $key, true);

        $offset    = ord(substr($hash, -1)) & 0x0F;
        $truncated = unpack('N', substr($hash, $offset, 4))[1] & 0x7FFFFFFF;

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
        for ($i = -$window; $i <= $window; $i++) {
            if (hash_equals($this->totp($secret, $now + ($i * 30)), $code)) {
                return true;
            }
        }
        return false;
    }

    private function generateRecoveryCodes(int $count = 8): array
    {
        $codes = [];
        for ($i = 0; $i < $count; $i++) {
            $codes[] = strtoupper(Str::random(10));
        }
        return $codes;
    }
}
