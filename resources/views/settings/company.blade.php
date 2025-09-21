@extends('layouts.admin')
@section('page-title')
{{ __('Settings') }}
@endsection
@php
use App\Models\Utility;
$logo = Utility::get_file('uploads/logo/');
$logo_light = Utility::getValByName('company_logo_light');
$logo_dark = Utility::getValByName('company_logo_dark');
$company_favicon = Utility::getValByName('company_favicon');
$EmailTemplates = App\Models\EmailTemplate::all();
$setting = App\Models\Utility::settings();
$lang = Utility::getValByName('company_default_language');
$color = !empty($setting['color']) ? $setting['color'] : 'theme-3';
$flag = !empty($setting['color_flag']) ? $setting['color_flag'] : 'false';

@endphp

<style>
    .dash-footer {
        margin-left: 0 !important;
    }
    /* .card-footer {
        padding: 2 !important;
    } */
</style>

{{-- <link rel="stylesheet" href="{{ asset('assets/css/footer-style.css') }}"> --}}

@push('script-page')
<script type="text/javascript">
    $(".email-template-checkbox").click(function() {

        var chbox = $(this);
        $.ajax({
            url: chbox.attr('data-url'),
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                status: chbox.val()
            },
            type: 'post',
            success: function(response) {
                if (response.is_success) {
                    -
                    // show_toastr('success', '{{ __('Link Copy on Clipboard') }}');
                    show_toastr('success', response.success, 'success');
                    if (chbox.val() == 1) {
                        $('#' + chbox.attr('id')).val(0);
                    } else {
                        $('#' + chbox.attr('id')).val(1);
                    }
                } else {
                    show_toastr('error', response.error, 'error');
                }
            },
            error: function(response) {
                response = response.responseJSON;
                if (response.is_success) {
                    show_toastr('error', response.error, 'error');
                } else {
                    show_toastr('error', response, 'error');
                }
            }
        })
    });
</script>
<script>
    var scrollSpy = new bootstrap.ScrollSpy(document.body, {
        target: '#useradd-sidenav',
        offset: 300
    })

    var themescolors = document.querySelectorAll(".themes-color > a");
    for (var h = 0; h < themescolors.length; h++) {
        var c = themescolors[h];
        c.addEventListener("click", function(event) {
            var targetElement = event.target;
            if (targetElement.tagName == "SPAN") {
                targetElement = targetElement.parentNode;
            }
            var temp = targetElement.getAttribute("data-value");
            removeClassByPrefix(document.querySelector("body"), "theme-");
            document.querySelector("body").classList.add(temp);
        });
    }

    function check_theme(color_val) {
        $('input[value="' + color_val + '"]').prop('checked', true);
        $('a[data-value]').removeClass('active_color');
        $('a[data-value="' + color_val + '"]').addClass('active_color');
        
        // Auto-sync document colors with main theme if they are set to default (white)
        updateDocumentColors(color_val);
    }
    
    function updateDocumentColors(themeValue) {
        // Color mapping from theme to hex values
        var colorMap = {
            'theme-1': '51459d',  // Blue
            'theme-2': 'ff3a6e',  // Pink  
            'theme-3': '007C38',  // Green (primary)
            'theme-4': 'ffa21d',  // Orange
            'theme-5': '3ec9d6'   // Cyan
        };
        
        var newColor = colorMap[themeValue] || '007C38';
        
        // Update invoice color if it's set to default white or not selected
        var invoiceColorChecked = $('input[name="invoice_color"]:checked').val();
        if (!invoiceColorChecked || invoiceColorChecked === 'ffffff') {
            $('input[name="invoice_color"][value="' + newColor + '"]').prop('checked', true);
            // Update invoice preview if it exists
            if ($('#invoice_frame').length) {
                var template = $("select[name='invoice_template']").val();
                $('#invoice_frame').attr('src', '{{ url('/invoices/preview') }}/' + template + '/' + newColor);
            }
        }
        
        // Update proposal color if it's set to default white or not selected
        var proposalColorChecked = $('input[name="proposal_color"]:checked').val();
        if (!proposalColorChecked || proposalColorChecked === 'ffffff') {
            $('input[name="proposal_color"][value="' + newColor + '"]').prop('checked', true);
            // Update proposal preview if it exists
            if ($('#proposal_frame').length) {
                var template = $("select[name='proposal_template']").val();
                $('#proposal_frame').attr('src', '{{ url('/proposal/preview') }}/' + template + '/' + newColor);
            }
        }
        
        // Update bill color if it's set to default white or not selected
        var billColorChecked = $('input[name="bill_color"]:checked').val();
        if (!billColorChecked || billColorChecked === 'ffffff') {
            $('input[name="bill_color"][value="' + newColor + '"]').prop('checked', true);
            // Update bill preview if it exists
            if ($('#bill_frame').length) {
                var template = $("select[name='bill_template']").val();
                $('#bill_frame').attr('src', '{{ url('/bill/preview') }}/' + template + '/' + newColor);
            }
        }
        
        // Update retainer color if it's set to default white or not selected
        var retainerColorChecked = $('input[name="retainer_color"]:checked').val();
        if (!retainerColorChecked || retainerColorChecked === 'ffffff') {
            $('input[name="retainer_color"][value="' + newColor + '"]').prop('checked', true);
            // Update retainer preview if it exists
            if ($('#retainer_frame').length) {
                var template = $("select[name='retainer_template']").val();
                $('#retainer_frame').attr('src', '{{ url('/retainer/preview') }}/' + template + '/' + newColor);
            }
        }
    }

    if ($('#cust-darklayout').length > 0) {
        var custthemedark = document.querySelector("#cust-darklayout");
        custthemedark.addEventListener("click", function() {
            if (custthemedark.checked) {
                document.querySelector("#style").setAttribute("href",
                    "{{ asset('assets/css/style-dark.css') }}");

                $('.dash-sidebar .main-logo a img').attr('src', '{{ $logo . $logo_light }}');

            } else {
                document.querySelector("#style").setAttribute("href",
                    "{{ asset('assets/css/style.css') }}");
                $('.dash-sidebar .main-logo a img').attr('src', '{{ $logo . $logo_dark }}');

            }
        });
    }

    if ($('#cust-theme-bg').length > 0) {
        var custthemebg = document.querySelector("#cust-theme-bg");
        custthemebg.addEventListener("click", function() {
            if (custthemebg.checked) {
                document.querySelector(".dash-sidebar").classList.add("transprent-bg");
                document
                    .querySelector(".dash-header:not(.dash-mob-header)")
                    .classList.add("transprent-bg");
            } else {
                document.querySelector(".dash-sidebar").classList.remove("transprent-bg");
                document
                    .querySelector(".dash-header:not(.dash-mob-header)")
                    .classList.remove("transprent-bg");
            }
        });
    }
</script>

<script>
    $(document).on("change", "select[name='invoice_template'], input[name='invoice_color'], select[name='invoice_font']", function() {
        var template = $("select[name='invoice_template']").val();
        var color = $("input[name='invoice_color']:checked").val();
        var font = $("select[name='invoice_font']").val() || 'Inter';
        $('#invoice_frame').attr('src', '{{ url('/invoices/preview') }}/' + template + '/' + color + '?font=' + encodeURIComponent(font));
    });

    $(document).on("change", "select[name='proposal_template'], input[name='proposal_color'], select[name='proposal_font']", function() {
        var template = $("select[name='proposal_template']").val();
        var color = $("input[name='proposal_color']:checked").val();
        var font = $("select[name='proposal_font']").val() || 'Lato';
        $('#proposal_frame').attr('src', '{{ url('/proposal/preview') }}/' + template + '/' + color + '?font=' + encodeURIComponent(font));
    });

    $(document).on("change", "select[name='bill_template'], input[name='bill_color'], select[name='bill_font']", function() {
        var template = $("select[name='bill_template']").val();
        var color = $("input[name='bill_color']:checked").val();
        var font = $("select[name='bill_font']").val() || 'Inter';
        $('#bill_frame').attr('src', '{{ url('/bill/preview') }}/' + template + '/' + color + '?font=' + encodeURIComponent(font));
    });

    $(document).on("change", "select[name='retainer_template'], input[name='retainer_color'], select[name='retainer_font']", function() {
        var template = $("select[name='retainer_template']").val();
        var color = $("input[name='retainer_color']:checked").val();
        var font = $("select[name='retainer_font']").val() || 'Inter';
        $('#retainer_frame').attr('src', '{{ url('/retainer/preview') }}/' + template + '/' + color + '?font=' + encodeURIComponent(font));
    });

</script>

<script>
    $(".list-group-item").click(function() {
        $('.list-group-item').filter(function() {
            return this.href == id;
        }).parent().removeClass('text-primary');
    });

    function check_theme(color_val) {
        $('#theme_color').prop('checked', false);
        $('input[value="' + color_val + '"]').prop('checked', true);
    }

    $(document).on('change', '[name=storage_setting]', function() {
        if ($(this).val() == 's3') {
            $('.s3-setting').removeClass('d-none');
            $('.wasabi-setting').addClass('d-none');
            $('.local-setting').addClass('d-none');
        } else if ($(this).val() == 'wasabi') {
            $('.s3-setting').addClass('d-none');
            $('.wasabi-setting').removeClass('d-none');
            $('.local-setting').addClass('d-none');
        } else {
            $('.s3-setting').addClass('d-none');
            $('.wasabi-setting').addClass('d-none');
            $('.local-setting').removeClass('d-none');
        }
    });
</script>

<script type="text/javascript">
    $(document).ready(function() {
        var checkBox = document.getElementById('tax_number');
        // Check if the element is selected/checked
        if (checkBox.checked) {
            $('#tax_checkbox_id').removeClass('d-none');
        } else {
            $('#tax_checkbox_id').addClass('d-none');
        }
        $(document).on('change', '#tax_number', function() {

            if ($(this).is(':checked') == true) {
                $('#tax_checkbox_id').removeClass('d-none');
            } else {
                $('#tax_checkbox_id').addClass('d-none');
            }
        });
    });
</script>
<script>
    $(document).on("click", '.send_email', function(e) {

        e.preventDefault();
        var title = $(this).attr('data-title');

        var size = 'md';
        var url = $(this).attr('data-url');
        if (typeof url != 'undefined') {
            $("#commonModal .modal-title").html(title);
            $("#commonModal .modal-dialog").addClass('modal-' + size);
            $("#commonModal").modal('show');

            $.post(url, {
                _token: '{{ csrf_token() }}',
                mail_driver: $("#mail_driver").val(),
                mail_host: $("#mail_host").val(),
                mail_port: $("#mail_port").val(),
                mail_username: $("#mail_username").val(),
                mail_password: $("#mail_password").val(),
                mail_encryption: $("#mail_encryption").val(),
                mail_from_address: $("#mail_from_address").val(),
                mail_from_name: $("#mail_from_name").val(),
            }, function(data) {
                $('#commonModal .body').html(data);
            });
        }
    });


    $(document).on('submit', '#test_email', function(e) {
        e.preventDefault();
        $("#email_sending").show();
        var post = $(this).serialize();
        var url = $(this).attr('action');
        $.ajax({
            type: "post",
            url: url,
            data: post,
            cache: false,
            beforeSend: function() {
                $('#test_email .btn-create').attr('disabled', 'disabled');
            },
            success: function(data) {
                if (data.is_success) {
                    show_toastr('success', data.message, 'success');
                } else {
                    show_toastr('error', data.message, 'error');
                }
                $("#email_sending").hide();
                $('#commonModal').modal('hide');
            },
            complete: function() {
                $('#test_email .btn-create').removeAttr('disabled');
            },
        });
    });
</script>
@endpush

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
<li class="breadcrumb-item">{{ __('Settings') }}</li>
@endsection

