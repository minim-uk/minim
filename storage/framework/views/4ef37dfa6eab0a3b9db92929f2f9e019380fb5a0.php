<?php $__env->startSection('title'); ?>
Import Into Collection
<?php $__env->stopSection(); ?>



<?php $__env->startSection('breadcrumbs'); ?>
                            <li><a href="<?php echo e(url('/')); ?>"><i class="icon-home2 position-left"></i>Dashboard</a></li>
                            <li><a href="<?php echo e(url('/import-into-collection')); ?>">Import Into Collection</a></li>
                            <li class="active"><?php echo e($legalBodyName_import); ?></li>

<?php $__env->stopSection(); ?>



<?php $__env->startSection('content'); ?>

<script language="javascript">
  setInterval(function(){
    var lastImportID = <?php echo e($lastImportID); ?>

    $.get( 'importlisten/' + lastImportID, function(importlisten){
      $('#importlisten').html( importlisten );
    });
  },1000); // 1 second
</script>

   <?php if(strlen($importXMLfile) > 0): ?>
       <?php /*  Import Form*/ ?>
                          <form id="upload_form" method="post" enctype="multipart/form-data" action="<?php echo e(url('/import-into-collection-now')); ?>" class="steps-validation">
                             
                          <input type="hidden" name="lastImportID" id="lastImportID" value="<?php echo e($lastImportID); ?>"/>

                          <?php echo csrf_field(); ?>


                              <div class="row">
                                  <div class="col-lg-12">
                                      <div class="panel registration-form">
                                          <div class="panel-body">
                                              <div class="text-center">

                                                  <div id="importintro">    
                                                      <div class="icon-object border-success text-success"><img style="max-width: 140px;" src="<?php echo e(url('/images/admin/xml-to-db.jpg')); ?>" alt=""></div>
                                                  </div>  

                                                  <!-- <p>Import into: <?php echo e($legalBodyID_import); ?></p> -->
                                                  <strong>
                                                  <?php if($import_type_import == "overwrite_all"): ?>
                                                    <p>Import type: <span style="color:red;">Delete Everything In Collection And Import From XML</span></p>
                                                  <?php endif; ?>  
                                                  <?php if($import_type_import == "overwrite_existing"): ?>
                                                    <p>Import type: <span style="color:#ff6600;">Overwrite Existing Instruments With XML, If Found</span></p>
                                                  <?php endif; ?>  
                                                  <?php if($import_type_import == "non_destructive"): ?>
                                                    <p>Import type: <span style="color:green;">Import From XML, But Don't Overwrite Any Instruments Currently In MINIM-UK</span></p>
                                                  <?php endif; ?>  
                                                  </strong>

                                                  <p>Import Into: <span style="font-size:18px;"><strong><?php echo e($legalBodyName_import); ?></strong></span></p>
                                                  <!-- <p>MDA Code: <?php echo e($legalBodyMDAcode_import); ?></p> -->
                                                  <p>Import File: <?php echo e($importXMLfile); ?></p>
                                                  <p>File Size: <?php echo e($filesize); ?> bytes (<?php echo e($filesize_k); ?> k)</p>
                                                  <p>Last Modified: <?php echo e($file_last_modified); ?></p>
                                                  <h5 class="content-group-lg" style="font-size:28px;" id="importsure">Are you sure you want to perform this import?</h5>

                                              </div> 
                                              <div class="row">
                                                 <div id="importlisten"></div>
                                              </div>           
                                              <div class="text-center">
                                                 <input type="button" id="submit_btn" value="Begin Import" class="btn bg-teal-400 btn-labeled btn-labeled-center" onclick="beginImport()">
                                              </div>
                                          </div>
                                      </div>
                                  </div>
                              </div>
                          </form>
        <!-- /Import Form -->
   <?php else: ?>
     <p>No XML to import. <a href="/existing-collections/xml/<?php echo e($legalBodyID_import); ?>">Set XML For <?php echo e($legalBodyName_import); ?>'s Collection</a>.</p>
   <?php endif; ?>    
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('layouts.loggedin', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>