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
    $selectedType = request('type', 'product');
@endphp
@section('content')

<div class="modal-body" style="background: var(--zameen-background-section); padding: 1.5rem;">
  
  <div style="background: linear-gradient(135deg, #00b98d 0%, #00d4a3 100%); padding: 1.75rem 2rem 1.25rem; border-radius: 12px 12px 0 0; margin: 0 1.5rem;">
    <div style="color: white; margin-bottom: 0.5rem;">
      <h4 style="margin: 0; font-weight: 600; font-size: 1.5rem; color: white;">{{ __('Create Product & Service') }}</h4>
      <p style="margin: 0; opacity: 0.9; font-size: 0.875rem;">{{ __('Add a new product or service to your inventory') }}</p>
    </div>
  </div>

  <div style="padding: 2rem; background: white; margin: 0 1.5rem; border-radius: 0 0 12px 12px;">
    <div style="display: flex; flex-direction: column; gap: 1.5rem; max-width: 720px; margin: 0 auto; padding: 1.5rem;">

    {{ Form::open(['url' => 'productservice', 'class'=>'needs-validation','novalidate']) }}

        @if (isset($plan->enable_chatgpt)  && $plan->enable_chatgpt == 'on')
            <div style="text-align: right; margin-bottom: 1rem;">
                <a href="#" data-size="md" data-ajax-popup-over="true"
                    data-url="{{ route('generate', ['product & service']) }}" data-bs-toggle="tooltip"
                    data-bs-placement="top" title="{{ __('Generate') }}" data-title="{{ __('Generate content with AI') }}"
                    class="zameen-btn zameen-btn-primary">
                    <i class="fas fa-robot"></i>
                    {{ __('Generate with AI') }}
                </a>
            </div>
        @endif

        <div class="zameen-form-section">
            <h6 class="zameen-section-title">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: inline; margin-right: 8px;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
                {{ __('Product/Service Information') }}
            </h6>

            <div class="zameen-form-group">
                <label class="zameen-form-label">{{ __('Type') }}</label>
                <div style="display: flex; gap: 2rem; margin-top: 0.5rem;">
                    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                        <input type="radio" class="type" name="type" value="Product" {{ $selectedType == 'product' ? 'checked' : '' }} style="margin: 0;">
                        <span>{{ __('Product') }}</span>
                    </label>
                    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                        <input type="radio" class="type" name="type" value="Service" {{ $selectedType == 'service' ? 'checked' : '' }} style="margin: 0;">
                        <span>{{ __('Service') }}</span>
                    </label>
                </div>
            </div>

            <div class="zameen-form-group material-type-wrap">
                <label class="zameen-form-label">{{ __('Material Classification') }}</label>
                <div style="display: flex; gap: 1.5rem; margin-top: 0.5rem; flex-wrap: wrap;">
                    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                        <input type="radio" name="material_type" value="raw" style="margin: 0;">
                        <span>{{ __('Raw Material') }}</span>
                    </label>
                    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                        <input type="radio" name="material_type" value="finished" style="margin: 0;">
                        <span>{{ __('Finished Product') }}</span>
                    </label>
                    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                        <input type="radio" name="material_type" value="both" style="margin: 0;">
                        <span>{{ __('Both') }}</span>
                    </label>
                </div>
                <small style="color: #6b7280; font-size: 0.875rem;">{{ __('Select material classification (required for Products)') }}</small>
            </div>

            <div class="zameen-form-group">
                <label class="zameen-form-label">
                    {{ __('Product/Service Name') }}
                    <span style="color: #ef4444; margin-left: 4px;">*</span>
                </label>
                {{ Form::text('name', '', [
                    'class' => 'zameen-form-input',
                    'placeholder' => __('Enter product or service name'),
                    'required' => 'required'
                ]) }}
            </div>

            <div class="zameen-form-group">
                <label class="zameen-form-label">
                    {{ __('SKU (Stock Keeping Unit)') }}
                    <span style="color: #ef4444; margin-left: 4px;">*</span>
                </label>
                {{ Form::text('sku', '', [
                    'class' => 'zameen-form-input',
                    'placeholder' => __('Enter unique SKU code'),
                    'required' => 'required'
                ]) }}
            </div>
        </div>

        <div class="zameen-form-section">
            <h6 class="zameen-section-title">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: inline; margin-right: 8px;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                </svg>
                {{ __('Pricing & Accounts') }}
            </h6>

            <div class="zameen-form-group">
                <label class="zameen-form-label">
                    {{ __('Sale Price') }}
                    <span style="color: #ef4444; margin-left: 4px;">*</span>
                </label>
                {{ Form::number('sale_price', '', [
                    'class' => 'zameen-form-input',
                    'placeholder' => __('Enter sale price'),
                    'required' => 'required',
                    'step' => '0.01'
                ]) }}
            </div>

            <div class="zameen-form-group">
                <label class="zameen-form-label">
                    {{ __('Income Account') }}
                    <span style="color: #ef4444; margin-left: 4px;">*</span>
                </label>
                <select name="sale_chartaccount_id" class="zameen-form-input" required="required">
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

            <div class="zameen-form-group">
                <label class="zameen-form-label">
                    {{ __('Purchase Price') }}
                    <span style="color: #ef4444; margin-left: 4px;">*</span>
                </label>
                {{ Form::number('purchase_price', '', [
                    'class' => 'zameen-form-input',
                    'placeholder' => __('Enter purchase price'),
                    'required' => 'required',
                    'step' => '0.01'
                ]) }}
            </div>

            <div class="zameen-form-group">
                <label class="zameen-form-label">
                    {{ __('Expense Account') }}
                    <span style="color: #ef4444; margin-left: 4px;">*</span>
                </label>
                <select name="expense_chartaccount_id" class="zameen-form-input" required="required">
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
        </div>

        <div class="zameen-form-section">
            <h6 class="zameen-section-title">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: inline; margin-right: 8px;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                </svg>
                {{ __('Classification & Tax') }}
            </h6>

            <div class="zameen-form-group">
                <label class="zameen-form-label">
                    {{ __('Category') }}
                    <span style="color: #ef4444; margin-left: 4px;">*</span>
                </label>
                {{ Form::select('category_id', $category, null, ['class' => 'zameen-form-input', 'required' => 'required']) }}
                <div style="margin-top: 0.5rem;">
                    <small style="color: #6b7280;">{{ __('Need to add a new category? ') }}</small>
                    <a href="#" id="add_category" style="color: #00b98d; font-weight: 600; text-decoration: none;">{{ __('Add Category') }}</a>
                </div>
            </div>

            @if($TAX_ENABLED)
            
            <div class="zameen-form-group">
                <label class="zameen-form-label">{{ __('Tax (Optional)') }}</label>
                {{ Form::select('tax_id[]', $tax, null, ['class' => 'zameen-form-input', 'id' => 'choices-multiple1', 'multiple']) }}
                <div style="margin-top: 0.5rem;">
                    <small style="color: #6b7280;">{{ __('Need to add a new tax rate? ') }}</small>
                    <a href="#" onclick="openAddTaxModal()" style="color: #00b98d; font-weight: 600; text-decoration: none;">{{ __('Add Tax') }}</a>
                </div>
            </div>
            @endif

            <div class="zameen-form-group unit-field">
                <label class="zameen-form-label">
                    {{ __('Unit') }}
                    <span style="color: #ef4444; margin-left: 4px;">*</span>
                </label>
                {{ Form::select('unit_id', $unit, null, ['class' => 'zameen-form-input', 'required' => 'required']) }}
                <div style="margin-top: 0.5rem;">
                    <small style="color: #6b7280;">{{ __('Need to add a new unit? ') }}</small>
                    <a href="#" id="add_unit" style="color: #00b98d; font-weight: 600; text-decoration: none;">{{ __('Add Unit') }}</a>
                </div>
            </div>
        </div>

        <div class="zameen-form-section">
            <h6 class="zameen-section-title">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: inline; margin-right: 8px;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
                {{ __('Inventory Details') }}
            </h6>

            <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                
                <div class="zameen-form-group quantity-field" style="flex: 1; min-width: 200px;">
                    <label class="zameen-form-label">
                        {{ __('Quantity') }}
                        <span style="color: #ef4444; margin-left: 4px;">*</span>
                    </label>
                    {{ Form::text('quantity', null, [
                        'class' => 'zameen-form-input',
                        'placeholder' => __('Enter quantity'),
                        'required' => 'required'
                    ]) }}
                </div>

                <div class="zameen-form-group reorder-field" style="flex: 1; min-width: 200px;">
                    <label class="zameen-form-label">
                        {{ __('Reorder Level') }}
                        <span style="color: #ef4444; margin-left: 4px;">*</span>
                    </label>
                    {{ Form::number('reorder_level', null, [
                        'class' => 'zameen-form-input',
                        'placeholder' => __('Enter reorder level'),
                        'required' => 'required'
                    ]) }}
                </div>
            </div>
        </div>

        <div class="zameen-form-section">
            <h6 class="zameen-section-title">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: inline; margin-right: 8px;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path>
                </svg>
                {{ __('Additional Information') }}
            </h6>

            <div class="zameen-form-group">
                <label class="zameen-form-label">{{ __('Description') }}</label>
                {!! Form::textarea('description', null, [
                    'class' => 'zameen-form-input',
                    'rows' => '4',
                    'placeholder' => __('Enter product or service description...')
                ]) !!}
            </div>

            @if (!$customFields->isEmpty())
                <div class="zameen-custom-fields">
                    @include('customFields.formBuilder')
                </div>
            @endif
        </div>
    </div>
  </div>
