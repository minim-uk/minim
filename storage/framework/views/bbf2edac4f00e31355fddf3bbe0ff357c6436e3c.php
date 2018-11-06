<?php $__env->startSection('title'); ?>
Account Settings
<?php $__env->stopSection(); ?>



<?php $__env->startSection('breadcrumbs'); ?>
                            <li><a href="<?php echo e(url('/')); ?>"><i class="icon-home2 position-left"></i>Dashboard</a></li>
                            <li class="active">Account Settings</li>
<?php $__env->stopSection(); ?>



<?php $__env->startSection('content'); ?>
        
<?php /*  CATCH FORM ERRORS */ ?>
<?php echo $__env->make('partials/message-block', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>       

<?php /*  ACCOUNT SETTINGS FORM */ ?>
                    <form id="adduser" method="post" enctype="multipart/form-data" action="<?php echo e(url('/account-settings/store')); ?>" class="steps-validation">
                    <input type="hidden" name="avatarOrig" value="<?php echo e(Session::get('avatar')); ?>"/>
                       
                        <?php echo csrf_field(); ?>


                        <div class="row">
                            <div class="col-lg-12">
                                <div class="panel registration-form">
                                    <div class="panel-body">
                                        <div class="text-center">
                                            <div class="icon-object border-success text-success"><img style="max-width: 100px;" src="<?php echo e(url('/images/users/thumbnails/')); ?>/<?php echo e(Auth::user()->avatar); ?>" alt=""></div>
                                            <h5 class="content-group-lg">Update your MINIM-UK Account Details <small class="display-block">All fields are required</small></h5>
                                        </div>            
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group has-feedback <?php echo e($errors->has('name') ? 'has-error' : ''); ?>">
                                                <label class="control-label">First Name</label>
                                                    <input value="<?php echo e(Session::get('forename')); ?>" type="text" name="name" class="form-control" placeholder="First name">
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group has-feedback <?php echo e($errors->has('surname') ? 'has-error' : ''); ?>">
                                                <label class="control-label">Surname</label>
                                                    <input value="<?php echo e(Session::get('surname')); ?>" type="text" name="surname" class="form-control" placeholder="Surname">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group has-feedback <?php echo e($errors->has('email') ? 'has-error' : ''); ?>">
                                                <label class="control-label">Email Address</label>
                                                    <input value="<?php echo e(Session::get('email')); ?>" type="email" class="form-control" name="email" placeholder="Email address">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group has-feedback <?php echo e($errors->has('confirmemail') ? 'has-error' : ''); ?>">
                                                <label class="control-label">Email Address</label>
                                                    <input type="email" class="form-control" name="confirmemail" placeholder="Repeat email address">
                                                </div>
                                            </div>
                                        </div>                                      
                                        <div class="row">
                                             <div class="col-md-12">
                                                <div class="form-group has-feedback">
                                                    <label class="control-label">Avatar Image:</label>
                                                    <div class="col-lg-12">
                                                        <input name="avatar" type="file" class="file-input-preview" data-show-remove="true">
                                                        <span class="help-block">Please upload an image. <code>jpg, gif, png</code> accepted.</span><br/>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <button type="submit" class="btn bg-teal-400 btn-labeled btn-labeled-right ml-10"><b><i class="icon-plus3"></i></b> Update your MINIM UK account</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
<!-- /My Account Settings Form -->
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('layouts.loggedin', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>