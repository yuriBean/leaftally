@php
    $settings = \Modules\LandingPage\Entities\LandingPageSetting::settings();
@endphp
@if ($settings['menubar_status'] == 'on')
    @if (is_array(json_decode($settings['menubar_page'])) || is_object(json_decode($settings['menubar_page'])))
        @foreach (json_decode($settings['menubar_page']) as $key => $value)
            @php
                // Define custom URLs for specific pages
                $customUrls = [
                    'about_us' => 'https://leaftally.com/',
                    'terms_and_conditions' => 'https://leaftally.com/terms-conditions/',
                    'privacy_policy' => 'https://leaftally.com/privacy-policy/'
                ];
                
                // Check if this page has a custom URL
                $customUrl = isset($customUrls[$value->page_slug]) ? $customUrls[$value->page_slug] : null;
            @endphp
            
            @if ((isset($value->login) && $value->login == "on") && (isset($value->template_name) && $value->template_name == 'page_content'))
                <li class="nav-item">
                    @if ($customUrl)
                        <a class="nav-link" target="_blank" href="{{ $customUrl }}">{{ $value->menubar_page_name }}</a>
                    @else
                        <a class="nav-link" href="{{ route('custom.page', $value->page_slug) }}">{{ $value->menubar_page_name }}</a>
                    @endif
                </li>
            @elseif ((isset($value->login) && $value->login == "on") && (isset($value->template_name) && $value->template_name == 'page_url'))
                <li class="nav-item">
                    @if ($customUrl)
                        <a class="nav-link" target="_blank" href="{{ $customUrl }}">{{ $value->menubar_page_name }}</a>
                    @else
                        <a class="nav-link" target="_blank" href="{{ $value->page_url }}">{{ $value->menubar_page_name }}</a>
                    @endif
                </li>
            @endif
        @endforeach
    @endif
@endif