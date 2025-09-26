<script src="{{ asset('js/unsaved.js') }}"></script>

@php
    $plan = \App\Models\Utility::getChatGPTSettings();
@endphp
{{ Form::open(['url' => 'chart-of-account','class'=>'needs-validation','novalidate']) }}
<div  style="background: linear-gradient(135deg, #007c38 0%, #10b981 100%); padding: 1.5rem 2rem; color: white; text-align: center; flex-shrink: 0; border-radius: 0;">
    <h5  style="margin: 0; font-weight: 600; font-size: 1.15rem;">Create Chart of Account</h5>
</div>
<div class="modal-body p-3 bg-[#FAFBFC]">
    <div class="bg-white overflow-hidden">
       <div class="row p-6">
        @if ($plan->enable_chatgpt == 'on')
            <div>
                <a href="#" data-size="md" data-ajax-popup-over="true"
                    data-url="{{ route('generate', ['chart of accounts']) }}" data-bs-toggle="tooltip"
                    data-bs-placement="top" title="{{ __('Generate') }}" data-title="{{ __('Generate content with AI') }}"
                    class="btn btn-primary btn-sm float-end">
                    <i class="fas fa-robot"></i>
                    {{ __('Generate with AI') }}
                </a>
            </div>
        @endif
    <div class="flex flex-col gap-2 mb-3">
            {{ Form::label('name', __('Name*'), ['class' => 'form-label']) }}
            {{ Form::text('name', '', ['class' => 'form-control', 'required' => 'required']) }}
        </div>
    <div class="flex flex-col gap-2 mb-3">
            {{ Form::label('code', __('Code*'), ['class' => 'form-label']) }}
            {{ Form::text('code', '', ['class' => 'form-control', 'required' => 'required']) }}
        </div>
    <div class="flex flex-col gap-2 mb-3">
            {{ Form::label('sub_type', __('Account Type*'), ['class' => 'form-label']) }}
            {{ Form::select('sub_type', $account_type, null, ['class' => 'form-control select', 'required' => 'required']) }}
        </div>

        <div class="col-md-2">
            <div class="form-group">
                {{ Form::label('is_enabled', __('Is Enabled'), ['class' => 'form-label']) }}
                <div class="form-check form-switch">
                    <input type="checkbox" class="form-check-input" name="is_enabled" id="is_enabled" checked>
                    <label class="custom-control-label form-check-label" for="is_enabled"></label>
                </div>
            </div>
        </div>

        <div class="col-md-4 mt-4 acc_check d-none">
            <div class="form-check">
                <input type="checkbox" class="form-check-input" id="account">
                <label class="form-check-label" for="account">{{__('Make this a sub-account')}}</label>
            </div>
        </div>

    <div class="flex flex-col gap-2 mb-3 acc_type d-none">
            {{ Form::label('parent', __('Parent Account'), ['class' => 'form-label']) }}
            <select class="form-control select" name="parent" id="parent">
            </select>
        </div>

    <div class="flex flex-col gap-2 mb-3 mb-0">
            {{ Form::label('description', __('Description'), ['class' => 'form-label']) }}
            {!! Form::textarea('description', null, ['class' => 'form-control', 'rows' => '2']) !!}
        </div>

    </div>
    </div>
</div>
<div class="modal-footer border-t border-[#E5E5E5] bg-[#FAFAFA] px-6 py-4 flex justify-end gap-3">
    <input type="button" value="{{__('Cancel')}}" class="btn  btn-light" data-bs-dismiss="modal">
    <input type="submit" value="{{__('Create')}}" class="btn  btn-primary">
</div>
{{ Form::close() }}