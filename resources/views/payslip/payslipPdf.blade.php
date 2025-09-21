@php
 use App\Models\Utility;
    $logo         = Utility::get_file('uploads/logo');
    $company_logo = Utility::getValByName('company_logo');
    $company_name = Utility::getValByName('company_name') ?? Utility::getValByName('company_name'); // fallback in case of typo
    $company_name = Utility::getValByName('company_name');
    $company_addr = Utility::getValByName('company_address');
    $company_city = Utility::getValByName('company_city');
    $company_state= Utility::getValByName('company_state');
    $company_zip  = Utility::getValByName('company_zipcode');
    $salaryMonth  = $payslip->salary_month;
    $salaryDate   = \Auth::user()->dateFormat($payslip->created_at);
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Payslip — {{ $employee->name }} — {{ $salaryMonth }}</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  {{-- Bootstrap (standalone styling) --}}
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { background:#f8fafc; }
    .invoice { background:#fff; border:1px solid #e5e7eb; border-radius:10px; box-shadow:0 4px 16px rgba(0,0,0,.04); }
    .invoice-title h4 { margin:0; }
    .invoice-number img { max-height:70px; }
    .table>:not(caption)>*>* { vertical-align: middle; }
    .text-sm { font-size:.95rem; }
    .text-xs { font-size:.85rem; }
    .btn-download { position: sticky; top: 16px; z-index: 10; }
    @media print {
      .no-print { display:none !important; }
      body { background:#fff; }
      .invoice { border:none; box-shadow:none; }
    }
  </style>
</head>
<body>

<div class="container my-4">
  <div class="d-flex justify-content-end no-print mb-2">
    <button class="btn btn-warning btn-download" onclick="saveAsPDF()" title="Download PDF">
      <!-- small inline download icon -->
      <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M5 20h14a1 1 0 1 1 0 2H5a1 1 0 1 1 0-2zm7-18a1 1 0 0 1 1 1v9.586l2.293-2.293a1 1 0 0 1 1.414 1.414l-4 4a1 1 0 0 1-1.414 0l-4-4A1 1 0 1 1 8.707 10.293L11 12.586V3a1 1 0 0 1 1-1z"/></svg>
    </button>
  </div>

  <div class="invoice p-4" id="printableArea">
    <div class="row">
      <div class="col-8">
        <div class="invoice-title">
          <h4 class="fw-semibold">Payslip</h4>
        </div>
      </div>
      <div class="col-4 text-end">
        <div class="invoice-number">
          <img src="{{ $logo.'/'.(!empty($company_logo) ? $company_logo : 'logo-dark.png') }}" alt="Company Logo">
        </div>
      </div>
    </div>

    <hr>

    <div class="row text-sm">
      <div class="col-md-6">
        <address class="mb-0">
          <strong>Name :</strong> {{ $employee->name }}<br>
          <strong>Position :</strong> {{ !empty($employee->designation) ? $employee->designation->name : 'Employee' }}<br>
          <strong>Salary Date :</strong> {{ $salaryDate }}<br>
        </address>
      </div>
      <div class="col-md-6 text-md-end">
        <address class="mb-0">
          <strong>{{ $company_name }}</strong><br>
          {{ $company_addr }}{{ $company_addr && $company_city ? ', ' : '' }}{{ $company_city }}<br>
          {{ $company_state }}{{ $company_state && $company_zip ? '-' : '' }}{{ $company_zip }}<br>
          <strong>Salary Slip :</strong> {{ $salaryMonth }}<br>
        </address>
      </div>
    </div>

    {{-- ================== Earnings ================== --}}
    <div class="mt-4">
      <div class="table-responsive">
        <table class="table table-striped table-hover table-md align-middle">
          <tbody>
            <tr class="fw-semibold">
              <th style="width:20%">Earning</th>
              <th style="width:45%">Title</th>
              <th style="width:15%">Type</th>
              <th class="text-end" style="width:20%">Amount</th>
            </tr>
            <tr>
              <td>Basic Salary</td>
              <td>-</td>
              <td>-</td>
              <td class="text-end">{{ \Auth::user()->priceFormat($payslip->basic_salary) }}</td>
            </tr>

            {{-- Allowances --}}
            @foreach(($payslipDetail['earning']['allowance'] ?? []) as $row)
              @php
                $title  = is_array($row) ? ($row['title'] ?? '-') : ($row->title ?? '-');
                $type   = strtolower(is_array($row) ? ($row['type'] ?? 'fixed') : ($row->type ?? 'fixed'));
                $amount = (float)(is_array($row) ? ($row['amount'] ?? 0) : ($row->amount ?? 0));
              @endphp
              <tr>
                <td>Allowance</td>
                <td>{{ $title }}</td>
                <td class="text-capitalize">{{ $type }}</td>
                <td class="text-end">
                  @if($type === 'percentage')
                    {{ rtrim(rtrim(number_format($amount,2), '0'),'.') }}% ({{ \Auth::user()->priceFormat(($amount * $payslip->basic_salary) / 100) }})
                  @else
                    {{ \Auth::user()->priceFormat($amount) }}
                  @endif
                </td>
              </tr>
            @endforeach

            {{-- Commissions --}}
            @foreach(($payslipDetail['earning']['commission'] ?? []) as $row)
              @php
                $title  = is_array($row) ? ($row['title'] ?? '-') : ($row->title ?? '-');
                $type   = strtolower(is_array($row) ? ($row['type'] ?? 'fixed') : ($row->type ?? 'fixed'));
                $amount = (float)(is_array($row) ? ($row['amount'] ?? 0) : ($row->amount ?? 0));
              @endphp
              <tr>
                <td>Commission</td>
                <td>{{ $title }}</td>
                <td class="text-capitalize">{{ $type }}</td>
                <td class="text-end">
                  @if($type === 'percentage')
                    {{ rtrim(rtrim(number_format($amount,2), '0'),'.') }}% ({{ \Auth::user()->priceFormat(($amount * $payslip->basic_salary) / 100) }})
                  @else
                    {{ \Auth::user()->priceFormat($amount) }}
                  @endif
                </td>
              </tr>
            @endforeach

            {{-- Other Payments --}}
            @foreach(($payslipDetail['earning']['otherPayment'] ?? []) as $row)
              @php
                $title  = is_array($row) ? ($row['title'] ?? '-') : ($row->title ?? '-');
                $type   = strtolower(is_array($row) ? ($row['type'] ?? 'fixed') : ($row->type ?? 'fixed'));
                $amount = (float)(is_array($row) ? ($row['amount'] ?? 0) : ($row->amount ?? 0));
              @endphp
              <tr>
                <td>Other Payment</td>
                <td>{{ $title }}</td>
                <td class="text-capitalize">{{ $type }}</td>
                <td class="text-end">
                  @if($type === 'percentage')
                    {{ rtrim(rtrim(number_format($amount,2), '0'),'.') }}% ({{ \Auth::user()->priceFormat(($amount * $payslip->basic_salary) / 100) }})
                  @else
                    {{ \Auth::user()->priceFormat($amount) }}
                  @endif
                </td>
              </tr>
            @endforeach

            {{-- Overtime --}}
            @foreach(($payslipDetail['earning']['overTime'] ?? []) as $row)
              @php
                // row may be array or object with keys number_of_days, hours, rate, title (optional)
                $title = is_array($row) ? ($row['title'] ?? 'Overtime') : ($row->title ?? 'Overtime');
                $d = (float)(is_array($row) ? ($row['number_of_days'] ?? 0) : ($row->number_of_days ?? 0));
                $h = (float)(is_array($row) ? ($row['hours'] ?? 0) : ($row->hours ?? 0));
                $r = (float)(is_array($row) ? ($row['rate'] ?? 0) : ($row->rate ?? 0));
                $otAmount = $d * $h * $r;
              @endphp
              <tr>
                <td>OverTime</td>
                <td>{{ $title }}</td>
                <td>-</td>
                <td class="text-end">{{ \Auth::user()->priceFormat($otAmount) }}</td>
              </tr>
            @endforeach

          </tbody>
        </table>
      </div>
    </div>

    {{-- ================== Deductions ================== --}}
    <div class="mt-4">
      <div class="table-responsive">
        <table class="table table-striped table-hover table-md align-middle">
          <tbody>
            <tr class="fw-semibold">
              <th style="width:20%">Deduction</th>
              <th style="width:45%">Title</th>
              <th style="width:15%">Type</th>
              <th class="text-end" style="width:20%">Amount</th>
            </tr>

            {{-- Loans --}}
            @foreach(($payslipDetail['deduction']['loan'] ?? []) as $row)
              @php
                $title  = is_array($row) ? ($row['title'] ?? '-') : ($row->title ?? '-');
                $type   = strtolower(is_array($row) ? ($row['type'] ?? 'fixed') : ($row->type ?? 'fixed'));
                $amount = (float)(is_array($row) ? ($row['amount'] ?? 0) : ($row->amount ?? 0));
              @endphp
              <tr>
                <td>Loan</td>
                <td>{{ $title }}</td>
                <td class="text-capitalize">{{ $type }}</td>
                <td class="text-end">
                  @if($type === 'percentage')
                    {{ rtrim(rtrim(number_format($amount,2), '0'),'.') }}% ({{ \Auth::user()->priceFormat(($amount * $payslip->basic_salary) / 100) }})
                  @else
                    {{ \Auth::user()->priceFormat($amount) }}
                  @endif
                </td>
              </tr>
            @endforeach

            {{-- Deductions --}}
            @foreach(($payslipDetail['deduction']['deduction'] ?? []) as $row)
              @php
                $title  = is_array($row) ? ($row['title'] ?? '-') : ($row->title ?? '-');
                $type   = strtolower(is_array($row) ? ($row['type'] ?? 'fixed') : ($row->type ?? 'fixed'));
                $amount = (float)(is_array($row) ? ($row['amount'] ?? 0) : ($row->amount ?? 0));
              @endphp
              <tr>
                <td>Saturation Deduction</td>
                <td>{{ $title }}</td>
                <td class="text-capitalize">{{ $type }}</td>
                <td class="text-end">
                  @if($type === 'percentage')
                    {{ rtrim(rtrim(number_format($amount,2), '0'),'.') }}% ({{ \Auth::user()->priceFormat(($amount * $payslip->basic_salary) / 100) }})
                  @else
                    {{ \Auth::user()->priceFormat($amount) }}
                  @endif
                </td>
              </tr>
            @endforeach

          </tbody>
        </table>
      </div>
    </div>

    {{-- ================== Totals ================== --}}
    <div class="row mt-4">
      <div class="col-lg-8"></div>
      <div class="col-lg-4 text-end text-sm">
        <div class="pb-2">
          <div class="fw-semibold">Total Earning</div>
          <div>{{ \Auth::user()->priceFormat($payslipDetail['totalEarning'] ?? 0) }}</div>
        </div>
        <div class="">
          <div class="fw-semibold">Total Deduction</div>
          <div>{{ \Auth::user()->priceFormat($payslipDetail['totalDeduction'] ?? 0) }}</div>
        </div>
        <hr class="my-2">
        <div>
          <div class="fw-semibold">Net Salary</div>
          <div class="fs-5">{{ \Auth::user()->priceFormat($payslip->net_payble) }}</div>
        </div>
      </div>
    </div>

    {{-- Footer --}}
    <hr>


  </div> {{-- /#printableArea --}}
</div>

<script src="{{ asset('js/html2pdf.bundle.min.js') }}"></script>
<script>
  function saveAsPDF() {
    var element = document.getElementById('printableArea');
    var opt = {
      margin: 0.3,
      filename: '{{ preg_replace("/[^A-Za-z0-9_\-\.]/", "_", $employee->name) }}_{{ $salaryMonth }}.pdf',
      image: { type: 'jpeg', quality: 1 },
      html2canvas: { scale: 4, dpi: 72, letterRendering: true },
      jsPDF: { unit: 'in', format: 'A4' }
    };
    html2pdf().set(opt).from(element).save();
  }
</script>
</body>
</html>
