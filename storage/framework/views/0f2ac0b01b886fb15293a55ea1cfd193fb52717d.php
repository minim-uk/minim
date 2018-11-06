<?php $__env->startSection('title'); ?>
Delete Instrument
<?php $__env->stopSection(); ?>



<?php $__env->startSection('breadcrumbs'); ?>
                            <li><a href="<?php echo e(url('/')); ?>"><i class="icon-home2 position-left"></i>Dashboard</a></li>
                            <li><a href="/instruments">Instruments</a></li>
                            <li><a href="/instruments/<?php echo e($legalBodyID); ?>"><?php echo e($legalBodyName); ?></a></li>
                            <li><a href="/edit-instrument/<?php echo e($instrumentID); ?>"><?php echo e($instrument_classifiedTitlePreferred); ?></a></li>
                            <li class="active">Delete</li>
<?php $__env->stopSection(); ?>



<?php $__env->startSection('content'); ?>
        
<style>
.myButton {
    -moz-box-shadow:inset 0px 1px 0px 0px #cf866c;
    -webkit-box-shadow:inset 0px 1px 0px 0px #cf866c;
    box-shadow:inset 0px 1px 0px 0px #cf866c;
    background:-webkit-gradient(linear, left top, left bottom, color-stop(0.05, #d0451b), color-stop(1, #bc3315));
    background:-moz-linear-gradient(top, #d0451b 5%, #bc3315 100%);
    background:-webkit-linear-gradient(top, #d0451b 5%, #bc3315 100%);
    background:-o-linear-gradient(top, #d0451b 5%, #bc3315 100%);
    background:-ms-linear-gradient(top, #d0451b 5%, #bc3315 100%);
    background:linear-gradient(to bottom, #d0451b 5%, #bc3315 100%);
    filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#d0451b', endColorstr='#bc3315',GradientType=0);
    background-color:#d0451b;
    -moz-border-radius:3px;
    -webkit-border-radius:3px;
    border-radius:3px;
    border:1px solid #942911;
    display:inline-block;
    cursor:pointer;
    color:#ffffff;
    font-family:Arial;
    font-size:13px;
    padding:6px 24px;
    text-decoration:none;
    text-shadow:0px 1px 0px #854629;
    width:100%;
}
.myButton:hover {
    background:-webkit-gradient(linear, left top, left bottom, color-stop(0.05, #bc3315), color-stop(1, #d0451b));
    background:-moz-linear-gradient(top, #bc3315 5%, #d0451b 100%);
    background:-webkit-linear-gradient(top, #bc3315 5%, #d0451b 100%);
    background:-o-linear-gradient(top, #bc3315 5%, #d0451b 100%);
    background:-ms-linear-gradient(top, #bc3315 5%, #d0451b 100%);
    background:linear-gradient(to bottom, #bc3315 5%, #d0451b 100%);
    filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#bc3315', endColorstr='#d0451b',GradientType=0);
    background-color:#bc3315;
}
.myButton:active {
    position:relative;
    top:1px;
}
</style>
<!-- DELETE INSTRUMENT FORM -->
                        <div class="row">
                            <div class="col-lg-8 col-lg-offset-2">
                                <div class="panel registration-form" style="border:0;">
                                    <div class="panel-body">
                                     <h3>Sure you want to delete <?php echo e($instrument_classifiedTitlePreferred); ?>?</h3>
                                     <p style="margin-top: 10px">This instrument belongs to <a title="Go to instruments belonging to <?php echo e($legalBodyName); ?>" href="/instruments/<?php echo e($legalBodyID); ?>"><?php echo e($legalBodyName); ?></a>'s collection</a>.</p>
                                    <div style="float:left;">
                                     <?php if($insimage != "none"): ?>    
                                     <div style="width:100px; float:left; margin-bottom: 15px;">
                                      <img src="/instrument_resources/images/<?php echo e($legalBodyMDAcode); ?>/thumbnails/<?php echo e($insimage); ?>" style="margin-top:4px; max-height:100px; max-width:100px; " alt="<?php echo e($instrument_classifiedTitlePreferred); ?>"/>
                                    </div>
                                     <?php endif; ?>   
                                     <div style="float:left; width:300px; margin-bottom: 15px;">
                                     <p style="margin-left:12px;"><strong>This instrument comprises:</strong></p>
                                     <ul>
                                        <li><a href="/edit-instrument/<?php echo e($instrumentID); ?>/images"><?php echo e($imageCount); ?></a> images</li>
                                        <li><a href="/edit-instrument/<?php echo e($instrumentID); ?>/sounds"><?php echo e($audioCount); ?></a> sounds</li>
                                        <li><a href="/edit-instrument/<?php echo e($instrumentID); ?>/videos"><?php echo e($videoCount); ?></a> videos</li>
                                        <li><?php echo e($num_inscriptions); ?> inscriptions</li>
                                        <li><?php echo e($num_serial); ?> serial edition numbers</li>
                                        <li><?php echo e($num_decorative); ?> decorative features</li>
                                     </ul>
                                     </div>  
                                            <form action="/delete-now" method="post">
                                                <input type="hidden" name="deletiontype" value="instrument"/>
                                                <input type="hidden" name="instrumentID" value="<?php echo e($instrumentID); ?>"/>

                                                  <?php echo csrf_field(); ?>


                                                <input class="myButton" type="submit" value="Instantly delete this instrument"/>
                                            </form>             
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
<!-- /DELETE INSTRUMENT FORM -->
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('layouts.loggedin', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>