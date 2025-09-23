<script src="{{ asset('js/unsaved.js') }}"></script>

{{ Form::open(['url' => 'vender', 'method' => 'post', 'class'=>'needs-validation','novalidate']) }}

<div class="modal-body" style="background: var(--zameen-background-section); padding: 1.5rem;">
  
  <div style="background: linear-gradient(135deg, #00b98d 0%, #00d4a3 100%); padding: 1.75rem 2rem 1.25rem; border-radius: 12px 12px 0 0; margin: 0 1.5rem;">
    <div style="color: white; margin-bottom: 0.5rem;">
      <h4 style="margin: 0; font-weight: 600; font-size: 1.5rem; color: white;">{{ __('Create New Vendor') }}</h4>
      <p style="margin: 0; opacity: 0.9; font-size: 0.875rem;">{{ __('Add a new vendor to your business') }}</p>
    </div>
  </div>

  <div style="padding: 2rem; background: white; margin: 0 1.5rem; border-radius: 0 0 12px 12px;">
    <div style="display: flex; flex-direction: column; gap: 1.5rem; max-width: 720px; margin: 0 auto; padding: 1.5rem;">

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

   <div style="display: flex; flex-direction: column; gap: 1.5rem;">
      
      <div class="bg-white rounded-[8px] mb-6 border border-[#E5E7EB] shadow-sm overflow-hidden">
         <div class="heading-cstm-form">
            <h6 class="mb-0 flex items-center gap-2">
               <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-pin-map" viewBox="0 0 16 16">
                  <path fill-rule="evenodd" d="M3.1 11.2a.5.5 0 0 1 .4-.2H6a.5.5 0 0 1 0 1H3.75L1.5 15h13l-2.25-3H10a.5.5 0 0 1 0-1h2.5a.5.5 0 0 1 .4.2l3 4a.5.5 0 0 1-.4.8H.5a.5.5 0 0 1-.4-.8z"></path>
                  <path fill-rule="evenodd" d="M8 1a3 3 0 1 0 0 6 3 3 0 0 0 0-6M4 4a4 4 0 1 1 4.5 3.969V13.5a.5.5 0 0 1-1 0V7.97A4 4 0 0 1 4 3.999z"></path>
               </svg>
               {{__('BIlling Address')}}
            </h6>
         </div>
         <div style="padding: 1.5rem; display: flex; flex-direction: column; gap: 1rem;">
            <div class="form-group">
               {{Form::label('billing_name',__('Name'),array('class'=>'form-label')) }}
               <div class="form-icon-user">
                  {{Form::text('billing_name',null,array('class'=>'form-control'))}}
               </div>
            </div>

            <div class="form-group">
               {{Form::label('billing_phone',__('Contact'),array('class'=>'form-label')) }}
               <div class="form-icon-user">
                  {{Form::text('billing_phone',null,array('class'=>'form-control'))}}
               </div>
            </div>

            <div class="form-group">
               {{Form::label('billing_address',__('Address'),array('class'=>'form-label')) }}
               <div class="input-group">
                  {{Form::textarea('billing_address',null,array('class'=>'form-control','rows'=>3))}}
               </div>
            </div>

            <div class="form-group">
               {{Form::label('billing_city',__('City'),array('class'=>'form-label')) }}
               <div class="form-icon-user">
                  {{Form::text('billing_city',null,array('class'=>'form-control'))}}
               </div>
            </div>

            <div class="form-group">
               {{Form::label('billing_state',__('State'),array('class'=>'form-label')) }}
               <div class="form-icon-user">
                  {{Form::text('billing_state',null,array('class'=>'form-control'))}}
               </div>
            </div>

            <div class="form-group">
               {{Form::label('billing_country',__('Country'),array('class'=>'form-label')) }}
               <div class="form-icon-user">
                  {{Form::text('billing_country',null,array('class'=>'form-control'))}}
               </div>
            </div>

            <div class="form-group">
               {{Form::label('billing_zip',__('Zip Code'),array('class'=>'form-label')) }}
               <div class="form-icon-user">
                  {{Form::text('billing_zip',null,array('class'=>'form-control','placeholder'=>__('')))}}
               </div>
            </div>
         </div>
      </div>

      @if(App\Models\Utility::getValByName('shipping_display')=='on')
      
      <div class="bg-white rounded-[8px] mb-6 border border-[#E5E7EB] shadow-sm overflow-hidden">
         <div class="heading-cstm-form">
            <h6 class="mb-0 flex items-center gap-2">
               <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-pin-map" viewBox="0 0 16 16">
                  <path fill-rule="evenodd" d="M3.1 11.2a.5.5 0 0 1 .4-.2H6a.5.5 0 0 1 0 1H3.75L1.5 15h13l-2.25-3H10a.5.5 0 0 1 0-1h2.5a.5.5 0 0 1 .4.2l3 4a.5.5 0 0 1-.4.8H.5a.5.5 0 0 1-.4-.8z"></path>
                  <path fill-rule="evenodd" d="M8 1a3 3 0 1 0 0 6 3 3 0 0 0 0-6M4 4a4 4 0 1 1 4.5 3.969V13.5a.5.5 0 0 1-1 0V7.97A4 4 0 0 1 4 3.999z"></path>
               </svg>
               {{__('Shipping Address')}}
            </h6>
         </div>
         <div style="padding: 1.5rem; display: flex; flex-direction: column; gap: 1rem;">
            <div class="form-group">
               {{Form::label('shipping_name',__('Name'),array('class'=>'form-label')) }}
               <div class="form-icon-user">
                  {{Form::text('shipping_name',null,array('class'=>'form-control'))}}
               </div>
            </div>

            <div class="form-group">
               {{Form::label('shipping_phone',__('Contact'),array('class'=>'form-label')) }}
               <div class="form-icon-user">
                  {{Form::text('shipping_phone',null,array('class'=>'form-control'))}}
               </div>
            </div>

            <div class="form-group">
               {{Form::label('shipping_address',__('Address'),array('class'=>'form-label')) }}
               <div class="input-group">
                  {{Form::textarea('shipping_address',null,array('class'=>'form-control','rows'=>3))}}
               </div>
            </div>

            <div class="form-group">
               {{Form::label('shipping_city',__('City'),array('class'=>'form-label')) }}
               <div class="form-icon-user">
                  {{Form::text('shipping_city',null,array('class'=>'form-control'))}}
               </div>
            </div>

            <div class="form-group">
               {{Form::label('shipping_state',__('State'),array('class'=>'form-label')) }}
               <div class="form-icon-user">
                  {{Form::text('shipping_state',null,array('class'=>'form-control'))}}
               </div>
            </div>

            <div class="form-group">
               {{Form::label('shipping_country',__('Country'),array('class'=>'form-label')) }}
               <div class="form-icon-user">
                  {{Form::text('shipping_country',null,array('class'=>'form-control'))}}
               </div>
            </div>

            <div class="form-group">
               {{Form::label('shipping_zip',__('Zip Code'),array('class'=>'form-label')) }}
               <div class="form-icon-user">
                  {{Form::text('shipping_zip',null,array('class'=>'form-control','placeholder'=>__('')))}}
               </div>
            </div>
         </div>
      </div>

      <div style="text-align: right; margin-top: 1rem;">
         <input type="button" id="billing_data" value="{{__('Shipping Same As Billing')}}" class="btn btn-primary">
      </div>
      @endif
   </div>
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
