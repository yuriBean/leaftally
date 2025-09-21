@extends('layouts.admin')
@push('script-page')
    <script type="text/javascript">
        $(document).on("click", ".email-template-checkbox", function() {
            var chbox = $(this);
            $.ajax({
                url: chbox.attr('data-url'),
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    status: chbox.val()
                },
                type: 'post',
                success: function(response) {
                    if (response.is_success) {
                        toastr('Success', response.success, 'success');
                        if (chbox.val() == 1) {
                            $('#' + chbox.attr('id')).val(0);
                        } else {
                            $('#' + chbox.attr('id')).val(1);
                        }
                    } else {
                        toastr('Error', response.error, 'error');
                    }
                },
                error: function(response) {
                    response = response.responseJSON;
                    if (response.is_success) {
                        toastr('Error', response.error, 'error');
                    } else {
                        toastr('Error', response, 'error');
                    }
                }
            })
        });
    </script>
@endpush
@section('page-title')
    @if (\Auth::user()->type == 'company')
        {{ __('Email Notification') }}
    @else
        {{ __('Manage Email Templates') }}
    @endif
@endsection
@section('title')
    <div class="d-inline-block">
        @if (\Auth::user()->type == 'company')
            <h5 class="h4 d-inline-block font-weight-400 mb-0">{{ __('Email Notification') }}</h5>
        @else
            <h5 class="h4 d-inline-block font-weight-400 mb-0">{{ __('Email Templates') }}</h5>
        @endif
    </div>
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    @if (\Auth::user()->type == 'company')
        <li class="breadcrumb-item active" aria-current="page">{{ __('Email Notification') }}</li>
    @else
        <li class="breadcrumb-item active" aria-current="page">{{ __('Email Template') }}</li>
    @endif
@endsection
@section('action-btn')
@endsection
@section('content')
    <div class="row">
        <div class="col-xl-12">
            
                <div class="bg-white border border-[#E5E5E5] rounded-[8px] p-3">
                    <h5></h5>
                    <div class="table-responsive">
                        <table class="table datatable" id="pc-dt-simple">
                            <thead>
                                <tr>
                                    <th scope="col" class="sort px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-start" data-sort="name">{{ __('Name') }}</th>
                                    @if (\Auth::user()->type == 'company')
                                        <th class="text-start px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600]" style="width: 150px;">{{ __('On / Off') }}</th>
                                    @else
                                        <th class="text-start px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600]" style="width: 150px;">{{ __('Action') }}</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($EmailTemplates as $EmailTemplate)
                                    <tr>
                                        <td class="px-4 py-3 border border-[#E5E5E5] text-[#323232] text-start">{{ $EmailTemplate->name }}</td>
                                        <td class="px-6 py-3 border border-[#E5E5E5] text-[#323232] text-start">
                                            <div class="dt-buttons">
                                                @if (\Auth::user()->type == 'super admin')
                                                    <div class="text-start">
                                                        <button class="flex w-100 text-gray-400 hover:text-gray-600 cursor-pointer" 
                                                            type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                            <i class="ti ti-dots-vertical"></i>
                                                        </button>
                                                        <div class="dropdown-menu dropdown-menu-end mt-0 w-[190px] bg-white border rounded-md shadow-lg text-sm p-0">
                                                            <a href="{{ route('manage.email.language', [$EmailTemplate->id, \Auth::user()->lang]) }}"
                                                               class="align-items-center dropdown-item flex text-[#323232] gap-2 w-full px-4 py-2 text-left hover:bg-[#007C3812]">
                                                                <i class="ti ti-eye"></i>
                                                                <span>{{ __('View') }}</span>
                                                            </a>
                                                            <a href="{{ route('manage.email.language', [$EmailTemplate->id, \Auth::user()->lang]) }}"
                                                               class="dropdown-item flex text-[#323232] gap-2 w-full px-4 py-2 text-left hover:bg-[#007C3812]">
                                                                <img src="{{ asset('web-assets/dashboard/icons/action_icons/edit.svg') }}"
                                                            alt="edit" />
                                                                <span>{{ __('Edit') }}</span>
                                                            </a>
                                                        </div>
                                                    </div>
                                                @endif
                                                @if (\Auth::user()->type == 'company')
                                                    <div class="text-start d-flex align-items-center justify-content-start gap-2">
                                                        <a href="{{ route('manage.email.language', [$EmailTemplate->id, \Auth::user()->lang]) }}"
                                                           class="btn btn-sm btn-primary">
                                                            <img src="{{ asset('web-assets/dashboard/icons/action_icons/edit.svg') }}"
                                                            alt="edit" /> {{ __('Edit') }}
                                                        </a>
                                                        <div class="form-check form-switch d-inline-block">
                                                            <input class="form-check-input email-template-checkbox" type="checkbox"
                                                                id="email_tempalte_{{ $EmailTemplate->template->id }}"
                                                                @if ($EmailTemplate->template->is_active == 1) checked="checked" @endif
                                                                value="{{ $EmailTemplate->template->is_active }}"
                                                                data-url="{{ route('status.email.language', [$EmailTemplate->template->id]) }}"
                                                                role="switch">
                                                            <label class="custom-control-label form-control-label"
                                                                for="email_tempalte_{{ $EmailTemplate->template->id }}"></label>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            
        </div>
    </div>
@endsection
