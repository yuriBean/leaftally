<script src="{{ asset('js/unsaved.js') }}"></script>

@extends('layouts.admin')

@section('page-title')
  {{ __('Manage Employee Salary') }}
@endsection

@section('content')
<style>
  .page{--acc:
  .page{background:var(--bg)}
  .container-w{max-width:1140px;margin:0 auto}
  .grid{display:grid;grid-template-columns:280px 1fr;gap:2rem}
  @media (max-width: 992px){.grid{grid-template-columns:1fr}}
  .sticky{position:sticky;top:70px}
  .aside{background:
  .aside .name{font-weight:800;color:var(--ink);font-size:1rem}
  .aside .meta{color:var(--muted);font-size:.9rem}
  .chip{display:inline-flex;align-items:center;gap:.45rem;padding:.3rem .6rem;border:1px solid var(--line);border-radius:999px;font-weight:700;font-size:.75rem;background:
  .chip i{width:.6rem;height:.6rem;border-radius:999px;background:var(--acc)}
  .actions{display:flex;gap:.5rem;flex-wrap:wrap;margin-top:.75rem}
  .btn{border-radius:10px;padding:.55rem .8rem;font-weight:700;border:1px solid var(--line);background:
  .btn:hover{background:
  .btn-primary{background:var(--acc);color:
  .btn-primary:hover{background:var(--acc-600)}

  .vnav{display:flex;flex-direction:column;gap:.35rem;margin-top:1rem}
  .vtab{display:flex;align-items:center;gap:.6rem;padding:.6rem .75rem;border-radius:10px;color:
  .vtab svg{width:18px;height:18px}
  .vtab.active{background:rgba(0,124,56,.08);border-color:rgba(0,124,56,.25);color:var(--acc)}
  .vtab:focus{outline:3px solid rgba(0,124,56,.2)}
  .content > section{display:none}
  .content > section.active{display:block}

  .section-head{display:flex;align-items:center;justify-content:space-between;gap:1rem;margin-bottom:.75rem}
  .title{font-weight:800;color:var(--ink);letter-spacing:.1px}
  .subtitle{color:var(--muted);font-size:.95rem}
  .divider{height:1px;background:var(--line);margin:0 0 1rem}

  .formgrid{display:grid;grid-template-columns:repeat(12,1fr);gap:1rem}
  .col-6{grid-column:span 6}
  .col-4{grid-column:span 4}
  .col-12{grid-column:span 12}
  @media (max-width: 768px){.col-6,.col-4{grid-column:span 12}}
  .field label{display:block;font-size:.85rem;font-weight:700;color:
  .ctl{width:100%;border:1px solid var(--line);border-radius:10px;padding:.55rem .7rem;font-size:.95rem;background:
  .ctl:focus{outline:none;box-shadow:0 0 0 3px rgba(0,124,56,.14);border-color:var(--acc)}

  .tablewrap{overflow:auto;border:1px solid var(--line);border-radius:12px;background:
  table.clean{width:100%;border-collapse:separate;border-spacing:0}
  thead th{position:sticky;top:0;background:
  tbody td{padding:.85rem .8rem;border-bottom:1px solid
  tbody tr:nth-child(odd){background:
  .badge{display:inline-flex;align-items:center;gap:.4rem;font-size:.75rem;font-weight:800;border-radius:999px;padding:.15rem .5rem;border:1px solid
  .menu-btn{border:none;background:transparent;color:
  .menu-btn:hover{color:

  .savebar{display:flex;justify-content:flex-end;margin-top:.5rem}
</style>

<div class="page py-3">
  <div class="container-w">
    <div class="grid">
      
      <aside class="sticky">
        <div class="aside">
          <div class="name">{{ $employee->name }}</div>
          <div class="meta">
            {{ optional($employee->designation)->name ?? '—' }} ·
            {{ optional($employee->department)->name ?? '—' }} ·
            {{ optional($employee->branch)->name ?? '—' }}
          </div>

          <div class="actions">
            <a href="{{ route('employee.show', \Illuminate\Support\Facades\Crypt::encrypt($employee->id)) }}" class="btn">
              {{ __('View Profile') }}
            </a>
            @can('create set salary')
              <button form="salary-form" class="btn-primary">{{ __('Save Salary') }}</button>
            @endcan
          </div>

          <div class="divider"></div>

          <div class="chip"><i></i> {{ __('Compensation Suite') }}</div>

          <nav class="vnav" role="tablist">
            <button class="vtab active" data-tab="tab-salary">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="2" d="M12 1v22M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H7"/></svg>
              {{ __('Salary') }}
            </button>
            <button class="vtab" data-tab="tab-allowance">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="2" d="M12 8V4m0 16v-4M4 12H2m20 0h-2"/></svg>
              {{ __('Allowance') }}
            </button>
            <button class="vtab" data-tab="tab-commission">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="2" d="M3 3h18v4H3zM3 9h18v4H3zM3 15h18v6H3z"/></svg>
              {{ __('Commission') }}
            </button>
            <button class="vtab" data-tab="tab-loan">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="2" d="M3 7h18M3 12h18M3 17h18M7 3v18"/></svg>
              {{ __('Loan') }}
            </button>
            <button class="vtab" data-tab="tab-deduction">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="2" d="M19 21H5a2 2 0 01-2-2V7l4-4h12l5 5v11a2 2 0 01-2 2z"/></svg>
              {{ __('Saturation Deduction') }}
            </button>
            <button class="vtab" data-tab="tab-other">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="12" cy="12" r="9" stroke-width="2"/><path stroke-width="2" d="M12 7v10M8 12h8"/></svg>
              {{ __('Other Payment') }}
            </button>
            <button class="vtab" data-tab="tab-overtime">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="12" cy="12" r="9" stroke-width="2"/><path stroke-width="2" d="M12 7v5l3 3"/></svg>
              {{ __('Overtime') }}
            </button>
          </nav>
        </div>
      </aside>

      <main class="content">
        
        <section id="tab-salary" class="active">
          <div class="section-head">
            <div>
              <h2 class="title mb-0">{{ __('Salary') }}</h2>
              <div class="subtitle">{{ __('Base pay & payslip type') }}</div>
            </div>
          </div>
          <div class="divider"></div>

          {{ Form::model($employee, ['route' => ['employee.salary.update', $employee->id], 'method' => 'POST', 'id'=>'salary-form']) }}
          <div class="formgrid">
            <div class="col-6">
              <label class="field">
                {{ Form::label('salary_type', __('Payslip Type')) }} <span class="text-danger">*</span>
                {{ Form::select('salary_type', $payslip_type, null, ['class' => 'ctl select2', 'required' => true]) }}
              </label>
            </div>
            <div class="col-6">
              <label class="field">
                {{ Form::label('salary', __('Salary')) }}
                {{ Form::number('salary', null, ['class' => 'ctl', 'required'=>true, 'step'=>'0.01', 'min'=>'0']) }}
              </label>
            </div>
            @can('create set salary')
              <div class="col-12 savebar">
                <button type="submit" class="btn-primary">{{ __('Save Changes') }}</button>
              </div>
            @endcan
          </div>
          {{ Form::close() }}
        </section>

        <section id="tab-allowance">
          <div class="section-head">
            <div>
              <h2 class="title mb-0">{{ __('Allowance') }}</h2>
              <div class="subtitle">{{ __('Recurring allowances for the employee') }}</div>
            </div>
          </div>
          <div class="divider"></div>

          {{ Form::open(['url'=>'allowance','method'=>'post']) }}
          @csrf
          {{ Form::hidden('employee_id',$employee->id) }}
          <div class="formgrid">
            <div class="col-4">
              <label class="field">
                {{ Form::label('allowance_option', __('Allowance Options')) }} <span class="text-danger">*</span>
                {{ Form::select('allowance_option',$allowance_options,null, ['class'=>'ctl select2','required'=>true]) }}
              </label>
            </div>
            <div class="col-4">
              <label class="field">
                {{ Form::label('title', __('Title')) }}
                {{ Form::text('title', null, ['class'=>'ctl','required'=>true]) }}
              </label>
            </div>
            <div class="col-4">
              <label class="field">
                {{ Form::label('amount', __('Amount')) }}
                {{ Form::number('amount', null, ['class'=>'ctl','required'=>true,'step'=>'0.01','min'=>'0']) }}
              </label>
            </div>
            @can('create allowance')
              <div class="col-12 savebar">
                <button class="btn-primary">{{ __('Add Allowance') }}</button>
              </div>
            @endcan
          </div>
          {{ Form::close() }}

          <div class="tablewrap mt-3">
            <table id="allowance-dataTable" class="clean">
              <thead>
                <tr>
                  <th>{{ __('Employee') }}</th>
                  <th>{{ __('Option') }}</th>
                  <th>{{ __('Title') }}</th>
                  <th>{{ __('Amount') }}</th>
                  <th width="120">{{ __('Action') }}</th>
                </tr>
              </thead>
              <tbody>
                @foreach ($allowances as $allowance)
                  <tr>
                    <td>{{ $allowance->employee()->name }}</td>
                    <td><span class="badge">{{ $allowance->allowance_option()->name }}</span></td>
                    <td>{{ $allowance->title }}</td>
                    <td>{{ \Auth::user()->priceFormat($allowance->amount) }}</td>
                    <td class="text-end">
                      @can('edit allowance')
                        <button class="menu-btn" data-url="{{ URL::to('allowance/'.$allowance->id.'/edit') }}" data-size="lg" data-ajax-popup="true" data-title="{{__('Edit Allowance')}}">✎</button>
                      @endcan
                      @can('delete allowance')
                        <a href="#" class="menu-btn"
                           data-confirm="{{__('Are You Sure?').'|'.__('This action can not be undone. Do you want to continue?')}}"
                           data-confirm-yes="document.getElementById('allowance-delete-form-{{$allowance->id}}').submit();">🗑</a>
                        {!! Form::open(['method' => 'DELETE', 'route' => ['allowance.destroy', $allowance->id],'id'=>'allowance-delete-form-'.$allowance->id]) !!}{!! Form::close() !!}
                      @endcan
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </section>

        <section id="tab-commission">
          <div class="section-head">
            <div>
              <h2 class="title mb-0">{{ __('Commission') }}</h2>
              <div class="subtitle">{{ __('Performance-based earnings') }}</div>
            </div>
          </div>
          <div class="divider"></div>

          {{ Form::open(['url'=>'commission','method'=>'post']) }}
          @csrf
          {{ Form::hidden('employee_id',$employee->id) }}
          <div class="formgrid">
            <div class="col-6">
              <label class="field">
                {{ Form::label('title', __('Title')) }}
                {{ Form::text('title', null, ['class'=>'ctl','required'=>true]) }}
              </label>
            </div>
            <div class="col-6">
              <label class="field">
                {{ Form::label('amount', __('Amount')) }}
                {{ Form::number('amount', null, ['class'=>'ctl','required'=>true,'step'=>'0.01','min'=>'0']) }}
              </label>
            </div>
            @can('create commission')
              <div class="col-12 savebar">
                <button class="btn-primary">{{ __('Add Commission') }}</button>
              </div>
            @endcan
          </div>
          {{ Form::close() }}

          <div class="tablewrap mt-3">
            <table id="commission-dataTable" class="clean">
              <thead>
                <tr>
                  <th>{{ __('Employee') }}</th>
                  <th>{{ __('Title') }}</th>
                  <th>{{ __('Amount') }}</th>
                  <th width="120">{{ __('Action') }}</th>
                </tr>
              </thead>
              <tbody>
                @foreach ($commissions as $commission)
                  <tr>
                    <td>{{ $commission->employee()->name }}</td>
                    <td>{{ $commission->title }}</td>
                    <td>{{ \Auth::user()->priceFormat($commission->amount) }}</td>
                    <td class="text-end">
                      @can('edit commission')
                        <button class="menu-btn" data-url="{{ URL::to('commission/'.$commission->id.'/edit') }}" data-size="lg" data-ajax-popup="true" data-title="{{__('Edit Commission')}}">✎</button>
                      @endcan
                      @can('delete commission')
                        <a href="#" class="menu-btn"
                           data-confirm="{{__('Are You Sure?').'|'.__('This action can not be undone. Do you want to continue?')}}"
                           data-confirm-yes="document.getElementById('commission-delete-form-{{$commission->id}}').submit();">🗑</a>
                        {!! Form::open(['method' => 'DELETE', 'route' => ['commission.destroy', $commission->id],'id'=>'commission-delete-form-'.$commission->id]) !!}{!! Form::close() !!}
                      @endcan
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </section>

        <section id="tab-loan">
          <div class="section-head">
            <div>
              <h2 class="title mb-0">{{ __('Loan') }}</h2>
              <div class="subtitle">{{ __('Company-issued loans') }}</div>
            </div>
          </div>
          <div class="divider"></div>

          {{ Form::open(['url'=>'loan','method'=>'post']) }}
          @csrf
          {{ Form::hidden('employee_id',$employee->id) }}
          <div class="formgrid">
            <div class="col-4">
              <label class="field">
                {{ Form::label('loan_option', __('Loan Options')) }} <span class="text-danger">*</span>
                {{ Form::select('loan_option',$loan_options,null,['class'=>'ctl select2','required'=>true]) }}
              </label>
            </div>
            <div class="col-4">
              <label class="field">
                {{ Form::label('title', __('Title')) }}
                {{ Form::text('title', null, ['class'=>'ctl','required'=>true]) }}
              </label>
            </div>
            <div class="col-4">
              <label class="field">
                {{ Form::label('amount', __('Loan Amount')) }}
                {{ Form::number('amount', null, ['class'=>'ctl','required'=>true,'step'=>'0.01','min'=>'0']) }}
              </label>
            </div>

            <div class="col-4">
              <label class="field">
                {{ Form::label('start_date', __('Start Date')) }}
                {{ Form::text('start_date', null, ['class'=>'ctl datepicker','required'=>true]) }}
              </label>
            </div>
            <div class="col-4">
              <label class="field">
                {{ Form::label('end_date', __('End Date')) }}
                {{ Form::text('end_date', null, ['class'=>'ctl datepicker','required'=>true]) }}
              </label>
            </div>
            <div class="col-4">
              <label class="field">
                {{ Form::label('reason', __('Reason')) }}
                {{ Form::textarea('reason', null, ['class'=>'ctl','rows'=>1,'required'=>true]) }}
              </label>
            </div>
            @can('create loan')
              <div class="col-12 savebar">
                <button class="btn-primary">{{ __('Add Loan') }}</button>
              </div>
            @endcan
          </div>
          {{ Form::close() }}

          <div class="tablewrap mt-3">
            <table id="loan-dataTable" class="clean">
              <thead>
                <tr>
                  <th>{{ __('Employee') }}</th>
                  <th>{{ __('Option') }}</th>
                  <th>{{ __('Title') }}</th>
                  <th>{{ __('Amount') }}</th>
                  <th>{{ __('Start Date') }}</th>
                  <th>{{ __('End Date') }}</th>
                  <th width="120">{{ __('Action') }}</th>
                </tr>
              </thead>
              <tbody>
                @foreach ($loans as $loan)
                  <tr>
                    <td>{{ $loan->employee()->name }}</td>
                    <td><span class="badge">{{ $loan->loan_option()->name }}</span></td>
                    <td>{{ $loan->title }}</td>
                    <td>{{ \Auth::user()->priceFormat($loan->amount) }}</td>
                    <td>{{ \Auth::user()->dateFormat($loan->start_date) }}</td>
                    <td>{{ \Auth::user()->dateFormat($loan->end_date) }}</td>
                    <td class="text-end">
                      @can('edit loan')
                        <button class="menu-btn" data-url="{{ URL::to('loan/'.$loan->id.'/edit') }}" data-size="lg" data-ajax-popup="true" data-title="{{__('Edit Loan')}}">✎</button>
                      @endcan
                      @can('delete loan')
                        <a href="#" class="menu-btn"
                           data-confirm="{{__('Are You Sure?').'|'.__('This action can not be undone. Do you want to continue?')}}"
                           data-confirm-yes="document.getElementById('loan-delete-form-{{$loan->id}}').submit();">🗑</a>
                        {!! Form::open(['method' => 'DELETE', 'route' => ['loan.destroy', $loan->id],'id'=>'loan-delete-form-'.$loan->id]) !!}{!! Form::close() !!}
                      @endcan
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </section>

        <section id="tab-deduction">
          <div class="section-head">
            <div>
              <h2 class="title mb-0">{{ __('Saturation Deduction') }}</h2>
              <div class="subtitle">{{ __('Deductions applied to the employee') }}</div>
            </div>
          </div>
          <div class="divider"></div>

          {{ Form::open(['url'=>'saturationdeduction','method'=>'post']) }}
          @csrf
          {{ Form::hidden('employee_id',$employee->id) }}
          <div class="formgrid">
            <div class="col-4">
              <label class="field">
                {{ Form::label('deduction_option', __('Deduction Options')) }} <span class="text-danger">*</span>
                {{ Form::select('deduction_option',$deduction_options,null,['class'=>'ctl select2','required'=>true]) }}
              </label>
            </div>
            <div class="col-4">
              <label class="field">
                {{ Form::label('title', __('Title')) }}
                {{ Form::text('title', null, ['class'=>'ctl','required'=>true]) }}
              </label>
            </div>
            <div class="col-4">
              <label class="field">
                {{ Form::label('amount', __('Amount')) }}
                {{ Form::number('amount', null, ['class'=>'ctl','required'=>true,'step'=>'0.01','min'=>'0']) }}
              </label>
            </div>
            @can('create saturation deduction')
              <div class="col-12 savebar">
                <button class="btn-primary">{{ __('Add Deduction') }}</button>
              </div>
            @endcan
          </div>
          {{ Form::close() }}

          <div class="tablewrap mt-3">
            <table id="saturation-deduction-dataTable" class="clean">
              <thead>
                <tr>
                  <th>{{ __('Employee') }}</th>
                  <th>{{ __('Option') }}</th>
                  <th>{{ __('Title') }}</th>
                  <th>{{ __('Amount') }}</th>
                  <th width="120">{{ __('Action') }}</th>
                </tr>
              </thead>
              <tbody>
                @foreach ($saturationdeductions as $sd)
                  <tr>
                    <td>{{ $sd->employee()->name }}</td>
                    <td><span class="badge">{{ $sd->deduction_option()->name }}</span></td>
                    <td>{{ $sd->title }}</td>
                    <td>{{ \Auth::user()->priceFormat($sd->amount) }}</td>
                    <td class="text-end">
                      @can('edit saturation deduction')
                        <button class="menu-btn" data-url="{{ URL::to('saturationdeduction/'.$sd->id.'/edit') }}" data-size="lg" data-ajax-popup="true" data-title="{{__('Edit Saturation Deduction')}}">✎</button>
                      @endcan
                      @can('delete saturation deduction')
                        <a href="#" class="menu-btn"
                           data-confirm="{{__('Are You Sure?').'|'.__('This action can not be undone. Do you want to continue?')}}"
                           data-confirm-yes="document.getElementById('deduction-delete-form-{{$sd->id}}').submit();">🗑</a>
                        {!! Form::open(['method' => 'DELETE', 'route' => ['saturationdeduction.destroy', $sd->id],'id'=>'deduction-delete-form-'.$sd->id]) !!}{!! Form::close() !!}
                      @endcan
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </section>

        <section id="tab-other">
          <div class="section-head">
            <div>
              <h2 class="title mb-0">{{ __('Other Payment') }}</h2>
              <div class="subtitle">{{ __('One-off or miscellaneous payments') }}</div>
            </div>
          </div>
          <div class="divider"></div>

          {{ Form::open(['url'=>'otherpayment','method'=>'post']) }}
          @csrf
          {{ Form::hidden('employee_id',$employee->id) }}
          <div class="formgrid">
            <div class="col-6">
              <label class="field">
                {{ Form::label('title', __('Title')) }}
                {{ Form::text('title', null, ['class'=>'ctl','required'=>true]) }}
              </label>
            </div>
            <div class="col-6">
              <label class="field">
                {{ Form::label('amount', __('Amount')) }}
                {{ Form::number('amount', null, ['class'=>'ctl','required'=>true,'step'=>'0.01','min'=>'0']) }}
              </label>
            </div>
            @can('create other payment')
              <div class="col-12 savebar">
                <button class="btn-primary">{{ __('Add Payment') }}</button>
              </div>
            @endcan
          </div>
          {{ Form::close() }}

          <div class="tablewrap mt-3">
            <table id="other-payment-dataTable" class="clean">
              <thead>
                <tr>
                  <th>{{ __('Employee') }}</th>
                  <th>{{ __('Title') }}</th>
                  <th>{{ __('Amount') }}</th>
                  <th width="120">{{ __('Action') }}</th>
                </tr>
              </thead>
              <tbody>
                @foreach ($otherpayments as $other)
                  <tr>
                    <td>{{ $other->employee()->name }}</td>
                    <td>{{ $other->title }}</td>
                    <td>{{ \Auth::user()->priceFormat($other->amount) }}</td>
                    <td class="text-end">
                      @can('edit other payment')
                        <button class="menu-btn" data-url="{{ URL::to('otherpayment/'.$other->id.'/edit') }}" data-size="lg" data-ajax-popup="true" data-title="{{__('Edit Other Payment')}}">✎</button>
                      @endcan
                      @can('delete other payment')
                        <a href="#" class="menu-btn"
                           data-confirm="{{__('Are You Sure?').'|'.__('This action can not be undone. Do you want to continue?')}}"
                           data-confirm-yes="document.getElementById('payment-delete-form-{{$other->id}}').submit();">🗑</a>
                        {!! Form::open(['method' => 'DELETE', 'route' => ['otherpayment.destroy', $other->id],'id'=>'payment-delete-form-'.$other->id]) !!}{!! Form::close() !!}
                      @endcan
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </section>

        <section id="tab-overtime">
          <div class="section-head">
            <div>
              <h2 class="title mb-0">{{ __('Overtime') }}</h2>
              <div class="subtitle">{{ __('Extra hours & rates') }}</div>
            </div>
          </div>
          <div class="divider"></div>

          {{ Form::open(['url'=>'overtime','method'=>'post']) }}
          @csrf
          {{ Form::hidden('employee_id',$employee->id) }}
          <div class="formgrid">
            <div class="col-6">
              <label class="field">
                {{ Form::label('title', __('Overtime Title')) }} <span class="text-danger">*</span>
                {{ Form::text('title', null, ['class'=>'ctl','required'=>true]) }}
              </label>
            </div>
            <div class="col-6">
              <label class="field">
                {{ Form::label('number_of_days', __('Number of days')) }}
                {{ Form::number('number_of_days', null, ['class'=>'ctl','required'=>true,'step'=>'0.01','min'=>'0']) }}
              </label>
            </div>
            <div class="col-6">
              <label class="field">
                {{ Form::label('hours', __('Hours')) }}
                {{ Form::number('hours', null, ['class'=>'ctl','required'=>true,'step'=>'0.01','min'=>'0']) }}
              </label>
            </div>
            <div class="col-6">
              <label class="field">
                {{ Form::label('rate', __('Rate')) }}
                {{ Form::number('rate', null, ['class'=>'ctl','required'=>true,'step'=>'0.01','min'=>'0']) }}
              </label>
            </div>
            @can('create overtime')
              <div class="col-12 savebar">
                <button class="btn-primary">{{ __('Add Overtime') }}</button>
              </div>
            @endcan
          </div>
          {{ Form::close() }}

          <div class="tablewrap mt-3">
            <table id="overtime-dataTable" class="clean">
              <thead>
              <tr>
                <th>{{ __('Employee') }}</th>
                <th>{{ __('Overtime Title') }}</th>
                <th>{{ __('Number of days') }}</th>
                <th>{{ __('Hours') }}</th>
                <th>{{ __('Rate') }}</th>
                <th width="120">{{ __('Action') }}</th>
              </tr>
              </thead>
              <tbody>
              @foreach ($overtimes as $overtime)
                <tr>
                  <td>{{ $overtime->employee()->name }}</td>
                  <td>{{ $overtime->title }}</td>
                  <td>{{ $overtime->number_of_days }}</td>
                  <td>{{ $overtime->hours }}</td>
                  <td>{{ \Auth::user()->priceFormat($overtime->rate) }}</td>
                  <td class="text-end">
                    @can('edit overtime')
                      <button class="menu-btn" data-url="{{ URL::to('overtime/'.$overtime->id.'/edit') }}" data-size="lg" data-ajax-popup="true" data-title="{{__('Edit OverTime')}}">✎</button>
                    @endcan
                    @can('delete overtime')
                      <a href="#" class="menu-btn"
                         data-confirm="{{__('Are You Sure?').'|'.__('This action can not be undone. Do you want to continue?')}}"
                         data-confirm-yes="document.getElementById('overtime-delete-form-{{$overtime->id}}').submit();">🗑</a>
                      {!! Form::open(['method' => 'DELETE', 'route' => ['overtime.destroy', $overtime->id],'id'=>'overtime-delete-form-'.$overtime->id]) !!}{!! Form::close() !!}
                    @endcan
                  </td>
                </tr>
              @endforeach
              </tbody>
            </table>
          </div>
        </section>
      </main>
    </div>
  </div>
</div>
@endsection

@push('script-page')
<script>
  document.querySelectorAll('.vtab').forEach(btn=>{
    btn.addEventListener('click',()=>{
      const id = btn.dataset.tab
      document.querySelectorAll('.vtab').forEach(b=>b.classList.remove('active'))
      btn.classList.add('active')
      document.querySelectorAll('.content > section').forEach(s=>s.classList.remove('active'))
      document.getElementById(id).classList.add('active')
    })
  })

  $(function () {
    $("#allowance-dataTable").dataTable({ "columnDefs":[{"sortable":false,"targets":[4]}] });
    $("#commission-dataTable").dataTable({ "columnDefs":[{"sortable":false,"targets":[3]}] });
    $("#loan-dataTable").dataTable({ "columnDefs":[{"sortable":false,"targets":[6]}] });
    $("#saturation-deduction-dataTable").dataTable({ "columnDefs":[{"sortable":false,"targets":[4]}] });
    $("#other-payment-dataTable").dataTable({ "columnDefs":[{"sortable":false,"targets":[3]}] });
    $("#overtime-dataTable").dataTable({ "columnDefs":[{"sortable":false,"targets":[5]}] });
  });

  $(document).on('click','[data-ajax-popup="true"]',function(e){ e.preventDefault(); });
</script>
@endpush
