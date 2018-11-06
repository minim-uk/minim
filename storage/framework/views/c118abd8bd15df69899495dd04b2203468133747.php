    <?php foreach($resource as $key => $resource): ?>


<?php $__env->startSection('title'); ?>
<?php echo e($legalBodyName); ?> / <?php echo e($titleSingle); ?>

<?php $__env->stopSection(); ?>



<?php $__env->startSection('breadcrumbs'); ?>


                            <li><a href="<?php echo e(url('/')); ?>"><i class="icon-home2 position-left"></i>Dashboard</a></li>
                            <li><a href="<?php echo e(url('/instruments')); ?>"></i>Instruments </a></li>
                            <li><a href="/instruments/<?php echo e($legalBodyID); ?>"></i><?php echo e($legalBodyName); ?></a></li>
                            <li><a href="/edit-instrument/<?php echo e($instrumentID); ?>"></i><?php echo e($instrumentName); ?></a></li>
 <?php if($resource->resourceType == "image"): ?>
                            <li><a href="/edit-instrument/<?php echo e($instrumentID); ?>/images"></i>Manage Images</a></li>
 <?php endif; ?>
 <?php if($resource->resourceType == "video"): ?>
                            <li><a href="/edit-instrument/<?php echo e($instrumentID); ?>/video"></i>Manage Video</a></li>
 <?php endif; ?>
 <?php if($resource->resourceType == "sound"): ?>
                            <li><a href="/edit-instrument/<?php echo e($instrumentID); ?>/audio"></i>Manage Audio</a></li>
 <?php endif; ?>
                            <li><a href="/edit-instrument/<?php echo e($instrumentID); ?>/resource/<?php echo e($resource->resourceID); ?>"><?php echo e($resource->resourceFileName); ?></a></li>
                            <li class="active">Rights Information</li>

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

<?php /* EDIT INSTRUMENT FORM WIZARD STEPS */ ?>   
                        <form id="instrumentRights" method="post" class="steps-validation" action="<?php echo e(url('/edit-resource/storerights')); ?>">
                       
                        <?php echo csrf_field(); ?>

                       
                        <input type="hidden" name="legalBodyID" id="legalBodyID" value="<?php echo e($legalBodyID); ?>">
                        <input type="hidden" name="instrumentID" id="instrumentID" value="<?php echo e($instrumentID); ?>">
                        <input type="hidden" name="resourceID" id="resourceID" value="<?php echo e($resource->resourceID); ?>">
                        <input type="hidden" name="resourceType" id="resourceType" value="<?php echo e($resource->resourceType); ?>">

                            <h6>Rights Information For This Resource (<?php echo e($legalBodyName); ?> / <?php echo e($instrumentName); ?> -> <?php echo e($resource->resourceFileName); ?> [<?php echo e($resource->resourceType); ?>] )</h6>
  <section data-step="1">
<?php foreach($resourceRights as $key => $rights_information): ?>
        <?php /* */$thiskey=$key+1001;/* */ ?>
        <script>
                    $('.remove-rights<?php echo e($thiskey); ?>').click(function(e){
                          e.preventDefault();
                        var fieldNum = <?php echo e($thiskey); ?>;
                        var fieldID = "#rights" + fieldNum;
                        $(this).remove();
                        $(fieldID).remove();
                    });
        </script>    


                            <div id="rights<?php echo e($thiskey); ?>">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group has-feedback">      
                                            <label class="control-label">Rights Type</label>
                                           
                                                <input style="font-weight:bold;" type="text" class="form-control" name="rights[<?php echo e($thiskey); ?>][rightsType]" value="<?php echo e($rights_information->rightsType); ?>" placeholder="Type" />
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group has-feedback">      
                                            <label class="control-label">Rights Earliest Date</label>
                                           
                                                <input style="font-weight:bold;" type="text" class="form-control" name="rights[<?php echo e($thiskey); ?>][rightsEarliestDate]" value="<?php echo e($rights_information->rightsEarliestDate); ?>" placeholder="Earliest Date" />
                                        </div>
                                    </div>
                                 </div>   
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group has-feedback">      
                                            <label class="control-label">Rights Latest Date</label>
                                           
                                                <input style="font-weight:bold;" type="text" class="form-control" name="rights[<?php echo e($thiskey); ?>][rightsLatestDate]" value="<?php echo e($rights_information->rightsLatestDate); ?>" placeholder="Latest Date" />
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group has-feedback">      
                                            <label class="control-label">Rights Holder Name</label>
                                           
                                                <input style="font-weight:bold;" type="text" class="form-control" name="rights[<?php echo e($thiskey); ?>][rightsHolderName]" value="<?php echo e($rights_information->rightsHolderName); ?>" placeholder="Rights Holder Name" />
                                        </div>
                                    </div>
                                </div> 
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group has-feedback">      
                                            <label class="control-label">Rights Holder Website</label>
                                           
                                                <input style="font-weight:bold;" type="text" class="form-control" name="rights[<?php echo e($thiskey); ?>][rightsHolderWebsite]" value="<?php echo e($rights_information->rightsHolderWebsite); ?>" placeholder="Rights Holder Name" />
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group has-feedback">      
                                            <label class="control-label">Rights Holder ID</label>
                                           
                                                <input style="font-weight:bold;" type="text" class="form-control" name="rights[<?php echo e($thiskey); ?>][rightsHolderID]" value="<?php echo e($rights_information->rightsHolderID); ?>" placeholder="Rights Holder ID" />
                                        </div>
                                    </div>
                                </div>                                   
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group has-feedback">      
                                            <label class="control-label">Rights CreditLine</label>
                                               <textarea rows="5" class="form-control" name="rights[<?php echo e($thiskey); ?>][rightsCreditLine]" placeholder="Text"><?php echo e($rights_information->rightsCreditLine); ?></textarea>
                                        </div>    
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-1">
                                        <button type="button" style="margin-top:25px;" class="btn btn-default remove-rights<?php echo e($thiskey); ?>"><i class="glyphicon glyphicon-minus"></i></button>
                                    </div> 
                                </div>
                            </div>  


                                
