<script src="{{ asset('js/unsaved.js') }}"></script>

@extends('layouts.admin')
@section('page-title') {{ __('Edit Production Draft') }} — {{ $job->code }} @endsection

@section('breadcrumb')
  <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
  <li class="breadcrumb-item"><a href="{{ route('production.index') }}">{{ __('Production') }}</a></li>
  <li class="breadcrumb-item">{{ __('Edit Draft') }}</li>
@endsection

@push('script-page')
<script>
/** Formatting helpers */
const fmt2 = (n)=> (isFinite(n) ? (new Intl.NumberFormat()).format((+n).toFixed(2)) : '0.00');
const fmt4 = (n)=> (isFinite(n) ? (+n).toFixed(4).replace(/\.?0+$/,'') : '0');
const num  = (v, d=0)=> (v==null || v==='' || isNaN(v)) ? d : +v;

// Use the current multiplier when filling desired qty from the anchor
const EDIT_MULTIPLIER = Number("{{ $job->multiplier ?? 1 }}");

let BOM = null;

function resetPreview(){
  document.getElementById('comp-body').innerHTML = '';
  document.getElementById('out-body').innerHTML = '';
  ['components-total','additional-total','batch-cost','kpi-multiplier','kpi-batch-cost','kpi-revenue','kpi-profit','kpi-margin']
    .forEach(id => document.getElementById(id).textContent = '0.00');
}

async function loadBom(bomId, opts = {}){
  BOM = null; resetPreview();
  const outSel = document.getElementById('target_output_id');
  outSel.innerHTML = `<option value="">${'{{ __('Select') }}'}</option>`;
  if(!bomId) return;

  const base = document.getElementById('bom-details-base').value; // e.g. /boms
  const res  = await fetch(`${base}/${bomId}/details`, { headers: {'X-Requested-With':'XMLHttpRequest'} });
  if(!res.ok) return;
  const data = await res.json();
  (data.inputs||[]).forEach(i => i.product = i.product || {});
  (data.outputs||[]).forEach(o => o.product = o.product || {});
  BOM = data;

  // Fill target outputs
  outSel.innerHTML = '';
  (data.outputs||[]).forEach(o=>{
    const opt = document.createElement('option');
    opt.value = o.product_id;
    opt.textContent = (o.product?.name ?? ('#'+o.product_id)) + ` — ${fmt4(num(o.qty_per_batch,0))} / {{ __('batch') }}`;
    outSel.appendChild(opt);
  });

  const oldTarget = (opts.oldTargetId || '').trim();
  if (oldTarget && outSel.querySelector(`option[value="${oldTarget}"]`)) {
    outSel.value = oldTarget;
  } else if ((data.outputs||[]).length) {
    outSel.value = data.outputs[0].product_id;
  }

  const qtyInput = document.getElementById('target_good_qty');
  // If user hasn't typed anything, derive from anchor × current multiplier
  if (!qtyInput.value && (data.outputs||[]).length){
    const anchor = data.outputs.find(o => +o.product_id === +outSel.value) || data.outputs[0];
    const baseAnchor = num(anchor.qty_per_batch, 1);
    qtyInput.value = fmt4(baseAnchor * Math.max(EDIT_MULTIPLIER, 0.0001));
  }

  // Set hidden multiplier to current value (so form remains stable)
  document.getElementById('batch_multiplier').value = EDIT_MULTIPLIER;

  recalc();
}

function recalc(){
  if(!BOM) { resetPreview(); return; }

  const targetId = +document.getElementById('target_output_id').value || null;
  const desired  = num(document.getElementById('target_good_qty').value, 0);

  const labor    = num(document.getElementById('labor_cost').value, 0);
  const overhead = num(document.getElementById('overhead_cost').value, 0);
  const other    = num(document.getElementById('other_cost').value, 0);
  const addl     = labor + overhead + other;
  document.getElementById('additional-total').textContent = fmt2(addl);

  const anchor = (BOM.outputs||[]).find(o => +o.product_id === targetId) || (BOM.outputs||[])[0];
  if(!anchor){ resetPreview(); return; }

  const baseAnchor = Math.max(num(anchor.qty_per_batch, 0.0001), 0.0001);
  const multiplier = desired > 0 ? (desired / baseAnchor) : num(document.getElementById('batch_multiplier').value || 1, 1);
  document.getElementById('batch_multiplier').value = multiplier > 0 ? multiplier : '';

  // Components
  let compTotal = 0;
  const compBody = document.getElementById('comp-body');
  compBody.innerHTML = '';
  (BOM.inputs||[]).forEach(row=>{
    const unit   = num(row.product?.purchase_price, 0);
    const req    = num(row.qty_per_batch, 0) * multiplier;
    const line   = unit * req;
    compTotal   += line;
    compBody.insertAdjacentHTML('beforeend', `
      <tr>
        <td class="px-3 py-2 border border-[#E5E5E5]">${row.product?.name ?? '-'}</td>
        <td class="px-3 py-2 border border-[#E5E5E5]">${row.product?.sku ?? '-'}</td>
        <td class="px-3 py-2 border border-[#E5E5E5] text-end">${fmt4(req)}</td>
        <td class="px-3 py-2 border border-[#E5E5E5]">${row.product?.unit?.name ?? '-'}</td>
        <td class="px-3 py-2 border border-[#E5E5E5] text-end">{{ \Auth::user()->currencySymbol() }} ${fmt2(unit)}</td>
        <td class="px-3 py-2 border border-[#E5E5E5] text-end">{{ \Auth::user()->currencySymbol() }} ${fmt2(line)}</td>
      </tr>
    `);
  });
  document.getElementById('components-total').textContent = fmt2(compTotal);
  const batchCost = compTotal + addl;
  document.getElementById('batch-cost').textContent = fmt2(batchCost);

  // Outputs & profitability
  let totalPlanned = 0;
  (BOM.outputs||[]).forEach(o => totalPlanned += num(o.qty_per_batch,0) * multiplier);

  let totRevenue = 0, totProfit = 0;
  const outBody = document.getElementById('out-body');
  outBody.innerHTML = '';
  (BOM.outputs||[]).forEach(o=>{
    const qty     = num(o.qty_per_batch,0) * multiplier;
    const share   = totalPlanned>0 ? qty/totalPlanned : 0;
    const alloc   = batchCost * share;
    const unitC   = qty>0 ? alloc/qty : 0;
    const sp      = num(o.product?.sale_price, 0);
    const revenue = sp * qty;
    const profit  = revenue - alloc;
    const margin  = revenue>0 ? (profit/revenue)*100 : 0;

    totRevenue += revenue; totProfit += profit;

    outBody.insertAdjacentHTML('beforeend', `
      <tr ${+o.product_id===targetId ? 'class="bg-[#F8FFF9]"' : ''}>
        <td class="px-3 py-2 border border-[#E5E5E5]">${o.product?.name ?? '-'}</td>
        <td class="px-3 py-2 border border-[#E5E5E5]">${o.product?.sku ?? '-'}</td>
        <td class="px-3 py-2 border border-[#E5E5E5] text-end">${fmt4(qty)}</td>
        <td class="px-3 py-2 border border-[#E5E5E5]">${o.product?.unit?.name ?? '-'}</td>
        <td class="px-3 py-2 border border-[#E5E5E5] text-end">{{ \Auth::user()->currencySymbol() }} ${fmt2(unitC)}</td>
        <td class="px-3 py-2 border border-[#E5E5E5] text-end>{{ \Auth::user()->currencySymbol() }} ${fmt2(sp)}</td>
        <td class="px-3 py-2 border border-[#E5E5E5] text-end">{{ \Auth::user()->currencySymbol() }} ${fmt2(revenue)}</td>
        <td class="px-3 py-2 border border-[#E5E5E5] text-end">{{ \Auth::user()->currencySymbol() }} ${fmt2(profit)}</td>
        <td class="px-3 py-2 border border-[#E5E5E5] text-end">${fmt2(margin)}%</td>
      </tr>
    `);
  });

  document.getElementById('kpi-multiplier').textContent = fmt4(multiplier);
  document.getElementById('kpi-batch-cost').textContent = fmt2(batchCost);
  document.getElementById('kpi-revenue').textContent    = fmt2(totRevenue);
  document.getElementById('kpi-profit').textContent     = fmt2(totProfit);
  const gm = totRevenue>0 ? (totProfit/totRevenue)*100 : 0;
  document.getElementById('kpi-margin').textContent     = fmt2(gm) + '%';
}

