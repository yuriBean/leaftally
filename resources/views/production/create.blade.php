<script src="{{ asset('js/unsaved.js') }}"></script>

{{-- resources/views/production/create.blade.php --}}
@extends('layouts.admin')
@section('page-title') {{ __('New Production Order') }} @endsection

@section('breadcrumb')
  <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
  <li class="breadcrumb-item"><a href="{{ route('production.index') }}">{{ __('Production') }}</a></li>
  <li class="breadcrumb-item">{{ __('Create') }}</li>
@endsection

@push('script-page')
<script>
const fmt2 = (n)=> (isFinite(n) ? (new Intl.NumberFormat()).format((+n).toFixed(2)) : '0.00');
const fmt4 = (n)=> (isFinite(n) ? (+n).toFixed(4).replace(/\.?0+$/,'') : '0');
const num  = (v, d=0)=> (v==null || v==='' || isNaN(v)) ? d : +v;

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

  const base = document.getElementById('bom-details-base').value;
  const res  = await fetch(`${base}/${bomId}/details`, { headers: {'X-Requested-With':'XMLHttpRequest'} });
  if(!res.ok) return;
  const data = await res.json();
  (data.inputs||[]).forEach(i => i.product = i.product || {});
  (data.outputs||[]).forEach(o => o.product = o.product || {});
  BOM = data;

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
  if (!qtyInput.value && (data.outputs||[]).length){
    const anchor = data.outputs.find(o => +o.product_id === +outSel.value) || data.outputs[0];
    qtyInput.value = fmt4(num(anchor.qty_per_batch,1));
  }

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
        <td class="px-3 py-2 border border-[#E5E5E5] text-end">{{ \Auth::user()->currencySymbol() }} ${fmt2(sp)}</td>
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
<div class="row mt-5">
  @if(isset($bomCount) && $bomCount === 0)
  <div class="col-12">
    <div class="alert alert-warning">
      <i class="ti ti-alert-triangle"></i>
      <strong>{{ __('No BOMs available') }}</strong>: {{ __('You need at least one active BOM before creating production orders.') }}
      <a href="{{ route('bom.create') }}" class="btn btn-sm btn-outline-dark ms-2">{{ __('Create BOM First') }}</a>
    </div>
  </div>
  @endif

  {{ Form::open(['route' => 'production.store', 'id'=>'prod-form', 'class'=>'w-100 needs-validation','novalidate']) }}
  <input type="hidden" id="bom-details-base" value="{{ url('boms') }}">
  {{ Form::hidden('batch_multiplier', old('batch_multiplier', ''), ['id'=>'batch_multiplier']) }}

  {{-- Toolbar / Title --}}
  <div class="col-12 mb-4">
    <div class="bg-white rounded border shadow-sm p-3 p-md-4 d-flex flex-wrap align-items-center justify-content-between gap-2">
      <div class="d-flex align-items-center gap-2">
        <span class="rounded-circle bg-[#007c38] mt-1" style="width:12px;height:12px;"></span>
        <h5 class="mb-0 fw-bold fs-4">{{ __('Create Production Order') }}</h5>
      </div>
      <div class="d-flex gap-2">
        <a href="{{ route('production.index') }}" class="btn btn-light">{{ __('Back to List') }}</a>
        <button type="submit" name="action" value="save_draft" class="btn btn-secondary">
          <i class="ti ti-device-floppy"></i> {{ __('Save Draft') }}
        </button>
        <button type="submit" name="action" value="start" class="btn btn-warning text-white">
          <i class="ti ti-player-play"></i> {{ __('Start') }}
        </button>
        <button type="submit" name="action" value="finish" class="btn btn-success text-white">
          <i class="ti ti-check"></i> {{ __('Finish Now') }}
        </button>
      </div>
    </div>
  </div>

  {{-- Main 2-column layout --}}
  <div class="col-12">
    <div class="row g-4">
      {{-- LEFT: setup + preview --}}
      <div class="col-12 col-lg-8">
        {{-- Production Setup --}}
        <div class="bg-white rounded border shadow-sm overflow-hidden">
          <div class="p-3 p-md-4 border-bottom">
            <h6 class="mb-0 d-flex align-items-center gap-2">
              <i class="ti ti-workflow"></i> <span>{{ __('Production Setup') }}</span>
            </h6>
          </div>
          <div class="p-3 p-md-4">
            <div class="row g-4">
              <div class="col-md-6">
                {{ Form::label('bom_id', __('Select BOM'), ['class'=>'form-label fw-semibold']) }} <x-required />
                {{ Form::select('bom_id', $bomOptions, old('bom_id', $preselected_bom_id ?? null), ['id'=>'bom_id','class'=>'form-control select','required'=>true]) }}
                <small class="text-muted">{{ __('Only active BOMs are listed.') }}</small>
              </div>

              <div class="col-md-6">
                <div class="row g-3">
                  <div class="col-12">
                    {{ Form::label('target_output_id', __('Target Output'), ['class'=>'form-label fw-semibold']) }}
                    <select id="target_output_id" class="form-control"></select>
                    <small class="text-muted">{{ __('Scaling is based on this finished item.') }}</small>
                  </div>
                  <div class="col-12">
                    {{ Form::label('target_good_qty', __('Desired Finished Qty'), ['class'=>'form-label fw-semibold']) }}
                    <input type="number" step="0.0001" min="0.0001" id="target_good_qty" name="target_good_qty" class="form-control" placeholder="0.0000" value="{{ old('target_good_qty') }}">
                  </div>
                </div>
              </div>

              <div class="col-md-4">
                {{ Form::label('planned_date', __('Planned Date'), ['class'=>'form-label fw-semibold']) }}
                {{ Form::date('planned_date', old('planned_date', date('Y-m-d')), ['class'=>'form-control']) }}
              </div>
              <div class="col-md-8">
                <div class="row g-3">
                  <div class="col-md-4">
                    <label class="form-label fw-semibold">{{ __('Labor / Making') }}</label>
                    <div class="input-group">
                      <span class="input-group-text">{{ \Auth::user()->currencySymbol() }}</span>
                      <input type="number" step="0.01" min="0" id="labor_cost" name="labor_cost" class="form-control" value="{{ old('labor_cost', 0) }}">
                    </div>
                  </div>
                  <div class="col-md-4">
                    <label class="form-label fw-semibold">{{ __('Overhead') }}</label>
                    <div class="input-group">
                      <span class="input-group-text">{{ \Auth::user()->currencySymbol() }}</span>
                      <input type="number" step="0.01" min="0" id="overhead_cost" name="overhead_cost" class="form-control" value="{{ old('overhead_cost', 0) }}">
                    </div>
                  </div>
                  <div class="col-md-4">
                    <label class="form-label fw-semibold">{{ __('Other') }}</label>
                    <div class="input-group">
                      <span class="input-group-text">{{ \Auth::user()->currencySymbol() }}</span>
                      <input type="number" step="0.01" min="0" id="other_cost" name="other_cost" class="form-control" value="{{ old('other_cost', 0) }}">
                    </div>
                  </div>
                  <div class="col-12">
                    <small class="text-muted">{{ __('These add to raw component total as Manufacturing Cost.') }}</small>
                  </div>
                </div>
              </div>

              <div class="col-12">
                {{ Form::label('notes', __('Notes'), ['class'=>'form-label fw-semibold']) }}
                {{ Form::textarea('notes', old('notes'), ['class'=>'form-control','rows'=>2,'placeholder'=>__('Optional: instructions, cautions, remarks…')]) }}
              </div>
            </div>
          </div>
        </div>

        {{-- Raw Materials Table --}}
        <div class="bg-white rounded border shadow-sm overflow-hidden mt-4">
          <div class="p-3 p-md-4 border-bottom">
            <h6 class="mb-0 d-flex align-items-center gap-2">
              <i class="ti ti-box"></i> <span>{{ __('Raw Materials Required') }}</span>
            </h6>
          </div>
          <div class="p-3 p-md-4">
            <div class="table-responsive">
              <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                  <tr>
                    <th>{{ __('Item') }}</th>
                    <th>{{ __('SKU') }}</th>
                    <th class="text-end">{{ __('Req. Qty') }}</th>
                    <th>{{ __('Unit') }}</th>
                    <th class="text-end">{{ __('Unit Cost') }}</th>
                    <th class="text-end">{{ __('Line Cost') }}</th>
                  </tr>
                </thead>
                <tbody id="comp-body"></tbody>
                <tfoot>
                  <tr>
                    <td colspan="5" class="text-end fw-semibold">{{ __('Components Total') }}</td>
                    <td class="text-end fw-bold">{{ \Auth::user()->currencySymbol() }} <span id="components-total">0.00</span></td>
                  </tr>
                  <tr>
                    <td colspan="5" class="text-end fw-semibold">{{ __('Additional Cost') }}</td>
                    <td class="text-end fw-bold">{{ \Auth::user()->currencySymbol() }} <span id="additional-total">0.00</span></td>
                  </tr>
                  <tr>
                    <td colspan="5" class="text-end fw-bold">{{ __('Estimated Batch Cost') }}</td>
                    <td class="text-end fw-bolder">{{ \Auth::user()->currencySymbol() }} <span id="batch-cost">0.00</span></td>
                  </tr>
                </tfoot>
              </table>
            </div>
          </div>
        </div>

        {{-- Finished Products Table --}}
        <div class="bg-white rounded border shadow-sm overflow-hidden mt-4">
          <div class="p-3 p-md-4 border-bottom">
            <h6 class="mb-0 d-flex align-items-center gap-2">
              <i class="ti ti-currency-dollar"></i> <span>{{ __('Finished Products & Profitability') }}</span>
            </h6>
          </div>
          <div class="p-3 p-md-4">
            <div class="table-responsive">
              <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                  <tr>
                    <th>{{ __('Product') }}</th>
                    <th>{{ __('SKU') }}</th>
                    <th class="text-end">{{ __('Planned Qty') }}</th>
                    <th>{{ __('Unit') }}</th>
                    <th class="text-end">{{ __('Unit Cost (Alloc.)') }}</th>
                    <th class="text-end">{{ __('Selling Price') }}</th>
                    <th class="text-end">{{ __('Revenue') }}</th>
                    <th class="text-end">{{ __('Profit') }}</th>
                    <th class="text-end">{{ __('Margin') }}</th>
                  </tr>
                </thead>
                <tbody id="out-body"></tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      {{-- RIGHT: sticky summary --}}
      <div class="col-12 col-lg-4">
        <div class="position-sticky" style="top: 80px;">
          {{-- KPI Cards --}}
          <div class="bg-white rounded border shadow-sm overflow-hidden">
            <div class="p-3 p-md-4 border-bottom">
              <h6 class="mb-0 d-flex align-items-center gap-2">
                <i class="ti ti-gauge"></i> <span>{{ __('Summary') }}</span>
              </h6>
            </div>
            <div class="p-3 p-md-4">
              <div class="row g-3">
                <div class="col-6">
                  <div class="p-3 rounded-3 border">
                    <div class="text-xs text-muted">{{ __('Multiplier') }}</div>
                    <div class="h5 mb-0"><span id="kpi-multiplier">0.00</span>x</div>
                  </div>
                </div>
                <div class="col-6">
                  <div class="p-3 rounded-3 border">
                    <div class="text-xs text-muted">{{ __('Batch Cost') }}</div>
                    <div class="h5 mb-0"><span id="kpi-batch-cost">0.00</span></div>
                  </div>
                </div>
                <div class="col-6">
                  <div class="p-3 rounded-3 border">
                    <div class="text-xs text-muted">{{ __('Revenue') }}</div>
                    <div class="h5 mb-0"><span id="kpi-revenue">0.00</span></div>
                  </div>
                </div>
                <div class="col-6">
                  <div class="p-3 rounded-3 border">
                    <div class="text-xs text-muted">{{ __('Gross Profit') }}</div>
                    <div class="h5 mb-0"><span id="kpi-profit">0.00</span></div>
                  </div>
                </div>
                <div class="col-12">
                  <div class="p-3 rounded-3 border">
                    <div class="text-xs text-muted">{{ __('Gross Margin') }}</div>
                    <div class="h5 mb-0"><span id="kpi-margin">0.00%</span></div>
                  </div>
                </div>
              </div>

              <hr class="my-4" />

              {{-- Action block --}}
              <div class="d-grid gap-2">
                <button type="submit" name="action" value="start" class="btn btn-warning text-white">
                  <i class="ti ti-player-play"></i> {{ __('Start (In Process)') }}
                </button>
                <button type="submit" name="action" value="finish" class="btn btn-success text-white">
                  <i class="ti ti-check"></i> {{ __('Finish Now') }}
                </button>
                <button type="submit" name="action" value="save_draft" class="btn btn-secondary">
                  <i class="ti ti-device-floppy"></i> {{ __('Save Draft') }}
                </button>
                <a href="{{ route('production.index') }}" class="btn btn-light">{{ __('Cancel') }}</a>
              </div>
            </div>
          </div>

          {{-- Helper / Tips (optional copy) --}}
          <div class="bg-white rounded border shadow-sm overflow-hidden mt-4">
            <div class="p-3 p-md-4">
              <div class="small text-muted">
                {{ __('Tip: Pick a BOM, set target item + quantity. Costs update instantly based on multiplier and additional manufacturing costs.') }}
              </div>
            </div>
          </div>
        </div>
      </div>
      {{-- /RIGHT --}}
    </div>
  </div>

  {{-- Footer (kept minimal, sticky actions live in the sidebar) --}}
  <div class="col-12 d-lg-none">
    <div class="modal-footer border-top bg-light px-3 px-md-4 py-3 d-flex justify-content-end gap-2">
      <a href="{{ route('production.index') }}" class="btn btn-light">{{ __('Cancel') }}</a>
      <button type="submit" name="action" value="save_draft" class="btn btn-secondary">
        <i class="ti ti-device-floppy"></i> {{ __('Save Draft') }}
      </button>
      <button type="submit" name="action" value="start" class="btn btn-warning text-white">
        <i class="ti ti-player-play"></i> {{ __('Start') }}
      </button>
      <button type="submit" name="action" value="finish" class="btn btn-success text-white">
        <i class="ti ti-check"></i> {{ __('Finish Now') }}
      </button>
    </div>
  </div>

  {{ Form::close() }}
</div>
@endsection
