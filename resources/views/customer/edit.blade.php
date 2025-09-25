<style>
   .zameen-user-container { background: transparent !important; padding: 0; max-height: 90vh; overflow-y: auto; }
   .zameen-user-card { background: transparent !important; border-radius: 0; box-shadow: none !important; width: 100%; max-width: none; margin: 0; max-height: 90vh; display: flex; flex-direction: column; }
      form.needs-validation { width: 100%; }
   .zameen-user-header { background: linear-gradient(135deg, #007c38 0%, #10b981 100%); padding: 1.5rem 2rem; color: white; text-align: center; flex-shrink: 0; border-radius: 12px 12px 0 0; }
   .zameen-user-header h2 { margin: 0; font-size: 1.5rem; font-weight: 600; }
   .zameen-user-header p { margin: 0; opacity: 0.9; font-size: 0.875rem; }
   .zameen-form-container { padding: 1.25rem 1.5rem; overflow-y: auto; flex: 1; }
   .zameen-form-group { margin-bottom: 1rem; }
   .zameen-label { font-weight: 500; color: #495057; margin-bottom: 0.5rem; font-size: 0.95rem; display: block; }
   .zameen-required { color: #f44336; margin-left: 0.25rem; }
   .zameen-input, .zameen-select { padding: 0.75rem 1rem; border: 1px solid #dee2e6; border-radius: 8px; font-size: 1rem; background: white; width: 100%; box-sizing: border-box; }
   .zameen-input:focus, .zameen-select:focus { outline: none; border-color: #007c38; box-shadow: 0 0 0 3px rgba(39,167,118,0.1); }
   .zameen-footer { background: #f8f9fa; padding: 1.25rem 1.5rem; border-top: 1px solid #f0f0f0; display: flex; justify-content: flex-end; gap: 1rem; flex-shrink: 0; }
      .zameen-btn { padding: 0.5rem 1.25rem; font-size: 1rem; border-radius: 8px; font-weight: 500; cursor: pointer; }
  /* Remove modal outer background and border-radius for edge-to-edge look */
  .modal-content {
    background: transparent !important;
    box-shadow: none !important;
    border-radius: 0 !important;
  }
  .modal-dialog {
    max-width: 100vw !important;
    margin: 0 !important;
  }
</style>

{{ Form::model($customer, [
   'route'  => ['customer.update', $customer->id],
   'method' => 'PUT',
   'class'  => 'needs-validation',
   'novalidate'
]) }}
<div class="zameen-user-container">
   <div class="zameen-user-card" style="border-radius: 20px; overflow: hidden; box-shadow: 0 4px 24px rgba(0,0,0,0.12); width: 100%; max-width: 700px; margin: 2rem auto;">
      <div class="zameen-user-header" style="background: linear-gradient(90deg, #198754 0%, #20c997 100%); padding: 2rem 2rem 1.25rem; text-align: center; position: relative;">
         <h2 style="margin: 0; font-size: 2rem; font-weight: 600; color: white;">{{ __('Edit Customer') }}</h2>
         <p style="margin: 0; opacity: 0.9; font-size: 1rem; color: white;">{{ __('Update customer information') }}</p>
      </div>
   <div class="zameen-form-container" style="padding: 2rem; background: white; overflow-y: auto;">
         <div class="zameen-form-group">
            <label class="zameen-label">{{ __('Customer Name') }}<span class="zameen-required">*</span></label>
            {{ Form::text('name', null, ['class' => 'zameen-input', 'placeholder' => __('Enter customer name'), 'required' => 'required']) }}
            @error('name')<div class="zameen-form-error">{{ $message }}</div>@enderror
         </div>
         <div class="zameen-form-group">
            <label class="zameen-label">{{ __('Contact') }}<span class="zameen-required">*</span></label>
            {{ Form::text('contact', null, ['class' => 'zameen-input', 'placeholder' => __('Enter contact number'), 'required' => 'required']) }}
            @error('contact')<div class="zameen-form-error">{{ $message }}</div>@enderror
         </div>
         <div class="zameen-form-group">
            <label class="zameen-label">{{ __('Email Address') }}<span class="zameen-required">*</span></label>
            {{ Form::email('email', null, ['class' => 'zameen-input', 'placeholder' => __('Enter email address'), 'required' => 'required']) }}
            @error('email')<div class="zameen-form-error">{{ $message }}</div>@enderror
         </div>
         <div class="zameen-form-group">
            <label class="zameen-label">{{ __('Tax Number') }}</label>
            {{ Form::text('tax_number', null, ['class' => 'zameen-input', 'placeholder' => __('Enter tax number')]) }}
            @error('tax_number')<div class="zameen-form-error">{{ $message }}</div>@enderror
         </div>
         {!! Form::hidden('role', 'company') !!}
         @if($customer->is_enable_login == 1)
         <div class="zameen-form-group">
            <label class="zameen-label">{{ __('Username') }}</label>
            {{ Form::text('user_name', $customer->user_name, ['class' => 'zameen-input', 'disabled' => true]) }}
         </div>
         @endif
         <hr class="my-4">
         <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem;">
            <h5 class="mb-0 font-semibold" style="font-size: 1.15rem;">{{ __('Billing Address') }}</h5>
            <input type="button" id="billing_data" value="Shipping Same As Billing" class="zameen-btn zameen-btn-primary" style="background: #198754; color: white; border-radius: 8px; padding: 0.5rem 1.25rem; font-weight: 500;" />
         </div>
         <div class="row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1rem;">
            <div class="zameen-form-group">
               <label class="zameen-label">{{ __('Name') }}</label>
               {{ Form::text('billing_name', null, ['class' => 'zameen-input']) }}
            </div>
            <div class="zameen-form-group">
               <x-mobile name="billing_phone"></x-mobile>
            </div>
         </div>
         <div class="zameen-form-group">
            <label class="zameen-label">{{ __('Address') }}</label>
            {{ Form::textarea('billing_address', null, ['class' => 'zameen-input', 'rows' => 3]) }}
         </div>
         <div class="flex flex-col md:flex-row gap-4">
            <div class="zameen-form-group w-full md:w-1/3">
               <label class="zameen-label">{{ __('City') }}</label>
               {{ Form::text('billing_city', null, ['class' => 'zameen-input']) }}
            </div>
            <div class="zameen-form-group w-full md:w-1/3">
               <label class="zameen-label">{{ __('State') }}</label>
               {{ Form::text('billing_state', null, ['class' => 'zameen-input']) }}
            </div>
            <div class="zameen-form-group w-full md:w-1/3">
               <label class="zameen-label">{{ __('Country') }}</label>
               {{ Form::text('billing_country', null, ['class' => 'zameen-input']) }}
            </div>
         </div>
         <div class="zameen-form-group">
            <label class="zameen-label">{{ __('Zip Code') }}</label>
            {{ Form::text('billing_zip', null, ['class' => 'zameen-input']) }}
         </div>
         <h5 class="mb-3 font-semibold" style="font-size: 1.15rem;">{{ __('Shipping Address') }}</h5>
         <div class="row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1rem;">
            <div class="zameen-form-group">
               <label class="zameen-label">{{ __('Name') }}</label>
               {{ Form::text('shipping_name', null, ['class' => 'zameen-input']) }}
            </div>
            <div class="zameen-form-group">
               <x-mobile name="shipping_phone"></x-mobile>
            </div>
         </div>
         <div class="zameen-form-group">
            <label class="zameen-label">{{ __('Address') }}</label>
            {{ Form::textarea('shipping_address', null, ['class' => 'zameen-input', 'rows' => 3]) }}
         </div>
         <div class="flex flex-col md:flex-row gap-4">
            <div class="zameen-form-group w-full md:w-1/3">
               <label class="zameen-label">{{ __('City') }}</label>
               {{ Form::text('shipping_city', null, ['class' => 'zameen-input']) }}
            </div>
            <div class="zameen-form-group w-full md:w-1/3">
               <label class="zameen-label">{{ __('State') }}</label>
               {{ Form::text('shipping_state', null, ['class' => 'zameen-input']) }}
            </div>
            <div class="zameen-form-group w-full md:w-1/3">
               <label class="zameen-label">{{ __('Country') }}</label>
               {{ Form::text('shipping_country', null, ['class' => 'zameen-input']) }}
            </div>
         </div>
         <div class="zameen-form-group">
            <label class="zameen-label">{{ __('Zip Code') }}</label>
            {{ Form::text('shipping_zip', null, ['class' => 'zameen-input']) }}
         </div>
      </div>
      <div class="zameen-footer" style="background: #f8f9fa; padding: 1.5rem 2rem; border-top: 1px solid #f0f0f0; display: flex; justify-content: flex-end; gap: 1rem;">
         <button type="button" class="zameen-btn" style="background: #fff; color: #198754; border: 1.5px solid #198754; transition: background 0.2s, color 0.2s; padding: 0.75rem 1.5rem; border-radius: 8px; font-weight: 500;" data-bs-dismiss="modal"
               onmouseover="this.style.background='#198754';this.style.color='#fff'" onmouseout="this.style.background='#fff';this.style.color='#198754'">
            {{ __('Cancel') }}
         </button>
         <button type="submit" class="zameen-btn" style="background: linear-gradient(135deg, #198754 0%, #20c997 100%); color: #fff; border: none; transition: background 0.2s; padding: 0.75rem 1.5rem; border-radius: 8px; font-weight: 500;">
            {{ __('Update') }}
         </button>
      </div>
   </div>
</div>
{{ Form::close() }}
