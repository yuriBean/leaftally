@extends('layouts.admin')
@section('page-title')
{{__('Bank Balance Transfer')}}
@endsection
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
<li class="breadcrumb-item">{{__('Bank Balance Transfer')}}</li>
@endsection
@section('action-btn')
<div class="flex items-center gap-2 mt-2 sm:mt-0">
   @can('create transfer')
   <a href="#" data-url="{{ route('transfer.create') }}" data-ajax-popup="true" data-title="{{__('Create Transfer')}}" data-bs-toggle="tooltip" title="{{__('Create')}}" class="flex items-center gap-2 bg-[#007C38] text-white px-4 py-2 rounded-[6px] text-[14px] font-[500] hover:bg-[#005f2a] transition-all duration-200 shadow-sm min-w-fit">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
      </svg>
      {{__('Create Transfer')}}
   </a>
   @endcan
</div>
@endsection
@section('content')
<div class="row">
   <div class="col-sm-12">
      <div class=" multi-collapse mt-2 " id="multiCollapseExample1">
         <div class="card border-0 rounded-2xl shadow-md overflow-hidden my-3">
            <div class="h-1 w-full" style="background:#007C38;"></div>
            <div class="card-body bg-white">
               {{ Form::open(array('route' => array('transfer.index'),'method' => 'GET','id'=>'transfer_form')) }}
               <div class="form-space-fix row d-flex align-items-center">
                  <div class="col-md-10 col-12">
                     <div class="row">
                        <div class="col-md-4 col-sm-12 col-12">
                           <div class="btn-box">
                              {{ Form::label('date', __('Date'), ['class' => 'block text-sm font-medium text-gray-700 mb-2']) }}
                              {{ Form::text('date', isset($_GET['date'])?$_GET['date']:date('Y-m-d'), array('class' => 'form-control form-control block w-full pl-3 pr-3 py-2 border border-[#E5E7EB] rounded-[6px] bg-white text-[14px] placeholder-[#9CA3AF] focus:outline-none focus:ring-1 focus:ring-[#007C38] focus:border-[#007C38] transition-all duration-200 month-btn','id'=>'pc-daterangepicker-1', 'placeholder' => 'YYYY-MM-DD')) }}
                           </div>
                        </div>
                        <div class="col-md-4 col-sm-12 col-12">
                           <div class="btn-box">
                              {{ Form::label('f_account', __('From Account'), ['class' => 'form-label block text-sm font-medium text-gray-700 mb-2']) }}
                              {{ Form::select('f_account',$account,isset($_GET['f_account'])?$_GET['f_account']:'', array('class' => 'form-control select appearance-none bg-white border border-[#E5E7EB] rounded-[6px] px-3 py-2 pr-8 text-[14px] text-[#374151] focus:outline-none focus:ring-1 focus:ring-[#007C38] focus:border-[#007C38] transition-all duration-200 w-full')) }}
                           </div>
                        </div>
                        <div class="col-md-4 col-sm-12 col-12">
                           <div class="btn-box">
                              {{ Form::label('t_account', __('To Account'), ['class' => 'form-label block text-sm font-medium text-gray-700 mb-2']) }}
                              {{ Form::select('t_account', $account,isset($_GET['t_account'])?$_GET['t_account']:'', array('class' => 'form-control select appearance-none bg-white border border-[#E5E7EB] rounded-[6px] px-3 py-2 pr-8 text-[14px] text-[#374151] focus:outline-none focus:ring-1 focus:ring-[#007C38] focus:border-[#007C38] transition-all duration-200 w-full')) }}
                           </div>
                        </div>
                     </div>
                  </div>
                  <div class="col-md-2 col-12">
                     <div class="col-auto d-flex justify-content-end mt-4">
                        <a href="#" class="btn btn-sm btn-primary me-2" onclick="document.getElementById('transfer_form').submit(); return false;" data-bs-toggle="tooltip"  title="Apply" data-original-title="{{__('Apply')}}">
                        <span class="btn-inner--icon"><i class="ti ti-search"></i></span>
                        </a>
                        <a href="{{ route('transfer.index') }}" class="btn btn-sm btn-danger" data-bs-toggle="tooltip"
                           title="{{ __('Reset') }}">
                        <span class="btn-inner--icon"><i class="ti ti-refresh text-white-off "></i></span>
                        </a>
                     </div>
                  </div>
               </div>
               {{ Form::close() }}
            </div>
         </div>
      </div>
   </div>
