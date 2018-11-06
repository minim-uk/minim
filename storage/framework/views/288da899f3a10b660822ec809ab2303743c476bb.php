<?php $__env->startSection('title'); ?>
<?php echo e($legalBodyName); ?> / <?php echo e($titleSingle); ?> / <?php echo e($eventType); ?> Event / Add Actor
<?php $__env->stopSection(); ?>



<?php $__env->startSection('breadcrumbs'); ?>
                            <li><a href="<?php echo e(url('/')); ?>"><i class="icon-home2 position-left"></i>Dashboard</a></li>
                            <li><a href="<?php echo e(url('/instruments')); ?>"></i>Instruments </a></li>
                            <li><a href="/instruments/<?php echo e($legalBodyID); ?>"><?php echo e($legalBodyName); ?></a></li>
                            <li><a href="/edit-instrument/<?php echo e($instrumentID); ?>"><?php echo e($titleSingle); ?></a></li>
                            <?php if($eventType == "Production"): ?>
                            <li><a href="/production-event/<?php echo e($instrumentID); ?>">Production Event</a></li>
                            <?php else: ?>
                            <li><a href="/edit-instrument/<?php echo e($instrumentID); ?>/events/<?php echo e($eventID); ?>"><?php echo e($eventType); ?> Event</a></li>
                            <?php endif; ?>
                            <li class="active">Add Actor</li>
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

                <!-- Add Actor -->
                <div class="row">
                    <div class="col-lg-8">
                        <div class="panel registration-form" style="border:0;">
                            <div class="panel-body">
                                 <h3><?php echo e($legalBodyName); ?> &gt; <?php echo e($titleSingle); ?> &gt; <?php echo e($eventType); ?> Event &gt; Add Actor</h3>             
<!-- Add Actor Form -->
                    <form id="addactor" method="post" enctype="multipart/form-data" action="<?php echo e(url('/actor/store')); ?>">
                    <input type="hidden" name="instrumentID" value="<?php echo e($instrumentID); ?>"/>
<?php if($eventType == "Production"): ?><input type="hidden" name="eventID" value="Production"/><?php endif; ?>
<?php if($eventType != "Production"): ?><input type="hidden" name="eventID" value="<?php echo e($eventID); ?>"/><?php endif; ?>
                    <input type="hidden" name="mode" value="new">

                    <!-- hidden input for actorID-->
                    <input type="hidden" name="actorID" id="actorID">

                        <?php echo csrf_field(); ?>


                                        <div class="row" style="margin-bottom:10px;">
                                            <div class="col-xs-12">
                                                <div class="form-group has-feedback">
                                                <label class="control-label">Actor Name</label>
                                                    <input type="text" class="form-control actor_name" name="actorname" id="actorname" placeholder="Actor Name" /> 
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row" style="margin-bottom:10px;">
                                            <div class="col-xs-6">
                                                <div class="form-group has-feedback">
                                                <label class="control-label">Actor Type</label>
                                                    <input type="text" class="form-control" name="eventActorType" placeholder="Actor Type" /> 
                                                </div>
                                            </div>
                                            <div class="col-xs-6">
                                                <div class="form-group has-feedback">
                                                <label class="control-label">Actor Source</label>
                                                    <input type="text" class="form-control" name="eventActorSource" placeholder="Actor Source" /> 
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row" style="margin-bottom:10px;">
                                            <div class="col-xs-4">
                                                <div class="form-group has-feedback">
                                                <label class="control-label">Actor Nationality</label>
                                                    <input type="text" class="form-control" name="eventActorNationality" placeholder="Actor Nationality" /> 
                                                </div>
                                            </div>
                                            <div class="col-xs-4">
                                                <div class="form-group has-feedback">
                                                <label class="control-label">Actor Birth Date</label>
                                                    <input type="text" class="form-control" name="eventActorBirthDate" placeholder="Actor Birth Date" /> 
                                                </div>
                                            </div>
                                            <div class="col-xs-4">
                                                <div class="form-group has-feedback">
                                                <label class="control-label">Actor Death Date</label>
                                                    <input type="text" class="form-control" name="eventActorDeathDate" placeholder="Actor Death Date" /> 
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row" style="margin-bottom:10px;">
                                            <div class="col-xs-4">
                                                <div class="form-group has-feedback">
                                                <label class="control-label">Actor Gender</label>
                                                    <select class="form-control" name="eventActorGender">
                                                         <option value="Male">Male</option>
                                                         <option value="Female">Female</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-xs-8">
                                                <div class="form-group has-feedback">
                                                <label class="control-label">Actor Display Role</label>
                                                    <input type="text" class="form-control" name="eventDisplayActorRole" placeholder="Actor Display Role" /> 
                                                </div>
                                            </div>
                                        </div>
                               <div class="row">
                                    <div class="text-right">
                                        <button type="submit" class="btn bg-teal-400 btn-labeled btn-labeled-right ml-10"><b><i class="icon-plus3"></i></b> Add Actor To <?php echo e($eventType); ?> Event</button>
                                    </div>
                                </div>
</form>
                                </div>
              </div></div>
              <!-- /Add Actor Form-->

            <!-- include rightside with instrument details -->        
            <?php echo $__env->make('partials/instrument_rightside', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

</div>
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('layouts.loggedin', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>