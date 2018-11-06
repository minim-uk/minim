<?php $__env->startSection('title'); ?>
Existing Users
<?php $__env->stopSection(); ?>



<?php $__env->startSection('breadcrumbs'); ?>
                            <li><a href="<?php echo e(url('/')); ?>"><i class="icon-home2 position-left"></i>Dashboard</a></li>
                            <li class="active">Existing Users</li>
<?php $__env->stopSection(); ?>



<?php $__env->startSection('demo'); ?>

                    <div class="panel panel-white">
                        <div class="panel-heading">
                            <h6 class="panel-title">Existing Users</h6>
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
                                <th style="width:100px;">id</th>
                                <th>Forename</th>
                                <th>Surname</th>
                                <th style="width:100px;">Avatar</th>
                                <th>Role</th>
                                <th style="min-width:188px;">Actions</th>
                            </tr>
                            </thead>
                        </table>
                    </div>


<!--
            {data: 'created_at', name: 'created_at'},
            {data: 'updated_at', name: 'updated_at'},
-->     
<?php $__env->stopSection(); ?>


<?php $__env->startSection('js'); ?>

$(document).ready(function() {
    var table = $('#users-table').DataTable( {
        processing: true,
        serverSide: true,
        ajax: '<?php echo e(url("eloquent/existing-users-data")); ?>',
        columns: [
            {data: 'id'},
            {data: 'name'},
            {data: 'surname'},
            {data: null, searchable:false, sortable:false, render: function ( data, type, full ) { return '<center><img src="/images/users/thumbnails/'+data['avatar']+'" style="border-radius: 50%; max-height:60px;"></>'; } },
            {data: 'role', name: 'role'},

            {
            "searchable": false, "targets": 0,
            "data": null,
            sortable: false,
            "defaultContent": "<button class='edit'>View / Manage</button>&nbsp;<button class='delete'>Delete</button>"



           }
       ]
    } );
 
    // EDIT BUTTON
    $('#users-table tbody').on( 'click', 'button.edit', function () {
        var data = table.row( $(this).parents('tr') ).data();
        //alert( "edit " + data['id'] );

        // redirect to edit page
        window.location = "/existing-users/edit/" + data['id'];

    } );

    // DELETE BUTTON
    $('#users-table tbody').on( 'click', 'button.delete', function () {
        var data = table.row( $(this).parents('tr') ).data();
       // alert( "delete " + data['id'] );

       // redirect to delete page
        window.location = "/existing-users/delete/" + data['id'];

    } );

} );


<?php $__env->stopSection(); ?>




                      
                               


























<?php $__env->startSection('scratch'); ?>
        


            "defaultContent": "       <ul class='icons-list'>
                                            <li class='dropdown'>
                                                <a href='#' class='dropdown-toggle' data-toggle='dropdown'>
                                                    <i class='icon-menu9'></i>
                                                </a>

                                                <ul class='dropdown-menu dropdown-menu-right'>
                                                    <li><a href='#'><i class='icon-file-pdf'></i> Export to .pdf</a></li>
                                                    <li><a href="#"><i class='icon-file-excel'></i> Export to .csv</a></li>
                                                    <li><a href='#'><i class='icon-file-word'></i> Export to .doc</a></li>
                                                </ul>
                                            </li>
                                        </ul>"




         {
                sortable: false,
                defaultContent: '<a onClick="testUpdateButton()" class="btn btn-info btn-sm">Edit</a> &nbsp;&nbsp;&nbsp; <a href="" class="btn btn-info btn-sm">Delete</a>'
            }





$(document).ready(function() {
    var table = $('#users-table').DataTable( {
        processing: true,
        serverSide: true,
        ajax: '<?php echo e(url("eloquent/existing-users-data")); ?>',
        columns: [
            {data: 'id'},
            {data: 'name'},
            {data: 'surname'},
            {data: 'email', name: 'email'},
            {data: 'role', name: 'role'},

            {
            "targets": -1,
            "data": null,
            "defaultContent": "<button class='edit'>Edit</button>&nbsp;<button class='delete'>Delete</button>"
           }
       ]
    } );
 
    $('#users-table tbody').on( 'click', 'button', function () {
        var data = table.row( $(this).parents('tr') ).data();
        alert( data[0] +"'s salary is: "+ data[ 5 ] );
    } );
} );



// working old
    $('#users-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '<?php echo e(url("eloquent/existing-users-data")); ?>',
        columns: [
            {data: 'id'},
            {data: 'name'},
            {data: 'surname'},
            {data: 'email', name: 'email'},
            {data: 'role', name: 'role'},

            {
            "targets": -1,
            "data": null,
            "defaultContent": "<button class='edit'>Edit</button>&nbsp;<button class='delete'>Delete</button>"
           }
       ]
    });
<?php $__env->stopSection(); ?>
<?php echo $__env->make('datatables.template', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>