@extends('layouts.admin')

@section('page-title')
  {{ __('BOM') }} — {{ $bom->code }} ({{ $bom->name }})
@endsection

@section('breadcrumb')
  <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
  <li class="breadcrumb-item"><a href="{{ route('bom.index') }}">{{ __('BOM') }}</a></li>
  <li class="breadcrumb-item">{{ $bom->code }}</li>
@endsection

@section('action-btn')
  <div class="flex items-center gap-2 mt-2 sm:mt-0">
    @can('edit bom')
    <a href="{{ route('bom.edit', $bom->id) }}"
       class="flex items-center gap-2 bg-[#007C38] text-white px-4 py-2 rounded-[6px] text-[14px] font-[500] hover:bg-[#005f2a] transition-all duration-200 shadow-sm min-w-fit"
       data-bs-toggle="tooltip" title="{{ __('Edit') }}">
      <i class="ti ti-pencil"></i>{{ __('Edit BOM') }}
    </a>
    @endcan

    @can('create production')
    <a href="{{ route('production.create') }}?bom_id={{ $bom->id }}"
       class="flex items-center gap-2 border border-[#007C38] text-[#007C38] bg-white px-4 py-2 rounded-[6px] text-[14px] font-[500] hover:bg-[#007C380f] transition-all duration-200 shadow-sm min-w-fit"
       data-bs-toggle="tooltip" title="{{ __('Start Production') }}">
      <i class="ti ti-player-play"></i>{{ __('Start Production') }}
    </a>
    @endcan

    @can('create bom')
    {!! Form::open(['method' => 'post', 'route' => ['bom.duplicate', $bom->id]]) !!}
      <button type="submit" class="flex items-center gap-2 border border-[#E5E7EB] text-[#374151] bg-white px-4 py-2 rounded-[6px] text-[14px] font-[500] hover:bg-[#F3F4F6] transition-all duration-200 shadow-sm min-w-fit"
              data-bs-toggle="tooltip" title="{{ __('Duplicate') }}">
        <i class="ti ti-copy"></i>{{ __('Duplicate') }}
      </button>
    {!! Form::close() !!}
    @endcan
  </div>
@endsection

@push('script-page')
<script>
  function sumColumn(cls) {
    let total = 0;
    document.querySelectorAll(cls).forEach(el => {
      const v = parseFloat((el.textContent || '0').replace(/,/g,''));
      if(!isNaN(v)) total += v;
    });
    return total;
  }
  function format(n){ try { return (new Intl.NumberFormat()).format(n.toFixed(2)); } catch(e){ return n.toFixed(2);} }

  document.addEventListener('DOMContentLoaded', function(){
    const compTotal = sumColumn('.js-comp-line-total');
    const compTotalEl = document.querySelector('#js-components-total');
    if(compTotalEl) compTotalEl.textContent = format(compTotal);

    const batchCost = compTotal;
    const batchCostEl = document.querySelector('#js-batch-cost');
    if(batchCostEl) batchCostEl.textContent = format(batchCost);

    const outRows = document.querySelectorAll('[data-output-row]');
    let totalOutQty = 0;
    outRows.forEach(r => totalOutQty += parseFloat(r.getAttribute('data-qty')) || 0);
    outRows.forEach(r => {
      const qty = parseFloat(r.getAttribute('data-qty')) || 0;
      const share = totalOutQty > 0 ? (qty / totalOutQty) : 0;
      const alloc = batchCost * share;
      const perUnit = qty > 0 ? alloc / qty : 0;
      r.querySelector('.js-output-alloc').textContent = format(alloc);
      r.querySelector('.js-output-unit-cost').textContent = format(perUnit);
    });
  });
