@extends('layouts.admin')
@section('page-title')
    {{__('Product Stock')}}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    <li class="breadcrumb-item">{{__('Report')}}</li>

    <li class="breadcrumb-item">{{__('Product Stock Report')}}</li>
@endsection
@section('action-btn')
    <div class="float-end">
        <a href="{{ route('productstock.export') }}" data-bs-toggle="tooltip" title="{{ __('Export') }}"
            class="btn btn-sm btn-primary">
            <i class="fas fa-file-pdf"></i>
        </a>

        {{-- <a href="#" class="btn btn-sm btn-primary" onclick="saveAsPDF()"data-bs-toggle="tooltip" title="{{__('Download PDF')}}" data-original-title="{{__('Download PDF')}}">
            <span class="btn-inner--icon"><i class="fas fa-file-pdf"></i></span>
        </a> --}}

    </div>
@endsection
{{-- @push('script-page')
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
        console.log(opt);
        html2pdf().set(opt).from(element).save();

    }

</script>
@endpush --}}
@section('content')
{{-- <div id="printableArea"> --}}
    <div class="row">
        <div class="col-md-12">
            {{-- <input type="hidden" value="{{__('Product Stock Report')}}" id="filename"> --}}
            <div class="card border-0 rounded-2xl shadow-md overflow-hidden my-3">
                <div class="h-1 w-full" style="background:#007C38;"></div>

                <div class="card-body table-border-style">
                    <div class="table-responsive bg-white p-4">
                        <table class="table datatable my-4">
                            <thead>
                            <tr>
                                <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-md">{{__('Date')}}</th>
                                <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-md">{{__('Product Name')}}</th>
                                <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-md">{{__('Quantity')}}</th>
                                <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-md">{{__('Type')}}</th>
                                <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-md">{{__('Description')}}</th>
                            </tr>
                            </thead>
                            <tbody>
                                @foreach ($stocks as $stock)
                                    <tr>
                                        <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">{{$stock->created_at->format('d M Y')}}</td>
                                        <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">{{ !empty($stock->product) ? $stock->product->name : '' }}
                                        <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">{{ $stock->quantity }}</td>
                                        <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">{{ ucfirst($stock->type) }}</td>
                                        <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">{{$stock->description}}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
        </div>
            
        </div>
    </div>
{{-- </div> --}}
@endsection