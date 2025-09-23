<script src="{{ asset('js/unsaved.js') }}"></script>

@extends('layouts.admin')

@section('page-title')
  {{ __('Edit Employee') }}
@endsection

@section('breadcrumb')
  <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
  <li class="breadcrumb-item"><a href="{{ route('employee.index') }}">{{ __('Employee') }}</a></li>
  <li class="breadcrumb-item">{{ $employeesId }}</li>
@endsection

@section('content')
<div class="row">
{{ Form::model($employee, ['route' => ['employee.update', $employee->id], 'method' => 'PUT', 'enctype' => 'multipart/form-data', 'class' => 'needs-validation', 'novalidate']) }}

  <div class="modal-body p-6 bg-[#FAFBFC] w-100">
    <div class="row">
      {{-- LEFT: Personal Detail --}}
      <div class="col-md-6 col-12 d-flex">
        <div class="bg-white rounded-[8px] border border-[#E5E7EB] mb-6 shadow-sm h-100 w-100">
          <div class="heading-cstm-form">
            <h6 class="mb-0">{{ __('Personal Detail') }}</h6>
          </div>
          <div class="row p-6 employee-detail-edit-body">
            <div class="form-group col-md-6 mb-3">
              {!! Form::label('name', __('Name'), ['class'=>'form-label']) !!} <x-required/>
              {!! Form::text('name', null, ['class' => 'form-control','required','placeholder'=>__('Enter employee name')]) !!}
            </div>

            <div class="form-group col-md-6 mb-3">
              <x-mobile label="{{__('Phone')}}" name="phone" value="{{ $employee->phone }}" required placeholder="Enter employee phone"></x-mobile>
            </div>

            <div class="form-group col-md-6 mb-3">
              {!! Form::label('dob', __('Date of Birth'),['class'=>'form-label']) !!} <x-required/>
              {!! Form::date('dob', null, ['class' => 'form-control', 'required']) !!}
            </div>
            <div class="form-group col-md-6 mb-3">
              {!! Form::label('email', __('Email'), ['class' => 'form-label']) !!} <x-required/>
              {!! Form::email('email', old('email'), ['class' => 'form-control', 'required', 'placeholder'=>__('Enter employee email')]) !!}
            </div>

            <div class="form-group col-md-6 mb-3">
              {!! Form::label('gender', __('Gender'),['class'=>'form-label']) !!} <x-required/>
              <div class="d-flex radio-check gap-3 mt-1">
                <label class="form-check form-check-inline">
                  <input type="radio" id="g_male" value="Male" name="gender" class="form-check-input" {{ $employee->gender == 'Male' ? 'checked' : '' }} required>
                  <span class="form-check-label">{{ __('Male') }}</span>
                </label>
                <label class="form-check form-check-inline">
                  <input type="radio" id="g_female" value="Female" name="gender" class="form-check-input" {{ $employee->gender == 'Female' ? 'checked' : '' }} required>
                  <span class="form-check-label">{{ __('Female') }}</span>
                </label>
              </div>
            </div>

            <div class="form-group col-12 mb-0">
              {!! Form::label('address', __('Address'),['class'=>'form-label']) !!} <x-required/>
              {!! Form::textarea('address', null, ['class' => 'form-control','rows'=>2,'required','placeholder'=>__('Enter employee address')]) !!}
            </div>

            @if(\Auth::user()->type == 'employee')
              <div class="col-12 mt-4">
                {!! Form::submit(__('Update'), ['class' => 'btn bg-[#007C38] text-white hover:bg-[#005f2a] px-6 py-2 rounded-[4px] text-[14px] font-[500] shadow-sm float-end']) !!}
              </div>
            @endif
          </div>
        </div>
      </div>

      {{-- RIGHT: Company Detail (hidden when editing as Employee) --}}
      @if(\Auth::user()->type != 'Employee')
        <div class="col-md-6 col-12 d-flex">
          <div class="bg-white rounded-[8px] border border-[#E5E7EB] mb-6 shadow-sm h-100 w-100">
            <div class="heading-cstm-form">
              <h6 class="mb-0">{{ __('Company Detail') }}</h6>
            </div>
            <div class="row p-6 employee-detail-edit-body">
              @csrf
              <div class="form-group col-md-12 mb-3">
                {!! Form::label('employee_id', __('Employee ID'),['class'=>'form-label']) !!}
                {!! Form::text('employee_id', $employeesId, ['class' => 'form-control','disabled'=>true]) !!}
              </div>

              <div class="form-group col-md-6 mb-3">
                {{ Form::label('branch_id', __('Branch'),['class'=>'form-label']) }} <x-required/>
                {{ Form::select('branch_id', $branches, null, ['class' => 'form-control select2','required','id' => 'branch_id', 'placeholder'=>__('Select Branch')]) }}
              </div>

              <div class="form-group col-md-6 mb-3">
                {{ Form::label('department_id', __('Department'),['class'=>'form-label']) }} <x-required/>
                {{ Form::select('department_id', $departments, null, ['class' => 'form-control select2','required','id' => 'department_id', 'placeholder'=>__('Select Department')]) }}
              </div>

              <div class="form-group col-md-6 mb-3">
                {{ Form::label('designation_id', __('Designation'),['class'=>'form-label']) }} <x-required/>
                {{ Form::select('designation_id', $designations, null, ['class' => 'form-control select2','required','id' => 'designation_id', 'placeholder'=>__('Select Designation')]) }}
              </div>

              <div class="form-group col-md-6 mb-0">
                {!! Form::label('company_doj', __('Company Date Of Joining'),['class'=>'form-label']) !!} <x-required/>
                {!! Form::date('company_doj', null, ['class' => 'form-control','required']) !!}
              </div>
            </div>
          </div>
        </div>
      @else
        {{-- Read-only company detail for Employee role --}}
        <div class="col-md-6 col-12 d-flex">
          <div class="bg-white rounded-[8px] border border-[#E5E7EB] mb-6 shadow-sm h-100 w-100">
            <div class="heading-cstm-form">
              <h6 class="mb-0">{{ __('Company Detail') }}</h6>
            </div>
            <div class="row p-6">
              <div class="col-md-6 mb-2">
                <strong>{{ __('Branch') }}</strong>
                <div>{{ optional($employee->branch)->name }}</div>
              </div>
              <div class="col-md-6 mb-2">
                <strong>{{ __('Department') }}</strong>
                <div>{{ optional($employee->department)->name }}</div>
              </div>
              <div class="col-md-6 mb-2">
                <strong>{{ __('Designation') }}</strong>
                <div>{{ optional($employee->designation)->name }}</div>
              </div>
              <div class="col-md-6 mb-0">
                <strong>{{ __('Date Of Joining') }}</strong>
                <div>{{ \Auth::user()->dateFormat($employee->company_doj) }}</div>
              </div>
            </div>
          </div>
        </div>
      @endif
    </div>

    @if(\Auth::user()->type != 'Employee')
      <div class="row">
        {{-- Documents --}}
        <div class="col-md-6 col-12 d-flex">
          <div class="bg-white rounded-[8px] border border-[#E5E7EB] mb-6 shadow-sm h-100 w-100">
            <div class="heading-cstm-form">
              <h6 class="mb-0">{{ __('Document') }}</h6>
            </div>
            <div class="p-6 employee-detail-edit-body">
              @php
                $employeedoc = $employee->documents()->pluck('document_value', __('document_id'));
                $basePath    = \App\Models\Utility::get_file('uploads/document/');
              @endphp

              @foreach($documents as $key => $document)
                <div class="row align-items-center mb-3">
                  <div class="col-md-4">
                    <label class="form-label mb-0">
                      {{ $document->name }}
                      @if($document->is_required == 1) <x-required/> @endif
                    </label>
                  </div>
                  <div class="col-md-8">
                    <input type="hidden" name="emp_doc_id[{{ $document->id}}]" value="{{ $document->id }}">
                    <div class="choose-files">
                      <label for="document[{{ $document->id }}]" class="w-100">
                        <div class="bg-primary text-white text-center py-2 rounded cursor-pointer">
                          <i class="ti ti-upload"></i> {{ __('Choose file here') }}
                        </div>
                        <input
                          class="form-control file-validate d-none @error('document') is-invalid @enderror"
                          @if($document->is_required == 1 && empty($employeedoc[$document->id])) required @endif
                          name="document[{{ $document->id}}]"
                          id="document[{{ $document->id }}]"
                          type="file"
                          data-filename="{{ $document->id.'_filename'}}"
                          onchange="document.getElementById('{{'preview_'.$key}}').src = window.URL.createObjectURL(this.files[0])">
                        <p class="file-error text-danger m-0"></p>
                      </label>

                      <img
                        id="{{'preview_'.$key}}"
                        src="{{ !empty($employeedoc[$document->id]) ? $basePath . '/' . $employeedoc[$document->id] : '' }}"
                        class="mt-2 rounded border"
                        style="max-width: 100%; height:auto;" />
                    </div>
                  </div>
                </div>
              @endforeach
            </div>
          </div>
        </div>

        {{-- Bank Account Detail --}}
        <div class="col-md-6 col-12 d-flex">
          <div class="bg-white rounded-[8px] border border-[#E5E7EB] mb-6 shadow-sm h-100 w-100">
            <div class="heading-cstm-form">
              <h6 class="mb-0">{{ __('Bank Account Detail') }}</h6>
            </div>
            <div class="row p-6 employee-detail-edit-body">
              <div class="form-group col-md-6 mb-3">
                {!! Form::label('account_holder_name', __('Account Holder Name'),['class'=>'form-label']) !!}
                {!! Form::text('account_holder_name', null, ['class' => 'form-control','placeholder'=>__('Enter account holder name')]) !!}
              </div>
              <div class="form-group col-md-6 mb-3">
                {!! Form::label('account_number', __('Account Number'),['class'=>'form-label']) !!}
                {!! Form::number('account_number', null, ['class' => 'form-control','placeholder'=>__('Enter account number')]) !!}
              </div>
              <div class="form-group col-md-6 mb-3">
                {!! Form::label('bank_name', __('Bank Name'),['class'=>'form-label']) !!}
                {!! Form::text('bank_name', null, ['class' => 'form-control','placeholder'=>__('Enter bank name')]) !!}
              </div>
              <div class="form-group col-md-6 mb-3">
                {!! Form::label('bank_identifier_code', __('Bank Identifier Code'),['class'=>'form-label']) !!}
                {!! Form::text('bank_identifier_code', null, ['class' => 'form-control','placeholder'=>__('Enter bank identifier code')]) !!}
              </div>
              <div class="form-group col-md-6 mb-3">
                {!! Form::label('branch_location', __('Branch Location'),['class'=>'form-label']) !!}
                {!! Form::text('branch_location', null, ['class' => 'form-control','placeholder'=>__('Enter branch location')]) !!}
              </div>
              <div class="form-group col-md-6 mb-0">
                {!! Form::label('tax_payer_id', __('Tax Payer Id'),['class'=>'form-label']) !!}
                {!! Form::text('tax_payer_id', null, ['class' => 'form-control','placeholder'=>__('Enter tax payer id')]) !!}
              </div>
            </div>
          </div>
        </div>
      </div>
    @else
      {{-- Employee read-only panels: Documents + Bank (unchanged content, styled) --}}
      <div class="row">
        <div class="col-md-6 col-12 d-flex">
          <div class="bg-white rounded-[8px] border border-[#E5E7EB] mb-6 shadow-sm h-100 w-100">
            <div class="heading-cstm-form">
              <h6 class="mb-0">{{ __('Document Detail') }}</h6>
            </div>
            <div class="row p-6">
              @php
                $employeedoc = $employee->documents()->pluck('document_value', __('document_id'));
              @endphp
              @foreach($documents as $key=>$document)
                <div class="col-md-12 mb-2">
                  <strong>{{ $document->name }}</strong>
                  <div>
                    <a href="{{ (!empty($employeedoc[$document->id])?asset(Storage::url('uploads/document')).'/'.$employeedoc[$document->id]:'') }}" target="_blank">
                      {{ (!empty($employeedoc[$document->id])?$employeedoc[$document->id]:'') }}
                    </a>
                  </div>
                </div>
              @endforeach
            </div>
          </div>
        </div>

        <div class="col-md-6 col-12 d-flex">
          <div class="bg-white rounded-[8px] border border-[#E5E7EB] mb-6 shadow-sm h-100 w-100">
            <div class="heading-cstm-form">
              <h6 class="mb-0">{{ __('Bank Account Detail') }}</h6>
            </div>
            <div class="row p-6">
              <div class="col-md-6 mb-2">
                <strong>{{ __('Account Holder Name') }}</strong>
                <div>{{ $employee->account_holder_name }}</div>
              </div>
              <div class="col-md-6 mb-2">
                <strong>{{ __('Account Number') }}</strong>
                <div>{{ $employee->account_number }}</div>
              </div>
              <div class="col-md-6 mb-2">
                <strong>{{ __('Bank Name') }}</strong>
                <div>{{ $employee->bank_name }}</div>
              </div>
              <div class="col-md-6 mb-2">
                <strong>{{ __('Bank Identifier Code') }}</strong>
                <div>{{ $employee->bank_identifier_code }}</div>
              </div>
              <div class="col-md-6 mb-2">
                <strong>{{ __('Branch Location') }}</strong>
                <div>{{ $employee->branch_location }}</div>
              </div>
              <div class="col-md-6 mb-0">
                <strong>{{ __('Tax Payer Id') }}</strong>
                <div>{{ $employee->tax_payer_id }}</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    @endif
  </div>

  {{-- Footer Actions (managers/admins) --}}
  @if(\Auth::user()->type != 'employee')
    <div class="modal-footer border-t border-[#E5E5E5] bg-[#FAFAFA] px-6 py-4 flex justify-end gap-3 w-100">
      <a href="{{ route('employee.index') }}"
         class="btn border border-[#E5E5E5] text-[#6B7280] bg-white hover:bg-[#F9FAFB] hover:border-[#D1D5DB] px-4 py-2 rounded-[4px] text-[14px] font-[500]">
        {{ __('Cancel') }}
      </a>
      <button type="submit"
              class="btn bg-[#007C38] text-white hover:bg-[#005f2a] px-6 py-2 rounded-[4px] text-[14px] font-[500] shadow-sm">
        <i class="ti ti-device-floppy text-[16px] mr-1"></i> {{ __('Update') }}
      </button>
    </div>
  @endif

