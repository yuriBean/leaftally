@extends('layouts.admin')

@section('page-title')
  {{ __('Manage Employee Salary') }}
@endsection

@section('breadcrumb')
  <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
  <li class="breadcrumb-item">{{ __('Employee Salary') }}</li>
@endsection

@section('content')
<div class="row">
  <div class="col-xl-12">
    <div class="card border-0 rounded-2xl shadow-md overflow-hidden my-3">
      <div class="h-1 w-full" style="background:#007C38;"></div>
      <div class="card-body table-border-style">
        <div class="table-responsive table-new-design bg-white p-4">
          <table class="table datatable border border-[#E5E5E5] rounded-[8px]">
            <thead>
              <tr>
                <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]">{{ __('Employee Id') }}</th>
                <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]">{{ __('Name') }}</th>
                <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]">{{ __('Payroll Type') }}</th>
                <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]">{{ __('Salary') }}</th>
                <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]">{{ __('Net Salary') }}</th>
                <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]" width="10%">{{ __('Action') }}</th>
              </tr>
            </thead>

            <tbody>
              @foreach ($employees as $employee)
                <tr>
                  <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">
                    <a href="{{ route('setsalary.show', $employee->id) }}"
                       class="border border-[#137051] leading-[24px] text-[#137051] rounded-[4px] text-[12px] font-[500] px-5"
                       data-bs-toggle="tooltip" title="{{ __('View') }}">
                      {{ \Auth::user()->employeeIdFormat($employee->employee_id) }}
                    </a>
                  </td>

                  <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">
                    {{ $employee->name }}
                  </td>

                  <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">
                    {{ optional($employee->salaryType)->name ?? '-' }}
                  </td>

                  <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">
                    {{ \Auth::user()->priceFormat($employee->salary) }}
                  </td>

                  <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">
                    {{ $employee->get_net_salary() ? \Auth::user()->priceFormat($employee->get_net_salary()) : '-' }}
                  </td>

                  <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">
                    <button class="w-100 text-gray-400 hover:text-gray-600 cursor-pointer" type="button"
                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                      <i class="ti ti-dots-vertical"></i>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end mt-0 w-[190px] bg-white border rounded-md shadow-lg text-sm p-0">
                      <li>
                        <a href="{{ route('setsalary.show', $employee->id) }}"
                           class="dropdown-item flex items-center text-gray-700 gap-2 w-full px-4 py-2 text-left hover:bg-[#007C3812] text-sm">
                          <i class="ti ti-eye"></i><span>{{ __('Set / View Salary') }}</span>
                        </a>
                      </li>
                      @can('edit employee salary')
                      <li>
                        <a href="{{ route('setsalary.show', $employee->id) }}#edit"
                           class="dropdown-item flex items-center text-gray-700 gap-2 w-full px-4 py-2 text-left hover:bg-[#007C3812] text-sm">
                          <i class="ti ti-pencil"></i><span>{{ __('Edit Components') }}</span>
                        </a>
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
