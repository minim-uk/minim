<!DOCTYPE html>
<html lang="en">
<!--
oooo     oooo ooooo oooo   oooo ooooo oooo     oooo      ooooo  oooo oooo   oooo 
 8888o   888   888   8888o  88   888   8888o   888        888    88   888  o88   
 88 888o8 88   888   88 888o88   888   88 888o8 88        888    88   888888     
 88  888  88   888   88   8888   888   88  888  88        888    88   888  88o   
o88o  8  o88o o888o o88o    88  o888o o88o  8  o88o        888oo88   o888o o888o
-->
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>MINIM-UK ~ <?php echo $__env->yieldContent('title'); ?></title>   
    <!-- do not allow indexing-->
    <meta name="robots" content="noindex">
    <!-- favicon -->
    <link rel="apple-touch-icon" sizes="57x57" href="/images/favicon/apple-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="60x60" href="/images/favicon/apple-icon-60x60.png">
    <link rel="apple-touch-icon" sizes="72x72" href="/images/favicon/apple-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="76x76" href="/images/favicon/apple-icon-76x76.png">
    <link rel="apple-touch-icon" sizes="114x114" href="/images/favicon/apple-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="120x120" href="/images/favicon/apple-icon-120x120.png">
    <link rel="apple-touch-icon" sizes="144x144" href="/images/favicon/apple-icon-144x144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="/images/favicon/apple-icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/images/favicon/apple-icon-180x180.png">
    <link rel="icon" type="image/png" sizes="192x192"  href="/images/favicon/android-icon-192x192.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/images/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="96x96" href="/images/favicon/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/images/favicon/favicon-16x16.png">
    <link rel="manifest" href="/images/favicon/manifest.json">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="/images/favicon/ms-icon-144x144.png">
    <meta name="theme-color" content="#ffffff">
    <!-- global stylesheets -->
    <link href="<?php echo asset('/css/admin/fonts.css')?>" rel="stylesheet" type="text/css">
    <link href="<?php echo asset('/css/admin/icons/icomoon/styles.css')?>" rel="stylesheet" type="text/css">   
    <link href="<?php echo asset('/css/admin/bootstrap.css')?>" rel="stylesheet" type="text/css">   
    <link href="<?php echo asset('/css/admin/core.css')?>" rel="stylesheet" type="text/css">   
    <link href="<?php echo asset('/css/admin/components.css')?>" rel="stylesheet" type="text/css">   
    <link href="<?php echo asset('/css/admin/colors.css')?>" rel="stylesheet" type="text/css">    
    <!-- /global stylesheets -->
    <!-- core js files -->
    <script type="text/javascript" src="<?php echo asset('/js/admin/plugins/loaders/pace.min.js')?>"></script>
    <script type="text/javascript" src="<?php echo asset('/js/admin/core/libraries/jquery.min.js')?>"></script>
    <script type="text/javascript" src="<?php echo asset('/js/admin/core/libraries/bootstrap.min.js')?>"></script>
    <script type="text/javascript" src="<?php echo asset('/js/admin/plugins/loaders/blockui.min.js')?>"></script> 
    <!-- /core JS files -->

    <?php if($page === "importintocollection" || $page === "importintocollection_go"): ?>
    <!-- for import form ajax -->
    <script type="text/javascript" src="<?php echo asset('/js/admin/jquery.form.min.js')?>"></script> 
    <script type="text/javascript">

<!-- https://www.youtube.com/watch?v=EraNFJiY0Eg -->

