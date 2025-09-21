@extends('layouts.admin')

@section('page-title')
  {{ __('Manage Production Orders') }}
@endsection

@section('breadcrumb')
  <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
  <li class="breadcrumb-item">{{ __('Production') }}</li>
@endsection

@section('action-btn')
<style>
  /* material checkbox (match other pages) */
  .mcheck{display:inline-flex;align-items:center;cursor:pointer;user-select:none}
  .mcheck input{position:absolute;opacity:0;width:0;height:0}
  .mcheck .box{width:20px;height:20px;border:2px solid #D1D5DB;border-radius:6px;background:#fff;display:inline-block;position:relative;transition:all .15s}
  .mcheck .box:hover{box-shadow:0 1px 3px rgba(0,0,0,.08)}
  .mcheck input:focus + .box{box-shadow:0 0 0 3px rgba(0,124,56,.2)}
  .mcheck input:checked + .box{background:#007C38;border-color:#007C38}
  .mcheck input:checked + .box::after{content:"";position:absolute;left:6px;top:2px;width:5px;height:10px;border:2px solid #fff;border-top:none;border-left:none;transform:rotate(45deg)}
</style>

<div class="flex items-center gap-2 mt-2 sm:mt-0">
  {{-- Export (ALL) --}}
  <a href="{{ route('production.export') }}"
     class="flex items-center gap-2 border border-[#007C38] text-[#007C38] bg-white px-4 py-2 rounded-[6px] text-[14px] font-[500] hover:bg-[#007C3808] transition-all duration-200 shadow-sm min-w-fit"
     style="border: 1px solid #007C38 !important"
     data-bs-toggle="tooltip" title="{{ __('Export') }}">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
    </svg>
    {{ __('Export') }}
  </a>

  @can('create production')
    <a href="{{ route('production.create') }}"
       class="flex items-center gap-2 bg-[#007C38] text-white px-4 py-2 rounded-[6px] text-[14px] font-[500] hover:bg-[#005f2a] transition-all duration-200 shadow-sm min-w-fit"
       data-bs-toggle="tooltip" title="{{ __('Create') }}">
      <i class="ti ti-plus"></i> {{ __('Create Production') }}
    </a>
  @endcan
</div>
@endsection

@section('content')
  {{-- Bulk toolbar (appears when rows selected) --}}
  @can('delete production')
<div class="mt-4">
      <x-bulk-toolbar
      :deleteRoute="route('production.bulk-destroy')"
      :exportRoute="route('production.export-selected')"
      scope="productions"
      tableId="productions-table"
      selectedLabel="{{ __('Production selected') }}"
    />
</div>
  @endcan

  <div class="row">
    <div class="col-md-12">
      <div class="card border-0 rounded-2xl shadow-md overflow-hidden my-3">
        <div class="h-1 w-full" style="background:#007C38;"></div>

      <div class="card-body bg-white m-4">
        <div class="table-responsive table-new-design bg-white">
          <table id="productions-table" class="table datatable border border-[#E5E5E5] rounded-[8px]">
            <thead class="bg-[#F6F6F6]">
              <tr>
                {{-- master checkbox --}}
                <th data-sortable="false" data-type="html"
                    class="input-checkbox border border-[#E5E5E5] px-4 lg:px-6 py-4 text-left text-[12px] font-[700] text-[#374151] uppercase tracking-wider w-12">
                  <label class="mcheck">
                    <input type="checkbox" class="jsb-master" data-scope="productions">
                    <span class="box"></span>
                  </label>
                </th>
                <th class="px-4 py-1 border border-[#E5E5E5] font-[600] text-[12px]">{{ __('Code') }}</th>
                <th class="px-4 py-1 border border-[#E5E5E5] font-[600] text-[12px]">{{ __('BOM') }}</th>
                <th class="px-4 py-1 border border-[#E5E5E5] font-[600] text-[12px]">{{ __('Planned Date') }}</th>
                <th class="px-4 py-1 border border-[#E5E5E5] font-[600] text-[12px]">{{ __('Status') }}</th>
                <th class="px-4 py-1 border border-[#E5E5E5] font-[600] text-[12px] text-end">{{ __('Total Cost') }}</th>
                @if (Gate::check('edit production') || Gate::check('delete production') || Gate::check('manage production'))
                  <th class="px-4 py-1 border border-[#E5E5E5] font-[600] text-[12px]">{{ __('Action') }}</th>
                @endif
              </tr>
            </thead>
            <tbody>
              @foreach ($jobs as $job)
                <tr class="prd_tr"
                    data-url="{{ route('production.show', $job->id) }}"
                    data-id="{{ $job->id }}">
                  {{-- row checkbox --}}
                  <td class="input-checkbox px-4 lg:px-6 py-4 text-left text-[12px] border-0 w-12">
                    <label class="mcheck">
                      <input type="checkbox"
                             class="jsb-item"
                             data-scope="productions"
                             value="{{ $job->id }}"
                             data-id="{{ $job->id }}">
                      <span class="box"></span>
                    </label>
                  </td>

                  <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">
                    <a href="{{ route('production.show', $job->id) }}"
                       class="border border-[#137051] leading-[24px] text-[#137051] rounded-[4px] text-[12px] font-[500] px-5">
                      {{ $job->code }}
                    </a>
                  </td>
                  <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">
                    {{ $job->bom->name ?? '-' }}
                  </td>
                  <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">
                    {{ $job->planned_date ? \Auth::user()->dateFormat($job->planned_date) : '-' }}
                  </td>
                  <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">
                    @if($job->status==='draft')
                      <span class="badge fix_badges bg-secondary p-2 px-3">{{ __('Draft') }}</span>
                    @elseif($job->status==='in_process')
                      <span class="badge fix_badges bg-warning p-2 px-3">{{ __('In Process') }}</span>
                    @elseif($job->status==='finished')
                      <span class="badge fix_badges bg-primary p-2 px-3">{{ __('Finished') }}</span>
                    @else
                      <span class="badge fix_badges bg-danger p-2 px-3">{{ __('Cancelled') }}</span>
                    @endif
                  </td>
                  <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700 text-end">
                    {{ \Auth::user()->priceFormat($job->total_cost ?? 0) }}
                  </td>

                  @if (Gate::check('edit production') || Gate::check('delete production') || Gate::check('manage production'))
                    <td class="Action px-4 py-3 text-right relative border border-[#E5E5E5]">
                      <button class="w-100 text-gray-400 hover:text-gray-600 cursor-pointer" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="ti ti-dots-vertical"></i>
                      </button>
                      <div class="dropdown-menu dropdown-menu-end mt-0 w-[220px] bg-white border rounded-md shadow-lg text-sm p-0">
                        <li>
                          <a href="{{ route('production.show', $job->id) }}"
                             class="dropdown-item flex items-center gap-2 w-full px-4 py-2 hover:bg-[#007C3812]">
                            <i class="ti ti-eye"></i><span>{{ __('Show') }}</span>
                          </a>
                        </li>
                        @can('edit production')
                          @if($job->status === 'draft')
                            <li>
                              <a href="{{ route('production.edit', $job->id) }}"
                                 class="dropdown-item flex items-center gap-2 w-full px-4 py-2 hover:bg-[#007C3812]">
                                <i class="ti ti-pencil"></i><span>{{ __('Edit') }}</span>
                              </a>
                            </li>
                          @endif
                        @endcan
                        @can('delete production')
                          <li>
                            {!! Form::open(['method' => 'DELETE', 'route' => ['production.destroy', $job->id]]) !!}
                              <a href="#"
                                 class="dropdown-item flex items-center gap-2 w-full px-4 py-2 hover:bg-[#007C3812] bs-pass-para">
                                <i class="ti ti-trash"></i><span>{{ __('Delete') }}</span>
                              </a>
                            {!! Form::close() !!}
                          </li>
                        @endcan
                      </div>
                    </td>
                  @endif
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
  // stop row navigation when clicking controls
  $(document).on('click', 'input[type=checkbox], label.mcheck, .dropdown-menu, [data-bs-toggle="dropdown"]', function(e){
    e.stopPropagation();
  });

  // row click -> show
  $(document).on('click', 'tr.prd_tr', function(){
    const url = $(this).data('url');
    if(url) window.location = url;
  });
</script>
@endpush