{!! Form::close() !!}
</div>
@endsection

@push('css-page')
<style>
  .select2-container { width: 100% !important; }
  .select2-container .select2-dropdown { z-index: 2005 !important; }
  .heading-cstm-form{ background:
</style>
@endpush

@push('script-page')
<script>
$(function () {
  function initSelect2($scope) {
    var $modal = $scope.closest('.modal');
    $scope.find('.select2').each(function(){
      var parent = $(this).closest('.modal');
      $(this).select2({
        width: '100%',
        dropdownParent: parent.length ? parent : ($modal.length ? $modal : $(document.body))
      });
    });
  }
  initSelect2($(document));
  $(document).on('shown.bs.modal', function(e){ initSelect2($(e.target)); });

  $('input[type="file"]').on('change', function(e){
    var file = e.target.files[0] ? e.target.files[0].name : '';
    var file_name = $(this).attr('data-filename');
    if(file_name){ $('.' + file_name).text(file); }
  });

  $(document).on('change', '#branch_id', function() {
    var branch_id = $(this).val();
    getDepartment(branch_id);
  });

  function getDepartment(branch_id) {
    $.ajax({
      url: '{{ route('employee.getdepartment') }}',
      method: 'POST',
      data: { "branch_id": branch_id, "_token": "{{ csrf_token() }}" },
      success: function(data) {
        var $dept = $('#department_id');
        $dept.empty().append('<option value="" disabled>{{ __("Select any Department") }}</option>');
        $.each(data, function(key, value) { $dept.append('<option value="'+key+'">'+value+'</option>'); });
        $dept.val('').trigger('change.select2');
      }
    });
  }

  function getDesignation(did) {
    $.ajax({
      url: '{{ route('employee.json') }}',
      type: 'POST',
      data: { "department_id": did, "_token": "{{ csrf_token() }}" },
      success: function (data) {
        var $des = $('#designation_id');
        $des.empty().append('<option value="">{{ __("Select any Designation") }}</option>');
        $.each(data, function (key, value) {
          var selected = (String(key) === '{{ $employee->designation_id }}') ? 'selected' : '';
          $des.append('<option value="'+key+'" '+selected+'>'+value+'</option>');
        });
        $des.trigger('change.select2');
      }
    });
  }

  var d_id = $('#department_id').val();
  if (d_id) { getDesignation(d_id); }

  $(document).on('change', 'select[name=department_id]', function () {
    getDesignation($(this).val());
  });

  $('.needs-validation').on('submit', function(e){
    if (!this.checkValidity()) {
      e.preventDefault(); e.stopPropagation();
      $(this).find(':invalid').addClass('border-red-500');
    }
    $(this).addClass('was-validated');
  });
  $('.form-control').on('input change', function(){
    if (this.checkValidity()) $(this).removeClass('border-red-500');
  });
});
</script>
@endpush
