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

  .zameen-form-section {
    margin-bottom: 2rem;
  }

  .zameen-section-title {
    font-size: 1.125rem;
    font-weight: 600;
    color: var(--zameen-text);
    margin-bottom: 1.5rem;
    padding-bottom: 0.5rem;
    border-bottom: 2px solid var(--zameen-border-light);
    display: flex;
    align-items: center;
  }

  .zameen-form-group {
    margin-bottom: 1.25rem;
  }

  .zameen-form-label {
    display: block;
    font-weight: 500;
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
    box-shadow: 0 0 0 3px rgba(0, 185, 141, 0.1);
  }

  .zameen-form-input::placeholder {
    color: var(--zameen-text-light);
  }

  .zameen-form-error {
    color: #ef4444;
    font-size: 0.75rem;
    margin-top: 0.25rem;
  }

  .zameen-checkbox {
    width: 18px;
    height: 18px;
    border: 2px solid var(--zameen-border);
    border-radius: 4px;
    background: white;
    cursor: pointer;
    transition: all 0.2s ease;
    position: relative;
    margin-right: 0.5rem;
  }

  .zameen-checkbox:checked {
    background: var(--zameen-primary);
    border-color: var(--zameen-primary);
  }

  .zameen-checkbox:checked::after {
    content: '✓';
    position: absolute;
    top: -1px;
    left: 2px;
    color: white;
    font-size: 12px;
    font-weight: bold;
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
    min-width: 120px;
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

  .zameen-form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
  }

  @media (max-width: 768px) {
    .zameen-form-row {
      grid-template-columns: 1fr;
    }
  }
</style>

<script src="{{ asset('js/unsaved.js') }}"></script>

{{ Form::open(['url' => 'vender', 'method' => 'post', 'class'=>'needs-validation','novalidate']) }}

