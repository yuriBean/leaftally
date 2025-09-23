@php
    $plan = \App\Models\Utility::getChatGPTSettings();
@endphp
{{ Form::open(['url' => 'contract','class'=>'needs-validation','novalidate']) }}
<div class="modal-body bg-[#FAFBFC]">
    <div class="bg-white p-6 rounded-[8px] border border-[#E5E7EB] shadow-sm overflow-hidden">
        <div class="row">
            @if ($plan->enable_chatgpt == 'on')
                <div>
                    <a href="#" data-size="md" data-ajax-popup-over="true"
                        data-url="{{ route('generate', ['contract']) }}" data-bs-toggle="tooltip" data-bs-placement="top"
                        title="{{ __('Generate') }}" data-title="{{ __('Generate content with AI') }}"
                        class="btn btn-primary btn-sm float-end">
                        <i class="fas fa-robot"></i>
                        {{ __('Generate with AI') }}
                    </a>
                </div>
            @endif
            <div class="form-group col-md-12">
                {{ Form::label('subject', __('Subject'), ['class' => 'col-form-label']) }}  
                {{ Form::text('subject', '', ['class' => 'form-control', 'required' => 'required']) }}
            </div>
            <div class="form-group col-md-12">
                {{ Form::label('customer', __('Customer'), ['class' => 'col-form-label']) }}<x-required></x-required>
                {{ Form::select('customer', $customers, null, ['class' => 'form-control ', 'required' => 'required']) }}
            </div>
    
            <div class="form-group col-md-6">
                {{ Form::label('type', __('Contract Type'), ['class' => 'col-form-label']) }}<x-required></x-required>
                {{ Form::select('type', $contractTypes, null, ['class' => 'form-control ', 'required' => 'required']) }}
            </div>
            <div class="form-group col-md-6">
                {{ Form::label('value', __('Contract Value'), ['class' => 'col-form-label']) }}<x-required></x-required>
                {{ Form::number('value', '', ['class' => 'form-control', 'required' => 'required', 'stage' => '0.01']) }}
            </div>
            <div class="form-group col-md-6">
                {{ Form::label('start_date', __('Start Date'), ['class' => 'col-form-label']) }}<x-required></x-required>
                {{ Form::date('start_date', date('Y-m-d'), ['class' => 'form-control', 'required' => 'required']) }}
            </div>
            <div class="form-group col-md-6">
                {{ Form::label('end_date', __('End Date'), ['class' => 'col-form-label']) }}<x-required></x-required>
                {{ Form::date('end_date', date('Y-m-d'), ['class' => 'form-control', 'required' => 'required']) }}
            </div>
        </div>
        <div class="row">
            <div class="form-group col-md-12">
                {{ Form::label('description', __('Description'), ['class' => 'col-form-label']) }}
                {!! Form::textarea('description', null, ['class' => 'form-control', 'rows' => '3']) !!}
            </div>
        </div>
    </div>
</div>
<div class="modal-footer border-t border-[#E5E5E5] bg-[#FAFAFA] px-6 py-4 flex justify-end gap-3 pr-0">
    <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('Close') }}</button>
    {{ Form::submit(__('Create'), ['class' => 'btn  btn-primary']) }}
</div>
{{ Form::close() }}

<script src="{{ asset('assets/js/plugins/choices.min.js') }}"></script>
<script>
    if ($(".multi-select").length > 0) {
        $($(".multi-select")).each(function(index, element) {
            var id = $(element).attr('id');
            var multipleCancelButton = new Choices(
                '#' + id, {
                    removeItemButton: true,
                }
            );
        });
    }
</script>
