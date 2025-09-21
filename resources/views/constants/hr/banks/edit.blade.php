<script src="{{ asset('js/unsaved.js') }}"></script>
<form action="{{ route('banks.update', $bank->id) }}" method="POST">
  @include('constants.hr.partials._form', [
    'method' => 'PUT',
    'label' => __('Bank Name'),
    'value' => $bank->name,
    'button' => __('Update')
  ])
</form>