<script src="{{ asset('js/unsaved.js') }}"></script>

{{ Form::open(array('url' => 'revenue','enctype' => 'multipart/form-data','class'=>'needs-validation','novalidate')) }}
<div class="modal-body">
    <div class="container-fluid">
        <div class="row">
        <div class="form-group col-md-6">
            {{ Form::label('date', __('Date'),['class'=>'form-label fw-semibold']) }}<x-required></x-required>
            {{Form::date('date',date('Y-m-d'),array('class'=>'form-control','required'=>'required'))}}
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('amount', __('Amount'),['class'=>'form-label fw-semibold']) }}<x-required></x-required>
            {{ Form::number('amount', '', array('class' => 'form-control','required'=>'required','step'=>'0.01','placeholder'=>__('Enter amount'))) }}
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('account_id', __('Account'),['class'=>'form-label fw-semibold']) }}<x-required></x-required>
            {{ Form::select('account_id',$accounts,null, array('class' => 'form-control select','required'=>'required')) }}
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('customer_id', __('Customer'),['class'=>'form-label fw-semibold']) }}<x-required></x-required>
            {{ Form::select('customer_id', $customers,null, array('class' => 'form-control select','required'=>'required')) }}
        </div>
        <div class="form-group col-md-12">
            {{ Form::label('description', __('Description'),['class'=>'form-label fw-semibold']) }}
            {{ Form::textarea('description', '', array('class' => 'form-control','rows'=>3,'placeholder'=>__('Enter description (optional)'))) }}
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('category_id', __('Category'),['class'=>'form-label fw-semibold']) }}<x-required></x-required>
            {{ Form::select('category_id', $categories,null, array('class' => 'form-control select','required'=>'required')) }}
            <div class="text-muted small mt-1">
                {{ __('Need to add a new category? ') }}<a href="#" id="add_category" class="text-primary fw-semibold text-decoration-none"><i class="fas fa-plus-circle me-1"></i>{{ __('Add Category') }}</a>
            </div>
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('reference', __('Reference'),['class'=>'form-label fw-semibold']) }}
            {{ Form::text('reference', '', array('class' => 'form-control','placeholder'=>__('Enter reference number (optional)'))) }}
        </div>

        <div class="form-group col-md-6">
            {{ Form::label('add_receipt', __('Payment Receipt'), ['class' => 'form-label fw-semibold']) }}
            <div class="choose-file form-group">
                <label for="file" class="form-label d-flex flex-column align-items-center justify-content-center border border-dashed border-2 p-4 rounded cursor-pointer">
                    <input type="file" name="add_receipt" id="files" class="form-control file-validate d-none" data-filename="upload_file" accept="image/*,.pdf">
                    <i class="fas fa-cloud-upload-alt fa-2x text-muted mb-2"></i>
                    <span class="text-muted">{{ __('Click to upload receipt') }}</span>
                    <span class="file-error text-danger"></span>
                </label>
                <p class="upload_file text-center mt-2"></p>
                <div class="text-center">
                    <img id="image" class="mt-2 border border-2 rounded img-thumbnail" src="{{asset(Storage::url('uploads/defualt/defualt.png'))}}" style="max-width: 120px; max-height: 120px; object-fit: cover;"/>
                </div>
            </div>
        </div>
        </div>
    </div>
</div>
<div class="modal-footer border-top bg-light d-flex justify-content-end gap-2 p-3">
    <input type="button" value="{{__('Cancel')}}" class="btn btn-light" data-bs-dismiss="modal">
    <input type="submit" value="{{__('Create')}}" class="btn btn-primary">
</div>
{{ Form::close() }}

@php
    $plan = \App\Models\Utility::getChatGPTSettings();
@endphp
<div class="modal fade" id="productCategoryModal" tabindex="-1" aria-labelledby="productCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
       <form id="add_category_form">
            <div class="modal-header">
                <h5 class="modal-title" id="productUnitModalLabel">{{ __('Create Product Category') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
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
        <div class="form-group col-md-12">
            {{ Form::label('name', __('Category Name'), ['class' => 'form-label']) }}<x-required></x-required>
            {{ Form::text('name', '', ['class' => 'form-control', 'required' => 'required']) }}
        </div>
        <div class="form-group col-md-12 account d-none">
            {{Form::label('chart_account_id',__('Account'),['class'=>'form-label'])}}
            <select class="form-control select" name="chart_account" id="chart_account"></select>
            <input type="hidden" name="type" value="income">
        </div>
        <div class="form-group col-md-12">
    {{ Form::label('color', __('Category Color'), ['class' => 'form-label']) }}<x-required></x-required>
    <div class="row gutters-xs">
        @foreach (App\Models\Utility::templateData()['colors'] as $key => $hexNoHash)
            @php
                $hex = '#'.$hexNoHash;
            @endphp
            <div class="col-auto">
                <label class="colorinput" title="{{ $hex }}">
                    <input name="color" type="radio"
                           value="{{ $hex }}"
                           class="colorinput-input" required>
                    <span class="colorinput-color" style="background: {{ $hex }}"></span>
                </label>
            </div>
        @endforeach
    </div>
    <small class="text-muted d-block mt-1">{{ __('For chart representation') }}</small>
</div>

    </div>
</div>
<div class="modal-footer">
    <input type="button" value="{{ __('Cancel') }}" class="btn  btn-light" data-bs-dismiss="modal">
    <input type="submit" value="{{ __('Create') }}" class="btn  btn-primary">
</div>
</form>
        </div>
    </div>
</div>

<script>
    document.getElementById('files').onchange = function () {
    var src = URL.createObjectURL(this.files[0])
    document.getElementById('image').src = src
    }
    $("#add_category").click(function (e) {
    $("#productCategoryModal").modal("show");
});
$("#add_category_form").submit(function (e) {
    e.preventDefault();
    var csrfToken = $('meta[name="csrf-token"]').attr('content');
    var data = new FormData(this);

    $.ajax({
        url: "{{ route('product-category-short') }}",
        method: "POST",
        dataType: "json",
        data: data,
        processData: false,
        contentType: false,
        headers: {
            'X-CSRF-TOKEN': csrfToken
        },
        success: function (response) {
            if (response.status == "1") {
                $("#productCategoryModal").modal("hide");
                $("#add_category_form")[0].reset();
                $("#category_id").html(response.options);
                show_toastr("success",response.message);
            }else{
                show_toastr("error",response.message);
            }
        },
        error: function (xhr, status, error) {
            toastr.error("Something went wrong. Please try again.");
            show_toastr("error",error.message);
        }
    });
});
</script>
