{{ Form::open(array('route' => array('productservice.import'),'method'=>'post', 'enctype' => "multipart/form-data", 'class'=>'needs-validation','novalidate')) }}
<div class="modal-body">
    <div class="row">
        <div class="col-md-12 mb-6">
            {{Form::label('file',__('Download sample product CSV file'),['class'=>'form-label text-[14px] font-[600] leading-[24px] text-[#323232] mt-8'])}}
            <a href="{{asset(Storage::url('uploads/sample')).'/sample-product.csv'}}" class="btn max-w-fit block flex items-center gap-2 bg-[#007C38] hover:bg-[#005f2a] text-white px-4 py-2 rounded-md text-sm font-medium mt-[14px]">
                <i class="ti ti-download"></i> {{__('Download')}}
            </a>
        </div>
        <div class="col-md-12">
            <div class="flex gap-1">
                {{Form::label('file',__('Select CSV File'),['class'=>'form-label block text-[14px] font-[600] text-[#323232] leading-[24px]'])}}<x-required></x-required>
            </div>
            <div class="choose-file form-group">
                <label for="file" class="form-label">
                    <input type="file" class="form-control w-full border border-gray-300 rounded-md  text-sm file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:bg-[#007C38] file:text-white hover:file:bg-[#005f2a]" name="file" id="file" data-filename="upload_file" required>
                </label>
                <p class="upload_file"></p>
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <input type="button" value="{{__('Cancel')}}" class="btn py-[6px] px-[10px] btn text-[#007C38] border-[#007C38] hover:bg-[#007C38] hover:text-white" data-bs-dismiss="modal">
    <input type="submit" value="{{__('Upload')}}" class="btn py-[6px] px-[10px] btn bg-[#007C38] text-white hover:bg-[#005f2a]">
</div>
{{Form::close()}}
