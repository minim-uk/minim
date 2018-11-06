<?php $__env->startSection('title'); ?>
<?php echo e(Session::get('legalBodyName')); ?> Profile
<?php $__env->stopSection(); ?>


<?php $__env->startSection('breadcrumbs'); ?>
                            <li><a href="<?php echo e(url('/')); ?>"><i class="icon-home2 position-left"></i>Dashboard</a></li>
                            <li class="active"><?php echo e(Session::get('legalBodyName')); ?> Profile</li>
<?php $__env->stopSection(); ?>


<?php foreach($legalbodies as $key => $legalbody): ?>

<?php $__env->startSection('content'); ?>
<!-- UPDATE COLLECTION/LEGAL BODY -->
                    <form id="adduser" method="POST" enctype="multipart/form-data" action="<?php echo e(url('/legalbody-profile/store')); ?>" class="steps-validation">
                    
                    <?php echo csrf_field(); ?>

                    
                    <input type="hidden" name="legalBodyOrigImage" value="<?php echo e($legalbody->legalBodyImage); ?>"/>
                    <input type="hidden" name="legalBodyMDAcodeOrig" class="form-control" value="<?php echo e($legalbody->legalBodyMDAcode); ?>"/>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="panel registration-form" style="border:0;">
                                    <div class="panel-body">
                                        <div class="text-center">
                                            <div class="icon-object border-success text-success"><i class="icon-plus3"></i></div>
                                            <h5 class="content-group-lg"><?php echo e($legalbody->legalBodyName); ?> Profile <small class="display-block">All fields are required</small></h5>
                                        </div>
                                    <div class="form-group has-feedback">
                                        <label class="control-label">Legal Body Name</label>
                                        <input disabled type="text" name="legalBodyName" class="form-control" placeholder="Legal Body Name" value="<?php echo e($legalbody->legalBodyName); ?>">
                                    </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group has-feedback">
                                                <label class="control-label">Legal Body Short Name</label>
                                                    <input disabled type="text" name="legalBodyShortName" class="form-control" placeholder="Legal Body Short Name" value="<?php echo e($legalbody->legalBodyShortName); ?>">
                                                    <div class="form-control-feedback">
                                                        <!--<i class="icon-user-check text-muted"></i>-->
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group has-feedback">
                                                <label class="control-label">Legal Body MDA Code</label>
                                                    <input disabled type="text" name="legalBodyMDAcode" class="form-control" placeholder="Legal Body MDA Code" value="<?php echo e($legalbody->legalBodyMDAcode); ?>">
                                                    <div class="form-control-feedback">
                                                        <!--<i class="icon-user-check text-muted"></i>-->
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group has-feedback">
                                                <label class="control-label">Legal Body Website</label>
                                                    <input type="text" name="legalBodyWebsite" class="form-control" placeholder="Legal Body Website" value="<?php echo e($legalbody->legalBodyWebsite); ?>">
                                                    <div class="form-control-feedback">
                                                        <!--<i class="icon-user-check text-muted"></i>-->
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group has-feedback">
                                                <label class="control-label">Legal Body Default Repository</label>
                                                    <input type="text" name="legalBodyDefaultRepository" class="form-control" placeholder="Legal Body Default Repository" value="<?php echo e($legalbody->legalBodyDefaultRepository); ?>">
                                                    <div class="form-control-feedback">
                                                        <!--<i class="icon-user-check text-muted"></i>-->
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                           <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group has-feedback">
                                                <label class="control-label">Longitude</label>
                                                    <input type="text" name="longitude" class="form-control" placeholder="Longitude" value="<?php echo e($legalbody->longitude); ?>">
                                                    <div class="form-control-feedback">
                                                        <!--<i class="icon-user-check text-muted"></i>-->
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group has-feedback">
                                                <label class="control-label">Latitude</label>
                                                    <input type="text" name="latitude" class="form-control" placeholder="Latitude" value="<?php echo e($legalbody->latitude); ?>">
                                                    <div class="form-control-feedback">
                                                        <!--<i class="icon-user-check text-muted"></i>-->
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

<?php if($legalbody->latitude && $legalbody->longitude): ?>

                            <!-- Map Preview -->
                            <div class="panel panel-flat">
                                <div class="panel-heading">
                                    <h6 class="panel-title">Map Preview</h6>
                                </div>
                                <div class="panel-body">
                                    <style>
                                        #map {
                                        height: 100%;
                                        min-width:200px;
                                        min-height:200px;
                                      }
                                    </style>
                                    <div id="map"></div>
                                    <script>
                                      function initMap() {
                                        var myLatLng = {lat: <?php echo e($legalbody->latitude); ?>, lng: <?php echo e($legalbody->longitude); ?>};

                                        var map = new google.maps.Map(document.getElementById('map'), {
                                          zoom: 14,
                                          center: myLatLng
                                        });

                                        var marker = new google.maps.Marker({
                                          position: myLatLng,
                                          map: map,
                                          title: '<?php echo e($legalbody->legalBodyName); ?>'
                                        });
                                      }
                                    </script>
                                    <script async defer
                                    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyA4pbIjBNKdBGmGB8rqMInNvyuM0VJPvQI&callback=initMap">
                                    </script>

                                </div>
                            </div>
                            <!-- /Map Preview -->
<?php endif; ?>
                                        <div class="form-group has-feedback">
                                            <label class="control-label">Main Image:</label>
                                            <div class="col-lg-12">
                                                <input name="legalBodyImage" type="file" class="file-input-preview" data-show-remove="true">
                                                <!--<span class="help-block">lorum ipsum</span> -->
                                                <span class="help-block">Please upload an image. <code>jpg, gif, png</code> accepted.</span><br/>
                                            </div>
                                        </div>
                                        <div class="form-group has-feedback">
                                        <label class="control-label">Legal Body Description</label>
                                            <textarea name="legalBodyDescription" id="editor-full" rows="4" cols="4">
                                               <?php echo e($legalbody->legalBodyDescription); ?>

                                            </textarea>
                                        </div>
                                        <div class="text-right">
                                            <button type="submit" class="btn bg-teal-400 btn-labeled btn-labeled-right ml-10"><b><i class="icon-plus3"></i></b> Update <?php echo e($legalbody->legalBodyName); ?>s Profile</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
<!-- /UPDATE COLLECTION/LEGAL BODY-->
<?php $__env->stopSection(); ?> 
<?php endforeach; ?>
<?php echo $__env->make('layouts.loggedin', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>