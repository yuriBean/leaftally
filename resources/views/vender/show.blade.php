@extends('layouts.admin')
@push('script-page')
@endpush
@section('page-title')
    {{ __('Manage Vendor-Detail') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('vender.index') }}">{{ __('Vendor') }}</a></li>
    <li class="breadcrumb-item">{{ $vendor['name'] }}</li>
@endsection

@section('action-btn')
    <div class="d-flex">
        @can('create bill')
            <a href="{{ route('bill.create', $vendor->id) }}" class="btn btn-sm btn-primary me-2"
            title="{{ __('Create Bill') }}"
                data-bs-toggle="tooltip">
                <i class="ti ti-plus"> </i>{{ __('Create Bill') }}
            </a>
        @endcan
        <a href="{{ route('vender.statement', $vendor['id']) }}" class="btn btn-sm btn-primary me-2"
        title="{{ __('Statement') }}"
                data-bs-toggle="tooltip">
            {{ __('Statement') }}
        </a>
        @can('edit vender')
            <a href="#" class="btn btn-sm btn-info me-2" data-size="xl"
                data-url="{{ route('vender.edit', $vendor['id']) }}" data-ajax-popup="true" title="{{ __('Edit') }}"
                data-bs-toggle="tooltip" data-original-title="{{ __('Edit') }}">
                <i class="ti ti-pencil"></i>
            </a>
        @endcan
        @can('delete vender')
            {!! Form::open([
                'method' => 'DELETE',
                'route' => ['vender.destroy', $vendor['id']],
                'class' => 'delete-form-btn',
                'id' => 'delete-form-' . $vendor['id'],
            ]) !!}
            <a href="#" class="btn btn-sm btn-danger bs-pass-para" data-bs-toggle="tooltip" title="{{ __('Delete') }}"
                data-original-title="{{ __('Delete') }}"
                data-confirm="{{ __('Are You Sure?') . '|' . __('This action can not be undone. Do you want to continue?') }}"
                data-confirm-yes="document.getElementById('delete-form-{{ $vendor['id'] }}').submit();">
                <i class="ti ti-trash text-white"></i>
            </a>
            {!! Form::close() !!}
        @endcan
    </div>
@endsection

