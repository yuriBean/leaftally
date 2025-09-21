<script src="{{ asset('js/unsaved.js') }}"></script>

@extends('layouts.admin')
@section('page-title') {{ __('Edit BOM') }} @endsection

@section('breadcrumb')
  <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
  <li class="breadcrumb-item"><a href="{{ route('bom.index') }}">{{ __('BOM') }}</a></li>
  <li class="breadcrumb-item">{{ $bom->code }}</li>
@endsection

@push('script-page')
<script src="{{ asset('js/jquery-ui.min.js') }}"></script>
<script src="{{ asset('js/jquery.repeater.min.js') }}"></script>
<script src="{{ asset('js/jquery-searchbox.js') }}"></script>
<script>
  $(function(){
    const presetInputs = @json(
      $bom->inputs->map(fn($i)=>[
        'product_id'    => $i->product_id,
        'qty_per_batch' => (float)$i->qty_per_batch,
      ])
    );
    const presetOutputs = @json(
      $bom->outputs->map(fn($o)=>[
        'product_id'    => $o->product_id,
        'qty_per_batch' => (float)$o->qty_per_batch,
      ])
    );

    const $ri = $('.repeater-inputs').repeater({
      initEmpty: presetInputs.length === 0,
      show: function(){ $(this).slideDown(); if (typeof JsSearchBox === 'function') JsSearchBox(); },
      hide: function(deleteElement){ if(confirm('{{ __('Are you sure?') }}')) { $(this).slideUp(deleteElement); $(this).remove(); } },
    });
    const $ro = $('.repeater-outputs').repeater({
      initEmpty: presetOutputs.length === 0,
      show: function(){ $(this).slideDown(); if (typeof JsSearchBox === 'function') JsSearchBox(); },
      hide: function(deleteElement){ if(confirm('{{ __('Are you sure?') }}')) { $(this).slideUp(deleteElement); $(this).remove(); } },
    });

    if(presetInputs.length){ $ri.setList(presetInputs); }
    if(presetOutputs.length){ $ro.setList(presetOutputs); }
  });
</script>
@endpush

@section('content')
<div class="row">
  {{ Form::model($bom, ['route' => ['bom.update', $bom->id], 'method' => 'PUT', 'class'=>'w-100 needs-validation', 'novalidate']) }}
  <div class="col-12">
    <div class="card">
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-3">
            {{ Form::label('code', __('BOM Code'), ['class'=>'form-label']) }}
            {{ Form::text('code', $bom->code, ['class'=>'form-control','readonly']) }}
          </div>
          <div class="col-md-5">
            {{ Form::label('name', __('BOM Name'), ['class'=>'form-label']) }}<x-required></x-required>
            {{ Form::text('name', $bom->name, ['class'=>'form-control','required']) }}
          </div>
          <div class="col-md-2">
            {{ Form::label('is_active', __('Active'), ['class'=>'form-label']) }}
            {{ Form::select('is_active', [1=>__('Yes'),0=>__('No')], $bom->is_active ? 1 : 0, ['class'=>'form-control']) }}
          </div>
          <div class="col-md-12">
            {{ Form::label('notes', __('Notes'), ['class'=>'form-label']) }}
            {{ Form::textarea('notes', $bom->notes, ['class'=>'form-control','rows'=>2]) }}
          </div>
        </div>

        {{-- Raw Materials --}}
        <div class="mt-4 card repeater-inputs">
          <div class="card-body table-border-style">
            <div class="d-flex align-items-center justify-content-between border-bottom pb-3 mb-4">
              <h5 class="h4">{{ __('Raw Materials') }}</h5>
              <a href="javascript:void(0)" data-repeater-create class="btn btn-primary">
                <i class="ti ti-plus"></i> {{ __('Add raw material') }}
              </a>
            </div>
            <div class="table-responsive">
              <table class="table table-custom-style" data-repeater-list="inputs">
                <thead>
                  <tr>
                    <th>{{ __('Item') }}</th>
                    <th class="text-end">{{ __('Qty / Batch') }}</th>
                    <th></th>
                  </tr>
                </thead>
                <tbody data-repeater-item>
                  <tr>
                    <td style="width:60%">
                      {{ Form::select('product_id', $rawProducts, null, ['class'=>'form-control item select js-searchBox','required'=>true]) }}
                    </td>
                    <td style="width:30%">
                      {{ Form::number('qty_per_batch', null, ['class'=>'form-control text-end','step'=>'0.0001','min'=>'0.0001','required'=>true]) }}
                    </td>
                    <td style="width:10%">
                      <div class="action-btn float-end" data-repeater-delete>
                        <a href="#" class="btn btn-sm d-inline-flex align-items-center m-2 p-2 bg-danger">
                          <i class="ti ti-trash text-white"></i>
                        </a>
                      </div>
                    </td>
                  </tr>
                </tbody>
                <tfoot><tr><td colspan="3" class="small text-muted">{{ __('List every raw material needed.') }}</td></tr></tfoot>
              </table>
            </div>
          </div>
        </div>

        {{-- Finished Products --}}
        <div class="mt-4 card repeater-outputs">
          <div class="card-body table-border-style">
            <div class="d-flex align-items-center justify-content-between border-bottom pb-3 mb-4">
              <h5 class="h4">{{ __('Finished Products (Outputs)') }}</h5>
              <a href="javascript:void(0)" data-repeater-create class="btn btn-primary">
                <i class="ti ti-plus"></i> {{ __('Add finished product') }}
              </a>
            </div>
            <div class="table-responsive">
              <table class="table table-custom-style" data-repeater-list="outputs">
                <thead>
                  <tr>
                    <th>{{ __('Product') }}</th>
                    <th class="text-end">{{ __('Qty / Batch') }}</th>
                    <th></th>
                  </tr>
                </thead>
                <tbody data-repeater-item>
                  <tr>
                    <td style="width:60%">
                      {{ Form::select('product_id', $finishedProducts, null, ['class'=>'form-control item select js-searchBox','required'=>true]) }}
                    </td>
                    <td style="width:30%">
                      {{ Form::number('qty_per_batch', null, ['class'=>'form-control text-end','step'=>'0.0001','min'=>'0.0001','required'=>true]) }}
                    </td>
                    <td style="width:10%">
                      <div class="action-btn float-end" data-repeater-delete>
                        <a href="#" class="btn btn-sm d-inline-flex align-items-center m-2 p-2 bg-danger">
                          <i class="ti ti-trash text-white"></i>
                        </a>
                      </div>
                    </td>
                  </tr>
                </tbody>
                <tfoot><tr><td colspan="3" class="small text-muted">{{ __('Add one or more finished items per batch.') }}</td></tr></tfoot>
              </table>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>

  <div class="modal-footer">
    <a href="{{ route('bom.index') }}" class="btn btn-light mx-3">{{ __('Cancel') }}</a>
    <button class="btn btn-primary" type="submit">{{ __('Update') }}</button>
  </div>
  {{ Form::close() }}
</div>
@endsection
