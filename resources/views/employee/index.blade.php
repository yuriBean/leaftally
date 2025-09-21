@extends('layouts.admin')

@section('page-title')
  {{ __('Manage Employees') }}
@endsection

@section('breadcrumb')
  <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
  <li class="breadcrumb-item">{{ __('Employees') }}</li>
@endsection

@section('action-btn')
<style>
  /* Material checkbox (match your other modules) */
  .mcheck{display:inline-flex;align-items:center;cursor:pointer;user-select:none}
  .mcheck input{position:absolute;opacity:0;width:0;height:0}
  .mcheck .box{width:20px;height:20px;border:2px solid #D1D5DB;border-radius:6px;background:#fff;display:inline-block;position:relative;transition:all .15s}
  .mcheck .box:hover{box-shadow:0 1px 3px rgba(0,0,0,.08)}
  .mcheck input:focus + .box{box-shadow:0 0 0 3px rgba(0,124,56,.2)}
  .mcheck input:checked + .box{background:#007C38;border-color:#007C38}
  .mcheck input:checked + .box::after{content:"";position:absolute;left:6px;top:2px;width:5px;height:10px;border:2px solid #fff;border-top:none;border-left:none;transform:rotate(45deg)}
</style>

<div class="flex items-center gap-2 mt-2 sm:mt-0">

  {{-- Toggle Filters --}}
  <button id="toggle-search"
          type="button"
          aria-expanded="false"
          class="flex items-center gap-2 border border-[#E5E7EB] text-[#374151] bg-white px-4 py-2 rounded-[6px] text-[14px] font-[500] hover:bg-[#F9FAFB] transition-all duration-200 shadow-sm">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
    </svg>
    <span class="js-label">{{ __('Search') }}</span>
  </button>

  {{-- Import --}}
  <a href="#"
     data-size="md"
     data-bs-toggle="tooltip" title="{{__('Import')}}"
     data-url="{{ route('employee.file.import') }}"
     data-ajax-popup="true"
     data-title="{{__('Import employee CSV file')}}"
     class="flex items-center gap-2 bg-white text-[#007C38] border border-[#007C38] px-4 py-2 rounded-[6px] text-[14px] font-[500] hover:bg-[#007C3808] transition-all duration-200 shadow-sm d-none">
    <i class="ti ti-file-import"></i> {{ __('Import') }}
  </a>

  {{-- Export All --}}
  <a href="{{ route('employee.export') }}"
     data-bs-toggle="tooltip" title="{{ __('Export') }}"
     class="flex items-center gap-2 bg-white text-[#007C38] border border-[#007C38] px-4 py-2 rounded-[6px] text-[14px] font-[500] hover:bg-[#007C3808] transition-all duration-200 shadow-sm">
    <i class="ti ti-file-export"></i> {{ __('Export') }}
  </a>

  {{-- Create --}}
  <a href="{{ route('employee.create') }}"
     data-bs-toggle="tooltip" title="{{ __('Create') }}"
     class="flex items-center gap-2 bg-[#007C38] text-white px-4 py-2 rounded-[6px] text-[14px] font-[500] hover:bg-[#005f2a] transition-all duration-200 shadow-sm">
    <i class="ti ti-plus"></i> {{ __('Create') }}
  </a>
</div>
@endsection

@section('content')
<div class="row">
  <div class="col-xl-12">

    {{-- SEARCH / FILTER PANEL (hidden by default; persisted) --}}
    <div id="search-panel" class="card border-0 shadow-sm rounded-[8px] mb-4 overflow-hidden" style="display:none">
      <div class="card border-0 rounded-2xl shadow-md overflow-hidden my-3">
        <div class="h-1 w-full" style="background:#007C38;"></div>
  
      <div class="card-body p-6">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-4">
          {{-- Keyword --}}
          <div class="lg:col-span-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Keyword') }}</label>
            <div class="relative">
              <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-[#6B7280]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
              </div>
              <input type="text" id="f-keyword"
                     class="block w-full pl-10 pr-3 py-2 border border-[#E5E7EB] rounded-[6px] bg-white text-[14px] placeholder-[#9CA3AF] focus:outline-none focus:ring-1 focus:ring-[#007C38] focus:border-[#007C38]"
                     placeholder="{{ __('Name, Email, ID, Phone') }}">
            </div>
          </div>

          {{-- Branch --}}
          <div class="lg:col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Branch') }}</label>
            <select id="f-branch"
                    class="w-full border border-[#E5E7EB] rounded-[6px] px-3 py-2 text-[14px] focus:ring-1 focus:ring-[#007C38] focus:border-[#007C38]">
              <option value="">{{ __('All') }}</option>
              @php $branchesList = $employees->map(fn($e)=> optional($e->branch)->name)->filter()->unique()->sort(); @endphp
              @foreach($branchesList as $b)
                <option value="{{ strtolower($b) }}">{{ $b }}</option>
              @endforeach
            </select>
          </div>

          {{-- Department --}}
          <div class="lg:col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Department') }}</label>
            <select id="f-department"
                    class="w-full border border-[#E5E7EB] rounded-[6px] px-3 py-2 text-[14px] focus:ring-1 focus:ring-[#007C38] focus:border-[#007C38]">
              <option value="">{{ __('All') }}</option>
              @php $departmentsList = $employees->map(fn($e)=> optional($e->department)->name)->filter()->unique()->sort(); @endphp
              @foreach($departmentsList as $d)
                <option value="{{ strtolower($d) }}">{{ $d }}</option>
              @endforeach
            </select>
          </div>

          {{-- Designation --}}
          <div class="lg:col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Designation') }}</label>
            <select id="f-designation"
                    class="w-full border border-[#E5E7EB] rounded-[6px] px-3 py-2 text-[14px] focus:ring-1 focus:ring-[#007C38] focus:border-[#007C38]">
              <option value="">{{ __('All') }}</option>
              @php $designationsList = $employees->map(fn($e)=> optional($e->designation)->name)->filter()->unique()->sort(); @endphp
              @foreach($designationsList as $g)
                <option value="{{ strtolower($g) }}">{{ $g }}</option>
              @endforeach
            </select>
          </div>

          {{-- Type --}}
          <div class="lg:col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Employee Type') }}</label>
            <select id="f-type"
                    class="w-full border border-[#E5E7EB] rounded-[6px] px-3 py-2 text-[14px] focus:ring-1 focus:ring-[#007C38] focus:border-[#007C38]">
              <option value="">{{ __('All') }}</option>
              <option value="permanent">{{ __('Permanent') }}</option>
              <option value="contract">{{ __('Contract') }}</option>
              <option value="temporary">{{ __('Temporary') }}</option>
              <option value="fixed-permanent">{{ __('Fixed Permanent') }}</option>
              <option value="fixed-temporary">{{ __('Fixed Temporary') }}</option>
            </select>
          </div>

          {{-- Status --}}
          <div class="lg:col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Status') }}</label>
            <select id="f-status"
                    class="w-full border border-[#E5E7EB] rounded-[6px] px-3 py-2 text-[14px] focus:ring-1 focus:ring-[#007C38] focus:border-[#007C38]">
              <option value="">{{ __('All') }}</option>
              <option value="active">{{ __('Active') }}</option>
              <option value="inactive">{{ __('Inactive') }}</option>
            </select>
          </div>

          {{-- DOJ From --}}
          <div class="lg:col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('DOJ From') }}</label>
            <input type="date" id="f-doj-from"
                   class="w-full border border-[#E5E7EB] rounded-[6px] px-3 py-2 text-[14px] focus:ring-1 focus:ring-[#007C38] focus:border-[#007C38]">
          </div>

          {{-- DOJ To --}}
          <div class="lg:col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('DOJ To') }}</label>
            <input type="date" id="f-doj-to"
                   class="w-full border border-[#E5E7EB] rounded-[6px] px-3 py-2 text-[14px] focus:ring-1 focus:ring-[#007C38] focus:border-[#007C38]">
          </div>

          {{-- Actions --}}
          <div class="lg:col-span-2 flex gap-2 items-end">
            <button id="filters-apply" type="button"
                    class="inline-flex items-center gap-2 bg-[#007C38] text-white px-4 py-2 rounded-[6px] text-[14px] font-[500] hover:bg-[#005f2a] transition-all duration-200">
              <i class="ti ti-search"></i> {{ __('Apply') }}
            </button>
            <button id="filters-clear" type="button"
                    class="inline-flex items-center gap-2 border border-[#E5E7EB] text-[#374151] bg-white px-4 py-2 rounded-[6px] text-[14px] font-[500] hover:bg-[#F9FAFB]">
              <i class="ti ti-x"></i> {{ __('Clear') }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>

<x-bulk-toolbar
  deleteRoute="{{ route('employee.bulk-destroy') }}"
  exportRoute="{{ route('employee.export-selected') }}"
  scope="employees"
  tableId="employees-table"
  selectedLabel="{{ __('employee(s) selected') }}"
/>

<div class="card border-0 rounded-2xl shadow-md overflow-hidden my-3">
  <div class="h-1 w-full" style="background:#007C38;"></div>
  <div class="card-body table-border-style">
        <div class="table-responsive table-new-design bg-white p-4">
          <table id="employees-table" class="table datatable border border-[#E5E5E5] rounded-[8px]">
            <thead>
              <tr>
                {{-- master checkbox --}}
                <th data-sortable="false" data-type="html"
                    class="input-checkbox border border-[#E5E5E5] px-4 py-4 text-left text-[12px] font-[700] text-[#374151] uppercase tracking-wider w-12">
                  <label class="mcheck">
                    <input type="checkbox" class="jsb-master" data-scope="employees">
                    <span class="box"></span>
                  </label>
                </th>
                <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]">{{ __('Employee ID') }}</th>
                <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]">{{ __('Name') }}</th>
                <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]">{{ __('Email / Phone') }}</th>
                <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]">{{ __('Branch / Department') }}</th>
                <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]">{{ __('Designation') }}</th>
                <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]">{{ __('Date Of Joining') }}</th>
                @if(Gate::check('edit employee') || Gate::check('delete employee') || Gate::check('manage employee'))
                  <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]" width="10%">{{ __('Action') }}</th>
                @endif
              </tr>
            </thead>

            <tbody>
              @foreach ($employees as $employee)
                <tr class="employee-row"
                    data-name="{{ strtolower($employee->name) }}"
                    data-email="{{ strtolower($employee->email) }}"
                    data-idfmt="{{ strtolower(\Auth::user()->employeeIdFormat($employee->employee_id)) }}"
                    data-branch="{{ strtolower(optional($employee->branch)->name ?? '') }}"
                    data-dept="{{ strtolower(optional($employee->department)->name ?? '') }}"
                    data-desig="{{ strtolower(optional($employee->designation)->name ?? '') }}"
                    data-type="{{ strtolower($employee->employee_type ?? '') }}"
                    data-status="{{ $employee->is_active ? 'active' : 'inactive' }}"
                    data-doj="{{ $employee->company_doj ?? '' }}"
                    data-phone="{{ strtolower($employee->phone ?? '') }}">
                  {{-- row checkbox --}}
                  <td class="input-checkbox px-4 py-4 text-left text-[12px] border-0 w-12">
                    <label class="mcheck">
                      <input type="checkbox" class="jsb-item" data-scope="employees" value="{{ $employee->id }}" data-id="{{ $employee->id }}">
                      <span class="box"></span>
                    </label>
                  </td>

                  {{-- ID (opens SHOW PAGE — no permission gate, not a modal) --}}
                  <td class="px-4 py-3 border border-[#E5E5E5]">
                    <a href="{{ route('employee.show', \Illuminate\Support\Facades\Crypt::encrypt($employee->id)) }}"
                       class="inline-flex items-center px-3 py-1 rounded-[6px] text-[13px] font-[600] bg-[#007C3810] text-[#007C38] border border-[#007C3820] hover:bg-[#007C3815]">
                      {{ \Auth::user()->employeeIdFormat($employee->employee_id) }}
                    </a>
                  </td>

                  <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">
                    <div class="flex items-center gap-3">
                      <div class="h-10 w-10 rounded-full bg-[#007C38] flex items-center justify-center text-white text-[12px] font-[700]">
                        {{ strtoupper(substr($employee->name,0,2)) }}
                      </div>
                      <div>
                        <div class="text-[14px] font-[600] text-[#111827]">{{ $employee->name }}</div>
                        @if(!empty($employee->employee_type))
                          <div class="text-[12px] text-[#6B7280] mt-1">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-[4px] text-[10px] font-[500]
                              @switch($employee->employee_type)
                                @case('permanent') bg-green-100 text-green-800 @break
                                @case('contract') bg-blue-100 text-blue-800 @break
                                @case('temporary') bg-yellow-100 text-yellow-800 @break
                                @case('fixed-permanent') bg-purple-100 text-purple-800 @break
                                @case('fixed-temporary') bg-purple-100 text-purple-800 @break
                                @default bg-gray-100 text-gray-800
                              @endswitch">
                              {{ ucwords(str_replace('-', ' ', $employee->employee_type)) }}
                            </span>
                          </div>
                        @endif
                      </div>
                    </div>
                  </td>

                  <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">
                    <div class="text-[14px]">{{ $employee->email }}</div>
                    @if(!empty($employee->phone))
                      <div class="text-[12px] text-[#6B7280] mt-1">{{ $employee->phone }}</div>
                    @endif
                  </td>

                  <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">
                    <div class="text-[14px]">{{ optional($employee->branch)->name ?? '—' }}</div>
                    <div class="text-[12px] text-[#6B7280] mt-1">{{ optional($employee->department)->name ?? '—' }}</div>
                  </td>

                  <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">
                    {{ optional($employee->designation)->name ?? '—' }}
                  </td>

                  <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">
                    {{ $employee->company_doj ? \Auth::user()->dateFormat($employee->company_doj) : '—' }}
                  </td>

                  @if(Gate::check('edit employee') || Gate::check('delete employee') || Gate::check('manage employee'))
                    <td class="Action px-4 py-3 border border-[#E5E5E5] relative text-gray-700">
                      <button class="absolute top-3 right-3 text-gray-400 hover:text-gray-600 cursor-pointer"
                              type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="ti ti-dots-vertical"></i>
                      </button>

                      <div class="dropdown-menu dropdown-menu-end mt-0 w-[190px] bg-white border rounded-md shadow-lg text-sm p-0">
                        {{-- View (go to page) --}}
                        <a href="{{ route('employee.show', \Illuminate\Support\Facades\Crypt::encrypt($employee->id)) }}"
                           class="dropdown-item flex items-center text-gray-700 gap-2 w-full px-4 py-2 text-left hover:bg-[#007C3812]">
                          <i class="ti ti-eye"></i><span>{{ __('Show') }}</span>
                        </a>

                        @can('edit employee')
                          <a href="{{ route('employee.edit', \Illuminate\Support\Facades\Crypt::encrypt($employee->id)) }}"
                             class="dropdown-item flex items-center text-gray-700 gap-2 w-full px-4 py-2 text-left hover:bg-[#007C3812]">
                            <i class="ti ti-pencil"></i><span>{{ __('Edit') }}</span>
                          </a>
                        @endcan

                        @can('delete employee')
                          {!! Form::open(['method' => 'DELETE', 'route' => ['employee.destroy', $employee->id], 'id' => 'delete-form-' . $employee->id]) !!}
                          <a href="#"
                             class="dropdown-item flex items-center text-gray-700 gap-2 w-full px-4 py-2 text-left hover:bg-[#007C3812] bs-pass-para"
                             data-confirm="{{ __('Are You Sure?') . '|' . __('This action can not be undone. Do you want to continue?') }}"
                             data-confirm-yes="document.getElementById('delete-form-{{ $employee->id }}').submit();">
                            <i class="ti ti-trash"></i><span>{{ __('Delete') }}</span>
                          </a>
                          {!! Form::close() !!}
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
(function(){
  // stop bubbling from controls
  $(document).on('click','input[type=checkbox], label.mcheck, .dropdown-menu, [data-bs-toggle="dropdown"]',function(e){e.stopPropagation();});

  // --- Search panel toggle (reliable + persisted) ---
  const panel   = $('#search-panel');
  const toggle  = $('#toggle-search');
  const labelEl = $('#toggle-search .js-label');
  const STORE   = 'ui:employees:filtersOpen';

  function setOpen(isOpen, animate=true){
    toggle.attr('aria-expanded', isOpen ? 'true' : 'false');
    labelEl.text(isOpen ? '{{ __("Hide Filters") }}' : '{{ __("Search") }}');
    localStorage.setItem(STORE, isOpen ? '1' : '0');
    if(animate){
      isOpen ? panel.stop(true,true).slideDown(180) : panel.stop(true,true).slideUp(180);
    }else{
      panel.toggle(isOpen);
    }
  }

  // initial from localStorage
  setOpen(localStorage.getItem(STORE) === '1', /*animate*/false);

  toggle.on('click', function(){
    const isOpen = toggle.attr('aria-expanded') === 'true';
    setOpen(!isOpen);
  });

  // --- Filters (client-side) ---
  function applyFilters(){
    const kw   = ($('#f-keyword').val()||'').toLowerCase().trim();
    const br   = ($('#f-branch').val()||'').toLowerCase();
    const dp   = ($('#f-department').val()||'').toLowerCase();
    const dg   = ($('#f-designation').val()||'').toLowerCase();
    const tp   = ($('#f-type').val()||'').toLowerCase();
    const st   = ($('#f-status').val()||'').toLowerCase();
    const dfrom= $('#f-doj-from').val() ? new Date($('#f-doj-from').val()) : null;
    const dto  = $('#f-doj-to').val()   ? new Date($('#f-doj-to').val())   : null;

    $('tr.employee-row').each(function(){
      const $r = $(this);
      const name  = String($r.data('name')||'');
      const email = String($r.data('email')||'');
      const idfmt = String($r.data('idfmt')||'');
      const phone = String($r.data('phone')||'');
      const rbr   = String($r.data('branch')||'');
      const rdp   = String($r.data('dept')||'');
      const rdg   = String($r.data('desig')||'');
      const rtp   = String($r.data('type')||'');
      const rst   = String($r.data('status')||'');
      const doj   = $r.data('doj') ? new Date($r.data('doj')) : null;

      const sOk = !kw || name.includes(kw) || email.includes(kw) || idfmt.includes(kw) || phone.includes(kw) || rdp.includes(kw) || rdg.includes(kw) || rbr.includes(kw);
      const brOk= !br || rbr===br;
      const dpOk= !dp || rdp===dp;
      const dgOk= !dg || rdg===dg;
      const tpOk= !tp || rtp===tp;
      const stOk= !st || rst===st;

      let dateOk = true;
      if(dfrom && doj){ dateOk = dateOk && (doj >= dfrom); }
      if(dto   && doj){ dateOk = dateOk && (doj <= dto);   }

      $r.toggle(sOk && brOk && dpOk && dgOk && tpOk && stOk && dateOk);
    });
  }

  $('#filters-apply').on('click', applyFilters);
  $('#filters-clear').on('click', function(){
    $('#f-keyword').val(''); $('#f-branch').val(''); $('#f-department').val('');
    $('#f-designation').val(''); $('#f-type').val(''); $('#f-status').val('');
    $('#f-doj-from').val(''); $('#f-doj-to').val('');
    applyFilters();
  });

  // initial pass
  applyFilters();
})();
</script>
@endpush
