<script src="{{ asset('js/unsaved.js') }}"></script>

@extends('layouts.admin')
@section('page-title') {{ __('Create BOM') }} @endsection

@section('breadcrumb')
  <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
  <li class="breadcrumb-item"><a href="{{ route('bom.index') }}">{{ __('BOM') }}</a></li>
  <li class="breadcrumb-item">{{ __('Create') }}</li>
@endsection

@push('script-page')
<script src="{{ asset('js/jquery-ui.min.js') }}"></script>
<script src="{{ asset('js/jquery.repeater.min.js') }}"></script>
<script src="{{ asset('js/jquery-searchbox.js') }}"></script>
<script>
  $(function(){
    $('.repeater-inputs').repeater({
      initEmpty: false,
      show: function(){ $(this).slideDown(); if (typeof JsSearchBox === 'function') JsSearchBox(); },
      hide: function(deleteElement){ if(confirm('{{ __('Are you sure?') }}')) { $(this).slideUp(deleteElement); $(this).remove(); } }
    });
    $('.repeater-outputs').repeater({
      initEmpty: false,
      show: function(){ $(this).slideDown(); if (typeof JsSearchBox === 'function') JsSearchBox(); },
      hide: function(deleteElement){ if(confirm('{{ __('Are you sure?') }}')) { $(this).slideUp(deleteElement); $(this).remove(); } }
    });
  });
</script>
@endpush

