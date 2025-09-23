@extends('layouts.admin')
@section('page-title')
    {{ __('Manage Budget Planner') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('Budget Planner') }}</li>
@endsection

@section('action-btn')
    @can('create budget planner')
        <div class="float-end">
            <a href="{{ route('budget.create', 0) }}" data-bs-toggle="tooltip" title="{{ __('Create') }}"
                class="flex items-center gap-2 bg-[#007C38] text-white px-4 py-2 rounded-[6px] text-[14px] font-[500] hover:bg-[#005f2a] transition-all duration-200 shadow-sm min-w-fit">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
            {{ __('Create Budget') }}
            </a>
        </div>
    @endcan
@endsection

@section('content')
    <div class="row">
        <div class="col-sm-12">
             
      <div id="bulk-actions-bar" class="card border-0 shadow-sm rounded-[8px] mb-4 overflow-hidden">
         <div class="card-body p-4 bg-[#F8FAFC]">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
               <div class="flex flex-wrap items-center gap-4">
                  <div class="flex items-center gap-2">
                     <svg class="w-5 h-5 text-[#007C38]" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"></path>
                     </svg>
                     <span class="text-[14px] font-[600] text-[#374151]">
                     <span id="selected-count">10</span> Name selected
                     </span>
                  </div>
                  <div class="flex gap-2">
                     <button type="button" id="select-all-btn" class="text-[14px] font-[500] text-[#007C38] hover:text-[#005f2a] transition-colors duration-200">
                     Select All
                     </button>
                     <button type="button" id="deselect-all-btn" class="text-[14px] font-[500] text-[#6B7280] hover:text-[#374151] transition-colors duration-200">
                     Deselect All
                     </button>
                  </div>
               </div>
               <div class="flex flex-wrap items-center gap-2 w-full sm:w-auto">
                  
                  <button type="button" id="bulk-export-btn" class="inline-flex items-center gap-2 px-3 py-2 border border-[#E5E7EB] text-[#374151] bg-white hover:bg-[#F9FAFB] rounded-[6px] text-[14px] font-[500] transition-all duration-200">
                     <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 712-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                     </svg>
                     <span class="hidden sm:inline">Export Selected</span>
                     <span class="sm:hidden">Export</span>
                  </button>
                  
                  <div class="relative">
                     <button type="button" id="bulk-edit-btn" class="inline-flex items-center gap-2 px-3 py-2 border border-[#E5E7EB] text-[#374151] bg-white hover:bg-[#F9FAFB] rounded-[6px] text-[14px] font-[500] transition-all duration-200" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                           <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        <span class="hidden sm:inline">Bulk Edit</span>
                        <span class="sm:hidden">Edit</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                           <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                     </button>
                     <div class="dropdown-menu dropdown-menu-end mt-2 w-48 bg-white border border-[#E5E7EB] rounded-[6px] shadow-lg p-1">
                        <button type="button" class="bulk-action-item w-full text-left px-3 py-2 text-[14px] text-[#374151] hover:bg-[#F3F4F6] rounded-[4px] transition-colors duration-200" data-action="change-department">
                        Change Department
                        </button>
                        <button type="button" class="bulk-action-item w-full text-left px-3 py-2 text-[14px] text-[#374151] hover:bg-[#F3F4F6] rounded-[4px] transition-colors duration-200" data-action="change-type">
                        Change Employee Type
                        </button>
                        <button type="button" class="bulk-action-item w-full text-left px-3 py-2 text-[14px] text-[#374151] hover:bg-[#F3F4F6] rounded-[4px] transition-colors duration-200" data-action="change-branch">
                        Change Branch
                        </button>
                     </div>
                  </div>
                  
                  <button type="button" id="bulk-delete-btn" class="inline-flex items-center gap-2 px-3 py-2 bg-red-600 text-white hover:bg-red-700 rounded-[6px] text-[14px] font-[500] transition-all duration-200">
                     <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                     </svg>
                     <span class="hidden sm:inline">Delete Selected</span>
                     <span class="sm:hidden">Delete</span>
                  </button>
               </div>
            </div>
         </div>
      </div>
      
                <div class="card-body table-border-style">
                    <div class="table-responsive table-new-design bg-white p-4">
                        <table class="table datatable">
                            <thead>
                                <tr>
                                    <th class="input-checkbox border border-[#E5E5E5] px-4 lg:px-6 py-4 text-left text-[12px] font-[700] text-[#374151] uppercase tracking-wider w-12"><input type="checkbox"></th>
                                    <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]"> {{ __('Name') }}</th>
                                    <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]"> {{ __('From') }}</th>
                                    <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]"> {{ __('Budget Period') }}</th>
                                    <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]" width="10%"> {{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>

                                @foreach ($budgets as $budget)
                                    <tr>
                                        <td class="input-checkbox px-4 lg:px-6 py-4 text-left text-[12px] font-[700] text-[#374151] uppercase tracking-wider border-0 w-12"><input type="checkbox"></td>
                                        <td class="font-style px-4 py-3 border border-[#E5E5E5] text-gray-700">{{ $budget->name }}</td>
                                        <td class="font-style px-4 py-3 border border-[#E5E5E5] text-gray-700">{{ $budget->from }}</td>
                                        <td class="font-style px-4 py-3 border border-[#E5E5E5] text-gray-700">{{ __(\App\Models\Budget::$period[$budget->period]) }}</td>
                                        <td class="Action px-4 py-3 border border-[#E5E5E5] text-gray-700 relative">
                                            <button
                                            class="absolute top-3 right-3 text-gray-400 hover:text-gray-600 cursor-pointer"
                                            type="button" data-bs-toggle="dropdown" aria-haspopup="true"
                                            aria-expanded="false">
                                            <i class="ti ti-dots-vertical"></i>
                                        </button>
                                         <div
                                            class="dropdown-menu dropdown-menu-end mt-0 w-[190px] bg-white border rounded-md shadow-lg text-sm p-0">
                                           
                                                @can('view budget planner')
                                                        <a href="{{ route('budget.show', \Crypt::encrypt($budget->id)) }}"
                                                            class="dropdown-item flex text-[#323232] gap-2 w-full px-4 py-2 text-left hover:bg-[#007C3812]"
                                                            data-bs-toggle="tooltip" title="{{ __('View') }}"
                                                            data-original-title="{{ __('Detail') }}">
                                                            <img src="{{ asset('web-assets/dashboard/icons/preview.svg') }}"
                                                            alt="edit" />
                                                        <span>{{ __('Detail') }}</span>
                                                        </a>
                                                    
                                                @endcan
                                                @can('edit budget planner')
                                                        <a href="{{ route('budget.edit', Crypt::encrypt($budget->id)) }}"
                                                            class="dropdown-item flex text-[#323232] gap-2 w-full px-4 py-2 text-left hover:bg-[#007C3812]"
                                                            data-bs-toggle="tooltip" title="{{ __('Edit') }}"
                                                            data-original-title="{{ __('Edit') }}">
                                                             <img src="{{ asset('web-assets/dashboard/icons/action_icons/edit.svg') }}"
                                                            alt="edit" />
                                                        <span>{{ __('Edit') }}</span>
                                                        </a>
                                                    
                                                @endcan

                                                @can('delete budget planner')
                                                        {!! Form::open([
                                                            'method' => 'DELETE',
                                                            'route' => ['budget.destroy', $budget->id],
                                                            'id' => 'delete-form-' . $budget->id,
                                                        ]) !!}

                                                        <a href="#"
                                                           class="dropdown-item flex text-[#323232] gap-2 w-full px-4 py-2 text-left hover:bg-[#007C3812]"
                                                            data-bs-toggle="tooltip" title="{{ __('Delete') }}"
                                                            data-original-title="{{ __('Delete') }}"
                                                            data-confirm="{{ __('Are You Sure?') . '|' . __('This action can not be undone. Do you want to continue?') }}"
                                                            data-confirm-yes="document.getElementById('delete-form-{{ $budget->id }}').submit();">
                                                             <img src="{{ asset('web-assets/dashboard/icons/action_icons/delete.svg') }}"
                                                            alt="delete" />
                                                        <span>{{ __('Delete') }}</span>
                                                        </a>
                                                        {!! Form::close() !!}
                                                    
                                                @endcan
                                            
                                        </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            
        </div>
    </div>
@endsection
