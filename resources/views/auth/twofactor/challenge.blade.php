@extends('layouts.auth')

@section('page-title', __('Two-Factor Authentication'))

@section('content')
      <h4 class="mb-2">{{ __('Enter your 6-digit code') }}</h4>
      <p class="text-muted mb-4">
        {{ __('Open your authenticator app and enter the current code. Or use a recovery code if you can’t access the app.') }}
      </p>

      <form method="POST" action="{{ route('2fa.challenge.confirm') }}" class="d-grid gap-3">
        @csrf
        <div>
          <label for="code" class="form-label">{{ __('Authenticator code') }}</label>
          <input id="code" name="code" inputmode="numeric" pattern="[0-9]*" maxlength="6"
                 class="form-control @error('code') is-invalid @enderror" autofocus>
          @error('code') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="text-center my-2">
          <span class="text-muted">{{ __('— or —') }}</span>
        </div>

        <div>
          <label for="recovery_code" class="form-label">{{ __('Recovery code') }}</label>
          <input id="recovery_code" name="recovery_code" class="form-control"
                 placeholder="ABCD1234EF">
        </div>

        <div class="form-check mt-2">
          <input class="form-check-input" type="checkbox" value="1" id="remember_device" name="remember_device">
          <label class="form-check-label" for="remember_device">
            {{ __('Remember this device for 7 days') }}
          </label>
        </div>

        <button class="btn btn-primary w-100 my-3">{{ __('Verify & Continue') }}</button>
      </form>

@endsection
