@extends('layouts.admin')
@section('page-title')
    {{__('Account Statement Summary')}}
@endsection
@push('script-page')
<script type="text/javascript" src="{{ asset('js/html2pdf.bundle.min.js') }}"></script>
    <script>
        var filename = $('#filename').val();

        function saveAsPDF() {
            var element = document.getElementById('printableArea');
            var opt = {
                margin: 0.3,
                filename: filename,
                image: {type: 'jpeg', quality: 1},
                html2canvas: {scale: 4, dpi: 72, letterRendering: true},
                jsPDF: {unit: 'in', format: 'A4'}
            };
            html2pdf().set(opt).from(element).save();
        }

        $(document).ready(function () {
            var filename = $('#filename').val();
            $('#report-dataTable').DataTable({
                dom: 'lBfrtip',
                buttons: [
                    {
                        extend: 'excel',
                        title: filename
                    },
                    {
                        extend: 'pdf',
                        title: filename
                    },  {
                        extend: 'csv',
                        title: filename
                    }
                ]
            });
        });
    </script>
@endpush
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    <li class="breadcrumb-item">{{__('Report')}}</li>

    <li class="breadcrumb-item">{{__('Account Statement Summary')}}</li>
@endsection


@section('action-btn')
    <div class="d-flex">
        <!-- <a class="btn btn-sm btn-primary" data-bs-toggle="collapse" href="#multiCollapseExample1" role="button" aria-expanded="false" aria-controls="multiCollapseExample1" data-bs-toggle="tooltip" title="{{__('Filter')}}">
            <i class="ti ti-filter"></i>
        </a> -->

        <a href="{{route('accountstatement.export')}}" data-bs-toggle="tooltip" title="{{__('Export')}}" class="btn btn-sm btn-primary me-2">
            <i class="fas fa-file-pdf"></i>
        </a>

        <a href="#" class="btn btn-sm btn-primary" onclick="saveAsPDF()"data-bs-toggle="tooltip" title="{{__('Download PDF')}}" data-original-title="{{__('Download PDF')}}">
            <span class="btn-inner--icon"><i class="fas fa-file-pdf"></i></span>
        </a>

    </div>
@endsection