<hr/>

<?php endforeach; ?>   


                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group has-feedback">      
                                            <label class="control-label">Rights Type</label>
                                           
                                                <input style="font-weight:bold;" type="text" class="form-control" name="rights[0][rightsType]" placeholder="Type" />
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group has-feedback">      
                                            <label class="control-label">Rights Earliest Date</label>
                                           
                                                <input style="font-weight:bold;" type="text" class="form-control" name="rights[0][rightsEarliestDate]" placeholder="Earliest Date" />
                                        </div>
                                    </div>
                                 </div>   
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group has-feedback">      
                                            <label class="control-label">Rights Latest Date</label>
                                           
                                                <input style="font-weight:bold;" type="text" class="form-control" name="rights[0][rightsLatestDate]" placeholder="Latest Date" />
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group has-feedback">      
                                            <label class="control-label">Rights Holder Name</label>
                                           
                                                <input style="font-weight:bold;" type="text" class="form-control" name="rights[0][rightsHolderName]" placeholder="Rights Holder Name" />
                                        </div>
                                    </div>
                                </div> 
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group has-feedback">      
                                            <label class="control-label">Rights Holder Website</label>
                                           
                                                <input style="font-weight:bold;" type="text" class="form-control" name="rights[0][rightsHolderWebsite]" placeholder="Rights Holder Name" />
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group has-feedback">      
                                            <label class="control-label">Rights Holder ID</label>
                                           
                                                <input style="font-weight:bold;" type="text" class="form-control" name="rights[0][rightsHolderID]" placeholder="Rights Holder ID" />
                                        </div>
                                    </div>
                                </div>                                   
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group has-feedback">      
                                            <label class="control-label">Rights CreditLine</label>
                                               <textarea rows="5" class="form-control" name="rights[0][rightsCreditLine]" placeholder="Text"></textarea>
                                        </div>    
                                    </div>
                                </div>
                             

                                  <div class="row">
                                    <div class="col-md-1">
                                        <button type="button" style="margin-top:25px;" class="btn btn-default addButton"><i class="glyphicon glyphicon-plus"></i></button>
                                    </div>

                                    <div class="form-group hide" id="bookTemplate" style="clear:left;">
   
<br style="clear:both;">                             
<hr/>

                                            <div class="row" style="clear:left; margin-top:20px;">
                                                <div class="col-md-6">
                                                    <div class="form-group has-feedback">      
                                                        <label class="control-label">Rights Type</label>
                                                       
                                                            <input style="font-weight:bold;" type="text" class="form-control" name="rightsType" placeholder="Type" />
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group has-feedback">      
                                                        <label class="control-label">Rights Earliest Date</label>
                                                       
                                                            <input style="font-weight:bold;" type="text" class="form-control" name="rightsEarliestDate" placeholder="Earliest Date" />
                                                    </div>
                                                </div>
                                             </div>   
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group has-feedback">      
                                                        <label class="control-label">Rights Latest Date</label>
                                                       
                                                            <input style="font-weight:bold;" type="text" class="form-control" name="rightsLatestDate" placeholder="Latest Date" />
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group has-feedback">      
                                                        <label class="control-label">Rights Holder Name</label>
                                                       
                                                            <input style="font-weight:bold;" type="text" class="form-control" name="rightsHolderName" placeholder="Rights Holder Name" />
                                                    </div>
                                                </div>
                                            </div> 
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group has-feedback">      
                                                        <label class="control-label">Rights Holder Website</label>
                                                       
                                                            <input style="font-weight:bold;" type="text" class="form-control" name="rightsHolderWebsite" placeholder="Rights Holder Name" />
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group has-feedback">      
                                                        <label class="control-label">Rights Holder ID</label>
                                                       
                                                            <input style="font-weight:bold;" type="text" class="form-control" name="rightsHolderID" placeholder="Rights Holder ID" />
                                                    </div>
                                                </div>
                                            </div>                                   
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group has-feedback">      
                                                        <label class="control-label">Rights CreditLine</label>
                                                           <textarea rows="5" class="form-control" name="rightsCreditLine" placeholder="Text"></textarea>
                                                    </div>    
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-1">
                                                    <button type="button" style="margin-top:25px;" class="btn btn-default removeButton"><i class="glyphicon glyphicon-minus"></i></button>
                                                </div> 
                                            </div>


                                    </div>
                                </div>    

                            </section>  
                        </form>
              </div></div></div>
              <!-- /edit instrument wizard -->

            <!-- include rightside with instrument details -->        
            <?php echo $__env->make('partials/instrument_rightside', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

    <?php endforeach; ?>

     </div></div>
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('layouts.loggedin', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>