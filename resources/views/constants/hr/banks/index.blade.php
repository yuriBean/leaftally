@extends('layouts.admin')
@section('page-title', __('Banks'))

@section('breadcrumb')
  <li class="breadcrumb-item"><a href="{{ route('constants.index') }}">{{ __('Account Setup') }}</a></li>
  <li class="breadcrumb-item">{{ __('Banks') }}</li>
@endsection

@section('action-btn')
<style>
  .mcheck{display:inline-flex;align-items:center;cursor:pointer;user-select:none}
  .mcheck input{position:absolute;opacity:0;width:0;height:0}
  .mcheck .box{width:20px;height:20px;border:2px solid
  .mcheck .box:hover{box-shadow:0 1px 3px rgba(0,0,0,.08)}
  .mcheck input:focus + .box{box-shadow:0 0 0 3px rgba(0,124,56,.2)}
  .mcheck input:checked + .box{background:
  .mcheck input:checked + .box::after{content:"";position:absolute;left:6px;top:2px;width:5px;height:10px;border:2px solid
</style>

<div class="flex items-center gap-2 mt-2 sm:mt-0">
  {{-- Export All --}}
  <a href="{{ route('banks.export') }}"
     data-bs-toggle="tooltip" title="{{ __('Export') }}"
     style="border: 1px solid #007C38 !important"
     class="flex items-center gap-2 border border-[#007C38] text-[#007C38] bg-white px-4 py-2 rounded-[6px] text-[14px] font-[500] hover:bg-[#007C3808] transition-all duration-200 shadow-sm min-w-fit">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
    </svg>
    {{ __('Export') }}
  </a>

  {{-- Create --}}
  @can('create constant bank')
  <div class="float-end">
    <a href="#"
       data-url="{{ route('banks.create') }}"
       data-ajax-popup="true"
       data-title="{{ __('Add Bank') }}"
       class="flex items-center gap-2 bg-[#007C38] text-white px-4 py-2 rounded-[6px] text-[14px] font-[500] hover:bg-[#005f2a] shadow-sm">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v12M6 12h12"/></svg>
      {{ __('Create New') }}
    </a>
  </div>
  @endcan
</div>
@endsection

@section('content')
<div class="row">
  <div class="col-sm-12">

    {{-- Bulk Toolbar: Delete + Export Selected --}}
    @can('delete constant bank')
      <x-bulk-toolbar
        :deleteRoute="route('banks.bulk-destroy')"
        :exportRoute="route('banks.export-selected')"
        scope="banks"
        tableId="banks-table"
        selectedLabel="{{ __('Bank(s) selected') }}"
      />
    @endcan
    <div class="card border-0 rounded-2xl shadow-md overflow-hidden my-3">
      <div class="h-1 w-full" style="background:#007C38;"></div>

    <div class="card-body table-border-style mt-4">
      <div class="table-responsive table-new-design bg-white p-4  rounded-[8px]">
        <table id="banks-table" class="table datatable">
          <thead>
            <tr>
              {{-- master checkbox --}}
              <th data-sortable="false" data-type="html"
                  class="input-checkbox border border-[#E5E5E5] px-4 py-4 text-left text-[12px] font-[700] text-[#374151] uppercase tracking-wider w-12">
                <label class="mcheck">
                  <input type="checkbox" class="jsb-master" data-scope="banks">
                  <span class="box"></span>
                </label>
              </th>

              <th class="px-4 py-2 bg-[#F6F6F6] text-[12px] font-[600]">{{ __('Bank') }}</th>
              <th class="px-4 py-2 bg-[#F6F6F6] text-[12px] font-[600]" width="10%">{{ __('Action') }}</th>
            </tr>
          </thead>
          <tbody>
            @forelse($items as $row)
              <tr>
                {{-- row checkbox --}}
                <td class="input-checkbox px-4 py-4 text-left text-[12px] border-0 w-12">
                  <label class="mcheck">
                    <input type="checkbox" class="jsb-item" data-scope="banks" value="{{ $row->id }}" data-id="{{ $row->id }}">
                    <span class="box"></span>
                  </label>
                </td>

                <td class="px-4 py-3">{{ $row->name }}</td>
                <td class="Action relative px-4 py-3">
                  <button class="absolute top-3 right-3 text-gray-400 hover:text-gray-600" type="button" data-bs-toggle="dropdown">
                    <i class="ti ti-dots-vertical"></i>
                  </button>
                  <div class="dropdown-menu dropdown-menu-end mt-0 w-[190px] bg-white border rounded-md shadow-lg text-sm p-0">
                    @can('edit constant bank')
                      <a href="#"
                         class="dropdown-item flex gap-2 w-full px-4 py-2 hover:bg-[#007C3812]"
                         data-url="{{ route('banks.edit', $row->id) }}"
                         data-ajax-popup="true"
                         data-title="{{ __('Edit Bank') }}">
                        <img src="{{ asset('web-assets/dashboard/icons/action_icons/edit.svg') }}" alt="edit"/>
                        <span>{{ __('Edit') }}</span>
                      </a>
                    @endcan

                    @can('delete constant bank')
                      <form action="{{ route('banks.destroy',$row->id) }}" method="POST" id="del-bank-{{ $row->id }}">
                        @csrf @method('DELETE')
                      </form>
                      <a href="#"
                         class="dropdown-item flex gap-2 w-full px-4 py-2 hover:bg-[#007C3812]"
                         data-confirm="{{ __('Are You Sure?') . '|' . __('This action can not be undone. Do you want to continue?') }}"
                         data-confirm-yes="document.getElementById('del-bank-{{ $row->id }}').submit();">
                        <img src="{{ asset('web-assets/dashboard/icons/action_icons/delete.svg') }}" alt="delete"/>
                        <span>{{ __('Delete') }}</span>
                      </a>
                    @endcan
                  </div>
                </td>
              </tr>
            @empty
              <tr><td class="px-4 py-3 text-slate-500" colspan="3">{{ __('No data found.') }}</td></tr>
            @endforelse
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
  $(document).on('click', 'input[type=checkbox], label.mcheck, .dropdown-menu, [data-bs-toggle="dropdown"]', function(e){
    e.stopPropagation();
  });
</script>
@endpush