document.addEventListener('DOMContentLoaded', function(){
  const bomSelect = document.getElementById('bom_id');
  const preVal = Number("{{ old('bom_id', (int)($preselected_bom_id ?? 0)) }}");
  const oldTargetId = "{{ old('target_output_id', '') }}";

  if(bomSelect) {
    bomSelect.addEventListener('change', e => loadBom(e.target.value, {oldTargetId}));
    if(preVal && preVal > 0){
      bomSelect.value = preVal;
      loadBom(preVal, {oldTargetId});
    }
  }
  ['target_output_id','target_good_qty','labor_cost','overhead_cost','other_cost'].forEach(id=>{
    const el = document.getElementById(id);
    el.addEventListener('input', recalc);
    el.addEventListener('change', recalc);
  });
  const form = document.getElementById('prod-form');
  if (form) form.addEventListener('submit', recalc);
});
</script>
@endpush

@section('content')
<div class="row">
  {{-- Flash / validation --}}
  @if (session('error'))
    <div class="col-12 mb-3">
      <div class="bg-red-50 text-red-700 border border-red-200 px-4 py-3 rounded-[6px]">
        {{ session('error') }}
      </div>
    </div>
  @endif
  @if ($errors->any())
    <div class="col-12 mb-3">
      <div class="bg-yellow-50 text-yellow-800 border border-yellow-200 px-4 py-3 rounded-[6px]">
        <ul class="mb-0 ps-4">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    </div>
  @endif

  {{ Form::open(['route' => ['production.update', $job->id], 'id'=>'prod-form', 'class'=>'w-100 needs-validation','novalidate', 'method'=>'PUT']) }}
  <input type="hidden" id="bom-details-base" value="{{ url('boms') }}">
  {{ Form::hidden('batch_multiplier', old('batch_multiplier', $job->multiplier), ['id'=>'batch_multiplier']) }}

  <div class="col-12">
    <div class="bg-white rounded-[8px] border border-[#E5E7EB] shadow-sm overflow-hidden mb-6">
      <div class="heading-cstm-form">
        <h6 class="mb-0 flex items-center gap-2">
          <i class="ti ti-workflow"></i> {{ __('Production Setup') }} <span class="text-xs text-gray-500 ms-2">({{ $job->code }})</span>
        </h6>
      </div>
      <div class="p-6">
        <div class="row g-4">
          <div class="col-md-6">
            <div class="form-group">
              {{ Form::label('bom_id', __('Select BOM'), ['class'=>'form-label']) }} <x-required />
              {{ Form::select('bom_id', $bomOptions, old('bom_id', $preselected_bom_id ?? null), ['id'=>'bom_id','class'=>'form-control select','required'=>true]) }}
              <small class="text-[#6B7280]">{{ __('Only active BOMs are listed.') }}</small>
            </div>
          </div>
          <div class="col-md-6">
            <div class="grid grid-cols-2 gap-3">
              <div class="form-group">
                {{ Form::label('target_output_id', __('Target Output'), ['class'=>'form-label']) }}
                <select id="target_output_id" class="form-control"></select>
                <small class="text-[#6B7280]">{{ __('Scaling is based on this finished item.') }}</small>
              </div>
              <div class="form-group">
                {{ Form::label('target_good_qty', __('Desired Finished Qty'), ['class'=>'form-label']) }}
                <input type="number" step="0.0001" min="0.0001" id="target_good_qty" name="target_good_qty" class="form-control"
                       placeholder="0.0000" value="{{ old('target_good_qty') }}">
              </div>
            </div>
          </div>

          <div class="col-md-3">
            {{ Form::label('planned_date', __('Planned Date'), ['class'=>'form-label']) }}
            {{ Form::date('planned_date', old('planned_date', optional($job->planned_date)->format('Y-m-d') ?? date('Y-m-d')), ['class'=>'form-control']) }}
          </div>
          <div class="col-md-9">
            <div class="grid md:grid-cols-3 gap-3">
              <div>
                <label class="form-label">{{ __('Labor / Making') }}</label>
                <div class="input-group">
                  <span class="input-group-text">₦</span>
                  <input type="number" step="0.01" min="0" id="labor_cost" name="labor_cost" class="form-control"
                         value="{{ old('labor_cost', 0) }}">
                </div>
              </div>
              <div>
                <label class="form-label">{{ __('Overhead') }}</label>
                <div class="input-group">
                  <span class="input-group-text">₦</span>
                  <input type="number" step="0.01" min="0" id="overhead_cost" name="overhead_cost" class="form-control"
                         value="{{ old('overhead_cost', 0) }}">
                </div>
              </div>
              <div>
                <label class="form-label">{{ __('Other') }}</label>
                <div class="input-group">
                  <span class="input-group-text">₦</span>
                  <input type="number" step="0.01" min="0" id="other_cost" name="other_cost" class="form-control"
                         value="{{ old('other_cost', 0) }}">
                </div>
              </div>
            </div>
            <small class="text-[#6B7280]">{{ __('These will be added to raw material cost as Manufacturing Cost.') }}</small>
          </div>

          <div class="col-md-12">
            {{ Form::label('notes', __('Notes'), ['class'=>'form-label']) }}
            {{ Form::textarea('notes', old('notes', $job->notes), ['class'=>'form-control','rows'=>2]) }}
          </div>
        </div>
      </div>
    </div>

    {{-- KPI Cards --}}
    <div class="grid md:grid-cols-5 gap-3 mb-6">
      <div class="p-3 rounded-lg border bg-white">
        <div class="text-xs text-[#6B7280]">{{ __('Multiplier') }}</div>
        <div class="text-lg font-semibold"><i class="ti ti-gauge"></i> <span id="kpi-multiplier">0.00</span>x</div>
      </div>
      <div class="p-3 rounded-lg border bg-white">
        <div class="text-xs text-[#6B7280]">{{ __('Estimated Batch Cost') }}</div>
        <div class="text-lg font-semibold"><i class="ti ti-cash"></i> <span id="kpi-batch-cost">0.00</span></div>
      </div>
      <div class="p-3 rounded-lg border bg-white">
        <div class="text-xs text-[#6B7280]">{{ __('Estimated Revenue') }}</div>
        <div class="text-lg font-semibold"><i class="ti ti-chart-bar"></i> <span id="kpi-revenue">0.00</span></div>
      </div>
      <div class="p-3 rounded-lg border bg-white">
        <div class="text-xs text-[#6B7280]">{{ __('Gross Profit') }}</div>
        <div class="text-lg font-semibold"><i class="ti ti-trending-up"></i> <span id="kpi-profit">0.00</span></div>
      </div>
      <div class="p-3 rounded-lg border bg-white">
        <div class="text-xs text-[#6B7280]">{{ __('Gross Margin') }}</div>
        <div class="text-lg font-semibold"><i class="ti ti-percentage"></i> <span id="kpi-margin">0.00%</span></div>
      </div>
    </div>

    {{-- Raw Materials --}}
    <div class="bg-white rounded-[8px] border border-[#E5E5E7] shadow-sm overflow-hidden mb-6">
      <div class="heading-cstm-form">
        <h6 class="mb-0 flex items-center gap-2"><i class="ti ti-box"></i> {{ __('Raw Materials Required') }}</h6>
      </div>
      <div class="table-responsive table-new-design bg-white p-4">
        <table class="table datatable border border-[#E5E5E5] rounded-[8px] dataTable-table">
          <thead>
            <tr>
              <th class="px-4 py-1 border bg-[#F6F6F6] font-[600] text-[12px]">{{ __('Item') }}</th>
              <th class="px-4 py-1 border bg-[#F6F6F6] font-[600] text-[12px]">{{ __('SKU') }}</th>
              <th class="px-4 py-1 border bg-[#F6F6F6] font-[600] text-[12px] text-end">{{ __('Req. Qty') }}</th>
              <th class="px-4 py-1 border bg-[#F6F6F6] font-[600] text-[12px]">{{ __('Unit') }}</th>
              <th class="px-4 py-1 border bg-[#F6F6F6] font-[600] text-[12px] text-end">{{ __('Unit Cost') }}</th>
              <th class="px-4 py-1 border bg-[#F6F6F6] font-[600] text-[12px] text-end">{{ __('Line Cost') }}</th>
            </tr>
          </thead>
          <tbody id="comp-body"></tbody>
          <tfoot>
            <tr>
              <td colspan="5" class="px-3 py-2 border text-end font-semibold">{{ __('Components Total') }}</td>
              <td class="px-3 py-2 border text-end font-bold">{{ \Auth::user()->currencySymbol() }} <span id="components-total">0.00</span></td>
            </tr>
            <tr>
              <td colspan="5" class="px-3 py-2 border text-end font-semibold">{{ __('Additional Cost') }}</td>
              <td class="px-3 py-2 border text-end font-bold">{{ \Auth::user()->currencySymbol() }} <span id="additional-total">0.00</span></td>
            </tr>
            <tr>
              <td colspan="5" class="px-3 py-2 border text-end font-bold">{{ __('Estimated Batch Cost') }}</td>
              <td class="px-3 py-2 border text-end font-extrabold">{{ \Auth::user()->currencySymbol() }} <span id="batch-cost">0.00</span></td>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>

    {{-- Finished Products & Profitability --}}
    <div class="bg-white rounded-[8px] border border-[#E5E5E7] shadow-sm overflow-hidden">
      <div class="heading-cstm-form">
        <h6 class="mb-0 flex items-center gap-2"><i class="ti ti-currency-dollar"></i> {{ __('Finished Products & Profitability') }}</h6>
      </div>
      <div class="table-responsive table-new-design bg-white p-4">
        <table class="table datatable border border-[#E5E5E5] rounded-[8px] dataTable-table">
          <thead>
            <tr>
              <th class="px-4 py-1 border bg-[#F6F6F6] font-[600] text-[12px]">{{ __('Product') }}</th>
              <th class="px-4 py-1 border bg-[#F6F6F6] font-[600] text-[12px]">{{ __('SKU') }}</th>
              <th class="px-4 py-1 border bg-[#F6F6F6] font-[600] text-[12px] text-end">{{ __('Planned Qty') }}</th>
              <th class="px-4 py-1 border bg-[#F6F6F6] font-[600] text-[12px]">{{ __('Unit') }}</th>
              <th class="px-4 py-1 border bg-[#F6F6F6] font-[600] text-[12px] text-end">{{ __('Unit Cost (Alloc.)') }}</th>
              <th class="px-4 py-1 border bg-[#F6F6F6] font-[600] text-[12px] text-end">{{ __('Selling Price') }}</th>
              <th class="px-4 py-1 border bg-[#F6F6F6] font-[600] text-[12px] text-end">{{ __('Revenue') }}</th>
              <th class="px-4 py-1 border bg-[#F6F6F6] font-[600] text-[12px] text-end">{{ __('Profit') }}</th>
              <th class="px-4 py-1 border bg-[#F6F6F6] font-[600] text-[12px] text-end">{{ __('Margin') }}</th>
            </tr>
          </thead>
          <tbody id="out-body"></tbody>
        </table>
      </div>
    </div>
  </div>

  <div class="modal-footer border-t border-[#E5E5E5] bg-[#FAFAFA] px-6 py-4 flex justify-end gap-3">
    <a href="{{ route('production.show', $job->id) }}" class="btn py-[6px] px-[10px] text-[#007C38] border-[#007C38] hover:bg-[#007C38] hover:text-white">{{ __('Cancel') }}</a>
    <button type="submit" class="btn py-[6px] px-[10px] bg-gray-600 text-white hover:bg-gray-700">
      <i class="ti ti-device-floppy"></i> {{ __('Save Changes') }}
    </button>
  </div>
  {{ Form::close() }}
</div>
@endsection
