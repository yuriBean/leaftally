@php
    use App\Models\Utility;
    use App\Services\Feature as PlanFeatureService;
    use App\Enum\PlanFeature as PF;

    $logo = \App\Models\Utility::get_file('uploads/logo/');

    if (\Auth::user()->type == 'super admin') {
        $company_logo = Utility::get_superadmin_logo();
    } else {
        $company_logo = Utility::get_company_logo();
    }

    $mode_setting = \App\Models\Utility::getLayoutsSetting();

    $emailTemplate = App\Models\EmailTemplate::first();

    // Plan feature helper for current user (used only to lock items, not remove)
    $planFeature = PlanFeatureService::for(\Auth::user());
    $F = fn(string $k) => $planFeature->enabled($k);

    // Flags to apply
    $featUserAccess     = $F(PF::USER_ACCESS);
    $featBudget         = $F(PF::BUDGETING);
    $featPayroll        = $F(PF::PAYROLL);
    $featTax            = $F(PF::TAX);
    $featAudit          = $F(PF::AUDIT);
    $featManufacturing  = $F(PF::MANUFACTURING);
@endphp

<style>
    h3.title-of-dashboard {
        font-size: 12px;
        background: #007c38;
        color: #fff;
        border-radius: 6px;
    }
    .nav-locked { opacity:.6; cursor:not-allowed; }
    .nav-locked i { font-size:.9rem; margin-left:.25rem; }
</style>

@if(\Auth::user()->type == 'super admin')
<style>
    .title-of-dashboard{
        display: none;
    }
</style>
@endif

<nav class="dash-sidebar light-sidebar transprent-bg">
    <div class="navbar-wrapper w-72 min-h-screen bg-white/90 backdrop-blur shadow-[0_10px_30px_rgba(0,124,56,0.12)] flex flex-col px-4 py-4">        
        <div class="flex items-center justify-between pb-3" style="border-bottom: 1px solid #e5e7eb;">
               <div class="flex items-center gap-2">
                <img src="{{ !empty($company_logo) ? \App\Models\Utility::get_file('uploads/logo/' . $company_logo) : asset(Storage::url('uploads/logo/logo-dark.png')) }}" alt="logo" style="width:30px">
                <h1 class="text-2xl font-bold text-black">LeafTally</h1>
            </div>
            @if (\Auth::guard('customer')->check())
                <a href="{{ route('customer.logout') }}"
                    onclick="event.preventDefault(); document.getElementById('frm-logout').submit();"
                    class="text-gray-500 hover:text-gray-800">
                    <i data-lucide="log-out" class="w-5 h-5"></i>
                </a>
                <form id="frm-logout" action="{{ route('customer.logout') }}" method="POST" class="d-none">
                    {{ csrf_field() }}
                </form>
            @elseif(\Auth::guard('vender')->check())
                <a href="{{ route('vender.logout') }}"
                    onclick="event.preventDefault(); document.getElementById('frm-logout').submit();"
                    class="text-gray-500 hover:text-gray-800">
                    <i data-lucide="log-out" class="w-5 h-5"></i>
                </a>
                <form id="frm-logout" action="{{ route('vender.logout') }}" method="POST" class="d-none">
                    {{ csrf_field() }}
                </form>
            @else
                <a href="{{ route('logout') }}"
                    onclick="event.preventDefault(); document.getElementById('frm-logout').submit();"
                    class="text-gray-500 hover:text-gray-800">
                    <i data-lucide="log-out" class="w-5 h-5"></i>
                </a>
                <form id="frm-logout" action="{{ route('logout') }}" method="POST" class="d-none">
                    {{ csrf_field() }}
                </form>
            @endif
        </div>

        @if (Gate::check('create product & service') ||
                Gate::check('create customer') ||
                Gate::check('create vender') ||
                Gate::check('create proposal') ||
                Gate::check('create invoice') ||
                Gate::check('create bill') ||
                Gate::check('create goal') ||
                Gate::check('create bank account'))
            <button class="dropdown dash-h-item w-full">
                <div class="dropdown notification-icon">    
                    <a class="dropdown-toggle arrow-none mt-2 w-full border border-transparent bg-[#007C38] text-white hover:bg-[#007C38]/90  font-semibold py-2 rounded-lg flex items-center justify-center transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                             viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                             stroke-linecap="round" stroke-linejoin="round"
                             class="w-4 h-4 mr-2">
                          <path d="M5 12h14"></path>
                          <path d="M12 5v14"></path>
                        </svg>
                        New
                      </a>
                      
                 
                    <div class="dropdown-menu" aria-labelledby="dropdownBookmark">
                        @if (Gate::check('create product & service'))
                            <a class="dropdown-item" href="{{ route('productservice.create') }}" data-title="{{ __('Create New Product') }}"><i
                                    class="ti ti-shopping-cart"></i>{{ __('Create New Product') }}</a>
                        @endif
                        @if (Gate::check('create customer'))
                            <a class="dropdown-item" href="#" data-size="lg"
                                data-url="{{ route('customer.create') }}" data-ajax-popup="true"
                                data-title="{{ __('Create New Customer') }}"><i
                                    class="ti ti-user"></i>{{ __('Create New Customer') }}</a>
                        @endif
                        @if (Gate::check('create vender'))
                            <a class="dropdown-item" href="#" data-size="lg"
                                data-url="{{ route('vender.create') }}" data-ajax-popup="true"
                                data-title="{{ __('Create New Vendor') }}"><i
                                    class="ti ti-note"></i>{{ __('Create New Vendor') }}</a>
                        @endif
                        <!-- @if (Gate::check('create proposal'))
                            <a class="dropdown-item" href="{{ route('proposal.create', 0) }}"><i
                                    class="ti ti-file"></i>{{ __('Create New Proposal') }}</a>
                        @endif -->
                        @if (Gate::check('create invoice'))
                            <a class="dropdown-item" href="{{ route('invoice.create', 0) }}"><i
                                    class="ti ti-file-invoice"></i>{{ __('Create New Invoice') }}</a>
                        @endif
                        @if (Gate::check('create bill'))
                            <a class="dropdown-item" href="{{ route('bill.create', 0) }}"><i
                                    class="ti ti-report-money"></i>{{ __('Create New Bill') }}</a>
                        @endif
                        @if (Gate::check('create bank account'))
                            <a class="dropdown-item" href="#" data-url="{{ route('bank-account.create') }}"
                                data-ajax-popup="true" data-title="{{ __('Create New Account') }}"><i
                                    class="ti ti-building-bank"></i>{{ __('Create New Account') }}</a>
                        @endif
                        @if (Gate::check('create goal'))
                            <a class="dropdown-item" href="#" data-url="{{ route('goal.create') }}"
                                data-ajax-popup="true" data-title="{{ __('Create New Goal') }}"><i
                                    class="ti ti-target"></i>{{ __('Create New Goal') }}</a>
                        @endif
                    </div>
                </div>
            </button>
        @endif

        <div class="mt-6 navbar-content">
       @if (!Auth::guard('customer')->check() && !Auth::guard('vender')->check())
       <div class="mt-6 my-3 px-3">
        <h3 class="flex items-center gap-2 text-xs font-semibold tracking-wide text-slate-600 uppercase">
          <span class="inline-flex h-6 w-6 items-center justify-center rounded-md bg-[#007C38]/10">
            <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M3 12h18M12 3v18" />
            </svg>
          </span>
          Management
        </h3>
        <div class="mt-1 h-[2px] w-full rounded bg-gradient-to-r from-[#007C38] via-[#26A269] to-transparent"></div>
      </div>        @endif


            <ul class="dash-navbar space-y-2 my-4">
                {{-- ------- Dashboard ---------- --}}
                <li class="dash-item">
                    @php
                      $isCustomer = \Auth::guard('customer')->check();
                      $isVendor   = \Auth::guard('vender')->check();
                      $isActive   = Request::route()->getName() == ($isCustomer ? 'customer.dashboard' : ($isVendor ? 'vender.dashboard' : 'dashboard'));
                      $route      = $isCustomer ? route('customer.dashboard') : ($isVendor ? route('vender.dashboard') : route('dashboard'));
                    @endphp
                
                    <a href="{{ $route }}"
                       class="group flex items-center gap-3 rounded-lg px-3 py-2.5 transition-all
                              {{ $isActive
                                  ? 'bg-[#007C38] text-[#007C38] shadow-sm'
                                  : 'text-slate-600 hover:bg-[#007C38]/10 hover:text-[#007C38]' }}">
                      <span class="flex h-8 w-8 items-center justify-center rounded-md
                                   {{ $isActive ? 'bg-white/15' : 'bg-slate-100 group-hover:bg-[#007C38]/10' }}">
                        <img src="{{ asset('web-assets/dashboard/icons/dashboard.svg') }}" alt="dashboard" class="h-4 w-4">
                      </span>
                      <span class="font-medium flex-1">{{ __('Dashboard') }}</span>
                    </a>
                  </li>
                  
