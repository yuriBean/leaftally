@extends('layouts.admin')
@push('script-page')
@endpush
@section('page-title')
{{ __('Manage Customer-Detail') }}
@endsection
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
<li class="breadcrumb-item"><a href="{{ route('customer.index') }}">{{ __('Customer') }}</a></li>
<li class="breadcrumb-item">{{ $customer['name'] }}</li>
@endsection
@section('action-btn')
<div class="d-flex">
   @can('create invoice')
   <a href="{{ route('invoice.create', $customer->id) }}" class="btn btn-sm btn-primary me-2"
      title="{{ __('Create Invoice') }}" data-bs-toggle="tooltip">
   {{ __('Create Invoice') }}
   </a>
   @endcan
<!--    @can('create proposal')
   <a href="{{ route('proposal.create', $customer->id) }}" class="btn btn-sm btn-primary me-2"
      title="{{ __('Create Proposal') }}" data-bs-toggle="tooltip">
   {{ __('Create Proposal') }}
   </a>
   @endcan -->
   <a href="{{ route('customer.statement', $customer['id']) }}" class="btn btn-sm btn-primary me-2"
      title="{{ __('Statement') }}" data-bs-toggle="tooltip">
   {{ __('Statement') }}
   </a>
   @can('edit customer')
   <a href="#" data-size="xl" data-url="{{ route('customer.edit', $customer['id']) }}" data-ajax-popup="true"
      title="{{ __('Edit Customer') }}" data-bs-toggle="tooltip" data-original-title="{{ __('Edit') }}"
      class="btn btn-sm btn-info me-2">
   <i class="ti ti-pencil"></i>
   </a>
   @endcan
   @can('delete customer')
   {!! Form::open([
   'method' => 'DELETE',
   'class' => 'delete-form-btn',
   'route' => ['customer.destroy', $customer['id']],
   ]) !!}
   <a href="#" data-bs-toggle="tooltip" title="{{ __('Delete Customer') }}"
      data-confirm="{{ __('Are You Sure?') . '|' . __('This action can not be undone. Do you want to continue?') }}"
      data-confirm-yes="document.getElementById('delete-form-{{ $customer['id'] }}').submit();"
      class="btn btn-sm btn-danger bs-pass-para">
   <i class="ti ti-trash text-white"></i>
   </a>
   {!! Form::close() !!}
   @endcan
</div>
@endsection
@section('content')
<div class="customer-text-sz">
<div class="mt-[46px] grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
    
<!--Customer Info-->
   <div class="card border text-[14px] leading-[24px] border-[#E5E5E5] rounded-[8px] px-[18px] py-[11px] text-[#323232]">
      <div class="">
         <div class="card-body py-2 p-0">
            <h5 class="h4 font-weight-400 mb-3 card-title">{{ __('Customer Info') }}</h5>
            <p class="card-text mb-0">{{ $customer['name'] }}</p>
            <p class="card-text mb-0">{{ $customer['email'] }}</p>
            <p class="card-text mb-0">{{ $customer['contact'] }}</p>
            <p class="card-text mb-0">{{ $customer['tax_number'] }}</p>
         </div>
      </div>
   </div>
  
 <!--Billing Info-->  
   <div class="card border text-[14px] leading-[24px] border-[#E5E5E5] rounded-[8px] px-[18px] py-[11px] text-[#323232]">
      <div class="">
         <div class="card-body py-2 p-0">
            <h5 class="card-title h4 font-weight-400 mb-3 card-title">{{ __('Billing Info') }}</h5>
            <p class="card-text mb-0">{{ $customer['billing_name'] }}</p>
            <p class="card-text mb-0">{{ $customer['billing_address'] }}</p>
            <p class="card-text mb-0">
               {{ $customer['billing_city'] . ', ' . $customer['billing_state'] . ', ' . $customer['billing_zip'] }}
            </p>
            <p class="card-text mb-0">{{ $customer['billing_country'] }}</p>
            <p class="card-text mb-0">{{ $customer['billing_phone'] }}</p>
         </div>
      </div>
   </div>

