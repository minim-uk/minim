<?php $__env->startSection('title'); ?>
<?php echo e($legalBodyName); ?> / <?php echo e($titleSingle); ?>

<?php $__env->stopSection(); ?>



<?php $__env->startSection('breadcrumbs'); ?>
                            <li><a href="<?php echo e(url('/')); ?>"><i class="icon-home2 position-left"></i>Dashboard</a></li>
                            <li><a href="<?php echo e(url('/instruments')); ?>"></i>Instruments </a></li>
                            <li><a href="/instruments/<?php echo e($legalBodyID); ?>"><?php echo e($legalBodyName); ?></a></li>
                            <li class="active"><?php echo e($titleSingle); ?></li>
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
                        <form id="profileForm" method="post" class="steps-validation" action="<?php echo e(url('/edit-instrument/store')); ?>">
                       
                        <?php echo csrf_field(); ?>

                       
                        <!-- hidden input for thesuarusID-->
                        <input type="hidden" name="legalBodyID" id="legalBodyID" value="<?php echo e($legalBodyID); ?>">
                        <input type="hidden" name="instrumentID" id="instrumentID" value="<?php echo e($instrumentID); ?>">
                        <!-- hidden input for thesaurusID-->
                        <input type="hidden" name="thesaurusID" id="thesaurusID" value="<?php echo e($thesaurusID); ?>">
                        <!-- hidden input for hornbostelID-->
                        <input type="hidden" name="hornbostelID" id="hornbostelID" value="<?php echo e($hornbostelID); ?>">

                            <h6>Basics</h6>
                            <section data-step="0">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group has-feedback <?php echo e($errors->has('titlePreferred') ? 'has-error' : ''); ?>">
                                            <label class="control-label">Instrument Classification / Name</label>
                                            <br/>
                                            <input type="text" style="font-weight: bold;" class="form-control" name="instrument_name" id="instrument_name" value="<?php echo e($titlePreferred); ?>" placeholder="Enter Instrument Name" />
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group has-feedback <?php echo e($errors->has('instrument_classifiedTitlePreferred') ? 'has-error' : ''); ?>">
                                            <label class="control-label">If Classified, Enter More Specific Name If Relevent</label>
                                            <br/>
                                            <input type="text" style="font-weight: bold;" class="form-control" name="instrument_classifiedTitlePreferred" id="instrument_classifiedTitlePreferred" placeholder="If Classified, Enter More Specific Instrument Name" value="<?php echo e($instrument_classifiedTitlePreferred); ?>" />
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group has-feedback <?php echo e($errors->has('hornbostelCat') ? 'has-error' : ''); ?>">
                                            <label class="control-label">Hornbostel–Sachs</label>
                                            <input type="text" style="font-weight: bold;" class="form-control" placeholder="Enter Hornbostel-Sachs Classification, If Known" name="hornbostelCat" id="hornbostelCat" value="<?php echo e($hornbostelCat); ?>" />
                                        </div>
                                    </div>
                                </div>                                  
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group has-feedback <?php echo e($errors->has('repositoryName') ? 'has-error' : ''); ?>">
                                            <label class="control-label">Repository</label>
                                            <input type="text" class="form-control" placeholder="Repository Name" name="repositoryName" value="<?php echo e($repositoryName); ?>" />
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group has-feedback <?php echo e($errors->has('inventoryNumber') ? 'has-error' : ''); ?>">
                                            <label class="control-label">Inventory Number</label>
                                            <input type="text" class="form-control" placeholder="Inventory Number" name="inventoryNumber" value="<?php echo e($inventoryNumber); ?>" />
                                        </div>
                                    </div>                                    
                                </div>    
 <?php foreach($serialEditionNumbers as $key => $serial): ?>
        <?php /* */$thiskey=$key+1001;/* */ ?>
        <script>
                    $('.remove-me<?php echo e($thiskey); ?>').click(function(e){
                          e.preventDefault();
                        var fieldNum = <?php echo e($thiskey); ?>;
                        var fieldID = "#serial" + fieldNum;
                        $(this).remove();
                        $(fieldID).remove();
                    });
        </script>            
        <?php if(strlen($serial) > 0): ?>
           <div class="row" style="margin-bottom: 15px;" id="serial<?php echo e($thiskey); ?>">
                <div class="col-md-11">
                    <div class="form-group has-feedback">
                        <label class="control-label">Existing Serial Number</label>
                        <input type="text" class="form-control" value="<?php echo e($serial); ?>" name="serialnumbers[<?php echo e($thiskey); ?>][serialnumber]" placeholder="Enter Serial Edition Number" />
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
                                            <label class="control-label">Serial Edition Number</label>
                                            <input type="text" class="form-control" name="serialnumbers[0][serialnumber]" placeholder="Enter Serial Edition Number" />
                                       </div>     
                                    </div>
                                       <div class="col-md-1">
                                        <button style="margin-top:25px;" type="button" style="margin-top:25px;" class="btn btn-default addSerialButton"><i class="glyphicon glyphicon-plus"></i></button>
                                    </div>
                                </div>
                              <div class="form-group hide" id="serialTemplate">
                                <div class="row" style="margin-bottom: 15px;">
                                    <div class="col-md-11">
                                        <div class="form-group has-feedback">                               
                                            <label class="control-label">Serial Edition Number</label>
                                            <input type="text" class="form-control" name="serialnumber" placeholder="Enter Serial Edition Number" />
                                        </div>    
                                    </div>
                                       <div class="col-md-1">
                                        <button type="button" style="margin-top:25px;" class="btn btn-default removeSerialButton"><i class="glyphicon glyphicon-minus"></i></button>
                                    </div>
                                </div>
                              </div> 
                            </section>
                            <h6>Description</h6>
                            <section data-step="1">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group has-feedback <?php echo e($errors->has('mainDescriptionType') ? 'has-error' : ''); ?>">
                                            <label class="control-label">Main Description Type</label>
                                                <input type="text" class="form-control"  value="<?php echo e($mainDescriptionType); ?>" name="mainDescriptionType" placeholder="Type" />
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group has-feedback <?php echo e($errors->has('mainDescriptionSource') ? 'has-error' : ''); ?>">
                                            <label class="control-label">Main Description Source</label>
                                               <input type="text" class="form-control"  value="<?php echo e($mainDescriptionSource); ?>" name="mainDescriptionSource" placeholder="Source" /> 
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group has-feedback <?php echo e($errors->has('mainDescriptionText') ? 'has-error' : ''); ?>">
                                            <label class="control-label">Main Description Text</label><textarea rows="5" class="form-control" name="mainDescriptionText"><?php echo e($mainDescriptionText); ?></textarea></div>    
                                    </div>
                                </div>