<!--                 @if (Gate::check('manage customer proposal'))
                    <li class="dash-item dash-hasmenu {{ Request::segment(1) == 'customer.proposal' || Request::segment(1) == 'customer.retainer' || in_array(Request::route()->getName(), ['customer.proposal', 'customer.proposal.show', 'customer.retainer', 'customer.retainer.show']) ? ' active dash-trigger' : '' }}">
                        <a href="#!"
                            class="flex mb-2 items-center gap-2 px-2 py-2 rounded hover:bg-[#007C380F] text-gray-700 hover:font-semibold hover:text-[#007C38]">
                            <img src="{{ asset('web-assets/dashboard/icons/dashboard.svg') }}" alt="dashboard">
                            <span>{{ __('Presale') }}</span>
                            <span class="dash-arrow"><i data-feather="chevron-right"></i></span>
                        </a>
                        <ul class="dash-submenu {{ Request::segment(1) == 'customer.proposal' || Request::segment(1) == 'customer.retainer' ? 'show' : '' }}">
                            @can('manage customer proposal')
                                <li class="dash-item {{ in_array(Request::route()->getName(), ['customer.proposal', 'customer.proposal.show']) ? 'font-semibold active' : '' }}">
                                    <a class="flex items-center gap-2 px-2 py-2 rounded hover:bg-[#007C380F] text-gray-700 hover:font-semibold hover:text-[#007C38] ml-[30px]"
                                       href="{{ route('customer.proposal') }}">{{ __('Proposal') }}</a>
                                </li>
                            @endcan
                            @can('manage customer proposal')
                                <li class="dash-item {{ in_array(Request::route()->getName(), ['customer.retainer', 'customer.retainer.show']) ? 'font-semibold active' : '' }}">
                                    <a class="flex items-center gap-2 px-2 py-2 rounded hover:bg-[#007C380F] text-gray-700 hover:font-semibold hover:text-[#007C38] ml-[30px]"
                                       href="{{ route('customer.retainer') }}">{{ __('Retainers') }}</a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endif -->

                {{-- ------- Customer Invoice ---------- --}}
                @can('manage customer invoice')
                    @php $isActive = in_array(Request::route()->getName(), ['customer.invoice','customer.invoice.show']); @endphp
                    <li class="dash-item {{ $isActive ? 'active' : '' }}">
                        <a href="{{ route('customer.invoice') }}"
                        class="group flex items-center gap-3 rounded-lg px-3 py-2.5 transition-all
                                {{ $isActive 
                                    ? 'bg-[#007C38] text-[#007C38] shadow-sm' 
                                    : 'text-slate-600 hover:bg-[#007C38]/10 hover:text-[#007C38]' }}">
                        <span class="flex h-8 w-8 items-center justify-center rounded-md
                                    {{ $isActive ? 'bg-white/15' : 'bg-slate-100 group-hover:bg-[#007C38]/10' }}">
                            <img src="{{ asset('web-assets/dashboard/icons/invoices.svg') }}" alt="invoice" class="h-4 w-4">
                        </span>
                        <span class="font-medium flex-1">{{ __('Invoice') }}</span>
                        <svg class="h-4 w-4 opacity-0 group-hover:opacity-100 transition {{ $isActive ? 'opacity-100' : '' }}"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M9 18l6-6-6-6" />
                        </svg>
                        </a>
                    </li>
                    @endcan

                    @can('manage customer payment')
                    @php $isActive = Request::route()->getName() == 'customer.payment'; @endphp
                    <li class="dash-item {{ $isActive ? 'active' : '' }}">
                        <a href="{{ route('customer.payment') }}"
                        class="group flex items-center gap-3 rounded-lg px-3 py-2.5 transition-all
                                {{ $isActive 
                                    ? 'bg-[#007C38] text-[#007C38] shadow-sm' 
                                    : 'text-slate-600 hover:bg-[#007C38]/10 hover:text-[#007C38]' }}">
                        <span class="flex h-8 w-8 items-center justify-center rounded-md
                                    {{ $isActive ? 'bg-white/15' : 'bg-slate-100 group-hover:bg-[#007C38]/10' }}">
                            <img src="{{ asset('web-assets/dashboard/icons/payments.svg') }}" alt="payment" class="h-4 w-4">
                        </span>
                        <span class="font-medium flex-1">{{ __('Payment') }}</span>
                        <svg class="h-4 w-4 opacity-0 group-hover:opacity-100 transition {{ $isActive ? 'opacity-100' : '' }}"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M9 18l6-6-6-6" />
                        </svg>
                        </a>
                    </li>
                    @endcan

                    @can('manage customer transaction')
                    @php $isActive = Request::route()->getName() == 'customer.transaction'; @endphp
                    <li class="dash-item {{ $isActive ? 'active' : '' }}">
                        <a href="{{ route('customer.transaction') }}"
                        class="group flex items-center gap-3 rounded-lg px-3 py-2.5 transition-all
                                {{ $isActive 
                                    ? 'bg-[#007C38] text-[#007C38] shadow-sm' 
                                    : 'text-slate-600 hover:bg-[#007C38]/10 hover:text-[#007C38]' }}">
                        <span class="flex h-8 w-8 items-center justify-center rounded-md
                                    {{ $isActive ? 'bg-white/15' : 'bg-slate-100 group-hover:bg-[#007C38]/10' }}">
                            <img src="{{ asset('web-assets/dashboard/icons/transactions.svg') }}" alt="transaction" class="h-4 w-4">
                        </span>
                        <span class="font-medium flex-1">{{ __('Transaction') }}</span>
                        <svg class="h-4 w-4 opacity-0 group-hover:opacity-100 transition {{ $isActive ? 'opacity-100' : '' }}"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M9 18l6-6-6-6" />
                        </svg>
                        </a>
                    </li>
                    @endcan

                    @can('manage vender bill')
                    @php $isActive = in_array(Request::route()->getName(), ['vender.bill','vender.bill.show']); @endphp
                    <li class="dash-item {{ $isActive ? 'active' : '' }}">
                        <a href="{{ route('vender.bill') }}"
                        class="group flex items-center gap-3 rounded-lg px-3 py-2.5 transition-all
                                {{ $isActive 
                                    ? 'bg-[#007C38] text-[#007C38] shadow-sm' 
                                    : 'text-slate-600 hover:bg-[#007C38]/10 hover:text-[#007C38]' }}">
                        <span class="flex h-8 w-8 items-center justify-center rounded-md
                                    {{ $isActive ? 'bg-white/15' : 'bg-slate-100 group-hover:bg-[#007C38]/10' }}">
                            <img src="{{ asset('web-assets/dashboard/icons/bills.svg') }}" alt="bill" class="h-4 w-4">
                        </span>
                        <span class="font-medium flex-1">{{ __('Bill') }}</span>
                        <svg class="h-4 w-4 opacity-0 group-hover:opacity-100 transition {{ $isActive ? 'opacity-100' : '' }}"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M9 18l6-6-6-6" />
                        </svg>
                        </a>
                    </li>
                    @endcan

                    @can('manage vender payment')
                    @php $isActive = Request::route()->getName() == 'vender.payment'; @endphp
                    <li class="dash-item {{ $isActive ? 'active' : '' }}">
                        <a href="{{ route('vender.payment') }}"
                        class="group flex items-center gap-3 rounded-lg px-3 py-2.5 transition-all
                                {{ $isActive 
                                    ? 'bg-[#007C38] text-[#007C38] shadow-sm' 
                                    : 'text-slate-600 hover:bg-[#007C38]/10 hover:text-[#007C38]' }}">
                        <span class="flex h-8 w-8 items-center justify-center rounded-md
                                    {{ $isActive ? 'bg-white/15' : 'bg-slate-100 group-hover:bg-[#007C38]/10' }}">
                            <img src="{{ asset('web-assets/dashboard/icons/payments.svg') }}" alt="payment" class="h-4 w-4">
                        </span>
                        <span class="font-medium flex-1">{{ __('Payment') }}</span>
                        <svg class="h-4 w-4 opacity-0 group-hover:opacity-100 transition {{ $isActive ? 'opacity-100' : '' }}"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M9 18l6-6-6-6" />
                        </svg>
                        </a>
                    </li>
                    @endcan

                    @can('manage vender transaction')
                    @php $isActive = Request::route()->getName() == 'vender.transaction'; @endphp
                    <li class="dash-item {{ $isActive ? 'active' : '' }}">
                        <a href="{{ route('vender.transaction') }}"
                        class="group flex items-center gap-3 rounded-lg px-3 py-2.5 transition-all
                                {{ $isActive 
                                    ? 'bg-[#007C38] text-[#007C38] shadow-sm' 
                                    : 'text-slate-600 hover:bg-[#007C38]/10 hover:text-[#007C38]' }}">
                        <span class="flex h-8 w-8 items-center justify-center rounded-md
                                    {{ $isActive ? 'bg-white/15' : 'bg-slate-100 group-hover:bg-[#007C38]/10' }}">
                            <img src="{{ asset('web-assets/dashboard/icons/transactions.svg') }}" alt="transaction" class="h-4 w-4">
                        </span>
                        <span class="font-medium flex-1">{{ __('Transaction') }}</span>
                        <svg class="h-4 w-4 opacity-0 group-hover:opacity-100 transition {{ $isActive ? 'opacity-100' : '' }}"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M9 18l6-6-6-6" />
                        </svg>
                        </a>
                    </li>
                    @endcan


                {{-- ------- Staff ---------- --}}
                @if (\Auth::user()->type == 'super admin')
                    @can('manage user')
                        @php $isActive = in_array(Request::route()->getName(), ['users.index','users.create','users.edit']); @endphp
                        <li class="dash-item {{ $isActive ? 'active' : '' }}">
                        <a href="{{ route('users.index') }}"
                            class="group flex items-center gap-3 rounded-lg px-3 py-2.5 transition-all
                                    {{ $isActive 
                                        ? 'bg-[#007C38] text-[#007C38] shadow-sm' 
                                        : 'text-slate-600 hover:bg-[#007C38]/10 hover:text-[#007C38]' }}">
                            <span class="flex h-8 w-8 items-center justify-center rounded-md
                                        {{ $isActive ? 'bg-white/15' : 'bg-slate-100 group-hover:bg-[#007C38]/10' }}">
                            <img src="{{ asset('web-assets/dashboard/icons/companies.svg') }}" alt="companies" class="h-4 w-4">
                            </span>
                            <span class="font-medium flex-1">{{ __('Companies') }}</span>
                        </a>
                        </li>
                    @endcan

                    @php $isActive = Request::segment(1) == 'plans' || Request::segment(1) == 'stripe'; @endphp
                    <li class="dash-item {{ $isActive ? 'active' : '' }}">
                        <a href="{{ route('plans.index') }}"
                        class="group flex items-center gap-3 rounded-lg px-3 py-2.5 transition-all
                                {{ $isActive 
                                    ? 'bg-[#007C38] text-[#007C38] shadow-sm' 
                                    : 'text-slate-600 hover:bg-[#007C38]/10 hover:text-[#007C38]' }}">
                        <span class="flex h-8 w-8 items-center justify-center rounded-md
                                    {{ $isActive ? 'bg-white/15' : 'bg-slate-100 group-hover:bg-[#007C38]/10' }}">
                            <img src="{{ asset('web-assets/dashboard/icons/plan.svg') }}" alt="plan" class="h-4 w-4">
                        </span>
                        <span class="font-medium flex-1">{{ __('Plan') }}</span>
                        </a>
                    </li>

                    @php $isActive = in_array(Request::route()->getName(), ['userlogs.index','userlogs.show']); @endphp
                    <li class="dash-item {{ $isActive ? 'active' : '' }}">
                        <a href="{{ route('userlogs.index') }}"
                        class="group flex items-center gap-3 rounded-lg px-3 py-2.5 transition-all
                                {{ $isActive 
                                    ? 'bg-[#007C38] text-[#007C38] shadow-sm' 
                                    : 'text-slate-600 hover:bg-[#007C38]/10 hover:text-[#007C38]' }}">
                        <span class="flex h-8 w-8 items-center justify-center rounded-md
                                    {{ $isActive ? 'bg-white/15' : 'bg-slate-100 group-hover:bg-[#007C38]/10' }}">
                            <img src="{{ asset('web-assets/dashboard/icons/staff.svg') }}" alt="user logs" class="h-4 w-4">
                        </span>
                        <span class="font-medium flex-1">{{ __('User Logs') }}</span>
                        </a>
                    </li>
                    @else
                    @if (Gate::check('manage user') || Gate::check('manage role'))
                        @php
                        $isStaffActive = Request::segment(1) == 'users' || Request::segment(1) == 'roles' || Request::segment(1) == 'permissions';
                        @endphp
                        <li class="dash-item dash-hasmenu {{ $isStaffActive ? 'active dash-trigger' : '' }}">
                        <a href="#!"
                            class="group flex items-center gap-3 rounded-lg px-3 py-2.5 transition-all
                                    {{ $isStaffActive 
                                        ? 'bg-[#007C38] text-[#007C38] shadow-sm' 
                                        : 'text-slate-600 hover:bg-[#007C38]/10 hover:text-[#007C38]' }}">
                            <span class="flex h-8 w-8 items-center justify-center rounded-md
                                        {{ $isStaffActive ? 'bg-white/15' : 'bg-slate-100 group-hover:bg-[#007C38]/10' }}">
                            <img src="{{ asset('web-assets/dashboard/icons/staff.svg') }}" alt="staff" class="h-4 w-4">
                            </span>
                            <span class="font-medium flex-1">{{ __('Staff') }}</span>
                            <svg class="h-4 w-4 transition-transform {{ $isStaffActive ? 'rotate-90 text-[#007C38]' : 'text-slate-400 group-hover:text-[#007C38]' }}" 
                                fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path d="M9 5l7 7-7 7" />
                            </svg>
                        </a>

                        <ul class="dash-submenu {{ $isStaffActive ? 'show' : '' }}">
                            @can('manage user')
                            <li class="dash-item {{ in_array(Request::route()->getName(), ['users.index','users.create','users.edit']) ? 'active' : '' }}">
                                @if($featUserAccess)
                                <a href="{{ route('users.index') }}"
                                    class="flex items-center gap-2 ml-[30px] px-3 py-2 rounded hover:bg-[#007C38]/10 hover:text-[#007C38]">
                                    {{ __('User') }}
                                </a>
                                @else
                                <a href="javascript:void(0)" 
                                    class="flex items-center gap-2 ml-[30px] px-3 py-2 rounded text-gray-500 nav-locked" 
                                    title="{{ __('Upgrade required to access User Management') }}">
                                    {{ __('User') }} <i class="ti ti-lock"></i>
                                </a>
                                @endif
                            </li>
                            @endcan

                            @can('manage role')
                            <li class="dash-item {{ in_array(Request::route()->getName(), ['roles.index','roles.create','roles.edit']) ? 'active' : '' }}">
                                @if($featUserAccess)
                                <a href="{{ route('roles.index') }}"
                                    class="flex items-center gap-2 ml-[30px] px-3 py-2 rounded hover:bg-[#007C38]/10 hover:text-[#007C38]">
                                    {{ __('Role') }}
                                </a>
                                @else
                                <a href="javascript:void(0)" 
                                    class="flex items-center gap-2 ml-[30px] px-3 py-2 rounded text-gray-500 nav-locked" 
                                    title="{{ __('Upgrade required to access Roles') }}">
                                    {{ __('Role') }} <i class="ti ti-lock"></i>
                                </a>
                                @endif
                            </li>
                            @endcan
                        </ul>
                        </li>
                    @endif

                    @if (!Auth::guard('customer')->check() && !Auth::guard('vender')->check())

                    <div class="mt-6 my-3 px-3">
                        <h3 class="flex items-center gap-2 text-xs font-semibold tracking-wide text-slate-600 uppercase">
                        <span class="inline-flex h-6 w-6 items-center justify-center rounded-md bg-[#007C38]/10">
                            <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                            <circle cx="12" cy="7" r="4" />
                            </svg>
                        </span>
                        <span class="uppercase">Customer Management</span>
                        </h3>
                        <div class="mt-2 h-[3px] w-full rounded bg-gradient-to-r from-[#007C38] via-[#26A269] to-transparent"></div>
                    </div>    
                    @endif
                {{-- Customer --}}
                        @if (Gate::check('manage customer'))
                        @php $isActive = Request::segment(1) == 'customer'; @endphp
                        <li class="dash-item {{ $isActive ? 'active' : '' }}">
                            <a href="{{ route('customer.index') }}"
                            class="group flex items-center gap-3 rounded-lg px-3 py-2.5 transition-all
                                    {{ $isActive
                                        ? 'bg-[#007C38] text-[#007C38] shadow-sm'
                                        : 'text-slate-600 hover:bg-[#007C38]/10 hover:text-[#007C38]' }}">
                            <span class="flex h-8 w-8 items-center justify-center rounded-md
                                        {{ $isActive ? 'bg-white/15' : 'bg-slate-100 group-hover:bg-[#007C38]/10' }}">
                                <img src="{{ asset('web-assets/dashboard/icons/customer.svg') }}" alt="customer" class="h-4 w-4">
                            </span>
                            <span class=" font-medium flex-1"> {{ __('Customer') }} </span>
                            </a>
                        </li>
                        @endif

                    {{-- Vendor --}}
                    @if (Gate::check('manage vender'))
                    @php $isActive = Request::segment(1) == 'vender'; @endphp
                    <li class="dash-item {{ $isActive ? 'active' : '' }}">
                        <a href="{{ route('vender.index') }}"
                            class="group flex items-center gap-3 rounded-lg px-3 py-2.5 transition-all
                                {{ $isActive
                                        ? 'bg-[#007C38] text-[#007C38] shadow-sm'
                                        : 'text-slate-600 hover:bg-[#007C38]/10 hover:text-[#007C38]' }}">
                            <span class="flex h-8 w-8 items-center justify-center rounded-md
                                    {{ $isActive ? 'bg-white/15' : 'bg-slate-100 group-hover:bg-[#007C38]/10' }}">
                            <img src="{{ asset('web-assets/dashboard/icons/vendor.svg') }}" alt="vendors" class="h-4 w-4">
                        </span>
                        <span class=" font-medium flex-1"> {{ __('Vendor') }} </span>
                        </a>
                    </li>
                    @endif


                @if (!Auth::guard('customer')->check() && !Auth::guard('vender')->check())
                <div class="mt-6 my-3 px-3">
                    <h3 class="flex items-center gap-2 text-xs font-semibold tracking-wide text-slate-600 uppercase">
                    <span class="inline-flex h-6 w-6 items-center justify-center rounded-md bg-[#007C38]/10">
                        <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                        <circle cx="12" cy="7" r="4" />
                        </svg>
                    </span>
                    <span class="uppercase">Inventory Management</span>
                    </h3>
                    <div class="mt-2 h-[3px] w-full rounded bg-gradient-to-r from-[#007C38] via-[#26A269] to-transparent"></div>
                </div>    
                @endif

                {{-- ------- Product & Service ---------- --}}
                @if (Gate::check('manage product & service'))
                @php $isActive = Request::segment(1) == 'productservice'; @endphp
                <li class="dash-item {{ $isActive ? 'active' : '' }}">
                    <a href="{{ route('productservice.index') }}"
                    class="group flex items-center gap-3 rounded-lg px-3 py-2.5 transition-all
                    {{ $isActive
                            ? 'bg-[#007C38] text-[#007C38] shadow-sm'
                            : 'text-slate-600 hover:bg-[#007C38]/10 hover:text-[#007C38]' }}">
                            <span class="flex h-8 w-8 items-center justify-center rounded-md
                            {{ $isActive ? 'bg-white/15' : 'bg-slate-100 group-hover:bg-[#007C38]/10' }}">
                        <img src="{{ asset('web-assets/dashboard/icons/product_&_services.svg') }}"
                                 alt="product_&_services" class="h-4 w-4"></span>
                                 <span class=" font-medium flex-1">{{ __('Product & Services') }}</span>
                        </a>
                    </li>
                @endif

                {{-- ------- Product & Stock ---------- --}}
                @if (Gate::check('manage product & service'))
                @php $isActive = Request::segment(1) == 'productstock'; @endphp
                    <li class="dash-item {{ $isActive ? 'active' : '' }}">
                        <a href="{{ route('productstock.index') }}"
                            class="group flex items-center gap-3 rounded-lg px-3 py-2.5 transition-all
                                {{ $isActive
                                        ? 'bg-[#007C38] text-[#007C38] shadow-sm'
                                        : 'text-slate-600 hover:bg-[#007C38]/10 hover:text-[#007C38]' }}">
                            <span class="flex h-8 w-8 items-center justify-center rounded-md
                                    {{ $isActive ? 'bg-white/15' : 'bg-slate-100 group-hover:bg-[#007C38]/10' }}">
                            <img src="{{ asset('web-assets/dashboard/icons/inventory.svg') }}" alt="inventory" class="h-4 w-4">
                        </span>
                        <span class=" font-medium flex-1"> {{ __('Inventory') }} </span>
                        </a>
                    </li>
                @endif
                    {{-- ------- Manufacturing ---------- --}}
                    @if ((Gate::check('manage bom') || Gate::check('manage production')) && \Auth::user()->type != 'super admin')
                    @php
                        $mfgActive = Request::segment(1) == 'boms' || Request::segment(1) == 'production' ||
                                    in_array(Request::route()->getName(), ['bom.index','bom.create','bom.edit','bom.show','production.index','production.create','production.edit','production.show']);
                    @endphp
                    <li class="dash-item dash-hasmenu {{ $mfgActive ? 'active dash-trigger' : '' }}">
                        
                        <a href="#!"
                            class="group flex items-center gap-3 rounded-lg px-3 py-2.5 transition-all
                                    {{ $mfgActive 
                                        ? 'bg-[#007C38] text-[#007C38] shadow-sm' 
                                        : 'text-slate-600 hover:bg-[#007C38]/10 hover:text-[#007C38]' }}">
                            <span class="flex h-8 w-8 items-center justify-center rounded-md
                                        {{ $mfgActive ? 'bg-white/15' : 'bg-slate-100 group-hover:bg-[#007C38]/10' }}">
                            <img src="{{ asset('web-assets/dashboard/icons/inventory.svg') }}" alt="staff" class="h-4 w-4">
                            </span>
                            <span class="font-medium flex-1">{{ __('Manufacturing') }}</span>
                            <svg class="h-4 w-4 transition-transform {{ $mfgActive ? 'rotate-90 text-[#007C38]' : 'text-slate-400 group-hover:text-[#007C38]' }}" 
                                fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                        
                        <ul id="mfg-sub" class="dash-submenu {{ $mfgActive ? 'show' : '' }} pl-2 space-y-1">
                        @can('manage bom')
                            @php $subActive = in_array(Request::route()->getName(), ['bom.index','bom.create','bom.edit','bom.show']); @endphp
                            <li class="dash-item {{ $subActive ? 'active' : '' }}">
                            @if($featManufacturing)
                                <a href="{{ route('bom.index') }}"
                                class="group ml-10 flex items-center gap-3 rounded-lg px-3 py-2.5 transition-all
                                        {{ $subActive ? 'bg-[#007C38] text-[#007C38] shadow-sm' : 'text-slate-600 hover:bg-[#007C38]/10 hover:text-[#007C38]' }}">
                                {{ __('BOMs') }}
                                </a>
                            @else
                                <a href="javascript:void(0)"
                                class="flex items-center gap-2 ml-[30px] px-3 py-2 rounded text-gray-500 nav-locked"
                                title="{{ __('Upgrade required for Manufacturing') }}">
                                {{ __('BOMs') }} <i class="ti ti-lock"></i>
                                </a>
                            @endif
                            </li>
                        @endcan

                        @can('manage production')
                            @php $subActive = in_array(Request::route()->getName(), ['production.index','production.create','production.edit','production.show']); @endphp
                            <li class="dash-item {{ $subActive ? 'active' : '' }}">
                            @if($featManufacturing)
                                <a href="{{ route('production.index') }}"
                                class="group ml-7 flex items-center gap-3 rounded-lg px-3 py-2.5 transition-all
                                        {{ $subActive ? 'bg-[#007C38] text-[#007C38] shadow-sm' : 'text-slate-600 hover:bg-[#007C38]/10 hover:text-[#007C38]' }}">
                                <i class="ti ti-workflow"></i>
                                <span>{{ __('Production Orders') }}</span>
                                </a>
                            @else
                                <a href="javascript:void(0)"
                                class="flex items-center gap-2 ml-[30px] px-3 py-2 rounded text-gray-500 nav-locked"
                                title="{{ __('Upgrade required for Manufacturing') }}">
                                <i class="ti ti-workflow"></i>
                                <span>{{ __('Production Orders') }}</span> <i class="ti ti-lock"></i>
                                </a>
                            @endif
                            </li>
                        @endcan
                        </ul>
                    </li>
                    @endif


                {{-- ------- Presale ---------- --}}
               <!--  @if (Gate::check('manage proposal') || Gate::check('manage retainer'))
                    <li class="dash-item dash-hasmenu {{ Request::segment(1) == 'proposal' || Request::segment(1) == 'retainer' || in_array(Request::route()->getName(), ['proposal.index', 'proposal.create', 'proposal.edit', 'proposal.show', 'retainer.index', 'retainer.create', 'retainer.edit', 'retainer.show']) ? ' active dash-trigger' : '' }}">
                        <a href="#!"
                           class="flex mb-2 items-center gap-2 px-2 py-2 rounded hover:bg-[#007C380F] text-gray-700 hover:font-semibold hover:text-[#007C38]">
                            <img src="{{ asset('web-assets/dashboard/icons/presale.svg') }}" alt="presale">
                            <span>{{ __('Presale') }}</span>
                            <span class="dash-arrow"><i data-feather="chevron-right"></i></span>
                        </a>
                        <ul class="dash-submenu {{ Request::segment(1) == 'proposal' || Request::segment(1) == 'retainer' ? 'show' : '' }}">
                            @can('manage proposal')
                                <li class="dash-item {{ in_array(Request::route()->getName(), ['proposal.index', 'proposal.create', 'proposal.edit', 'proposal.show']) ? 'font-semibold active' : '' }}">
                                    <a class="flex items-center gap-2 px-2 py-2 rounded hover:bg-[#007C380F] text-gray-700 hover:font-semibold hover:text-[#007C38] ml-[30px]"
                                       href="{{ route('proposal.index') }}">{{ __('Proposal') }}</a>
                                </li>
                            @endcan
                            @can('manage retainer')
                                <li class="dash-item {{ in_array(Request::route()->getName(), ['retainer.index', 'retainer.create', 'retainer.edit', 'retainer.show']) ? 'font-semibold active' : '' }}">
                                    <a class="flex items-center gap-2 px-2 py-2 rounded hover:bg-[#007C380F] text-gray-700 hover:font-semibold hover:text-[#007C38] ml-[30px]"
                                       href="{{ route('retainer.index') }}">{{ __('Retainers') }}</a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endif

 -->

       {{-- ===== Financial Management ===== --}}
                @if (!Auth::guard('customer')->check() && !Auth::guard('vender')->check())
                <div class="mt-6 my-3 px-3">
                    <h3 class="flex items-center gap-2 text-xs font-semibold tracking-wide text-slate-600 uppercase">
                    <span class="inline-flex h-6 w-6 items-center justify-center rounded-md bg-[#007C38]/10">
                        <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M3 12h18M12 3v18" />
                        </svg>
                    </span>
                    <span>Financial Management</span>
                    </h3>
                    <div class="mt-2 h-[3px] w-full rounded bg-gradient-to-r from-[#007C38] via-[#26A269] to-transparent"></div>
                </div>
                @endif

                {{-- Banking --}}
                @if (Gate::check('manage bank account') || Gate::check('manage transfer'))
                @php
                    $bankingActive = Request::segment(1) == 'bank-account' || Request::segment(1) == 'transfer' ||
                    in_array(Request::route()->getName(), [
                        'bank-account.index','bank-account.create','bank-account.edit',
                        'transfer.index','transfer.create','transfer.edit'
                    ]);
                @endphp
                <li class="dash-item dash-hasmenu {{ $bankingActive ? 'active dash-trigger' : '' }}">
                    <a href="#!"
                    class="group flex items-center gap-3 rounded-lg px-3 py-2.5 transition-all
                            {{ $bankingActive ? 'bg-[#007C38] text-[#007C38] shadow-sm'
                                                : 'text-slate-600 hover:bg-[#007C38]/10 hover:text-[#007C38]' }}">
                    <span class="flex h-8 w-8 items-center justify-center rounded-md
                                {{ $bankingActive ? 'bg-white/15' : 'bg-slate-100 group-hover:bg-[#007C38]/10' }}">
                        <img src="{{ asset('web-assets/dashboard/icons/banking.svg') }}" alt="banking" class="h-4 w-4">
                    </span>
                    <span class="font-medium flex-1">{{ __('Banking') }}</span>
                    <svg class="h-4 w-4 transition-transform {{ $bankingActive ? 'rotate-90 text-[#007C38]' : 'text-slate-400 group-hover:text-[#007C38]' }}"
                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M9 5l7 7-7 7" />
                    </svg>
                    </a>
                    <ul class="dash-submenu {{ $bankingActive ? 'show' : '' }} pl-2 space-y-1">
                    @can('manage bank account')
                        @php $subActive = in_array(Request::route()->getName(), ['bank-account.index','bank-account.create','bank-account.edit']); @endphp
                        <li class="dash-item {{ $subActive ? 'active' : '' }}">
                        <a href="{{ route('bank-account.index') }}"
                            class="group ml-8 flex items-center gap-3 rounded-lg px-3 py-2.5 transition-all
                                    {{ $subActive ? 'bg-[#007C38] text-[#007C38] shadow-sm'
                                                : 'text-slate-700 hover:bg-[#007C38]/10 hover:text-[#007C38]' }}">
                            {{ __('Account') }}
                        </a>
                        </li>
                    @endcan
                    @can('manage transfer')
                        @php $subActive = in_array(Request::route()->getName(), ['transfer.index','transfer.create','transfer.edit']); @endphp
                        <li class="dash-item {{ $subActive ? 'active' : '' }}">
                        <a href="{{ route('transfer.index') }}"
                            class="group ml-8 flex items-center gap-3 rounded-lg px-3 py-2.5 transition-all
                                    {{ $subActive ? 'bg-[#007C38] text-[#007C38] shadow-sm'
                                                : 'text-slate-700 hover:bg-[#007C38]/10 hover:text-[#007C38]' }}">
                            {{ __('Transfer') }}
                        </a>
                        </li>
                    @endcan
                    </ul>
                </li>
                @endif

                {{-- Income --}}
                @if (Gate::check('manage invoice') || Gate::check('manage revenue') || Gate::check('manage credit note'))
                @php
                    $incomeActive = Request::segment(1) == 'invoice' || Request::segment(1) == 'revenue' || Request::segment(1) == 'credit-note' ||
                    in_array(Request::route()->getName(), [
                        'invoice.index','invoice.create','invoice.edit','invoice.show',
                        'revenue.index','revenue.create','revenue.edit','credit.note'
                    ]);
                @endphp
                <li class="dash-item dash-hasmenu {{ $incomeActive ? 'active dash-trigger' : '' }}">
                    <a href="#!"
                    class="group flex items-center gap-3 rounded-lg px-3 py-2.5 transition-all
                            {{ $incomeActive ? 'bg-[#007C38] text-[#007C38] shadow-sm'
                                            : 'text-slate-600 hover:bg-[#007C38]/10 hover:text-[#007C38]' }}">
                    <span class="flex h-8 w-8 items-center justify-center rounded-md
                                {{ $incomeActive ? 'bg-white/15' : 'bg-slate-100 group-hover:bg-[#007C38]/10' }}">
                        <span class="ti ti-currency-naira text-base"></span>
                    </span>
                    <span class="font-medium flex-1">{{ __('Income') }}</span>
                    <svg class="h-4 w-4 transition-transform {{ $incomeActive ? 'rotate-90 text-[#007C38]' : 'text-slate-400 group-hover:text-[#007C38]' }}"
                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M9 5l7 7-7 7" />
                    </svg>
                    </a>
                    <ul class="dash-submenu {{ $incomeActive ? 'show' : '' }} pl-2 space-y-1">
                    @can('manage invoice')
                        @php $subActive = in_array(Request::route()->getName(), ['invoice.index','invoice.create','invoice.edit','invoice.show']); @endphp
                        <li class="dash-item {{ $subActive ? 'active' : '' }}">
                        <a href="{{ route('invoice.index') }}"
                            class="group ml-8 flex items-center gap-3 rounded-lg px-3 py-2.5 transition-all
                                    {{ $subActive ? 'bg-[#007C38] text-[#007C38] shadow-sm'
                                                : 'text-slate-700 hover:bg-[#007C38]/10 hover:text-[#007C38]' }}">
                            {{ __('Invoice') }}
                        </a>
                        </li>
                    @endcan
                    @can('manage revenue')
                        @php $subActive = in_array(Request::route()->getName(), ['revenue.index','revenue.create','revenue.edit']); @endphp
                        <li class="dash-item {{ $subActive ? 'active' : '' }}">
                        <a href="{{ route('revenue.index') }}"
                            class="group ml-8 flex items-center gap-3 rounded-lg px-3 py-2.5 transition-all
                                    {{ $subActive ? 'bg-[#007C38] text-[#007C38] shadow-sm'
                                                : 'text-slate-700 hover:bg-[#007C38]/10 hover:text-[#007C38]' }}">
                            {{ __('Revenue') }}
                        </a>
                        </li>
                    @endcan
                    @can('manage credit note')
                        @php $subActive = Request::route()->getName() == 'credit.note'; @endphp
                        <li class="dash-item {{ $subActive ? 'active' : '' }}">
                        <a href="{{ route('credit.note') }}"
                            class="group ml-8 flex items-center gap-3 rounded-lg px-3 py-2.5 transition-all
                                    {{ $subActive ? 'bg-[#007C38] text-[#007C38] shadow-sm'
                                                : 'text-slate-700 hover:bg-[#007C38]/10 hover:text-[#007C38]' }}">
                            {{ __('Credit Note') }}
                        </a>
                        </li>
                    @endcan
                    </ul>
                </li>
                @endif

                {{-- Expense --}}
                @if (Gate::check('manage bill') || Gate::check('manage payment') || Gate::check('manage debit note'))
                @php
                    $expenseActive = Request::segment(1) == 'bill' || Request::segment(1) == 'payment' || Request::segment(1) == 'debit-note' ||
                    in_array(Request::route()->getName(), ['bill.index','bill.create','bill.edit','bill.show','payment.index','payment.create','payment.edit','debit.note']);
                @endphp
                <li class="dash-item dash-hasmenu {{ $expenseActive ? 'active dash-trigger' : '' }}">
                    <a href="#!"
                    class="group flex items-center gap-3 rounded-lg px-3 py-2.5 transition-all
                            {{ $expenseActive ? 'bg-[#007C38] text-[#007C38] shadow-sm'
                                                : 'text-slate-600 hover:bg-[#007C38]/10 hover:text-[#007C38]' }}">
                    <span class="flex h-8 w-8 items-center justify-center rounded-md
                                {{ $expenseActive ? 'bg-white/15' : 'bg-slate-100 group-hover:bg-[#007C38]/10' }}">
                        <img src="{{ asset('web-assets/dashboard/icons/expense.svg') }}" alt="expense" class="h-4 w-4">
                    </span>
                    <span class="font-medium flex-1">{{ __('Expense') }}</span>
                    <svg class="h-4 w-4 transition-transform {{ $expenseActive ? 'rotate-90 text-[#007C38]' : 'text-slate-400 group-hover:text-[#007C38]' }}"
                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M9 5l7 7-7 7" />
                    </svg>
                    </a>
                    <ul class="dash-submenu {{ $expenseActive ? 'show' : '' }} pl-2 space-y-1">
                    @can('manage bill')
                        @php $subActive = in_array(Request::route()->getName(), ['bill.index','bill.create','bill.edit','bill.show']); @endphp
                        <li class="dash-item {{ $subActive ? 'active' : '' }}">
                        <a href="{{ route('bill.index') }}"
                            class="group ml-8 flex items-center gap-3 rounded-lg px-3 py-2.5 transition-all
                                    {{ $subActive ? 'bg-[#007C38] text-[#007C38] shadow-sm'
                                                : 'text-slate-700 hover:bg-[#007C38]/10 hover:text-[#007C38]' }}">
                            {{ __('Bill') }}
                        </a>
                        </li>
                    @endcan
                    @can('manage payment')
                        @php $subActive = in_array(Request::route()->getName(), ['payment.index','payment.create','payment.edit']); @endphp
                        <li class="dash-item {{ $subActive ? 'active' : '' }}">
                        <a href="{{ route('payment.index') }}"
                            class="group ml-8 flex items-center gap-3 rounded-lg px-3 py-2.5 transition-all
                                    {{ $subActive ? 'bg-[#007C38] text-[#007C38] shadow-sm'
                                                : 'text-slate-700 hover:bg-[#007C38]/10 hover:text-[#007C38]' }}">
                            {{ __('Payment') }}
                        </a>
                        </li>
                    @endcan
                    @can('manage debit note')
                        @php $subActive = Request::route()->getName() == 'debit.note'; @endphp
                        <li class="dash-item {{ $subActive ? 'active' : '' }}">
                        <a href="{{ route('debit.note') }}"
                            class="group ml-8 flex items-center gap-3 rounded-lg px-3 py-2.5 transition-all
                                    {{ $subActive ? 'bg-[#007C38] text-[#007C38] shadow-sm'
                                                : 'text-slate-700 hover:bg-[#007C38]/10 hover:text-[#007C38]' }}">
                            {{ __('Debit Note') }}
                        </a>
                        </li>
                    @endcan
                    </ul>
                </li>
                @endif

                {{-- Double Entry --}}
                @if (Gate::check('manage chart of account') || Gate::check('manage journal entry') || Gate::check('balance sheet report') || Gate::check('ledger report') || Gate::check('trial balance report'))
                @php
                    $doubleActive =
                    Request::segment(1) == 'chart-of-account' || Request::segment(1) == 'journal-entry' ||
                    Request::segment(2) == 'ledger' || Request::segment(2) == 'balance-sheet' || Request::segment(2) == 'trial-balance' ||
                    in_array(Request::route()->getName(), ['chart-of-account.index','journal-entry.index','journal-entry.show','report.ledger','report.balance.sheet','trial.balance']);
                @endphp
                <li class="dash-item dash-hasmenu {{ $doubleActive ? 'active dash-trigger' : '' }}">
                    <a href="#!"
                    class="group flex items-center gap-3 rounded-lg px-3 py-2.5 transition-all
                            {{ $doubleActive ? 'bg-[#007C38] text-[#007C38] shadow-sm'
                                            : 'text-slate-600 hover:bg-[#007C38]/10 hover:text-[#007C38]' }}">
                    <span class="flex h-8 w-8 items-center justify-center rounded-md
                                {{ $doubleActive ? 'bg-white/15' : 'bg-slate-100 group-hover:bg-[#007C38]/10' }}">
                        <img src="{{ asset('web-assets/dashboard/icons/double-entry.svg') }}" alt="double-entry" class="h-4 w-4">
                    </span>
                    <span class="font-medium flex-1">{{ __('Double Entry') }}</span>
                    <svg class="h-4 w-4 transition-transform {{ $doubleActive ? 'rotate-90 text-[#007C38]' : 'text-slate-400 group-hover:text-[#007C38]' }}"
                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M9 5l7 7-7 7" />
                    </svg>
                    </a>
                    <ul class="dash-submenu {{ $doubleActive ? 'show' : '' }} pl-2 space-y-1">
                    @can('manage chart of account')
                        @php $subActive = in_array(Request::route()->getName(), ['chart-of-account.index']); @endphp
                        <li class="dash-item {{ $subActive ? 'active' : '' }}">
                        <a href="{{ route('chart-of-account.index') }}"
                            class="group ml-8 flex items-center gap-3 rounded-lg px-3 py-2.5 transition-all
                                    {{ $subActive ? 'bg-[#007C38] text-[#007C38] shadow-sm'
                                                : 'text-slate-700 hover:bg-[#007C38]/10 hover:text-[#007C38]' }}">
                            {{ __('Chart of Accounts') }}
                        </a>
                        </li>
                    @endcan
                    @can('manage journal entry')
                        @php $subActive = in_array(Request::route()->getName(), ['journal-entry.index','journal-entry.show']); @endphp
                        <li class="dash-item {{ $subActive ? 'active' : '' }}">
                        <a href="{{ route('journal-entry.index') }}"
                            class="group ml-8 flex items-center gap-3 rounded-lg px-3 py-2.5 transition-all
                                    {{ $subActive ? 'bg-[#007C38] text-[#007C38] shadow-sm'
                                                : 'text-slate-700 hover:bg-[#007C38]/10 hover:text-[#007C38]' }}">
                            {{ __('Journal Account') }}
                        </a>
                        </li>
                    @endcan
                    @can('ledger report')
                        @php $subActive = in_array(Request::route()->getName(), ['report.ledger']); @endphp
                        <li class="dash-item {{ $subActive ? 'active' : '' }}">
                        <a href="{{ route('report.ledger') }}"
                            class="group ml-8 flex items-center gap-3 rounded-lg px-3 py-2.5 transition-all
                                    {{ $subActive ? 'bg-[#007C38] text-[#007C38] shadow-sm'
                                                : 'text-slate-700 hover:bg-[#007C38]/10 hover:text-[#007C38]' }}">
                            {{ __('Ledger Summary') }}
                        </a>
                        </li>
                    @endcan
                    @can('balance sheet report')
                        @php $subActive = in_array(Request::route()->getName(), ['report.balance.sheet']); @endphp
                        <li class="dash-item {{ $subActive ? 'active' : '' }}">
                        <a href="{{ route('report.balance.sheet') }}"
                            class="group ml-8 flex items-center gap-3 rounded-lg px-3 py-2.5 transition-all
                                    {{ $subActive ? 'bg-[#007C38] text-[#007C38] shadow-sm'
                                                : 'text-slate-700 hover:bg-[#007C38]/10 hover:text-[#007C38]' }}">
                            {{ __('Balance Sheet') }}
                        </a>
                        </li>
                    @endcan
                    @can('trial balance report')
                        @php $subActive = in_array(Request::route()->getName(), ['trial.balance']); @endphp
                        <li class="dash-item {{ $subActive ? 'active' : '' }}">
                        <a href="{{ route('trial.balance') }}"
                            class="group ml-8 flex items-center gap-3 rounded-lg px-3 py-2.5 transition-all
                                    {{ $subActive ? 'bg-[#007C38] text-[#007C38] shadow-sm'
                                                : 'text-slate-700 hover:bg-[#007C38]/10 hover:text-[#007C38]' }}">
                            {{ __('Trial Balance') }}
                        </a>
                        </li>
                    @endcan
                    </ul>
                </li>
                @endif

                {{-- Report --}}
                @if (Gate::check('income report') || Gate::check('expense report') || Gate::check('income vs expense report') || Gate::check('tax report') || Gate::check('loss & profit report') || Gate::check('invoice report') || Gate::check('bill report') || Gate::check('manage transaction') || Gate::check('stock report') || Gate::check('statement report') || Gate::check('balance sheet report'))
                @php
                    $reportActive =
                    (Request::segment(1) == 'report' || Request::segment(1) == 'transaction') &&
                    Request::segment(2) != 'ledger' && Request::segment(2) != 'trial-balance'
                    || in_array(Request::route()->getName(), [
                        'transaction.index','transfer.create','transaction.edit',
                        'report.account.statement','report.income.summary','report.expense.summary',
                        'report.income.vs.expense.summary','report.tax.summary','report.monthly.cashflow',
                        'report.invoice.summary','report.bill.summary','report.product.stock.report',
                        'report.balance.sheet','report.profit.loss'
                        ]);
                @endphp
                <li class="dash-item dash-hasmenu {{ $reportActive ? 'active dash-trigger' : '' }}">
                    <a href="#!"
                    class="group flex items-center gap-3 rounded-lg px-3 py-2.5 transition-all
                            {{ $reportActive ? 'bg-[#007C38] text-[#007C38] shadow-sm'
                                            : 'text-slate-600 hover:bg-[#007C38]/10 hover:text-[#007C38]' }}">
                    <span class="flex h-8 w-8 items-center justify-center rounded-md
                                {{ $reportActive ? 'bg-white/15' : 'bg-slate-100 group-hover:bg-[#007C38]/10' }}">
                        <img src="{{ asset('web-assets/dashboard/icons/report.svg') }}" alt="report" class="h-4 w-4">
                    </span>
                    <span class="font-medium flex-1">{{ __('Report') }}</span>
                    <svg class="h-4 w-4 transition-transform {{ $reportActive ? 'rotate-90 text-[#007C38]' : 'text-slate-400 group-hover:text-[#007C38]' }}"
                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M9 5l7 7-7 7" />
                    </svg>
                    </a>

                    <ul class="dash-submenu {{ $reportActive ? 'show' : '' }} pl-2 space-y-1">
                    @can('manage transaction')
                        @php $subActive = in_array(Request::route()->getName(), ['transaction.index','transfer.create','transaction.edit']); @endphp
                        <li class="dash-item {{ $subActive ? 'active' : '' }}">
                        <a href="{{ route('transaction.index') }}"
                            class="group ml-8 flex items-center gap-3 rounded-lg px-3 py-2.5 transition-all
                                    {{ $subActive ? 'bg-[#007C38] text-[#007C38] shadow-sm'
                                                : 'text-slate-700 hover:bg-[#007C38]/10 hover:text-[#007C38]' }}">
                            {{ __('Transaction') }}
                        </a>
                        </li>
                    @endcan
                    @can('statement report')
                        @php $subActive = in_array(Request::route()->getName(), ['report.account.statement']); @endphp
                        <li class="dash-item {{ $subActive ? 'active' : '' }}">
                        <a href="{{ route('report.account.statement') }}"
                            class="group ml-8 flex items-center gap-3 rounded-lg px-3 py-2.5 transition-all
                                    {{ $subActive ? 'bg-[#007C38] text-[#007C38] shadow-sm'
                                                : 'text-slate-700 hover:bg-[#007C38]/10 hover:text-[#007C38]' }}">
                            {{ __('Account Statement') }}
                        </a>
                        </li>
                    @endcan
                    @can('income report')
                        @php $subActive = in_array(Request::route()->getName(), ['report.income.summary']); @endphp
                        <li class="dash-item {{ $subActive ? 'active' : '' }}">
                        <a href="{{ route('report.income.summary') }}"
                            class="group ml-8 flex items-center gap-3 rounded-lg px-3 py-2.5 transition-all
                                    {{ $subActive ? 'bg-[#007C38] text-[#007C38] shadow-sm'
                                                : 'text-slate-700 hover:bg-[#007C38]/10 hover:text-[#007C38]' }}">
                            {{ __('Income Summary') }}
                        </a>
                        </li>
                    @endcan
                    @can('expense report')
                        @php $subActive = in_array(Request::route()->getName(), ['report.expense.summary']); @endphp
                        <li class="dash-item {{ $subActive ? 'active' : '' }}">
                        <a href="{{ route('report.expense.summary') }}"
                            class="group ml-8 flex items-center gap-3 rounded-lg px-3 py-2.5 transition-all
                                    {{ $subActive ? 'bg-[#007C38] text-[#007C38] shadow-sm'
                                                : 'text-slate-700 hover:bg-[#007C38]/10 hover:text-[#007C38]' }}">
                            {{ __('Expense Summary') }}
                        </a>
                        </li>
                    @endcan
                    @can('income vs expense report')
                        @php $subActive = in_array(Request::route()->getName(), ['report.income.vs.expense.summary']); @endphp
                        <li class="dash-item {{ $subActive ? 'active' : '' }}">
                        <a href="{{ route('report.income.vs.expense.summary') }}"
                            class="group ml-8 flex items-center gap-3 rounded-lg px-3 py-2.5 transition-all
                                    {{ $subActive ? 'bg-[#007C38] text-[#007C38] shadow-sm'
                                                : 'text-slate-700 hover:bg-[#007C38]/10 hover:text-[#007C38]' }}">
                            {{ __('Income VS Expense') }}
                        </a>
                        </li>
                    @endcan
                    @can('tax report')
                        @php $subActive = in_array(Request::route()->getName(), ['report.tax.summary']); @endphp
                        <li class="dash-item {{ $subActive ? 'active' : '' }}">
                        @if($featTax)
                            <a href="{{ route('report.tax.summary') }}"
                            class="group ml-8 flex items-center gap-3 rounded-lg px-3 py-2.5 transition-all
                                    {{ $subActive ? 'bg-[#007C38] text-[#007C38] shadow-sm'
                                                    : 'text-slate-700 hover:bg-[#007C38]/10 hover:text-[#007C38]' }}">
                            {{ __('Tax Summary') }}
                            </a>
                        @else
                            <a href="javascript:void(0)"
                            class="flex items-center gap-2 ml-8 px-3 py-2 rounded text-gray-500 nav-locked"
                            title="{{ __('Upgrade required for Tax Management') }}">
                            {{ __('Tax Summary') }} <i class="ti ti-lock"></i>
                            </a>
                        @endif
                        </li>
                    @endcan
                    @can('income vs expense report')
                        @php $subActive = in_array(Request::route()->getName(), ['report.profit.loss']); @endphp
                        <li class="dash-item {{ $subActive ? 'active' : '' }}">
                        <a href="{{ route('report.profit.loss') }}"
                            class="group ml-8 flex items-center gap-3 rounded-lg px-3 py-2.5 transition-all
                                    {{ $subActive ? 'bg-[#007C38] text-[#007C38] shadow-sm'
                                                : 'text-slate-700 hover:bg-[#007C38]/10 hover:text-[#007C38]' }}">
                            {{ __('Profit & Loss') }}
                        </a>
                        </li>
                    @endcan
                    @can('balance sheet report')
                        @php $subActive = in_array(Request::route()->getName(), ['report.balance.sheet']); @endphp
                        <li class="dash-item {{ $subActive ? 'active' : '' }}">
                        <a href="{{ route('report.balance.sheet') }}"
                            class="group ml-8 flex items-center gap-3 rounded-lg px-3 py-2.5 transition-all
                                    {{ $subActive ? 'bg-[#007C38] text-[#007C38] shadow-sm'
                                                : 'text-slate-700 hover:bg-[#007C38]/10 hover:text-[#007C38]' }}">
                            {{ __('Balance Sheet') }}
                        </a>
                        </li>
                    @endcan
                    @can('loss & profit report')
                        @php $subActive = in_array(Request::route()->getName(), ['report.monthly.cashflow']); @endphp
                        <li class="dash-item {{ $subActive ? 'active' : '' }}">
                        <a href="{{ route('report.monthly.cashflow') }}"
                            class="group ml-8 flex items-center gap-3 rounded-lg px-3 py-2.5 transition-all
                                    {{ $subActive ? 'bg-[#007C38] text-[#007C38] shadow-sm'
                                                : 'text-slate-700 hover:bg-[#007C38]/10 hover:text-[#007C38]' }}">
                            {{ __('Cash Flow') }}
                        </a>
                        </li>
                    @endcan
                    @can('invoice report')
                        @php $subActive = in_array(Request::route()->getName(), ['report.invoice.summary']); @endphp
                        <li class="dash-item {{ $subActive ? 'active' : '' }}">
                        <a href="{{ route('report.invoice.summary') }}"
                            class="group ml-8 flex items-center gap-3 rounded-lg px-3 py-2.5 transition-all
                                    {{ $subActive ? 'bg-[#007C38] text-[#007C38] shadow-sm'
                                                : 'text-slate-700 hover:bg-[#007C38]/10 hover:text-[#007C38]' }}">
                            {{ __('Invoice Summary') }}
                        </a>
                        </li>
                    @endcan
                    @can('bill report')
                        @php $subActive = in_array(Request::route()->getName(), ['report.bill.summary']); @endphp
                        <li class="dash-item {{ $subActive ? 'active' : '' }}">
                        <a href="{{ route('report.bill.summary') }}"
                            class="group ml-8 flex items-center gap-3 rounded-lg px-3 py-2.5 transition-all
                                    {{ $subActive ? 'bg-[#007C38] text-[#007C38] shadow-sm'
                                                : 'text-slate-700 hover:bg-[#007C38]/10 hover:text-[#007C38]' }}">
                            {{ __('Bill Summary') }}
                        </a>
                        </li>
                    @endcan
                    @can('stock report')
                        @php $subActive = in_array(Request::route()->getName(), ['report.product.stock.report']); @endphp
                        <li class="dash-item {{ $subActive ? 'active' : '' }}">
                        <a href="{{ route('report.product.stock.report') }}"
                            class="group ml-8 flex items-center gap-3 rounded-lg px-3 py-2.5 transition-all
                                    {{ $subActive ? 'bg-[#007C38] text-[#007C38] shadow-sm'
                                                : 'text-slate-700 hover:bg-[#007C38]/10 hover:text-[#007C38]' }}">
                            {{ __('Product Stock') }}
                        </a>
                        </li>
                    @endcan
                    @if($featPayroll)
                        @php $subActive = Request::route()->getName() == 'report.payroll'; @endphp
                        <li class="dash-item {{ $subActive ? 'active' : '' }}">
                        <a href="{{ route('report.payroll') }}"
                            class="group ml-8 flex items-center gap-3 rounded-lg px-3 py-2.5 transition-all
                                    {{ $subActive ? 'bg-[#007C38] text-[#007C38] shadow-sm'
                                                : 'text-slate-700 hover:bg-[#007C38]/10 hover:text-[#007C38]' }}">
                            {{ __('Payroll Summary') }}
                        </a>
                        </li>
                    @else
                        <li class="dash-item">
                        <a href="javascript:void(0)"
                            class="flex items-center gap-2 ml-8 px-3 py-2 rounded text-gray-500 nav-locked"
                            title="{{ __('Upgrade required for Payroll') }}">
                            {{ __('Payroll Summary') }} <i class="ti ti-lock"></i>
                        </a>
                        </li>
                    @endif
                    </ul>
                </li>
                @endif

                {{-- Assets --}}
                @if (Gate::check('manage assets'))
                @php $isActive = Request::segment(1) == 'account-assets'; @endphp
                <li class="dash-item {{ $isActive ? 'active' : '' }}">
                    <a href="{{ route('account-assets.index') }}"
                    class="group flex items-center gap-3 rounded-lg px-3 py-2.5 transition-all
                            {{ $isActive ? 'bg-[#007C38] text-[#007C38] shadow-sm'
                                        : 'text-slate-600 hover:bg-[#007C38]/10 hover:text-[#007C38]' }}">
                    <span class="flex h-8 w-8 items-center justify-center rounded-md
                                {{ $isActive ? 'bg-white/15' : 'bg-slate-100 group-hover:bg-[#007C38]/10' }}">
                        <img src="{{ asset('web-assets/dashboard/icons/assets.svg') }}" alt="assets" class="h-4 w-4">
                    </span>
                    <span class="font-medium flex-1">{{ __('Assets') }}</span>
                    </a>
                </li>
                @endif

                {{-- Goal --}}
                @if (Gate::check('manage goal'))
                @php $isActive = Request::segment(1) == 'goal'; @endphp
                <li class="dash-item {{ $isActive ? 'active' : '' }}">
                    <a href="{{ route('goal.index') }}"
                    class="group flex items-center gap-3 rounded-lg px-3 py-2.5 transition-all
                            {{ $isActive ? 'bg-[#007C38] text-[#007C38] shadow-sm'
                                        : 'text-slate-600 hover:bg-[#007C38]/10 hover:text-[#007C38]' }}">
                    <span class="flex h-8 w-8 items-center justify-center rounded-md
                                {{ $isActive ? 'bg-white/15' : 'bg-slate-100 group-hover:bg-[#007C38]/10' }}">
                        <img src="{{ asset('web-assets/dashboard/icons/goal.svg') }}" alt="goal" class="h-4 w-4">
                    </span>
                    <span class="font-medium flex-1">{{ __('Goal') }}</span>
                    </a>
                </li>
                @endif


                {{-- ===== Payroll Management ===== --}}
                @if (!Auth::guard('customer')->check() && !Auth::guard('vender')->check())
                <div class="mt-6 my-3 px-3">
                    <h3 class="flex items-center gap-2 text-xs font-semibold tracking-wide text-slate-600 uppercase">
                    <span class="inline-flex h-6 w-6 items-center justify-center rounded-md bg-[#007C38]/10">
                        <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M4 7h16M4 12h16M4 17h10" />
                        </svg>
                    </span>
                    <span>Payroll Management</span>
                    </h3>
                    <div class="mt-2 h-[3px] w-full rounded bg-gradient-to-r from-[#007C38] via-[#26A269] to-transparent"></div>
                </div>
                @endif

                @php
                $payrollSegments = [
                    'employee','payslip','setsalary',
                    'document','branch','department','designation',
                    'paysliptype','allowanceoption','loanoption','deductionoption',
                ];
                $currentSeg1 = Request::segment(1);
                $currentRoute = optional(Request::route())->getName();

                $isPayrollActive =
                    in_array($currentSeg1, $payrollSegments, true)
                    || in_array($currentRoute, ['employee.index','payslip.index','setsalary.index'], true);

                $isPayrollCoreOpen = in_array($currentSeg1, ['employee','payslip','setsalary'], true);

                $canSeePayroll =
                    Gate::check('manage employee') ||
                    Gate::check('manage set salary') ||
                    Gate::check('manage pay slip') ||
                    Gate::check('manage branch') ||
                    Gate::check('manage department') ||
                    Gate::check('manage designation') ||
                    Gate::check('manage payslip type') ||
                    Gate::check('create document type') ||
                    Gate::check('create allowance option') ||
                    Gate::check('create loan option') ||
                    Gate::check('create deduction option');
                @endphp

                @if ($canSeePayroll)
                <li class="dash-item dash-hasmenu {{ $isPayrollActive ? 'active dash-trigger' : '' }}">
                    <a href="#!"
                    class="group flex items-center gap-3 rounded-lg px-3 py-2.5 transition-all
                            {{ $isPayrollActive ? 'bg-[#007C38]/10 text-[#007C38]'
                                                : 'text-slate-600 hover:bg-[#007C38]/10 hover:text-[#007C38]' }}">
                    <span class="flex h-8 w-8 items-center justify-center rounded-md bg-slate-100 group-hover:bg-[#007C38]/10">
                        <img src="{{ asset('web-assets/dashboard/icons/payroll.svg') }}" alt="payroll" class="h-4 w-4">
                    </span>
                    <span class="font-medium flex-1">{{ __('Payroll') }}</span>
                    <svg class="h-4 w-4 transition-transform {{ $isPayrollActive ? 'rotate-90' : '' }} {{ $isPayrollActive ? 'text-[#007C38]' : 'text-slate-400 group-hover:text-[#007C38]' }}"
                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M9 5l7 7-7 7" />
                    </svg>
                    </a>

                    <ul class="dash-submenu {{ $isPayrollCoreOpen ? 'show' : '' }} pl-2 space-y-1">
                    @can('manage employee')
                        @php $subActive = $currentRoute === 'employee.index'; @endphp
                        <li class="dash-item {{ $subActive ? 'active' : '' }}">
                        @if($featPayroll)
                            <a href="{{ route('employee.index') }}"
                            class="group ml-8 flex items-center gap-3 rounded-lg px-3 py-2.5 transition-all
                                    {{ $subActive ? 'bg-[#007C38]/10 text-[#007C38]'
                                                    : 'text-slate-700 hover:bg-[#007C38]/10 hover:text-[#007C38]' }}">
                            {{ __('Employees') }}
                            </a>
                        @else
                            <a href="javascript:void(0)"
                            class="flex items-center gap-2 ml-8 px-3 py-2 rounded text-gray-500 nav-locked"
                            title="{{ __('Upgrade required for Payroll') }}">
                            {{ __('Employees') }} <i class="ti ti-lock"></i>
                            </a>
                        @endif
                        </li>
                    @endcan

                    @can('manage set salary')
                        @php $subActive = $currentRoute === 'setsalary.index'; @endphp
                        <li class="dash-item {{ $subActive ? 'active' : '' }}">
                        @if($featPayroll)
                            <a href="{{ route('setsalary.index') }}"
                            class="group ml-8 flex items-center gap-3 rounded-lg px-3 py-2.5 transition-all
                                    {{ $subActive ? 'bg-[#007C38]/10 text-[#007C38]'
                                                    : 'text-slate-700 hover:bg-[#007C38]/10 hover:text-[#007C38]' }}">
                            {{ __('Set Salary') }}
                            </a>
                        @else
                            <a href="javascript:void(0)"
                            class="flex items-center gap-2 ml-8 px-3 py-2 rounded text-gray-500 nav-locked"
                            title="{{ __('Upgrade required for Payroll') }}">
                            {{ __('Set Salary') }} <i class="ti ti-lock"></i>
                            </a>
                        @endif
                        </li>
                    @endcan

                    @can('manage pay slip')
                        @php $subActive = $currentRoute === 'payslip.index'; @endphp
                        <li class="dash-item {{ $subActive ? 'active' : '' }}">
                        @if($featPayroll)
                            <a href="{{ route('payslip.index') }}"
                            class="group ml-8 flex items-center gap-3 rounded-lg px-3 py-2.5 transition-all
                                    {{ $subActive ? 'bg-[#007C38]/10 text-[#007C38]'
                                                    : 'text-slate-700 hover:bg-[#007C38]/10 hover:text-[#007C38]' }}">
                            {{ __('Payslip') }}
                            </a>
                        @else
                            <a href="javascript:void(0)"
                            class="flex items-center gap-2 ml-8 px-3 py-2 rounded text-gray-500 nav-locked"
                            title="{{ __('Upgrade required for Payroll') }}">
                            {{ __('Payslip') }} <i class="ti ti-lock"></i>
                            </a>
                        @endif
                        </li>
                    @endcan

                    @if(
                        Gate::check('manage branch') || Gate::check('manage department') || Gate::check('manage designation') ||
                        Gate::check('manage payslip type') || Gate::check('create document type') ||
                        Gate::check('create allowance option') || Gate::check('create loan option') || Gate::check('create deduction option')
                    )
                        @php
                        $setupActive = in_array($currentSeg1, ['document','branch','department','designation','paysliptype','allowanceoption','loanoption','deductionoption'], true);
                        @endphp
                        <li class="dash-item {{ $setupActive ? 'active' : '' }}">
                        @if($featPayroll)
                            <a href="{{ route('branch.index') }}"
                            class="group ml-8 flex items-center gap-3 rounded-lg px-3 py-2.5 transition-all
                                    {{ $setupActive ? 'bg-[#007C38]/10 text-[#007C38]'
                                                    : 'text-slate-700 hover:bg-[#007C38]/10 hover:text-[#007C38]' }}">
                            {{ __('Payroll Setup') }}
                            </a>
                        @else
                            <a href="javascript:void(0)"
                            class="flex items-center gap-2 ml-8 px-3 py-2 rounded text-gray-500 nav-locked"
                            title="{{ __('Upgrade required for Payroll') }}">
                            {{ __('Payroll Setup') }} <i class="ti ti-lock"></i>
                            </a>
                        @endif
                        </li>
                    @endif
                    </ul>
                </li>
                @endif

                {{-- ===== Admin Extras ===== --}}

                @if (\Auth::user()->type == 'super admin')
                @php $isActive = request()->is('plan_request*'); @endphp
                <li class="dash-item {{ $isActive ? 'active' : '' }}">
                    <a href="{{ route('plan_request.index') }}"
                    class="group flex items-center gap-3 rounded-lg px-3 py-2.5 transition-all
                            {{ $isActive ? 'bg-[#007C38]/10 text-[#007C38]'
                                        : 'text-slate-600 hover:bg-[#007C38]/10 hover:text-[#007C38]' }}">
                    <span class="flex h-8 w-8 items-center justify-center rounded-md bg-slate-100 group-hover:bg-[#007C38]/10">
                        <img src="{{ asset('web-assets/dashboard/icons/plan_request.svg') }}" alt="plan_request" class="h-4 w-4">
                    </span>
                    <span class="font-medium flex-1">{{ __('Plan Request') }}</span>
                    </a>
                </li>
                @endif

                @if (Gate::check('manage coupon'))
                @php $isActive = Request::segment(1) == 'coupons'; @endphp
                <li class="dash-item {{ $isActive ? 'active' : '' }}">
                    <a href="{{ route('coupons.index') }}"
                    class="group flex items-center gap-3 rounded-lg px-3 py-2.5 transition-all
                            {{ $isActive ? 'bg-[#007C38]/10 text-[#007C38]'
                                        : 'text-slate-600 hover:bg-[#007C38]/10 hover:text-[#007C38]' }}">
                    <span class="flex h-8 w-8 items-center justify-center rounded-md bg-slate-100 group-hover:bg-[#007C38]/10">
                        <img src="{{ asset('web-assets/dashboard/icons/coupon.svg') }}" alt="coupon" class="h-4 w-4">
                    </span>
                    <span class="font-medium flex-1">{{ __('Coupon') }}</span>
                    </a>
                </li>
                @endif

                @if (\Auth::user()->type == 'super admin')
                @php $isActive = Request::segment(1) == 'email_template_lang'; @endphp
                <li class="dash-item {{ $isActive ? 'active' : '' }}">
                    <a href="{{ route('email_template.index') }}"
                    class="group flex items-center gap-3 rounded-lg px-3 py-2.5 transition-all
                            {{ $isActive ? 'bg-[#007C38]/10 text-[#007C38]'
                                        : 'text-slate-600 hover:bg-[#007C38]/10 hover:text-[#007C38]' }}">
                    <span class="flex h-8 w-8 items-center justify-center rounded-md bg-slate-100 group-hover:bg-[#007C38]/10">
                        <img src="{{ asset('web-assets/dashboard/icons/email.svg') }}" alt="email" class="h-4 w-4">
                    </span>
                    <span class="font-medium flex-1">{{ __('Email Template') }}</span>
                    </a>
                </li>
                @endif


                {{-- (Payroll Templates moved into Payroll section above) --}}

       @if (!Auth::guard('customer')->check() && !Auth::guard('vender')->check())
       <div class="mt-6 my-3 px-3">
        <h3 class="flex items-center gap-2 text-xs font-semibold tracking-wide text-slate-600 uppercase">
          <span class="inline-flex h-6 w-6 items-center justify-center rounded-md bg-[#007C38]/10">
            <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M12 15.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7Z" />
              <path d="M19.4 15a1.8 1.8 0 0 0 .36 1.98l.05.05a2 2 0 1 1-2.83 2.83l-.05-.05A1.8 1.8 0 0 0 15 19.4a1.8 1.8 0 0 0-1 .33 1.8 1.8 0 0 0-.9 1.57V22a2 2 0 1 1-4 0v-.7a1.8 1.8 0 0 0-.9-1.57 1.8 1.8 0 0 0-1-.33 1.8 1.8 0 0 0-1.98.36l-.05.05a2 2 0 1 1-2.83-2.83l.05-.05A1.8 1.8 0 0 0 4.6 15a1.8 1.8 0 0 0-.33-1 1.8 1.8 0 0 0-1.57-.9H2a2 2 0 1 1 0-4h.7a1.8 1.8 0 0 0 1.57-.9 1.8 1.8 0 0 0 .33-1A1.8 1.8 0 0 0 3.24 4.2l-.05-.05A2 2 0 1 1 6.02 1.3l.05.05A1.8 1.8 0 0 0 8 1.67c.36 0 .7.1 1 .33.57.4.9 1.04.9 1.72V4a2 2 0 1 1 4 0v-.28c0-.68.33-1.32.9-1.72.3-.22.64-.33 1-.33.66 0 1.3.26 1.95.95l.05.05A2 2 0 1 1 21.7 6.2l-.05.05c-.69.65-.95 1.29-.95 1.95 0 .36.1.7.33 1 .4.57 1.04.9 1.72.9H22a2 2 0 1 1 0 4h-.28c-.68 0-1.32.33-1.72.9-.22.3-.33.64-.33 1Z" />
            </svg>
          </span>
          <span>System & Settings</span>
        </h3>
        <div class="mt-2 h-[3px] w-full rounded bg-gradient-to-r from-[#007C38] via-[#26A269] to-transparent"></div>
      </div>        @endif

                {{-- ------- Email Notification ---------- --}}

