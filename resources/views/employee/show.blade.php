@extends('layouts.admin')

@section('page-title')
  {{ __('Employee') }}
@endsection

@section('breadcrumb')
  <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
  <li class="breadcrumb-item"><a href="{{ route('employee.index') }}">{{ __('Employees') }}</a></li>
  <li class="breadcrumb-item">
    {{ \Auth::user()->employeeIdFormat($employee->employee_id ?? '') ?: __('N/A') }}
  </li>
@endsection


@section('content')
@if(!empty($employee))
  @php
    $empIdFmt = \Auth::user()->employeeIdFormat($employee->employee_id ?? '');
    $docsMap  = $employee->documents()->pluck('document_value',__('document_id'));
  @endphp

  <div class="row">
    <div class="col-xl-12">

      {{-- Header Card --}}
      <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-6">
          <div class="flex items-center gap-4">
            <div class="w-16 h-16 bg-gradient-to-br from-[#007C38] to-[#005f2a] rounded-full flex items-center justify-center shadow-md">
              <span class="text-[20px] font-[700] text-white">{{ strtoupper(substr($employee->name ?? 'NA', 0, 2)) }}</span>
            </div>
            <div class="flex-1">
              <div class="flex items-center gap-3 mb-2">
                <h4 class="text-[20px] font-[700] text-[#111827] mb-0">{{ $employee->name ?? 'N/A' }}</h4>
                @if(!empty($employee->employee_type))
                  <span class="inline-flex items-center px-3 py-1 rounded-full text-[12px] font-[600]
                    @if($employee->employee_type == 'permanent') bg-green-100 text-green-800 border border-green-200
                    @elseif($employee->employee_type == 'contract') bg-blue-100 text-blue-800 border border-blue-200
                    @elseif($employee->employee_type == 'temporary') bg-yellow-100 text-yellow-800 border border-yellow-200
                    @elseif($employee->employee_type == 'fixed-permanent') bg-purple-100 text-purple-800 border border-purple-200
                    @elseif($employee->employee_type == 'fixed-temporary') bg-purple-100 text-purple-800 border border-purple-200
                    @else bg-gray-100 text-gray-800 border border-gray-200 @endif">
                    {{ ucwords(str_replace('-', ' ', $employee->employee_type)) }}
                  </span>
                @endif
              </div>
              <div class="flex flex-wrap gap-6 text-[14px] text-[#6B7280]">
                <div class="flex items-center gap-2">
                  <i class="ti ti-mail"></i>
                  <span>{{ $employee->email ?? 'N/A' }}</span>
                </div>
                <div class="flex items-center gap-2">
                  <i class="ti ti-phone"></i>
                  <span>{{ $employee->phone ?? 'N/A' }}</span>
                </div>
                <div class="flex items-center gap-2">
                  <i class="ti ti-id"></i>
                  <span class="font-[600]">{{ $empIdFmt ?: 'N/A' }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      {{-- Personal Information --}}
      <div class="card border-0 shadow-sm mb-4 overflow-hidden">
        <div class="px-6 py-4" style="background:linear-gradient(90deg,#007C38,#005f2a)">
          <h6 class="text-[16px] font-[700] text-white mb-0">{{ __('Personal Information') }}</h6>
        </div>
        <div class="card-body p-6">
          <div class="row g-4">
            <div class="col-md-4">
              <div class="text-[12px] text-[#6B7280] mb-1">{{ __('Full Name') }}</div>
              <div class="text-[14px]">{{ $employee->name ?? 'N/A' }}</div>
            </div>
            <div class="col-md-4">
              <div class="text-[12px] text-[#6B7280] mb-1">{{ __('Employee ID') }}</div>
              <div class="text-[14px]">{{ $empIdFmt ?: 'N/A' }}</div>
            </div>
            <div class="col-md-4">
              <div class="text-[12px] text-[#6B7280] mb-1">{{ __('Employment Type') }}</div>
              <div class="text-[14px]">{{ !empty($employee->employee_type) ? ucwords(str_replace('-',' ',$employee->employee_type)) : 'N/A' }}</div>
            </div>

            <div class="col-md-4">
              <div class="text-[12px] text-[#6B7280] mb-1">{{ __('Date of Birth') }}</div>
              <div class="text-[14px]">{{ $employee->dob ? \Auth::user()->dateFormat($employee->dob) : 'N/A' }}</div>
            </div>
            <div class="col-md-8">
              <div class="text-[12px] text-[#6B7280] mb-1">{{ __('Address') }}</div>
              <div class="text-[14px]">{{ $employee->address ?? 'N/A' }}</div>
            </div>
          </div>
        </div>
      </div>

      {{-- Company Information --}}
      <div class="card border-0 shadow-sm mb-4 overflow-hidden">
        <div class="px-6 py-4" style="background:linear-gradient(90deg,#007C38,#005f2a)">
          <h6 class="text-[16px] font-[700] text-white mb-0">{{ __('Company Information') }}</h6>
        </div>
        <div class="card-body p-6">
          <div class="row g-4">
            <div class="col-md-4">
              <div class="text-[12px] text-[#6B7280] mb-1">{{ __('Branch') }}</div>
              <div class="text-[14px]">{{ optional($employee->branch)->name ?? 'N/A' }}</div>
            </div>
            <div class="col-md-4">
              <div class="text-[12px] text-[#6B7280] mb-1">{{ __('Department') }}</div>
              <div class="text-[14px]">{{ optional($employee->department)->name ?? 'N/A' }}</div>
            </div>
            <div class="col-md-4">
              <div class="text-[12px] text-[#6B7280] mb-1">{{ __('Designation') }}</div>
              <div class="text-[14px]">{{ optional($employee->designation)->name ?? 'N/A' }}</div>
            </div>
            <div class="col-md-4">
              <div class="text-[12px] text-[#6B7280] mb-1">{{ __('Date of Joining') }}</div>
              <div class="text-[14px]">{{ $employee->company_doj ? \Auth::user()->dateFormat($employee->company_doj) : 'N/A' }}</div>
            </div>
            @if(!empty($employee->salaryType) || !empty($employee->salary))
              <div class="col-md-4">
                <div class="text-[12px] text-[#6B7280] mb-1">{{ __('Salary Type') }}</div>
                <div class="text-[14px]">{{ optional($employee->salaryType)->name ?? 'N/A' }}</div>
              </div>
              <div class="col-md-4">
                <div class="text-[12px] text-[#6B7280] mb-1">{{ __('Basic Salary') }}</div>
                <div class="text-[14px]">{{ $employee->salary ?? 'N/A' }}</div>
              </div>
            @endif
          </div>
        </div>
      </div>

      {{-- Banking Information --}}
      <div class="card border-0 shadow-sm mb-4 overflow-hidden">
        <div class="px-6 py-4" style="background:linear-gradient(90deg,#007C38,#005f2a)">
          <h6 class="text-[16px] font-[700] text-white mb-0">{{ __('Banking Information') }}</h6>
        </div>
        <div class="card-body p-6">
          <div class="row g-4">
            <div class="col-md-4">
              <div class="text-[12px] text-[#6B7280] mb-1">{{ __('Account Holder') }}</div>
              <div class="text-[14px]">{{ $employee->account_holder_name ?? 'N/A' }}</div>
            </div>
            <div class="col-md-4">
              <div class="text-[12px] text-[#6B7280] mb-1">{{ __('Account Number') }}</div>
              <div class="text-[14px]">{{ $employee->account_number ?? 'N/A' }}</div>
            </div>
            <div class="col-md-4">
              <div class="text-[12px] text-[#6B7280] mb-1">{{ __('Bank Name') }}</div>
              <div class="text-[14px]">{{ $employee->bank_name ?? 'N/A' }}</div>
            </div>
            <div class="col-md-4">
              <div class="text-[12px] text-[#6B7280] mb-1">{{ __('Bank Identifier Code') }}</div>
              <div class="text-[14px]">{{ $employee->bank_identifier_code ?? 'N/A' }}</div>
            </div>
            <div class="col-md-4">
              <div class="text-[12px] text-[#6B7280] mb-1">{{ __('Branch Location') }}</div>
              <div class="text-[14px]">{{ $employee->branch_location ?? 'N/A' }}</div>
            </div>
            <div class="col-md-4">
              <div class="text-[12px] text-[#6B7280] mb-1">{{ __('Tax Payer ID') }}</div>
              <div class="text-[14px]">{{ $employee->tax_payer_id ?? 'N/A' }}</div>
            </div>
          </div>
        </div>
      </div>

      {{-- Documents --}}
      @isset($documents)
        <div class="card border-0 shadow-sm mb-4 overflow-hidden">
          <div class="px-6 py-4" style="background:linear-gradient(90deg,#007C38,#005f2a)">
            <h6 class="text-[16px] font-[700] text-white mb-0">{{ __('Document Detail') }}</h6>
          </div>
          <div class="card-body p-6">
            @if($documents->isNotEmpty())
              <div class="row g-4">
                @foreach($documents as $document)
                  @php
                    $fileName = $docsMap[$document->id] ?? null;
                    $fileUrl  = $fileName ? asset(Storage::url('uploads/document')).'/'.$fileName : null;
                  @endphp
                  <div class="col-md-4">
                    <div class="text-[12px] text-[#6B7280] mb-1">{{ $document->name }}</div>
                    <div class="text-[14px]">
                      @if($fileUrl)
                        <a class="text-[#007C38] hover:underline" href="{{ $fileUrl }}" target="_blank">{{ $fileName }}</a>
                      @else
                        <span class="text-[#6B7280]">—</span>
                      @endif
                    </div>
                  </div>
                @endforeach
              </div>
            @else
              <div class="text-[14px] text-[#6B7280]">{{ __('No Document Type Added.') }}</div>
            @endif
          </div>
        </div>
      @endisset

      {{-- Footer meta --}}
      <div class="card border-0 shadow-sm">
        <div class="card-body d-flex justify-content-between align-items-center" style="background:#F9FAFB;">
          <div class="text-[12px] text-[#6B7280]">
            <span>{{ __('Employee ID:') }} <strong>{{ $empIdFmt ?: 'N/A' }}</strong></span>
            @if(!empty($employee->company_doj))
              <span class="mx-2">•</span>
              <span>{{ __('Joined:') }} <strong>{{ \Auth::user()->dateFormat($employee->company_doj) }}</strong></span>
            @endif
          </div>
          <div class="d-flex gap-2">
            <a href="{{ route('employee.index') }}" class="btn btn-light border">{{ __('Back') }}</a>
            @can('edit employee')
              <a href="{{ route('employee.edit', \Illuminate\Support\Facades\Crypt::encrypt($employee->id)) }}" class="btn btn-success text-white">{{ __('Edit Employee') }}</a>
            @endcan
          </div>
        </div>
      </div>

    </div>
  </div>
@endif
@endsection
