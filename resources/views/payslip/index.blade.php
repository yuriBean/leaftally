@extends('layouts.admin')

@section('page-title')
  {{ __('Payslip') }}
@endsection

@section('breadcrumb')
  <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
  <li class="breadcrumb-item">{{ __('Payslip') }}</li>
  <style>
    .mcheck{display:inline-flex;align-items:center;cursor:pointer;user-select:none}
    .mcheck input{position:absolute;opacity:0;width:0;height:0}
    .mcheck .box{width:20px;height:20px;border:2px solid #dee2e6;border-radius:4px;position:relative;}
    .mcheck .box:hover{box-shadow:0 1px 3px rgba(0,0,0,.08)}
    .mcheck input:focus + .box{box-shadow:0 0 0 3px rgba(0,124,56,.2)}
    .mcheck input:checked + .box{background:#007C38;border-color:#007C38;}
    .mcheck input:checked + .box::after{content:"";position:absolute;left:6px;top:2px;width:5px;height:10px;border:2px solid white;border-top:0;border-left:0;transform:rotate(45deg);}

    .is-hidden{display:none !important;}
    .opacity-50{opacity:.5}
    .cursor-not-allowed{cursor:not-allowed}
  </style>
@endsection

@section('content')
<div class="col-sm-12 col-lg-12 col-xl-12 col-md-12 mt-4">
  <div class="card border-0 rounded-2xl shadow-md overflow-hidden my-3">
    <div class="h-1 w-full" style="background:#007C38;"></div>
  <div class="card-body">
      {{ Form::open(['route' => ['payslip.store'], 'method' => 'POST', 'id' => 'payslip_form']) }}
      <div class="d-flex flex-wrap items-end justify-content-starts gap-3">
        <div class="col-md-3 col-lg-3 col-md-3">
          <div class="btn-box">
            {{ Form::label('month', __('Select Month'), ['class' => 'text-type block text-sm font-medium text-gray-700 mb-2']) }}
            {{ Form::select('month', $month, date('m'), ['class' => 'form-control appearance-none bg-white border border-[#E5E7EB] rounded-[6px] px-3 py-2 pr-8 text-[14px] text-[#374151] focus:outline-none focus:ring-1 focus:ring-[#007C38] focus:border-[#007C38] duration-200 w-full select', 'id' => 'month']) }}
          </div>
        </div>
        <div class="col-md-3 col-lg-3 col-md-3">
          <div class="btn-box">
            {{ Form::label('year', __('Select Year'), ['class' => 'text-type block text-sm font-medium text-gray-700 mb-2']) }}
            {{ Form::select('year', $year, date('Y'), ['class' => 'form-control appearance-none bg-white border border-[#E5E7EB] rounded-[6px] px-3 py-2 pr-8 text-[14px] text-[#374151] focus:outline-none focus:ring-1 focus:ring-[#007C38] focus:border-[#007C38] duration-200 w-full select']) }}
          </div>
        </div>
        <div class="col-auto">
          <a href="#" class="flex items-center gap-2 bg-[#007C38] text-white px-4 py-2 rounded-[6px] text-[14px] font-[500] hover:bg-[#005f2a] transition-all duration-200 shadow-sm"
             onclick="document.getElementById('payslip_form').submit(); return false;"
             data-bs-toggle="tooltip" title="{{ __('Generate Payslip') }}">
             <i class="ti ti-report-money"></i> {{ __('Generate Payslip') }}
          </a>
        </div>
      </div>
      {{ Form::close() }}
    </div>
  </div>
</div>

<div class="col-12">
  <div class="card border-0 rounded-2xl shadow-md overflow-hidden my-3">
    <div class="h-1 w-full" style="background:#007C38;"></div>
  <div class="card-header">
      <div class="row w-100">
        <div class="col-md-4">
          <div class="d-flex align-items-center justify-content-start mt-2">
            <h5 class="mb-0">{{ __('Find Employee Payslip') }}</h5>
          </div>
        </div>
        <div class="col-md-8">
          <div class="d-flex flex-wrap items-center justify-content-end gap-2">
            <div class="col-xl-2 col-lg-3 col-md-4">
              <div class="btn-box">
                <select class="form-control month_date appearance-none bg-white border border-[#E5E7EB] rounded-[6px] px-3 py-2 pr-8 text-[14px] text-[#374151] focus:outline-none focus:ring-1 focus:ring-[#007C38] focus:border-[#007C38] duration-200 w-full" name="month">
                  <option value="--">--</option>
                  @foreach ($month as $k => $mon)
                    <option value="{{ $k }}" {{ date('m') == $k ? 'selected' : '' }}>{{ $mon }}</option>
                  @endforeach
                </select>
              </div>
            </div>

            <div class="col-xl-2 col-lg-3 col-md-4">
              <div class="btn-box">
                {{ Form::select('year', $year, date('Y'), ['class' => 'form-control year_date appearance-none bg-white border border-[#E5E7EB] rounded-[6px] px-3 py-2 pr-8 text-[14px] text-[#374151] focus:outline-none focus:ring-1 focus:ring-[#007C38] focus:border-[#007C38] duration-200 w-full']) }}
              </div>
            </div>

            <div class="col-xl-3 col-lg-4 col-md-5">
              <input id="payslip_search" type="search"
                     class="form-control appearance-none bg-white border border-[#E5E7EB] rounded-[6px] px-3 py-2 text-[14px]"
                     placeholder="{{ __('Search payslips…') }}">
            </div>

            <div class="col-auto">
              {{ Form::open(['route' => ['payslip.export'], 'method' => 'POST', 'id' => 'payslip_export_form']) }}
              <input type="hidden" name="filter_month" class="filter_month">
              <input type="hidden" name="filter_year"  class="filter_year">
              <button type="submit" id="export_selected_btn" disabled
                class="flex items-center gap-2 bg-white text-[#007C38] border border-[#007C38] px-4 py-2 rounded-[6px] text-[14px] font-[500] transition-all duration-200 shadow-sm opacity-50 cursor-not-allowed">
                <i class="ti ti-file-export"></i> {{ __('Export') }}
              </button>
              {{ Form::close() }}
            </div>

            @can('create pay slip')
            <div class="col-auto me-0">
              <button id="bulk_payment" disabled
                class="flex items-center gap-2 bg-[#007C38] text-white px-4 py-2 rounded-[6px] text-[14px] font-[500] transition-all duration-200 shadow-sm opacity-50 cursor-not-allowed">
                <i class="ti ti-currency-dollar"></i> {{ __('Bulk Payment') }}
              </button>
            </div>
            @endcan
          </div>
        </div>
      </div>
    </div>

    <div class="card-body table-border-style">
      <div class="table-responsive table-new-design bg-white p-4">
          <table class="w-full border-collapse text-sm" id="pc-dt-render-column-cells">
            <thead class="bg-[#F6F6F6] text-[#374151] text-[12px] font-[600] uppercase tracking-wider">
              <tr>
                <th class="w-12 px-4 py-3 border border-[#E5E5E5]">
                  <label class="flex items-center justify-center">
                    <input type="checkbox" class="jsb-master hidden" data-scope="payslips">
                    <span class="w-4 h-4 border border-gray-400 rounded-sm block"></span>
                  </label>
                </th>
                <th class="px-4 py-3 border border-[#E5E5E5] text-left">{{ __('Employee Id') }}</th>
                <th class="px-4 py-3 border border-[#E5E5E5] text-left">{{ __('Name') }}</th>
                <th class="px-4 py-3 border border-[#E5E5E5] text-left">{{ __('Payroll Type') }}</th>
                <th class="px-4 py-3 border border-[#E5E5E5] text-right">{{ __('Salary') }}</th>
                <th class="px-4 py-3 border border-[#E5E5E5] text-right">{{ __('Net Salary') }}</th>
                <th class="px-4 py-3 border border-[#E5E5E5] text-center">{{ __('Status') }}</th>
                <th class="px-4 py-3 border border-[#E5E5E5] text-center w-[10%]">{{ __('Action') }}</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-[#E5E5E5]"></tbody>
            <tfoot>
              <tr id="ytd-row" class="bg-[#F9FAFB] font-[600]">
                <td class="px-4 py-3"></td>
                <td colspan="3" class="px-4 py-3 text-right">
                  {{ __('YTD (') }}<span id="ytd-label">—</span>)
                </td>
                <td class="px-4 py-3 text-right" id="ytd-basic">—</td>
                <td class="px-4 py-3 text-right" id="ytd-net">—</td>
                <td class="px-4 py-3 text-center" id="ytd-status">—</td>
                <td class="px-4 py-3 text-center">—</td>
              </tr>
              <tr id="no-results-row" class="is-hidden">
                <td colspan="8" class="text-center text-gray-500 py-4">{{ __('No matching records') }}</td>
              </tr>
            </tfoot>
          </table>
        
      </div>
    </div>

  </div>
</div>
@endsection

@push('script-page')
<script>
$(document).ready(function () {
  window.payslipSelected = window.payslipSelected || new Set();

  function setButtonsEnabled(enabled){
    const $export = $('#export_selected_btn');
    const $bulk   = $('#bulk_payment');
    $export.prop('disabled', !enabled).toggleClass('opacity-50 cursor-not-allowed', !enabled);
    $bulk.prop('disabled', !enabled).toggleClass('opacity-50 cursor-not-allowed', !enabled);
  }

  function updateMasterState() {
    const $master = $('.jsb-master[data-scope="payslips"]');
    const $items  = $('input.jsb-item[data-scope="payslips"]:enabled:visible');
    if (!$items.length) { 
      $master.prop({checked:false, indeterminate:false}); 
      setButtonsEnabled(window.payslipSelected.size > 0);
      return; 
    }
    const total   = $items.length;
    const checked = $('input.jsb-item[data-scope="payslips"]:enabled:visible:checked').length;
    $master.prop('checked', checked === total);
    $master.prop('indeterminate', checked > 0 && checked < total);
    setButtonsEnabled(window.payslipSelected.size > 0);
  }

  function applyFilter(q){
    var query = (q || '').toLowerCase().trim();
    var $rows = $('#pc-dt-render-column-cells tbody tr.js-row');

    if (!query) {
      $rows.removeClass('is-hidden');
    } else {
      $rows.each(function(){
        var hay = (this.getAttribute('data-search') || '').toLowerCase();
        if (hay.indexOf(query) > -1) $(this).removeClass('is-hidden');
        else $(this).addClass('is-hidden');
      });
    }

    var visibleCount = $('#pc-dt-render-column-cells tbody tr.js-row:not(.is-hidden)').length;
    $('#no-results-row').toggleClass('is-hidden', visibleCount > 0);

    updateMasterState();
  }

  function debounce(fn, wait){ let t; return function(){ clearTimeout(t); t = setTimeout(()=>fn.apply(this, arguments), wait); }; }

  function renderRows(data, datePicker){
    const $tbody = $('#pc-dt-render-column-cells tbody');
    $tbody.empty();

    if (!Array.isArray(data) || !data.length) {
      $('#no-results-row').removeClass('is-hidden').find('td').text('{{ __("No records found for") }} ' + datePicker);
      loadYTD(datePicker);
      updateMasterState();
      return;
    } else {
      $('#no-results-row').addClass('is-hidden');
    }

    data.forEach(function(item){
      var employeeId = item[0];
      var empCode     = item[1];
      var name        = item[2];
      var payroll     = item[3];
      var salary      = item[4];
      var netSalary   = item[5];
      var statusText  = item[6];
      var payslipId   = item[7];
      var urlEmployee = item['url'];

      var statusHtml  = (statusText === 'Paid')
        ? '<span class="badge fix_badges bg-success p-2 px-3">Paid</span>'
        : '<span class="badge fix_badges bg-danger p-2 px-3">UnPaid</span>';

      var actions = ''
        + '<button class="w-100 text-gray-400 hover:text-gray-600 cursor-pointer" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">'
        +   '<i class="ti ti-dots-vertical"></i>'
        + '</button>'
        + '<div class="dropdown-menu dropdown-menu-end mt-0 w-[220px] bg-white border rounded-md shadow-lg text-sm p-0">';

      if (payslipId != 0) {
        actions += '<li><a href="#" data-url="{{ url('payslip/pdf/') }}/'+ employeeId +'/'+ datePicker +'" data-size="lg" data-ajax-popup="true" class="dropdown-item flex items-center gap-2 w-full px-4 py-2 text-left hover:bg-[#007C3812] text-sm" data-title="{{ __('Employee Payslip') }}">'
                 +   '<i class="ti ti-report-money"></i><span>{{ __('Payslip') }}</span></a></li>';
      }

      if (statusText === "UnPaid" && payslipId != 0) {
        actions += '<li><a href="{{ url('payslip/paysalary/') }}/'+ employeeId +'/'+ datePicker +'" class="dropdown-item flex items-center gap-2 w-full px-4 py-2 text-left hover:bg-[#007C3812] text-sm">'
                 +   '<i class="ti ti-currency-dollar"></i><span>{{ __('Click To Paid') }}</span></a></li>';
      }

      if (payslipId != 0) {
        actions += '<li><a href="#" data-url="{{ url('payslip/showemployee/') }}/'+ payslipId +'" data-ajax-popup="true" class="dropdown-item flex items-center gap-2 w-full px-4 py-2 text-left hover:bg-[#007C3812] text-sm" data-title="{{ __('View Employee Detail') }}">'
                 +   '<i class="ti ti-eye"></i><span>{{ __('View') }}</span></a></li>';
      }

      if (payslipId != 0 && statusText === "UnPaid") {
        actions += '<li><a href="#" data-url="{{ url('payslip/editemployee/') }}/'+ payslipId +'" data-ajax-popup="true" class="dropdown-item flex items-center gap-2 w-full px-4 py-2 text-left hover:bg-[#007C3812] text-sm" data-title="{{ __('Edit Employee salary') }}">'
                 +   '<i class="ti ti-pencil"></i><span>{{ __('Edit') }}</span></a></li>';
      }

      @if (\Auth::user()->type != 'Employee')
        if (payslipId != 0) {
          var delUrl = '{{ route('payslip.delete', ':id') }}'.replace(':id', payslipId);
          actions += '<li><a href="#" data-url="'+ delUrl +'" class="payslip_delete dropdown-item flex items-center gap-2 w-full px-4 py-2 text-left hover:bg-[#007C3812] text-sm">'
                   +   '<i class="ti ti-trash"></i><span>{{ __('Delete') }}</span></a></li>';
        }
      @endif

      actions += '</div>';

      var checked  = (payslipId && window.payslipSelected.has(String(payslipId))) ? 'checked' : '';
      var disabled = (payslipId == 0) ? 'disabled' : '';

      var searchKey = (empCode + ' ' + name + ' ' + payroll + ' ' + (salary||'') + ' ' + (netSalary||'') + ' ' + statusText).toLowerCase();

      var rowHtml = ''
        + '<tr class="js-row" data-search="'+ $('<div>').text(searchKey).html() +'">'
        +   '<td class="input-checkbox px-4 py-3 border border-[#E5E5E5] text-gray-700 w-12">'
        +     '<label class="mcheck">'
        +       '<input type="checkbox" class="jsb-item" data-scope="payslips" value="'+ payslipId +'" '+ checked +' '+ disabled +'>'
        +       '<span class="box"></span>'
        +     '</label>'
        +   '</td>'
        +   '<td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">'
        +     '<a class="border border-[#137051] leading-[24px] text-[#137051] rounded-[4px] text-[12px] font-[500] px-5" href="'+ urlEmployee +'">'+ empCode +'</a>'
        +   '</td>'
        +   '<td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">'+ name +'</td>'
        +   '<td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">'+ payroll +'</td>'
        +   '<td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">'+ (salary || '') +'</td>'
        +   '<td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">'+ (netSalary || '') +'</td>'
        +   '<td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">'+ statusHtml +'</td>'
        +   '<td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">'+ actions +'</td>'
        + '</tr>';

      $('#pc-dt-render-column-cells tbody').append(rowHtml);
    });

    applyFilter($('#payslip_search').val() || '');
    updateMasterState();

    loadYTD(datePicker);
  }

  function loadData(){
    var month = $(".month_date").val();
    var year  = $(".year_date").val();

    $('.filter_month').val(month);
    $('.filter_year').val(year);

    if(!month || month === '--'){
      month = '{{ date('m', strtotime('last month')) }}';
      year  = '{{ date('Y') }}';
      $('.filter_month').val(month);
      $('.filter_year').val(year);
    }

    var datePicker = year + '-' + month;

    $.ajax({
      url: '{{ route('payslip.search_json') }}',
      type: 'POST',
      data: { "datePicker": datePicker, "_token": "{{ csrf_token() }}" },
      success: function(data) {
        renderRows(data, datePicker);
      }
    });
  }

  function loadYTD(datePicker){
    $.ajax({
      url: '{{ route('payslip.ytd') }}',
      type: 'POST',
      data: { "datePicker": datePicker, "_token": "{{ csrf_token() }}" },
      success: function(r){
        $('#ytd-label').text(r.label || datePicker);
        $('#ytd-basic').text(r.total_basic_formatted || '—');
        $('#ytd-net').text(r.total_net_formatted || '—');
        $('#ytd-status').html(
          (r.paid_count ?? 0) + ' {{ __("Paid") }} / ' + (r.unpaid_count ?? 0) + ' {{ __("Unpaid") }}'
        );
      },
      error: function(){
        $('#ytd-label').text('—');
        $('#ytd-basic').text('—');
        $('#ytd-net').text('—');
        $('#ytd-status').text('—');
      }
    });
  }

  loadData();

  $('#payslip_search').on('input', debounce(function(){
    applyFilter(this.value);
  }, 120));

  $(document).on("change", ".month_date,.year_date", function() {
    $('.jsb-master[data-scope="payslips"]').prop({checked:false, indeterminate:false});
    loadData();
  });

  $(document).on('click', 'input[type=checkbox], label.mcheck, .dropdown-menu, [data-bs-toggle="dropdown"]', function(e){
    e.stopPropagation();
  });

  $(document).on('change', '.jsb-master[data-scope="payslips"]', function() {
    const on = $(this).is(':checked');
    $('input.jsb-item[data-scope="payslips"]:enabled:visible').each(function(){
      const $cb = $(this);
      $cb.prop('checked', on).trigger('change.selectionOnly');
    });
    updateMasterState();
  });

  $(document).on('change.selectionOnly change', 'input.jsb-item[data-scope="payslips"]', function(e){
    const id = String($(this).val() || '');
    if (!id || $(this).is(':disabled')) return;
    if ($(this).is(':checked')) window.payslipSelected.add(id);
    else window.payslipSelected.delete(id);
    if (e.type !== 'change.selectionOnly') { updateMasterState(); }
  });

  $('#payslip_export_form').on('submit', function(e){
    $(this).find('input[name="ids[]"]').remove();
    const ids = Array.from(window.payslipSelected);
    if (!ids.length) { e.preventDefault(); return false; }
    ids.forEach(id => $(this).append($('<input>', {type:'hidden', name:'ids[]', value:id})));
  });

  $(document).on('click', '#bulk_payment', function () {
    const ids = Array.from(window.payslipSelected);
    if (!ids.length) return;

    var month = $(".month_date").val();
    var year  = $(".year_date").val();
    var datePicker = year + '-' + month;

    let url = 'payslip/bulk_pay_create/' + datePicker + '?' + $.param({ 'ids': ids });

    $("#commonModal .modal-title").html('{{ __('Bulk Payment') }}');
    $("#commonModal .modal-dialog").addClass('modal-md');
    $.ajax({
      url: url,
      success: function(html){
        if (html && html.length) {
          $('#commonModal .body').html(html);
          $("#commonModal").modal('show');
          const $form = $('#commonModal .body').find('form');
          ids.forEach(id => $form.append($('<input>',{type:'hidden', name:'ids[]', value:id})));
        } else {
          if (typeof show_toastr === 'function') show_toastr('error', '{{ __('Unable to load bulk payment dialog.') }}');
          $("#commonModal").modal('hide');
        }
      },
      error: function(resp){
        var d = resp.responseJSON || {};
        if (typeof show_toastr === 'function') show_toastr('error', d.error || 'Error');
        $("#commonModal").modal('hide');
      }
    });
  });

  $(document).on("click", ".payslip_delete", function(e) {
    e.preventDefault();
    var url = $(this).data('url');
    if(!url) return;
    if(!confirm("{{ __('Are you sure you want to delete this payslip?') }}")) return;

    $.ajax({
      type: "GET",
      url: url,
      dataType: "JSON",
      success: function(){
        if (typeof show_toastr === 'function') show_toastr('success', '{{ __('Payslip Deleted Successfully') }}', 'success');
        setTimeout(function(){ location.reload(); }, 800);
      }
    });
  });
});
</script>
@endpush
