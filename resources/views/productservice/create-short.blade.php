
@php
    $TAX_ENABLED = \App\Services\Feature::for(\Auth::user())
        ->enabled(\App\Enum\PlanFeature::TAX);

    $plan = \App\Models\Utility::getChatGPTSettings();

    $selectedType = request('type', 'product');
@endphp

<div class="modal-body" style="background: var(--zameen-background-section); padding: 1.5rem;">
  
  <div style="background: linear-gradient(135deg, #00b98d 0%, #00d4a3 100%); padding: 1.75rem 2rem 1.25rem; border-radius: 12px 12px 0 0; margin: 0 1.5rem;">
    <div style="color: white; margin-bottom: 0.5rem;">
      <h4 style="margin: 0; font-weight: 600; font-size: 1.5rem; color: white;">{{ __('Quick Add Product/Service') }}</h4>
      <p style="margin: 0; opacity: 0.9; font-size: 0.875rem;">{{ __('Add a new product or service quickly') }}</p>
    </div>
  </div>

  <div style="padding: 2rem; background: white; margin: 0 1.5rem; border-radius: 0 0 12px 12px;">
    <div style="display: flex; flex-direction: column; gap: 1.5rem; max-width: 720px; margin: 0 auto; padding: 1.5rem;">

    {{ Form::open(['id' => 'add_product_form', 'class' => 'needs-validation', 'novalidate' => true]) }}

        @if (isset($plan->enable_chatgpt) && $plan->enable_chatgpt == 'on')
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
                        <input type="radio" class="type" name="type" value="Product" {{ $selectedType === 'product' ? 'checked' : '' }} style="margin: 0;">
                        <span>{{ __('Product') }}</span>
                    </label>
                    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                        <input type="radio" class="type" name="type" value="Service" {{ $selectedType === 'service' ? 'checked' : '' }} style="margin: 0;">
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
                        <input type="radio" name="material_type" value="both" checked style="margin: 0;">
                        <span>{{ __('Both') }}</span>
                    </label>
                </div>
                <small style="color: #6b7280; font-size: 0.875rem;">{{ __('Required for Products only') }}</small>
            </div>

            <div class="zameen-form-group">
                <label class="zameen-form-label">
                    {{ __('Product/Service Name') }}
                    <span style="color: #ef4444; margin-left: 4px;">*</span>
                </label>
                {{ Form::text('name', '', [
                    'class' => 'zameen-form-input',
                    'placeholder' => __('Enter product or service name'),
                    'required' => true
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
                    'required' => true
                ]) }}
            </div>

            <div class="zameen-form-group">
                <label class="zameen-form-label">
                    {{ __('Sale Price') }}
                    <span style="color: #ef4444; margin-left: 4px;">*</span>
                </label>
                {{ Form::number('sale_price', '', [
                    'class' => 'zameen-form-input',
                    'placeholder' => __('Enter sale price'),
                    'required' => true,
                    'step' => '0.01'
                ]) }}
            </div>

            <div class="zameen-form-group">
                <label class="zameen-form-label">
                    {{ __('Income Account') }}
                    <span style="color: #ef4444; margin-left: 4px;">*</span>
                </label>
                <select name="sale_chartaccount_id" class="zameen-form-input" required>
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
                    'required' => true,
                    'step' => '0.01'
                ]) }}
            </div>

            <div class="zameen-form-group">
                <label class="zameen-form-label">
                    {{ __('Expense Account') }}
                    <span style="color: #ef4444; margin-left: 4px;">*</span>
                </label>
                <select name="expense_chartaccount_id" class="zameen-form-input" required>
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
            
            <div class="zameen-form-group">
                <label class="zameen-form-label">{{ __('Tax (Optional)') }}</label>
                {{ Form::select('tax_id[]', $tax, null, ['class' => 'zameen-form-input', 'multiple']) }}
            </div>
            @endif

            <div class="zameen-form-group">
                <label class="zameen-form-label">
                    {{ __('Category') }}
                    <span style="color: #ef4444; margin-left: 4px;">*</span>
                </label>
                {{ Form::select('category_id', $category, null, ['class' => 'zameen-form-input', 'required' => true]) }}
            </div>

            <div class="zameen-form-group unit-field">
                <label class="zameen-form-label">
                    {{ __('Unit') }}
                    <span style="color: #ef4444; margin-left: 4px;">*</span>
                </label>
                {{ Form::select('unit_id', $unit, null, ['class' => 'zameen-form-input', 'required' => true]) }}
            </div>

            <div class="zameen-form-group quantity-field">
                <label class="zameen-form-label">
                    {{ __('Quantity') }}
                    <span style="color: #ef4444; margin-left: 4px;">*</span>
                </label>
                {{ Form::text('quantity', null, [
                    'class' => 'zameen-form-input',
                    'placeholder' => __('Enter quantity'),
                    'required' => true
                ]) }}
            </div>

            <div class="zameen-form-group reorder-field">
                <label class="zameen-form-label">
                    {{ __('Reorder Level') }}
                    <span style="color: #ef4444; margin-left: 4px;">*</span>
                </label>
                {{ Form::number('reorder_level', null, [
                    'class' => 'zameen-form-input',
                    'placeholder' => __('Enter reorder level'),
                    'required' => true
                ]) }}
            </div>

            @if (!$customFields->isEmpty())
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
  <button type="button" class="zameen-btn zameen-btn-outline" data-bs-dismiss="modal">
    {{ __('Cancel') }}
  </button>
  <button type="button" class="zameen-btn zameen-btn-primary" id="submit_add_product">
    {{ __('Create Product/Service') }}
  </button>
</div>

{{ Form::close() }}
            @endif
        </div>
    </div>
  </div>
</div>
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

