<?php $__env->startSection('title'); ?>
Add New User
<?php $__env->stopSection(); ?>



<?php $__env->startSection('breadcrumbs'); ?>
                            <li><a href="<?php echo e(url('/')); ?>"><i class="icon-home2 position-left"></i>Dashboard</a></li>
                            <li class="active">Add New User</li>
<?php $__env->stopSection(); ?>




<?php $__env->startSection('content'); ?>
        
<?php /*  CATCH FORM ERRORS */ ?>
<?php echo $__env->make('partials/message-block', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>       

<?php /*  REGISTER NEW USER FORM */ ?>
                    <form id="adduser" method="POST" enctype="multipart/form-data" action="<?php echo e(url('/add-user/store')); ?>" class="steps-validation">
                    <input type="hidden" name="id" value="0"/> 
                    <input type="hidden" name="avatarOrig" value=""/>
                      
                      <?php echo csrf_field(); ?>


                        <div class="row">
                            <div class="col-lg-12">
                                <div class="panel registration-form">
                                    <div class="panel-body">
                                        <div class="text-center">
                                            <div class="icon-object border-success text-success"><i class="icon-plus3"></i></div>
                                            <h5 class="content-group-lg">Create a new MINIM-UK account <small class="display-block">All fields except Avatar image are required</small></h5>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group has-feedback">
                                                  <label class="control-label">Select Admin Level For New User</label>
                                                    <div class="multi-select-full">
                                                        <select class="form-control" name="adminLevel">
                                                        
                                                            <option value="SuperAdmin">Access To All Collections and User Functions  [ SUPERADMIN ]</option>) 
                                                            <option value="Cataloguer">Access To All Collections  [ CATALOGUER ]</option>) 

                                                            <?php foreach($legalbodies as $legalbody): ?>
                                                                <option value="admin_<?php echo e($legalbody->legalBodyID); ?>"><?php echo e($legalbody->legalBodyName); ?>'s Collection Only [ ADMIN ]</option>
                                                            <?php endforeach; ?>

                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                 <div class="form-group has-feedback">
                                                  <label class="control-label">Default Collection <span style="font-size:9px;">(SuperAdmin And Cataloguer Only)</span></label>
                                                    <div class="multi-select-full">
                                                        <select class="form-control" name="legalBodyID">
                                                   
                                                            <?php foreach($legalbodies as $legalbody): ?>
                                                                <option value="<?php echo e($legalbody->legalBodyID); ?>"><?php echo e($legalbody->legalBodyName); ?></option>
                                                            <?php endforeach; ?>

                                                        </select>
                                                    </div>
                                                </div>
                                            </div>    
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group has-feedback <?php echo e($errors->has('name') ? 'has-error' : ''); ?>">
                                                <label class="control-label">First Name</label>
                                                    <input type="text" name="name" class="form-control" placeholder="First name" value="<?php echo e(Request::old('name')); ?>">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group has-feedback <?php echo e($errors->has('surname') ? 'has-error' : ''); ?>">
                                                <label class="control-label">Surname</label>
                                                    <input type="text" name="surname" class="form-control" placeholder="Surname" value="<?php echo e(Request::old('surname')); ?>">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group has-feedback <?php echo e($errors->has('password') ? 'has-error' : ''); ?>">
                                                <label class="control-label">Enter Password</label>
                                                    <input type="password" class="form-control" name="password" placeholder="Enter password" >
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group has-feedback <?php echo e($errors->has('confirmPassword') ? 'has-error' : ''); ?>">
                                                <label class="control-label">Repeat Password</label>
                                                    <input type="password" class="form-control" name="confirmPassword" placeholder="Repeat password">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group has-feedback <?php echo e($errors->has('email') ? 'has-error' : ''); ?>">
                                                <label class="control-label">Enter Email Address</label>
                                                    <input type="email" class="form-control" name="email" placeholder="Email address" value="<?php echo e(Request::old('email')); ?>">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group has-feedback <?php echo e($errors->has('confirmemail') ? 'has-error' : ''); ?>">
                                                <label class="control-label">Repeat Email Address</label>
                                                    <input type="email" class="form-control" name="confirmemail" placeholder="Repeat email address" value="<?php echo e(Request::old('confirmemail')); ?>">
                                                </div>
                                            </div>
                                        </div>
                                            <div class="col-md-12">
                                                <div class="form-group has-feedback">
                                                    <label class="control-label">Avatar Image:</label>
                                                    <div class="col-lg-12">
                                                        <input name="avatar" type="file" class="file-input-preview" data-show-remove="true">
                                                        <span class="help-block">Please upload an image. <code>jpg, gif, png</code> accepted.</span><br/>
                                                    </div>
                                                </div>
                                            </div>
                                        <div class="text-right">
                                            <button type="submit" class="btn bg-teal-400 btn-labeled btn-labeled-right ml-10"><b><i class="icon-plus3"></i></b> Create new MINIM UK account</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
<!-- /Register new user form -->
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('layouts.loggedin', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>