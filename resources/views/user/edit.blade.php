{{ Form::model($user, [
    'route' => ['users.update', $user->id],
    'method' => 'PUT',
    'class' => 'needs-validation',
    'novalidate',
    'files' => true,
]) }}
<div class="modal-body bg-[#FAFBFC]">
  <div class="bg-white p-6 rounded-[8px] border border-[#E5E5EB] shadow-sm overflow-hidden">
    <div class="row">
      <div class="col-md-6">
        <div class="form-group ">
          {{ Form::label('name', __('Name'), ['class'=>'form-label']) }}<x-required/>
          {{ Form::text('name', null, ['class'=>'form-control font-style','placeholder'=>__('Enter User Name'), 'required' => 'required']) }}
          @error('name') <small class="invalid-name" role="alert"><strong class="text-danger">{{ $message }}</strong></small> @enderror
        </div>
      </div>

      <div class="col-md-6">
        <div class="form-group">
          {{ Form::label('email', __('Email'), ['class'=>'form-label']) }}<x-required/>
          {{ Form::email('email', null, ['class'=>'form-control','placeholder'=>__('Enter User Email'), 'required' => 'required']) }}
          @error('email') <small class="invalid-email" role="alert"><strong class="text-danger">{{ $message }}</strong></small> @enderror
        </div>
      </div>

      {{-- Avatar upload (Edit) --}}
      @php
        $currentAvatar = !empty($user->avatar)
            ? asset(Storage::url('uploads/avatar/' . $user->avatar))
            : asset('web-assets/dashboard/icons/avatar.png');
      @endphp
      <div class="col-md-6">
        <div class="form-group">
          {{ Form::label('avatar', __('Profile Picture'), ['class' => 'form-label']) }}
          <div class="d-flex align-items-center gap-3">
            <img id="avatarPreviewEdit"
                 src="{{ $currentAvatar }}"
                 class="rounded-circle border js-avatar-preview"
                 style="width:72px;height:72px;object-fit:cover;">
            {{ Form::file('avatar', [
                'id' => 'avatarInputEdit',
                'class' => 'form-control',
                'accept' => 'image/png,image/jpeg,image/webp',
                'data-preview' => '#avatarPreviewEdit',
                'name' => 'avatar',
            ]) }}
          </div>
          <small class="text-muted">{{ __('JPG, PNG, or WEBP up to 2MB') }}</small>
          @error('avatar') <small class="invalid-avatar" role="alert"><strong class="text-danger">{{ $message }}</strong></small> @enderror
        </div>
      </div>

      @if(\Auth::user()->type != 'super admin')
        <div class="form-group col-md-12 mb-0">
          {{ Form::label('role', __('User Role'), ['class'=>'form-label']) }}<x-required/>
          {!! Form::select('role', $roles, $user->roles, ['class' => 'form-control select','required'=>'required']) !!}
          @error('role') <small class="invalid-role" role="alert"><strong class="text-danger">{{ $message }}</strong></small> @enderror
        </div>
      @endif

      @if(!$customFields->isEmpty())
        <div class="col-md-6">
          <div class="tab-pane fade show" id="tab-2" role="tabpanel">
            @include('customFields.formBuilder')
          </div>
        </div>
      @endif
    </div>
  </div>
</div>

<div class="modal-footer border-t border-[#E5E5E5] bg-[#FAFAFA] px-6 py-4 flex justify-end gap-3">
  <input type="button" value="{{__('Cancel')}}" class="btn btn-light" data-bs-dismiss="modal">
  <input type="submit" value="{{__('Update')}}" class="btn btn-primary">
</div>
{{ Form::close() }}
