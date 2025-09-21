@php
    $plan = \App\Models\Utility::getChatGPTSettings();
@endphp
{{ Form::model($customField, ['route' => ['custom-field.update', $customField->id], 'method' => 'PUT','class'=>'needs-validation','novalidate']) }}
<div class="modal-body bg-[#FAFBFC]">
     <div class="card-body bg-white p-6 rounded-[8px] border border-[#E5E7EB] shadow-sm overflow-hidden">
    <div class="row">
        @if ($plan->enable_chatgpt == 'on')
            <div>
                <a href="#" data-size="md" data-ajax-popup-over="true"
                    data-url="{{ route('generate', ['custom field']) }}" data-bs-toggle="tooltip" data-bs-placement="top"
                    title="{{ __('Generate') }}" data-title="{{ __('Generate content with AI') }}"
                    class="btn btn-primary btn-sm float-end">
                    <i class="fas fa-robot"></i>
                    {{ __('Generate with AI') }}
                </a>
            </div>
        @endif
        <div class="form-group col-md-12">
            {{ Form::label('name', __('Custom Field Name'), ['class' => 'form-label']) }}<x-required></x-required>
            {{ Form::text('name', null, ['class' => 'form-control', 'required' => 'required']) }}
        </div>
        <div class="form-group col-md-12">
            {{ Form::label('type', __('Type'), ['class' => 'form-label']) }}
            <div class="form-control-plaintext">{{ $types[$customField->type] ?? $customField->type }}</div>
        </div>
        @if($customField->type == 'select')
        <div class="form-group col-md-12">
            {{ Form::label('options', __('Dropdown Options'), ['class' => 'form-label']) }}<x-required></x-required>
            {{ Form::textarea('options', null, ['class' => 'form-control', 'rows' => 3, 'placeholder' => 'Enter each option on a new line']) }}
            <small class="form-text text-muted">{{ __('Enter each option on a new line') }}</small>
        </div>
        @endif
        <div class="form-group col-md-12">
            {{ Form::label('module', __('Module'), ['class' => 'form-label']) }}
            <div class="form-control-plaintext">{{ $modules[$customField->module] ?? $customField->module }}</div>
        </div>

    </div>
</div>
</div>
<div class="modal-footer border-t border-[#E5E5E5] bg-[#FAFAFA] px-6 py-4 flex justify-end gap-3">
    <input type="button" value="{{ __('Cancel') }}" class="btn  btn-light" data-bs-dismiss="modal">
    <input type="submit" value="{{ __('Update') }}" class="btn  btn-primary">
</div>
{{ Form::close() }}
