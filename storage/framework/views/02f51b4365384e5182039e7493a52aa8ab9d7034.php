<?php $__env->startSection('title'); ?>
Instruments
    <?php if($adminlegalBodyName != ""): ?>
     / <?php echo e($adminlegalBodyName); ?>

    <?php endif; ?>
<?php $__env->stopSection(); ?>



<?php $__env->startSection('breadcrumbs'); ?>
        

                       <li><a href="<?php echo e(url('/')); ?>"><i class="icon-home2 position-left"></i>Dashboard</a></li>

                       <!-- tied to collection -->
                       <?php if($adminlegalBodyName != ""): ?>
                            <li><a href="<?php echo e(url('/instruments')); ?>"></i>Instruments</a></li>
                            <li class="active"><?php echo e($adminlegalBodyName); ?></li>
                        <?php endif; ?>


                       <!-- superadmin --> 
                       <?php if($adminlegalBodyName == "" && $collectionID ==""): ?>
                            <!-- <li class="active">View or Edit Instrument</li> -->
                            <li><a href="<?php echo e(url('/instruments')); ?>">Instruments</a></li>
                            <li class="active">All</li>

                       <?php endif; ?>


                       <?php if($adminlegalBodyName == "" && $collectionID !=""): ?>
                            <!-- <li class="active">View or Edit Instrument</li> -->
                            <li><a href="<?php echo e(url('/instruments')); ?>">Instruments </a></li>
                            <li class="active"><?php echo e($adminlegalBodyName); ?></li>

                       <?php endif; ?>


<?php $__env->stopSection(); ?>



<?php $__env->startSection('demo'); ?>

                    <div class="panel panel-white">
                        <div class="panel-heading">
                             <?php if($role != "SuperAdmin"): ?>
                           <h6 class="panel-title">View or Edit Instrument</h6> 
                             <?php endif; ?>

                            <?php if($role == "SuperAdmin"): ?>
                                <div class="col-sm-4">
                                    <select class="form-control" style="margin-left:-10px;" onchange="location = this.options[this.selectedIndex].value;">
                                        <option value="/instruments">Viewing All Instruments In All Collections</option>


                                            <?php foreach($legalbodies as $legalbody): ?>
                                                <option <?php if($collectionID == $legalbody->legalBodyID): ?> selected="selected" <?php endif; ?> value="/instruments/<?php echo e($legalbody->legalBodyID); ?>"><?php echo e($legalbody->legalBodyName); ?></option>
                                            <?php endforeach; ?>


                                    </select>
                                </div>                
                               <br clear="all"/>

                                <?php if(strlen($collectionID) > 0): ?>
                                    <p style="margin-top:24px; margin-left:10px; ">&#149;&nbsp;Viewing instruments belonging to <a href="/existing-collections/edit/<?php echo e($collectionID); ?>"><?php echo e($adminlegalBodyName); ?></a>'s collection.</p>
                                <?php endif; ?>

                            <?php endif; ?>



                            <div class="heading-elements">
                                <ul class="icons-list">
                                   <!-- <li><a data-action="collapse"></a></li> -->
                                    <li><a data-action="reload"></a></li>
                                    <li><a data-action="close"></a></li>
                                </ul>
                            </div>
                        </div>


             
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
                     

                        <table id="users-table" class="table table-condensed">
                             <thead>
                            <tr>
                                <th>ID</th>
                                <th style="min-width:108px;">Title</th>
                                <th>Status</th>
                                <th style="min-width:74px;">Earliest Year</th>
                                <th style="min-width:74px;">Latest Year</th>
                                <th>Place of Production</th>
                                <th style="min-width:198px;">Actions</th>
                            </tr>
                            </thead>
                        </table>
                    </div>

<?php $__env->stopSection(); ?>



<?php $__env->startSection('js'); ?>


$(document).ready(function() {
    var table = $('#users-table').DataTable( {
        processing: true,
        serverSide: true,
        ajax: '<?php echo e(url("eloquent/basic-object-data/".$collectionID)); ?>',
        columns: [

            {data: 'instrumentID'},
            {data: 'titlePreferred'},
            {data: 'status'},
            {data: 'productionEventEarliestDate', name: 'productionEventEarliestDate'},
            {data: 'productionEventLatestDate', name: 'productionEventLatestDate'},
            {data: 'productionEventLocation', name: 'productionEventLocation'},

            {
            "searchable": false, "targets": 0,
            "data": null,
            sortable: false,
            "defaultContent": "<button class='preview'>Preview</button>&nbsp;<button class='edit'>Edit</button>&nbsp;<button class='delete'>Delete</button>"
           }
       ]
    } );



    // PREVIEW BUTTON
    $('#users-table tbody').on( 'click', 'button.preview', function () {
    // $(this).removeData('#modal_remote');
    // $(this).empty();

     var data = table.row( $(this).parents('tr') ).data();
            $('#modal_remote').on('show.bs.modal', function(instrumentID) {

             //   $(this).find('.modal-body').load('/preview-instrument/' + data['instrumentID'], function() {
             //   });
               
                    var link = '/preview-instrument/' + data['instrumentID'];
                    $(this).find(".modal-body").load('/preview-instrument/' + data['instrumentID']);  



            });

           $('#modal_remote').modal('show');
    
    });


    // EDIT BUTTON
    $('#users-table tbody').on( 'click', 'button.edit', function () {
        var data = table.row( $(this).parents('tr') ).data();
        //alert( "edit " + data['id'] );

        // redirect to edit page
        window.location = "/edit-instrument/" + data['instrumentID'];

    } );


    // DELETE BUTTON
    $('#users-table tbody').on( 'click', 'button.delete', function () {
        var data = table.row( $(this).parents('tr') ).data();
        //alert( "delete instrument here: " + data['instrumentID'] );

        // redirect to delete page
        window.location = "/delete-instrument/" + data['instrumentID'];

    } );

} ); // end document ready





<?php $__env->stopSection(); ?>




<?php echo $__env->make('datatables.template', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>