<script src="{{ asset('js/unsaved.js') }}"></script>

@php
    $plan = \App\Models\Utility::getChatGPTSettings();
@endphp
{{ Form::model($chartOfAccount, ['route' => ['chart-of-account.update', $chartOfAccount->id], 'method' => 'PUT','class'=>'needs-validation','novalidate']) }}
<div class="modal-header" style="background: linear-gradient(90deg, #2e7d32 0%, #43a047 100%); color: #fff; border-top-left-radius: 8px; border-top-right-radius: 8px; padding: 18px 24px; margin-bottom: 0;">
    <h5 class="modal-title" style="margin: 0; font-weight: 600; font-size: 1.15rem;">Edit Chart of Account</h5>
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
            {{ Form::text('name', null, ['class' => 'form-control', 'required' => 'required']) }}
        </div>
    <div class="flex flex-col gap-2 mb-3">
            {{ Form::label('code', __('Code'), ['class' => 'form-label']) }}<x-required></x-required>
            {{ Form::text('code', null, ['class' => 'form-control', 'required' => 'required']) }}
        </div>

    <div class="flex flex-col gap-2 mb-3">
            {{ Form::label('is_enabled', __('Is Enabled'), ['class' => 'form-label']) }}
            <div class="form-check form-switch">
                <input type="checkbox" class="form-check-input" name="is_enabled" id="is_enabled"
                    {{ $chartOfAccount->is_enabled == 1 ? 'checked' : '' }}>
                <label class="custom-control-label form-check-label" for="is_enabled"></label>
            </div>
        </div>

    <div class="flex flex-col gap-2 mb-3">
            {{ Form::label('description', __('Description'), ['class' => 'form-label']) }}
            {!! Form::textarea('description', null, ['class' => 'form-control', 'rows' => '3']) !!}
        </div>

    </div>
    </div>
</div>
<div class="modal-footer flex justify-end gap-2 mt-4" style="border-top: none; background: #f7f7f7;">
    <button type="button" class="zameen-btn zameen-btn-cancel px-4 py-2" data-bs-dismiss="modal" style="background: #e0e0e0; color: #333;">{{ __('Cancel') }}</button>
    <button type="submit" class="zameen-btn zameen-btn-primary px-4 py-2" style="background: #2e7d32; color: #fff;">{{ __('Update') }}</button>
</div>
{{ Form::close() }}
