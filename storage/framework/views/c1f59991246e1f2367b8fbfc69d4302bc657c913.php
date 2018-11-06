<?php $__env->startSection('title'); ?>
Change Password
<?php $__env->stopSection(); ?>




<?php $__env->startSection('breadcrumbs'); ?>
                            <li><a href="<?php echo e(url('/')); ?>"><i class="icon-home2 position-left"></i>Dashboard</a></li>
                            <li class="active">Change Password</li>
<?php $__env->stopSection(); ?>




<?php $__env->startSection('content'); ?>
        
<?php /*  CATCH FORM ERRORS */ ?>
<?php echo $__env->make('partials/message-block', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>       

<?php /*  CHANGE PASSWORD FORM */ ?>
                    <form id="adduser" method="POST" action="<?php echo e(url('/change-password/store')); ?>" class="steps-validation">
                    <input type="hidden" name="checkUser" value="<?php echo e($user_id); ?>"/>

                      <?php echo csrf_field(); ?>


                        <div class="row">
                            <div class="col-lg-12">
                                <div class="panel registration-form">
                                    <div class="panel-body">
                                        <div class="text-center">
                                            <h5 class="content-group-lg">Update your MINIM-UK Password <small class="display-block">All fields are required</small></h5>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group has-feedback <?php echo e($errors->has('password') ? 'has-error' : ''); ?>">
                                                <label class="control-label">Enter New Password</label>
                                                    <input type="password" class="form-control" name="password" placeholder="Enter new password">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group has-feedback <?php echo e($errors->has('password') ? 'has-error' : ''); ?>">
                                                <label class="control-label">Repeat New Password</label>
                                                    <input type="password" class="form-control" name="confirmPassword" placeholder="Repeat new password">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <button type="submit" class="btn bg-teal-400 btn-labeled btn-labeled-right ml-10"><b><i class="icon-plus3"></i></b> Update your MINIM UK Password</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
<!-- /Change Password Form -->
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('layouts.loggedin', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>