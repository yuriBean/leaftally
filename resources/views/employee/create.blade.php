@extends('layouts.admin')

@section('page-title')
  {{ __('Create Employee') }}
@endsection

@section('breadcrumb')
  <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Home') }}</a></li>
  <li class="breadcrumb-item"><a href="{{ route('employee.index') }}">{{ __('Employee') }}</a></li>
  <li class="breadcrumb-item">{{ __('Create Employee') }}</li>
@endsection

@push('css-page')
<style>
  :root {
    --zameen-primary: #007c38;
    --zameen-primary-light: #10b981;
    --zameen-primary-dark: #007c38;
    --zameen-secondary: #3f51b5;
    --zameen-success: #4caf50;
    --zameen-danger: #f44336;
    --zameen-warning: #ff9800;
    --zameen-info: #2196f3;
    --zameen-light: #f8f9fa;
    --zameen-dark: #212529;
    --zameen-gray-100: #f8f9fa;
    --zameen-gray-200: #e9ecef;
    --zameen-gray-300: #dee2e6;
    --zameen-gray-400: #ced4da;
    --zameen-gray-500: #adb5bd;
    --zameen-gray-600: #6c757d;
    --zameen-gray-700: #495057;
    --zameen-gray-800: #343a40;
    --zameen-gray-900: #212529;
    --zameen-border: #e0e0e0;
    --zameen-border-light: #f0f0f0;
    --zameen-shadow: 0 2px 10px rgba(0,0,0,.1);
    --zameen-shadow-lg: 0 4px 20px rgba(0,0,0,.15);
    --zameen-radius: 8px;
    --zameen-radius-lg: 12px;
  }

  .zameen-container{background:#f8f9fa;min-height:100vh;padding:2rem 1rem;}
  .zameen-card{background:#fff;border-radius:var(--zameen-radius-lg);box-shadow:var(--zameen-shadow);overflow:hidden;margin:1rem auto;}
  .zameen-form-container{padding:2.5rem;}
  .zameen-section{margin-bottom:2.5rem;}
  .zameen-section-title{display:flex;align-items:center;font-size:1.125rem;font-weight:600;color:var(--zameen-gray-800);margin-bottom:1.5rem;padding-bottom:.75rem;border-bottom:2px solid var(--zameen-primary);}
  .zameen-section-icon{width:20px;height:20px;margin-right:.75rem;color:var(--zameen-primary);}
  .zameen-form-row{display:grid;grid-template-columns:1fr;gap:1.5rem;margin-bottom:1.5rem;}
  .zameen-form-row.two-cols{grid-template-columns:1fr 1fr;}
  .zameen-form-group{display:flex;flex-direction:column;}
  .zameen-label{font-weight:500;color:var(--zameen-gray-700);margin-bottom:.5rem;font-size:.875rem;}
  .zameen-required{color:var(--zameen-danger);margin-left:.25rem;}
  .zameen-input{padding:.75rem 1rem;border:1px solid var(--zameen-gray-300);border-radius:var(--zameen-radius);font-size:1rem;transition:.2s;background:#fff;}
  .zameen-input:focus{outline:none;border-color:var(--zameen-primary);box-shadow:0 0 0 3px rgba(39,167,118,.1);}
  .zameen-input:hover{border-color:var(--zameen-gray-400);}
  .zameen-textarea{resize:vertical;min-height:80px;}
  .zameen-btn{padding:.75rem 1.5rem;border-radius:var(--zameen-radius);font-weight:500;font-size:.875rem;cursor:pointer;transition:.2s;border:none;display:inline-flex;align-items:center;justify-content:center;text-decoration:none;}
  .zameen-btn-primary{background:var(--zameen-primary);color:#fff;}
  .zameen-btn-primary:hover{background:var(--zameen-primary-dark);transform:translateY(-1px);box-shadow:var(--zameen-shadow);}
  .zameen-btn-outline{background:transparent;color:var(--zameen-gray-600);border:1px solid var(--zameen-gray-300);}
  .zameen-btn-outline:hover{background:var(--zameen-gray-100);border-color:var(--zameen-gray-400);}
  .zameen-error{color:var(--zameen-danger);font-size:.75rem;margin-top:.25rem;}
  .zameen-footer{background:var(--zameen-gray-100);border-top:1px solid var(--zameen-border-light);display:flex;justify-content:flex-end;gap:1rem;margin:0 auto 1rem;border-radius:0 0 8px 8px;}
  .select2-container{width:100%!important;}
  .select2-container .select2-dropdown{z-index:2005!important;}
  @media(max-width:768px){.zameen-form-row.two-cols{grid-template-columns:1fr}.zameen-form-container{padding:1.5rem}.zameen-container{padding:1rem}}
</style>
@endpush

@section('content')
<div class="zameen-container">
  {{ Form::open(['route' => ['employee.store'], 'method' => 'post', 'enctype' => 'multipart/form-data', 'class'=>'needs-validation', 'novalidate']) }}

  {{-- Personal Detail --}}
  <div class="zameen-card">
    <div class="zameen-form-container">
      <div class="zameen-section">
        <div class="zameen-section-title">
          <svg class="zameen-section-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
          </svg>
          {{ __('Personal Detail') }}
        </div>

        <div class="row">
          <div class="form-group col-md-6 mb-3">
            {!! Form::label('name', __('Name'), ['class' => 'form-label']) !!} <x-required/>
            {!! Form::text('name', old('name'), ['class' => 'form-control', 'required', 'placeholder'=>__('Enter employee name')]) !!}
          </div>

          <div class="form-group col-md-6 mb-3">
            <x-mobile label="{{__('Phone')}}" name="phone" value="{{old('phone')}}" required placeholder="Enter employee phone"></x-mobile>
          </div>

          <div class="col-md-6 mb-3">
            {!! Form::label('dob', __('Date of Birth'), ['class' => 'form-label']) !!} <x-required/>
            {{ Form::date('dob', null, ['class' => 'form-control', 'required', 'autocomplete' => 'off', 'placeholder'=>__('Select Date of Birth')]) }}
          </div>

          <div class="col-md-6 mb-3">
            {!! Form::label('gender', __('Gender'), ['class' => 'form-label']) !!} <x-required/>
            <div class="d-flex radio-check gap-3">
              <label class="custom-control custom-radio custom-control-inline">
                <input type="radio" id="g_male" value="Male" name="gender" class="form-check-input" checked>
                <span class="form-check-label">{{ __('Male') }}</span>
              </label>
              <label class="custom-control custom-radio custom-control-inline">
                <input type="radio" id="g_female" value="Female" name="gender" class="form-check-input">
                <span class="form-check-label">{{ __('Female') }}</span>
              </label>
            </div>
          </div>

          <div class="form-group col-md-6 mb-3">
            {!! Form::label('email', __('Email'), ['class' => 'form-label']) !!} <x-required/>
            {!! Form::email('email', old('email'), ['class' => 'form-control', 'required', 'placeholder'=>__('Enter employee email')]) !!}
          </div>

          <div class="form-group col-12 mb-0">
            {!! Form::label('address', __('Address'), ['class' => 'form-label']) !!} <x-required/>
            {!! Form::textarea('address', old('address'), ['class' => 'form-control', 'rows' => 2, 'placeholder'=>__('Enter employee address'), 'required']) !!}
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- Company Detail --}}
  <div class="zameen-card">
    <div class="zameen-form-container">
      <div class="zameen-section">
        <div class="zameen-section-title">
          <svg class="zameen-section-icon" fill="currentColor" viewBox="0 0 16 16">
            <path d="M2 1a1 1 0 0 1 1-1h10a1 1 0 0 1 1 1v6.5a.5.5 0 0 1-1 0V1H3v14h3v-2.5a.5.5 0 0 1 .5-.5H8v4H3a1 1 0 0 1-1-1z"/>
            <path d="M4.5 2a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5zm3 0a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5zm3 0a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5z"/>
          </svg>
          {{ __('Company Detail') }}
        </div>

        @csrf
        <div class="row">
          <div class="form-group col-12 mb-3">
            {!! Form::label('employee_id', __('Employee ID'), ['class' => 'form-label']) !!}
            {!! Form::text('employee_id', $employeesId, ['class' => 'form-control', 'disabled' => true]) !!}
          </div>

          <div class="form-group col-md-6 mb-3">
            {{ Form::label('branch_id', __('Select Branch'), ['class' => 'form-label']) }} <x-required/>
            {{ Form::select('branch_id', $branches, null, ['class' => 'form-control select2', 'placeholder' => __('Select Branch'), 'required']) }}
          </div>

          <div class="form-group col-md-6 mb-3">
            {{ Form::label('department_id', __('Select Department'), ['class' => 'form-label']) }} <x-required/>
            {{ Form::select('department_id', $departments, null, ['class' => 'form-control select2 department_id', 'id' => 'department_id', 'placeholder' => __('Select Department'), 'required']) }}
          </div>

          <div class="form-group col-md-6 mb-3">
            {{ Form::label('designation_id', __('Select Designation'), ['class' => 'form-label']) }} <x-required/>
            {{ Form::select('designation_id', $designations, null, ['class' => 'form-control select2', 'id' => 'designation_id', 'placeholder' => __('Select Designation'), 'required']) }}
          </div>

          <div class="form-group col-md-6 mb-0">
            {!! Form::label('company_doj', __('Company Date Of Joining'), ['class' => 'form-label']) !!} <x-required/>
            {{ Form::date('company_doj', null, ['class' => 'form-control', 'required', 'autocomplete' => 'off', 'placeholder'=>__('Select company date of joining')]) }}
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- Documents --}}
  <div class="zameen-card">
    <div class="zameen-form-container">
      <div class="zameen-section">
        <div class="zameen-section-title">
          <svg class="zameen-section-icon" fill="currentColor" viewBox="0 0 16 16">
            <path d="M9.828 4a.5.5 0 0 1 .354.146l.646.647H14a2 2 0 0 1 2 2v.5H0V6a2 2 0 0 1 2-2z"/>
            <path d="M0 7.5V12a2 2 0 0 0 2 2h10.5a2 2 0 0 0 1.937-1.5l1.5-6A2 2 0 0 0 14 5.5H9.172l-.646-.647A1.5 1.5 0 0 0 7.828 4H2a2 2 0 0 0-2 2z"/>
          </svg>
          {{ __('Document') }}
        </div>

        <div class="row">
          @foreach ($documents as $key => $document)
            <div class="col-12 mb-3">
              <div class="row align-items-center">
                <div class="col-md-4">
                  <label class="form-label mb-0">
                    {{ $document->name }}
                    @if ($document->is_required == 1) <x-required/> @endif
                  </label>
                </div>
                <div class="col-md-8">
                  <input type="hidden" name="emp_doc_id[{{ $document->id }}]" value="{{ $document->id }}">
                  <div class="choose-files">
                    <label for="document[{{ $document->id }}]" class="w-100">
                      <div class="bg-primary text-white text-center py-2 rounded cursor-pointer">
                        <i class="ti ti-upload"></i> {{ __('Choose file here') }}
                      </div>
                      <input
                        type="file"
                        class="form-control file file-validate d-none @error('document') is-invalid @enderror"
                        @if ($document->is_required == 1) required @endif
                        name="document[{{ $document->id }}]"
                        id="document[{{ $document->id }}]"
                        data-filename="{{ $document->id . '_filename' }}"
                        onchange="document.getElementById('{{ 'preview_'.$key }}').src = window.URL.createObjectURL(this.files[0])"
                      >
                      <p class="file-error text-danger m-0"></p>
                    </label>
                    <img id="{{ 'preview_'.$key }}" src="" class="mt-2 rounded border" style="max-width:100%;height:auto;" />
                  </div>
                </div>
              </div>
            </div>
          @endforeach
        </div>

      </div>
    </div>
  </div>

  {{-- Bank Account Detail --}}
  <div class="zameen-card">
    <div class="zameen-form-container">
      <div class="zameen-section">
        <div class="zameen-section-title">
          <svg class="zameen-section-icon" fill="currentColor" viewBox="0 0 16 16">
            <path d="M5 8a2 2 0 1 0 0-4 2 2 0 0 0 0 4m4-2.5a.5.5 0 0 1 .5-.5h4a.5.5 0 0 1 0 1h-4a.5.5 0 0 1-.5-.5"/>
            <path d="M2 2a2 2 0 0 0-2 2v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2z"/>
          </svg>
          {{ __('Bank Account Detail') }}
        </div>

        <div class="row">
          <div class="form-group col-md-6 mb-3">
            {!! Form::label('account_holder_name', __('Account Holder Name'), ['class' => 'form-label']) !!}
            {!! Form::text('account_holder_name', old('account_holder_name'), ['class' => 'form-control', 'placeholder'=>__('Enter account holder name')]) !!}
          </div>

          <div class="form-group col-md-6 mb-3">
            {!! Form::label('account_number', __('Account Number'), ['class' => 'form-label']) !!}
            {!! Form::number('account_number', old('account_number'), ['class' => 'form-control', 'placeholder'=>__('Enter account number')]) !!}
          </div>

          <div class="form-group col-md-6 mb-3">
            {!! Form::label('bank_name', __('Bank Name'), ['class' => 'form-label']) !!}
            {!! Form::text('bank_name', old('bank_name'), ['class' => 'form-control', 'placeholder'=>__('Enter bank name')]) !!}
          </div>

          <div class="form-group col-md-6 mb-3">
            {!! Form::label('bank_identifier_code', __('Bank Identifier Code'), ['class' => 'form-label']) !!}
            {!! Form::text('bank_identifier_code', old('bank_identifier_code'), ['class' => 'form-control', 'placeholder'=>__('Enter bank identifier code')]) !!}
          </div>

          <div class="form-group col-md-6 mb-3">
            {!! Form::label('branch_location', __('Branch Location'), ['class' => 'form-label']) !!}
            {!! Form::text('branch_location', old('branch_location'), ['class' => 'form-control', 'placeholder'=>__('Enter branch location')]) !!}
          </div>

          <div class="form-group col-md-6 mb-0">
            {!! Form::label('tax_payer_id', __('Tax Payer Id'), ['class' => 'form-label']) !!}
            {!! Form::text('tax_payer_id', old('tax_payer_id'), ['class' => 'form-control', 'placeholder'=>__('Enter tax payer id')]) !!}
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- Footer --}}
  <div class="zameen-footer">
    <a href="{{ route('employee.index') }}" class="zameen-btn zameen-btn-outline">
      {{ __('Cancel') }}
    </a>
    <button type="submit" class="zameen-btn zameen-btn-primary">
      <svg style="width:16px;height:16px;margin-right:.5rem" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
      </svg>
      {{ __('Create Employee') }}
    </button>
  </div>

  {!! Form::close() !!}
</div>
@endsection

@push('script-page')
<script src="{{ asset('js/unsaved.js') }}"></script>
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

  function getDesignation(did) {
    $.ajax({
      url: '{{ route('employee.json') }}',
      type: 'POST',
      data: { "department_id": did, "_token": "{{ csrf_token() }}" },
      success: function(data) {
        var $sel = $('#designation_id');
        $sel.empty().append('<option value="">{{ __("Select any Designation") }}</option>');
        $.each(data, function (key, value) { $sel.append('<option value="'+key+'">'+value+'</option>'); });
        $sel.trigger('change.select2');
      }
    });
  }

  var initDeptId = $('.department_id').val();
  if (initDeptId) { getDesignation(initDeptId); }

  $(document).on('change', 'select[name=department_id]', function() {
    var department_id = $(this).val();
    getDesignation(department_id);
  });
});
</script>
@endpush
