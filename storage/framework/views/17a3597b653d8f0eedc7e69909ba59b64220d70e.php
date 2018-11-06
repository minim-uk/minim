<?php $__env->startSection('title'); ?>
Report Problem
<?php $__env->stopSection(); ?>



<?php $__env->startSection('breadcrumbs'); ?>
                            <li><a href="<?php echo e(url('/')); ?>"><i class="icon-home2 position-left"></i>Dashboard</a></li>
                            <li class="active">Report Problem</li>
<?php $__env->stopSection(); ?>



<?php $__env->startSection('content'); ?>
<!-- Report problem form -->
                    <form id="reportproblem" method="POST" enctype="multipart/form-data" action="<?php echo e(url('/report-problem/store')); ?>" class="steps-validation">
                    <input type="hidden" name="id" value="0"/> 
                    <input type="hidden" name="avatarOrig" value=""/>
                   
                    <?php echo csrf_field(); ?>


                        <div class="row">
                            <div class="col-lg-12">
                                <div class="panel registration-form">
                                    <div class="panel-body">
                                        <div class="text-center">
                                            <div class="icon-object border-success text-success"><i class="icon-plus3"></i></div>
                                            <h5 class="content-group-lg">Report a problem <small class="display-block">All fields are required</small></h5>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group has-feedback">
                                                <label class="control-label">Problem Page (defaults to last page viewed)</label>
                                                    <input type="text" name="page_name" value="<?php echo e(URL::previous()); ?>" class="form-control" placeholder="Problem Page">
                                                    <div class="form-control-feedback">
                                                        <!--<i class="icon-user-check text-muted"></i>-->
                                                    </div>
                                                </div>
                                            </div>
                                        </div>    
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group has-feedback">
                                                <label class="control-label">Comments</label>
                                                <textarea name="comment" class="form-control" rows="4" cols="4" placeholder="Comments"></textarea>
                                                    <div class="form-control-feedback">
                                                        <!--<i class="icon-user-check text-muted"></i>-->
                                                    </div>
                                                </div>
                                            </div>
                                        </div>    
                                        <div class="text-right">
                                            <button type="submit" class="btn bg-teal-400 btn-labeled btn-labeled-right ml-10"><b><i class="icon-plus3"></i></b> Log a problem</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
<!-- /Report problem form -->
<style>
.delete a { color:red; float:right; }
.delete a:hover { font-weight: bold; }
</style>
	 <?php foreach($problems as $key => $problem): ?>
	                        <div class="row">
	                            <div class="col-lg-12">
	                                <div class="panel registration-form">
	                                    <div class="panel-body">
		                                     <p><strong>Date reported</strong>: <?php echo e($problem->problemDate); ?></p>
		                                     <p><strong>Page: </strong> <?php echo e($problem->page_name); ?></p>
		                                     <p><strong>Comment : </strong> <?php echo e($problem->comment); ?></p>
												<?php if(strlen($problem->replyText) < 1): ?>
		                                     	    <p><strong>Admin Reply: </strong><span style="color:green;">PLEASE WAIT</span></p>
		                                     	<?php else: ?>   
		                                     	    <p><strong>Admin Reply: [<?php echo e($problem->replyDate); ?>]</strong><br/><br/><span style="color:green;"><?php echo e($problem->replyText); ?></span></p>
		                                     	<?php endif; ?> 
	                                   	    <p class="delete"><a href="/report-problem/delete/<?php echo e($problem->reportProblemID); ?>">Delete report</a></p>
	                                    </div>
	                                </div>
	                            </div>
	                        </div>
	<?php endforeach; ?>
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('layouts.loggedin', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>