function _(el){
    return document.getElementById(el);
}
function beginImport(){
    var formdata = new FormData();
    var ajax = new XMLHttpRequest();
    // disable and fade submit button 
    $('#importsure').delay(2500).fadeOut(6500); 
    $('#submit_btn').attr("disabled", true);
    $('#submit_btn').delay(5000).fadeOut(4000);
    ajax.upload.addEventListener("progress", progressHandler, false);
    ajax.addEventListener("load", completeHandler, false);
    ajax.addEventListener("error", errorHandler, false);
    ajax.addEventListener("abort", abortHandler, false);
    ajax.open("POST", "import-into-collection-now");
    ajax.setRequestHeader('X-CSRF-Token', $('input[name="_token"]').val());    
    ajax.send(formdata);
}
function progressHandler(event){
    _("loaded_n_total").innerHTML = "Uploaded "+event.loaded+" bytes of "+event.total;
    var percent = (event.loaded / event.total) * 100;
    _("progressBar").value = Math.round(percent);
    _("status").innerHTML = Math.round(percent)+"% uploaded... please wait";
}
function completeHandler(event){
    _("status").innerHTML = event.target.responseText;
    _("progressBar").value = 0;
    //$('#submit_btn').hide();
}
function errorHandler(event){
    _("status").innerHTML = "Upload Failed";
}
function abortHandler(event){
    _("status").innerHTML = "Upload Aborted";
}
</script>
    <?php endif; ?>

    <?php if($page === "addinstrument" || $page == "editinstrument" || $page == "instrumentrights" || $page == "resourcerights" || $page === "eventmaterials" || $page == "productionevent" || $page === "editevent" || $page === "addevent" || $page === "addactor" || $page === "editactor"): ?> 
    <!-- for autocomplete text -->
    <script type="text/javascript" src="<?php echo asset('/js/admin/jquery-ui.min.js')?>"></script>
    <script type="text/javascript" src="<?php echo asset('/js/admin/plugins/forms/selects/select2.min.js')?>"></script>
    <script type="text/javascript" src="<?php echo asset('/js/admin/plugins/forms/styling/uniform.min.js')?>"></script>
    <script type="text/javascript" src="<?php echo asset('/js/admin/core/libraries/jasny_bootstrap.min.js')?>"></script>
    <script type="text/javascript" src="<?php echo asset('/js/admin/plugins/extensions/cookie.js')?>"></script>   
    <?php endif; ?>
    <?php if($page === "legalbodyprofile" || $page === "addcollection" || $page === "editcollection" || $page === "accountsettings" || $page === "adduser" || $page === "editresource" || $page === "addresource" || $page === "edituser" || $page === "managexml"): ?>  
    <!-- WYSIWYG -->
    <script type="text/javascript" src="<?php echo asset('/js/admin/ckeditor/ckeditor.js')?>"></script>
    <!-- for ajax file uploads -->
    <script type="text/javascript" src="<?php echo asset('/js/admin/plugins/uploaders/fileinput.unmin.js')?>"></script>
    <?php endif; ?>
    <?php if($page === "viewedit"): ?>
    <script src="<?php echo e(asset('/js/admin/plugins/notifications/bootbox.min.js')); ?>"></script>
    <script src="<?php echo e(asset('/js/admin/plugins/notifications/sweet_alert.min.js')); ?>"></script>
    <?php endif; ?> 
    <!-- core admin js -->
    <script type="text/javascript" src="<?php echo asset('/js/admin/core/app.js')?>"></script>
    <!-- form validation css -->
    <link rel="stylesheet" href="<?php echo asset('/css/admin/formValidation.min.css')?>">
    <!-- for the steps form validation -->
    <script type="text/javascript" src="<?php echo asset('/js/admin/formValidation.min.js')?>"></script>  
    <!-- form validation framework -->
    <script type="text/javascript" src="<?php echo asset('/js/admin/formvalidation_bootstrap.min.js')?>"></script>    
    <!-- form validation specific to the page starts here -->
    <?php if($page === "addinstrument"): ?>  
    <script type="text/javascript" src="<?php echo asset('/js/admin/formio_jquery.steps.unmin.js')?>"></script> 
    <script type="text/javascript" src="<?php echo asset('/js/admin/add-instrument.js')?>"></script>
    <?php endif; ?>
    <?php if($page === "addactor" || $page === "editactor"): ?>  
    <script type="text/javascript" src="<?php echo asset('/js/admin/formio_jquery.steps.unmin.js')?>"></script> 
    <script type="text/javascript" src="<?php echo asset('/js/admin/add-actor.js')?>"></script>  
    <?php endif; ?>
    <?php if($page === "editevent"): ?>  
     <script type="text/javascript" src="<?php echo asset('/js/admin/edit-event.js')?>"></script>   
    <?php endif; ?>
    <?php if($page === "addevent"): ?>  
     <script type="text/javascript" src="<?php echo asset('/js/admin/add-event.js')?>"></script>   
    <?php endif; ?>
    <?php if($page === "reportproblem"): ?>  
     <script type="text/javascript" src="<?php echo asset('/js/admin/report-problem.js')?>"></script>   
    <?php endif; ?>
    <?php if($page === "instrumentrights" || $page === "resourcerights"): ?>  
     <script type="text/javascript" src="<?php echo asset('/js/admin/instrument-rights.js')?>"></script>   
    <?php endif; ?>    
    <?php if($page === "productionevent"): ?>  
    <!-- for ajax file uploads -->
    <script type="text/javascript" src="<?php echo asset('/js/admin/plugins/uploaders/fileinput.unmin.js')?>"></script>
    <script type="text/javascript" src="<?php echo asset('/js/admin/formio_jquery.steps.unmin.js')?>"></script> 
    <script type="text/javascript" src="<?php echo asset('/js/admin/add-event.js')?>"></script> 
    <?php endif; ?>
    <?php if($page === "editinstrument"): ?>  
    <script type="text/javascript" src="<?php echo asset('/js/admin/formio_jquery.steps.unmin.js')?>"></script> 
    <script type="text/javascript" src="<?php echo asset('/js/admin/edit-instrument.js')?>"></script>
    <?php endif; ?>
    <?php if($page === "instrumentrights" || $page === "resourcerights" || $page === "eventmaterials"): ?>  
    <script type="text/javascript" src="<?php echo asset('/js/admin/formio_jquery.steps.unmin.js')?>"></script> 
    <script type="text/javascript" src="<?php echo asset('/js/admin/event-materials.js')?>"></script>
    <?php endif; ?>
    <?php if($page === "legalbodyprofile"): ?>  
    <!-- EDIT OWN COLLECTION HAS INITIAL PREVIEW AND HAS SESSION IMAGE BY DEFAULT-->
    <script type="text/javascript" src="<?php echo asset('/js/admin/legal-body.js')?>"></script>
    <script type="text/javascript">
        $(function() {
    $(".file-input-preview").fileinput({
        browseLabel: 'Browse',
        browseIcon: '<i class="icon-file-plus"></i>',
        uploadIcon: '<i class="icon-file-upload2"></i>',
        removeIcon: '<i class="icon-cross3"></i>',
        initialPreview: [
        "<img src='/images/legalBodyImages/<?php echo e($legalbody->legalBodyImage); ?>' class='file-preview-image' alt=''>",
        ],
        allowedFileExtensions: ["jpg", "gif", "png"],
        overwriteInitial: true
    });
});
</script>
<script type="text/javascript" src="<?php echo asset('/js/admin/pages/editor_ckeditor.js')?>"></script>
<?php endif; ?>
<?php if($page === "managexml"): ?>
        <script type="text/javascript" src="<?php echo asset('/js/admin/manage-xml.js')?>"></script>
        <script type="text/javascript">
            $(function() {
            // Single File - no initial preview
            $(".file-input-preview").fileinput({
                browseLabel: 'Browse',
                browseIcon: '<i class="icon-file-plus"></i>',
                uploadIcon: '<i class="icon-file-upload2"></i>',
                removeIcon: '<i class="icon-cross3"></i>',
                
                <?php if(strlen($importXMLfile) > 0): ?>

                    initialPreview: [
                    "<?php echo e($preview_file); ?>",
                    ],

                <?php endif; ?>

                allowedFileExtensions: ["xml"],
                overwriteInitial: true
            });
        });
        </script>
        <script type="text/javascript" src="<?php echo asset('/js/admin/pages/editor_ckeditor.js')?>"></script>
