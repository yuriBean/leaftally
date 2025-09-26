{{ Form::open(array('url' => 'goal','class'=>'needs-validation','novalidate')) }}
<div  style="background: linear-gradient(135deg, #007c38 0%, #10b981 100%); padding: 1.5rem 2rem; color: white; text-align: center; flex-shrink: 0; border-radius: 0;">
    <h5  style="margin: 0; font-weight: 600; font-size: 1.15rem; ">Create New Goal</h5>
</div>

<div class="modal-body bg-[#FAFBFC]">
    <div class="bg-white p-2 overflow-hidden">
        <div class="row">
        <div class="form-group col-md-6">
            {{ Form::label('name', __('Name'),['class'=>'form-label']) }}<x-required></x-required>
            {{ Form::text('name', '', array('class' => 'form-control','required'=>'required')) }}
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('amount', __('Amount'),['class'=>'form-label']) }}<x-required></x-required>
            {{ Form::number('amount', '', array('class' => 'form-control','required'=>'required','step'=>'0.01')) }}
        </div>
        <div class="form-group  col-md-12">
            {{ Form::label('type', __('Type'),['class'=>'form-label']) }}<x-required></x-required>
            {{ Form::select('type',$types,null, array('class' => 'form-control select','required'=>'required')) }}
        </div>
        <div class="form-group  col-md-6">
            {{ Form::label('from', __('From'),['class'=>'form-label']) }}<x-required></x-required>
            {{Form::date('from',date('Y-m-d'),array('class'=>'form-control','required'=>'required'))}}

        </div>
        <div class="form-group  col-md-6">
            {{ Form::label('to', __('To'),['class'=>'form-label']) }}<x-required></x-required>
            {{Form::date('to',date('Y-m-d'),array('class'=>'form-control','required'=>'required'))}}

        </div>
        <div class="form-group col-md-12">
            <input class="form-check-input" type="checkbox" name="is_display" id="is_display" checked>
            <label class="custom-control-label form-label" for="is_display">{{__('Display On Dashboard')}}</label>

        </div>

    </div>
    </div>
</div>

<div class="modal-footer border-t border-[#E5E5E5] bg-[#FAFAFA] px-6 py-4 flex justify-end gap-3">
    <input type="button" value="{{__('Cancel')}}" class="btn  btn-light" data-bs-dismiss="modal">
    <input type="submit" value="{{__('Create')}}" class="btn  btn-primary">
</div>

{{ Form::close() }}

