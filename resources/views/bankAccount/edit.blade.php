<script src="{{ asset('js/unsaved.js') }}"></script>

<div class="zameen-card-header" style="background: linear-gradient(135deg, #007c38 0%, #10b981 100%); color: white; padding: 1.5rem 2rem 1rem; border-bottom: none; position: relative;">
    <div style="display: flex; align-items: center; justify-content: center; gap: 0.5rem; width: 100%;">
        <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24" style="display: block;">
            <path d="M2 4h20v4H2V4zm0 6h20v10H2V10zm4 2h12v2H6v-2zm0 4h8v2H6v-2z"/>
        </svg>
        <h5 style="margin: 0; font-weight: 600; font-size: 1.25rem; color: white; text-align: center;">{{ __('Edit Bank Account Details') }}</h5>
    </div>
</div>

{{ Form::model($bankAccount, ['route' => ['bank-account.update', $bankAccount->id], 'method' => 'PUT','class'=>'needs-validation','novalidate']) }}
<div class="modal-body bg-[#FAFBFC]">
    <div class="bg-white p-6 rounded-[8px] border border-[#E5E7EB] shadow-sm overflow-hidden">
    <div class="row">
        <div class="form-group col-md-6">
            {{ Form::label('chart_account_id', __('Account'), ['class' => 'form-label']) }}<x-required></x-required>
            <select name="chart_account_id" class="form-control" required="required">
                @foreach ($chartAccounts as $key => $chartAccount)
                    <option value="{{ $key }}" class="subAccount"
                        {{ $bankAccount->chart_account_id == $key ? 'selected' : '' }}>{{ $chartAccount }}</option>
                    @foreach ($subAccounts as $subAccount)
                        @if ($key == $subAccount['account'])
                            <option value="{{ $subAccount['id'] }}" class="ms-5"
                                {{ $bankAccount->chart_account_id == $subAccount['id'] ? 'selected' : '' }}> &nbsp;
                                &nbsp;&nbsp; {{ $subAccount['name'] }}</option>
                        @endif
                    @endforeach
                @endforeach
            </select>
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('holder_name', __('Bank Holder Name'), ['class' => 'form-label']) }}<x-required></x-required>
            <div class="form-icon-user">
                <span><i class="ti ti-address-card"></i></span>
                {{ Form::text('holder_name', null, ['class' => 'form-control', 'required' => 'required']) }}
            </div>
        </div>
        <div class="form-group  col-md-6">
            {{ Form::label('bank_name', __('Bank Name'), ['class' => 'form-label']) }}<x-required></x-required>
            <div class="form-icon-user">
                <span><i class="ti ti-university"></i></span>
                {{ Form::text('bank_name', null, ['class' => 'form-control', 'required' => 'required']) }}
            </div>
        </div>
        <div class="form-group  col-md-6">
            {{ Form::label('account_number', __('Account Number'), ['class' => 'form-label']) }}<x-required></x-required>
            <div class="form-icon-user">
                <span><i class="ti ti-notes-medical"></i></span>
                {{ Form::text('account_number', null, ['class' => 'form-control', 'required' => 'required']) }}
            </div>  
        </div>
        <div class="form-group  col-md-6">
            {{ Form::label('opening_balance', __('Opening Balance'), ['class' => 'form-label']) }}<x-required></x-required>
            <div class="form-icon-user">
                <span><i class="ti ti-dollar-sign"></i></span>
                {{ Form::number('opening_balance', null, ['class' => 'form-control', 'required' => 'required', 'step' => '0.01']) }}
            </div>
        </div>

        <x-mobile  div-class="col-md-6 " name="contact_number" label="{{ __('Contact Number') }} " ></x-mobile>

        <div class="form-group  col-md-12">
            {{ Form::label('bank_address', __('Bank Address'), ['class' => 'form-label']) }}
            {{ Form::textarea('bank_address', null, ['class' => 'form-control', 'rows' => 2]) }}
        </div>
        @if (!$customFields->isEmpty())
            <div class="col-md-12">
                <div class="tab-pane fade show" id="tab-2" role="tabpanel">
                    @include('customFields.formBuilder')
                </div>
            </div>
        @endif
    </div>
</div>
</div>
<div class="modal-footer border-t border-[#E5E5E5] bg-[#FAFAFA] px-6 py-4 flex justify-end gap-3">
    <input type="button" value="{{ __('Cancel') }}" class="btn  btn-light" data-bs-dismiss="modal">
    <input type="submit" value="{{ __('Update') }}" class="btn  btn-primary">
</div>
{{ Form::close() }}

<style>
  /* Hide only the default modal header and title, but keep the close button visible */
  #commonModal .modal-header, #commonModal .modal-title {
    display: none !important;
  }
  /* Unhide the default close button */
  #commonModal .btn-close {
    display: block !important;
    position: absolute;
    right: 1.5rem;
    top: 1.2rem;
    color: white;
    background: none;
    font-size: 1.5rem;
    opacity: 1;
  }
  #commonModal .btn-close:hover {
    color: #f87171;
    background: rgba(255,255,255,0.1);
  }
  #commonModal .modal-content {
    padding-top: 0 !important;
  }
  .zameen-close-btn:hover {
    color: #f87171;
    background: rgba(255,255,255,0.1);
  }
</style>
