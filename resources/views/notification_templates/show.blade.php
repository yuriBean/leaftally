@php
    $chatgpt = App\Models\Utility::getValByName('enable_chatgpt');
    $languages = \App\Models\Utility::languages();
    $lang = isset($curr_noti_tempLang->lang) ? $curr_noti_tempLang->lang : 'en';
    if ($lang == null) {
        $lang = 'en';
    }
@endphp

@extends('layouts.admin')
@section('page-title')
    {{ __('Manage Notification Templates') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Home') }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ __('Notification Templates') }}</li>
@endsection
@push('css-page')
<link rel="stylesheet" href="{{asset('css/summernote/summernote-bs4.css')}}">
@endpush

@push('script-page')
<script src="{{asset('css/summernote/summernote-bs4.js')}}"></script>
@endpush

@section('content')
    @if ($chatgpt == 'on')
        <div class="text-end mb-2">
            <a href="#" class="btn btn-sm btn-primary" data-size="medium" data-ajax-popup-over="true"
                data-url="{{ route('generate', ['notification-templates']) }}" data-bs-toggle="tooltip"
                data-bs-placement="top" title="{{ __('Generate') }}" data-title="{{ __('Generate Content With AI') }}">
                <i class="fas fa-robot"></i>{{ __(' Generate With AI') }}
            </a>
        </div>
    @endif
    <div class="mt-4">
        <div class="col-md-12 col-12">
            <div class="bg-white p-4 border border-[#E5E5E5] rounded-[8px] mb-4">
                <div class="card-header card-body">
                    <h5></h5>
                    <div class="row text-xs">

                        <h6 class="font-weight-bold mb-4">{{ __('Variables') }}</h6>
                        @php
                            $variables = json_decode($curr_noti_tempLang->variables);
                        @endphp
                        @if (!empty($variables) > 0)
                            @foreach ($variables as $key => $var)
                                <div class="col-6 pb-1 text-[12px] font-[400]">
                                    <p class="mb-1">{{ __($key) }} : <span
                                            class="pull-right text-primary">{{ '{' . $var . '}' }}</span></p>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12">
            <h5></h5>
            <div class="row">
                <div class="col-sm-3 col-md-3 col-lg-3 col-xl-3  ">
                    <div class="card sticky-top language-sidebar mb-0 border border-[#E5E5E5] rounded-[8px]">
                        <div class="list-group list-group-flush" id="useradd-sidenav">
                            @foreach ($languages as $key => $lang)
                                <a class="list-group-item list-group-item-action border-0 {{ $curr_noti_tempLang->lang == $key ? 'active' : '' }}"
                                    href="{{ route('manage.notification.language', [$notification_template->id, $key]) }}">
                                    {{ Str::ucfirst($lang) }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="col-lg-9 col-md-9 col-sm-9 ">
                    <div class="card h-100 p-3 border border-[#E5E5E5] rounded-[8px]">
                        {{ Form::model($curr_noti_tempLang, ['route' => ['notification-templates.update', $curr_noti_tempLang->parent_id], 'method' => 'PUT']) }}
                        <div class="row">
                            <div class="form-group col-12">
                                {{ Form::label('name', __('Name'), ['class' => 'col-form-label text-dark']) }}
                                {{ Form::text('name', $notification_template->name, ['class' => 'form-control font-style', 'disabled' => 'disabled']) }}
                            </div>
                            <div class="form-group col-12">
                                {{ Form::label('content', __('Notification Message'), ['class' => 'col-form-label text-dark']) }}
                                {{ Form::textarea('content', $curr_noti_tempLang->content, ['class' => 'summernote-simple', 'required' => 'required']) }}
                            </div>
                            <div class="col-md-12 text-end mb-3">
                                {{ Form::hidden('lang', null) }}
                                <input type="submit" value="{{ __('Save') }}"
                                    class="btn btn-print-invoice  btn-primary m-r-10">
                            </div>
                        </div>
                        {{ Form::close() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection