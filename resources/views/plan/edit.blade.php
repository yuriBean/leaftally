<script src="{{ asset('js/unsaved.js') }}"></script>

@php
    $chatGPT = \App\Models\Utility::settings('enable_chatgpt');
    $enable_chatgpt = !empty($chatGPT);
    $admin = \App\Models\Utility::getAdminPaymentSetting();
@endphp

{{ Form::model($plan, ['route' => ['plans.update', $plan->id], 'method' => 'PUT', 'class' => 'needs-validation', 'novalidate']) }}

<div class="modal-body p-0">
  <div class="d-flex align-items-center justify-content-between px-4 py-3 border-bottom bg-white sticky-top" style="top:0;z-index:2;">
    <div class="d-flex align-items-center gap-2">
      <span class="badge text-bg-primary rounded-pill d-inline-flex align-items-center justify-content-center" style="width:36px;height:36px;">
        <i class="ti ti-pencil"></i>
      </span>
      <div>
        <div class="fw-semibold">{{ __('Edit Plan') }}</div>
        <small class="text-muted">{{ $plan->name }}</small>
      </div>
    </div>
    <div class="d-flex align-items-center gap-2">
      @if ($enable_chatgpt)
        <a href="#"
           data-size="md"
           data-ajax-popup-over="true"
           data-url="{{ route('generate', ['plan']) }}"
           data-bs-toggle="tooltip"
           title="{{ __('Generate content with AI') }}"
           data-title="{{ __('Generate content with AI') }}"
           class="btn btn-outline-secondary btn-sm">
          <i class="fas fa-robot me-1"></i>{{ __('Generate with AI') }}
        </a>
      @endif
      <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
      <button class="btn btn-primary btn-sm"><i class="ti ti-check me-1"></i>{{ __('Update') }}</button>
    </div>
  </div>

  <div class="px-4 py-4">
    <!-- BASICS -->
    <div class="card border-0 shadow-sm mb-4">
      <div class="card-header bg-white border-0">
        <div class="fw-semibold">{{ __('Basics') }}</div>
        <small class="text-muted">{{ __('Name, billing, price & description') }}</small>
      </div>
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-8">
            {{ Form::label('name', __('Name'), ['class' => 'form-label']) }}<x-required></x-required>
            {{ Form::text('name', null, ['class' => 'form-control form-control-lg', 'required' => 'required']) }}
          </div>
          @if ($plan->id != 1)
            <div class="col-md-4">
              {{ Form::label('duration', __('Duration'), ['class' => 'form-label']) }}<x-required></x-required>
              {!! Form::select('duration', $arrDuration, null, ['class' => 'form-select', 'required' => 'required']) !!}
            </div>
          @endif
          @if ($plan->price > 0)
            <div class="col-md-4">
              {{ Form::label('price', __('Price'), ['class' => 'form-label']) }}<x-required></x-required>
              <div class="input-group">
                <span class="input-group-text">{{ !empty($admin['currency_symbol']) ? $admin['currency_symbol'] : '$' }}</span>
                {{ Form::number('price', null, ['class' => 'form-control', 'required' => 'required', 'min'=>'0','step'=>'0.01']) }}
              </div>
            </div>
          @endif
          @if ($plan->id != 1)
            <div class="col-md-4">
              <label class="form-label d-flex align-items-center justify-content-between mb-1">
                <span>{{ __('Trial') }}</span>
                <span class="form-check form-switch m-0">
                  <input type="checkbox" name="trial" class="form-check-input" value="1" id="trial" {{ $plan['trial'] ? 'checked' : '' }}>
                </span>
              </label>
              <div class="input-group plan_div {{ $plan['trial'] ? '' : 'd-none' }}">
                {{ Form::number('trial_days', null, ['class' => 'form-control', 'placeholder' => __('Trial days'), 'min'=>'1','step'=>'1']) }}
                <span class="input-group-text">{{ __('days') }}</span>
              </div>
            </div>
          @endif
          <div class="col-12">
            {{ Form::label('description', __('Short Description'), ['class' => 'form-label']) }}
            {!! Form::textarea('description', null, ['class' => 'form-control', 'rows' => 3]) !!}
          </div>
          <div class="col-12">
            <div class="form-check form-switch">
              <input type="checkbox" class="form-check-input" name="enable_chatgpt" id="enable_chatgpt" {{ $plan->enable_chatgpt == 'on' ? 'checked' : '' }}>
              <label class="form-check-label" for="enable_chatgpt">{{ __('Enable ChatGPT features') }}</label>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- GLOBAL LIMITS (only storage) -->
    <div class="card border-0 shadow-sm mb-4">
      <div class="card-header bg-white border-0">
        <div class="fw-semibold">{{ __('Global Limits') }}</div>
        <small class="text-muted">{{ __('Use -1 for Unlimited') }}</small>
      </div>
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-6">
            {{ Form::label('storage_limit', __('Storage Limit'), ['class' => 'form-label']) }}<x-required></x-required>
            <div class="input-group">
              <input type="number" name="storage_limit" value="{{ $plan->storage_limit }}" class="form-control" required>
              <span class="input-group-text">{{ __('MB') }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- FEATURES MATRIX -->
    <div class="card border-0 shadow-sm">
      <div class="card-header bg-white border-0">
        <div class="fw-semibold">{{ __('Feature Matrix') }}</div>
        <small class="text-muted">{{ __('Toggle features. If a quota applies, set it (blank or -1 = Unlimited).') }}</small>
      </div>

      <div class="card-body">
        <div class="table-responsive">
          <table class="table align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th style="width:45%">{{ __('Feature') }}</th>
                <th class="text-center" style="width:15%">{{ __('Enabled') }}</th>
                <th style="width:40%">{{ __('Quota (optional)') }}</th>
              </tr>
            </thead>
            <tbody>
              @php
                $rowLockedOn = function($plan, $id, $label, $hasQuota=false, $quotaId=null, $note=null) {
                  $val = $quotaId ? old($quotaId, $plan->{$quotaId} ?? '') : '';
                  echo '<tr>';
                  echo '<td class="fw-semibold">'.$label;
                  if ($note) echo ' <small class="text-muted">('.e($note).')</small>';
                  echo '</td>';
                  echo '<td class="text-center">';
                  echo '<input type="checkbox" class="form-check-input" checked disabled>';
                  echo '<input type="hidden" name="'.$id.'" value="1">';
                  echo '</td>';
                  echo '<td>';
                  if ($hasQuota) {
                    echo '<div class="input-group"><span class="input-group-text">'.__('Limit').'</span>';
                    echo '<input type="number" class="form-control" id="'.$quotaId.'" name="'.$quotaId.'" value="'.$val.'" placeholder="'.__('-1 or blank = Unlimited').'">';
                    echo '</div>';
                  } else {
                    echo '<span class="text-muted small">—</span>';
                  }
                  echo '</td>';
                  echo '</tr>';
                };

                $rowToggle = function($plan, $id, $label, $hasQuota=false, $quotaId=null) {
                  $on  = old($id, $plan->{$id} ?? false) ? 'checked' : '';
                  $val = $quotaId ? old($quotaId, $plan->{$quotaId} ?? '') : '';
                  echo '<tr>';
                  echo '<td class="fw-semibold">'.$label.'</td>';
                  echo '<td class="text-center"><input type="checkbox" class="form-check-input feature-toggle" id="'.$id.'" name="'.$id.'" '.$on.'></td>';
                  echo '<td>';
                  if ($hasQuota) {
                    echo '<div class="input-group"><span class="input-group-text">'.__('Limit').'</span>';
                    echo '<input type="number" class="form-control feature-quota" id="'.$quotaId.'" name="'.$quotaId.'" value="'.$val.'" placeholder="'.__('-1 or blank = Unlimited').'">';
                    echo '</div>';
                  } else {
                    echo '<span class="text-muted small">—</span>';
                  }
                  echo '</td>';
                  echo '</tr>';
                };
              @endphp

              {{-- Toggles (editable) --}}
              {!! $rowToggle($plan, 'user_access_management', __('User access management'), true, 'max_users') !!}
              {!! $rowToggle($plan, 'payroll_enabled', __('Payroll management'), true, 'payroll_quota') !!}
              {!! $rowToggle($plan, 'budgeting_enabled', __('Budgeting & forecasting')) !!}
              {!! $rowToggle($plan, 'tax_management_enabled', __('Tax management')) !!}
              {!! $rowToggle($plan, 'audit_trail_enabled', __('Audit trail')) !!}
              {!! $rowToggle($plan, 'manufacturing_enabled', __('Manufacturing'), true, 'manufacturing_quota') !!}

              {{-- Always ON + quotas --}}
              {!! $rowLockedOn($plan, 'invoice_enabled', __('Invoice management'), true, 'invoice_quota') !!}
              {!! $rowLockedOn($plan, 'product_management_enabled', __('Product management'), true, 'product_quota') !!}
              {!! $rowLockedOn($plan, 'client_management_enabled', __('Client management'), true, 'client_quota') !!}
              {!! $rowLockedOn($plan, 'vendor_management_enabled', __('Vendor management'), true, 'vendor_quota') !!}

              {{-- Inventory (derived) --}}
              {!! $rowLockedOn($plan, 'inventory_enabled', __('Inventory (auto with Product)')) !!}
            </tbody>
          </table>
        </div>
      </div>
    </div>

  </div>
</div>

<div class="modal-footer">
  <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
  <button type="submit" class="btn btn-primary">{{ __('Update') }}</button>
</div>

{{ Form::close() }}

<script>
  (function () {
    const root = $('#commonModal').length ? $('#commonModal') : $(document);

    root.on('change', '#trial', function(){
      root.find('.plan_div').toggleClass('d-none', !this.checked);
    });

    const pairs = {
      'user_access_management' : 'max_users',
      'payroll_enabled'        : 'payroll_quota',
      'manufacturing_enabled'  : 'manufacturing_quota',
    };
    function syncQuotas(){
      Object.keys(pairs).forEach(function(tid){
        const qid = pairs[tid];
        const on  = root.find('#'+tid).is(':checked');
        const $q  = root.find('#'+qid);
        if(!$q.length) return;
        $q.prop('disabled', !on).toggleClass('bg-light', !on);
        if(!on && !$q.val()) $q.attr('placeholder','{{ __("-1 or blank = Unlimited") }}');
      });
    }
    root.on('change', Object.keys(pairs).map(id=>'#'+id).join(','), syncQuotas);
    syncQuotas();
  })();
</script>