<!--                 @if (\Auth::user()->type == 'company')
                    <li class="dash-item {{ Request::segment(1) == 'Notifications' ? 'active' : '' }}">
                        <a href="{{ route('notification-templates.index') }}"
                           class="flex items-center gap-2 px-2 py-2 rounded hover:bg-[#007C380F] text-gray-700 hover:font-semibold hover:text-[#007C38]">
                            <img src="{{ asset('web-assets/dashboard/icons/notification.svg') }}"
                                 alt="notification">
                            <span>{{ __('Notification Template') }}</span>
                        </a>
                    </li>
                @endif -->
{{-- ------- Constants (hub link; stays active in any constants subpage) ---------- --}}
@if (Gate::check('manage constant tax') ||
     Gate::check('manage constant category') ||
     Gate::check('manage constant unit') ||
     Gate::check('manage constant payment method') ||
     Gate::check('manage constant custom field') ||
     Gate::check('manage constant contract type') ||
     Gate::check('manage constant chart of account'))

    @php
        $isConstantsArea =
            Request::routeIs('constants.index') ||
            Request::segment(1) === 'taxes' ||
            Request::segment(1) === 'product-category' ||
            Request::segment(1) === 'product-unit' ||
            Request::segment(1) === 'payment-method' ||
            Request::segment(1) === 'custom-field' ||
            Request::segment(1) === 'chart-of-account-type' ||
            in_array(Request::route()->getName(), [
                // indexes
                'taxes.index','product-category.index','product-unit.index','payment-method.index',
                'custom-field.index','contractType.index','chart-of-account-type.index',
                // common creates/edits (keep if you use them)
                'taxes.create','taxes.edit',
                'product-category.create','product-category.edit',
                'product-unit.create','product-unit.edit',
                'payment-method.create','payment-method.edit',
                'custom-field.create','custom-field.edit',
                'contractType.create','contractType.edit',
                'chart-of-account-type.create','chart-of-account-type.edit',
            ]);
    @endphp

