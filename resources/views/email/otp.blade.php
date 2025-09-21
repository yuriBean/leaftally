<!doctype html>
<html>
  <body style="font-family: system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial;">
    <h2 style="margin:0 0 8px">{{ __('Hi') }}, {{ $name }}</h2>
    <p style="margin:0 0 16px">{{ __('Use the code below to verify your email and continue your signup:') }}</p>
    <div style="font-size:28px;font-weight:700;letter-spacing:6px">{{ $code }}</div>
    <p style="margin:16px 0 8px;color:#666">{{ __('This code will expire in 10 minutes.') }}</p>
    <p style="margin:0;color:#666">{{ config('app.name') }}</p>
  </body>
</html>
