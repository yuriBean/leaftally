<script src="{{ asset('js/unsaved.js') }}"></script>

{{ Form::model($unit, array('route' => array('product-unit.update', $unit->id), 'method' => 'PUT','class'=>'needs-validation','novalidate')) }}
<div class="modal-body bg-[#FAFBFC]">
    <div class="bg-white p-6 rounded-[8px] border border-[#E5E7EB] shadow-sm overflow-hidden">
        <div class="row">
        <div class="form-group col-md-12">
            {{ Form::label('name', __('Unit Name'),['class'=>'form-label']) }}<x-required></x-required>
            {{ Form::text('name', null, array('class' => 'form-control','required'=>'required')) }}
            @error('name')
            <small class="invalid-name" role="alert">
                <strong class="text-danger">{{ $message }}</strong>
            </small>
            @enderror
        </div>

    </div>
    </div>
</div>
<div class="modal-footer" style="background: #f8f9fa; border-top: 1px solid #e0e0e0; padding: 1.5rem 2rem; display: flex; justify-content: flex-end; gap: 1rem; border-radius: 0 0 8px 8px;">
    <input type="button" value="{{__('Cancel')}}" class="btn btn-light" data-bs-dismiss="modal" style="border-radius: 8px; padding: 0.75rem 1.5rem; border: 1.5px solid #e0e0e0; color: #2d3748; font-weight: 500; background: #fff;">
    <input type="submit" value="{{__('Update')}}" class="btn btn-success" style="background: #007c38; color: #fff; border-radius: 8px; padding: 0.75rem 1.5rem; font-weight: 500; border: none;">
</div>
{{ Form::close() }}
