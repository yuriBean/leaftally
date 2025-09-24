<script src="{{ asset('js/unsaved.js') }}"></script>

{{ Form::model($payment, array('route' => array('payment.update', $payment->id), 'method' => 'PUT','enctype' => 'multipart/form-data','class'=>'needs-validation','novalidate')) }}
<div class="modal-header" style="background: linear-gradient(90deg, #2e7d32 0%, #43a047 100%); color: #fff; border-top-left-radius: 8px; border-top-right-radius: 8px; padding: 18px 24px; margin-bottom: 0;">
    <h5 class="modal-title" style="margin: 0; font-weight: 600; font-size: 1.15rem;">Edit Payment</h5>
</div>
<div class="modal-body bg-[#FAFBFC]">
    <div class="bg-white p-6 rounded-[8px] border border-[#E5E7EB] shadow-sm overflow-hidden">
        <div class="row">
    <div class="flex flex-col gap-2 mb-3">
            {{ Form::label('date', __('Date'),['class'=>'form-label']) }}<x-required></x-required>
            <div class="form-icon-user">
                {{Form::date('date',null,array('class'=>'form-control','required'=>'required'))}}

            </div>
        </div>
    <div class="flex flex-col gap-2 mb-3">
            {{ Form::label('amount', __('Amount'),['class'=>'form-label']) }}<x-required></x-required>
            <div class="form-icon-user">
                {{ Form::number('amount', null, array('class' => 'form-control','required'=>'required','step'=>'0.01')) }}
            </div>
        </div>
    <div class="flex flex-col gap-2 mb-3">
            {{ Form::label('account_id', __('Account'),['class'=>'form-label']) }}<x-required></x-required>
            {{ Form::select('account_id',$accounts,null, array('class' => 'form-control select','required'=>'required')) }}
        </div>
    <div class="flex flex-col gap-2 mb-3">
            {{ Form::label('vender_id', __('Vendor'),['class'=>'form-label']) }}<x-required></x-required>
            {{ Form::select('vender_id', $venders,null, array('class' => 'form-control select','required'=>'required')) }}
        </div>
    <div class="flex flex-col gap-2 mb-3">
            {{ Form::label('description', __('Description'),['class'=>'form-label']) }}
            {{ Form::textarea('description', null, array('class' => 'form-control','rows'=>3)) }}
        </div>
    <div class="flex flex-col gap-2 mb-3">
            {{ Form::label('category_id', __('Category'),['class'=>'form-label']) }}<x-required></x-required>
            {{ Form::select('category_id', $categories,null, array('class' => 'form-control select','required'=>'required')) }}
        </div>
    <div class="flex flex-col gap-2 mb-3">
            {{ Form::label('reference', __('Reference'),['class'=>'form-label']) }}
            <div class="form-icon-user">
                {{ Form::text('reference', null, array('class' => 'form-control')) }}
            </div>
        </div>

        <div class="flex flex-col gap-2 mb-3">
            <div class="choose-file">
                {{Form::label('add_receipt',__('Payment Receipt'),['class'=>'d-block form-label'])}}
                <label for="image" class="form-label">
                    <input type="file" class="form-control file-validate" name="add_receipt" id="files" data-filename="upload_file">
                    <span id="" class="file-error text-danger"></span>
                </label>
                <p class="upload_file"></p>
                @if (isset($payment->add_receipt))
                    <img id="image" class="mt-2 border border-primary" src="{{\App\Models\Utility::get_file('uploads/payment/'.$payment->add_receipt)}}" width="120px" height="120px"/>
                @else
                    <img id="image" class="mt-2 border border-primary" src="{{asset(Storage::url('uploads/defualt/defualt.png'))}}" width="120px" height="120px"/>
                @endif
            </div>
        </div>

    </div>
    </div>
</div>

<div class="modal-footer flex justify-end gap-2 mt-4" style="border-top: none; background: #f7f7f7;">
    <button type="button" class="zameen-btn zameen-btn-cancel px-4 py-2" data-bs-dismiss="modal" style="background: #e0e0e0; color: #333;">{{__('Cancel')}}</button>
    <button type="submit" class="zameen-btn zameen-btn-primary px-4 py-2" style="background: #2e7d32; color: #fff;">{{__('Update')}}</button>
</div>
{{ Form::close() }}

<script>
    document.getElementById('files').onchange = function () {
    var src = URL.createObjectURL(this.files[0])
    document.getElementById('image').src = src
    }

</script>

