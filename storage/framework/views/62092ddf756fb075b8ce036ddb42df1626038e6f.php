 <?php foreach($thisevent as $key => $thisevent): ?>

<?php $__env->startSection('title'); ?>
<?php echo e($legalBodyName); ?> / <?php echo e($titleSingle); ?> / <?php echo e($eventType); ?> Event
<?php $__env->stopSection(); ?>



<?php $__env->startSection('breadcrumbs'); ?>
                            <li><a href="<?php echo e(url('/')); ?>"><i class="icon-home2 position-left"></i>Dashboard</a></li>
                            <li><a href="<?php echo e(url('/instruments')); ?>"></i>Instruments </a></li>
                            <li><a href="/instruments/<?php echo e($legalBodyID); ?>"><?php echo e($legalBodyName); ?></a></li>
                            <li><a href="/edit-instrument/<?php echo e($instrumentID); ?>"><?php echo e($titleSingle); ?></a></li>
                            <li class="active"><?php echo e($eventType); ?> Event</li>
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
        
                <!-- Production Event-->
                <div class="row">
                    <div class="col-lg-8">
                        <div class="panel registration-form" style="border:0;">
                            <div class="panel-body">
                                <h3><?php echo e($legalBodyName); ?> &gt; <?php echo e($titleSingle); ?> &gt; <?php echo e($eventType); ?> Event</h3>  
