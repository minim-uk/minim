<?php $__env->startSection('title'); ?>
Existing Collections
<?php $__env->stopSection(); ?>


<?php $__env->startSection('breadcrumbs'); ?>
                            <li><a href="<?php echo e(url('/')); ?>"><i class="icon-home2 position-left"></i>Dashboard</a></li>
                            <li class="active">Existing Collections</li>
<?php $__env->stopSection(); ?>


<?php $__env->startSection('demo'); ?>
                    <div class="panel panel-white">
                        <div class="panel-heading">
                            <h6 class="panel-title">Existing Collections</h6>
                            <div class="heading-elements">
                                <ul class="icons-list">
                                    <li><a data-action="collapse"></a></li>
                                    <li><a data-action="reload"></a></li>
                                    <li><a data-action="close"></a></li>
                                </ul>
                            </div>
                        </div>
                        <table id="users-table" class="table table-condensed">
                             <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Image</th>
                                <th>Last Updated</th>
                                <!--<th style="width:50px;">Creator</th>-->
                                <th style="min-width:195px;">Actions</th>
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
        ajax: '<?php echo e(url("eloquent/existing-collections-data")); ?>',
        columns: [
            {data: 'legalBodyID'},
            {data: 'legalBodyName'},
            {data: null, searchable:false, sortable:false, render: function ( data, type, full ) { return '<img src="/images/legalBodyImages/thumbnails/'+data['legalBodyImage']+'" style="max-height:60px;"></>'; } },
            {data: 'updated_at', name: 'updated_at'},
            //{data: 'creatorAdminID', name: 'creatorAdminID'},
            {"searchable": false, "targets": 0,
            "data": null,
            sortable: false,
            "defaultContent": "<button class='edit'>View / Manage</button>&nbsp;<button class='delete'>Delete</button>"
           }
       ]
    } );
    // EDIT BUTTON
    $('#users-table tbody').on( 'click', 'button.edit', function () {
        var data = table.row( $(this).parents('tr') ).data();
        // redirect to edit page
        window.location = "/existing-collections/edit/" + data['legalBodyID'];
    } );
    // DELETE BUTTON
    $('#users-table tbody').on( 'click', 'button.delete', function () {
        var data = table.row( $(this).parents('tr') ).data();
        // redirect to delete page
        window.location = "/existing-collections/delete/" + data['legalBodyID'];
    } );
} );
<?php $__env->stopSection(); ?>
<?php echo $__env->make('datatables.template', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>