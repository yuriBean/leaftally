@extends('layouts.admin')

@section('page-title')
  {{ __('Manage Designation') }}
@endsection

@section('breadcrumb')
  <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
  <li class="breadcrumb-item">{{ __('Designation') }}</li>
@endsection

@section('action-btn')
  @can('create designation')
    <div class="flex items-center gap-2 mt-2 sm:mt-0 float-end">
      <a href="#"
         data-url="{{ route('designation.create') }}"
         data-ajax-popup="true"
         data-title="{{ __('Create New Designation') }}"
         data-bs-toggle="tooltip"
         title="{{ __('Create') }}"
         class="flex items-center gap-2 bg-[#007C38] text-white px-4 py-2 rounded-[6px] text-[14px] font-[500] hover:bg-[#005f2a] transition-all duration-200 shadow-sm">
        <i class="ti ti-plus"></i> {{ __('Create') }}
      </a>
    </div>
  @endcan
@endsection

@section('content')
<div class="row">
   <div class="col-12">
            @include('layouts.payroll_setup')
        </div>
  <div class="col-12">
    <div class="card border-0 rounded-2xl shadow-md overflow-hidden my-3">
      <div class="h-1 w-full" style="background:#007C38;"></div>
        <div class="card-body table-border-style">
        <div class="table-responsive table-new-design bg-white p-4">
          <table class="table datatable border border-[#E5E5E5] rounded-[8px]">
            <thead>
              <tr>
                <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]">{{ __('Branch') }}</th>
                <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]">{{ __('Department') }}</th>
                <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]">{{ __('Designation') }}</th>
                <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]" width="10%">{{ __('Action') }}</th>
              </tr>
            </thead>

            <tbody class="font-style">
              @foreach ($designations as $designation)
                <tr>
                  <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">
                    {{ optional($designation->branch)->name ?? '-' }}
                  </td>
                  <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">
                    {{ optional($designation->department)->name ?? '-' }}
                  </td>
                  <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">
                    {{ $designation->name }}
                  </td>

                  <td class="Action px-4 py-3 border border-[#E5E5E5] text-gray-700">
                    <button class="w-100 text-gray-400 hover:text-gray-600 cursor-pointer" type="button"
                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                      <i class="ti ti-dots-vertical"></i>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end mt-0 w-[190px] bg-white border rounded-md shadow-lg text-sm p-0">
                      @can('edit designation')
                        <li>
                          <a href="#"
                             class="dropdown-item flex items-center text-gray-700 gap-2 w-full px-4 py-2 text-left hover:bg-[#007C3812] text-sm"
                             data-url="{{ route('designation.edit', $designation->id) }}"
                             data-ajax-popup="true"
                             data-title="{{ __('Edit Designation') }}"
                             data-bs-toggle="tooltip">
                            <i class="ti ti-pencil"></i><span>{{ __('Edit') }}</span>
                          </a>
                        </li>
                      @endcan

                      @can('delete designation')
                        <li>
                          {!! Form::open([
                              'method' => 'DELETE',
                              'route'  => ['designation.destroy', $designation->id],
                              'id'     => 'delete-form-' . $designation->id,
                          ]) !!}
                          <a href="#"
                             class="dropdown-item flex items-center text-gray-700 gap-2 w-full px-4 py-2 text-left hover:bg-[#007C3812] text-sm bs-pass-para"
                             data-bs-toggle="tooltip"
                             data-confirm="{{ __('Are You Sure?') . '|' . __('This action can not be undone. Do you want to continue?') }}"
                             data-confirm-yes="document.getElementById('delete-form-{{ $designation->id }}').submit();">
                            <i class="ti ti-trash"></i><span>{{ __('Delete') }}</span>
                          </a>
                          {!! Form::close() !!}
                        </li>
                      @endcan
                    </div>
                  </td>
                </tr>
              @endforeach
            </tbody>

          </table>
        </div> {{-- table-responsive --}}
      </div>
    </div>
  </div>
</div>
@endsection

@push('script-page')
<script>
  $(document).on('click', '.dropdown-menu, [data-bs-toggle="dropdown"]', function(e){
    e.stopPropagation();
  });

  $(document).on('change', '#branch_id', function() {
    var branch_id = $(this).val();
    getDepartment(branch_id);
  });

  function getDepartment(branch_id) {
    $.ajax({
      url: '{{ route('employee.getdepartment') }}',
      method: 'POST',
      data: {
        branch_id: branch_id,
        _token: '{{ csrf_token() }}',
      },
      success: function(data) {
        $('#department_id').empty()
          .append('<option value="" disabled>{{ __('Select Department') }}</option>');
        $.each(data, function(key, value) {
          $('#department_id').append('<option value="'+key+'">'+value+'</option>');
        });
        $('#department_id').val('');
      }
    });
  }
</script>
@endpush
