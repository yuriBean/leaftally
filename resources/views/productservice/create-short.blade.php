@php
    $TAX_ENABLED = \App\Services\Feature::for(\Auth::user())
        ->enabled(\App\Enum\PlanFeature::TAX);
    $plan = \App\Models\Utility::getChatGPTSettings();
@endphp

<style>
/* Green theme styling for create service form */
.modal-body .form-control:focus,
.modal-body .form-select:focus {
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

<div class="modal-header" style="background: linear-gradient(90deg, #2e7d32 0%, #43a047 100%); color: #fff; border-top-left-radius: 8px; border-top-right-radius: 8px; padding: 18px 24px; margin-bottom: 0;">
    <h5 class="modal-title" style="margin: 0; font-weight: 600; font-size: 1.15rem;">Create Product & Service</h5>
</div>
<div class="modal-body">
    @if (isset($plan->enable_chatgpt) && $plan->enable_chatgpt == 'on')
        <div class="text-end mb-3">
            <a href="#" data-size="md" data-ajax-popup-over="true"
               data-url="{{ route('generate', ['product & service']) }}" data-bs-toggle="tooltip"
               data-bs-placement="top" title="{{ __('Generate') }}" data-title="{{ __('Generate content with AI') }}"
               class="btn btn-primary">
                <i class="fas fa-robot"></i>
                {{ __('Generate with AI') }}
            </a>
        </div>
    @endif

    <div class="row">
                <form id="addProductForm" method="POST" action="{{ route('productservice.store') }}">
                    @csrf
                    <div class="form-group mb-2">
                        <label class="d-block mb-1">Type</label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="type" id="type_product" value="product" checked>
                            <label class="form-check-label" for="type_product">Product</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="type" id="type_service" value="service">
                            <label class="form-check-label" for="type_service">Service</label>
                        </div>
                    </div>

                    <div class="form-group mb-2">
                        <label class="d-block mb-1">Material Classification</label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="material_classification" id="mc_raw" value="raw">
                            <label class="form-check-label" for="mc_raw">Raw Material</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="material_classification" id="mc_finished" value="finished">
                            <label class="form-check-label" for="mc_finished">Finished Product</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="material_classification" id="mc_both" value="both">
                            <label class="form-check-label" for="mc_both">Both</label>
                        </div>
                        <div class="form-text text-muted" style="font-size: 13px;">Select material classification (required for Products)</div>
                    </div>
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="sku">SKU</label>
                        <input type="text" class="form-control" id="sku" name="sku">
                    </div>
                    <div class="form-group">
                        <label for="price">Price</label>
                        <input type="number" class="form-control" id="price" name="price" required>
                    </div>
                    <div class="form-group">
                        <label for="quantity">Quantity</label>
                        <input type="number" class="form-control" id="quantity" name="quantity">
                    </div>
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea class="form-control" id="description" name="description"></textarea>
                    </div>
                </form>
                <!-- Removed duplicate material classification radio buttons -->
        </div>

        <div class="form-group col-md-6">
            {{ Form::label('sale_price', __('Sale Price'), ['class' => 'form-label']) }}<x-required></x-required>
            {{ Form::number('sale_price', '', ['class' => 'form-control', 'required' => true, 'step' => '0.01']) }}
        </div>

        <div class="form-group col-md-6">
            {{ Form::label('sale_chartaccount_id', __('Income Account'), ['class'=>'form-label']) }}<x-required></x-required>
            <select name="sale_chartaccount_id" class="form-control" required>
                @foreach ($incomeChartAccounts as $key => $chartAccount)
                    <option value="{{ $key }}" class="subAccount">{{ $chartAccount }}</option>
                    @foreach ($incomeSubAccounts as $subAccount)
                        @if ($key == $subAccount['account'])
                            <option value="{{ $subAccount['id'] }}" class="ms-5">&nbsp;&nbsp;&nbsp;{{ $subAccount['name'] }}</option>
                        @endif
                    @endforeach
                @endforeach
            </select>
        </div>

        <div class="form-group col-md-6">
            {{ Form::label('purchase_price', __('Purchase Price'), ['class' => 'form-label']) }}<x-required></x-required>
            {{ Form::number('purchase_price', '', ['class' => 'form-control', 'required' => true, 'step' => '0.01']) }}
        </div>

        <div class="form-group col-md-6">
            {{ Form::label('expense_chartaccount_id', __('Expense Account'), ['class'=>'form-label']) }}<x-required></x-required>
            <select name="expense_chartaccount_id" class="form-control" required>
                @foreach ($expenseChartAccounts as $key => $chartAccount)
                    <option value="{{ $key }}" class="subAccount">{{ $chartAccount }}</option>
                    @foreach ($expenseSubAccounts as $subAccount)
                        @if ($key == $subAccount['account'])
                            <option value="{{ $subAccount['id'] }}" class="ms-5">&nbsp;&nbsp;&nbsp;{{ $subAccount['name'] }}</option>
                        @endif
                    @endforeach
                @endforeach
            </select>
        </div>

        @if ($TAX_ENABLED)
            <div class="form-group col-md-6">
                {{ Form::label('tax_id', __('Tax (Optional)'), ['class' => 'form-label']) }}
                {{ Form::select('tax_id[]', $tax, null, ['class' => 'form-control select2', 'id' => 'choices-multiple1', 'multiple']) }}
            </div>
        @endif

        <div class="form-group col-md-6">
            {{ Form::label('category_id', __('Category'), ['class' => 'form-label']) }}<x-required></x-required>
            {{ Form::select('category_id', $category, null, ['class' => 'form-control select', 'required' => true, 'id' => 'category_id']) }}
            <div class="text-xs mt-2">
                {{ __('Need to add a new category? ') }}<a href="#" id="add_category" class="text-primary font-semibold">{{ __('Add Category') }}</a>
            </div>
        </div>

        <div class="form-group col-md-6 unit-field">
            {{ Form::label('unit_id', __('Unit'), ['class' => 'form-label']) }}<x-required></x-required>
            {{ Form::select('unit_id', $unit, null, ['class' => 'form-control select', 'required' => true, 'id' => 'unit_id']) }}
        </div>

        <div class="form-group col-md-3 quantity-field">
            {{ Form::label('quantity', __('Quantity'), ['class' => 'form-label']) }}<x-required></x-required>
            {{ Form::text('quantity', null, ['class' => 'form-control', 'required' => true]) }}
        </div>

        <div class="form-group col-md-3 reorder-field">
            {{ Form::label('reorder_level', __('Reorder Level'), ['class' => 'form-label']) }}
            {{ Form::number('reorder_level', null, ['class' => 'form-control', 'min' => 0]) }}
        </div>

        <div class="form-group col-md-12">
            {{ Form::label('description', __('Description'), ['class' => 'form-label']) }}
            {!! Form::textarea('description', null, ['class' => 'form-control', 'rows' => 2]) !!}
        </div>

        @if (!empty($customFields) && !$customFields->isEmpty())
            <div class="col-lg-12 col-md-12 col-sm-12">
                <div class="tab-pane fade show" id="tab-2" role="tabpanel">
                    @include('customFields.formBuilder')
                </div>
            </div>
        @endif

    {{ Form::close() }}
    </div>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
    <button type="button" class="btn btn-primary" id="submit_add_product">{{ __('Create') }}</button>
</div>