<?php endif; ?>
<?php if($page == "editcollection"): ?>  
<!-- edit collection has initial preview -->
<script type="text/javascript" src="<?php echo asset('/js/admin/legal-body.js')?>"></script>
<script type="text/javascript">
    $(function() {
    $(".file-input-preview").fileinput({
        browseLabel: 'Browse',
        browseIcon: '<i class="icon-file-plus"></i>',
        uploadIcon: '<i class="icon-file-upload2"></i>',
        removeIcon: '<i class="icon-cross3"></i>',
        initialPreview: [
        "<img src='/images/legalBodyImages/<?php echo e($activity->legalBodyImage); ?>' class='file-preview-image' alt=''>",
        ],
        allowedFileExtensions: ["jpg", "gif", "png"],
        overwriteInitial: true
    });
});
</script>
<script type="text/javascript" src="<?php echo asset('/js/admin/pages/editor_ckeditor.js')?>"></script>
<?php endif; ?>
<?php if($page === "addcollection"): ?>
<script type="text/javascript" src="<?php echo asset('/js/admin/legal-body.js')?>"></script>
<?php endif; ?>
<?php if($page === "addcollection" || $page === "adduser"): ?>  
<!-- add collection and add user has no inital preview -->
<script type="text/javascript">
    $(function() {
    // Single File - display preview of existing image and overwrite it on upload
    $(".file-input-preview").fileinput({
        browseLabel: 'Browse',
        browseIcon: '<i class="icon-file-plus"></i>',
        uploadIcon: '<i class="icon-file-upload2"></i>',
        removeIcon: '<i class="icon-cross3"></i>',
        allowedFileExtensions: ["jpg", "gif", "png"],
        overwriteInitial: true
    });
});
</script>
<script type="text/javascript" src="<?php echo asset('/js/admin/pages/editor_ckeditor.js')?>"></script>
<?php endif; ?>
<?php if(isset($resourceOrigFileType)): ?>
<!-- ADD COLLECTION AND ADD USER HAS NO INITIAL PREVIEW -->
    <?php if($resourceOrigFileType === "image"): ?>
        <script type="text/javascript" src="<?php echo asset('/js/admin/legal-body.js')?>"></script>
        <script type="text/javascript">
            $(function() {
            // Single File - display preview of existing image and overwrite it on upload
            $(".file-input-preview").fileinput({
                browseLabel: 'Browse',
                browseIcon: '<i class="icon-file-plus"></i>',
                uploadIcon: '<i class="icon-file-upload2"></i>',
                removeIcon: '<i class="icon-cross3"></i>',
                allowedFileExtensions: ["jpg", "gif", "png"],
                overwriteInitial: true
            });
        });
        </script>
        <script type="text/javascript" src="<?php echo asset('/js/admin/pages/editor_ckeditor.js')?>"></script>
    <?php endif; ?>
    <?php if($resourceOrigFileType === "sound"): ?>
        <script type="text/javascript" src="<?php echo asset('/js/admin/legal-body.js')?>"></script>
        <script type="text/javascript">
            $(function() {
            // Single File - display preview of existing image and overwrite it on upload
            $(".file-input-preview").fileinput({
                browseLabel: 'Browse',
                browseIcon: '<i class="icon-file-plus"></i>',
                uploadIcon: '<i class="icon-file-upload2"></i>',
                removeIcon: '<i class="icon-cross3"></i>',
                allowedFileExtensions: ["mp3"],
                overwriteInitial: true
            });
        });
        </script>
        <script type="text/javascript" src="<?php echo asset('/js/admin/pages/editor_ckeditor.js')?>"></script>
    <?php endif; ?>
    <?php if($resourceOrigFileType === "video"): ?>
        <script type="text/javascript" src="<?php echo asset('/js/admin/legal-body.js')?>"></script>
        <script type="text/javascript">
            $(function() {
            // Single File - display preview of existing image and overwrite it on upload
            $(".file-input-preview").fileinput({
                browseLabel: 'Browse',
                browseIcon: '<i class="icon-file-plus"></i>',
                uploadIcon: '<i class="icon-file-upload2"></i>',
                removeIcon: '<i class="icon-cross3"></i>',
                allowedFileExtensions: ["mp4"],
                overwriteInitial: true
            });
        });
        </script>
        <script type="text/javascript" src="<?php echo asset('/js/admin/pages/editor_ckeditor.js')?>"></script>
    <?php endif; ?>
