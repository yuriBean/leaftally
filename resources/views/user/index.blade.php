@extends('layouts.admin')
@php
    $profile = asset(Storage::url('uploads/avatar/'));
@endphp
@section('page-title')
    {{ __('Manage Companies') }}
@endsection
@push('script-page')
@endpush
@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a>
    </li>
    <li class="breadcrumb-item">{{ __('User') }}</li>
@endsection
@section('action-btn')
    <div class="flex gap-2">
        <button data-size="lg" data-url="{{ route('users.create') }}" data-ajax-popup="true" data-bs-toggle="tooltip"
            title="{{ __('Create New User') }}"
            class="flex items-center gap-2 border border-[#007C38] text-[#007C38] bg-white px-4 py-2 rounded-[6px] text-[14px] font-[500] hover:bg-[#007C3808] transition-all duration-200 shadow-sm min-w-fit">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Create new user
        </button>

        @if (\Auth::user()->type == 'company')
            <a data-size="md" href="{{ route('userlogs.index') }}"
               class="flex items-center gap-2 bg-[#007C38] text-white px-4 py-2 rounded-[6px] text-[14px] font-[500] hover:bg-[#005f2a] transition-all duration-200 shadow-sm min-w-fit">
                <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M13.1263 9.00047C15.4044 9.00047 17.2513 10.8473 17.2513 13.1255C17.2513 15.4036 15.4044 17.2505 13.1263 17.2505C10.8481 17.2505 9.00127 15.4036 9.00127 13.1255C9.00127 10.8473 10.8481 9.00047 13.1263 9.00047ZM11.1414 12.8603C10.995 12.7138 10.7575 12.7138 10.6111 12.8603C10.4646 13.0067 10.4646 13.2442 10.6111 13.3906L12.1111 14.8906C12.2575 15.0371 12.495 15.0371 12.6414 14.8906L15.6414 11.8906C15.7879 11.7442 15.7879 11.5067 15.6414 11.3603C15.495 11.2138 15.2575 11.2138 15.1111 11.3603L12.3763 14.0951L11.1414 12.8603ZM9.01792 10.4999C8.7954 10.8474 8.61577 11.225 8.48647 11.6252L3.19056 11.6254C2.88037 11.6254 2.62891 11.8769 2.62891 12.1871V12.6203C2.62891 13.022 2.77224 13.4106 3.03313 13.7161C3.97311 14.8169 5.44752 15.3762 7.50127 15.3762C7.94857 15.3762 8.3685 15.3497 8.76142 15.2968C8.94532 15.668 9.17595 16.0119 9.44505 16.3216C8.84842 16.4417 8.19975 16.5012 7.50127 16.5012C5.14185 16.5012 3.35234 15.8224 2.17762 14.4467C1.74279 13.9375 1.50391 13.2899 1.50391 12.6203V12.1871C1.50391 11.2555 2.25904 10.5004 3.19056 10.5004L9.01792 10.4999ZM7.50127 1.50391C9.57232 1.50391 11.2513 3.18284 11.2513 5.25391C11.2513 7.32498 9.57232 9.00392 7.50127 9.00392C5.43018 9.00392 3.75124 7.32498 3.75124 5.25391C3.75124 3.18284 5.43018 1.50391 7.50127 1.50391ZM7.50127 2.62891C6.0515 2.62891 4.87624 3.80416 4.87624 5.25391C4.87624 6.70366 6.0515 7.87892 7.50127 7.87892C8.95102 7.87892 10.1263 6.70366 10.1263 5.25391C10.1263 3.80416 8.95102 2.62891 7.50127 2.62891Z"
                        fill="white" />
                </svg>
                User log
            </a>
        @endif
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="user-div">
            <div class="col-xxl-12">
                <div class="justify-center flex flex-wrap gap-4">
                    @foreach ($users as $user)
                        <div class="single-user max-w-[275px] w-full bg-white shadow-sm border brder-[#E5E5E5] rounded-lg overflow-hidden text-center relative">
                            <div class="single-user-col">
                                <div class="user-col-fs">
                                    <div class="d-flex justify-content-between align-items-center">
                                        @if (\Auth::user()->type == 'super admin')
                                            <span class="absolute top-3 left-3 bg-green-100 text-green-700 text-xs font-medium px-6 py-0.5 rounded-full">
                                                {{ !empty($user->currentPlan) ? $user->currentPlan->name : '' }}
                                            </span>
                                        @else
                                            <span class="absolute top-3 left-3 bg-green-100 text-green-700 text-xs font-medium px-6 py-0.5 rounded-full">
                                                {{ ucfirst($user->type) }}
                                            </span>
                                        @endif
                                    </div>

                                    @if (Gate::check('edit user') || Gate::check('delete user'))
                                        @if ($user->is_active == 1 && $user->is_disable == 1)
                                            <button class="absolute top-3 right-3 text-gray-400 hover:text-gray-600 cursor-pointer"
                                                    type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="ti ti-dots-vertical"></i>
                                            </button>

                                            <div class="dropdown-menu dropdown-menu-end mt-0 w-[190px] bg-white border rounded-md shadow-lg text-sm p-0">
                                                @can('edit user')
                                                    <a href="#!" data-size="lg" data-url="{{ route('users.edit', $user->id) }}"
                                                       data-ajax-popup="true"
                                                       class="dropdown-item flex text-[#323232] gap-2 w-full px-4 py-2 text-left hover:bg-[#007C3812]"
                                                       data-bs-original-title="{{ __('Edit') }}">
                                                        <img src="{{ asset('web-assets/dashboard/icons/action_icons/edit.svg') }}" alt="edit">
                                                        <span>{{ __('Edit') }}</span>
                                                    </a>
                                                @endcan

                                                @can('delete user')
                                                    {!! Form::open([
                                                        'method' => 'DELETE',
                                                        'route' => ['users.destroy', $user['id']],
                                                        'id' => 'delete-form-' . $user['id'],
                                                    ]) !!}
                                                    <a href="#!"
                                                       class="dropdown-item bs-pass-para flex text-[#323232] gap-2 w-full px-4 py-2 text-left hover:bg-[#007C3812]">
                                                        <img src="{{ asset('web-assets/dashboard/icons/action_icons/delete.svg') }}" alt="delete">
                                                        <span>{{ __('Delete') }}</span>
                                                    </a>
                                                    {!! Form::close() !!}
                                                @endcan

                                                @if (Auth::user()->type == 'super admin')
                                                    <a href="{{ route('login.with.company', $user->id) }}"
                                                       class="dropdown-item flex text-[#323232] gap-2 w-full px-4 py-2 text-left hover:bg-[#007C3812]"
                                                       data-bs-original-title="{{ __('Login As Company') }}">
                                                        <i class="ti ti-replace"></i>
                                                        <span>{{ __('Login As Company') }}</span>
                                                    </a>
                                                @endif

                                                <a href="#!"
                                                   data-url="{{ route('users.reset', \Crypt::encrypt($user->id)) }}"
                                                   data-ajax-popup="true" data-size="md"
                                                   class="dropdown-item flex text-[#323232] gap-2 w-full px-4 py-2 text-left hover:bg-[#007C3812]s"
                                                   data-bs-original-title="{{ __('Reset Password') }}">
                                                    <img src="{{ asset('web-assets/dashboard/icons/action_icons/arrow-reset.svg') }}" alt="reset">
                                                    <span>{{ __('Reset Password') }}</span>
                                                </a>

                                                @if ($user->is_enable_login == 1)
                                                    <a href="{{ route('users.login', \Crypt::encrypt($user->id)) }}"
                                                       class="dropdown-item flex text-[#BE123C] gap-2 w-full px-4 py-2 text-left hover:bg-[#be123c2b]">
                                                        <img src="{{ asset('web-assets/dashboard/icons/action_icons/disable.svg') }}" alt="disable">
                                                        <span class="text-danger">{{ __('Login Disable') }}</span>
                                                    </a>
                                                @elseif ($user->is_enable_login == 0 && $user->password == null)
                                                    <a href="#"
                                                       data-url="{{ route('users.reset', \Crypt::encrypt($user->id)) }}"
                                                       data-ajax-popup="true" data-size="md" class="dropdown-item login_enable"
                                                       data-title="{{ __('New Password') }}">
                                                        <i class="ti ti-road-sign"></i>
                                                        <span class="text-success">{{ __('Login Enable') }}</span>
                                                    </a>
                                                @else
                                                    <a href="{{ route('users.login', \Crypt::encrypt($user->id)) }}" class="dropdown-item">
                                                        <i class="ti ti-road-sign"></i>
                                                        <span class="text-success">{{ __('Login Enable') }}</span>
                                                    </a>
                                                @endif
                                            </div>
                                        @else
                                            <a href="#" class="action-item text-lg"><i class="ti ti-lock"></i></a>
                                        @endif
                                    @endif
                                </div>

                                <div class="user-col-btm">
                                    <div class="flex justify-center user-img-cstm">
                                        <img src="{{ !empty($user->avatar) ? asset(Storage::url('uploads/avatar/' . $user->avatar)) : asset('web-assets/dashboard/icons/avatar.png') }}"
                                             class="w-[120px] h-[120px] rounded-full object-cover" width="120" height="120">
                                    </div>
                                    <div class="text-user border-t pt-4 {{ \Auth::user()->type == 'super admin' ? 'pb-0' : 'pb-4' }} border-[#E5E5E5]">
                                        <h4 class="text-gray-900 font-semibold">{{ $user->name }}</h4>
                                        <small class="text-gray-500 text-sm">{{ $user->email }}</small>
                                    </div>
                                    <div class="text-center" data-bs-toggle="tooltip" title="{{ __('Last Login') }}">
                                        {{ !empty($user->last_login_at) ? $user->last_login_at : '' }}
                                    </div>

                                    @if (\Auth::user()->type == 'super admin')
                                        <div class="row mt-3 px-4">
                                            <div class="col-12">
                                                <div class="bg-[#F6F6F6] rounded-[6px] py-2 px-4">
                                                    <div class="row">
                                                        <div class="col-4">
                                                            <div class="flex gap-2 text-[#323232] text-sm" data-bs-toggle="tooltip" title="{{ __('Users') }}">
                                                                <img src="{{ asset('web-assets/dashboard/icons/staff.svg') }}" alt="staff">
                                                                {{ $user->totalCompanyUser($user->id) }}
                                                            </div>
                                                        </div>
                                                        <div class="col-4">
                                                            <div class="flex gap-2 text-[#323232] text-sm" data-bs-toggle="tooltip" title="{{ __('Customers') }}">
                                                                <img src="{{ asset('web-assets/dashboard/icons/staff.svg') }}" alt="staff">
                                                                {{ $user->totalCompanyCustomer($user->id) }}
                                                            </div>
                                                        </div>
                                                        <div class="col-4">
                                                            <div class="flex gap-2 text-[#323232] text-sm" data-bs-toggle="tooltip" title="{{ __('Vendors') }}">
                                                                <img src="{{ asset('web-assets/dashboard/icons/staff.svg') }}" alt="staff">
                                                                {{ $user->totalCompanyVender($user->id) }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mt-4">
                                            <div class="px-4 flex gap-2 justify-center items-center">
                                                <div class="text-center Id text-[14px] leading-[24px] font-[500] ">
                                                    <a href="#" data-url="{{ route('plan.upgrade', $user->id) }}"
                                                       data-size="lg" data-ajax-popup="true"
                                                       class="py-[6px] px-[10px] btn bg-[#007C38] text-white hover:bg-[#005f2a]"
                                                       data-title="{{ __('Upgrade Plan') }}">{{ __('Upgrade Plan') }}</a>
                                                </div>
                                                <div class="text-center Id text-[14px] leading-[24px] font-[500] ">
                                                    <a href="#" data-url="{{ route('company.info', $user->id) }}"
                                                       data-size="lg" data-ajax-popup="true"
                                                       class="py-[6px] px-[10px] btn text-[#007C38] border-[#007C38] hover:bg-[#007C38] hover:text-white"
                                                       data-title="{{ __('Company Info') }}">{{ __('AdminHub') }}</a>
                                                </div>
                                            </div>
                                            <div class="text-[10px] mt-3 font-[500] leading-[24px] mb-2">
                                                <span class="text-[#323232]">{{ __('Plan Expired : ') }}
                                                    <span class="text-[#727272]">
                                                        {{ !empty($user->plan_expire_date) ? \Auth::user()->dateFormat($user->plan_expire_date) : __('Lifetime') }}
                                                    </span>
                                                </span>
                                            </div>

                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach

                </div>
            </div>
        </div>
    </div>
@endsection

@push('script-page')
<script>
$(document).on('change', '#password_switch', function() {
    if ($(this).is(':checked')) {
        $('.ps_div').removeClass('d-none');
        $('#password').attr("required", true);
    } else {
        $('.ps_div').addClass('d-none');
        $('#password').val(null);
        $('#password').removeAttr("required");
    }
});

$(document).on('click', '.login_enable', function() {
    setTimeout(function() {
        $('.modal-body').append($('<input>', {
            type: 'hidden',
            val: 'true',
            name: 'login_enable'
        }));
    }, 2000);
});

document.addEventListener('change', function (e) {
    const input = e.target;
    if (!input.matches('input[type="file"][name="avatar"]')) return;

    const file = input.files && input.files[0];
    if (!file) return;

    const sel = input.getAttribute('data-preview');
    let img = sel ? document.querySelector(sel) : null;
    if (!img) {
        img = input.closest('.form-group, .flex, .d-flex, .modal-body')?.querySelector('img.js-avatar-preview, img[id^="avatarPreview"]');
    }
    if (!img) return;

    const reader = new FileReader();
    reader.onload = (ev) => { img.src = ev.target.result; };
    reader.readAsDataURL(file);
}, false);
</script>
@endpush
