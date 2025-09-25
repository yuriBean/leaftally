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
    --zameen-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    --zameen-shadow-lg: 0 4px 20px rgba(0, 0, 0, 0.15);
    --zameen-radius: 8px;
    --zameen-radius-lg: 12px;
  }

  .zameen-container {
    background: #f8f9fa;
    min-height: 100vh;
    padding: 2rem 1rem;
  }

  .zameen-card {
    background: white;
    border-radius: var(--zameen-radius-lg);
    box-shadow: var(--zameen-shadow);
    overflow: hidden;
    max-width: 800px;
    margin: 0 auto;
  }

  .zameen-header {
    
    background: linear-gradient(135deg, var(--zameen-primary) 0%, var(--zameen-primary-light) 100%);
    padding: 2rem;
    color: white;
    text-align: center;
  }

  .zameen-header h2 {
    margin: 0;
    font-size: 1.75rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
  }

  .zameen-header p {;
    margin: 0;
    opacity: 0.9;
    font-size: 1rem;
  }

  .zameen-form-container {
    padding: 2.5rem;
  }

  .zameen-section {
    margin-bottom: 2.5rem;
  }

  .zameen-section-title {
    display: flex;
    align-items: center;
    font-size: 1.125rem;
    font-weight: 600;
    color: var(--zameen-gray-800);
    margin-bottom: 1.5rem;
    padding-bottom: 0.75rem;
    border-bottom: 2px solid var(--zameen-primary);
  }

  .zameen-section-icon {
    width: 20px;
    height: 20px;
    margin-right: 0.75rem;
    color: var(--zameen-primary);
  }

  .zameen-form-row {
    display: grid;
    grid-template-columns: 1fr;
    gap: 1.5rem;
    margin-bottom: 1.5rem;
  }

  .zameen-form-row.two-cols {
    grid-template-columns: 1fr 1fr;
  }

  .zameen-form-group {
    display: flex;
    flex-direction: column;
  }

  .zameen-label {
    font-weight: 500;
    color: var(--zameen-gray-700);
    margin-bottom: 0.5rem;
    font-size: 0.875rem;
  }

  .zameen-required {
    color: var(--zameen-danger);
    margin-left: 0.25rem;
  }

  .zameen-input {
    padding: 0.75rem 1rem;
    border: 1px solid var(--zameen-gray-300);
    border-radius: var(--zameen-radius);
    font-size: 1rem;
    transition: all 0.2s ease;
    background: white;
  }

  .zameen-input:focus {
    outline: none;
    border-color: var(--zameen-primary);
    box-shadow: 0 0 0 3px rgba(39, 167, 118, 0.1);
  }

  .zameen-input:hover {
    border-color: var(--zameen-gray-400);
  }

  .zameen-textarea {
    resize: vertical;
    min-height: 80px;
  }

  .zameen-toggle-container {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1rem;
    background: var(--zameen-gray-100);
    border-radius: var(--zameen-radius);
    border: 1px solid var(--zameen-gray-200);
  }

  .zameen-toggle {
    position: relative;
    width: 50px;
    height: 24px;
  }

  .zameen-toggle input {
    opacity: 0;
    width: 0;
    height: 0;
  }

  .zameen-toggle-slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: var(--zameen-gray-400);
    transition: 0.3s;
    border-radius: 24px;
  }

  .zameen-toggle-slider:before {
    position: absolute;
    content: "";
    height: 18px;
    width: 18px;
    left: 3px;
    bottom: 3px;
    background-color: white;
    transition: 0.3s;
    border-radius: 50%;
  }

  .zameen-toggle input:checked + .zameen-toggle-slider {
    background-color: var(--zameen-primary);
  }

  .zameen-toggle input:checked + .zameen-toggle-slider:before {
    transform: translateX(26px);
  }

  .zameen-btn {
    padding: 0.75rem 1.5rem;
    border-radius: var(--zameen-radius);
    font-weight: 500;
    font-size: 0.875rem;
    cursor: pointer;
    transition: all 0.2s ease;
    border: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
  }

  .zameen-btn-primary {
    background: var(--zameen-primary);
    color: white;
  }

  .zameen-btn-primary:hover {
    background: var(--zameen-primary-dark);
    transform: translateY(-1px);
    box-shadow: var(--zameen-shadow);
  }

  .zameen-btn-secondary {
    background: var(--zameen-gray-200);
    color: var(--zameen-gray-700);
  }

  .zameen-btn-secondary:hover {
    background: var(--zameen-gray-300);
  }

  .zameen-btn-outline {
    background: transparent;
    color: var(--zameen-gray-600);
    border: 1px solid var(--zameen-gray-300);
  }

  .zameen-btn-outline:hover {
    background: var(--zameen-gray-100);
    border-color: var(--zameen-gray-400);
  }

  .zameen-error {
    color: var(--zameen-danger);
    font-size: 0.75rem;
    margin-top: 0.25rem;
  }

  .zameen-footer {
    background: var(--zameen-gray-100);
    padding: 1.5rem 2.5rem;
    border-top: 1px solid var(--zameen-border-light);
    display: flex;
    justify-content: flex-end;
    gap: 1rem;
  }

  .ps_div {
    margin-top: 1rem;
    padding: 1rem;
    background: var(--zameen-gray-50);
    border-radius: var(--zameen-radius);
    border-left: 4px solid var(--zameen-primary);
  }

  @media (max-width: 768px) {
    .zameen-form-row.two-cols {
      grid-template-columns: 1fr;
    }
    
    .zameen-form-container {
      padding: 1.5rem;
    }
    
    .zameen-container {
      padding: 1rem;
    }
  }