<li class="dash-item {{ $isConstantsArea ? 'active' : '' }}">
    <a href="{{ route('constants.index') }}"
       class="group flex items-center gap-3 rounded-lg px-3 py-2.5 transition-all
              {{ $isConstantsArea ? 'bg-[#007C38]/10 text-[#007C38]'
                                  : 'text-slate-600 hover:bg-[#007C38]/10 hover:text-[#007C38]' }}">
      <span class="flex h-8 w-8 items-center justify-center rounded-md bg-slate-100 group-hover:bg-[#007C38]/10">
        <img src="{{ asset('web-assets/dashboard/icons/constant.svg') }}" alt="constant" class="h-4 w-4">
      </span>
      <span class="font-medium flex-1">{{ __('Account Setup') }}</span>
    </a>
  </li>@endif
                {{-- ------- Plan ---------- --}}
                @if (Gate::check('manage plan'))
                <li class="dash-item {{ $isActive ? 'active' : '' }}">
                    <a href="{{ route('plans.index') }}"
                       class="group flex items-center gap-3 rounded-lg px-3 py-2.5 transition-all
                              {{ $isActive ? 'bg-[#007C38]/10 text-[#007C38]'
                                           : 'text-slate-600 hover:bg-[#007C38]/10 hover:text-[#007C38]' }}">
                      <span class="flex h-8 w-8 items-center justify-center rounded-md bg-slate-100 group-hover:bg-[#007C38]/10">
                        <img src="{{ asset('web-assets/dashboard/icons/plan.svg') }}" alt="plan" class="h-4 w-4">
                      </span>
                      <span class="font-medium flex-1">{{ __('Subscription Plan') }}</span>
                    </a>
                  </li>                @endif
                  {{-- ------- Order ---------- --}}
                @if (Gate::check('manage order'))
                <li class="dash-item {{ $isActive ? 'active' : '' }}">
                    <a href="{{ route('order.index') }}"
                       class="group flex items-center gap-3 rounded-lg px-3 py-2.5 transition-all
                              {{ $isActive ? 'bg-[#007C38]/10 text-[#007C38]'
                                           : 'text-slate-600 hover:bg-[#007C38]/10 hover:text-[#007C38]' }}">
                      <span class="flex h-8 w-8 items-center justify-center rounded-md bg-slate-100 group-hover:bg-[#007C38]/10">
                        <img src="{{ asset('web-assets/dashboard/icons/order.svg') }}" alt="order" class="h-4 w-4">
                      </span>
                      <span class="font-medium flex-1">{{ __('Subscription Order History') }}</span>
                    </a>
                  </li>                @endif

                    @if (\Auth::user()->type == 'company')
                    @if (Gate::check('manage company settings'))
                    <li class="dash-item {{ $isActive ? 'active' : '' }}">
                        <a href="{{ route('company.setting') }}"
                           class="group flex items-center gap-3 rounded-lg px-3 py-2.5 transition-all
                                  {{ $isActive ? 'bg-[#007C38]/10 text-[#007C38]'
                                               : 'text-slate-600 hover:bg-[#007C38]/10 hover:text-[#007C38]' }}">
                          <span class="flex h-8 w-8 items-center justify-center rounded-md bg-slate-100 group-hover:bg-[#007C38]/10">
                            <img src="{{ asset('web-assets/dashboard/icons/setting.svg') }}" alt="setting" class="h-4 w-4">
                          </span>
                          <span class="font-medium flex-1">{{ __('System Setting') }}</span>
                        </a>
                      </li>                    @endif
                @endif
            {{-- User Logs (gated by AUDIT) --}}
              <li class="dash-item {{ in_array(Request::route()->getName(), ['userlogs.index', 'userlogs.show']) ? 'font-semibold active' : '' }}">
    @if($featAudit)
    <a href="{{ route('userlogs.index') }}"
       class="group flex items-center gap-3 rounded-lg px-3 py-2.5 transition-all
              {{ $isActive ? 'bg-[#007C38]/10 text-[#007C38]'
                           : 'text-slate-600 hover:bg-[#007C38]/10 hover:text-[#007C38]' }}">
      <span class="flex h-8 w-8 items-center justify-center rounded-md bg-slate-100 group-hover:bg-[#007C38]/10">
        <i class="ti ti-history text-base"></i>
      </span>
      <span class="font-medium flex-1">{{ __('Audit Logs') }}</span>
    </a>    @else
    <a href="javascript:void(0)"
       class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-gray-500 nav-locked"
       title="{{ __('Upgrade required to access Audit Logs') }}">
      <span class="flex h-8 w-8 items-center justify-center rounded-md bg-slate-100">
        <i class="ti ti-history text-base"></i>
      </span>
      <span class="font-medium flex-1">{{ __('Audit Logs') }}</span>
      <i class="ti ti-lock"></i>
    </a>    @endif
