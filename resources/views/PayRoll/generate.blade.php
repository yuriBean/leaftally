@extends('layouts.admin')
@php
$profile = asset(Storage::url('uploads/avatar/'));
@endphp

@section('page-title')
{{ __('Set Salary') }}
@endsection
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
<li class="breadcrumb-item">{{ __('Generate Payroll') }}</li>
@endsection

@section('action-btn')
<div class="flex items-center gap-2 mt-2 sm:mt-0">
    <a href="{{ route('active.payroll.export') }}" data-bs-toggle="tooltip" title="{{ __('Export') }}"
        style="border: 1px solid #007C38 !important"
        class="flex items-center gap-2 border border-[#007C38] text-[#007C38] bg-white px-4 py-2 rounded-[6px] text-[14px] font-[500] hover:bg-[#007C3808] transition-all duration-200 shadow-sm min-w-fit">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 712-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
        {{ __('Export') }}
    </a>

            <form id="import-form" action="{{ route('employees.file.import') }}" method="POST" enctype="multipart/form-data" class="hidden">
                @csrf
                <input type="file" id="import-file" name="import_file" accept=".csv,.xlsx,.xls" class="hidden">
            </form>

            <button type="button"
                id="import-btn"
                data-bs-toggle="tooltip"
                title="{{ __('Import Employees') }}"
                class="flex items-center gap-2 border border-[#007C38] text-[#007C38] bg-white px-4 py-2 rounded-[6px] text-[14px] font-[500] hover:bg-[#007C3808] transition-all duration-200 shadow-sm min-w-fit">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                </svg>
                {{ __('Import') }}
            </button>

    <a href="#" data-size="xl" data-url="{{ route('payroll.generate.create') }}" data-ajax-popup="true"
        data-bs-toggle="tooltip" title="{{ __('Create') }}" data-title="{{ __('Generate Payroll') }}"
        class="flex items-center gap-2 bg-[#007C38] text-white px-4 py-2 rounded-[6px] text-[14px] font-[500] hover:bg-[#005f2a] transition-all duration-200 shadow-sm min-w-fit">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
        {{ __('Create New ') }}

    </a>
</div>
@endsection