</style>

<script src="{{ asset('js/unsaved.js') }}"></script>

{{ Form::open(['url' => 'customer', 'method' => 'post', 'class'=>'needs-validation','novalidate']) }}

<div class="" >
  <div class="">
    <div class="zameen-header">
      <h2>{{ __('Create New Customer') }}</h2>
      <p>{{ __('Add a new customer to your business') }}</p>
    </div>

    <div class="zameen-form-container">
      <!-- Basic Information Section -->
      <div class="zameen-section">
        <div class="zameen-section-title">
          <svg class="zameen-section-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
          </svg>
          {{ __('Basic Information') }}
        </div>

        <div class="zameen-form-row two-cols">
          <div class="zameen-form-group">
            <label class="zameen-label">
              {{ __('Customer Name') }}
              <span class="zameen-required">*</span>
            </label>
            {{ Form::text('name', null, [
              'class' => 'zameen-input',
              'placeholder' => __('Enter customer name'),
              'required' => 'required'
            ]) }}
            @error('name')
              <div class="zameen-error">{{ $message }}</div>
            @enderror
          </div>

          <div class="zameen-form-group">
            <label class="zameen-label">
              {{ __('Contact') }}
              <span class="zameen-required">*</span>
            </label>
            {{ Form::text('contact', null, [
              'class' => 'zameen-input',
              'placeholder' => __('Enter contact number'),
              'required' => 'required'
            ]) }}
            @error('contact')
              <div class="zameen-error">{{ $message }}</div>
            @enderror
          </div>
        </div>

        <div class="zameen-form-row two-cols">
          <div class="zameen-form-group">
            <label class="zameen-label">
              {{ __('Email Address') }}
              <span class="zameen-required">*</span>
            </label>
            {{ Form::email('email', null, [
              'class' => 'zameen-input',
              'placeholder' => __('Enter email address'),
              'required' => 'required'
            ]) }}
            @error('email')
              <div class="zameen-error">{{ $message }}</div>
            @enderror
          </div>

          <div class="zameen-form-group">
            <label class="zameen-label">{{ __('Tax Number') }}</label>
            {{ Form::text('tax_number', null, [
              'class' => 'zameen-input',
              'placeholder' => __('Enter tax number')
            ]) }}
            @error('tax_number')
              <div class="zameen-error">{{ $message }}</div>
            @enderror
          </div>
        </div>

        {!! Form::hidden('role', 'company') !!}

        <div class="zameen-form-row">
          <div class="zameen-form-group">
            <div class="zameen-toggle-container">
              <div>
                <label class="zameen-label" style="margin-bottom: 0;">{{ __('Enable Login Access') }}</label>
                <p style="font-size: 0.75rem; color: var(--zameen-gray-500); margin: 0;">Allow customer to login to portal</p>
              </div>
              <div class="zameen-toggle">
                <input type="checkbox" name="password_switch" id="password_switch" value="on">
                <label class="zameen-toggle-slider" for="password_switch"></label>
              </div>
            </div>
          </div>
        </div>

        <div class="ps_div d-none">
          <div class="zameen-form-row two-cols">
            <div class="zameen-form-group">
              <label class="zameen-label">{{ __('Username') }}</label>
              {{ Form::text('user_name', null, [
                'class' => 'zameen-input',
                'placeholder' => __('Enter username')
              ]) }}
              @error('user_name')
                <div class="zameen-error">{{ $message }}</div>
              @enderror
            </div>

            <div class="zameen-form-group">
              <label class="zameen-label">{{ __('Password') }}</label>
              {{ Form::password('password', [
                'class' => 'zameen-input',
                'placeholder' => __('Enter password'),
                'minlength' => '6'
              ]) }}
              @error('password')
                <div class="zameen-error">{{ $message }}</div>
              @enderror
            </div>
          </div>
        </div>

        @if (!$customFields->isEmpty())
          <div class="zameen-custom-fields">
            @include('customFields.formBuilder')
          </div>
        @endif
      </div>
      <!-- Billing Address Section -->
      <div class="zameen-section">
        <div class="zameen-section-title">
          <svg class="zameen-section-icon" fill="currentColor" viewBox="0 0 16 16">
            <path fill-rule="evenodd" d="M3.1 11.2a.5.5 0 0 1 .4-.2H6a.5.5 0 0 1 0 1H3.75L1.5 15h13l-2.25-3H10a.5.5 0 0 1 0-1h2.5a.5.5 0 0 1 .4.2l3 4a.5.5 0 0 1-.4.8H.5a.5.5 0 0 1-.4-.8z"/>
            <path fill-rule="evenodd" d="M8 1a3 3 0 1 0 0 6 3 3 0 0 0 0-6M4 4a4 4 0 1 1 4.5 3.969V13.5a.5.5 0 0 1-1 0V7.97A4 4 0 0 1 4 3.999z"/>
          </svg>
          {{ __('Billing Address') }}
        </div>

        <div class="zameen-form-row two-cols">
          <div class="zameen-form-group">
            <label class="zameen-label">{{ __('Name') }}</label>
            {{ Form::text('billing_name', null, [
              'class' => 'zameen-input',
              'placeholder' => __('Enter billing name')
            ]) }}
          </div>

          <div class="zameen-form-group">
            <label class="zameen-label">{{ __('Phone') }}</label>
            {{ Form::text('billing_phone', null, [
              'class' => 'zameen-input',
              'placeholder' => __('Enter phone number')
            ]) }}
          </div>
        </div>

        <div class="zameen-form-row">
          <div class="zameen-form-group">
            <label class="zameen-label">{{ __('Address') }}</label>
            {{ Form::textarea('billing_address', null, [
              'class' => 'zameen-input zameen-textarea',
              'rows' => '3',
              'placeholder' => __('Enter billing address')
            ]) }}
          </div>
        </div>

        <div class="zameen-form-row two-cols">
          <div class="zameen-form-group">
            <label class="zameen-label">{{ __('City') }}</label>
            {{ Form::text('billing_city', null, [
              'class' => 'zameen-input',
              'placeholder' => __('Enter city')
            ]) }}
          </div>

          <div class="zameen-form-group">
            <label class="zameen-label">{{ __('State') }}</label>
            {{ Form::text('billing_state', null, [
              'class' => 'zameen-input',
              'placeholder' => __('Enter state')
            ]) }}
          </div>
        </div>

        <div class="zameen-form-row two-cols">
          <div class="zameen-form-group">
            <label class="zameen-label">{{ __('Country') }}</label>
            {{ Form::text('billing_country', null, [
              'class' => 'zameen-input',
              'placeholder' => __('Enter country')
            ]) }}
          </div>

          <div class="zameen-form-group">
            <label class="zameen-label">{{ __('Zip Code') }}</label>
            {{ Form::text('billing_zip', null, [
              'class' => 'zameen-input',
              'placeholder' => __('Enter zip code')
            ]) }}
          </div>
        </div>

        <div class="zameen-form-row">
          <div style="text-align: right;">
            <button type="button" id="billing_data" class="zameen-btn zameen-btn-secondary">
              <svg style="width: 16px; height: 16px; margin-right: 0.5rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
              </svg>
              {{ __('Copy to Shipping') }}
            </button>
          </div>
        </div>
      </div>

      <!-- Shipping Address Section -->
      <div class="zameen-section">
        <div class="zameen-section-title">
          <svg class="zameen-section-icon" fill="currentColor" viewBox="0 0 24 24">
            <path d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125z"/>
          </svg>
          {{ __('Shipping Address') }}
        </div>

        <div class="zameen-form-row two-cols">
          <div class="zameen-form-group">
            <label class="zameen-label">{{ __('Name') }}</label>
            {{ Form::text('shipping_name', null, [
              'class' => 'zameen-input',
              'placeholder' => __('Enter shipping name')
            ]) }}
          </div>

          <div class="zameen-form-group">
            <label class="zameen-label">{{ __('Phone') }}</label>
            {{ Form::text('shipping_phone', null, [
              'class' => 'zameen-input',
              'placeholder' => __('Enter phone number')
            ]) }}
          </div>
        </div>

        <div class="zameen-form-row">
          <div class="zameen-form-group">
            <label class="zameen-label">{{ __('Address') }}</label>
            {{ Form::textarea('shipping_address', null, [
              'class' => 'zameen-input zameen-textarea',
              'rows' => '3',
              'placeholder' => __('Enter shipping address')
            ]) }}
          </div>
        </div>

        <div class="zameen-form-row two-cols">
          <div class="zameen-form-group">
            <label class="zameen-label">{{ __('City') }}</label>
            {{ Form::text('shipping_city', null, [
              'class' => 'zameen-input',
              'placeholder' => __('Enter city')
            ]) }}
          </div>

          <div class="zameen-form-group">
            <label class="zameen-label">{{ __('State') }}</label>
            {{ Form::text('shipping_state', null, [
              'class' => 'zameen-input',
              'placeholder' => __('Enter state')
            ]) }}
          </div>
        </div>

        <div class="zameen-form-row two-cols">
          <div class="zameen-form-group">
            <label class="zameen-label">{{ __('Country') }}</label>
            {{ Form::text('shipping_country', null, [
              'class' => 'zameen-input',
              'placeholder' => __('Enter country')
            ]) }}
          </div>

          <div class="zameen-form-group">
            <label class="zameen-label">{{ __('Zip Code') }}</label>
            {{ Form::text('shipping_zip', null, [
              'class' => 'zameen-input',
              'placeholder' => __('Enter zip code')
            ]) }}
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="zameen-footer">
  <button type="button" class="zameen-btn zameen-btn-outline" data-bs-dismiss="modal">
    <svg style="width: 16px; height: 16px; margin-right: 0.5rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
    </svg>
    {{ __('Cancel') }}
  </button>
  <button type="submit" class="zameen-btn zameen-btn-primary">
    <svg style="width: 16px; height: 16px; margin-right: 0.5rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
    </svg>
    {{ __('Create Customer') }}
  </button>
</div>

{{ Form::close() }}

@push('script-page')
<script>
  $(document).on('click', '.login_enable', function() {
    setTimeout(function() {
      $('.modal-body').append($('<input>', {
        type: 'hidden',
        val: 'true',
        name: 'login_enable'
      }));
    }, 2000);
  });

  $('#password_switch').on('change', function() {
    if ($(this).is(':checked')) {
      $('.ps_div').removeClass('d-none');
    } else {
      $('.ps_div').addClass('d-none');
    }
  });

  $('#billing_data').on('click', function() {
    $('input[name="shipping_name"]').val($('input[name="billing_name"]').val());
    $('input[name="shipping_phone"]').val($('input[name="billing_phone"]').val());
    $('textarea[name="shipping_address"]').val($('textarea[name="billing_address"]').val());
    $('input[name="shipping_city"]').val($('input[name="billing_city"]').val());
    $('input[name="shipping_state"]').val($('input[name="billing_state"]').val());
    $('input[name="shipping_country"]').val($('input[name="billing_country"]').val());
    $('input[name="shipping_zip"]').val($('input[name="billing_zip"]').val());
  });
</script>
@endpush
