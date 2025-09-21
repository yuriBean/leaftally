@php
    use \App\Models\Utility;
    // $logo=asset(Storage::url('uploads/logo/'));
    $logo = \App\Models\Utility::get_file('uploads/logo/');
    $company_favicon = Utility::getValByName('company_favicon');
    $SITE_RTL = env('SITE_RTL');
    $seo_setting = App\Models\Utility::getSeoSetting();
    $setting = \App\Models\Utility::getLayoutsSetting();
    $color = !empty($setting['color']) ? $setting['color'] : 'theme-3';
    $settings = \App\Models\Utility::settings();
    if(isset($settings['color_flag']) && $settings['color_flag'] == 'true')
    {
        $themeColor = 'custom-color';
    }
    else {
        $themeColor = $color;
    }
    $SITE_RTL = 'theme-3';
    if (!empty($setting['SITE_RTL'])) {
        $SITE_RTL = $setting['SITE_RTL'];
    }

    $mode_setting = \App\Models\Utility::mode_layout();
    $set_cookie = Utility::cookies();
@endphp

<!DOCTYPE html>

<html lang="en" dir="{{ $SITE_RTL == 'on' ? 'rtl' : '' }}">

<head>
    <title>
        {{ Utility::getValByName('title_text') ? Utility::getValByName('title_text') : config('app.name', 'AccountGo SaaS') }}
        - @yield('page-title')</title>
    <script src="{{ asset('js/html5shiv.js') }}"></script>
    <script src="{{ asset('js/respond.min.js') }}"></script>
    <!-- Meta -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="url" content="{{ url('') . '/' . config('chatify.path') }}" data-user="{{ Auth::user()->id }}">

    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <!-- Primary Meta Tags -->
    <meta name="title" content={{ $seo_setting['meta_keywords'] }}>
    <meta name="description" content={{ $seo_setting['meta_description'] }}>

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content={{ env('APP_URL') }}>
    <meta property="og:title" content={{ $seo_setting['meta_keywords'] }}>
    <meta property="og:description" content={{ $seo_setting['meta_description'] }}>
    <meta property="og:image" content={{ asset('/' . $seo_setting['meta_image']) }}>

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content={{ env('APP_URL') }}>
    <meta property="twitter:title" content={{ $seo_setting['meta_keywords'] }}>
    <meta property="twitter:description" content={{ $seo_setting['meta_description'] }}>
    <meta property="twitter:image"
        content={{ asset(Storage::url('uploads/metaevent/' . $seo_setting['meta_image'])) }}>
    <link rel="icon"
        href="{{ !empty($company_favicon) ? \App\Models\Utility::get_file('uploads/logo/' . $company_favicon) : asset(Storage::url('uploads/logo/favicon.png')) }}"
        type="image" sizes="16x16">
    <!-- Favicon icon -->
    {{-- @if (\Auth::user()->type == 'owner')

        <link rel="icon" href="{{$logo.'/'.(isset($company_favicon) && !empty($company_favicon)?$company_favicon:'favicon.png')}}" type="image" sizes="16x16">
    @else
        <link rel="icon" href="{{$logo.'/'.(isset($favicon) && !empty($favicon)? $favicon:'favicon.png')}}" type="image" sizes="16x16">

    @endif --}}
    <link rel="stylesheet" href="{{ asset('assets/css/plugins/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/plugins/animate.min.css') }}">

    <link rel="stylesheet" href="{{ asset('assets/css/plugins/datepicker-bs5.min.css') }}">

    <!--bootstrap switch-->
    <link rel="stylesheet" href="{{ asset('assets/css/plugins/bootstrap-switch-button.min.css') }}">


    <!-- font css -->
    <link rel="stylesheet" href="{{ asset('assets/fonts/tabler-icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/fonts/feather.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/fonts/fontawesome.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/fonts/material.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/plugins/main.css') }}">
    <!-- vendor css -->

    <link rel="stylesheet" href="{{ asset('assets/css/plugins/flatpickr.min.css') }}">


    @if ($SITE_RTL == 'on')
        <link rel="stylesheet" href="{{ asset('assets/css/style-rtl.css') }}">
    @endif
    @if ($setting['cust_darklayout'] == 'on')
        <link rel="stylesheet" href="{{ asset('assets/css/style-dark.css') }}" id="style">
    @else
        <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}" id="style">
    @endif

    <link rel="stylesheet" href="{{ asset('assets/css/plugins/dropzone.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/customizer.css') }}">

    <link rel="stylesheet" href="{{ asset('css/custom.css') }}" id="main-style-link">
    @if ($setting['cust_darklayout'] == 'on')
        <link rel="stylesheet" href="{{ asset('css/custom-dark.css') }}" id="style">
    @endif

    @if ($SITE_RTL == 'on')
        <link rel="stylesheet" href="{{ asset('css/custom-rtl.css') }}">
    @endif

    <style>
        :root {
            --color-customColor: <?= $color ?>;
        }
    </style>
    <link rel="stylesheet" href="{{ asset('css/custom-color.css') }}">
    <link rel="stylesheet" href="{{ asset('css/font-standardization.css') }}">
    @stack('css-page')


    <style>
        [dir="rtl"] .dash-sidebar {
            left: auto !important;
        }

        [dir="rtl"] .dash-header {
            left: 0;
            right: 280px;
        }

        [dir="rtl"] .dash-header:not(.transprent-bg) .header-wrapper {
            padding: 0 0 0 30px;
        }

        [dir="rtl"] .dash-header:not(.transprent-bg):not(.dash-mob-header)~.dash-container {
            margin-left: 0px !important;
        }

        [dir="rtl"] .me-auto.dash-mob-drp {
            margin-right: 10px !important;
        }

        [dir="rtl"] .me-auto {
            margin-left: 10px !important;
        }
    </style>
      <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/alpinejs" defer></script>
  <script src="https://unpkg.com/lucide@latest" defer></script>
    <script>
            document.addEventListener("DOMContentLoaded", () => {
            lucide.createIcons();
        });
    </script>
