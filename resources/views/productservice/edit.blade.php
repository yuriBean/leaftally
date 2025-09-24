<script src="{{ asset('js/unsaved.js') }}"></script>

@php
    $TAX_ENABLED = \App\Services\Feature::for(\Auth::user())->enabled(\App\Enum\PlanFeature::TAX);
    $plan = \App\Models\Utility::getChatGPTSettings();
@endphp

<style>
/* Green theme styling for edit product/service form */
.modal-body .form-control:focus,
.modal-body .form-select:focus {
    border-color: #007C38 !important;
    box-shadow: 0 0 0 0.2rem rgba(0, 124, 56, 0.25) !important;
}

.modal-body .form-control:focus-visible,
.modal-body .form-select:focus-visible {
    border-color: #007C38 !important;
    box-shadow: 0 0 0 0.2rem rgba(0, 124, 56, 0.25) !important;
}

.modal-body .form-control,
.modal-body .form-select {
    border-color: #dee2e6;
    transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
}

.modal-body .form-control:hover,
.modal-body .form-select:hover {
    border-color: #007C38;
}

.modal-body .btn-primary {
    background-color: #007C38;
    border-color: #007C38;
}

.modal-body .btn-primary:hover {
    background-color: #006b30;
    border-color: #006b30;
}
</style>

