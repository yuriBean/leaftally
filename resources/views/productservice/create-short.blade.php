
@php
    // If you need TAX toggle inside modal
    $TAX_ENABLED = \App\Services\Feature::for(\Auth::user())
        ->enabled(\App\Enum\PlanFeature::TAX);

    // Optional plan for AI button if you use it inside short modals
    $plan = \App\Models\Utility::getChatGPTSettings();

    // Default type if you open modal with ?type=... (fallback to product)
    $selectedType = request('type', 'product');
@endphp

<div class="modal-body">
    {{-- NOTE: no @extends / @section here; this is modal content only --}}
    {{ Form::open(['id' => 'add_product_form', 'class' => 'needs-validation', 'novalidate' => true]) }}

    <div class="row">
        @if (isset($plan->enable_chatgpt) && $plan->enable_chatgpt == 'on')
            <div class="mb-2">
                <a href="#" data-size="md" data-ajax-popup-over="true"
                   data-url="{{ route('generate', ['product & service']) }}" data-bs-toggle="tooltip"
                   data-bs-placement="top" title="{{ __('Generate') }}" data-title="{{ __('Generate content with AI') }}"
                   class="ml-auto bg-[#007C38] text-white text-sm font-medium px-3 py-1.5 rounded-md hover:bg-[#005f2a] transition">
                    <i class="fas fa-robot"></i>
                    {{ __('Generate with AI') }}
                </a>
            </div>
        @endif

        {{-- Product / Service --}}
        <div class="col-md-12 mb-2">
            <div class="form-group">
                <label class="d-block form-label">{{ __('Type') }}</label>
                <div class="d-flex gap-4">
                    <label class="form-check form-check-inline">
                        <input type="radio" class="form-check-input type" name="type" value="Product"
                               {{ $selectedType === 'product' ? 'checked' : '' }}>
                        <span class="form-label">{{ __('Product') }}</span>
                    </label>
                    <label class="form-check form-check-inline">
                        <input type="radio" class="form-check-input type" name="type" value="Service"
                               {{ $selectedType === 'service' ? 'checked' : '' }}>
                        <span class="form-label">{{ __('Service') }}</span>
                    </label>
                </div>
            </div>
        </div>

        {{-- NEW: Material classification (only for Product) --}}
        <div class="col-md-12 mb-2 material-type-wrap">
            <div class="form-group">
                <label class="d-block form-label">{{ __('Material') }}</label>
                <div class="row">
                    <div class="col-md-4 raw_material">
                        <label class="form-check form-check-inline">
                            <input type="radio" class="form-check-input" id="mt_raw" name="material_type" value="raw">
                            <span class="form-label">{{ __('Raw material') }}</span>
                        </label>
                    </div>
                    <div class="col-md-4 finished_product">
                        <label class="form-check form-check-inline">
                            <input type="radio" class="form-check-input " id="mt_finished" name="material_type" value="finished">
                            <span class="form-label">{{ __('Finished product') }}</span>
                        </label>
                    </div>
                    <div class="col-md-4">
                        <label class="form-check form-check-inline">
                            <input type="radio" class="form-check-input" id="mt_both" name="material_type" value="both" checked="true">
                            <span class="form-label">{{ __('Both') }}</span>
                        </label>
                    </div>
                </div>
                <small class="text-muted">{{ __('Required for Products only.') }}</small>
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('name', __('Name'), ['class' => 'form-label']) }}<x-required></x-required>
                {{ Form::text('name', '', ['class' => 'form-control', 'required' => true]) }}
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('sku', __('SKU'), ['class' => 'form-label']) }}<x-required></x-required>
                {{ Form::text('sku', '', ['class' => 'form-control', 'required' => true]) }}
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('sale_price', __('Sale Price'), ['class' => 'form-label']) }}<x-required></x-required>
                {{ Form::number('sale_price', '', ['class' => 'form-control', 'required' => true, 'step' => '0.01']) }}
            </div>
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

        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('purchase_price', __('Purchase Price'), ['class' => 'form-label']) }}<x-required></x-required>
                {{ Form::number('purchase_price', '', ['class' => 'form-control', 'required' => true, 'step' => '0.01']) }}
            </div>
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
                <div class="text-xs mt-2">
                    {{ __('Need to add a new tax rate? ') }}<a href="#" onclick="openAddTaxModal()" class="text-[#007C38] font-semibold">{{ __('Add Tax') }}</a>
                </div>
            </div>
        @endif

        <div class="form-group col-md-6">
            {{ Form::label('category_id', __('Category'), ['class' => 'form-label']) }}<x-required></x-required>
            {{ Form::select('category_id', $category, null, ['class' => 'form-control select', 'required' => true, 'id' => 'category_id']) }}
        </div>

        <div class="form-group col-md-6 unit-field">
            {{ Form::label('unit_id', __('Unit'), ['class' => 'form-label']) }}<x-required></x-required>
            {{ Form::select('unit_id', $unit, null, ['class' => 'form-control select', 'required' => true, 'id' => 'unit_id']) }}
        </div>

        {{-- Quantity (Product only) --}}
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
            <div class="col-lg-6 col-md-6 col-sm-6">
                <div class="tab-pane fade show" id="tab-2" role="tabpanel">
                    @include('customFields.formBuilder')
                </div>
            </div>
        @endif

    </div> {{-- /.row --}}
    {{ Form::close() }}
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
    <button type="button" class="btn btn-primary" id="submit_add_product">{{ __('Create') }}</button>
</div>



