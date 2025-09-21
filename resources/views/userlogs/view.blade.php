@php 
    $details = json_decode($details->details);
@endphp
 <div class="data-userlog-view bg-[#FAFBFC] p-6">
<div class="row g-0">
    <div class="col-md-6">
        <div class="userlog-col">
             <div class="form-control-label"><b>{{__('Status')}}</b></div>
        <p class="text-muted">
            {{$details->status}}
        </p>
        </div>
    </div>
    <div class="col-md-6">
        <div class="userlog-col">
            <div class="form-control-label"><b>{{__('Country')}} </b></div>
        <p class="text-muted">
            {{$details->country}}
        </p>
        </div>
    </div>
    <div class="col-md-6">
        <div class="userlog-col">
            <div class="form-control-label"><b>{{__('Country Code')}} </b></div>
        <p class="text-muted">
            {{$details->countryCode}}
        </p>
        </div>
    </div>
    <div class="col-md-6">
        <div class="userlog-col">
            <div class="form-control-label"><b>{{__('Region')}}</b></div>
        <p class="text-muted">{{$details->region}}</p>
        </div>
    </div>
    <div class="col-md-6">
        <div class="userlog-col">
            <div class="form-control-label"><b>{{__('Region Name')}}</b></div>
        <p class="text-muted">{{$details->regionName}}</p>
        </div>
    </div>
    <div class="col-md-6">
        <div class="userlog-col">
            <div class="form-control-label"><b>{{__('City')}}</b></div>
        <p class="text-muted">{{$details->city}}</p>
        </div>
    </div>
    <div class="col-md-6 ">
        <div class="userlog-col">
            <div class="form-control-label"><b>{{__('Zip')}}</b></div>
        <p class="text-muted">{{$details->zip}}</p>
        </div>
    </div>
    <div class="col-md-6 ">
        <div class="userlog-col">
            <div class="form-control-label"><b>{{__('Latitude')}}</b></div>
        <p class="text-muted">{{$details->lat}}</p>
        </div>
    </div>
    <div class="col-md-6 ">
        <div class="userlog-col">
            <div class="form-control-label"><b>{{__('Longitude')}}</b></div>
        <p class="text-muted">{{$details->lon}}</p>
        </div>
    </div>
    <div class="col-md-6 ">
        <div class="userlog-col">
            <div class="form-control-label"><b>{{__('Timezone')}}</b></div>
        <p class="text-muted">{{$details->timezone}}</p>
        </div>
    </div>
    <div class="col-md-6 ">
        <div class="userlog-col">
            <div class="form-control-label"><b>{{__('Isp')}}</b></div>
        <p class="text-muted">{{$details->isp}}</p>
        </div>
    </div>
    <div class="col-md-6 ">
        <div class="userlog-col">
             <div class="form-control-label"><b>{{__('Org')}}</b></div>
        <p class="text-muted">{{$details->org}}</p>
        </div>
    </div>
    <div class="col-md-6 ">
        <div class="userlog-col">
            <div class="form-control-label"><b>{{__('As')}}</b></div>
        <p class="text-muted">{{$details->as}}</p>
        </div>
    </div>
    <div class="col-md-6 ">
        <div class="userlog-col">
             <div class="form-control-label"><b>{{__('Query')}}</b></div>
        <p class="text-muted">{{$details->query}}</p>
        </div>
    </div>
    <div class="col-md-6 ">
        <div class="userlog-col">
             <div class="form-control-label"><b>{{__('Browser Name')}}</b></div>
        <p class="text-muted">{{$details->browser_name}}</p>
        </div>
    </div>
    <div class="col-md-6 ">
        <div class="userlog-col">
            <div class="form-control-label"><b>{{__('Os Name')}}</b></div>
        <p class="text-muted">{{$details->os_name}}</p>
        </div>
    </div>
    <div class="col-md-6 ">
        <div class="userlog-col">
            <div class="form-control-label"><b>{{__('Browser Language')}}</b></div>
        <p class="text-muted">{{$details->browser_language}}</p>
        </div>
    </div>
    <div class="col-md-6 ">
        <div class="userlog-col">
            <div class="form-control-label"><b>{{__('Device Type')}}</b></div>
        <p class="text-muted">{{$details->device_type}}</p>
        </div>
    </div>
    <div class="col-md-6 ">
        <div class="userlog-col">
            <div class="form-control-label"><b>{{__('Referrer Host')}}</b></div>
        <p class="text-muted">{{$details->referrer_host}}</p>
        </div>
    </div>
    <div class="col-md-6 ">
        <div class="userlog-col">
            <div class="form-control-label"><b>{{__('Referrer Path')}}</b></div>
        <p class="text-muted">{{$details->referrer_path}}</p>
        </div>
    </div>
    </div>
</div>
