<div class="bg-gredient2 our-system" id="section">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <h3>See our system <span> on images</span></h3>
                <ul class="nav nav-tabs" id="tabs">

                </ul>
            </div>
            <div class="tab-content" id="tab-content">

            </div>
        </div>
    </div>
</div>
<div id="ul-section">
    <ul class="list-group list-group-horizontal tooltip1text" style="z-index:200;padding-top: 100px;">
        <li class="list-group-item"><button class="btn btn-default" id="delete"><i class="fa fa-trash"></i></button></li>
        <li class="list-group-item"><button class="btn btn-default" data-toggle="modal" id="setting_btn"><i class="fa fa-cogs"></i></button></li>
        <li class="list-group-item"><button class="btn btn-default" onclick="copySection(this)" id="copy_btn"><i class="fa fa-copy"></i></button></li>
        <li class="list-group-item"><a class="btn btn-default handle"><i class="fa fa-arrows"></i></a></li>
    </ul>
</div>

<div class="modal fade component_modal" tabindex="-1" role="dialog" aria-labelledby="setting-modal-label" style="display: flex; align-items: center; justify-content: center;">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius: 16px; overflow: hidden; box-shadow: 0 4px 24px rgba(0,0,0,0.12);">
            <div style="background: #007c38; padding: 1.75rem 2rem 1.25rem; position: relative;">
                <div style="color: white; margin-bottom: 0.5rem;">
                    <h4 style="margin: 0; font-weight: 600; font-size: 1.5rem; color: white;">Section Setting</h4>
                    <p style="margin: 0; opacity: 0.9; font-size: 0.875rem;">Customize this section's settings</p>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="position: absolute; top: 1.5rem; right: 2rem; color: white; font-size: 1.5rem; background: none; border: none;">&times;</button>
            </div>
            <div style="padding: 2rem; background: white;">
                <form enctype="multipart/form-data" id="testnomial_add_class_form">
                    <div class="form-group" style="margin-bottom: 1.5rem;">
                        <label for="custom_class_name" style="font-weight: 500; margin-bottom: 0.5rem;">Custom class name</label>
                        <input type="text" class="form-control" placeholder="class name" id="custom_class_name" name="custom_class_name" style="border-radius: 8px; border: 1.5px solid #007c38; padding: 0.75rem 1rem;">
                    </div>
                    <div style="display: flex; justify-content: flex-end; gap: 1rem;">
                        <button type="button" class="btn btn-light" data-dismiss="modal" style="border-radius: 8px; padding: 0.75rem 1.5rem; border: 1.5px solid #e0e0e0; color: #2d3748; font-weight: 500; background: #fff;">Cancel</button>
                        <button class="btn btn-success" type="button" id="add_class_btn" style="background: #007c38; color: white; border-radius: 8px; padding: 0.75rem 1.5rem; font-weight: 500; border: none;">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

