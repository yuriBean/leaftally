@props([
    'deleteRoute' => null,
    'exportRoute' => null,
    'scope' => 'items',
    'tableId' => null,
    'selectedLabel' => 'selected',
])

@php
  $barId         = 'bulkbar-'.$scope;
  $countId       = 'bulkcount-'.$scope;
  $formId        = 'bulkform-'.$scope;
  $holderId      = 'bulkids-'.$scope;

  $exportFormId   = 'bulkexportform-'.$scope;
  $exportHolderId = 'bulkexportids-'.$scope;

  $tableSel      = $tableId ? '#'.$tableId : 'table';
  $storageKey    = 'bulk:'.$scope;
@endphp

{{-- Hidden DELETE form (non-AJAX) to get proper redirect + flash --}}
<form id="{{ $formId }}" action="{{ $deleteRoute }}" method="POST" style="display:none;">
  @csrf
  @method('DELETE')
  <div id="{{ $holderId }}"></div>
</form>

{{-- Hidden EXPORT form (opens new tab so page state remains intact) --}}
@if($exportRoute)
<form id="{{ $exportFormId }}" action="{{ $exportRoute }}" method="POST" target="_blank" style="display:none;">
  @csrf
  <div id="{{ $exportHolderId }}"></div>
</form>
@endif

<div id="{{ $barId }}" class="card border-0 shadow-sm rounded-[8px] mb-4 overflow-hidden" style="display:none">
  <div class="card-body p-4 bg-[#F8FAFC]">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
      <div class="flex flex-wrap items-center gap-4">
        <div class="flex items-center gap-2">
          <svg class="w-5 h-5 text-[#007C38]" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"></path>
          </svg>
          <span class="text-[14px] font-[600] text-[#374151]">
            <span id="{{ $countId }}">0</span> {{ $selectedLabel }}
          </span>
        </div>
        <div class="flex gap-2">
          <button type="button"
                  data-bulk-select-page
                  data-scope="{{ $scope }}"
                  class="text-[14px] font-[500] text-[#007C38] hover:text-[#005f2a] transition-colors duration-200">
            {{ __('Select All (this page)') }}
          </button>
          <button type="button"
                  data-bulk-clear
                  data-scope="{{ $scope }}"
                  class="text-[14px] font-[500] text-[#6B7280] hover:text-[#374151] transition-colors duration-200">
            {{ __('Deselect All') }}
          </button>
        </div>
      </div>

      <div class="flex flex-wrap items-center gap-2 w-full sm:w-auto">
        @if($exportRoute)
          <button type="button"
                  data-bulk-export
                  data-scope="{{ $scope }}"
                  class="inline-flex items-center gap-2 px-3 py-2 border border-[#E5E7EB] text-[#374151] bg-white hover:bg-[#F9FAFB] rounded-[6px] text-[14px] font-[500] transition-all duration-200">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
            </svg>
            <span class="hidden sm:inline">{{ __('Export Selected') }}</span>
            <span class="sm:hidden">{{ __('Export') }}</span>
          </button>
        @endif

        <button type="button"
                data-bulk-delete
                data-scope="{{ $scope }}"
                class="inline-flex items-center gap-2 px-3 py-2 bg-red-600 text-white hover:bg-red-700 rounded-[6px] text-[14px] font-[500] transition-all duration-200">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
          </svg>
          <span class="hidden sm:inline">{{ __('Delete Selected') }}</span>
          <span class="sm:hidden">{{ __('Delete') }}</span>
        </button>
      </div>
    </div>
  </div>
</div>