</li>
                                @endcan
                {{-- ------- Landing Page ---------- --}}
                @if (\Auth::user()->type == 'super admin')
                <li class="dash-item {{ $isActive ? 'active' : '' }}">
                    <a href="{{ route('landingpage.index') }}"
                       class="group flex items-center gap-3 rounded-lg px-3 py-2.5 transition-all
                              {{ $isActive ? 'bg-[#007C38]/10 text-[#007C38]'
                                           : 'text-slate-600 hover:bg-[#007C38]/10 hover:text-[#007C38]' }}">
                      <span class="flex h-8 w-8 items-center justify-center rounded-md bg-slate-100 group-hover:bg-[#007C38]/10">
                        <img src="{{ asset('web-assets/dashboard/icons/loading-page.svg') }}" alt="landing-page" class="h-4 w-4">
                      </span>
                      <span class="font-medium flex-1">{{ __('Landing Page') }}</span>
                    </a>
                  </li>                @endif
    {{-- ===== Marketing & Growth ===== --}}
@if (!Auth::guard('customer')->check() && !Auth::guard('vender')->check())
<div class="mt-6 my-3 px-3">
  <h3 class="flex items-center gap-2 text-xs font-semibold tracking-wide text-slate-600 uppercase">
    <span class="inline-flex h-6 w-6 items-center justify-center rounded-md bg-[#007C38]/10">
      <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M3 12a9 9 0 1 0 18 0A9 9 0 0 0 3 12Z" />
        <path d="M12 7v5l3 2" />
      </svg>
    </span>
    <span>Marketing & Growth</span>
  </h3>
  <div class="mt-2 h-[3px] w-full rounded bg-gradient-to-r from-[#007C38] via-[#26A269] to-transparent"></div>