{{ Form::model($productService, ['route' => ['productservice.update', $productService->id], 'method' => 'PUT', 'class'=>'needs-validation','novalidate']) }}
<div class="modal-body">
    <div class="row">
        @if (isset($plan->enable_chatgpt) && $plan->enable_chatgpt == 'on')
            <div class="mb-3">
                <a href="#" data-size="md" data-ajax-popup-over="true"
                    data-url="{{ route('generate', ['product & service']) }}" data-bs-toggle="tooltip"
                    data-bs-placement="top" title="{{ __('Generate') }}" data-title="{{ __('Generate content with AI') }}"
                    class="btn btn-primary btn-sm float-end">
                    <i class="fas fa-robot"></i>
                    {{ __('Generate with AI') }}
                </a>
            </div>
        @endif

        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('name', __('Name'), ['class' => 'form-label']) }}<x-required></x-required>
                <div class="form-icon-user">
                    {{ Form::text('name', null, ['class' => 'form-control', 'required' => 'required']) }}
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('sku', __('SKU'), ['class' => 'form-label']) }}<x-required></x-required>
                <div class="form-icon-user">
                    {{ Form::text('sku', null, ['class' => 'form-control', 'required' => 'required']) }}
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('sale_price', __('Sale Price'), ['class' => 'form-label']) }}<x-required></x-required>
                <div class="form-icon-user">
                    {{ Form::number('sale_price', null, ['class' => 'form-control', 'required' => 'required', 'step' => '0.01']) }}
                </div>
            </div>
        </div>

        <div class="form-group col-md-6">
            {{ Form::label('sale_chartaccount_id', __('Income Account'),['class'=>'form-label']) }}<x-required></x-required>
            <select name="sale_chartaccount_id" class="form-control" required="required">
                @foreach ($incomeChartAccounts as $key => $chartAccount)
                    <option value="{{ $key }}" class="subAccount" {{ ($productService->sale_chartaccount_id == $key) ? 'selected' : ''}}>
                        {{ $chartAccount }}
                    </option>
                    @foreach ($incomeSubAccounts as $subAccount)
                        @if ($key == $subAccount['account'])
                            <option value="{{ $subAccount['id'] }}" class="ms-5" {{ ($productService->sale_chartaccount_id == $subAccount['id']) ? 'selected' : ''}}>
                                &nbsp;&nbsp;&nbsp;{{ $subAccount['name'] }}
                            </option>
                        @endif
                    @endforeach
                @endforeach
            </select>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('purchase_price', __('Purchase Price'), ['class' => 'form-label']) }}<x-required></x-required>
                <div class="form-icon-user">
                    {{ Form::number('purchase_price', null, ['class' => 'form-control', 'required' => 'required', 'step' => '0.01']) }}
                </div>
            </div>
        </div>

        <div class="form-group col-md-6">
            {{ Form::label('expense_chartaccount_id', __('Expense Account'),['class'=>'form-label']) }}<x-required></x-required>
            <select name="expense_chartaccount_id" class="form-control" required="required">
                @foreach ($expenseChartAccounts as $key => $chartAccount)
                    <option value="{{ $key }}" class="subAccount" {{ ($productService->expense_chartaccount_id == $key) ? 'selected' : ''}}>
                        {{ $chartAccount }}
                    </option>
                    @foreach ($expenseSubAccounts as $subAccount)
                        @if ($key == $subAccount['account'])
                            <option value="{{ $subAccount['id'] }}" class="ms-5" {{ ($productService->expense_chartaccount_id == $subAccount['id']) ? 'selected' : ''}}>
                                &nbsp;&nbsp;&nbsp;{{ $subAccount['name'] }}
                            </option>
                        @endif
                    @endforeach
                @endforeach
            </select>
        </div>

        @if($TAX_ENABLED)
            <div class="form-group col-md-6">
                {{ Form::label('tax_id', __('Tax'), ['class' => 'form-label']) }}<x-required></x-required>
                {{ Form::select('tax_id[]', $tax, $productService->tax_id ?? null, ['class' => 'form-control select2', 'id' => 'choices-multiple1', 'multiple' => '']) }}
            </div>
        @endif

        <div class="form-group col-md-6">
            {{ Form::label('category_id', __('Category'), ['class' => 'form-label']) }}<x-required></x-required>
            {{ Form::select('category_id', $category, null, ['class' => 'form-control select', 'required' => 'required']) }}
        </div>

        <div class="form-group col-md-6">
            {{ Form::label('unit_id', __('Unit'), ['class' => 'form-label']) }}<x-required></x-required>
            {{ Form::select('unit_id', $unit, null, ['class' => 'form-control select', 'required' => 'required']) }}
        </div>

        <div class="form-group col-md-3 quantity {{ $productService->type=='Service' ? 'd-none' : 'd-block' }}">
            {{ Form::label('quantity', __('Quantity'), ['class' => 'form-label']) }}<x-required></x-required>
            {{ Form::text('quantity', null, ['class' => 'form-control', 'required' => $productService->type=='Service' ? false : true]) }}
        </div>
        <div class="form-group col-md-3 reorder {{ $productService->type=='Service' ? 'd-none' : 'd-block' }}">
            {{ Form::label('reorder_level', __('Reorder Level'), ['class' => 'form-label']) }}<x-required></x-required>
            {{ Form::text('reorder_level', null, ['class' => 'form-control', 'required' => $productService->type=='Service' ? false : true]) }}
        </div>

        <div class="col-md-6">
            <div class="form-group">
                <label class="d-block form-label">{{ __('Type') }}</label>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-check form-check-inline">
                            <input type="radio" class="form-check-input type" id="type_product_e" name="type"
                                   value="Product" @if ($productService->type == 'Product') checked @endif>
                            <label class="custom-control-label form-label" for="type_product_e">{{ __('Product') }}</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-check form-check-inline">
                            <input type="radio" class="form-check-input type" id="type_service_e" name="type"
                                   value="Service" @if ($productService->type == 'Service') checked @endif>
                            <label class="custom-control-label form-label" for="type_service_e">{{ __('Service') }}</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-12 mb-3 material-type-wrap {{ $productService->type=='Service' ? 'd-none' : '' }}">
            <div class="form-group">
                <label class="d-block form-label">{{ __('Material') }}</label>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-check form-check-inline">
                            <input type="radio" class="form-check-input" id="mt_raw_e" name="material_type" value="raw"
                                   {{ $productService->material_type === 'raw' ? 'checked' : '' }}>
                            <label class="custom-control-label form-label" for="mt_raw_e">{{ __('Raw material') }}</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-check form-check-inline">
                            <input type="radio" class="form-check-input" id="mt_finished_e" name="material_type" value="finished"
                                   {{ $productService->material_type === 'finished' ? 'checked' : '' }}>
                            <label class="custom-control-label form-label" for="mt_finished_e">{{ __('Finished product') }}</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-check form-check-inline">
                            <input type="radio" class="form-check-input" id="mt_both_e" name="material_type" value="both"
                                   {{ $productService->material_type === 'both' ? 'checked' : '' }}>
                            <label class="custom-control-label form-label" for="mt_both_e">{{ __('Both') }}</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group col-md-12">
            {{ Form::label('description', __('Description'), ['class' => 'form-label']) }}
            {!! Form::textarea('description', null, ['class' => 'form-control', 'rows' => '2']) !!}
        </div>

        @if (!empty($customFields) && $customFields->isNotEmpty())
            <div class="col-md-6">
                <div class="tab-pane fade show" id="tab-2" role="tabpanel">
                    @include('customFields.formBuilder')
                </div>
            </div>
        @endif
    </div>
</div>
<div class="modal-footer">
    <input type="button" value="{{ __('Cancel') }}" class="btn btn-light" data-bs-dismiss="modal">
    <input type="submit" value="{{ __('Update') }}" class="btn btn-primary">
</div>
{{ Form::close() }}

<script>
$(document).on('click', 'input[name="type"]', function () {
    var type = $(this).val();
    if (type === 'Product') {
        $('.quantity').removeClass('d-none').addClass('d-block');
        $('.reorder').removeClass('d-none').addClass('d-block');
        $('input[name="quantity"]').prop('required', true);
        $('input[name="reorder_level"]').prop('required', true);

        $('.material-type-wrap').removeClass('d-none');
        if (!$('input[name="material_type"]:checked').length) {
            $('#mt_finished_e').prop('checked', true);
        }
    } else {
        $('.quantity').addClass('d-none').removeClass('d-block');
        $('.reorder').addClass('d-none').removeClass('d-block');
        $('input[name="quantity"]').val('').prop('required', false);
        $('input[name="reorder_level"]').val('').prop('required', false);

        $('.material-type-wrap').addClass('d-none');
        $('input[name="material_type"]').prop('checked', false);
    }
});
</script>
