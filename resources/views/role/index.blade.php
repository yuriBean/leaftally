@extends('layouts.admin')
@section('page-title')
    {{ __('Manage Roles') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('Role') }}</li>
@endsection


@section('action-btn')
    <div class="float-end">
        @can('create role')
            <a href="#" data-size="xl" data-url="{{ route('roles.create') }}" data-ajax-popup="true" data-bs-toggle="tooltip"
                title="{{ __('Create') }}"
                class="flex items-center gap-2 bg-[#007C38] text-white px-4 py-2 rounded-[6px] text-[14px] font-[500] hover:bg-[#005f2a] transition-all duration-200 shadow-sm min-w-fit">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Create New Role
            </a>
        @endcan
    </div>
@endsection

@section('content')
    <div class="row table-new-data">
        <div class="col-xl-12">
            <div class="card border-0 rounded-2xl shadow-md overflow-hidden my-3">
                <div class="h-1 w-full" style="background:#007C38;"></div>
                          <div class="card-body table-border-style table-border-style">
                <div class="bg-white border rounded-lg shadow-sm p-4 pb-0">
                    <div class="table-responsive">
                        <table class="border border-[#E5E5E5] min-w-full text-sm text-left">
                            <thead>
                                <tr class="bg-[#F6F6F6] text-[#323232] font-600 text-[12px] leading-[24px]">
                                    <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600]">
                                        {{ __('Role') }} </th>
                                    <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600]">
                                        {{ __('Permissions') }} </th>
                                    <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600]" width="150">
                                        {{ __('Action') }} </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($roles as $role)
                                    <tr class="font-style border-b hover:bg-gray-50">
                                        <td style="vertical-align: baseline;"
                                            class="Role px-4 py-3 font-medium text-[#323232] bg-white whitespace-nowrap border border-[#E5E5E5]">
                                            {{ $role->name }}</td>
                                        <td class="Permission px-4 py-3 flex flex-wrap gap-2 bg-white">
                                            @for ($j = 0; $j < count($role->permissions()->pluck('name')); $j++)
                                                <span
                                                    class="bg-green-100 text-[#137051] leading-[14px] text-[12px] font-[500] px-2 py-0.5 rounded-full">{{ $role->permissions()->pluck('name')[$j] }}</span>
                                            @endfor
                                        </td>
                                        <td
                                            class="align-top Action bg-white px-4 py-3 text-right relative border border-[#E5E5E5]">
                                            <button
                                                class="w-100 text-gray-400 hover:text-gray-600 cursor-pointer"
                                                type="button" data-bs-toggle="dropdown" aria-haspopup="true"
                                                aria-expanded="false">
                                                <i class="ti ti-dots-vertical"></i>
                                            </button>
                                            <div
                                                class="dropdown-menu dropdown-menu-end mt-0 w-[190px] bg-white border rounded-md shadow-lg text-sm p-0">
                                                @can('edit role')
                                                    <li>
                                                        <a href="#"
                                                            class="dropdown-item flex items-center text-[#323232] gap-2 w-full px-4 py-2 text-left hover:bg-[#007C3812] text-sm"
                                                            data-url="{{ route('roles.edit', $role->id) }}"
                                                            data-ajax-popup="true" data-size="lg" data-bs-toggle="tooltip"
                                                            title="{{ __('Edit') }}" data-title="{{ __('Edit Product') }}">
                                                            <i class="ti ti-pencil"></i>
                                                            <span>{{ __('Edit') }}</span>
                                                        </a>
                                                    </li>
                                                @endcan
                                                @can('delete role')
                                                    <li>
                                                        {!! Form::open([
                                                            'method' => 'DELETE',
                                                            'route' => ['roles.destroy', $role->id],
                                                            'id' => 'delete-form-' . $role->id,
                                                        ]) !!}
                                                        <a href="#"
                                                            class="dropdown-item flex items-center text-[#323232] gap-2 w-full px-4 py-2 text-left hover:bg-[#007C3812] text-sm bs-pass-para"
                                                            data-bs-toggle="tooltip" title="{{ __('Delete') }}">
                                                            <i class="ti ti-trash"></i>
                                                            <span>{{ __('Delete') }}</span>
                                                        </a>
                                                        {!! Form::close() !!}
                                                    </li>
                                                @endcan
                                            </div>
                                            {{--  </td>  --}}


                                            {{--  <button class="absolute top-3 right-3 text-gray-400 hover:text-gray-600 cursor-pointer" type="button" class="btn " data-bs-toggle="dropdown"
                                                    aria-haspopup="true" aria-expanded="false">
                                                    <i class="ti ti-dots-vertical"></i>
                                                </button>

                                                <div class="dropdown-menu dropdown-menu-end mt-0 w-[190px] bg-white border rounded-md shadow-lg text-sm p-0">

                                                    @can('edit role')
                                                            <a href="#" class="dropdown-item flex text-[#323232] gap-2 w-full px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-left hover:bg-[#007C3812]" data-bs-toggle="tooltip" title="{{__('Edit')}}" data-url="{{ route('roles.edit',$role->id) }}" data-size="xl" data-ajax-popup="true"  data-original-title="{{__('Edit')}}">
                                                            <img src="{{ asset('web-assets/dashboard/icons/action_icons/edit.svg') }}" alt="edit">
                                                            <span>{{ __('Edit') }}</span>
                                                        </a>
                                                    @endcan

                                                    @can('delete role')
                                                        {!! Form::open(['method' => 'DELETE', 'route' => ['roles.destroy', $role->id],'id'=>'delete-form-'.$role->id]) !!}
                                                        <a href="#!" class="dropdown-item bs-pass-para flex text-[#323232] gap-2 w-full px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-left hover:bg-[#007C3812]">
                                                            <img src="{{ asset('web-assets/dashboard/icons/action_icons/delete.svg') }}" alt="edit">
                                                            <span>
                                                                {{ __('Delete') }}
                                                            </span>
                                                        </a>
                                                        {!! Form::close() !!}
                                                    @endcan  --}}



                                            {{-- <span>
                                        @can('edit role')
                                                <div class="action-btn me-2">
                                                    <a href="#" class="mx-3 btn btn-sm d-inline-flex align-items-center bg-warning" data-bs-toggle="tooltip" title="{{__('Edit')}}" data-url="{{ route('roles.edit',$role->id) }}" data-size="xl" data-ajax-popup="true"  data-original-title="{{__('Edit')}}">
                                                       <span> <i class="ti ti-pencil text-white"></i></span>
                                                    </a>
                                                </div>
                                            @endcan

                                        @can('delete role')
                                        <div class="action-btn">
                                            {!! Form::open(['method' => 'DELETE', 'route' => ['roles.destroy', $role->id],'id'=>'delete-form-'.$role->id]) !!}
                                            <a href="#" class="mx-3 btn btn-sm  align-items-center bs-pass-para bg-danger" data-bs-toggle="tooltip" title="{{__('Delete')}}"><i class="ti ti-trash text-white text-white"></i></a>

                                            {!! Form::close() !!}
                                        </div>
                                        @endcan
                                    </span> --}}
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
    </div>
@endsection
