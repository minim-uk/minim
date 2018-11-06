<?php $__env->startSection('title'); ?>
<?php echo e($legalBodyName); ?> / <?php echo e($titleSingle); ?> / Production Event
<?php $__env->stopSection(); ?>



<?php $__env->startSection('breadcrumbs'); ?>
                            <li><a href="<?php echo e(url('/')); ?>"><i class="icon-home2 position-left"></i>Dashboard</a></li>
                            <li><a href="<?php echo e(url('/instruments')); ?>"></i>Instruments </a></li>
                            <li><a href="/instruments/<?php echo e($legalBodyID); ?>"><?php echo e($legalBodyName); ?></a></li>
                            <li><a href="/edit-instrument/<?php echo e($instrumentID); ?>"><?php echo e($titleSingle); ?></a></li>
                            <li class="active">Production Event</li>
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
                                <h3 style="margin-bottom:10px;"><?php echo e($legalBodyName); ?> &gt; <?php echo e($titleSingle); ?> &gt; Production Event</h3>  
                    <form id="addevent" method="post" action="<?php echo e(url('/production-event/store')); ?>">
                    <!-- hiddenID for instrument -->
                    <input type="hidden" name="instrumentID" value="<?php echo e($instrumentID); ?>"/>
                    <!--hiddenID production location autocomplete -->
                    <input type="hidden" name="cityID" id="cityID" value="<?php echo e($cityID); ?>"/>

                        <?php echo csrf_field(); ?>


                                    <div class="row" style="margin-bottom:10px;">
                                        <div class="col-md-4">
                                          <div class="form-group has-feedback">
                                            <label class="control-label">Event Type</label>
                                            <select name="eventType" class="form-control" disabled>
                                                <option>Production</option></select>
                                          </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group has-feedback">
                                                <label class="control-label">Event Name</label>
                                                <input type="text" class="form-control" name="productionEventName" id="productionEventName" value="<?php echo e($productionEventName); ?>" placeholder="Event Name" /> 
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                          <div class="form-group has-feedback">
                                            <label class="control-label">Production Location</label>
                                            <input type="text" class="form-control" name="productionEventLocation" id="productionEventLocation" value="<?php echo e($productionEventLocation); ?>" placeholder="Production Event Location"/>
                                         </div>
                                       </div>
                                      </div>

                                <div class="row" style="margin-bottom:10px;">
                                    <div class="col-md-3">
                                        <div class="form-group has-feedback">
                                            <label class="control-label">Production Earliest Date</label>
                                             <input type="text" class="form-control" name="productionEventEarliestDate" value="<?php echo e($productionEventEarliestDate); ?>" placeholder="Production Earliest Date" /> 
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group has-feedback">
                                            <label class="control-label">Production Latest Date</label>
                                             <input type="text" class="form-control" name="productionEventLatestDate" value="<?php echo e($productionEventLatestDate); ?>" placeholder="Production Latest Date" /> 
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group has-feedback">
                                            <label class="control-label">Production Date Text</label>
                                             <input type="text" class="form-control" name="productionEventDateText" value="<?php echo e($productionEventDateText); ?>" placeholder="Production Event Date Text" /> 
                                        </div>
                                    </div>
                              </div>      
                              <div class="row" style="margin-bottom:10px;">
                                    <div class="col-md-6">
                                        <div class="form-group has-feedback">
                                            <label class="control-label">Production Period Name</label>
                                            <input type="text" class="form-control" name="productionPeriodName" value="<?php echo e($productionPeriodName); ?>" placeholder="Production Period Name" /> 
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group has-feedback">
                                            <label class="control-label">Production Culture</label>
                                            <input type="text" class="form-control" name="productionEventCulture" value="<?php echo e($productionEventCulture); ?>" placeholder="Production Culture" /> 
                                        </div>
                                    </div>                                    
                                </div>

                               <div class="row">
                                  <p style="margin-top:-10px; margin-left: 10px; font-size:15px;"><a href="/production-event/<?php echo e($instrumentID); ?>/materials">Production Event Materials</a></p>
                               </div>   

                               <div class="row">
                                    <div class="text-right">
                                         <button type="submit" class="btn bg-teal-400 btn-labeled btn-labeled-right ml-10"><b><i class="icon-plus3"></i></b> Update Production Event</button>
                                    </div>
                                </div>
                           </div>

                    </form>
<!-- /Edit Production Event Form -->

</div></div>
<!-- /Production Event-->

            <!-- include rightside with instrument details -->        
            <?php echo $__env->make('partials/instrument_rightside', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
</div>
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('layouts.loggedin', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>