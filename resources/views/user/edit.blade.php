
<style>
  .zameen-user-container { background: #f8f9fa; padding: 1rem; max-height: 90vh; overflow-y: auto; }
  .zameen-user-card { background: white; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); max-width: 500px; margin: 0 auto; max-height: 85vh; display: flex; flex-direction: column; }
  .zameen-user-header { background: linear-gradient(135deg, #007c38 0%, #10b981 100%); padding: 1.5rem 2rem; color: white; text-align: center; flex-shrink: 0; }
  .zameen-user-header h2 { margin: 0; font-size: 1.5rem; font-weight: 600; }
  .zameen-user-header p { margin: 0; opacity: 0.9; font-size: 0.875rem; }
  .zameen-form-container { padding: 1.5rem 2rem; overflow-y: auto; flex: 1; }
  .zameen-form-group { margin-bottom: 1rem; }
  .zameen-label { font-weight: 500; color: #495057; margin-bottom: 0.5rem; font-size: 0.95rem; display: block; }
  .zameen-required { color: #f44336; margin-left: 0.25rem; }
  .zameen-input, .zameen-select { padding: 0.75rem 1rem; border: 1px solid #dee2e6; border-radius: 8px; font-size: 1rem; background: white; width: 100%; box-sizing: border-box; }
  .zameen-input:focus, .zameen-select:focus { outline: none; border-color: #007c38; box-shadow: 0 0 0 3px rgba(39,167,118,0.1); }
  .zameen-footer { background: #f8f9fa; padding: 1rem 2rem; border-top: 1px solid #f0f0f0; display: flex; justify-content: flex-end; gap: 1rem; flex-shrink: 0; }
</style>

{{ Form::model($user, [
    'route' => ['users.update', $user->id],
    'method' => 'PUT',
    'class' => 'needs-validation',
    'novalidate',
    'files' => true,
]) }}
<div class="zameen-user-container">
  <div class="zameen-user-card">
    <div class="zameen-user-header">
      <h2>{{ __('Edit User') }}</h2>
      <p>{{ __('Update user details') }}</p>
    </div>
    <div class="zameen-form-container">
      <div class="zameen-avatar-section" style="text-align:center; margin-bottom:1.25rem;">
        @php
          $currentAvatar = !empty($user->avatar)
              ? asset(Storage::url('uploads/avatar/' . $user->avatar))
              : asset('web-assets/dashboard/icons/avatar.png');
        @endphp
        <img id="avatarPreviewEdit" class="zameen-avatar-preview" src="{{ $currentAvatar }}" alt="avatar" style="width:100px;height:100px;border-radius:50%;border:3px solid #007c38;object-fit:cover;margin-bottom:0.75rem;display:block;margin-left:auto;margin-right:auto;">
        <div>
          <label for="avatarInputEdit" class="zameen-upload-btn" style="background:#007c38;color:white;padding:0.5rem 1rem;border-radius:8px;font-size:0.875rem;font-weight:500;border:none;cursor:pointer;display:inline-flex;align-items:center;gap:0.5rem;">
            <i class="ti ti-upload"></i>{{ __('Choose Profile Picture') }}
          </label>
          <input type="file" class="zameen-file-input" name="avatar" id="avatarInputEdit" accept="image/*" onchange="document.getElementById('avatarPreviewEdit').src = window.URL.createObjectURL(this.files[0])">
        </div>
        <small class="text-muted">{{ __('JPG, PNG, or WEBP up to 2MB') }}</small>
      </div>
      <div class="zameen-form-group">
        <label class="zameen-label">{{ __('Name') }}<span class="zameen-required">*</span></label>
        {{ Form::text('name', null, ['class'=>'zameen-input','placeholder'=>__('Enter User Name'), 'required' => 'required']) }}
        @error('name') <small class="invalid-name" role="alert"><strong class="text-danger">{{ $message }}</strong></small> @enderror
      </div>
      <div class="zameen-form-group">
        <label class="zameen-label">{{ __('Email') }}<span class="zameen-required">*</span></label>
        {{ Form::email('email', null, ['class'=>'zameen-input','placeholder'=>__('Enter User Email'), 'required' => 'required']) }}
        @error('email') <small class="invalid-email" role="alert"><strong class="text-danger">{{ $message }}</strong></small> @enderror
      </div>
      @if(\Auth::user()->type != 'super admin')
        <div class="zameen-form-group">
          <label class="zameen-label">{{ __('User Role') }}<span class="zameen-required">*</span></label>
          {!! Form::select('role', $roles, $user->roles, ['class' => 'zameen-select','required'=>'required']) !!}
          @error('role') <small class="invalid-role" role="alert"><strong class="text-danger">{{ $message }}</strong></small> @enderror
        </div>
      @endif
      @if(!$customFields->isEmpty())
        <div class="zameen-form-group">
          <div class="tab-pane fade show" id="tab-2" role="tabpanel">
            @include('customFields.formBuilder')
          </div>
        </div>
      @endif
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
        {{ __('Update') }}
      </button>
    </div>
  </div>
</div>
{{ Form::close() }}
