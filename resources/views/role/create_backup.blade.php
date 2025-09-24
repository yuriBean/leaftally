<style>
  :root {
    --zameen-primary: #27a776;
    --zameen-primary-light: #33c182;
    --zameen-primary-dark: #1e8863;
    --zameen-secondary: #3f51b5;
    --zameen-success: #4caf50;
    --zameen-danger: #f44336;
    --zameen-warning: #ff9800;
    --zameen-info: #2196f3;
    --zameen-light: #f8f9fa;
    --zameen-dark: #212529;
    --zameen-gray-100: #f8f9fa;
    --zameen-gray-200: #e9ecef;
    --zameen-gray-300: #dee2e6;
    --zameen-gray-400: #ced4da;
    --zameen-gray-500: #adb5bd;
    --zameen-gray-600: #6c757d;
    --zameen-gray-700: #495057;
    --zameen-gray-800: #343a40;
    --zameen-gray-900: #212529;
    --zameen-border: #e0e0e0;
    --zameen-border-light: #f0f0f0;
    --zameen-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    --zameen-shadow-lg: 0 4px 20px rgba(0, 0, 0, 0.15);
    --zameen-radius: 8px;
    --zameen-radius-lg: 12px;
  }

  .zameen-role-container {
    background: #f8f9fa;
    padding: 1rem;
    max-height: 90vh;
    overflow-y: auto;
  }

  .zameen-role-card {
    background: white;
    border-radius: var(--zameen-radius-lg);
    box-shadow: var(--zameen-shadow);
    overflow: hidden;
    max-width: 900px;
    margin: 0 auto;
    max-height: 85vh;
    display: flex;
    flex-direction: column;
  }

  .zameen-role-header {
    background: linear-gradient(135deg, var(--zameen-primary) 0%, var(--zameen-primary-light) 100%);
    padding: 1.5rem 2rem;
    color: white;
    text-align: center;
    flex-shrink: 0;
  }

  .zameen-role-header h2 {
    margin: 0;
    font-size: 1.5rem;
    font-weight: 600;
    margin-bottom: 0.25rem;
  }

  .zameen-role-header p {
    margin: 0;
    opacity: 0.9;
    font-size: 0.875rem;
  }

  .zameen-role-form-container {
    padding: 1.5rem 2rem;
    overflow-y: auto;
    flex: 1;
  }

  .zameen-form-group {
    margin-bottom: 1.5rem;
  }

  .zameen-label {
    font-weight: 500;
    color: var(--zameen-gray-700);
    margin-bottom: 0.5rem;
    font-size: 0.875rem;
    display: block;
  }

  .zameen-required {
    color: var(--zameen-danger);
    margin-left: 0.25rem;
  }

  .zameen-input {
    padding: 0.75rem 1rem;
    border: 1px solid var(--zameen-gray-300);
    border-radius: var(--zameen-radius);
    font-size: 1rem;
    transition: all 0.2s ease;
    background: white;
    width: 100%;
    box-sizing: border-box;
  }

  .zameen-input:focus {
    outline: none;
    border-color: var(--zameen-primary);
    box-shadow: 0 0 0 3px rgba(39, 167, 118, 0.1);
  }

  .zameen-input:hover {
    border-color: var(--zameen-gray-400);
  }

  .zameen-permissions-section {
    margin-top: 2rem;
  }

  .zameen-permissions-title {
    font-size: 1rem;
    font-weight: 600;
    color: var(--zameen-gray-800);
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }

  .zameen-permissions-icon {
    width: 20px;
    height: 20px;
    color: var(--zameen-primary);
  }

  .zameen-permissions-table {
    border: 1px solid var(--zameen-gray-300);
    border-radius: var(--zameen-radius);
    overflow: hidden;
    background: white;
    box-shadow: var(--zameen-shadow);
  }

  .zameen-table-header {
    background: linear-gradient(135deg, var(--zameen-primary) 0%, var(--zameen-primary-light) 100%);
    color: white;
  }

  .zameen-table-header th {
    padding: 1rem;
    font-weight: 500;
    font-size: 0.875rem;
    border: none;
  }

  .zameen-table-header th:first-child {
    border-right: 1px solid rgba(255, 255, 255, 0.2);
  }

  .zameen-table-row {
    border-bottom: 1px solid var(--zameen-gray-200);
    transition: background-color 0.2s ease;
  }

  .zameen-table-row:hover {
    background: var(--zameen-gray-50);
  }

  .zameen-table-row:last-child {
    border-bottom: none;
  }

  .zameen-table-cell {
    padding: 1rem;
    vertical-align: middle;
    border: none;
  }

  .zameen-table-cell:first-child {
    border-right: 1px solid var(--zameen-gray-200);
    background: var(--zameen-gray-50);
    font-weight: 500;
    color: var(--zameen-gray-700);
  }

  .zameen-module-checkbox {
    margin-right: 0.5rem;
    accent-color: var(--zameen-primary);
  }

  .zameen-permission-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
    gap: 0.75rem;
  }

  .zameen-checkbox-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.25rem;
  }

  .zameen-checkbox {
    accent-color: var(--zameen-primary);
    width: 16px;
    height: 16px;
  }

  .zameen-checkbox-label {
    font-size: 0.8125rem;
    color: var(--zameen-gray-700);
    cursor: pointer;
    margin: 0;
  }

  .zameen-checkbox-item:hover .zameen-checkbox-label {
    color: var(--zameen-primary);
  }

  .zameen-master-checkbox {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 1rem;
    padding: 0.75rem;
    background: var(--zameen-gray-100);
    border-radius: var(--zameen-radius);
    border: 1px solid var(--zameen-gray-200);
  }

  .zameen-master-checkbox input {
    accent-color: var(--zameen-primary);
  }

  .zameen-master-checkbox label {
    font-weight: 500;
    color: var(--zameen-gray-700);
    margin: 0;
    cursor: pointer;
  }

  .zameen-footer {
    background: var(--zameen-gray-100);
    padding: 1rem 2rem;
    border-top: 1px solid var(--zameen-border-light);
    display: flex;
    justify-content: flex-end;
    gap: 1rem;
    flex-shrink: 0;
  }

  .zameen-btn {
    padding: 0.75rem 1.5rem;
    border-radius: var(--zameen-radius);
    font-weight: 500;
    font-size: 0.875rem;
    cursor: pointer;
    transition: all 0.2s ease;
    border: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
  }

  .zameen-btn-primary {
    background: var(--zameen-primary);
    color: white;
  }

  .zameen-btn-primary:hover {
    background: var(--zameen-primary-dark);
    transform: translateY(-1px);
    box-shadow: var(--zameen-shadow);
  }

  .zameen-btn-outline {
    background: transparent;
    color: var(--zameen-gray-600);
    border: 1px solid var(--zameen-gray-300);
  }

  .zameen-btn-outline:hover {
    background: var(--zameen-gray-100);
    border-color: var(--zameen-gray-400);
  }

  .zameen-error {
    color: var(--zameen-danger);
    font-size: 0.75rem;
    margin-top: 0.25rem;
  }

  @media (max-width: 768px) {
    .zameen-role-form-container {
      padding: 1rem;
    }
    
    .zameen-role-container {
      padding: 0.5rem;
    }

    .zameen-permission-grid {
      grid-template-columns: repeat(auto-fit, minmax(100px, 1fr));
      gap: 0.5rem;
    }

    .zameen-table-cell {
      padding: 0.75rem 0.5rem;
    }
  }
