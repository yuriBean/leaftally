@extends('layouts.admin')
@section('page-title')
    @if (\Auth::user()->type == 'company')
        {{ __('Manage Notification Templates') }}
    @endif
@endsection
@section('title')
    <div class="d-inline-block">
        @if (\Auth::user()->type == 'company')
            <h5 class="h4 d-inline-block font-weight-400 mb-0">{{ __('Notification Templates') }}</h5>
        @endif
    </div>
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    @if (\Auth::user()->type == 'company')
        <li class="breadcrumb-item active" aria-current="page">{{ __('Notification Templates') }}</li>
    @else
        <li class="breadcrumb-item active" aria-current="page">{{ __('Email Template') }}</li>
    @endif
@endsection
@section('action-btn')
@endsection
@section('content')
    <div class="mt-4">
        <div class="col-xl-12">

            <div class="card-header card-body table-border-style">
                <h5></h5>
                <div class="table-responsive table-new-design bg-white p-4">
                    <table class="table datatable" id="pc-dt-simple">
                        <thead>
                            <tr>
                                <th scope="col"
                                    class="sort px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]"
                                    data-sort="name"> {{ __('Name') }}</th>
                                @if (\Auth::user()->type == 'company')
                                    <th class=" px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]">
                                        {{ __('Action') }}</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($notification_templates as $notification_template)
                                <tr>
                                    <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">
                                        {{ $notification_template->name }}</td>
                                    <td class="px-4 py-3 border text-center border-[#E5E5E5] text-gray-700">
                                        <button class="text-gray-400 hover:text-gray-600 cursor-pointer"
                                            type="button" data-bs-toggle="dropdown" aria-haspopup="true"
                                            aria-expanded="false">
                                            <i class="ti ti-dots-vertical"></i>
                                        </button>
                                        <div
                                            class="dropdown-menu dropdown-menu-end mt-0 w-[190px] bg-white border rounded-md shadow-lg text-sm p-0">
                                            @if (\Auth::user()->type == 'company')
                                                <li>
                                                    <a href="{{ route('manage.notification.language', [$notification_template->id, \Auth::user()->lang]) }}"
                                                        class="dropdown-item flex items-center text-gray-700 gap-2 w-full px-4 py-2 text-left hover:bg-[#007C3812] text-sm"
                                                        data-bs-toggle="tooltip"
                                                        data-bs-original-title="{{ __('View') }}" title="">
                                                        <i class="ti ti-eye"></i>
                                                        <span>{{ __('View') }}</span>
                                                    </a>
                                                </li>
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
