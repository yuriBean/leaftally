<script src="{{ asset('js/unsaved.js') }}"></script>

@php
    $plan = \App\Models\Utility::getChatGPTSettings();
@endphp
{{ Form::open(['url' => 'chart-of-account','class'=>'needs-validation','novalidate']) }}
<div class="modal-header" style="background: linear-gradient(90deg, #2e7d32 0%, #43a047 100%); color: #fff; border-top-left-radius: 8px; border-top-right-radius: 8px; padding: 18px 24px; margin-bottom: 0;">
    <h5 class="modal-title" style="margin: 0; font-weight: 600; font-size: 1.15rem;">Create Chart of Account</h5>
</div>
<div class="modal-body p-6 bg-[#FAFBFC]">
    <div class="bg-white rounded-[8px] border border-[#E5E7EB] shadow-sm overflow-hidden">
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
            {{ Form::label('name', __('Name'), ['class' => 'form-label']) }}<x-required></x-required>
            {{ Form::text('name', '', ['class' => 'form-control', 'required' => 'required']) }}
        </div>
    <div class="flex flex-col gap-2 mb-3">
            {{ Form::label('code', __('Code'), ['class' => 'form-label']) }}<x-required></x-required>
            {{ Form::text('code', '', ['class' => 'form-control', 'required' => 'required']) }}
        </div>
    <div class="flex flex-col gap-2 mb-3">
            {{ Form::label('sub_type', __('Account Type'), ['class' => 'form-label']) }}<x-required></x-required>
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
<div class="modal-footer flex justify-end gap-2 mt-4" style="border-top: none; background: #f7f7f7;">
    <button type="button" class="zameen-btn zameen-btn-cancel px-4 py-2" data-bs-dismiss="modal" style="background: #e0e0e0; color: #333;">{{ __('Cancel') }}</button>
    <button type="submit" class="zameen-btn zameen-btn-primary px-4 py-2" style="background: #2e7d32; color: #fff;">{{ __('Create') }}</button>
</div>
{{ Form::close() }}