<!-- Edit Event Form -->
                    <form id="editevent" method="post" enctype="multipart/form-data" action="<?php echo e(url('/event/store')); ?>">
                    <input type="hidden" name="instrumentID" value="<?php echo e($instrumentID); ?>"/>
                    <input type="hidden" name="eventID" value="<?php echo e($eventID); ?>"/>
                    <!--hiddenID production location autocomplete -->
                    <input type="hidden" name="cityID" id="cityID" value="<?php echo e($thisevent->cityID); ?>"/>

                        <?php echo csrf_field(); ?>


                                    <div class="row" style="margin-bottom:10px;">
                                        <div class="col-md-4">
                                          <div class="form-group has-feedback">
                                            <label class="control-label">Event Type</label>
                                            <select name="eventType" class="form-control">
                                                <option <?php if($eventType == "Acquisition"): ?> selected="selected" <?php endif; ?>>Acquisition</option>
                                                <option <?php if($eventType == "Creation"): ?> selected="selected" <?php endif; ?>>Creation</option>
                                                <option <?php if($eventType == "Finding"): ?> selected="selected" <?php endif; ?>>Finding</option>
                                                <option <?php if($eventType == "Modification"): ?> selected="selected" <?php endif; ?>>Modification</option>
                                                <option <?php if($eventType == "Use"): ?> selected="selected" <?php endif; ?>>Use</option>
                                                <option <?php if($eventType == "Collecting"): ?> selected="selected" <?php endif; ?>>Collecting</option>
                                                <option <?php if($eventType == "Designing"): ?> selected="selected" <?php endif; ?>>Designing</option>
                                                <option <?php if($eventType == "Destruction"): ?> selected="selected" <?php endif; ?>>Destruction</option>
                                                <option <?php if($eventType == "Excavation"): ?> selected="selected" <?php endif; ?>>Excavation</option>
                                                <option <?php if($eventType == "Exhibition"): ?> selected="selected" <?php endif; ?>>Exhibition</option>
                                                <option <?php if($eventType == "Loss"): ?> selected="selected" <?php endif; ?>>Loss</option>
                                                <option <?php if($eventType == "Move"): ?> selected="selected" <?php endif; ?>>Move</option>
                                                <option <?php if($eventType == "Order"): ?> selected="selected" <?php endif; ?>>Order</option>
                                                <option <?php if($eventType == "Part addition"): ?> selected="selected" <?php endif; ?>>Part addition</option>
                                                <option <?php if($eventType == "Part removal"): ?> selected="selected" <?php endif; ?>>Part removal</option>
                                                <option <?php if($eventType == "Performance"): ?> selected="selected" <?php endif; ?>>Performance</option>
                                                <option <?php if($eventType == "Planning"): ?> selected="selected" <?php endif; ?>> Planning</option>
                                                <option <?php if($eventType == "Provenance"): ?> selected="selected" <?php endif; ?>>Provenance</option>
                                                <option <?php if($eventType == "Publication"): ?> selected="selected" <?php endif; ?>>Publication</option>
                                                <option <?php if($eventType == "Restoration"): ?> selected="selected" <?php endif; ?>>Restoration</option>
                                                <option <?php if($eventType == "Transformation"): ?> selected="selected" <?php endif; ?>>Transformation</option>
                                                <option <?php if($eventType == "Type assignment"): ?> selected="selected" <?php endif; ?>>Type assignment</option>
                                                <option <?php if($eventType == "Type creation"): ?> selected="selected" <?php endif; ?>>Type creation</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group has-feedback">
                                            <label class="control-label">Event Name</label>
                                            <input type="text" class="form-control" name="eventName" id="eventName" value="<?php echo e($thisevent->eventName); ?>" placeholder="Event Name" /> 
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group has-feedback">
                                            <label class="control-label">Event Location</label>
                                            <input type="text" class="form-control" name="location" id="location" value="<?php echo e($thisevent->location); ?>" placeholder="Event Location" /> 
                                        </div>
                                    </div>
                                  </div>

                                <div class="row" style="margin-bottom:10px;">
                                    <div class="col-md-3">
                                        <div class="form-group has-feedback">
                                            <label class="control-label">Event Earliest Date</label>
                                            <input type="text" class="form-control" name="earliestDate" value="<?php echo e($thisevent->eventEarliestDate); ?>" placeholder="Event Earliest Date" /> 
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group has-feedback">
                                            <label class="control-label">Event Latest Date</label>
                                            <input type="text" class="form-control" name="latestDate" value="<?php echo e($thisevent->eventLatestDate); ?>" placeholder="Event Latest Date" /> 
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group has-feedback">
                                            <label class="control-label">Event Date Text</label>
                                             <input type="text" class="form-control" name="eventDateText" value="<?php echo e($thisevent->eventDateText); ?>" placeholder="Event Date Text" /> 
                                        </div>
                                    </div>
                                </div>
                                <div class="row" style="margin-bottom:10px;">
                                    <div class="col-md-6">
                                        <div class="form-group has-feedback">
                                            <label class="control-label">Event Culture</label>
                                            <input type="text" class="form-control" name="eventCulture" value="<?php echo e($thisevent->eventCulture); ?>" placeholder="Event Culture" /> 
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group has-feedback">
                                            <label class="control-label">Event Period Name</label>
                                            <input type="text" class="form-control" name="periodName" value="<?php echo e($thisevent->periodName); ?>" placeholder="Event Period Name" /> 
                                        </div>
                                    </div>
                                </div>

                               <div class="row">
                                  <p style="margin-top:-10px; margin-left: 10px; font-size:15px;"><a href="/edit-instrument/<?php echo e($instrumentID); ?>/events/<?php echo e($thisevent->eventID); ?>/materials">Event Materials</a></p>
                               </div>   

                               <div class="row">
                                    <div class="text-right">
                                        <button type="submit" class="btn bg-teal-400 btn-labeled btn-labeled-right ml-10"><b><i class="icon-plus3"></i></b> Update <?php echo e($eventType); ?> Event</button>
                                    </div>
                                </div>
                         </div>
                    </form>
<!-- /Edit Event Form -->

       </div></div>
       <!-- /Production Event-->
<?php endforeach; ?>

            <!-- include rightside with instrument details -->        
            <?php echo $__env->make('partials/instrument_rightside', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

</div>
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('layouts.loggedin', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>