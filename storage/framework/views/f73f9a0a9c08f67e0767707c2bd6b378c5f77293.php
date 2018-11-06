<?php $__env->startSection('title'); ?>
<?php echo e($legalBodyName); ?> / <?php echo e($instrumentName); ?> / Images
<?php $__env->stopSection(); ?>



<?php $__env->startSection('breadcrumbs'); ?>
                            <li><a href="<?php echo e(url('/')); ?>"><i class="icon-home2 position-left"></i>Dashboard</a></li>
                            <li><a href="<?php echo e(url('/instruments')); ?>"></i>Instruments </a></li>
                            <li><a href="/instruments/<?php echo e($legalBodyID); ?>"></i><?php echo e($legalBodyName); ?></a></li>
                            <li><a href="/edit-instrument/<?php echo e($instrumentID); ?>"></i><?php echo e($instrumentName); ?></a></li>
                            <li class="active">Manage Images</li>
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
                <!-- Content area -->
                <div class="content">
                    <!-- Dashboard content -->
                    <div class="row">
                        <div class="col-lg-8">
                                <div class="panel registration-form">
                                    <div class="panel-body">
                            <?php foreach($images as $key => $image): ?>
                                <div style="background-color:#555555; padding:20px; border:1px dotted lightgray; float:left; position: relative; background-color:#fff; margin:8px; min-height:400px; width:30.6%;">
                                   <?php if($image->resourceCaption != ""): ?>
                                     <p style="z-index: 99999999999999!important"><strong>Caption: </strong><?php echo e(str_limit($image->resourceCaption, $limit = 75, $end = '...')); ?></p>
                                   <?php endif; ?>
                                   <?php if($image->resourceCaption == ""): ?>
                                     <p><strong>Caption: </strong>none.</p>
                                   <?php endif; ?>
                                   <p><a href="/edit-instrument/<?php echo e($instrumentID); ?>/resource/<?php echo e($image->resourceID); ?>">Edit</a>&nbsp;&nbsp;<a class="deleteimage" href="/edit-instrument/<?php echo e($instrumentID); ?>/resource/<?php echo e($image->resourceID); ?>/delete">Delete</a></p>
<a href="/edit-instrument/<?php echo e($instrumentID); ?>/resource/<?php echo e($image->resourceID); ?>">

                            <p style="margin-top:-10px;"><img style=" position: absolute;
                            left: 0;
                            bottom: 0;
                            width: auto; /* to keep proportions */
                            height: auto; /* to keep proportions */
                            max-width: 100%; /* not to stand out from div */
                            max-height: 100%; /* not to stand out from div */
                            margin: auto auto 0; /* position to bottom and center */ border:1px dotted lightgray;  vertical-align: bottom" src="/instrument_resources/images/<?php echo e($legalBodyMDAcode); ?>/thumbnails/<?php echo e($image->resourceFileName); ?>" alt=""/></a></p>
                                              
                               </div>       
                            <?php endforeach; ?>
                                <div style="padding:20px; background-color:#4caf50; border:1px dotted lightgray; float:left; position: relative; margin:8px; width:240px;">
                                         <a class="addresource" href="/edit-instrument/<?php echo e($instrumentID); ?>/add-resource"><p>Add New Resource</p>
                                         <span class="addresourcetext">to <?php echo e($legalBodyName); ?>'s <?php echo e($instrumentName); ?></span></a>
                            </div>   
                       </div><!--/endcol-->
 
             </div></div>


            <!-- include rightside with instrument details -->        
            <?php echo $__env->make('partials/instrument_rightside', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>


</div>


<?php $__env->stopSection(); ?> 
<?php echo $__env->make('layouts.loggedin', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>