<?php endif; ?>
<?php if($page === "addresource"): ?>
        <script type="text/javascript" src="<?php echo asset('/js/admin/legal-body.js')?>"></script>
        <script type="text/javascript">
            $(function() {
            // Single File - display preview of existing image and overwrite it on upload
            $(".file-input-preview").fileinput({
                browseLabel: 'Browse',
                browseIcon: '<i class="icon-file-plus"></i>',
                uploadIcon: '<i class="icon-file-upload2"></i>',
                removeIcon: '<i class="icon-cross3"></i>',
                allowedFileExtensions: ["jpg", "gif", "png", "mp3", "mp4"],
                overwriteInitial: true
            });
        });
        </script>
        <script type="text/javascript" src="<?php echo asset('/js/admin/pages/editor_ckeditor.js')?>"></script>
<?php endif; ?>
<?php if($page === "accountsettings"): ?>  
<script type="text/javascript">
    $(function() {
    // Single File - display preview of existing image and overwrite it on upload
    $(".file-input-preview").fileinput({
        browseLabel: 'Browse',
        browseIcon: '<i class="icon-file-plus"></i>',
        uploadIcon: '<i class="icon-file-upload2"></i>',
        removeIcon: '<i class="icon-cross3"></i>',
        initialPreview: [
        "<img src='/images/users/thumbnails/<?php echo e(Session::get('avatar')); ?>' class='file-preview-image' alt=''>",
        ],
        allowedFileExtensions: ["jpg", "gif", "png"],
        overwriteInitial: true
    });
});
</script>
<script type="text/javascript" src="<?php echo asset('/js/admin/pages/editor_ckeditor.js')?>"></script>
<?php endif; ?>
<?php if($page === "edituser"): ?>  
<script type="text/javascript">
    $(function() {
    // Single File - display preview of existing image and overwrite it on upload
    $(".file-input-preview").fileinput({
        browseLabel: 'Browse',
        browseIcon: '<i class="icon-file-plus"></i>',
        uploadIcon: '<i class="icon-file-upload2"></i>',
        removeIcon: '<i class="icon-cross3"></i>',
        initialPreview: [
        "<img src='/images/users/thumbnails/<?php echo e($user->avatar); ?>' class='file-preview-image' alt=''>",
        ],
        allowedFileExtensions: ["jpg", "gif", "png"],
        overwriteInitial: true
    });
});
</script>
<script type="text/javascript" src="<?php echo asset('/js/admin/pages/editor_ckeditor.js')?>"></script>
<?php endif; ?>
<?php if($page === "adduser"): ?>  
<script type="text/javascript" src="<?php echo asset('/js/admin/add-new-user.js')?>"></script>
<?php endif; ?>
<?php if($page === "editresource"): ?>  
<script type="text/javascript" src="<?php echo asset('/js/admin/edit-resource.js')?>"></script>
<?php endif; ?>
<?php if($page === "addresource"): ?>  
<script type="text/javascript" src="<?php echo asset('/js/admin/add-resource.js')?>"></script>
<?php endif; ?>
<?php if($page === "accountsettings" || $page === "edituser"): ?>  
<script type="text/javascript" src="<?php echo asset('/js/admin/account-settings.js')?>"></script>
<?php endif; ?>  
<?php if($page === "changepassword"): ?>  
<script type="text/javascript" src="<?php echo asset('/js/admin/add-new-user.js')?>"></script>
<?php endif; ?>  
<!-- /form validation specific to the page ends here -->
<?php if($page === "viewedit" || $page === "existingcollections" || $page === "existingusers" || $page === "accountsettings" || $page === "adduser"): ?>
<link href="<?php echo e(asset('/datatables/css/datatables.bootstrap.css')); ?>" rel="stylesheet">
<link rel="stylesheet" href="<?php echo e(asset('/datatables/highlight/styles/zenburn.css')); ?>">
<script src="<?php echo e(asset('/datatables/highlight/highlight.pack.js')); ?>"></script>
<script>hljs.initHighlightingOnLoad();</script>
<?php endif; ?> 
<?php if($page === "viewedit" || $page === "editinstrument" || $page == "instrumentrights" || $page === "editaudio" || $page === "editresource" || $page === "editimages"): ?>
<script type="text/javascript">
$(document).ready(function() {
               $(document).on("hidden.bs.modal", function (e) {
                   //alert ("clear modal body here") 
                   //  $(e.target).removeData("#modal_remote").find(".modal-body").html('');
                });     
});
</script>
<?php endif; ?> 
<script type='text/javascript'  src='<?php echo asset('/js/admin/jqgrowl/jqgrowl.js')?>' ></script>
<link   type='text/css'        href="<?php echo e(asset('/js/admin/jqgrowl/jqgrowl.css')); ?>" rel='stylesheet' />
<!-- idle timeout -->
<script type='text/javascript'  src='<?php echo asset('/js/admin/vanilla.idle.js')?>' ></script>
  <script type="text/javascript">
    idle({
      onIdle: function(){
        window.location.replace("/logout");
      },
      idle: 2700000, // 60000 is 1 min - 2700000 is 45mins 
      keepTracking: true
    }).start();
  </script>
