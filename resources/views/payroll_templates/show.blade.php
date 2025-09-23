@extends('layouts.admin')
@php
use \App\Models\Utility;
$settings = Utility::settings();
$lang = isset($currEmailTempLang->lang) ? $currEmailTempLang->lang : 'en';
if ($lang == null) {
$lang = 'en';
}
$LangName = \App\Models\Language::where('code', $lang)->first();

$chatGPT = \App\Models\Utility::settings('enable_chatgpt');
$enable_chatgpt = !empty($chatGPT);

@endphp
@section('page-title')
{{ __('Manage Payroll Email Templates') }}
@endsection

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Home') }}</a></li>
<li class="breadcrumb-item"><a href="{{ route('payroll_template.index') }}">{{ __('Payroll Email Template') }}</a></li>
<li class="breadcrumb-item active" aria-current="page">{{ __('Show') }}</li>
@endsection

@push('css-page')
<link rel="stylesheet" href="{{asset('css/summernote/summernote-bs4.css')}}">
@endpush

@push('script-page')
<script src="{{asset('css/summernote/summernote-bs4.js')}}"></script>
@endpush

@section('content')
<div class="row">
        <div class="col-md-4 col-12">
                <div class="card mb-0 h-100">
                        <div class="card-header card-body">
                                <h5></h5>
                                {{Form::model($emailTemplate, array('route' => array('payroll_template.update', $emailTemplate->id), 'method' => 'PUT')) }}
                                <div class="row">
                                        <div class="form-group col-md-12">
                                                {{Form::label('name',__('Name'),['class'=>'col-form-label text-dark'])}}
                                                {{Form::text('name',null,array('class'=>'form-control font-style','disabled'=>'disabled'))}}
                                        </div>
                                        <div class="form-group col-md-12">
                                                {{Form::label('from',__('From'),['class'=>'col-form-label text-dark'])}}
                                                {{Form::text('from',null,array('class'=>'form-control font-style','required'=>'required'))}}
                                        </div>
                                        {{Form::hidden('lang',$currEmailTempLang->lang,array('class'=>''))}}
                                        <div class="col-12 text-end">
                                                <input type="submit" value="{{__('Save')}}" class="btn btn-print-invoice  btn-primary m-r-10">
                                        </div>
                                </div>
                                {{ Form::close() }}
                        </div>
                </div>
        </div>
        <div class="col-md-8 col-12">
                <div class="card mb-0 h-100">
                        <div class="card-header card-body">
                                <h5></h5>
                                <div class="row text-xs">

                                        <h6 class="font-weight-bold mb-4">{{__('Variables')}}</h6>
                                        @if($emailTemplate->slug == 'payslip_generated')
                                        <div class="row">
                                                <p class="col-6">{{ __('App Name') }} : <span
                                                                class="pull-end text-primary">{app_name}</span></p>
                                                <p class="col-6">{{ __('Company Name') }} : <span
                                                                class="pull-right text-primary">{company_name}</span></p>
                                                <p class="col-6">{{ __('App Url') }} : <span
                                                                class="pull-right text-primary">{app_url}</span></p>
                                                <p class="col-6">{{ __('Employee Name') }} : <span
                                                                class="pull-right text-primary">{employee_name}</span></p>
                                                <p class="col-6">{{ __('Employee ID') }} : <span
                                                                class="pull-right text-primary">{employee_id}</span></p>
                                                <p class="col-6">{{ __('Month') }} : <span
                                                                class="pull-right text-primary">{month}</span></p>
                                                <p class="col-6">{{ __('Year') }} : <span
                                                                class="pull-right text-primary">{year}</span></p>
                                                <p class="col-6">{{ __('Pay Period') }} : <span
                                                                class="pull-right text-primary">{pay_period}</span></p>
                                                <p class="col-6">{{ __('Basic Salary') }} : <span
                                                                class="pull-right text-primary">{basic_salary}</span></p>
                                                <p class="col-6">{{ __('Total Allowances') }} : <span
                                                                class="pull-right text-primary">{total_allowances}</span></p>
                                                <p class="col-6">{{ __('Total Deductions') }} : <span
                                                                class="pull-right text-primary">{total_deductions}</span></p>
                                                <p class="col-6">{{ __('Net Salary') }} : <span
                                                                class="pull-right text-primary">{net_salary}</span></p>
                                        </div>
                                        @elseif($emailTemplate->slug == 'payslip_sent')
                                        <div class="row">
                                                <p class="col-6">{{ __('App Name') }} : <span
                                                                class="pull-end text-primary">{app_name}</span></p>
                                                <p class="col-6">{{ __('Company Name') }} : <span
                                                                class="pull-right text-primary">{company_name}</span></p>
                                                <p class="col-6">{{ __('Employee Name') }} : <span
                                                                class="pull-right text-primary">{employee_name}</span></p>
                                                <p class="col-6">{{ __('Month') }} : <span
                                                                class="pull-right text-primary">{month}</span></p>
                                                <p class="col-6">{{ __('Year') }} : <span
                                                                class="pull-right text-primary">{year}</span></p>
                                                <p class="col-6">{{ __('Gross Salary') }} : <span
                                                                class="pull-right text-primary">{gross_salary}</span></p>
                                                <p class="col-6">{{ __('Net Salary') }} : <span
                                                                class="pull-right text-primary">{net_salary}</span></p>
                                                <p class="col-6">{{ __('Payment Date') }} : <span
                                                                class="pull-right text-primary">{payment_date}</span></p>
                                                <p class="col-6">{{ __('Payment Method') }} : <span
                                                                class="pull-right text-primary">{payment_method}</span></p>
                                        </div>
                                        @elseif($emailTemplate->slug == 'salary_payment')
                                        <div class="row">
                                                <p class="col-6">{{ __('App Name') }} : <span
                                                                class="pull-end text-primary">{app_name}</span></p>
                                                <p class="col-6">{{ __('Company Name') }} : <span
                                                                class="pull-right text-primary">{company_name}</span></p>
                                                <p class="col-6">{{ __('Employee Name') }} : <span
                                                                class="pull-right text-primary">{employee_name}</span></p>
                                                <p class="col-6">{{ __('Month') }} : <span
                                                                class="pull-right text-primary">{month}</span></p>
                                                <p class="col-6">{{ __('Year') }} : <span
                                                                class="pull-right text-primary">{year}</span></p>
                                                <p class="col-6">{{ __('Net Salary') }} : <span
                                                                class="pull-right text-primary">{net_salary}</span></p>
                                                <p class="col-6">{{ __('Payment Date') }} : <span
                                                                class="pull-right text-primary">{payment_date}</span></p>
                                                <p class="col-6">{{ __('Transaction ID') }} : <span
                                                                class="pull-right text-primary">{transaction_id}</span></p>
                                                <p class="col-6">{{ __('Bank Account') }} : <span
                                                                class="pull-right text-primary">{bank_account}</span></p>
                                        </div>
                                        @elseif($emailTemplate->slug == 'payroll_reminder')
                                        <div class="row">
                                                <p class="col-6">{{ __('App Name') }} : <span
                                                                class="pull-end text-primary">{app_name}</span></p>
                                                <p class="col-6">{{ __('Company Name') }} : <span
                                                                class="pull-right text-primary">{company_name}</span></p>
                                                <p class="col-6">{{ __('Employee Name') }} : <span
                                                                class="pull-right text-primary">{employee_name}</span></p>
                                                <p class="col-6">{{ __('Timesheet Deadline') }} : <span
                                                                class="pull-right text-primary">{timesheet_deadline}</span></p>
                                                <p class="col-6">{{ __('Processing Date') }} : <span
                                                                class="pull-right text-primary">{processing_date}</span></p>
                                                <p class="col-6">{{ __('Payment Date') }} : <span
                                                                class="pull-right text-primary">{payment_date}</span></p>
                                        </div>
                                        @elseif($emailTemplate->slug == 'bonus_payment')
                                        <div class="row">
                                                <p class="col-6">{{ __('App Name') }} : <span
                                                                class="pull-end text-primary">{app_name}</span></p>
                                                <p class="col-6">{{ __('Company Name') }} : <span
                                                                class="pull-right text-primary">{company_name}</span></p>
                                                <p class="col-6">{{ __('Employee Name') }} : <span
                                                                class="pull-right text-primary">{employee_name}</span></p>
                                                <p class="col-6">{{ __('Bonus Type') }} : <span
                                                                class="pull-right text-primary">{bonus_type}</span></p>
                                                <p class="col-6">{{ __('Bonus Amount') }} : <span
                                                                class="pull-right text-primary">{bonus_amount}</span></p>
                                                <p class="col-6">{{ __('Bonus Reason') }} : <span
                                                                class="pull-right text-primary">{bonus_reason}</span></p>
                                                <p class="col-6">{{ __('Payment Date') }} : <span
                                                                class="pull-right text-primary">{payment_date}</span></p>
                                        </div>
                                        @elseif($emailTemplate->slug == 'salary_adjustment')
                                        <div class="row">
                                                <p class="col-6">{{ __('App Name') }} : <span
                                                                class="pull-end text-primary">{app_name}</span></p>
                                                <p class="col-6">{{ __('Company Name') }} : <span
                                                                class="pull-right text-primary">{company_name}</span></p>
                                                <p class="col-6">{{ __('Employee Name') }} : <span
                                                                class="pull-right text-primary">{employee_name}</span></p>
                                                <p class="col-6">{{ __('Previous Salary') }} : <span
                                                                class="pull-right text-primary">{previous_salary}</span></p>
                                                <p class="col-6">{{ __('New Salary') }} : <span
                                                                class="pull-right text-primary">{new_salary}</span></p>
                                                <p class="col-6">{{ __('Effective Date') }} : <span
                                                                class="pull-right text-primary">{effective_date}</span></p>
                                                <p class="col-6">{{ __('Adjustment Reason') }} : <span
                                                                class="pull-right text-primary">{adjustment_reason}</span></p>
                                        </div>
                                        @endif
                                </div>
                        </div>
                </div>
        </div>
        <div class="mt-4 col-12">
                <h5></h5>
                <div class="row">
                        <div class="col-sm-3 col-md-3 col-lg-3 col-xl-3 ">
                                <div class="card sticky-top language-sidebar mb-0">
                                        <div class="list-group list-group-flush" id="useradd-sidenav">
                                                @foreach($languages as $key => $lang)
                                                <a class="list-group-item list-group-item-action border-0 {{($currEmailTempLang->lang == $key)?'active':''}}" href="{{route('manage.payroll.language',[$emailTemplate->id,$key])}}">
                                                        {{Str::ucfirst($lang)}}
                                                </a>
                                                @endforeach
                                        </div>
                                </div>
                        </div>

                        <div class="col-lg-9 col-md-9 col-sm-9  ">
                        <div class="card h-100 p-3">
                                {{Form::model($currEmailTempLang, array('route' => array('store.payroll.language',$currEmailTempLang->parent_id), 'method' => 'PUT')) }}
                                <div class="row">
                                        <div class="form-group col-12">
                                                {{Form::label('subject',__('Subject'),['class'=>'col-form-label text-dark'])}}
                                                {{Form::text('subject',null,array('class'=>'form-control font-style','required'=>'required'))}}
                                        </div>
                                        <div class="form-group col-12">
                                                {{Form::label('content',__('Email Message'),['class'=>'col-form-label text-dark'])}}
                                                {{Form::textarea('content',$currEmailTempLang->content,array('class'=>'summernote-simple','id'=>'content','required'=>'required'))}}
                                        </div>

                                        <div class="col-md-12 text-end mb-3">
                                                {{Form::hidden('lang',null)}}
                                                <input type="submit" value="{{__('Save')}}" class="btn btn-print-invoice  btn-primary m-r-10">
                                        </div>
                                </div>
                                {{ Form::close() }}
                        </div>
                        </div>
                </div>
        </div>
</div>

@endsection
@push('scripts')
<script src="{{ asset('assets/js/plugins/summernote-0.8.18-dist/summernote-lite.min.js') }}"></script>
@endpush