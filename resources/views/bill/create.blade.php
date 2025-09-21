<script src="{{ asset('js/unsaved.js') }}"></script>

@extends('layouts.admin')
@php
    $TAX_ENABLED = \App\Services\Feature::for(\Auth::user())
        ->enabled(\App\Enum\PlanFeature::TAX);
@endphp
@section('page-title')
{{ __('Bill Create') }}
@endsection

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
<li class="breadcrumb-item"><a href="{{ route('bill.index') }}">{{ __('Bill') }}</a></li>
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
   
               // for item SearchBox ( this function is  custom Js )
               JsSearchBox();
   
               $('.select2').select2();
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
   
   $(document).on('change', '#vender', function() {
       $('#vender_detail').removeClass('d-none');
       $('#vender_detail').addClass('d-block');
       $('#vender-box').removeClass('d-block');
       $('#vender-box').addClass('d-none');
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
                   $('#vender_detail').html(data);
               } else {
                   $('#vender-box').removeClass('d-none');
                   $('#vender-box').addClass('d-block');
                   $('#vender_detail').removeClass('d-block');
                   $('#vender_detail').addClass('d-none');
               }
           },
       });
   });
   
   $(document).on('click', '#remove', function() {
       $('#vender-box').removeClass('d-none');
       $('#vender-box').addClass('d-block');
       $('#vender_detail').removeClass('d-block');
       $('#vender_detail').addClass('d-none');
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
               // console.log(item)
   
               $(el.parent().parent().find('.quantity')).val(1);
               $(el.parent().parent().find('.price')).val(item.product.purchase_price);
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
               var itemTaxPrice = parseFloat((totalItemTaxRate / 100) * (item.product
                   .purchase_price * 1));
   
               $(el.parent().parent().find('.itemTaxPrice')).val(itemTaxPrice.toFixed(2));
               $(el.parent().parent().find('.itemTaxRate')).val(totalItemTaxRate.toFixed(2));
               $(el.parent().parent().find('.taxes')).html(taxes);
               $(el.parent().parent().find('.tax')).val(tax);
               $(el.parent().parent().find('.unit')).html(item.unit);
               $(el.parent().parent().find('.discount')).val(0);
               // $(el.parent().parent().find('.amount')).html(item.totalAmount);
   
   
               var inputs = $(".amount");
               var subTotal = 0;
               for (var i = 0; i < inputs.length; i++) {
                   subTotal = parseFloat(subTotal) + parseFloat($(inputs[i]).html());
               }
   
   
               var accountinputs = $(".accountamount");
               var accountSubTotal = 0;
               for (var i = 0; i < accountinputs.length; i++) {
                   var currentInputValue = parseFloat(accountinputs[i].innerHTML);
                   if (!isNaN(currentInputValue)) {
                       accountSubTotal += currentInputValue;
                   }
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
   
   
               $('.subTotal').html((totalItemPrice + accountSubTotal).toFixed(2));
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
   
   
       // var totalAccount = 0;
       // var accountInput = $('.accountAmount');
       // for (var j = 0; j < accountInput.length; j++) {
       //     if(typeof accountInput[j].value != 'undefined')
       //     {
       //         var accountInputPrice = accountInput[j].value;
       //     }
       //     else {
       //        var accountInputPrice = 0;
       //     }
       //     totalAccount += (parseFloat(accountInputPrice));
       // }
   
       var totalAccount = 0;
       var accountInput = $('.accountAmount');
   
       for (var j = 0; j < accountInput.length; j++) {
           if (typeof accountInput[j].value != 'undefined') {
               var accountInputPrice = parseFloat(accountInput[j].value);
   
               if (isNaN(accountInputPrice)) {
                   totalAccount = 0;
               } else {
                   totalAccount += accountInputPrice;
               }
           }
       }
   
   
   
       var inputs = $(".amount");
       var subTotal = 0;
       for (var i = 0; i < inputs.length; i++) {
           subTotal = parseFloat(subTotal) + parseFloat($(inputs[i]).html());
       }
   
       console.log(totalAccount)
   
       var sumAmount = totalItemPrice + totalAccount;
   
       $('.subTotal').html((sumAmount).toFixed(2));
       $('.totalTax').html(totalItemTaxPrice.toFixed(2));
       $('.totalAmount').html((parseFloat(subTotal) + totalAccount).toFixed(2));
   
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
   
   
       var totalAccount = 0;
       var accountInput = $('.accountAmount');
   
       for (var j = 0; j < accountInput.length; j++) {
           if (typeof accountInput[j].value != 'undefined') {
               var accountInputPrice = parseFloat(accountInput[j].value);
   
               if (isNaN(accountInputPrice)) {
                   totalAccount = 0;
               } else {
                   totalAccount += accountInputPrice;
               }
           }
       }
   
       var inputs = $(".amount");
       var subTotal = 0;
       for (var i = 0; i < inputs.length; i++) {
           subTotal = parseFloat(subTotal) + parseFloat($(inputs[i]).html());
       }
   
   
       $('.subTotal').html((totalItemPrice + totalAccount).toFixed(2));
       $('.totalTax').html(totalItemTaxPrice.toFixed(2));
       $('.totalAmount').html((parseFloat(subTotal) + totalAccount).toFixed(2));
   
   
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
   
       var totalAccount = 0;
       var accountInput = $('.accountAmount');
   
       for (var j = 0; j < accountInput.length; j++) {
           if (typeof accountInput[j].value != 'undefined') {
               var accountInputPrice = parseFloat(accountInput[j].value);
   
               if (isNaN(accountInputPrice)) {
                   totalAccount = 0;
               } else {
                   totalAccount += accountInputPrice;
               }
           }
       }
   
   
       // $('.subTotal').html(totalItemPrice.toFixed(2));
       $('.subTotal').html((totalItemPrice + totalAccount).toFixed(2));
   
       $('.totalTax').html(totalItemTaxPrice.toFixed(2));
   
       $('.totalAmount').html((parseFloat(subTotal) + totalAccount).toFixed(2));
       $('.totalDiscount').html(totalItemDiscountPrice.toFixed(2));
   
   
   })
   
   
   $(document).on('keyup change', '.accountAmount', function() {
   
       var el1 = $(this).parent().parent().parent().parent();
       var el = $(this).parent().parent().parent().parent().parent();
   
       var quantityDiv = $(el.find('.quantity'));
       var priceDiv = $(el.find('.price'));
       var discountDiv = $(el.find('.discount'));
   
       var itemSubTotal = 0;
       for (var p = 0; p < priceDiv.length; p++) {
           var quantity = quantityDiv[p].value;
           var price = priceDiv[p].value;
           var discount = discountDiv[p].value;
           if (discount.length <= 0) {
               discount = 0;
           }
           itemSubTotal += (quantity * price) - (discount);
       }
   
   
       // var totalItemTaxPrice = 0;
       // var itemTaxPriceInput = $('.itemTaxPrice');
       // for (var j = 0; j < itemTaxPriceInput.length; j++) {
       //
       //     totalItemTaxPrice += parseFloat(itemTaxPriceInput[j].value);
       //
       // }
   
       var totalItemTaxPrice = 0;
       var itemTaxPriceInput = $('.itemTaxPrice');
   
       for (var j = 0; j < itemTaxPriceInput.length; j++) {
           var parsedValue = parseFloat(itemTaxPriceInput[j].value);
   
           if (!isNaN(parsedValue)) {
               totalItemTaxPrice += parsedValue;
           }
       }
   
   
       var amount = $(this).val();
       el1.find('.accountamount').html(amount);
       var totalAccount = 0;
       var accountInput = $('.accountAmount');
       for (var j = 0; j < accountInput.length; j++) {
           totalAccount += (parseFloat(accountInput[j].value));
       }
   
   
       var inputs = $(".accountamount");
       var subTotal = 0;
       for (var i = 0; i < inputs.length; i++) {
   
           subTotal = parseFloat(subTotal) + parseFloat($(inputs[i]).html());
       }
   
       // console.log(subTotal)
   
   
       $('.subTotal').text((totalAccount + itemSubTotal).toFixed(2));
       $('.totalAmount').text((parseFloat((subTotal + itemSubTotal) + (totalItemTaxPrice))).toFixed(2));
   
   
   })
   
   $(document).on('change', '.item', function() {
       $('.item option').prop('hidden', false);
       $('.item :selected').each(function() {
           var id = $(this).val();
           $(".item option[value=" + id + "]").prop("hidden", true);
       });
   });
   
   $(document).on('click', '[data-repeater-create]', function() {
       $('.item option').prop('hidden', false);
       $('.item :selected').each(function() {
           var id = $(this).val();
           $(".item option[value=" + id + "]").prop("hidden", true);
       });
   })
   
   var vendorId = '{{ $vendorId }}';
   if (vendorId > 0) {
       $('#vender').val(vendorId).change();
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
   
   // for item SearchBox ( this function is  custom Js )
   JsSearchBox();
</script>
@endpush
@section('content')
<div class="row">
   {{ Form::open(['url' => 'bill', 'class' => 'w-100 needs-validation','novalidate']) }}
   <div class="col-12">
      <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
      <div class="card border-0 rounded-2xl shadow-md overflow-hidden my-3">
        <div class="h-1 w-full" style="background:#007C38;"></div>
           <div class="card-body">
            <div class="row">
               <div class="col-md-6">
                  <div class="form-group" id="vender-box">
                     {{ Form::label('vender_id', __('Vendor'), ['class' => 'form-label']) }}
                     <x-required></x-required>
                     {{ Form::select('vender_id', $venders, $vendorId, ['class' => 'form-control select', 'id' => 'vender', 'data-url' => route('bill.vender'), 'required' => 'required']) }}
                  </div>
                  <div class=" text-sm mx-4 text-end"><a
                                        href="javascript:void(0)" id="add_vender"><b>{{ __('Add Vendor') }}</b></a>
                </div>
                  <div id="vender_detail" class="d-none">
                  </div>
               </div>
               <div class="col-md-6">
                  <div class="row">
                     <div class="col-md-6">
                        <div class="form-group">
                           {{ Form::label('bill_date', __('Bill Date'), ['class' => 'form-label']) }}
                           <x-required></x-required>
                           <div class="form-icon-user">
                              {{ Form::date('bill_date', date('Y-m-d'), ['class' => 'form-control', 'required' => 'required']) }}
                           </div>
                        </div>
                     </div>
                     <div class="col-md-6">
                        <div class="form-group">
                           {{ Form::label('due_date', __('Due Date'), ['class' => 'form-label']) }}
                           <x-required></x-required>
                           <div class="form-icon-user">
                              {{ Form::date('due_date', date('Y-m-d'), ['class' => 'form-control', 'required' => 'required']) }}
                           </div>
                        </div>
                     </div>
                     <div class="col-md-6">
                        <div class="form-group">
                           {{ Form::label('bill_number', __('Bill Number'), ['class' => 'form-label']) }}
                           <div class="form-icon-user">
                              <input type="text" class="form-control" value="{{ $bill_number }}"
                                 readonly>
                           </div>
                        </div>
                     </div>
                     <div class="col-md-6">
                        <div class="form-group mb-1">
                           {{ Form::label('category_id', __('Category'), ['class' => 'form-label']) }}
                           <x-required></x-required>
                           {{ Form::select('category_id', $category, null, ['class' => 'form-control select', 'required' => 'required']) }}
                        </div>
                         <div class="text-xs">
                {{ __('Need to add a new category? ') }}<a href="#" id="add_category"  class="text-[#007C38] font-semibold">🔴 {{ __('Add Category') }}</a>
            </div>
                     </div>
                     <div class="col-md-6">
                        <div class="form-group">
                           {{ Form::label('order_number', __('Order Number'), ['class' => 'form-label']) }}
                           <div class="form-icon-user">
                              {{ Form::number('order_number', '', ['class' => 'form-control']) }}
                           </div>
                        </div>
                     </div>
                     @if (!$customFields->isEmpty())
                     <div class="col-md-6">
                        <div class="tab-pane fade show" id="tab-2" role="tabpanel">
                           @include('customFields.formBuilder')
                        </div>
                     </div>
                     @endif
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
   <div class="col-12">
    <div class="card border-0 rounded-2xl shadow-md overflow-hidden my-3">
        <div class="h-1 w-full" style="background:#007C38;"></div>
            <div class="card-body">
         <div class="item-section">
            <div class="row justify-content-between align-items-center">
               <div class="col-md-12">
                  <div class="border-bottom d-flex align-items-center justify-content-between pb-3 mb-4">
                     <h5 class="h4 d-inline-block font-weight-400">{{ __('Product & Services') }}</h5>
                     <div class="all-button-box">
                         <a href="#" data-size="lg" data-url="{{ route('productservice.create-short') }}" data-ajax-popup="true" data-bs-toggle="tooltip" title="{{__('Create Product')}}" data-title="{{__('Create Product & Service')}}">
                                    Add New Product
                                </a>
                        <a href="javascript:void(0)" data-repeater-create="" class="btn btn-primary mr-2"
                           data-toggle="modal" data-target="#add-bank">
                        <i class="ti ti-plus"></i> {{ __('Add item') }}
                        </a>
                     </div>
                  </div>
               </div>
            </div>
         </div>
         <div class="mt-3 table-border-style">
            <div class="table-responsive">
               <table class="table mb-0" data-repeater-list="items" id="sortable-table">
                  
                  <tbody class="ui-sortable" data-repeater-item>
                    <tr>
                      <td colspan="{{ $TAX_ENABLED ? 7 : 6 }}">
                        <div class="border rounded p-3 mb-3 bg-light-subtle">
                          
                          {{-- Item --}}
                          <div class="mb-3">
                            {{ Form::label('item', __('Item'), ['class' => 'form-label fw-semibold']) }}
                            {{ Form::select('item', $product_services, '', [
                                'class' => 'form-control item',
                                'data-url' => route('bill.product'),
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
                              {{ Form::hidden('tax', '', ['class'=>'form-control tax']) }}
                              {{ Form::hidden('itemTaxPrice', '', ['class'=>'form-control itemTaxPrice']) }}
                              {{ Form::hidden('itemTaxRate', '', ['class'=>'form-control itemTaxRate']) }}
                            </div>
                          </div>
                          @endif
                  
                          {{-- Chart of Account --}}
                          <div class="mb-3">
                            {{ Form::label('chart_account_id', __('Chart of Account'), ['class' => 'form-label fw-semibold']) }}
                            <select name="chart_account_id" class="form-control">
                              @foreach ($chartAccounts as $key => $chartAccount)
                                <option value="{{ $key }}" class="subAccount">{{ $chartAccount }}</option>
                                @foreach ($subAccounts as $subAccount)
                                  @if ($key == $subAccount['account'])
                                    <option value="{{ $subAccount['id'] }}" class="ms-5">
                                      &nbsp;&nbsp;&nbsp; {{ $subAccount['name'] }}
                                    </option>
                                  @endif
                                @endforeach
                              @endforeach
                            </select>
                          </div>
                  
                          {{-- Account Amount --}}
                          <div class="mb-3">
                            {{ Form::label('accountAmount', __('Account Amount'), ['class' => 'form-label fw-semibold']) }}
                            <div class="input-group">
                              {{ Form::text('amount', '', ['class' => 'form-control accountAmount','placeholder'=>__('Amount')]) }}
                              <span class="input-group-text bg-white">{{ \Auth::user()->currencySymbol() }}</span>
                            </div>
                          </div>
                  
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
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td></td>
                        <td class="text-end"><strong>{{ __('Sub Total') }} ({{ \Auth::user()->currencySymbol() }})</strong>
                        </td>
                        <td class="text-end subTotal">0.00</td>
                        <td></td>
                     </tr>
                     <tr>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td></td>
                        <td class="text-end"><strong>{{ __('Discount') }} ({{ \Auth::user()->currencySymbol() }})</strong></td>
                        <td class="text-end totalDiscount">0.00</td>
                        <td></td>
                     </tr>
                     @if($TAX_ENABLED)
                     <tr>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td></td>
                        <td class="text-end"><strong>{{ __('Tax') }} ({{ \Auth::user()->currencySymbol() }})</strong></td>
                        <td class="text-end totalTax">0.00</td>
                        <td></td>
                     </tr>
                     @endif
                     <tr>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td class="text-end"><strong>{{ __('Total Amount') }}
                           ({{ \Auth::user()->currencySymbol() }})</strong>
                        </td>
                        <td class="blue-text text-end totalAmount"></td>
                        <td></td>
                     </tr>
                  </tfoot>
               </table>
            </div>
         </div>
         </div>
      </div>
   </div>
   <div class="modal-footer">
      <input type="button" value="{{ __('Cancel') }}" onclick="location.href = '{{ route('bill.index') }}';"
         class="btn btn-light mx-3">
      <input type="submit" value="{{ __('Create') }}" class="btn  btn-primary">
   </div>
   {{ Form::close() }}
</div>
   <div class="modal fade" id="venderModal" tabindex="-1" aria-labelledby="productCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
       <form id="add_vender_form" class="needs-validation">
            <div class="modal-header">
                <h5 class="modal-title" id="productUnitModalLabel">{{ __('Create Vender') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
<div class="modal-body">
    <div class="row">
        <div class="col-lg-12">
            <div class="form-group">
                {{ Form::label('name', __('Name'), ['class' => 'form-label']) }}<x-required></x-required>
                <div class="form-icon-user">
                    {{ Form::text('name', null, ['class' => 'form-control', 'required' => 'required']) }}
                </div>
            </div>
        </div>

        <x-mobile  div-class="col-md-12 " name="contact" label="{{ __('Contact') }} " required></x-mobile>

        <div class="col-lg-12">
            <div class="form-group">
                {{ Form::label('email', __('Email'), ['class' => 'form-label']) }}<x-required></x-required>
                <div class="form-icon-user">
                    {{ Form::text('email', null, ['class' => 'form-control', 'required' => 'required']) }}
                </div>
            </div>
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

@php
    $plan = \App\Models\Utility::getChatGPTSettings();
@endphp
<div class="modal fade" id="productCategoryModal" tabindex="-1" aria-labelledby="productCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
       <form id="add_category_form">
            <div class="modal-header">
                <h5 class="modal-title" id="productUnitModalLabel">{{ __('Create Expense Category') }}</h5>
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
            <input type="hidden" name="type" value="expense">
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
@endsection


@push('script-page')
<script type="text/javascript">
    $("#add_vender").click(function (e) {
    $(".card").addClass("blurred");
    $("#venderModal").modal("show");
});
$("#venderModal").on("hidden.bs.modal", function () {
    $(".card").removeClass("blurred"); 
});
$("#add_vender_form").submit(function (e) {
    e.preventDefault();
    var csrfToken = $('meta[name="csrf-token"]').attr('content');
    var data = new FormData(this);

    $.ajax({
        url: "{{ route('vender_short') }}",
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
                $("#venderModal").modal("hide");
                $("#add_vender_form")[0].reset(); 
                $("#vender").html(response.options);
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
    // Toggle fields based on type
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