<?php foreach($descriptions as $key => $description): ?>
        <?php /* */$thiskey=$key+1001;/* */ ?>
        <script>
                    $('.remove-description<?php echo e($thiskey); ?>').click(function(e){
                          e.preventDefault();
                        var fieldNum = <?php echo e($thiskey); ?>;
                        var fieldID = "#description" + fieldNum;
                        $(this).remove();
                        $(fieldID).remove();
                    });
        </script>            
                            <div id="description<?php echo e($thiskey); ?>">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group has-feedback">      
                                            <label class="control-label">Description Text</label>
                                               <textarea rows="5" class="form-control" name="desc[<?php echo e($thiskey); ?>][text]" placeholder="Text"><?php echo e($description->descriptionText); ?></textarea>
                                        </div>    
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group has-feedback">      
                                            <label class="control-label">Description Type</label>
                                           
                                                <input type="text" class="form-control" name="desc[<?php echo e($thiskey); ?>][type]" value="<?php echo e($description->descriptionType); ?>" placeholder="Type" />
                                        </div>
                                    </div>
                                    <div class="col-md-5">
                                        <div class="form-group has-feedback">      
                                            <label class="control-label">Description Source</label>
                                       
                                               <input type="text" class="form-control" name="desc[<?php echo e($thiskey); ?>][textsource]" value="<?php echo e($description->descriptionTextSource); ?>" placeholder="Source" /> 
                                        </div>
                                    </div>
                                    <div class="col-md-1">
                                        <button type="button" style="margin-top:25px;" class="btn btn-default remove-description<?php echo e($thiskey); ?>"><i class="glyphicon glyphicon-minus"></i></button>
                                    </div>                                    
                                </div>
                            </div>    
