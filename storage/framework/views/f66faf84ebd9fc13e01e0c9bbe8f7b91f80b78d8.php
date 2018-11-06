<?php $__env->startSection('title'); ?>
Import Into Collection
<?php $__env->stopSection(); ?>



<?php $__env->startSection('breadcrumbs'); ?>
                            <li><a href="<?php echo e(url('/')); ?>"><i class="icon-home2 position-left"></i>Dashboard</a></li>                 
                            <li class="active"><a href="<?php echo e(url('/import-into-collection')); ?>">Import Into Collection</a></li>



<?php $__env->stopSection(); ?>



<?php $__env->startSection('content'); ?>

<?php /*  Import Form*/ ?>
                    <form id="upload_form" method="post" enctype="multipart/form-data" action="<?php echo e(url('/import-into-collection-go')); ?>" class="steps-validation">

                    <div class="row">

                    <?php echo csrf_field(); ?>


                        <div class="row">
                            <div class="col-lg-12">
                                <div class="panel registration-form">
                                    <div class="panel-body">
                                        <div class="text-center">
                                            <div class="icon-object border-success text-success"><img style="max-width: 140px;" src="<?php echo e(url('/images/admin/xml-to-db.jpg')); ?>" alt=""></div>
                                            <h5 class="content-group-lg">Import Into Collection <small class="display-block">All fields are required</small></h5>
                                        </div> 

                                        <select class="form-control" name="legalBodyID" style="margin-bottom: 20px;">
                                                <?php foreach($legalbodies as $legalbody): ?>
                                                    <option <?php if($collectionID == $legalbody->legalBodyID): ?> selected="selected" <?php endif; ?> value="<?php echo e($legalbody->legalBodyID); ?>"><?php echo e($legalbody->legalBodyName); ?></option>
                                                <?php endforeach; ?>
                                        </select>
                        
                                       <p style="margin-bottom: 20px; margin-left:10px;">&nbsp;<input type="radio" name="import_type" value="overwrite_all"><strong>&nbsp;<span style="font-size:120%;">Overwrite entire collection</span></strong> <span style="color:red;">[Destructive]</span><br/>
                                       <small>(Immediately <strong>deletes everything in collection</strong> and imports from the XML)</small>
                                       </p>

                                       <p style="margin-bottom: 20px; margin-left:10px;">
                                       <input type="radio" name="import_type" value="non_destructive" checked="checked"><strong>&nbsp;<span style="font-size:120%;">Don't overwrite existing instruments</span></strong> <span style="color:green">[<strong>Non-Destructive</strong>]</span><br/>
                                       <small>(Checks every record and <strong>if a record exists in MINIM-UK, the import ignores it</strong> - this selection will only import instruments not currently within MINIM-UK)</small></p>
                                     
                                       <input style="margin-top:1px; margin-left:10px;" type="radio" name="import_type" value="overwrite_existing">&nbsp;<strong><span style="font-size:120%;">Overwrite existing instruments</span></strong> <span style="color:#ff6600;">[Destructive]</span><br/>
                                       <small style="margin-left:10px; margin-top: -6px;">(Checks every record - any existing existing instruments that exist in both MINIM-UK and the remote data will be overwritten - <strong>if MIINIM-UK contains instruments for this collection not currently available in the remote XML, these will be unaffected</strong>)</small>                                    
                                  
                                      <p><center>
                                         <button type="submit" class="btn bg-teal-400 btn-labeled btn-labeled-right ml-10"><b><i class="icon-plus3"></i></b> Proceed To Import Confirmation</button>
                                      </center></p>

                                    </div>
                                </div>
                            </div>
                        </div>
                        </div>
                    </form>
<!-- /Import Form -->
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('layouts.loggedin', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>