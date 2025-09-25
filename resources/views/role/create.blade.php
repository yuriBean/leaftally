{{ Form::open(['url' => 'roles','method' => 'post','class' => 'needs-validation','novalidate']) }}
<div class="modal-body p-6 bg-[#FAFBFC]">
  <div class="bg-white rounded-[8px] border border-[#E5E7EB] p-6 shadow-sm overflow-hidden">
    <div class="row">
      <div class="col-md-12">
        <div class="form-group">
          {{ Form::label('name', __('Name'), ['class' => 'form-label']) }}<x-required></x-required>
          {{ Form::text('name', null, ['class' => 'form-control','placeholder' => __('Enter Role Name'),'required' => 'required']) }}
          @error('name')
            <small class="invalid-name" role="alert">
              <strong class="text-danger">{{ $message }}</strong>
            </small>
          @enderror
        </div>
      </div>
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

    <div class="row">
      <div class="col-md-12">
        <div class="form-group mb-0">
          @if(!empty($permissions))
            <label class="form-label">{{ __('Assign Permission to Roles') }}</label>
            <table class="table table-roles w-full text-[12px] border border-[#E5E5E5] rounded-[4px]" id="dataTable-1">
              <thead>
                <tr class="bg-[#F6F6F6]">
                  <th class="py-2 px-4 border-r border-[#E5E5E5]">
                    <input type="checkbox" class="form-check-input align-middle me-1" name="checkall" id="checkall">
                    {{ __('Module') }}
                  </th>
                  <th class="py-2 px-4">{{ __('Permissions') }}</th>
                </tr>
              </thead>
              <tbody>
              @foreach($visibleModules as $module)
                @php
                  $slug = str_replace(' ', '', $module);
                @endphp
                <tr class="border border-[#E5E5E5]">
                  <td class="border-r border-[#E5E5E5] py-2 px-4 align-middle">
                    <input type="checkbox" class="form-check-input align-middle ischeck" data-id="{{ $slug }}">
                    <label class="ischeck ml-1" data-id="{{ $slug }}">{{ ucfirst($module) }}</label>
                  </td>
                  <td>
                      <div class="row">
                        @if(in_array('manage '.$module, $permNames))
                          @php $key = array_search('manage '.$module, $permissions); @endphp
                          @if($key !== false)
                            <div class="col-md-3 custom-control custom-checkbox">
                              {{ Form::checkbox('permissions[]', $key, false, ['class'=>'form-check-input isscheck isscheck_'.$slug,'id'=>'permission'.$key]) }}
                              {{ Form::label('permission'.$key, 'Manage', ['class' => 'form-check-label']) }}<br>
                            </div>
                          @endif
                        @endif

                        @if(in_array('create '.$module, $permNames))
                          @php $key = array_search('create '.$module, $permissions); @endphp
                          @if($key !== false)
                            <div class="col-md-3 custom-control custom-checkbox">
                              {{ Form::checkbox('permissions[]', $key, false, ['class'=>'form-check-input isscheck isscheck_'.$slug,'id'=>'permission'.$key]) }}
                              {{ Form::label('permission'.$key, 'Create', ['class' => 'form-check-label']) }}<br>
                            </div>
                          @endif
                        @endif

                        @if(in_array('edit '.$module, $permNames))
                          @php $key = array_search('edit '.$module, $permissions); @endphp
                          @if($key !== false)
                            <div class="col-md-3 custom-control custom-checkbox">
                              {{ Form::checkbox('permissions[]', $key, false, ['class'=>'form-check-input isscheck isscheck_'.$slug,'id'=>'permission'.$key]) }}
                              {{ Form::label('permission'.$key, 'Edit', ['class' => 'form-check-label']) }}<br>
                            </div>
                          @endif
                        @endif

                        @if(in_array('delete '.$module, $permNames))
                          @php $key = array_search('delete '.$module, $permissions); @endphp
                          @if($key !== false)
                            <div class="col-md-3 custom-control custom-checkbox">
                              {{ Form::checkbox('permissions[]', $key, false, ['class'=>'form-check-input isscheck isscheck_'.$slug,'id'=>'permission'.$key]) }}
                              {{ Form::label('permission'.$key, 'Delete', ['class' => 'form-check-label']) }}<br>
                            </div>
                          @endif
                        @endif

                        @if(in_array('show '.$module, $permNames))
                          @php $key = array_search('show '.$module, $permissions); @endphp
                          @if($key !== false)
                            <div class="col-md-3 custom-control custom-checkbox">
                              {{ Form::checkbox('permissions[]', $key, false, ['class'=>'form-check-input isscheck isscheck_'.$slug,'id'=>'permission'.$key]) }}
                              {{ Form::label('permission'.$key, 'Show', ['class' => 'form-check-label']) }}<br>
                            </div>
                          @endif
                        @endif

                        @if(in_array('buy '.$module, $permNames))
                          @php $key = array_search('buy '.$module, $permissions); @endphp
                          @if($key !== false)
                            <div class="col-md-3 custom-control custom-checkbox">
                              {{ Form::checkbox('permissions[]', $key, false, ['class'=>'form-check-input isscheck isscheck_'.$slug,'id'=>'permission'.$key]) }}
                              {{ Form::label('permission'.$key, 'Buy', ['class' => 'form-check-label']) }}<br>
                            </div>
                          @endif
                        @endif

                        @if(in_array('send '.$module, $permNames))
                          @php $key = array_search('send '.$module, $permissions); @endphp
                          @if($key !== false)
                            <div class="col-md-3 custom-control custom-checkbox">
                              {{ Form::checkbox('permissions[]', $key, false, ['class'=>'form-check-input isscheck isscheck_'.$slug,'id'=>'permission'.$key]) }}
                              {{ Form::label('permission'.$key, 'Send', ['class' => 'form-check-label']) }}<br>
                            </div>
                          @endif
                        @endif

                        @if(in_array('create payment '.$module, $permNames))
                          @php $key = array_search('create payment '.$module, $permissions); @endphp
                          @if($key !== false)
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
          @endif
        </div>
      </div>
    </div>
  </div>
</div>
<div class="modal-footer" style="background: #f8f9fa; border-top: 1px solid #e0e0e0; padding: 1.5rem 2rem; display: flex; justify-content: flex-end; gap: 1rem; border-radius: 0 0 8px 8px;">
  <input type="button" value="{{ __('Cancel') }}" class="btn btn-light" data-bs-dismiss="modal" style="border-radius: 8px; padding: 0.75rem 1.5rem; border: 1.5px solid #e0e0e0; color: #2d3748; font-weight: 500; background: #fff;">
  <input type="submit" value="{{ __('Create') }}" class="btn btn-success" style="background: #007c38; color: #fff; border-radius: 8px; padding: 0.75rem 1.5rem; font-weight: 500; border: none;">
</div>
{{ Form::close() }}
