<?php $__env->startSection('content'); ?>
                    <!-- login -->
                    <form role="form" method="POST" action="<?php echo e(url('/login')); ?>">
                      <?php echo csrf_field(); ?>

                        <div class="panel panel-body login-form">
                            <div class="text-center">
                                <div>
                                    <img src="<?php echo e(url('/images/admin/MINIM-UK_logo_black.png')); ?>" alt="">
                                </div>
                                <h5 class="content-group-lg">Login to your MINIM-UK account <small class="display-block">Please enter your details</small></h5>
                            </div>
                            <div class="form-group<?php echo e($errors->has('email') ? ' has-error' : ''); ?> has-feedback has-feedback-left">
                                <input type="email" class="form-control input-lg" name="email" placeholder="Email" value="<?php echo e(old('email')); ?>">
                                <?php if($errors->has('email')): ?>
                                    <span class="help-block">
                                        <strong><?php echo e($errors->first('email')); ?></strong>
                                    </span>
                                <?php endif; ?>
                                <div class="form-control-feedback">
                                    <i class="icon-user text-muted"></i>
                                </div>
                            </div>
                            <div class="form-group<?php echo e($errors->has('password') ? ' has-error' : ''); ?> has-feedback has-feedback-left">
                                <input type="password" name="password" class="form-control input-lg" placeholder="Password">
                                <?php if($errors->has('password')): ?>
                                    <span class="help-block">
                                        <strong><?php echo e($errors->first('password')); ?></strong>
                                    </span>
                                <?php endif; ?>
                                <div class="form-control-feedback">
                                    <i class="icon-lock2 text-muted"></i>
                                </div>
                            </div>
                            <div class="form-group login-options">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <label class="checkbox-inline">
                                            <input type="checkbox" class="styled" checked="checked" name="remember">
                                            Remember
                                        </label>
                                    </div>

                                    <div class="col-sm-6 text-right">
                                        <a href="<?php echo e(url('/password/reset')); ?>">Forgot password?</a>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn bg-blue btn-block btn-lg">Login to MINIM-UK<i class="icon-arrow-right14 position-right"></i></button>
                            </div>
                            <span class="help-block text-center">By logging into MINIM-UK, you're confirming that you've read and agree to our <a href="#">Terms and Conditions</a> and <a href="#">Cookie Policy</a></span>
                        </div>
                    </form>
                    <!-- /login -->
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>