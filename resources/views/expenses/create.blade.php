<script src="{{ asset('js/unsaved.js') }}"></script>

{{ Form::open(array('url' => 'expenses','enctype' => "multipart/form-data")) }}
<div class="zameen-modal-body" style="max-width: 420px; margin: auto; background: #f7f7f7; border-radius: 12px; padding: 24px;">
    <div class="flex flex-col gap-2 mb-3">
        {{ Form::label('category_id', __('Category'), ['class' => 'zameen-label']) }}
        {{ Form::select('category_id', $category, null, ['class' => 'zameen-input', 'required' => 'required']) }}
        @error('category_id')
        <span class="invalid-category_id" role="alert"><strong class="text-danger">{{ $message }}</strong></span>
        @enderror
    </div>
    <div class="flex flex-col gap-2 mb-3">
        {{ Form::label('amount', __('Amount'), ['class' => 'zameen-label']) }}
        {{ Form::number('amount', '', ['class' => 'zameen-input', 'required' => 'required']) }}
        @error('amount')
        <span class="invalid-amount" role="alert"><strong class="text-danger">{{ $message }}</strong></span>
        @enderror
    </div>
    <div class="flex flex-col gap-2 mb-3">
        {{ Form::label('date', __('Date'), ['class' => 'zameen-label']) }}
        {{ Form::text('date', '', ['class' => 'zameen-input pc-datepicker-1', 'required' => 'required']) }}
        @error('date')
        <span class="invalid-date" role="alert"><strong class="text-danger">{{ $message }}</strong></span>
        @enderror
    </div>
    <div class="flex flex-col gap-2 mb-3">
        {{ Form::label('project_id', __('Project'), ['class' => 'zameen-label']) }}
        {{ Form::select('project_id', $projects, null, ['class' => 'zameen-input', 'required' => 'required']) }}
        @error('project_id')
        <span class="invalid-project_id" role="alert"><strong class="text-danger">{{ $message }}</strong></span>
        @enderror
    </div>
    <div class="flex flex-col gap-2 mb-3">
        {{ Form::label('user_id', __('User'), ['class' => 'zameen-label']) }}
        {{ Form::select('user_id', $users, null, ['class' => 'zameen-input', 'required' => 'required']) }}
        @error('user_id')
        <span class="invalid-user_id" role="alert"><strong class="text-danger">{{ $message }}</strong></span>
        @enderror
    </div>
    <div class="flex flex-col gap-2 mb-3">
        {{ Form::label('attachment', __('Attachment'), ['class' => 'zameen-label']) }}
        {{ Form::file('attachment', ['class' => 'zameen-input', 'accept' => '.jpeg,.jpg,.png,.doc,.pdf']) }}
        @error('attachment')
        <span class="invalid-attachment" role="alert"><strong class="text-danger">{{ $message }}</strong></span>
        @enderror
    </div>
    <div class="flex flex-col gap-2 mb-3">
        {{ Form::label('description', __('Description'), ['class' => 'zameen-label']) }}
        {!! Form::textarea('description', null, ['class' => 'zameen-input', 'rows' => '3']) !!}
        @error('terms')
        <span class="invalid-terms" role="alert"><strong class="text-danger">{{ $message }}</strong></span>
        @enderror
    </div>
    <div class="flex justify-end gap-2 mt-4">
        <button type="button" class="zameen-btn zameen-btn-cancel px-4 py-2" data-dismiss="modal" style="background: #e0e0e0; color: #333;">{{__('Cancel')}}</button>
        {{Form::submit(__('Create'),array('class'=>'zameen-btn zameen-btn-primary px-4 py-2', 'style'=>'background: #2e7d32; color: #fff;'))}}
    </div>
</div>
{{ Form::close() }}