</head>

<body class="{{ $themeColor }}">
    <!-- [ Pre-loader ] start -->
    <div class="loader-bg">
        <div class="loader-track">
            <div class="loader-fill"></div>
        </div>
    </div>

    @include('partials.admin.menu')
    <!-- [ navigation menu ] end -->
    <!-- [ Header ] start -->
    @include('partials.admin.header')

    <!-- Modal -->
    <div class="modal notification-modal fade" id="notification-modal" tabindex="-1" role="dialog" aria-hidden="true">

        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <button type="button" class="btn-close float-end" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                    <h6 class="mt-2">
                        <i data-feather="monitor" class="me-2"></i>Desktop settings
                    </h6>
                    <hr />
                    <div class="form-check form-switch">
                        <input type="checkbox" class="form-check-input" id="pcsetting1" checked />
                        <label class="form-check-label f-w-600 pl-1" for="pcsetting1">Allow desktop
                            notification</label>
                    </div>
                    <p class="text-muted ms-5">
                        you get lettest content at a time when data will updated
                    </p>
                    <div class="form-check form-switch">
                        <input type="checkbox" class="form-check-input" id="pcsetting2" />
                        <label class="form-check-label f-w-600 pl-1" for="pcsetting2">Store Cookie</label>
                    </div>
                    <h6 class="mb-0 mt-5">
                        <i data-feather="save" class="me-2"></i>Application settings
                    </h6>
                    <hr />
                    <div class="form-check form-switch">
                        <input type="checkbox" class="form-check-input" id="pcsetting3" />
                        <label class="form-check-label f-w-600 pl-1" for="pcsetting3">Backup Storage</label>
                    </div>
                    <p class="text-muted mb-4 ms-5">
                        Automatically take backup as per schedule
                    </p>
                    <div class="form-check form-switch">
                        <input type="checkbox" class="form-check-input" id="pcsetting4" />cookie
                        <label class="form-check-label f-w-600 pl-1" for="pcsetting4">Allow guest to print
                            file</label>
                    </div>
                    <h6 class="mb-0 mt-5">
                        <i data-feather="cpu" class="me-2"></i>System settings
                    </h6>
                    <hr />
                    <div class="form-check form-switch">
                        <input type="checkbox" class="form-check-input" id="pcsetting5" checked />
                        <label class="form-check-label f-w-600 pl-1" for="pcsetting5">View other user chat</label>
                    </div>
                    <p class="text-muted ms-5">Allow to show public user message</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light-danger btn-sm" data-bs-dismiss="modal">
                        Close
                    </button>
                    <button type="button" class="btn btn-light-primary btn-sm">
                        Save changes
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- [ Header ] end -->


    <!-- [ Main Content ] start -->
    <div class="dash-container">
        <div class="dash-content">
            <div class="page-header">
                <div class="page-block">
                    <div class="row align-items-center">
                        <div class="col-6">
                            <div class="page-header-title">
                                <h4 class="m-b-10">@yield('page-title')</h4>
                            </div>
                            <ul class="breadcrumb">
                                @yield('breadcrumb')
                            </ul>
                        </div>
                        <div class="col-6 d-flex justify-content-end">
                            @yield('action-btn')
                        </div>
                    </div>
                </div>
            </div>
            @yield('content')
            <!-- [ Main Content ] end -->
        </div>
    </div>
    <div class="modal fade" id="commonModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title font-[700] text-[16px] text-black leading-[24px]" id="exampleModalLabel"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="body">
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="commonModalOver" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title font-[700] text-[16px] text-black leading-[24px]" id="exampleModalLabel"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                </div>
            </div>
        </div>
    </div>


    <div class="position-fixed top-0 end-0 p-3" style="z-index: 99999">
        <div id="liveToast" class="toast text-white  fade" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body"></div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                    aria-label="Close"></button>
            </div>
        </div>
    </div>
    @include('partials.admin.footer')
