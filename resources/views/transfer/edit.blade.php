@php
    $plan = \App\Models\Utility::getChatGPTSettings();
@endphp
<style>
  .modal-modern-header {
    background: linear-gradient(135deg, #007c38 0%, #10b981 100%);
    color: white;
    padding: 1.75rem 2rem 1.25rem;
    border-top-left-radius: 16px;
    border-top-right-radius: 16px;
    position: relative;
    text-align: center;
  }
  .modal-modern-header h2 {
    margin: 0;
    font-size: 1.5rem;
    font-weight: 600;
    color: white;
    text-align: center;
  }
  .modal-modern-close {
    position: absolute;
    top: 1.5rem;
    right: 2rem;
    color: white;
    font-size: 1.5rem;
    background: none;
    border: none;
    cursor: pointer;
  }
  .modal-modern-body {
    padding: 2rem;
    background: white;
    border-bottom-left-radius: 16px;
    border-bottom-right-radius: 16px;
    box-shadow: 0 4px 24px rgba(0,0,0,0.12);
  }
  .modal-modern-form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.5rem;
    margin-bottom: 1.5rem;
  }
  .modal-modern-form-group {
    margin-bottom: 1.25rem;
  }
  .modal-modern-label {
    font-weight: 500;
    color: #495057;
    margin-bottom: 0.5rem;
    font-size: 0.95rem;
    display: block;
  }
  .modal-modern-input, .modal-modern-select, .modal-modern-textarea {
    width: 100%;
    padding: 0.75rem 1rem;
    border: 1.5px solid #e0e0e0;
    border-radius: 8px;
    font-size: 1rem;
    background: white;
    box-sizing: border-box;
    transition: border-color 0.2s;
  }
  .modal-modern-input:focus, .modal-modern-select:focus, .modal-modern-textarea:focus {
    outline: none;
    border-color: #007c38;
    box-shadow: 0 0 0 3px rgba(39,167,118,0.1);
  }
  .modal-modern-footer {
    background: #f8f9fa;
    padding: 1.5rem 2rem;
    border-top: 1px solid #e0e0e0;
    display: flex;
    justify-content: flex-end;
    gap: 1rem;
    border-radius: 0 0 16px 16px;
  }
  #commonModal .modal-title {
    display: none !important;
  }
  #commonModal .modal-header .btn-close {
    display: none !important;
  }
  #commonModal .modal-header {
    margin: 0 !important;
    padding: 0 !important;
    min-height: 0 !important;
    height: 0 !important;
    border: none !important;
  }
  /* Hide default modal header and close button injected by AJAX system */
  #commonModal .modal-header, #commonModal .modal-title, #commonModal .btn-close {
    display: none !important;
  }
  /* Remove gap above custom header */
  #commonModal .modal-content {
    padding-top: 0 !important;
  }
</style>

{{ Form::model($transfer, ['route' => ['transfer.update', $transfer->id], 'method' => 'PUT','class'=>'needs-validation','novalidate']) }}
<div style="border-radius: 16px; overflow: hidden; box-shadow: 0 4px 24px rgba(0,0,0,0.12); width: 100%; max-width: 600px; margin: 2rem auto;">
  <div class="modal-modern-header">
    <h2>Edit Transfer</h2>
    <button type="button" class="modal-modern-close" data-bs-dismiss="modal" aria-label="Close">&times;</button>
  </div>
  <div class="modal-modern-body">
    <div class="modal-modern-form-row">
      <div class="modal-modern-form-group">
        <label class="modal-modern-label">From Account<span style="color: #f44336; margin-left: 0.25rem;">*</span></label>
        {{ Form::select('from_account', $bankAccount, null, ['class' => 'modal-modern-select', 'required' => 'required']) }}
      </div>
      <div class="modal-modern-form-group">
        <label class="modal-modern-label">To Account<span style="color: #f44336; margin-left: 0.25rem;">*</span></label>
        {{ Form::select('to_account', $bankAccount, null, ['class' => 'modal-modern-select', 'required' => 'required']) }}
      </div>
    </div>
    <div class="modal-modern-form-row">
      <div class="modal-modern-form-group">
        <label class="modal-modern-label">Amount<span style="color: #f44336; margin-left: 0.25rem;">*</span></label>
        {{ Form::number('amount', null, ['class' => 'modal-modern-input', 'required' => 'required', 'step' => '0.01']) }}
      </div>
      <div class="modal-modern-form-group">
        <label class="modal-modern-label">Date<span style="color: #f44336; margin-left: 0.25rem;">*</span></label>
        {{ Form::date('date', null, ['class' => 'modal-modern-input', 'required' => 'required']) }}
      </div>
    </div>
    <div class="modal-modern-form-group">
      <label class="modal-modern-label">Reference</label>
      {{ Form::text('reference', null, ['class' => 'modal-modern-input']) }}
    </div>
    <div class="modal-modern-form-group">
      <label class="modal-modern-label">Description<span style="color: #f44336; margin-left: 0.25rem;">*</span></label>
      {{ Form::textarea('description', null, ['class' => 'modal-modern-textarea', 'rows' => 3, 'required' => 'required']) }}
    </div>
  </div>
  <div class="modal-modern-footer">
    <button type="button" class="btn btn-light" data-bs-dismiss="modal" style="border-radius: 8px; padding: 0.75rem 1.5rem; border: 1.5px solid #e0e0e0; color: #2d3748; font-weight: 500; background: #fff;">Cancel</button>
    <button type="submit" class="btn btn-success" style="background: #007c38; color: #fff; border-radius: 8px; padding: 0.75rem 1.5rem; font-weight: 500; border: none;">Update</button>
  </div>
</div>
{{ Form::close() }}
