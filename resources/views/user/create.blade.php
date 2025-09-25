<style>
  :root {
    --zameen-primary: #007c38;
    --zameen-primary-light: #10b981;
    --zameen-primary-dark: #007c38;
    --zameen-secondary: #3f51b5;
    --zameen-success: #4caf50;
    --zameen-danger: #f44336;
    --zameen-warning: #ff9800;
    --zameen-info: #2196f3;
    --zameen-light: #f8f9fa;
    --zameen-dark: #212529;
    --zameen-gray-100: #f8f9fa;
    --zameen-gray-200: #e9ecef;
    --zameen-gray-300: #dee2e6;
    --zameen-gray-400: #ced4da;
    --zameen-gray-500: #adb5bd;
    --zameen-gray-600: #6c757d;
    --zameen-gray-700: #495057;
    --zameen-gray-800: #343a40;
    --zameen-gray-900: #212529;
    --zameen-border: #e0e0e0;
    --zameen-border-light: #f0f0f0;
    --zameen-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    --zameen-shadow-lg: 0 4px 20px rgba(0, 0, 0, 0.15);
    --zameen-radius: 8px;
    --zameen-radius-lg: 12px;
  }

  .zameen-user-container {
    background: #f8f9fa;
    padding: 1rem;
    max-height: 90vh;
    overflow-y: auto;
  }

  .zameen-user-card {
    background: white;
    border-radius: var(--zameen-radius-lg);
    box-shadow: var(--zameen-shadow);
    overflow: hidden;
    max-width: 500px;
    margin: 0 auto;
    max-height: 85vh;
    display: flex;
    flex-direction: column;
  }

  .zameen-user-header {
    background: linear-gradient(135deg, var(--zameen-primary) 0%, var(--zameen-primary-light) 100%);
    padding: 1.5rem 2rem;
    color: white;
    text-align: center;
    flex-shrink: 0;
  }

  .zameen-user-header h2 {
    margin: 0;
    font-size: 1.5rem;
    font-weight: 600;
    margin-bottom: 0.25rem;
  }

  .zameen-user-header p {
    margin: 0;
    opacity: 0.9;
    font-size: 0.875rem;
  }

  .zameen-form-container {
    padding: 1.5rem 2rem;
    overflow-y: auto;
    flex: 1;
  }

  .zameen-form-group {
    margin-bottom: 1rem;
  }

  .zameen-label {
    font-weight: 500;
    color: var(--zameen-gray-700);
    margin-bottom: 0.5rem;
    font-size: 0.875rem;
    display: block;
  }

  .zameen-required {
    color: var(--zameen-danger);
    margin-left: 0.25rem;
  }

  .zameen-input {
    padding: 0.75rem 1rem;
    border: 1px solid var(--zameen-gray-300);
    border-radius: var(--zameen-radius);
    font-size: 1rem;
    transition: all 0.2s ease;
    background: white;
    width: 100%;
    box-sizing: border-box;
  }

  .zameen-input:focus {
    outline: none;
    border-color: var(--zameen-primary);
    box-shadow: 0 0 0 3px rgba(39, 167, 118, 0.1);
  }

  .zameen-input:hover {
    border-color: var(--zameen-gray-400);
  }

  .zameen-select {
    padding: 0.75rem 1rem;
    border: 1px solid var(--zameen-gray-300);
    border-radius: var(--zameen-radius);
    font-size: 1rem;
    transition: all 0.2s ease;
    background: white;
    width: 100%;
    box-sizing: border-box;
    appearance: none;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3e%3c/svg%3e");
    background-position: right 0.5rem center;
    background-repeat: no-repeat;
    background-size: 1.5em 1.5em;
    padding-right: 2.5rem;
  }

  .zameen-select:focus {
    outline: none;
    border-color: var(--zameen-primary);
    box-shadow: 0 0 0 3px rgba(39, 167, 118, 0.1);
  }

  .zameen-avatar-section {
    text-align: center;
    margin-bottom: 1.25rem;
  }

  .zameen-avatar-upload {
    position: relative;
    display: inline-block;
  }

  .zameen-avatar-preview {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    border: 3px solid var(--zameen-primary);
    object-fit: cover;
    margin-bottom: 0.75rem;
    display: block;
    margin-left: auto;
    margin-right: auto;
  }

  .zameen-upload-btn {
    background: var(--zameen-primary);
    color: white;
    padding: 0.5rem 1rem;
    border-radius: var(--zameen-radius);
    font-size: 0.875rem;
    font-weight: 500;
    border: none;
    cursor: pointer;
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
  }

  .zameen-upload-btn:hover {
    background: var(--zameen-primary-dark);
    transform: translateY(-1px);
  }

  .zameen-upload-btn i {
    font-size: 1rem;
  }

  .zameen-file-input {
    display: none;
  }

  .zameen-toggle-container {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0.75rem;
    background: var(--zameen-gray-100);
    border-radius: var(--zameen-radius);
    border: 1px solid var(--zameen-gray-200);
  }

  .zameen-toggle {
    position: relative;
    width: 50px;
    height: 24px;
  }

  .zameen-toggle input {
    opacity: 0;
    width: 0;
    height: 0;
  }

  .zameen-toggle-slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: var(--zameen-gray-400);
    transition: 0.3s;
    border-radius: 24px;
  }

  .zameen-toggle-slider:before {
    position: absolute;
    content: "";
    height: 18px;
    width: 18px;
    left: 3px;
    bottom: 3px;
    background-color: white;
    transition: 0.3s;
    border-radius: 50%;
  }

  .zameen-toggle input:checked + .zameen-toggle-slider {
    background-color: var(--zameen-primary);
  }

  .zameen-toggle input:checked + .zameen-toggle-slider:before {
    transform: translateX(26px);
  }

  .zameen-btn {
    padding: 0.75rem 1.5rem;
    border-radius: var(--zameen-radius);
    font-weight: 500;
    font-size: 0.875rem;
    cursor: pointer;
    transition: all 0.2s ease;
    border: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
  }

  .zameen-btn-primary {
    background: var(--zameen-primary);
    color: white;
  }

  .zameen-btn-primary:hover {
    background: var(--zameen-primary-dark);
    transform: translateY(-1px);
    box-shadow: var(--zameen-shadow);
  }

  .zameen-btn-outline {
    background: transparent;
    color: var(--zameen-gray-600);
    border: 1px solid var(--zameen-gray-300);
  }

  .zameen-btn-outline:hover {
    background: var(--zameen-gray-100);
    border-color: var(--zameen-gray-400);
  }

  .zameen-footer {
    background: var(--zameen-gray-100);
    padding: 1rem 2rem;
    border-top: 1px solid var(--zameen-border-light);
    display: flex;
    justify-content: flex-end;
    gap: 1rem;
    flex-shrink: 0;
  }

  .ps_div {
    margin-top: 1rem;
    padding: 1rem;
    background: var(--zameen-gray-50);
    border-radius: var(--zameen-radius);
    border-left: 4px solid var(--zameen-primary);
  }

  @media (max-width: 768px) {
    .zameen-form-container {
      padding: 1.5rem;
    }
    
    .zameen-user-container {
      padding: 1rem;
    }
  }
