                    <?php foreach($resource as $key => $resource): ?>
<?php $__env->startSection('title'); ?>
<?php echo e($legalBodyName); ?> / <?php echo e($instrumentName); ?> /
<?php if($resource->resourceType == "image"): ?>
Images
<?php endif; ?>
<?php if($resource->resourceType == "sound"): ?>
Audio
<?php endif; ?>
<?php if($resource->resourceType == "video"): ?>
Video
<?php endif; ?>
 / <?php echo e($resource->resourceFileName); ?>

<?php $__env->stopSection(); ?>



<?php $__env->startSection('breadcrumbs'); ?>
                            <li><a href="<?php echo e(url('/')); ?>"><i class="icon-home2 position-left"></i>Dashboard</a></li>
                            <li><a href="<?php echo e(url('/instruments')); ?>"></i>Instruments </a></li>
                            <li><a href="/instruments/<?php echo e($legalBodyID); ?>"></i><?php echo e($legalBodyName); ?></a></li>
                            <li><a href="/edit-instrument/<?php echo e($instrumentID); ?>"></i><?php echo e($instrumentName); ?></a></li>
 <?php if($resource->resourceType == "image"): ?>
                            <li><a href="/edit-instrument/<?php echo e($instrumentID); ?>/images"></i>Manage Images</a></li>
 <?php endif; ?>
 <?php if($resource->resourceType == "video"): ?>
                            <li><a href="/edit-instrument/<?php echo e($instrumentID); ?>/video"></i>Manage Video</a></li>
 <?php endif; ?>
 <?php if($resource->resourceType == "sound"): ?>
                            <li><a href="/edit-instrument/<?php echo e($instrumentID); ?>/audio"></i>Manage Audio</a></li>
 <?php endif; ?>
                            <li class="active"><?php echo e($resource->resourceFileName); ?></li>
<?php $__env->stopSection(); ?>



<?php $__env->startSection('content'); ?>     
                        <div id="modal_remote" class="modal">
                            <div class="modal-dialog modal-full">
                                <div class="modal-content">
                                    <div class="modal-body"></div>
                                    <br clear="all"/>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-primary" data-dismiss="modal" style="clear:both;">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>
<?php if($resourceOrigFileType == "video"): ?>
        <link href="/css/admin/video-js.css" rel="stylesheet">
<?php endif; ?>
<!-- Register new user form -->
                    <form id="editresource" method="POST" enctype="multipart/form-data" action="<?php echo e(url('/edit-resource/store')); ?>" class="steps-validation">
                    <input type="hidden" name="resourceID" value="<?php echo e($resource->resourceID); ?>"/>
                    <input type="hidden" name="instrumentID" value="<?php echo e($instrumentID); ?>"/>
                    <input type="hidden" name="resourceOrigFileType" value="<?php echo e($resourceOrigFileType); ?>"/>
                    <input type="hidden" name="resourceOrigFileName" value="<?php echo e($resourceOrigFileName); ?>"/>

                     <?php echo csrf_field(); ?>


                        <div class="row">
                            <div class="col-lg-8">
                                <div class="panel registration-form">
                                    <div class="panel-body">
                           <h3 style="margin-bottom:21px;"><?php echo e($legalBodyName); ?> &gt; <?php echo e($instrumentName); ?> &gt; <?php echo e($resource->resourceFileName); ?><!-- | <a href="/edit-instrument/<?php echo e($instrumentID); ?>/resource/<?php echo e($resource->resourceID); ?>/delete">Delete Immediately</a>--></h3>        
<?php if($resourceOrigFileType == "image"): ?>
                           <p><a title="Click image to see full size in a new window." href="/instrument_resources/images/<?php echo e($legalBodyMDAcode); ?>/<?php echo e($resource->resourceFileName); ?>" target="_blank"><img style="max-width:300px; max-height:300px;" src="/instrument_resources/images/<?php echo e($legalBodyMDAcode); ?>/<?php echo e($resource->resourceFileName); ?>" alt=""/></a></p>
<?php endif; ?>


