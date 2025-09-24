@extends('layouts.admin')

@section('page-title')
  {{ __('Manage Product Stock') }}
@endsection

@section('breadcrumb')
  <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
  <li class="breadcrumb-item">{{ __('Product Stock') }}</li>
@endsection

@section('content')

  {{-- Material checkbox styles (same design used elsewhere) --}}
  <style>
    .low-stock { background-color: #ffebee; }
    .mcheck{display:inline-flex;align-items:center;cursor:pointer;user-select:none}
    .mcheck input{position:absolute;opacity:0;width:0;height:0}
    .mcheck .box{width:20px;height:20px;border:2px solid #dee2e6;border-radius:4px;position:relative;}
    .mcheck .box:hover{box-shadow:0 1px 3px rgba(0,0,0,.08)}
    .mcheck input:focus + .box{box-shadow:0 0 0 3px rgba(0,124,56,.2)}
    .mcheck input:checked + .box{background:#007C38;border-color:#007C38;}
    .mcheck input:checked + .box::after{content:"";position:absolute;left:6px;top:2px;width:5px;height:10px;border:2px solid white;border-top:0;border-left:0;transform:rotate(45deg);}
  </style>

  {{-- Hidden form for non-AJAX export (so file downloads cleanly) --}}
  <form id="stock-export-form" action="{{ route('productstock.export-selected') }}" method="POST" style="display:none;">
    @csrf
    <div id="stock-export-holder"></div>
  </form>

  {{-- Bulk actions bar (shown only when something is selected) --}}
  <div id="bulk-actions-bar" class="card border-0 shadow-sm rounded-[8px] mb-4 overflow-hidden mt-4" style="display:none">
    <div class="card-body p-4 bg-[#F8FAFC]">
      <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div class="flex flex-wrap items-center gap-4">
          <div class="flex items-center gap-2">
            <svg class="w-5 h-5 text-[#007C38]" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"></path>
            </svg>
            <span class="text-[14px] font-[600] text-[#374151]">
              <span id="selected-count">0</span> {{ __('Products selected') }}
            </span>
          </div>
          <div class="flex gap-2">
            <button type="button" id="select-all-btn"
                    class="text-[14px] font-[500] text-[#007C38] hover:text-[#005f2a] transition-colors duration-200">
              {{ __('Select All') }}
            </button>
            <button type="button" id="deselect-all-btn"
                    class="text-[14px] font-[500] text-[#6B7280] hover:text-[#374151] transition-colors duration-200">
              {{ __('Deselect All') }}
            </button>
          </div>
        </div>

        <div class="flex flex-wrap items-center gap-2 w-full sm:w-auto">
          {{-- Export Selected ONLY (no delete on this page) --}}
          <button type="button" id="bulk-export-btn"
                  class="inline-flex items-center gap-2 px-3 py-2 border border-[#E5E7EB] text-[#374151] bg-white hover:bg-[#F9FAFB] rounded-[6px] text-[14px] font-[500] transition-all duration-200">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 011 1h5.586a1 1 0 01.707.293L18.707 10a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <span class="hidden sm:inline">{{ __('Export Selected') }}</span>
            <span class="sm:hidden">{{ __('Export') }}</span>
          </button>
        </div>
      </div>
    </div>
  </div>

  <div class="row table-new-design border-0 rounded-2xl shadow-md overflow-hidden my-3">
    <div class="h-1 w-full" style="background:#007C38;"></div>

    <div class="col-xl-12">
      <div class="bg-white rounded-[8px] p-3">
        <div class="table-responsive table-new-design">
          <table id="stock-table" class="table datatable min-w-full text-sm text-left border rounded-lg overflow-x-auto">
            <thead class="bg-[#F6F6F6]">
              <tr role="row">
                {{-- master checkbox: non-sortable for simple-datatables --}}
                <th data-sortable="false" data-type="html"
                    class="input-checkbox border border-[#E5E5E5] px-4 lg:px-6 py-4 text-left text-[12px] font-[700] text-[#374151] uppercase tracking-wider w-12">
                  <label class="mcheck">
                    <input type="checkbox" class="jsb-master" data-scope="stock">
                    <span class="box"></span>
                  </label>
                </th>
                <th class="px-4 py-1 border border-[#E5E5E5] font-[600]">{{ __('Name') }}</th>
                <th class="px-4 py-1 border border-[#E5E5E5] font-[600]">{{ __('Sku') }}</th>
                <th class="px-4 py-1 border border-[#E5E5E5] font-[600]">{{ __('Type') }}</th>
                <th class="px-4 py-1 border border-[#E5E5E5] font-[600]">{{ __('Material Type') }}</th>
                <th class="px-4 py-1 border border-[#E5E5E5] font-[600]">{{ __('Current Quantity') }}</th>
                <th class="px-4 py-1 border border-[#E5E5E5] font-[600]">{{ __('Reorder Level') }}</th>
                <th class="px-4 py-1 border border-[#E5E5E5] font-[600]">{{ __('Created By') }}</th>
                <th class="px-4 py-1 border border-[#E5E5E5] font-[600]">{{ __('Created At') }}</th>
                <th class="px-4 py-1 border border-[#E5E5E5] font-[600]">{{ __('Updated At') }}</th>
                <th class="px-4 py-1 border border-[#E5E5E5] font-[600]">{{ __('Action') }}</th>
              </tr>
            </thead>
            <tbody class="border border-[#E5E5E5]">
              @foreach ($productServices as $productService)
                  @php
    $isLow = $productService->type === 'Product'
        && !is_null($productService->reorder_level)
        && (int)$productService->quantity < (int)$productService->reorder_level;
@endphp
<tr class="border-b hover:bg-gray-50 {{ $isLow ? 'low-stock' : '' }}">
                  <td class="input-checkbox px-4 lg:px-6 py-4 text-left text-[12px] font-[700] text-[#374151] uppercase tracking-wider border-0 w-12">
                    <label class="mcheck">
                      <input type="checkbox"
                             class="jsb-item"
                             data-scope="stock"
                             value="{{ $productService->id }}"
                             data-id="{{ $productService->id }}">
                      <span class="box"></span>
                    </label>
                  </td>
                  <td class="px-4 py-3 border border-[#E5E5E5] text-[#323232]">
                    {{ $productService->name }}
                  </td>
                  <td class="px-4 py-3 border border-[#E5E5E5] text-[#323232]">
                    {{ $productService->sku }}
                  </td>
                  <td class="px-4 py-3 border border-[#E5E5E5] text-[#323232]">
                    <span class="me-5 badge p-2 px-3 text-capitalize fix_badge {{ $productService->type === 'Product' ? 'bg-primary' : 'bg-success' }}">
                      {{ $productService->type }}
                    </span>
                  </td>
                  <td class="px-4 py-3 border border-[#E5E5E5] text-[#323232]">
                    {{ $productService->material_type }}
                  </td>
                  <td class="px-4 py-3 border border-[#E5E5E5] text-[#323232]">
                    {{ $productService->quantity }}
                  </td>
                  <td class="px-4 py-3 border border-[#E5E5E5] text-[#323232]">
                    {{ $productService->reorder_level }}
                  </td>
                  <td class="px-4 py-3 border border-[#E5E5E5] text-[#323232]">
                    {{ $productService->createdBy ? $productService->createdBy->name : __('Unknown User') }}
                  </td>
                  <td class="px-4 py-3 border border-[#E5E5E5] text-[#323232]">
                    {{ \Carbon\Carbon::parse($productService->created_at)->format('d M Y, h:i A') }}
                  </td>
                  <td class="px-4 py-3 border border-[#E5E5E5] text-[#323232]">
                    {{ \Carbon\Carbon::parse($productService->updated_at)->format('d M Y, h:i A') }}
                  </td>
                  <td class="px-4 py-3 border border-[#E5E5E5] text-[#323232] Action">
                    <button class="text-gray-400 hover:text-gray-600 cursor-pointer"
                            type="button" data-bs-toggle="dropdown" aria-haspopup="true"
                            aria-expanded="false">
                      <i class="ti ti-dots-vertical"></i>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end mt-0 w-[190px] bg-white border rounded-md shadow-lg text-sm p-0">
                      <li>
                        <a data-size="md" href="#"
                           class="dropdown-item flex items-center text-[#323232] gap-2 w-full px-4 py-2 text-left hover:bg-[#007C3812] text-sm"
                           data-url="{{ route('productstock.edit', $productService->id) }}"
                           data-ajax-popup="true" data-size="xl" data-bs-toggle="tooltip"
                           title="{{ __('Update Quantity') }}">
                          <img src="{{ asset('web-assets/dashboard/icons/action_icons/edit.svg') }}" alt="edit" />
                          <span>{{ __('Update Quantity') }}</span>
                        </a>
                      </li>
                    </div>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div> {{-- .table-responsive --}}
      </div>
    </div>
  </div>
@endsection

@push('script-page')
<script>
(function(){
  const scope = 'stock';
  const STORAGE_KEY = 'bulk:' + scope;
  const $table  = $('#stock-table');
  const $bar    = $('#bulk-actions-bar');
  const $count  = $('#selected-count');

  const getSel = () => { try { return Array.from(new Set(JSON.parse(localStorage.getItem(STORAGE_KEY) || '[]'))); } catch { return []; } };
  const saveSel = (arr) => localStorage.setItem(STORAGE_KEY, JSON.stringify(Array.from(new Set(arr))));
  const addSel = (ids)=>{ const s=new Set(getSel()); ids.forEach(i=>s.add(String(i))); saveSel(Array.from(s)); };
  const delSel = (ids)=>{ const s=new Set(getSel()); ids.forEach(i=>s.delete(String(i))); saveSel(Array.from(s)); };
  const clrSel = ()=>{ saveSel([]); };

  function updateBar(){
    const n = getSel().length;
    $count.text(n);
    $bar.toggle(n > 0);
  }

  function refresh(){
    const selected = new Set(getSel());
    $table.find('tbody input.jsb-item[data-scope="'+scope+'"]').each(function(){
      const id = String($(this).data('id') || $(this).val());
      $(this).prop('checked', selected.has(id));
    });

    const $rows   = $table.find('tbody input.jsb-item[data-scope="'+scope+'"]');
    const $master = $table.find('thead input.jsb-master[data-scope="'+scope+'"]');
    const total   = $rows.length;
    const checked = $rows.filter(function(){ return $(this).prop('checked'); }).length;
    const allSel  = checked > 0 && checked === total;
    $master.prop('checked', allSel);
    $master.prop('indeterminate', !allSel && checked > 0);

    updateBar();
  }

  $(document).on('click', 'input[type=checkbox], label.mcheck', function(e){ e.stopPropagation(); });

  $(document).on('change', 'input.jsb-item[data-scope="'+scope+'"]', function(){
    const id = String($(this).data('id') || $(this).val());
    if ($(this).is(':checked')) addSel([id]); else delSel([id]);
    refresh();
  });

  $(document).on('change', 'input.jsb-master[data-scope="'+scope+'"]', function(){
    const $rows = $table.find('tbody input.jsb-item[data-scope="'+scope+'"]');
    const ids = $rows.map(function(){ return String($(this).data('id') || $(this).val()); }).get();
    if ($(this).is(':checked')) { addSel(ids); $rows.prop('checked', true); }
    else { delSel(ids); $rows.prop('checked', false); }
    refresh();
  });

  $('#select-all-btn').on('click', function(){
    const $rows = $table.find('tbody input.jsb-item[data-scope="'+scope+'"]');
    const ids = $rows.map(function(){ return String($(this).data('id') || $(this).val()); }).get();
    addSel(ids); refresh();
  });

  $('#deselect-all-btn').on('click', function(){
    clrSel(); refresh();
  });

  $('#bulk-export-btn').on('click', function(){
    const ids = getSel();
    if (!ids.length) {
      if (typeof Swal !== 'undefined') {
        Swal.fire({ icon:'info', title:'{{ __("No selection") }}', text:'{{ __("Please select at least one row.") }}' });
      } else {
        alert('{{ __("Please select at least one row.") }}');
      }
      return;
    }
    const $holder = $('#stock-export-holder');
    $holder.empty();
    ids.forEach(function(id){
      $holder.append($('<input>', { type:'hidden', name:'ids[]', value: id }));
    });
    clrSel();
    $('#stock-export-form').trigger('submit');
  });

  try {
    $table.on('datatable.page datatable.search datatable.sort', function(){ refresh(); });
  } catch(e) {}

  refresh();
})();
</script>
@endpush
