{{ Form::open([
    'url' => 'users',
    'method' => 'post',
    'class' => 'needs-validation',
    'novalidate',
    'files' => true,   // <<< IMPORTANT
]) }}
<div class="modal-body bg-[#FAFBFC]">
  <div class="bg-white p-6 rounded-[8px] border border-[#E5E5EB] shadow-sm overflow-hidden">
    <div class="flex flex-col gap-4">
      @if (\Auth::user()->type == 'super admin')
        <div class="w-full flex flex-col gap-2 mb-4">
          <div class="flex gap-1">{{ Form::label('name', __('Name'), ['class' => 'text-[14px] font-[600] leading-[24px] text-[#323232] mb-0']) }}<x-required/></div>
          {{ Form::text('name', null, ['class' => 'form-control border border-[#E5E5E5] rounded-[8px] px-4 py-2 text-[12px] text-[#323232] leading-[24px] font-[400]', 'placeholder' => __('Enter Company Name'), 'required' => 'required']) }}
          @error('name') <small class="invalid-name" role="alert"><strong class="text-[14px] font-[600] leading-[24px] text-[#323232]">{{ $message }}</strong></small> @enderror
        </div>

        <div class="w-full flex flex-col gap-2 mb-4">
          <div class="flex gap-1">{{ Form::label('email', __('Email'), ['class' => 'text-[14px] font-[600] leading-[24px] text-[#323232] mb-0']) }}<x-required/></div>
          {{ Form::email('email', null, ['class' => 'form-control border border-[#E5E5E5] rounded-[8px] px-4 py-2 text-[12px] text-[#323232] leading-[24px] font-[400]', 'placeholder' => __('Enter Company Email'), 'required' => 'required']) }}
          @error('email') <small class="invalid-email" role="alert"><strong class="text-[14px] font-[600] leading-[24px] text-[#323232]">{{ $message }}</strong></small> @enderror
        </div>

        {!! Form::hidden('role', 'company') !!}

        {{-- Avatar upload --}}
        <div class="w-full flex flex-col gap-2 mb-4">
          <div class="flex gap-1">
            {{ Form::label('avatar', __('Profile Picture'), ['class' => 'text-[14px] font-[600] leading-[24px] text-[#323232] mb-0']) }}
          </div>
          <div class="flex items-center gap-4">
            <img id="avatarPreviewCreate"
                 src="{{ asset('web-assets/dashboard/icons/avatar.png') }}"
                 alt="avatar preview"
                 class="rounded-full border border-[#E5E5E5] js-avatar-preview"
                 style="width:72px;height:72px;object-fit:cover;">
            {{ Form::file('avatar', [
                'id' => 'avatarInputCreate',
                'class' => 'form-control border border-[#E5E5E5] rounded-[8px] px-4 py-2 text-[12px] text-[#323232] leading-[24px] font-[400]',
                'accept' => 'image/png,image/jpeg,image/webp',
                'data-preview' => '#avatarPreviewCreate',
                'name' => 'avatar',
            ]) }}
          </div>
          <small class="text-muted text-xs">{{ __('JPG, PNG, or WEBP up to 2MB') }}</small>
          @error('avatar') <small class="invalid-avatar" role="alert"><strong class="text-[14px] font-[600] leading-[24px] text-[#323232]">{{ $message }}</strong></small> @enderror
        </div>

        <div class="col-md-6 form-group mb-4">
          <label for="password_switch">{{ __('Login is enable') }}</label>
          <div class="form-check form-switch custom-switch-v1 float-end">
            <input type="checkbox" name="password_switch" class="form-check-input input-primary pointer" value="on" id="password_switch">
            <label class="form-check-label" for="password_switch"></label>
          </div>
        </div>

        <div class="w-full flex flex-col gap-2 mb-4 ps_div d-none">
          <div class="flex gap-1">{{ Form::label('password', __('Password'), ['class' => 'text-[14px] font-[600] leading-[24px] text-[#323232] mb-0']) }}</div>
          {{ Form::password('password', ['id'=>'password','class' => 'form-control border border-[#E5E5E5] rounded-[8px] px-4 py-2 text-[12px] text-[#323232] leading-[24px] font-[400]', 'placeholder' => __('Enter Company Password'), 'minlength' => '6']) }}
          @error('password') <small class="invalid-password" role="alert"><strong class="text-[14px] font-[600] leading-[24px] text-[#323232]">{{ $message }}</strong></small> @enderror
        </div>
      @else
        <div class="w-full flex flex-col gap-2 mb-4">
          <div class="flex gap-1">{{ Form::label('name', __('Name'), ['class' => 'text-[14px] font-[600] leading-[24px] text-[#323232] mb-0']) }}<x-required/></div>
          {{ Form::text('name', null, ['class' => 'form-control border border-[#E5E5E5] rounded-[8px] px-4 py-2 text-[12px] text-[#323232] leading-[24px] font-[400]', 'placeholder' => __('Enter User Name'), 'required' => 'required']) }}
          @error('name') <small class="invalid-name" role="alert"><strong class="text-[14px] font-[600] leading-[24px] text-[#323232]">{{ $message }}</strong></small> @enderror
        </div>

        <div class="w-full flex flex-col gap-2 mb-4">
          <div class="flex gap-1">{{ Form::label('email', __('Email'), ['class' => 'text-[14px] font-[600] leading-[24px] text-[#323232] mb-0']) }}<x-required/></div>
          {{ Form::text('email', null, ['class' => 'form-control border border-[#E5E5E5] rounded-[8px] px-4 py-2 text-[12px] text-[#323232] leading-[24px] font-[400]', 'placeholder' => __('Enter User Email'), 'required' => 'required']) }}
          @error('email') <small class="invalid-email" role="alert"><strong class="text-[14px] font-[600] leading-[24px] text-[#323232]">{{ $message }}</strong></small> @enderror
        </div>

        <div class="w-full flex flex-col gap-2 mb-4">
          <div class="flex gap-1">{{ Form::label('role', __('User Role'), ['class' => 'text-[14px] font-[600] leading-[24px] text-[#323232] mb-0']) }}<x-required/></div>
          {!! Form::select('role', $roles, null, ['class' => 'form-control select border border-[#E5E5E5] rounded-[8px] px-4 py-2 text-[12px] text-[#323232] leading-[24px] font-[400]', 'required' => 'required']) !!}
          @error('role') <small class="invalid-role" role="alert"><strong class="text-[14px] font-[600] leading-[24px] text-[#323232]">{{ $message }}</strong></small> @enderror
        </div>

        {{-- Avatar upload --}}
        <div class="w-full flex flex-col gap-2 mb-4">
          <div class="flex gap-1">{{ Form::label('avatar', __('Profile Picture'), ['class' => 'text-[14px] font-[600] leading-[24px] text-[#323232] mb-0']) }}</div>
          <div class="flex items-center gap-4">
            <img id="avatarPreviewCreate"
                 src="{{ asset('web-assets/dashboard/icons/avatar.png') }}"
                 alt="avatar preview"
                 class="rounded-full border border-[#E5E5E5] js-avatar-preview"
                 style="width:72px;height:72px;object-fit:cover;">
            {{ Form::file('avatar', [
                'id' => 'avatarInputCreate',
                'class' => 'form-control border border-[#E5E5E5] rounded-[8px] px-4 py-2 text-[12px] text-[#323232] leading-[24px] font-[400]',
                'accept' => 'image/png,image/jpeg,image/webp',
                'data-preview' => '#avatarPreviewCreate',
                'name' => 'avatar',
            ]) }}
          </div>
          <small class="text-muted text-xs">{{ __('JPG, PNG, or WEBP up to 2MB') }}</small>
          @error('avatar') <small class="invalid-avatar" role="alert"><strong class="text-[14px] font-[600] leading-[24px] text-[#323232]">{{ $message }}</strong></small> @enderror
        </div>

        {{-- Hidden DOB (default) --}}
        <div class="w-full flex flex-col gap-2 mb-4 d-none">
          <div class="flex gap-1">
            {{ Form::label('date_of_birth', __('Date of Birth'), ['class' => 'text-[14px] font-[600] leading-[24px] text-[#323232] mb-0']) }} <x-required/>
          </div>
          {!! Form::date('date_of_birth', old('date_of_birth', '2000-01-01'), ['class' => 'form-control select border border-[#E5E5E5] rounded-[8px] px-4 py-2 text-[12px] text-[#323232] leading-[24px] font-[400]', 'required' => 'required']) !!}
          @error('date_of_birth') <small class="invalid-role" role="alert"><strong class="text-[14px] font-[600] leading-[24px] text-[#323232]">{{ $message }}</strong></small> @enderror
        </div>

        <div class="flex col-md-5 mb-3 form-group mt-4 w-full gap-[14px]">
          <label for="password_switch">{{ __('Login is enable') }}</label>
          <div class="form-check form-switch custom-switch-v1 float-end">
            <input type="checkbox" name="password_switch" class="form-check-input input-primary pointer" value="on" id="password_switch">
            <label class="form-check-label" for="password_switch"></label>
          </div>
        </div>

        <div class="w-full flex flex-col gap-2 mb-4 ps_div d-none">
          <div class="flex gap-1">{{ Form::label('password', __('Password'), ['class' => 'text-[14px] font-[600] leading-[24px] text-[#323232] mb-0']) }}</div>
          {{ Form::password('password', ['id'=>'password','class' => 'form-control border border-[#E5E5E5] rounded-[8px] px-4 py-2 text-[12px] text-[#323232] leading-[24px] font-[400]', 'placeholder' => __('Enter Company Password'), 'minlength' => '6']) }}
          @error('password') <small class="invalid-password" role="alert"><strong class="text-[14px] font-[600] leading-[24px] text-[#323232]">{{ $message }}</strong></small> @enderror
        </div>
      @endif

      @if (!$customFields->isEmpty())
        <div class="w-full flex flex-col gap-2 mb-4">
          <div class="tab-pane fade show" id="tab-2" role="tabpanel">
            @include('customFields.formBuilder')
          </div>
        </div>
      @endif
    </div>
  </div>
</div>

<div class="modal-footer border-t border-[#E5E5E5] bg-[#FAFAFA] px-6 py-4 flex justify-end gap-3">
  <input type="button" value="{{ __('Cancel') }}" class="btn py-[6px] px-[10px] btn text-[#007C38] border-[#007C38] hover:bg-[#007C38] hover:text-white" data-bs-dismiss="modal">
  <input type="submit" value="{{ __('Create') }}" class="btn py-[6px] px-[10px] btn bg-[#007C38] text-white hover:bg-green-700">
</div>
{{ Form::close() }}