<?php if($resourceOrigFileType == "sound"): ?>

   		 <!-- EMBED THE SOUND HERE -->
        <div style="clear:left;" id="player4" class="aplayer"></div>
        <script type="text/javascript" src="<?php echo asset('/js/admin/plugins/audioplayer/APlayer.min.js')?>"></script>
        <script type="text/javascript">   
        var ap4 = new APlayer({
            element: document.getElementById('player4'),
            narrow: false,
            autoplay: false,
            showlrc: false,
            mutex: true,
            theme: '#ad7a86',
            music: [
               
                    {
                        title: '<?php echo e($resource->resourceFileName); ?>',
                        author: '<?php echo e($legalBodyName); ?> / <?php echo e($instrumentName); ?>',
                        url: '/instrument_resources/sound/<?php echo e($legalBodyMDAcode); ?>/<?php echo e($resource->resourceFileName); ?>',
                        pic: '/instrument_resources/images/<?php echo e($legalBodyMDAcode); ?>/thumbnails/<?php echo e($soundimage); ?>'
                      
                    },
            ]
        });
        ap4.init();
        </script>
<?php endif; ?>


<?php if($resourceOrigFileType == "video"): ?>
   		 <!-- EMBED THE VIDEO HERE -->
         <!--<h4><?php echo e($resource->resourceFileName); ?></h4> -->
          <video id="<?php echo e($resource->resourceFileName); ?>" class="video-js" controls preload="auto" width="320" height="132"
          poster="/instrument_resources/images/<?php echo e($legalBodyMDAcode); ?>/<?php echo e($videoimage); ?>" data-setup="{}">

          <source src="http://royal-college-of-music.dev/instrument_resources/video/<?php echo e($legalBodyMDAcode); ?>/<?php echo e($resource->resourceFileName); ?>" type='video/webm'> -->
            <!-- ISSUE HERE WITH SAME VIDEO OF DIFFERENT TYPE FOR FALLBACKS ON WEB PLAY NEEDING TO BE GROUPED PER VID... -->

            <p class="vjs-no-js">
              To view this video please enable JavaScript, and consider upgrading to a web browser that
              <a href="http://videojs.com/html5-video-support/" target="_blank">supports HTML5 video</a>
            </p>
          </video>
<?php endif; ?>

                                   <?php if($resourcemessage != ""): ?>                                  
                                       <p><strong><?php echo e($resourcemessage); ?></strong></p>
                                   <?php endif; ?>
                                   <?php if($soundimage == "" && $videoimage == ""): ?>
                           				<p style="font-size:11px;">[ Click image to see full size in a new window. ]</p>
                                   <?php endif; ?>

                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group has-feedback">
                                                <label class="control-label">Caption</label>
                                                   
													 <textarea name="resourceCaption" rows="3" class="form-control" placeholder="Enter Caption (not required)"><?php echo e($resource->resourceCaption); ?></textarea>

                                                    <div class="form-control-feedback">
                                                        <!--<i class="icon-user-check text-muted"></i>-->
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group has-feedback">
                                                    <label class="control-label">Replace File:</label>
                                                    <div class="col-lg-12">
                                                        <input name="resource" type="file" class="file-input-preview" data-show-remove="true">
                                                        <?php if($soundimage != ""): ?>
                                                            <span class="help-block">Replace this sound file <code>mp3</code> accepted.</span><br/> 
                                                        <?php elseif($videoimage != ""): ?>
                                                            <span class="help-block">Replace this video file <code>mp4</code> accepted.</span><br/> 
                                                        <?php else: ?> 
                                                        	<span class="help-block">Replace this image file <code>jpg, gif, png</code> accepted.</span><br/>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <div class="text-right">
                                            <button type="submit" class="btn bg-teal-400 btn-labeled btn-labeled-right ml-10"><b><i class="icon-plus3"></i></b> Update Resource</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
<!-- /Register new user form -->      
   <?php endforeach; ?>

            <!-- include rightside with instrument details -->        
            <?php echo $__env->make('partials/instrument_rightside', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
</div>
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('layouts.loggedin', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>