<!--Shipping Info-->
   <div class="card border text-[14px] leading-[24px] border-[#E5E5E5] rounded-[8px] px-[18px] py-[11px] text-[#323232]">
      <div class="">
         <div class="card-body py-2 p-0">
            <h5 class="card-title h4 font-weight-400 mb-3 card-title">{{ __('Shipping Info') }}</h5>
            <p class="card-text mb-0">{{ $customer['shipping_name'] }}</p>
            <p class="card-text mb-0">{{ $customer['shipping_address'] }}</p>
            <p class="card-text mb-0">
               {{ $customer['shipping_city'] . ', ' . $customer['shipping_state'] . ', ' . $customer['shipping_zip'] }}
            </p>
            <p class="card-text mb-0">{{ $customer['shipping_country'] }}</p>
            <p class="card-text mb-0">{{ $customer['shipping_phone'] }}</p>
         </div>
      </div>
   </div>
</div>
<!--Company Info--> 
<div class="row">
   <div class="col-md-12 text-[14px] leading-[24px] px-[16px] text-[#323232]">
      <div class="card px-[18px] py-[11px] border border-[#E5E5E5] rounded-[8px]">
         <div class="card-body py-2 p-0">
            <h5 class="card-title h4 font-weight-400 mb-3 card-title">{{ __('Company Info') }}</h5>
            <div class="row">
               @php
               $totalInvoiceSum = $customer->customerTotalInvoiceSum($customer['id']);
               $totalInvoice = $customer->customerTotalInvoice($customer['id']);
               $averageSale = $totalInvoiceSum != 0 ? $totalInvoiceSum / $totalInvoice : 0;
               @endphp
               <div class="col-md-3 col-sm-6">
                  <div class="customer-detail">
                     <p class="card-text mb-0">{{ __('Customer Id') }}</p>
                     <h6 class="report-text mb-3">
                        {{ AUth::user()->customerNumberFormat($customer['customer_id']) }}
                     </h6>
                     <p class="card-text mb-0">{{ __('Total Sum of Invoices') }}</p>
                     <h6 class="report-text mb-0">{{ \Auth::user()->priceFormat($totalInvoiceSum) }}</h6>
                  </div>
               </div>
               <div class="col-md-3 col-sm-6">
                  <div class="customer-detail">
                     <p class="card-text mb-0">{{ __('Date of Creation') }}</p>
                     <h6 class="report-text mb-3">{{ \Auth::user()->dateFormat($customer['created_at']) }}</h6>
                     <p class="card-text mb-0">{{ __('Quantity of Invoice') }}</p>
                     <h6 class="report-text mb-0">{{ $totalInvoice }}</h6>
                  </div>
               </div>
               <div class="col-md-3 col-sm-6">
                  <div class="customer-detail">
                     <p class="card-text mb-0">{{ __('Balance') }}</p>
                     <h6 class="report-text mb-3">{{ \Auth::user()->priceFormat($customer['balance']) }}</h6>
                     <p class="card-text mb-0">{{ __('Average Sales') }}</p>
                     <h6 class="report-text mb-0">{{ \Auth::user()->priceFormat($averageSale) }}</h6>
                  </div>
               </div>
               <div class="col-md-3 col-sm-6">
                  <div class="customer-detail">
                     <p class="card-text mb-0">{{ __('Overdue') }}</p>
                     <h6 class="report-text mb-3">
                        {{ \Auth::user()->priceFormat($customer->customerOverdue($customer['id'])) }}
                     </h6>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
<!--Proposal-->
<div class="row d-none">
   <div class="col-12">
      <div class="card">
         <div class="card-body">
             <h5 class="h4 font-weight-400 mb-4">{{ __('Proposal') }}</h5>
             <div class="table-border-style p-4">
                 <div class="table-responsive table-new-design">
               <table class="table datatable dataTable-table">
                  <thead class="bg-[#F6F6F6] text-[#323232] font-600 uppercase text-[12px] leading-[24px]">
                     <tr>
                        <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]">
                           {{ __('Proposal') }}
                        </th>
                        <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]">
                           {{ __('Issue Date') }}
                        </th>
                        <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]">
                           {{ __('Amount') }}
                        </th>
                        <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]">
                           {{ __('Status') }}
                        </th>
                        @if (Gate::check('edit proposal') || Gate::check('delete proposal') || Gate::check('show proposal'))
                        <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]"
                           width="10%"> {{ __('Action') }}</th>
                        @endif
                     </tr>
                  </thead>
                  <tbody>
                     @foreach ($customer->customerProposal($customer->id) as $proposal)
                     <tr class="border-b hover:bg-gray-50">
                        <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700 Id">
                           @if (\Auth::guard('customer')->check())
                           <a href="{{ route('customer.proposal.show', \Crypt::encrypt($proposal->id)) }}"
                              class="border border-[#137051] leading-[24px] text-[#137051] rounded-[4px] text-[12px] font-[500] px-5">{{ AUth::user()->proposalNumberFormat($proposal->proposal_id) }}
                           </a>
                           @else
                           <a href="{{ route('proposal.show', \Crypt::encrypt($proposal->id)) }}"
                              class="border border-[#137051] leading-[24px] text-[#137051] rounded-[4px] text-[12px] font-[500] px-5">{{ AUth::user()->proposalNumberFormat($proposal->proposal_id) }}
                           </a>
                           @endif
                        </td>
                        <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">
                           {{ Auth::user()->dateFormat($proposal->issue_date) }}
                        </td>
                        <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">
                           {{ Auth::user()->priceFormat($proposal->getTotal()) }}
                        </td>
                        <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">
                           @if ($proposal->status == 0)
                           <span
                              class="text-[#509A16] bg-[#338F0914] border rounded-full text-[12px] font-[500] leading-[24px] border-[#509A16] px-3">{{ __(\App\Models\Proposal::$statues[$proposal->status]) }}</span>
                           @elseif($proposal->status == 1)
                           <span
                              class="text-[#509A16] bg-[#338F0914] border rounded-full text-[12px] font-[500] leading-[24px] border-[#509A16] px-3">{{ __(\App\Models\Proposal::$statues[$proposal->status]) }}</span>
                           @elseif($proposal->status == 2)
                           <span
                              class="text-[#509A16] bg-[#338F0914] border rounded-full text-[12px] font-[500] leading-[24px] border-[#509A16] px-3">{{ __(\App\Models\Proposal::$statues[$proposal->status]) }}</span>
                           @elseif($proposal->status == 3)
                           <span
                              class="text-[#509A16] bg-[#338F0914] border rounded-full text-[12px] font-[500] leading-[24px] border-[#509A16] px-3">{{ __(\App\Models\Proposal::$statues[$proposal->status]) }}</span>
                           @elseif($proposal->status == 4)
                           <span
                              class="text-[#509A16] bg-[#338F0914] border rounded-full text-[12px] font-[500] leading-[24px] border-[#509A16] px-3">{{ __(\App\Models\Proposal::$statues[$proposal->status]) }}</span>
                           @endif
                        </td>
                        @if (Gate::check('edit proposal') || Gate::check('delete proposal') || Gate::check('show proposal'))
                        <td class="px-4 py-2 border border-[#E5E5E5] text-[12px] font-[600] Action">
                           <button
                              class="w-100 text-gray-400 hover:text-gray-600 cursor-pointer"
                              type="button" data-bs-toggle="dropdown" aria-haspopup="true"
                              aria-expanded="false">
                           <i class="ti ti-dots-vertical"></i>
                           </button>
                           <div
                              class="dropdown-menu dropdown-menu-end mt-0 w-[190px] bg-white border rounded-md shadow-lg text-sm p-0">
                              @if ($proposal->is_convert == 0)
                              @if ($proposal->converted_invoice_id == 0)
                              @can('convert retainer proposal')
                              <li>
                                 {!! Form::open([
                                 'method' => 'get',
                                 'route' => ['proposal.convert', $proposal->id],
                                 'id' => 'proposal-form-' . $proposal->id,
                                 ]) !!}
                                 <a href="#"
                                    class="dropdown-item flex items-center text-[#323232] gap-2 w-full px-4 py-2 text-left hover:bg-[#007C3812] text-sm bs-pass-para"
                                    data-bs-toggle="tooltip"
                                    title="{{ __('Convert into Retainer') }}"
                                    data-original-title="{{ __('Convert to Retainer') }}"
                                    data-confirm="{{ __('You want to confirm convert to invoice. Press Yes to continue or Cancel to go back') }}"
                                    data-confirm-yes="document.getElementById('proposal-form-{{ $proposal->id }}').submit();">
                                 <i class="ti ti-exchange"></i>
                                 <span>{{ __('Convert to Retainer') }}</span>
                                 </a>
                                 {!! Form::close() !!}
                              </li>
                              @endcan
                              @endif
                              @else
                              @if ($proposal->converted_invoice_id == 0)
                              @can('convert retainer proposal')
                              <li>
                                 <a href="{{ route('retainer.show', \Crypt::encrypt($proposal->converted_retainer_id)) }}"
                                    class="dropdown-item flex items-center text-[#323232] gap-2 w-full px-4 py-2 text-left hover:bg-[#007C3812] text-sm"
                                    data-bs-toggle="tooltip"
                                    title="{{ __('Already convert to Retainer') }}"
                                    data-original-title="{{ __('Already convert to Invoice') }}">
                                 <i class="ti ti-file-invoice"></i>
                                 <span>{{ __('Already Converted to Retainer') }}</span>
                                 </a>
                              </li>
                              @endcan
                              @endif
                              @endif
                              @if ($proposal->converted_invoice_id == 0)
                              @if ($proposal->is_convert == 0)
                              @can('convert invoice proposal')
                              <li>
                                 {!! Form::open([
                                 'method' => 'get',
                                 'route' => ['proposal.convertinvoice', $proposal->id],
                                 'id' => 'proposal-form-' . $proposal->id,
                                 ]) !!}
                                 <a href="#"
                                    class="dropdown-item flex items-center text-[#323232] gap-2 w-full px-4 py-2 text-left hover:bg-[#007C3812] text-sm bs-pass-para"
                                    data-bs-toggle="tooltip"
                                    title="{{ __('Convert into Invoice') }}"
                                    data-original-title="{{ __('Convert to Invoice') }}"
                                    data-confirm="{{ __('You want to confirm convert to invoice. Press Yes to continue or Cancel to go back') }}"
                                    data-confirm-yes="document.getElementById('proposal-form-{{ $proposal->id }}').submit();">
                                 <i class="ti ti-exchange"></i>
                                 <span>{{ __('Convert to Invoice') }}</span>
                                 </a>
                                 {!! Form::close() !!}
                              </li>
                              @endcan
                              @endif
                              @else
                              @can('show invoice')
                              <li>
                                 <a href="{{ route('invoice.show', \Crypt::encrypt($proposal->converted_invoice_id)) }}"
                                    class="dropdown-item flex items-center text-[#323232] gap-2 w-full px-4 py-2 text-left hover:bg-[#007C3812] text-sm"
                                    data-bs-toggle="tooltip"
                                    title="{{ __('Already convert to Invoice') }}"
                                    data-original-title="{{ __('Already convert to Invoice') }}">
                                 <i class="ti ti-file-invoice"></i>
                                 <span>{{ __('Already Converted to Invoice') }}</span>
                                 </a>
                              </li>
                              @endcan
                              @endif
                              @can('duplicate proposal')
                              <li>
                                 {!! Form::open([
                                 'method' => 'get',
                                 'route' => ['proposal.duplicate', $proposal->id],
                                 'id' => 'duplicate-form-' . $proposal->id,
                                 ]) !!}
                                 <a href="#"
                                    class="dropdown-item flex items-center text-[#323232] gap-2 w-full px-4 py-2 text-left hover:bg-[#007C3812] text-sm bs-pass-para"
                                    data-bs-toggle="tooltip" title="{{ __('Duplicate Proposal') }}"
                                    data-original-title="{{ __('Duplicate') }}"
                                    data-confirm="{{ __('You want to confirm convert to invoice. Press Yes to continue or Cancel to go back') }}"
                                    data-confirm-yes="document.getElementById('duplicate-form-{{ $proposal->id }}').submit();">
                                 <i class="ti ti-copy"></i>
                                 <span>{{ __('Duplicate') }}</span>
                                 </a>
                                 {!! Form::close() !!}
                              </li>
                              @endcan
                              @can('show proposal')
                              @if (\Auth::guard('customer')->check())
                              <li>
                                 <a href="{{ route('customer.proposal.show', $proposal->id) }}"
                                    class="dropdown-item flex items-center text-[#323232] gap-2 w-full px-4 py-2 text-left hover:bg-[#007C3812] text-sm"
                                    data-bs-toggle="tooltip" title="{{ __('Show') }}"
                                    data-original-title="{{ __('Detail') }}">
                                 <i class="ti ti-eye"></i>
                                 <span>{{ __('Show') }}</span>
                                 </a>
                              </li>
                              @else
                              <li>
                                 <a href="{{ route('proposal.show', \Crypt::encrypt($proposal->id)) }}"
                                    class="dropdown-item flex items-center text-[#323232] gap-2 w-full px-4 py-2 text-left hover:bg-[#007C3812] text-sm"
                                    data-bs-toggle="tooltip" title="{{ __('Show') }}"
                                    data-original-title="{{ __('Detail') }}">
                                 <i class="ti ti-eye"></i>
                                 <span>{{ __('Show') }}</span>
                                 </a>
                              </li>
                              @endif
                              @endcan
                              @can('edit proposal')
                              <li>
                                 <a href="{{ route('proposal.edit', \Crypt::encrypt($proposal->id)) }}"
                                    class="dropdown-item flex items-center text-[#323232] gap-2 w-full px-4 py-2 text-left hover:bg-[#007C3812] text-sm"
                                    data-bs-toggle="tooltip" title="{{ __('Edit') }}"
                                    data-original-title="{{ __('Edit') }}">
                                 <i class="ti ti-pencil"></i>
                                 <span>{{ __('Edit') }}</span>
                                 </a>
                              </li>
                              @endcan
                              @can('delete proposal')
                              <li>
                                 {!! Form::open([
                                 'method' => 'DELETE',
                                 'route' => ['proposal.destroy', $proposal->id],
                                 'id' => 'delete-form-' . $proposal->id,
                                 ]) !!}
                                 <a href="#"
                                    class="dropdown-item flex items-center text-[#323232] gap-2 w-full px-4 py-2 text-left hover:bg-[#007C3812] text-sm bs-pass-para"
                                    data-bs-toggle="tooltip" title="Delete"
                                    data-original-title="{{ __('Delete') }}"
                                    data-confirm="{{ __('Are You Sure?') . '|' . __('This action can not be undone. Do you want to continue?') }}"
                                    data-confirm-yes="document.getElementById('delete-form-{{ $proposal->id }}').submit();">
                                 <i class="ti ti-trash"></i>
                                 <span>{{ __('Delete') }}</span>
                                 </a>
                                 {!! Form::close() !!}
                              </li>
                              @endcan
                           </div>
                        </td>
                        @endif
                     </tr>
                     @endforeach
                  </tbody>
               </table>
            </div>
             </div>
         </div>
      </div>
   </div>
