<!-- Main Content -->
<?php $__env->startSection('content'); ?>

                    <?php if(session('status')): ?>
                        <div class="alert alert-success">
                            <?php echo e(session('status')); ?>

                        </div>
                    <?php endif; ?>
                    <!-- Password recovery -->
                    <form role="form" method="POST" action="<?php echo e(url('/password/email')); ?>">
                        <?php echo csrf_field(); ?>

                        <div class="panel panel-body login-form">
                            <div class="text-center">
                                <div>
                                    <img src="<?php echo e(url('/images/admin/MINIM-UK_logo_black.png')); ?>" alt="">
                                </div>
                                <h5 class="content-group">Password recovery <small class="display-block">We'll send you instructions in an email</small></h5>
                            </div>

                            <div class="form-group<?php echo e($errors->has('email') ? ' has-error' : ''); ?> has-feedback">
                                <input type="email" class="form-control" placeholder="Your email" name="email" value="<?php echo e(old('email')); ?>">
                                <?php if($errors->has('email')): ?>
                                    <span class="help-block">
                                        <strong><?php echo e($errors->first('email')); ?></strong>
                                    </span>
                                <?php endif; ?>
                                <div class="form-control-feedback">
                                    <i class="icon-mail5 text-muted"></i>
                                </div>
                            </div>

                            <button type="submit" class="btn bg-blue btn-block">Reset your MINIM-UK password <i class="icon-arrow-right14 position-right"></i></button>
                        </div>
                    </form>
                    <!-- /password recovery -->
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>