@extends('layouts.admin')

@php
    $TAX_ENABLED = \App\Services\Feature::for(\Auth::user())->enabled(\App\Enum\PlanFeature::TAX);
@endphp

@section('page-title')
    {{ __('Product & Services') }}
@endsection

@push('script-page')
@endpush

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('Product & Services') }}</li>
@endsection

@section('action-btn')
    <style>
        .low-stock { background-color:
        .mcheck{display:inline-flex;align-items:center;cursor:pointer;user-select:none}
        .mcheck input{position:absolute;opacity:0;width:0;height:0}
        .mcheck .box{width:20px;height:20px;border:2px solid
        .mcheck .box:hover{box-shadow:0 1px 3px rgba(0,0,0,.08)}
        .mcheck input:focus + .box{box-shadow:0 0 0 3px rgba(0,124,56,.2)}
        .mcheck input:checked + .box{background:
        .mcheck input:checked + .box::after{content:"";position:absolute;left:6px;top:2px;width:5px;height:10px;border:2px solid

            table-layout: fixed !important;
            width: 100% !important;
        }

            border-collapse: separate !important;
            border-spacing: 0 !important;
            text-align: left !important;
            vertical-align: middle !important;
        }

        .dataTables_length,
        .dataTables_wrapper .dataTables_length,
        div.dataTables_length,
            background: white !important;
            padding: 8px 12px !important;
            border: 1px solid
            border-radius: 6px !important;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1) !important;
            margin-bottom: 10px !important;
            display: inline-block !important;
        }

        .dataTables_length label,
            margin: 0 !important;
            font-weight: 500 !important;
            color:
        }

        .dataTables_length select,
            margin: 0 4px !important;
            padding: 4px 8px !important;
            border: 1px solid
            border-radius: 4px !important;
        }
    </style>

    <div class="flex items-center gap-2 mt-2 sm:mt-0">
        <a href="#" data-size="md" data-bs-toggle="tooltip" title="{{ __('Import') }}"
           data-url="{{ route('productservice.file.import') }}" data-ajax-popup="true"
           data-title="{{ __('Import product CSV file') }}" style="border: 1px solid #007C38 !important"
           class="flex items-center gap-1 border border-[#007C38] text-[#007C38] px-3 py-1.5 rounded-md text-sm hover:bg-green-50 d-none">
            <img src="{{ asset('web-assets/dashboard/icons/import.svg') }}" alt="Import">
            {{ __('Import') }}
        </a>

        <a href="{{ route('productservice.export') }}" data-bs-toggle="tooltip" title="{{ __('Export') }}"
           style="border: 1px solid #007C38 !important"
           class="flex items-center gap-1 border border-[#007C38] text-[#007C38] px-3 py-1.5 rounded-md text-sm hover:bg-green-50">
            <img src="{{ asset('web-assets/dashboard/icons/export.svg') }}" alt="Export">
            {{ __('Export') }}
        </a>

        <a href="#" onclick="showProductServiceTypeSelection()"
           data-bs-toggle="tooltip" title="{{ __('Create') }}"
           class="flex gap-1 items-center btn bg-[#007C38] text-white px-4 py-1.5 rounded-md text-sm hover:bg-green-700">
            <i class="ti ti-plus"></i>
            {{ __('Create New') }}
        </a>
    </div>
@endsection

@section('content')
<div class="card border-0 rounded-2xl shadow-md overflow-hidden my-3">
    <div class="h-1 w-full" style="background:#007C38;"></div>
      <div class="flex justify-between mb-4 p-4">
            <div class="col-sm-12">
                <div class="multi-collapse mt-2 {{ (request()->filled('category') || request()->filled('material_type')) ? 'show' : '' }}" id="multiCollapseExample1">
                    {{ Form::open(['route' => ['productservice.index'], 'method' => 'GET', 'id' => 'product_service']) }}
                    <div class="row d-flex align-items-center justify-content-end text-md">
                        <div class="flex justify-end">
                            
                            <div class="">
                                {{ Form::select('category', $categories, request('category'), [
                                    'class' => 'form-control select  px-2 py-1 outline-none w-[160px] rounded-none rounded-tl-[6px] rounded-bl-[6px]',
                                    'id' => 'choices-category'
                                ]) }}
                            </div>
                            
                            <div class="">
                                {{ Form::select('material_type', $materialTypes ?? ['' => __('All Materials')], request('material_type'), [
                                    'class' => 'form-control select px-2 py-1 outline-none w-[180px] rounded-none',
                                    'id' => 'choices-material'
                                ]) }}
                            </div>
                            <a href="#"
                               class="p-1 px-2 rounded-tr-[6px] rounded-br-[6px] bg-[#137051] text-white hover:bg-green-700 "
                               onclick="document.getElementById('product_service').submit(); return false;"
                               data-bs-toggle="tooltip" title="{{ __('Apply') }}">
                                <span class="btn-inner--icon"><i class="ti ti-search"></i></span>
                            </a>
                        </div>
                    </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>

        @can('delete product & service')
            <x-bulk-toolbar
                :deleteRoute="route('productservice.bulk-destroy')"
                :exportRoute="route('productservice.export-selected')"
                scope="products"
                tableId="products-table"
                selectedLabel="{{ __('Item selected') }}"
            />
        @endcan

        <div class="row">
            <div class="col-xl-12 ">
                <div class="card-body table-border-style">
                    <div class="table-responsive table-new-design bg-white p-4">
                        <table id="products-table" class="table datatable border border-[#E5E5E5] rounded-[8px] mt-4">
                            <thead>
                                <tr>
                                    <th data-sortable="false" data-type="html"