<?php endforeach; ?>   
<hr/>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group has-feedback">      
                                            <label class="control-label">New Description Type</label>
                                                <input type="text" class="form-control" name="desc[0][type]" placeholder="New Description Type" value="general description" />
                                        </div>
                                    </div>
                                    <div class="col-md-5">
                                        <div class="form-group has-feedback">      
                                            <label class="control-label">New Description Source</label>
                                               <input type="text" class="form-control" name="desc[0][textsource]" placeholder="New Description Source" /> 
                                        </div>
                                    </div>
                                    <div class="col-md-1">
                                        <button type="button" style="margin-top:25px;" class="btn btn-default addButton"><i class="glyphicon glyphicon-plus"></i></button>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group has-feedback">      
                                            <label class="control-label">New Description Text</label>
                                               <textarea rows="5" class="form-control" name="desc[0][text]" placeholder="New Description Text"></textarea>
                                        </div>    
                                    </div>
                                </div>                                
                                <div class="row">
                                    <div class="form-group hide" id="bookTemplate">
                                       <!-- <div class="row">
                                            <div class="col-md-12">
                                                <label class="control-label">Another Description</label>
                                            </div>
                                        </div> -->
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group has-feedback">      
                                                    <label class="control-label">Description Type</label>
                                                        <input type="text" class="form-control" name="type" placeholder="Enter Description Type" value="general description"/>
                                                </div>
                                            </div>
                                            <div class="col-md-5">
                                                <div class="form-group has-feedback">    
                                                    <label class="control-label">Description Source</label>
                                                       <input type="text" class="form-control" name="textsource" placeholder="Enter Description Source" /> 
                                                </div>
                                            </div>
                                            <div class="col-md-1">
                                                <button type="button" style="margin-top:25px;" class="btn btn-default removeButton"><i class="glyphicon glyphicon-minus"></i></button>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group has-feedback">    
                                                    <label class="control-label">Description Text</label>
                                                       <textarea rows="5" class="form-control" name="text" placeholder="Enter Text"></textarea>
                                                </div>    
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </section>
                            <h6>Inscriptions &amp; Decorative Elements</h6>
                            <section data-step="2">
<?php foreach($inscriptions as $key => $inscription): ?>
        <?php /* */$thiskey=$key+1001;/* */ ?>
    <script>
                $('.remove-inscription<?php echo e($thiskey); ?>').click(function(e){
                      e.preventDefault();
                    var fieldNum = <?php echo e($thiskey); ?>;
                    var fieldID = "#inscription" + fieldNum;
                    $(this).remove();
                    $(fieldID).remove();
                });
    </script>            
    <?php if(strlen($inscription) > 0): ?>

                               <div class="row" style="margin-bottom: 15px;" id="inscription<?php echo e($thiskey); ?>">
                                    <div class="col-md-11">
                                        <div class="form-group has-feedback">  
                                            <label class="control-label">Existing Inscription</label>
                                            <textarea rows="3" class="form-control" name="inscriptions[<?php echo e($thiskey); ?>][inscription]" placeholder="Enter Inscription"><?php echo e($inscription); ?></textarea> 
                                        </div>    
                                    </div>
                                       <div class="col-md-1">
                                        <button type="button" style="margin-top:25px;" class="btn btn-default remove-inscription<?php echo e($thiskey); ?>"><i class="glyphicon glyphicon-minus"></i></button>
                                    </div>
                                </div>
                           
    <?php endif; ?>                            
 <?php endforeach; ?>    
                                <div class="row">
                                    <div class="col-md-11">
                                        <div class="form-group has-feedback">  
                                            <label class="control-label">New Inscription</label>
                                            <textarea rows="3" class="form-control" name="inscriptions[0][inscription]" placeholder="Enter New Inscription"></textarea> 
                                        </div>    
                                    </div>
                                       <div class="col-md-1">
                                        <button type="button" style="margin-top:25px;" class="btn btn-default addInscriptionButton"><i class="glyphicon glyphicon-plus"></i></button>
                                    </div>
                                </div>
                                <div class="row" style="margin-bottom:20px;">
                                    <div class="form-group hide" id="inscriptionTemplate">

                                    <div class="col-md-11" style="margin-bottom: 20px;">
                                        <div class="form-group has-feedback">
                                            <label class="control-label">New Inscription</label>
                                            <textarea rows="3" class="form-control" name="inscription" placeholder="Enter New Inscription"></textarea> 
                                        </div>    
                                    </div>
                                        <div class="col-md-1">
                                            <button type="button" class="btn btn-default removeInscriptionButton" style="margin-top:25px;"><i class="glyphicon glyphicon-minus"></i></button>
                                        </div>
                                    </div>
                                </div>

