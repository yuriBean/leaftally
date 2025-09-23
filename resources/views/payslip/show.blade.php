<div class="card bg-none card-box payroll-view">
  <style>
    .payroll-view{--acc:
    .pv-wrap{padding:16px}
    .pv-summary{display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:6px}
    .pv-stat{background:
    .pv-stat .k{font-size:.72rem;text-transform:uppercase;letter-spacing:.06em;color:
    .pv-stat .v{font-weight:800;color:
    .pv-net{background:linear-gradient(135deg,rgba(0,124,56,.08),
    .pv-net .k{color:var(--acc)}
    .pv-tabs{border-bottom:1px solid var(--line);margin:10px 0 12px}
    .pv-tabs .nav{gap:.45rem;flex-wrap:wrap}
    .pv-tabs .nav-link{border:1px solid var(--line)!important;border-radius:999px!important;color:
    .pv-tabs .nav-link.active{background:rgba(0,124,56,.09)!important;border-color:rgba(0,124,56,.25)!important;color:var(--acc)!important}
    .pv-section{background:
    .pv-table{width:100%;border-collapse:separate;border-spacing:0}
    .pv-table thead th{font-size:12px;text-transform:uppercase;letter-spacing:.06em;background:
    .pv-table tbody td{border:1px solid var(--line);padding:.8rem .7rem;vertical-align:middle}
    .chip{display:inline-flex;align-items:center;gap:.45rem;background:
    .chip i{width:.5rem;height:.5rem;border-radius:999px;background:var(--acc)}
    @media (max-width:992px){.pv-summary{grid-template-columns:1fr 1fr}}
    @media (max-width:576px){.pv-summary{grid-template-columns:1fr}}
  </style>

  <div class="pv-wrap">
    {{-- Summary --}}
    <div class="pv-summary">
      <div class="pv-stat">
        <div class="k">{{ __('Employee Detail') }}</div>
        <div class="v">{{ !empty($payslip->employees) ? \Auth::user()->employeeIdFormat($payslip->employees->employee_id) : '' }}</div>
      </div>
      <div class="pv-stat">
        <div class="k">{{ __('Basic Salary') }}</div>
        <div class="v">{{ \Auth::user()->priceFormat($payslip->basic_salary) }}</div>
      </div>
      <div class="pv-stat">
        <div class="k">{{ __('Payroll Month') }}</div>
        <div class="v">{{ \Auth::user()->dateFormat($payslip->salary_month) }}</div>
      </div>
      <div class="pv-stat pv-net">
        <div class="k">{{ __('Net Salary') }}</div>
        <div class="v">{{ \Auth::user()->priceFormat($payslip->net_payble) }}</div>
      </div>
    </div>

    {{-- Pills --}}
    <div class="pv-tabs">
      <ul class="nav nav-tabs my-2" role="tablist">
        <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#pv-allowance" role="tab">{{ __('Allowance') }}</a></li>
        <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#pv-commission" role="tab">{{ __('Commission') }}</a></li>
        <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#pv-loan" role="tab">{{ __('Loan') }}</a></li>
        <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#pv-deduction" role="tab">{{ __('Saturation Deduction') }}</a></li>
        <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#pv-payment" role="tab">{{ __('Other Payment') }}</a></li>
        <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#pv-overtime" role="tab">{{ __('Overtime') }}</a></li>
      </ul>
    </div>

    <div class="tab-content">
      {{-- Allowance --}}
      <div id="pv-allowance" class="tab-pane fade show active">
        <div class="pv-section">
          @php $allowances = json_decode($payslip->allowance); @endphp
          <div class="table-responsive">
            <table class="pv-table">
              <thead>
                <tr>
                  <th>{{ __('Title') }}</th>
                  <th>{{ __('Type') }}</th>
                  <th>{{ __('Amount') }}</th>
                </tr>
              </thead>
              <tbody>
                @foreach($allowances as $allowance)
                  @php
                    $emp = \App\Models\Employee::find($allowance->employee_id);
                    $percentValue = $emp ? ($allowance->amount * $emp->salary / 100) : 0;
                  @endphp
                  <tr>
                    <td>
                      <span class="chip"><i></i>{{ $allowance->title }}</span>
                    </td>
                    <td>{{ ucfirst($allowance->type) }}</td>
                    <td>
                      @if($allowance->type !== 'percentage')
                        {{ \Auth::user()->priceFormat($allowance->amount) }}
                      @else
                        {{ $allowance->amount }}% ({{ \Auth::user()->priceFormat($percentValue) }})
                      @endif
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>

      {{-- Commission --}}
      <div id="pv-commission" class="tab-pane fade">
        <div class="pv-section">
          @php $commissions = json_decode($payslip->commission); @endphp
          <div class="table-responsive">
            <table class="pv-table">
              <thead>
                <tr>
                  <th>{{ __('Title') }}</th>
                  <th>{{ __('Type') }}</th>
                  <th>{{ __('Amount') }}</th>
                </tr>
              </thead>
              <tbody>
                @foreach($commissions as $commission)
                  @php
                    $emp = \App\Models\Employee::find($commission->employee_id);
                    $percentValue = $emp ? ($commission->amount * $emp->salary / 100) : 0;
                  @endphp
                  <tr>
                    <td><span class="chip"><i></i>{{ $commission->title }}</span></td>
                    <td>{{ ucfirst($commission->type) }}</td>
                    <td>
                      @if($commission->type !== 'percentage')
                        {{ \Auth::user()->priceFormat($commission->amount) }}
                      @else
                        {{ $commission->amount }}% ({{ \Auth::user()->priceFormat($percentValue) }})
                      @endif
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>

      {{-- Loan --}}
      <div id="pv-loan" class="tab-pane fade">
        <div class="pv-section">
          @php $loans = json_decode($payslip->loan); @endphp
          <div class="table-responsive">
            <table class="pv-table">
              <thead>
                <tr>
                  <th>{{ __('Title') }}</th>
                  <th>{{ __('Type') }}</th>
                  <th>{{ __('Amount') }}</th>
                </tr>
              </thead>
              <tbody>
                @foreach($loans as $loan)
                  @php
                    $emp = \App\Models\Employee::find($loan->employee_id);
                    $percentValue = $emp ? ($loan->amount * $emp->salary / 100) : 0;
                  @endphp
                  <tr>
                    <td><span class="chip"><i></i>{{ $loan->title }}</span></td>
                    <td>{{ ucfirst($loan->type) }}</td>
                    <td>
                      @if($loan->type !== 'percentage')
                        {{ \Auth::user()->priceFormat($loan->amount) }}
                      @else
                        {{ $loan->amount }}% ({{ \Auth::user()->priceFormat($percentValue) }})
                      @endif
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>

      {{-- Saturation Deduction --}}
      <div id="pv-deduction" class="tab-pane fade">
        <div class="pv-section">
          @php $saturation_deductions = json_decode($payslip->saturation_deduction); @endphp
          <div class="table-responsive">
            <table class="pv-table">
              <thead>
                <tr>
                  <th>{{ __('Title') }}</th>
                  <th>{{ __('Type') }}</th>
                  <th>{{ __('Amount') }}</th>
                </tr>
              </thead>
              <tbody>
                @foreach($saturation_deductions as $deduction)
                  @php
                    $emp = \App\Models\Employee::find($deduction->employee_id);
                    $percentValue = $emp ? ($deduction->amount * $emp->salary / 100) : 0;
                  @endphp
                  <tr>
                    <td><span class="chip"><i></i>{{ $deduction->title }}</span></td>
                    <td>{{ ucfirst($deduction->type) }}</td>
                    <td>
                      @if($deduction->type !== 'percentage')
                        {{ \Auth::user()->priceFormat($deduction->amount) }}
                      @else
                        {{ $deduction->amount }}% ({{ \Auth::user()->priceFormat($percentValue) }})
                      @endif
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>

      {{-- Other Payment --}}
      <div id="pv-payment" class="tab-pane fade">
        <div class="pv-section">
          @php $other_payments = json_decode($payslip->other_payment); @endphp
          <div class="table-responsive">
            <table class="pv-table">
              <thead>
                <tr>
                  <th>{{ __('Title') }}</th>
                  <th>{{ __('Type') }}</th>
                  <th>{{ __('Amount') }}</th>
                </tr>
              </thead>
              <tbody>
                @foreach($other_payments as $payment)
                  @php
                    $emp = \App\Models\Employee::find($payment->employee_id);
                    $percentValue = $emp ? ($payment->amount * $emp->salary / 100) : 0;
                  @endphp
                  <tr>
                    <td><span class="chip"><i></i>{{ $payment->title }}</span></td>
                    <td>{{ ucfirst($payment->type) }}</td>
                    <td>
                      @if($payment->type !== 'percentage')
                        {{ \Auth::user()->priceFormat($payment->amount) }}
                      @else
                        {{ $payment->amount }}% ({{ \Auth::user()->priceFormat($percentValue) }})
                      @endif
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>

      {{-- Overtime --}}
      <div id="pv-overtime" class="tab-pane fade">
        <div class="pv-section">
          @php $overtimes = json_decode($payslip->overtime); @endphp
          <div class="table-responsive">
            <table class="pv-table">
              <thead>
                <tr>
                  <th>{{ __('Title') }}</th>
                  <th>{{ __('Amount') }}</th>
                </tr>
              </thead>
              <tbody>
                @foreach($overtimes as $overtime)
                  <tr>
                    <td><span class="chip"><i></i>{{ $overtime->title }}</span></td>
                    <td>{{ \Auth::user()->priceFormat($overtime->rate) }}</td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>
