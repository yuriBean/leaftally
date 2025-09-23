<script src="{{ asset('js/unsaved.js') }}"></script>

@extends('layouts.admin')
@php
    $TAX_ENABLED = \App\Services\Feature::for(\Auth::user())
        ->enabled(\App\Enum\PlanFeature::TAX);
@endphp
@section('page-title')
{{ __('Invoice Create') }}
@endsection
@section('breadcrumb')
<style>
    .raw_material{
        display: none;
    }
</style>
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
<li class="breadcrumb-item"><a href="{{ route('invoice.index') }}">{{ __('Invoice') }}</a></li>
@endsection
@push('script-page')
<script src="{{ asset('js/jquery-ui.min.js') }}"></script>
<script src="{{ asset('js/jquery.repeater.min.js') }}"></script>
<script src="{{ asset('js/jquery-searchbox.js') }}"></script>
<script>
   var selector = "body";
   if ($(selector + " .repeater").length) {
       var $dragAndDrop = $("body .repeater tbody").sortable({
           handle: '.sort-handler'
       });
       var $repeater = $(selector + ' .repeater').repeater({
           initEmpty: false,
           defaultValues: {
               'status': 1
           },
           show: function() {
               $(this).slideDown();
               var file_uploads = $(this).find('input.multi');
               if (file_uploads.length) {
                   $(this).find('input.multi').MultiFile({
                       max: 3,
                       accept: 'png|jpg|jpeg',
                       max_size: 2048
                   });
               }
               JsSearchBox();

           },
           hide: function(deleteElement) {
               if (confirm('Are you sure you want to delete this element?')) {
                   $(this).slideUp(deleteElement);
                   $(this).remove();
   
                   var inputs = $(".amount");
                   var subTotal = 0;
                   for (var i = 0; i < inputs.length; i++) {
                       subTotal = parseFloat(subTotal) + parseFloat($(inputs[i]).html());
                   }
                   $('.subTotal').html(subTotal.toFixed(2));
                   $('.totalAmount').html(subTotal.toFixed(2));
               }
           },
           ready: function(setIndexes) {
   
               $dragAndDrop.on('drop', setIndexes);
           },
           isFirstItemUndeletable: true
       });
       var value = $(selector + " .repeater").attr('data-value');
       if (typeof value != 'undefined' && value.length != 0) {
           value = JSON.parse(value);
           $repeater.setList(value);
       }
   
   }
   
   $(document).on('change', '#customer', function() {
       $('#customer_detail').removeClass('d-none');
       $('#customer_detail').addClass('d-block');
       $('#customer-box').removeClass('d-block');
       $('#customer-box').addClass('d-none');
       var id = $(this).val();
       var url = $(this).data('url');
       $.ajax({
           url: url,
           type: 'POST',
           headers: {
               'X-CSRF-TOKEN': jQuery('#token').val()
           },
           data: {
               'id': id
           },
           cache: false,
           success: function(data) {
               if (data != '') {
                   $('#customer_detail').html(data);
               } else {
                   $('#customer-box').removeClass('d-none');
                   $('#customer-box').addClass('d-block');
                   $('#customer_detail').removeClass('d-block');
                   $('#customer_detail').addClass('d-none');
               }
   
           },
   
       });
   });
   
   $(document).on('click', '#remove', function() {
       $('#customer-box').removeClass('d-none');
       $('#customer-box').addClass('d-block');
       $('#customer_detail').removeClass('d-block');
       $('#customer_detail').addClass('d-none');
   })
   
   $(document).on('change', '.item', function() {
   
       var iteams_id = $(this).val();
       var url = $(this).data('url');
       var el = $(this);
   
       $.ajax({
           url: url,
           type: 'POST',
           headers: {
               'X-CSRF-TOKEN': jQuery('#token').val()
           },
           data: {
               'product_id': iteams_id
           },
           cache: false,
           success: function(data) {
               var item = JSON.parse(data);
               $(el.parent().parent().find('.quantity')).val(1);
               $(el.parent().parent().find('.price')).val(item.product.sale_price);
               $(el.parent().parent().parent().find('.pro_description')).val(item.product
                   .description);
   
               var taxes = '';
               var tax = [];
   
               var totalItemTaxRate = 0;
   
               if (item.taxes == 0) {
                   taxes += '-';
               } else {
                   for (var i = 0; i < item.taxes.length; i++) {
                       taxes += '<span class="badge bg-primary mt-1 mr-2">' + item.taxes[i].name +
                           ' ' + '(' + item.taxes[i].rate + '%)' + '</span>';
                       tax.push(item.taxes[i].id);
                       totalItemTaxRate += parseFloat(item.taxes[i].rate);
                   }
               }
               var itemTaxPrice = parseFloat((totalItemTaxRate / 100)) * parseFloat((item.product
                   .sale_price * 1));
               $(el.parent().parent().find('.itemTaxPrice')).val(itemTaxPrice.toFixed(2));
               $(el.parent().parent().find('.itemTaxRate')).val(totalItemTaxRate.toFixed(2));
               $(el.parent().parent().find('.taxes')).html(taxes);
               $(el.parent().parent().find('.tax')).val(tax);
               $(el.parent().parent().find('.unit')).html(item.unit);
               $(el.parent().parent().find('.discount')).val(0);

               var inputs = $(".amount");
               var subTotal = 0;
               for (var i = 0; i < inputs.length; i++) {
                   subTotal = parseFloat(subTotal) + parseFloat($(inputs[i]).html());
               }

               var totalItemPrice = 0;
               var priceInput = $('.price');
               for (var j = 0; j < priceInput.length; j++) {
                   totalItemPrice += parseFloat(priceInput[j].value);
               }
   
               var totalItemTaxPrice = 0;
               var itemTaxPriceInput = $('.itemTaxPrice');
               for (var j = 0; j < itemTaxPriceInput.length; j++) {
                   totalItemTaxPrice += parseFloat(itemTaxPriceInput[j].value);
                   $(el.parent().parent().find('.amount')).html(parseFloat(item.totalAmount) +
                       parseFloat(itemTaxPriceInput[j].value));
               }
   
               var totalItemDiscountPrice = 0;
               var itemDiscountPriceInput = $('.discount');
   
               for (var k = 0; k < itemDiscountPriceInput.length; k++) {
   
                   totalItemDiscountPrice += parseFloat(itemDiscountPriceInput[k].value);
               }
   
               $('.subTotal').html(totalItemPrice.toFixed(2));
               $('.totalTax').html(totalItemTaxPrice.toFixed(2));
               $('.totalAmount').html((parseFloat(totalItemPrice) - parseFloat(
                   totalItemDiscountPrice) + parseFloat(totalItemTaxPrice)).toFixed(2));
   
           },
       });
   });
   
   $(document).on('keyup', '.quantity', function() {
       var quntityTotalTaxPrice = 0;
   
       var el = $(this).parent().parent().parent().parent();
   
       var quantity = $(this).val();
       var price = $(el.find('.price')).val();
       var discount = $(el.find('.discount')).val();
       if (discount.length <= 0) {
           discount = 0;
       }
   
       var totalItemPrice = (quantity * price) - discount;
   
       var amount = (totalItemPrice);

       var totalItemTaxRate = $(el.find('.itemTaxRate')).val();
       var itemTaxPrice = parseFloat((totalItemTaxRate / 100) * (totalItemPrice));
       $(el.find('.itemTaxPrice')).val(itemTaxPrice.toFixed(2));
   
       $(el.find('.amount')).html(parseFloat(itemTaxPrice) + parseFloat(amount));
   
       var totalItemTaxPrice = 0;
       var itemTaxPriceInput = $('.itemTaxPrice');
       for (var j = 0; j < itemTaxPriceInput.length; j++) {
           totalItemTaxPrice += parseFloat(itemTaxPriceInput[j].value);
       }

       var totalItemPrice = 0;
       var inputs_quantity = $(".quantity");
   
       var priceInput = $('.price');
       for (var j = 0; j < priceInput.length; j++) {
           totalItemPrice += (parseFloat(priceInput[j].value) * parseFloat(inputs_quantity[j].value));
       }
   
       var inputs = $(".amount");
   
       var subTotal = 0;
       for (var i = 0; i < inputs.length; i++) {
           subTotal = parseFloat(subTotal) + parseFloat($(inputs[i]).html());
       }
   
       $('.subTotal').html(totalItemPrice.toFixed(2));
       $('.totalTax').html(totalItemTaxPrice.toFixed(2));
   
       $('.totalAmount').html((parseFloat(subTotal)).toFixed(2));
   
   })
   
   $(document).on('keyup change', '.price', function() {
       var el = $(this).parent().parent().parent().parent();
       var price = $(this).val();
       var quantity = $(el.find('.quantity')).val();
   
       var discount = $(el.find('.discount')).val();
       if (discount.length <= 0) {
           discount = 0;
       }
       var totalItemPrice = (quantity * price) - discount;
   
       var amount = (totalItemPrice);

       var totalItemTaxRate = $(el.find('.itemTaxRate')).val();
       var itemTaxPrice = parseFloat((totalItemTaxRate / 100) * (totalItemPrice));
       $(el.find('.itemTaxPrice')).val(itemTaxPrice.toFixed(2));
   
       $(el.find('.amount')).html(parseFloat(itemTaxPrice) + parseFloat(amount));
   
       var totalItemTaxPrice = 0;
       var itemTaxPriceInput = $('.itemTaxPrice');
       for (var j = 0; j < itemTaxPriceInput.length; j++) {
           totalItemTaxPrice += parseFloat(itemTaxPriceInput[j].value);
       }

       var totalItemPrice = 0;
       var inputs_quantity = $(".quantity");
   
       var priceInput = $('.price');
       for (var j = 0; j < priceInput.length; j++) {
           totalItemPrice += (parseFloat(priceInput[j].value) * parseFloat(inputs_quantity[j].value));
       }
   
       var inputs = $(".amount");
   
       var subTotal = 0;
       for (var i = 0; i < inputs.length; i++) {
           subTotal = parseFloat(subTotal) + parseFloat($(inputs[i]).html());
       }
   
       $('.subTotal').html(totalItemPrice.toFixed(2));
       $('.totalTax').html(totalItemTaxPrice.toFixed(2));
   
       $('.totalAmount').html((parseFloat(subTotal)).toFixed(2));

   })
   
   $(document).on('keyup change', '.discount', function() {
       var el = $(this).parent().parent().parent();
       var discount = $(this).val();
       if (discount.length <= 0) {
           discount = 0;
       }
   
       var price = $(el.find('.price')).val();
       var quantity = $(el.find('.quantity')).val();
       var totalItemPrice = (quantity * price) - discount;

       var amount = (totalItemPrice);

       var totalItemTaxRate = $(el.find('.itemTaxRate')).val();
       var itemTaxPrice = parseFloat((totalItemTaxRate / 100) * (totalItemPrice));
       $(el.find('.itemTaxPrice')).val(itemTaxPrice.toFixed(2));
   
       $(el.find('.amount')).html(parseFloat(itemTaxPrice) + parseFloat(amount));
   
       var totalItemTaxPrice = 0;
       var itemTaxPriceInput = $('.itemTaxPrice');
       for (var j = 0; j < itemTaxPriceInput.length; j++) {
           totalItemTaxPrice += parseFloat(itemTaxPriceInput[j].value);
       }

       var totalItemPrice = 0;
       var inputs_quantity = $(".quantity");
   
       var priceInput = $('.price');
       for (var j = 0; j < priceInput.length; j++) {
           totalItemPrice += (parseFloat(priceInput[j].value) * parseFloat(inputs_quantity[j].value));
       }
   
       var inputs = $(".amount");
   
       var subTotal = 0;
       for (var i = 0; i < inputs.length; i++) {
           subTotal = parseFloat(subTotal) + parseFloat($(inputs[i]).html());
       }

       var totalItemDiscountPrice = 0;
       var itemDiscountPriceInput = $('.discount');
   
       for (var k = 0; k < itemDiscountPriceInput.length; k++) {
   
           totalItemDiscountPrice += parseFloat(itemDiscountPriceInput[k].value);
       }

       $('.subTotal').html(totalItemPrice.toFixed(2));
       $('.totalTax').html(totalItemTaxPrice.toFixed(2));
   
       $('.totalAmount').html((parseFloat(subTotal)).toFixed(2));
       $('.totalDiscount').html(totalItemDiscountPrice.toFixed(2));

   })
   
   $(document).on('change', '.item', function () {
       $('.item option').prop('hidden', false);
       $('.item :selected').each(function () {
           var id = $(this).val();
           $(".item option[value=" + id + "]").prop("hidden", true);
       });
   });
   
   $(document).on('click', '[data-repeater-create]', function () {
       $('.item option').prop('hidden', false);
       $('.item :selected').each(function () {
           var id = $(this).val();
           $(".item option[value=" + id + "]").prop("hidden", true);
       });
   })
   
   var customerId = '{{ $customerId }}';
   if (customerId > 0) {
       $('#customer').val(customerId).change();
   }
