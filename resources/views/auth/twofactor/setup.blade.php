@extends('layouts.auth')

@section('page-title', __('Enable Two-Factor'))

@section('content')
  <div class="card shadow-sm mx-auto" style="max-width: 560px;">
    <div class="card-body">
      <h4 class="mb-2">{{ __('Protect your account with 2FA') }}</h4>
      <p class="text-muted mb-4">
        {{ __('Scan the QR code with Google Authenticator, Microsoft Authenticator, or any TOTP app. Then enter the 6-digit code to confirm.') }}
      </p>

      <div class="d-flex align-items-center gap-4 mb-4">
        <img src="{{ $qrUrl }}" alt="QR" class="rounded border" width="180" height="180">
        <div class="flex-1">
          <div class="small text-muted mb-1">{{ __('Can’t scan? Enter this key manually:') }}</div>
          <code class="h5 d-inline-block">{{ $secret }}</code>
          <div class="small text-muted mt-2">{{ __('Account:') }} {{ auth()->user()->email }}</div>
          <div class="small text-muted">{{ __('Issuer:') }} {{ config('app.name') }}</div>
        </div>
      </div>

      <form method="POST" action="{{ route('2fa.enable') }}" class="d-grid gap-3">
        @csrf
        <div>
          <label for="code" class="form-label">{{ __('6-digit code') }}</label>
          <input id="code" name="code" inputmode="numeric" pattern="[0-9]*" maxlength="6"
                 class="form-control @error('code') is-invalid @enderror" required autofocus>
          @error('code') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <button class="btn btn-primary w-100 my-4">{{ __('Confirm & Enable 2FA') }}</button>
        <a href="{{ \App\Providers\RouteServiceProvider::HOME }}" class="btn btn-outline-secondary w-100">{{ __('Skip for now') }}</a>
      </form>
    </div>
  </div>
@endsection
