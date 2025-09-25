<div class="features-inner-part" id="section">
    <div class="features-part">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="see-more">
                        <span id="text-1">See more features</span>
                    </div>
                </div>
                <div class="col-lg-12 inner-main-text">
                    <h3 id="text-2">All Features <span id="text-3">in one place</span></h3>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-3 features-card">
                    <div class="inner-text">
                        <h5 id="text-4">
                        </h5>
                        <p id="text-5">

                        </p>
                    </div>
                </div>
                <div class="col-lg-3 features-card">
                    <div class="inner-text">
                        <p id="text-6">
                        </p>
                    </div>
                </div>
                <div class="col-lg-3 features-card">
                    <div class="inner-text">
                        <p id="text-7">
                        </p>
                    </div>
                </div>
                <div class="col-lg-3 features-card">
                    <div class="inner-text">
                        <p id="text-8">
                        </p>
                    </div>
                </div>
                <div class="features-button col-lg-12"><a href="#" id="button"></a></div>
            </div>
        </div>
    </div>
</div>
<div id="ul-section">
    <ul class="list-group list-group-horizontal tooltip1text" style="z-index: 200;" >
        <li class="list-group-item"><button class="btn btn-default" id="delete"><i class="fa fa-trash"></i></button></li>
        <li class="list-group-item"><button class="btn btn-default" data-toggle="modal" id="setting_btn"><i class="fa fa-cogs"></i></button></li>
        <li class="list-group-item"><button class="btn btn-default" onclick="copySection(this)" id="copy_btn"><i class="fa fa-copy"></i></button></li>
        <li class="list-group-item"><a class="btn btn-default handle"><i class="fa fa-arrows"></i></a></li>
    </ul>
</div>
<div class="modal fade component_modal" tabindex="-1" role="dialog" aria-labelledby="setting-modal-label" style="display: flex; align-items: center; justify-content: center;">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius: 20px; overflow: hidden; box-shadow: 0 4px 24px rgba(0,0,0,0.12);">
            <div style="background: linear-gradient(90deg, #198754 0%, #20c997 100%); padding: 2rem 2rem 1.25rem; position: relative;">
                <div style="color: white; margin-bottom: 0.5rem;">
                    <h4 style="margin: 0; font-weight: 600; font-size: 1.5rem; color: white;">Section Setting</h4>
                    <p style="margin: 0; opacity: 0.9; font-size: 0.875rem;">Customize this section's settings</p>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="position: absolute; top: 1.5rem; right: 2rem; color: white; font-size: 1.5rem; background: none; border: none;">&times;</button>
            </div>
            <div style="padding: 2rem; background: white;">
                <form enctype="multipart/form-data">
                    <div class="form-group" style="margin-bottom: 1.5rem;">
                        <label for="text-1" style="font-weight: 500; margin-bottom: 0.5rem;">Text</label>
                        <input type="text" class="form-control" placeholder="Text-1" id="text-1" name="text_value[]" style="border-radius: 10px; border: 1px solid #20c997; padding: 0.75rem 1rem; margin-bottom: 1rem;">
                        <input type="text" class="form-control" placeholder="Text-2" id="text-2" name="text_value[]" style="border-radius: 10px; border: 1px solid #20c997; padding: 0.75rem 1rem; margin-bottom: 1rem;">
                        <input type="text" class="form-control" placeholder="Text-3" id="text-3" name="text_value[]" style="border-radius: 10px; border: 1px solid #20c997; padding: 0.75rem 1rem; margin-bottom: 1rem;">
                        <textarea class="form-control" rows="5" id="text-4" name="text_value[]" style="border-radius: 10px; border: 1px solid #20c997; padding: 0.75rem 1rem; margin-bottom: 1rem;"></textarea>
                        <textarea class="form-control" rows="3" id="text-5" name="text_value[]" style="border-radius: 10px; border: 1px solid #20c997; padding: 0.75rem 1rem; margin-bottom: 1rem;"></textarea>
                        <textarea class="form-control" rows="5" id="text-6" name="text_value[]" style="border-radius: 10px; border: 1px solid #20c997; padding: 0.75rem 1rem; margin-bottom: 1rem;"></textarea>
                        <textarea class="form-control" rows="3" id="text-7" name="text_value[]" style="border-radius: 10px; border: 1px solid #20c997; padding: 0.75rem 1rem; margin-bottom: 1rem;"></textarea>
                        <textarea class="form-control" rows="3" id="text-8" name="text_value[]" style="border-radius: 10px; border: 1px solid #20c997; padding: 0.75rem 1rem; margin-bottom: 1rem;"></textarea>
                    </div>
                    <div style="display: flex; justify-content: flex-end; gap: 1rem;">
                        <button type="button" class="btn btn-light" data-dismiss="modal" style="border-radius: 8px; padding: 0.75rem 1.5rem; border: 1px solid #e0e7ff;">Cancel</button>
                        <button class="btn btn-success" type="submit" style="background: #198754; color: white; border-radius: 8px; padding: 0.75rem 1.5rem; font-weight: 500;">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <input type="text" class="form-control" placeholder="Text-1" id="text-1" name="text_value[]">
                                    </div>
                                    <div class="form-group">
                                        <input type="text" class="form-control" placeholder="Text-2" id="text-2" name="text_value[]">
                                    </div>
                                    <div class="form-group">
                                        <input type="text" class="form-control" placeholder="Text-3" id="text-3" name="text_value[]">
                                    </div>
                                    <div class="form-group">
                                        <textarea class="form-control" rows="5" id="text-4" name="text_value[]"></textarea>
                                    </div>
                                    <div class="form-group">
                                        <textarea class="form-control" rows="3" id="text-5" name="text_value[]"></textarea>
                                    </div>
                                    <div class="form-group">
                                        <textarea class="form-control" rows="5" id="text-6" name="text_value[]"></textarea>
                                    </div>
                                    <div class="form-group">
                                        <textarea class="form-control" rows="3" id="text-7" name="text_value[]"></textarea>
                                    </div>
                                    <div class="form-group">
                                        <textarea class="form-control" rows="3" id="text-8" name="text_value[]"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card mb-2">
                        <div class="card-header"><h6>Button</h6></div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-12" id="button">
                                    <div class="form-group">
                                        <input type="text" class="form-control" placeholder="Enter button text" name="button[text]">
                                    </div>
                                    <div class="form-group">
                                        <input type="text" class="form-control" placeholder="Enter button link" name="button[href]">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card mb-2">
                        <div class="card-header"><h6>Custom class name</h6></div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <input type="text" class="form-control" placeholder="class name" id="custom_class_name" name="custom_class_name">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" id="save">Save</button>
            </div>
        </div>
    </div>
</div>
