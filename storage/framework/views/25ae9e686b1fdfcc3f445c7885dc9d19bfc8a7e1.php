<?php $__env->startSection('title'); ?>
<?php echo e($legalBodyName); ?> / <?php echo e($titleSingle); ?>

<?php $__env->stopSection(); ?>



<?php $__env->startSection('breadcrumbs'); ?>
                            <li><a href="<?php echo e(url('/')); ?>"><i class="icon-home2 position-left"></i>Dashboard</a></li>
                            <li><a href="<?php echo e(url('/instruments')); ?>"></i>Instruments </a></li>
                            <li><a href="/instruments/<?php echo e($legalBodyID); ?>"><?php echo e($legalBodyName); ?></a></li>
                            <li><?php echo e($titleSingle); ?></li>
                            <li class="active">Rights Information</li>

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

                <div class="row">
                    <div class="col-lg-8">
                        <div class="panel registration-form" style="border:0;">
                            <div class="panel-body">

<?php /*  CATCH FORM ERRORS */ ?>
<?php echo $__env->make('partials/message-block', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>       

<?php /* event materials */ ?>   
            <form id="instrumentRights" method="post" class="steps-validation" action="<?php echo e(url('/edit-instrument/storerights')); ?>">
           
            <?php echo csrf_field(); ?>

           
            <input type="hidden" name="legalBodyID" id="legalBodyID" value="<?php echo e($legalBodyID); ?>">
            <input type="hidden" name="instrumentID" id="instrumentID" value="<?php echo e($instrumentID); ?>">


                <h6>Materials For <?php echo e($legalBodyName); ?>'s <?php echo e($instrumentName); ?></h6>
             <section data-step="1">






            </section>  
            </form>
            </div></div></div>
              <!-- /event materials -->

            <!-- include rightside with instrument details -->        
            <?php echo $__env->make('partials/instrument_rightside', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

     </div></div>
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('layouts.loggedin', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>