<style>
  :root {
    --zameen-primary: #007c38;
    --zameen-primary-light: #10b981;
    --zameen-primary-dark: #007c38;
    --zameen-background-section: #f8f9fa;
    --zameen-background-light: #ffffff;
    --zameen-border: #e0e0e0;
    --zameen-border-light: #f0f0f0;
    --zameen-text: #2d3748;
    --zameen-text-light: #718096;
    --zameen-radius: 8px;
    --zameen-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  }

  .zameen-card {
    background: white;
    border-radius: 12px;
    box-shadow: var(--zameen-shadow);
    border: 1px solid var(--zameen-border-light);
    overflow: hidden;
  }

  .zameen-card-header {
    background: linear-gradient(135deg, var(--zameen-primary) 0%, var(--zameen-primary-light) 100%);
    color: white;
    padding: 1.5rem 2rem 1rem;
    border-bottom: none;
  }

  .zameen-form-group {
    margin-bottom: 1.5rem;
  }

  .zameen-form-label {
    display: block;
    font-weight: 600;
    color: var(--zameen-text);
    margin-bottom: 0.5rem;
    font-size: 0.875rem;
  }

  .zameen-form-input {
    width: 100%;
    padding: 0.75rem 1rem;
    border: 2px solid var(--zameen-border);
    border-radius: var(--zameen-radius);
    font-size: 0.875rem;
    color: var(--zameen-text);
    background: white;
    transition: all 0.2s ease;
  }

  .zameen-form-input:focus {
    outline: none;
    border-color: var(--zameen-primary);
    box-shadow: 0 0 0 3px rgba(39, 167, 118, 0.1);
  }

  .zameen-btn {
    padding: 0.75rem 1.5rem;
    font-size: 0.875rem;
    font-weight: 500;
    border-radius: var(--zameen-radius);
    border: 2px solid transparent;
    cursor: pointer;
    transition: all 0.2s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
  }

  .zameen-btn-primary {
    background: var(--zameen-primary);
    color: white;
    border-color: var(--zameen-primary);
  }

  .zameen-btn-primary:hover {
    background: var(--zameen-primary-dark);
    border-color: var(--zameen-primary-dark);
    color: white;
  }

  .zameen-btn-outline {
    background: white;
    color: var(--zameen-text);
    border-color: var(--zameen-border);
  }

  .zameen-btn-outline:hover {
    background: #f8f9fa;
    border-color: #adb5bd;
  }

  .zameen-form-icon {
    position: relative;
  }

  .zameen-form-icon span {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--zameen-text-light);
    z-index: 2;
  }

  .zameen-form-icon .zameen-form-input {
    padding-left: 2.5rem;
  }
</style>

<script src="{{ asset('js/unsaved.js') }}"></script>

{{ Form::open(['url' => 'bank-account','class'=>'needs-validation','novalidate']) }}
<div class="modal-body" style="background: var(--zameen-background-section); padding: 2rem;">
    <div class="zameen-card">
        <div class="zameen-card-header">
            <h5 style="margin: 0; font-weight: 600; font-size: 1.25rem; color: white; display: flex; align-items: center; gap: 0.5rem;">
                <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M2 4h20v4H2V4zm0 6h20v10H2V10zm4 2h12v2H6v-2zm0 4h8v2H6v-2z"/>
                </svg>
                {{ __('Bank Account Details') }}
            </h5>
        </div>
        <div style="padding: 2rem;">
            <div class="row">
                <div class="zameen-form-group col-md-6">
                    <label class="zameen-form-label">
                        {{ __('Account') }}
                        <span style="color: #ef4444; margin-left: 4px;">*</span>
                    </label>
                    <select name="chart_account_id" class="zameen-form-input" required="required">
                        @foreach ($chartAccounts as $key => $chartAccount)
                            <option value="{{ $key }}" class="subAccount">{{ $chartAccount }}</option>
                            @foreach ($subAccounts as $subAccount)
                                @if ($key == $subAccount['account'])
                                    <option value="{{ $subAccount['id'] }}" class="ms-5"> &nbsp; &nbsp;&nbsp;
                                        {{ $subAccount['name'] }}</option>
                                @endif
                            @endforeach
                        @endforeach
                    </select>
                </div>
                <div class="zameen-form-group col-md-6">
                    <label class="zameen-form-label">
                        {{ __('Bank Holder Name') }}
                        <span style="color: #ef4444; margin-left: 4px;">*</span>
                    </label>
                    <div class="zameen-form-icon">
                        <span><i class="ti ti-address-card"></i></span>
                        {{ Form::text('holder_name', '', ['class' => 'zameen-form-input', 'required' => 'required']) }}
                    </div>
                </div>
                <div class="zameen-form-group col-md-6">
                    <label class="zameen-form-label">
                        {{ __('Bank Name') }}
                        <span style="color: #ef4444; margin-left: 4px;">*</span>
                    </label>
                    <div class="zameen-form-icon">
                        <span><i class="ti ti-university"></i></span>
                        {{ Form::text('bank_name', '', ['class' => 'zameen-form-input', 'required' => 'required']) }}
                    </div>
                </div>
                <div class="zameen-form-group col-md-6">
                    <label class="zameen-form-label">
                        {{ __('Account Number') }}
                        <span style="color: #ef4444; margin-left: 4px;">*</span>
                    </label>
                    <div class="zameen-form-icon">
                        <span><i class="ti ti-notes-medical"></i></span>
                        {{ Form::text('account_number', '', ['class' => 'zameen-form-input', 'required' => 'required']) }}
                    </div>
                </div>
                <div class="zameen-form-group col-md-6">
                    <label class="zameen-form-label">
                        {{ __('Opening Balance') }}
                        <span style="color: #ef4444; margin-left: 4px;">*</span>
                    </label>
                    <div class="zameen-form-icon">
                        <span><i class="ti ti-dollar-sign"></i></span>
                        {{ Form::number('opening_balance', '', ['class' => 'zameen-form-input', 'required' => 'required', 'step' => '0.01']) }}
                    </div>
                </div>

                <x-mobile  div-class="col-md-6 zameen-form-group" name="contact_number" label="{{ __('Contact Number') }} " ></x-mobile>

                <div class="zameen-form-group col-md-12">
                    <label class="zameen-form-label">{{ __('Bank Address') }}</label>
                    {{ Form::textarea('bank_address', '', ['class' => 'zameen-form-input', 'rows' => 3, 'placeholder' => __('Enter bank address')]) }}
                </div>
                @if (!$customFields->isEmpty())
                    <div class="col-md-12">
                        <div class="tab-pane fade show" id="tab-2" role="tabpanel">
                            @include('customFields.formBuilder')
                        </div>
                    </div>
                @endif
            </div>
        </div>
<div style="background: var(--zameen-background-light); border-top: 1px solid var(--zameen-border-light); padding: 1.5rem 2rem; display: flex; justify-content: flex-end; gap: 1rem; border-radius: 0 0 12px 12px;">
    <input type="button" value="{{ __('Cancel') }}" class="zameen-btn zameen-btn-outline" data-bs-dismiss="modal">
    <input type="submit" value="{{ __('Create') }}" class="zameen-btn zameen-btn-primary">
</div>
{{ Form::close() }}
