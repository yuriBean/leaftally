<script src="{{ asset('js/unsaved.js') }}"></script>

{{ Form::model($revenue, array('route' => array('revenue.update', $revenue->id), 'method' => 'PUT','enctype' => 'multipart/form-data','class'=>'needs-validation','novalidate')) }}
<div class="modal-body modal-body bg-[#FAFBFC]">
    <div class="bg-white p-6 rounded-[8px] border border-[#E5E7EB] shadow-sm overflow-hidden">
        <div class="row">
        <div class="form-group  col-md-6">
            {{ Form::label('date',   __('Date'),['class'=>'form-label']) }}<x-required></x-required>
            <div class="form-icon-user">
                {{Form::date('date',null,array('class'=>'form-control','required'=>'required'))}}
            </div>
        </div>
        <div class="form-group  col-md-6">
            {{ Form::label('amount', __('Amount'),['class'=>'form-label']) }}<x-required></x-required>
            <div class="form-icon-user">
                {{ Form::number('amount', null, array('class' => 'form-control','required'=>'required','step'=>'0.01')) }}
            </div>
        </div>
        <div class="form-group  col-md-6">
            {{ Form::label('account_id', __('Account'),['class'=>'form-label']) }}<x-required></x-required>
            {{ Form::select('account_id',$accounts,null, array('class' => 'form-control select','required'=>'required')) }}
        </div>
        <div class="form-group  col-md-6">
            {{ Form::label('customer_id', __('Customer'),['class'=>'form-label']) }}<x-required></x-required>
            {{ Form::select('customer_id', $customers,null, array('class' => 'form-control select','required'=>'required')) }}
        </div>
        <div class="form-group  col-md-12">
            {{ Form::label('description', __('Description'),['class'=>'form-label']) }}
            {{ Form::textarea('description', null, array('class' => 'form-control','rows'=>3)) }}
        </div>
        <div class="form-group  col-md-6">
            {{ Form::label('category_id', __('Category'),['class'=>'form-label']) }}<x-required></x-required>
            {{ Form::select('category_id', $categories,null, array('class' => 'form-control select','required'=>'required')) }}
        </div>

        <div class="form-group  col-md-6">
            {{ Form::label('reference', __('Reference'),['class'=>'form-label']) }}
            <div class="form-icon-user">
                {{ Form::text('reference', null, array('class' => 'form-control')) }}
            </div>
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('add_receipt', __('Payment Receipt'), ['class' => 'form-label']) }}
            <label for="image">
                <input type="file" name="add_receipt" id="files" class="form-control file-validate" data-filename="upload_file">
                <span id="" class="file-error text-danger"></span>
            </label>
            <p class="upload_file"></p>
            @if (isset($revenue->add_receipt))
                <img id="image" class="mt-2 border border-primary" src="{{asset(Storage::url('uploads/revenue/'.$revenue->add_receipt))}}" width="120px" height="120px"/>
            @else
                <img id="image" class="mt-2 border border-primary" src="{{asset(Storage::url('uploads/defualt/defualt.png'))}}" width="120px" height="120px"/>
            @endif
        </div>
    </div>
    </div>
</div>
<div class="modal-footer" style="background: #f8f9fa; border-top: 1px solid #e0e0e0; padding: 1.5rem 2rem; display: flex; justify-content: flex-end; gap: 1rem; border-radius: 0 0 8px 8px;">
    <input type="button" value="{{__('Cancel')}}" class="btn btn-light" data-bs-dismiss="modal" style="border-radius: 8px; padding: 0.75rem 1.5rem; border: 1.5px solid #e0e0e0; color: #2d3748; font-weight: 500; background: #fff;">
    <input type="submit" value="{{__('Update')}}" class="btn btn-success" style="background: #007c38; color: #fff; border-radius: 8px; padding: 0.75rem 1.5rem; font-weight: 500; border: none;">
</div>
{{ Form::close() }}

<script>
    document.getElementById('files').onchange = function () {
    var src = URL.createObjectURL(this.files[0])
    document.getElementById('image').src = src
    }
</script>