@section('content')

    <div class="row">
        <div class="col-sm-12">
            <div class=" multi-collapse mt-2 " id="multiCollapseExample1">
                <div class="card border-0 rounded-2xl shadow-md overflow-hidden my-3">
                    <div class="h-1 w-full" style="background:#007C38;"></div>
                        <div class="card-body">
                        {{ Form::open(array('route' => array('report.account.statement'),'method'=>'get','id'=>'report_account')) }}
                        <div class="row align-items-center justify-content-end">
                            <div class="col-md-10 col-12">
                                <div class="row">

                                    <div class="col-md-3 col-sm-12 col-12">
                                        <div class="btn-box">
                                        {{ Form::label('start_month', __('Start Month'), ['class' => 'text-type']) }}
                                            {{Form::month('start_month',isset($_GET['start_month'])?$_GET['start_month']:date('Y-m'),array('class'=>'month-btn form-control'))}}
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-sm-12 col-12">
                                        <div class="btn-box">
                                        {{ Form::label('end_month', __('End Month'), ['class' => 'text-type']) }}
                                            {{Form::month('end_month',isset($_GET['end_month'])?$_GET['end_month']:date('Y-m', strtotime("-5 month")),array('class'=>'month-btn form-control'))}}
                                        </div>
                                    </div>

                                    <div class="col-md-3 col-sm-12 col-12">
                                        <div class="btn-box">
                                        {{ Form::label('account', __('Account'), ['class' => 'text-type']) }}
                                            {{Form::select('account', $account,isset($_GET['account'])?$_GET['account']:'', array('class' => 'form-control')) }}
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-sm-12 col-12">
                                        <div class="btn-box">
                                        {{ Form::label('type', __('Type'), ['class' => 'text-type']) }}
                                            {{ Form::select('type',$types,isset($_GET['type'])?$_GET['type']:'', array('class' => 'form-control')) }}
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <div class="col-md-2 col-12">
                                <div class="row">
                                    <div class="col-auto d-flex mt-4">

                                        <a href="#" class="btn btn-sm btn-primary me-2" onclick="document.getElementById('report_account').submit(); return false;" data-bs-toggle="tooltip" title="{{__('Apply')}}" data-original-title="{{__('Apply')}}">
                                            <span class="btn-inner--icon"><i class="ti ti-search"></i></span>
                                        </a>

                                        <a href="{{route('report.account.statement')}}" class="btn btn-sm btn-danger " data-bs-toggle="tooltip"  title="{{ __('Reset') }}" data-original-title="{{__('Reset')}}">
                                            <span class="btn-inner--icon"><i class="ti ti-refresh text-white-off "></i></span>
                                        </a>


                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>

    <div id="printableArea">
        <div class="row mt-3">
            <div class="col">
                <input type="hidden" value="{{__('Account Statement').' '.$filter['type'].' '.'Report of'.' '.$filter['startDateRange'].' to '.$filter['endDateRange']}}" id="filename">
                <div class="card p-4 mb-4">
                    <h7 class="report-text gray-text mb-0">{{__('Report')}} :</h7>
                    <h6 class="report-text mb-0">{{__('Account Statement Summary')}}</h6>
                </div>
            </div>
            @if($filter['account']!=__('All'))
                <div class="col">
                    <div class="card p-4 mb-4 shadow-lg">
                        <h7 class="report-text gray-text mb-0">{{__('Account')}} :</h7>
                        <h6 class="report-text mb-0">{{$filter['account']}}</h6>
                    </div>
                </div>
            @endif
            @if($filter['type']!=__('All'))
                <div class="col">
                    <div class="card p-4 mb-4">
                        <h7 class="report-text gray-text mb-0">{{__('Type')}} :</h7>
                        <h6 class="report-text mb-0">{{$filter['type']}}</h6>
                    </div>
                </div>
            @endif
            <div class="col">
                <div class="card p-4 mb-4">
                    <h7 class="report-text gray-text mb-0">{{__('Duration')}} :</h7>
                    <h6 class="report-text mb-0">{{$filter['startDateRange'].' to '.$filter['endDateRange']}}</h6>
                </div>
            </div>
        </div>

        @if(!empty($reportData['revenueAccounts']))
            <div class="row">
                @foreach($reportData['revenueAccounts'] as $account)
                    <div class="col-xl-3 col-md-6 col-lg-3">
                        <div class="card p-4 mb-4">
                            @if($account->holder_name =='Cash')
                                <h7 class="report-text gray-text mb-0">{{$account->holder_name}}</h7>
                            @elseif(empty($account->holder_name))
                                <h7 class="report-text gray-text mb-0">{{__('Stripe / Paypal')}}</h7>
                            @else
                                <h7 class="report-text gray-text mb-0">{{$account->holder_name.' - '.$account->bank_name}}</h7>
                            @endif
                            <h6 class="report-text mb-0">{{\Auth::user()->priceFormat($account->total)}}</h6>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        @if(!empty($reportData['paymentAccounts']))
            <div class="row">
                @foreach($reportData['paymentAccounts'] as $account)
                    <div class="col-xl-3 col-md-6 col-lg-3">
                        <div class="card p-4 mb-4">
                            @if($account->holder_name =='Cash')
                                <h5 class="report-text gray-text mb-0">{{$account->holder_name}}</h5>
                            @elseif(empty($account->holder_name))
                                <h5 class="report-text gray-text mb-0">{{__('Stripe / Paypal')}}</h5>
                            @else
                                <h5 class="report-text gray-text mb-0">{{$account->holder_name.' - '.$account->bank_name}}</h5>
                            @endif
                            <h5 class="report-text mb-0">{{\Auth::user()->priceFormat($account->total)}}</h5>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card border-0 rounded-2xl shadow-md overflow-hidden my-3">
                <div class="h-1 w-full" style="background:#007C38;"></div>
                <div class="card-body table-border-style">
                    <div class="table-responsive table-new-design bg-white p-4">
                        <table id="products-table" class="table border border-[#E5E5E5] rounded-[8px] mt-4">
                            <thead>
                            <tr>
                                <th class="sort px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]">{{__('Date')}}</th>
                                <th class="sort px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]">{{__('Amount')}}</th>
                                <th class="sort px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]">{{__('Description')}}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if(!empty($reportData['revenues']))
                                @foreach ($reportData['revenues'] as $revenue)
                                    <tr class="font-style">
                                        <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">{{ Auth::user()->dateFormat($revenue->date) }}</td>
                                        <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">{{ Auth::user()->priceFormat($revenue->amount) }}</td>
                                        <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">{{$revenue->description}} </td>
                                    </tr>
                                @endforeach
                            @endif
                            @if(!empty($reportData['payments']))
                                @foreach ($reportData['payments'] as $payments)
                                    <tr class="font-style">
                                        <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">{{ Auth::user()->dateFormat($payments->date) }}</td>
                                        <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">{{ Auth::user()->priceFormat($payments->amount) }}</td>
                                        <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">{{!empty($payments->description)?$payments->description:'-'}} </td>
                                    </tr>
                                @endforeach
                            @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