<div>
  
  <div style="background: linear-gradient(135deg,#007c38  0%, #10b981 100%); padding: 1.75rem 2rem 1.25rem;">
    <div style="color: white; margin-bottom: 0.5rem; text-align:center">
      <h4 style="margin: 0; font-weight: 600; font-size: 1.5rem; color: white;">{{ __('Create New Vendor') }}</h4>
      <p style="margin: 0; opacity: 0.9; font-size: 0.875rem;">{{ __('Add a new vendor to your business') }}</p>
    </div>
  </div>

  <div style="padding: 2rem; background: white; margin: 0 1.5rem; border-radius: 0 0 12px 12px;">
    <div ">

      <div class="zameen-form-section">
        <h6 class="zameen-section-title">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: inline; margin-right: 8px;">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
          </svg>
          {{ __('Basic Information') }}
        </h6>

        <div class="zameen-form-group">
          <label class="zameen-form-label">
            {{ __('Vendor Name') }}
            <span style="color: #ef4444; margin-left: 4px;">*</span>
          </label>
          {{ Form::text('name', null, [
            'class' => 'zameen-form-input',
            'placeholder' => __('Enter vendor name'),
            'required' => 'required'
          ]) }}
          @error('name')
            <div class="zameen-form-error">{{ $message }}</div>
          @enderror
        </div>

        <div class="zameen-form-group">
          <label class="zameen-form-label">
            {{ __('Contact') }}
            <span style="color: #ef4444; margin-left: 4px;">*</span>
          </label>
          {{ Form::text('contact', null, [
            'class' => 'zameen-form-input',
            'placeholder' => __('Enter contact number'),
            'required' => 'required'
          ]) }}
          @error('contact')
            <div class="zameen-form-error">{{ $message }}</div>
          @enderror
        </div>

        <div class="zameen-form-group">
          <label class="zameen-form-label">
            {{ __('Email Address') }}
            <span style="color: #ef4444; margin-left: 4px;">*</span>
          </label>
          {{ Form::email('email', null, [
            'class' => 'zameen-form-input',
            'placeholder' => __('Enter email address'),
            'required' => 'required'
          ]) }}
          @error('email')
            <div class="zameen-form-error">{{ $message }}</div>
          @enderror
        </div>

        <div class="zameen-form-group">
          <label class="zameen-form-label">{{ __('Tax Number') }}</label>
          {{ Form::text('tax_number', null, [
            'class' => 'zameen-form-input',
            'placeholder' => __('Enter tax number')
          ]) }}
          @error('tax_number')
            <div class="zameen-form-error">{{ $message }}</div>
          @enderror
        </div>

        {!! Form::hidden('role', 'company') !!}

        <div class="zameen-form-group">
          <div class="zameen-toggle-group">
            <label class="zameen-form-label">{{ __('Enable Login Access') }}</label>
            <div class="zameen-toggle-wrapper">
              <input type="checkbox" name="password_switch" class="zameen-toggle-input" value="on" id="password_switch">
              <label class="zameen-toggle-label" for="password_switch">
                <span class="zameen-toggle-slider"></span>
              </label>
            </div>
          </div>
        </div>

                </div>

        <div class="ps_div d-none">
          <div class="zameen-form-group">
            <label class="zameen-form-label">{{ __('Username') }}</label>
            {{ Form::text('user_name', null, [
              'class' => 'zameen-form-input',
              'placeholder' => __('Enter username')
            ]) }}
            @error('user_name')
              <div class="zameen-form-error">{{ $message }}</div>
            @enderror
          </div>

          <div class="zameen-form-group">
            <label class="zameen-form-label">{{ __('Password') }}</label>
            {{ Form::password('password', [
              'class' => 'zameen-form-input',
              'placeholder' => __('Enter password'),
              'minlength' => '6'
            ]) }}
            @error('password')
              <div class="zameen-form-error">{{ $message }}</div>
            @enderror
          </div>
        </div>

        @if (!$customFields->isEmpty())
          <div class="zameen-custom-fields">
            @include('customFields.formBuilder')
          </div>
        @endif
      </div>

      <div class="zameen-form-section">
        <h6 class="zameen-section-title">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-pin-map" viewBox="0 0 16 16" style="display: inline; margin-right: 8px;">
            <path fill-rule="evenodd" d="M3.1 11.2a.5.5 0 0 1 .4-.2H6a.5.5 0 0 1 0 1H3.75L1.5 15h13l-2.25-3H10a.5.5 0 0 1 0-1h2.5a.5.5 0 0 1 .4.2l3 4a.5.5 0 0 1-.4.8H.5a.5.5 0 0 1-.4-.8z"/>
            <path fill-rule="evenodd" d="M8 1a3 3 0 1 0 0 6 3 3 0 0 0 0-6M4 4a4 4 0 1 1 4.5 3.969V13.5a.5.5 0 0 1-1 0V7.97A4 4 0 0 1 4 3.999z"/>
          </svg>
          {{ __('Billing Address') }}
        </h6>

        <div style="display: flex; flex-direction: column; gap: 1rem;">
          <div class="zameen-form-group">
            <label class="zameen-form-label">{{ __('Name') }}</label>
            {{ Form::text('billing_name', null, [
              'class' => 'zameen-form-input',
              'placeholder' => __('Enter billing name')
            ]) }}
          </div>
        </div>

      
        <div style="display: flex; flex-direction: column; gap: 1rem;">
  
          <div class="zameen-form-group">
            <label class="zameen-form-label">{{ __('Contact') }}</label>
            {{ Form::text('billing_phone', null, [
              'class' => 'zameen-form-input',
              'placeholder' => __('Enter contact number')
            ]) }}
          </div>
        
          <div class="zameen-form-group">
            <label class="zameen-form-label">{{ __('Address') }}</label>
            {{ Form::textarea('billing_address', null, [
              'class' => 'zameen-form-input',
              'rows' => 3,
              'placeholder' => __('Enter address')
            ]) }}
          </div>
        
          <div class="zameen-form-group">
            <label class="zameen-form-label">{{ __('City') }}</label>
            {{ Form::text('billing_city', null, [
              'class' => 'zameen-form-input',
              'placeholder' => __('Enter city')
            ]) }}
          </div>
        
          <div class="zameen-form-group">
            <label class="zameen-form-label">{{ __('State') }}</label>
            {{ Form::text('billing_state', null, [
              'class' => 'zameen-form-input',
              'placeholder' => __('Enter state')
            ]) }}
          </div>
        
          <div class="zameen-form-group">
            <label class="zameen-form-label">{{ __('Country') }}</label>
            {{ Form::text('billing_country', null, [
              'class' => 'zameen-form-input',
              'placeholder' => __('Enter country')
            ]) }}
          </div>
        
          <div class="zameen-form-group">
            <label class="zameen-form-label">{{ __('Zip Code') }}</label>
            {{ Form::text('billing_zip', null, [
              'class' => 'zameen-form-input',
              'placeholder' => __('Enter zip code')
            ]) }}
          </div>
        
        </div>
        
      </div>

      @if(App\Models\Utility::getValByName('shipping_display')=='on')
      
      <div class="zameen-form-section">
        <h6 class="zameen-section-title">
              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-pin-map" viewBox="0 0 16 16" style="display: inline; margin-right: 8px;">
                <path fill-rule="evenodd" d="M3.1 11.2a.5.5 0 0 1 .4-.2H6a.5.5 0 0 1 0 1H3.75L1.5 15h13l-2.25-3H10a.5.5 0 0 1 0-1h2.5a.5.5 0 0 1 .4.2l3 4a.5.5 0 0 1-.4.8H.5a.5.5 0 0 1-.4-.8z"/>
                <path fill-rule="evenodd" d="M8 1a3 3 0 1 0 0 6 3 3 0 0 0 0-6M4 4a4 4 0 1 1 4.5 3.969V13.5a.5.5 0 0 1-1 0V7.97A4 4 0 0 1 4 3.999z"/>
               </svg>
               {{__('Shipping Address')}}
            </h6>
            <div style="text-align: right; margin-top: 1rem;">
              <input type="button" id="billing_data" value="{{__('Shipping Same As Billing')}}" class="btn btn-primary">
           </div>

            <div style="display: flex; flex-direction: column; gap: 1rem;">

              <div class="zameen-form-group">
                <label class="zameen-form-label">{{ __('Name') }}</label>
                {{ Form::text('shipping_name', null, [
                  'class' => 'zameen-form-input',
                  'placeholder' => __('Enter shipping name')
                ]) }}
              </div>
            
              <div class="zameen-form-group">
                <label class="zameen-form-label">{{ __('Contact') }}</label>
                {{ Form::text('shipping_phone', null, [
                  'class' => 'zameen-form-input',
                  'placeholder' => __('Enter contact number')
                ]) }}
              </div>
            
              <div class="zameen-form-group">
                <label class="zameen-form-label">{{ __('Address') }}</label>
                {{ Form::textarea('shipping_address', null, [
                  'class' => 'zameen-form-input',
                  'rows' => 3,
                  'placeholder' => __('Enter address')
                ]) }}
              </div>
            
              <div class="zameen-form-group">
                <label class="zameen-form-label">{{ __('City') }}</label>
                {{ Form::text('shipping_city', null, [
                  'class' => 'zameen-form-input',
                  'placeholder' => __('Enter city')
                ]) }}
              </div>
            
              <div class="zameen-form-group">
                <label class="zameen-form-label">{{ __('State') }}</label>
                {{ Form::text('shipping_state', null, [
                  'class' => 'zameen-form-input',
                  'placeholder' => __('Enter state')
                ]) }}
              </div>
            
              <div class="zameen-form-group">
                <label class="zameen-form-label">{{ __('Country') }}</label>
                {{ Form::text('shipping_country', null, [
                  'class' => 'zameen-form-input',
                  'placeholder' => __('Enter country')
                ]) }}
              </div>
            
              <div class="zameen-form-group">
                <label class="zameen-form-label">{{ __('Zip Code') }}</label>
                {{ Form::text('shipping_zip', null, [
                  'class' => 'zameen-form-input',
                  'placeholder' => __('Enter zip code')
                ]) }}
              </div>
            
            </div>
                  </div>

      
      @endif
  </div>
</div>

<div class="modal-footer" style="background: var(--zameen-background-light); border-top: 1px solid var(--zameen-border-light); padding: 1.5rem 2rem; display: flex; justify-content: flex-end; gap: 1rem;">
  <button type="button" class="zameen-btn zameen-btn-outline" data-bs-dismiss="modal">
    {{ __('Cancel') }}
  </button>
  <button type="submit" class="zameen-btn zameen-btn-primary">
    {{ __('Create Vendor') }}
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
