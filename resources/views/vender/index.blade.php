@extends('layouts.admin')

@php
    $profile = asset(Storage::url('uploads/avatar/'));
@endphp

@section('page-title')
    {{ __('Manage Vendors') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('Vendor') }}</li>
@endsection

@section('action-btn')
<style>
  .mcheck{display:inline-flex;align-items:center;cursor:pointer;user-select:none}
  .mcheck input{position:absolute;opacity:0;width:0;height:0}
  .mcheck .box{width:20px;height:20px;border:2px solid #dee2e6;border-radius:4px;position:relative;}
  .mcheck .box:hover{box-shadow:0 1px 3px rgba(0,0,0,.08)}
  .mcheck input:focus + .box{box-shadow:0 0 0 3px rgba(0,124,56,.2)}
  .mcheck input:checked + .box{background:#007C38;border-color:#007C38;}
  .mcheck input:checked + .box::after{content:"";position:absolute;left:6px;top:2px;width:5px;height:10px;border:2px solid white;border-top:0;border-left:0;transform:rotate(45deg);}
</style>

<div class="flex items-center gap-2 mt-2 sm:mt-0">
    <a href="#" style="border: 1px solid #007C38 !important"
       class="flex items-center gap-2 border border-[#007C38] text-[#007C38] bg-white px-4 py-2 rounded-[6px] text-[14px] font-[500] hover:bg-[#007C3808] transition-all duration-200 shadow-sm min-w-fit d-none"
       data-url="{{ route('vender.file.import') }}" data-ajax-popup="true" data-bs-toggle="tooltip"
       title="{{ __('Import') }}">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 711-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
        </svg>
        {{ __('Import') }}
    </a>

    <a href="{{ route('vender.export') }}" style="border: 1px solid #007C38 !important"
       class="flex items-center gap-2 border border-[#007C38] text-[#007C38] bg-white px-4 py-2 rounded-[6px] text-[14px] font-[500] hover:bg-[#007C3808] transition-all duration-200 shadow-sm min-w-fit"
       data-bs-toggle="tooltip" title="{{ __('Export All') }}">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
        </svg>
        {{ __('Export All') }}
    </a>

    @can('create vender')
      <a href="#" data-size="xl" data-url="{{ route('vender.create') }}" data-ajax-popup="true"
         data-title="{{ __('Create New Vendor') }}" data-bs-toggle="tooltip" title="{{ __('Create') }}"
         class="flex items-center gap-2 bg-[#007C38] text-white px-4 py-2 rounded-[6px] text-[14px] font-[500] hover:bg-[#005f2a] transition-all duration-200 shadow-sm min-w-fit">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
        </svg>
        {{ __('Add Vendor') }}
      </a>
    @endcan
</div>
@endsection

@section('content')
<div class="row">
  <div class="col-md-12">

    @can('delete vender')
      {{-- Bulk toolbar with Delete + Export Selected --}}
      <x-bulk-toolbar
        :deleteRoute="route('vender.bulk-destroy')"
        :exportRoute="route('vender.export-selected')"
        scope="vendors"
        tableId="vendors-table"
        selectedLabel="{{ __('Vendor selected') }}"
        exportLabel="{{ __('Export Selected') }}"
      />
    @endcan

    <div class="card border-0 rounded-2xl shadow-md overflow-hidden">
      <div class="h-1 w-full" style="background:#007C38;"></div>

      <div class="card-body table-border-style table-border-style">
        <div class="table-responsive table-new-design bg-white p-4">
          <table id="vendors-table" class="table datatable border border-[#E5E5E5] rounded-[8px]">
          <thead class="bg-[#F6F6F6] text-[#323232] text-[12px] leading-[24px]">
            <tr>
              <th class="input-checkbox border border-[#E5E5E5] px-4 lg:px-6 py-4 text-left text-[12px] font-[700] text-[#374151] uppercase tracking-wider w-12">
                <label class="mcheck">
                  <input type="checkbox" class="jsb-master" data-scope="vendors">
                  <span class="box"></span>
                </label>
              </th>
              <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600]">
              <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600]">{{ __('Name') }}</th>
              <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600]">{{ __('Contact') }}</th>
              <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600]">{{ __('Email') }}</th>
              <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600]">{{ __('Balance') }}</th>
              <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600]">{{ __('Last Login At') }}</th>
              <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600]">{{ __('Action') }}</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($venders as $k => $Vender)
              <tr class="cust_tr" id="vend_detail">
                <td class="input-checkbox px-4 lg:px-6 py-4 text-left text-[12px] font-[700] text-[#374151] uppercase tracking-wider w-12">
                  <label class="mcheck">
                    <input type="checkbox"
                           class="jsb-item"
                           data-scope="vendors"
                           value="{{ $Vender['id'] }}"
                           data-id="{{ $Vender['id'] }}">
                    <span class="box"></span>
                  </label>
                </td>

                <td class="Id px-4 py-3 border border-[#E5E5E5] text-gray-700">
                  @can('show vender')
                    <a href="{{ route('vender.show', \Crypt::encrypt($Vender['id'])) }}"
                       class="border border-[#137051] leading-[24px] text-[#137051] rounded-[4px] text-[12px] font-[500] px-5">
                      {{ \Auth::user()->venderNumberFormat($Vender['vender_id']) }}
                    </a>
                  @else
                    <a href="#"
                       class="border border-[#137051] leading-[24px] text-[#137051] rounded-[4px] text-[12px] font-[500] px-5">
                      {{ \Auth::user()->venderNumberFormat($Vender['vender_id']) }}
                    </a>
                  @endcan
                </td>

                <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">{{ $Vender['name'] }}</td>
                <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">{{ $Vender['contact'] }}</td>
                <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">{{ $Vender['email'] }}</td>
                <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">{{ \Auth::user()->priceFormat($Vender['balance']) }}</td>
                <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">{{ !empty($Vender->last_login_at) ? $Vender->last_login_at : '-' }}</td>

                <td class="Action px-4 py-3 border border-[#E5E5E5] text-gray-700 relative">
                  <button class="absolute top-3 right-3 text-gray-400 hover:text-gray-600 cursor-pointer"
                          type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="ti ti-dots-vertical"></i>
                  </button>

                  <div class="dropdown-menu dropdown-menu-end mt-0 w-[190px] bg-white border rounded-md shadow-lg text-sm p-0">
                    @if ($Vender['is_active'] == 0)
                      <div class="dropdown-item flex items-center text-[#323232] gap-2 px-4 py-2">
                        <i class="ti ti-lock text-red-500" title="Inactive"></i>
                        <span>{{ __('Inactive') }}</span>
                      </div>
                    @else
                      @if ($Vender->is_enable_login == 0 && $Vender->password == null)
                        <a href="#"
                           class="dropdown-item flex text-[#323232] gap-2 w-full px-4 py-2 text-left hover:bg-[#007C3812]"
                           data-url="{{ route('vender.reset', \Crypt::encrypt($Vender['id'])) }}"
                           data-ajax-popup="true" data-size="md" data-bs-toggle="tooltip"
                           title="{{ __('Forgot Password') }}" data-title="{{ __('Reset Password') }}">
                           <img src="{{ asset('web-assets/dashboard/icons/action_icons/arrow-reset.svg') }}" alt="reset" />
                           <span>{{ __('Forgot Password') }}</span>
                        </a>
                      @endif

                      @can('show vender')
                        <a href="{{ route('vender.show', \Crypt::encrypt($Vender['id'])) }}"
                           class="dropdown-item flex text-[#323232] gap-2 w-full px-4 py-2 text-left hover:bg-[#007C3812]"
                           data-bs-toggle="tooltip" title="{{ __('View') }}">
                           <img src="{{ asset('web-assets/dashboard/icons/preview.svg') }}" alt="preview" />
                           <span>{{ __('Preview') }}</span>
                        </a>
                      @endcan

                      @can('edit vender')
                        <a href="#"
                           class="dropdown-item flex text-[#323232] gap-2 w-full px-4 py-2 text-left hover:bg-[#007C3812]"
                           data-size="xl" data-title="{{ __('Edit Vendor') }}"
                           data-url="{{ route('vender.edit', $Vender['id']) }}"
                           data-ajax-popup="true" title="{{ __('Edit') }}"
                           data-bs-toggle="tooltip">
                           <img src="{{ asset('web-assets/dashboard/icons/action_icons/edit.svg') }}" alt="edit" />
                           <span>{{ __('Edit') }}</span>
                        </a>
                      @endcan

                      @can('delete vender')
                        {!! Form::open([
                            'method' => 'DELETE',
                            'route' => ['vender.destroy', $Vender['id']],
                            'id' => 'delete-form-' . $Vender['id'],
                        ]) !!}
                          <a href="#"
                             class="dropdown-item bs-pass-para flex text-[#323232] gap-2 w-full px-4 py-2 text-left hover:bg-[#007C3812]"
                             data-bs-toggle="tooltip" title="{{ __('Delete') }}">
                             <img src="{{ asset('web-assets/dashboard/icons/action_icons/delete.svg') }}" alt="delete" />
                             <span>{{ __('Delete') }}</span>
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
      </div>
    </div>
  </div>

  </div>