<<<<<<< HEAD
                                        class="border-0 border-b border-r border-[#E5E5E5] px-2 py-3 bg-[#F6F6F6] text-xs font-semibold text-center">
=======
                                        class="border border-[#E5E5E5] px-4 py-1 bg-[#F6F6F6] text-sm font-semibold w-12">
>>>>>>> e913e7eefde6f89ab8509dd6cf5133d7e3e7ed85
                                        <label class="mcheck">
                                            <input type="checkbox" class="jsb-master" data-scope="products">
                                            <span class="box"></span>
                                        </label>
                                    </th>
<<<<<<< HEAD
                                    <th class="border-0 border-b border-r border-[#E5E5E5] px-4 py-3 bg-[#F6F6F6] text-xs font-semibold">{{ __('Name') }}</th>
                                    <th class="border-0 border-b border-r border-[#E5E5E5] px-4 py-3 bg-[#F6F6F6] text-xs font-semibold">{{ __('Sku') }}</th>
                                    <th class="border-0 border-b border-r border-[#E5E5E5] px-4 py-3 bg-[#F6F6F6] text-xs font-semibold">{{ __('Sale price') }}</th>
                                    <th class="border-0 border-b border-r border-[#E5E5E5] px-4 py-3 bg-[#F6F6F6] text-xs font-semibold">{{ __('Purchase price') }}</th>
                                    @if($TAX_ENABLED)
                                        <th class="border-0 border-b border-r border-[#E5E5E5] px-4 py-3 bg-[#F6F6F6] text-xs font-semibold">{{ __('Tax') }}</th>
                                    @endif
                                    <th class="border-0 border-b border-r border-[#E5E5E5] px-4 py-3 bg-[#F6F6F6] text-xs font-semibold">{{ __('Category') }}</th>
                                    <th class="border-0 border-b border-r border-[#E5E5E5] px-4 py-3 bg-[#F6F6F6] text-xs font-semibold">{{ __('Unit') }}</th>
                                    <th class="border-0 border-b border-r border-[#E5E5E5] px-4 py-3 bg-[#F6F6F6] text-xs font-semibold">{{ __('Quantity') }}</th>
                                    <th class="border-0 border-b border-r border-[#E5E5E5] px-4 py-3 bg-[#F6F6F6] text-xs font-semibold">{{ __('Reorder Level') }}</th>
                                    <th class="border-0 border-b border-r border-[#E5E5E5] px-4 py-3 bg-[#F6F6F6] text-xs font-semibold">{{ __('Type') }}</th>
                                    
                                    <th class="border-0 border-b border-r border-[#E5E5E5] px-4 py-3 bg-[#F6F6F6] text-xs font-semibold">{{ __('Material') }}</th>
                                    <th class="border-0 border-b border-[#E5E5E5] px-4 py-3 bg-[#F6F6F6] text-xs font-semibold">{{ __('Action') }}</th>
=======
                                    <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] text-sm font-semibold">{{ __('Name') }}</th>
                                    <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] text-sm font-semibold">{{ __('Sku') }}</th>
                                    <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] text-sm font-semibold">{{ __('Sale price') }}</th>
                                    <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] text-sm font-semibold">{{ __('Purchase price') }}</th>
                                    @if($TAX_ENABLED)
                                        <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] text-sm font-semibold">{{ __('Tax') }}</th>
                                    @endif
                                    <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] text-sm font-semibold">{{ __('Category') }}</th>
                                    <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] text-sm font-semibold">{{ __('Unit') }}</th>
                                    <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] text-sm font-semibold">{{ __('Quantity') }}</th>
                                    <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] text-sm font-semibold">{{ __('Reorder Level') }}</th> 
                                    <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] text-sm font-semibold">{{ __('Type') }}</th>
                                    
                                    <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] text-sm font-semibold">{{ __('Material') }}</th>
                                    <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] text-sm font-semibold">{{ __('Action') }}</th>
