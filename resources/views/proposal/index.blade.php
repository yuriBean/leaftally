@extends('layouts.admin')

@section('page-title')    .mcheck{display:inline-flex;align-items:center;cursor:pointer;user-select:none}
    .mcheck input{position:absolute;opacity:0;width:0;height:0}
    .mcheck .box{width:20px;height:20px;border:2px solid #dee2e6;border-radius:4px;position:relative;}
    .mcheck .box:hover{box-shadow:0 1px 3px rgba(0,0,0,.08)}
    .mcheck input:focus + .box{box-shadow:0 0 0 3px rgba(0,124,56,.2)}
    .mcheck input:checked + .box{background:#007C38;border-color:#007C38;}
    .mcheck input:checked + .box::after{content:"";position:absolute;left:6px;top:2px;width:5px;height:10px;border:2px solid white;border-top:0;border-left:0;transform:rotate(45deg);} __('Manage Proposals') }}
@endsection

@section('breadcrumb')
    @if (\Auth::guard('customer')->check())
        <li class="breadcrumb-item"><a href="{{ route('customer.dashboard') }}">{{ __('Dashboard') }}</a></li>
    @else
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    @endif
    <li class="breadcrumb-item">{{ __('Proposal') }}</li>
@endsection

@section('action-btn')
    <div class="flex items-center gap-2 mt-2 sm:mt-0">
        <a href="#" data-size="md" data-bs-toggle="tooltip" title="{{ __('Import') }}"
           data-url="Proposal" data-ajax-popup="true"
           data-title="" style="border: 1px solid #007C38 !important"
           class="flex items-center gap-2 border border-[#007C38] text-[#007C38] bg-white px-4 py-2 rounded-[6px] text-[14px] font-[500] hover:bg-[#007C3808] transition-all duration-200 shadow-sm min-w-fit">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 0112-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            {{ __('Import') }}
        </a>

        {{-- Export all (unchanged) --}}
        <a href="{{ route('proposal.export') }}" style="border: 1px solid #007C38 !important"
           class="flex items-center gap-2 border border-[#007C38] text-[#007C38] bg-white px-4 py-2 rounded-[6px] text-[14px] font-[500] hover:bg-[#007C3808] transition-all duration-200 shadow-sm min-w-fit"
           data-bs-toggle="tooltip" title="{{ __('Export') }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
            </svg>
            {{ __('Export') }}
        </a>

        @can('create proposal')
            <a href="{{ route('proposal.create', 0) }}"
               class="flex items-center gap-2 bg-[#007C38] text-white px-4 py-2 rounded-[6px] text-[14px] font-[500] hover:bg-[#005f2a] transition-all duration-200 shadow-sm min-w-fit"
               data-bs-toggle="tooltip" title="{{ __('Create') }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                {{ __('Create') }}
            </a>
        @endcan
    </div>
@endsection

@push('css-page')
<style>
  .mcheck{display:inline-flex;align-items:center;cursor:pointer;user-select:none}
  .mcheck input{position:absolute;opacity:0;width:0;height:0}
  .mcheck .box{width:20px;height:20px;border:2px solid #dee2e6;border-radius:4px;position:relative;}
  .mcheck .box:hover{box-shadow:0 1px 3px rgba(0,0,0,.08)}
  .mcheck input:focus + .box{box-shadow:0 0 0 3px rgba(0,124,56,.2)}
  .mcheck input:checked + .box{background:#007C38;border-color:#007C38;}
  .mcheck input:checked + .box::after{content:"";position:absolute;left:6px;top:2px;width:5px;height:10px;border:2px solid white;border-top:0;border-left:0;transform:rotate(45deg);}
</style>
@endpush

@section('content')
    {{-- filters (unchanged) ... --}}

    <div class="row">
        <div class="col-md-12">

            {{-- Show bulk toolbar only to staff (not customer) and with permission --}}
            @if (!\Auth::guard('customer')->check())
                @can('delete proposal')
                    <x-bulk-toolbar
                        :deleteRoute="route('proposal.bulk-destroy')"
                        :exportRoute="route('proposal.export-selected')"
                        scope="proposals"
                        tableId="proposals-table"
                        selectedLabel="{{ __('Proposals selected') }}"
                        exportLabel="{{ __('Export Selected') }}"
                    />
                @endcan
            @endif

            <div class="card-body bg-white border border-[#E5E5E5] rounded-[8px] p-4">
                <div class="table-responsive table-new-design">
                    <table id="proposals-table" class="table datatable">
                        <thead class="bg-[#F6F6F6] text-[#323232] text-[12px] leading-[24px]">
                            <tr>
                                {{-- master checkbox (staff only) --}}
                                @if (!\Auth::guard('customer')->check())
                                    <th class="input-checkbox border border-[#E5E5E5] px-4 lg:px-6 py-4 text-left text-[12px] font-[700] text-[#374151] uppercase tracking-wider w-12">
                                        <label class="mcheck">
                                            <input type="checkbox" class="jsb-master" data-scope="proposals">
                                            <span class="box"></span>
                                        </label>
                                    </th>
                                @endif

                                <th class="px-4 py-1 border border-[#E5E5E5] font-[600]">{{ __('Proposal') }}</th>

                                @if (!\Auth::guard('customer')->check())
                                    <th class="px-4 py-1 border border-[#E5E5E5] font-[600]">{{ __('Customer') }}</th>
                                @endif

                                <th class="px-4 py-1 border border-[#E5E5E5] font-[600]">{{ __('Category') }}</th>
                                <th class="px-4 py-1 border border-[#E5E5E5] font-[600]">{{ __('Issue Date') }}</th>
                                <th class="px-4 py-1 border border-[#E5E5E5] font-[600]">{{ __('Status') }}</th>

                                @if (Gate::check('edit proposal') || Gate::check('delete proposal') || Gate::check('show proposal'))
                                    <th class="px-4 py-1 border border-[#E5E5E5] font-[600]" width="10%">{{ __('Action') }}</th>
                                @endif
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($proposals as $proposal)
                                <tr class="font-style">
                                    {{-- row checkbox (staff only) --}}
                                    @if (!\Auth::guard('customer')->check())
                                        <td class="input-checkbox px-4 lg:px-6 py-4 w-12">
                                            <label class="mcheck">
                                                <input type="checkbox"
                                                       class="jsb-item"
                                                       data-scope="proposals"
                                                       value="{{ $proposal->id }}"
                                                       data-id="{{ $proposal->id }}">
                                                <span class="box"></span>
                                            </label>
                                        </td>
                                    @endif

                                    <td class="Id px-4 py-3 border border-[#E5E5E5] text-gray-700">
                                        @if (\Auth::guard('customer')->check())
                                            <a href="{{ route('customer.proposal.show', \Crypt::encrypt($proposal->id)) }}"
                                               class="border border-[#137051] leading-[24px] text-[#137051] rounded-[4px] text-[12px] font-[500] px-5">
                                                {{ \Auth::user()->proposalNumberFormat($proposal->proposal_id) }}
                                            </a>
                                        @else
                                            <a href="{{ route('proposal.show', \Crypt::encrypt($proposal->id)) }}"
                                               class="border border-[#137051] leading-[24px] text-[#137051] rounded-[4px] text-[12px] font-[500] px-5">
                                                {{ \Auth::user()->proposalNumberFormat($proposal->proposal_id) }}
                                            </a>
                                        @endif
                                    </td>

                                    @if (!\Auth::guard('customer')->check())
                                        <td class="px-4 py-3 border border-[#E5E5E5]">
                                            {{ optional($proposal->customer)->name }}
                                        </td>
                                    @endif

                                    <td class="px-4 py-3 border border-[#E5E5E5]">
                                        {{ optional($proposal->category)->name }}
                                    </td>
                                    <td class="px-4 py-3 border border-[#E5E5E5]">
                                        {{ \Auth::user()->dateFormat($proposal->issue_date) }}
                                    </td>
                                    <td class="px-4 py-3 border border-[#E5E5E5]">
                                        @php $st = \App\Models\Proposal::$statues[$proposal->status] ?? ''; @endphp
                                        <span class="badge fix_badge p-2 px-3
                                            {{ $proposal->status==0?'bg-primary':($proposal->status==1?'bg-info':($proposal->status==2?'bg-secondary':($proposal->status==3?'bg-warning':'bg-danger'))) }}">
                                            {{ __($st) }}
                                        </span>
                                    </td>

                                    @if (Gate::check('edit proposal') || Gate::check('delete proposal') || Gate::check('show proposal'))
                                      <td class="Action text-end border border-[#E5E5E5]">
  <button class="w-100 text-gray-400 hover:text-gray-600 cursor-pointer" type="button"
          data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
    <i class="ti ti-dots-vertical"></i>
  </button>
  <div class="dropdown-menu dropdown-menu-end mt-0 w-[190px] bg-white border rounded-md shadow-lg text-sm p-0">

    @if ($proposal->is_convert == 0)
      @if ($proposal->converted_invoice_id == 0)
        @can('convert retainer proposal')
          <li>
            {!! Form::open([
                'method' => 'get',
                'route' => ['proposal.convert', $proposal->id],
                'id' => 'proposal-form-' . $proposal->id,
            ]) !!}
            <a href="#"
               class="dropdown-item flex items-center text-[#323232] gap-2 w-full px-4 py-2 text-left hover:bg-[#007C3812] text-sm bs-pass-para"
               data-bs-toggle="tooltip"
               title="{{ __('Convert into Retainer') }}"
               data-original-title="{{ __('Convert to Retainer') }}"
               data-confirm="{{ __('You want to confirm convert to invoice. Press Yes to continue or Cancel to go back') }}"
               data-confirm-yes="document.getElementById('proposal-form-{{ $proposal->id }}').submit();">
              <i class="ti ti-exchange"></i>
              <span>{{ __('Convert to Retainer') }}</span>
            </a>
            {!! Form::close() !!}
          </li>
        @endcan
      @endif
    @else
      @if ($proposal->converted_invoice_id == 0)
        @can('convert retainer proposal')
          <li>
            <a href="{{ route('retainer.show', \Crypt::encrypt($proposal->converted_retainer_id)) }}"
               class="dropdown-item flex items-center text-[#323232] gap-2 w-full px-4 py-2 text-left hover:bg-[#007C3812] text-sm"
               data-bs-toggle="tooltip"
               title="{{ __('Already convert to Retainer') }}">
              <i class="ti ti-file-invoice"></i>
              <span>{{ __('View Retainer') }}</span>
            </a>
          </li>
        @endcan
      @endif
    @endif

    @if ($proposal->converted_invoice_id == 0)
      @if ($proposal->is_convert == 0)
        @can('convert invoice proposal')
          <li>
            {!! Form::open([
                'method' => 'get',
                'route' => ['proposal.convertinvoice', $proposal->id],
                'id'    => 'proposal-form-' . $proposal->id,
            ]) !!}
            <a href="#"
               class="dropdown-item flex items-center text-[#323232] gap-2 w-full px-4 py-2 text-left hover:bg-[#007C3812] text-sm bs-pass-para"
               data-bs-toggle="tooltip"
               title="{{ __('Convert into Invoice') }}"
               data-confirm="{{ __('You want to confirm convert to invoice. Press Yes to continue or Cancel to go back') }}"
               data-confirm-yes="document.getElementById('proposal-form-{{ $proposal->id }}').submit();">
              <i class="ti ti-exchange"></i>
              <span>{{ __('Convert to Invoice') }}</span>
            </a>
            {!! Form::close() !!}
          </li>
        @endcan
      @endif
    @else
      @can('show invoice')
        @if (\Auth::guard('customer')->check())
          <li>
            <a href="{{ route('customer.invoice.show', \Crypt::encrypt($proposal->converted_invoice_id)) }}"
               class="dropdown-item flex items-center text-[#323232] gap-2 w-full px-4 py-2 text-left hover:bg-[#007C3812] text-sm"
               data-bs-toggle="tooltip"
               title="{{ __('Already convert to Invoice') }}">
              <i class="ti ti-file-invoice"></i>
              <span>{{ __('View Invoice') }}</span>
            </a>
          </li>
        @else
          <li>
            <a href="{{ route('invoice.show', \Crypt::encrypt($proposal->converted_invoice_id)) }}"
               class="dropdown-item flex items-center text-[#323232] gap-2 w-full px-4 py-2 text-left hover:bg-[#007C3812] text-sm"
               data-bs-toggle="tooltip"
               title="{{ __('Already convert to Invoice') }}">
              <i class="ti ti-file-invoice"></i>
              <span>{{ __('View Invoice') }}</span>
            </a>
          </li>
        @endif
      @endcan
    @endif

    @can('duplicate proposal')
      <li>
        {!! Form::open([
            'method' => 'get',
            'route'  => ['proposal.duplicate', $proposal->id],
            'id'     => 'duplicate-form-' . $proposal->id,
        ]) !!}
        <a href="#"
           class="dropdown-item flex items-center text-[#323232] gap-2 w-full px-4 py-2 text-left hover:bg-[#007C3812] text-sm bs-pass-para"
           data-bs-toggle="tooltip" title="{{ __('Duplicate') }}"
           data-confirm="{{ __('You want to confirm duplicate this invoice. Press Yes to continue or Cancel to go back') }}"
           data-confirm-yes="document.getElementById('duplicate-form-{{ $proposal->id }}').submit();">
          <i class="ti ti-copy"></i>
          <span>{{ __('Duplicate') }}</span>
        </a>
        {!! Form::close() !!}
      </li>
    @endcan

    @can('show proposal')
      @if (\Auth::guard('customer')->check())
        <li>
          <a href="{{ route('customer.proposal.show', \Crypt::encrypt($proposal->id)) }}"
             class="dropdown-item flex items-center text-[#323232] gap-2 w-full px-4 py-2 text-left hover:bg-[#007C3812] text-sm"
             data-bs-toggle="tooltip" title="{{ __('Show') }}">
            <i class="ti ti-eye"></i>
            <span>{{ __('Show') }}</span>
          </a>
        </li>
      @else
        <li>
          <a href="{{ route('proposal.show', \Crypt::encrypt($proposal->id)) }}"
             class="dropdown-item flex items-center text-[#323232] gap-2 w-full px-4 py-2 text-left hover:bg-[#007C3812] text-sm"
             data-bs-toggle="tooltip" title="{{ __('Show') }}">
            <i class="ti ti-eye"></i>
            <span>{{ __('Show') }}</span>
          </a>
        </li>
      @endif
    @endcan

    @can('edit proposal')
      <li>
        <a href="{{ route('proposal.edit', \Crypt::encrypt($proposal->id)) }}"
           class="dropdown-item flex items-center text-[#323232] gap-2 w-full px-4 py-2 text-left hover:bg-[#007C3812] text-sm"
           data-bs-toggle="tooltip" title="{{ __('Edit') }}">
          <i class="ti ti-pencil"></i>
          <span>{{ __('Edit') }}</span>
        </a>
      </li>
    @endcan

    @can('delete proposal')
      <li>
        {!! Form::open([
            'method' => 'DELETE',
            'route'  => ['proposal.destroy', $proposal->id],
            'id'     => 'delete-form-' . $proposal->id,
        ]) !!}
        <a href="#"
           class="dropdown-item flex items-center text-[#323232] gap-2 w-full px-4 py-2 text-left hover:bg-[#007C3812] text-sm bs-pass-para"
           data-bs-toggle="tooltip" title="{{ __('Delete') }}"
           data-confirm="{{ __('Are You Sure?') . '|' . __('This action can not be undone. Do you want to continue?') }}"
           data-confirm-yes="document.getElementById('delete-form-{{ $proposal->id }}').submit();">
          <i class="ti ti-trash"></i>
          <span>{{ __('Delete') }}</span>
        </a>
        {!! Form::close() !!}
      </li>
    @endcan

  </div>
</td>

                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
@endsection

@push('script-page')
<script>
  $(document).on('click', 'input[type=checkbox], label.mcheck, .dropdown-menu, [data-bs-toggle="dropdown"]', function(e){ e.stopPropagation(); });

  (function(){
    const $table = $('#proposals-table');
    const tbody = $table.find('tbody').get(0);
    if (tbody && 'MutationObserver' in window) {
      new MutationObserver(function(){ setTimeout(function(){ $(document).trigger('proposals-table-updated'); }, 0); })
        .observe(tbody, {childList:true});
    }
  })();
</script>
@endpush
