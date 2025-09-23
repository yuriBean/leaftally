<div class="{{ $divClass }}">
    <div class="form-group">
        {{ Form::label($name, $label, ['class' => 'form-label']) }}
        @if($required) <x-required></x-required> @endif

        {{ Form::text($name, $value, [
            'class' => $class,
            'placeholder' => $placeholder,
            'pattern' => '^\d{11}$',
            'id' => $id,
            'required' => $required
        ]) }}

        <div class="text-xs text-danger">
            {{ __('11-digit phone number.') }}
        </div>
    </div>
</div>