</style>

{{ Form::open([
    'url' => 'users',
    'method' => 'post',
    'class' => 'needs-validation',
    'novalidate',
    'files' => true,
]) }}

<div >
  <div >
    <div class="zameen-user-header">
      <h2>{{ __('Create New User') }}</h2>
      <p>{{ __('Add a new user to your team') }}</p>
    </div>
    <div class="zameen-form-container">
      @if (\Auth::user()->type == 'super admin')
        <div class="zameen-avatar-section">
          <img id="blah3" class="zameen-avatar-preview" src="{{ asset('web-assets/dashboard/icons/avatar.png') }}" alt="avatar">
          <div>
            <label for="avatar" class="zameen-upload-btn">
              <i class="ti ti-upload"></i>{{ __('Choose Profile Picture') }}
            </label>
            <input type="file" class="zameen-file-input" name="avatar" id="avatar" accept="image/*" onchange="document.getElementById('blah3').src = window.URL.createObjectURL(this.files[0])">
          </div>
        </div>

        <div class="zameen-form-group">
          <label class="zameen-label">
            {{ __('Company Name') }}
            <span class="zameen-required">*</span>
          </label>
          {{ Form::text('name', null, ['class' => 'zameen-input', 'placeholder' => __('Enter Company Name'), 'required' => 'required']) }}
        </div>

        <div class="zameen-form-group">
          <label class="zameen-label">
            {{ __('Company Email') }}
            <span class="zameen-required">*</span>
          </label>
            {{ Form::text('name', null, ['class' => 'zameen-input', 'placeholder' => __('Enter Company Name'), 'required' => 'required']) }}
        </div>

        {!! Form::hidden('role', 'company') !!}

        <div class="zameen-form-group">
          <div class="zameen-toggle-container">
            <div>
              <label class="zameen-label" style="margin-bottom: 0;">{{ __('Enable Login') }}</label>
              <p style="font-size: 0.75rem; color: var(--zameen-gray-500); margin: 0;">Allow company to access the system</p>
            </div>
            <div class="zameen-toggle">
              <input type="checkbox" name="password_switch" id="password_switch" value="on">
              <label class="zameen-toggle-slider" for="password_switch"></label>
            </div>
          </div>
        </div>

        <div class="ps_div d-none">
          <div class="zameen-form-group">
            <label class="zameen-label">{{ __('Password') }}</label>
            {{ Form::password('password', ['id' => 'password', 'class' => 'zameen-input', 'placeholder' => __('Enter Password'), 'minlength' => '6']) }}
          </div>
        </div>

      @else
        <div class="zameen-avatar-section">
          <img id="blah3" class="zameen-avatar-preview" src="{{ asset('web-assets/dashboard/icons/avatar.png') }}" alt="avatar">
          <div>
            <label for="avatar" class="zameen-upload-btn">
              <i class="ti ti-upload"></i>{{ __('Choose Profile Picture') }}
            </label>
            <input type="file" class="zameen-file-input" name="avatar" id="avatar" accept="image/*" onchange="document.getElementById('blah3').src = window.URL.createObjectURL(this.files[0])">
          </div>
        </div>

        <div class="zameen-form-group">
          <label class="zameen-label">
            {{ __('Name') }}
            <span class="zameen-required">*</span>
          </label>
          {{ Form::text('name', null, ['class' => 'zameen-input', 'placeholder' => __('Enter Name'), 'required' => 'required']) }}
        </div>

        <div class="zameen-form-group">
          <label class="zameen-label">
            {{ __('Email') }}
            <span class="zameen-required">*</span>
          </label>
            {{ Form::text('name', null, ['class' => 'zameen-input','placeholder' => __('Enter Name'),'required' => 'required']) }}
        </div>

        <div class="zameen-form-group">
          <label class="zameen-label">
            {{ __('User Role') }}
            <span class="zameen-required">*</span>
          </label>
          {!! Form::select('role', $roles, null, ['class' => 'zameen-select', 'required' => 'required']) !!}
        </div>

        <div class="d-none">
          <div class="zameen-form-group">
            <label class="zameen-label">
              {{ __('Date of Birth') }}
              <span class="zameen-required">*</span>
            </label>
            {!! Form::date('date_of_birth', old('date_of_birth', '2000-01-01'), ['class' => 'zameen-input', 'required' => 'required']) !!}
          </div>
        </div>

        <div class="zameen-form-group">
          <div class="zameen-toggle-container">
            <div>
              <label class="zameen-label" style="margin-bottom: 0;">{{ __('Enable Login') }}</label>
              <p style="font-size: 0.75rem; color: var(--zameen-gray-500); margin: 0;">Allow user to access the system</p>
            </div>
            <div class="zameen-toggle">
              <input type="checkbox" name="password_switch" id="password_switch" value="on">
              <label class="zameen-toggle-slider" for="password_switch"></label>
            </div>
          </div>
        </div>

        <div class="ps_div d-none">
          <div class="zameen-form-group">
            <label class="zameen-label">{{ __('Password') }}</label>
            {{ Form::password('password', ['id' => 'password', 'class' => 'zameen-input', 'placeholder' => __('Enter Password'), 'minlength' => '6']) }}
          </div>
        </div>
      @endif

      @if (!$customFields->isEmpty())
        <div class="zameen-form-group">
          <div class="tab-pane fade show" id="tab-2" role="tabpanel">
            @include('customFields.formBuilder')
          </div>
        </div>
      @endif
    </div>
  </div>
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
    {{ __('Create') }}
  </button>
</div>

{{ Form::close() }}