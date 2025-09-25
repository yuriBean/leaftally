
<div class="card bg-none card-box">
    {{ Form::open(array('route' => array('vender.bill.send.mail', $bill_id),'class'=>'needs-validation','novalidate')) }}
    <div class="row mt-2 mx-2">
        <div class="form-group col-md-12">
            {{ Form::label('email', __('Email'), ['class' => 'form-label']) }}<x-required></x-required>
            {{ Form::email('email', '', ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('Enter email')]) }}
            @error('email')
                <span class="invalid-feedback" role="alert">
                    <strong class="text-danger">{{ $message }}</strong>
                </span>
            @enderror
        </div>
    </div>

    <div class="row mt-3 mx-2">
        <div class="col-md-12 text-end" style="display: flex; justify-content: flex-end; gap: 1rem;">
            <input type="button" value="{{ __('Cancel') }}" class="btn btn-light" data-bs-dismiss="modal" style="border-radius: 8px; padding: 0.75rem 1.5rem; border: 1.5px solid #e0e0e0; color: #2d3748; font-weight: 500; background: #fff;">
            <input type="submit" value="{{ __('Send') }}" class="btn btn-success" style="background: #007c38; color: #fff; border-radius: 8px; padding: 0.75rem 1.5rem; font-weight: 500; border: none;">
        </div>
    </div>
    {{ Form::close() }}
</div>
