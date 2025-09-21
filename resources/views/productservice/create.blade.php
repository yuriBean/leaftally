<script src="{{ asset('js/unsaved.js') }}"></script>

@extends('layouts.admin')
@php
    $TAX_ENABLED = \App\Services\Feature::for(\Auth::user())
        ->enabled(\App\Enum\PlanFeature::TAX);
@endphp
@section('page-title')
    {{ __('Create Product & Services') }}
@endsection
@php
    $plan = \App\Models\Utility::getChatGPTSettings();
    $selectedType = request('type', 'product'); // Get type from URL parameter, default to product
@endphp
@section('content')
<div class="bg-white border border-[#E5E5E5] rounded-[8px] p-6 mt-4">
    {{ Form::open(['url' => 'productservice', 'class'=>'needs-validation','novalidate']) }}
    <div class="row">
        @if (isset($plan->enable_chatgpt)  && $plan->enable_chatgpt == 'on')
            <div>
                <a href="#" data-size="md" data-ajax-popup-over="true"
                    data-url="{{ route('generate', ['product & service']) }}" data-bs-toggle="tooltip"
                    data-bs-placement="top" title="{{ __('Generate') }}" data-title="{{ __('Generate content with AI') }}"
                    class="ml-auto bg-[#007C38] text-white text-sm font-medium px-4 py-2 rounded-md hover:bg-[#005f2a] transition">
                    <i class="fas fa-robot"></i>
                    {{ __('Generate with AI') }}
                </a>
            </div>
        @endif
        
        <!-- Type Selection at the top -->
        <div class="col-md-12 mb-3">
            <div class="form-group">
                <div class="btn-box">
                    <label class="d-block form-label">{{ __('Type') }}</label>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-check form-check-inline">
                                <input type="radio" class="form-check-input type" id="customRadio5" name="type"
                                       value="Product" {{ $selectedType == 'product' ? 'checked' : '' }}>
                                <label class="custom-control-label form-label" for="customRadio5">{{ __('Product') }}</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check form-check-inline">
                                <input type="radio" class="form-check-input type" id="customRadio6" name="type"
                                       value="Service" {{ $selectedType == 'service' ? 'checked' : '' }}>
                                <label class="custom-control-label form-label" for="customRadio6">{{ __('Service') }}</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- NEW: Material Type radios (only for Product) -->
        <div class="col-md-12 mb-3 material-type-wrap">
            <div class="form-group">
                <label class="d-block form-label">{{ __('Material') }}</label>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-check form-check-inline">
                            <input type="radio" class="form-check-input" id="mt_raw" name="material_type" value="raw">
                            <label class="custom-control-label form-label" for="mt_raw">{{ __('Raw material') }}</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-check form-check-inline">
                            <input type="radio" class="form-check-input" id="mt_finished" name="material_type" value="finished">
                            <label class="custom-control-label form-label" for="mt_finished">{{ __('Finished product') }}</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-check form-check-inline">
                            <input type="radio" class="form-check-input" id="mt_both" name="material_type" value="both">
                            <label class="custom-control-label form-label" for="mt_both">{{ __('Both') }}</label>
                        </div>
                    </div>
                </div>
                <small class="text-muted">{{ __('Select material classification (required for Products).') }}</small>
            </div>
        </div>
        <!-- /NEW -->

        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('name', __('Name'), ['class' => 'form-label']) }}<x-required></x-required>
                <div class="form-icon-user">
                    {{ Form::text('name', '', ['class' => 'form-control', 'required' => 'required']) }}
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('sku', __('SKU'), ['class' => 'form-label']) }}<x-required></x-required>
                <div class="form-icon-user">
                    {{ Form::text('sku', '', ['class' => 'form-control', 'required' => 'required']) }}
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('sale_price', __('Sale Price'), ['class' => 'form-label']) }}<x-required></x-required>
                <div class="form-icon-user">
                    {{ Form::number('sale_price', '', ['class' => 'form-control', 'required' => 'required', 'step' => '0.01']) }}
                </div>
            </div>
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('sale_chartaccount_id', __('Income Account'),['class'=>'form-label']) }}<x-required></x-required>
            <select name="sale_chartaccount_id" class="form-control"  required="required">
                @foreach ($incomeChartAccounts as $key => $chartAccount)
                    <option value="{{ $key }}" class="subAccount">{{ $chartAccount }}</option>
                    @foreach ($incomeSubAccounts as $subAccount)
                        @if ($key == $subAccount['account'])
                            <option value="{{ $subAccount['id'] }}" class="ms-5"> &nbsp; &nbsp;&nbsp; {{ $subAccount['name'] }}</option>
                        @endif
                    @endforeach
                @endforeach
            </select>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('purchase_price', __('Purchase Price'), ['class' => 'form-label']) }}<x-required></x-required>
                <div class="form-icon-user">
                    {{ Form::number('purchase_price', '', ['class' => 'form-control', 'required' => 'required', 'step' => '0.01']) }}
                </div>
            </div>
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('expense_chartaccount_id', __('Expense Account'),['class'=>'form-label']) }}<x-required></x-required>
            <select name="expense_chartaccount_id" class="form-control" required="required">
                @foreach ($expenseChartAccounts as $key => $chartAccount)
                    <option value="{{ $key }}" class="subAccount">{{ $chartAccount }}</option>
                    @foreach ($expenseSubAccounts as $subAccount)
                        @if ($key == $subAccount['account'])
                            <option value="{{ $subAccount['id'] }}" class="ms-5"> &nbsp; &nbsp;&nbsp; {{ $subAccount['name'] }}</option>
                        @endif
                    @endforeach
                @endforeach
            </select>
        </div>
        @if($TAX_ENABLED)
        <div class="form-group col-md-6">
            {{ Form::label('tax_id', __('Tax (Optional)'), ['class' => 'form-label']) }}
            {{ Form::select('tax_id[]', $tax, null, ['class' => 'form-control select2', 'id' => 'choices-multiple1', 'multiple']) }}
            
            <div class="text-xs mt-2">
                {{ __('Need to add a new tax rate? ') }}<a href="#" onclick="openAddTaxModal()" class="text-[#007C38] font-semibold">{{ __('Add Tax') }}</a>
            </div>
        </div>
        @endif
        <div class="form-group col-md-6">
            {{ Form::label('category_id', __('Category'), ['class' => 'form-label']) }}<x-required></x-required>
            {{ Form::select('category_id', $category, null, ['class' => 'form-control select', 'required' => 'required']) }}

            <div class="text-xs mt-2">
                {{ __('Need to add a new category? ') }}<a href="#" id="add_category"  class="text-[#007C38] font-semibold">🔴 {{ __('Add Category') }}</a>
            </div>
        </div>
        <div class="form-group col-md-6 unit-field">
            {{ Form::label('unit_id', __('Unit'), ['class' => 'form-label']) }}<x-required></x-required>
            {{ Form::select('unit_id', $unit, null, ['class' => 'form-control select', 'required' => 'required']) }}
            
            <div class="text-xs mt-2">
                {{ __('Need to add a new unit? ') }}<a href="#" id="add_unit" class="text-[#007C38] font-semibold">🔴 {{ __('Add Unit') }}</a>
            </div>
        </div>

        <!-- <div class="form-group col-md-6">
            {{ Form::label('quantity', __('Quantity'), ['class' => 'form-label']) }}<x-required></x-required>
            {{ Form::text('quantity', null, ['class' => 'form-control', 'required' => 'required']) }}
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <div class="btn-box">
                    <label class="d-block form-label">{{ __('Type') }}</label>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-check form-check-inline">
                                <input type="radio" class="form-check-input" id="customRadio5" name="type"
                                    value="Product" checked="checked" onclick="hide_show(this)">
                                <label class="custom-control-label form-label"
                                    for="customRadio5">{{ __('Product') }}</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check form-check-inline">
                                <input type="radio" class="form-check-input" id="customRadio6" name="type"
                                    value="Service" onclick="hide_show(this)">
                                <label class="custom-control-label form-label"
                                    for="customRadio6">{{ __('Service') }}</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> -->
        <div class="form-group col-md-3 quantity-field">
            {{ Form::label('quantity', __('Quantity'), ['class' => 'form-label']) }}<x-required></x-required>
            {{ Form::text('quantity', null, ['class' => 'form-control', 'required' => 'required']) }}
        </div>
        <div class="form-group col-md-3 reorder-field">
            {{ Form::label('reorder_level', __('Reorder Level'), ['class' => 'form-label']) }}<x-required></x-required>
            {{ Form::number('reorder_level', null, ['class' => 'form-control', 'required' => 'required']) }}
        </div>
        <div class="form-group col-md-12">
            {{ Form::label('description', __('Description'), ['class' => 'form-label']) }}
            {!! Form::textarea('description', null, ['class' => 'form-control', 'rows' => '2']) !!}
        </div>
        @if (!$customFields->isEmpty())
            <div class="col-lg-6 col-md-6 col-sm-6">
                <div class="tab-pane fade show" id="tab-2" role="tabpanel">
                    @include('customFields.formBuilder')
                </div>
            </div>
        @endif
    </div>
    
    <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-[#E5E5E5]">
        <a href="{{ route('productservice.index') }}" class="btn py-[6px] px-[10px] btn text-[#007C38] border-[#007C38] hover:bg-[#007C38] hover:text-white">
            {{ __('Cancel') }}
        </a>
        <input type="submit" value="{{ __('Create') }}" class="btn py-[6px] px-[10px] btn bg-[#007C38] text-white hover:bg-[#005f2a]">
    </div>
    
    {{ Form::close() }}
</div>

@php
    $plan = \App\Models\Utility::getChatGPTSettings();
@endphp
<div class="modal fade" id="productCategoryModal" tabindex="-1" aria-labelledby="productCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
       <form id="add_category_form">
            <div class="modal-header">
                <h5 class="modal-title" id="productUnitModalLabel">{{ __('Create Product Category') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
    <div class="row">
        @if ($plan->enable_chatgpt == 'on')
            <div>
                <a href="#" data-size="md" data-ajax-popup-over="true"
                    data-url="{{ route('generate', ['category']) }}" data-bs-toggle="tooltip" data-bs-placement="top"
                    title="{{ __('Generate') }}" data-title="{{ __('Generate content with AI') }}"
                    class="btn btn-primary btn-sm float-end">
                    <i class="fas fa-robot"></i>
                    {{ __('Generate with AI') }}
                </a>
            </div>
        @endif
        <div class="form-group col-md-12">
            {{ Form::label('name', __('Category Name'), ['class' => 'form-label']) }}<x-required></x-required>
            {{ Form::text('name', '', ['class' => 'form-control', 'required' => 'required']) }}
        </div>
        <div class="form-group col-md-12 account d-none">
            {{Form::label('chart_account_id',__('Account'),['class'=>'form-label'])}}
            <select class="form-control select" name="chart_account" id="chart_account"></select>
            <input type="hidden" name="type" value="product & service">
        </div>
        <div class="form-group col-md-12">
    {{ Form::label('color', __('Category Color'), ['class' => 'form-label']) }}<x-required></x-required>
    <div class="row gutters-xs">
        @foreach (App\Models\Utility::templateData()['colors'] as $key => $hexNoHash)
            @php
                $hex = '#'.$hexNoHash; // store with leading '#', same as the old color input
            @endphp
            <div class="col-auto">
                <label class="colorinput" title="{{ $hex }}">
                    <input name="color" type="radio"
                           value="{{ $hex }}"
                           class="colorinput-input" required>
                    <span class="colorinput-color" style="background: {{ $hex }}"></span>
                </label>
            </div>
        @endforeach
    </div>
    <small class="text-muted d-block mt-1">{{ __('For chart representation') }}</small>
</div>

    </div>
</div>
<div class="modal-footer">
    <input type="button" value="{{ __('Cancel') }}" class="btn  btn-light" data-bs-dismiss="modal">
    <input type="submit" value="{{ __('Create') }}" class="btn  btn-primary">
</div>
</form>
        </div>
    </div>
</div>
<!-- unit modal -->
<div class="modal fade" id="productUnitModal" tabindex="-1" aria-labelledby="productUnitModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
       <form id="add_unit_form">
            <div class="modal-header">
                <h5 class="modal-title" id="productUnitModalLabel">{{ __('Create Product Unit') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="form-group col-md-12">
                        {{ Form::label('name', __('Unit Name'),['class'=>'form-label']) }}<x-required></x-required>
                        {{ Form::text('name', '', array('class' => 'form-control','required'=>'required')) }}
                        @error('name')
                            <small class="invalid-name" role="alert">
                                <strong class="text-danger">{{ $message }}</strong>
                            </small>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <input type="button" value="{{__('Cancel')}}" class="btn btn-light" data-bs-dismiss="modal">
                <input type="submit" value="{{__('Create')}}" class="btn btn-primary">
            </div>
</form>
        </div>
    </div>
</div>
@endsection

@push('script-page')
<script>
// Handle type selection changes
$(document).on('click', 'input[name="type"]', function () {
    var type = $(this).val();
    
    if (type === 'Product') {
        $('.quantity-field').show();
        $('.reorder-field').show();
        $('.unit-field').show();
        $('input[name="quantity"]').prop('required', true);
        $('input[name="reorder_level"]').prop('required', true);
        $('select[name="unit_id"]').prop('required', true);

        // Show material radios and default to 'finished' if nothing selected
        $('.material-type-wrap').show();
        if (!$('input[name="material_type"]:checked').length) {
            $('#mt_finished').prop('checked', true);
        }
    } else if (type === 'Service') {
        // Hide quantity and unit fields for services
        $('.quantity-field').hide();
        $('.reorder-field').hide();
        $('.unit-field').hide();
        $('input[name="quantity"]').val('').prop('required', false);
        $('input[name="reorder_level"]').val('').prop('required', false);
        $('select[name="unit_id"]').val('').prop('required', false);

        // Hide and clear material radios for services
        $('.material-type-wrap').hide();
        $('input[name="material_type"]').prop('checked', false);
    }
});

// Initialize form based on selected type
$(document).ready(function() {
    var selectedType = $('input[name="type"]:checked').val();
    if (selectedType === 'Service') {
        $('.quantity-field').hide();
        $('.unit-field').hide();
        $('input[name="quantity"]').prop('required', false);
        $('input[name="reorder_level"]').prop('required', false);
        $('select[name="unit_id"]').prop('required', false);

        // Hide material for service
        $('.material-type-wrap').hide();
        $('input[name="material_type"]').prop('checked', false);
    } else {
        // For Product: ensure material default if not chosen
        $('.material-type-wrap').show();
        if (!$('input[name="material_type"]:checked').length) {
            $('#mt_finished').prop('checked', true);
        }
    }
});


$("#add_category").click(function (e) {
    $("#productCategoryModal").modal("show");
});
$("#add_category_form").submit(function (e) {
    e.preventDefault();
    var csrfToken = $('meta[name="csrf-token"]').attr('content');
    var data = new FormData(this);

    $.ajax({
        url: "{{ route('product-category-short') }}",
        method: "POST",
        dataType: "json",
        data: data,
        processData: false,
        contentType: false,
        headers: {
            'X-CSRF-TOKEN': csrfToken
        },
        success: function (response) {
            if (response.status == "1") {
                $("#productCategoryModal").modal("hide");
                $("#add_category_form")[0].reset(); 
                $("#category_id").html(response.options);
                show_toastr("success",response.message);
            }else{
                show_toastr("error",response.message);
            }
        },
        error: function (xhr, status, error) {
            toastr.error("Something went wrong. Please try again.");
            show_toastr("error",error.message);
        }
    });
});

$("#add_unit").click(function (e) {

    $("#productUnitModal").modal("show");
});
$("#add_unit_form").submit(function (e) {
    e.preventDefault();
    var data = {
        name: $(this).find("input[name='name']").val(),
        _token: $('meta[name="csrf-token"]').attr('content')
    };

    $.ajax({
        url: "{{ route('product-unit-short') }}",
        method: "POST",
        dataType: "json",
        data: data,
        success: function (response) {
            if (response.status == "1") {
                $("#productUnitModal").modal("hide");
                $("#add_unit_form")[0].reset();
                $("#unit_id").html(response.options)
                show_toastr("success",response.message);
            }else{
                show_toastr("error",response.message);
            }
        },
        error: function (xhr, status, error) {
            console.error("Error: ", error);
            console.error("Response: ", xhr.responseText);
            show_toastr("error",error.message);
        }
    });
});
function openAddTaxModal() {
    // Open tax creation modal in new tab
    window.open("{{ route('taxes.index') }}", '_blank');
    
    // Show instruction message
    Swal.fire({
        title: 'Add Tax',
        text: 'A new tab has opened where you can add tax rates. After adding, please refresh this page to see the new taxes.',
        icon: 'info',
        confirmButtonText: 'Got it!',
        confirmButtonColor: '#007C38'
    });
}
</script>
@endpush
