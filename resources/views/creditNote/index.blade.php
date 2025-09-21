@extends('layouts.admin')
@section('page-title')
    {{ __('Manage Credit Notes') }}
@endsection
@push('script-page')
    <script>
        $(document).on('change', '#invoice', function() {

            var id = $(this).val();
            var url = "{{ route('invoice.get') }}";

            $.ajax({
                url: url,
                type: 'get',
                cache: false,
                data: {
                    'id': id,

                },
                success: function(data) {
                    $('#amount').val(data)
                },

            });

        })
    </script>
@endpush
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('Credit Note') }}</li>
@endsection

@section('action-btn')
    <div class="float-end">
        @can('create credit note')
            <a href="#" data-url="{{ route('invoice.custom.credit.note') }}"data-bs-toggle="tooltip"
                title="{{ __('Create') }}" data-ajax-popup="true" data-title="{{ __('Create New Credit Note') }}"
                class="flex gap-1 items-center btn bg-[#007C38] text-white px-4 py-1.5 rounded-md text-sm hover:bg-green-700">
                <i class="ti ti-plus"></i>
                {{ __('Create') }}
            </a>
        @endcan
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card border-0 rounded-2xl shadow-md overflow-hidden my-3">
                <div class="h-1 w-full" style="background:#007C38;"></div>
          
            <div class="card-body table-border-style mt-2">
                <h5></h5>
                <div class="table-responsive table-new-design bg-white p-4">
                    <table class="table datatable">
                        <thead>
                            <tr>
                                <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]">
                                    {{ __('Invoice') }}</th>
                                <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]">
                                    {{ __('Customer') }}</th>
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
                            @foreach ($invoices as $invoice)
                                @if (!empty($invoice->creditNote))
                                    @foreach ($invoice->creditNote as $creditNote)
                                        <tr>
                                            <td class="Id  px-4 py-3 border border-[#E5E5E5] text-gray-700">
                                                <a href="{{ route('invoice.show', \Crypt::encrypt($creditNote->invoice)) }}"
                                                    class="border border-[#137051] leading-[24px] text-[#137051] rounded-[4px] text-[12px] font-[500] px-5">{{ AUth::user()->invoiceNumberFormat($invoice->invoice_id) }}</a>
                                            </td>
                                            <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">
                                                {{ !empty($invoice->customer) ? $invoice->customer->name : '-' }}</td>
                                            <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">
                                                {{ Auth::user()->dateFormat($creditNote->date) }}</td>
                                            <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">
                                                {{ Auth::user()->priceFormat($creditNote->amount) }}</td>
                                            <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">
                                                {{ !empty($creditNote->description) ? $creditNote->description : '-' }}</td>
                                            <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">
                                                <button class="w-100 text-gray-400 hover:text-gray-600 cursor-pointer"
                                                    type="button" data-bs-toggle="dropdown" aria-haspopup="true"
                                                    aria-expanded="false">
                                                    <i class="ti ti-dots-vertical"></i>
                                                </button>
                                                <div
                                                    class="dropdown-menu dropdown-menu-end mt-0 w-[190px] bg-white border rounded-md shadow-lg text-sm p-0">
                                                    @can('edit credit note')
                                                        <li>
                                                            <a data-url="{{ route('invoice.edit.credit.note', [$creditNote->invoice, $creditNote->id]) }}"
                                                                data-ajax-popup="true"
                                                                data-title="{{ __('Edit Credit Note') }}" href="#"
                                                                class="dropdown-item flex items-center text-gray-700 gap-2 w-full px-4 py-2 text-left hover:bg-[#007C3812] text-sm"
                                                                data-bs-toggle="tooltip" title="{{ __('Edit') }}"
                                                                data-original-title="{{ __('Edit') }}">
                                                                <i class="ti ti-pencil"></i>
                                                                <span>{{ __('Edit') }}</span>
                                                            </a>
                                                        </li>
                                                    @endcan
                                                    @can('edit credit note')
                                                        <li>
                                                            {!! Form::open([
                                                                'method' => 'DELETE',
                                                                'route' => ['invoice.delete.credit.note', $creditNote->invoice, $creditNote->id],
                                                                'class' => 'delete-form-btn',
                                                                'id' => 'delete-form-' . $creditNote->id,
                                                            ]) !!}
                                                            <a href="#"
                                                                class="dropdown-item flex items-center text-gray-700 gap-2 w-full px-4 py-2 text-left hover:bg-[#007C3812] text-sm bs-pass-para"
                                                                data-bs-toggle="tooltip" title="{{ __('Delete') }}"
                                                                data-original-title="{{ __('Delete') }}"
                                                                data-confirm="{{ __('Are You Sure?') . '|' . __('This action can not be undone. Do you want to continue?') }}"
                                                                data-confirm-yes="document.getElementById('delete-form-{{ $creditNote->id }}').submit();">
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