</head>
<body>
    <!-- Main navbar -->
    <div class="navbar navbar-inverse">
        <div class="navbar-header">
            <a class="navbar-brand" href="<?php echo e(url('/dashboard')); ?>"><img src="<?php echo e(url('/images/admin/logo_light.png')); ?>" alt=""></a>

            <ul class="nav navbar-nav visible-xs-block">
                <li><a data-toggle="collapse" data-target="#navbar-mobile"><i class="icon-tree5"></i></a></li>
                <li><a class="sidebar-mobile-main-toggle"><i class="icon-paragraph-justify3"></i></a></li>
            </ul>
        </div>
        <div class="navbar-collapse collapse" id="navbar-mobile">
            <ul class="nav navbar-nav">
                <li><a class="sidebar-control sidebar-main-toggle hidden-xs"><i class="icon-paragraph-justify3"></i></a></li>               
            </ul>
            <p class="navbar-text">
                <span class="label bg-success">Online</span>
            </p>
            <div class="navbar-right">
                <ul class="nav navbar-nav">
                    <li class="dropdown dropdown-user">
                        <a class="dropdown-toggle" data-toggle="dropdown">
                            <img src="<?php echo e(url('/images/users/thumbnails/')); ?>/<?php echo e(Auth::user()->avatar); ?>" alt="">
                            <span><?php echo e(Auth::user()->name); ?> </span>
                            <i class="caret"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-right">
