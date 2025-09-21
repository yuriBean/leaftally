@extends('layouts.auth')

@section('page-title', __('Verify Email'))

@section('content')
  <div class="d-flex text-center align-items-center justify-content-between">
    <h2 class="mb-3 w-100 f-w-600">{{ __('Verify your email') }}</h2>
  </div>

  @if (session('status'))
    <div class="alert alert-info">{{ session('status') }}</div>
  @endif

  <p class="text-muted mb-4">
    {{ __('Enter the 6-digit code we sent to') }}
    <strong>{{ \Illuminate\Support\Str::mask($pending->email ?? '', '*', 2, 6) }}</strong>
  </p>

  <form method="POST" action="{{ route('register.verify') }}" class="needs-validation" novalidate>
    @csrf
    <div class="form-group mb-3">
      <label class="form-label">{{ __('6-digit code') }}</label>
      <input type="text" inputmode="numeric" pattern="\d{6}" maxlength="6"
             name="code" class="form-control @error('code') is-invalid @enderror"
             placeholder="••••••" required autofocus>
      @error('code')
        <div class="invalid-feedback d-block">{{ $message }}</div>
      @enderror
    </div>

    <div class="d-grid">
      <button class="btn btn-primary">{{ __('Verify') }}</button>
    </div>
  </form>

  <form method="POST" action="{{ route('register.otp.resend') }}" class="mt-3">
    @csrf
    <button class="btn btn-link p-0">{{ __('Resend code') }}</button>
  </form>
@endsection
