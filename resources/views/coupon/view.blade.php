@extends('layouts.admin')
@section('page-title')
    {{ __('Manage Coupon Details') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('coupons.index') }}">{{ __('Coupon') }}</a></li>
    <li class="breadcrumb-item">{{ __('Coupon Details') }}</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
                <div class="bg-white border border-[#E5E5E5] rounded-[8px] p-3">
                    <div class="table-responsive">
                        <table class="table datatable">
                            <thead>
                                <tr>
                                    <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600]"> {{ __('User') }}</th>
                                    <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600]"> {{ __('Date') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($userCoupons as $userCoupon)
                                    <tr class="font-style">
                                        <td class="px-4 py-3 border border-[#E5E5E5] text-[#323232]">{{ !empty($userCoupon->userDetail) ? $userCoupon->userDetail->name : '' }}</td>
                                        <td class="px-4 py-3 border border-[#E5E5E5] text-[#323232]">{{ $userCoupon->created_at }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
        </div>
    </div>
@endsection
