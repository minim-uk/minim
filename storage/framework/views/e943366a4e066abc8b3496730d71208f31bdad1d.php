<?php $__env->startSection('title'); ?>
Add New Instrument
<?php $__env->stopSection(); ?>



<?php $__env->startSection('breadcrumbs'); ?>
                            <li><a href="<?php echo e(url('/')); ?>"><i class="icon-home2 position-left"></i>Dashboard</a></li>
                            <li class="active">Add New Instrument</li>
<?php $__env->stopSection(); ?>



<?php $__env->startSection('content'); ?>
                    <div class="panel panel-white">
                        <div class="panel-heading">
                            <?php if($role != "SuperAdmin" && $role != "Cataloguer"): ?>
                                <h6 class="panel-title">Add Instrument To <?php echo e(Session::get('legalBodyName')); ?>'s Collection</h6> 
                            <?php endif; ?>
                            <?php if($role == "SuperAdmin" || $role == "Cataloguer"): ?>
                                <div class="row">
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <select style="margin-bottom:-20px;" class="form-control" onchange="location = this.options[this.selectedIndex].value;">
                                                <?php foreach($legalbodies as $key => $legalbody): ?>           
                                                         <option value="/addinstrument/collection/<?php echo e($legalbody->legalBodyID); ?>" <?php if($legalBodyID === $legalbody->legalBodyID ): ?> selected="selected" <?php endif; ?>><?php echo e($legalbody->legalBodyName); ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>      
                                    </div>  
                                </div>            
                            <?php endif; ?>
                            <div class="heading-elements">
                                <ul class="icons-list">
                                    <li><a data-action="collapse"></a></li>
                                    <li><a data-action="close"></a></li>
                                </ul>
                            </div>
                        </div>
                        