</script>
@endpush

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card border border-[#E5E5E5] rounded-[8px]">
      <div class="card-body p-4">
        <div class="row">
          <div class="col-md-8">
            <h3 class="text-[#111827] text-lg font-semibold mb-1">{{ $bom->name }} <span class="text-sm text-[#6B7280]">({{ $bom->code }})</span></h3>
            @if($bom->notes)<p class="text-[#6B7280] mb-0">{{ $bom->notes }}</p>@endif
          </div>
          <div class="col-md-4">
            <div class="grid grid-cols-2 gap-2">
              <div class="p-3 bg-[#F9FAFB] rounded-[8px] border">
                <div class="text-xs text-[#6B7280]">{{ __('Active') }}</div>
                <div class="text-[16px] font-[600]">
                  @if($bom->is_active)
                    <span class="badge fix_badges bg-primary p-2 px-3">{{ __('Yes') }}</span>
                  @else
                    <span class="badge fix_badges bg-secondary p-2 px-3">{{ __('No') }}</span>
                  @endif
                </div>
              </div>
              <div class="p-3 bg-[#F9FAFB] rounded-[8px] border">
                <div class="text-xs text-[#6B7280]">{{ __('Updated') }}</div>
                <div class="text-[16px] font-[600]">{{ \Auth::user()->dateFormat($bom->updated_at) }}</div>
              </div>
            </div>
          </div>
        </div>

        {{-- Components --}}
        <div class="mt-4">
          <h5 class="h5 mb-3">{{ __('Raw Materials') }}</h5>
          <div class="table-responsive">
            <table class="table table-custom-style">
              <thead class="bg-[#F6F6F6]">
                <tr>
                  <th class="border px-3 py-2">{{ __('Item') }}</th>
                  <th class="border px-3 py-2">{{ __('SKU') }}</th>
                  <th class="border px-3 py-2 text-end">{{ __('Qty / Batch') }}</th>
                  <th class="border px-3 py-2">{{ __('Unit') }}</th>
                  <th class="border px-3 py-2 text-end">{{ __('Avg Cost') }} ({{ \Auth::user()->currencySymbol() }})</th>
                  <th class="border px-3 py-2 text-end">{{ __('Line Cost') }} ({{ \Auth::user()->currencySymbol() }})</th>
                </tr>
              </thead>
              <tbody>
                @forelse($bom->inputs as $row)
                  @php
                    $avg = $row->product->purchase_price ?? 0;
                    $lineCost = ($row->qty_per_batch) * $avg;
                  @endphp
                  <tr>
                    <td class="border px-3 py-2">{{ $row->product->name ?? '-' }}</td>
                    <td class="border px-3 py-2">{{ $row->product->sku ?? '-' }}</td>
                    <td class="border px-3 py-2 text-end">{{ rtrim(rtrim(number_format($row->qty_per_batch, 4, '.', ''), '0'), '.') }}</td>
                    <td class="border px-3 py-2">{{ $row->product?->unit?->name ?? '-' }}</td>
                    <td class="border px-3 py-2 text-end">{{ number_format($avg,2) }}</td>
                    <td class="border px-3 py-2 text-end js-comp-line-total">{{ number_format($lineCost,2) }}</td>
                  </tr>
                @empty
                  <tr><td colspan="6" class="text-center py-3 text-[#6B7280]">{{ __('No components') }}</td></tr>
                @endforelse
              </tbody>
              <tfoot>
                <tr>
                  <td colspan="5" class="text-end font-[600]">{{ __('Components Total') }} ({{ \Auth::user()->currencySymbol() }})</td>
                  <td class="text-end font-[700]" id="js-components-total">0.00</td>
                </tr>
              </tfoot>
            </table>
          </div>
        </div>

        {{-- Outputs & allocation --}}
        <div class="mt-4">
          <h5 class="h5 mb-3">{{ __('Finished Products (per batch)') }}</h5>
          <div class="table-responsive">
            <table class="table table-custom-style">
              <thead class="bg-[#F6F6F6]">
                <tr>
                  <th class="border px-3 py-2">{{ __('Product') }}</th>
                  <th class="border px-3 py-2">{{ __('SKU') }}</th>
                  <th class="border px-3 py-2 text-end">{{ __('Qty / Batch') }}</th>
                  <th class="border px-3 py-2">{{ __('Unit') }}</th>
                  <th class="border px-3 py-2 text-end">{{ __('Allocated Cost') }}</th>
                  <th class="border px-3 py-2 text-end">{{ __('Unit Cost') }}</th>
                </tr>
              </thead>
              <tbody>
                @forelse($bom->outputs as $out)
                  <tr data-output-row data-qty="{{ (float)$out->qty_per_batch }}">
                    <td class="border px-3 py-2">{{ $out->product->name ?? '-' }}</td>
                    <td class="border px-3 py-2">{{ $out->product->sku ?? '-' }}</td>
                    <td class="border px-3 py-2 text-end">{{ rtrim(rtrim(number_format($out->qty_per_batch,4,'.',''), '0'),'.') }}</td>
                    <td class="border px-3 py-2">{{$out->product?->unit?->name ?? '-' }}</td>
                    <td class="border px-3 py-2 text-end js-output-alloc">0.00</td>
                    <td class="border px-3 py-2 text-end js-output-unit-cost">0.00</td>
                  </tr>
                @empty
                  <tr><td colspan="6" class="text-center py-3 text-[#6B7280]">{{ __('No outputs configured') }}</td></tr>
                @endforelse
              </tbody>
              <tfoot>
                <tr>
                  <td colspan="4" class="text-end font-[600]">{{ __('Batch Cost (Components)') }} ({{ \Auth::user()->currencySymbol() }})</td>
                  <td class="text-end font-[700]" id="js-batch-cost">0.00</td>
                  <td></td>
                </tr>
              </tfoot>
            </table>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>
@endsection