</style>

{{ Form::open(['url' => 'roles','method' => 'post','class' => 'needs-validation','novalidate']) }}

<div class="modal-header border-0 pb-0">
  <h4 class="modal-title text-xl font-semibold text-[#27a776]">
    <svg style="width: 20px; height: 20px; display: inline-block; margin-right: 0.5rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.196-2.12M17 20v-2a3 3 0 00-3-3h-4a3 3 0 00-3 3v2m17 0H7m10 0v-2a3 3 0 00-3-3m3 3h3m-3 0h-3"></path>
    </svg>
    {{ __('Create New Role') }}
  </h4>
  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<div class="modal-body">
  <div class="zameen-role-form-container">
      <div class="zameen-form-group">
        <label class="zameen-label">
          {{ __('Role Name') }}
          <span class="zameen-required">*</span>
        </label>
        {{ Form::text('name', null, ['class' => 'zameen-input','placeholder' => __('Enter Role Name'),'required' => 'required']) }}
        @error('name')
          <div class="zameen-error">{{ $message }}</div>
        @enderror
      </div>

    @php
      $planFeature = \App\Services\Feature::for(\Auth::user());
      $F = fn(string $k) => $planFeature->enabled($k);

      $featUserAccess    = $F(\App\Enum\PlanFeature::USER_ACCESS);
      $featPayroll       = $F(\App\Enum\PlanFeature::PAYROLL);
      $featTax           = $F(\App\Enum\PlanFeature::TAX);
      $featManufacturing = $F(\App\Enum\PlanFeature::MANUFACTURING);

