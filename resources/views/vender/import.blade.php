{{ Form::open(array('route' => array('vender.import'),'method'=>'post', 'enctype' => "multipart/form-data",'class'=>'needs-validation','novalidate')) }}
<div class="modal-body">

    <div class="row">
        <div class="col-md-12 mb-6">
            {{Form::label('file',__('Download sample vendor CSV file'),['class'=>'form-label'])}}
            <a href="{{asset(Storage::url('uploads/sample')).'/sample-vendor.csv'}}" class="btn btn-sm btn-primary">
                <i class="ti ti-download"></i> {{__('Download')}}
            </a>
        </div>
        <div class="col-md-12">
            {{Form::label('file',__('Select CSV File'),['class'=>'form-label'])}}<x-required></x-required>
            <div class="choose-file form-group">
                <label for="file" class="form-label">
                    <div>{{__('Choose file here')}}</div>
                    <input type="file" class="form-control" name="file" id="file" data-filename="upload_file" required>
                </label>
                <p class="upload_file"></p>
            </div>
        </div>

    </div>
</div>
<div class="modal-footer" style="background: #f8f9fa; border-top: 1px solid #e0e0e0; padding: 1.5rem 2rem; display: flex; justify-content: flex-end; gap: 1rem; border-radius: 0 0 8px 8px;">
    <input type="button" value="{{__('Cancel')}}" class="btn btn-light" data-bs-dismiss="modal" style="border-radius: 8px; padding: 0.75rem 1.5rem; border: 1.5px solid #e0e0e0; color: #2d3748; font-weight: 500; background: #fff;">
    <input type="submit" value="{{__('Upload')}}" class="btn btn-success" style="background: #007c38; color: #fff; border-radius: 8px; padding: 0.75rem 1.5rem; font-weight: 500; border: none;">
</div>
{{ Form::close() }}

