<script src="{{ asset('js/unsaved.js') }}"></script>

{{ Form::model($customer, [
    'route'  => ['customer.update', $customer->id],
    'method' => 'PUT',
    'class'  => 'needs-validation',
    'novalidate'
]) }}

<div class="modal-body" style="background: var(--zameen-background-section); padding: 1.5rem;">
  
  <div style="background: linear-gradient(135deg, #00b98d 0%, #00d4a3 100%); padding: 1.75rem 2rem 1.25rem; border-radius: 12px 12px 0 0; margin: 0 1.5rem;">
    <div style="color: white; margin-bottom: 0.5rem;">
      <h4 style="margin: 0; font-weight: 600; font-size: 1.5rem; color: white;">{{ __('Edit Customer') }}</h4>
      <p style="margin: 0; opacity: 0.9; font-size: 0.875rem;">{{ __('Update customer information') }}</p>
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
            {{ __('Customer Name') }}
            <span style="color: #ef4444; margin-left: 4px;">*</span>
          </label>
          {{ Form::text('name', null, [
            'class' => 'zameen-form-input',
            'placeholder' => __('Enter customer name'),
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
                  @if($customer->is_enable_login == 1)
         <div class="col-lg-4 col-md-4 col-sm-6">
            <div class="form-group">
               {{ Form::label('user_name', __('Username'), ['class' => 'form-label']) }}
               <div class="form-icon-user">
                  {{ Form::text('user_name', $customer->user_name, [
                     'class' => 'form-control',
                     'disabled' => true
                  ]) }}
               </div>
            </div>
         </div>
         @endif
      </div>
   </div>
   <div class="row">
      <div class="col-md-6">
         
         <div class="bg-white rounded-[8px] border border-[#E5E7EB] shadow-sm overflow-hidden">
            <div class="heading-cstm-form">
               <h6 class="mb-0 flex items-center gap-2">
                 <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-pin-map" viewBox="0 0 16 16">
                  <path fill-rule="evenodd" d="M3.1 11.2a.5.5 0 0 1 .4-.2H6a.5.5 0 0 1 0 1H3.75L1.5 15h13l-2.25-3H10a.5.5 0 0 1 0-1h2.5a.5.5 0 0 1 .4.2l3 4a.5.5 0 0 1-.4.8H.5a.5.5 0 0 1-.4-.8z"/>
                  <path fill-rule="evenodd" d="M8 1a3 3 0 1 0 0 6 3 3 0 0 0 0-6M4 4a4 4 0 1 1 4.5 3.969V13.5a.5.5 0 0 1-1 0V7.97A4 4 0 0 1 4 3.999z"/>
                </svg>
                  {{ __('Billing Address') }}
               </h6>
            </div>
            <div class="row p-6">
               <div class="col-lg-6 col-md-6 col-sm-6">
                  <div class="form-group">
                     {{ Form::label('billing_name', __('Name'), ['class' => 'form-label']) }}
                     <div class="form-icon-user">
                        {{ Form::text('billing_name', null, ['class' => 'form-control']) }}
                     </div>
                  </div>
               </div>
               <x-mobile div-class="col-md-6 " name="billing_phone" label="{{ __('Phone') }}"></x-mobile>
               <div class="col-md-12">
                  <div class="form-group">
                     {{ Form::label('billing_address', __('Address'), ['class' => 'form-label']) }}
                     <div class="input-group">
                        {{ Form::textarea('billing_address', null, ['class' => 'form-control', 'rows' => 3]) }}
                     </div>
                  </div>
               </div>
               <div class="col-lg-6 col-md-6 col-sm-6">
                  <div class="form-group">
                     {{ Form::label('billing_city', __('City'), ['class' => 'form-label']) }}
                     <div class="form-icon-user">
                        {{ Form::text('billing_city', null, ['class' => 'form-control']) }}
                     </div>
                  </div>
               </div>
               <div class="col-lg-6 col-md-6 col-sm-6">
                  <div class="form-group">
                     {{ Form::label('billing_state', __('State'), ['class' => 'form-label']) }}
                     <div class="form-icon-user">
                        {{ Form::text('billing_state', null, ['class' => 'form-control']) }}
                     </div>
                  </div>
               </div>
               <div class="col-lg-6 col-md-6 col-sm-6">
                  <div class="form-group">
                     {{ Form::label('billing_country', __('Country'), ['class' => 'form-label']) }}
                     <div class="form-icon-user">
                        {{ Form::text('billing_country', null, ['class' => 'form-control']) }}
                     </div>
                  </div>
               </div>
               <div class="col-lg-6 col-md-6 col-sm-6">
                  <div class="form-group">
                     {{ Form::label('billing_zip', __('Zip Code'), ['class' => 'form-label']) }}
                     <div class="form-icon-user">
                        {{ Form::text('billing_zip', null, ['class' => 'form-control']) }}
                     </div>
                  </div>
               </div>
               <div class="col-md-12 text-end">
                  <input type="button" id="billing_data" value="Shipping Same As Billing" class="btn py-[6px] px-[10px] btn bg-[#007C38] text-white hover:bg-[#005f2a]">
               </div>
            </div>
         </div>
      </div>
      <div class="col-md-6">
         
         <div class="bg-white rounded-[8px] border border-[#E5E7EB] shadow-sm overflow-hidden">
            <div class="heading-cstm-form">
               <h6 class="mb-0 flex items-center gap-2">
                  <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-pin-map" viewBox="0 0 16 16">
                      <path fill-rule="evenodd" d="M3.1 11.2a.5.5 0 0 1 .4-.2H6a.5.5 0 0 1 0 1H3.75L1.5 15h13l-2.25-3H10a.5.5 0 0 1 0-1h2.5a.5.5 0 0 1 .4.2l3 4a.5.5 0 0 1-.4.8H.5a.5.5 0 0 1-.4-.8z"/>
                      <path fill-rule="evenodd" d="M8 1a3 3 0 1 0 0 6 3 3 0 0 0 0-6M4 4a4 4 0 1 1 4.5 3.969V13.5a.5.5 0 0 1-1 0V7.97A4 4 0 0 1 4 3.999z"/>
                    </svg>
                  {{ __('Shipping Address') }}
               </h6>
            </div>
            <div class="row p-6">
               <div class="col-lg-6 col-md-6 col-sm-6">
                  <div class="form-group">
                     {{ Form::label('shipping_name', __('Name'), ['class' => 'form-label']) }}
                     <div class="form-icon-user">
                        {{ Form::text('shipping_name', null, ['class' => 'form-control']) }}
                     </div>
                  </div>
               </div>
               <x-mobile div-class="col-md-6 " name="shipping_phone" label="{{ __('Phone') }}"></x-mobile>
               <div class="col-md-12">
                  <div class="form-group">
                     {{ Form::label('shipping_address', __('Address'), ['class' => 'form-label']) }}
                     <div class="input-group">
                        {{ Form::textarea('shipping_address', null, ['class' => 'form-control', 'rows' => 3]) }}
                     </div>
                  </div>
               </div>
               <div class="col-lg-6 col-md-6 col-sm-6">
                  <div class="form-group">
                     {{ Form::label('shipping_city', __('City'), ['class' => 'form-label']) }}
                     <div class="form-icon-user">
                        {{ Form::text('shipping_city', null, ['class' => 'form-control']) }}
                     </div>
                  </div>
               </div>
               <div class="col-lg-6 col-md-6 col-sm-6">
                  <div class="form-group">
                     {{ Form::label('shipping_state', __('State'), ['class' => 'form-label']) }}
                     <div class="form-icon-user">
                        {{ Form::text('shipping_state', null, ['class' => 'form-control']) }}
                     </div>
                  </div>
               </div>
               <div class="col-lg-6 col-md-6 col-sm-6">
                  <div class="form-group">
                     {{ Form::label('shipping_country', __('Country'), ['class' => 'form-label']) }}
                     <div class="form-icon-user">
                        {{ Form::text('shipping_country', null, ['class' => 'form-control']) }}
                     </div>
                  </div>
               </div>
               <div class="col-lg-6 col-md-6 col-sm-6">
                  <div class="form-group">
                     {{ Form::label('shipping_zip', __('Zip Code'), ['class' => 'form-label']) }}
                     <div class="form-icon-user">
                        {{ Form::text('shipping_zip', null, ['class' => 'form-control']) }}
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
<div class="modal-footer border-t border-[#E5E5E5] bg-[#FAFAFA] px-6 py-4 flex justify-end gap-3">
   <input type="button" value="{{ __('Cancel') }}" class="btn py-[6px] px-[10px] btn text-[#007C38] border-[#007C38] hover:bg-[#007C38] hover:text-white" data-bs-dismiss="modal">
   <input type="submit" value="{{ __('Update') }}" class="btn py-[6px] px-[10px] btn bg-[#007C38] text-white hover:bg-[#005f2a]">
</div>
{{ Form::close() }}
