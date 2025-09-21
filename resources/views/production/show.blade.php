@extends('layouts.admin')
@section('page-title') {{ __('Production Order') }} — {{ $job->code }} @endsection

@section('breadcrumb')
  <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
  <li class="breadcrumb-item"><a href="{{ route('production.index') }}">{{ __('Production') }}</a></li>
  <li class="breadcrumb-item">{{ $job->code }}</li>
@endsection

@section('action-btn')
  <div class="flex items-center gap-2 mt-2 sm:mt-0">
    @if($job->status === 'draft')
      <a href="{{ route('production.edit', $job->id) }}"
         class="flex items-center gap-2 bg-[#007C38] text-white px-4 py-2 rounded-[6px] text-[14px] font-[500] hover:bg-[#005f2a] transition-all duration-200 shadow-sm min-w-fit">
        <i class="ti ti-pencil"></i>{{ __('Edit Draft') }}
      </a>
      {!! Form::open(['route'=>['production.transition', $job->id],'method'=>'POST']) !!}
        {{ Form::hidden('to','in_process') }}
        <button class="flex items-center gap-2 bg-[#F59E0B] text-white px-4 py-2 rounded-[6px] text-[14px] font-[500] hover:bg-[#D97706] transition-all duration-200 shadow-sm min-w-fit">
          <i class="ti ti-player-play"></i>{{ __('Start') }}
        </button>
      {!! Form::close() !!}
    @endif

    @if($job->status === 'in_process')
      {!! Form::open(['route'=>['production.transition', $job->id],'method'=>'POST']) !!}
        {{ Form::hidden('to','finished') }}
        <button class="flex items-center gap-2 bg-[#007C38] text-white px-4 py-2 rounded-[6px] text-[14px] font-[500] hover:bg-[#005f2a] transition-all duration-200 shadow-sm min-w-fit">
          <i class="ti ti-check"></i>{{ __('Mark Finished') }}
        </button>
      {!! Form::close() !!}
    @endif

    @if(in_array($job->status, ['draft','in_process']))
      {!! Form::open(['route'=>['production.transition', $job->id],'method'=>'POST']) !!}
        {{ Form::hidden('to','cancelled') }}
        <button class="flex items-center gap-2 bg-red-600 text-white px-4 py-2 rounded-[6px] text-[14px] font-[500] hover:bg-red-700 transition-all duration-200 shadow-sm min-w-fit">
          <i class="ti ti-x"></i>{{ __('Cancel') }}
        </button>
      {!! Form::close() !!}
    @endif
  </div>
@endsection