>>>>>>> e913e7eefde6f89ab8509dd6cf5133d7e3e7ed85
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($productServices as $productService)
                                @php
    $isLow = $productService->type === 'Product'
        && !is_null($productService->reorder_level)
        && (int)$productService->quantity < (int)$productService->reorder_level;
@endphp
<tr class="border-b hover:bg-gray-50 {{ $isLow ? 'low-stock' : '' }}">
                                        <td class="border-0 border-b border-r border-[#E5E5E5] px-2 py-3 text-center">
                                            <label class="mcheck">
                                                <input type="checkbox"
                                                       class="jsb-item"
                                                       data-scope="products"
                                                       value="{{ $productService->id }}"
                                                       data-id="{{ $productService->id }}">
                                                <span class="box"></span>
                                            </label>
                                        </td>

                                    <td class="border-0 border-b border-r border-[#E5E5E5] px-4 py-3">

                                          <a href="#"
                                             data-url="{{ route('productservice.show', $productService->id) }}"
                                             data-ajax-popup="true"
                                             data-size="xl"
                                             data-title="{{ __('Product Details') }}"
                                             class="inline-flex items-center px-3 py-1 rounded-[6px] text-[13px] font-[600] bg-[#007C3810] text-[#007C38] border border-[#007C3820] hover:bg-[#007C3815]">
                                            {{ $productService->name }}
                                          </a>
                                        </td>
                                        <td class="border-0 border-b border-r border-[#E5E5E5] px-4 py-3 text-sm text-[#323232]">
                                            {{ $productService->sku }}
                                        </td>
                                        <td class="border-0 border-b border-r border-[#E5E5E5] px-4 py-3 text-sm text-[#323232]">
                                            {{ \Auth::user()->priceFormat($productService->sale_price) }}
                                        </td>
                                        <td class="border-0 border-b border-r border-[#E5E5E5] px-4 py-3 text-sm text-[#323232]">
                                            {{ \Auth::user()->priceFormat($productService->purchase_price) }}
                                        </td>

                                        @if($TAX_ENABLED)
                                            <td class="border-0 border-b border-r border-[#E5E5E5] px-4 py-3 text-sm text-[#323232]">
                                                @if (!empty($productService->tax_id))
                                                    @php
                                                        $taxes = \App\Models\Utility::tax($productService->tax_id);
                                                    @endphp
                                                    @foreach ($taxes as $tax)
                                                        {{ !empty($tax) ? $tax->name : '' }}<br>
                                                    @endforeach
                                                @else
                                                    -
                                                @endif
                                            </td>
                                        @endif

                                        <td class="border-0 border-b border-r border-[#E5E5E5] px-4 py-3 text-sm text-[#323232]">
                                            {{ optional($productService->category)->name }}
                                        </td>
                                        <td class="border-0 border-b border-r border-[#E5E5E5] px-4 py-3 text-sm text-[#323232]">
                                            {{ optional($productService->unit)->name }}
                                        </td>

                                        @if ($productService->type == 'Product')
                                            <td class="border-0 border-b border-r border-[#E5E5E5] px-4 py-3 text-sm text-[#323232]">
                                                {{ $productService->quantity }}
                                            </td>
                                            <td class="border-0 border-b border-r border-[#E5E5E5] px-4 py-3 text-sm text-[#323232]">
                                                {{ $productService->reorder_level ?? '-' }}
                                            </td>
                                        @else
                                            <td class="border-0 border-b border-r border-[#E5E5E5] px-4 py-3 text-sm text-[#323232]">-</td>
                                        @endif

                                        <td class="border-0 border-b border-r border-[#E5E5E5] px-4 py-3 text-sm text-[#323232]">
                                            {{ $productService->type }}
                                        </td>

                                        <td class="border-0 border-b border-r border-[#E5E5E5] px-4 py-3 text-sm text-[#323232]">
                                            @php
                                                $mt = $productService->material_type;
                                                $mtLabel = '-';
                                                if ($mt === 'raw') $mtLabel = __('Raw material');
                                                elseif ($mt === 'finished') $mtLabel = __('Finished product');
                                                elseif ($mt === 'both') $mtLabel = __('Both');
                                            @endphp
                                            {{ $mtLabel }}
                                        </td>

                                        <td class="border-0 border-b border-[#E5E5E5] px-4 py-3 text-[#323232] Action">
                                            <button class="w-100 text-gray-400 hover:text-gray-600 cursor-pointer"
                                                    type="button" data-bs-toggle="dropdown" aria-haspopup="true"
                                                    aria-expanded="false">
                                                <i class="ti ti-dots-vertical"></i>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-end mt-0 w-[190px] bg-white border rounded-md shadow-lg text-sm p-0">
                                                @can('edit product & service')
                                                    <a href="#"
                                                       class="dropdown-item flex items-center text-[#323232] gap-2 w-full px-4 py-2 text-left hover:bg-[#007C3812]"
                                                       data-url="{{ route('productservice.edit', $productService->id) }}"
                                                       data-ajax-popup="true" data-size="lg" data-bs-toggle="tooltip"
                                                       title="{{ __('Edit') }}" data-title="{{ __('Edit Product') }}">
                                                        <img src="{{ asset('web-assets/dashboard/icons/action_icons/edit.svg') }}" alt="edit" />
                                                        <span>{{ __('Edit') }}</span>
                                                    </a>
                                                @endcan
                                                @can('delete product & service')
                                                    {!! Form::open([
                                                        'method' => 'DELETE',
                                                        'route' => ['productservice.destroy', $productService->id],
                                                        'id' => 'delete-form-' . $productService->id,
                                                    ]) !!}
                                                        <a href="#!"
                                                           class="dropdown-item bs-pass-para flex items-center text-[#323232] gap-2 w-full px-4 py-2 text-left hover:bg-[#007C3812]"
                                                           data-bs-toggle="tooltip" title="{{ __('Delete') }}">
                                                            <img src="{{ asset('web-assets/dashboard/icons/action_icons/delete.svg') }}" alt="delete" />
                                                            <span>{{ __('Delete') }}</span>
                                                        </a>
                                                    {!! Form::close() !!}
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div> {{-- .table-responsive --}}
                </div> {{-- .card-body --}}
            </div>
        </div>
    </div>
