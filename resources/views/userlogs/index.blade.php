@extends('layouts.admin')
@section('page-title')
    {{ __('Users logs') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('Users logs') }}</li>
@endsection

@section('content')
<div class="card border-0 rounded-2xl shadow-md overflow-hidden my-3">
    <div class="h-1 w-full" style="background:#007C38;"></div>
      <div class=" mt-2 " id="multiCollapseExample1" style="">
            {{-- <div class="card"> --}}
            <div class="card-body">
                {{ Form::open(['route' => ['userlogs.index'], 'method' => 'get', 'id' => 'userlogs_filter']) }}
                <div class="row d-flex align-items-center justify-content-start">
                    <div class="col-md-3 col-lg-3 col-md-6 col-sm-12 col-12">
                        <div class="btn-box">
                            {{ Form::label('month', __('Month'), ['class' => 'form-label block text-sm font-medium text-gray-700 mb-2']) }}
                            {{ Form::month('month', isset($_GET['month']) ? $_GET['month'] : date('Y-m'), ['class' => 'form-control block w-full pl-3 pr-3 py-2 border border-[#E5E7EB] rounded-[6px] bg-white text-[14px] placeholder-[#9CA3AF] focus:outline-none focus:ring-1 focus:ring-[#007C38] focus:border-[#007C38] transition-all duration-200']) }}
                        </div>
                    </div>
                    <div class="col-md-3 col-lg-3 col-md-6 col-sm-12 col-12">
                        <div class="btn-box">
                            {{ Form::label('users', __('Users'), ['class' => 'form-label block text-sm font-medium text-gray-700 mb-2']) }}
                            {{ Form::select('user', $usersList, isset($_GET['user']) ? $_GET['user'] : '', ['class' => 'form-control select appearance-none bg-white border border-[#E5E7EB] rounded-[6px] px-3 py-2 pr-8 text-[14px] text-[#374151] focus:outline-none focus:ring-1 focus:ring-[#007C38] focus:border-[#007C38] transition-all duration-200 w-full', 'id' => 'id']) }}
                        </div>
                    </div>
                    <div class="col-md-3 d-flex mt-4">
                        <a href="#" class="btn btn-sm btn-primary me-2"
                            onclick="document.getElementById('userlogs_filter').submit(); return false;"
                            data-bs-toggle="tooltip" title="{{ __('Apply') }}">
                            <span class="btn-inner--icon"><i class="ti ti-search"></i></span>
                        </a>
                        <a href="{{ route('userlogs.index') }}" class="btn btn-sm btn-danger " data-bs-toggle="tooltip"
                            title="{{ __('Reset') }}">
                            <span class="btn-inner--icon"><i class="ti ti-refresh text-white-off"></i></span>
                        </a>
                    </div>
                </div>
                {{ Form::close() }}
            </div>
            {{-- </div> --}}
        </div>
    </div>
    <div class="row table-new-design">
        <div class="col-md-12">
            <div class="card border-0 rounded-2xl shadow-md overflow-hidden my-3">
                <div class="h-1 w-full" style="background:#007C38;"></div>
                      {{-- <div class="card"> --}}
            <div class="card-body table-border-style m-4">
                <h5></h5>
                <div class="table-responsive bg-white">
                    <table class="table datatable min-w-full text-sm text-left  border rounded-lg overflow-x-auto">
                        <thead class="bg-[#F6F6F6] text-[#323232] font-600 uppercase text-[12px] leading-[24px]">
                            <tr>
                                <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600]"> {{ __('User') }}
                                </th>
                                <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600]"> {{ __('Role') }}
                                </th>
                                <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600]"> {{ __('Ip') }}
                                </th>
                                <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600]">
                                    {{ __('Last Login') }}</th>
                                <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600]"> {{ __('Country') }}
                                </th>
                                <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600]">
                                    {{ __('Device Type') }}</th>
                                <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600]"> {{ __('Os') }}
                                </th>
                                <th class="px-4 py-1 font-[600]"> {{ __('Action') }}</th>
                            </tr>
                        </thead>

                        <tbody>

                            @foreach ($logindetails as $logindetail)
                                @php
                                    $details = json_decode($logindetail->details);
                                @endphp
                                @if ($details->status != 'fail')
                                    <tr class="border-b hover:bg-gray-50 font-style">
                                        <td class="px-4 py-3 border border-[#E5E5E5] text-[#323232]">
                                            @php
                                                $user = $logindetail->Getuser(
                                                    $logindetail->type,
                                                    $logindetail->user_id,
                                                );
                                                $name = !empty($user) ? $user->name : '';
                                                $email = !empty($user) ? $user->email : '';
                                                $avatar = !empty($user)
                                                    ? (!empty($user->avatar)
                                                        ? \App\Models\Utility::get_file($user->avatar)
                                                        : asset(Storage::url('uploads/avatar/avatar.png')))
                                                    : asset(Storage::url('uploads/avatar/avatar.png'));
                                            @endphp
                                            <div class="d-flex align-items-start">
                                                <div class="theme-avtar">
                                                    <a href="#">
                                                        <img src="{{ $avatar }}"
                                                            class="img-fluid rounded border-2 border border-primary">
                                                    </a>
                                                </div>
                                                <h6 class="ms-2 mb-0">{{ $name }}</h6>
                                                {{-- <div class="ms-2">
                                                    <h4 class="mb-0">{{ $name }}</h4>
                                                    <p class="text-sm mb-0">{{ $name }}</p>
    
                                                </div> --}}
                                            </div>
                                        </td>
                                        {{-- <td class="text-capitalize">{{ $logindetail->type }}</td> --}}
                                        <td class="px-4 py-3 border border-[#E5E5E5] text-[#323232]">
                                            <span
                                                class="me-5 badge p-2 px-3 text-capitalize fix_badge
                                                @if($logindetail->type == 'vender')
                                                    bg-secondary
                                                @elseif($logindetail->type == 'customer')
                                                    bg-info
                                                @elseif($logindetail->type == 'user')
                                                    bg-primary
                                                @endif
                                                ">{{ $logindetail->type }}</span>
                                        </td>
                                        <td class="px-4 py-3 border border-[#E5E5E5] text-[#323232]">{{ $logindetail->ip }}
                                        </td>
                                        <td class="px-4 py-3 border border-[#E5E5E5] text-[#323232]">
                                            {{ $logindetail->date }}</td>
                                        <td class="px-4 py-3 border border-[#E5E5E5] text-[#323232]">
                                            {{ $details->country }}</td>
                                        <td class="px-4 py-3 border border-[#E5E5E5] text-[#323232]">
                                            {{ $details->device_type }}</td>
                                        <td class="px-4 py-3 border border-[#E5E5E5] text-[#323232]">
                                            {{ $details->os_name }}</td>
                                        <td class="Action px-4 py-3 border border-[#E5E5E5] text-[#323232]">
                                            <button class="w-100 text-gray-400 hover:text-gray-600 cursor-pointer"
                                                type="button" data-bs-toggle="dropdown" aria-haspopup="true"
                                                aria-expanded="false">
                                                <i class="ti ti-dots-vertical"></i>
                                            </button>
                                            <div
                                                class="dropdown-menu dropdown-menu-end mt-0 w-[190px] bg-white border rounded-md shadow-lg text-sm p-0">
                                                <li>
                                                    <a href="#"
                                                        class="dropdown-item flex items-center text-[#323232] gap-2 w-full px-4 py-2 text-left hover:bg-[#007C3812] text-sm"
                                                        data-bs-toggle="modal" data-size="lg" data-ajax-popup="true"
                                                        data-url="{{ route('userlogs.show', [$logindetail->id]) }}"
                                                        data-bs-toggle="tooltip" title="{{ __('View') }}"
                                                        data-size="lg">
                                                        <i class="ti ti-eye"></i>
                                                        <span>{{ __('View') }}</span>
                                                    </a>
                                                </li>
                                                <li>
                                                    {!! Form::open([
                                                        'method' => 'DELETE',
                                                        'route' => ['userlogs.destroy', $logindetail->id],
                                                        'id' => 'delete-form-' . $logindetail->id,
                                                    ]) !!}
                                                    <a href="#"
                                                        class="dropdown-item flex items-center text-[#323232] gap-2 w-full px-4 py-2 text-left hover:bg-[#007C3812] text-sm bs-pass-para"
                                                        data-bs-toggle="tooltip" title="{{ __('Delete') }}">
                                                        <i class="ti ti-trash"></i>
                                                        <span>{{ __('Delete') }}</span>
                                                    </a>
                                                    {!! Form::close() !!}
                                                </li>
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            {{-- </div> --}}
        </div>
        </div>
    </div>
@endsection
