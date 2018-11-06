<?php $__env->startSection('title'); ?>
<?php echo e($legalBodyName); ?> / <?php echo e($instrumentName); ?> / Audio
<?php $__env->stopSection(); ?>



<?php $__env->startSection('breadcrumbs'); ?>
                            <li><a href="<?php echo e(url('/')); ?>"><i class="icon-home2 position-left"></i>Dashboard</a></li>
                            <li><a href="<?php echo e(url('/instruments')); ?>"></i>Instruments </a></li>
                            <li><a href="/instruments/<?php echo e($legalBodyID); ?>"></i><?php echo e($legalBodyName); ?></a></li>
                            <li><a href="/edit-instrument/<?php echo e($instrumentID); ?>"></i><?php echo e($instrumentName); ?></a></li>
                            <li class="active">Manage Audio</li>
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
        <script type="text/javascript" src="<?php echo asset('/js/admin/plugins/audioplayer/APlayer.min.js')?>"></script>
                <!-- Content area -->
                <div class="content">
                    <!-- Dashboard content -->
                    <div class="row">
                        <div class="col-lg-8">
                                <div class="panel registration-form">
                                    <div class="panel-body">
                           
                            <?php foreach($sounds as $key => $sound): ?>
                                       <div style="padding:20px; border:1px dotted lightgray; float:left; position: relative; background-color:#fff; margin:8px; min-height:400px; width:30%;">
                                       <!-- EMBED SOUND -->
                                        <div style="clear:left; margin-bottom: 20px;" id="player<?php echo e($sound->resourceID); ?>" class="aplayer"></div>
                                        <script type="text/javascript">   
                                        var ap4 = new APlayer({
                                            element: document.getElementById('player<?php echo e($sound->resourceID); ?>'),
                                            narrow: false,
                                            autoplay: false,
                                            showlrc: false,
                                            mutex: true,
                                            theme: '#ad7a86',
                                            music: [
                                               
                                                    {
                                                        title: '<?php echo e($sound->resourceFileName); ?>',
                                                        author: '<?php echo e($legalBodyName); ?> / <?php echo e($instrumentName); ?>',
                                                        url: '/instrument_resources/sound/<?php echo e($legalBodyMDAcode); ?>/<?php echo e($sound->resourceFileName); ?>',
                                                        pic: '/instrument_resources/images/<?php echo e($legalBodyMDAcode); ?>/thumbnails/<?php echo e($soundimage); ?>'
                                                    },
                                            ]
                                        });
                                        ap4.init();
                                        </script>
                                   <?php if($sound->resourceCaption != ""): ?>
                                     <p style="z-index: 99999999999999!important"><strong>Caption: </strong><?php echo e(str_limit($sound->resourceCaption, $limit = 31, $end = '...')); ?></p>
                                   <?php endif; ?>
                                   <?php if($sound->resourceCaption == ""): ?>
                                     <p><strong>Caption: </strong>none.</p>
                                   <?php endif; ?>
                                   <p><a href="/edit-instrument/<?php echo e($instrumentID); ?>/resource/<?php echo e($sound->resourceID); ?>">Edit</a>&nbsp;&nbsp;<a class="deleteimage" href="/edit-instrument/<?php echo e($instrumentID); ?>/resource/<?php echo e($sound->resourceID); ?>/delete">Delete</a></p>

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