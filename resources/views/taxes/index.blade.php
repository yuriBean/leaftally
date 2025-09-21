@extends('layouts.admin')

@section('page-title')
    {{ __('Manage Tax Rate') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('constants.index')}}">{{__('Account Setup')}}</a></li>
    <li class="breadcrumb-item">{{ __('Taxes') }}</li>
@endsection

@section('action-btn')
<style>
  /* material checkbox (same as Assets/Goals) */
  .mcheck{display:inline-flex;align-items:center;cursor:pointer;user-select:none}
  .mcheck input{position:absolute;opacity:0;width:0;height:0}
  .mcheck .box{width:20px;height:20px;border:2px solid #D1D5DB;border-radius:6px;background:#fff;display:inline-block;position:relative;transition:all .15s}
  .mcheck .box:hover{box-shadow:0 1px 3px rgba(0,0,0,.08)}
  .mcheck input:focus + .box{box-shadow:0 0 0 3px rgba(0,124,56,.2)}
  .mcheck input:checked + .box{background:#007C38;border-color:#007C38}
  .mcheck input:checked + .box::after{content:"";position:absolute;left:6px;top:2px;width:5px;height:10px;border:2px solid #fff;border-top:none;border-left:none;transform:rotate(45deg)}
</style>

<div class="flex items-center gap-2 mt-2 sm:mt-0">
    {{-- Export All --}}
    <a href="{{ route('taxes.export') }}"
       data-bs-toggle="tooltip" title="{{ __('Export') }}"
       style="border: 1px solid #007C38 !important"
       class="flex items-center gap-2 border border-[#007C38] text-[#007C38] bg-white px-4 py-2 rounded-[6px] text-[14px] font-[500] hover:bg-[#007C3808] transition-all duration-200 shadow-sm min-w-fit">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
        </svg>
        {{ __('Export') }}
    </a>

    {{-- Create --}}
    @can('create constant tax')
        <a href="#"
           data-url="{{ route('taxes.create') }}"
           data-ajax-popup="true" data-title="{{ __('Create Tax Rate') }}"
           data-bs-toggle="tooltip" title="{{ __('Create') }}"
           class="flex items-center gap-2 bg-[#007C38] text-white px-4 py-2 rounded-[6px] text-[14px] font-[500] hover:bg-[#005f2a] transition-all duration-200 shadow-sm min-w-fit">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            {{ __('Create') }}
        </a>
    @endcan
</div>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">

            {{-- ✅ Bulk toolbar (component you already use) --}}
            @can('delete constant tax')
                <x-bulk-toolbar
                    :deleteRoute="route('taxes.bulk-destroy')"
                    :exportRoute="route('taxes.export-selected')"
                    scope="taxes"
                    tableId="taxes-table"
                    selectedLabel="{{ __('Tax rate(s) selected') }}"
                />
            @endcan
            <div class="card border-0 rounded-2xl shadow-md overflow-hidden my-3">
                <div class="h-1 w-full" style="background:#007C38;"></div>
          
            <div class="card-body table-border-style">
                <div class="table-responsive table-new-design bg-white p-4">
                    <table id="taxes-table" class="table datatable">
                        <thead>
                            <tr>
                                {{-- master checkbox --}}
                                <th data-sortable="false" data-type="html"
                                    class="input-checkbox border border-[#E5E5E5] px-4 py-4 text-left text-[12px] font-[700] text-[#374151] uppercase tracking-wider w-12">
                                    <label class="mcheck">
                                        <input type="checkbox" class="jsb-master" data-scope="taxes">
                                        <span class="box"></span>
                                    </label>
                                </th>

                                <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]">{{ __('Tax Name') }}</th>
                                <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]">{{ __('Rate %') }}</th>
                                <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]" width="10%">{{ __('Action') }}</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($taxes as $taxe)
                                <tr class="font-style">
                                    {{-- row checkbox --}}
                                    <td class="input-checkbox px-4 py-4 text-left text-[12px] border-0 w-12">
                                        <label class="mcheck">
                                            <input type="checkbox" class="jsb-item" data-scope="taxes" value="{{ $taxe->id }}" data-id="{{ $taxe->id }}">
                                            <span class="box"></span>
                                        </label>
                                    </td>

                                    <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">{{ $taxe->name }}</td>
                                    <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">{{ $taxe->rate }}</td>

                                    <td class="Action px-4 py-3 border border-[#E5E5E5] relative text-gray-700">
                                        <button
                                            class="absolute top-3 right-3 text-gray-400 hover:text-gray-600 cursor-pointer"
                                            type="button" data-bs-toggle="dropdown" aria-haspopup="true"
                                            aria-expanded="false">
                                            <i class="ti ti-dots-vertical"></i>
                                        </button>

                                        <div class="dropdown-menu dropdown-menu-end mt-0 w-[190px] bg-white border rounded-md shadow-lg text-sm p-0">
                                            @can('edit constant tax')
                                                <a href="#"
                                                   class="dropdown-item bs-pass-para flex text-[#323232] gap-2 w-full px-4 py-2 text-left hover:bg-[#007C3812]"
                                                   data-url="{{ route('taxes.edit', $taxe->id) }}"
                                                   data-ajax-popup="true" data-title="{{ __('Edit Tax Rate') }}"
                                                   data-bs-toggle="tooltip" title="{{ __('Edit') }}">
                                                    <img src="{{ asset('web-assets/dashboard/icons/action_icons/edit.svg') }}" alt="edit" />
                                                    <span>{{ __('Edit') }}</span>
                                                </a>
                                            @endcan

                        @can('delete constant tax')
                            {!! Form::open([
                                'method' => 'DELETE',
                                'route' => ['taxes.destroy', $taxe->id],
                                'id' => 'delete-form-' . $taxe->id,
                            ]) !!}
                            <a href="#"
                               class="dropdown-item bs-pass-para flex text-[#323232] gap-2 w-full px-4 py-2 text-left hover:bg-[#007C3812]"
                               data-bs-toggle="tooltip" title="{{ __('Delete') }}"
                               data-confirm="{{ __('Are You Sure?') . '|' . __('This action can not be undone. Do you want to continue?') }}"
                               data-confirm-yes="document.getElementById('delete-form-{{ $taxe->id }}').submit();">
                                <img src="{{ asset('web-assets/dashboard/icons/action_icons/delete.svg') }}" alt="delete" />
                                <span>{{ __('Delete') }}</span>
                            </a>
                            {!! Form::close() !!}
                        @endcan
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
  // Keep dropdowns & checkboxes from interfering with row/cell clicks
  $(document).on('click', 'input[type=checkbox], label.mcheck, .dropdown-menu, [data-bs-toggle="dropdown"]', function(e){
    e.stopPropagation();
  });
</script>
@endpush
