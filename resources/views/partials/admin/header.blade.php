@php
    use App\Models\Utility;
    $users = \Auth::user();
    $profile = asset(Storage::url('uploads/avatar/'));
    $currantLang = $users->currentLanguage();
    $languages = \App\Models\Language::where('code', $currantLang)->first();
    $mode_setting = \App\Models\Utility::getLayoutsSetting();
@endphp

<header class="sticky top-0 z-40 border-b border-gray-200/70
  bg-gradient-to-b from-white/90 to-[#F7FBF9]/90 backdrop-blur
  {{ isset($mode_setting['cust_theme_bg']) && $mode_setting['cust_theme_bg'] == 'on' ? 'bg-white/90' : '' }}">
  <div class="header-wrapper">
    <div class="flex justify-end items-center gap-3 px-4 py-3">

      <div class="group flex items-center gap-2 border border-[#007C38] text-[#007C38]
                  bg-white rounded-lg pr-2 pl-3 py-1.5 shadow-sm min-w-[14rem]
                  hover:bg-[#007C3808] transition-all duration-200 focus-within:ring-2 focus-within:ring-[#007C38]/30">
        <i data-lucide="search" class="w-4 h-4"></i>
        <input id="global-kbar" type="text" placeholder="Search…" autocomplete="off"
               class="bg-transparent focus:outline-none text-sm w-full placeholder:text-[#007C38]/60" />
        <span class="ml-1 text-[11px] leading-none bg-gray-50 border border-gray-200 rounded px-1.5 py-1
                     text-gray-700 font-semibold shadow-xs tracking-wide">
          ⌘K
        </span>
      </div>

      <ul class="flex items-center gap-2 m-0">
        @impersonating($guard = null)
          <li>
            <a class="inline-flex items-center gap-1.5 rounded-lg bg-red-50 text-red-700 px-3 py-1.5 text-sm font-medium border border-red-100 hover:bg-red-100 transition">
              <i class="ti ti-ban"></i>
              <span>{{ __('Exit Company Login') }}</span>
            </a>
          </li>
        @endImpersonating



        <li class="dropdown drp-company">
          <a class="relative w-9 h-9 rounded-full ring-1 ring-gray-200 hover:ring-[#007C38]/30 transition
                    overflow-hidden flex items-center justify-center bg-white"
             data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
            @if (\Auth::guard('customer')->check())
              <img src="{{ isset(\Auth::user()->avatar) && !empty(\Auth::user()->avatar) ? \App\Models\Utility::get_file('uploads/avatar/' . \Auth::user()->avatar) : 'logo-dark.png' }}"
                   class="w-full h-full object-cover" alt="avatar">
            @else
              <img src="{{ !empty(\Auth::user()->avatar) ? \App\Models\Utility::get_file(\Auth::user()->avatar) : asset(Storage::url('uploads/avatar/avatar.png')) }}"
                   class="w-full h-full object-cover" alt="avatar">
            @endif
          </a>

          <div class="dropdown-menu dropdown-menu-end rounded-xl shadow-xl border-0 p-2 min-w-[180px]">
            @if (\Auth::guard('customer')->check())
              <a href="{{ route('customer.profile') }}" class="dropdown-item rounded-md px-3 py-2">
                <i class="ti ti-user mr-1"></i> {{ __('My Profile') }}
              </a>
            @elseif(\Auth::guard('vender')->check())
              <a href="{{ route('vender.profile') }}" class="dropdown-item rounded-md px-3 py-2">
                <i class="ti ti-user mr-1"></i> {{ __('My Profile') }}
              </a>
            @else
              <a href="{{ route('profile') }}" class="dropdown-item rounded-md px-3 py-2">
                <i class="ti ti-user mr-1"></i> {{ __('My Profile') }}
              </a>
            @endif

            <div class="dropdown-divider my-2"></div>

            @if (\Auth::guard('customer')->check())
              <a href="{{ route('customer.logout') }}"
                 onclick="event.preventDefault(); document.getElementById('frm-logout').submit();"
                 class="dropdown-item rounded-md px-3 py-2 text-red-600 hover:bg-red-50">
                <i class="ti ti-power mr-1"></i> {{ __('Logout') }}
              </a>
              <form id="frm-logout" action="{{ route('customer.logout') }}" method="POST" class="d-none">
                {{ csrf_field() }}
              </form>
            @elseif(\Auth::guard('vender')->check())
              <a href="{{ route('vender.logout') }}"
                 onclick="event.preventDefault(); document.getElementById('frm-logout').submit();"
                 class="dropdown-item rounded-md px-3 py-2 text-red-600 hover:bg-red-50">
                <i class="ti ti-power mr-1"></i> {{ __('Logout') }}
              </a>
              <form id="frm-logout" action="{{ route('vender.logout') }}" method="POST" class="d-none">
                {{ csrf_field() }}
              </form>
            @else
              <a href="{{ route('logout') }}"
                 onclick="event.preventDefault(); document.getElementById('frm-logout').submit();"
                 class="dropdown-item rounded-md px-3 py-2 text-red-600 hover:bg-red-50">
                <i class="ti ti-power mr-1"></i> {{ __('Logout') }}
              </a>
              <form id="frm-logout" action="{{ route('logout') }}" method="POST" class="d-none">
                {{ csrf_field() }}
              </form>
            @endif
          </div>
        </li>
      </ul>

    </div>
  </div>
</header>

<script>
  // ⌘/Ctrl + K focuses the search
  document.addEventListener('keydown', (e) => {
    const isMac = /Mac|iPod|iPhone|iPad/.test(navigator.platform);
    if ((isMac && e.metaKey && e.key.toLowerCase() === 'k') || (!isMac && e.ctrlKey && e.key.toLowerCase() === 'k')) {
      e.preventDefault();
      const input = document.getElementById('global-kbar');
      if (input) { input.focus(); input.select(); }
    }
  });
</script>