<?php if($role != "SuperAdmin"): ?>
                            <li><a href="<?php echo e(url('/legalbody-profile')); ?>"><i class="icon-user-plus"></i> <?php echo e(Session::get('legalBodyShortName')); ?> profile</a></li>                          
<?php endif; ?>
                            

                            <li class="divider" style="background-color:#fff; height:26px;"></li> 


                            <li><a href="<?php echo e(url('/account-settings')); ?>"><i class="icon-cog5"></i> My Account settings</a></li>
                            <li><a href="<?php echo e(url('/change-password')); ?>"><i class="icon-key"></i> Change Password</a></li>
                            <li><a href="<?php echo e(url('/report-problem')); ?>"><span style="color:red;"><i class="icon-bug2"></i></span><span style="margin-left: 10px;"> Report Problem</span></a></li>
                            <li><a href="<?php echo e(url('/logout')); ?>"><strong><i class="icon-switch2"></i><span style="margin-left: 10px;"> Logout</span></strong></a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <!-- /main navbar -->
    <noscript>
        <style type="text/css">
            .page-container {display:none;}
            .noscriptmsg p { font-size:20px; }
        </style>
        <div class="noscriptmsg">
        <p>You don't have javascript enabled.  MINIM UK Admin only works on a <a href="https://www.mozilla.org">modern browser</a> with javascript enabled.</p>
        </div>
    </noscript>
    <!-- Page container -->
    <div class="page-container">
        <!-- Page content -->
        <div class="page-content">
            <!-- Main sidebar -->
            <div class="sidebar sidebar-main">
                <div class="sidebar-content">
                    <!-- User menu -->
                    <div class="sidebar-user">
                        <div class="category-content">
                            <div class="media">
                                <a href="<?php echo e(url('/account-settings')); ?>" class="media-left"><img src="<?php echo e(url('/images/users/thumbnails/')); ?>/<?php echo e(Auth::user()->avatar); ?>" class="img-circle img-sm" alt=""></a>
                                <div class="media-body">
                                    <span class="media-heading text-semibold"><?php echo e(Auth::user()->name); ?> <?php echo e(Auth::user()->surname); ?></span>
                                    <div class="text-size-mini text-muted">
                                        <i class="icon-headset text-size-small"></i> &nbsp;<?php echo e(Auth::user()->role); ?>

                                    </div>
                                </div>
                                <div class="media-right media-middle">
                                    <ul class="icons-list">
                                        <li>
                                            <a href="<?php echo e(url('/account-settings')); ?>"><i class="icon-cog3"></i></a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /user menu -->
                    <!-- Main navigation -->
                    <div class="sidebar-category sidebar-category-visible">
                        <div class="category-content no-padding">
                            <ul class="navigation navigation-main navigation-accordion">
                                <!-- Main -->
                                <li class="navigation-header"><span>Main</span> <i class="icon-menu" title="Main pages"></i></li>
                                <li
                                <?php if($page === "dashboard"): ?> class="active" <?php endif; ?>
                                ><a href="<?php echo e(url('/dashboard')); ?>"><i class="icon-home4"></i> <span>Dashboard</span></a></li>                               
                                <li>
                                    <a href="#"><i class="icon-piano"></i> <span>Instruments</span></a>
                                    <ul>
                                        <li
                                        <?php if($page === "addinstrument" || $page === "addinstrument_two"): ?> class="active" <?php endif; ?>
                                        ><a href="<?php echo e(url('/addinstrument')); ?>">Add New Instrument</a></li>
                                        <li
                                        <?php if($page === "viewedit"  OR $page === "editinstrument" OR $page === "instrumentrights" OR $page == "resourcerights" OR $page === "eventmaterials" OR $page === "editresource" || $page === "addresource" || $page == "productionevent" || $page == "editimages" || $page == "editaudio" || $page == "editvideo" || $page == "addevent" || $page == "addactor" || $page == "editevent" || $page == "addevent" || $page === "editactor" || $page == "deleteinstrument"): ?> class="active" <?php endif; ?>
                                        ><a href="<?php echo e(url('/instruments')); ?>">View or Edit Instrument</a></li>
                                    </ul>
                                </li>

                                <?php if($role === "SuperAdmin"): ?>  
                                <!-- SuperAdmin -->
                                <li class="navigation-header"><span>SuperAdmin Options</span> <i class="icon-menu" title="SuperAdmin options"></i></li>
                                <li>
                                    <a href="#"><i class="icon-database-edit2"></i> <span>Collections</span></a>
                                    <ul>
                                        <li
                                        <?php if($page === "addcollection"): ?> class="active" <?php endif; ?>
                                        ><a href="<?php echo e(url('/add-collection')); ?>">Add New Collection</a></li>
                                        <li
                                        <?php if($page === "existingcollections" || $page === "editcollection" || $page === "managexml" ): ?> class="active" <?php endif; ?>
                                        ><a href="<?php echo e(url('/existing-collections')); ?>">Existing Collections</a></li>
                                        <li
                                        <?php if($page === "importintocollection"  || $page === "importintocollection_go"): ?> class="active" <?php endif; ?>
                                        ><a href="<?php echo e(url('/import-into-collection')); ?>">Import Into Collection</a></li>
                                    </ul>
                                </li>
                                <li>
                                    <a href="#"><i class="icon-users2"></i> <span>Users</span></a>
                                    <ul>
                                        <li
                                        <?php if($page === "adduser"): ?> class="active" <?php endif; ?>
                                        ><a href="<?php echo e(url('/add-user')); ?>">Add New User</a></li>
                                        <li
                                        <?php if(($page === "existingusers" || $page === "edituser")): ?> class="active" <?php endif; ?>
                                        ><a href="<?php echo e(url('/existing-users')); ?>">Existing Users</a></li>
                                    </ul>
                                </li>
                                <?php endif; ?>
                                <?php if($role === "Cataloguer"): ?>  
                                <!-- Cataloguer -->
                                <li class="navigation-header"><span>Cataloguer Options</span> <i class="icon-menu" title="SuperAdmin options"></i></li>
                                <li>
                                    <a href="#"><i class="icon-database-edit2"></i> <span>Collections</span></a>
                                    <ul>
                                        <li
                                        <?php if($page === "addcollection"): ?> class="active" <?php endif; ?>
                                        ><a href="<?php echo e(url('/add-collection')); ?>">Add New Collection</a></li>
                                        <li
                                        <?php if($page === "existingcollections" || $page === "editcollection" || $page === "managexml" ): ?> class="active" <?php endif; ?>
                                        ><a href="<?php echo e(url('/existing-collections')); ?>">Existing Collections</a></li>
                                    </ul>
                                </li>    
                                <?php endif; ?>
                                <?php if($role === "Admin"): ?>  
                                <!-- Admin -->
                                <li class="navigation-header"><span>Admin Options</span> <i class="icon-menu" title="Admin options"></i></li>
                                <?php endif; ?>   
                                <li>
                                    <a href="#"><i class="icon-wrench"></i> <span>Account Settings</span></a>
                                    <ul>
