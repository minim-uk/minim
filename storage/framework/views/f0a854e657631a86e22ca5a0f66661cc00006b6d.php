<?php $__env->startSection('title'); ?>
<?php echo e($legalBodyName); ?> / <?php echo e($instrumentName); ?> / Video
<?php $__env->stopSection(); ?>



<?php $__env->startSection('breadcrumbs'); ?>
                            <li><a href="<?php echo e(url('/')); ?>"><i class="icon-home2 position-left"></i>Dashboard</a></li>
                            <li><a href="<?php echo e(url('/instruments')); ?>"></i>Instruments </a></li>
                            <li><a href="/instruments/<?php echo e($legalBodyID); ?>"></i><?php echo e($legalBodyName); ?></a></li>
                            <li><a href="/edit-instrument/<?php echo e($instrumentID); ?>"></i><?php echo e($instrumentName); ?></a></li>
                            <li class="active">Manage Video</li>
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
<style>
a.deleteimage {
    color:red;
}
a.addresource {
    color:#fff;
    line-height: 10px;
    font-size: 16px;
    margin-top:-4px;
}
span.addresourcetext {
    color:#fff;
    font-size:14px;
    line-height: 14px;
    margin-top: -4px;
}
</style>
    <link href="http://vjs.zencdn.net/5.10.4/video-js.css" rel="stylesheet">
        <script type="text/javascript" src="<?php echo asset('/js/admin/plugins/audioplayer/APlayer.min.js')?>"></script>
                <!-- Content area -->
                <div class="content">
                    <!-- Dashboard content -->
                    <div class="row">
                        <div class="col-lg-8">
                                <div class="panel registration-form">
                                    <div class="panel-body">
                            <?php foreach($videos as $key => $video): ?>
                                       <div style="padding:20px; border:1px dotted lightgray; float:left; position: relative; background-color:#fff; margin:8px; min-height:400px; width:31%;">
<div style="float:left;margin-bottom: 20px;">
         <!--<h4><?php echo e($video->resourceFileName); ?></h4>-->
          <video id="<?php echo e($video->resourceFileName); ?>" class="video-js" controls preload="auto" width="100%" poster="/instrument_resources/images/<?php echo e($legalBodyMDAcode); ?>/<?php echo e($videoimage); ?>" data-setup="{}">
            <source src="http://royal-college-of-music.dev/instrument_resources/video/<?php echo e($legalBodyMDAcode); ?>/<?php echo e($video->resourceFileName); ?>" type='video/webm'> -->
            <!-- ISSUE HERE WITH SAME VIDEO OF DIFFERENT TYPE FOR FALLBACKS ON WEB PLAY NEEDING TO BE GROUPED PER VID... -->
            <p class="vjs-no-js">
              To view this video please enable JavaScript, and consider upgrading to a web browser that
              <a href="http://videojs.com/html5-video-support/" target="_blank">supports HTML5 video</a>
            </p>
          </video>
</div>     

                                   <?php if($video->resourceCaption != ""): ?>
                                     <p style="z-index: 99999999999999!important"><strong>Caption: </strong><?php echo e(str_limit($video->resourceCaption, $limit = 75, $end = '...')); ?></p>
                                   <?php endif; ?>
                                   <?php if($video->resourceCaption == ""): ?>
                                     <p><strong>Caption: </strong>none.</p>
                                   <?php endif; ?>
                                   <p><a href="/edit-instrument/<?php echo e($instrumentID); ?>/resource/<?php echo e($video->resourceID); ?>">Edit</a>&nbsp;&nbsp;<a class="deleteimage" href="/edit-instrument/<?php echo e($instrumentID); ?>/resource/<?php echo e($video->resourceID); ?>/delete">Delete</a></p>
                               </div>       
                            <?php endforeach; ?>
                                <div style="padding:20px; background-color:#4caf50; border:1px dotted lightgray; float:left; position: relative; margin:8px; width:240px;">
                                         <a class="addresource" href="/edit-instrument/<?php echo e($instrumentID); ?>/add-resource"><p>Add New Resource</p>
                                         <span class="addresourcetext">to <?php echo e($legalBodyName); ?>'s <?php echo e($instrumentName); ?></span></a>
                            </div>   
                       </div>
                                    
              </div></div>

            <!-- include rightside with instrument details -->        
            <?php echo $__env->make('partials/instrument_rightside', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

</div>
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('layouts.loggedin', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>