@extends('layouts.admin')
@section('page-title')
    {{ __('Plan Request') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('Plan Request') }}</li>
@endsection
@section('title')
    <div class="d-inline-block">
        <h5 class="h4 d-inline-block font-weight-400 mb-0">{{ __('Plan Request') }}</h5>
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">

            <div class="bg-white border border-[#E5E5E5] rounded-[8px] p-3">
                <div class="table-responsive">
                    <table class="table datatable">

                        <thead>
                            <tr>
                                <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600]">{{ __('Name') }}
                                </th>
                                <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600]">{{ __('Plan name') }}
                                </th>
                                <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600]">
                                    {{ __('Total users') }}</th>
                                <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600]">
                                    {{ __('Total customers') }}</th>
                                <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600]">
                                    {{ __('Total vendors') }}</th>
                                <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600]">{{ __('Duration') }}
                                </th>
                                <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600]">{{ __('Date') }}
                                </th>
                                <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600]">{{ __('Action') }}
                                </th>

                            </tr>
                        </thead>
                        <tbody>
                            @if ($plan_requests->count() > 0)
                                @foreach ($plan_requests as $prequest)
                                    <tr>
                                        <td class="px-4 py-3 border border-[#E5E5E5] text-[#323232]">
                                            <div class="font-style font-weight-bold">{{ $prequest->user->name }}</div>
                                        </td>
                                        <td class="px-4 py-3 border border-[#E5E5E5] text-[#323232]">
                                            <div class="font-style font-weight-bold">{{ $prequest->plan->name }}</div>
                                        </td>
                                        <td class="px-4 py-3 border border-[#E5E5E5] text-[#323232]">

                                            <div class="font-weight-bold">{{ $prequest->plan->max_users }}</div>

                                        </td>
                                        <td class="px-4 py-3 border border-[#E5E5E5] text-[#323232]">
                                            <div class="font-weight-bold">{{ $prequest->plan->max_customers }}</div>

                                        </td>
                                        <td class="px-4 py-3 border border-[#E5E5E5] text-[#323232]">
                                            <div class="font-weight-bold">{{ $prequest->plan->max_venders }}</div>

                                        </td>
                                        <td class="px-4 py-3 border border-[#E5E5E5] text-[#323232]">
                                            <div class="font-style font-weight-bold">
                                                {{ __('One ') . $prequest->plan->duration }}
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 border border-[#E5E5E5] text-[#323232]">
                                            {{ App\Models\Utility::getDateFormated($prequest->created_at, true) }}</td>
                                        <td class="relative px-4 py-3 border border-[#E5E5E5] text-[#323232]">

                                            <button
                                                class="absolute top-3 right-3 text-gray-400 hover:text-gray-600 cursor-pointer"
                                                type="button" data-bs-toggle="dropdown" aria-haspopup="true"
                                                aria-expanded="false">
                                                <i class="ti ti-dots-vertical"></i>
                                            </button>
                                            <div
                                                class="dropdown-menu dropdown-menu-end mt-0 w-[190px] bg-white border rounded-md shadow-lg text-sm p-0">
                                                <!-- <a href="{{ route('response.request', [$prequest->id, 1]) }}"
                                                            class="btn btn-success btn-sm me-2"
                                                            data-bs-toggle="tooltip"
                                                            title="{{ __('Approve') }}"
                                                             >
                                                            <i class="ti ti-check"></i>
                                                        </a> -->
                                                <a href="{{ route('response.request', [$prequest->id, 1]) }}"
                                                    class="dropdown-item flex text-[#323232] gap-2 w-full px-4 py-2 text-left hover:bg-[#007C3812]"
                                                    data-bs-toggle="tooltip" title="{{ __('Approve') }}">
                                                    <i class="ti ti-check"></i>
                                                    {{ __('Approve') }}
                                                </a>
                                                <a href="{{ route('response.request', [$prequest->id, 0]) }}"
                                                    class="dropdown-item bs-pass-para flex text-[#323232] gap-2 w-full px-4 py-2 text-left hover:bg-[#007C3812]"
                                                    data-bs-toggle="tooltip" title="{{ __('Cancel') }}">
                                                    <i class="ti ti-x"></i>
                                                    {{ __('Cancel') }}
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <th scope="col" colspan="7">
                                        <h6 class="text-center">{{ __('No Manually Plan Request Found.') }}</h6>
                                    </th>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
@endsection
