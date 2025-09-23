@if($customFields)
    @foreach($customFields as $customField)
        @if($customField->type == 'text')
            <div class="form-group">
                {{ Form::label('customField-'.$customField->id, __($customField->name),['class'=>'form-label']) }}
                <div class="input-group">
                    {{ Form::text('customField['.$customField->id.']', null, array('class' => 'form-control')) }}
                </div>
            </div>
        @elseif($customField->type == 'email')
            <div class="form-group">
                {{ Form::label('customField-'.$customField->id, __($customField->name),['class'=>'form-label']) }}
                <div class="input-group">
                    {{ Form::email('customField['.$customField->id.']', null, array('class' => 'form-control')) }}
                </div>
            </div>
        @elseif($customField->type == 'number')
            <div class="form-group">
                {{ Form::label('customField-'.$customField->id, __($customField->name),['class'=>'form-label']) }}
                <div class="input-group">
                    {{ Form::number('customField['.$customField->id.']', null, array('class' => 'form-control')) }}
                </div>
            </div>
        @elseif($customField->type == 'date')
            <div class="form-group">
                {{ Form::label('customField-'.$customField->id, __($customField->name),['class'=>'form-label']) }}
                <div class="input-group">
                    {{ Form::date('customField['.$customField->id.']', null, array('class' => 'form-control')) }}
                </div>
            </div>
        @elseif($customField->type == 'textarea')
            <div class="form-group">
                {{ Form::label('customField-'.$customField->id, __($customField->name),['class'=>'form-label']) }}
                <div class="input-group">
                    {{ Form::textarea('customField['.$customField->id.']', null, array('class' => 'form-control')) }}
                </div>
            </div>
        @elseif($customField->type == 'select')
            <div class="form-group">
                {{ Form::label('customField-'.$customField->id, __($customField->name),['class'=>'form-label']) }}
                <div class="input-group">
                    @php
                        $options = [];
                        if($customField->options) {
                            $optionLines = explode("\n", $customField->options);
                            foreach($optionLines as $line) {
                                $line = trim($line);
                                if(!empty($line)) {
                                    $options[$line] = $line;
                                }
                            }
                        }
                    @endphp
                    {{ Form::select('customField['.$customField->id.']', ['' => 'Select Option'] + $options, null, array('class' => 'form-control')) }}
                </div>
            </div>
        @endif
    @endforeach
@endif

