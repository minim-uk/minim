<?php $__env->startSection('title'); ?>
<?php echo e($legalBodyName); ?> / <?php echo e($instrumentName); ?> / Add Resource
<?php $__env->stopSection(); ?>



<?php $__env->startSection('breadcrumbs'); ?>
                            <li><a href="<?php echo e(url('/')); ?>"><i class="icon-home2 position-left"></i>Dashboard</a></li>
                            <li><a href="<?php echo e(url('/instruments')); ?>"></i>Instruments </a></li>
                            <li><a href="/instruments/<?php echo e($legalBodyID); ?>"></i><?php echo e($legalBodyName); ?></a></li>
                            <li><a href="/edit-instrument/<?php echo e($instrumentID); ?>"></i><?php echo e($instrumentName); ?></a></li>
                            <li class="active">Add Resource</li>
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
                            
<!-- Add Resource form -->
                    <form id="editresource" method="POST" enctype="multipart/form-data" action="<?php echo e(url('/add-resource/store')); ?>" class="steps-validation">
                    <input type="hidden" name="instrumentID" value="<?php echo e($instrumentID); ?>"/>

                       <?php echo csrf_field(); ?>


                        <div class="row">
                            <div class="col-lg-8">
                                <div class="panel registration-form">
                                    <div class="panel-body">

                                        <h3><?php echo e($legalBodyName); ?> &gt; <?php echo e($instrumentName); ?> &gt; Add Resource</h3>  
  
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group has-feedback">
                                                <label class="control-label">Caption</label>
													 <textarea name="resourceCaption" rows="3" class="form-control" placeholder="Enter Caption (not required)"></textarea>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group has-feedback">
                                                    <label class="control-label">Upload New Resource File:</label>
                                                    <div class="col-lg-12">
                                                        <input name="resource" type="file" class="file-input-preview" data-show-remove="true">
                                                        	<span class="help-block"><strong>Images: </strong><code>jpg, gif, png</code> accepted. <strong>Audio: <code>mp3</code></strong> accepted. <strong>Video</strong><code>mp4</code> accepted.</span><br/>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <button type="submit" class="btn bg-teal-400 btn-labeled btn-labeled-right ml-10"><b><i class="icon-plus3"></i></b> Add New Resource</button>
                                            </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
<!-- /Add resource form-->      

            <!-- include rightside with instrument details -->        
            <?php echo $__env->make('partials/instrument_rightside', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

</div>
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('layouts.loggedin', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>