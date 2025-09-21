<script src="{{ asset('js/unsaved.js') }}"></script>

{{-- resources/views/PayRoll/create.blade.php --}}
{{ Form::open(['url' => 'payroll', 'method' => 'post', 'class' => 'needs-validation', 'novalidate']) }}
<div class="modal-body p-6 bg-[#FAFBFC]">
  <div class="row">
    <div class="col-md-6"><!-- Basic info -->
      <div class="bg-white rounded-[8px] border border-[#E5E7EB] mb-6 shadow-sm overflow-hidden">
        <div class="heading-cstm-form">
          <h6 class="mb-0 flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
            </svg>
            {{ __('Basic Info') }}
          </h6>
        </div>
        <div class="row p-6">
          <div class="col-lg-12 col-md-4 col-sm-6">
            <div class="form-group mb-0">
              {{ Form::label('employee_id', __('Select Employee'), ['class' => 'form-label']) }} <x-required />
              <div class="form-icon-user">
                {!! Form::select('employee_id', $employees, null, [
                  'class' => 'form-control border border-[#E5E5E5] rounded-[8px] px-4 py-2 text-[12px] text-[#323232] leading-[24px] font-[400]',
                  'required' => 'required',
                ]) !!}
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-md-6"><!-- Salary details -->
      <div class="bg-white rounded-[8px] border border-[#E5E7EB] mb-6 shadow-sm overflow-hidden">
        <div class="heading-cstm-form">
          <h6 class="mb-0 flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
            </svg>
            {{ __('Salary details') }}
          </h6>
        </div>
        <div class="row p-6">
          <div class="col-lg-12 col-md-12 col-sm-6">
            <div class="form-group mb-0">
              {{ Form::label('basic_salary', __('Basic Salary'), ['class' => 'form-label']) }}
              <div class="form-icon-user">
                {{ Form::number('basic_salary', null, ['class' => 'form-control', 'min'=>0, 'step'=>'0.01']) }}
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    {{-- Allowance --}}
    <div class="col-md-4">
      <div class="bg-white rounded-[8px] border border-[#E5E7EB] shadow-sm overflow-hidden">
        <div class="heading-cstm-form">
          <h6 class="mb-0 flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-hand-index-thumb" viewBox="0 0 16 16">
              <path d="M6.75 1a.75.75 0 0 1 .75.75V8a.5.5 0 0 0 1 0V5.467l.086-.004c.317-.012.637-.008.816.027.134.027.294.096.448.182.077.042.15.147.15.314V8a.5.5 0 0 0 1 0V6.435l.106-.01c.316-.024.584-.01.708.04.118.046.3.207.486.43.081.096.15.19.2.259V8.5a.5.5 0 1 0 1 0v-1h.342a1 1 0 0 1 .995 1.1l-.271 2.715a2.5 2.5 0 0 1-.317.991l-1.395 2.442a.5.5 0 0 1-.434.252H6.118a.5.5 0 0 1-.447-.276l-1.232-2.465-2.512-4.185a.517.517 0 0 1 .809-.631l2.41 2.41A.5.5 0 0 0 6 9.5V1.75A.75.75 0 0 1 6.75 1M8.5 4.466V1.75a1.75 1.75 0 1 0-3.5 0v6.543L3.443 6.736A1.517 1.517 0 0 0 1.07 8.588l2.491 4.153 1.215 2.43A1.5 1.5 0 0 0 6.118 16h6.302a1.5 1.5 0 0 0 1.302-.756l1.395-2.441a3.5 3.5 0 0 0 .444-1.389l.271-2.715a2 2 0 0 0-1.99-2.199h-.581a5 5 0 0 0-.195-.248c-.191-.229-.51-.568-.88-.716-.364-.146-.846-.132-1.158-.108l-.132.012a1.26 1.26 0 0 0-.56-.642 2.6 2.6 0 0 0-.738-.288c-.31-.062-.739-.058-1.05-.046zm2.094 2.025"/>
            </svg>
            {{ __('Allowance') }}
          </h6>
        </div>
        <div class="p-6">
          <div id="allowances-wrapper" class="row">
            <div class="allowance-group grid grid-cols-1 md:grid-cols-2 gap-6 mb-2">
              <div class="space-y-2">
                <div class="form-group mb-0">
                  {{ Form::label('allowances[0][type]', __('Allowance Type'), ['class' => 'form-label']) }}
                  {!! Form::select('allowances[0][type]', $allowanceTypes ?? [], null, [
                    'class' => 'form-control',
                    'placeholder' => __('Select type')
                  ]) !!}
                </div>
              </div>
              <div class="space-y-2">
                <div class="form-group mb-0">
                  {{ Form::label('allowances[0][amount]', __('Allowance Amount'), ['class' => 'form-label']) }}
                  <div class="d-flex align-items-end gap-2">
                    <input type="number" name="allowances[0][amount]" class="form-control" min="0" step="0.01">
                  </div>
                </div>
              </div>
            </div>
          </div>

          <button type="button" onclick="addAllowanceBtn()" id="addAllowance" class="btn btn-outline-success mt-2 col-12">
            {{ __('Add Another Allowance') }}
          </button>
        </div>
      </div>
    </div>

    {{-- Deduction --}}
    <div class="col-md-4">
      <div class="bg-white rounded-[8px] border border-[#E5E7EB] shadow-sm overflow-hidden">
        <div class="heading-cstm-form">
          <h6 class="mb-0 flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-clipboard-minus" viewBox="0 0 16 16">
              <path fill-rule="evenodd" d="M5.5 9.5A.5.5 0 0 1 6 9h4a.5.5 0 0 1 0 1H6a.5.5 0 0 1-.5-.5"/>
              <path d="M4 1.5H3a2 2 0 0 0-2 2V14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V3.5a2 2 0 0 0-2-2h-1v1h1a1 1 0 0 1 1 1V14a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V3.5a1 1 0 0 1 1-1h1z"/>
              <path d="M9.5 1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-3a.5.5 0 0 1-.5-.5v-1a.5.5 0 0 1 .5-.5zm-3-1A1.5 1.5 0 0 0 5 1.5v1A1.5 1.5 0 0 0 6.5 4h3A1.5 1.5 0 0 0 11 2.5v-1A1.5 1.5 0 0 0 9.5 0z"/>
            </svg>
            {{ __('Deduction') }}
          </h6>
        </div>
        <div class="p-6">
          <div id="deductions-wrapper" class="row">
            <div class="deduction-group grid grid-cols-1 md:grid-cols-2 gap-6 mb-2">
              <div class="space-y-2">
                <div class="form-group mb-0">
                  {{ Form::label('deductions[0][type]', __('Deduction Type'), ['class' => 'form-label']) }}
                  {!! Form::select('deductions[0][type]', $deductionTypes ?? [], null, [
                    'class' => 'form-control',
                    'placeholder' => __('Select type')
                  ]) !!}
                </div>
              </div>
              <div class="space-y-2">
                <div class="form-group mb-0">
                  {{ Form::label('deductions[0][amount]', __('Deduction Amount'), ['class' => 'form-label']) }}
                  <div class="d-flex align-items-end gap-2">
                    <input type="number" name="deductions[0][amount]" class="form-control" min="0" step="0.01">
                  </div>
                </div>
              </div>
            </div>
          </div>

          <button type="button" onclick="addDeductionBtn()" id="addDeduction" class="btn btn-outline-danger mt-2 col-12">
            {{ __('Add Another Deduction') }}
          </button>
        </div>
      </div>
    </div>

    {{-- Bonus --}}
    <div class="col-md-4">
      <div class="bg-white rounded-[8px] border border-[#E5E7EB] shadow-sm overflow-hidden">
        <div class="heading-cstm-form">
          <h6 class="mb-0 flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-folder-plus" viewBox="0 0 16 16">
              <path d="m.5 3 .04.87a2 2 0 0 0-.342 1.311l.637 7A2 2 0 0 0 2.826 14H9v-1H2.826a1 1 0 0 1-.995-.91l-.637-7A1 1 0 0 1 2.19 4h11.62a1 1 0 0 1 .996 1.09L14.54 8h1.005l.256-2.819A2 2 0 0 0 13.81 3H9.828a2 2 0 0 1-1.414-.586l-.828-.828A2 2 0 0 0 6.172 1H2.5a2 2 0 0 0-2 2m5.672-1a1 1 0 0 1 .707.293L7.586 3H2.19q-.362.002-.683.12L1.5 2.98a1 1 0 0 1 1-.98z"/>
              <path d="M13.5 9a.5.5 0 0 1 .5.5V11h1.5a.5.5 0 1 1 0 1H14v1.5a.5.5 0 1 1-1 0V12h-1.5a.5.5 0 0 1 0-1H13V9.5a.5.5 0 0 1 .5-.5"/>
            </svg>
            {{ __('Bonus') }}
          </h6>
        </div>
        <div class="p-6">
          <div id="bonuses-wrapper" class="row">
            <div class="bonus-group grid grid-cols-1 md:grid-cols-2 gap-6 mb-2">
              <div class="space-y-2">
                <div class="form-group mb-0">
                  {{ Form::label('bonuses[0][type]', __('Bonus Type'), ['class' => 'form-label']) }}
                  {!! Form::select('bonuses[0][type]', $bonusTypes ?? [], null, [
                    'class' => 'form-control',
                    'placeholder' => __('Select type')
                  ]) !!}
                </div>
              </div>
              <div class="space-y-2">
                <div class="form-group mb-0">
                  {{ Form::label('bonuses[0][amount]', __('Bonus Amount'), ['class' => 'form-label']) }}
                  <div class="d-flex align-items-end gap-2">
                    <input type="number" name="bonuses[0][amount]" class="form-control" min="0" step="0.01">
                  </div>
                </div>
              </div>
            </div>
          </div>

          <button type="button" onclick="addBonusBtn()" id="addBonus" class="btn btn-outline-primary mt-2 col-12">
            {{ __('Add Another Bonus') }}
          </button>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal-footer border-t border-[#E5E5E5] bg-[#FAFAFA] px-6 py-4 flex justify-end gap-3">
  <input type="button" value="{{ __('Cancel') }}" class="btn py-[6px] px-[10px] text-[#007C38] border-[#007C38] hover:bg-[#007C38] hover:text-white" data-bs-dismiss="modal">
  <input type="submit" value="{{ __('Create') }}" class="btn py-[6px] px-[10px] bg-[#007C38] text-white hover:bg-[#005f2a]">
</div>
{{ Form::close() }}

<script>
  // Maps from controller
  const ALLOWANCE_TYPES = @json($allowanceTypes ?? (object) []);
  const DEDUCTION_TYPES = @json($deductionTypes ?? (object) []);
  const BONUS_TYPES     = @json($bonusTypes ?? (object) []);

  const esc = s => String(s).replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));

  function buildOptions(map) {
    let html = `<option value="">${esc(`{{ __('Select type') }}`)}</option>`;
    Object.entries(map).forEach(([value, label]) => {
      html += `<option value="${esc(value)}">${esc(label)}</option>`;
    });
    return html;
  }

  function addRow(wrapperId, groupClass, namePrefix, optionsMap, typeLabel, amountLabel) {
    const wrapper = document.getElementById(wrapperId);
    const idx = wrapper.querySelectorAll(`.${groupClass}`).length;

    const row = `
      <div class="${groupClass} grid grid-cols-1 md:grid-cols-2 gap-6 mb-2">
        <div class="space-y-2">
          <div class="form-group mb-0">
            <label class="form-label">${esc(typeLabel)}</label>
            <select name="${namePrefix}[${idx}][type]" class="form-control">
              ${buildOptions(optionsMap)}
            </select>
          </div>
        </div>
        <div class="space-y-2">
          <div class="form-group mb-0">
            <label class="form-label">${esc(amountLabel)}</label>
            <div class="d-flex align-items-end gap-2">
              <input type="number" name="${namePrefix}[${idx}][amount]" class="form-control" min="0" step="0.01">
              <button type="button" class="btn btn-light btn-sm p-1" title="{{ __('Remove') }}"
                onclick="removeGroup(this,'${wrapperId}','${groupClass}','${namePrefix}')">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <polyline points="3 6 5 6 21 6"></polyline>
                  <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"></path>
                  <path d="M10 11v6M14 11v6"></path>
                  <path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"></path>
                </svg>
              </button>
            </div>
          </div>
        </div>
      </div>`;
    wrapper.insertAdjacentHTML('beforeend', row);
  }

  function removeGroup(btn, wrapperId, groupClass, namePrefix) {
    const group = btn.closest(`.${groupClass}`);
    if (group) group.remove();
    renumber(wrapperId, groupClass, namePrefix);
  }

  function renumber(wrapperId, groupClass, namePrefix) {
    const wrapper = document.getElementById(wrapperId);
    wrapper.querySelectorAll(`.${groupClass}`).forEach((g, i) => {
      g.querySelectorAll('select, input').forEach(el => {
        if (el.name) el.name = el.name.replace(new RegExp(`^${namePrefix}\\[\\d+\\]`), `${namePrefix}[${i}]`);
      });
    });
  }

  function addAllowanceBtn(){ addRow('allowances-wrapper','allowance-group','allowances', ALLOWANCE_TYPES, `{{ __('Allowance Type') }}`, `{{ __('Allowance Amount') }}`); }
  function addDeductionBtn(){ addRow('deductions-wrapper','deduction-group','deductions', DEDUCTION_TYPES, `{{ __('Deduction Type') }}`, `{{ __('Deduction Amount') }}`); }
  function addBonusBtn(){ addRow('bonuses-wrapper','bonus-group','bonuses', BONUS_TYPES, `{{ __('Bonus Type') }}`, `{{ __('Bonus Amount') }}`); }
</script>
