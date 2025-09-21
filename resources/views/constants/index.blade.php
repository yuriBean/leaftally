{{-- resources/views/constants/index.blade.php --}}
@extends('layouts.admin') {{-- or your main app layout --}}
@section('page-title', __('Account Setup'))

@php
  // convenience color
  $brand = '#007C38';
@endphp

@section('breadcrumb')
 <div class="d-block">
    <h1 class="text-2xl md:text-3xl font-semibold text-slate-800">{{ __('Account Setup') }}</h1>
  <p class="text-slate-500 mt-1">{{ __('Configure all master lists used across the app—taxes, categories, units, payroll items, etc.') }}</p>

 </div>@endsection

@section('content')
<div class=" py-6">

  {{-- Top actions / search --}}
  <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-6">
    <div class="relative w-full md:max-w-2xl">
      <input
        type="text"
        id="constants-search"
        placeholder="{{ __('Search constants…') }}"
        class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 pr-10 focus:ring-2 focus:ring-[{{ $brand }}]/30 focus:border-[{{ $brand }}] outline-none"
        oninput="filterCards(this.value)"
      >
      <svg class="absolute right-3 top-2.5 h-5 w-5 text-slate-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-4.35-4.35M11 19a8 8 0 1 1 0-16 8 8 0 0 1 0 16z"/></svg>
    </div>

    {{-- Optional: back to settings --}}
    <a href="{{ route('company.setting') }}"
       class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2 text-slate-700 hover:bg-slate-50">
      <img src="{{ asset('web-assets/dashboard/icons/setting.svg') }}" class="h-4 w-4" alt="">
      <span>{{ __('System Setting') }}</span>
    </a>
  </div>

  {{-- Sections --}}
  <div class="space-y-8">

    {{-- Financial / Tax --}}
    <section>
      <h2 class="mb-3 text-sm font-semibold uppercase tracking-wide text-slate-500">{{ __('Financial & Tax') }}</h2>
      <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
        @can('manage constant bank')
          @include('constants._card', [
            'icon' => 'bank.svg',
            'title' => __('Banks'),
            'desc' => __('Bank names used for employee banking info.'),
            'indexRoute' => route('banks.index'),
            'createCan' => Gate::check('create constant bank'),
            'createAttrs' => [
              'href' => route('banks.create'),
              'data-ajax-popup' => 'true',
              'data-title' => __('Add Bank'),
            ],
          ])
        @endcan
        @can('manage constant tax')
        @include('constants._card', [
          'icon' => 'tax.svg',
          'title' => __('Taxes'),
          'desc' => __('VAT/GST rates used on invoices, bills, and products.'),
          'indexRoute' => route('taxes.index'),
          'createCan' => Gate::check('manage constant tax'),
          'createAttrs' => [
            'href' => route('taxes.create'),
            'data-ajax-popup' => 'true',
            'data-title' => __('Add Tax'),
          ],
        ])
        @endcan

        @can('manage constant chart of account')
        @include('constants._card', [
          'icon' => 'double-entry.svg',
          'title' => __('Chart of Account Types'),
          'desc' => __('Account types for categorising ledgers and postings.'),
          'indexRoute' => route('chart-of-account-type.index'),
          'createCan' => Gate::check('manage constant chart of account'),
          'createAttrs' => [
            'href' => route('chart-of-account-type.create'),
            'data-ajax-popup' => 'true',
            'data-title' => __('Add Account Type'),
          ],
        ])
        @endcan
      </div>
    </section>

    {{-- Sales & Products --}}
    <section>
      <h2 class="mb-3 text-sm font-semibold uppercase tracking-wide text-slate-500">{{ __('Sales & Products') }}</h2>
      <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
        @can('manage constant category')
        @include('constants._card', [
          'icon' => 'product_&_services.svg',
          'title' => __('Product Categories'),
          'desc' => __('Group products & services for reporting and filters.'),
          'indexRoute' => route('product-category.index'),
          'createCan' => Gate::check('manage constant category'),
          'createAttrs' => [
            'href' => route('product-category.create'),
            'data-ajax-popup' => 'true',
            'data-title' => __('Add Category'),
          ],
        ])
        @endcan

        @can('manage constant unit')
        @include('constants._card', [
          'icon' => 'inventory.svg',
          'title' => __('Units'),
          'desc' => __('Measurement units (pcs, hrs, kg) for products.'),
          'indexRoute' => route('product-unit.index'),
          'createCan' => Gate::check('manage constant unit'),
          'createAttrs' => [
            'href' => route('product-unit.create'),
            'data-ajax-popup' => 'true',
            'data-title' => __('Add Unit'),
          ],
        ])
        @endcan

        @can('manage constant payment method')
        @include('constants._card', [
          'icon' => 'payments.svg',
          'title' => __('Payment Methods'),
          'desc' => __('Cash, bank transfer, card, cheque, mobile wallet, etc.'),
          'indexRoute' => route('payment-method.index'),
          'createCan' => Gate::check('manage constant payment method'),
          'createAttrs' => [
            'href' => route('payment-method.create'),
            'data-ajax-popup' => 'true',
            'data-title' => __('Add Payment Method'),
          ],
        ])
        @endcan
      </div>
    </section>

    {{-- Projects / Contracts / Custom Fields --}}
    <section>
      <h2 class="mb-3 text-sm font-semibold uppercase tracking-wide text-slate-500">{{ __('Projects & Customisation') }}</h2>
      <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
        @can('manage constant contract type')
        @include('constants._card', [
          'icon' => 'contract.svg',
          'title' => __('Contract Types'),
          'desc' => __('Reusable types for client/vendor contracts.'),
          'indexRoute' => route('contractType.index'),
          'createCan' => Gate::check('manage constant contract type'),
          'createAttrs' => [
            'href' => route('contractType.create'),
            'data-ajax-popup' => 'true',
            'data-title' => __('Add Contract Type'),
          ],
        ])
        @endcan

        @can('manage constant custom field')
        @include('constants._card', [
          'icon' => 'custom-field.svg', // add this asset or reuse 'setting.svg'
          'title' => __('Custom Fields'),
          'desc' => __('Add extra fields to forms (customers, invoices, etc.).'),
          'indexRoute' => route('custom-field.index'),
          'createCan' => Gate::check('manage constant custom field'),
          'createAttrs' => [
            'href' => route('custom-field.create'),
            'data-ajax-popup' => 'true',
            'data-title' => __('Add Custom Field'),
          ],
        ])
        @endcan
      </div>
    </section>

   
  </div>
</div>

{{-- Tiny filter (client-side only) --}}
<script>
  function filterCards(q='') {
    const term = q.trim().toLowerCase();
    document.querySelectorAll('[data-constant-card]').forEach(card => {
      const hay = (card.getAttribute('data-title') + ' ' + card.getAttribute('data-desc')).toLowerCase();
      card.style.display = hay.includes(term) ? '' : 'none';
    });
  }
</script>
@endsection
