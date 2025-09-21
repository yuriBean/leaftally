<div class="col-form-label pay-modal">
  <style>
    .pay-modal{--acc:#007C38;--acc-600:#01612c;--line:#E5E7EB;--ink:#0F172A;--muted:#6B7280}
    .pay-modal .summary{display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin-bottom:10px}
    .pay-modal .stat{background:#fff;border:1px solid var(--line);border-radius:12px;padding:.8rem .9rem}
    .pay-modal .stat .k{font-size:.75rem;text-transform:uppercase;letter-spacing:.06em;color:#6B7280;font-weight:800;margin-bottom:.15rem}
    .pay-modal .stat .v{font-weight:800;color:#111827}
    .pay-modal .nav-wrap{border-bottom:1px solid var(--line);margin:.75rem 0 1rem}
    .pay-modal .nav-pills{gap:.4rem;flex-wrap:wrap}
    .pay-modal .nav-pills .nav-link{border:1px solid var(--line);border-radius:999px;color:#374151;font-weight:700;padding:.4rem .8rem}
    .pay-modal .nav-pills .nav-link.active{background:rgba(0,124,56,.08);border-color:rgba(0,124,56,.25);color:var(--acc)}
    .pay-modal .section{background:#fff;border:1px solid var(--line);border-radius:12px;padding:12px}
    .pay-modal .row.compact > [class*="col-"]{margin-bottom:12px}
    .pay-modal label.col-form-label{font-weight:700;color:#374151;margin-bottom:.35rem}
    .pay-modal .form-control{border:1px solid var(--line);border-radius:10px;padding:.55rem .7rem}
    .pay-modal .form-control:focus{border-color:var(--acc);box-shadow:0 0 0 3px rgba(0,124,56,.14)}
    .pay-modal .badge-chip{display:inline-flex;align-items:center;gap:.45rem;background:#fff;border:1px solid var(--line);border-radius:999px;padding:.25rem .6rem;font-weight:800;font-size:.75rem;color:#374151}
    .pay-modal .badge-chip i{width:.55rem;height:.55rem;border-radius:999px;background:var(--acc)}
    .pay-modal .modal-footer{border-top:1px solid var(--line);margin-top:12px;padding-top:12px}
    @media (max-width:768px){.pay-modal .summary{grid-template-columns:1fr}}
  </style>

  {{-- Header summary chips --}}
  <div class="summary">
    <div class="stat">
      <div class="k">{{ __('Employee') }}</div>
      <div class="v">
        {{ !empty($payslip->employees) ? \Auth::user()->employeeIdFormat($payslip->employees->employee_id) : '' }}
      </div>
    </div>
    <div class="stat">
      <div class="k">{{ __('Basic Salary') }}</div>
      <div class="v">{{ \Auth::user()->priceFormat($payslip->basic_salary) }}</div>
    </div>
    <div class="stat">
      <div class="k">{{ __('Payroll Month') }}</div>
      <div class="v">{{ \Auth::user()->dateFormat($payslip->salary_month) }}</div>
    </div>
  </div>

  {{ Form::open(['route'=>['payslip.updateemployee',$payslip->employee_id],'method'=>'post']) }}
  {!! Form::hidden('payslip_id', $payslip->id) !!}

  {{-- Modern pills --}}
  <div class="nav-wrap">
    <ul class="nav nav-pills" id="pills-tab" role="tablist">
      <li class="nav-item"><a class="nav-link active" data-bs-toggle="pill" href="#allowance" role="tab">{{ __('Allowance') }}</a></li>
      <li class="nav-item"><a class="nav-link" data-bs-toggle="pill" href="#commission" role="tab">{{ __('Commission') }}</a></li>
      <li class="nav-item"><a class="nav-link" data-bs-toggle="pill" href="#loan" role="tab">{{ __('Loan') }}</a></li>
      <li class="nav-item"><a class="nav-link" data-bs-toggle="pill" href="#deduction" role="tab">{{ __('Saturation Deduction') }}</a></li>
      <li class="nav-item"><a class="nav-link" data-bs-toggle="pill" href="#payment" role="tab">{{ __('Other Payment') }}</a></li>
      <li class="nav-item"><a class="nav-link" data-bs-toggle="pill" href="#overtime" role="tab">{{ __('Overtime') }}</a></li>
    </ul>
  </div>

  <div class="tab-content pt-1">
    {{-- ALLOWANCE --}}
    <div id="allowance" class="tab-pane fade show active">
      <div class="section">
        @php $allowances = json_decode($payslip->allowance); @endphp
        <div class="row compact">
          @foreach($allowances as $a)
            <div class="col-md-12">
              <span class="badge-chip mb-1"><i></i> {{ $a->title }}</span>
              {!! Form::label('allowance[]', __('Amount'), ['class'=>'col-form-label d-none']) !!}
              {!! Form::number('allowance[]', $a->amount, ['class'=>'form-control','placeholder'=>__($a->title),'step'=>'0.01','min'=>'0']) !!}
              {!! Form::hidden('allowance_id[]', $a->id) !!}
            </div>
          @endforeach
        </div>
      </div>
    </div>

    {{-- COMMISSION --}}
    <div id="commission" class="tab-pane fade">
      <div class="section">
        @php $commissions = json_decode($payslip->commission); @endphp
        <div class="row compact">
          @foreach($commissions as $c)
            <div class="col-md-12">
              <span class="badge-chip mb-1"><i></i> {{ $c->title }}</span>
              {!! Form::number('commission[]', $c->amount, ['class'=>'form-control','placeholder'=>__($c->title),'step'=>'0.01','min'=>'0']) !!}
              {!! Form::hidden('commission_id[]', $c->id) !!}
            </div>
          @endforeach
        </div>
      </div>
    </div>

    {{-- LOAN --}}
    <div id="loan" class="tab-pane fade">
      <div class="section">
        @php $loans = json_decode($payslip->loan); @endphp
        <div class="row compact">
          @foreach($loans as $l)
            <div class="col-md-12">
              <span class="badge-chip mb-1"><i></i> {{ $l->title }}</span>
              {!! Form::number('loan[]', $l->amount, ['class'=>'form-control','placeholder'=>__($l->title),'step'=>'0.01','min'=>'0']) !!}
              {!! Form::hidden('loan_id[]', $l->id) !!}
            </div>
          @endforeach
        </div>
      </div>
    </div>

    {{-- DEDUCTION --}}
    <div id="deduction" class="tab-pane fade">
      <div class="section">
        @php $saturation_deductions = json_decode($payslip->saturation_deduction); @endphp
        <div class="row compact">
          @foreach($saturation_deductions as $d)
            <div class="col-md-12">
              <span class="badge-chip mb-1"><i></i> {{ $d->title }}</span>
              {!! Form::number('saturation_deductions[]', $d->amount, ['class'=>'form-control','placeholder'=>__($d->title),'step'=>'0.01','min'=>'0']) !!}
              {!! Form::hidden('saturation_deductions_id[]', $d->id) !!}
            </div>
          @endforeach
        </div>
      </div>
    </div>

    {{-- OTHER PAYMENT --}}
    <div id="payment" class="tab-pane fade">
      <div class="section">
        @php $other_payments = json_decode($payslip->other_payment); @endphp
        <div class="row compact">
          @foreach($other_payments as $p)
            <div class="col-md-12">
              <span class="badge-chip mb-1"><i></i> {{ $p->title }}</span>
              {!! Form::number('other_payment[]', $p->amount, ['class'=>'form-control','placeholder'=>__($p->title),'step'=>'0.01','min'=>'0']) !!}
              {!! Form::hidden('other_payment_id[]', $p->id) !!}
            </div>
          @endforeach
        </div>
      </div>
    </div>

    {{-- OVERTIME --}}
    <div id="overtime" class="tab-pane fade">
      <div class="section">
        @php $overtimes = json_decode($payslip->overtime); @endphp
        <div class="row compact">
          @foreach($overtimes as $o)
            <div class="col-md-6">
              <span class="badge-chip mb-1"><i></i> {{ $o->title }} — {{ __('Rate') }}</span>
              {!! Form::number('rate[]', $o->rate, ['class'=>'form-control','placeholder'=>__($o->title.' '.__('Rate')),'step'=>'0.01','min'=>'0']) !!}
              {!! Form::hidden('rate_id[]', $o->id) !!}
            </div>
            <div class="col-md-6">
              <span class="badge-chip mb-1"><i></i> {{ $o->title }} — {{ __('Hours') }}</span>
              {!! Form::number('hours[]', $o->hours, ['class'=>'form-control','placeholder'=>__($o->title.' '.__('Hours')),'step'=>'0.01','min'=>'0']) !!}
            </div>
          @endforeach
        </div>
      </div>
    </div>
  </div>

  <div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
    <button type="submit" class="btn btn-primary">{{ __('Update') }}</button>
  </div>
  {{ Form::close() }}
</div>
