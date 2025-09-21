<script src="{{ asset('js/unsaved.js') }}"></script>

<form action="{{ route('banks.store') }}" method="POST">
  @include('constants.hr.partials._form', [
    'label' => __('Bank Name'),
    'placeholder' => __('e.g. Standard Chartered'),
    'button' => __('Create')
  ])
</form>