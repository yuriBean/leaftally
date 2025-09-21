@extends('layouts.admin')
@section('page-title')
    {{ __('Manage Debit Notes') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('Debit Note') }}</li>
@endsection
@push('script-page')
    <script>
        $(document).on('change', '#bill', function() {

            var id = $(this).val();
            var url = "{{ route('bill.get') }}";

            $.ajax({
                url: url,
                type: 'get',
                cache: false,
                data: {
                    'bill_id': id,

                },
                success: function(data) {
                    $('#amount').val(data)
                },

            });

        })
    </script>
@endpush

@section('action-btn')
    <div class="float-end">
        @can('create debit note')
            <a href="#" data-url="{{ route('bill.custom.debit.note') }}" data-ajax-popup="true"
                data-title="{{ __('Create New Debit Note') }}" data-bs-toggle="tooltip" title="{{ __('Create') }}"
                class="flex items-center gap-2 bg-[#007C38] text-white px-4 py-2 rounded-[6px] text-[14px] font-[500] hover:bg-[#005f2a] transition-all duration-200 shadow-sm min-w-fit">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                {{ __('Create New') }}
            </a>
        @endcan
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card border-0 rounded-2xl shadow-md overflow-hidden my-3">
                <div class="h-1 w-full" style="background:#007C38;"></div>
          
            <div class="card-body table-border-style">
                <div class="table-responsive table-new-design bg-white p-4">
                    <table class="table datatable">
                        <thead>
                            <tr>
                                <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]">
                                    {{ __('Bill') }}</th>
                                <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]">
                                    {{ __('Vendor') }}</th>
                                <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]">
                                    {{ __('Date') }}</th>
                                <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]">
                                    {{ __('Amount') }}</th>
                                <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]">
                                    {{ __('Description') }}</th>
                                <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]"
                                    width="10%"> {{ __('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>

                            @foreach ($bills as $bill)
                                @if (!empty($bill->debitNote))
                                    @foreach ($bill->debitNote as $debitNote)
                                        <tr class="font-style">
                                            <td class="Id px-4 py-3 border border-[#E5E5E5] text-gray-700">
                                                <a href="{{ route('bill.show', \Crypt::encrypt($debitNote->bill)) }}"
                                                    class="border border-[#137051] leading-[24px] text-[#137051] rounded-[4px] text-[12px] font-[500] px-5">{{ AUth::user()->billNumberFormat($bill->id) }}
                                                </a>
                                            </td>
                                            <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">
                                                {{ !empty($bill->vender) ? $bill->vender->name : '-' }}</td>
                                            <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">
                                                {{ Auth::user()->dateFormat($debitNote->date) }}</td>
                                            <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">
                                                {{ Auth::user()->priceFormat($debitNote->amount) }}</td>
                                            <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">
                                                {{ !empty($debitNote->description) ? $debitNote->description : '-' }}</td>
                                            <td class="Action px-4 py-3 border border-[#E5E5E5] text-gray-700">
                                                <button
                                                    class="w-100 text-gray-400 hover:text-gray-600 cursor-pointer"
                                                    type="button" data-bs-toggle="dropdown" aria-haspopup="true"
                                                    aria-expanded="false">
                                                    <i class="ti ti-dots-vertical"></i>
                                                </button>
                                                <div
                                                    class="dropdown-menu dropdown-menu-end mt-0 w-[190px] bg-white border rounded-md shadow-lg text-sm p-0">
                                                    @can('edit debit note')
                                                        <li>
                                                            <a data-url="{{ route('bill.edit.debit.note', [$debitNote->bill, $debitNote->id]) }}"
                                                                data-ajax-popup="true" data-title="{{ __('Edit Debit Note') }}"
                                                                href="#"
                                                                class="dropdown-item flex items-center text-gray-700 gap-2 w-full px-4 py-2 text-left hover:bg-[#007C3812] text-sm"
                                                                data-bs-toggle="tooltip" title="{{ __('Edit') }}"
                                                                data-original-title="{{ __('Edit') }}">
                                                                <i class="ti ti-pencil"></i>
                                                                <span>{{ __('Edit') }}</span>
                                                            </a>
                                                        </li>
                                                    @endcan
                                                    @can('edit debit note')
                                                        <li>
                                                            {!! Form::open([
                                                                'method' => 'DELETE',
                                                                'route' => ['bill.delete.debit.note', $debitNote->bill, $debitNote->id],
                                                                'id' => 'delete-form-' . $debitNote->id,
                                                            ]) !!}
                                                            <a href="#"
                                                                class="dropdown-item flex items-center text-gray-700 gap-2 w-full px-4 py-2 text-left hover:bg-[#007C3812] text-sm bs-pass-para"
                                                                data-bs-toggle="tooltip" title="{{ __('Delete') }}"
                                                                data-original-title="{{ __('Delete') }}"
                                                                data-confirm="{{ __('Are You Sure?') . '|' . __('This action can not be undone. Do you want to continue?') }}"
                                                                data-confirm-yes="document.getElementById('delete-form-{{ $debitNote->id }}').submit();">
                                                                <i class="ti ti-trash"></i>
                                                                <span>{{ __('Delete') }}</span>
                                                            </a>
                                                            {!! Form::close() !!}
                                                        </li>
                                                    @endcan
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        </div>
    </div>
@endsection