</div>
<div class="row table-new-design">
   <div class="col-md-12">
      <div class="card border-0 rounded-2xl shadow-md overflow-hidden my-3">
      <div class="h-1 w-full" style="background:#007C38;"></div>
      <div class="card-body bg-white m-4">
         <h5></h5>
         <div class="table-responsive">
            <table class="table datatable min-w-full text-sm text-left">
               <thead class="bg-[#F6F6F6] text-[#323232] font-600 text-[12px] leading-[24px]">
                  <tr>
                     <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600]"> {{__('Date')}}</th>
                     <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600]"> {{__('From Account')}}</th>
                     <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600]"> {{__('To Account')}}</th>
                     <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600]"> {{__('Amount')}}</th>
                     <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600]"> {{__('Reference')}}</th>
                     <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600]"> {{__('Description')}}</th>
                     @if(Gate::check('edit transfer') || Gate::check('delete transfer'))
                     <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600]" width="10%"> {{__('Action')}}</th>
                     @endif
                  </tr>
               </thead>
               <tbody>
                  @foreach ($transfers as $transfer)
                  <tr class="font-style">
                     <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">{{ \Auth::user()->dateFormat( $transfer->date) }}</td>
                     <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">{{ !empty($transfer->fromBankAccount())? $transfer->fromBankAccount()->bank_name.' '.$transfer->fromBankAccount()->holder_name:''}}</td>
                     <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">{{!empty( $transfer->toBankAccount())? $transfer->toBankAccount()->bank_name.' '. $transfer->toBankAccount()->holder_name:''}}</td>
                     <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">{{  \Auth::user()->priceFormat( $transfer->amount)}}</td>
                     <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">{{  $transfer->reference}}</td>
                     <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">{{  $transfer->description}}</td>
                     @if(Gate::check('edit transfer') || Gate::check('delete transfer'))
                     <td class="Action px-4 py-3 border border-[#E5E5E5] text-gray-700 relative">
                        <button
                           class="absolute top-3 right-3 text-gray-400 hover:text-gray-600 cursor-pointer"
                           type="button" data-bs-toggle="dropdown" aria-haspopup="true"
                           aria-expanded="false">
                        <i class="ti ti-dots-vertical"></i>
                        </button>
                        <div
                           class="dropdown-menu dropdown-menu-end mt-0 w-[190px] bg-white border rounded-md shadow-lg text-sm p-0">
                           @can('edit transfer')
                           <a href="#" class="dropdown-item flex text-[#323232] gap-2 w-full px-4 py-2 text-left hover:bg-[#007C3812]" data-url="{{ route('transfer.edit',$transfer->id) }}" data-ajax-popup="true" title="{{__('Edit')}}" data-title="{{__('Edit Transfer')}}" data-bs-toggle="tooltip" data-original-title="{{__('Edit')}}">
                           <img src="{{ asset('web-assets/dashboard/icons/action_icons/edit.svg') }}"
                              alt="edit" />
                           <span>{{ __('Edit') }}</span>
                           </a>
                           @endcan
                           @can('delete transfer')
                           {!! Form::open(['method' => 'DELETE', 'route' => ['transfer.destroy', $transfer->id],'id'=>'delete-form-'.$transfer->id]) !!}
                           <a href="#" class="dropdown-item flex text-[#323232] gap-2 w-full px-4 py-2 text-left hover:bg-[#007C3812]" data-bs-toggle="tooltip" data-original-title="{{__('Delete')}}" title="{{__('Delete')}}" data-confirm="{{__('Are You Sure?').'|'.__('This action can not be undone. Do you want to continue?')}}" data-confirm-yes="document.getElementById('delete-form-{{$transfer->id}}').submit();">
                           <img src="{{ asset('web-assets/dashboard/icons/action_icons/delete.svg') }}"
                              alt="delete" />
                           <span>{{ __('Delete') }}</span>
                           </a>
                           {!! Form::close() !!}
                           @endcan
                        </div>
                     </td>
                     @endif
                  </tr>
                  @endforeach
               </tbody>
            </table>
         </div></div>
      </div>
   </div>
</div>
@endsection