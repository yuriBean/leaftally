{{-- <div class="card sticky-top" style="top:30px">
    <div class="list-group list-group-flush" id="useradd-sidenav">
        <a href="{{route('branch.index')}}" class="list-group-item list-group-item-action border-0 {{ (request()->is('branch*') ? 'active' : '')}}">{{__('Branch')}} <div class="float-end"><i class="ti ti-chevron-right"></i></div></a>

        <a href="{{ route('department.index') }}" class="list-group-item list-group-item-action border-0 {{ (request()->is('department*') ? 'active' : '')}}">{{__('Department')}}<div class="float-end"><i class="ti ti-chevron-right"></i></div></a>

        <a href="{{ route('designation.index') }}" class="list-group-item list-group-item-action border-0 {{ (request()->is('designation*') ? 'active' : '')}}">{{__('Designation')}}<div class="float-end"><i class="ti ti-chevron-right"></i></div></a>

        <a href="{{ route('document.index') }}" class="list-group-item list-group-item-action border-0 {{ (Request::route()->getName() == 'document.index' ? 'active' : '')}}">{{__('Document Type')}}<div class="float-end"><i class="ti ti-chevron-right"></i></div></a>

        <a href="{{ route('paysliptype.index') }}" class="list-group-item list-group-item-action border-0 {{ (request()->is('paysliptype*') ? 'active' : '')}}">{{__('Payslip Type')}}<div class="float-end"><i class="ti ti-chevron-right"></i></div></a>

        <a href="{{ route('allowanceoption.index') }}" class="list-group-item list-group-item-action border-0 {{ (request()->is('allowanceoption*') ? 'active' : '')}}">{{__('Allowance Option')}}<div class="float-end"><i class="ti ti-chevron-right"></i></div></a>

        <a href="{{ route('loanoption.index') }}" class="list-group-item list-group-item-action border-0 {{ (request()->is('loanoption*') ? 'active' : '')}}">{{__('Loan Option')}}<div class="float-end"><i class="ti ti-chevron-right"></i></div></a>

        <a href="{{ route('deductionoption.index') }}" class="list-group-item list-group-item-action border-0 {{ (request()->is('deductionoption*') ? 'active' : '')}}">{{__('Deduction Option')}}<div class="float-end"><i class="ti ti-chevron-right"></i></div></a>

    </div>
</div> --}}

<ul class="nav nav-pills nav-fill information-tab hrm_setup_tab" id="pills-tab" role="tablist">
    @can('manage branch')
        <li class="nav-item" role="presentation">
            <a href="{{ route('branch.index') }}" class="list-group-item list-group-item-action border-0">
                <button class="nav-link {{ Request::route()->getName() == 'branch.index' ? 'active' : '' }}"
                    id="branch-setting-tab" data-bs-toggle="pill" data-bs-target="#branch-setting"
                    type="button">{{ __('Branch') }}</button>
            </a>
        </li>
    @endcan
    @can('manage department')
        <li class="nav-item" role="presentation">
            <a href="{{ route('department.index') }}" class="list-group-item list-group-item-action border-0 ">
                <button class="nav-link {{ Request::route()->getName() == 'department.index' ? 'active' : '' }}"
                    id="department-setting-tab" data-bs-toggle="pill" data-bs-target="#department-setting"
                    type="button">{{ __('Department') }}</button>
            </a>
        </li>
    @endcan
    @can('manage designation')
        <li class="nav-item" role="presentation">
            <a href="{{ route('designation.index') }}" class="list-group-item list-group-item-action border-0">
                <button class="nav-link {{ request()->is('designation*') ? 'active' : '' }}" id="designation-setting-tab"
                    data-bs-toggle="pill" data-bs-target="#designation-setting"
                    type="button">{{ __('Designation') }}</button>
            </a>
        </li>
    @endcan
    @can('manage document type')
        <li class="nav-item" role="presentation">
            <a href="{{ route('document.index') }}" class="list-group-item list-group-item-action border-0">
                <button class="nav-link {{ Request::route()->getName() == 'document.index' ? 'active' : '' }}"
                    id="document-setting-tab" data-bs-toggle="pill" data-bs-target="#document-setting"
                    type="button">{{ __('Document Type') }}</button>
            </a>
        </li>
    @endcan
    @can('manage payslip type')
        <li class="nav-item" role="presentation">
            <a href="{{ route('paysliptype.index') }}" class="list-group-item list-group-item-action border-0">
                <button class="nav-link {{ request()->is('paysliptype*') ? 'active' : '' }} " id="payslip-setting-tab"
                    data-bs-toggle="pill" data-bs-target="#payslip-setting"
                    type="button">{{ __('Payslip Type') }}</button>
            </a>
        </li>
    @endcan
    @can('manage allowance option')
        <li class="nav-item" role="presentation">
            <a href="{{ route('allowanceoption.index') }}" class="list-group-item list-group-item-action border-0">
                <button class="nav-link {{ request()->is('allowanceoption*') ? 'active' : '' }} "
                    id="allowance-setting-tab" data-bs-toggle="pill" data-bs-target="#allowance-setting"
                    type="button">{{ __('Allowance Option') }}</button>
            </a>
        </li>
    @endcan
    @can('manage loan option')
        <li class="nav-item" role="presentation">
            <a href="{{ route('loanoption.index') }}" class="list-group-item list-group-item-action border-0">
                <button class="nav-link {{ request()->is('loanoption*') ? 'active' : '' }} " id="loan-setting-tab"
                    data-bs-toggle="pill" data-bs-target="#loan-setting" type="button">{{ __('Loan Option') }}</button>
            </a>
        </li>
    @endcan
    @can('manage deduction option')
        <li class="nav-item" role="presentation">
            <a href="{{ route('deductionoption.index') }}" class="list-group-item list-group-item-action border-0">
                <button class="nav-link {{ request()->is('deductionoption*') ? 'active' : '' }} "
                    id="deduction-setting-tab" data-bs-toggle="pill" data-bs-target="#deduction-setting"
                    type="button">{{ __('Deduction Option') }}</button>
            </a>
        </li>
    @endcan
</ul>
