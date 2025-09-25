
{{Form::model($user,array('route' => array('user.password.update', $user->id), 'method' => 'post','class'=>'needs-validation','novalidate')) }}
<div class="modal-body bg-[#FAFBFC]">
     <div class="bg-white p-6 rounded-[8px] border border-[#E5E7EB] shadow-sm overflow-hidden">
    <div class="row">
        <div class="form-group col-md-6 mb-0">
            {{ Form::label('password', __('New Password'),['class'=>'form-label']) }}<x-required></x-required>
            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password" placeholder="{{ __('Enter New Password') }}">
            @error('password')
            <span class="invalid-feedback" role="alert">
                   <strong>{{ $message }}</strong>
               </span>
            @enderror
        </div>
        <div class="form-group col-md-6 mb-0">
            {{ Form::label('password_confirmation', __('Confirm New Password'),['class'=>'form-label']) }}<x-required></x-required>
            <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password" placeholder="{{ __('Confirm New Password') }}">
        </div>
    </div>
    </div>
</div>
<div class="modal-footer" style="background: #f8f9fa; border-top: 1px solid #e0e0e0; padding: 1.5rem 2rem; display: flex; justify-content: flex-end; gap: 1rem; border-radius: 0 0 8px 8px;">
    <input type="button" value="{{__('Cancel')}}" class="btn btn-light" data-bs-dismiss="modal" style="border-radius: 8px; padding: 0.75rem 1.5rem; border: 1.5px solid #e0e0e0; color: #2d3748; font-weight: 500; background: #fff;">
    <input type="submit" value="{{__('Update')}}" class="btn btn-success" style="background: #007c38; color: #fff; border-radius: 8px; padding: 0.75rem 1.5rem; font-weight: 500; border: none;">
</div>

{{ Form::close() }}
