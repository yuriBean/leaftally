<script src="{{ asset('js/unsaved.js') }}"></script>


<style>
    .zameen-user-container { background: #f8f9fa; padding: 0; max-height: 90vh; overflow-y: auto; }
    .zameen-user-card { background: white; border-radius: 0; box-shadow: none; width: 100%; max-width: none; margin: 0; max-height: 90vh; display: flex; flex-direction: column; }
    form.needs-validation { width: 100%; }
    .zameen-user-header { background: linear-gradient(135deg, #007c38 0%, #10b981 100%); padding: 1.5rem 2rem; color: white; text-align: center; flex-shrink: 0; border-radius: 0; }
    .zameen-user-header h2 { margin: 0; font-size: 1.5rem; font-weight: 600; }
    .zameen-user-header p { margin: 0; opacity: 0.9; font-size: 0.875rem; }
    .zameen-form-container { padding: 1.25rem 1.5rem; overflow-y: auto; flex: 1; display: flex; flex-direction: column; gap: 1rem; }
    .zameen-form-group { margin-bottom: 0; }
    .zameen-label { font-weight: 500; color: #495057; margin-bottom: 0.5rem; font-size: 0.95rem; display: block; }
    .zameen-required { color: #f44336; margin-left: 0.25rem; }
    .zameen-input, .zameen-select { padding: 0.75rem 1rem; border: 1px solid #dee2e6; border-radius: 8px; font-size: 1rem; background: white; width: 100%; box-sizing: border-box; }
    .zameen-input:focus, .zameen-select:focus { outline: none; border-color: #007c38; box-shadow: 0 0 0 3px rgba(39,167,118,0.1); }
    .zameen-footer { background: #f8f9fa; padding: 1.25rem 1.5rem; border-top: 1px solid #f0f0f0; display: flex; justify-content: flex-end; gap: 1rem; flex-shrink: 0; }
        .zameen-btn { padding: 0.35rem 0.85rem; font-size: 1rem; border-radius: 8px; font-weight: 500; cursor: pointer; }
</style>

{{ Form::open(array('url' => 'revenue','enctype' => 'multipart/form-data','class'=>'needs-validation','novalidate')) }}
<div class="zameen-user-container">
    <div class="zameen-user-card">
        <div class="zameen-user-header">
            <h2>{{ __('Create Revenue') }}</h2>
            <p>{{ __('Add a new revenue entry') }}</p>
        </div>
        <div class="zameen-form-container">
            <div class="zameen-form-group">
                <label class="zameen-label">{{ __('Date') }}<span class="zameen-required">*</span></label>
                {{ Form::date('date', date('Y-m-d'), ['class' => 'zameen-input', 'required' => 'required']) }}
            </div>
            <div class="zameen-form-group">
                <label class="zameen-label">{{ __('Amount') }}<span class="zameen-required">*</span></label>
                {{ Form::number('amount', '', ['class' => 'zameen-input', 'required' => 'required', 'step' => '0.01', 'placeholder' => __('Enter amount')]) }}
            </div>
            <div class="zameen-form-group">
                <label class="zameen-label">{{ __('Account') }}<span class="zameen-required">*</span></label>
                {{ Form::select('account_id', $accounts, null, ['class' => 'zameen-input zameen-select', 'required' => 'required']) }}
            </div>
            <div class="zameen-form-group">
                <label class="zameen-label">{{ __('Customer') }}<span class="zameen-required">*</span></label>
                {{ Form::select('customer_id', $customers, null, ['class' => 'zameen-input zameen-select', 'required' => 'required']) }}
            </div>
            <div class="zameen-form-group">
                <label class="zameen-label">{{ __('Description') }}</label>
                {{ Form::textarea('description', '', ['class' => 'zameen-input', 'rows' => 3, 'placeholder' => __('Enter description (optional)')]) }}
            </div>
            <div class="zameen-form-group">
                <label class="zameen-label">{{ __('Category') }}<span class="zameen-required">*</span></label>
                {{ Form::select('category_id', $categories, null, ['class' => 'zameen-input zameen-select', 'required' => 'required']) }}
                <div class="text-muted small mt-1">
                    {{ __('Need to add a new category? ') }}<a href="#" id="add_category" class="text-primary fw-semibold text-decoration-none"><i class="fas fa-plus-circle me-1"></i>{{ __('Add Category') }}</a>
                </div>
            </div>
            <div class="zameen-form-group">
                <label class="zameen-label">{{ __('Reference') }}</label>
                {{ Form::text('reference', '', ['class' => 'zameen-input', 'placeholder' => __('Enter reference number (optional)')]) }}
            </div>
            <div class="zameen-form-group">
                <label class="zameen-label">{{ __('Payment Receipt') }}</label>
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
        <div class="zameen-footer">
            <button type="button" class="zameen-btn" style="background: #fff; color: #007c38; border: 1.5px solid #007c38; transition: background 0.2s, color 0.2s; padding: 0.35rem 0.85rem;" data-bs-dismiss="modal"
                onmouseover="this.style.background='#007c38';this.style.color='#fff'" onmouseout="this.style.background='#fff';this.style.color='#007c38'">
                <svg style="width: 16px; height: 16px; margin-right: 0.5rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
                {{ __('Cancel') }}
            </button>
            <button type="submit" class="zameen-btn" style="background: linear-gradient(135deg, #007c38 0%, #10b981 100%); color: #fff; border: none; transition: background 0.2s; padding: 0.35rem 0.85rem;">
                <svg style="width: 16px; height: 16px; margin-right: 0.5rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                {{ __('Create') }}
            </button>
        </div>
    </div>
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
<div class="modal-footer" style="background: #f8f9fa; border-top: 1px solid #e0e0e0; padding: 1.5rem 2rem; display: flex; justify-content: flex-end; gap: 1rem; border-radius: 0 0 8px 8px;">
    <input type="button" value="{{ __('Cancel') }}" class="btn btn-light" data-bs-dismiss="modal" style="border-radius: 8px; padding: 0.75rem 1.5rem; border: 1.5px solid #e0e0e0; color: #2d3748; font-weight: 500; background: #fff;">
    <input type="submit" value="{{ __('Create') }}" class="btn btn-success" style="background: #007c38; color: #fff; border-radius: 8px; padding: 0.75rem 1.5rem; font-weight: 500; border: none;">
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