$modules = [
    'dashboard','user','role','invoice','bill','revenue','payment',
    'invoice product','bill product',
    'goal','credit note','debit note','bank account','employee','set salary','pay slip','transfer','transaction',
    'product & service','customer','vender','plan','contract',
    'constant tax','constant category','constant unit','constant custom field','constant contract type',
    'constant bank','branch','designation','department','loan option','allowance option','payslip type','document type',
    'company settings','assets','chart of account','journal entry','report','bom','production','allowance',
    'commission',
    'loan',
    'saturation deduction',
    'other payment',
    'overtime'
];

      if(\Auth::user()->type === 'super admin'){
        $modules[] = 'language';
        $modules[] = 'permission';
      }

      $gateMap = [
        'user'                 => $featUserAccess,
        'role'                 => $featUserAccess,

        'constant branch'      => $featPayroll,
        'constant designation' => $featPayroll,
        'constant department'  => $featPayroll,
        'constant allowance'   => $featPayroll,
        'constant deduction'   => $featPayroll,
        'constant bonus'       => $featPayroll,

        'constant tax'         => $featTax,

        'bom'                  => $featManufacturing,
        'production'           => $featManufacturing,
      ];

      $visibleModules = array_values(array_filter($modules, fn($m) => $gateMap[$m] ?? true));

      $permNames = array_values((array) $permissions);
    @endphp

      <div class="zameen-permissions-section">
        @if(!empty($permissions))
          <div class="zameen-permissions-title">
            <svg class="zameen-permissions-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
            </svg>
            {{ __('Assign Permission to Roles') }}
          </div>

          <div class="zameen-master-checkbox">
            <input type="checkbox" class="zameen-checkbox" name="checkall" id="checkall">
            <label for="checkall">{{ __('Select All Permissions') }}</label>
          </div>

          <div class="zameen-permissions-table">
            <table class="table w-full" id="dataTable-1">
              <thead class="zameen-table-header">
                <tr>
                  <th>{{ __('Module') }}</th>
                  <th>{{ __('Permissions') }}</th>
                </tr>
              </thead>
              <tbody>
              @foreach($visibleModules as $module)
                @php
                  $slug = str_replace(' ', '', $module);
                @endphp
                <tr class="zameen-table-row">
                  <td class="zameen-table-cell">
                    <input type="checkbox" class="zameen-module-checkbox ischeck" data-id="{{ $slug }}">
                    <label class="ischeck" data-id="{{ $slug }}">{{ ucfirst($module) }}</label>
                  </td>
                  <td class="zameen-table-cell">
                    <div class="zameen-permission-grid">
                      @if(in_array('manage '.$module, $permNames))
                        @php $key = array_search('manage '.$module, $permissions); @endphp
                        @if($key !== false)
                          <div class="zameen-checkbox-item">
                            {{ Form::checkbox('permissions[]', $key, false, ['class'=>'zameen-checkbox isscheck isscheck_'.$slug,'id'=>'permission'.$key]) }}
                            {{ Form::label('permission'.$key, 'Manage', ['class' => 'zameen-checkbox-label']) }}
                          </div>
                        @endif
                      @endif

                      @if(in_array('create '.$module, $permNames))
                        @php $key = array_search('create '.$module, $permissions); @endphp
                        @if($key !== false)
                          <div class="zameen-checkbox-item">
                            {{ Form::checkbox('permissions[]', $key, false, ['class'=>'zameen-checkbox isscheck isscheck_'.$slug,'id'=>'permission'.$key]) }}
                            {{ Form::label('permission'.$key, 'Create', ['class' => 'zameen-checkbox-label']) }}
                          </div>
                        @endif
                      @endif

                      @if(in_array('edit '.$module, $permNames))
                        @php $key = array_search('edit '.$module, $permissions); @endphp
                        @if($key !== false)
                          <div class="zameen-checkbox-item">
                            {{ Form::checkbox('permissions[]', $key, false, ['class'=>'zameen-checkbox isscheck isscheck_'.$slug,'id'=>'permission'.$key]) }}
                            {{ Form::label('permission'.$key, 'Edit', ['class' => 'zameen-checkbox-label']) }}
                          </div>
                        @endif
                      @endif

                      @if(in_array('delete '.$module, $permNames))
                        @php $key = array_search('delete '.$module, $permissions); @endphp
                        @if($key !== false)
                          <div class="zameen-checkbox-item">
                            {{ Form::checkbox('permissions[]', $key, false, ['class'=>'zameen-checkbox isscheck isscheck_'.$slug,'id'=>'permission'.$key]) }}
                            {{ Form::label('permission'.$key, 'Delete', ['class' => 'zameen-checkbox-label']) }}
                          </div>
                        @endif
                      @endif

                      @if(in_array('show '.$module, $permNames))
                        @php $key = array_search('show '.$module, $permissions); @endphp
                        @if($key !== false)
                          <div class="zameen-checkbox-item">
                            {{ Form::checkbox('permissions[]', $key, false, ['class'=>'zameen-checkbox isscheck isscheck_'.$slug,'id'=>'permission'.$key]) }}
                            {{ Form::label('permission'.$key, 'Show', ['class' => 'zameen-checkbox-label']) }}
                          </div>
                        @endif
                      @endif

                      @if(in_array('buy '.$module, $permNames))
                        @php $key = array_search('buy '.$module, $permissions); @endphp
                        @if($key !== false)
                          <div class="zameen-checkbox-item">
                            {{ Form::checkbox('permissions[]', $key, false, ['class'=>'zameen-checkbox isscheck isscheck_'.$slug,'id'=>'permission'.$key]) }}
                            {{ Form::label('permission'.$key, 'Buy', ['class' => 'zameen-checkbox-label']) }}
                          </div>
                        @endif
                      @endif

                      @if(in_array('send '.$module, $permNames))
                        @php $key = array_search('send '.$module, $permissions); @endphp
                        @if($key !== false)
                          <div class="zameen-checkbox-item">
                            {{ Form::checkbox('permissions[]', $key, false, ['class'=>'zameen-checkbox isscheck isscheck_'.$slug,'id'=>'permission'.$key]) }}
                            {{ Form::label('permission'.$key, 'Send', ['class' => 'zameen-checkbox-label']) }}
                          </div>
                        @endif
                      @endif

                      @if(in_array('create payment '.$module, $permNames))
                        @php $key = array_search('create payment '.$module, $permissions); @endphp
                        @if($key !== false)
                          <div class="zameen-checkbox-item">
                            {{ Form::checkbox('permissions[]', $key, false, ['class'=>'zameen-checkbox isscheck isscheck_'.$slug,'id'=>'permission'.$key]) }}
                            {{ Form::label('permission'.$key, 'Create Payment', ['class' => 'zameen-checkbox-label']) }}
                          </div>
                        @endif
                      @endif

                      @if(in_array('delete payment '.$module, $permNames))
                        @php $key = array_search('delete payment '.$module, $permissions); @endphp
                        @if($key !== false)
                          <div class="zameen-checkbox-item">
                            {{ Form::checkbox('permissions[]', $key, false, ['class'=>'zameen-checkbox isscheck isscheck_'.$slug,'id'=>'permission'.$key]) }}
                            {{ Form::label('permission'.$key, 'Delete Payment', ['class' => 'zameen-checkbox-label']) }}
                          </div>
                        @endif
                      @endif

                      @if(in_array('duplicate '.$module, $permNames))
                        @php $key = array_search('duplicate '.$module, $permissions); @endphp
                        @if($key !== false)
                          <div class="zameen-checkbox-item">
                            {{ Form::checkbox('permissions[]', $key, false, ['class'=>'zameen-checkbox isscheck isscheck_'.$slug,'id'=>'permission'.$key]) }}
                            {{ Form::label('permission'.$key, 'Duplicate', ['class' => 'zameen-checkbox-label']) }}
                          </div>
                        @endif
                      @endif
                    </div>
                  </td>
                </tr>
              @endforeach
              </tbody>
            </table>
          </div>
        @endif
      </div>
    </div>
  </div>
