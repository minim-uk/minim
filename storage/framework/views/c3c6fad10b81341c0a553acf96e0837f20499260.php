<?php foreach($admin_user_activity as $key => $activity): ?>






<?php $__env->startSection('title'); ?>
Delete <?php echo e($activity->legalBodyName); ?>'s Collection And Profile
<?php $__env->stopSection(); ?>





<?php $__env->startSection('breadcrumbs'); ?>
                            <li><a href="<?php echo e(url('/')); ?>"><i class="icon-home2 position-left"></i>Dashboard</a></li>
                            <li><a href="<?php echo e(url('/existing-collections')); ?>">Existing Collections</a></li>
                            <li><a href="/existing-collections/edit/<?php echo e($activity->legalBodyID); ?>"><?php echo e($activity->legalBodyName); ?></a></li>
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


<!-- DELETE COLLECTION -->
                        <div class="row">
                            <div class="col-lg-8 col-lg-offset-2">
                                <div class="panel registration-form" style="border:0;">
                                    <div class="panel-body">
<?php if(count($collectionusers) > 0): ?>
<h3>Can't delete <?php echo e($activity->legalBodyName); ?>'s Collection</h3>
<p>The users below must be assigned to a new collection or deleted first. Only SuperAdmins can do this.</p>

    <?php foreach($collectionusers as $key => $user): ?>
        <?php if(strlen($user->name) > 0): ?>
            <?php if($role == "SuperAdmin"): ?>
                <p><a href="/existing-users/edit/<?php echo e($user->id); ?>"><?php echo e($user->name); ?> <?php echo e($user->surname); ?></p>
            <?php else: ?>
                <p><?php echo e($user->name); ?> <?php echo e($user->surname); ?></p>
            <?php endif; ?>
        <?php endif; ?>
     <?php endforeach; ?> 
<?php else: ?>         
                                     <h3>Sure you want to delete <?php echo e($activity->legalBodyName); ?>'s Collection?</h3>
                                     <p style="margin-top: 10px">This collection contains <a title="Go to instruments belonging to <?php echo e($activity->legalBodyName); ?>" href="/instruments/<?php echo e($activity->legalBodyID); ?>"><?php echo e($insCount); ?> instruments</a> which will <strong>also be deleted</strong>.</p>
                                            <form action="/delete-now" method="post">
                                                <input type="hidden" name="deletiontype" value="collection"/>
                                                <input type="hidden" name="legalBodyID" value="<?php echo e($activity->legalBodyID); ?>"/>

                                                  <?php echo csrf_field(); ?>


                                                <input class="myButton" type="submit" value="Instantly delete this collection and all its instruments"/>
                                            </form>    
<?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
<!-- /DELETE COLLECTION->
<?php endforeach; ?>








<?php $__env->stopSection(); ?> 







<?php echo $__env->make('layouts.loggedin', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>