</body>
@if ($set_cookie['enable_cookie'] == 'on')
    @include('layouts.cookie_consent')
@endif
<script type="text/javascript">
var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
(function(){
var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
s1.async=true;
s1.src='https://embed.tawk.to/68c110bd0e49f131d3b57e5d/1j4p2ipoo';
s1.charset='UTF-8';
s1.setAttribute('crossorigin','*');
s0.parentNode.insertBefore(s1,s0);
})();
</script>
<script>
document.addEventListener('DOMContentLoaded', function () {
  const nav = document.querySelector('nav.dash-sidebar');
  if (!nav) return;

  // Find the *actual* scrollable element inside nav
  function isScrollable(el){
    const cs = getComputedStyle(el);
    const canScrollY = /(auto|scroll)/.test(cs.overflowY);
    return (el.scrollHeight - el.clientHeight) > 5 && (canScrollY || cs.overflowY !== 'visible');
  }

  const all = [nav, ...nav.querySelectorAll('*')];
  const candidates = all.filter(isScrollable);

  // pick the deepest, largest overflow container
  let scroller = nav, bestScore = -1, bestDepth = -1;
  for (const el of candidates){
    const score = el.scrollHeight - el.clientHeight;
    let depth = 0, p = el;
    while (p && p !== nav){ depth++; p = p.parentElement; }
    if (score > bestScore || (score === bestScore && depth > bestDepth)){
      bestScore = score; bestDepth = depth; scroller = el;
    }
  }

  // TEST: force scroll to bottom immediately
  scroller.scrollTop = scroller.scrollHeight;

  // also try known wrappers in your markup (if they exist)
  ['.navbar-wrapper', '.navbar-content', '.dash-navbar'].forEach(sel => {
    const el = nav.querySelector(sel);
    if (el && el.scrollHeight > el.clientHeight) el.scrollTop = el.scrollHeight;
  });

  // then center the active item within the detected scroller
  const active = nav.querySelector('.dash-item.active');
  function centerActive(){
    if (!active) return;
    const aRect = active.getBoundingClientRect();
    const cRect = scroller.getBoundingClientRect();
    const targetTop = scroller.scrollTop + (aRect.top - cRect.top) - (cRect.height/2 - aRect.height/2);
    scroller.scrollTop = Math.max(0, Math.min(targetTop, scroller.scrollHeight));
  }

  requestAnimationFrame(centerActive);
  setTimeout(centerActive, 120);   // handle late layout/icon loads
  window.addEventListener('load', centerActive);
});
</script>
<script>
$(document).ready(function () {
    function setDefaultValue(input) {
        if (!$(input).val().trim()) {
            $(input).val("0.00");
        }
    }

    function applyDefaults(root) {
        $(root).find('input[name="sale_price"], input[name="purchase_price"]').each(function () {
            setDefaultValue(this);
        });
    }

    applyDefaults(document);

    $(document).on('change blur', 'input[name="sale_price"], input[name="purchase_price"]', function () {
        setDefaultValue(this);
    });

    $(document).on('shown.bs.modal', '.modal', function () {
        applyDefaults(this);
    });

    $(document).ajaxComplete(function () {
        applyDefaults(document);
    });

    new MutationObserver(function (muts) {
        muts.forEach(function (m) {
            m.addedNodes && m.addedNodes.forEach(function (n) {
                if (n.nodeType === 1) applyDefaults(n);
            });
        });
    }).observe(document.body, { childList: true, subtree: true });
});
</script>

