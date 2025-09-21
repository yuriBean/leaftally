{{ Form::model(null, ['route' => ['custom_page.update', $key], 'method' => 'PUT']) }}
<div class="modal-body">
    @csrf
    <div class="row">
        <div class="form-group col-md-12">
            {{ Form::label('name', __('Page Name'), ['class' => 'form-label']) }}
            {{ Form::text('menubar_page_name', $page['menubar_page_name'], ['class' => 'form-control font-style', 'placeholder' => __('Enter Page Name'), 'required' => 'required', 'disabled' => true]) }}
            {{ Form::hidden('menubar_page_name', $page['menubar_page_name']) }}
        </div>

        <div class="col-lg-3 col-xl-3 col-md-3">
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="template_name" value="page_content" id="page_content"
                    data-name="page_content"
                    {{ isset($page['template_name']) && $page['template_name'] == 'page_content' ? "checked = 'checked'" : '' }}>
                <label class="form-check-label" for="page_content">
                    {{ 'Page Content' }}
                </label>
            </div>
        </div>
        <div class="col-lg-3 col-xl-3 col-md-3">
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="template_name" value="page_url" id="page_url"
                    data-name="page_url"
                    {{ isset($page['template_name']) && $page['template_name'] == 'page_url' ? "checked = 'checked'" : '' }}>
                <label class="form-check-label" for="page_url">
                    {{ 'Page URL' }}
                </label>
            </div>
        </div>
        <div class="form-group col-md-12 page_url mt-2 d-none">
            {{ Form::label('page_url', __('Page URL'), ['class' => 'form-label']) }}
            {{ Form::text('page_url', isset($page['page_url']) && !empty($page['page_url']) ? $page['page_url'] : '', ['class' => 'form-control font-style', 'placeholder' => __('Enter Page URL')]) }}
        </div>
        <div class="form-group col-md-12 page_content mt-2">
            {{ Form::label('description', __('Page Content'), ['class' => 'form-label']) }}
            {!! Form::textarea(
                'menubar_page_contant',
                isset($page['menubar_page_contant']) && !empty($page['menubar_page_contant']) ? $page['menubar_page_contant'] : '',
                [
                    'class' => 'form-control summernote-simple',
                ],
            ) !!}
        </div>
        <div class="col-lg-2 col-xl-2 col-md-2">
            <div class="form-check form-switch ml-1">
                <input type="checkbox" class="form-check-input" id="cust-theme-bg" name="header"
                    {{ $page['header'] == 'on' ? 'checked' : '' }} />
                <label class="form-check-label f-w-600 pl-1" for="cust-theme-bg">{{ __('Header') }}</label>
            </div>
        </div>
        <div class="col-lg-2 col-xl-2 col-md-2">
            <div class="form-check form-switch ml-1">
                <input type="checkbox" class="form-check-input" id="cust-darklayout"
                    name="footer"{{ $page['footer'] == 'on' ? 'checked' : '' }} />
                <label class="form-check-label f-w-600 pl-1" for="cust-darklayout">{{ __('Footer') }}</label>
            </div>
        </div>
        <div class="col-lg-2 col-xl-2 col-md-2">
            <div class="form-check form-switch ml-1">
                <input type="checkbox" class="form-check-input" id="login"
                    name="login"{{ $page['login'] == 'on' ? 'checked' : '' }} />
                <label class="form-check-label f-w-600 pl-1" for="login">{{ __('login') }}</label>
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <input type="button" value="{{ __('Cancel') }}" class="btn  btn-light" data-bs-dismiss="modal">
    <input type="submit" value="{{ __('Update') }}" class="btn  btn-primary">
</div>
{{ Form::close() }}

</script>
<script>
    $(document).ready(function() {
        $('.summernote-simple').summernote({
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'italic', 'underline', 'strikethrough']],
                ['list', ['ul', 'ol', 'paragraph']],
                ['insert', ['link', 'unlink']],
            ],
            height: 200,
        });
    });
</script>

<script>
    $(document).ready(function() {
        $('input[name="template_name"][id="page_content"]').prop('checked', true);
        $('input[name="template_name"]').change(function() {
            var radioValue = $('input[name="template_name"]:checked').val();
            var page_content = $('.page_content');
            if (radioValue === "page_content") {
                $('.page_content').removeClass('d-none');
                $('.page_url').addClass('d-none');
            } else {
                $('.page_content').addClass('d-none');
                $('.page_url').removeClass('d-none');
            }
        });
    });
</script>
