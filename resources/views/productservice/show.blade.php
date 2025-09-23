@php
  $product = $productService;
  $isProduct = strtolower($product->type) === 'product';
  $mt = $product->material_type;
  $mtLabel = '-';
  if ($mt === 'raw') $mtLabel = __('Raw material');
  elseif ($mt === 'finished') $mtLabel = __('Finished product');
  elseif ($mt === 'both') $mtLabel = __('Both');

  $taxList = [];
  if (!empty($product->tax_id)) {
      $taxIds = array_filter(array_map('intval', explode(',', (string) $product->tax_id)));
      if (!empty($taxIds)) {
          $taxList = \App\Models\Tax::whereIn('id', $taxIds)->get();
      }
  }
@endphp

<div class="p-0">

  {{-- Header --}}
  <div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-6">
      <div class="d-flex align-items-center gap-3">
        <div class="w-16 h-16 rounded-circle d-flex align-items-center justify-content-center shadow-md"
             style="background:linear-gradient(135deg,#007C38,#005f2a)">
          <span class="text-white fw-bold" style="font-size:20px;">
            {{ strtoupper(substr($product->name ?? 'NA', 0, 2)) }}
          </span>
        </div>
        <div class="flex-1">
          <div class="d-flex align-items-center gap-2 mb-2">
            <h4 class="mb-0 fw-bold text-dark">{{ $product->name ?? 'N/A' }}</h4>
            <span class="badge rounded-pill
              {{ $isProduct ? 'bg-green-100 text-success border border-success' : 'bg-blue-100 text-primary border border-primary' }}"
              style="font-weight:600;">
              {{ $product->type }}
            </span>
            @if($isProduct && $mt)
              <span class="badge rounded-pill bg-gray-100 text-dark border"
                    style="font-weight:600;">
                {{ $mtLabel }}
              </span>
            @endif
          </div>
          <div class="d-flex flex-wrap gap-3 text-muted">
            <div class="d-flex align-items-center gap-1">
              <i class="ti ti-tag"></i>
              <span class="small">{{ __('SKU:') }} <strong>{{ $product->sku ?? 'N/A' }}</strong></span>
            </div>
            <div class="d-flex align-items-center gap-1">
              <i class="ti ti-calendar"></i>
              <span class="small">{{ __('Created:') }}
                <strong>{{ \Auth::user()->dateFormat($product->created_at) }}</strong></span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- Pricing / Stock --}}
  <div class="card border-0 shadow-sm mb-4 overflow-hidden px-4">
    <div class="px-4 py-3" style="background:linear-gradient(90deg,#007C38,#005f2a)">
      <h6 class="text-white fw-bold mb-0">{{ __('Pricing & Stock') }}</h6>
    </div>
    <div class="card-body p-4">
      <div class="row g-4">
        <div class="col-md-4">
          <div class="text-muted small mb-1">{{ __('Sale price') }}</div>
          <div>{{ \Auth::user()->priceFormat($product->sale_price) }}</div>
        </div>
        <div class="col-md-4">
          <div class="text-muted small mb-1">{{ __('Purchase price') }}</div>
          <div>{{ \Auth::user()->priceFormat($product->purchase_price) }}</div>
        </div>
        <div class="col-md-4">
          <div class="text-muted small mb-1">{{ __('Quantity') }}</div>
          <div>{{ $isProduct ? $product->quantity : '—' }}</div>
        </div>
      </div>
    </div>
  </div>

  {{-- Classification --}}
  <div class="card border-0 shadow-sm mb-4 overflow-hidden px-4">
    <div class="px-4 py-3" style="background:linear-gradient(90deg,#007C38,#005f2a)">
      <h6 class="text-white fw-bold mb-0">{{ __('Classification') }}</h6>
    </div>
    <div class="card-body p-4">
      <div class="row g-4">
        <div class="col-md-4">
          <div class="text-muted small mb-1">{{ __('Category') }}</div>
          <div>{{ optional($product->category)->name ?? 'N/A' }}</div>
        </div>
        <div class="col-md-4">
          <div class="text-muted small mb-1">{{ __('Unit') }}</div>
          <div>{{ optional($product->unit)->name ?? 'N/A' }}</div>
        </div>
        <div class="col-md-4">
          <div class="text-muted small mb-1">{{ __('Material') }}</div>
          <div>{{ $isProduct ? $mtLabel : '—' }}</div>
        </div>
      </div>
    </div>
  </div>

  {{-- Taxes --}}
  <div class="card border-0 shadow-sm mb-4 overflow-hidden px-4">
    <div class="px-4 py-3" style="background:linear-gradient(90deg,#007C38,#005f2a)">
      <h6 class="text-white fw-bold mb-0">{{ __('Taxes') }}</h6>
    </div>
    <div class="card-body p-4">
      @if(!empty($taxList) && count($taxList))
        <div class="d-flex flex-wrap gap-2">
          @foreach($taxList as $tax)
            <span class="badge bg-light text-dark border">{{ $tax->name }}</span>
          @endforeach
        </div>
      @else
        <div class="text-muted">{{ __('No tax applied.') }}</div>
      @endif
    </div>
  </div>

  {{-- Accounts --}}
  <div class="card border-0 shadow-sm mb-4 overflow-hidden px-4">
    <div class="px-4 py-3" style="background:linear-gradient(90deg,#007C38,#005f2a)">
      <h6 class="text-white fw-bold mb-0">{{ __('Accounts') }}</h6>
    </div>
    <div class="card-body p-4">
      <div class="row g-4">
        <div class="col-md-6">
          <div class="text-muted small mb-1">{{ __('Sales Account') }}</div>
          <div>
            @php
              $saleAccount = $product->sale_chartaccount_id
                ? \App\Models\ChartOfAccount::find($product->sale_chartaccount_id)
                : null;
            @endphp
            {{ $saleAccount ? ($saleAccount->code.' - '.$saleAccount->name) : '—' }}
          </div>
        </div>
        <div class="col-md-6">
          <div class="text-muted small mb-1">{{ __('Expense Account') }}</div>
          <div>
            @php
              $expAccount = $product->expense_chartaccount_id
                ? \App\Models\ChartOfAccount::find($product->expense_chartaccount_id)
                : null;
            @endphp
            {{ $expAccount ? ($expAccount->code.' - '.$expAccount->name) : '—' }}
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- Description --}}
  <div class="card border-0 shadow-sm mb-2 overflow-hidden px-4">
    <div class="px-4 py-3" style="background:linear-gradient(90deg,#007C38,#005f2a)">
      <h6 class="text-white fw-bold mb-0">{{ __('Description') }}</h6>
    </div>
    <div class="card-body p-4">
      <div class="text-muted">
        {!! nl2br(e($product->description ?? __('No description.'))) !!}
      </div>
    </div>
  </div>

</div>
