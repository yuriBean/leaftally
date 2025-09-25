<style>
  /* Hide default modal header and close button injected by AJAX system */
  #commonModal .modal-header, #commonModal .modal-title, #commonModal .btn-close {
    display: none !important;
  }
  #commonModal .modal-content {
    padding-top: 0 !important;
  }
</style>
{{ Form::model($webhook, array('route' => array('webhook.update', $webhook->id), 'method' => 'PUT')) }}
<div class="modal-body">
<div class="row">
    <div class="col-md-12">
    <div class="form-group">
        {{Form::label('module',__('Module'),['class'=>'col-form-label']) }}
        {{ Form::select('module', $module, null, ['class' => 'form-control', 'required' => 'required']) }}
    </div>
    </div>
    <div class="col-md-12">
    <div class="form-group">
        {{Form::label('method',__('Method'),['class'=>'col-form-label']) }}
        {{ Form::select('method', $method, null, ['class' => 'form-control', 'required' => 'required']) }}
    </div>
    </div>
    <div class="col-md-12">
    <div class="form-group">
        {{Form::label('url',__('URL'),['class'=>'form-label']) }}
        {{Form::text('url',null,array('class'=>'form-control','placeholder'=>__('Enter Url'),'required'=>'required'))}}
    </div>
    </div>

</div>
</div>

<div class="modal-footer" style="background: #f8f9fa; border-top: 1px solid #e0e0e0; padding: 1.5rem 2rem; display: flex; justify-content: flex-end; gap: 1rem; border-radius: 0 0 8px 8px;">
    <button type="button" class="btn btn-light" data-bs-dismiss="modal" style="border-radius: 8px; padding: 0.75rem 1.5rem; border: 1.5px solid #e0e0e0; color: #2d3748; font-weight: 500; background: #fff;">{{ __('Close') }}</button>
    <input type="submit" value="{{ __('Update') }}" class="btn btn-success" style="background: #007c38; color: #fff; border-radius: 8px; padding: 0.75rem 1.5rem; font-weight: 500; border: none;">
</div>

{{ Form::close() }}