</div>
@endif

{{-- Global Referral & Settings --}}
@if (Gate::check('manage system settings'))
@php $isActive = Request::route()->getName() == 'referral-program.index'; @endphp
<li class="dash-item {{ $isActive ? 'active' : '' }}">
  <a href="{{ route('referral-program.index') }}"
     class="group flex items-center gap-3 rounded-lg px-3 py-2.5 transition-all
            {{ $isActive ? 'bg-[#007C38]/10 text-[#007C38]'
                         : 'text-slate-600 hover:bg-[#007C38]/10 hover:text-[#007C38]' }}">
    <span class="flex h-8 w-8 items-center justify-center rounded-md bg-slate-100 group-hover:bg-[#007C38]/10">
      <img src="{{ asset('web-assets/dashboard/icons/referral_program.svg') }}" alt="referral_program" class="h-4 w-4">
    </span>
    <span class="font-medium flex-1">{{ __('Referral Program') }}</span>
  </a>
</li>

@php $isActive = Request::route()->getName() == 'settings.index'; @endphp
<li class="dash-item {{ $isActive ? 'active' : '' }}">
  <a href="{{ route('settings.index') }}"
     class="group flex items-center gap-3 rounded-lg px-3 py-2.5 transition-all
            {{ $isActive ? 'bg-[#007C38]/10 text-[#007C38]'
                         : 'text-slate-600 hover:bg-[#007C38]/10 hover:text-[#007C38]' }}">
    <span class="flex h-8 w-8 items-center justify-center rounded-md bg-slate-100 group-hover:bg-[#007C38]/10">
      <img src="{{ asset('web-assets/dashboard/icons/setting.svg') }}" alt="setting" class="h-4 w-4">
    </span>
    <span class="font-medium flex-1">{{ __('Settings') }}</span>
  </a>
</li>
@endif

{{-- Company Referral --}}
@if (\Auth::user()->type == 'company' && Gate::check('manage company settings'))
@php $isActive = Request::route()->getName() == 'referral-program.company'; @endphp
<li class="dash-item {{ $isActive ? 'active' : '' }}">
  <a href="{{ route('referral-program.company') }}"
     class="group flex items-center gap-3 rounded-lg px-3 py-2.5 transition-all
            {{ $isActive ? 'bg-[#007C38]/10 text-[#007C38]'
                         : 'text-slate-600 hover:bg-[#007C38]/10 hover:text-[#007C38]' }}">
    <span class="flex h-8 w-8 items-center justify-center rounded-md bg-slate-100 group-hover:bg-[#007C38]/10">
      <img src="{{ asset('web-assets/dashboard/icons/referral_program.svg') }}" alt="referral_program" class="h-4 w-4">
    </span>
    <span class="font-medium flex-1">{{ __('Referral Program') }}</span>
  </a>
</li>
@endif

            </ul>
        </div>
    </div>
</nav>
