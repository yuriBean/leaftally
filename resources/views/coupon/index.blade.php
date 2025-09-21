@extends('layouts.admin')
@push('script-page')
    <script>
        $(document).on('click', '.code', function () {
            var type = $(this).val();
            if (type == 'manual') {
                $('#manual').removeClass('d-none');
                $('#manual').addClass('d-block');
                $('#auto').removeClass('d-block');
                $('#auto').addClass('d-none');
            } else {
                $('#auto').removeClass('d-none');
                $('#auto').addClass('d-block');
                $('#manual').removeClass('d-block');
                $('#manual').addClass('d-none');
            }
        });

        $(document).on('click', '#code-generate', function () {
            var length = 10;
            var result = '';
            var characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
            var charactersLength = characters.length;
            for (var i = 0; i < length; i++) {
                result += characters.charAt(Math.floor(Math.random() * charactersLength));
            }
            $('#auto-code').val(result);
        });
    </script>
@endpush

@section('page-title')
    {{__('Manage Coupon')}}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    <li class="breadcrumb-item">{{__('Coupon')}}</li>
@endsection


@section('action-btn')
    @can('create coupon')
        <div class="float-end">
                <a href="#" data-url="{{ route('coupons.create') }}" data-ajax-popup="true" data-title="{{__('Create Coupon')}}"  data-bs-toggle="tooltip" title="{{__('Create')}}" class="btn btn-sm btn-primary">
                    <i class="ti ti-plus"></i>
                    {{__('Create')}}
                </a>
            </div>
    @endcan
@endsection

@section('content')

    <div class="row">
        <div class="col-sm-12">
            
            <div class="bg-white border border-[#E5E5E5] rounded-[8px] p-3">
                <div class="table-responsive">
                    <table class="table datatable">
                        <thead>
                        <tr>
                            <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600]"> {{__('Name')}}</th>
                            <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600]"> {{__('Code')}}</th>
                            <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600]"> {{__('Discount (%)')}}</th>
                            <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600]"> {{__('Limit')}}</th>
                            <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600]"> {{__('Used')}}</th>
                            <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600]" width="10%"> {{__('Action')}}</th>
                        </tr>
                        </thead>

                        <tbody>
                        @foreach ($coupons as $coupon)
                            <tr class="font-style">
                                <td class="px-4 py-3 border border-[#E5E5E5] text-[#323232]">{{ $coupon->name }}</td>
                                <td class="px-4 py-3 border border-[#E5E5E5] text-[#323232]">{{ $coupon->code }}</td>
                                <td class="px-4 py-3 border border-[#E5E5E5] text-[#323232]">{{ $coupon->discount }}</td>
                                <td class="px-4 py-3 border border-[#E5E5E5] text-[#323232]">{{ $coupon->limit }}</td>
                                <td class="px-4 py-3 border border-[#E5E5E5] text-[#323232]">{{ $coupon->used_coupon() }}</td>
                                <td class="Action relative px-4 py-3 border border-[#E5E5E5] text-[#323232]">
                                    <button
                                                class="absolute top-3 right-3 text-gray-400 hover:text-gray-600 cursor-pointer"
                                                type="button" data-bs-toggle="dropdown" aria-haspopup="true"
                                                aria-expanded="false">
                                                <i class="ti ti-dots-vertical"></i>
                                            </button>
                                    
                                    
                                    
                                    
                                     <div class="dropdown-menu dropdown-menu-end mt-0 w-[190px] bg-white border rounded-md shadow-lg text-sm p-0">
                                        <a href="{{ route('coupons.show',$coupon->id) }}" data-bs-toggle="tooltip" title="{{__('View')}}" class="dropdown-item flex text-[#323232] gap-2 w-full px-4 py-2 text-left hover:bg-[#007C3812]">
                                           <img src="{{ asset('web-assets/dashboard/icons/preview.svg') }}"
                                                            alt="preview" />
                                                        <span>{{ __('Preview') }}</span>
                                        </a>
                                    @can('edit coupon')
                                        <a href="#" class="dropdown-item flex text-[#323232] gap-2 w-full px-4 py-2 text-left hover:bg-[#007C3812]" data-url="{{ route('coupons.edit',$coupon->id) }}" data-ajax-popup="true" data-title="{{__('Edit Coupon')}}" data-bs-toggle="tooltip" title="{{__('Edit')}}"  data-original-title="{{__('Edit')}}">
                                           <img src="{{ asset('web-assets/dashboard/icons/action_icons/edit.svg') }}"
                                                            alt="edit" />
                                                        <span>{{ __('Edit') }}</span>
                                        </a>
                                        @endcan
                                        @can('delete coupon')
                                        {!! Form::open(['method' => 'DELETE', 'route' => ['coupons.destroy', $coupon->id],'id'=>'delete-form-'.$coupon->id]) !!}
                                            <a href="#" class="dropdown-item bs-pass-para flex text-[#323232] gap-2 w-full px-4 py-2 text-left hover:bg-[#007C3812]"data-bs-toggle="tooltip" title="{{__('Delete')}}" data-original-title="{{__('Delete')}}" data-confirm="{{__('Are You Sure?').'|'.__('This action can not be undone. Do you want to continue?')}}" data-confirm-yes="document.getElementById('delete-form-{{$coupon->id}}').submit();">
                                                <img src="{{ asset('web-assets/dashboard/icons/action_icons/delete.svg') }}"
                                                            alt="delete" />
                                                        <span>{{ __('Delete') }}</span>
                                            </a>
                                        {!! Form::close() !!}
                                        @endcan
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
@endsection