</div>

<div class="zameen-footer">
  <button type="button" class="zameen-btn zameen-btn-outline" data-bs-dismiss="modal">
    <svg style="width: 16px; height: 16px; margin-right: 0.5rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
    </svg>
    {{ __('Cancel') }}
  </button>
  <button type="submit" class="zameen-btn zameen-btn-primary">
    <svg style="width: 16px; height: 16px; margin-right: 0.5rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
    </svg>
    {{ __('Create Role') }}
  </button>
</div>
                          <div class="col-md-3 custom-control custom-checkbox">
                            {{ Form::checkbox('permissions[]', $key, false, ['class'=>'form-check-input isscheck isscheck_'.$slug,'id'=>'permission'.$key]) }}
                            {{ Form::label('permission'.$key, 'Create Payment', ['class' => 'form-check-label']) }}<br>
                          </div>
                        @endif
                      @endif

                      @if(in_array('delete payment '.$module, $permNames))
                        @php $key = array_search('delete payment '.$module, $permissions); @endphp
                        @if($key !== false)
                          <div class="col-md-3 custom-control custom-checkbox">
                            {{ Form::checkbox('permissions[]', $key, false, ['class'=>'form-check-input isscheck isscheck_'.$slug,'id'=>'permission'.$key]) }}
                            {{ Form::label('permission'.$key, 'Delete Payment', ['class' => 'form-check-label']) }}<br>
                          </div>
                        @endif
                      @endif

                      @if(in_array('income '.$module, $permNames))
                        @php $key = array_search('income '.$module, $permissions); @endphp
                        @if($key !== false)
                          <div class="col-md-3 custom-control custom-checkbox">
                            {{ Form::checkbox('permissions[]', $key, false, ['class'=>'form-check-input isscheck isscheck_'.$slug,'id'=>'permission'.$key]) }}
                            {{ Form::label('permission'.$key, 'Income', ['class' => 'form-check-label']) }}<br>
                          </div>
                        @endif
                      @endif

                      @if(in_array('expense '.$module, $permNames))
                        @php $key = array_search('expense '.$module, $permissions); @endphp
                        @if($key !== false)
                          <div class="col-md-3 custom-control custom-checkbox">
                            {{ Form::checkbox('permissions[]', $key, false, ['class'=>'form-check-input isscheck isscheck_'.$slug,'id'=>'permission'.$key]) }}
                            {{ Form::label('permission'.$key, 'Expense', ['class' => 'form-check-label']) }}<br>
                          </div>
                        @endif
                      @endif

                      @if(in_array('income vs expense '.$module, $permNames))
                        @php $key = array_search('income vs expense '.$module, $permissions); @endphp
                        @if($key !== false)
                          <div class="col-md-3 custom-control custom-checkbox">
                            {{ Form::checkbox('permissions[]', $key, false, ['class'=>'form-check-input isscheck isscheck_'.$slug,'id'=>'permission'.$key]) }}
                            {{ Form::label('permission'.$key, 'Income VS Expense', ['class' => 'form-check-label']) }}<br>
                          </div>
                        @endif
                      @endif

                      @if(in_array('loss & profit '.$module, $permNames))
                        @php $key = array_search('loss & profit '.$module, $permissions); @endphp
                        @if($key !== false)
                          <div class="col-md-3 custom-control custom-checkbox">
                            {{ Form::checkbox('permissions[]', $key, false, ['class'=>'form-check-input isscheck isscheck_'.$slug,'id'=>'permission'.$key]) }}
                            {{ Form::label('permission'.$key, 'Loss & Profit', ['class' => 'form-check-label']) }}<br>
                          </div>
                        @endif
                      @endif

                      @if(in_array('tax '.$module, $permNames))
                        @php $key = array_search('tax '.$module, $permissions); @endphp
                        @if($key !== false)
                          <div class="col-md-3 custom-control custom-checkbox">
                            {{ Form::checkbox('permissions[]', $key, false, ['class'=>'form-check-input isscheck isscheck_'.$slug,'id'=>'permission'.$key]) }}
                            {{ Form::label('permission'.$key, 'Tax', ['class' => 'form-check-label']) }}<br>
                          </div>
                        @endif
                      @endif

                      @if(in_array('invoice '.$module, $permNames))
                        @php $key = array_search('invoice '.$module, $permissions); @endphp
                        @if($key !== false)
                          <div class="col-md-3 custom-control custom-checkbox">
                            {{ Form::checkbox('permissions[]', $key, false, ['class'=>'form-check-input isscheck isscheck_'.$slug,'id'=>'permission'.$key]) }}
                            {{ Form::label('permission'.$key, 'Invoice', ['class' => 'form-check-label']) }}<br>
                          </div>
                        @endif
                      @endif

                      @if(in_array('bill '.$module, $permNames))
                        @php $key = array_search('bill '.$module, $permissions); @endphp
                        @if($key !== false)
                          <div class="col-md-3 custom-control custom-checkbox">
                            {{ Form::checkbox('permissions[]', $key, false, ['class'=>'form-check-input isscheck isscheck_'.$slug,'id'=>'permission'.$key]) }}
                            {{ Form::label('permission'.$key, 'Bill', ['class' => 'form-check-label']) }}<br>
                          </div>
                        @endif
                      @endif

                      @if(in_array('duplicate '.$module, $permNames))
                        @php $key = array_search('duplicate '.$module, $permissions); @endphp
                        @if($key !== false)
                          <div class="col-md-3 custom-control custom-checkbox">
                            {{ Form::checkbox('permissions[]', $key, false, ['class'=>'form-check-input isscheck isscheck_'.$slug,'id'=>'permission'.$key]) }}
                            {{ Form::label('permission'.$key, 'Duplicate', ['class' => 'form-check-label']) }}<br>
                          </div>
                        @endif
                      @endif

                      @if(in_array('balance sheet '.$module, $permNames))
                        @php $key = array_search('balance sheet '.$module, $permissions); @endphp
                        @if($key !== false)
                          <div class="col-md-3 custom-control custom-checkbox">
                            {{ Form::checkbox('permissions[]', $key, false, ['class'=>'form-check-input isscheck isscheck_'.$slug,'id'=>'permission'.$key]) }}
                            {{ Form::label('permission'.$key, 'Balance Sheet', ['class' => 'form-check-label']) }}<br>
                          </div>
                        @endif
                      @endif

                      @if(in_array('ledger '.$module, $permNames))
                        @php $key = array_search('ledger '.$module, $permissions); @endphp
                        @if($key !== false)
                          <div class="col-md-3 custom-control custom-checkbox">
                            {{ Form::checkbox('permissions[]', $key, false, ['class'=>'form-check-input isscheck isscheck_'.$slug,'id'=>'permission'.$key]) }}
                            {{ Form::label('permission'.$key, 'Ledger', ['class' => 'form-check-label']) }}<br>
                          </div>
                        @endif
                      @endif

                      @if(in_array('trial balance '.$module, $permNames))
                        @php $key = array_search('trial balance '.$module, $permissions); @endphp
                        @if($key !== false)
                          <div class="col-md-3 custom-control custom-checkbox">
                            {{ Form::checkbox('permissions[]', $key, false, ['class'=>'form-check-input isscheck isscheck_'.$slug,'id'=>'permission'.$key]) }}
                            {{ Form::label('permission'.$key, 'Trial Balance', ['class' => 'form-check-label']) }}<br>
                          </div>
                        @endif
                      @endif

                      @if(in_array('contract '.$module, $permNames))
                        @php $key = array_search('contract '.$module, $permissions); @endphp
                        @if($key !== false)
                          <div class="col-md-3 custom-control custom-checkbox">
                            {{ Form::checkbox('permissions[]', $key, false, ['class'=>'form-check-input isscheck isscheck_'.$slug,'id'=>'permission'.$key]) }}
                            {{ Form::label('permission'.$key, 'Contract', ['class' => 'form-check-label']) }}<br>
                          </div>
                        @endif
                      @endif

                      @if(in_array('convert invoice '.$module, $permNames))
                        @php $key = array_search('convert invoice '.$module, $permissions); @endphp
                        @if($key !== false)
                          <div class="col-md-3 custom-control custom-checkbox">
                            {{ Form::checkbox('permissions[]', $key, false, ['class'=>'form-check-input isscheck isscheck_'.$slug,'id'=>'permission'.$key]) }}
                            {{ Form::label('permission'.$key, 'Convert To Invoice', ['class' => 'form-check-label']) }}<br>
                          </div>
                        @endif
                      @endif

                      @if(in_array('convert retainer '.$module, $permNames))
                        @php $key = array_search('convert retainer '.$module, $permissions); @endphp
                        @if($key !== false)
                          <div class="col-md-3 custom-control custom-checkbox">
                            {{ Form::checkbox('permissions[]', $key, false, ['class'=>'form-check-input isscheck isscheck_'.$slug,'id'=>'permission'.$key]) }}
                            {{ Form::label('permission'.$key, 'Convert To Retainer', ['class' => 'form-check-label']) }}<br>
                          </div>
                        @endif
                      @endif
                    </div>
                  </td>
                </tr>
              @endforeach
              </tbody>
            </table>
          </div>
        @endif
      </div>
    </div>
  </div>
</div>

<div class="modal-footer border-t border-[#E5E5E5] bg-[#FAFAFA] px-6 py-4 flex justify-end gap-3">
  <button type="button" class="zameen-btn zameen-btn-outline" data-bs-dismiss="modal">
    <svg style="width: 16px; height: 16px; margin-right: 0.5rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
    </svg>
    {{ __('Cancel') }}
  </button>
  <button type="submit" class="zameen-btn zameen-btn-primary">
    <svg style="width: 16px; height: 16px; margin-right: 0.5rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
    </svg>
    {{ __('Create Role') }}
  </button>
</div>

{{ Form::close() }}

<script>
  $(function () {
    $("#checkall").on('click', function(){
      $('input:checkbox').not(this).prop('checked', this.checked);
    });
    $(".ischeck").on('click', function(){
      var id = $(this).data('id');
      if(!id) return;
      $('.isscheck_' + id).prop('checked', this.checked);
    });
  });
</script>
