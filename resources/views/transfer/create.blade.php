@php
    $plan = \App\Models\Utility::getChatGPTSettings();
@endphp

<style>
:root {
    --zameen-primary: #007c38;
    --zameen-primary-dark: #1e8a5f;
    --zameen-secondary: #f8fafc;
    --zameen-accent: #e2f4ed;
    --zameen-text: #2d3748;
    --zameen-border: #e2e8f0;
    --zameen-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    --zameen-gradient: linear-gradient(135deg, #007c38 0%, #10b981 100%);
}

.zameen-modal-header {
    background: var(--zameen-gradient);
    color: white;
    padding: 1.5rem 2rem;
    border: none;
    display: flex;
    align-items: center;
    gap: 12px;
}

.zameen-form-container {
    background: white;
    padding: 2rem;
    border-radius: 0 0 12px 12px;
    box-shadow: var(--zameen-shadow);
}

.zameen-form-group {
    margin-bottom: 1.5rem;
}

.zameen-form-label {
    display: block;
    font-weight: 600;
    color: var(--zameen-text);
    margin-bottom: 0.5rem;
    font-size: 0.875rem;
}

.zameen-form-control {
    width: 100%;
    padding: 0.75rem 1rem;
    border: 2px solid var(--zameen-border);
    border-radius: 8px;
    font-size: 0.875rem;
    transition: all 0.2s ease;
    background-color: #fff;
}

.zameen-form-control:focus {
    outline: none;
    border-color: var(--zameen-primary);
    box-shadow: 0 0 0 3px rgba(39, 167, 118, 0.1);
}

.zameen-input-icon {
    position: relative;
}

.zameen-input-icon .form-control {
    padding-left: 2.5rem;
}

.zameen-input-icon::before {
    content: '';
    position: absolute;
    left: 0.75rem;
    top: 50%;
    transform: translateY(-50%);
    width: 1rem;
    height: 1rem;
    background-size: contain;
    z-index: 2;
}

.zameen-btn {
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    font-weight: 600;
    font-size: 0.875rem;
    text-transform: uppercase;
    letter-spacing: 0.025em;
    transition: all 0.2s ease;
    border: none;
    cursor: pointer;
}

.zameen-btn-primary {
    background: var(--zameen-gradient);
    color: white;
}

.zameen-btn-primary:hover {
    transform: translateY(-1px);
    box-shadow: 0 8px 15px rgba(39, 167, 118, 0.3);
}

.zameen-btn-light {
    background: #f8fafc;
    color: #64748b;
    border: 1px solid #e2e8f0;
}

.zameen-btn-light:hover {
    background: #f1f5f9;
}
</style>

{{ Form::open(['url' => 'transfer','class'=>'needs-validation','novalidate']) }}

<div class="zameen-modal-header">
    <i class="fas fa-exchange-alt" style="font-size: 1.25rem;"></i>
    <h4 class="mb-0">{{ __('Create Transfer') }}</h4>
</div>

<div class="zameen-form-container ">
    <div class="row ">
        @if ($plan->enable_chatgpt == 'on')
            <div class="col-12 text-end mb-3">
                <a href="#" data-size="md" data-ajax-popup-over="true"
                    data-url="{{ route('generate', ['transfer']) }}" data-bs-toggle="tooltip" data-bs-placement="top"
                    title="{{ __('Generate') }}" data-title="{{ __('Generate content with AI') }}"
                    class="zameen-btn zameen-btn-primary btn-sm">
                    <i class="fas fa-robot"></i>
                    {{ __('Generate with AI') }}
                </a>
            </div>
        @endif
        <div class="zameen-form-group col-md-6">
            {{ Form::label('from_account', __('From Account'), ['class' => 'zameen-form-label']) }}
            <div class="zameen-input-icon" style="--icon: url('data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 fill=%22%23007c38%22 viewBox=%220 0 24 24%22><path d=%22M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z%22/></svg>');">
                {{ Form::select('from_account', $bankAccount, null, ['class' => 'zameen-form-control', 'required' => 'required']) }}
            </div>
        </div>
        <div class="zameen-form-group col-md-6">
            {{ Form::label('to_account', __('To Account*'), ['class' => 'zameen-form-label']) }}
            <div class="zameen-input-icon" style="--icon: url('data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 fill=%22%23007c38%22 viewBox=%220 0 24 24%22><path d=%22M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z%22/></svg>');">
                {{ Form::select('to_account', $bankAccount, null, ['class' => 'zameen-form-control', 'required' => 'required']) }}
            </div>
        </div>
        <div class="zameen-form-group col-md-6">
            {{ Form::label('amount', __('Amount*'), ['class' => 'zameen-form-label']) }}
            <div class="zameen-input-icon" style="--icon: url('data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 fill=%22%23007c38%22 viewBox=%220 0 24 24%22><path d=%22M11.8 10.9c-2.27-.59-3-1.2-3-2.15 0-1.09 1.01-1.85 2.7-1.85 1.78 0 2.44.85 2.5 2.1h2.21c-.07-1.72-1.12-3.3-3.21-3.81V3h-3v2.16c-1.94.42-3.5 1.68-3.5 3.61 0 2.31 1.91 3.46 4.7 4.13 2.5.6 3 1.48 3 2.41 0 .69-.49 1.79-2.7 1.79-2.06 0-2.87-.92-2.98-2.1h-2.2c.12 2.19 1.76 3.42 3.68 3.83V21h3v-2.15c1.95-.37 3.5-1.5 3.5-3.55 0-2.84-2.43-3.81-4.7-4.4z%22/></svg>');">
                {{ Form::number('amount', '', ['class' => 'zameen-form-control', 'required' => 'required', 'step' => '0.01']) }}
            </div>
        </div>
        <div class="zameen-form-group col-md-6">
            {{ Form::label('date', __('Date*'), ['class' => 'zameen-form-label']) }}
            <div class="zameen-input-icon" style="--icon: url('data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 fill=%22%23007c38%22 viewBox=%220 0 24 24%22><path d=%22M9 11H7v2h2v-2zm4 0h-2v2h2v-2zm4 0h-2v2h2v-2zm2-7h-1V2h-2v2H8V2H6v2H5c-1.1 0-1.99.9-1.99 2L3 20c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V9h14v11z%22/></svg>');">
                {{ Form::date('date', date('Y-m-d'), ['class' => 'zameen-form-control', 'required' => 'required']) }}
            </div>
        </div>
        <div class="zameen-form-group col-md-12">
            {{ Form::label('reference', __('Reference'), ['class' => 'zameen-form-label']) }}
            <div class="zameen-input-icon" style="--icon: url('data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 fill=%22%23007c38%22 viewBox=%220 0 24 24%22><path d=%22M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2M18,20H6V4H13V9H18V20Z%22/></svg>');">
                {{ Form::text('reference', '', ['class' => 'zameen-form-control']) }}
            </div>
        </div>
        <div class="zameen-form-group col-md-12 mb-0">
            {{ Form::label('description', __('Description*'), ['class' => 'zameen-form-label']) }}
            <div class="zameen-input-icon" style="--icon: url('data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 fill=%22%23007c38%22 viewBox=%220 0 24 24%22><path d=%22M14,10H19.5L14,4.5V10M5,3H15L21,9V19A2,2 0 0,1 19,21H5C3.89,21 3,20.1 3,19V5C3,3.89 3.89,3 5,3M5,12V14H19V12H5M5,16V18H14V16H5Z%22/></svg>');">
                {{ Form::textarea('description', '', ['class' => 'zameen-form-control', 'rows' => 3, 'required' => 'required']) }}
            </div>
        </div>
    </div>
</div>

<div class="modal-footer" style="background: #f8fafc; border-top: 1px solid #e2e8f0; padding: 1.5rem 2rem; display: flex; justify-content: flex-end; gap: 1rem;">
    <input type="button" value="{{ __('Cancel') }}" class="zameen-btn zameen-btn-light" data-bs-dismiss="modal">
    <input type="submit" value="{{ __('Create Transfer') }}" class="zameen-btn zameen-btn-primary">
</div>
{{ Form::close() }}
