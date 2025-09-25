<script src="{{ asset('js/unsaved.js') }}"></script>

{{Form::open(array('url'=>'vender','method'=>'post','class'=>'needs-validation','novalidate'))}}
<div class="modal-body p-6 bg-[#FAFBFC]">
   <div class="bg-white rounded-[8px] mb-6 border border-[#E5E7EB] shadow-sm overflow-hidden">
      <div class="heading-cstm-form">
         <h6 class="mb-0 flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
               <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
            </svg>
            {{__('Basic Info')}}
         </h6>
      </div>
      <div style="padding: 1.5rem; display: flex; flex-direction: column; gap: 1rem;">
         <div class="form-group">
            {{Form::label('name',__('Name'),array('class'=>'form-label')) }}
            <x-required></x-required>
            <div class="form-icon-user">
               {{Form::text('name',null,array('class'=>'form-control','required'=>'required'))}}
            </div>
         </div>

         <div class="form-group">
            {{Form::label('contact',__('Contact'),array('class'=>'form-label')) }}
            <x-required></x-required>
            <div class="form-icon-user">
               {{Form::text('contact',null,array('class'=>'form-control','required'=>'required'))}}
            </div>
         </div>

         <div class="form-group">
            {{Form::label('email',__('Email'),['class'=>'form-label'])}}
            <x-required></x-required>
            <div class="form-icon-user">
               {{Form::text('email',null,array('class'=>'form-control','required'=>'required'))}}
            </div>
         </div>

         {!! Form::hidden('role', 'company', null, ['class' => 'form-control select2', 'required' => 'required']) !!}

         <div class="form-group">
            {{Form::label('tax_number',__('Tax Number'),['class'=>'form-label'])}}
            <div class="form-icon-user">
               {{Form::text('tax_number',null,array('class'=>'form-control'))}}
            </div>
         </div>

         <div class="flex mb-3 form-group mt-4 gap-[14px]">
            <label for="password_switch">{{ __('Login is enable') }}</label>
            <div class="form-check form-switch custom-switch-v1 float-end">
               <input type="checkbox" name="password_switch" class="form-check-input input-primary pointer" value="on" id="password_switch">
               <label class="form-check-label" for="password_switch"></label>
            </div>
         </div>

         <div class="ps_div d-none" style="display: flex; flex-direction: column; gap: 1rem;">
            <div class="form-group">
                {{ Form::label('user_name', __('Username'), ['class' => 'form-label']) }}
                <div class="form-icon-user">
                    {{ Form::text('user_name', null, ['class' => 'form-control', 'placeholder' => __('Enter Username')]) }}
                </div>
            </div>

            <div class="form-group">
               {{ Form::label('password', __('Password'), ['class' => 'form-label']) }}
               <div class="form-icon-user">
                  {{ Form::password('password', ['class' => 'form-control','minlength' => '6']) }}
               </div>
            </div>
         </div>

         @if(!$customFields->isEmpty())
         <div class="form-group">
            <div class="tab-pane fade show" id="tab-2" role="tabpanel">
               @include('customFields.formBuilder')
            </div>
         </div>
         @endif
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
<div class="modal-footer" style="background: #f8f9fa; border-top: 1px solid #e0e0e0; padding: 1.5rem 2rem; display: flex; justify-content: flex-end; gap: 1rem; border-radius: 0 0 8px 8px;">
   <input type="button" value="{{__('Cancel')}}" class="btn btn-light" data-bs-dismiss="modal" style="border-radius: 8px; padding: 0.75rem 1.5rem; border: 1.5px solid #e0e0e0; color: #2d3748; font-weight: 500; background: #fff;">
   <input type="submit" value="{{__('Create')}}" class="btn btn-success" style="background: #007c38; color: #fff; border-radius: 8px; padding: 0.75rem 1.5rem; font-weight: 500; border: none;">
</div>
{{Form::close()}}
@push('script-page')
<script>
 $(document).on('change', '#password_switch', function() {
       if ($(this).is(':checked')) {
           $('.ps_div').removeClass('d-none');
           $('#password').attr("required", true);
           $('#user_name').attr("required", true);

       } else {
           $('.ps_div').addClass('d-none');
           $('#password').val(null);
           $('#user_name').val(null);
           $('#password').removeAttr("required");
           $('#user_name').removeAttr("required");
       }
   });
   $(document).on('click', '.login_enable', function() {
       setTimeout(function() {
           $('.modal-body').append($('<input>', {
               type: 'hidden',
               val: 'true',
               name: 'login_enable'
           }));
       }, 2000);
   });
</script>
@endpush