</div>

<div class="modal-footer" style="background: var(--zameen-background-light); border-top: 1px solid var(--zameen-border-light); padding: 1.5rem 2rem; display: flex; justify-content: flex-end; gap: 1rem;">
  <a href="{{ route('productservice.index') }}" class="zameen-btn zameen-btn-outline">
    {{ __('Cancel') }}
  </a>
  <button type="submit" class="zameen-btn zameen-btn-primary">
    {{ __('Create Product/Service') }}
  </button>
</div>

{{ Form::close() }}

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
                $hex = '#'.$hexNoHash;
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
$(document).on('click', 'input[name="type"]', function () {
    var type = $(this).val();

    if (type === 'Product') {
        $('.quantity-field').show();
        $('.reorder-field').show();
        $('.unit-field').show();
        $('input[name="quantity"]').prop('required', true);
        $('input[name="reorder_level"]').prop('required', true);
        $('select[name="unit_id"]').prop('required', true);

        $('.material-type-wrap').show();
        if (!$('input[name="material_type"]:checked').length) {
            $('#mt_finished').prop('checked', true);
        }
    } else if (type === 'Service') {
        $('.quantity-field').hide();
        $('.reorder-field').hide();
        $('.unit-field').hide();
        $('input[name="quantity"]').val('').prop('required', false);
        $('input[name="reorder_level"]').val('').prop('required', false);
        $('select[name="unit_id"]').val('').prop('required', false);

        $('.material-type-wrap').hide();
        $('input[name="material_type"]').prop('checked', false);
    }
});

$(document).ready(function() {
    var selectedType = $('input[name="type"]:checked').val();
    if (selectedType === 'Service') {
        $('.quantity-field').hide();
        $('.unit-field').hide();
        $('input[name="quantity"]').prop('required', false);
        $('input[name="reorder_level"]').prop('required', false);
        $('select[name="unit_id"]').prop('required', false);

        $('.material-type-wrap').hide();
        $('input[name="material_type"]').prop('checked', false);
    } else {
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
    window.open("{{ route('taxes.index') }}", '_blank');

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
