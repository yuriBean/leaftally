@extends('layouts.admin')

@php
    $profile = asset(Storage::url('uploads/avatar/'));
@endphp

@section('page-title')
    {{ __('Manage Customers') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('Customer') }}</li>
@endsection

@section('action-btn')
<style>
  .sub-title{font-weight:600;background:#f8f9fa;color:#495057;padding:0.5rem 1rem;border-radius:6px;}
  .mcheck{display:inline-flex;align-items:center;cursor:pointer;user-select:none}
  .mcheck input{position:absolute;opacity:0;width:0;height:0}
  .mcheck .box{width:20px;height:20px;border:2px solid #dee2e6;border-radius:4px;position:relative;}
  .mcheck .box:hover{box-shadow:0 1px 3px rgba(0,0,0,.08)}
  .mcheck input:focus + .box{box-shadow:0 0 0 3px rgba(0,124,56,.2)}
  .mcheck input:checked + .box{background:#007C38;border-color:#007C38;}
  .mcheck input:checked + .box::after{content:"";position:absolute;left:6px;top:2px;width:5px;height:10px;border:2px solid white;border-top:0;border-left:0;transform:rotate(45deg);}
</style>

<div class="flex items-center gap-2 mt-2 sm:mt-0">

    <a href="#"
       data-size="md"
       data-bs-toggle="tooltip"
       title="{{ __('Import') }}"
       data-url="{{ route('customer.file.import') }}"
       data-ajax-popup="true"
       data-title="{{ __('Import customer CSV file') }}"
       style="border: 1px solid #007C38 !important"
       class="flex items-center gap-2 border border-[#007C38] text-[#007C38] bg-white px-4 py-2 rounded-[6px] text-[14px] font-[500] hover:bg-[#007C3808] transition-all duration-200 shadow-sm min-w-fit d-none">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 712-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
        </svg>
        {{ __('Import') }}
    </a>

    {{-- Export dropdown: All / Selected --}}
    <div class="dropdown">
      <button class="flex items-center gap-2 border border-[#007C38] text-[#007C38] bg-white px-4 py-2 rounded-[6px] text-[14px] font-[500] hover:bg-[#007C3808] transition-all duration-200 shadow-sm min-w-fit"
              data-bs-toggle="dropdown" aria-expanded="false" style="border: 1px solid #007C38 !important">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
        </svg>
        {{ __('Export') }}
        <i class="ti ti-chevron-down text-sm"></i>
      </button>
      <div class="dropdown-menu dropdown-menu-end mt-2 w-56 bg-white border border-[#E5E5E5] rounded-[6px] shadow-lg p-1">
        <a class="dropdown-item px-3 py-2 text-[14px] text-[#374151] hover:bg-[#F3F4F6] rounded-[4px]"
           href="{{ route('customer.export') }}">{{ __('Export All') }}</a>
        <a class="dropdown-item px-3 py-2 text-[14px] text-[#374151] hover:bg-[#F3F4F6] rounded-[4px]"
           href="#"
           data-export-selected
           data-scope="customers"
           data-route="{{ route('customer.export-selected') }}">{{ __('Export Selected') }}</a>
      </div>
    </div>

    <a href="#"
       data-size="xl"
       data-url="{{ route('customer.create') }}"
       data-ajax-popup="true"
       data-bs-toggle="tooltip"
       title="{{ __('Create') }}"
       data-title="{{ __('Create Customer') }}"
       class="flex items-center gap-2 bg-[#007C38] text-white px-4 py-2 rounded-[6px] text-[14px] font-[500] hover:bg-[#005f2a] transition-all duration-200 shadow-sm min-w-fit">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
        </svg>
        {{ __('Add Customer') }}
    </a>
</div>
@endsection

@section('content')
<div class="row">
  <div class="col-md-12">

    <div class="multi-collapse mt-2" id="multiCollapseExample1">
      <div class="card border-0 rounded-2xl shadow-md overflow-hidden">
        <div class="h-1 w-full" style="background:#007C38;"></div>
        <div class="card-body p-4 sm:p-6">
          {{ Form::open(['route' => ['customer.index'], 'method' => 'GET', 'id' => 'frm_submit']) }}
          <div class="row g-3 align-items-end">
            <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12">
              <label class="block text-sm font-semibold text-gray-800 mb-1.5">{{ __('Name') }}</label>
              <div class="relative">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A4 4 0 018 17h8a4 4 0 012.879 1.196M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                {{ Form::text('name', request('name',''), ['class' => 'w-full appearance-none bg-white border border-[#E5E7EB] rounded-xl px-3 pl-9 py-2 text-[14px] text-[#374151] placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-[#007C38] focus:border-[#007C38] transition-all', 'placeholder' => 'Enter Name']) }}
              </div>
            </div>
      
            <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12">
              <label class="block text-sm font-semibold text-gray-800 mb-1.5">{{ __('Email') }}</label>
              <div class="relative">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12H8m12 0a4 4 0 01-4 4H8a4 4 0 01-4-4m16 0a4 4 0 00-4-4H8a4 4 0 00-4 4"/></svg>
                {{ Form::text('email', request('email',''), ['class' => 'w-full appearance-none bg-white border border-[#E5E7EB] rounded-xl px-3 pl-9 py-2 text-[14px] text-[#374151] placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-[#007C38] focus:border-[#007C38] transition-all', 'placeholder' => 'Enter Email']) }}
              </div>
            </div>
      
            <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12">
              <label class="block text-sm font-semibold text-gray-800 mb-1.5">{{ __('Contact') }}</label>
              <div class="relative">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3l2 4H7l4 8 3-6h4l-3 6 2 4H9l-6-12z"/></svg>
                {{ Form::text('contact', request('contact',''), ['class' => 'w-full appearance-none bg-white border border-[#E5E7EB] rounded-xl px-3 pl-9 py-2 text-[14px] text-[#374151] placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-[#007C38] focus:border-[#007C38] transition-all', 'placeholder' => 'Enter Phone Number']) }}
              </div>
            </div>
      
            <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12">
              <label class="block text-sm font-semibold text-gray-800 mb-1.5">{{ __('Balance') }}</label>
              <div class="relative">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-2.21 0-4 .895-4 2s1.79 2 4 2 4 .895 4 2-1.79 2-4 2m0-8V4m0 12v4"/></svg>
                {{ Form::text('balance', request('balance',''), ['class' => 'w-full appearance-none bg-white border border-[#E5E7EB] rounded-xl px-3 pl-9 py-2 text-[14px] text-[#374151] placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-[#007C38] focus:border-[#007C38] transition-all', 'placeholder' => 'Enter Balance']) }}
              </div>
            </div>
      
            <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12">
              <label class="block text-sm font-semibold text-gray-800 mb-1.5">{{ __('Last Login') }}</label>
              <div class="relative">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3M4 11h16M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2h-3M4 7a2 2 0 00-2 2v8a2 2 0 002 2h1"/></svg>
                {{ Form::text('last_login', request('last_login',''), ['class' => 'w-full appearance-none bg-white border border-[#E5E7EB] rounded-xl px-3 pl-9 py-2 text-[14px] text-[#374151] placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-[#007C38] focus:border-[#007C38] transition-all month-btn', 'id' => 'pc-daterangepicker-1', 'placeholder' => 'Select Date']) }}
              </div>
            </div>
      
            <div class="col-12 col-md-auto ms-auto">
              <div class="flex flex-wrap gap-2 justify-end mt-1">
                <a href="#" data-bs-toggle="tooltip" title="{{ __('Apply') }}"
                   onclick="document.getElementById('frm_submit').submit(); return false;"
                   class="inline-flex items-center gap-2 rounded-xl bg-[#007C38] px-4 py-2 text-[14px] font-semibold text-white shadow-sm transition-all hover:bg-[#056e33] hover:shadow-md">
                  <i class="ti ti-search text-base"></i>
                  {{ __('Apply') }}
                </a>
                <a href="{{ route('customer.index') }}" data-bs-toggle="tooltip" title="{{ __('Reset') }}"
                   class="inline-flex items-center gap-2 rounded-xl border border-gray-300 bg-white px-4 py-2 text-[14px] font-semibold text-gray-700 shadow-sm transition-all hover:bg-gray-50 hover:shadow-md">
                  <i class="ti ti-refresh text-base"></i>
                  {{ __('Reset') }}
                </a>
              </div>
            </div>
          </div>
          {{ Form::close() }}
        </div>
      </div>
      
    </div>

    @can('delete customer')
      <x-bulk-toolbar
        :deleteRoute="route('customer.bulk-destroy')"
        :exportRoute="route('customer.export-selected')"
        scope="customers"
        tableId="customers-table"
        selectedLabel="{{ __('Customer selected') }}"
      />
    @endcan

    <div class="card border-0 rounded-2xl shadow-md overflow-hidden">
      <div class="h-1 w-full" style="background:#007C38;"></div>
      <div class="card-body table-border-style table-border-style">
      <div class="table-responsive table-new-design bg-white p-4">
        <table id="customers-table" class="table datatable border border-[#E5E5E5] rounded-[8px] ">
          <thead>
            <tr>
              {{-- Important for simple-datatables: disable sorting and treat as HTML --}}
              <th data-sortable="false" data-type="html"
                  class="input-checkbox border border-[#E5E5E5] px-4 lg:px-6 py-4 text-left text-[12px] font-[700] text-[#374151] uppercase tracking-wider w-12">
                <label class="mcheck">
                  <input type="checkbox" class="jsb-master" data-scope="customers">
                  <span class="box"></span>
                </label>
              </th>
              <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]">
              <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]">{{ __('Name') }}</th>
              <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]">{{ __('Contact') }}</th>
              <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]">{{ __('Email') }}</th>
              <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]">{{ __('Balance') }}</th>
              <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]">{{ __('Last Login') }}</th>
              <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]">{{ __('Action') }}</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($customers as $k => $customer)
              <tr class="cust_tr" id="cust_detail"
                  data-url="{{ route('customer.show', $customer['id']) }}"
                  data-id="{{ $customer['id'] }}">
                <td class="input-checkbox px-4 lg:px-6 py-4 text-left text-[12px] font-[700] text-[#374151] uppercase tracking-wider border-0 w-12">
                  <label class="mcheck">
                    <input type="checkbox"
                           class="jsb-item"
                           data-scope="customers"
                           value="{{ $customer['id'] }}"
                           data-id="{{ $customer['id'] }}">
                    <span class="box"></span>
                  </label>
                </td>

                <td class="Id px-4 py-3 border border-[#E5E5E5] text-gray-700">
                  @can('show customer')
                    <a href="{{ route('customer.show', \Crypt::encrypt($customer['id'])) }}"
                       class="border border-[#137051] leading-[24px] text-[#137051] rounded-[4px] text-[12px] font-[500] px-5">
                       {{ \Auth::user()->customerNumberFormat($customer['customer_id']) }}
                    </a>
                  @else
                    <a href="#" class="border border-[#137051] leading-[24px] text-[#137051] rounded-[4px] text-[12px] font-[500] px-5">
                      {{ \Auth::user()->customerNumberFormat($customer['customer_id']) }}
                    </a>
                  @endcan
                </td>

                <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">{{ $customer['name'] }}</td>
                <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">{{ $customer['contact'] }}</td>
                <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">{{ $customer['email'] }}</td>
                <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">{{ \Auth::user()->priceFormat($customer['balance']) }}</td>
                <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">{{ !empty($customer->last_login_at) ? $customer->last_login_at : '-' }}</td>

                <td class="Action px-4 py-3 text-right relative border border-[#E5E5E5]">
                  <button class="w-100 text-gray-400 hover:text-gray-600 cursor-pointer" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="ti ti-dots-vertical"></i>
                  </button>
                  <div class="dropdown-menu dropdown-menu-end mt-0 w-[190px] bg-white border rounded-md shadow-lg text-sm p-0">
                    @if ($customer['is_active'] == 0)
                      <div class="flex dropdown-item flex items-center text-[#323232] gap-2 px-4 py-2">
                        <i class="ti ti-lock text-red-500" title="Inactive"></i>
                        <span>{{ __('Inactive') }}</span>
                      </div>
                    @else
                      @if ($customer->is_enable_login == 0 && $customer->password == null)
                        <a href="#" class="flex dropdown-item items-center flex text-[#323232] gap-2 w-full px-4 py-2 text-left hover:bg-[#007C3812]"
                           data-url="{{ route('customer.reset', \Crypt::encrypt($customer['id'])) }}"
                           data-ajax-popup="true" data-size="md" data-bs-toggle="tooltip"
                           title="{{ __('Forgot Password') }}" data-title="{{ __('Reset Password') }}">
                           <i class="ti ti-key text-gray-500"></i><span>{{ __('Reset Password') }}</span>
                        </a>
                      @endif

                      @can('show customer')
                        <a href="{{ route('customer.show', \Crypt::encrypt($customer['id'])) }}"
                           class="flex dropdown-item flex items-center text-[#323232] gap-2 w-full px-4 py-2 text-left hover:bg-[#007C3812]"
                           data-bs-toggle="tooltip" title="{{ __('View') }}">
                           <i class="ti ti-eye"></i><span>{{ __('Preview') }}</span>
                        </a>
                      @endcan

                      @can('edit customer')
                        <a href="#" class="flex dropdown-item flex items-center text-[#323232] gap-2 w-full px-4 py-2 text-left hover:bg-[#007C3812]"
                           data-url="{{ route('customer.edit', $customer['id']) }}"
                           data-ajax-popup="true" data-size="xl" data-bs-toggle="tooltip"
                           title="{{ __('Edit') }}" data-title="{{ __('Edit Customer') }}">
                           <i class="ti ti-pencil"></i><span>{{ __('Edit') }}</span>
                        </a>
                      @endcan

                      @can('delete customer')
                        {!! Form::open([
                            'method' => 'DELETE',
                            'route' => ['customer.destroy', $customer['id']],
                            'id' => 'delete-form-' . $customer['id'],
                        ]) !!}
                          <a href="#!" class="flex dropdown-item items-center bs-pass-para flex text-[#323232] gap-2 w-full px-4 py-2 text-left hover:bg-[#007C3812]">
                            <i class="ti ti-trash"></i><span>{{ __('Delete') }}</span>
                          </a>
                        {!! Form::close() !!}
                      @endcan
                    @endif
                  </div>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div></div>
    </div>

  </div>
</div>
@endsection

