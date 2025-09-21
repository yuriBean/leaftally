@extends('layouts.admin')
@php
    $dir = asset(Storage::url('uploads/plan'));
    $admin = \App\Models\Utility::getAdminPaymentSetting();
@endphp
@section('page-title')
    {{ __('Manage Plan') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('Plan') }}</li>
@endsection
@section('action-btn')
    <div class="float-end mb-2">
        @can('create plan')
            <a href="#" data-url="{{ route('plans.create') }}" data-ajax-popup="true" data-bs-toggle="tooltip"
                title="{{ __('Create') }}" data-title="{{ __('Create New Plan') }}" class="btn btn-sm btn-primary"
                data-size="lg">
                <i class="ti ti-plus"></i>
                {{ __('Create Plan') }}
            </a>
        @endcan
    </div>
@endsection
@section('content')
    <div class="bg-white py-10 px-4">
    
        <div class="max-w-7xl grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach ($plans as $plan)
            <div class="card border-0 rounded-2xl shadow-md overflow-hidden my-3">
                <div class="h-1 w-full" style="background:#007C38;"></div>
    
                <div class="relative bg-white shadow-md overflow-hidden  p-6 flex flex-col border-0 rounded-2xl relative shadow-lg hover:shadow-xl transition-shadow duration-300">

                    @if ($plan->is_popular)
                        <span class="absolute top-0 right-0 bg-green-600 text-white text-sm font-medium px-4 py-2 rounded-bl-xl">
                            {{ __('Most Popular') }}
                        </span>
                    @endif

                    <h3 class="text-xl font-semibold">{{ $plan->name }}</h3>

                    <p class="text-5xl font-[600] mt-2 text-black">
                        {{ !empty($admin['currency_symbol']) ? $admin['currency_symbol'] : '$' }}{{ number_format($plan->price) }}
                        <span class="text-[#727272] text-[16px] font-[600] mt-1">/{{ __(\App\Models\Plan::$arrDuration[$plan->duration]) }}</span>
                    </p>

                    <p class="text-[#727272] text-[12px] font-[600] mt-6">{{ @$plan->description }}</p>

                    @if (\Auth::user()->type != 'super admin')
                        @if ($plan->price > 0 && \Auth::user()->trial_plan == 0 && \Auth::user()->plan != $plan->id && $plan->trial == 1)
                            <a href="{{ route('plan.trial', \Illuminate\Support\Facades\Crypt::encrypt($plan->id)) }}"
                                class="mt-6 bg-green-700 text-white text-center py-2 rounded-[8px] hover:bg-green-700">
                                {{ __('Start Free Trial') }}
                            </a>
                        @endif
                        @if ($plan->id != \Auth::user()->plan)
                            @if ($plan->price > 0)
                                <a href="{{ route('stripe', \Illuminate\Support\Facades\Crypt::encrypt($plan->id)) }}"
                                    class="mt-3 bg-green-700 text-white text-center py-2 rounded-[8px] hover:bg-green-700">
                                    {{ __('Buy Plan') }}
                                </a>
                            @endif
                        @endif
                        @if ($plan->id != \Auth::user()->plan)
                            @if (\Auth::user()->requested_plan != $plan->id)
                                <a href="{{ route('send.request', [\Illuminate\Support\Facades\Crypt::encrypt($plan->id)]) }}"
                                    class="mt-3 py-[6px] px-[10px] btn text-[#007C38] border-[#007C38] hover:bg-[#007C38] hover:text-white"
                                    data-title="{{ __('Send Request') }}" data-bs-toggle="tooltip"
                                    title="{{ __('Send Request') }}">
                                    <i class="ti ti-corner-up-right mr-1"></i> {{ __('Send Request') }}
                                </a>
                            @else
                                <a href="{{ route('request.cancel', \Auth::user()->id) }}"
                                    class="mt-3 btn bg-red-600 text-white p-2 rounded-lg hover:bg-red-700 flex items-center justify-center transition duration-200"
                                    data-title="{{ __('Cancel Request') }}" data-bs-toggle="tooltip"
                                    title="{{ __('Cancel Request') }}">
                                    <i class="ti ti-x mr-1"></i> {{ __('Cancel Request') }}
                                </a>
                            @endif
                        @endif
                    @endif

                    {{-- Company user active label --}}
                    @if (\Auth::user()->type == 'company' && \Auth::user()->plan == $plan->id)
                        <div class="absolute top-4 right-4 flex items-center bg-green-100 text-green-700 px-3 py-1 rounded-full text-sm font-medium">
                            <i class="fas fa-circle text-green-500 text-xs mr-2"></i>
                            <span>{{ __('Active') }}</span>
                        </div>
                    @endif

                    {{-- Super admin disable switch --}}
                    @if (\Auth::user()->type == 'super admin')
                        <div class="absolute top-4 right-8 flex items-center bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-sm font-medium">
                            <div class="form-check form-switch custom-switch-v1 flex items-center">
                                <input type="checkbox" name="plan_disable" class="form-check-input input-primary is_disable"
                                       value="1" data-id='{{ $plan->id }}' data-name="{{ __('plan') }}"
                                       {{ $plan->is_disable == 1 ? 'checked' : '' }}>
                                <label class="form-check-label text-blue-700" for="plan_disable">{{ __('Disable') }}</label>
                            </div>
                        </div>
                    @endif

                    {{-- Features & quotas --}}
                    <ul class="mt-6 space-y-2 text-gray-700 flex-grow pb-6">
                        <strong class="text-xl font-semibold mb-3 block">{{ __('Features') }}</strong>

                        @php
                            $rows = [
                                ['label' => __('User access management'),           'on' => (bool)$plan->user_access_management, 'q' => null],

                                ['label' => __('Invoice management'),               'on' => true,                                'q' => $plan->invoice_quota],

                                ['label' => __('Payroll management'),               'on' => (bool)$plan->payroll_enabled,        'q' => $plan->payroll_quota],

                                ['label' => __('Budgeting & forecasting'),          'on' => (bool)$plan->budgeting_enabled,      'q' => null],

                                ['label' => __('Product management'),               'on' => true,                                'q' => $plan->product_quota],
                                ['label' => __('Inventory (auto with Product)'),    'on' => true,                                'q' => null],

                                ['label' => __('Tax management'),                   'on' => (bool)$plan->tax_management_enabled, 'q' => null],


                                ['label' => __('Audit trail'),                      'on' => (bool)$plan->audit_trail_enabled,    'q' => null],

                                ['label' => __('Client management'),                'on' => true,                                'q' => $plan->client_quota],
                                ['label' => __('Vendor management'),                'on' => true,                                'q' => $plan->vendor_quota],

                                // NEW: Manufacturing
                                ['label' => __('Manufacturing'),                    'on' => (bool)$plan->manufacturing_enabled,  'q' => $plan->manufacturing_quota],
                            ];
                            $fmtQuota = function($q) {
                                if ($q === null) return '';
                                if ((int)$q === -1) return ' — ' . __('Unlimited');
                                return ' — ' . __('Limit:') . ' ' . (int)$q;
                            };
                        @endphp

                        @foreach ($rows as $r)
                            <li class="text-[#727272] text-[12px] font-[600] mt-1 flex items-center">
                                <span class="text-white {{ $r['on'] ? 'bg-green-700' : 'bg-gray-400' }} px-[7px] py-[2px] rounded-full mr-2">
                                    {{ $r['on'] ? '✔' : '–' }}
                                </span>
                                {{ $r['label'] }}{!! $fmtQuota($r['q']) !!}
                            </li>
                        @endforeach

                        @php $legacy = json_decode($plan->features, true); @endphp
                        @if (is_array($legacy) && count($legacy))
                            <li class="mt-3 font-semibold">{{ __('Legacy feature flags') }}</li>
                            @foreach ($legacy as $feature)
                                <li class="text-[#727272] text-[12px] font-[600] mt-1 flex items-center">
                                    <span class="text-white bg-green-500 px-[7px] py-[2px] rounded-full mr-2">✔</span>
                                    {{ $feature }}
                                </li>
                            @endforeach
                        @endif
                    </ul>

                    @if (\Auth::user()->type == 'super admin' && $plan->price > 0)
                        <button class="absolute top-3 right-3 text-gray-400 hover:text-gray-600 cursor-pointer"
                            type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="ti ti-dots-vertical"></i>
                        </button>
                    @endif

                    <div class="dropdown-menu dropdown-menu-end mt-0 w-[190px] bg-white border rounded-md shadow-lg text-sm p-0">
                        @if (\Auth::user()->type == 'super admin')
                            <a href="#"
                               class="dropdown-item flex text-[#323232] gap-2 w-full px-4 py-2 text-left hover:bg-[#007C3812]"
                               data-url="{{ route('plans.edit', $plan->id) }}" data-ajax-popup="true"
                               data-bs-toggle="tooltip" title="{{ __('Edit') }}">
                                <img src="{{ asset('web-assets/dashboard/icons/action_icons/edit.svg') }}" alt="edit" />
                                <span>{{ __('Edit') }}</span>
                            </a>
                            @if ($plan->price > 0)
                                {!! Form::open(['method' => 'DELETE','route' => ['plans.destroy', $plan->id],'id' => 'delete-form-' . $plan->id,'class' => 'inline-block']) !!}
                                <a href="#!" class="dropdown-item bs-pass-para flex text-[#323232] gap-2 w-full px-4 py-2 text-left hover:bg-[#007C3812]"
                                   data-bs-toggle="tooltip" title="{{ __('Delete') }}">
                                   <img src="{{ asset('web-assets/dashboard/icons/action_icons/delete.svg') }}" alt="delete" />
                                   <span>{{ __('Delete') }}</span>
                                </a>
                                {!! Form::close() !!}
                            @endif
                        @endif
                    </div>

                </div></div>
            @endforeach
        </div>
    </div>
@endsection

@push('script-page')
    <script>
        $(document).on('click', '.is_disable', function () {
            var id = $(this).attr('data-id');
            var is_disable = ($(this).is(':checked')) ? $(this).val() : 0;

            $.ajax({
                url: '{{ route('plan.disable') }}',
                type: 'POST',
                data: {
                    "is_disable": is_disable,
                    "id": id,
                    "_token": "{{ csrf_token() }}",
                },
                success: function (data) {
                    if (data.success) {
                        show_toastr('success', data.success, 'success');
                    } else {
                        show_toastr('error', data.error, 'error');
                    }
                }
            });
        });
    </script>
@endpush
