@csrf
@if(!empty($method) && strtoupper($method) === 'PUT')
  @method('PUT')
@endif

<div class="modal-body bg-[#FAFBFC]">
  <div class="bg-white p-6 rounded-[8px] border border-[#E5E7EB] shadow-sm overflow-hidden">
    <div class="row">
      <div class="form-group col-md-12">
        <label class="form-label text-[13px] font-[600] text-[#374151]">{{ $label ?? __('Name') }}</label>
        <input type="text" name="name" value="{{ old('name', $value ?? '') }}" required
               class="form-control" placeholder="{{ $placeholder ?? __('Enter name') }}">
        @error('name')
          <small class="text-red-600">{{ $message }}</small>
        @enderror
      </div>
    </div>
  </div>
</div>

<div class="modal-footer border-t border-[#E5E5E5] bg-[#FAFAFA] px-6 py-4 flex justify-end gap-3">
  <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
  <button type="submit" class="btn btn-primary">{{ $button ?? __('Save') }}</button>
</div>
