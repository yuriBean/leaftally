@if(isset($payroll) && $payroll)
{{Form::model($payroll,array('route' => array('payroll.update', $payroll->id), 'method' => 'PUT', 'class'=>'needs-validation','novalidate')) }}
@else
<div class="modal-body">
    <div class="alert alert-warning">
        <strong>{{ __('No Pending Payrolls') }}</strong><br>
        {{ __('There are no pending payroll records to generate. Please create employee payroll records first by going to') }}
        <a href="{{ route('payroll.index') }}" class="alert-link">{{ __('Payroll Management') }}</a>
        {{ __('and clicking "Create New".') }}
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
    <a href="{{ route('payroll.index') }}" class="btn btn-primary">{{ __('Go to Payroll Management') }}</a>
</div>
@endif

@if(isset($payroll) && $payroll)

    <div class="modal-body">
    <h6 class="sub-title bg-[#F6F6F6] px-4 py-2 rounded-md text-[14px] font-semibold text-black">
        {{ __('Basic Info') }}
    </h6>

    <div class="row">
        <div class="col-lg-6">
            <div class="form-group">
                {{ Form::label('employee_id', __('Select Employee'), ['class' => 'form-label']) }}<x-required />
                {!! Form::select('employee_id', $employees, $payroll->employee_id, [
                    'id' => 'employee_id_select',
                    'class' => 'form-control select',
                    'required' => true,
                ]) !!}
            </div>
        </div>
        <div class="col-lg-6">
            <div class="form-group">
                {{ Form::label('payroll_month', __('Payroll Month'), ['class' => 'form-label']) }}<x-required />
                {!! Form::select('payroll_month', isset($monthes[$payroll->id]) ? $monthes[$payroll->id] : [], null, [
                    'class' => 'form-control select',
                    'required' => 'required',
                    'id' => 'payroll_month_select'
                ]) !!}
            </div>
        </div>
    </div>

    <h6 class="sub-title bg-[#F6F6F6] px-4 py-2 rounded-md mt-4 text-[14px] font-semibold text-black">
        {{ __('Salary Details') }}
    </h6>

 <div class="row mt-6">
        <div class="col-lg-12 col-md-12 col-sm-6">
            <div class="form-group">
                {{ Form::label('basic_salary', __('Basic Salary'), ['class' => 'form-label']) }}
                <div class="form-icon-user">
                    {{ Form::number('basic_salary', null, [
                        'class' => 'form-control',
                        Request::is('payroll.index') ? '' : 'disabled',
                    ]) }}
                </div>
            </div>
        </div>
    </div>
    <div id="allowances-wrapper" class="row mt-6">
        @foreach ($payroll->allowances as $index => $allowance)
            <div class="allowance-group col-12 row">
                <div class="col-md-5">
                    {{ Form::label("allowances[{$index}][type]", __("Allowance {$index}"), ['class' => 'form-label']) }}
                    {{ Form::text("allowances[{$index}][type]", $allowance->type, ['class' => 'form-control']) }}
                </div>
                <div class="col-md-6">
                    {{ Form::label("allowances[{$index}][amount]", __('Allowance Amount'), ['class' => 'form-label']) }}
                    {{ Form::number("allowances[{$index}][amount]", $allowance->amount, ['class' => 'form-control']) }}
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn btn-danger mt-4" onclick="removeAllowanceBtn(this)">
                        <i class="fa fa-trash"></i>
                    </button>
                </div>
            </div>
        @endforeach
    </div>
    <button type="button" onclick="addAllowanceBtn()" id="addAllowance"
        class="btn btn-outline-success mt-2 col-12">{{ __('Add Another Allowance') }}</button>
    <div id="deductions-wrapper" class="row mt-6">
        @foreach ($payroll->deductions as $index => $deduction)
            <div class="deduction-group col-12 row">
                <div class="col-md-5">
                    {{ Form::label("deductions[{$index}][type]", __("Deduction {$index}"), ['class' => 'form-label']) }}
                    {{ Form::text("deductions[{$index}][type]", $deduction->type, ['class' => 'form-control']) }}
                </div>
                <div class="col-md-6">
                    {{ Form::label("deductions[{$index}][amount]", __('Deduction Amount'), ['class' => 'form-label']) }}
                    {{ Form::number("deductions[{$index}][amount]", $deduction->amount, ['class' => 'form-control']) }}
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn btn-danger mt-4" onclick="removeDeductionBtn(this)">
                        <i class="fa fa-trash"></i>
                    </button>
                </div>
            </div>
        @endforeach
    </div>
    <button type="button" onclick="addDeductionBtn()" id="addDeduction"
        class="btn btn-outline-danger mt-2 col-12">{{ __('Add Another Deduction') }}</button>
    <div id="bonuses-wrapper" class="row mt-6">
        @foreach ($payroll->bonuses as $index => $bonus)
            <div class="bonus-group col-12 row">
                <div class="col-md-5">
                    {{ Form::label("bonuses[{$index}][type]", __('Bonus Type'), ['class' => 'form-label']) }}
                    {{ Form::text("bonuses[{$index}][type]", $bonus->type, ['class' => 'form-control']) }}
                </div>
                <div class="col-md-6">
                    {{ Form::label("bonuses[{$index}][amount]", __('Bonus Amount'), ['class' => 'form-label']) }}
                    {{ Form::number("bonuses[{$index}][amount]", $bonus->amount, ['class' => 'form-control']) }}
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn btn-danger mt-4" onclick="removeBonusBtn(this)">
                        <i class="fa fa-trash"></i>
                    </button>
                </div>
            </div>
        @endforeach
    </div>
    <button type="button" onclick="addBonusBtn()" id="addBonus"
        class="btn btn-outline-primary mt-2 col-12">{{ __('Add Another Bonus') }}</button>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
    <button type="submit" class="btn btn-success">{{ __('Generate Payroll') }}</button>
</div>
{{ Form::close() }}

<script>
document.addEventListener('DOMContentLoaded', function() {
    const employeeSelect = document.getElementById('employee_id_select');
    const monthSelect = document.getElementById('payroll_month_select');
    
    // Available months data from the controller
    const monthesData = @json($monthes);
    
    // Handle employee selection change
    employeeSelect.addEventListener('change', function() {
        const selectedPayrollId = this.value;
        
        // Clear current month options
        monthSelect.innerHTML = '<option value="">{{ __("Select Month") }}</option>';
        
        // Add available months for selected employee
        if (monthesData[selectedPayrollId]) {
            Object.entries(monthesData[selectedPayrollId]).forEach(([value, label]) => {
                const option = document.createElement('option');
                option.value = value;
                option.textContent = label;
                monthSelect.appendChild(option);
            });
        }
    });
    
    // Trigger initial load if there's a selected employee
    if (employeeSelect.value) {
        employeeSelect.dispatchEvent(new Event('change'));
    }
});
</script>
@endif

