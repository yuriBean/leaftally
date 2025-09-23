<link rel='stylesheet' href='{{ asset('css/cookieconsent.css') }}' media="screen" />
<style id="cc-theme-overrides">
  :root{
    --cc-btn-primary-bg:
    --cc-btn-primary-color:
    --cc-btn-primary-border-color:
    --cc-btn-primary-hover-bg:
    --cc-btn-primary-hover-color:
    --cc-btn-primary-hover-border-color:

    --cc-btn-secondary-bg: transparent;
    --cc-btn-secondary-color:
    --cc-btn-secondary-border-color:
    --cc-btn-secondary-hover-bg: rgba(0,124,56,.08);
    --cc-btn-secondary-hover-color:
    --cc-btn-secondary-hover-border-color:

    --cc-link-color:
    --cc-link-hover-color:

    --cc-toggle-bg-off:
    --cc-toggle-bg-on:
    --cc-toggle-knob-bg:

    --cc-btn-border-radius: 8px;
    --cc-separator-border-color:
  }

  .cc-btn.cc-btn-primary{
    background:
    border-color:
    color:
    border-radius: 8px;
  }
  .cc-btn.cc-btn-primary:hover{ background:

  .cc-btn.cc-btn-secondary{
    background: transparent !important;
    color:
    border-color:
    border-radius: 8px;
    box-shadow: inset 0 0 0 1px
  }
  .cc-btn.cc-btn-secondary:hover{ background: rgba(0,124,56,.08) !important; }

  .cc-link{ color:
  .cc-link:hover{ color:
</style>
<script src="{{ asset('js/cookieconsent.js') }}"></script>

<script>
<?php
    $setting = \App\Models\Utility::settings();
    $data = json_encode($setting);
?>
var data = {!! json_encode($data) !!};
var parsed = JSON.parse(data);

let language_code = document.documentElement.getAttribute('lang');
let languages = {};
languages[language_code] = {
  consent_modal: {
    title: 'hello',
    description: 'description',
    primary_btn: { text: 'primary_btn text', role: 'accept_all' },
    secondary_btn: { text: 'secondary_btn text', role: 'accept_necessary' }
  },
  settings_modal: {
    title: 'settings_modal',
    save_settings_btn: 'save_settings_btn',
    accept_all_btn: 'accept_all_btn',
    reject_all_btn: 'reject_all_btn',
    close_btn_label: 'close_btn_label',
    blocks: [
      { title: 'block title', description: 'block description' },
      {
        title: 'title',
        description: 'description',
        toggle: { value: 'necessary', enabled: true, readonly: false }
      },
    ]
  }
};
</script>

<script>
function setCookie(cname, cvalue, exdays){
  const d = new Date();
  d.setTime(d.getTime() + (exdays*24*60*60*1000));
  document.cookie = cname + "=" + cvalue + ";expires=" + d.toUTCString() + ";path=/";
}

function getCookie(cname){
  let name = cname + "=";
  let decodedCookie = decodeURIComponent(document.cookie);
  let ca = decodedCookie.split(';');
  for (let i=0;i<ca.length;i++){
    let c = ca[i];
    while (c.charAt(0) == ' ') c = c.substring(1);
    if (c.indexOf(name) == 0) return c.substring(name.length, c.length);
  }
  return "";
}

var cc = initCookieConsent();

cc.run({
  current_lang: 'en',
  autoclear_cookies: true,
  page_scripts: true,

  gui_options: {
    consent_modal: {
      layout: 'cloud',
      position: 'bottom center',
      transition: 'slide',
      swap_buttons: false
    },
    settings_modal: {
      layout: 'box',
      transition: 'slide'
    }
  },

  onChange: function(cookie, changed_preferences){},
  onAccept: function(cookie){
    if (!getCookie('cookie_consent_logged')) {
      var cookie = cookie.level;
      $.ajax({
        url: '{{ route('cookie-consent') }}',
        datType: 'json',
        data: { cookie: cookie },
      })
      setCookie('cookie_consent_logged', '1', 182, '/');
    }
  },

  languages: {
    'en': {
      consent_modal: {
        title: parsed.cookie_title,
        description: parsed.cookie_description + ' <button type="button" data-cc="c-settings" class="cc-link">Let me choose</button>',
        primary_btn: { text: 'Accept all', role: 'accept_all' },
        secondary_btn: { text: 'Reject all', role: 'accept_necessary' }
      },
      settings_modal: {
        title: 'Cookie preferences',
        save_settings_btn: 'Save settings',
        accept_all_btn: 'Accept all',
        reject_all_btn: 'Reject all',
        close_btn_label: 'Close',
        cookie_table_headers: [
          { col1: 'Name' }, { col2: 'Domain' }, { col3: 'Expiration' }, { col4: 'Description' }
        ],
        blocks: [
          { title: parsed.cookie_title + ' 📢', description: parsed.cookie_description + '.' },
          {
            title: parsed.strictly_cookie_title,
            description: parsed.strictly_cookie_description,
            toggle: { value: 'necessary', enabled: true, readonly: true }
          },
          {
            title: 'More information',
            description: '{{$setting['more_information_description']}} <a class="cc-link" href="{{$setting['contactus_url']}}">contact us</a>.'
          }
        ]
      }
    }
  }
});
</script>
