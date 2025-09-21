@extends('layouts.admin')
@section('page-title')
    {{ __('Manage Journal Entry') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('Journal Entry') }}</li>
@endsection

@section('action-btn')
    <div class="float-end">
        @can('create journal entry')
            <a href="{{ route('journal-entry.create') }}" data-title="{{ __('Create New Journal') }}" data-bs-toggle="tooltip"
                title="{{ __('Create') }}" class="flex items-center gap-2 bg-[#007C38] text-white px-4 py-2 rounded-[6px] text-[14px] font-[500] hover:bg-[#005f2a] transition-all duration-200 shadow-sm min-w-fit">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                {{ __('Create New') }}
            </a>
        @endcan
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-xl-12">
            <div class="card border-0 rounded-2xl shadow-md overflow-hidden my-3">
                <div class="h-1 w-full" style="background:#007C38;"></div>
          
            <div class="card-body table-border-style">
                <div class="table-responsive table-new-design bg-white p-4">
                    <table class="table datatable">
                        <thead>
                            <tr>
                                <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]">
                                    {{ __('Journal ID') }}</th>
                                <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]">
                                    {{ __('Date') }}</th>
                                <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]">
                                    {{ __('Amount') }}</th>
                                <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]">
                                    {{ __('Description') }}</th>
                                <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]"
                                    width="10%"> {{ __('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($journalEntries as $journalEntry)
                                <tr>
                                    <td class="Id px-4 py-3 border border-[#E5E5E5] text-gray-700">
                                        <a href="{{ route('journal-entry.show', $journalEntry->id) }}"
                                            class="border border-[#137051] leading-[24px] text-[#137051] rounded-[4px] text-[12px] font-[500] px-5">{{ AUth::user()->journalNumberFormat($journalEntry->journal_id) }}</a>
                                    </td>
                                    <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">
                                        {{ Auth::user()->dateFormat($journalEntry->date) }}</td>
                                    <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">
                                        {{ \Auth::user()->priceFormat($journalEntry->totalCredit()) }}
                                    </td>
                                    <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">
                                        {{ !empty($journalEntry->description) ? $journalEntry->description : '-' }}</td>
                                    <td class="px-4 relative text-center py-3 border border-[#E5E5E5] text-gray-700">

                                        <button
                                            class="absolute top-3 text-gray-400 hover:text-gray-600 cursor-pointer"
                                            type="button" data-bs-toggle="dropdown" aria-haspopup="true"
                                            aria-expanded="false">
                                            <i class="ti ti-dots-vertical"></i>
                                        </button>
                                        <div
                                            class="dropdown-menu dropdown-menu-end mt-0 w-[190px] bg-white border rounded-md shadow-lg text-sm p-0">
                                            @can('edit journal entry')
                                               
                                                    <a data-title="{{ __('Edit Journal') }}"
                                                        href="{{ route('journal-entry.edit', [$journalEntry->id]) }}"
                                                        class="dropdown-item flex text-[#323232] gap-2 w-full px-4 py-2 text-left hover:bg-[#007C3812]"
                                                        data-bs-toggle="tooltip" title="{{ __('Edit') }}"
                                                        data-original-title="{{ __('Edit') }}">
                                                          <img src="{{ asset('web-assets/dashboard/icons/action_icons/edit.svg') }}"
                                                            alt="edit" />
                                                        <span>{{ __('Edit') }}</span>
                                                    </a>
                                            
                                            @endcan
                                            @can('delete journal entry')
                                                
                                                    {!! Form::open([
                                                        'method' => 'DELETE',
                                                        'route' => ['journal-entry.destroy', $journalEntry->id],
                                                        'id' => 'delete-form-' . $journalEntry->id,
                                                    ]) !!}

                                                    <a href="#"
                                                        class="dropdown-item flex text-[#323232] gap-2 w-full px-4 py-2 text-left hover:bg-[#007C3812]"
                                                        data-bs-toggle="tooltip" title="{{ __('Delete') }}"
                                                        data-original-title="{{ __('Delete') }}"
                                                        data-confirm="{{ __('Are You Sure?') . '|' . __('This action can not be undone. Do you want to continue?') }}"
                                                        data-confirm-yes="document.getElementById('delete-form-{{ $journalEntry->id }}').submit();">
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
    </div>
@endsection
