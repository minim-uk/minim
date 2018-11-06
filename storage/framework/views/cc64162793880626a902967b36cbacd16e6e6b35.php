<?php $__env->startSection('title'); ?>
Add Collection
<?php $__env->stopSection(); ?>




<?php $__env->startSection('breadcrumbs'); ?>
                            <li><a href="<?php echo e(url('/')); ?>"><i class="icon-home2 position-left"></i>Dashboard</a></li>
                            <li class="active">Add Collection</li>
<?php $__env->stopSection(); ?>




<?php $__env->startSection('content'); ?>
        
<?php /*  CATCH FORM ERRORS */ ?>
<?php echo $__env->make('partials/message-block', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>       

<?php /*  ADD COLLECTION FORM */ ?>
                    <form id="adduser" method="POST" enctype="multipart/form-data" action="<?php echo e(url('/add-collection/store')); ?>" class="steps-validation">
                      
                     <?php echo csrf_field(); ?>


                        <div class="row">
                            <div class="col-lg-12">
                                <div class="panel registration-form" style="border:0;">
                                    <div class="panel-body">
                                    <div class="text-center">
                                        <div class="icon-object border-success text-success"><i class="icon-plus3"></i></div>
                                        <h5 class="content-group-lg">Add New Collection<small class="display-block">All fields except co-ordinates and image are required</small></h5>
                                    </div>
                                    <div class="form-group has-feedback <?php echo e($errors->has('legalBodyName') ? 'has-error' : ''); ?>">
                                        <label class="control-label">Legal Body Name</label>
                                        <input type="text" name="legalBodyName" class="form-control" placeholder="Legal Body Name" value="<?php echo e(Request::old('legalBodyName')); ?>">
                                    </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group has-feedback <?php echo e($errors->has('legalBodyShortName') ? 'has-error' : ''); ?>">
                                                <label class="control-label">Legal Body Short Name</label>
                                                    <input type="text" name="legalBodyShortName" class="form-control" placeholder="Legal Body Short Name" value="<?php echo e(Request::old('legalBodyShortName')); ?>">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group has-feedback <?php echo e($errors->has('legalBodyMDAcode') ? 'has-error' : ''); ?>">
                                                <label class="control-label">Legal Body MDA Code</label>
                                                    <input type="text" name="legalBodyMDAcode" class="form-control" placeholder="Legal Body MDA Code" value="<?php echo e(Request::old('legalBodyMDAcode')); ?>">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group has-feedback <?php echo e($errors->has('legalBodyWebsite') ? 'has-error' : ''); ?>">
                                                <label class="control-label">Legal Body Website</label>
                                                    <input type="text" name="legalBodyWebsite" class="form-control" placeholder="Legal Body Website" value="<?php echo e(Request::old('legalBodyWebsite')); ?>">
                                                </div>
                                            </div>
                                        <div class="col-md-6">
                                                <div class="form-group has-feedback <?php echo e($errors->has('legalBodyDefaultRepository') ? 'has-error' : ''); ?>">
                                                <label class="control-label">Legal Body Default Repository</label>
                                                    <input type="text" name="legalBodyDefaultRepository" class="form-control" placeholder="Legal Body Default Repository" value="<?php echo e(Request::old('legalBodyDefaultRepository')); ?>">
                                                </div>
                                            </div>
                                        </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group has-feedback <?php echo e($errors->has('latitude') ? 'has-error' : ''); ?>">
                                        <label class="control-label">Latitude</label>
                                            <input type="text" name="latitude" class="form-control" placeholder="Latitude" value="<?php echo e(Request::old('latitude')); ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group has-feedback <?php echo e($errors->has('longitude') ? 'has-error' : ''); ?>">
                                        <label class="control-label">Longitude</label>
                                            <input type="text" name="longitude" class="form-control" placeholder="Longitude" value="<?php echo e(Request::old('longitude')); ?>">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group has-feedback">
                                    <label class="control-label">Main Image:</label>
                                    <div class="col-lg-12">
                                        <input name="legalBodyImage" type="file" class="file-input-preview" data-show-remove="true">
                                        <span class="help-block">Please upload an image. <code>jpg, gif, png</code> accepted.</span><br/>
                                    </div>
                                </div>
                                <div class="form-group has-feedback <?php echo e($errors->has('legalBodyDescription') ? 'has-error' : ''); ?>">
                                <label class="control-label">Legal Body Description</label>
                                        <textarea name="legalBodyDescription" id="editor-full" rows="4" cols="4">
                                       <?php echo e(Request::old('legalBodyDescription')); ?>

                                        </textarea>
                                </div>
                                        <div class="text-right">
                                            <button type="submit" class="btn bg-teal-400 btn-labeled btn-labeled-right ml-10"><b><i class="icon-plus3"></i></b> Add New Collection</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
<!-- /ADD COLLECTION FORM -->
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('layouts.loggedin', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>