@section('content')
<div class="row table-new-design">
    <div class="col-md-12">

      <div id="bulk-actions-bar" class="card border-0 shadow-sm rounded-[8px] mb-4 overflow-hidden">
         <div class="card-body p-4 bg-[#F8FAFC]">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
               <div class="flex flex-wrap items-center gap-4">
                  <div class="flex items-center gap-2">
                     <svg class="w-5 h-5 text-[#007C38]" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"></path>
                     </svg>
                     <span class="text-[14px] font-[600] text-[#374151]">
                     <span id="selected-count">10</span> Employee selected
                     </span>
                  </div>
                  <div class="flex gap-2">
                     <button type="button" id="select-all-btn" class="text-[14px] font-[500] text-[#007C38] hover:text-[#005f2a] transition-colors duration-200">
                     Select All
                     </button>
                     <button type="button" id="deselect-all-btn" class="text-[14px] font-[500] text-[#6B7280] hover:text-[#374151] transition-colors duration-200">
                     Deselect All
                     </button>
                  </div>
               </div>
               <div class="flex flex-wrap items-center gap-2 w-full sm:w-auto">
                  
                  <button type="button" id="bulk-export-btn" class="inline-flex items-center gap-2 px-3 py-2 border border-[#E5E7EB] text-[#374151] bg-white hover:bg-[#F9FAFB] rounded-[6px] text-[14px] font-[500] transition-all duration-200">
                     <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 712-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                     </svg>
                     <span class="hidden sm:inline">Export Selected</span>
                     <span class="sm:hidden">Export</span>
                  </button>
                  
                  <div class="relative">
                     <button type="button" id="bulk-edit-btn" class="inline-flex items-center gap-2 px-3 py-2 border border-[#E5E7EB] text-[#374151] bg-white hover:bg-[#F9FAFB] rounded-[6px] text-[14px] font-[500] transition-all duration-200" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                           <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        <span class="hidden sm:inline">Bulk Edit</span>
                        <span class="sm:hidden">Edit</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                           <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                     </button>
                     <div class="dropdown-menu dropdown-menu-end mt-2 w-48 bg-white border border-[#E5E7EB] rounded-[6px] shadow-lg p-1">
                        <button type="button" class="bulk-action-item w-full text-left px-3 py-2 text-[14px] text-[#374151] hover:bg-[#F3F4F6] rounded-[4px] transition-colors duration-200" data-action="change-department">
                        Change Department
                        </button>
                        <button type="button" class="bulk-action-item w-full text-left px-3 py-2 text-[14px] text-[#374151] hover:bg-[#F3F4F6] rounded-[4px] transition-colors duration-200" data-action="change-type">
                        Change Employee Type
                        </button>
                        <button type="button" class="bulk-action-item w-full text-left px-3 py-2 text-[14px] text-[#374151] hover:bg-[#F3F4F6] rounded-[4px] transition-colors duration-200" data-action="change-branch">
                        Change Branch
                        </button>
                     </div>
                  </div>
                  
                  <button type="button" id="bulk-delete-btn" class="inline-flex items-center gap-2 px-3 py-2 bg-red-600 text-white hover:bg-red-700 rounded-[6px] text-[14px] font-[500] transition-all duration-200">
                     <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                     </svg>
                     <span class="hidden sm:inline">Delete Selected</span>
                     <span class="sm:hidden">Delete</span>
                  </button>
               </div>
            </div>
         </div>
      </div>
      
        <div class="card-body bg-white border border-[#E5E5E5] rounded-[8px] p-4 table-border-style table-border-style">
            <div class="table-responsive">
                <table class="table datatable border border-[#E5E5E5] rounded-[8px] ">
                    <thead>
                        <tr>
                            <th class="input-checkbox border border-[#E5E5E5] px-4 lg:px-6 py-4 text-left text-[12px] font-[700] text-[#374151] uppercase tracking-wider w-12"><input type="checkbox"></th>
                            <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]">{{ __('Employee ID')}}</th>
                            <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]">
                                {{ __('Name') }}
                            </th>
                            <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]">
                                {{ __('Email') }} 
                            </th>
                            <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]">
                                {{ __('Salary') }}
                            </th>
                            <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]">
                                {{ __('Net Salary') }}
                            </th>
                            <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]">
                                {{ __('Payroll Month') }}
                            </th>
                            <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]">
                                {{ __('Action') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($payrolls as $k => $payroll)
                        <tr class="cust_tr" id="cust_detail"
                            data-url="{{ route('customer.show', $payroll['id']) }}"
                            data-id="{{ $payroll['id'] }}">
                            <td class="input-checkbox px-4 lg:px-6 py-4 text-left text-[12px] font-[700] text-[#374151] uppercase tracking-wider border-0 w-12"><input type="checkbox"></td>
                            <td class="Id px-4 py-3 border border-[#E5E5E5] text-gray-700">
                                @can('show customer')
                                <a href="#"
                                    class="border border-[#137051] leading-[24px] text-[#137051] rounded-[4px] text-[12px] font-[500] px-5">
                                    {{ $payroll->employee['employee_id'] }}
                                </a>
                                @else
                                <a href="#"
                                    class="border border-[#137051] leading-[24px] text-[#137051] rounded-[4px] text-[12px] font-[500] px-5">
                                    {{ $payroll->employee['employee_id'] }}
                                </a>
                                @endcan
                            </td>
                            <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">{{ $payroll->employee['name'] }}
                            </td>
                            <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">{{ $payroll->employee['email'] }}
                            </td>
                            <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">
                                {{ $payroll['basic_salary'] }}
                            </td>

                            <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">
                                {{ $payroll['net_salary'] }}
                            </td>

                            <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">
                                {{ \Carbon\Carbon::parse($payroll['payroll_month'])->format('F') }}
                            </td>

                            <td class="Action px-4 py-3 text-right relative border border-[#E5E5E5]">
                                <button
                                    class="absolute top-3 right-3 text-gray-400 hover:text-gray-600 cursor-pointer"
                                    type="button" data-bs-toggle="dropdown" aria-haspopup="true"
                                    aria-expanded="false">
                                    <i class="ti ti-dots-vertical"></i>
                                </button>

                                <div
                                    class="dropdown-menu dropdown-menu-end mt-0 w-[190px] bg-white border rounded-md shadow-lg text-sm p-0">

                                    @can('edit customer')
                                    <a href="#"
                                        class="dropdown-item flex text-[#323232] gap-2 w-full px-4 py-2 text-left hover:bg-[#007C3812]"
                                        data-url="{{ route('payroll.edit', $payroll['id']) }}"
                                        data-ajax-popup="true" data-size="xl" data-bs-toggle="tooltip"
                                        title="{{ __('Edit') }}" data-title="{{ __('Edit Payroll') }}">
                                        <img src="{{ asset('web-assets/dashboard/icons/action_icons/edit.svg') }}"
                                            alt="edit" />
                                        <span>{{ __('Edit') }}</span>
                                    </a>
                                    @endcan

                                    @can('delete customer')
                                    {!! Form::open([
                                    'method' => 'DELETE',
                                    'route' => ['payroll.destroy', $payroll['id']],
                                    'id' => 'delete-form-' . $payroll['id'],
                                    ]) !!}
                                    <a href="#!"
                                        class="dropdown-item bs-pass-para flex text-[#323232] gap-2 w-full px-4 py-2 text-left hover:bg-[#007C3812]">
                                        <img src="{{ asset('web-assets/dashboard/icons/action_icons/delete.svg') }}"
                                            alt="delete" />
                                        <span>{{ __('Delete') }}</span>
                                    </a>
                                    {!! Form::close() !!}
                                    @endcan
                                    {{-- @endif --}}
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
@endsection

@push('script-page')
<script>
    let allowanceIndex = 1;
    let deductionIndex = 1;
    let bonusIndex = 1;

    function addAllowanceBtn() {
        let html = `
                <div class="allowance-group col-12 row mt-2">
                    <div class="col-md-6">
                        <input type="text" name="allowances[${allowanceIndex}][type]" class="form-control" placeholder="Allowance Type">
                    </div>
                    <div class="col-md-6">
                        <input type="number" name="allowances[${allowanceIndex}][amount]" class="form-control" placeholder="Amount">
                    </div>
                </div>`;
        document.getElementById('allowances-wrapper').insertAdjacentHTML('beforeend', html);
        allowanceIndex++;
    };

    function addDeductionBtn() {
        let html = `
                <div class="deduction-group col-12 row mt-2">
                    <div class="col-md-6">
                        <input type="text" name="deductions[${deductionIndex}][type]" class="form-control" placeholder="Deduction Type">
                    </div>
                    <div class="col-md-6">
                        <input type="number" name="deductions[${deductionIndex}][amount]" class="form-control" placeholder="Amount">
                    </div>
                </div>`;
        document.getElementById('deductions-wrapper').insertAdjacentHTML('beforeend', html);
        deductionIndex++;
    }

    function addBonusBtn() {
        let html = `
                <div class="bonus-group col-12 row mt-2">
                    <div class="col-md-6">
                        <input type="text" name="bonuses[${bonusIndex}][type]" class="form-control" placeholder="Bonus Type">
                    </div>
                    <div class="col-md-6">
                        <input type="number" name="bonuses[${bonusIndex}][amount]" class="form-control" placeholder="Amount">
                    </div>
                </div>`;
        document.getElementById('bonuses-wrapper').insertAdjacentHTML('beforeend', html);
        bonusIndex++;
    }
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.addEventListener('change', function(event) {
            const target = event.target;
            console.log(target);
            if (target && target.id === 'employee_id_select') {
                const empId = target.value;
                if (!empId) return;

                fetch(`/payroll/employee-info/${empId}`)
                    .then(res => res.json())
                    .then(data => {
                        if (data.error) {
                            console.error(data.message);
                            return;
                        }

                        document.querySelector('input[name="basic_salary"]').value = data.basic_salary || 0;

                        document.getElementById('allowances-wrapper').innerHTML = '';
                        document.getElementById('deductions-wrapper').innerHTML = '';
                        document.getElementById('bonuses-wrapper').innerHTML = '';

                        (data.allowances || []).forEach((a) => {
                            document.getElementById('allowances-wrapper').innerHTML += `
                            <div class="col-md-6">
                                <input type="text" class="form-control mb-2" value="${a.type}" disabled>
                            </div>
                            <div class="col-md-6">
                                <input type="number" class="form-control mb-2" value="${a.amount}" disabled>
                            </div>
                        `;
                        });

                        (data.deductions || []).forEach((d) => {
                            document.getElementById('deductions-wrapper').innerHTML += `
                            <div class="col-md-6">
                                <input type="text" class="form-control mb-2" value="${d.type}" disabled>
                            </div>
                            <div class="col-md-6">
                                <input type="number" class="form-control mb-2" value="${d.amount}" disabled>
                            </div>
                        `;
                        });

                        (data.bonuses || []).forEach((b) => {
                            document.getElementById('bonuses-wrapper').innerHTML += `
                            <div class="col-md-6">
                                <input type="text" class="form-control mb-2" value="${b.type}" disabled>
                            </div>
                            <div class="col-md-6">
                                <input type="number" class="form-control mb-2" value="${b.amount}" disabled>
                            </div>
                        `;
                        });
                    })
                    .catch(err => console.error('Failed to load employee payroll:', err));
            }
        });
    });
</script>
<script>
    function removeAllowanceBtn(button) {
        button.closest('.allowance-group').remove();
    }

    function removeDeductionBtn(button) {
        button.closest('.deduction-group').remove();
    }

    function removeBonusBtn(button) {
        button.closest('.bonus-group').remove();
    }
</script>
@endpush