@section('content')
<div class="customer-text-sz">
    <div class="mt-[46px] grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6 ">
        <div class="card mb-0 border text-[14px] leading-[24px] border-[#E5E5E5] rounded-[8px] px-[18px] py-[11px] text-[#323232]">
            <div class="pb-0 customer-detail-box customer_card">
                <div class="card-body py-2 p-0">
                    <h5 class="h4 font-weight-400 mb-3 card-title">{{ __('Vendor Info') }}</h5>
                        <p class="card-text mb-0">{{ $vendor->name }}</p>
                        <p class="card-text mb-0">{{ $vendor->email }}</p>
                        <p class="card-text mb-0">{{ $vendor->contact }}</p>
                        <p class="card-text mb-0">{{ $vendor->tax_number }}</p>
                </div>
            </div>
        </div>
        <div class="card mb-0 border text-[14px] leading-[24px] border-[#E5E5E5] rounded-[8px] px-[18px] py-[11px] text-[#323232]">
            <div class="pb-0 customer-detail-box customer_card">
                <div class="card-body py-2 p-0">
                    <h5 class="h4 font-weight-400 mb-3 card-title">{{ __('Billing Info') }}</h5>
                    <p class="card-text mb-0">{{ $vendor->billing_name }}</p>
                    <p class="card-text mb-0">{{ $vendor->billing_address }}</p>
                    <p class="card-text mb-0">
                        {{ $vendor->billing_city . ', ' . $vendor->billing_state . ', ' . $vendor->billing_zip }}</p>
                    <p class="card-text mb-0">{{ $vendor->billing_country }}</p>
                    <p class="card-text mb-0">{{ $vendor->billing_phone }}</p>
                </div>
            </div>
        </div>
        <div class="card mb-0 border text-[14px] leading-[24px] border-[#E5E5E5] rounded-[8px] px-[18px] py-[11px] text-[#323232]">
            <div class="pb-0 customer-detail-box customer_card">
                <div class="card-body py-2 p-0">
                    <h5 class="h4 font-weight-400 mb-3 card-title">{{ __('Shipping Info') }}</h5>
                    <p class="card-text mb-0">{{ $vendor->shipping_name }}</p>
                    <p class="card-text mb-0">{{ $vendor->shipping_address }}</p>
                    <p class="card-text mb-0">
                        {{ $vendor->shipping_city . ', ' . $vendor->shipping_state . ', ' . $vendor->shipping_zip }}</p>
                    <p class="card-text mb-0">{{ $vendor->shipping_country }}</p>
                    <p class="card-text mb-0">{{ $vendor->shipping_phone }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12 text-[14px] leading-[24px] px-[16px] text-[#323232]">
            <div class="card px-[18px] py-[11px] border border-[#E5E5E5] rounded-[8px]">
                <div class="card-body py-2 p-0">
                    <h5 class="h4 font-weight-400 mb-3 card-title">{{ __('Company Info') }}</h5>
                    <div class="row">
                        @php
                            $totalBillSum = $vendor->vendorTotalBillSum($vendor['id']);
                            $totalBill = $vendor->vendorTotalBill($vendor['id']);
                            $averageSale = $totalBillSum != 0 ? $totalBillSum / $totalBill : 0;
                        @endphp
                        <div class="col-md-3 col-sm-6">
                            <div class="customer-detail">
                                <p class="card-text mb-0">{{ __('Vendor Id') }}</p>
                                <h6 class="report-text mb-3">{{ \Auth::user()->venderNumberFormat($vendor->vender_id) }}
                                </h6>
                                <p class="card-text mb-0">{{ __('Total Sum of Bills') }}</p>
                                <h6 class="report-text mb-0">{{ \Auth::user()->priceFormat($totalBillSum) }}</h6>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="customer-detail">
                                <p class="card-text mb-0">{{ __('Date of Creation') }}</p>
                                <h6 class="report-text mb-3">{{ \Auth::user()->dateFormat($vendor->created_at) }}</h6>
                                <p class="card-text mb-0">{{ __('Quantity of Bills') }}</p>
                                <h6 class="report-text mb-0">{{ $totalBill }}</h6>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="customer-detail">
                                <p class="card-text mb-0">{{ __('Balance') }}</p>
                                <h6 class="report-text mb-3">{{ \Auth::user()->priceFormat($vendor->balance) }}</h6>
                                <p class="card-text mb-0">{{ __('Average Sales') }}</p>
                                <h6 class="report-text mb-0">{{ \Auth::user()->priceFormat($averageSale) }}</h6>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="customer-detail">
                                <p class="card-text mb-0">{{ __('Overdue') }}</p>
                                <h6 class="report-text mb-3">
                                    {{ \Auth::user()->priceFormat($vendor->vendorOverdue($vendor->id)) }}</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
               <div class="card">
                    <div class="card-body table-border-style">
                     <h5 class="h4 font-weight-400 mb-4">{{ __('Bills') }}</h5>
                    <div class="table-responsive table-new-design bg-white p-4">
                        <table class="table datatable">
                            <thead class="bg-[#F6F6F6] text-[#323232] font-600 uppercase text-[12px] leading-[24px]">
                                <tr>
                                    <th class="px-4 py-2 border border-[#E5E5E5] text-[12px] font-[600]">{{ __('Bill') }}</th>
                                    <th class="px-4 py-2 border border-[#E5E5E5] text-[12px] font-[600]">{{ __('Bill Date') }}</th>
                                    <th class="px-4 py-2 border border-[#E5E5E5] text-[12px] font-[600]">{{ __('Due Date') }}</th>
                                    <th class="px-4 py-2 border border-[#E5E5E5] text-[12px] font-[600]">{{ __('Amount Due') }}</th>
                                    <th class="px-4 py-2 border border-[#E5E5E5] text-[12px] font-[600]">{{ __('Status') }}</th>
                                    @if (Gate::check('edit bill') || Gate::check('delete bill') || Gate::check('show bill'))
                                        <th width="10%"> {{ __('Action') }}</th>
                                    @endif
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($vendor->vendorBill($vendor->id) as $bill)
                                    <tr class="font-style">
                                        <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700 Id">
                                            @if (\Auth::guard('vender')->check())
                                                <a href="{{ route('vender.bill.show', \Crypt::encrypt($bill->id)) }}"
                                                    class="border border-[#137051] leading-[24px] text-[#137051] rounded-[4px] text-[12px] font-[500] px-5">{{ AUth::user()->billNumberFormat($bill->bill_id) }}
                                                </a>
                                            @else
                                                <a href="{{ route('bill.show', \Crypt::encrypt($bill->id)) }}"
                                                    class="border border-[#137051] leading-[24px] text-[#137051] rounded-[4px] text-[12px] font-[500] px-5">{{ AUth::user()->billNumberFormat($bill->bill_id) }}
                                                </a>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700 ">{{ Auth::user()->dateFormat($bill->bill_date) }}</td>
                                        <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700 ">
                                            @if ($bill->due_date < date('Y-m-d'))
                                                <p class="text-[#FFA21D] bg-[#FFA21D29] border rounded-full text-[12px] font-[500] leading-[24px] border-[#FFA21D29] px-3 text-center"> {{ \Auth::user()->dateFormat($bill->due_date) }}
                                                </p>
                                            @else
                                                {{ \Auth::user()->dateFormat($bill->due_date) }}
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700 ">{{ \Auth::user()->priceFormat($bill->getDue()) }}</td>
                                        <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700 ">
                                            @if ($bill->status == 0)
                                                <span
                                                    class="badge bg-primary p-2 px-3 fix_badge">{{ __(\App\Models\Invoice::$statues[$bill->status]) }}</span>
                                            @elseif($bill->status == 1)
                                                <span
                                                    class="badge bg-warning p-2 px-3  fix_badge">{{ __(\App\Models\Invoice::$statues[$bill->status]) }}</span>
                                            @elseif($bill->status == 2)
                                                <span
                                                    class="badge bg-danger p-2 px-3  fix_badge">{{ __(\App\Models\Invoice::$statues[$bill->status]) }}</span>
                                            @elseif($bill->status == 3)
                                                <span
                                                    class="badge bg-info p-2 px-3  fix_badge">{{ __(\App\Models\Invoice::$statues[$bill->status]) }}</span>
                                            @elseif($bill->status == 4)
                                                <span
                                                    class="badge bg-success p-2 px-3  fix_badge">{{ __(\App\Models\Invoice::$statues[$bill->status]) }}</span>
                                            @endif
                                        </td>
                                        @if (Gate::check('edit bill') || Gate::check('delete bill') || Gate::check('show bill'))
                                            <td class="Action">
                                                <span class="d-flex">
                                                    @can('duplicate bill')
                                                      
                                                        <div class="action-btn me-2">

                                                            {!! Form::open([
                                                                'method' => 'get',
                                                                'route' => ['bill.duplicate', $bill->id],
                                                                'id' => 'bill-duplicate-form-' . $bill->id,
                                                            ]) !!}
                                                            <a href="#"
                                                                class="mx-3 btn btn-sm align-items-center bs-pass-para bg-secondary"
                                                                data-bs-toggle="tooltip" title="{{ __('Duplicate Bill') }}"
                                                                data-original-title="{{ __('Duplicate') }}"
                                                                data-confirm="{{ __('You want to confirm this action. Press Yes to continue or Cancel to go back') }}"
                                                                data-confirm-yes="document.getElementById('bill-duplicate-form-{{ $bill->id }}').submit();">
                                                                <i class="ti ti-copy text-white text-white"></i>
                                                            </a>
                                                            {!! Form::close() !!}

                                                        </div>
                                                    @endcan
                                                    @can('show bill')
                                                        @if (\Auth::guard('vender')->check())
                                                            <div class="action-btn bg-info me-2">
                                                                <a href="{{ route('vender.bill.show', \Crypt::encrypt($bill->id)) }}"
                                                                    class="mx-3 btn btn-sm  align-items-center bg-warning"
                                                                    data-bs-toggle="tooltip" title="{{ __('Show') }}"
                                                                    data-original-title="{{ __('Detail') }}">
                                                                    <i class="ti ti-eye text-white text-white"></i>
                                                                </a>
                                                            </div>
                                                        @else
                                                            <div class="action-btn me-2">
                                                                <a href="{{ route('bill.show', \Crypt::encrypt($bill->id)) }}"
                                                                    class="mx-3 btn btn-sm  align-items-center bg-warning"
                                                                    data-bs-toggle="tooltip" title="{{ __('Show') }}"
                                                                    data-original-title="{{ __('Detail') }}">
                                                                    <i class="ti ti-eye text-white text-white"></i>
                                                                </a>
                                                            </div>
                                                        @endif
                                                    @endcan
                                                    @can('edit bill')
                                                        <div class="action-btn me-2">
                                                            <a href="{{ route('bill.edit', \Crypt::encrypt($bill->id)) }}"
                                                                class="mx-3 btn btn-sm  align-items-center btn-info"
                                                                data-bs-toggle="tooltip" title="{{ __('Edit') }}"
                                                                data-original-title="{{ __('Edit') }}">
                                                                <i class="ti ti-pencil text-white"></i>
                                                            </a>
                                                        </div>
                                                    @endcan
                                                    @can('delete bill')
                                                        <div class="action-btn">
                                                            {!! Form::open([
                                                                'method' => 'DELETE',
                                                                'route' => ['bill.destroy', $bill->id],
                                                                'id' => 'delete-form-' . $bill->id,
                                                            ]) !!}

                                                            <a href="#"
                                                                class="mx-3 btn btn-sm  align-items-center bs-pass-para bg-danger"
                                                                data-bs-toggle="tooltip"
                                                                data-original-title="{{ __('Delete') }}"
                                                                title="{{ __('Delete') }}"
                                                                data-confirm="{{ __('Are You Sure?') . '|' . __('This action can not be undone. Do you want to continue?') }}"
                                                                data-confirm-yes="document.getElementById('delete-form-{{ $bill->id }}').submit();">
                                                                <i class="ti ti-trash text-white text-white"></i>
                                                            </a>
                                                            {!! Form::close() !!}
                                                        </div>
                                                    @endcan
                                                </span>
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
    </div>
</div>
@endsection
