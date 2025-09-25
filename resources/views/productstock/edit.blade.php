{{ Form::model($productService, array('route' => array('productstock.update', $productService->id), 'method' => 'PUT')) }}
<div class="modal-body bg-[#FAFBFC]">
    <div class="bg-white p-6 rounded-[8px] border border-[#E5E7EB] shadow-sm overflow-hidden">
        <div class="row">

        <div class="form-group col-md-6">
            {{ Form::label('Product', __('Product'),['class'=>'form-label']) }}<br>
            {{$productService->name}}

        </div>
        <div class="form-group col-md-6">
            {{ Form::label('Product', __('SKU'),['class'=>'form-label']) }}<br>
            {{$productService->sku}}

        </div>

        <div class="form-group quantity">
            <div class="d-flex radio-check ">
                <div class="form-check form-check-inline form-group col-md-6">
                    <input type="radio" id="plus_quantity" value="Add" name="quantity_type" class="form-check-input" checked="checked">
                    <label class="form-check-label" for="plus_quantity">{{__('Add Quantity')}}</label>
                </div>
                <div class="form-check form-check-inline form-group col-md-6">
                    <input type="radio" id="minus_quantity" value="Less" name="quantity_type" class="form-check-input">
                    <label class="form-check-label" for="minus_quantity">{{__('Less Quantity')}}</label>
                </div>
            </div>
        </div>

        <div class="form-group col-md-12">
            {{ Form::label('quantity', __('Quantity'),['class'=>'form-label']) }}<span class="text-danger">*</span>
            {{ Form::number('quantity',"", array('class' => 'form-control','required'=>'required')) }}
        </div>
    </div>
    </div>
</div>
<div class="modal-footer" style="background: #f8f9fa; border-top: 1px solid #e0e0e0; padding: 1.5rem 2rem; display: flex; justify-content: flex-end; gap: 1rem; border-radius: 0 0 8px 8px;">
    <input type="button" value="{{__('Cancel')}}" class="btn btn-light" data-bs-dismiss="modal" style="border-radius: 8px; padding: 0.75rem 1.5rem; border: 1.5px solid #e0e0e0; color: #2d3748; font-weight: 500; background: #fff;">
    <input type="submit" value="{{__('Save')}}" class="btn btn-success" style="background: #007c38; color: #fff; border-radius: 8px; padding: 0.75rem 1.5rem; font-weight: 500; border: none;">
</div>
{{Form::close()}}
