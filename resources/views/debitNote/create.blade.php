<script src="{{ asset('js/unsaved.js') }}"></script>


<style>
    .zameen-user-container { background: #f8f9fa; padding: 0; max-height: 90vh; overflow-y: auto; }
    .zameen-user-card { background: white; border-radius: 0; box-shadow: none; width: 100%; max-width: none; margin: 0; max-height: 90vh; display: flex; flex-direction: column; }
    form.needs-validation { width: 100%; }
    .zameen-user-header { background: linear-gradient(135deg, #007c38 0%, #10b981 100%); padding: 1.5rem 2rem; color: white; text-align: center; flex-shrink: 0; border-radius: 0; }
    .zameen-user-header h2 { margin: 0; font-size: 1.5rem; font-weight: 600; }
    .zameen-user-header p { margin: 0; opacity: 0.9; font-size: 0.875rem; }
    .zameen-form-container { padding: 1.25rem 1.5rem; overflow-y: auto; flex: 1; }
    .zameen-form-group { margin-bottom: 1rem; }
    .zameen-label { font-weight: 500; color: #495057; margin-bottom: 0.5rem; font-size: 0.95rem; display: block; }
    .zameen-required { color: #f44336; margin-left: 0.25rem; }
    .zameen-input, .zameen-select { padding: 0.75rem 1rem; border: 1px solid #dee2e6; border-radius: 8px; font-size: 1rem; background: white; width: 100%; box-sizing: border-box; }
    .zameen-input:focus, .zameen-select:focus { outline: none; border-color: #007c38; box-shadow: 0 0 0 3px rgba(39,167,118,0.1); }
    .zameen-footer { background: #f8f9fa; padding: 1.25rem 1.5rem; border-top: 1px solid #f0f0f0; display: flex; justify-content: flex-end; gap: 1rem; flex-shrink: 0; }
    .zameen-btn { padding: 0.5rem 1.25rem; font-size: 1rem; border-radius: 8px; font-weight: 500; cursor: pointer; }
</style>

{{ Form::open(array('route' => array('bill.debit.note.store',$bill_id),'mothod'=>'post','class'=>'needs-validation','novalidate')) }}
<div class="zameen-user-container">
    <div class="zameen-user-card">
        <div class="zameen-user-header">
            <h2>{{ __('Create Debit Note') }}</h2>
            <p>{{ __('Add a new debit note') }}</p>
        </div>
        <div class="zameen-form-container">
            <div class="zameen-form-group">
                <label class="zameen-label">{{ __('Date') }}<span class="zameen-required">*</span></label>
                {{ Form::date('date', null, ['class' => 'zameen-input', 'required' => 'required']) }}
            </div>
            <div class="zameen-form-group">
                <label class="zameen-label">{{ __('Amount') }}<span class="zameen-required">*</span></label>
                {{ Form::number('amount', !empty($billDue)?$billDue->getDue():0, ['class' => 'zameen-input', 'required' => 'required', 'step' => '0.01']) }}
            </div>
            <div class="zameen-form-group">
                <label class="zameen-label">{{ __('Description') }}</label>
                {{ Form::textarea('description', null, ['class' => 'zameen-input', 'rows' => 3]) }}
            </div>
        </div>
        <div class="zameen-footer">
            <button type="button" class="zameen-btn" style="background: #fff; color: #007c38; border: 1.5px solid #007c38; transition: background 0.2s, color 0.2s; padding: 0.5rem 1.25rem;" data-bs-dismiss="modal"
                onmouseover="this.style.background='#007c38';this.style.color='#fff'" onmouseout="this.style.background='#fff';this.style.color='#007c38'">
                <svg style="width: 16px; height: 16px; margin-right: 0.5rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
                {{ __('Cancel') }}
            </button>
            <button type="submit" class="zameen-btn" style="background: linear-gradient(135deg, #007c38 0%, #10b981 100%); color: #fff; border: none; transition: background 0.2s; padding: 0.5rem 1.25rem;">
                <svg style="width: 16px; height: 16px; margin-right: 0.5rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                {{ __('Add') }}
            </button>
        </div>
    </div>
</div>
{{ Form::close() }}