</script>
<script>
   $(document).on('click', '[data-repeater-delete]', function() {
       $(".price").change();
       $(".discount").change();
   
       $('.item option').prop('hidden', false);
       $('.item :selected').each(function () {
           var id = $(this).val();
           $(".item option[value=" + id + "]").prop("hidden", true);
       });
   });
   JsSearchBox();
</script>
@endpush
@section('content')
<div class="row">
    {{ Form::open(['url' => 'invoice','class'=>'w-100 needs-validation','novalidate']) }}
    <div class="col-12">
      <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
  
      {{-- HEADER / SHELL --}}
      <div class="card border-0 shadow-sm rounded-3 overflow-hidden mb-4">
        <div class="w-100" style="height:4px;background:#007C38;"></div>
        <div class="card-body p-3 p-md-4">
          <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
            <div class="d-flex align-items-center gap-2">
              <span class="rounded-circle" style="width:10px;height:10px;background:#007C38;"></span>
              <h5 class="mb-0 fw-bold text-2xl">{{ __('Create Invoice') }}</h5>
            </div>
            <div class="text-muted small">{{ __('Select a customer, set details, then add items below.') }}</div>
          </div>
        </div>
      </div>
  
      {{-- MAIN LAYOUT --}}
      <div class="row g-4">
  
        {{-- LEFT: CUSTOMER --}}
        <div class="col-12 col-lg-5">
          <div class="card border-0 shadow-sm rounded-3 h-100 overflow-hidden">
        <div class="w-100" style="height:4px;background:#007C38;"></div>

            <div class="card-body p-3 p-md-4">
              <div class="mb-3" id="customer-box">
                {{ Form::label('customer_id', __('Customer'), ['class' => 'form-label fw-semibold']) }} <x-required/>
                {{ Form::select('customer_id', $customers, $customerId, [
                    'class' => 'form-control select',
                    'id' => 'customer',
                    'data-url' => route('invoice.customer'),
                    'required' => 'required'
                ]) }}
              </div>
  
              <div id="customer_detail" class="d-none"></div>
  
              <div class="d-flex justify-content-end">
                <a href="javascript:void(0)"
                   id="add_customer"
                   class="small fw-semibold text-decoration-none"
                   data-bs-toggle="modal" data-bs-target="#customerModal">
                  + {{ __('Add Customer') }}
                </a>
              </div>
            </div>
          </div>
        </div>
  
        {{-- RIGHT: INVOICE META --}}
        <div class="col-12 col-lg-7">
            <div class="card border-0 shadow-sm rounded-3 h-100 overflow-hidden">
                <div class="w-100" style="height:4px;background:#007C38;"></div>

            <div class="card-body p-3 p-md-4">
              <div class="row g-3">
                <div class="col-12 col-sm-6">
                  {{ Form::label('issue_date', __('Issue Date'), ['class' => 'form-label fw-semibold']) }} <x-required/>
                  {{ Form::date('issue_date', date('Y-m-d'), ['class' => 'form-control','required'=>'required']) }}
                </div>
                <div class="col-12 col-sm-6">
                  {{ Form::label('due_date', __('Due Date'), ['class' => 'form-label fw-semibold']) }} <x-required/>
                  {{ Form::date('due_date', date('Y-m-d'), ['class' => 'form-control','required'=>'required']) }}
                </div>
  
                <div class="col-12 col-sm-6">
                  {{ Form::label('invoice_number', __('Invoice Number'), ['class' => 'form-label fw-semibold']) }}
                  <input type="text" class="form-control bg-light" value="{{ $invoice_number }}" readonly>
                </div>
  
                <div class="col-12 col-sm-6">
                  {{ Form::label('category_id', __('Category'), ['class' => 'form-label fw-semibold']) }} <x-required/>
                  {{ Form::select('category_id', $category, null, ['class'=>'form-control select','required'=>'required']) }}
                  <div class="small mt-1">
                    {{ __('Need to add a new category?') }}
                    <a href="#" id="add_category" class="text-decoration-none fw-semibold" style="color:#007C38">
                      {{ __('Add Category') }}
                    </a>
                  </div>
                </div>
  
                <div class="col-12 col-sm-6">
                  {{ Form::label('ref_number', __('Ref Number'), ['class' => 'form-label fw-semibold']) }}
                  <div class="input-group">
                    <span class="input-group-text bg-white"><i class="ti ti-joint"></i></span>
                    {{ Form::text('ref_number', '', ['class' => 'form-control']) }}
                  </div>
                </div>
  
                @if (!$customFields->isEmpty())
                  <div class="col-12 col-sm-6">
                    <div class="tab-pane fade show" id="tab-2" role="tabpanel">
                      @include('customFields.formBuilder')
                    </div>
                  </div>
                @endif
              </div>
            </div>
          </div>
        </div>
  
        {{-- ITEMS / REPEATER --}}
        <div class="col-12">
          <div class="card repeater border-0 shadow-sm rounded-3 overflow-hidden">
            <div class="w-100" style="height:4px;background:#007C38;"></div>

            {{-- Section Header --}}
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 p-3 p-md-4 border-bottom">
              <h6 class="mb-0 fw-bold d-flex align-items-center gap-2">
                <i class="ti ti-box text-lg "></i> {{ __('Products & Services') }}
              </h6>
              <div class="d-flex align-items-center gap-3">
                <a href="#"
                   data-size="lg"
                   data-url="{{ route('productservice.create-short') }}"
                   data-ajax-popup="true"
                   data-bs-toggle="tooltip"
                   title="{{__('Create Product')}}"
                   data-title="{{__('Create Product & Service')}}">
                  {{ __('Add New Product') }}
                </a>
                <a href="javascript:void(0)" data-repeater-create class="btn btn-primary">
                  <i class="ti ti-plus"></i> {{ __('Add Item') }}
                </a>
              </div>
            </div>
  
            {{-- Table --}}
            <div class="p-3 p-md-4">
              <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" data-repeater-list="items" id="sortable-table">
  
                  <tbody class="ui-sortable" data-repeater-item>
                    <tr>
                      <td colspan="{{ $TAX_ENABLED ? 7 : 6 }}">
                        <div class="border rounded p-3 mb-3 bg-light-subtle">
                          
                          {{-- Item --}}
                          <div class="mb-3">
                            {{ Form::label('item', __('Item'), ['class' => 'form-label fw-semibold']) }}
                            {{ Form::select('item', $product_services, '', [
                                'class' => 'form-control select2 item',
                                'data-url' => route('invoice.product'),
                                'required' => 'required'
                            ]) }}
                          </div>
                  
                          {{-- Quantity --}}
                          <div class="mb-3">
                            {{ Form::label('quantity', __('Quantity'), ['class' => 'form-label fw-semibold']) }}
                            <div class="input-group">
                              {{ Form::text('quantity', '', ['class'=>'form-control quantity','placeholder'=>__('Qty'),'required']) }}
                              <span class="unit input-group-text bg-white"></span>
                            </div>
                          </div>
                  
                          {{-- Price --}}
                          <div class="mb-3">
                            {{ Form::label('price', __('Price'), ['class' => 'form-label fw-semibold']) }}
                            <div class="input-group">
                              {{ Form::text('price', '', ['class'=>'form-control price','placeholder'=>__('Price'),'required']) }}
                              <span class="input-group-text bg-white">{{ \Auth::user()->currencySymbol() }}</span>
                            </div>
                          </div>
                  
                          {{-- Discount --}}
                          <div class="mb-3">
                            {{ Form::label('discount', __('Discount'), ['class' => 'form-label fw-semibold']) }}
                            <div class="input-group">
                              {{ Form::text('discount', '', ['class'=>'form-control discount','placeholder'=>__('Discount')]) }}
                              <span class="input-group-text bg-white">{{ \Auth::user()->currencySymbol() }}</span>
                            </div>
                          </div>
                  
                          {{-- Tax (optional) --}}
                          @if($TAX_ENABLED)
                          <div class="mb-3">
                            {{ Form::label('tax', __('Tax (%)'), ['class' => 'form-label fw-semibold']) }}
                            <div class="input-group">
                              <div class="taxes"></div>
                              {{ Form::hidden('tax', '', ['class'=>'form-control tax text-dark']) }}
                              {{ Form::hidden('itemTaxPrice', '', ['class'=>'form-control itemTaxPrice']) }}
                              {{ Form::hidden('itemTaxRate', '', ['class'=>'form-control itemTaxRate']) }}
                            </div>
                          </div>
                          @endif
                  
                          {{-- Description --}}
                          <div class="mb-3">
                            {{ Form::label('description', __('Description'), ['class' => 'form-label fw-semibold']) }}
                            {{ Form::textarea('description', null, ['class'=>'form-control pro_description','rows'=>2]) }}
                          </div>
                  
                          {{-- Amount + Delete --}}
                          <div class="d-flex justify-content-between align-items-center">
                            <div>
                              <strong>{{ __('Amount') }}:</strong>
                              <span class="amount fw-bold">0.00</span>
                            </div>
                            <div data-repeater-delete>
                              <a href="#" class="btn btn-sm btn-outline-danger">
                                <i class="ti ti-trash"></i> {{ __('Remove') }}
                              </a>
                            </div>
                          </div>
                  
                        </div>
                      </td>
                    </tr>
                  </tbody>

                  <tfoot>
                    <tr>
                      <td colspan="{{ $TAX_ENABLED ? 5 : 4 }}"></td>
                      <td class="text-end">
                        <strong>{{ __('Sub Total') }} ({{ \Auth::user()->currencySymbol() }})</strong>
                      </td>
                      <td class="text-end subTotal">0.00</td>
                    </tr>
                    <tr>
                      <td colspan="{{ $TAX_ENABLED ? 5 : 4 }}"></td>
                      <td class="text-end">
                        <strong>{{ __('Discount') }} ({{ \Auth::user()->currencySymbol() }})</strong>
                      </td>
                      <td class="text-end totalDiscount">0.00</td>
                    </tr>
                    @if($TAX_ENABLED)
                      <tr>
                        <td colspan="5"></td>
                        <td class="text-end">
                          <strong>{{ __('Tax') }} ({{ \Auth::user()->currencySymbol() }})</strong>
                        </td>
                        <td class="text-end totalTax">0.00</td>
                      </tr>
                    @endif
                    <tr class="table-active">
                      <td colspan="{{ $TAX_ENABLED ? 5 : 4 }}"></td>
                      <td class="text-end text-primary">
                        <strong>{{ __('Total Amount') }} ({{ \Auth::user()->currencySymbol() }})</strong>
                      </td>
                      <td class="text-end totalAmount text-primary fw-bold">0.00</td>
                    </tr>
                  </tfoot>
  
                </table>
              </div>
            </div>
          </div>
        </div>
  
      </div> {{-- /MAIN LAYOUT --}}
    </div>
  
    {{-- STICKY ACTIONS --}}
    <div class="modal-footer  position-sticky bottom-0 border-top mt-4 d-flex justify-content-end gap-2">
      <input type="button" value="{{ __('Cancel') }}"
             onclick="location.href = '{{ route('invoice.index') }}';"
             class="btn btn-light">
      <input type="submit" value="{{ __('Create') }}" class="btn btn-primary">
    </div>
  
    {{ Form::close() }}
  </div>
  
  {{-- CREATE CUSTOMER MODAL --}}
  <div class="modal fade" id="customerModal" tabindex="-1" aria-labelledby="productCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <form id="add_customer_form">
          <div class="modal-header">
            <h5 class="modal-title" id="productUnitModalLabel">{{ __('Create Customer') }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Close') }}"></button>
          </div>
  
          <div class="modal-body">
            <div class="row g-3">
              <div class="col-12">
                <div class="form-group">
                  {{ Form::label('name', __('Name'), ['class' => 'form-label']) }} <x-required/>
                  {{ Form::text('name', null, ['class' => 'form-control', 'required' => 'required']) }}
                </div>
              </div>
  
              <x-mobile div-class="col-12" name="contact" label="{{ __('Contact') }}" required></x-mobile>
  
              <div class="col-12">
                <div class="form-group">
                  {{ Form::label('email', __('Email'), ['class' => 'form-label']) }} <x-required/>
                  {{ Form::text('email', null, ['class' => 'form-control', 'required' => 'required']) }}
                </div>
              </div>
            </div>
          </div>
  
          <div class="modal-footer">
            <input type="button" value="{{ __('Cancel') }}" class="btn btn-light" data-bs-dismiss="modal">
            <input type="submit" value="{{ __('Create') }}" class="btn btn-primary">
          </div>
        </form>
      </div>
    </div>
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
            <input type="hidden" name="type" value="income">
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
@endsection
@push('script-page')
<script type="text/javascript">
    $("#add_customer").click(function (e) {
    $(".card").addClass("blurred");
    $("#customerModal").modal("show");
});
$("#customerModal").on("hidden.bs.modal", function () {
    $(".card").removeClass("blurred"); 
});
$("#add_customer_form").submit(function (e) {
    e.preventDefault();
    var csrfToken = $('meta[name="csrf-token"]').attr('content');
    var data = new FormData(this);

    $.ajax({
        url: "{{ route('customer_short') }}",
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
                $("#customerModal").modal("hide");
                $("#add_customer_form")[0].reset(); 
                $("#customer").html(response.options);
                show_toastr("success",response.message);
            } else {
                $("input[type='submit']").attr("disabled",false);
                show_toastr("error",response.message);
            }
        },
        error: function (xhr, status, error) {
            $("input[type='submit']").attr("disabled",false);
            show_toastr("error",error.message);
        }
    });
});
$(document).on("click","#submit_add_product",function(){
    $("#add_product_form").submit();
})
$(document).on('submit',"#add_product_form",function(e){
    e.preventDefault();
     var csrfToken = $('meta[name="csrf-token"]').attr('content');
    var data = new FormData(this);

    $.ajax({
        url: "{{ route('product_short') }}",
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
                show_toastr("success",response.message);
                $("#commonModal").modal("hide");
                $("#add_product_form")[0].reset(); 
                
                $("body .repeater").find(".item").each(function() {
    $(this).append(new Option(response.name, response.id));
    });

            } else {
                $("input[type='submit']").attr("disabled",false);
                show_toastr("error",response.message); 
            }
        },
        error: function (xhr, status, error) {
            $("input[type='submit']").attr("disabled",false);
            show_toastr("error",error.message); 
        }
    });
})

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
(function () {
    function applyTypeUI(type) {
        if (type === 'Product') {
            $('.quantity-field').show();
            $('.reorder-field').show();
            $('.unit-field').show();
            $('.material-type-wrap').show();

            $('input[name="quantity"]').prop('required', true);
            $('input[name="reorder-field"]').prop('required', true);
            $('select[name="unit_id"]').prop('required', true);

            $('input[name="material_type"]').prop('required', true);
            if (!$('input[name="material_type"]:checked').length) {
                $('#mt_finished').prop('checked', true);
            }
        } else {
            $('.quantity-field').hide();
            $('.reorder-field').hide();
            $('.unit-field').hide();
            $('.material-type-wrap').hide();

            $('input[name="quantity"]').val('').prop('required', false);
            $('input[name="reorder_level"]').val('').prop('required', false);
            $('select[name="unit_id"]').val('').prop('required', false);

            $('input[name="material_type"]').prop('checked', false).prop('required', false);
        }
    }
    $(document).ready(function () {
        const selectedType = $('input[name="type"]:checked').val() || 'Product';
        applyTypeUI(selectedType);
    });
    $(document).on('click', 'input[name="type"]', function () {
        applyTypeUI($(this).val());
    });

})();
</script>
@endpush