<?php /*  CATCH FORM ERRORS */ ?>
<?php echo $__env->make('partials/message-block', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>       

<?php /* ADD INSTRUMENT FORM WIZARD STEPS */ ?>    
                        <form id="profileForm" method="post" class="steps-validation" action="<?php echo e(url('/addinstrument/store')); ?>">
                      
                        <?php echo csrf_field(); ?>

                      
                        <!-- hidden input for thesuarusID-->
                        <input type="hidden" name="thesaurusID" id="thesaurusID" value="<?php echo e(Request::old('thesaurusID')); ?>">
                        <!-- hidden input for hornbostelID-->
                        <input type="hidden" name="hornbostelID" id="hornbostelID" value="<?php echo e(Request::old('hornbostelID')); ?>">
                        <input type="hidden" name="legalBodyID" id="legalBodyID" value="<?php echo e($legalBodyID); ?>">
                        <!--hiddenID production location autocomplete -->
                        <input type="hidden" name="cityID" id="cityID" value="<?php echo e(Request::old('cityID')); ?>"/>

                            <h6>Basics</h6>
                            <section data-step="0">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group has-feedback  <?php echo e($errors->has('instrument_name') ? 'has-error' : ''); ?>">
                                            <label class="control-label">Instrument Classification / Name</label>
                                            <br/>
                                            <input type="text" style="font-weight: bold;" class="form-control" name="instrument_name" id="instrument_name" placeholder="Enter Instrument Name" value="<?php echo e(Request::old('instrument_name')); ?>" />
                                        </div>
                                    </div>
                                </div>   
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group has-feedback  <?php echo e($errors->has('instrument_classifiedTitlePreferred') ? 'has-error' : ''); ?>">
                                            <label class="control-label">If Classified, Enter More Specific Name If Relevent</label>
                                            <br/>
                                            <input type="text" style="font-weight: bold;" class="form-control" name="instrument_classifiedTitlePreferred" id="instrument_classifiedTitlePreferred" placeholder="If Classified, Enter More Specific Instrument Name" value="<?php echo e(Request::old('instrument_classifiedTitlePreferred')); ?>" />
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group has-feedback <?php echo e($errors->has('hornbostelCat') ? 'has-error' : ''); ?>">
                                            <label class="control-label">Hornbostel–Sachs</label>
                                            <input type="text" style="font-weight: bold;" class="form-control" placeholder="Enter Hornbostel-Sachs Classification, If Known" name="hornbostelCat" id="hornbostelCat" value="<?php echo e(Request::old('hornbostelCat')); ?>" />
                                        </div>
                                    </div>
                                </div>   
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group has-feedback  <?php echo e($errors->has('repositoryName') ? 'has-error' : ''); ?>">
                                            <label class="control-label">Repository</label>
                                            <input type="text" class="form-control" name="repositoryName" placeholder="Repository Name" value="<?php echo e($defaultRepositoryName); ?>" />
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group has-feedback  <?php echo e($errors->has('inventoryNumber') ? 'has-error' : ''); ?>">
                                            <label class="control-label">Inventory Number</label>
                                            <input type="text" class="form-control" placeholder="Enter Inventory Number" name="inventoryNumber" value="<?php echo e(Request::old('inventoryNumber')); ?>" />
                                        </div>
                                    </div>
                                </div>    
                                <div class="row">
                                    <div class="col-md-11">
                                        <div class="form-group has-feedback">
                                             <label class="control-label">Inscription</label>
                                             <textarea rows="3" class="form-control" name="inscriptions[0][inscription]" placeholder="Enter Inscription"></textarea> 
                                        </div>        
                                    </div>
                                       <div class="col-md-1">
                                        <button type="button" class="btn btn-default addInscriptionButton" style="margin-top:25px;"><i class="glyphicon glyphicon-plus"></i></button>
                                    </div>
                                </div>
                                <div class="row" style="margin-bottom:20px;">
                                    <div class="form-group hide" id="inscriptionTemplate">

                                    <div class="col-md-11" style="margin-bottom: 20px;">
                                        <div class="form-group has-feedback">
                                            <label class="control-label">New Inscription</label>
                                            <textarea rows="3" class="form-control" name="inscription" placeholder="New Inscription"></textarea> 
                                        </div>    
                                    </div>
                                        <div class="col-md-1">
                                            <button type="button" class="btn btn-default removeInscriptionButton" style="margin-top:25px;"><i class="glyphicon glyphicon-minus"></i></button>
                                        </div>
                                    </div>
                                </div>
                                <div class="row" style="margin-top:20px;">
                                    <div class="col-md-11">
                                        <div class="form-group has-feedback">
                                            <label class="control-label">Decorative Element</label>
                                            <input type="text" class="form-control" name="decoratives[0][decorative]" placeholder="Enter Decorative Element" />
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
                               <div class="row" style="margin-top:20px;">
                                    <div class="col-md-11">
                                        <div class="form-group has-feedback  <?php echo e($errors->has('serialnumbers[0][serialnumber]') ? 'has-error' : ''); ?>">
                                            <label class="control-label">Serial Number</label>
                                            <input type="text" class="form-control" name="serialnumbers[0][serialnumber]" placeholder="Enter Serial Edition Number" />
                                        </div>
                                    </div>
                                       <div class="col-md-1">
                                        <button type="button" class="btn btn-default addSerialButton" style="margin-top:25px;"><i class="glyphicon glyphicon-plus"></i></button>
                                    </div>
                                </div>
                                <div class="row" style="margin-bottom:20px;">
                                    <div class="form-group hide" id="serialTemplate">
                                    <div class="col-md-11" style="margin-bottom: 20px;">
                                        <div class="form-group has-feedback">
                                             <label class="control-label">New Serial Edition Number</label>
                                             <input type="text" class="form-control" name="serialnumber" placeholder="New Serial Edition Number" />
                                        </div>     
                                    </div>
                                        <div class="col-md-1">
                                            <button type="button" class="btn btn-default removeSerialButton" style="margin-top:25px;"><i class="glyphicon glyphicon-minus"></i></button>
                                        </div>
                                    </div>
                                </div>
                            </section>
                            <h6>Main Description</h6>
                            <section data-step="1">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group has-feedback <?php echo e($errors->has('mainDescriptionType') ? 'has-error' : ''); ?>">
                                            <label class="control-label">Description Type</label>
                                                <input type="text" class="form-control"  name="mainDescriptionType" value="General Description" placeholder="Type" />
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group has-feedback <?php echo e($errors->has('mainDescriptionSource') ? 'has-error' : ''); ?>">
                                            <label class="control-label">Description Source</label>
                                               <input type="text" class="form-control"  value="<?php echo e(Request::old('mainDescriptionSource')); ?>" name="mainDescriptionSource" placeholder="Source" /> 
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group has-feedback <?php echo e($errors->has('mainDescriptionText') ? 'has-error' : ''); ?>">
                                            <label class="control-label">Description Text</label><textarea rows="5" class="form-control" name="mainDescriptionText"><?php echo e(Request::old('mainDescriptionText')); ?></textarea></div>    
                                    </div>
                                </div>
                            </section>
                            <h6>Production Event</h6>
                            <section data-step="2">
                                        <div class="row" style="margin-bottom:10px;">
                                            <div class="col-md-4">
                                                <label class="control-label"><strong>Production Event</strong></label>
                                            </div>
                                        </div>
                                        <div class="row" style="margin-bottom:10px;">
                                            <div class="col-xs-8">
                                                <div class="form-group has-feedback <?php echo e($errors->has('productionEventLocation') ? 'has-error' : ''); ?>">
                                                <label class="control-label">Location</label>
                                                    <input type="text" class="form-control" name="productionEventLocation" id="productionEventLocation" placeholder="Production Event Location" value="<?php echo e(Request::old('productionEventLocation')); ?>" /> 
                                                </div>
                                            </div>
                                            <div class="col-xs-4">
                                                <div class="form-group has-feedback <?php echo e($errors->has('productionEventCulture') ? 'has-error' : ''); ?>">
                                                <label class="control-label">Culture</label>
                                                    <input type="text" class="form-control" name="productionEventCulture" placeholder="Production Event Culture" value="<?php echo e(Request::old('productionEventCulture')); ?>" /> 
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row" style="margin-bottom:10px;">
                                            <div class="col-md-4">
                                                <div class="form-group has-feedback <?php echo e($errors->has('productionEventEarliestDate') ? 'has-error' : ''); ?>">
                                                <label class="control-label">Earliest Date</label>
                                                    <input type="text" class="form-control" name="productionEventEarliestDate" placeholder="Production Earliest Date" value="<?php echo e(Request::old('productionEventEarliestDate')); ?>" /> 
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group has-feedback <?php echo e($errors->has('productionEventLatestDate') ? 'has-error' : ''); ?>">
                                                <label class="control-label">Latest Date</label>
                                                    <input type="text" class="form-control" name="productionEventLatestDate" placeholder="Production Latest Date" value="<?php echo e(Request::old('productionEventLatestDate')); ?>" /> 
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group has-feedback <?php echo e($errors->has('productionPeriodName') ? 'has-error' : ''); ?>">
                                                <label class="control-label">Period Name</label>
                                                    <input type="text" class="form-control" name="productionPeriodName" placeholder="Production Period Name" value="<?php echo e(Request::old('productionPeriodName')); ?>" /> 
                                                </div>
                                            </div>
                                        </div>   
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group has-feedback <?php echo e($errors->has('productionEventDateText') ? 'has-error' : ''); ?>">
                                                <label class="control-label">Date Text</label>
                                                   
                                                    <textarea class="form-control" name="productionEventDateText" placeholder="Production Event Date Text"><?php echo e(Request::old('productionEventDateText')); ?></textarea>

                                                </div>
                                            </div>
                                        </div>                                         
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group has-feedback <?php echo e($errors->has('productionMaterialsText') ? 'has-error' : ''); ?>">
                                                <label class="control-label">Materials Text</label>
                                                   
                                                    <textarea class="form-control" name="productionMaterialsText" placeholder="Production Materials"><?php echo e(Request::old('productionMaterialsText')); ?></textarea>

                                                </div>
                                            </div>
                                        </div>
                            </section>  
                            <h6>Measurements</h6>
                            <section data-step="3">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label class="control-label">Free Text Measurements</label>
                                    </div>
                                </div>
                                <div class="row">    
                                    <div class="col-xs-12">
                                        <input type="text" class="form-control" name="measurements_freetext" placeholder="Enter Measurements Free Text" value="<?php echo e(Request::old('measurements_freetext')); ?>" />
                                    </div>                        
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <label class="control-label">Specific Measurements</label>
                                    </div>
                                </div>
                                <div class="row"> 
                                    <div class="col-md-4">
                                       <div class="form-group has-feedback">                                     
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
                                        <button type="button" class="btn btn-default addButtonMeas"><i class="glyphicon glyphicon-plus"></i></button>
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
                                                <button type="button" class="btn btn-default removeButtonMeas"><i class="glyphicon glyphicon-minus"></i></button>
                                            </div>
                                        </div>
                                    </div>
                            </section>  
                        </form>
                    </div>
                    <!-- /add instrument wizard -->
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('layouts.loggedin', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>