@endsection

@push('script-page')
<style>
.swal2-popup .swal2-actions .swal2-cancel.swal2-cancel-custom,
.swal2-cancel.swal2-cancel-custom {
    background-color:
    border: 1px solid
    color:
    font-weight: 500 !important;
    box-shadow: none !important;
    margin: 8px !important;
    padding: 10px 20px !important;
    border-radius: 6px !important;
}
.swal2-popup .swal2-actions .swal2-cancel.swal2-cancel-custom:hover,
.swal2-cancel.swal2-cancel-custom:hover {
    background-color:
    border-color:
    color:
}

.swal2-popup .swal2-actions .swal2-confirm.swal2-confirm-custom,
.swal2-confirm.swal2-confirm-custom {
    background-color:
    border: 1px solid
    color:
    font-weight: 500 !important;
    box-shadow: none !important;
    margin: 8px !important;
    padding: 10px 20px !important;
    border-radius: 6px !important;
}
.swal2-popup .swal2-actions .swal2-confirm.swal2-confirm-custom:hover,
.swal2-confirm.swal2-confirm-custom:hover {
    background-color:
    border-color:
    color:
}
</style>
<script>
function showProductServiceTypeSelection() {
    Swal.fire({
        title: 'What would you like to create?',
        text: 'Please select whether you want to create a Product or a Service.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Product',
        cancelButtonText: 'Service',
        buttonsStyling: false,
        customClass: {
            confirmButton: 'swal2-confirm swal2-confirm-custom',
            cancelButton: 'swal2-cancel swal2-cancel-custom'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = "{{ route('productservice.create') }}?type=product";
        } else if (result.dismiss === Swal.DismissReason.cancel) {
            window.location.href = "{{ route('productservice.create') }}?type=service";
        }
    });
}

$(document).ready(function() {
    function styleDataTableLength() {
        const lengthElements = document.querySelectorAll('.dataTables_length, #products-table_length');
        lengthElements.forEach(function(element) {
            element.style.position = '';
            element.style.top = '';
            element.style.left = '';
            element.style.zIndex = '';
            element.style.background = 'white';
            element.style.padding = '8px 12px';
            element.style.border = '1px solid #E5E5E5';
            element.style.borderRadius = '6px';
            element.style.boxShadow = '0 2px 8px rgba(0,0,0,0.1)';
            element.style.marginBottom = '10px';
            element.style.display = 'inline-block';
        });
    }

    styleDataTableLength();

    setTimeout(styleDataTableLength, 500);

    $(document).on('draw.dt', '#products-table', function() {
        setTimeout(styleDataTableLength, 100);
    });
});
</script>
@endpush
