@extends('layouts.admin')

@section('page-title')
  {{ __('Assets') }}
@endsection
<script src="{{ asset('js/unsaved.js') }}"></script>

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
  <a href="{{ route('account-assets.export') }}"
     data-bs-toggle="tooltip" title="{{ __('Export') }}"
     style="border: 1px solid #007C38 !important"
     class="flex items-center gap-2 border border-[#007C38] text-[#007C38] bg-white px-4 py-2 rounded-[6px] text-[14px] font-[500] hover:bg-[#007C3808] transition-all duration-200 shadow-sm min-w-fit">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
    </svg>
    {{ __('Export') }}
  </a>

  {{-- Create --}}
  @can('create assets')
    <a href="#"
       data-url="{{ route('account-assets.create') }}"
       data-bs-toggle="tooltip" title="{{ __('Create') }}"
       data-ajax-popup="true" data-title="{{ __('Create New Assets') }}"
       class="flex items-center gap-2 bg-[#007C38] text-white px-4 py-2 rounded-[6px] text-[14px] font-[500] hover:bg-[#005f2a] transition-all duration-200 shadow-sm min-w-fit">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
      </svg>
      {{ __('Create New') }}
    </a>
  @endcan
</div>
@endsection

@section('content')
  <div class="row">
    <div class="col-sm-12">

      {{-- Bulk Toolbar: Delete + Export Selected (only shows when selection exists) --}}
      @can('delete assets')
        <x-bulk-toolbar
          :deleteRoute="route('account-assets.bulk-destroy')"
          :exportRoute="route('account-assets.export-selected')"
          scope="assets"
          tableId="assets-table"
          selectedLabel="{{ __('Asset(s) selected') }}"
        />
      @endcan
      <div class="card border-0 rounded-2xl shadow-md overflow-hidden my-3">
        <div class="h-1 w-full" style="background:#007C38;"></div>

      <div class="card-body table-border-style">
        <div class="table-responsive table-new-design bg-white p-4">
          <table id="assets-table" class="table datatable border border-[#E5E5E5] rounded-[8px] mt-4">
            <thead>
              <tr>
                {{-- master checkbox --}}
                <th data-sortable="false" data-type="html"
                    class="input-checkbox border border-[#E5E5E5] px-4 py-4 text-left text-[12px] font-[700] text-[#374151] uppercase tracking-wider w-12">
                  <label class="mcheck">
                    <input type="checkbox" class="jsb-master" data-scope="assets">
                    <span class="box"></span>
                  </label>
                </th>

                <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]">{{ __('Name') }}</th>
                <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]">{{ __('Purchase Date') }}</th>
                <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]">{{ __('Supported Date') }}</th>
                <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]">{{ __('Amount') }}</th>
                <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]">{{ __('Depreciation Rate') }}</th>
                <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]">{{ __('Current Book Value') }}</th>
                <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]">{{ __('Description') }}</th>
                <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]">{{ __('Action') }}</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($assets as $asset)
                <tr>
                  {{-- row checkbox --}}
                  <td class="input-checkbox px-4 py-4 text-left text-[12px] border-0 w-12">
                    <label class="mcheck">
                      <input type="checkbox" class="jsb-item" data-scope="assets" value="{{ $asset->id }}" data-id="{{ $asset->id }}">
                      <span class="box"></span>
                    </label>
                  </td>

                  <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">{{ $asset->name }}</td>
                  <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">{{ \Auth::user()->dateFormat($asset->purchase_date) }}</td>
                  <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">{{ \Auth::user()->dateFormat($asset->supported_date) }}</td>
                  <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">{{ \Auth::user()->priceFormat($asset->amount) }}</td>
                  <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">{{ $asset->depreciation_rate ?? 0 }}%</td>
                  <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">{{ \Auth::user()->priceFormat($asset->getCurrentBookValue()) }}</td>
                  <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">{{ $asset->description ?: '-' }}</td>

                  <td class="Action px-4 relative py-3 border border-[#E5E5E5] text-gray-700">
                    <button class="w-100 text-gray-400 hover:text-gray-600 cursor-pointer"
                            type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                      <i class="ti ti-dots-vertical"></i>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end mt-0 w-[190px] bg-white border rounded-md shadow-lg text-sm p-0">
                      @can('edit assets')
                        <li>
                          <a href="#"
                             class="dropdown-item flex items-center text-gray-700 gap-2 w-full px-4 py-2 text-left hover:bg-[#007C3812] text-sm"
                             data-url="{{ route('account-assets.edit', $asset->id) }}"
                             data-ajax-popup="true" data-title="{{ __('Edit Assets') }}"
                             data-bs-toggle="tooltip" title="{{ __('Edit') }}">
                            <i class="ti ti-pencil"></i>
                            <span>{{ __('Edit') }}</span>
                          </a>
                        </li>
                      @endcan
                      @can('delete assets')
                        <li>
                          {!! Form::open([
                            'method' => 'DELETE',
                            'route' => ['account-assets.destroy', $asset->id],
                            'id' => 'delete-form-' . $asset->id,
                          ]) !!}
                          <a href="#"
                             class="dropdown-item flex items-center text-gray-700 gap-2 w-full px-4 py-2 text-left hover:bg-[#007C3812] text-sm bs-pass-para"
                             data-bs-toggle="tooltip" title="{{ __('Delete') }}"
                             data-confirm="{{ __('Are You Sure?') . '|' . __('This action can not be undone. Do you want to continue?') }}"
                             data-confirm-yes="document.getElementById('delete-form-{{ $asset->id }}').submit();">
                            <i class="ti ti-trash"></i>
                            <span>{{ __('Delete') }}</span>
                          </a>
                          {!! Form::close() !!}
                        </li>
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
  $(document).on('click', 'input[type=checkbox], label.mcheck, .dropdown-menu, [data-bs-toggle="dropdown"]', function(e){ e.stopPropagation(); });
</script>
@endpush
