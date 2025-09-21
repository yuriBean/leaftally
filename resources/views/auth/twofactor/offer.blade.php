@extends('layouts.auth')

@section('page-title', __('Secure your account'))

@section('content')
  <div class="text-center mb-4">
    <h2 class="f-w-600">{{ __('Set up two-factor authentication?') }}</h2>
    <p class="text-muted">
      {{ __('Add a one-time code app like Google Authenticator for extra security. You can also skip and enable it later in Profile → Security.') }}
    </p>
  </div>

  <div class="d-grid gap-3">
    <a href="{{ route('2fa.setup') }}" class="btn btn-primary text-white mb-4">{{ __('Set up now') }}</a>
    <a href="{{ \App\Providers\RouteServiceProvider::HOME }}" class="btn btn-outline-secondary">{{ __('Skip for now') }}</a>
  </div>
@endsection
