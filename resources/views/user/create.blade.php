{{ Form::open([
    'url' => 'users',
    'method' => 'post',
    'class' => 'needs-validation',
    'novalidate',
    'files' => true,
]) }}

<div class="modal-body">
    @if (\Auth::user()->type == 'super admin')
        <div class="form-group">
            <label for="avatar" class="form-label">{{ __('Profile Picture') }}</label>
            <div class="choose-files">
                <label for="avatar">
                    <div class="bg-primary profile_update">
                        <i class="ti ti-upload px-1"></i>{{ __('Choose file here') }}
                    </div>
                    <input type="file" class="form-control file" name="avatar" id="avatar" accept="image/*" onchange="document.getElementById('blah3').src = window.URL.createObjectURL(this.files[0])">
                </label>
                <img id="blah3" class="img-fluid mx-auto d-block h-auto" src="{{ asset('web-assets/dashboard/icons/avatar.png') }}" alt="avatar" width="75" height="75">
            </div>
        </div>

        <div class="form-group">
            <label for="name" class="col-form-label">{{ __('Company Name') }} <span class="text-danger">*</span></label>
            {{ Form::text('name', null, ['class' => 'form-control', 'placeholder' => __('Enter Company Name'), 'required' => 'required']) }}
        </div>

        <div class="form-group">
            <label for="email" class="col-form-label">{{ __('Company Email') }} <span class="text-danger">*</span></label>
            {{ Form::email('email', null, ['class' => 'form-control', 'placeholder' => __('Enter Company Email'), 'required' => 'required']) }}
        </div>

        {!! Form::hidden('role', 'company') !!}

        <div class="form-group">
            <div class="form-switch d-inline-block">
                <input type="checkbox" name="password_switch" class="form-check-input" value="on" id="password_switch">
                <label class="custom-control-label form-control-label" for="password_switch">{{ __('Enable Login') }}</label>
            </div>
        </div>

        <div class="ps_div d-none">
            <div class="form-group">
                <label for="password" class="col-form-label">{{ __('Password') }}</label>
                {{ Form::password('password', ['id' => 'password', 'class' => 'form-control', 'placeholder' => __('Enter Password'), 'minlength' => '6']) }}
            </div>
        </div>

    @else
        <div class="form-group">
            <label for="avatar" class="form-label">{{ __('Profile Picture') }}</label>
            <div class="choose-files">
                <label for="avatar">
                    <div class="bg-primary profile_update">
                        <i class="ti ti-upload px-1"></i>{{ __('Choose file here') }}
                    </div>
                    <input type="file" class="form-control file" name="avatar" id="avatar" accept="image/*" onchange="document.getElementById('blah3').src = window.URL.createObjectURL(this.files[0])">
                </label>
                <img id="blah3" class="img-fluid mx-auto d-block h-auto" src="{{ asset('web-assets/dashboard/icons/avatar.png') }}" alt="avatar" width="75" height="75">
            </div>
        </div>

        <div class="form-group">
            <label for="name" class="col-form-label">{{ __('Name') }} <span class="text-danger">*</span></label>
            {{ Form::text('name', null, ['class' => 'form-control', 'placeholder' => __('Enter Name'), 'required' => 'required']) }}
        </div>

        <div class="form-group">
            <label for="email" class="col-form-label">{{ __('Email') }} <span class="text-danger">*</span></label>
            {{ Form::text('email', null, ['class' => 'form-control', 'placeholder' => __('Enter Email'), 'required' => 'required']) }}
        </div>

        <div class="form-group">
            <label for="role" class="col-form-label">{{ __('User Role') }} <span class="text-danger">*</span></label>
            {!! Form::select('role', $roles, null, ['class' => 'form-control select', 'required' => 'required']) !!}
        </div>

        <div class="d-none">
            <div class="form-group">
                <label for="date_of_birth" class="col-form-label">{{ __('Date of Birth') }} <span class="text-danger">*</span></label>
                {!! Form::date('date_of_birth', old('date_of_birth', '2000-01-01'), ['class' => 'form-control', 'required' => 'required']) !!}
            </div>
        </div>

        <div class="form-group">
            <div class="form-switch d-inline-block">
                <input type="checkbox" name="password_switch" class="form-check-input" value="on" id="password_switch">
                <label class="custom-control-label form-control-label" for="password_switch">{{ __('Enable Login') }}</label>
            </div>
        </div>

        <div class="ps_div d-none">
            <div class="form-group">
                <label for="password" class="col-form-label">{{ __('Password') }}</label>
                {{ Form::password('password', ['id' => 'password', 'class' => 'form-control', 'placeholder' => __('Enter Password'), 'minlength' => '6']) }}
            </div>
        </div>
    @endif

    @if (!$customFields->isEmpty())
        <div class="col-12">
            <div class="tab-pane fade show" id="tab-2" role="tabpanel">
                @include('customFields.formBuilder')
            </div>
        </div>
    @endif
</div>

<div class="modal-footer">
    <input type="button" value="{{ __('Cancel') }}" class="btn btn-light" data-bs-dismiss="modal">
    <input type="submit" value="{{ __('Create') }}" class="btn btn-primary">
</div>

{{ Form::close() }}