@section('content')
<div class="row">
    <!-- [ sample-page ] start -->
    <div class="col-sm-12">
        <div class="row">
            <div class="col-xl-3">
                <div class="card sticky-top" style="top:30px">
                    <div class="list-group list-group-flush" id="useradd-sidenav">
                        <a href="#useradd-1"
                            class="list-group-item list-group-item-action border-0">{{ __('Brand Settings') }}
                            <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                        </a>
                        <a href="#useradd-2"
                            class="list-group-item list-group-item-action border-0">{{ __('System Settings') }}
                            <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                        </a>
                        <a href="#useradd-3"
                            class="list-group-item list-group-item-action border-0">{{ __('Company Settings') }}
                            <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                        </a>
                        <a href="#useradd-12"
                            class="list-group-item list-group-item-action border-0 d-none">{{ __('Email Settings') }}
                            <div class="float-end "><i class="ti ti-chevron-right"></i></div>
                        </a>
                        <a href="#useradd-4"
                            class="list-group-item list-group-item-action border-0 d-none">{{ __('Proposal Print Settings') }}
                            <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                        </a>
                        <a href="#useradd-10"
                            class="list-group-item list-group-item-action border-0 d-none">{{ __('Retainer Print Settings') }}
                            <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                        </a>
                        <a href="#useradd-5"
                            class="list-group-item list-group-item-action border-0">{{ __('Invoice Print Settings') }}
                            <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                        </a>
                        <a href="#useradd-6"
                            class="list-group-item list-group-item-action border-0">{{ __('Bill Print Settings') }}
                            <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                        </a>
                        <a href="#useradd-7"
                            class="list-group-item list-group-item-action border-0 d-none">{{ __('Payment Settings') }}
                            <div class="float-end "><i class="ti ti-chevron-right"></i></div>
                        </a>
                        <a href="#useradd-8"
                            class="list-group-item list-group-item-action border-0 d-none">{{ __('Twilio Settings') }}
                            <div class="float-end "><i class="ti ti-chevron-right"></i></div>
                        </a>
                        <a href="#useradd-9"
                            class="list-group-item list-group-item-action border-0">{{ __('Email Notification Settings') }}
                            <div class="float-end "><i class="ti ti-chevron-right"></i></div>
                        </a>
                        <a href="#useradd-10"
                            class="list-group-item list-group-item-action border-0 d-none">{{ __('Webhook Settings') }}
                            <div class="float-end "><i class="ti ti-chevron-right"></i></div>
                        </a>

                    </div>
                </div>
            </div>


            <div class="col-xl-9">

                <!--Business Setting-->
                <div id="useradd-1" class="card border-0 rounded-2xl shadow-md overflow-hidden my-3">
                        <div class="h-1 w-full" style="background:#007C38;"></div>
                  
                    {{ Form::model($settings, ['route' => 'business.setting', 'class' => 'mb-0', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
                    <div class="card-header">
                        <h5>{{ __('Brand Settings') }}</h5>
                        <small class="text-muted">{{ __('Edit your brand details') }}</small>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 col-sm-6  dashboard-card">
                                <div class="card border-0 rounded-2xl shadow-md overflow-hidden my-3">
                                    <div class="card-header">
                                        <h5>{{ __('Logo') }}</h5>
                                    </div>
                                    <div class="card-body pt-0 d-flex flex-column align-items-center justify-content-center text-center">
                                        <div class="setting-card">
                                            <div class="logo-content mt-4">
                                                @if(isset($logo_dark) && !empty($logo_dark))
                                                    <a href="{{ $logo . $logo_dark . '?' . time() }}" target="_blank">
                                                        <img id="logoPreview" 
                                                             alt="your image" 
                                                             src="{{ $logo . $logo_dark . '?' . time() }}" 
                                                             class="big-logo"
                                                             style="max-width: 200px; height: auto; display:block; margin:0 auto;">
                                                    </a>
                                                @else
                                                    <div class="empty-logo-placeholder bg-light d-flex align-items-center justify-content-center" 
                                                         style="height: 120px; width:200px; margin:0 auto; border: 2px dashed #ddd; border-radius: 8px;">
                                                        <span class="text-muted">{{ __('No logo uploaded') }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                    
                                            <div class="choose-files mt-4">
                                                <label for="company_logo">
                                                    <div class="bg-primary text-white px-3 py-2 rounded cursor-pointer company_logo_update">
                                                        <i class="ti ti-upload px-1"></i>{{ __('Choose file here') }}
                                                    </div>
                                                    <input type="file" 
                                                           name="company_logo_dark" 
                                                           id="company_logo" 
                                                           class="form-control file d-none"
                                                           data-filename="company_logo_update"
                                                           onchange="document.getElementById('logoPreview').src = window.URL.createObjectURL(this.files[0])">
                                                </label>
                                            </div>
                                    
                                            @error('company_logo')
                                            <div class="row mt-2">
                                                <span class="invalid-logo" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                            </div>
                                            @enderror
                                        </div>
                                    </div>
                                    
                                </div>
                            </div>
                            
                            {{-- <div class="col-lg-4 col-sm-6 col-md-6 dashboard-card">
                                <div class="card border-0 rounded-2xl shadow-md overflow-hidden my-3">
                                    <div class="card-header">
                                        <h5>{{ __('Logo dark') }}</h5>
                                    </div>
                                    <div class="card-body pt-0">
                                        <div class=" setting-card">
                                            <div class="logo-content mt-4">
                                                @if(isset($logo_dark) && !empty($logo_dark))
                                                    <a href="{{ $logo . $logo_dark . '?' . time() }}" target="_blank">
                                                        <img id="blah" alt="your image" src="{{ $logo . $logo_dark . '?' . time() }}" width="" class="big-logo">
                                                    </a>
                                                @else
                                                    <div class="empty-logo-placeholder bg-light d-flex align-items-center justify-content-center" style="height: 100px; border: 2px dashed #ddd; border-radius: 8px;">
                                                        <span class="text-muted">{{ __('No logo uploaded') }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="choose-files mt-5">
                                                <label for="company_logo">
                                                    <div class=" bg-primary company_logo_update "> <i
                                                            class="ti ti-upload px-1"></i>{{ __('Choose file here') }}
                                                    </div>
                                                    <input type="file" name="company_logo_dark" id="company_logo"
                                                        class="form-control file" data-filename="company_logo_update"
                                                        onchange="document.getElementById('blah').src = window.URL.createObjectURL(this.files[0])">

                                                  
                                                </label>
                                            </div>
                                            @error('company_logo')
                                            <div class="row">
                                                <span class="invalid-logo" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                            </div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 col-sm-6 col-md-6 dashboard-card">
                                <div class="card border-0 rounded-2xl shadow-md overflow-hidden my-3">
                                    <div class="card-header">
                                        <h5>{{ __('Logo Light') }}</h5>
                                    </div>
                                    <div class="card-body pt-0">
                                        <div class=" setting-card">
                                            <div class="logo-content mt-4">
                                                @if(isset($logo_light) && !empty($logo_light))
                                                    <a href="{{ $logo . $logo_light . '?' . time() }}" target="_blank">
                                                        <img id="blah1" alt="your image" src="{{ $logo . $logo_light . '?' . time() }}" width="150px" class="big-logo img_setting">
                                                    </a>
                                                @else
                                                    <div class="empty-logo-placeholder bg-light d-flex align-items-center justify-content-center" style="height: 100px; border: 2px dashed #ddd; border-radius: 8px;">
                                                        <span class="text-muted">{{ __('No logo uploaded') }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="choose-files mt-5">
                                                <label for="company_logo_light">
                                                    <div class=" bg-primary dark_logo_update "> <i
                                                            class="ti ti-upload px-1"></i>{{ __('Choose file here') }}
                                                    </div>
                                                    <input type="file" name="company_logo_light"
                                                        id="company_logo_light" class="form-control file"
                                                        data-filename="dark_logo_update"
                                                        onchange="document.getElementById('blah1').src = window.URL.createObjectURL(this.files[0])">


                                                </label>
                                            </div>
                                            @error('company_logo_light')
                                            <div class="row">
                                                <span class="invalid-logo" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                            </div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 col-sm-6 col-md-6 dashboard-card">
                                <div class="card border-0 rounded-2xl shadow-md overflow-hidden my-3">
                                    <div class="card-header">
                                        <h5>{{ __('Favicon') }}</h5>
                                    </div>
                                    <div class="card-body pt-0">
                                        <div class=" setting-card">
                                            <div class="logo-content mt-4">
                                                @if(isset($company_favicon) && !empty($company_favicon))
                                                    <a href="{{ $logo . $company_favicon . '?' . time() }}" target="_blank">
                                                        <img id="blah2" alt="your image" src="{{ $logo . $company_favicon . '?' . time() }}" width="60px" height="63px" class="img_setting">
                                                    </a>
                                                @else
                                                    <div class="empty-logo-placeholder bg-light d-flex align-items-center justify-content-center" style="height: 63px; width: 60px; border: 2px dashed #ddd; border-radius: 8px;">
                                                        <span class="text-muted text-center" style="font-size: 10px;">{{ __('No favicon') }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="choose-files mt-5">
                                                <label for="company_favicon">
                                                    <div class="bg-primary company_favicon_update "> <i
                                                            class="ti ti-upload px-1"></i>{{ __('Choose file here') }}
                                                    </div>
                                                    <input type="file" name="company_favicon" id="company_favicon"
                                                        class="form-control file"
                                                        data-filename="company_favicon_update"
                                                        onchange="document.getElementById('blah2').src = window.URL.createObjectURL(this.files[0])">

                                                </label>
                                            </div>
                                            @error('logo')
                                            <div class="row">
                                                <span class="invalid-logo" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                            </div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div> --}}

                            {{-- <div class="row">
                                    <div class="form-group col-md-6">
                                        {{ Form::label('title_text', __('Title Text'), ['class' => 'form-label']) }}
                            {{ Form::text('title_text', null, ['class' => 'form-control', 'placeholder' => __('Enter Title Text')]) }}
                            @error('title_text')
                            <span class="invalid-title_text" role="alert">
                                <strong class="text-danger">{{ $message }}</strong>
                            </span>
                            @enderror
                            </div>


                            <div class="col-3 my-auto">
                                <div class="form-group">
                                    <label class="text-dark mb-1" for="SITE_RTL">{{ __('Enable RTL') }}</label>
                                    <div class="">
                                        <input type="checkbox" name="SITE_RTL" id="SITE_RTL"
                                            data-toggle="switchbutton"
                                            {{ $settings['SITE_RTL'] == 'on' ? 'checked="checked"' : '' }}
                                            data-onstyle="primary">
                                        <label class="form-check-labe" for="SITE_RTL"></label>
                                    </div>
                                </div>
                            </div>

                        <div class="col-3">
                            <div class="form-group"
                                style="
                                        margin-left: -70px;
                                        margin-right: -25px;">
                                {{ Form::label('company_default_language', __('Default Language'), ['class' => 'form-label text-dark']) }}
                                <div class="changeLanguage">
                                    <select name="company_default_language" id="company_default_language"
                                        class="form-control select">
                                        @foreach (App\Models\Utility::languages() as $code => $language)
                                        <option @if ($lang==$code) selected @endif
                                            value="{{ $code }}">
                                            {{ Str::upper($language) }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('company_default_language')
                                <span class="invalid-company_default_language" role="alert">
                                    <strong class="text-danger">{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>

                        </div>

                    </div> --}}
                    <div class="row">
                        {{-- <div class="form-group col-md-3">
                            {{Form::label('title_text',__('Title Text'),array('class'=>'form-label')) }}
                            {{Form::text('title_text',Utility::getValByName('title_text') ? Utility::getValByName('title_text') : 'ErpGo Saas',array('class'=>'form-control','placeholder'=>__('Title Text')))}}
                            @error('title_text')
                            <span class="invalid-title_text" role="alert">
                                <strong class="text-danger">{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                        <div class="col-md-3 form-group">
                            {{Form::label('footer_text',__('Footer Text'),['class'=>'form-label']) }}
                            {{Form::text('footer_text',Utility::getValByName('footer_text'),array('class'=>'form-control','placeholder'=>__('Enter Footer Text')))}}
                            @error('footer_text')
                            <span class="invalid-footer_text" role="alert">
                                <strong class="text-danger">{{ $message }}</strong>
                            </span>
                            @enderror
                        </div> --}}
                        {{-- <div class="col-md-3">
                            <div class="form-group">
                                {{Form::label('default_language',__('Default Language'),['class'=>'form-label text-dark']) }}
                                <div class="changeLanguage">
                                    <select name="default_language" id="default_language" class="form-control select">
                                        @foreach (\App\Models\Utility::languages() as $code => $language)
                                        <option @if ($lang==$code) selected @endif value="{{ $code }}">
                                            {{ucFirst($language) }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('default_language')
                                <span class="invalid-default_language" role="alert">
                                    <strong class="text-danger">{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div> --}}


                        {{-- <div class="col-3 form-group">
                            <div class="custom-control custom-switch">
                                <label class=" mb-1 mt-1 " for="SITE_RTL">{{ __('Enable RTL') }}</label>
                                <div class="">
                                    <input type="checkbox" name="SITE_RTL" id="SITE_RTL" data-toggle="switchbutton" {{ $settings['SITE_RTL'] == 'on' ? 'checked="checked"' : '' }} data-onstyle="primary">
                                    <label class="custom-control-label" for="SITE_RTL"></label>
                                </div>
                            </div>
                        </div> --}}


                    </div>
                    <h4 class="small-title">{{ __('Theme Customizer') }}</h4>
                    <div class="setting-card setting-logo-box p-3">
                        <div class="row">
                            <div class="col-lg-4 col-xl-4 col-md-4">
                                <h6 class="mt-2">
                                    <i data-feather="credit-card"
                                        class="me-2"></i>{{ __('Primary color settings') }}
                                </h6>
                                <hr class="my-2" />
                                <div class="color-wrp">
                                    <div class="theme-color themes-color">
                                        <a href="#!"
                                            class="themes-color-change {{ $color == 'theme-1' ? 'active_color' : '' }}"
                                            data-value="theme-1"></a>
                                        <input type="radio" class="theme_color d-none" name="color"
                                            value="theme-1" {{ $color == 'theme-1' ? 'checked' : '' }}>
                                        <a href="#!"
                                            class="themes-color-change {{ $color == 'theme-2' ? 'active_color' : '' }}"
                                            data-value="theme-2"></a>
                                        <input type="radio" class="theme_color d-none" name="color"
                                            value="theme-2" {{ $color == 'theme-2' ? 'checked' : '' }}>
                                        <a href="#!"
                                            class="themes-color-change {{ $color == 'theme-3' ? 'active_color' : '' }}"
                                            data-value="theme-3"></a>
                                        <input type="radio" class="theme_color d-none" name="color"
                                            value="theme-3" {{ $color == 'theme-3' ? 'checked' : '' }}>
                                        <a href="#!"
                                            class="themes-color-change {{ $color == 'theme-4' ? 'active_color' : '' }}"
                                            data-value="theme-4"></a>
                                        <input type="radio" class="theme_color d-none" name="color"
                                            value="theme-4" {{ $color == 'theme-4' ? 'checked' : '' }}>
                                        <a href="#!"
                                            class="themes-color-change {{ $color == 'theme-5' ? 'active_color' : '' }}"
                                            data-value="theme-5"></a>
                                        <input type="radio" class="theme_color d-none" name="color"
                                            value="theme-5" {{ $color == 'theme-5' ? 'checked' : '' }}>
                                        <br>
                                        <a href="#!"
                                            class="themes-color-change {{ $color == 'theme-6' ? 'active_color' : '' }}"
                                            data-value="theme-6"></a>
                                        <input type="radio" class="theme_color d-none" name="color"
                                            value="theme-6" {{ $color == 'theme-6' ? 'checked' : '' }}>
                                        <a href="#!"
                                            class="themes-color-change {{ $color == 'theme-7' ? 'active_color' : '' }}"
                                            data-value="theme-7"></a>
                                        <input type="radio" class="theme_color d-none" name="color"
                                            value="theme-7" {{ $color == 'theme-7' ? 'checked' : '' }}>
                                        <a href="#!"
                                            class="themes-color-change {{ $color == 'theme-8' ? 'active_color' : '' }}"
                                            data-value="theme-8"></a>
                                        <input type="radio" class="theme_color d-none" name="color"
                                            value="theme-8" {{ $color == 'theme-8' ? 'checked' : '' }}>
                                        <a href="#!"
                                            class="themes-color-change {{ $color == 'theme-9' ? 'active_color' : '' }}"
                                            data-value="theme-9"></a>
                                        <input type="radio" class="theme_color d-none" name="color"
                                            value="theme-9" {{ $color == 'theme-9' ? 'checked' : '' }}>
                                        <a href="#!"
                                            class="themes-color-change {{ $color == 'theme-10' ? 'active_color' : '' }}"
                                            data-value="theme-10"></a>
                                        <input type="radio" class="theme_color d-none" name="color"
                                            value="theme-10" {{ $color == 'theme-10' ? 'checked' : '' }}>
                                    </div>
                                    <div class="color-picker-wrp">
                                        <input type="color" value="{{ $color ? $color : '' }}"
                                            class="colorPicker {{ isset($settings['color_flag']) && $settings['color_flag'] == 'true' ? 'active_color' : '' }} image-input"
                                            name="custom_color" data-bs-toggle="tooltip"
                                            data-bs-placement="right"
                                            title="{{ __('Select Your Own Brand Color') }}"
                                            id="color-picker">
                                        <input type="hidden" name="custom-color" id="colorCode">
                                        <input type='hidden' name="color_flag"
                                            value={{ isset($settings['color_flag']) && $settings['color_flag'] == 'true' ? 'true' : 'false' }}>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 col-xl-4 col-md-4">
                                <h6 class="mt-2">
                                    <i data-feather="layout" class="me-2"></i>{{ __('Sidebar settings') }}
                                </h6>
                                <hr class="my-2" />
                                <div class="form-check form-switch">
                                    <input type="checkbox" class="form-check-input" id="cust-theme-bg"
                                        name="cust_theme_bg"
                                        {{ !empty($settings['cust_theme_bg']) && $settings['cust_theme_bg'] == 'on' ? 'checked' : '' }} />
                                    <label class="form-check-label f-w-600 pl-1"
                                        for="cust-theme-bg">{{ __('Transparent layout') }}</label>
                                </div>
                            </div>
                            <div class="col-lg-4 col-xl-4 col-md-4">
                                <h6 class="mt-2">
                                    <i data-feather="sun" class="me-2"></i>{{ __('Layout settings') }}
                                </h6>
                                <hr class="my-2" />
                                <div class="form-check form-switch mt-2">
                                    <input type="checkbox" class="form-check-input" id="cust-darklayout"
                                        name="cust_darklayout"
                                        {{ !empty($settings['cust_darklayout']) && $settings['cust_darklayout'] == 'on' ? 'checked' : '' }} />
                                    <label class="form-check-label f-w-600 pl-1"
                                        for="cust-darklayout">{{ __('Dark Layout') }}</label>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                </div>
                <div class="card-footer text-end">
                      
                    <input class="btn btn-print-invoice btn-primary m-r-10" type="submit"
                        value="{{ __('Save Changes') }}">
                       
                </div>
                {{ Form::close() }}
            
        </div>
        <!--System Setting-->
        <div id="useradd-2" class="card border-0 rounded-2xl shadow-md overflow-hidden my-3">
                <div class="h-1 w-full" style="background:#007C38;"></div>

            <div class="card-header">
                <h5>{{ __('System Settings') }}</h5>
                <small class="text-muted">{{ __('Edit your system details') }}</small>
            </div>

            {{ Form::model($settings, ['route' => 'system.settings', 'class' => 'mb-0', 'method' => 'post']) }}
            <div class="card-body">
                <div class="row">
                    <div class="form-group col-md-6">
                        {{ Form::label('site_currency', __('Currency *'), ['class' => 'form-label']) }}
                        {{ Form::select('site_currency', $currencies, $settings['site_currency'] ?? 'NGN', ['class' => 'form-control font-style', 'id' => 'currency-select']) }}
                        @error('site_currency')
                        <span class="invalid-site_currency" role="alert">
                            <strong class="text-danger">{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                    <div class="form-group col-md-6">
                        {{ Form::label('site_currency_symbol', __('Currency Symbol *'), ['class' => 'form-label']) }}
                        {{ Form::text('site_currency_symbol', $settings['site_currency_symbol'] ?? '₦', ['class' => 'form-control', 'id' => 'currency-symbol', 'readonly' => 'readonly']) }}
                        @error('site_currency_symbol')
                        <span class="invalid-site_currency_symbol" role="alert">
                            <strong class="text-danger">{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label"
                                for="example3cols3Input">{{ __('Currency Symbol Position') }}</label>
                            <div class="row px-3">
                                <div class="form-check col-md-6">
                                    <input class="form-check-input" type="radio"
                                        name="site_currency_symbol_position" value="pre"
                                        @if (@$settings['site_currency_symbol_position']=='pre' ) checked @endif id="flexCheckDefault"
                                        checked>
                                    <label class="form-check-label" for="flexCheckDefault">
                                        {{ __('Pre') }}
                                    </label>
                                </div>
                                <div class="form-check col-md-6">
                                    <input class="form-check-input" type="radio"
                                        name="site_currency_symbol_position" value="post"
                                        @if (@$settings['site_currency_symbol_position']=='post' ) checked @endif id="flexCheckChecked">
                                    <label class="form-check-label" for="flexCheckChecked">
                                        {{ __('Post') }}
                                    </label>
                                </div>

                                {{-- <div class="col-md-6">
                                            <div class="custom-control custom-radio mb-3">

                                                <input type="radio" id="customRadio5" name="site_currency_symbol_position" value="pre" class="custom-control-input" @if (@$settings['site_currency_symbol_position'] == 'pre') checked @endif>
                                                <label class="custom-control-label" for="customRadio5">{{__('Pre')}}</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="custom-control custom-radio mb-3">
                                <input type="radio" id="customRadio6" name="site_currency_symbol_position" value="post" class="custom-control-input" @if (@$settings['site_currency_symbol_position']=='post' ) checked @endif>
                                <label class="custom-control-label" for="customRadio6">{{__('Post')}}</label>
                            </div>
                        </div> --}}
                    </div>
                </div>
            </div>
            <div class="form-group col-md-6">
                <label for="site_date_format" class="form-label">{{ __('Date Format') }}</label>
                <select type="text" name="site_date_format" class="form-control selectric"
                    id="site_date_format">
                    <option value="M j, Y"
                        @if (@$settings['site_date_format']=='M j, Y' ) selected="selected" @endif>Jan 1,2015</option>
                    <option value="d-m-Y"
                        @if (@$settings['site_date_format']=='d-m-Y' ) selected="selected" @endif>dd-mm-yyyy</option>
                    <option value="m-d-Y"
                        @if (@$settings['site_date_format']=='m-d-Y' ) selected="selected" @endif>mm-dd-yyyy</option>
                    <option value="Y-m-d"
                        @if (@$settings['site_date_format']=='Y-m-d' ) selected="selected" @endif>yyyy-mm-dd</option>
                </select>
            </div>
            <div class="form-group col-md-6">
                <label for="site_time_format" class="form-label">{{ __('Time Format') }}</label>
                <select type="text" name="site_time_format" class="form-control selectric"
                    id="site_time_format">
                    <option value="g:i A"
                        @if (@$settings['site_time_format']=='g:i A' ) selected="selected" @endif>10:30 PM</option>
                    <option value="g:i a"
                        @if (@$settings['site_time_format']=='g:i a' ) selected="selected" @endif>10:30 pm</option>
                    <option value="H:i"
                        @if (@$settings['site_time_format']=='H:i' ) selected="selected" @endif>22:30</option>
                </select>
            </div>
            <div class="form-group col-md-6">
                {{ Form::label('invoice_prefix', __('Invoice Prefix'), ['class' => 'form-label']) }}

                {{ Form::text('invoice_prefix', null, ['class' => 'form-control']) }}
                @error('invoice_prefix')
                <span class="invalid-invoice_prefix" role="alert">
                    <strong class="text-danger">{{ $message }}</strong>
                </span>
                @enderror
            </div>

            <div class="form-group col-md-6">
                {{ Form::label('invoice_starting_number', __('Invoice Starting Number'), ['class' => 'form-label']) }}
                {{ Form::text('invoice_starting_number', null, ['class' => 'form-control']) }}
                @error('invoice_starting_number')
                <span class="invalid-invoice_starting_number" role="alert">
                    <strong class="text-danger">{{ $message }}</strong>
                </span>
                @enderror
            </div>
            <div class="form-group col-md-6 d-none">
                {{ Form::label('proposal_prefix', __('Proposal Prefix'), ['class' => 'form-label']) }}
                {{ Form::text('proposal_prefix', null, ['class' => 'form-control']) }}
                @error('proposal_prefix')
                <span class="invalid-proposal_prefix" role="alert">
                    <strong class="text-danger">{{ $message }}</strong>
                </span>
                @enderror
            </div>
            <div class="form-group col-md-6 d-none">
                {{ Form::label('proposal_starting_number', __('Proposal Starting Number'), ['class' => 'form-label']) }}
                {{ Form::text('proposal_starting_number', null, ['class' => 'form-control']) }}
                @error('proposal_starting_number')
                <span class="invalid-proposal_starting_number" role="alert">
                    <strong class="text-danger">{{ $message }}</strong>
                </span>
                @enderror
            </div>

            <div class="form-group col-md-6">
                {{ Form::label('bill_prefix', __('Bill Prefix'), ['class' => 'form-label']) }}
                {{ Form::text('bill_prefix', null, ['class' => 'form-control']) }}
                @error('bill_prefix')
                <span class="invalid-bill_prefix" role="alert">
                    <strong class="text-danger">{{ $message }}</strong>
                </span>
                @enderror
            </div>
            <div class="form-group col-md-6 d-none">
                {{ Form::label('retainer_starting_number', __('Retainer Starting Number'), ['class' => 'form-label']) }}
                {{ Form::text('retainer_starting_number', null, ['class' => 'form-control']) }}
                @error('retainer_starting_number')
                <span class="invalid-proposal_starting_number" role="alert">
                    <strong class="text-danger">{{ $message }}</strong>
                </span>
                @enderror
            </div>

            <div class="form-group col-md-6 d-none">
                {{ Form::label('retainer_prefix', __('Retainer Prefix'), ['class' => 'form-label']) }}
                {{ Form::text('retainer_prefix', null, ['class' => 'form-control']) }}
                @error('retainer_prefix')
                <span class="invalid-bill_prefix" role="alert">
                    <strong class="text-danger">{{ $message }}</strong>
                </span>
                @enderror
            </div>
            <div class="form-group col-md-6">
                {{ Form::label('bill_starting_number', __('Bill Starting Number'), ['class' => 'form-label']) }}
                {{ Form::text('bill_starting_number', null, ['class' => 'form-control']) }}
                @error('bill_starting_number')
                <span class="invalid-bill_starting_number" role="alert">
                    <strong class="text-danger">{{ $message }}</strong>
                </span>
                @enderror
            </div>
            <div class="form-group col-md-6">
                {{ Form::label('customer_prefix', __('Customer Prefix'), ['class' => 'form-label']) }}
                {{ Form::text('customer_prefix', null, ['class' => 'form-control']) }}
                @error('customer_prefix')
                <span class="invalid-customer_prefix" role="alert">
                    <strong class="text-danger">{{ $message }}</strong>
                </span>
                @enderror
            </div>
            <div class="form-group col-md-6">
                {{ Form::label('vender_prefix', __('Vender Prefix'), ['class' => 'form-label']) }}
                {{ Form::text('vender_prefix', null, ['class' => 'form-control']) }}
                @error('vender_prefix')
                <span class="invalid-vender_prefix" role="alert">
                    <strong class="text-danger">{{ $message }}</strong>
                </span>
                @enderror
            </div>
            <div class="form-group col-md-6">
                {{ Form::label('footer_title', __('Invoice/Bill Footer Title'), ['class' => 'form-label']) }}
                {{ Form::text('footer_title', null, ['class' => 'form-control']) }}
                @error('footer_title')
                <span class="invalid-footer_title" role="alert">
                    <strong class="text-danger">{{ $message }}</strong>
                </span>
                @enderror
            </div>

            <div class="form-group col-md-6">
                {{ Form::label('decimal_number', __('Decimal Number Format'), ['class' => 'form-label']) }}
                {{ Form::number('decimal_number', null, ['class' => 'form-control']) }}
                @error('decimal_number')
                <span class="invalid-decimal_number" role="alert">
                    <strong class="text-danger">{{ $message }}</strong>
                </span>
                @enderror
            </div>

            <div class="form-group col-md-6">
                {{ Form::label('journal_prefix', __('Journal Prefix'), ['class' => 'form-label']) }}
                {{ Form::text('journal_prefix', null, ['class' => 'form-control']) }}
                @error('journal_prefix')
                <span class="invalid-journal_prefix" role="alert">
                    <strong class="text-danger">{{ $message }}</strong>
                </span>
                @enderror
            </div>


            <div class="form-group col-md-6">
                {{ Form::label('shipping_display', __('Display Shipping in Proposal / Invoice / Bill'), ['class' => 'form-label']) }}
                <div class=" form-switch form-switch-left">
                    <input type="checkbox" class="form-check-input" name="shipping_display"
                        id="email_tempalte_13"
                        {{ $settings['shipping_display'] == 'on' ? 'checked' : '' }}>
                    <label class="form-check-label" for="email_tempalte_13"></label>
                </div>

                @error('shipping_display')
                <span class="invalid-shipping_display" role="alert">
                    <strong class="text-danger">{{ $message }}</strong>
                </span>
                @enderror
            </div>
            <div class="form-group col-md-6">
                {{ Form::label('footer_notes', __('Invoice/Bill Footer Notes'), ['class' => 'form-label']) }}
                <textarea class="summernote-simple" name="footer_notes">{!! $settings['footer_notes'] !!}</textarea>
                @error('footer_notes')
                <span class="invalid-footer_notes" role="alert">
                    <strong class="text-danger">{{ $message }}</strong>
                </span>
                @enderror
            </div>
        </div>
    </div>
    <div class="card-footer text-end">
       
        <input class="btn btn-print-invoice  btn-primary m-r-10" type="submit"
            value="{{ __('Save Changes') }}">
       
    </div>
    {{ Form::close() }}
</div>

<!--Company Setting-->
<div id="useradd-3" class="card border-0 rounded-2xl shadow-md overflow-hidden my-3">
    <div class="h-1 w-full" style="background:#007C38;"></div>

    <div class="card-header">
        <h5>{{ __('Company Settings') }}</h5>
        <small class="text-muted">{{ __('Edit your company details') }}</small>
    </div>
    {{ Form::model($settings, ['route' => 'company.settings', 'method' => 'post', 'class' => 'mb-0']) }}
    <div class="card-body">
        <div class="row">
            <div class="form-group col-md-6">
                {{ Form::label('company_name *', __('Company Name *'), ['class' => 'form-label']) }}
                {{ Form::text('company_name', null, ['class' => 'form-control font-style']) }}
                @error('company_name')
                <span class="invalid-company_name" role="alert">
                    <strong class="text-danger">{{ $message }}</strong>
                </span>
                @enderror
            </div>
            <div class="form-group col-md-6">
                {{ Form::label('company_address', __('Address'), ['class' => 'form-label']) }}
                {{ Form::text('company_address', null, ['class' => 'form-control font-style']) }}
                @error('company_address')
                <span class="invalid-company_address" role="alert">
                    <strong class="text-danger">{{ $message }}</strong>
                </span>
                @enderror
            </div>
            <div class="form-group col-md-6">
                {{ Form::label('company_city', __('City'), ['class' => 'form-label']) }}
                {{ Form::text('company_city', null, ['class' => 'form-control font-style']) }}
                @error('company_city')
                <span class="invalid-company_city" role="alert">
                    <strong class="text-danger">{{ $message }}</strong>
                </span>
                @enderror
            </div>
            <div class="form-group col-md-6">
                {{ Form::label('company_state', __('State'), ['class' => 'form-label']) }}
                {{ Form::text('company_state', null, ['class' => 'form-control font-style']) }}
                @error('company_state')
                <span class="invalid-company_state" role="alert">
                    <strong class="text-danger">{{ $message }}</strong>
                </span>
                @enderror
            </div>
            <div class="form-group col-md-6">
                {{ Form::label('company_zipcode', __('Zip/Post Code'), ['class' => 'form-label']) }}
                {{ Form::text('company_zipcode', null, ['class' => 'form-control']) }}
                @error('company_zipcode')
                <span class="invalid-company_zipcode" role="alert">
                    <strong class="text-danger">{{ $message }}</strong>
                </span>
                @enderror
            </div>
            <div class="form-group  col-md-6">
                {{ Form::label('company_country', __('Country'), ['class' => 'form-label']) }}
                {{ Form::text('company_country', null, ['class' => 'form-control font-style']) }}
                @error('company_country')
                <span class="invalid-company_country" role="alert">
                    <strong class="text-danger">{{ $message }}</strong>
                </span>
                @enderror
            </div>
            <div class="form-group col-md-6">
                {{ Form::label('company_telephone', __('Telephone'), ['class' => 'form-label']) }}
                {{ Form::text('company_telephone', null, ['class' => 'form-control']) }}
                @error('company_telephone')
                <span class="invalid-company_telephone" role="alert">
                    <strong class="text-danger">{{ $message }}</strong>
                </span>
                @enderror
            </div>
            <div class="form-group col-md-6">
                {{ Form::label('registration_number', __('Company Registration Number *'), ['class' => 'form-label']) }}
                {{ Form::text('registration_number', null, ['class' => 'form-control']) }}
                @error('registration_number')
                <span class="invalid-registration_number" role="alert">
                    <strong class="text-danger">{{ $message }}</strong>
                </span>
                @enderror
            </div>
            <div class="form-group col-md-6">
                <div class="row mt-4">
                    <div class="col-md-6">
                        {{ Form::label('tax_number', __('Tax Number'), ['class' => 'form-chech-label']) }}
                        <div class="form-check form-switch custom-switch-v1 float-end">
                            <input type="checkbox" class="form-check-input" name="tax_number"
                                id="tax_number"
                                {{ $settings['tax_number'] == 'on' ? 'checked' : '' }}>
                            <label class="form-check-label" for="vat_gst_number_switch"></label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group col-md-6" id="tax_checkbox_id">
                <div class="form-group">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-check form-check-inline form-group mb-3">
                                <input type="radio" id="customRadio8" name="tax_type"
                                    value="VAT" class="form-check-input"
                                    {{ $settings['tax_type'] == 'VAT' ? 'checked' : '' }}>
                                <label class="form-check-label"
                                    for="customRadio8">{{ __('VAT Number') }}</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check form-check-inline form-group mb-3">
                                <input type="radio" id="customRadio7" name="tax_type"
                                    value="GST" class="form-check-input"
                                    {{ $settings['tax_type'] == 'GST' ? 'checked' : '' }}>
                                <label class="form-check-label"
                                    for="customRadio7">{{ __('GST Number') }}</label>
                            </div>
                        </div>
                    </div>
                    {{ Form::text('vat_number', null, ['class' => 'form-control', 'placeholder' => __('Enter VAT / GST Number')]) }}
                </div>
            </div>
        </div>
    </div>
    <div class="card-footer text-end">
        <input class="btn btn-print-invoice btn-primary m-r-10" type="submit" id="addSig"
            value="{{ __('Save Changes') }}">
    </div>
    {{ Form::close() }}
</div>

<!--Email Setting-->
<div id="useradd-12" class="card d-none">
    <div class="card-header">
        <h5>{{ __('Email Settings') }}</h5>
        <small
            class="text-muted">{{ __('This SMTP will be used for system-level email sending. Additionally, if a company user does not set their SMTP,
                                                                then this SMTP will be used for sending emails.') }}</small>
    </div>
    <div class="card-body">
        {{ Form::model($settings, ['route' => ['company.email.settings'], 'method' => 'post', 'class' => 'mb-0']) }}
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    {{ Form::label('mail_driver', __('Mail Driver'), ['class' => 'form-label']) }}
                    {{ Form::text('mail_driver', null, ['class' => 'form-control', 'placeholder' => __('Enter Mail Driver')]) }}
                    @error('mail_driver')
                    <span class="invalid-mail_driver" role="alert">
                        <strong class="text-danger">{{ $message }}</strong>
                    </span>
                    @enderror
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    {{ Form::label('mail_host', __('Mail Host'), ['class' => 'form-label']) }}
                    {{ Form::text('mail_host', null, ['class' => 'form-control ', 'placeholder' => __('Enter Mail Host')]) }}
                    @error('mail_host')
                    <span class="invalid-mail_driver" role="alert">
                        <strong class="text-danger">{{ $message }}</strong>
                    </span>
                    @enderror
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    {{ Form::label('mail_port', __('Mail Port'), ['class' => 'form-label']) }}
                    {{ Form::text('mail_port', null, ['class' => 'form-control', 'placeholder' => __('Enter Mail Port')]) }}
                    @error('mail_port')
                    <span class="invalid-mail_port" role="alert">
                        <strong class="text-danger">{{ $message }}</strong>
                    </span>
                    @enderror
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    {{ Form::label('mail_username', __('Mail Username'), ['class' => 'form-label']) }}
                    {{ Form::text('mail_username', null, ['class' => 'form-control', 'placeholder' => __('Enter Mail Username')]) }}
                    @error('mail_username')
                    <span class="invalid-mail_username" role="alert">
                        <strong class="text-danger">{{ $message }}</strong>
                    </span>
                    @enderror
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    {{ Form::label('mail_password', __('Mail Password'), ['class' => 'form-label']) }}
                    {{ Form::text('mail_password', null, ['class' => 'form-control', 'placeholder' => __('Enter Mail Password')]) }}
                    @error('mail_password')
                    <span class="invalid-mail_password" role="alert">
                        <strong class="text-danger">{{ $message }}</strong>
                    </span>
                    @enderror
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    {{ Form::label('mail_encryption', __('Mail Encryption'), ['class' => 'form-label']) }}
                    {{ Form::text('mail_encryption', null, ['class' => 'form-control', 'placeholder' => __('Enter Mail Encryption')]) }}
                    @error('mail_encryption')
                    <span class="invalid-mail_encryption" role="alert">
                        <strong class="text-danger">{{ $message }}</strong>
                    </span>
                    @enderror
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    {{ Form::label('mail_from_address', __('Mail From Address'), ['class' => 'form-label']) }}
                    {{ Form::text('mail_from_address', null, ['class' => 'form-control', 'placeholder' => __('Enter Mail From Address')]) }}
                    @error('mail_from_address')
                    <span class="invalid-mail_from_address" role="alert">
                        <strong class="text-danger">{{ $message }}</strong>
                    </span>
                    @enderror
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    {{ Form::label('mail_from_name', __('Mail From Name'), ['class' => 'form-label']) }}
                    {{ Form::text('mail_from_name', null, ['class' => 'form-control', 'placeholder' => __('Enter Mail From Name')]) }}
                    @error('mail_from_name')
                    <span class="invalid-mail_from_name" role="alert">
                        <strong class="text-danger">{{ $message }}</strong>
                    </span>
                    @enderror
                </div>
            </div>
        </div>
    </div>

    <!-- <div class="row"> -->
        <div class="card-footer d-flex justify-content-end mb-0">
            <div class="mb-0 me-2">
                <a href="javascript:void(0)" class="btn btn-primary send_email "
                    data-title="{{ __('Send Test Mail') }}"
                    data-url="{{ route('test.mail') }}">
                    {{ __('Send Test Mail') }}
                </a>
            </div>
            <div class=" text-end mb-0">
                <input class="btn btn-primary  m-r-10" type="submit" value="{{ __('Save Changes') }}">
            </div>
        </div>
    <!-- </div> -->
    {{ Form::close() }}
    
</div>

<!--Proposal Print Setting-->
<div id="useradd-4" class="card d-none">
    <div class="card-header">
        <h5>{{ __('Proposal Print Settings') }}</h5>
        <small class="text-muted">{{ __('Edit your company proposal details') }}</small>
    </div>

    <div class="bg-none">
        <div class="row company-setting">
            <div class="col-md-4">
                <div class="card-header card-body">
                    <form id="setting-form" method="post"
                        action="{{ route('proposal.template.setting') }}"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label for="address"
                                class="col-form-label">{{ __('Proposal Print Template') }}</label>
                            <select class="form-control" name="proposal_template">
                                @foreach (App\Models\Utility::templateData()['templates'] as $key => $template)
                                <option value="{{ $key }}"
                                    {{ isset($settings['proposal_template']) && $settings['proposal_template'] == $key ? 'selected' : '' }}>
                                    {{ $template }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        {{-- <div class="col-md-4"> --}}
                        <div class="form-group">
                            <label for="address"
                                class="col-form-label">{{ __('QR Display?') }}</label>
                            <div class="d-flex align-items-center">
                                <div class="form-check form-switch custom-switch-v1 mt-2">
                                    <input type="hidden" name="qr_display" value="off">
                                    <input type="checkbox" class="form-check-input input-primary"
                                        id="customswitchv1-1 qr_display" name="qr_display"
                                        {{ isset($settings['qr_display']) && $settings['qr_display'] == 'on' ? 'checked="checked"' : '' }}>
                                </div>
                            </div>
                        </div>
                        {{-- </div>  --}}
                        <div class="form-group">
                            <label class="col-form-label">{{ __('Color Input') }}</label>
                            <div class="row gutters-xs">
                                @foreach (App\Models\Utility::templateData()['colors'] as $key => $color)
                                <div class="col-auto">
                                    <label class="colorinput">
                                        <input name="proposal_color" type="radio"
                                            value="{{ $color }}" class="colorinput-input"
                                            {{ isset($settings['proposal_color']) && $settings['proposal_color'] == $color ? 'checked' : '' }}>
                                        <span class="colorinput-color"
                                            style="background: #{{ $color }}"></span>
                                    </label>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-form-label">{{ __('Proposal Logo') }}</label>
                            <div class="choose-files mt-5 ">
                                <label for="proposal_logo">
                                    <div class=" bg-primary proposal_logo_update"> <i
                                            class="ti ti-upload px-1"></i>{{ __('Choose file here') }}
                                    </div>
                                    <img id="blah4" class="mt-3" width="70%" />
                                    <input type="file" class="form-control file"
                                        name="proposal_logo" id="proposal_logo"
                                        data-filename="proposal_logo_update"
                                        onchange="document.getElementById('blah4').src = window.URL.createObjectURL(this.files[0])">
                                </label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="proposal_font" class="col-form-label">{{ __('Font Family') }}</label>
                            <select class="form-control" name="proposal_font" id="proposal_font">
                                <option value="Inter" {{ isset($settings['proposal_font']) && $settings['proposal_font'] == 'Inter' ? 'selected' : '' }}>Inter</option>
                                <option value="Roboto" {{ isset($settings['proposal_font']) && $settings['proposal_font'] == 'Roboto' ? 'selected' : '' }}>Roboto</option>
                                <option value="Montserrat" {{ isset($settings['proposal_font']) && $settings['proposal_font'] == 'Montserrat' ? 'selected' : '' }}>Montserrat</option>
                                <option value="Open Sans" {{ isset($settings['proposal_font']) && $settings['proposal_font'] == 'Open Sans' ? 'selected' : '' }}>Open Sans</option>
                                <option value="Lato" {{ isset($settings['proposal_font']) && $settings['proposal_font'] == 'Lato' ? 'selected' : '' }}>Lato (Default)</option>
                                <option value="Poppins" {{ isset($settings['proposal_font']) && $settings['proposal_font'] == 'Poppins' ? 'selected' : '' }}>Poppins</option>
                            </select>
                        </div>
                        <div class="form-group mt-2 text-end">
                            <input type="submit" value="{{ __('Save Changes') }}"
                                class="btn btn-print-invoice  btn-primary m-r-10">
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-md-8">
                @if (isset($settings['proposal_template']) && isset($settings['proposal_color']))
                <iframe id="proposal_frame" class="w-100 h-100" frameborder="0"
                    src="{{ route('proposal.preview', [$settings['proposal_template'], $settings['proposal_color']]) }}"></iframe>
                @else
                <iframe id="proposal_frame" class="w-100 h-100" frameborder="0"
                    src="{{ route('proposal.preview', ['template1', 'ffffff']) }}"></iframe>
                @endif
            </div>
        </div>
    </div>

</div>

<!--Retainer Print Setting-->
<div id="useradd-10" class="card d-none">
    <div class="card-header">
        <h5>{{ __('Retainer Print Settings') }}</h5>
        <small class="text-muted">{{ __('Edit your company retainer details') }}</small>
    </div>

    <div class="bg-none">
        <div class="row company-setting">
            <div class="col-md-4">
                <div class="card-header card-body">
                    <form id="setting-form" method="post"
                        action="{{ route('retainer.template.setting') }}"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label for="address"
                                class="col-form-label">{{ __('Retainer Print Template') }}</label>
                            <select class="form-control" name="retainer_template">
                                @foreach (App\Models\Utility::templateData()['templates'] as $key => $template)
                                <option value="{{ $key }}"
                                    {{ isset($settings['retainer_template']) && $settings['retainer_template'] == $key ? 'selected' : '' }}>
                                    {{ $template }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        {{-- <div class="col-md-4"> --}}
                        <div class="form-group">
                            <label for="address"
                                class="col-form-label">{{ __('QR Display?') }}</label>
                            <div class="d-flex align-items-center">
                                <div class="form-check form-switch custom-switch-v1 mt-2">
                                    <input type="hidden" name="retainer_qr_display" value="off">
                                    <input type="checkbox" class="form-check-input input-primary"
                                        id="customswitchv1-1 retainer_qr_display"
                                        name="retainer_qr_display"
                                        {{ isset($settings['retainer_qr_display']) && $settings['retainer_qr_display'] == 'on' ? 'checked="checked"' : '' }}>
                                </div>
                            </div>
                        </div>
                        {{-- </div> --}}
                        <div class="form-group">
                            <label class="col-form-label">{{ __('Color Input') }}</label>
                            <div class="row gutters-xs">
                                @foreach (App\Models\Utility::templateData()['colors'] as $key => $color)
                                <div class="col-auto">
                                    <label class="colorinput">
                                        <input name="retainer_color" type="radio"
                                            value="{{ $color }}" class="colorinput-input"
                                            {{ isset($settings['retainer_color']) && $settings['retainer_color'] == $color ? 'checked' : '' }}>
                                        <span class="colorinput-color"
                                            style="background: #{{ $color }}"></span>
                                    </label>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-form-label">{{ __('Retainer Logo') }}</label>
                            <div class="choose-files mt-5 ">
                                <label for="retainer_logo">
                                    <div class=" bg-primary proposal_logo_update"> <i
                                            class="ti ti-upload px-1"></i>{{ __('Choose file here') }}
                                    </div>
                                    <img id="blah5" class="mt-3" width="70%" />
                                    <input type="file" class="form-control file"
                                        name="retainer_logo" id="retainer_logo"
                                        data-filename="retainer_logo_update"
                                        onchange="document.getElementById('blah5').src = window.URL.createObjectURL(this.files[0])">
                                    <!-- <input type="file" class="form-control file" name="retainer_logo" id="retainer_logo" data-filename="retainer_logo_update"> -->
                                </label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="retainer_font" class="col-form-label">{{ __('Font Family') }}</label>
                            <select class="form-control" name="retainer_font" id="retainer_font">
                                <option value="Inter" {{ isset($settings['retainer_font']) && $settings['retainer_font'] == 'Inter' ? 'selected' : '' }}>Inter (Default)</option>
                                <option value="Roboto" {{ isset($settings['retainer_font']) && $settings['retainer_font'] == 'Roboto' ? 'selected' : '' }}>Roboto</option>
                                <option value="Montserrat" {{ isset($settings['retainer_font']) && $settings['retainer_font'] == 'Montserrat' ? 'selected' : '' }}>Montserrat</option>
                                <option value="Open Sans" {{ isset($settings['retainer_font']) && $settings['retainer_font'] == 'Open Sans' ? 'selected' : '' }}>Open Sans</option>
                                <option value="Lato" {{ isset($settings['retainer_font']) && $settings['retainer_font'] == 'Lato' ? 'selected' : '' }}>Lato</option>
                                <option value="Poppins" {{ isset($settings['retainer_font']) && $settings['retainer_font'] == 'Poppins' ? 'selected' : '' }}>Poppins</option>
                            </select>
                        </div>
                        <div class="form-group mt-2 text-end">
                            <input type="submit" value="{{ __('Save Changes') }}"
                                class="btn btn-print-invoice  btn-primary m-r-10">
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-md-8">
                @if (isset($settings['retainer_template']) && isset($settings['retainer_color']))
                <iframe id="retainer_frame" class="w-100 h-100" frameborder="0"
                    src="{{ route('retainer.preview', [$settings['retainer_template'], $settings['retainer_color']]) }}"></iframe>
                @else
                <iframe id="retainer_frame" class="w-100 h-100" frameborder="0"
                    src="{{ route('retainer.preview', ['template1', 'ffffff']) }}"></iframe>
                @endif
            </div>
        </div>
    </div>

</div>

<!--Invoice Setting-->
<div id="useradd-5" class="card border-0 rounded-2xl shadow-md overflow-hidden my-3">
    <div class="h-1 w-full" style="background:#007C38;"></div>

    <div class="card-header">
        <h5>{{ __('Invoice Print Settings') }}</h5>
        <small class="text-muted">{{ __('Edit your company invoice details') }}</small>
    </div>

    <div class="bg-none">
        <div class="row company-setting">
            <div class="col-md-4">
                <div class="card-header card-body">
                    <form id="setting-form" method="post"
                        action="{{ route('invoice.template.setting') }}"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label for="address"
                                class="col-form-label">{{ __('Invoice Template') }}</label>
                            <select class="form-control" name="invoice_template">
                                @foreach (Utility::templateData()['templates'] as $key => $template)
                                <option value="{{ $key }}"
                                    {{ isset($settings['invoice_template']) && $settings['invoice_template'] == $key ? 'selected' : '' }}>
                                    {{ $template }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        {{-- <div class="col-md-4"> --}}
                        <div class="form-group">
                            <label for="address"
                                class="col-form-label">{{ __('QR Display?') }}</label>
                            <div class="d-flex align-items-center">
                                <div class="form-check form-switch custom-switch-v1 mt-2">
                                    <input type="hidden" name="invoice_qr_display" value="off">
                                    <input type="checkbox" class="form-check-input input-primary"
                                        id="customswitchv1-1 invoice_qr_display"
                                        name="invoice_qr_display"
                                        {{ isset($settings['invoice_qr_display']) && $settings['invoice_qr_display'] == 'on' ? 'checked="checked"' : '' }}>
                                </div>
                            </div>
                        </div>
                        {{-- </div> --}}
                        <div class="form-group">
                            <label class="col-form-label">{{ __('Color Input') }}</label>
                            <div class="row gutters-xs">
                                @foreach (Utility::templateData()['colors'] as $key => $color)
                                <div class="col-auto">
                                    <label class="colorinput">
                                        <input name="invoice_color" type="radio"
                                            value="{{ $color }}" class="colorinput-input"
                                            {{ isset($settings['invoice_color']) && $settings['invoice_color'] == $color ? 'checked' : '' }}>
                                        <span class="colorinput-color"
                                            style="background: #{{ $color }}"></span>
                                    </label>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="invoice_font" class="col-form-label">{{ __('Font Family') }}</label>
                            <select class="form-control" name="invoice_font" id="invoice_font">
                                <option value="Inter" {{ isset($settings['invoice_font']) && $settings['invoice_font'] == 'Inter' ? 'selected' : '' }}>Inter (Default)</option>
                                <option value="Arial" {{ isset($settings['invoice_font']) && $settings['invoice_font'] == 'Arial' ? 'selected' : '' }}>Arial</option>
                                <option value="Helvetica" {{ isset($settings['invoice_font']) && $settings['invoice_font'] == 'Helvetica' ? 'selected' : '' }}>Helvetica</option>
                                <option value="Times New Roman" {{ isset($settings['invoice_font']) && $settings['invoice_font'] == 'Times New Roman' ? 'selected' : '' }}>Times New Roman</option>
                                <option value="Georgia" {{ isset($settings['invoice_font']) && $settings['invoice_font'] == 'Georgia' ? 'selected' : '' }}>Georgia</option>
                                <option value="Verdana" {{ isset($settings['invoice_font']) && $settings['invoice_font'] == 'Verdana' ? 'selected' : '' }}>Verdana</option>
                                <option value="Roboto" {{ isset($settings['invoice_font']) && $settings['invoice_font'] == 'Roboto' ? 'selected' : '' }}>Roboto</option>
                                <option value="Montserrat" {{ isset($settings['invoice_font']) && $settings['invoice_font'] == 'Montserrat' ? 'selected' : '' }}>Montserrat</option>
                                <option value="Open Sans" {{ isset($settings['invoice_font']) && $settings['invoice_font'] == 'Open Sans' ? 'selected' : '' }}>Open Sans</option>
                                <option value="Lato" {{ isset($settings['invoice_font']) && $settings['invoice_font'] == 'Lato' ? 'selected' : '' }}>Lato</option>
                                <option value="Poppins" {{ isset($settings['invoice_font']) && $settings['invoice_font'] == 'Poppins' ? 'selected' : '' }}>Poppins</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="col-form-label">{{ __('Invoice Logo') }}</label>
                            <div class="choose-files mt-5 ">
                                <label for="invoice_logo">
                                    <div class=" bg-primary invoice_logo_update"> <i
                                            class="ti ti-upload px-1"></i>{{ __('Choose file here') }}
                                    </div>
                                    <img id="blah6" class="mt-3" width="70%" />
                                    <input type="file" class="form-control file"
                                        name="invoice_logo" id="invoice_logo"
                                        data-filename="invoice_logo_update"
                                        onchange="document.getElementById('blah6').src = window.URL.createObjectURL(this.files[0])">
                                    <!-- <input type="file" class="form-control file" name="invoice_logo" id="invoice_logo" data-filename="invoice_logo_update"> -->
                                </label>
                            </div>
                        </div>
                        <div class="form-group mt-2 text-end">
                            <input type="submit" value="{{ __('Save Changes') }}"
                                class="btn btn-print-invoice  btn-primary m-r-10">
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-md-8">
                @if (isset($settings['invoice_template']) && isset($settings['invoice_color']))
                <iframe id="invoice_frame" class="w-100 h-100" frameborder="0"
                    src="{{ route('invoice.preview', [$settings['invoice_template'], $settings['invoice_color']]) }}"></iframe>
                @else
                <iframe id="invoice_frame" class="w-100 h-100" frameborder="0"
                    src="{{ route('invoice.preview', ['template1', 'ffffff']) }}"></iframe>
                @endif
            </div>


        </div>
    </div>


</div>

<!--Bill Setting-->
<!-- <div id="useradd-8" class="card border-0 rounded-2xl shadow-md overflow-hidden my-3">
    <div class="card-header">
        <h5>{{ __('Bill Print Settings') }}</h5>
        <small class="text-muted">{{ __('Edit your company bill details') }}</small>
    </div>

    <div class="bg-none">
        <div class="row company-setting">
            <div class="col-md-4">
                <div class="card-header card-body">
                    <form id="setting-form" method="post"
                        action="{{ route('bill.template.setting') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label for="address"
                                class="form-label">{{ __('Bill Template') }}</label>
                            <select class="form-control" name="bill_template">
                                @foreach (App\Models\Utility::templateData()['templates'] as $key => $template)
                                <option value="{{ $key }}"
                                    {{ isset($settings['bill_template']) && $settings['bill_template'] == $key ? 'selected' : '' }}>
                                    {{ $template }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        {{-- <div class="col-md-4"> --}}
                        <div class="form-group">
                            <label for="address"
                                class="col-form-label">{{ __('QR Display?') }}</label>
                            <div class="d-flex align-items-center">
                                <div class="form-check form-switch custom-switch-v1 mt-2">
                                    <input type="hidden" name="bill_qr_display" value="off">
                                    <input type="checkbox" class="form-check-input input-primary"
                                        id="customswitchv1-1 bill_qr_display" name="bill_qr_display"
                                        {{ isset($settings['bill_qr_display']) && $settings['bill_qr_display'] == 'on' ? 'checked="checked"' : '' }}>
                                </div>
                            </div>
                        </div>
                        {{-- </div> --}}
                        <div class="form-group">
                            <label class="col-form-label">{{ __('Color Input') }}</label>
                            <div class="row gutters-xs">
                                @foreach (Utility::templateData()['colors'] as $key => $color)
                                <div class="col-auto">
                                    <label class="colorinput">
                                        <input name="bill_color" type="radio"
                                            value="{{ $color }}" class="colorinput-input"
                                            {{ isset($settings['bill_color']) && $settings['bill_color'] == $color ? 'checked' : '' }}>
                                        <span class="colorinput-color"
                                            style="background: #{{ $color }}"></span>
                                    </label>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="bill_font" class="col-form-label">{{ __('Font Family') }}</label>
                            <select class="form-control" name="bill_font" id="bill_font">
                                <option value="Inter" {{ isset($settings['bill_font']) && $settings['bill_font'] == 'Inter' ? 'selected' : '' }}>Inter (Default)</option>
                                <option value="Arial" {{ isset($settings['bill_font']) && $settings['bill_font'] == 'Arial' ? 'selected' : '' }}>Arial</option>
                                <option value="Helvetica" {{ isset($settings['bill_font']) && $settings['bill_font'] == 'Helvetica' ? 'selected' : '' }}>Helvetica</option>
                                <option value="Times New Roman" {{ isset($settings['bill_font']) && $settings['bill_font'] == 'Times New Roman' ? 'selected' : '' }}>Times New Roman</option>
                                <option value="Georgia" {{ isset($settings['bill_font']) && $settings['bill_font'] == 'Georgia' ? 'selected' : '' }}>Georgia</option>
                                <option value="Verdana" {{ isset($settings['bill_font']) && $settings['bill_font'] == 'Verdana' ? 'selected' : '' }}>Verdana</option>
                                <option value="Roboto" {{ isset($settings['bill_font']) && $settings['bill_font'] == 'Roboto' ? 'selected' : '' }}>Roboto</option>
                                <option value="Montserrat" {{ isset($settings['bill_font']) && $settings['bill_font'] == 'Montserrat' ? 'selected' : '' }}>Montserrat</option>
                                <option value="Open Sans" {{ isset($settings['bill_font']) && $settings['bill_font'] == 'Open Sans' ? 'selected' : '' }}>Open Sans</option>
                                <option value="Lato" {{ isset($settings['bill_font']) && $settings['bill_font'] == 'Lato' ? 'selected' : '' }}>Lato</option>
                                <option value="Poppins" {{ isset($settings['bill_font']) && $settings['bill_font'] == 'Poppins' ? 'selected' : '' }}>Poppins</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="col-form-label">{{ __('Bill Logo') }}</label>
                            <div class="choose-files mt-5 ">
                                <label for="bill_logo">
                                    <div class=" bg-primary bill_logo_update"> <i
                                            class="ti ti-upload px-1"></i>{{ __('Choose file here') }}
                                    </div>
                                    <img id="blah7" class="mt-3" width="70%" />
                                    <input type="file" class="form-control file" name="bill_logo"
                                        id="bill_logo" data-filename="bill_logo_update"
                                        onchange="document.getElementById('blah7').src = window.URL.createObjectURL(this.files[0])">
                                </label>
                            </div>
                        </div>
                        <div class="form-group mt-2 text-end">
                            <input type="submit" value="{{ __('Save Changes') }}"
                                class="btn btn-print-invoice  btn-primary m-r-10">
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-md-8">
                @if (isset($settings['bill_template']) && isset($settings['bill_color']))
                <iframe id="bill_frame" class="w-100 h-100" frameborder="0"
                    src="{{ route('bill.preview', [$settings['bill_template'], $settings['bill_color']]) }}"></iframe>
                @else
                <iframe id="bill_frame" class="w-100 h-100" frameborder="0"
                    src="{{ route('bill.preview', ['template1', 'ffffff']) }}"></iframe>
                @endif
            </div>
        </div>
    </div>
</div> -->
<div id="useradd-6" class="card border-0 rounded-2xl shadow-md overflow-hidden my-3">
    <div class="h-1 w-full" style="background:#007C38;"></div>

                    <div class="card-header">
                        <h5>{{ __('Bill Print Settings') }}</h5>
                        <small class="text-muted">{{ __('Edit your company bill details') }}</small>
                    </div>

                    <div class="bg-none">
                        <div class="row company-setting">
                            <div class="col-md-4">
                                <div class="card-header card-body">
                                    <!-- <h5></h5> -->
                                    <form id="setting-form" method="post" action="{{ route('bill.template.setting') }}" enctype="multipart/form-data">
                                        @csrf
                                        <div class="form-group">
                                            <label for="address" class="form-label">{{ __('Bill Template') }}</label>
                                            <select class="form-control" name="bill_template">
                                                @foreach (App\Models\Utility::templateData()['templates'] as $key => $template)
                                                <option value="{{ $key }}" {{ isset($settings['bill_template']) && $settings['bill_template'] == $key ? 'selected' : '' }}>
                                                    {{ $template }}
                                                </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="address" class="col-form-label">{{ __('QR Display?') }}</label>
                                            <div class="d-flex align-items-center">
                                                <div class="form-check form-switch custom-switch-v1 mt-2">
                                                    <input type="hidden" name="bill_qr_display" value="off">
                                                    <input type="checkbox" class="form-check-input input-primary" id="customswitchv1-1 bill_qr_display" name="bill_qr_display" {{ isset($settings['bill_qr_display']) && $settings['bill_qr_display'] == 'on' ? 'checked="checked"' : '' }}>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-form-label">{{ __('Color Input') }}</label>
                                            <div class="row gutters-xs">
                                                @foreach (Utility::templateData()['colors'] as $key => $color)
                                                <div class="col-auto">
                                                    <label class="colorinput">
                                                        <input name="bill_color" type="radio" value="{{ $color }}" class="colorinput-input" {{ isset($settings['bill_color']) && $settings['bill_color'] == $color ? 'checked' : '' }}>
                                                        <span class="colorinput-color" style="background: #{{ $color }}"></span>
                                                    </label>
                                                </div>
                                                @endforeach
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-form-label">{{ __('Bill Logo') }}</label>
                                            <div class="choose-files mt-5 ">
                                                <label for="bill_logo">
                                                    <div class=" bg-primary bill_logo_update"> <i class="ti ti-upload px-1"></i>{{ __('Choose file here') }}
                                                    </div>
                                                    <img id="blah7" class="mt-3"  width="70%" />
                                                    <input type="file" class="form-control file" name="bill_logo" id="bill_logo" data-filename="bill_logo_update" onchange="document.getElementById('blah7').src = window.URL.createObjectURL(this.files[0])">
                                                </label>
                                            </div>
                                        </div>
                                        <div class="form-group mt-2 text-end">
                                            <input type="submit" value="{{ __('Save Changes') }}" class="btn btn-print-invoice  btn-primary m-r-10">
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <div class="col-md-8">
                                @if (isset($settings['bill_template']) && isset($settings['bill_color']))
                                <iframe id="bill_frame" class="w-100 h-100" frameborder="0" src="{{ route('bill.preview', [$settings['bill_template'], $settings['bill_color']]) }}"></iframe>
                                @else
                                <iframe id="bill_frame" class="w-100 h-100" frameborder="0" src="{{ route('bill.preview', ['template1', 'ffffff']) }}"></iframe>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>


    <!--Payment Setting-->
    <div class="card d-none" id="useradd-7">
        <div class="card-header">
            <h5>{{ __('Payment Settings') }}</h5>
            <small class="text-secondary font-weight-bold">
                {{ __(' These details will be used to collect invoice payments. Each invoice will have a payment button based on the below configuration.') }}
            </small>
        </div>
        {{ Form::model($settings, ['route' => 'company.payment.settings', 'method' => 'POST', 'class' => 'mb-0']) }}
        @csrf
        <div class="card-body">
            <div class="row">
                <div class="col-12">
                    <div class="faq justify-content-center">
                        <div class="row">
                            <div class="col-12">
                                <div class="accordion accordion-flush setting-accordion"
                                    id="accordionExample">

                                    <!-- Bank Transfer -->
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="headingOne">
                                            <button class="accordion-button collapsed" type="button"
                                                data-bs-toggle="collapse" data-bs-target="#collapseBank"
                                                aria-expanded="false" aria-controls="collapseBank">
                                                <span class="d-flex align-items-center">

                                                    {{ __('Bank Transfer') }}
                                                </span>
                                                <div class="d-flex align-items-center">
                                                    <span class="me-2">{{ __('Enable:') }}</span>
                                                    <div class="form-check form-switch custom-switch-v1">
                                                        <input type="hidden" name="is_bank_enabled"
                                                            value="off">
                                                        <input type="checkbox" class="form-check-input"
                                                            name="is_bank_enabled" id="is_bank_enabled"
                                                            {{ isset($company_payment_setting['is_bank_enabled']) && $company_payment_setting['is_bank_enabled'] == 'on' ? 'checked="checked"' : '' }}>

                                                    </div>
                                                </div>
                                            </button>
                                        </h2>
                                        <div id="collapseBank" class="accordion-collapse collapse"
                                            aria-labelledby="headingOne"
                                            data-bs-parent="#accordionExample">
                                            <div class="accordion-body">
                                                <div class="row gy-4">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label
                                                                class="col-form-label">{{ __('Bank Details') }}</label>
                                                            <textarea class="form-control" rows="5" name="bank_detail">{{ !empty($company_payment_setting['bank_detail']) ? $company_payment_setting['bank_detail'] : '' }}</textarea>
                                                            <small>{{ __('Example : Bank : Bank name </br> Account Number : 0000 0000 </br>') }}</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Stripe -->
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="headingOne">
                                            <button class="accordion-button collapsed" type="button"
                                                data-bs-toggle="collapse" data-bs-target="#collapseOne"
                                                aria-expanded="false" aria-controls="collapseOne">
                                                <span class="d-flex align-items-center">
                                                    {{ __('Stripe') }}
                                                </span>
                                                <div class="d-flex align-items-center">
                                                    <span class="me-2">{{ __('Enable:') }}</span>
                                                    <div class="form-check form-switch custom-switch-v1">
                                                        <input type="hidden" name="is_stripe_enabled"
                                                            value="off">
                                                        <input type="checkbox" class="form-check-input"
                                                            name="is_stripe_enabled"
                                                            id="is_stripe_enabled"
                                                            {{ isset($company_payment_setting['is_stripe_enabled']) && $company_payment_setting['is_stripe_enabled'] == 'on' ? 'checked="checked"' : '' }}>

                                                    </div>
                                                </div>
                                            </button>
                                        </h2>
                                        <div id="collapseOne" class="accordion-collapse collapse"
                                            aria-labelledby="headingOne"
                                            data-bs-parent="#accordionExample">
                                            <div class="accordion-body">
                                                <div class="row gy-4">
                                                    <div class="col-lg-6">
                                                        <div class="input-edits">
                                                            <div class="form-group">
                                                                <label for="stripe_key"
                                                                    class="col-form-label">{{ __('Stripe Key') }}</label>
                                                                <input class="form-control"
                                                                    placeholder="{{ __('Enter Stripe Key') }}"
                                                                    name="stripe_key" type="text"
                                                                    value="{{ !isset($company_payment_setting['stripe_key']) || is_null($company_payment_setting['stripe_key']) ? '' : $company_payment_setting['stripe_key'] }}"
                                                                    id="stripe_key">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6">
                                                        <div class="input-edits">
                                                            <div class="form-group">
                                                                <label for="stripe_secret"
                                                                    class="col-form-label">{{ __('Stripe Secret') }}</label>
                                                                <input class="form-control "
                                                                    placeholder="Enter Stripe Secret"
                                                                    name="stripe_secret" type="text"
                                                                    value="{{ !isset($company_payment_setting['stripe_secret']) || is_null($company_payment_setting['stripe_secret']) ? '' : $company_payment_setting['stripe_secret'] }}"
                                                                    id="stripe_secret">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Paypal -->
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="headingTwo">
                                            <button class="accordion-button collapsed" type="button"
                                                data-bs-toggle="collapse" data-bs-target="#collapseTwo"
                                                aria-expanded="false" aria-controls="collapseTwo">
                                                <span class="d-flex align-items-center">
                                                    {{ __('Paypal') }}</span>
                                                <div class="d-flex align-items-center">
                                                    <span class="me-2">{{ __('Enable:') }}</span>
                                                    <div class="form-check form-switch custom-switch-v1">
                                                        <input type="hidden" name="is_paypal_enabled"
                                                            value="off">
                                                        <input type="checkbox" class="form-check-input"
                                                            name="is_paypal_enabled"
                                                            id="is_paypal_enabled"
                                                            {{ isset($company_payment_setting['is_paypal_enabled']) && $company_payment_setting['is_paypal_enabled'] == 'on' ? 'checked="checked"' : '' }}>

                                                    </div>
                                                </div>
                                            </button>
                                        </h2>
                                        <div id="collapseTwo" class="accordion-collapse collapse"
                                            aria-labelledby="headingTwo"
                                            data-bs-parent="#accordionExample">
                                            <div class="accordion-body">
                                                <div class="row gy-4">
                                                    <div class="col-md-12">
                                                        <label class="paypal-label col-form-label"
                                                            for="paypal_mode">{{ __('Paypal Mode') }}</label>
                                                        <br>
                                                        <div class="d-flex">
                                                            <div class="mr-2"
                                                                style="margin-right: 15px;">
                                                                <div class="border card p-3">
                                                                    <div class="form-check">
                                                                        <label
                                                                            class="form-check-labe text-dark {{ isset($company_payment_setting['paypal_mode']) && $company_payment_setting['paypal_mode'] == 'sandbox' ? 'active' : '' }}">
                                                                            <input type="radio"
                                                                                name="paypal_mode"
                                                                                value="sandbox"
                                                                                class="form-check-input"
                                                                                {{ isset($company_payment_setting['paypal_mode']) && $company_payment_setting['paypal_mode'] == 'sandbox' ? 'checked="checked"' : '' }}>

                                                                            {{ __('Sandbox') }}
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="mr-2">
                                                                <div class="border card p-3">
                                                                    <div class="form-check">
                                                                        <label
                                                                            class="form-check-labe text-dark">
                                                                            <input type="radio"
                                                                                name="paypal_mode"
                                                                                value="live"
                                                                                class="form-check-input"
                                                                                {{ isset($company_payment_setting['paypal_mode']) && $company_payment_setting['paypal_mode'] == 'live' ? 'checked="checked"' : '' }}>

                                                                            {{ __('Live') }}
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="paypal_client_id"
                                                                class="col-form-label">{{ __('Client ID') }}</label>
                                                            <input type="text"
                                                                name="paypal_client_id"
                                                                id="paypal_client_id"
                                                                class="form-control"
                                                                value="{{ !isset($company_payment_setting['paypal_client_id']) || is_null($company_payment_setting['paypal_client_id']) ? '' : $company_payment_setting['paypal_client_id'] }}"
                                                                placeholder="{{ __('Client ID') }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="paypal_secret_key"
                                                                class="col-form-label">{{ __('Secret Key') }}</label>
                                                            <input type="text"
                                                                name="paypal_secret_key"
                                                                id="paypal_secret_key"
                                                                class="form-control"
                                                                value="{{ !isset($company_payment_setting['paypal_secret_key']) || is_null($company_payment_setting['paypal_secret_key']) ? '' : $company_payment_setting['paypal_secret_key'] }}"
                                                                placeholder="{{ __('Secret Key') }}">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Paystack -->
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="headingThree">
                                            <button class="accordion-button collapsed" type="button"
                                                data-bs-toggle="collapse"
                                                data-bs-target="#collapseThree" aria-expanded="false"
                                                aria-controls="collapseThree">
                                                <span class="d-flex align-items-center">
                                                    {{ __('Paystack') }}
                                                </span>
                                                <div class="d-flex align-items-center">
                                                    <span class="me-2">{{ __('Enable:') }}</span>
                                                    <div class="form-check form-switch custom-switch-v1">
                                                        <input type="checkbox" class="form-check-input"
                                                            name="is_paystack_enabled"
                                                            id="is_paystack_enabled"
                                                            {{ isset($company_payment_setting['is_paystack_enabled']) && $company_payment_setting['is_paystack_enabled'] == 'on' ? 'checked' : '' }}>

                                                    </div>
                                                </div>
                                            </button>
                                        </h2>
                                        <div id="collapseThree" class="accordion-collapse collapse"
                                            aria-labelledby="headingThree"
                                            data-bs-parent="#accordionExample">
                                            <div class="accordion-body">
                                                <div class="row gy-4">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="paypal_client_id"
                                                                class="col-form-label">{{ __('Public Key') }}</label>
                                                            <input type="text"
                                                                name="paystack_public_key"
                                                                id="paystack_public_key"
                                                                class="form-control"
                                                                value="{{ !isset($company_payment_setting['paystack_public_key']) || is_null($company_payment_setting['paystack_public_key']) ? '' : $company_payment_setting['paystack_public_key'] }}"
                                                                placeholder="{{ __('Public Key') }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="paystack_secret_key"
                                                                class="col-form-label">{{ __('Secret Key') }}</label>
                                                            <input type="text"
                                                                name="paystack_secret_key"
                                                                id="paystack_secret_key"
                                                                class="form-control"
                                                                value="{{ !isset($company_payment_setting['paystack_secret_key']) || is_null($company_payment_setting['paystack_secret_key']) ? '' : $company_payment_setting['paystack_secret_key'] }}"
                                                                placeholder="{{ __('Secret Key') }}">
                                                            <div>

                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Flutterwave -->
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="headingFour">
                                            <button class="accordion-button collapsed" type="button"
                                                data-bs-toggle="collapse" data-bs-target="#collapseFour"
                                                aria-expanded="false" aria-controls="collapseFour">
                                                <span
                                                    class="d-flex align-items-center">{{ __('Flutterware') }}</span>
                                                <div class="d-flex align-items-center">
                                                    <span class="me-2">{{ __('Enable:') }}</span>
                                                    <div class="form-check form-switch custom-switch-v1">
                                                        <input type="hidden"
                                                            name="is_flutterwave_enabled"
                                                            value="off">
                                                        <input type="checkbox" class="form-check-input"
                                                            name="is_flutterwave_enabled"
                                                            id="is_flutterwave_enabled"
                                                            {{ isset($company_payment_setting['is_flutterwave_enabled']) && $company_payment_setting['is_flutterwave_enabled'] == 'on' ? 'checked' : '' }}>

                                                    </div>
                                                </div>
                                            </button>
                                        </h2>
                                        <div id="collapseFour" class="accordion-collapse collapse"
                                            aria-labelledby="headingFour"
                                            data-bs-parent="#accordionExample">
                                            <div class="accordion-body">
                                                <div class="row gy-4">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="paypal_client_id"
                                                                class="col-form-label">{{ __('Public Key') }}</label>
                                                            <input type="text"
                                                                name="flutterwave_public_key"
                                                                id="flutterwave_public_key"
                                                                class="form-control"
                                                                value="{{ !isset($company_payment_setting['flutterwave_public_key']) || is_null($company_payment_setting['flutterwave_public_key']) ? '' : $company_payment_setting['flutterwave_public_key'] }}"
                                                                placeholder="Public Key">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="paystack_secret_key"
                                                                class="col-form-label">{{ __('Secret Key') }}</label>
                                                            <input type="text"
                                                                name="flutterwave_secret_key"
                                                                id="flutterwave_secret_key"
                                                                class="form-control"
                                                                value="{{ !isset($company_payment_setting['flutterwave_secret_key']) || is_null($company_payment_setting['flutterwave_secret_key']) ? '' : $company_payment_setting['flutterwave_secret_key'] }}"
                                                                placeholder="Secret Key">
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Razorpay -->
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="headingFive">
                                            <button class="accordion-button collapsed" type="button"
                                                data-bs-toggle="collapse" data-bs-target="#collapseFive"
                                                aria-expanded="false" aria-controls="collapseFive">
                                                <span class="d-flex align-items-center">
                                                    {{ __('Razorpay') }}</span>
                                                <div class="d-flex align-items-center">
                                                    <span class="me-2">{{ __('Enable:') }}</span>
                                                    <div class="form-check form-switch custom-switch-v1">
                                                        <input type="hidden" name="is_razorpay_enabled"
                                                            value="off">
                                                        <input type="checkbox" class="form-check-input"
                                                            name="is_razorpay_enabled"
                                                            id="is_razorpay_enabled"
                                                            {{ isset($company_payment_setting['is_razorpay_enabled']) && $company_payment_setting['is_razorpay_enabled'] == 'on' ? 'checked="checked"' : '' }}>

                                                    </div>
                                                </div>
                                            </button>
                                        </h2>
                                        <div id="collapseFive" class="accordion-collapse collapse"
                                            aria-labelledby="headingFive"
                                            data-bs-parent="#accordionExample">
                                            <div class="accordion-body">
                                                <div class="row gy-4">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="paypal_client_id"
                                                                class="col-form-label">{{ __('Public Key') }}</label>

                                                            <input type="text"
                                                                name="razorpay_public_key"
                                                                id="razorpay_public_key"
                                                                class="form-control"
                                                                value="{{ !isset($company_payment_setting['razorpay_public_key']) || is_null($company_payment_setting['razorpay_public_key']) ? '' : $company_payment_setting['razorpay_public_key'] }}"
                                                                placeholder="Public Key">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="paystack_secret_key"
                                                                class="col-form-label">{{ __('Secret Key') }}</label>
                                                            <input type="text"
                                                                name="razorpay_secret_key"
                                                                id="razorpay_secret_key"
                                                                class="form-control"
                                                                value="{{ !isset($company_payment_setting['razorpay_secret_key']) || is_null($company_payment_setting['razorpay_secret_key']) ? '' : $company_payment_setting['razorpay_secret_key'] }}"
                                                                placeholder="Secret Key">
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Paytm -->
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="headingSix">
                                            <button class="accordion-button collapsed" type="button"
                                                data-bs-toggle="collapse" data-bs-target="#collapseSix"
                                                aria-expanded="false" aria-controls="collapseSix">
                                                <span
                                                    class="d-flex align-items-center">{{ __('Paytm') }}</span>
                                                <div class="d-flex align-items-center">
                                                    <span class="me-2">{{ __('Enable:') }}</span>
                                                    <div class="form-check form-switch custom-switch-v1">
                                                        <input type="hidden" name="is_paytm_enabled"
                                                            value="off">
                                                        <input type="checkbox" class="form-check-input"
                                                            name="is_paytm_enabled"
                                                            id="is_paytm_enabled"
                                                            {{ isset($company_payment_setting['is_paytm_enabled']) && $company_payment_setting['is_paytm_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                    </div>
                                                </div>
                                            </button>
                                        </h2>
                                        <div id="collapseSix" class="accordion-collapse collapse"
                                            aria-labelledby="headingSix"
                                            data-bs-parent="#accordionExample">
                                            <div class="accordion-body">
                                                <div class="col-md-12 pb-4">
                                                    <label class="paypal-label col-form-label"
                                                        for="paypal_mode">{{ __('Paytm Environment') }}</label>
                                                    <br>
                                                    <div class="d-flex">
                                                        <div class="mr-2" style="margin-right: 15px;">
                                                            <div class="border card p-3">
                                                                <div class="form-check">
                                                                    <label
                                                                        class="form-check-labe text-dark">

                                                                        <input type="radio"
                                                                            name="paytm_mode"
                                                                            value="local"
                                                                            class="form-check-input"
                                                                            {{ !isset($company_payment_setting['paytm_mode']) || $company_payment_setting['paytm_mode'] == '' || $company_payment_setting['paytm_mode'] == 'local' ? 'checked="checked"' : '' }}>

                                                                        {{ __('Local') }}
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="mr-2">
                                                            <div class="border card p-3">
                                                                <div class="form-check">
                                                                    <label
                                                                        class="form-check-labe text-dark">
                                                                        <input type="radio"
                                                                            name="paytm_mode"
                                                                            value="production"
                                                                            class="form-check-input"
                                                                            {{ isset($company_payment_setting['paytm_mode']) && $company_payment_setting['paytm_mode'] == 'production' ? 'checked="checked"' : '' }}>

                                                                        {{ __('Production') }}
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row gy-4">
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label for="paytm_public_key"
                                                                class="col-form-label">{{ __('Merchant ID') }}</label>
                                                            <input type="text"
                                                                name="paytm_merchant_id"
                                                                id="paytm_merchant_id"
                                                                class="form-control"
                                                                value="{{ !isset($company_payment_setting['paytm_merchant_id']) || is_null($company_payment_setting['paytm_merchant_id']) ? '' : $company_payment_setting['paytm_merchant_id'] }}"
                                                                placeholder="Merchant ID">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label for="paytm_secret_key"
                                                                class="col-form-label">{{ __('Merchant Key') }}</label>
                                                            <input type="text"
                                                                name="paytm_merchant_key"
                                                                id="paytm_merchant_key"
                                                                class="form-control"
                                                                value="{{ !isset($company_payment_setting['paytm_merchant_key']) || is_null($company_payment_setting['paytm_merchant_key']) ? '' : $company_payment_setting['paytm_merchant_key'] }}"
                                                                placeholder="Merchant Key">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label for="paytm_industry_type"
                                                                class="col-form-label">{{ __('Industry Type') }}</label>
                                                            <input type="text"
                                                                name="paytm_industry_type"
                                                                id="paytm_industry_type"
                                                                class="form-control"
                                                                value="{{ !isset($company_payment_setting['paytm_industry_type']) || is_null($company_payment_setting['paytm_industry_type']) ? '' : $company_payment_setting['paytm_industry_type'] }}"
                                                                placeholder="Industry Type">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Mercado Pago -->
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="headingseven">
                                            <button class="accordion-button collapsed" type="button"
                                                data-bs-toggle="collapse"
                                                data-bs-target="#collapseseven" aria-expanded="false"
                                                aria-controls="collapseseven">
                                                <span
                                                    class="d-flex align-items-center">{{ __('Mercado Pago') }}</span>
                                                <div class="d-flex align-items-center">
                                                    <span class="me-2">{{ __('Enable:') }}</span>
                                                    <div class="form-check form-switch custom-switch-v1">
                                                        <input type="hidden" name="is_mercado_enabled"
                                                            value="off">
                                                        <input type="checkbox" class="form-check-input"
                                                            name="is_mercado_enabled"
                                                            id="is_mercado_enabled"
                                                            {{ isset($company_payment_setting['is_mercado_enabled']) && $company_payment_setting['is_mercado_enabled'] == 'on' ? 'checked' : '' }}>

                                                    </div>
                                                </div>
                                            </button>
                                        </h2>
                                        <div id="collapseseven" class="accordion-collapse collapse"
                                            aria-labelledby="headingseven"
                                            data-bs-parent="#accordionExample">
                                            <div class="accordion-body">
                                                <div class="col-md-12 pb-4">
                                                    <label class="coingate-label col-form-label"
                                                        for="mercado_mode">{{ __('Mercado Mode') }}</label>
                                                    <br>
                                                    <div class="d-flex">
                                                        <div class="mr-2" style="margin-right: 15px;">
                                                            <div class="border card p-3">
                                                                <div class="form-check">
                                                                    <label
                                                                        class="form-check-labe text-dark">
                                                                        <input type="radio"
                                                                            name="mercado_mode"
                                                                            value="sandbox"
                                                                            class="form-check-input"
                                                                            {{ (isset($company_payment_setting['mercado_mode']) && $company_payment_setting['mercado_mode'] == '') || (isset($company_payment_setting['mercado_mode']) && $company_payment_setting['mercado_mode'] == 'sandbox') ? 'checked="checked"' : '' }}>
                                                                        {{ __('Sandbox') }}
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="mr-2">
                                                            <div class="border card p-3">
                                                                <div class="form-check">
                                                                    <label
                                                                        class="form-check-labe text-dark">
                                                                        <input type="radio"
                                                                            name="mercado_mode"
                                                                            value="live"
                                                                            class="form-check-input"
                                                                            {{ isset($company_payment_setting['mercado_mode']) && $company_payment_setting['mercado_mode'] == 'live' ? 'checked="checked"' : '' }}>
                                                                        {{ __('Live') }}
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="mercado_access_token"
                                                            class="col-form-label">{{ __('Access Token') }}</label>
                                                        <input type="text"
                                                            name="mercado_access_token"
                                                            id="mercado_access_token"
                                                            class="form-control"
                                                            value="{{ isset($company_payment_setting['mercado_access_token']) ? $company_payment_setting['mercado_access_token'] : '' }}"
                                                            placeholder="{{ __('Access Token') }}" />
                                                        @if ($errors->has('mercado_secret_key'))
                                                        <span class="invalid-feedback d-block">
                                                            {{ $errors->first('mercado_access_token') }}
                                                        </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Mollie -->
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="headingeight">
                                            <button class="accordion-button collapsed" type="button"
                                                data-bs-toggle="collapse"
                                                data-bs-target="#collapseeight" aria-expanded="false"
                                                aria-controls="collapseeight">
                                                <span class="d-flex align-items-center">
                                                    {{ __('Mollie') }}
                                                </span>
                                                <div class="d-flex align-items-center">
                                                    <span class="me-2">{{ __('Enable:') }}</span>
                                                    <div class="form-check form-switch custom-switch-v1">
                                                        <input type="hidden" name="is_mollie_enabled"
                                                            value="off">
                                                        <input type="checkbox" class="form-check-input"
                                                            name="is_mollie_enabled"
                                                            id="is_mollie_enabled"
                                                            {{ isset($company_payment_setting['is_mollie_enabled']) && $company_payment_setting['is_mollie_enabled'] == 'on' ? 'checked' : '' }}>
                                                    </div>
                                                </div>
                                            </button>
                                        </h2>
                                        <div id="collapseeight" class="accordion-collapse collapse"
                                            aria-labelledby="headingeight"
                                            data-bs-parent="#accordionExample">
                                            <div class="accordion-body">
                                                <div class="row gy-4">

                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label for="mollie_api_key"
                                                                class="col-form-label">{{ __('Mollie Api Key') }}</label>
                                                            <input type="text" name="mollie_api_key"
                                                                id="mollie_api_key" class="form-control"
                                                                value="{{ !isset($company_payment_setting['mollie_api_key']) || is_null($company_payment_setting['mollie_api_key']) ? '' : $company_payment_setting['mollie_api_key'] }}"
                                                                placeholder="Mollie Api Key">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label for="mollie_profile_id"
                                                                class="col-form-label">{{ __('Mollie Profile ID') }}</label>
                                                            <input type="text"
                                                                name="mollie_profile_id"
                                                                id="mollie_profile_id"
                                                                class="form-control"
                                                                value="{{ !isset($company_payment_setting['mollie_profile_id']) || is_null($company_payment_setting['mollie_profile_id']) ? '' : $company_payment_setting['mollie_profile_id'] }}"
                                                                placeholder="Mollie Profile Id">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label for="mollie_partner_id"
                                                                class="col-form-label">{{ __('Mollie Partner ID') }}</label>
                                                            <input type="text"
                                                                name="mollie_partner_id"
                                                                id="mollie_partner_id"
                                                                class="form-control"
                                                                value="{{ !isset($company_payment_setting['mollie_partner_id']) || is_null($company_payment_setting['mollie_partner_id']) ? '' : $company_payment_setting['mollie_partner_id'] }}"
                                                                placeholder="Mollie Partner Id">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Skrill -->
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="headingnine">
                                            <button class="accordion-button collapsed" type="button"
                                                data-bs-toggle="collapse" data-bs-target="#collapsenine"
                                                aria-expanded="false" aria-controls="collapsenine">
                                                <span class="d-flex align-items-center">
                                                    {{ __('Skrill') }}</span>
                                                <div class="d-flex align-items-center">
                                                    <span class="me-2">{{ __('Enable:') }}</span>
                                                    <div class="form-check form-switch custom-switch-v1">
                                                        <input type="hidden" name="is_skrill_enabled"
                                                            value="off">
                                                        <input type="checkbox" class="form-check-input"
                                                            name="is_skrill_enabled"
                                                            id="is_skrill_enabled"
                                                            {{ isset($company_payment_setting['is_skrill_enabled']) && $company_payment_setting['is_skrill_enabled'] == 'on' ? 'checked' : '' }}>
                                                    </div>
                                                </div>
                                            </button>
                                        </h2>
                                        <div id="collapsenine" class="accordion-collapse collapse"
                                            aria-labelledby="headingnine"
                                            data-bs-parent="#accordionExample">
                                            <div class="accordion-body">
                                                <div class="row gy-4">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="mollie_api_key"
                                                                class="col-form-label">{{ __('Skrill Email') }}</label>
                                                            <input type="text" name="skrill_email"
                                                                id="skrill_email" class="form-control"
                                                                value="{{ !isset($company_payment_setting['skrill_email']) || is_null($company_payment_setting['skrill_email']) ? '' : $company_payment_setting['skrill_email'] }}"
                                                                placeholder="Enter Skrill Email">
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- CoinGate -->
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="headingten">
                                            <button class="accordion-button collapsed" type="button"
                                                data-bs-toggle="collapse" data-bs-target="#collapseten"
                                                aria-expanded="false" aria-controls="collapseten">
                                                <span class="d-flex align-items-center">
                                                    {{ __('CoinGate') }}
                                                </span>
                                                <div class="d-flex align-items-center">
                                                    <span class="me-2">{{ __('Enable:') }}</span>
                                                    <div class="form-check form-switch custom-switch-v1">
                                                        <input type="hidden" name="is_coingate_enabled"
                                                            value="off">
                                                        <input type="checkbox" class="form-check-input"
                                                            name="is_coingate_enabled"
                                                            id="is_coingate_enabled"
                                                            {{ isset($company_payment_setting['is_coingate_enabled']) && $company_payment_setting['is_coingate_enabled'] == 'on' ? 'checked' : '' }}>

                                                    </div>
                                                </div>
                                            </button>
                                        </h2>
                                        <div id="collapseten" class="accordion-collapse collapse"
                                            aria-labelledby="headingten"
                                            data-bs-parent="#accordionExample">
                                            <div class="accordion-body">
                                                <div class="row gy-4">
                                                    <div class="col-md-12">
                                                        <label class="col-form-label"
                                                            for="coingate_mode">{{ __('CoinGate Mode') }}</label>
                                                        <br>
                                                        <div class="d-flex">
                                                            <div class="mr-2"
                                                                style="margin-right: 15px;">
                                                                <div class="border card p-3">
                                                                    <div class="form-check">
                                                                        <label
                                                                            class="form-check-labe text-dark">

                                                                            <input type="radio"
                                                                                name="coingate_mode"
                                                                                value="sandbox"
                                                                                class="form-check-input"
                                                                                {{ !isset($company_payment_setting['coingate_mode']) || $company_payment_setting['coingate_mode'] == '' || $company_payment_setting['coingate_mode'] == 'sandbox' ? 'checked="checked"' : '' }}>

                                                                            {{ __('Sandbox') }}
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="mr-2">
                                                                <div class="border card p-3">
                                                                    <div class="form-check">
                                                                        <label
                                                                            class="form-check-labe text-dark">
                                                                            <input type="radio"
                                                                                name="coingate_mode"
                                                                                value="live"
                                                                                class="form-check-input"
                                                                                {{ isset($company_payment_setting['coingate_mode']) && $company_payment_setting['coingate_mode'] == 'live' ? 'checked="checked"' : '' }}>
                                                                            {{ __('Live') }}
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="coingate_auth_token"
                                                                class="col-form-label">{{ __('CoinGate Auth Token') }}</label>
                                                            <input type="text"
                                                                name="coingate_auth_token"
                                                                id="coingate_auth_token"
                                                                class="form-control"
                                                                value="{{ !isset($company_payment_setting['coingate_auth_token']) || is_null($company_payment_setting['coingate_auth_token']) ? '' : $company_payment_setting['coingate_auth_token'] }}"
                                                                placeholder="CoinGate Auth Token">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- PaymentWall -->
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="headingeleven">
                                            <button class="accordion-button collapsed" type="button"
                                                data-bs-toggle="collapse"
                                                data-bs-target="#collapseeleven" aria-expanded="false"
                                                aria-controls="collapseeleven">
                                                <span
                                                    class="d-flex align-items-center">{{ __('PaymentWall') }}</span>
                                                <div class="d-flex align-items-center">
                                                    <span class="me-2">{{ __('Enable:') }}</span>
                                                    <div class="form-check form-switch custom-switch-v1">
                                                        <input type="hidden"
                                                            name="is_paymentwall_enabled"
                                                            value="off">
                                                        <input type="checkbox" class="form-check-input"
                                                            name="is_paymentwall_enabled"
                                                            id="is_paymentwall_enabled"
                                                            {{ isset($company_payment_setting['is_paymentwall_enabled']) && $company_payment_setting['is_paymentwall_enabled'] == 'on' ? 'checked' : '' }}>

                                                    </div>
                                                </div>
                                            </button>
                                        </h2>
                                        <div id="collapseeleven" class="accordion-collapse collapse"
                                            aria-labelledby="headingeleven"
                                            data-bs-parent="#accordionExample">
                                            <div class="accordion-body">
                                                <div class="row gy-4">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="paymentwall_public_key"
                                                                class="col-form-label">{{ __('Public Key') }}</label>
                                                            <input type="text"
                                                                name="paymentwall_public_key"
                                                                id="paymentwall_public_key"
                                                                class="form-control"
                                                                value="{{ !isset($company_payment_setting['paymentwall_public_key']) || is_null($company_payment_setting['paymentwall_public_key']) ? '' : $company_payment_setting['paymentwall_public_key'] }}"
                                                                placeholder="{{ __('Public Key') }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="paymentwall_secret_key"
                                                                class="col-form-label">{{ __('Private Key') }}</label>
                                                            <input type="text"
                                                                name="paymentwall_secret_key"
                                                                id="paymentwall_secret_key"
                                                                class="form-control"
                                                                value="{{ !isset($company_payment_setting['paymentwall_secret_key']) || is_null($company_payment_setting['paymentwall_secret_key']) ? '' : $company_payment_setting['paymentwall_secret_key'] }}"
                                                                placeholder="{{ __('Private Key') }}">
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Toyyibpay -->
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="headingtwelve">
                                            <button class="accordion-button collapsed" type="button"
                                                data-bs-toggle="collapse"
                                                data-bs-target="#collapsetwelve" aria-expanded="false"
                                                aria-controls="collapsetwelve">
                                                <span class="d-flex align-items-center">
                                                    {{ __('Toyyibpay') }}
                                                </span>
                                                <div class="d-flex align-items-center">
                                                    <span class="me-2">{{ __('Enable:') }}</span>
                                                    <div class="form-check form-switch custom-switch-v1">
                                                        <input type="hidden"
                                                            name="is_toyyibpay_enabled" value="off">
                                                        <input type="checkbox" class="form-check-input"
                                                            name="is_toyyibpay_enabled"
                                                            id="is_toyyibpay_enabled"
                                                            {{ isset($company_payment_setting['is_toyyibpay_enabled']) && $company_payment_setting['is_toyyibpay_enabled'] == 'on' ? 'checked' : '' }}>

                                                    </div>
                                                </div>
                                            </button>
                                        </h2>
                                        <div id="collapsetwelve" class="accordion-collapse collapse"
                                            aria-labelledby="headingtwelve"
                                            data-bs-parent="#accordionExample">
                                            <div class="accordion-body">
                                                <div class="row gy-4">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="toyyibpay_secret_key"
                                                                class="col-form-label">{{ __('Secret Key') }}</label>
                                                            <input type="text"
                                                                name="toyyibpay_secret_key"
                                                                id="toyyibpay_secret_key"
                                                                class="form-control"
                                                                value="{{ !isset($company_payment_setting['toyyibpay_secret_key']) || is_null($company_payment_setting['toyyibpay_secret_key']) ? '' : $company_payment_setting['toyyibpay_secret_key'] }}"
                                                                placeholder="{{ __('Secret Key') }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="category_code"
                                                                class="col-form-label">{{ __('Category Code') }}</label>
                                                            <input type="text" name="category_code"
                                                                id="category_code" class="form-control"
                                                                value="{{ !isset($company_payment_setting['category_code']) || is_null($company_payment_setting['category_code']) ? '' : $company_payment_setting['category_code'] }}"
                                                                placeholder="{{ __('Category Code') }}">
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Payfast -->
                                    <div class="accordion accordion-flush setting-accordion"
                                        id="accordionExample">
                                        <div class="accordion-item">
                                            <h2 class="accordion-header" id="headingOne">
                                                <button class="accordion-button collapsed"
                                                    type="button" data-bs-toggle="collapse"
                                                    data-bs-target="#collapseOne13"
                                                    aria-expanded="false" aria-controls="collapseOne13">
                                                    <span class="d-flex align-items-center">
                                                        {{ __('PayFast') }}
                                                    </span>
                                                    <div class="d-flex align-items-center">
                                                        <span class="me-2">{{ __('Enable:') }}</span>
                                                        <div
                                                            class="form-check form-switch custom-switch-v1">
                                                            <input type="hidden"
                                                                name="is_payfast_enabled"
                                                                value="off">
                                                            <input type="checkbox"
                                                                class="form-check-input"
                                                                name="is_payfast_enabled"
                                                                id="is_payfast_enabled"
                                                                {{ isset($company_payment_setting['is_payfast_enabled']) && $company_payment_setting['is_payfast_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                        </div>
                                                    </div>
                                                </button>
                                            </h2>
                                            <div id="collapseOne13" class="accordion-collapse collapse"
                                                aria-labelledby="headingOne"
                                                data-bs-parent="#accordionExample">
                                                <div class="accordion-body">
                                                    <div class="row">
                                                        <label class="paypal-label col-form-label"
                                                            for="payfast_mode">{{ __('Payfast Mode') }}</label>
                                                        <div class="d-flex">
                                                            <div class="mr-2"
                                                                style="margin-right: 15px;">
                                                                <div class="border card p-3">
                                                                    <div class="form-check">
                                                                        <label
                                                                            class="form-check-labe text-dark {{ isset($company_payment_setting['payfast_mode']) && $company_payment_setting['payfast_mode'] == 'sandbox' ? 'active' : '' }}">
                                                                            <input type="radio"
                                                                                name="payfast_mode"
                                                                                value="sandbox"
                                                                                class="form-check-input"
                                                                                {{ isset($company_payment_setting['payfast_mode']) && $company_payment_setting['payfast_mode'] == 'sandbox' ? 'checked="checked"' : '' }}>

                                                                            {{ __('Sandbox') }}
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="mr-2">
                                                                <div class="border card p-3">
                                                                    <div class="form-check">
                                                                        <label
                                                                            class="form-check-labe text-dark">
                                                                            <input type="radio"
                                                                                name="payfast_mode"
                                                                                value="live"
                                                                                class="form-check-input"
                                                                                {{ isset($company_payment_setting['payfast_mode']) && $company_payment_setting['payfast_mode'] == 'live' ? 'checked="checked"' : '' }}>

                                                                            {{ __('Live') }}
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label for="paytm_public_key"
                                                                    class="col-form-label">{{ __('Merchant ID') }}</label>
                                                                <input type="text"
                                                                    name="payfast_merchant_id"
                                                                    id="payfast_merchant_id"
                                                                    class="form-control"
                                                                    value="{{ !isset($company_payment_setting['payfast_merchant_id']) || is_null($company_payment_setting['payfast_merchant_id']) ? '' : $company_payment_setting['payfast_merchant_id'] }}"
                                                                    placeholder="Merchant ID">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label for="paytm_secret_key"
                                                                    class="col-form-label">{{ __('Merchant Key') }}</label>
                                                                <input type="text"
                                                                    name="payfast_merchant_key"
                                                                    id="payfast_merchant_key"
                                                                    class="form-control"
                                                                    value="{{ !isset($company_payment_setting['payfast_merchant_key']) || is_null($company_payment_setting['payfast_merchant_key']) ? '' : $company_payment_setting['payfast_merchant_key'] }}"
                                                                    placeholder="Merchant Key">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label for="payfast_signature"
                                                                    class="col-form-label">{{ __('Salt Passphrase') }}</label>
                                                                <input type="text"
                                                                    name="payfast_signature"
                                                                    id="payfast_signature"
                                                                    class="form-control"
                                                                    value="{{ !isset($company_payment_setting['payfast_signature']) || is_null($company_payment_setting['payfast_signature']) ? '' : $company_payment_setting['payfast_signature'] }}"
                                                                    placeholder="Salt passphrase">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Iyzipay -->
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="headingFourteen">
                                            <button class="accordion-button collapsed" type="button"
                                                data-bs-toggle="collapse"
                                                data-bs-target="#collapseFourteen" aria-expanded="false"
                                                aria-controls="collapseFourteen">
                                                <span class="d-flex align-items-center">
                                                    {{ __('IyziPay') }}
                                                </span>
                                                <div class="d-flex align-items-center">
                                                    <span class="me-2">{{ __('Enable:') }}</span>
                                                    <div class="form-check form-switch custom-switch-v1">
                                                        <input type="hidden" name="is_iyzipay_enabled"
                                                            value="off">
                                                        <input type="checkbox" class="form-check-input"
                                                            name="is_iyzipay_enabled"
                                                            id="is_iyzipay_enabled"
                                                            {{ isset($company_payment_setting['is_iyzipay_enabled']) && $company_payment_setting['is_iyzipay_enabled'] == 'on' ? 'checked="checked"' : '' }}>

                                                    </div>
                                                </div>
                                            </button>
                                        </h2>
                                        <div id="collapseFourteen" class="accordion-collapse collapse"
                                            aria-labelledby="headingFourteen"
                                            data-bs-parent="#accordionExample">
                                            <div class="accordion-body">
                                                <div class="row gy-4">
                                                    <div class="col-md-12">
                                                        <label class="paypal-label col-form-label"
                                                            for="paypal_mode">{{ __('IyziPay Mode') }}</label>
                                                        <br>
                                                        <div class="d-flex">
                                                            <div class="mr-2"
                                                                style="margin-right: 15px;">
                                                                <div class="border card p-3">
                                                                    <div class="form-check">
                                                                        <label
                                                                            class="form-check-labe text-dark {{ isset($company_payment_setting['iyzipay_mode']) && $company_payment_setting['iyzipay_mode'] == 'sandbox' ? 'active' : '' }}">
                                                                            <input type="radio"
                                                                                name="iyzipay_mode"
                                                                                value="sandbox"
                                                                                class="form-check-input"
                                                                                {{ isset($company_payment_setting['iyzipay_mode']) && $company_payment_setting['iyzipay_mode'] == 'sandbox' ? 'checked="checked"' : '' }}>

                                                                            {{ __('Sandbox') }}
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="mr-2">
                                                                <div class="border card p-3">
                                                                    <div class="form-check">
                                                                        <label
                                                                            class="form-check-labe text-dark">
                                                                            <input type="radio"
                                                                                name="iyzipay_mode"
                                                                                value="live"
                                                                                class="form-check-input"
                                                                                {{ isset($company_payment_setting['iyzipay_mode']) && $company_payment_setting['iyzipay_mode'] == 'live' ? 'checked="checked"' : '' }}>

                                                                            {{ __('Live') }}
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="iyzipay_private_key"
                                                                class="col-form-label">{{ __('Private Key') }}</label>
                                                            <input type="text"
                                                                name="iyzipay_private_key"
                                                                id="iyzipay_private_key"
                                                                class="form-control"
                                                                value="{{ !isset($company_payment_setting['iyzipay_private_key']) || is_null($company_payment_setting['iyzipay_private_key']) ? '' : $company_payment_setting['iyzipay_private_key'] }}"
                                                                placeholder="{{ __('Private key') }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="iyzipay_secret_key"
                                                                class="col-form-label">{{ __('Secret Key') }}</label>
                                                            <input type="text"
                                                                name="iyzipay_secret_key"
                                                                id="iyzipay_secret_key"
                                                                class="form-control"
                                                                value="{{ !isset($company_payment_setting['iyzipay_secret_key']) || is_null($company_payment_setting['iyzipay_secret_key']) ? '' : $company_payment_setting['iyzipay_secret_key'] }}"
                                                                placeholder="{{ __('Secret Key') }}">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- SSPAY -->
                                    <div class="accordion accordion-flush setting-accordion"
                                        id="accordionExample">
                                        <div class="accordion-item">
                                            <h2 class="accordion-header" id="headingFourteen">
                                                <button class="accordion-button collapsed"
                                                    type="button" data-bs-toggle="collapse"
                                                    data-bs-target="#collapse15" aria-expanded="false"
                                                    aria-controls="collapse15">
                                                    <span class="d-flex align-items-center">
                                                        {{ __('Sspay') }}
                                                    </span>
                                                    <div class="d-flex align-items-center">
                                                        <span class="me-2">{{ __('Enable:') }}</span>
                                                        <div
                                                            class="form-check form-switch custom-switch-v1">
                                                            <input type="hidden"
                                                                name="is_sspay_enabled" value="off">
                                                            <input type="checkbox"
                                                                class="form-check-input"
                                                                name="is_sspay_enabled"
                                                                id="is_sspay_enabled"
                                                                {{ isset($company_payment_setting['is_sspay_enabled']) && $company_payment_setting['is_sspay_enabled'] == 'on' ? 'checked' : '' }}>

                                                        </div>
                                                    </div>
                                                </button>
                                            </h2>
                                            <div id="collapse15" class="accordion-collapse collapse"
                                                aria-labelledby="headingFourteen"
                                                data-bs-parent="#accordionExample">
                                                <div class="accordion-body">
                                                    <div class="row gy-4">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="sspay_category_code"
                                                                    class="col-form-label">{{ __('Category Code') }}</label>
                                                                <input type="text"
                                                                    name="sspay_category_code"
                                                                    id="sspay_category_code"
                                                                    class="form-control"
                                                                    value="{{ !isset($company_payment_setting['sspay_category_code']) || is_null($company_payment_setting['sspay_category_code']) ? '' : $company_payment_setting['sspay_category_code'] }}"
                                                                    placeholder="{{ __('Category code') }}">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="sspay_secret_key"
                                                                    class="col-form-label">{{ __('Secret Key') }}</label>
                                                                <input type="text"
                                                                    name="sspay_secret_key"
                                                                    id="sspay_secret_key"
                                                                    class="form-control"
                                                                    value="{{ !isset($company_payment_setting['sspay_secret_key']) || is_null($company_payment_setting['sspay_secret_key']) ? '' : $company_payment_setting['sspay_secret_key'] }}"
                                                                    placeholder="{{ __('Secret Key') }}">
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Paytab -->
                                    <div class="accordion accordion-flush setting-accordion"
                                        id="accordionExample">
                                        <div class="accordion-item">
                                            <h2 class="accordion-header" id="headingFourteen">
                                                <button class="accordion-button collapsed"
                                                    type="button" data-bs-toggle="collapse"
                                                    data-bs-target="#collapse16" aria-expanded="false"
                                                    aria-controls="collapse16">
                                                    <span class="d-flex align-items-center">
                                                        {{ __('Paytab') }}
                                                    </span>
                                                    <div class="d-flex align-items-center">
                                                        <span class="me-2">{{ __('Enable:') }}</span>
                                                        <div
                                                            class="form-check form-switch d-inline-block custom-switch-v1">
                                                            <input type="hidden"
                                                                name="is_paytab_enabled" value="off">
                                                            <input type="checkbox"
                                                                class="form-check-input"
                                                                name="is_paytab_enabled"
                                                                id="is_paytab_enabled"
                                                                {{ isset($company_payment_setting['is_paytab_enabled']) && $company_payment_setting['is_paytab_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                            <label class="custom-control-label form-label"
                                                                for="is_paytab_enabled"></label>
                                                        </div>
                                                    </div>
                                                </button>
                                            </h2>
                                            <div id="collapse16" class="accordion-collapse collapse"
                                                aria-labelledby="headingFourteen"
                                                data-bs-parent="#accordionExample">
                                                <div class="accordion-body">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="paytab_profile_id"
                                                                    class="col-form-label">{{ __('Profile Id') }}</label>
                                                                <input type="text"
                                                                    name="paytab_profile_id"
                                                                    id="paytab_profile_id"
                                                                    class="form-control"
                                                                    value="{{ isset($company_payment_setting['paytab_profile_id']) ? $company_payment_setting['paytab_profile_id'] : '' }}"
                                                                    placeholder="{{ __('Profile Id') }}">
                                                            </div>
                                                            @if ($errors->has('paytab_profile_id'))
                                                            <span class="invalid-feedback d-block">
                                                                {{ $errors->first('paytab_profile_id') }}
                                                            </span>
                                                            @endif
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="paytab_server_key"
                                                                    class="col-form-label">{{ __('Server Key') }}</label>
                                                                <input type="text"
                                                                    name="paytab_server_key"
                                                                    id="paytab_server_key"
                                                                    class="form-control"
                                                                    value="{{ isset($company_payment_setting['paytab_server_key']) ? $company_payment_setting['paytab_server_key'] : '' }}"
                                                                    placeholder="{{ __('Sspay Secret') }}">
                                                            </div>
                                                            @if ($errors->has('paytab_server_key'))
                                                            <span class="invalid-feedback d-block">
                                                                {{ $errors->first('paytab_server_key') }}
                                                            </span>
                                                            @endif
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="paytab_region"
                                                                    class="form-label">{{ __('Region') }}</label>
                                                                <input type="text"
                                                                    name="paytab_region"
                                                                    id="paytab_region"
                                                                    class="form-control form-control-label"
                                                                    value="{{ isset($company_payment_setting['paytab_region']) ? $company_payment_setting['paytab_region'] : '' }}"
                                                                    placeholder="{{ __('Region') }}" /><br>
                                                                @if ($errors->has('paytab_region'))
                                                                <span
                                                                    class="invalid-feedback d-block">
                                                                    {{ $errors->first('paytab_region') }}
                                                                </span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Benefit -->
                                    <div class="accordion accordion-flush setting-accordion"
                                        id="accordionExample">
                                        <div class="accordion-item">
                                            <h2 class="accordion-header" id="headingFourteen">
                                                <button class="accordion-button collapsed"
                                                    type="button" data-bs-toggle="collapse"
                                                    data-bs-target="#collapse17" aria-expanded="false"
                                                    aria-controls="collapse17">
                                                    <span class="d-flex align-items-center">
                                                        {{ __('Benefit') }}
                                                    </span>
                                                    <div class="d-flex align-items-center">
                                                        <span class="me-2">{{ __('Enable:') }}</span>
                                                        <div
                                                            class="form-check form-switch custom-switch-v1">
                                                            <input type="hidden"
                                                                name="is_benefit_enabled"
                                                                value="off">
                                                            <input type="checkbox"
                                                                class="form-check-input input-primary"
                                                                name="is_benefit_enabled"
                                                                id="is_benefit_enabled"
                                                                {{ isset($company_payment_setting['is_benefit_enabled']) && $company_payment_setting['is_benefit_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                            <label class="form-check-label"
                                                                for="is_benefit_enabled"></label>
                                                        </div>
                                                    </div>
                                                </button>
                                            </h2>
                                            <div id="collapse17" class="accordion-collapse collapse"
                                                aria-labelledby="headingFourteen"
                                                data-bs-parent="#accordionExample">
                                                <div class="accordion-body">
                                                    <div class="row gy-4">

                                                        <div class="col-lg-6">
                                                            <div class="form-group">
                                                                {{ Form::label('benefit_api_key', __('Benefit Key'), ['class' => 'col-form-label']) }}
                                                                {{ Form::text('benefit_api_key', isset($company_payment_setting['benefit_api_key']) ? $company_payment_setting['benefit_api_key'] : '', ['class' => 'form-control', 'placeholder' => __('Enter Benefit Key')]) }}
                                                                @error('benefit_api_key')
                                                                <span class="invalid-benefit_api_key"
                                                                    role="alert">
                                                                    <strong
                                                                        class="text-danger">{{ $message }}</strong>
                                                                </span>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-6">
                                                            <div class="form-group">
                                                                {{ Form::label('benefit_secret_key', __('Benefit Secret Key'), ['class' => 'col-form-label']) }}
                                                                {{ Form::text('benefit_secret_key', isset($company_payment_setting['benefit_secret_key']) ? $company_payment_setting['benefit_secret_key'] : '', ['class' => 'form-control ', 'placeholder' => __('Enter Benefit Secret key')]) }}
                                                                @error('benefit_secret_key')
                                                                <span class="invalid-benefit_secret_key"
                                                                    role="alert">
                                                                    <strong
                                                                        class="text-danger">{{ $message }}</strong>
                                                                </span>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Cashfree -->
                                    <div class="accordion accordion-flush setting-accordion"
                                        id="accordionExample">
                                        <div class="accordion-item">
                                            <h2 class="accordion-header" id="headingFourteen">
                                                <button class="accordion-button collapsed"
                                                    type="button" data-bs-toggle="collapse"
                                                    data-bs-target="#collapse18" aria-expanded="false"
                                                    aria-controls="collapse18">
                                                    <span class="d-flex align-items-center">
                                                        {{ __('Cashfree') }}
                                                    </span>
                                                    <div class="d-flex align-items-center">
                                                        <span class="me-2">{{ __('Enable:') }}</span>
                                                        <div
                                                            class="form-check form-switch custom-switch-v1">
                                                            <input type="hidden"
                                                                name="is_cashfree_enabled"
                                                                value="off">
                                                            <input type="checkbox"
                                                                class="form-check-input input-primary"
                                                                name="is_cashfree_enabled"
                                                                id="is_cashfree_enabled"
                                                                {{ isset($company_payment_setting['is_cashfree_enabled']) && $company_payment_setting['is_cashfree_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                            <label class="form-check-label"
                                                                for="is_cashfree_enabled"></label>
                                                        </div>
                                                    </div>
                                                </button>
                                            </h2>
                                            <div id="collapse18" class="accordion-collapse collapse"
                                                aria-labelledby="headingFourteen"
                                                data-bs-parent="#accordionExample">
                                                <div class="accordion-body">
                                                    <div class="row gy-4">
                                                        <div class="col-lg-6">
                                                            <div class="form-group">
                                                                {{ Form::label('cashfree_api_key', __('Cashfree Key'), ['class' => 'col-form-label']) }}
                                                                {{ Form::text('cashfree_api_key', isset($company_payment_setting['cashfree_api_key']) ? $company_payment_setting['cashfree_api_key'] : '', ['class' => 'form-control', 'placeholder' => __('Enter Cashfree Key')]) }}
                                                                @error('cashfree_api_key')
                                                                <span class="invalid-cashfree_api_key"
                                                                    role="alert">
                                                                    <strong
                                                                        class="text-danger">{{ $message }}</strong>
                                                                </span>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-6">
                                                            <div class="form-group">
                                                                {{ Form::label('cashfree_secret_key', __('Cashfree Secret Key'), ['class' => 'col-form-label']) }}
                                                                {{ Form::text('cashfree_secret_key', isset($company_payment_setting['cashfree_secret_key']) ? $company_payment_setting['cashfree_secret_key'] : '', ['class' => 'form-control ', 'placeholder' => __('Enter Cashfree Secret key')]) }}
                                                                @error('cashfree_secret_key')
                                                                <span class="invalid-cashfree_secret_key"
                                                                    role="alert">
                                                                    <strong
                                                                        class="text-danger">{{ $message }}</strong>
                                                                </span>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- aamarpay -->
                                    <div class="accordion accordion-flush setting-accordion"
                                        id="accordionExample">
                                        <div class="accordion-item">
                                            <h2 class="accordion-header" id="headingFourteen">
                                                <button class="accordion-button collapsed"
                                                    type="button" data-bs-toggle="collapse"
                                                    data-bs-target="#collapse19" aria-expanded="false"
                                                    aria-controls="collapse19">
                                                    <span class="d-flex align-items-center">
                                                        {{ __('Aamarpay') }}
                                                    </span>
                                                    <div class="d-flex align-items-center">
                                                        <span class="me-2">{{ __('Enable:') }}</span>
                                                        <div
                                                            class="form-check form-switch custom-switch-v1">
                                                            <input type="hidden"
                                                                name="is_aamarpay_enabled"
                                                                value="off">
                                                            <input type="checkbox"
                                                                class="form-check-input input-primary"
                                                                name="is_aamarpay_enabled"
                                                                id="is_aamarpay_enabled"
                                                                {{ isset($company_payment_setting['is_aamarpay_enabled']) && $company_payment_setting['is_aamarpay_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                        </div>
                                                    </div>
                                                </button>
                                            </h2>
                                            <div id="collapse19" class="accordion-collapse collapse"
                                                aria-labelledby="headingFourteen"
                                                data-bs-parent="#accordionExample">
                                                <div class="accordion-body">
                                                    <div class="row pt-2">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                {{ Form::label('aamarpay_store_id', __('Store Id'), ['class' => 'form-label']) }}
                                                                {{ Form::text('aamarpay_store_id', isset($company_payment_setting['aamarpay_store_id']) ? $company_payment_setting['aamarpay_store_id'] : '', ['class' => 'form-control', 'placeholder' => __('Store Id')]) }}<br>
                                                                @if ($errors->has('aamarpay_store_id'))
                                                                <span
                                                                    class="invalid-feedback d-block">
                                                                    {{ $errors->first('aamarpay_store_id') }}
                                                                </span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                {{ Form::label('aamarpay_signature_key', __('Signature Key'), ['class' => 'form-label']) }}
                                                                {{ Form::text('aamarpay_signature_key', isset($company_payment_setting['aamarpay_signature_key']) ? $company_payment_setting['aamarpay_signature_key'] : '', ['class' => 'form-control', 'placeholder' => __('Signature Key')]) }}<br>
                                                                @if ($errors->has('aamarpay_signature_key'))
                                                                <span
                                                                    class="invalid-feedback d-block">
                                                                    {{ $errors->first('aamarpay_signature_key') }}
                                                                </span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                {{ Form::label('aamarpay_description', __('Description'), ['class' => 'form-label']) }}
                                                                {{ Form::text('aamarpay_description', isset($company_payment_setting['aamarpay_description']) ? $company_payment_setting['aamarpay_description'] : '', ['class' => 'form-control', 'placeholder' => __('Description')]) }}<br>
                                                                @if ($errors->has('aamarpay_description'))
                                                                <span
                                                                    class="invalid-feedback d-block">
                                                                    {{ $errors->first('aamarpay_description') }}
                                                                </span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- PayTR -->
                                    <div class="accordion accordion-flush setting-accordion"
                                        id="accordionExample">
                                        <div class="accordion-item">
                                            <h2 class="accordion-header" id="headingFourteen">
                                                <button class="accordion-button collapsed"
                                                    type="button" data-bs-toggle="collapse"
                                                    data-bs-target="#collapse20" aria-expanded="false"
                                                    aria-controls="collapse20">
                                                    <span class="d-flex align-items-center">
                                                        {{ __('PayTR') }}
                                                    </span>
                                                    <div class="d-flex align-items-center">
                                                        <span class="me-2">{{ __('Enable:') }}</span>
                                                        <div
                                                            class="form-check form-switch custom-switch-v1">
                                                            <input type="hidden"
                                                                name="is_paytr_enabled" value="off">
                                                            <input type="checkbox"
                                                                class="form-check-input input-primary"
                                                                name="is_paytr_enabled"
                                                                id="is_paytr_enabled"
                                                                {{ isset($company_payment_setting['is_paytr_enabled']) && $company_payment_setting['is_paytr_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                        </div>
                                                    </div>
                                                </button>
                                            </h2>
                                            <div id="collapse20" class="accordion-collapse collapse"
                                                aria-labelledby="headingFourteen"
                                                data-bs-parent="#accordionExample">
                                                <div class="accordion-body">
                                                    <div class="row pt-2">
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                {{ Form::label('paytr_merchant_id', __('Merchant Id'), ['class' => 'form-label']) }}
                                                                {{ Form::text('paytr_merchant_id', isset($company_payment_setting['paytr_merchant_id']) ? $company_payment_setting['paytr_merchant_id'] : '', ['class' => 'form-control', 'placeholder' => __('Merchant Id')]) }}<br>
                                                                @if ($errors->has('paytr_merchant_id'))
                                                                <span
                                                                    class="invalid-feedback d-block">
                                                                    {{ $errors->first('paytr_merchant_id') }}
                                                                </span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                {{ Form::label('paytr_merchant_key', __('Merchant Key'), ['class' => 'form-label']) }}
                                                                {{ Form::text('paytr_merchant_key', isset($company_payment_setting['paytr_merchant_key']) ? $company_payment_setting['paytr_merchant_key'] : '', ['class' => 'form-control', 'placeholder' => __('Merchant Key')]) }}<br>
                                                                @if ($errors->has('paytr_merchant_key'))
                                                                <span
                                                                    class="invalid-feedback d-block">
                                                                    {{ $errors->first('paytr_merchant_key') }}
                                                                </span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                {{ Form::label('paytr_merchant_salt', __('Merchant Salt'), ['class' => 'form-label']) }}
                                                                {{ Form::text('paytr_merchant_salt', isset($company_payment_setting['paytr_merchant_salt']) ? $company_payment_setting['paytr_merchant_salt'] : '', ['class' => 'form-control', 'placeholder' => __('Merchant Salt')]) }}<br>
                                                                @if ($errors->has('paytr_merchant_salt'))
                                                                <span
                                                                    class="invalid-feedback d-block">
                                                                    {{ $errors->first('paytr_merchant_salt') }}
                                                                </span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- YooKassa -->
                                    <div class="accordion accordion-flush setting-accordion"
                                        id="accordionExample">
                                        <div class="accordion-item">
                                            <h2 class="accordion-header" id="headingFifteen">
                                                <button class="accordion-button collapsed"
                                                    type="button" data-bs-toggle="collapse"
                                                    data-bs-target="#collapse21" aria-expanded="false"
                                                    aria-controls="collapse21">
                                                    <span class="d-flex align-items-center">
                                                        {{ __('YooKassa') }}
                                                    </span>
                                                    <div class="d-flex align-items-center">
                                                        <span class="me-2">{{ __('Enable:') }}</span>
                                                        <div
                                                            class="form-check form-switch custom-switch-v1">
                                                            <input type="hidden"
                                                                name="is_yookassa_enabled"
                                                                value="off">
                                                            <input type="checkbox"
                                                                class="form-check-input input-primary"
                                                                name="is_yookassa_enabled"
                                                                id="is_yookassa_enabled"
                                                                {{ isset($company_payment_setting['is_yookassa_enabled']) && $company_payment_setting['is_yookassa_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                        </div>
                                                    </div>
                                                </button>
                                            </h2>
                                            <div id="collapse21" class="accordion-collapse collapse"
                                                aria-labelledby="headingFifteen"
                                                data-bs-parent="#accordionExample">
                                                <div class="accordion-body">
                                                    <div class="row pt-2">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                {{ Form::label('yookassa_shop_id', __('Shop id'), ['class' => 'form-label']) }}
                                                                {{ Form::text('yookassa_shop_id', isset($company_payment_setting['yookassa_shop_id']) ? $company_payment_setting['yookassa_shop_id'] : '', ['class' => 'form-control', 'placeholder' => __('Shop id')]) }}<br>
                                                                @if ($errors->has('yookassa_shop_id'))
                                                                <span
                                                                    class="invalid-feedback d-block">
                                                                    {{ $errors->first('yookassa_shop_id') }}
                                                                </span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                {{ Form::label('yookassa_secret', __('Merchant Key'), ['class' => 'form-label']) }}
                                                                {{ Form::text('yookassa_secret', isset($company_payment_setting['yookassa_secret']) ? $company_payment_setting['yookassa_secret'] : '', ['class' => 'form-control', 'placeholder' => __('Merchant Key')]) }}<br>
                                                                @if ($errors->has('yookassa_secret'))
                                                                <span
                                                                    class="invalid-feedback d-block">
                                                                    {{ $errors->first('yookassa_secret') }}
                                                                </span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Xendit -->
                                    <div class="accordion accordion-flush setting-accordion"
                                        id="accordionExample">
                                        <div class="accordion-item">
                                            <h2 class="accordion-header" id="headingSixteen">
                                                <button class="accordion-button collapsed"
                                                    type="button" data-bs-toggle="collapse"
                                                    data-bs-target="#collapse22" aria-expanded="false"
                                                    aria-controls="collapse22">
                                                    <span class="d-flex align-items-center">
                                                        {{ __('Xendit') }}
                                                    </span>
                                                    <div class="d-flex align-items-center">
                                                        <span class="me-2">{{ __('Enable') }}</span>
                                                        <div
                                                            class="form-check form-switch custom-switch-v1">
                                                            <input type="hidden"
                                                                name="is_xendit_enabled" value="off">
                                                            <input type="checkbox"
                                                                class="form-check-input input-primary"
                                                                name="is_xendit_enabled"
                                                                id="is_xendit_enabled"
                                                                {{ isset($company_payment_setting['is_xendit_enabled']) && $company_payment_setting['is_xendit_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                            <label class="form-check-label"
                                                                for="customswitchv1-2"></label>
                                                        </div>
                                                    </div>
                                                </button>
                                            </h2>
                                            <div id="collapse22" class="accordion-collapse collapse"
                                                aria-labelledby="headingSixteen"
                                                data-bs-parent="#accordionExample">
                                                <div class="accordion-body">
                                                    <div class="row">

                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="xendit_api"
                                                                    class="form-label">{{ __('API Key') }}</label>
                                                                <input type="text" name="xendit_api"
                                                                    id="xendit_api" class="form-control"
                                                                    value="{{ !isset($company_payment_setting['xendit_api']) || is_null($company_payment_setting['xendit_api']) ? '' : $company_payment_setting['xendit_api'] }}"
                                                                    placeholder="{{ __('API Key') }}">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="xendit_token"
                                                                    class="form-label">{{ __('Token') }}</label>
                                                                <input type="text"
                                                                    name="xendit_token"
                                                                    id="xendit_token"
                                                                    class="form-control"
                                                                    value="{{ !isset($company_payment_setting['xendit_token']) || is_null($company_payment_setting['xendit_token']) ? '' : $company_payment_setting['xendit_token'] }}"
                                                                    placeholder="{{ __('Token') }}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Midtrans -->
                                    <div class="accordion accordion-flush setting-accordion"
                                        id="accordionExample">
                                        <div class="accordion-item">
                                            <h2 class="accordion-header" id="headingSeventeen">
                                                <button class="accordion-button collapsed"
                                                    type="button" data-bs-toggle="collapse"
                                                    data-bs-target="#collapse23" aria-expanded="false"
                                                    aria-controls="collapse23">
                                                    <span class="d-flex align-items-center">
                                                        {{ __('Midtrans') }}
                                                    </span>
                                                    <div class="d-flex align-items-center">
                                                        <span class="me-2">{{ __('Enable') }}</span>
                                                        <div
                                                            class="form-check form-switch custom-switch-v1">
                                                            <input type="hidden"
                                                                name="is_midtrans_enabled"
                                                                value="off">
                                                            <input type="checkbox"
                                                                class="form-check-input input-primary"
                                                                name="is_midtrans_enabled"
                                                                id="is_midtrans_enabled"
                                                                {{ isset($company_payment_setting['is_midtrans_enabled']) && $company_payment_setting['is_midtrans_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                            <label class="form-check-label"
                                                                for="customswitchv1-2"></label>
                                                        </div>
                                                    </div>
                                                </button>
                                            </h2>
                                            <div id="collapse23" class="accordion-collapse collapse"
                                                aria-labelledby="headingSeventeen"
                                                data-bs-parent="#accordionExample">
                                                <div class="accordion-body">
                                                    <label class="paypal-label col-form-label"
                                                        for="midtrans_mode">{{ __('Midtrans Mode') }}</label>
                                                    <br>

                                                    <div class="d-flex">
                                                        <div class="mr-2" style="margin-right: 15px;">
                                                            <div class="border card p-3">
                                                                <div class="form-check">
                                                                    <label
                                                                        class="form-check-labe text-dark {{ isset($company_payment_setting['midtrans_mode']) && $company_payment_setting['midtrans_mode'] == 'sandbox' ? 'active' : '' }}">
                                                                        <input type="radio"
                                                                            name="midtrans_mode"
                                                                            value="sandbox"
                                                                            class="form-check-input"
                                                                            {{ isset($company_payment_setting['midtrans_mode']) && $company_payment_setting['midtrans_mode'] == 'sandbox' ? 'checked="checked"' : '' }}>

                                                                        {{ __('Sandbox') }}
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="mr-2">
                                                            <div class="border card p-3">
                                                                <div class="form-check">
                                                                    <label
                                                                        class="form-check-labe text-dark">
                                                                        <input type="radio"
                                                                            name="midtrans_mode"
                                                                            value="live"
                                                                            class="form-check-input"
                                                                            {{ isset($company_payment_setting['midtrans_mode']) && $company_payment_setting['midtrans_mode'] == 'live' ? 'checked="checked"' : '' }}>

                                                                        {{ __('Live') }}
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="midtrans_secret"
                                                                    class="form-label">{{ __('Secret Key') }}</label>
                                                                <input type="text"
                                                                    name="midtrans_secret"
                                                                    id="midtrans_secret"
                                                                    class="form-control"
                                                                    value="{{ !isset($company_payment_setting['midtrans_secret']) || is_null($company_payment_setting['midtrans_secret']) ? '' : $company_payment_setting['midtrans_secret'] }}"
                                                                    placeholder="{{ __('Secret Key') }}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Paiementpro --}}
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="heading-paiementpro">
                                            <button class="accordion-button" type="button"
                                                data-bs-toggle="collapse"
                                                data-bs-target="#collapse-paiementpro"
                                                aria-expanded="true"
                                                aria-controls="collapse-paiementpro">
                                                <span
                                                    class="d-flex align-items-center">{{ __('Paiementpro') }}</span>
                                                <div class="d-flex align-items-center">
                                                    <span class="me-2">{{ __('On/Off') }}:</span>
                                                    <div class="form-check form-switch custom-switch-v1">
                                                        <input type="hidden"
                                                            name="is_paiementpro_enabled"
                                                            value="off">
                                                        <input type="checkbox" class="form-check-input"
                                                            name="is_paiementpro_enabled"
                                                            id="is_paiementpro_enabled"
                                                            {{ isset($company_payment_setting['is_paiementpro_enabled']) && $company_payment_setting['is_paiementpro_enabled'] == 'on' ? 'checked' : '' }}>
                                                        <label
                                                            class="custom-control-label form-control-label"
                                                            for="is_paiementpro_enabled"></label>
                                                    </div>
                                                </div>
                                            </button>
                                        </h2>
                                        <div id="collapse-paiementpro"
                                            class="accordion-collapse collapse"
                                            aria-labelledby="heading-paiementpro"
                                            data-bs-parent="#accordionExample">
                                            <div class="accordion-body">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="paiementpro_merchant_id"
                                                                class="col-form-label">{{ __('Merchant ID') }}</label>
                                                            <input type="text"
                                                                name="paiementpro_merchant_id"
                                                                id="paiementpro_merchant_id"
                                                                class="form-control"
                                                                value="{{ !isset($company_payment_setting['paiementpro_merchant_id']) || is_null($company_payment_setting['paiementpro_merchant_id']) ? '' : $company_payment_setting['paiementpro_merchant_id'] }}"
                                                                placeholder="{{ __('Merchant ID') }}">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Nepalste --}}
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="heading-nepalste">
                                            <button class="accordion-button" type="button"
                                                data-bs-toggle="collapse"
                                                data-bs-target="#collapse-nepalste" aria-expanded="true"
                                                aria-controls="collapse-nepalste">
                                                <span
                                                    class="d-flex align-items-center">{{ __('Nepalste') }}</span>
                                                <div class="d-flex align-items-center">
                                                    <span class="me-2">{{ __('On/Off') }}:</span>
                                                    <div class="form-check form-switch custom-switch-v1">
                                                        <input type="hidden" name="is_nepalste_enabled"
                                                            value="off">
                                                        <input type="checkbox" class="form-check-input"
                                                            name="is_nepalste_enabled"
                                                            id="is_nepalste_enabled"
                                                            {{ isset($company_payment_setting['is_nepalste_enabled']) && $company_payment_setting['is_nepalste_enabled'] == 'on' ? 'checked' : '' }}>
                                                        <label
                                                            class="custom-control-label form-control-label"
                                                            for="is_nepalste_enabled"></label>
                                                    </div>
                                                </div>
                                            </button>
                                        </h2>
                                        <div id="collapse-nepalste" class="accordion-collapse collapse"
                                            aria-labelledby="heading-nepalste"
                                            data-bs-parent="#accordionExample">
                                            <div class="accordion-body">
                                                <div class="row">
                                                    <div class="col-md-12 pb-4 form-group">
                                                        <label class="nepalste-label form-label"
                                                            for="nepalste_mode">{{ __('Nepalste Mode') }}</label>
                                                        <br>
                                                        <div class="d-flex">
                                                            <div class="col-lg-3"
                                                                style="margin-right: 15px;">
                                                                <div class="border accordion-header p-3">
                                                                    <div class="form-check">
                                                                        <label
                                                                            class="form-check-label text-dark">
                                                                            <input type="radio"
                                                                                name="nepalste_mode"
                                                                                value="sandbox"
                                                                                class="form-check-input"
                                                                                {{ !isset($company_payment_setting['nepalste_mode']) || $company_payment_setting['nepalste_mode'] == '' || $company_payment_setting['nepalste_mode'] == 'sandbox' ? 'checked="checked"' : '' }}>
                                                                            {{ __('Sandbox') }}
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-3">
                                                                <div class="border accordion-header p-3">
                                                                    <div class="form-check">
                                                                        <label
                                                                            class="form-check-label text-dark">
                                                                            <input type="radio"
                                                                                name="nepalste_mode"
                                                                                value="live"
                                                                                class="form-check-input"
                                                                                {{ isset($company_payment_setting['nepalste_mode']) && $company_payment_setting['nepalste_mode'] == 'live' ? 'checked="checked"' : '' }}>
                                                                            {{ __('Live') }}
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="nepalste_public_key"
                                                                class="col-form-label">{{ __('Nepalste Public Key') }}</label>
                                                            <input type="text"
                                                                name="nepalste_public_key"
                                                                id="nepalste_public_key"
                                                                class="form-control"
                                                                value="{{ !isset($company_payment_setting['nepalste_public_key']) || is_null($company_payment_setting['nepalste_public_key']) ? '' : $company_payment_setting['nepalste_public_key'] }}"
                                                                placeholder="{{ __('Nepalste Public Key') }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="nepalste_secret_key"
                                                                class="col-form-label">{{ __('Nepalste Secret Key') }}</label>
                                                            <input type="text"
                                                                name="nepalste_secret_key"
                                                                id="nepalste_secret_key"
                                                                class="form-control"
                                                                value="{{ !isset($company_payment_setting['nepalste_secret_key']) || is_null($company_payment_setting['nepalste_secret_key']) ? '' : $company_payment_setting['nepalste_secret_key'] }}"
                                                                placeholder="{{ __('Nepalste Secret Key') }}">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Cinetpay --}}
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="heading-cinetpay">
                                            <button class="accordion-button" type="button"
                                                data-bs-toggle="collapse"
                                                data-bs-target="#collapse-cinetpay" aria-expanded="true"
                                                aria-controls="collapse-cinetpay">
                                                <span
                                                    class="d-flex align-items-center">{{ __('Cinetpay') }}</span>
                                                <div class="d-flex align-items-center">
                                                    <span class="me-2">{{ __('On/Off') }}:</span>
                                                    <div class="form-check form-switch custom-switch-v1">
                                                        <input type="hidden" name="is_cinetpay_enabled"
                                                            value="off">
                                                        <input type="checkbox" class="form-check-input"
                                                            name="is_cinetpay_enabled"
                                                            id="is_cinetpay_enabled"
                                                            {{ isset($company_payment_setting['is_cinetpay_enabled']) && $company_payment_setting['is_cinetpay_enabled'] == 'on' ? 'checked' : '' }}>
                                                        <label
                                                            class="custom-control-label form-control-label"
                                                            for="is_cinetpay_enabled"></label>
                                                    </div>
                                                </div>
                                            </button>
                                        </h2>
                                        <div id="collapse-cinetpay" class="accordion-collapse collapse"
                                            aria-labelledby="heading-cinetpay"
                                            data-bs-parent="#accordionExample">
                                            <div class="accordion-body">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="cinetpay_api_key"
                                                                class="col-form-label">{{ __('Cinetpay Api Key') }}</label>
                                                            <input type="text"
                                                                name="cinetpay_api_key"
                                                                id="cinetpay_api_key"
                                                                class="form-control"
                                                                value="{{ !isset($company_payment_setting['cinetpay_api_key']) || is_null($company_payment_setting['cinetpay_api_key']) ? '' : $company_payment_setting['cinetpay_api_key'] }}"
                                                                placeholder="{{ __('Cinetpay Api Key') }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="cinetpay_site_id"
                                                                class="col-form-label">{{ __('Cinetpay Site Id') }}</label>
                                                            <input type="text"
                                                                name="cinetpay_site_id"
                                                                id="cinetpay_site_id"
                                                                class="form-control"
                                                                value="{{ !isset($company_payment_setting['cinetpay_site_id']) || is_null($company_payment_setting['cinetpay_site_id']) ? '' : $company_payment_setting['cinetpay_site_id'] }}"
                                                                placeholder="{{ __('Cinetpay Site Id') }}">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="cinetpay_api_key"
                                                                class="col-form-label">{{ __('Cinetpay Secret Key') }}</label>
                                                            <input type="text"
                                                                name="cinetpay_secret_key"
                                                                id="cinetpay_secret_key"
                                                                class="form-control"
                                                                value="{{ !isset($company_payment_setting['cinetpay_secret_key']) || is_null($company_payment_setting['cinetpay_secret_key']) ? '' : $company_payment_setting['cinetpay_secret_key'] }}"
                                                                placeholder="{{ __('Cinetpay Secret Key') }}">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Fedapay --}}
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="heading-fedapay">
                                            <button class="accordion-button" type="button"
                                                data-bs-toggle="collapse"
                                                data-bs-target="#collapse-fedapay" aria-expanded="true"
                                                aria-controls="collapse-fedapay">
                                                <span
                                                    class="d-flex align-items-center">{{ __('Fedapay') }}</span>
                                                <div class="d-flex align-items-center">
                                                    <span class="me-2">{{ __('On/Off') }}:</span>
                                                    <div class="form-check form-switch custom-switch-v1">
                                                        <input type="hidden" name="is_fedapay_enabled"
                                                            value="off">
                                                        <input type="checkbox" class="form-check-input"
                                                            name="is_fedapay_enabled"
                                                            id="is_fedapay_enabled"
                                                            {{ isset($company_payment_setting['is_fedapay_enabled']) && $company_payment_setting['is_fedapay_enabled'] == 'on' ? 'checked' : '' }}>
                                                        <label
                                                            class="custom-control-label form-control-label"
                                                            for="is_fedapay_enabled"></label>
                                                    </div>
                                                </div>
                                            </button>
                                        </h2>
                                        <div id="collapse-fedapay" class="accordion-collapse collapse"
                                            aria-labelledby="heading-fedapay"
                                            data-bs-parent="#accordionExample">
                                            <div class="accordion-body">
                                                <div class="row">
                                                    <div class="col-md-12 pb-4 form-group">
                                                        <label class="fedapay-label form-label"
                                                            for="fedapay_mode">{{ __('Fedapay Mode') }}</label>
                                                        <br>
                                                        <div class="d-flex">
                                                            <div class="col-lg-3"
                                                                style="margin-right: 15px;">
                                                                <div class="border accordion-header p-3">
                                                                    <div class="form-check">
                                                                        <label
                                                                            class="form-check-label text-dark">
                                                                            <input type="radio"
                                                                                name="fedapay_mode"
                                                                                value="sandbox"
                                                                                class="form-check-input"
                                                                                {{ !isset($company_payment_setting['fedapay_mode']) || $company_payment_setting['fedapay_mode'] == '' || $company_payment_setting['fedapay_mode'] == 'sandbox' ? 'checked="checked"' : '' }}>
                                                                            {{ __('Sandbox') }}
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-3">
                                                                <div class="border accordion-header p-3">
                                                                    <div class="form-check">
                                                                        <label
                                                                            class="form-check-label text-dark">
                                                                            <input type="radio"
                                                                                name="fedapay_mode"
                                                                                value="live"
                                                                                class="form-check-input"
                                                                                {{ isset($company_payment_setting['fedapay_mode']) && $company_payment_setting['fedapay_mode'] == 'live' ? 'checked="checked"' : '' }}>
                                                                            {{ __('Live') }}
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="fedapay_public_key"
                                                                class="col-form-label">{{ __('Public Key') }}</label>
                                                            <input type="text"
                                                                name="fedapay_public_key"
                                                                id="fedapay_public_key"
                                                                class="form-control"
                                                                value="{{ !isset($company_payment_setting['fedapay_public_key']) || is_null($company_payment_setting['fedapay_public_key']) ? '' : $company_payment_setting['fedapay_public_key'] }}"
                                                                placeholder="{{ __('Public Key') }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="fedapay_secret_key"
                                                                class="col-form-label">{{ __('Secret Key') }}</label>
                                                            <input type="text"
                                                                name="fedapay_secret_key"
                                                                id="fedapay_secret_key"
                                                                class="form-control"
                                                                value="{{ !isset($company_payment_setting['fedapay_secret_key']) || is_null($company_payment_setting['fedapay_secret_key']) ? '' : $company_payment_setting['fedapay_secret_key'] }}"
                                                                placeholder="{{ __('Secret Key') }}">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- PayHere --}}
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="heading-2-3">
                                            <button class="accordion-button" type="button"
                                                data-bs-toggle="collapse" data-bs-target="#collapse18"
                                                aria-expanded="true" aria-controls="collapse18">
                                                <span
                                                    class="d-flex align-items-center">{{ __('PayHere') }}</span>
                                                <div class="d-flex align-items-center">
                                                    <span class="me-2">{{ __('On/Off') }}:</span>
                                                    <div class="form-check form-switch custom-switch-v1">
                                                        <input type="hidden" name="is_payhere_enabled"
                                                            value="off">
                                                        <input type="checkbox" class="form-check-input"
                                                            name="is_payhere_enabled"
                                                            id="is_payhere_enabled"
                                                            {{ isset($company_payment_setting['is_payhere_enabled']) && $company_payment_setting['is_payhere_enabled'] == 'on' ? 'checked' : '' }}>
                                                        <label
                                                            class="custom-control-label form-control-label"
                                                            for="is_payhere_enabled"></label>
                                                    </div>
                                                </div>
                                            </button>
                                        </h2>
                                        <div id="collapse18" class="accordion-collapse collapse"
                                            aria-labelledby="heading-2-3"
                                            data-bs-parent="#accordionExample">
                                            <div class="accordion-body">
                                                <div class="row">
                                                    <div class="col-md-12 pb-4">
                                                        <label class="payhere-label col-form-label"
                                                            for="payhere_mode">{{ __('PayHere Mode') }}</label>
                                                        <br>
                                                        <div class="d-flex">
                                                            <div class="col-lg-3"
                                                                style="margin-right: 15px;">
                                                                <div class="border accordion-header p-3">
                                                                    <div class="form-check">
                                                                        <label
                                                                            class="form-check-label text-dark">
                                                                            <input type="radio"
                                                                                name="payhere_mode"
                                                                                value="sandbox"
                                                                                class="form-check-input"
                                                                                {{ !isset($company_payment_setting['payhere_mode']) || $company_payment_setting['payhere_mode'] == '' || $company_payment_setting['payhere_mode'] == 'sandbox' ? 'checked="checked"' : '' }}>
                                                                            {{ __('Sandbox') }}
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-3">
                                                                <div class="border accordion-header p-3">
                                                                    <div class="form-check">
                                                                        <label
                                                                            class="form-check-label text-dark">
                                                                            <input type="radio"
                                                                                name="payhere_mode"
                                                                                value="live"
                                                                                class="form-check-input"
                                                                                {{ isset($company_payment_setting['payhere_mode']) && $company_payment_setting['payhere_mode'] == 'live' ? 'checked="checked"' : '' }}>
                                                                            {{ __('Live') }}
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="payhere_merchant_id"
                                                                class="col-form-label">{{ __('Merchant ID') }}</label>
                                                            <input type="text"
                                                                name="payhere_merchant_id"
                                                                id="payhere_merchant_id"
                                                                class="form-control"
                                                                value="{{ !isset($company_payment_setting['payhere_merchant_id']) || is_null($company_payment_setting['payhere_merchant_id']) ? '' : $company_payment_setting['payhere_merchant_id'] }}"
                                                                placeholder="{{ __('Merchant ID') }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="payhere_merchant_secret"
                                                                class="col-form-label">{{ __('Merchant Secret') }}</label>
                                                            <input type="text"
                                                                name="payhere_merchant_secret"
                                                                id="payhere_merchant_secret"
                                                                class="form-control"
                                                                value="{{ !isset($company_payment_setting['payhere_merchant_secret']) || is_null($company_payment_setting['payhere_merchant_secret']) ? '' : $company_payment_setting['payhere_merchant_secret'] }}"
                                                                placeholder="{{ __('Merchant Secret') }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="payhere_app_id"
                                                                class="col-form-label">{{ __('App ID') }}</label>
                                                            <input type="text" name="payhere_app_id"
                                                                id="payhere_app_id" class="form-control"
                                                                value="{{ !isset($company_payment_setting['payhere_app_id']) || is_null($company_payment_setting['payhere_app_id']) ? '' : $company_payment_setting['payhere_app_id'] }}"
                                                                placeholder="{{ __('App ID') }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="payhere_app_secret"
                                                                class="col-form-label">{{ __('App Secret') }}</label>
                                                            <input type="text"
                                                                name="payhere_app_secret"
                                                                id="payhere_app_secret"
                                                                class="form-control"
                                                                value="{{ !isset($company_payment_setting['payhere_app_secret']) || is_null($company_payment_setting['payhere_app_secret']) ? '' : $company_payment_setting['payhere_app_secret'] }}"
                                                                placeholder="{{ __('App Secret') }}">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Tap --}}
                                    <div class="accordion-item card shadow-none">
                                        <h2 class="accordion-header" id="heading-2-30">
                                            <button class="accordion-button collapsed" type="button"
                                                data-bs-toggle="collapse" data-bs-target="#collapse32"
                                                aria-expanded="true" aria-controls="collapse32">
                                                <span class="d-flex align-items-center">
                                                    {{ __('Tap') }}
                                                </span>

                                                <div class="d-flex align-items-center">
                                                    <span class="me-2">{{ __('On/Off: ') }}</span>
                                                    <div class="form-check form-switch custom-switch-v1">
                                                        <input type="hidden" name="is_tap_enabled"
                                                            value="off">
                                                        <input type="checkbox"
                                                            class="form-check-input input-primary"
                                                            name="is_tap_enabled" id="is_tap_enabled"
                                                            {{ isset($company_payment_setting['is_tap_enabled']) && $company_payment_setting['is_tap_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                        <label class="form-check-label"
                                                            for="customswitchv1-2"></label>
                                                    </div>
                                                </div>

                                            </button>
                                        </h2>

                                        <div id="collapse32" class="accordion-collapse collapse"
                                            aria-labelledby="heading-2-30"
                                            data-bs-parent="#accordionExample">
                                            <div class="accordion-body">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-check form-group">
                                                            <label for="company_tap_secret_key"
                                                                class="form-label">{{ __('Secret Key') }}</label>
                                                            <input type="text"
                                                                name="company_tap_secret_key"
                                                                id="company_tap_secret_key"
                                                                class="form-control"
                                                                value="{{ !isset($company_payment_setting['company_tap_secret_key']) || is_null($company_payment_setting['company_tap_secret_key']) ? '' : $company_payment_setting['company_tap_secret_key'] }}"
                                                                placeholder="{{ __('Secret Key') }}">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- AuthorizeNet --}}
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="heading-2-27">
                                            <button class="accordion-button collapsed" type="button"
                                                data-bs-toggle="collapse" data-bs-target="#collapse33"
                                                aria-expanded="true" aria-controls="collapse33">
                                                <span class="d-flex align-items-center">
                                                    {{ __('AuthorizeNet') }}
                                                </span>
                                                <div class="d-flex align-items-center">
                                                    <span class="me-2">{{ __('On/Off:') }}</span>
                                                    <div
                                                        class="form-check form-switch d-inline-block custom-switch-v1">
                                                        <input type="hidden"
                                                            name="is_authorizenet_enabled"
                                                            value="off">
                                                        <input type="checkbox" class="form-check-input"
                                                            name="is_authorizenet_enabled"
                                                            id="is_authorizenet_enabled"
                                                            {{ isset($company_payment_setting['is_authorizenet_enabled']) && $company_payment_setting['is_authorizenet_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                        <label class="custom-control-label form-label"
                                                            for="is_authorizenet_enabled"></label>
                                                    </div>
                                                </div>
                                            </button>
                                        </h2>
                                        <div id="collapse33" class="accordion-collapse collapse"
                                            aria-labelledby="heading-2-27"
                                            data-bs-parent="#accordionExample">
                                            <div class="accordion-body">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <label for="authorizenet_mode"
                                                            class="col-form-label">{{ __('AuthorizeNet Mode') }}</label>
                                                        <div class="d-flex">
                                                            <div class="me-2">
                                                                <div class="border card p-3">
                                                                    <div class="form-check">
                                                                        <label
                                                                            class="form-check-labe text-dark {{ isset($company_payment_setting['authorizenet_mode']) && $company_payment_setting['authorizenet_mode'] == 'sandbox' ? 'active' : '' }}">
                                                                            <input type="radio"
                                                                                name="authorizenet_mode"
                                                                                value="sandbox"
                                                                                class="form-check-input"
                                                                                {{ (isset($company_payment_setting['authorizenet_mode']) && $company_payment_setting['authorizenet_mode'] == '') || (isset($company_payment_setting['authorizenet_mode']) && $company_payment_setting['authorizenet_mode'] == 'sandbox') ? 'checked="checked"' : '' }}>{{ __('Sandbox') }}
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="me-2">
                                                                <div class="border card p-3">
                                                                    <div class="form-check">
                                                                        <label
                                                                            class="form-check-labe text-dark {{ isset($company_payment_setting['authorizenet_mode']) && $company_payment_setting['authorizenet_mode'] == 'live' ? 'active' : '' }}">
                                                                            <input type="radio"
                                                                                name="authorizenet_mode"
                                                                                value="live"
                                                                                class="form-check-input"
                                                                                {{ isset($company_payment_setting['authorizenet_mode']) && $company_payment_setting['authorizenet_mode'] == 'live' ? 'checked="checked"' : '' }}>{{ __('Live') }}
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="authorizenet_merchant_login_id"
                                                                class="col-form-label">{{ __('Merchant Login ID') }}</label>
                                                            <input class="form-control"
                                                                placeholder="Enter Merchant Login ID"
                                                                name="authorizenet_merchant_login_id"
                                                                type="text"
                                                                value="{{ $company_payment_setting['authorizenet_merchant_login_id'] ?? '' }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label
                                                                for="authorizenet_merchant_transaction_key"
                                                                class="col-form-label">{{ __('Merchant Transaction Key') }}</label>
                                                            <input class="form-control"
                                                                placeholder="Enter Merchant Transaction Key"
                                                                name="authorizenet_merchant_transaction_key"
                                                                type="text"
                                                                value="{{ $company_payment_setting['authorizenet_merchant_transaction_key'] ?? '' }}">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Khalti --}}
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="heading-2-30">
                                            <button class="accordion-button collapsed" type="button"
                                                data-bs-toggle="collapse" data-bs-target="#collapse34"
                                                aria-expanded="true" aria-controls="collapse34">
                                                <span class="d-flex align-items-center">
                                                    {{ __('Khalti') }}
                                                </span>

                                                <div class="d-flex align-items-center">
                                                    <span class="me-2">{{ __('On/Off: ') }}</span>
                                                    <div class="form-check form-switch custom-switch-v1">
                                                        <input type="hidden" name="is_khalti_enabled"
                                                            value="off">
                                                        <input type="checkbox"
                                                            class="form-check-input input-primary"
                                                            name="is_khalti_enabled"
                                                            id="is_khalti_enabled"
                                                            {{ isset($company_payment_setting['is_khalti_enabled']) && $company_payment_setting['is_khalti_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                        <label class="form-check-label"
                                                            for="customswitchv1-2"></label>
                                                    </div>
                                                </div>

                                            </button>
                                        </h2>

                                        <div id="collapse34" class="accordion-collapse collapse"
                                            aria-labelledby="heading-2-30"
                                            data-bs-parent="#accordionExample">
                                            <div class="accordion-body">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <label for="khalti_mode"
                                                            class="col-form-label">{{ __('Khalti Mode') }}</label>
                                                        <div class="d-flex">
                                                            <div class="me-2">
                                                                <div class="border card p-3">
                                                                    <div class="form-check">
                                                                        <label
                                                                            class="form-check-labe text-dark {{ isset($company_payment_setting['khalti_mode']) && $company_payment_setting['khalti_mode'] == 'sandbox' ? 'active' : '' }}">
                                                                            <input type="radio"
                                                                                name="khalti_mode"
                                                                                value="sandbox"
                                                                                class="form-check-input"
                                                                                {{ (isset($company_payment_setting['khalti_mode']) && $company_payment_setting['khalti_mode'] == '') || (isset($company_payment_setting['khalti_mode']) && $company_payment_setting['khalti_mode'] == 'sandbox') ? 'checked="checked"' : '' }}>{{ __('Sandbox') }}
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="me-2">
                                                                <div class="border card p-3">
                                                                    <div class="form-check">
                                                                        <label
                                                                            class="form-check-labe text-dark {{ isset($company_payment_setting['khalti_mode']) && $company_payment_setting['khalti_mode'] == 'live' ? 'active' : '' }}">
                                                                            <input type="radio"
                                                                                name="khalti_mode"
                                                                                value="live"
                                                                                class="form-check-input"
                                                                                {{ isset($company_payment_setting['khalti_mode']) && $company_payment_setting['khalti_mode'] == 'live' ? 'checked="checked"' : '' }}>{{ __('Live') }}
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="khalti_secret_key"
                                                                class="col-form-label">{{ __('Secret Key') }}</label>
                                                            <input class="form-control"
                                                                placeholder="Enter Secret Key"
                                                                name="khalti_secret_key" type="text"
                                                                value="{{ $company_payment_setting['khalti_secret_key'] ?? '' }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="khalti_public_key"
                                                                class="col-form-label">{{ __('Public Key') }}</label>
                                                            <input class="form-control"
                                                                placeholder="Enter Public Key"
                                                                name="khalti_public_key" type="text"
                                                                value="{{ $company_payment_setting['khalti_public_key'] ?? '' }}">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Ozow --}}
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="heading-2-35">
                                            <button class="accordion-button collapsed" type="button"
                                                data-bs-toggle="collapse" data-bs-target="#collapse35"
                                                aria-expanded="true" aria-controls="collapse35">
                                                <span class="d-flex align-items-center">
                                                    {{ __('Ozow') }}
                                                </span>

                                                <div class="d-flex align-items-center">
                                                    <span class="me-2">{{ __('On/Off: ') }}</span>
                                                    <div class="form-check form-switch custom-switch-v1">
                                                        <input type="hidden" name="is_ozow_enabled"
                                                            value="off">
                                                        <input type="checkbox"
                                                            class="form-check-input input-primary"
                                                            name="is_ozow_enabled" id="is_ozow_enabled"
                                                            {{ isset($company_payment_setting['is_ozow_enabled']) && $company_payment_setting['is_ozow_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                        <label class="form-check-label"
                                                            for="customswitchv1-2"></label>
                                                    </div>
                                                </div>
                                            </button>
                                        </h2>
                                        <div id="collapse35" class="accordion-collapse collapse"
                                            aria-labelledby="heading-2-35"
                                            data-bs-parent="#accordionExample">
                                            <div class="accordion-body">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <label for="ozow_mode"
                                                            class="col-form-label">{{ __('Ozow Mode') }}</label>
                                                        <div class="d-flex">
                                                            <div class="me-2">
                                                                <div class="border card p-3">
                                                                    <div class="form-check">
                                                                        <label
                                                                            class="form-check-labe text-dark {{ isset($company_payment_setting['ozow_mode']) && $company_payment_setting['ozow_mode'] == 'sandbox' ? 'active' : '' }}">
                                                                            <input type="radio"
                                                                                name="ozow_mode"
                                                                                value="sandbox"
                                                                                class="form-check-input"
                                                                                {{ (isset($company_payment_setting['ozow_mode']) && $company_payment_setting['ozow_mode'] == '') || (isset($company_payment_setting['ozow_mode']) && $company_payment_setting['ozow_mode'] == 'sandbox') ? 'checked="checked"' : '' }}>{{ __('Sandbox') }}
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="me-2">
                                                                <div class="border card p-3">
                                                                    <div class="form-check">
                                                                        <label
                                                                            class="form-check-labe text-dark {{ isset($company_payment_setting['ozow_mode']) && $company_payment_setting['ozow_mode'] == 'live' ? 'active' : '' }}">
                                                                            <input type="radio"
                                                                                name="ozow_mode"
                                                                                value="live"
                                                                                class="form-check-input"
                                                                                {{ isset($company_payment_setting['ozow_mode']) && $company_payment_setting['ozow_mode'] == 'live' ? 'checked="checked"' : '' }}>{{ __('Live') }}
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="ozow_site_key"
                                                                class="col-form-label">{{ __('Ozow Site Key') }}</label>
                                                            <input class="form-control"
                                                                placeholder="Enter Site Key"
                                                                name="ozow_site_key" type="text"
                                                                value="{{ $company_payment_setting['ozow_site_key'] ?? '' }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="ozow_private_key"
                                                                class="col-form-label">{{ __('Ozow Private Key') }}</label>
                                                            <input class="form-control"
                                                                placeholder="Enter Private Key"
                                                                name="ozow_private_key" type="text"
                                                                value="{{ $company_payment_setting['ozow_private_key'] ?? '' }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="ozow_api_key"
                                                                class="col-form-label">{{ __('Ozow Api Key') }}</label>
                                                            <input class="form-control"
                                                                placeholder="Enter Api Key"
                                                                name="ozow_api_key" type="text"
                                                                value="{{ $company_payment_setting['ozow_api_key'] ?? '' }}">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
        <div class="card-footer text-end">
            <input class="btn btn-print-invoice  btn-primary m-r-10" type="submit"
                value="{{ __('Save Changes') }}">
        </div>
        {{ Form::close() }}
    </div>

    <!--Twilio Setting-->
    <div id="useradd-7" class="card d-none">
        <div class="card-header">
            <h5>{{ __('Twilio Settings') }}</h5>
            <small class="text-muted">{{ __('Edit your company twilio setting details') }}</small>
        </div>

        <div class="card-body">
            {{ Form::model($settings, ['route' => 'twilio.settings', 'method' => 'post', 'class' => 'mb-0']) }}
            <div class="row">

                <div class="col-md-4">
                    <div class="form-group">
                        {{ Form::label('twilio_sid', __('Twilio SID '), ['class' => 'form-label']) }}
                        {{ Form::text('twilio_sid', isset($settings['twilio_sid']) ? $settings['twilio_sid'] : '', ['class' => 'form-control w-100', 'placeholder' => __('Enter Twilio SID'), 'required' => 'required']) }}
                        @error('twilio_sid')
                        <span class="invalid-twilio_sid" role="alert">
                            <strong class="text-danger">{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {{ Form::label('twilio_token', __('Twilio Token'), ['class' => 'form-label']) }}
                        {{ Form::text('twilio_token', isset($settings['twilio_token']) ? $settings['twilio_token'] : '', ['class' => 'form-control w-100', 'placeholder' => __('Enter Twilio Token'), 'required' => 'required']) }}
                        @error('twilio_token')
                        <span class="invalid-twilio_token" role="alert">
                            <strong class="text-danger">{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        {{ Form::label('twilio_from', __('Twilio From'), ['class' => 'form-label']) }}
                        {{ Form::text('twilio_from', isset($settings['twilio_from']) ? $settings['twilio_from'] : '', ['class' => 'form-control w-100', 'placeholder' => __('Enter Twilio From'), 'required' => 'required']) }}
                        @error('twilio_from')
                        <span class="invalid-twilio_from" role="alert">
                            <strong class="text-danger">{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                </div>


                <div class="col-md-12 mt-4 mb-2">
                    <h5 class="small-title">{{ __('Module Settings') }}</h5>
                </div>
                <div class="col-md-4 mb-2">
                    <ul class="list-group">
                        <li class="list-group-item">
                            <div class=" form-switch form-switch-right">
                                <span>{{ __('New Customer') }}</span>
                                {{ Form::checkbox('customer_notification', '1', isset($settings['customer_notification']) && $settings['customer_notification'] == '1' ? 'checked' : '', ['class' => 'form-check-input', 'id' => 'customer_notification']) }}
                                <label class="form-check-label" for="customer_notification"></label>
                            </div>

                        </li>
                        <li class="list-group-item">
                            <div class=" form-switch form-switch-right">
                                <span>{{ __('New Vendor') }}</span>
                                {{ Form::checkbox('vender_notification', '1', isset($settings['vender_notification']) && $settings['vender_notification'] == '1' ? 'checked' : '', ['class' => 'form-check-input', 'id' => 'vender_notification']) }}
                                <label class="form-check-label" for="vender_notification"></label>
                            </div>
                        </li>
                    </ul>
                </div>
                <div class="col-md-4 mb-2">
                    <ul class="list-group">
                        <li class="list-group-item">
                            <div class=" form-switch form-switch-right">
                                <span>{{ __('New Invoice') }}</span>
                                {{ Form::checkbox('invoice_notification', '1', isset($settings['invoice_notification']) && $settings['invoice_notification'] == '1' ? 'checked' : '', ['class' => 'form-check-input', 'id' => 'invoice_notification']) }}
                                <label class="form-check-label" for="invoice_notification"></label>
                            </div>
                        </li>

                        <li class="list-group-item">
                            <div class=" form-switch form-switch-right">
                                <span>{{ __('New Revenue') }}</span>
                                {{ Form::checkbox('revenue_notification', '1', isset($settings['revenue_notification']) && $settings['revenue_notification'] == '1' ? 'checked' : '', ['class' => 'form-check-input', 'id' => 'revenue_notification']) }}
                                <label class="form-check-label" for="revenue_notification"></label>
                            </div>
                        </li>
                    </ul>
                </div>
                <div class="col-md-4 mb-2">
                    <ul class="list-group">
                        <li class="list-group-item">
                            <div class=" form-switch form-switch-right">
                                <span>{{ __('New Bill') }}</span>
                                {{ Form::checkbox('bill_notification', '1', isset($settings['bill_notification']) && $settings['bill_notification'] == '1' ? 'checked' : '', ['class' => 'form-check-input', 'id' => 'bill_notification']) }}
                                <label class="form-check-label" for="bill_notification"></label>
                            </div>
                        </li>

                        <li class="list-group-item">
                            <div class=" form-switch form-switch-right">
                                <span>{{ __('New Proposal') }}</span>
                                {{ Form::checkbox('proposal_notification', '1', isset($settings['proposal_notification']) && $settings['proposal_notification'] == '1' ? 'checked' : '', ['class' => 'form-check-input', 'id' => 'proposal_notification']) }}
                                <label class="form-check-label" for="proposal_notification"></label>
                            </div>
                        </li>

                    </ul>
                </div>
                <div class="col-md-4 mb-2">
                    <ul class="list-group">
                        <li class="list-group-item">
                            <div class=" form-switch form-switch-right">
                                <span>{{ __('New Payment') }}</span>
                                {{ Form::checkbox('payment_notification', '1', isset($settings['payment_notification']) && $settings['payment_notification'] == '1' ? 'checked' : '', ['class' => 'form-check-input', 'id' => 'payment_notification']) }}
                                <label class="form-check-label" for="payment_notification"></label>
                            </div>
                        </li>

                        <li class="list-group-item">
                            <div class=" form-switch form-switch-right">
                                <span>{{ __('Invoice Reminder') }}</span>
                                {{ Form::checkbox('reminder_notification', '1', isset($settings['reminder_notification']) && $settings['reminder_notification'] == '1' ? 'checked' : '', ['class' => 'form-check-input', 'id' => 'reminder_notification']) }}
                                <label class="form-check-label" for="reminder_notification"></label>
                            </div>
                        </li>
                    </ul>
                </div>



            </div>
           
        </div>
        <div class="card-footer text-end">   
            <input class="btn btn-print-invoice  btn-primary m-r-10" type="submit"
                value="{{ __('Save Changes') }}">
        </div>
        {{ Form::close() }}

    </div>

    <!--Email Notification Setting-->
    <div id="useradd-9" class="card border-0 rounded-2xl shadow-md overflow-hidden my-3">
        <div class="h-1 w-full" style="background:#007C38;"></div>

        {{ Form::model($settings, ['route' => ['status.email.language'], 'method' => 'post', 'class' => 'mb-0']) }}
        @csrf
        <div class="col-md-12">
            <div class="card-header">
                <div class="row">
                    <div class="col-lg-8 col-md-8 col-sm-8">
                        <h5>{{ __('Email Notification Settings') }}</h5>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <div class="row">
                    @foreach ($EmailTemplates as $EmailTemplate)
                    <div class="col-lg-4 col-md-6 col-sm-6 form-group">
                        <div class="list-group">
                            <div class="list-group-item form-switch form-switch-right">
                                <label class="form-label"
                                    style="margin-left:5%;">{{ $EmailTemplate->name }}</label>
                                <input class="form-check-input" name='{{ $EmailTemplate->id }}'
                                    @if ($EmailTemplate->template) id="email_tempalte_{{ $EmailTemplate->template->id }}"
                                type="checkbox"
                                @if ($EmailTemplate->template->is_active == 1) checked="checked" @endif
                                type="checkbox" value="1"
                                data-url="{{ route('status.email.language', [$EmailTemplate->template->id]) }}"
                                @endif />
                                <label class="form-check-label"
                                    @if ($EmailTemplate->template) for="email_tempalte_{{ $EmailTemplate->template->id }}" @endif></label>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            <div class="card-footer text-end">   
                <input class="btn btn-print-invoice  btn-primary m-r-10 " type="submit"
                    value="{{ __('Save Changes') }}">
            </div>
        </div>
        {{ Form::close() }}
    </div>

    <!--Webhook Setting-->
    <div class="d-none" id="useradd-10">
        <div class="card border-0 rounded-2xl shadow-md overflow-hidden my-3">
            <div class="h-1 w-full" style="background:#007C38;"></div>

            <div class="card-header d-flex justify-content-between">
                <h5>{{ __('Webhook Settings') }}</h5>
               
                <a data-url="{{ route('webhook.create') }}" style="height: 30px;" class="btn btn-sm btn-primary mx-3 align-items-center  d-inline-flex justify-content-center"
                                        data-bs-toggle="tooltip" data-bs-original-title="{{ __('Create') }}"
                                        data-bs-placement="bottom" data-size="md" data-ajax-popup="true"
                                        data-title="{{ __('Create New Webhook') }}">
                                        <i class="ti ti-plus text-white"></i>
                                    </a>
            </div>
            <div class="card-body table-border-style ">
                <div class="table-responsive">
                    <table class="table" id="pc-dt-simple">
                        <thead>
                            <tr>
                                <th> {{ __('Modules') }}</th>
                                <th> {{ __('Url') }}</th>
                                <th> {{ __('Method') }}</th>
                                <th width="200px"> {{ __('Action') }}</th>
                            </tr>
                        </thead>
                        @php
                        $webhooks = App\Models\Webhook::where(
                        'created_by',
                        Auth::user()->id,
                        )->get();
                        @endphp
                        <tbody>
                            @foreach ($webhooks as $webhook)
                            <tr class="action">
                                <td class="sorting_1">
                                    {{ $webhook->module }}
                                </td>
                                <td class="sorting_3">
                                    {{ $webhook->url }}
                                </td>
                                <td class="sorting_2">
                                    {{ $webhook->method }}
                                </td>
                                <td class="">
                                    <div class="action-btn me-2">
                                        <a class="mx-3 btn btn-sm align-items-center bg-info d-inline-flex justify-content-center"
                                            data-url="{{ route('webhook.edit', $webhook->id) }}"
                                            style="height: 30px;"
                                            data-size="md" data-ajax-popup="true"
                                            title="{{ __('Edit') }}"
                                            data-bs-toggle="tooltip"
                                            data-bs-placement="bottom" class="edit-icon"
                                           ><i class="ti ti-pencil text-white"></i></a>
                                    </div>
                        
                                    <div class="action-btn">
                                    {!! Form::open([
                                        'method' => 'DELETE',
                                        'route' => ['webhook.destroy', $webhook->id],
                                        'id' => 'delete-form-' . $webhook->id,
                                        ]) !!}
                                        <a href="#" class="mx-3 btn btn-sm  align-items-center bs-pass-para bg-danger d-inline-flex justify-content-center" 
                                        style="height: 30px;"
                                        data-bs-placement="bottom"
                                        data-bs-toggle="tooltip" title="{{__('Delete')}}"><i class="ti ti-trash text-white text-white"></i></a>
                                        {!! Form::close() !!}
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
<!-- [ sample-page ] end -->
</div>
<!-- [ Main Content ] end -->
</div>
@endsection


@push('css-page')
<link rel="stylesheet" href="{{asset('css/summernote/summernote-bs4.css')}}">
@endpush
@push('script-page')
<script src="{{asset('css/summernote/summernote-bs4.js')}}"></script>

<script>
    $(document).ready(function() {
        $('.summernote-simple').summernote({
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'italic', 'underline', 'strikethrough']],
                ['list', ['ul', 'ol', 'paragraph']],
                ['insert', ['link', 'unlink']],
            ],
            height: 200,
        });
    });
</script>

<script>
    $(document).on('click', 'input[name="theme_color"]', function() {
        var eleParent = $(this).attr('data-theme');
        $('#themefile').val(eleParent);
        var imgpath = $(this).attr('data-imgpath');
        $('.' + eleParent + '_img').attr('src', imgpath);
    });

    $(document).ready(function() {
        setTimeout(function(e) {
            var checked = $("input[type=radio][name='theme_color']:checked");
            $('#themefile').val(checked.attr('data-theme'));
            $('.' + checked.attr('data-theme') + '_img').attr('src', checked.attr('data-imgpath'));
        }, 300);
    });

    function check_theme(color_val) {

        $('.theme-color').prop('checked', false);
        $('input[value="' + color_val + '"]').prop('checked', true);
        $('#color_value').val(color_val);
    }
</script>

<script>
    $('.colorPicker').on('click', function(e) {
        $('body').removeClass('custom-color');
        if (/^theme-\d+$/) {
            $('body').removeClassRegex(/^theme-\d+$/);
        }
        $('body').addClass('custom-color');
        $('.themes-color-change').removeClass('active_color');
        $(this).addClass('active_color');
        const input = document.getElementById("color-picker");
        setColor();
        input.addEventListener("input", setColor);

        function setColor() {
            $(':root').css('--color-customColor', input.value);
        }

        $(`input[name='color_flag`).val('true');
    });

    $('.themes-color-change').on('click', function() {

        $(`input[name='color_flag`).val('false');

        var color_val = $(this).data('value');
        $('body').removeClass('custom-color');
        if (/^theme-\d+$/) {
            $('body').removeClassRegex(/^theme-\d+$/);
        }
        $('body').addClass(color_val);
        $('.theme-color').prop('checked', false);
        $('.themes-color-change').removeClass('active_color');
        $('.colorPicker').removeClass('active_color');
        $(this).addClass('active_color');
        $(`input[value=${color_val}]`).prop('checked', true);
    });

    $.fn.removeClassRegex = function(regex) {
        return $(this).removeClass(function(index, classes) {
            return classes.split(/\s+/).filter(function(c) {
                return regex.test(c);
            }).join(' ');
        });
    };
</script>

<script>
    // Currency selection functionality
    $(document).ready(function() {
        // Currency data mapping
        const currencyData = @json($currencies);
        const currencySymbols = @json(\App\Models\Currency::all()->pluck('symbol', 'code')->toArray());
        
        // Function to update currency symbol
        function updateCurrencySymbol() {
            const selectedCurrency = $('#currency-select').val();
            const symbol = currencySymbols[selectedCurrency] || '$';
            $('#currency-symbol').val(symbol);
        }
        
        // Update symbol when currency is changed
        $('#currency-select').on('change', function() {
            updateCurrencySymbol();
        });
        
        // Initialize symbol on page load
        updateCurrencySymbol();
    });
</script>
@endpush