@section('content')
<div class="row">
  <div class="col-12">
    <div class="bg-white rounded-[8px] border border-[#E5E5E5] shadow-sm overflow-hidden mb-6">
      <div class="p-4">
        <div class="row g-3">
          <div class="col-md-7">
            <h3 class="text-[#111827] text-lg font-semibold mb-1">{{ $job->code }}</h3>
            <div class="text-[#6B7280]">
              {{ data_get($job, 'bom.code', '') }} — {{ data_get($job, 'bom.name', '') }}
            </div>

            <div class="mt-2">
              <span class="text-xs text-[#6B7280]">{{ __('Status') }}:</span>
              @if($job->status==='draft')
                <span class="badge fix_badges bg-secondary p-2 px-3">{{ __('Draft (Not started)') }}</span>
              @elseif($job->status==='in_process')
                <span class="badge fix_badges bg-warning p-2 px-3">{{ __('In Process') }}</span>
              @elseif($job->status==='finished')
                <span class="badge fix_badges bg-primary p-2 px-3">{{ __('Finished') }}</span>
              @else
                <span class="badge fix_badges bg-danger p-2 px-3">{{ __('Cancelled') }}</span>
              @endif
            </div>

            @if(!empty($job->notes))
              <p class="mt-3 text-[#374151]">{{ $job->notes }}</p>
            @endif
          </div>
          <div class="col-md-5">
            <div class="grid grid-cols-2 gap-2">
              <div class="p-3 bg-[#F9FAFB] rounded border">
                <div class="text-xs text-[#6B7280]">
                  {{ $job->status==='draft' ? __('Planned Date') : __('Started / Planned Date') }}
                </div>
                <div class="font-[600]">
                  @if($job->status==='in_process' || $job->status==='finished')
                    {{ \Auth::user()->dateFormat($job->started_at ?? $job->planned_date ?? $job->created_at) }}
                  @else
                    {{ \Auth::user()->dateFormat($job->planned_date ?? $job->created_at) }}
                  @endif
                </div>
              </div>
              <div class="p-3 bg-[#F9FAFB] rounded border">
                <div class="text-xs text-[#6B7280]">
                  {{ $job->status==='draft' ? __('Estimated Batch Cost') : __('Total Cost') }}
                </div>
                <div class="font-[600]">{{ \Auth::user()->priceFormat($job->total_cost ?? 0) }}</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- Materials --}}
    <div class="bg-white rounded-[8px] border border-[#E5E5E5] shadow-sm overflow-hidden mb-6">
      <div class="heading-cstm-form">
        <h6 class="mb-0 flex items-center gap-2">
          <i class="ti ti-box"></i>
          @if($job->status==='draft')
            {{ __('Raw Materials Required (Planned)') }}
          @else
            {{ __('Raw Materials (Issued)') }}
          @endif
        </h6>
      </div>
      <div class="table-responsive table-new-design bg-white p-4">
        <table class="table datatable border border-[#E5E5E5] rounded-[8px] dataTable-table">
          <thead>
            <tr>
              <th class="px-4 py-1 border bg-[#F6F6F6] font-[600] text-[12px]">{{ __('Item') }}</th>
              <th class="px-4 py-1 border bg-[#F6F6F6] font-[600] text-[12px] text-end">{{ $job->status==='draft' ? __('Planned Qty') : __('Qty') }}</th>
              <th class="px-4 py-1 border bg-[#F6F6F6] font-[600] text-[12px]">{{ __('Unit') }}</th>
              <th class="px-4 py-1 border bg-[#F6F6F6] font-[600] text-[12px] text-end">{{ __('Valuation') }}</th>
            </tr>
          </thead>
          <tbody>
            @forelse(($job->components ?? []) as $c)
              <tr>
                <td class="px-3 py-2 border">{{ data_get($c, 'product.name', '-') }}</td>
                <td class="px-3 py-2 border text-end">
                  {{ rtrim(rtrim(number_format((float)($c->qty ?? 0), 4, '.', ''), '0'), '.') }}
                </td>
                <td class="px-3 py-2 border">{{ data_get($c, 'product.unit.name', '-') }}</td>
                <td class="px-3 py-2 border text-end">{{ \Auth::user()->priceFormat((float)($c->line_cost ?? 0)) }}</td>
              </tr>
            @empty
              <tr>
                <td colspan="4" class="px-3 py-3 text-center text-gray-500">
                  {{ __('No lines to display.') }}
                </td>
              </tr>
            @endforelse
          </tbody>
          <tfoot>
            <tr>
              <td colspan="3" class="px-3 py-2 border text-end font-[600]">{{ __('Components Total') }}</td>
              <td class="px-3 py-2 border text-end font-[700]">{{ \Auth::user()->priceFormat((float)($job->components_total ?? 0)) }}</td>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>

    {{-- Additional costs --}}
    @php
      $labor = $job->labor_cost ?? null; $overhead = $job->overhead_cost ?? null; $other = $job->other_cost ?? null;
      $addl = (float)($job->manufacturing_cost ?? 0);
    @endphp
    @if($addl > 0)
    <div class="bg-white rounded-[8px] border border-[#E5E5E5] shadow-sm overflow-hidden mb-6">
      <div class="heading-cstm-form">
        <h6 class="mb-0 flex items-center gap-2"><i class="ti ti-currency-dollar"></i>
          {{ $job->status==='draft' ? __('Estimated Additional Costs') : __('Additional Costs') }}
        </h6>
      </div>
      <div class="table-responsive table-new-design bg-white p-4">
        <table class="table datatable border border-[#E5E5E5] rounded-[8px] dataTable-table">
          <tbody>
            @if(!is_null($labor))
              <tr>
                <td class="px-3 py-2 border">{{ __('Labor / Making') }}</td>
                <td class="px-3 py-2 border text-end">{{ \Auth::user()->priceFormat((float)$labor) }}</td>
              </tr>
            @endif
            @if(!is_null($overhead))
              <tr>
                <td class="px-3 py-2 border">{{ __('Overhead') }}</td>
                <td class="px-3 py-2 border text-end">{{ \Auth::user()->priceFormat((float)$overhead) }}</td>
              </tr>
            @endif
            @if(!is_null($other))
              <tr>
                <td class="px-3 py-2 border">{{ __('Other') }}</td>
                <td class="px-3 py-2 border text-end">{{ \Auth::user()->priceFormat((float)$other) }}</td>
              </tr>
            @endif
          </tbody>
          <tfoot>
            <tr>
              <td class="px-3 py-2 border text-end font-[600]">{{ $job->status==='draft' ? __('Estimated Additional Total') : __('Additional Total') }}</td>
              <td class="px-3 py-2 border text-end font-[700]">{{ \Auth::user()->priceFormat($addl) }}</td>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>
    @endif

    {{-- Outputs --}}
    <div class="bg-white rounded-[8px] border border-[#E5E5E5] shadow-sm overflow-hidden">
      <div class="heading-cstm-form">
        <h6 class="mb-0 flex items-center gap-2">
          <i class="ti ti-package"></i>
          @if($job->status==='draft')
            {{ __('Planned Outputs') }}
          @elseif($job->status==='in_process')
            {{ __('Planned Outputs (Pending Receipt)') }}
          @else
            {{ __('Finished Products (Received)') }}
          @endif
        </h6>
      </div>
      <div class="table-responsive table-new-design bg-white p-4">
        <table class="table datatable border border-[#E5E5E5] rounded-[8px] dataTable-table">
          <thead>
            <tr>
              <th class="px-4 py-1 border bg-[#F6F6F6] font-[600] text-[12px]">{{ __('Product') }}</th>
              <th class="px-4 py-1 border bg-[#F6F6F6] font-[600] text-[12px] text-end">{{ $job->status==='draft' ? __('Planned Qty') : __('Qty') }}</th>
              <th class="px-4 py-1 border bg-[#F6F6F6] font-[600] text-[12px]">{{ __('Unit') }}</th>
              <th class="px-4 py-1 border bg-[#F6F6F6] font-[600] text-[12px] text-end">{{ __('Allocated Cost') }}</th>
              <th class="px-4 py-1 border bg-[#F6F6F6] font-[600] text-[12px] text-end">{{ __('Unit Cost') }}</th>
            </tr>
          </thead>
          <tbody>
            @forelse(($job->outputs ?? []) as $o)
              @php
                $qty   = (float)($o->qty ?? 0);
                $alloc = (float)($o->allocated_cost ?? 0);
                $ucost = $qty > 0 ? $alloc / $qty : 0;
              @endphp
              <tr>
                <td class="px-3 py-2 border">{{ data_get($o, 'product.name', '-') }}</td>
                <td class="px-3 py-2 border text-end">{{ rtrim(rtrim(number_format($qty,4,'.',''), '0'),'.') }}</td>
                <td class="px-3 py-2 border">{{ data_get($o, 'product.unit.name', '-') }}</td>
                <td class="px-3 py-2 border text-end">{{ \Auth::user()->priceFormat($alloc) }}</td>
                <td class="px-3 py-2 border text-end">{{ \Auth::user()->priceFormat($ucost) }}</td>
              </tr>
            @empty
              <tr>
                <td colspan="5" class="px-3 py-3 text-center text-gray-500">
                  {{ __('No lines to display.') }}
                </td>
              </tr>
            @endforelse
          </tbody>
          <tfoot>
            <tr>
              <td colspan="3" class="px-3 py-2 border text-end font-[600]">
                {{ $job->status==='draft' ? __('Estimated Batch Cost') : __('Batch Cost') }}
              </td>
              <td class="px-3 py-2 border text-end font-[700]">{{ \Auth::user()->priceFormat((float)($job->total_cost ?? 0)) }}</td>
              <td class="px-3 py-2 border"></td>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>

  </div>
</div>
@endsection
