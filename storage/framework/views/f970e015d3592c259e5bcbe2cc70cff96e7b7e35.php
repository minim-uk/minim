<?php foreach($admin_user_activity as $key => $activity): ?>





<?php $__env->startSection('title'); ?>
<?php echo e($activity->legalBodyName); ?>'s Profile
<?php $__env->stopSection(); ?>



<?php $__env->startSection('breadcrumbs'); ?>
                            <li><a href="<?php echo e(url('/')); ?>"><i class="icon-home2 position-left"></i>Dashboard</a></li>
                            <li><a href="<?php echo e(url('/existing-collections')); ?>">Existing Collections</a></li>
                            <li class="active"><?php echo e($activity->legalBodyName); ?></li>
<?php $__env->stopSection(); ?>



<?php $__env->startSection('content'); ?>       
<?php /*  CATCH FORM ERRORS */ ?>
<?php echo $__env->make('partials/message-block', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>            

<?php /*  EDIT COLLECTION FORM */ ?>
                    <form id="adduser" method="POST" enctype="multipart/form-data" action="<?php echo e(url('/existing-collections/edit/store')); ?>" class="steps-validation">
                    <input type="hidden" name="legalBodyID" value="<?php echo e($activity->legalBodyID); ?>"/>
                    <input type="hidden" name="legalBodyOrigImage" value="<?php echo e($activity->legalBodyImage); ?>"/>
                    <input type="hidden" name="legalBodyMDAcodeOrig" class="form-control" value="<?php echo e($activity->legalBodyMDAcode); ?>"/>

                        <?php echo csrf_field(); ?>


                        <div class="row">
                            <div class="col-lg-8">
                                <div class="panel registration-form">
                                    <div class="panel-body">
                                        <div class="text-center">
                                            <div class="icon-object border-success text-success"><i class="icon-plus3"></i></div>
                                            <h5 class="content-group-lg"><?php echo e($activity->legalBodyName); ?>'s Profile <small class="display-block">All fields except co-ordinates and image are required</small></h5>
                                        </div>
                                    <div class="form-group has-feedback <?php echo e($errors->has('legalBodyName') ? 'has-error' : ''); ?>">
                                        <label class="control-label">Legal Body Name</label>
                                        <input type="text" name="legalBodyName" class="form-control" placeholder="Legal Body Name" value="<?php echo e($activity->legalBodyName); ?>">
                                    </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group has-feedback <?php echo e($errors->has('legalBodyShortName') ? 'has-error' : ''); ?>">
                                                <label class="control-label">Legal Body Short Name</label>
                                                    <input type="text" name="legalBodyShortName" class="form-control" placeholder="Legal Body Short Name" value="<?php echo e($activity->legalBodyShortName); ?>">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group has-feedback <?php echo e($errors->has('legalBodyMDAcode') ? 'has-error' : ''); ?>">
                                                <label class="control-label">Legal Body MDA Code</label>
                                                    <input type="text" name="legalBodyMDAcode" class="form-control" placeholder="Legal Body MDA Code" value="<?php echo e($activity->legalBodyMDAcode); ?>">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group has-feedback <?php echo e($errors->has('legalBodyWebsite') ? 'has-error' : ''); ?>">
                                                <label class="control-label">Legal Body Website</label>
                                                    <input type="text" name="legalBodyWebsite" class="form-control" placeholder="Legal Body Website" value="<?php echo e($activity->legalBodyWebsite); ?>">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group has-feedback <?php echo e($errors->has('legalBodyDefaultRepository') ? 'has-error' : ''); ?>">
                                                <label class="control-label">Legal Body Default Repository</label>
                                                    <input type="text" name="legalBodyDefaultRepository" class="form-control" placeholder="Legal Body Default Repository" value="<?php echo e($activity->legalBodyDefaultRepository); ?>">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group has-feedback <?php echo e($errors->has('latitude') ? 'has-error' : ''); ?>">
                                                <label class="control-label">Latitude</label>
                                                    <input type="text" name="latitude" class="form-control" placeholder="Latitude" value="<?php echo e($activity->latitude); ?>">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group has-feedback <?php echo e($errors->has('longitude') ? 'has-error' : ''); ?>">
                                                <label class="control-label">Longitude</label>
                                                    <input type="text" name="longitude" class="form-control" placeholder="Longitude" value="<?php echo e($activity->longitude); ?>">
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
                                       <?php echo e($activity->legalBodyDescription); ?>

                                    </textarea>
                                        </div>
                                        <div class="text-right">
                                            <button type="submit" class="btn bg-teal-400 btn-labeled btn-labeled-right ml-10"><b><i class="icon-plus3"></i></b> Update <?php echo e($activity->legalBodyName); ?>'s Profile</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
<?php endforeach; ?>

                        <!-- open right column -->
                        <div class="col-lg-4">
                            <div class="panel panel-flat">
                                <div class="panel-heading">
                                    <h6 class="panel-title">Collection Details</h6>
                                </div>
                                <div class="panel-body">
                                    <ul class="media-list" style="margin-top: -10px;">
                                            <li class="media">
                <?php if($insCount != ""): ?>

                                                <div class="media-left media-middle">
                                                    <a href="/instruments/<?php echo e($activity->legalBodyID); ?>" title="<?php echo e($activity->legalBodyName); ?> contains <?php echo e($insCount); ?> instruments"><img src="/images/legalBodyImages/thumbnails/<?php echo e($activity->legalBodyImage); ?>" class="img-circle img-xs" alt=""></a>
                                                </div>
                                                <div class="media-body">
                                                    <p style="margin-top: 10px">This collection contains <a title="Go to instruments belonging to <?php echo e($activity->legalBodyName); ?>" href="/instruments/<?php echo e($activity->legalBodyID); ?>"><?php echo e($insCount); ?> instruments</a>.</p>
        

                                                    <p style="margin-top: 10px"><a title="Add instrument to <?php echo e($activity->legalBodyName); ?>'s Collection" href="/addinstrument/collection/<?php echo e($activity->legalBodyID); ?>">Add Instrument To This Collection</a>.</p>

                                                </div>

                <?php else: ?>
                                                <div class="media-left media-middle">
                                                    <a href="/instruments/<?php echo e($activity->legalBodyID); ?>" title="<?php echo e($activity->legalBodyName); ?> contains <?php echo e($insCount); ?> instruments"><img src="/images/legalBodyImages/thumbnails/<?php echo e($activity->legalBodyImage); ?>" class="img-circle img-xs" alt=""></a>
                                                </div>

                                                <div class="media-body">
                                                    <p style="margin-top: 10px">This collection contains no instruments</a>.</p>
                                                    <p style="margin-top: 10px"><a title="Add instrument to <?php echo e($activity->legalBodyName); ?>'s Collection" href="/addinstrument/collection/<?php echo e($activity->legalBodyID); ?>">Add Instrument To This Collection</a>.</p>
                                                </div>
                <?php endif; ?>                
                                             </li>
                                    </ul>         
                                </div>
                            </div>



<?php if($role == "SuperAdmin"): ?>
                             <div class="panel panel-flat">
                                <div class="panel-heading">
                                    <h6 class="panel-title">LIDO XML For This Collection</h6>
                                </div>
                                <div class="panel-body">
                                    <ul class="media-list" style="margin-top: -10px;">
                                            <li class="media">

                                                <div class="media-left media-middle">
                                                    <a href="/instruments/<?php echo e($activity->legalBodyID); ?>" title="<?php echo e($activity->legalBodyName); ?> contains <?php echo e($insCount); ?> instruments"><img src="/images/legalBodyImages/thumbnails/<?php echo e($activity->legalBodyImage); ?>" class="img-circle img-xs" alt=""></a>
                                                </div>

                                                <div class="media-body">

                                                 <?php if(strlen($importXMLfile) > 0): ?>
                                                    <p style="margin-top: 10px"><strong>Import XML set as:</strong><br/><?php echo e($importXMLfile); ?></p>
                                                    <p style="margin-top: 0px"><strong>File Location:</strong><br/><?php echo e($display_file); ?></p>

                                                        <?php if(strlen($filesize_k) > 0): ?>
                                                             <p style="margin-top: 0px"><strong>File Size:</strong><br/><?php echo e($filesize_k); ?>k</p>
                                                             <p style="margin-top: 0px"><strong>Last Modified:</strong><br/><?php echo e($file_last_modified); ?></p>
                                                             <p style="margin-top: 0px"><a title="Import Into Collection From <?php echo e($importXMLfile); ?>" href="/import-into-collection/<?php echo e($activity->legalBodyID); ?>">Import Into This Collection From This XML</a>.</p>  
                                                        <?php else: ?>
                                                             <p><strong>Cannot Find This File. Please Upload A New One.</strong></p>  

                                                        <?php endif; ?>        

                                                    <p style="margin-top: 10px"><a title="Upload New XML For <?php echo e($activity->legalBodyName); ?>'s Collection" href="/existing-collections/xml/<?php echo e($activity->legalBodyID); ?>">Upload New XML For This Collection</a>.</p>

                                                <?php else: ?>
                                                    <p style="margin-top: 0px">No XML currently set for this collection.</p> 

                                                    <p style="margin-top: 10px"><a title="Upload XML For <?php echo e($activity->legalBodyName); ?>'s Collection" href="/existing-collections/xml/<?php echo e($activity->legalBodyID); ?>">Upload XML For This Collection</a>.</p>

                                                <?php endif; ?>    


                                                
                                                </div>
               
                                             </li>
                                    </ul>         
                                </div>
                            </div>
<?php endif; ?>



<?php if($activity->latitude && $activity->longitude): ?>
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
                                            var myLatLng = {lat: <?php echo e($activity->latitude); ?>, lng: <?php echo e($activity->longitude); ?>};
                                            var map = new google.maps.Map(document.getElementById('map'), {
                                              zoom: 14,
                                              center: myLatLng
                                            });
                                            var marker = new google.maps.Marker({
                                              position: myLatLng,
                                              map: map,
                                              title: '<?php echo e($activity->legalBodyName); ?>'
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
                            <!-- Recent Activity -->
                            <div class="panel panel-flat">
                                <div class="panel-heading">
                                    <h6 class="panel-title">Recent Profile Activity</h6>
                                </div>
                                <div class="panel-body">
                                    <div class="content-group-xs" id="bullets"></div>
                                    <ul class="media-list" style="margin-top: -10px;">
                                    <?php if(count($collection_activity) < 1): ?>
                                        <?php /* This collection has no activity*/ ?>
                                        <p style="margin-top:-20px;">This collection has no activity.</p>
                                    <?php endif; ?>
                                
                                    <?php foreach($collection_activity as $key => $collectionactivity): ?>
                                        <?php if(strlen(strstr($collectionactivity->activity,'You added a new collection'))>0): ?>   
                                            <li class="media">
                                                <div class="media-left media-middle">
                                                    <a title="<?php echo e($admin_name[$key]); ?> added this collection"><img src="/images/users/thumbnails/<?php echo e($admin_pic[$key]); ?>" class="img-circle img-xs" alt=""></a>
                                                </div>
                                                <div class="media-body">
                                                    <p><?php echo e($admin_name[$key]); ?> added this collection</p>
                                                    <div class="media-annotation"><?php echo e($activity_time_ago[$key]); ?></div>
                                                </div>
                                             </li>
                                        <?php endif; ?>     
                                        <?php if(strlen(strstr($collectionactivity->activity,'You updated a collection'))>0): ?>   
                                            <li class="media">
                                                <div class="media-left media-middle">
                                                    <a title="<?php echo e($admin_name[$key]); ?> added this collection"><img src="/images/users/thumbnails/<?php echo e($admin_pic[$key]); ?>" class="img-circle img-xs" alt=""></a>
                                                </div>                                                
                                                <div class="media-body">
                                                    <p><?php echo e($admin_name[$key]); ?> updated this collection</p>
                                                    <div class="media-annotation"><?php echo e($activity_time_ago[$key]); ?></div>
                                                </div>
                                             </li>
                                        <?php endif; ?>  
                                        <?php if(strlen(strstr($collectionactivity->activity,'imported'))>0): ?>   
                                            <li class="media">
                                                <div class="media-left media-middle">
                                                    <a title="<?php echo e($admin_name[$key]); ?> added this collection"><img src="/images/users/thumbnails/<?php echo e($admin_pic[$key]); ?>" class="img-circle img-xs" alt=""></a>
                                                </div>                                                
                                                <div class="media-body">
                                                    <p><?php echo e($admin_name[$key]); ?> <?php echo e($collectionactivity->activity); ?></p>
                                                    <div class="media-annotation"><?php echo e($activity_time_ago[$key]); ?></div>
                                                </div>
                                             </li>
                                        <?php endif; ?>  


                                     <?php endforeach; ?>   
                                    </ul>
                                </div></div>
                            </div>
                            <!-- /Recent Activity -->
                        </div>
                    </form>
<!-- /UPDATE COLLECTION/LEGAL BODY-->
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('layouts.loggedin', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>