<?php foreach($decorativeElements as $key => $decorative): ?>
        <?php /* */$thiskey=$key+1001;/* */ ?>
            <script>
                        $('.remove-decorative<?php echo e($thiskey); ?>').click(function(e){
                              e.preventDefault();
                            var fieldNum = <?php echo e($thiskey); ?>;
                            var fieldID = "#decorative" + fieldNum;
                            $(this).remove();
                            $(fieldID).remove();
                        });
            </script>            
            <?php if(strlen($decorative) > 0): ?>             
                   <div class="row" style="margin-bottom: 15px; margin-top: 30px;" id="decorative<?php echo e($thiskey); ?>">
                        <div class="col-md-11">
                             <div class="form-group has-feedback">  
                                <label class="control-label">Existing Decorative Element</label>
                                <input type="text" class="form-control" value="<?php echo e($decorative); ?>" name="decoratives[<?php echo e($thiskey); ?>][decorative]" placeholder="Decorative Element" />
                            </div>    
                        </div>
                           <div class="col-md-1">
                            <button style="margin-top:25px;" type="button" class="btn btn-default remove-decorative<?php echo e($thiskey); ?>"><i class="glyphicon glyphicon-minus"></i></button>
                        </div>
                    </div>
           <?php endif; ?>                                      
<?php endforeach; ?>   
                                <div class="row" style="margin-top:20px;">
                                    <div class="col-md-11">
                                        <div class="form-group has-feedback">
                                            <label class="control-label">New Decorative Element</label>
                                            <input type="text" class="form-control" name="decoratives[0][decorative]" placeholder="Enter New Decorative Element" />
                                        </div>
                                    </div>
                                       <div class="col-md-1">
                                        <button type="button" class="btn btn-default addDecorativeButton" style="margin-top:25px;"><i class="glyphicon glyphicon-plus"></i></button>
                                    </div>
                                </div>
                                <div class="row" style="margin-bottom:20px;">
                                    <div class="form-group hide" id="decorativeTemplate">
                                    <div class="col-md-11" style="margin-bottom: 20px;">
                                        <div class="form-group has-feedback">
                                            <label class="control-label">New Decorative Element</label>
                                            <input type="text" class="form-control" name="decorative" placeholder="New Decorative Element" />
                                        </div>
                                    </div>
                                        <div class="col-md-1">
                                            <button type="button" class="btn btn-default removeDecorativeButton" style="margin-top:25px;"><i class="glyphicon glyphicon-minus"></i></button>
                                        </div>
                                    </div>
                                </div>


                            </section>  
                            <h6>Measurements</h6>
                            <section data-step="3">
                                <div class="row">    
                                    <div class="col-xs-12">
                                        <div class="form-group has-feedback">
                                            <label class="control-label">Free Text Measurements</label>
                                            <textarea rows="3" class="form-control" name="measurements_freetext" placeholder="Enter Measurements Free Text"><?php echo e($measurementsFreeText); ?></textarea>
                                        </div>    
                                    </div>                        
                                </div>

    <div class="row" style="margin-bottom:10px; margin-top: 30px;">
        <div class="col-md-4">
            <label class="control-label">Specific Measurements</label>
        </div>
    </div>