</div>
@endsection

@push('script-page')
<script>
    $(document).on('click', '#billing_data', function() {
      $("[name='shipping_name']").val($("[name='billing_name']").val());
      $("[name='shipping_country']").val($("[name='billing_country']").val());
      $("[name='shipping_state']").val($("[name='billing_state']").val());
      $("[name='shipping_city']").val($("[name='billing_city']").val());
      $("[name='shipping_phone']").val($("[name='billing_phone']").val());
      $("[name='shipping_zip']").val($("[name='billing_zip']").val());
      $("[name='shipping_address']").val($("[name='billing_address']").val());
  });
   $(document).on('change', '#password_switch', function() {
       if ($(this).is(':checked')) {
           $('.ps_div').removeClass('d-none');
           $('#password').attr("required", true);
           $('#user_name').attr("required", true);
   
       } else {
           $('.ps_div').addClass('d-none');
           $('#password').val(null);
           $('#user_name').val(null);
           $('#password').removeAttr("required");
           $('#user_name').removeAttr("required");
       }
   });
  $(document).on('click', 'input[type=checkbox], label.mcheck, .dropdown-menu, [data-bs-toggle="dropdown"]', function(e){ e.stopPropagation(); });

  (function(){
    const $table = $('#vendors-table');
    const tbody = $table.find('tbody').get(0);
    if (tbody && 'MutationObserver' in window) {
      new MutationObserver(function(){ setTimeout(function(){ $(document).trigger('vendors-table-updated'); }, 0); })
        .observe(tbody, {childList:true});
    }
  })();
</script>
@endpush