</div>
<!--Invoice-->
<div class="row">
   <div class="col-12">
      <div class="card">
         <div class="card-body">
              <h5 class="h4 font-weight-400 mb-4">{{ __('Invoice') }}</h5>
             <div class="table-border-style p-4">
                 <div class="table-responsive table-new-design">
               <table class="table datatable dataTable-table">
                  <thead class="bg-[#F6F6F6] text-[#323232] font-600 uppercase text-[12px] leading-[24px]">
                     <tr>
                        <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]">
                           {{ __('Invoice') }}
                        </th>
                        <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]">
                           {{ __('Issue Date') }}
                        </th>
                        <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]">
                           {{ __('Due Date') }}
                        </th>
                        <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]">
                           {{ __('Amount Due') }}
                        </th>
                        <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]">
                           {{ __('Status') }}
                        </th>
                        @if (Gate::check('edit invoice') || Gate::check('delete invoice') || Gate::check('show invoice'))
                        <th width="10%"
                           class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]">
                           {{ __('Action') }}
                        </th>
                        @endif
                     </tr>
                  </thead>
                  <tbody>
                     @foreach ($customer->customerInvoice($customer->id) as $invoice)
                     <tr class="border-b hover:bg-gray-50">
                        <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700 Id">
                           @if (\Auth::guard('customer')->check())
                           <a href="{{ route('customer.invoice.show', \Crypt::encrypt($invoice->id)) }}"
                              class="border border-[#137051] leading-[24px] text-[#137051] rounded-[4px] text-[12px] font-[500] px-5">{{ AUth::user()->invoiceNumberFormat($invoice->invoice_id) }}
                           </a>
                           @else
                           <a href="{{ route('invoice.show', \Crypt::encrypt($invoice->id)) }}"
                              class="border border-[#137051] leading-[24px] text-[#137051] rounded-[4px] text-[12px] font-[500] px-5">{{ AUth::user()->invoiceNumberFormat($invoice->invoice_id) }}
                           </a>
                           @endif
                        </td>
                        <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">
                           {{ \Auth::user()->dateFormat($invoice->issue_date) }}
                        </td>
                        <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">
                           @if ($invoice->due_date < date('Y-m-d'))
                           <p class="text-danger">
                              {{ \Auth::user()->dateFormat($invoice->due_date) }}
                           </p>
                           @else
                           {{ \Auth::user()->dateFormat($invoice->due_date) }}
                           @endif
                        </td>
                        <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">
                           {{ \Auth::user()->priceFormat($invoice->getDue()) }}
                        </td>
                        <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">
                           @if ($invoice->status == 0)
                           <span
                              class="text-[#509A16] bg-[#338F0914] border rounded-full text-[12px] font-[500] leading-[24px] border-[#509A16] px-3">{{ __(\App\Models\Invoice::$statues[$invoice->status]) }}</span>
                           @elseif($invoice->status == 1)
                           <span
                              class="text-[#509A16] bg-[#338F0914] border rounded-full text-[12px] font-[500] leading-[24px] border-[#509A16] px-3">{{ __(\App\Models\Invoice::$statues[$invoice->status]) }}</span>
                           @elseif($invoice->status == 2)
                           <span
                              class="text-[#509A16] bg-[#338F0914] border rounded-full text-[12px] font-[500] leading-[24px] border-[#509A16] px-3">{{ __(\App\Models\Invoice::$statues[$invoice->status]) }}</span>
                           @elseif($invoice->status == 3)
                           <span
                              class="text-[#509A16] bg-[#338F0914] border rounded-full text-[12px] font-[500] leading-[24px] border-[#509A16] px-3">{{ __(\App\Models\Invoice::$statues[$invoice->status]) }}</span>
                           @elseif($invoice->status == 4)
                           <span
                              class="text-[#509A16] bg-[#338F0914] border rounded-full text-[12px] font-[500] leading-[24px] border-[#509A16] px-3">{{ __(\App\Models\Invoice::$statues[$invoice->status]) }}</span>
                           @endif
                        </td>
                        @if (Gate::check('edit invoice') || Gate::check('delete invoice') || Gate::check('show invoice'))
                        <td class="Action px-4 py-2 border border-[#E5E5E5] text-[12px] font-[600]">
                           <button
                              class="w-100 text-gray-400 hover:text-gray-600 cursor-pointer"
                              type="button" data-bs-toggle="dropdown" aria-haspopup="true"
                              aria-expanded="false">
                           <i class="ti ti-dots-vertical"></i>
                           </button>
                           <div
                              class="dropdown-menu dropdown-menu-end mt-0 w-[190px] bg-white border rounded-md shadow-lg text-sm p-0">
                              @can('duplicate invoice')
                              <li>
                                 {!! Form::open([
                                 'method' => 'get',
                                 'route' => ['invoice.duplicate', $invoice->id],
                                 'id' => 'invoice-duplicate-form-' . $invoice->id,
                                 ]) !!}
                                 <a href="#"
                                    class="dropdown-item flex items-center text-[#323232] gap-2 w-full px-4 py-2 text-left hover:bg-[#007C3812] text-sm bs-pass-para"
                                    data-bs-toggle="tooltip" title="{{ __('Duplicate Invoice') }}"
                                    data-original-title="{{ __('Duplicate') }}"
                                    data-confirm="{{ __('You want to confirm this action. Press Yes to continue or Cancel to go back') }}"
                                    data-confirm-yes="document.getElementById('invoice-duplicate-form-{{ $invoice->id }}').submit();">
                                 <i class="ti ti-copy"></i>
                                 <span>{{ __('Duplicate') }}</span>
                                 </a>
                                 {!! Form::close() !!}
                              </li>
                              @endcan
                              @can('show invoice')
                              @if (\Auth::guard('customer')->check())
                              <li>
                                 <a href="{{ route('customer.invoice.show', \Crypt::encrypt($invoice->id)) }}"
                                    class="dropdown-item flex items-center text-[#323232] gap-2 w-full px-4 py-2 text-left hover:bg-[#007C3812] text-sm"
                                    data-bs-toggle="tooltip" title="{{ __('Show') }}"
                                    data-original-title="{{ __('Detail') }}">
                                 <i class="ti ti-eye"></i>
                                 <span>{{ __('Show') }}</span>
                                 </a>
                              </li>
                              @else
                              <li>
                                 <a href="{{ route('invoice.show', \Crypt::encrypt($invoice->id)) }}"
                                    class="dropdown-item flex items-center text-[#323232] gap-2 w-full px-4 py-2 text-left hover:bg-[#007C3812] text-sm"
                                    data-bs-toggle="tooltip" title="{{ __('Show') }}"
                                    data-original-title="{{ __('Detail') }}">
                                 <i class="ti ti-eye"></i>
                                 <span>{{ __('Show') }}</span>
                                 </a>
                              </li>
                              @endif
                              @endcan
                              @can('edit invoice')
                              <li>
                                 <a href="{{ route('invoice.edit', \Crypt::encrypt($invoice->id)) }}"
                                    class="dropdown-item flex items-center text-[#323232] gap-2 w-full px-4 py-2 text-left hover:bg-[#007C3812] text-sm"
                                    data-bs-toggle="tooltip" title="{{ __('Edit') }}"
                                    data-original-title="{{ __('Edit') }}">
                                 <i class="ti ti-pencil"></i>
                                 <span>{{ __('Edit') }}</span>
                                 </a>
                              </li>
                              @endcan
                              @can('delete invoice')
                              <li>
                                 {!! Form::open([
                                 'method' => 'DELETE',
                                 'route' => ['invoice.destroy', $invoice->id],
                                 'id' => 'delete-form-' . $invoice->id,
                                 ]) !!}
                                 <a href="#"
                                    class="dropdown-item flex items-center text-[#323232] gap-2 w-full px-4 py-2 text-left hover:bg-[#007C3812] text-sm bs-pass-para"
                                    data-bs-toggle="tooltip" title="{{ __('Delete') }}"
                                    data-original-title="{{ __('Delete') }}"
                                    data-confirm="{{ __('Are You Sure?') . '|' . __('This action can not be undone. Do you want to continue?') }}"
                                    data-confirm-yes="document.getElementById('delete-form-{{ $invoice->id }}').submit();">
                                 <i class="ti ti-trash"></i>
                                 <span>{{ __('Delete') }}</span>
                                 </a>
                                 {!! Form::close() !!}
                              </li>
                              @endcan
                           </div>
                        </td>
                        @endif
                     </tr>
                     @endforeach
                  </tbody>
               </table>
            </div>
             </div>
         </div>
      </div>
   </div>
</div>
</div>
@endsection