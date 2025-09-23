<script src="{{ asset('js/unsaved.js') }}"></script>

@php
    $plan = \App\Models\Utility::getChatGPTSettings();
@endphp
{{ Form::model($category, ['route' => ['product-category.update', $category->id], 'method' => 'PUT','class'=>'needs-validation','novalidate']) }}
<div class="modal-body bg-[#FAFBFC]">
    <div class="bg-white p-6 rounded-[8px] border border-[#E5E7EB] shadow-sm overflow-hidden">
        <div class="row">
        @if ($plan->enable_chatgpt == 'on')
            <div>
                <a href="#" data-size="md" data-ajax-popup-over="true"
                    data-url="{{ route('generate', ['category']) }}" data-bs-toggle="tooltip" data-bs-placement="top"
                    title="{{ __('Generate') }}" data-title="{{ __('Generate content with AI') }}"
                    class="btn btn-primary btn-sm float-end">
                    <i class="fas fa-robot"></i>
                    {{ __('Generate with AI') }}
                </a>
            </div>
        @endif
        </div>
        <div class="row">
            <div class="form-group col-md-12">
                {{ Form::label('name', __('Category Name'),['class'=>'form-label']) }}<x-required></x-required>
                {{ Form::text('name', null, array('class' => 'form-control font-style','required'=>'required')) }}
            </div>

            <div class="form-group col-md-12 d-block">
                {{ Form::label('type', __('Category Type'),['class'=>'form-label']) }}<x-required></x-required>
                {{ Form::select('type',$types,null, array('class' => 'form-control select cattype','required'=>'required')) }}
            </div>

            <div class="form-group col-md-12 account {{$category->type =='product & service'? 'd-none':''}}">
                {{Form::label('chart_account_id',__('Account'),['class'=>'form-label'])}}
                <select class="form-control select" name="chart_account" id="chart_account" >
                </select>

            </div>

           <div class="form-group col-md-12">
    {{ Form::label('color', __('Category Color'), ['class' => 'form-label']) }}<x-required></x-required>
    @php
        $current = ltrim((string)($category->color ?? ''), '#');
    @endphp
    <div class="row gutters-xs">
        @foreach (App\Models\Utility::templateData()['colors'] as $key => $hexNoHash)
            @php
                $hex = '#'.$hexNoHash;
                $checked = $hexNoHash === $current ? 'checked' : '';
            @endphp
            <div class="col-auto">
                <label class="colorinput" title="{{ $hex }}">
                    <input name="color" type="radio"
                           value="{{ $hex }}"
                           class="colorinput-input" required {{ $checked }}>
                    <span class="colorinput-color" style="background: {{ $hex }}"></span>
                </label>
            </div>
        @endforeach
    </div>
    <small class="text-muted d-block mt-1">{{ __('For chart representation') }}</small>
</div>

        </div>
    </div>
</div>
<div class="modal-footer border-t border-[#E5E5E5] bg-[#FAFAFA] px-6 py-4 flex justify-end gap-3">
    <input type="button" value="{{ __('Cancel') }}" class="btn  btn-light" data-bs-dismiss="modal">
    <input type="submit" value="{{ __('Update') }}" class="btn  btn-primary">
</div>
{{ Form::close() }}

<script>

    $(document).on('click', '.cattype', function ()
    {
        var type = $(this).val();
        if (type != 'product & service') {
            $('.account').removeClass('d-none')
            $('.account').addClass('d-block');
        } else {
            $('.account').addClass('d-none')
            $('.account').removeClass('d-block');
        }
    });

    $(document).on('change', '#type', function () {
        var type = $(this).val();

        $.ajax({
            url: '{{route('productServiceCategory.getaccount')}}',
            type: 'POST',
            data: {
                "type": type,
                "_token": "{{ csrf_token() }}",
            },
            success: function (data) {
                $('#chart_account').empty();
                $('#chart_account').append('<option value="">{{__(' --- Select Account ---')}}</option>');
                $.each(data.chart_accounts, function (key, value) {
                    var select = '';
                    if (key == '{{ $category->chart_account_id }}') {
                        select = 'selected';
                    }
                    $('#chart_account').append('<option value="' + key + '"  ' + select + ' class="subAccount">' + value + '</option>');

                    $.each(data.sub_accounts, function (subkey, subvalue) {
                    var select1 = '';

                        if (subvalue.id == '{{ $category->chart_account_id }}') {
                        select1 = 'selected';
                    }
                        if(key == subvalue.account)
                        {
                            $('#chart_account').append('<option value="' + subvalue.id + '"  ' + select1 + '>' + '&nbsp; &nbsp;&nbsp;' + subvalue.name + '</option>');
                        }
                });
                });
            }
        });
    });
    $(document).ready(function (){
        $('#type').trigger('change')
    })
</script>