<?php if($role != "SuperAdmin"): ?>
                                        <li
                                        <?php if($page === "legalbodyprofile"): ?> class="active" <?php endif; ?>
                                        ><a href="<?php echo e(url('/legalbody-profile')); ?>"><?php echo e(Session::get('legalBodyName')); ?> Profile</a>
                                        </li>
<?php endif; ?>
                                        <li
                                        <?php if($page === "accountsettings"): ?> class="active" <?php endif; ?>
                                        ><a href="<?php echo e(url('/account-settings')); ?>">My Account Settings</a>
                                       </li>
                                        <li
                                        <?php if($page === "changepassword"): ?> class="active" <?php endif; ?>
                                        ><a href="<?php echo e(url('/change-password')); ?>">Change Password</a>
                                       </li>
                                    </ul>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <!-- /main navigation -->
                </div>
            </div>
            <!-- /main sidebar -->
            <!-- Main content -->
            <div class="content-wrapper">
                <!-- Page header -->
                <div class="page-header page-header-default">
                    <div class="page-header-content">
                        <div class="page-title">
                           <h4><i class="icon-arrow-right32 position-left"></i>
                             
                            <?php echo $__env->yieldContent('title'); ?>

                        </h4>
                    </div>
                </div>
                <div class="breadcrumb-line">
                    <ul class="breadcrumb">

                        <?php echo $__env->yieldContent('breadcrumbs'); ?>

                    </ul>
                </div>
            </div>
            <!-- /page header -->
            <!-- content area -->
            <div class="content">
                <?php if(session()->has('flashdata')): ?>
                <!-- flash data -->
                <script>
                    window.onload = function(){
                        $.jqGrowl.init( { position: 'absolute', top: '8px', right: '8px' }, 3000);
                        $.jqGrowl.msg('<?php echo e(Session::get('flashdata')); ?>', 'Success!');
                    }
                </script>
                <!-- /flash data  -->
                <?php endif; ?>

                <?php echo $__env->yieldContent('content'); ?>

                <!-- Footer --> 
                <div class="footer text-muted">
                    &copy; <?php echo date("Y"); ?>, MINIM-UK.
                </div>
                <!-- /footer -->
            </div>
            <!-- /content area -->
        </div>
        <!-- /main content -->
    </div>
    <!-- /page content -->
</div> 
<!-- /page container -->
<script src="<?php echo e(asset('datatables/js/jquery.dataTables.min.js')); ?>"></script>
<script src="<?php echo e(asset('datatables/js/datatables.bootstrap.js')); ?>"></script>
<script src="<?php echo e(asset('datatables/js/handlebars.js')); ?>"></script>
<?php echo $__env->yieldPushContent('scripts'); ?>
<?php if(config('analytics.enabled', false)): ?>
<?php echo $__env->make('analytics', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
<?php endif; ?>
</body>
</html>