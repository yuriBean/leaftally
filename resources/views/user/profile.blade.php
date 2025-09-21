@extends('layouts.admin')

@php
$profile = asset(Storage::url('app/public/uploads/avatar/'));
@endphp

@section('page-title', __('Profile Account'))

@section('breadcrumb')
<li class="breadcrumb-item">{{ __('Profile') }}</li>
@endsection

@section('content')
<div class="row">
    <div class="col-xl-3">
        <div class="card sticky-top" style="top:30px">
            <div class="list-group list-group-flush" id="useradd-sidenav">
                <a href="#personal_info" class="list-group-item list-group-item-action">{{ __('Personal Info') }}
                    <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                </a>

                <a href="#change_password" class="list-group-item list-group-item-action">{{ __('Change Password') }}
                    <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                </a>

                <a href="#security" class="list-group-item list-group-item-action">{{ __('Security') }}
                    <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                </a>
            </div>
        </div>
    </div>

    <div class="col-xl-9">
        {{-- Personal Info --}}
        <div id="personal_info" class="card border-0 rounded-2xl shadow-md overflow-hidden my-3">
      <div class="h-1 w-full" style="background:#007C38;"></div>
            
            <div class="card-header"><h5>{{ __('Personal Info') }}</h5></div>
            <div class="card-body">
                {{ Form::model($userDetail, ['route' => ['update.account'], 'method' => 'post', 'enctype' => 'multipart/form-data']) }}
                @csrf
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label>{{ __('Name') }}</label><x-required />
                            <input class="form-control" name="name" value="{{ $userDetail->name }}" required>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label>{{ __('Email') }}</label><x-required />
                            <input class="form-control" name="email" type="email" value="{{ $userDetail->email }}" required>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label>{{ __('Avatar') }}</label>
                            <div class="choose-files">
                                <label for="avatar">
                                    <div class="bg-primary profile_update">
                                        <i class="ti ti-upload px-1"></i>{{ __('Choose file here') }}
                                    </div>
                                    <input type="file" name="profile" id="avatar" class="form-control file">
                                    <img src="{{ \App\Models\Utility::get_file($userDetail->avatar) ?? asset('uploads/avatar/avatar.png') }}"
                                         width="100" class="mt-2 rounded border">
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 text-end">
                        <button type="submit" class="btn btn-primary">{{ __('Save Changes') }}</button>
                    </div>
                </div>
                {{ Form::close() }}
            </div>
        </div>

        {{-- Change Password --}}
        <div id="change_password" class="card border-0 rounded-2xl shadow-md overflow-hidden my-3">
      <div class="h-1 w-full" style="background:#007C38;"></div>
      <div class="card-header"><h5>{{ __('Change Password') }}</h5></div>
            <div class="card-body">
                <form method="post" action="{{ route('update.password') }}">
                    @csrf
                    <div class="row">
                        <div class="col-lg-6 form-group">
                            <label>{{ __('Old Password') }}</label><x-required />
                            <input type="password" name="current_password" class="form-control" required>
                        </div>
                        <div class="col-lg-6 form-group">
                            <label>{{ __('New Password') }}</label><x-required />
                            <input type="password" name="new_password" class="form-control" required>
                        </div>
                        <div class="col-lg-6 form-group">
                            <label>{{ __('Confirm New Password') }}</label><x-required />
                            <input type="password" name="confirm_password" class="form-control" required>
                        </div>
                        <div class="col-12 text-end">
                            <button class="btn btn-primary">{{ __('Save Changes') }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Security / Two-Factor --}}
        <div id="security" class="card border-0 rounded-2xl shadow-md overflow-hidden my-3">
      <div class="h-1 w-full" style="background:#007C38;"></div>
      <div class="card-header"><h5>{{ __('Two-Factor Authentication') }}</h5></div>
            <div class="card-body">
                @if ($userDetail->two_factor_secret)
                    <p class="text-success">{{ __('Two-factor authentication is enabled.') }}</p>
                    <form action="{{ route('2fa.disable') }}" method="POST" class="d-inline">
                        @csrf
                        <button class="btn btn-danger">{{ __('Disable 2FA') }}</button>
                    </form>
                    <form action="{{ route('2fa.recovery.regenerate') }}" method="POST" class="d-inline ms-2">
                        @csrf
                        <button class="btn btn-secondary">{{ __('Regenerate Recovery Codes') }}</button>
                    </form>

                    @if(!empty($recoveryCodes))
                        <div class="mt-4">
                            <h6>{{ __('Your Recovery Codes') }}</h6>
                            <ul class="list-group">
                                @foreach($recoveryCodes as $code)
                                    <li class="list-group-item font-monospace">{{ $code }}</li>
                                @endforeach
                            </ul>
                            <small class="text-muted">{{ __('Save these codes in a safe place. Each can be used once if you lose access to your authenticator app.') }}</small>
                        </div>
                    @endif
                @else
                    <p>{{ __('Two-factor is currently disabled.') }}</p>
                    <a href="{{ route('2fa.setup') }}" class="btn btn-primary my-2 ">{{ __('Enable 2FA') }}</a>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
