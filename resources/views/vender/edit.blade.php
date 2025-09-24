
<style>
  .zameen-user-container { background: #f8f9fa; padding: 0; max-height: 90vh; overflow-y: auto; }
  .zameen-user-card { background: white; border-radius: 0; box-shadow: none; width: 100%; max-width: none; margin: 0; max-height: 90vh; display: flex; flex-direction: column; }
  form.needs-validation { width: 100%; }
  .zameen-user-header { background: linear-gradient(135deg, #007c38 0%, #10b981 100%); padding: 1.5rem 2rem; color: white; text-align: center; flex-shrink: 0; border-radius: 0; }
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
</style>

{{ Form::model($vender, [
    'route'  => ['vender.update', $vender->id],
    'method' => 'PUT',
    'class'  => 'needs-validation',
    'novalidate'
]) }}
<div class="zameen-user-container">
  <div class="zameen-user-card">
    <div class="zameen-user-header">
      <h2>{{ __('Edit Vendor') }}</h2>
      <p>{{ __('Update vendor information') }}</p>
    </div>
    <div class="zameen-form-container">
      <div class="zameen-form-group">
        <label class="zameen-label">{{ __('Name') }}<span class="zameen-required">*</span></label>
        {{ Form::text('name', null, ['class' => 'zameen-input', 'required' => 'required']) }}
      </div>
      <div class="zameen-form-group">
        <label class="zameen-label">{{ __('Contact') }}<span class="zameen-required">*</span></label>
        <x-mobile name="contact" required></x-mobile>
      </div>
      <div class="zameen-form-group">
        <label class="zameen-label">{{ __('Email') }}<span class="zameen-required">*</span></label>
        {{ Form::text('email', null, ['class' => 'zameen-input', 'required' => 'required']) }}
      </div>
      @if($vender->is_enable_login == 1)
      <div class="zameen-form-group">
        <label class="zameen-label">{{ __('Username') }}</label>
        {{ Form::text('user_name', $vender->user_name, ['class' => 'zameen-input', 'disabled' => true]) }}
      </div>
      @endif
      @if(!$customFields->isEmpty())
      <div class="zameen-form-group">
        @include('customFields.formBuilder')
      </div>
      @endif
      <hr class="my-4">
      <h5 class="mb-3 font-semibold">{{ __('Billing Address') }}</h5>
      <div class="row" style="display: flex; gap: 1rem; margin-bottom: 1rem;">
        <div class="zameen-form-group" style="flex:1; min-width: 0;">
          <label class="zameen-label">{{ __('Name') }}</label>
          {{ Form::text('billing_name', null, ['class' => 'zameen-input']) }}
        </div>
        <div class="zameen-form-group" style="flex:1; min-width: 0;">
          <label class="zameen-label" style="font-weight: bold;">{{ __('Mobile No') }}</label>
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
      @if(App\Models\Utility::getValByName('shipping_display')=='on')
      <h5 class="mb-3 font-semibold">{{ __('Shipping Address') }}</h5>
      <div class="row" style="display: flex; gap: 1rem; margin-bottom: 1rem;">
        <div class="zameen-form-group" style="flex:1; min-width: 0;">
          <label class="zameen-label">{{ __('Name') }}</label>
          {{ Form::text('shipping_name', null, ['class' => 'zameen-input']) }}
        </div>
        <div class="zameen-form-group" style="flex:1; min-width: 0;">
          <label class="zameen-label" style="font-weight: bold;">{{ __('Mobile No') }}</label>
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
      <div class="text-end mb-4">
        <input type="button" id="billing_data" value="Shipping Same As Billing" class="zameen-btn zameen-btn-primary">
      </div>
      @endif
    </div>
    <div class="zameen-footer">
      <button type="button" class="zameen-btn" style="background: #fff; color: #007c38; border: 1.5px solid #007c38; transition: background 0.2s, color 0.2s; padding: 0.5rem 1.25rem;" data-bs-dismiss="modal"
        onmouseover="this.style.background='#007c38';this.style.color='#fff'" onmouseout="this.style.background='#fff';this.style.color='#007c38'">
        <svg style="width: 16px; height: 16px; margin-right: 0.5rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
        </svg>
        {{ __('Cancel') }}
      </button>
      <button type="submit" class="zameen-btn" style="background: linear-gradient(135deg, #007c38 0%, #10b981 100%); color: #fff; border: none; transition: background 0.2s; padding: 0.5rem 1.25rem;">
        <svg style="width: 16px; height: 16px; margin-right: 0.5rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
        </svg>
        {{ __('Update') }}
      </button>
    </div>
  </div>
</div>
{{ Form::close() }}