@push('script-page')
<script>
(function(){
  const scope          = @json($scope);
  const STORAGE_KEY    = 'bulk:' + scope;
  const $table         = $(@json($tableSel));
  const $bar           = $('#'+@json($barId));
  const $count         = $('#'+@json($countId));
  const $form          = $('#'+@json($formId));
  const $holder        = $('#'+@json($holderId));
  const $exportForm    = $('#'+@json($exportFormId));
  const $exportHolder  = $('#'+@json($exportHolderId));

  const getSel   = () => { try { return Array.from(new Set(JSON.parse(localStorage.getItem(STORAGE_KEY) || '[]'))); } catch { return []; } };
  const saveSel  = (arr) => localStorage.setItem(STORAGE_KEY, JSON.stringify(Array.from(new Set(arr))));
  const setCount = () => $count.text(getSel().length);
  const toggleBar= () => $bar.toggle(getSel().length > 0);

  function refresh() {
    const selected = new Set(getSel());
    $table.find('tbody input.jsb-item[data-scope="'+scope+'"]').each(function(){
      const id = String($(this).data('id') || $(this).val());
      $(this).prop('checked', selected.has(id));
    });
    const $master = $table.find('thead input.jsb-master[data-scope="'+scope+'"]');
    const $rows = $table.find('tbody input.jsb-item[data-scope="'+scope+'"]');
    if ($rows.length) {
      const checkedCt = $rows.filter(function(){ return $(this).prop('checked'); }).length;
      const allSel = checkedCt > 0 && checkedCt === $rows.length;
      $master.prop('checked', allSel);
      $master.prop('indeterminate', !allSel && checkedCt > 0);
    } else {
      $master.prop('checked', false).prop('indeterminate', false);
    }
    setCount(); toggleBar();
  }
  const addSel = (ids)=>{ const s=new Set(getSel()); ids.forEach(i=>s.add(String(i))); saveSel(Array.from(s)); setCount(); toggleBar(); };
  const delSel = (ids)=>{ const s=new Set(getSel()); ids.forEach(i=>s.delete(String(i))); saveSel(Array.from(s)); setCount(); toggleBar(); };
  const clrSel = ()=>{ saveSel([]); setCount(); toggleBar(); };

  $(document).on('click', 'input[type=checkbox], label.mcheck, .dropdown-menu, [data-bs-toggle="dropdown"]', function(e){ e.stopPropagation(); });

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

  $(document).on('click', '[data-bulk-select-page][data-scope="'+scope+'"]', function(){
    const $rows = $table.find('tbody input.jsb-item[data-scope="'+scope+'"]');
    const ids = $rows.map(function(){ return String($(this).data('id') || $(this).val()); }).get();
    addSel(ids); refresh();
  });

  $(document).on('click', '[data-bulk-clear][data-scope="'+scope+'"]', function(){
    clrSel(); refresh();
  });

  $(document).on('click', '[data-bulk-delete][data-scope="'+scope+'"]', function(){
    const ids = getSel();
    if (!ids.length) {
      Swal.fire({ icon:'info', title:'{{ __('No selection') }}', text:'{{ __('Please select at least one row.') }}' });
      return;
    }
    const swalWithBootstrapButtons = Swal.mixin({
      customClass: { confirmButton: 'btn btn-success', cancelButton: 'btn btn-danger' },
      buttonsStyling: false
    });
    swalWithBootstrapButtons.fire({
      title: '{{ __('Delete selected?') }}',
      text: "{{ __('This action can not be undone. Do you want to continue?') }}",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: '{{ __('Yes') }}',
      cancelButtonText: '{{ __('No') }}',
      reverseButtons: true
    }).then((result) => {
      if (result.isConfirmed) {
        $holder.empty();
        ids.forEach(function(id){
          $holder.append($('<input>', { type:'hidden', name:'ids[]', value: id }));
        });
        clrSel();
        $form.trigger('submit');
      }
    });
  });

  $(document).on('click', '[data-bulk-export][data-scope="'+scope+'"]', function(){
    const ids = getSel();
    if (!ids.length) {
      Swal.fire({ icon:'info', title:'{{ __('No selection') }}', text:'{{ __('Please select at least one row.') }}' });
      return;
    }
    if (!$exportForm.length) return;
    $exportHolder.empty();
    ids.forEach(id => $exportHolder.append($('<input>', {type:'hidden', name:'ids[]', value:id})));
    $exportForm.trigger('submit');
  });

  try { $table.on('draw.dt', function(){ refresh(); }); } catch(e) {}

  refresh();
})();
</script>
@endpush
