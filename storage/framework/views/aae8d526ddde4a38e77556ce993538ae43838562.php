<?php $__env->startSection('title'); ?>
<?php echo e($legalBodyName); ?> / <?php echo e($titleSingle); ?>

<?php $__env->stopSection(); ?>



<?php $__env->startSection('breadcrumbs'); ?>
                            <li><a href="<?php echo e(url('/')); ?>"><i class="icon-home2 position-left"></i>Dashboard</a></li>
                            <li><a href="<?php echo e(url('/instruments')); ?>"></i>Instruments </a></li>
                            <li><a href="/instruments/<?php echo e($legalBodyID); ?>"><?php echo e($legalBodyName); ?></a></li>
                            <li><a href="/edit-instrument/<?php echo e($instrumentID); ?>"><?php echo e($titleSingle); ?></a></li>

                            <?php if($eventType == "Production"): ?>
                            <li><a href="/production-event/<?php echo e($instrumentID); ?>">Production Event</a></li>
                            <?php else: ?>
                            <li><?php echo e($eventType); ?> Event</li>
                            <?php endif; ?>

                            <li class="active">Materials</li>

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

<?php /*  CATCH FORM ERRORS */ ?>
<?php echo $__env->make('partials/message-block', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>       

<?php /* event materials */ ?>   
            <form id="eventMaterials" method="post" class="steps-validation" action="<?php echo e(url('/event-materials/store')); ?>">
           
            <?php echo csrf_field(); ?>

           
            <input type="hidden" name="legalBodyID" id="legalBodyID" value="<?php echo e($legalBodyID); ?>">
            <input type="hidden" name="instrumentID" id="instrumentID" value="<?php echo e($instrumentID); ?>">
            <input type="hidden" name="eventID" id="eventID" value="<?php echo e($eventID); ?>">
            <input type="hidden" name="eventType" id="eventType" value="<?php echo e($eventType); ?>">

            <h6>Materials For <?php echo e($legalBodyName); ?> / <?php echo e($instrumentName); ?>'s <?php echo e($eventType); ?> Event</h6>
            <section data-step="1">

                         <div class="row">    
                                    <div class="col-xs-12">
                                        <div class="form-group has-feedback">
                                            <label class="control-label">Materials Free Text</label>
                                            <textarea rows="3" class="form-control" name="materialsFreeText" placeholder="Enter Materials Free Text"><?php echo e($materialsFreeText); ?></textarea>
                                        </div>    
                                    </div>                        
                                </div>

                                <div class="row" style="margin-bottom:10px; margin-top: 30px;">
                                    <div class="col-md-4">
                                        <label class="control-label">Specific Materials</label>
                                    </div>
                                </div>
                                 <?php foreach($materials as $key => $material): ?>
                                        <?php /* */$thiskey=$key+1001;/* */ ?>
                                        <script>
                                                    $('.remove-me<?php echo e($thiskey); ?>').click(function(e){
                                                          e.preventDefault();
                                                        var fieldNum = <?php echo e($thiskey); ?>;
                                                        var fieldID = "#material" + fieldNum;
                                                        $(this).remove();
                                                        $(fieldID).remove();
                                                    });
                                        </script>            
                                        <?php if(strlen($material) > 0): ?>
                                           <div class="row" style="margin-bottom: 15px;" id="material<?php echo e($thiskey); ?>">
                                                <div class="col-md-11">
                                                    <div class="form-group has-feedback">
                                                        <label class="control-label">Existing Material</label>
                                                        <input type="text" class="form-control" value="<?php echo e($material); ?>" name="materials[<?php echo e($thiskey); ?>][material]" placeholder="Enter One Specific Material" />
                                                    </div>    
                                                </div>
                                                   <div class="col-md-1">
                                                    <button style="margin-top:25px;" type="button" class="btn btn-default remove-me<?php echo e($thiskey); ?>"><i class="glyphicon glyphicon-minus"></i></button>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                 <?php endforeach; ?>    

                               <div class="row" style="margin-bottom: 15px;">
                                    <div class="col-md-11">
                                        <div class="form-group has-feedback">
                                            <label class="control-label">Specific Material</label>
                                            <input type="text" class="form-control" name="materials[0][material]" placeholder="Enter One Specific Material" />
                                       </div>     
                                    </div>
                                       <div class="col-md-1">
                                        <button style="margin-top:25px;" type="button" style="margin-top:25px;" class="btn btn-default addSerialButton"><i class="glyphicon glyphicon-plus"></i></button>
                                    </div>
                                </div>
                              <div class="form-group hide" id="materialTemplate">
                                <div class="row" style="margin-bottom: 15px;">
                                    <div class="col-md-11">
                                        <div class="form-group has-feedback">                               
                                            <label class="control-label">Specific Material</label>
                                            <input type="text" class="form-control" name="material" placeholder="Enter One Specific Material" />
                                        </div>    
                                    </div>
                                       <div class="col-md-1">
                                        <button type="button" style="margin-top:25px;" class="btn btn-default removeSerialButton"><i class="glyphicon glyphicon-minus"></i></button>
                                    </div>
                                </div>
                              </div> 





            </section>  
            </form>
            </div></div></div>
              <!-- /event materials -->

            <!-- include rightside with instrument details -->        
            <?php echo $__env->make('partials/instrument_rightside', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

     </div></div>
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('layouts.loggedin', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>