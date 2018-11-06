<?php $__env->startSection('title'); ?>
Not Found!
<?php $__env->stopSection(); ?>



<?php $__env->startSection('breadcrumbs'); ?>
    <li class="active"><i class="icon-home2 position-left"></i> Not Found!</li>
<?php $__env->stopSection(); ?>



<?php $__env->startSection('content'); ?>
                <!-- Content area -->
                <div class="content">
                    <!-- Error title -->
                    <div class="text-center content-group">
                        <h1 class="error-title offline-title">Not Found!</h1>
                        <h5>Sorry, that content has been moved, deleted, or never existed.</h5>
                    </div>
                    <!-- /error title -->
                    <!-- Error content -->
                    <div class="row">
                        <div class="col-lg-4 col-lg-offset-4 col-sm-6 col-sm-offset-3">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <a href="#" onclick="window.history.go(-1); return false;" class="btn btn-primary btn-block content-group"><i class="icon-circle-left2 position-left"></i> Prev</a>
                                    </div>
                                    <div class="col-sm-4">
                                        <a href="/" class="btn btn-primary btn-block content-group"><i class="icon-circle-right2 position-left"></i> Dash</a>
                                    </div>
                                    <div class="col-sm-4">
                                        <a href="/report-problem" class="btn btn-warning btn-block content-group"><i class="icon-bug2 position-left"></i> Report</a>
                                    </div>
                        </div>
                    </div>
                    <!-- /error wrapper -->
                  </div><!-- /dashboard content -->
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('layouts.loggedin', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>