<?php foreach($measurements as $key => $thismeasurement): ?>
        <?php /* */$thiskey=$key+1001;/* */ ?>
    <script>
                $('.remove-measurement<?php echo e($thiskey); ?>').click(function(e){
                      e.preventDefault();
                    var fieldNum = <?php echo e($thiskey); ?>;
                    var fieldID = "#measurement" + fieldNum;
                    $(this).remove();
                    $(fieldID).remove();
                });
    </script>            
    <div class="row" style="clear:both; padding-top:0px; margin-bottom:10px;" id="measurement<?php echo e($thiskey); ?>"> 
        <div class="col-md-4">
           <div class="form-group"> 
                <div class="multi-select-full">
                    <label class="control-label">Unit Of Measurement</label>
                    <select class="form-control" name="measurements[<?php echo e($thiskey); ?>][unit]">
                       <option value="mm" <?php if($thismeasurement->unit == "mm"): ?> selected="selected" <?php endif; ?>>Millimeters</option>
                       <option value="cm" <?php if($thismeasurement->unit == "cm"): ?> selected="selected" <?php endif; ?>>Centimeters</option>
                       <option value="inches" <?php if($thismeasurement->unit == "inches"): ?> selected="selected" <?php endif; ?>>Inches</option>
                       <option value="feet" <?php if($thismeasurement->unit == "feet"): ?> selected="selected" <?php endif; ?>>Feet</option>
                       <option value="meters" <?php if($thismeasurement->unit == "meters"): ?> selected="selected" <?php endif; ?>>Meters</option>
                    </select>
                </div>
            </div>    
        </div>
        <div class="col-md-4">
           <div class="form-group has-feedback"> 
              <label class="control-label">Type Of Measurement</label> 
              <input type="text" class="form-control" name="measurements[<?php echo e($thiskey); ?>][type]" value="<?php echo e($thismeasurement->type); ?>" placeholder="Type" /> 
           </div> 
        </div>                
        <div class="col-md-3">
           <div class="form-group has-feedback">
              <label class="control-label">Value</label> 
              <input type="text" class="form-control" name="measurements[<?php echo e($thiskey); ?>][value]" value="<?php echo e($thismeasurement->value); ?>" placeholder="Value" /> 
           </div>
        </div>
        <div class="col-md-1">
            <button style="margin-top:25px;" type="button" class="btn btn-default remove-measurement<?php echo e($thiskey); ?>"><i class="glyphicon glyphicon-minus"></i></button>
        </div>                                  
    </div>
<?php endforeach; ?>                                
                                <div class="row" style="clear:both; padding-top:0px; margin-bottom:10px;"> 
                                    <div class="col-md-4">
                                            <label class="control-label">Unit Of Measurement</label>
                                            <div class="multi-select-full">
                                                <select class="form-control" name="measurements[0][unit]">
                                                   <option value="mm">Millimeters</option>
                                                   <option value="cm">Centimeters</option>
                                                   <option value="inches">Inches</option>
                                                   <option value="feet">Feet</option>
                                                   <option value="meters">Meters</option>
                                                </select>
                                            </div>
                                    </div>
                                    <div class="col-md-4">
                                       <div class="form-group has-feedback"> 
                                          <label class="control-label">Type Of Measurement</label> 
                                          <input type="text" class="form-control" name="measurements[0][type]" placeholder="Type" /> 
                                       </div> 
                                    </div>                
                                    <div class="col-md-3">
                                       <div class="form-group has-feedback">
                                          <label class="control-label">Value</label> 
                                        <input type="text" class="form-control" name="measurements[0][value]" placeholder="Value" /> 
                                        </div>
                                    </div>
                                    <div class="col-md-1">
                                        <button style="margin-top:25px;" type="button" class="btn btn-default addButtonMeas"><i class="glyphicon glyphicon-plus"></i></button>
                                    </div>                                  
                                </div>
                                <div class="form-group hide" id="measurementsTemplate" style="margin-bottom:10px;">
                                    <div class="row" style="clear:both; padding-top:30px; margin-bottom:10px;"> 
                                            <div class="col-md-4">
                                            <label class="control-label">Unit Of Measurement</label>
                                            <div class="multi-select-full">
                                                <select class="form-control" name="unit">
                                                   <option value="mm">Millimeters</option>
                                                   <option value="cm">Centimeters</option>
                                                   <option value="inches">Inches</option>
                                                   <option value="feet">Feet</option>
                                                   <option value="meters">Meters</option>
                                                </select>
                                            </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group has-feedback">
                                                    <label class="control-label">Type Of Measurement</label>
                                                        <input type="text" class="form-control" name="type" placeholder="Type" /> 
                                                </div>        
                                            </div>                
                                            <div class="col-md-3">
                                                <div class="form-group has-feedback">
                                                    <label class="control-label">Value</label>
                                                        <input type="text" class="form-control" name="value" placeholder="Value" /> 
                                                </div>        
                                            </div>
                                            <div class="col-md-1">
                                                <button style="margin-top:25px;" type="button" class="btn btn-default removeButtonMeas"><i class="glyphicon glyphicon-minus"></i></button>
                                            </div>
                                        </div>
                                    </div>
                            </section>  
                        </form>
              </div></div></div>
              <!-- /edit instrument wizard -->

            <!-- include rightside with instrument details -->        
            <?php echo $__env->make('partials/instrument_rightside', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

     </div></div>
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('layouts.loggedin', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>