<script>
  // Copy billing -> shipping (unchanged)
  $(document).on('click', '#billing_data', function() {
      $("[name='shipping_name']").val($("[name='billing_name']").val());
      $("[name='shipping_country']").val($("[name='billing_country']").val());
      $("[name='shipping_state']").val($("[name='billing_state']").val());
      $("[name='shipping_city']").val($("[name='billing_city']").val());
      $("[name='shipping_phone']").val($("[name='billing_phone']").val());
      $("[name='shipping_zip']").val($("[name='billing_zip']").val());
      $("[name='shipping_address']").val($("[name='billing_address']").val());
  });
     $(document).on('change', '#password_switch', function() {
       if ($(this).is(':checked')) {
           $('.ps_div').removeClass('d-none');
           $('#password').attr("required", true);
           $('#user_name').attr("required", true);
   
       } else {
           $('.ps_div').addClass('d-none');
           $('#password').val(null);
           $('#user_name').val(null);
           $('#password').removeAttr("required");
           $('#user_name').removeAttr("required");
       }
   });

  // Row click navigate, but ignore controls
  $(document).on('click', 'input[type=checkbox], label.mcheck, .dropdown-menu, [data-bs-toggle="dropdown"]', function(e){ e.stopPropagation(); });
  $(document).on('click', 'tr.cust_tr', function(){ const url=$(this).data('url'); if(url) window.location=url; });

  // Top toolbar "Export Selected" uses same selection list
  $(document).on('click','[data-export-selected][data-scope="customers"]',function(e){
    e.preventDefault();
    const scope = $(this).data('scope');
    const route = $(this).data('route');
    const key   = 'bulk:'+scope;
    let ids = [];
    try { ids = JSON.parse(localStorage.getItem(key) || '[]'); } catch(e) {}

    if(!ids.length){
      Swal.fire({ icon:'info', title:'{{ __('No selection') }}', text:'{{ __('Please select at least one row.') }}' });
      return;
    }
    const token = $('meta[name="csrf-token"]').attr('content') || '{{ csrf_token() }}';
    const $f = $('<form>', { method:'POST', action:route, target:'_blank' });
    $f.append($('<input>',{type:'hidden', name:'_token', value:token}));
    ids.forEach(id => $f.append($('<input>',{type:'hidden', name:'ids[]', value:id})));
    $(document.body).append($f);
    $f.trigger('submit').remove();
  });

  // Single delete confirmation is handled by loadConfirm() in custom.js
</script>
</html>