@section('content')
<div class="row">
  {{ Form::open(['route' => 'bom.store', 'class'=>'w-100 needs-validation','novalidate']) }}
  <div class="col-12">
    <div class="card shadow-sm border-0 rounded-3">
      <div class="card-header bg-white border-0 pb-0">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
          <div class="d-flex align-items-center gap-2">
            <span class="rounded-full bg-[#007c38] w-4 h-4"></span>
            <h5 class="mb-0 fw-bold">{{ __('Bill of Materials') }}</h5>
          </div>
          <small class="text-muted">{{ __('Create a batch recipe using inputs and outputs') }}</small>
        </div>
      </div>

      <div class="card-body">
        <div class="row gx-4">
          <div class="col-md-6">
            {{ Form::label('name', __('BOM Name'), ['class' => 'form-label fw-semibold']) }}<x-required></x-required>
            {{ Form::text('name', null, ['class'=>'form-control','required']) }}
          </div>

          <div class="col-md-3">
            <div class="form-group">
              {{ Form::label('code', __('BOM Code'), ['class' => 'form-label fw-semibold']) }} <x-required/>
              <div class="input-group">
                {{ Form::text('code', old('code', $code ?? ''), ['class'=>'form-control','id'=>'bom_code','required'=>true]) }}
                <span class="input-group-text bg-light text-muted">{{ __('Auto') }}</span>
              </div>
              <small class="text-muted">{{ __('You can edit the suggested code if needed') }}</small>
            </div>
          </div>

          <div class="col-md-3">
            {{ Form::label('is_active', __('Active'), ['class'=>'form-label fw-semibold']) }}
            {{-- switched to form-select for nicer native style --}}
            {{ Form::select('is_active', [1=>__('Yes'),0=>__('No')], 1, ['class'=>'form-select']) }}
          </div>

          <div class="col-12">
            {{ Form::label('notes', __('Notes'), ['class'=>'form-label fw-semibold']) }}
            {{ Form::textarea('notes', null, ['class'=>'form-control','rows'=>2,'placeholder'=>__('Optional: recipe notes, cautions, etc.')]) }}
          </div>
        </div>

        {{-- Raw Materials --}}
        <div class="mt-4 card border-0 shadow-sm">
          <div class="card-header bg-white d-flex align-items-center justify-content-between">
            <h5 class="h6 mb-0 fw-bold">{{ __('Raw Materials') }}</h5>
            <a href="javascript:void(0)" data-repeater-create class="btn btn-sm btn-primary d-inline-flex align-items-center gap-2">
              <i class="ti ti-plus"></i> <span>{{ __('Add raw material') }}</span>
            </a>
          </div>
          <div class="card-body pt-0">
            <div class="table-responsive">
              <table class="table table-hover table-sm align-middle mb-2" data-repeater-list="inputs">
                <thead class="table-light">
                  <tr>
                    <th class="text-uppercase small text-muted">{{ __('Item') }}</th>
                    <th class="text-start text-uppercase small text-muted" style="width:30%">{{ __('Qty / Batch') }}</th>
                    <th style="width:10%"></th>
                  </tr>
                </thead>

                <tbody data-repeater-item>
                  <tr>
                    <td>
                      {{ Form::select('product_id', $rawProducts, null, ['class'=>'form-control item select js-searchBox','required'=>true]) }}
                    </td>
                    <td>
                      {{ Form::number('qty_per_batch', null, ['class'=>'form-control text-start','step'=>'0.0001','min'=>'0.0001','required'=>true,'placeholder'=>'0.0000']) }}
                    </td>
                    <td>
                      <div class="d-flex justify-content-end" data-repeater-delete>
                        <a href="#" class="btn btn-outline-danger btn-sm d-inline-flex align-items-center">
                          <i class="ti ti-trash me-1"></i> {{ __('Remove') }}
                        </a>
                      </div>
                    </td>
                  </tr>
                </tbody>

                <tfoot>
                  <tr>
                    <td colspan="3" class="small text-muted">
                      {{ __('Add all components needed to make one batch.') }}
                    </td>
                  </tr>
                </tfoot>
              </table>
            </div>
          </div>
        </div>

        {{-- Finished Products --}}
        <div class="mt-4 card border-0 shadow-sm">
          <div class="card-header bg-white d-flex align-items-center justify-content-between">
            <h5 class="h6 mb-0 fw-bold">{{ __('Finished Products (Outputs)') }}</h5>
            <a href="javascript:void(0)" data-repeater-create class="btn btn-sm btn-primary d-inline-flex align-items-center gap-2">
              <i class="ti ti-plus"></i> <span>{{ __('Add finished product') }}</span>
            </a>
          </div>
          <div class="card-body pt-0">
            <div class="table-responsive">
              <table class="table table-hover table-sm align-middle mb-2" data-repeater-list="outputs">
                <thead class="table-light">
                  <tr>
                    <th class="text-uppercase small text-muted">{{ __('Product') }}</th>
                    <th class="text-start text-uppercase small text-muted" style="width:30%">{{ __('Qty / Batch') }}</th>
                    <th style="width:10%"></th>
                  </tr>
                </thead>

                <tbody data-repeater-item>
                  <tr>
                    <td>
                      {{ Form::select('product_id', $finishedProducts, null, ['class'=>'form-control item select js-searchBox','required'=>true]) }}
                    </td>
                    <td>
                      {{ Form::number('qty_per_batch', null, ['class'=>'form-control text-start','step'=>'0.0001','min'=>'0.0001','required'=>true,'placeholder'=>'0.0000']) }}
                    </td>
                    <td>
                      <div class="d-flex justify-content-end" data-repeater-delete>
                        <a href="#" class="btn btn-outline-danger btn-sm d-inline-flex align-items-center">
                          <i class="ti ti-trash me-1"></i> {{ __('Remove') }}
                        </a>
                      </div>
                    </td>
                  </tr>
                </tbody>

                <tfoot>
                  <tr>
                    <td colspan="3" class="small text-muted">
                      {{ __('Add one or more finished items per batch.') }}
                    </td>
                  </tr>
                </tfoot>
              </table>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>

  <div class="modal-footer position-sticky bottom-0 border-top  d-flex justify-content-end">
    <a href="{{ route('bom.index') }}" class="btn btn-light me-2">{{ __('Cancel') }}</a>
    <button type="submit" class="btn btn-primary">{{ __('Create') }}</button>
  </div>
  {{ Form::close() }}
</div>

@endsection

@push('script-page')
<script>
async function generateBOMCode() {
  const res = await fetch("{{ route('boms.generateCode') }}");
  if(res.ok){
    const data = await res.json();
    const input = document.getElementById('bom_code');
    if(input && !input.value){ // only prefill if empty
      input.value = data.code;
    }
  }
}

document.addEventListener('DOMContentLoaded', function(){
  generateBOMCode(); // auto when page loads
});
</script>
@endpush
