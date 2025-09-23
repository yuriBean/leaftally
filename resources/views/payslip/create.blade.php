<script src="{{ asset('js/unsaved.js') }}"></script>

@extends('layouts.admin')
@section('content')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>{{__('Employee')}}</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="{{route('home')}}">{{__('Dashboard')}}</a></div>
                    <div class="breadcrumb-item">{{__('Employee')}}</div>
                </div>
            </div>
            <form method="post" action="{{route('employee.store')}}" enctype="multipart/form-data">

                @csrf
                <div class="section-body">
                    <div class="row">
                        <div class="col-md-6 ">
                            <div class="card">
                                <div class="card-header"><h4>{{__('Personal Detail')}}</h4></div>
                                <div class="card-body">

                                    <div class="form-group">
                                        {!! Form::label('name', 'Name') !!}<span class="text-danger pl-1">*</span>
                                        {!! Form::text('name', null, ['class' => 'form-control','required' => 'required', 'placeholder' => __('Enter Name')]) !!}

                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                {!! Form::label('dob', 'Date of Birth') !!}
                                                {!! Form::text('dob', null, ['class' => 'form-control datepicker', 'placeholder' => __('Enter Date of Birth')]) !!}
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                {!! Form::label('gender', 'Gender') !!}<span class="text-danger pl-1">*</span>
                                                <br>
                                                {{ Form::radio('gender', 'Male' , true,['class' => 'mt-2']) }}{{ __('Male') }} &nbsp&nbsp&nbsp
                                                {{ Form::radio('gender', 'Female' , false) }}{{ __('Female') }}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        {!! Form::label('phone', 'Phone') !!}<span class="text-danger pl-1">*</span>
                                        {!! Form::number('phone',null, ['class' => 'form-control', 'placeholder' => 'Enter Phone Number']) !!}
                                    </div>
                                    <div class="form-group">
                                        {!! Form::label('address', 'Address') !!}
                                        {!! Form::textarea('address',null, ['class' => 'form-control', 'placeholder' => 'Enter Address']) !!}
                                    </div>
                                    <div class="form-group">
                                        {!! Form::label('email', 'Email') !!}<span class="text-danger pl-1">*</span>
                                        {!! Form::email('email',null, ['class' => 'form-control','required' => 'required', 'placeholder' => 'Enter Email']) !!}
                                    </div>
                                    <div class="form-group">
                                        {!! Form::label('password', 'Password') !!}<span class="text-danger pl-1">*</span>
                                        {!! Form::text('password',null, ['class' => 'form-control','required' => 'required', 'placeholder' => 'Enter Password']) !!}
                                    </div>

                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 ">
                            <div class="card">
                                <div class="card-header"><h4>{{__('Company Detail')}}</h4></div>
                                <div class="card-body">

                                    @csrf
                                    <div class="form-group">
                                        {!! Form::label('employee_id', 'Employee ID') !!}
                                        {!! Form::text('employee_id', \Illuminate\Support\Facades\Auth::user()->employeeIdFormat(1), ['class' => 'form-control','disabled'=>'disabled', 'placeholder' => 'Enter Employee ID']) !!}
                                    </div>

                                    <div class="form-group">
                                        {{ Form::label('branch_id', __('Branch')) }}
                                        {{ Form::select('branch_id', $branches,null, array('class' => 'form-control select2','required'=>'required', 'placeholder' => 'Select Branch')) }}
                                    </div>

                                    <div class="form-group">
                                        {{ Form::label('department_id', __('Department')) }}
                                        {{ Form::select('department_id', $departments,null, array('class' => 'form-control select2','id'=>'department_id','required'=>'required', 'placeholder' => 'Select Department')) }}
                                    </div>

                                    <div class="form-group">
                                        {{ Form::label('designation_id', __('Designation')) }}
                                        <select class="select2 form-control select2-multiple" id="designation_id" name="designation_id" data-toggle="select2" data-placeholder="{{ __('Select Designation ...') }}">
                                            <option value="">{{__('Select any Designation')}}</option>

                                        </select>
                                    </div>

                                    <div class="form-group">
                                        {!! Form::label('company_doj', 'Company Date Of Joining') !!}
                                        {!! Form::text('company_doj', null, ['class' => 'form-control datepicker','required' => 'required', 'placeholder' => 'Company Date of Joining']) !!}
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 ">
                            <div class="card">
                                <div class="card-header"><h4>{{__('Document')}}</h4></div>
                                <div class="card-body">
                                    @foreach($documents as $key=>$document)

                                        <div class="row">
                                            <div class="form-group col-10">
                                                <div class="float-left">
                                                    <label for="document" class="float-left pt-1">{{ $document->name }} @if($document->is_required == 1) <span class="text-danger">*</span> @endif</label>
                                                </div>
                                                <div class="float-right">
                                                    <input class="form-control float-right @error('document') is-invalid @enderror border-0" @if($document->is_required == 1) required @endif name="document[{{ $document->id}}]" type="file" id="document[{{ $document->id }}]" accept="image

