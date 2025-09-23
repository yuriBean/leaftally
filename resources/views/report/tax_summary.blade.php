@extends('layouts.admin')
@section('page-title')
    {{__('Tax Summary')}}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    <li class="breadcrumb-item">{{__('Report')}}</li>
    <li class="breadcrumb-item">{{__('Tax Summary')}}</li>
@endsection
@push('script-page')
    <script type="text/javascript" src="{{ asset('js/html2pdf.bundle.min.js') }}"></script>
    <script>
        var year = '{{$currentYear}}';

        var filename = $('#filename').val();

        function saveAsPDF() {
            var element = document.getElementById('printableArea');
            var opt = {
                margin: 0.3,
                filename: filename,
                image: {type: 'jpeg', quality: 1},
                html2canvas: {scale: 4, dpi: 72, letterRendering: true},
                jsPDF: {unit: 'in', format: 'A2'}
            };
            html2pdf().set(opt).from(element).save();
        }
    </script>
@endpush

@section('action-btn')
    <div class="d-flex">
    
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
                    {{ Form::open(array('route' => array('report.tax.summary'),'method' => 'GET','id'=>'report_tax_summary')) }}
                        <div class="row align-items-center justify-content-end">
                            <div class="col-md-10">
                                <div class="row">
                                    
                                    <div class="col-md-4 col-sm-12 col-12">
                                        <div class="btn-box">
                                        {{ Form::select('year',$yearList,isset($_GET['year'])?$_GET['year']:'', array('class' => 'form-control')) }}
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <div class="col-md-2 col-12">
                                <div class="row">
                                    <div class="col-auto d-flex">

                                        <a href="#" class="btn btn-sm btn-primary me-2" onclick="document.getElementById('report_tax_summary').submit(); return false;" data-bs-toggle="tooltip" title="{{__('Apply')}}" data-original-title="{{__('apply')}}">
                                            <span class="btn-inner--icon"><i class="ti ti-search"></i></span>
                                        </a>

                                        <a href="{{route('report.tax.summary')}}" class="btn btn-sm btn-danger " data-bs-toggle="tooltip"  title="{{ __('Reset') }}" data-original-title="{{__('Reset')}}">
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
        {{-- <div class="row mt-3">
            <div class="col">
                <input type="hidden" value="{{__('Tax Summary').' '.'Report of'.' '.$filter['startDateRange'].' to '.$filter['endDateRange']}}" id="filename">
                <div class="card p-4 mb-4">
                    <h7 class="report-text gray-text mb-0">{{__('Report')}} :</h7>
                    <h6 class="report-text mb-0">{{__('Tax Summary')}}</h6>
                </div>
            </div>
            <div class="col">
                <div class="card p-4 mb-4">
                    <h7 class="report-text gray-text mb-0">{{__('Duration')}} :</h7>
                    <h6 class="report-text mb-0">{{$filter['startDateRange'].' to '.$filter['endDateRange']}}</h6>
                </div>
            </div>
        </div> --}}

        <div class="row bg-white py-4 border border-[#E5E5E5] rounded-[8px]">
            <div class="col-12">
                <div class="card border-0 rounded-2xl shadow-md overflow-hidden my-3">
                    <div class="h-1 w-full" style="background:#007C38;"></div>

                    <div class="card-body table-border-style">
                        <div class="col-sm-12">
                            <h5 class="text-[14px] text-black font-[700]">{{__('Income')}}</h5>
                            <div class="table-responsive bg-white p-4 mt-3 mb-3">
                                <table class="border border-[#E5E5E5] rounded-[8px] table">
                                    <thead>
                                    <tr>
                                        <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]">{{__('Tax')}}</th>
                                        @foreach($monthList as $month)
                                            <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]">{{$month}}</th>
                                        @endforeach
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @forelse(array_keys($incomes) as $k=> $taxName)
                                        <tr>
                                            <td class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]">{{$taxName}}</td>
                                            @foreach(array_values($incomes)[$k] as $price)
                                                <td class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]">{{\Auth::user()->priceFormat($price)}}</td>
                                            @endforeach
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="13" class="text-center p-5">{{__('Income tax not found')}}</td>
                                        </tr>
                                    @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <h5 class="text-[14px] text-black font-[700]">{{__('Expense')}}</h5>
                            <div class="table-responsive bg-white p-4 mt-4">
                                <table class="border border-[#E5E5E5] rounded-[8px] table">
                                    <thead>
                                    <tr>
                                        <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]">{{__('Tax')}}</th>
                                        @foreach($monthList as $month)
                                            <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]">{{$month}}</th>
                                        @endforeach
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @forelse(array_keys($expenses) as $k=> $taxName)
                                        <tr>
                                            <td class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]">{{$taxName}}</td>
                                            @foreach(array_values($expenses)[$k] as $price)
                                                <td class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]">{{\Auth::user()->priceFormat($price)}}</td>
                                            @endforeach
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="13" class="text-center p-5">{{__('Expense tax not found')}}</td>
                                        </tr>
                                    @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
            </div>
                
            </div>
        </div>
    </div>
@endsection

