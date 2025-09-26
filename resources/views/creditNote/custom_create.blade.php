
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

{{ Form::open(array('route' => array('invoice.custom.credit.note.store'),'mothod'=>'post','class'=>'needs-validation','novalidate')) }}
<div class="zameen-user-container">
    <div class="zameen-user-card">
        <div class="zameen-user-header">
            <h2>{{ __('Create New Credit Note') }}</h2>
            <p>{{ __('Add a new credit note') }}</p>
        </div>
        <div class="zameen-form-container" style="display: flex; flex-direction: column; gap: 1rem;">
            <div class="zameen-form-group">
                <label class="zameen-label">{{ __('Invoice') }}<span class="zameen-required">*</span></label>
                <select class="zameen-input zameen-select" required="required" id="invoice" name="invoice">
                    <option value="0">{{__('Select Invoice')}}</option>
                    @foreach($invoices as $key=>$invoice)
                        <option value="{{$key}}">{{\Auth::user()->invoiceNumberFormat($invoice)}}</option>
                    @endforeach
                </select>
            </div>
            <div class="zameen-form-group">
                <label class="zameen-label">{{ __('Amount') }}<span class="zameen-required">*</span></label>
                {{ Form::number('amount', null, ['class' => 'zameen-input', 'required' => 'required', 'step' => '0.01']) }}
            </div>
            <div class="zameen-form-group">
                <label class="zameen-label">{{ __('Date') }}<span class="zameen-required">*</span></label>
                {{ Form::date('date', date('Y-m-d'), ['class' => 'zameen-input', 'required' => 'required']) }}
            </div>
            <div class="zameen-form-group">
                <label class="zameen-label">{{ __('Description') }}</label>
                {{ Form::textarea('description', null, ['class' => 'zameen-input', 'rows' => 3]) }}
            </div>
        </div>
        <div class="modal-footer border-top bg-light d-flex justify-content-end gap-2 p-3">
            <input type="button" value="{{__('Cancel')}}" class="btn btn-light" data-bs-dismiss="modal">
            <input type="submit" value="{{__('Create')}}" class="btn btn-primary">
        </div>
    </div>
</div>
{{ Form::close() }}