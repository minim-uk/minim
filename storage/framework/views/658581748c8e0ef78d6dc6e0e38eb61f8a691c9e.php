<?php $__env->startSection('title'); ?>
<?php echo e($legalBodyName); ?> / <?php echo e($titleSingle); ?> / Add Event
<?php $__env->stopSection(); ?>



<?php $__env->startSection('breadcrumbs'); ?>
                            <li><a href="<?php echo e(url('/')); ?>"><i class="icon-home2 position-left"></i>Dashboard</a></li>
                            <li><a href="<?php echo e(url('/instruments')); ?>"></i>Instruments </a></li>
                            <li><a href="/instruments/<?php echo e($legalBodyID); ?>"><?php echo e($legalBodyName); ?></a></li>
                            <li><a href="/edit-instrument/<?php echo e($instrumentID); ?>"><?php echo e($titleSingle); ?></a></li>
                            <li class="active">Add Event</li>
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
                <div class="row">
                    <div class="col-lg-8">
                        <div class="panel registration-form" style="border:0;">
                            <div class="panel-body">
                                 <h3><?php echo e($legalBodyName); ?> &gt; <?php echo e($instrumentName); ?> &gt; Add Event</h3>  
<!-- Add Event Form -->
                    <form id="addevent" method="post" enctype="multipart/form-data" action="<?php echo e(url('/event/store')); ?>">
                    <input type="hidden" name="instrumentID" value="<?php echo e($instrumentID); ?>"/>
                    <input type="hidden" name="eventID" value="NEWEVENT"/>
                    <!--hiddenID production location autocomplete -->
                    <input type="hidden" name="cityID" id="cityID" value=""/>

                        <?php echo csrf_field(); ?>


                                    <div class="row" style="margin-bottom:10px;">
                                        <div class="col-md-4">
                                          <div class="form-group has-feedback">
                                            <label class="control-label">Event Type</label>
                                            <select name="eventType" class="form-control">
                                                <option>Acquisition</option>
                                                <option>Creation</option>
                                                <option>Finding</option>
                                                <option>Acquisition</option>
                                                <option>Modification</option>
                                                <option>Use</option>
                                                <option>Collecting</option>
                                                <option>Designing</option>
                                                <option>Destruction</option>
                                                <option>Excavation</option>
                                                <option>Exhibition</option>
                                                <option>Loss</option>
                                                <option>Move</option>
                                                <option>Order</option>
                                                <option>Part addition</option>
                                                <option>Part removal</option>
                                                <option>Performance</option>
                                                <option>Planning</option>
                                                <option>Provenance</option>
                                                <option>Publication</option>
                                                <option>Restoration</option>
                                                <option>Transformation</option>
                                                <option>Type assignment</option>
                                                <option>Type creation</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group has-feedback">
                                            <label class="control-label">Event Location</label>
                                            <input type="text" class="form-control" name="location" id="location" placeholder="Event Location" /> 
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group has-feedback">
                                            <label class="control-label">Event Culture</label>
                                            <input type="text" class="form-control" name="eventCulture" placeholder="Event Culture" /> 
                                        </div>
                                    </div>
                                </div>
                                <div class="row" style="margin-bottom:10px;">
                                    <div class="col-md-3">
                                        <div class="form-group has-feedback">
                                            <label class="control-label">Event Earliest Date</label>
                                            <input type="text" class="form-control" name="earliestDate" placeholder="Event Earliest Date" /> 
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group has-feedback">
                                            <label class="control-label">Event Latest Date</label>
                                            <input type="text" class="form-control" name="latestDate" placeholder="Event Latest Date" /> 
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group has-feedback">
                                            <label class="control-label">Event Date Text</label>
                                             <input type="text" class="form-control" name="eventDateText" placeholder="Event Date Text" /> 
                                        </div>
                                    </div>
                                </div>
                                <div class="row" style="margin-bottom:10px;">
                                    <div class="col-md-6">
                                        <div class="form-group has-feedback">
                                            <label class="control-label">Event Period Name</label>
                                            <input type="text" class="form-control" name="periodName" placeholder="Event Period Name" /> 
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group has-feedback">
                                            <label class="control-label">Event Materials</label>
                                            <input type="text" class="form-control" name="materialsText" placeholder="Materials Freetext" /> 
                                        </div>
                                    </div>
                                </div>
                               <div class="row">
                                    <div class="text-right">
                                        <button type="submit" class="btn bg-teal-400 btn-labeled btn-labeled-right ml-10"><b><i class="icon-plus3"></i></b> Add Event</button>
                                    </div>
                                </div>
                            </div>
                      </form>
              </div></div>         
<!-- /Add Event Form -->
            
            <!-- include rightside with instrument details -->        
            <?php echo $__env->make('partials/instrument_rightside', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

</div></div>
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('layouts.loggedin', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>