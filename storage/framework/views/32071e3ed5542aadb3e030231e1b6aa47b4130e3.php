<?php foreach($user as $key => $user): ?>



<?php $__env->startSection('title'); ?>
Existing Users / <?php echo e($user->name); ?> <?php echo e($user->surname); ?>

<?php $__env->stopSection(); ?>



<?php $__env->startSection('breadcrumbs'); ?>
                            <li><a href="<?php echo e(url('/')); ?>"><i class="icon-home2 position-left"></i>Dashboard</a></li>
                            <li><a href="<?php echo e(url('/existing-users')); ?>"></i>Existing Users</a></li>
                            <li class="active"><?php echo e($user->name); ?> <?php echo e($user->surname); ?></li>
<?php $__env->stopSection(); ?>



<?php $__env->startSection('content'); ?>
        
<?php /*  CATCH FORM ERRORS */ ?>
<?php echo $__env->make('partials/message-block', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>       

<?php /*  EDIT EXISTING USER FORM */ ?>
                    <form id="adduser" method="post" enctype="multipart/form-data" action="<?php echo e(url('/edit-user/store')); ?>" class="steps-validation">
                    <input type="hidden" name="id" value="<?php echo e($id); ?>"/>
                    <input type="hidden" name="avatarOrig" value="<?php echo e($user->avatar); ?>"/>

                        <?php echo csrf_field(); ?>


                        <div class="row">
                            <div class="col-lg-8">
                                <div class="panel registration-form">
                                    <div class="panel-body">
                                        <div class="text-center">
                                            <div class="icon-object border-success text-success"><img style="max-width: 100px;" src="<?php echo e(url('/images/users')); ?>/<?php echo e($user->avatar); ?>" alt=""></div>
                                            <h5 class="content-group-lg"><?php echo e($user->name); ?> <?php echo e($user->surname); ?>'s MINIM-UK Account Details<small class="display-block">All fields except Avatar image are required</small></h5>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group has-feedback">
                                                  <label class="control-label">Select Admin Level For New User</label>
                                                    <div class="multi-select-full">
                                                        <select class="form-control" name="adminLevel">
                                                            <option value="SuperAdmin" <?php if($user->role=="SuperAdmin"): ?> selected="selected" <?php endif; ?>>Access To All Collections and User Functions  [ SUPERADMIN ]</option>) 
                                                            <option value="Cataloguer" <?php if($user->role=="Cataloguer"): ?> selected="selected" <?php endif; ?>>Access To All Collections  [ CATALOGUER ]</option>) 
                                                            <?php foreach($legalbodies as $legalbody): ?>
                                                                <option 
                                                                <?php if( ($user->legalBodyID == $legalbody->legalBodyID) && ($user->role == "Admin")): ?>
                                                                selected="selected"
                                                                <?php endif; ?> 
                                                                value="admin_<?php echo e($legalbody->legalBodyID); ?>"><?php echo e($legalbody->legalBodyName); ?>'s Collection Only [ ADMIN ]</option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                 <div class="form-group has-feedback">
                                                  <label class="control-label">Default Collection <span style="font-size:9px;">(SuperAdmin And Cataloguer Only)</span></label>
                                                    <div class="multi-select-full">
                                                        <select class="form-control" name="legalBodyID">
                                                            <?php foreach($legalbodies as $legalbody): ?>
                                                                <option
                                                                <?php if($user->legalBodyID == $legalbody->legalBodyID): ?>
                                                                selected="selected"
                                                                <?php endif; ?> 
                                                                 value="<?php echo e($legalbody->legalBodyID); ?>"><?php echo e($legalbody->legalBodyName); ?></option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>    
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group has-feedback <?php echo e($errors->has('name') ? 'has-error' : ''); ?>">
                                                <label class="control-label">First Name</label>
                                                    <input type="text" name="name" class="form-control" placeholder="First name" value="<?php echo e($user->name); ?>">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group has-feedback <?php echo e($errors->has('surname') ? 'has-error' : ''); ?>">
                                                <label class="control-label">Surname</label>
                                                    <input type="text" name="surname" class="form-control" placeholder="Surname" value="<?php echo e($user->surname); ?>">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group has-feedback <?php echo e($errors->has('email') ? 'has-error' : ''); ?>">
                                                <label class="control-label">Email Address</label>
                                                    <input value="<?php echo e($user->email); ?>" type="email" class="form-control" name="email" placeholder="Email address">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group has-feedback <?php echo e($errors->has('confirmemail') ? 'has-error' : ''); ?>">
                                                <label class="control-label">Email Address</label>
                                                    <input value="<?php echo e($user->email); ?>" type="email" class="form-control" name="confirmemail" placeholder="Repeat email address">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group has-feedback">
                                                    <label class="control-label">Avatar Image:</label>
                                                    <div class="col-lg-12">
                                                        <input name="avatar" type="file" class="file-input-preview" data-show-remove="true">
                                                        <span class="help-block">Please upload an image. <code>jpg, gif, png</code> accepted.</span><br/>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <button type="submit" class="btn bg-teal-400 btn-labeled btn-labeled-right ml-10"><b><i class="icon-plus3"></i></b> Update this MINIM UK account</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <!-- open right column -->
                        <div class="col-lg-4">
<!-- Recent Activity -->
                            <div class="panel panel-flat">
                                <div class="panel-heading">
                                    <h6 class="panel-title"><?php echo e($user->name); ?> <?php echo e($user->surname); ?>'s Recent Activity</h6>
                                </div>
                                <div class="panel-body">
                                    <div class="content-group-xs" id="bullets"></div>
                            <?php if(count($user_activity) < 1): ?>
                                <?php /* This user has no activity*/ ?>
                                <p style="margin-top:-20px;">This user has no activity.</p>
                            <?php endif; ?>
                                    <ul class="media-list" style="margin-top: -10px;">
                             <?php foreach($user_activity as $key => $activity): ?>
                                      <?php if($activity->type == 'admin_import'): ?>    
                                         <!-- IMPORTED -->
                                            <li class="media">
                                                <div class="media-left media-middle">
                                                    <a title="<?php echo e($user->name); ?> imported instruments"><img src="/images/users/<?php echo e($user->avatar); ?>" class="img-circle img-xs" alt=""></a>
                                                </div>
                                                
                                                <div class="media-body">
                                                    <p><?php echo e($user->name); ?> imported instruments to <a href="/existing-collections/edit/<?php echo e($activity->legalBodyID); ?>"><?php echo e($activity->legalBodyName); ?></a></p>
                                                    <div class="media-annotation"><?php echo e($activity_time_ago[$key]); ?></div>
                                                </div>
                                             </li>    
                                        <?php endif; ?>
                                       <?php if($activity->activity == 'You changed your password'): ?>        
                                            <!-- CHANGED PASSWORD -->
                                            <li class="media">
                                                <div class="media-left">
                                                    <a title="<?php echo e($user->name); ?> changed their password" class="btn border-teal-400 text-teal btn-flat btn-rounded btn-icon btn-xs"><i class="icon-user-lock"></i></a>
                                                </div>
                                                <div class="media-body">
                                                    <p><?php echo e($user->name); ?> changed their password</p>
                                                    <div class="media-annotation"><?php echo e($activity_time_ago[$key]); ?></div>
                                                </div>
                                             </li>
                                        <?php endif; ?>
                                        <?php if($activity->activity == 'You updated your account details'): ?>        
                                                    <!-- UPDATED ACCOUNT DETAILS -->
                                                    <li class="media">
                                                        <div class="media-left">
                                                            <a title="<?php echo e($user->name); ?> updated their account details" class="btn border-warning-400 text-warning-400 btn-flat btn-rounded btn-icon btn-xs"><i class="icon-folder-check"></i></a>
                                                        </div>
                                                        
                                                        <div class="media-body">
                                                            <p><?php echo e($user->name); ?> updated their account details</p>
                                                            <div class="media-annotation"><?php echo e($activity_time_ago[$key]); ?></div>
                                                        </div>
                                                    </li>
                                        <?php endif; ?>
                                        <?php if($activity->activity == 'You logged in'): ?>        
                                                    <!-- LOGGED IN -->
                                                    <li class="media">
                                                        <div class="media-left">
                                                            <a title="<?php echo e($user->name); ?> logged in" class="btn border-teal-400 text-teal btn-flat btn-rounded btn-icon btn-xs"><i class="icon-user-plus"></i></a>
                                                        </div>
                                                        <div class="media-body">
                                                            <p><?php echo e($user->name); ?> logged in</p>
                                                            <div class="media-annotation"><?php echo e($activity_time_ago[$key]); ?></div>
                                                        </div>
                                                        <div class="media-right media-middle">
                                                            <ul class="icons-list">
                                                                <li>
                                                                    <a href="#" title="go to instrument"><i class="icon-arrow-right13"></i></a>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </li>
                                        <?php endif; ?>
                                        <?php if($activity->activity == 'You logged out'): ?>        
                                                    <!-- LOGGED OUT -->
                                                    <li class="media">
                                                        <div class="media-left">
                                                            <a title="<?php echo e($user->name); ?> logged out" class="btn border-primary text-primary btn-flat btn-rounded btn-icon btn-xs"><i class="icon-user-minus"></i></a>
                                                        </div>
                                                        <div class="media-body">
                                                            <p><?php echo e($user->name); ?> logged out</p>
                                                            <div class="media-annotation"><?php echo e($activity_time_ago[$key]); ?></div>
                                                        </div>
                                                        <div class="media-right media-middle">
                                                            <ul class="icons-list">
                                                                <li>
                                                                    <a href="#" title="go to instrument"><i class="icon-arrow-right13"></i></a>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </li>
                                        <?php endif; ?>
                                        <?php if($activity->activity == 'You added an instrument'): ?>   
                                                    <!-- ADDED AN INSTRUMENT -->
                                                    <li class="media">
                                                        <div class="media-left">
                                                            <a title="go to instrument (<?php echo e($activity->instrumentName); ?>)" href="/edit-instrument/<?php echo e($activity->instrumentID); ?>" class="btn border-success text-success btn-flat btn-rounded btn-icon btn-xs"><i class="icon-folder-plus"></i></a>
                                                        </div>
                                                        <div class="media-body">
                                                            <p><?php echo e($user->name); ?> created an instrument</p>
                                                              <p><a href="/existing-collections/edit/<?php echo e($activity->legalBodyID); ?>"><?php echo e($activity->legalBodyName); ?></a> / <a title="go to instrument (<?php echo e($activity->instrumentName); ?>)" href="/edit-instrument/<?php echo e($activity->instrumentID); ?>"><?php echo e($activity->instrumentName); ?></a></p>
                                                            <div class="media-annotation"><?php echo e($activity_time_ago[$key]); ?></div>
                                                        </div>
                                                        <div class="media-right media-middle">
                                                            <ul class="icons-list">
                                                                <li>
                                                                    <a href="/edit-instrument/<?php echo e($activity->instrumentID); ?>" title="go to instrument (<?php echo e($activity->instrumentName); ?>)"><i class="icon-arrow-right13"></i></a>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </li>
                                        <?php endif; ?>
                                        <?php if($activity->activity == "You added an actor to an instrument's event"): ?>   
                                                    <!-- ADDED ACTOR -->
                                                    <li class="media">
                                                        <div class="media-left">
                                                            <a title="go to instrument (<?php echo e($activity->instrumentName); ?>)" href="/edit-instrument/<?php echo e($activity->instrumentID); ?>" class="btn border-success text-success btn-flat btn-rounded btn-icon btn-xs"><i class="icon-folder-plus"></i></a>
                                                        </div>
                                                        <div class="media-body">
                                                            <p><?php echo e($user->name); ?> added an actor to an instrument's event</p>
                                                              <p><a href="/edit-instrument/<?php echo e($activity->instrumentID); ?>">Go to instrument</a></p>
                                                            <div class="media-annotation"><?php echo e($activity_time_ago[$key]); ?></div>
                                                        </div>
                                                        <div class="media-right media-middle">
                                                            <ul class="icons-list">
                                                                <li>
                                                                    <a href="/edit-instrument/<?php echo e($activity->instrumentID); ?>" title="go to instrument (<?php echo e($activity->instrumentName); ?>)"><i class="icon-arrow-right13"></i></a>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </li>
                                        <?php endif; ?>
                                        <?php if($activity->activity == 'You added an image'): ?>   
                                                    <!-- ADDED AN IMAGE TO AN INSTRUMENT -->
                                                    <li class="media">
                                                        <div class="media-left">
                                                            <a title="go to instrument (<?php echo e($activity->instrumentName); ?>)" href="/edit-instrument/<?php echo e($activity->instrumentID); ?>" class="btn border-success text-success btn-flat btn-rounded btn-icon btn-xs"><i class="icon-folder-plus"></i></a>
                                                        </div>
                                                        <div class="media-body">
                                                            <p><?php echo e($user->name); ?> added an image to <?php echo e($activity->legalBodyName); ?>'s</p>
                                                              <p> <a title="go to instrument (<?php echo e($activity->instrumentName); ?>)" href="/edit-instrument/<?php echo e($activity->instrumentID); ?>"><?php echo e($activity->instrumentName); ?></a> | <a title="go to image" href="/edit-instrument/<?php echo e($activity->instrumentID); ?>/resource/<?php echo e($activity->resourceID); ?>"><?php echo e($activity->resourceName); ?></a></p>
                                                            <div class="media-annotation"><?php echo e($activity_time_ago[$key]); ?></div>
                                                        </div>
                                                        <div class="media-right media-middle">
                                                            <ul class="icons-list">
                                                                <li>
                                                                    <a href="/edit-instrument/<?php echo e($activity->instrumentID); ?>" title="go to instrument (<?php echo e($activity->instrumentName); ?>)"><i class="icon-arrow-right13"></i></a>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </li>
                                        <?php endif; ?>
                                        <?php if($activity->activity == 'You added a video'): ?>   
                                                    <!-- ADDED VIDEO -->
                                                    <li class="media">
                                                        <div class="media-left">
                                                            <a title="go to instrument (<?php echo e($activity->instrumentName); ?>)" href="/edit-instrument/<?php echo e($activity->instrumentID); ?>" class="btn border-success text-success btn-flat btn-rounded btn-icon btn-xs"><i class="icon-folder-plus"></i></a>
                                                        </div>
                                                        <div class="media-body">
                                                            <p><?php echo e($user->name); ?> added a video to <?php echo e($activity->legalBodyName); ?>'s</p>
                                                              <p> <a title="go to instrument (<?php echo e($activity->instrumentName); ?>)" href="/edit-instrument/<?php echo e($activity->instrumentID); ?>"><?php echo e($activity->instrumentName); ?></a> | <a title="go to video" href="/edit-instrument/<?php echo e($activity->instrumentID); ?>/resource/<?php echo e($activity->resourceID); ?>"><?php echo e($activity->resourceName); ?></a></p>
                                                            <div class="media-annotation"><?php echo e($activity_time_ago[$key]); ?></div>
                                                        </div>
                                                        <div class="media-right media-middle">
                                                            <ul class="icons-list">
                                                                <li>
                                                                    <a href="/edit-instrument/<?php echo e($activity->instrumentID); ?>" title="go to instrument (<?php echo e($activity->instrumentName); ?>)"><i class="icon-arrow-right13"></i></a>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </li>
                                        <?php endif; ?>
                                        <?php if($activity->activity == 'You added audio'): ?>   
                                                    <!-- ADDED AUDIO -->
                                                    <li class="media">
                                                        <div class="media-left">
                                                            <a title="go to instrument (<?php echo e($activity->instrumentName); ?>)" href="/edit-instrument/<?php echo e($activity->instrumentID); ?>" class="btn border-success text-success btn-flat btn-rounded btn-icon btn-xs"><i class="icon-folder-plus"></i></a>
                                                        </div>
                                                        <div class="media-body">
                                                            <p><?php echo e($user->name); ?> added audio to <?php echo e($activity->legalBodyName); ?>'s</p>
                                                              <p> <a title="go to instrument (<?php echo e($activity->instrumentName); ?>)" href="/edit-instrument/<?php echo e($activity->instrumentID); ?>"><?php echo e($activity->instrumentName); ?></a> | <a title="go to audio" href="/edit-instrument/<?php echo e($activity->instrumentID); ?>/resource/<?php echo e($activity->resourceID); ?>"><?php echo e($activity->resourceName); ?></a></p>
                                                            <div class="media-annotation"><?php echo e($activity_time_ago[$key]); ?></div>
                                                        </div>
                                                        <div class="media-right media-middle">
                                                            <ul class="icons-list">
                                                                <li>
                                                                    <a href="/edit-instrument/<?php echo e($activity->instrumentID); ?>" title="go to instrument (<?php echo e($activity->instrumentName); ?>)"><i class="icon-arrow-right13"></i></a>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </li>
                                        <?php endif; ?>
                                        <?php if($activity->activity == 'You deleted a video'): ?>   
                                                    <!-- ADDED VIDEO -->
                                                    <li class="media">
                                                        <div class="media-left">
                                                            <a title="go to instrument (<?php echo e($activity->instrumentName); ?>)" href="/edit-instrument/<?php echo e($activity->instrumentID); ?>" class="btn border-success text-success btn-flat btn-rounded btn-icon btn-xs"><i class="icon-folder-plus"></i></a>
                                                        </div>
                                                        <div class="media-body">
                                                            <p><?php echo e($user->name); ?> deleted a video from <?php echo e($activity->legalBodyName); ?>'s <?php echo e($activity->instrumentName); ?></p>
                                                            <div class="media-annotation"><?php echo e($activity_time_ago[$key]); ?></div>
                                                        </div>
                                                        <div class="media-right media-middle">
                                                            <ul class="icons-list">
                                                                <li>
                                                                    <a href="/edit-instrument/<?php echo e($activity->instrumentID); ?>" title="go to instrument (<?php echo e($activity->instrumentName); ?>)"><i class="icon-arrow-right13"></i></a>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </li>
                                        <?php endif; ?>
                                        <?php if($activity->activity == 'You deleted a image'): ?>   
                                                    <!-- DELETED IMAGE -->
                                                    <li class="media">
                                                        <div class="media-left">
                                                            <a title="go to instrument (<?php echo e($activity->instrumentName); ?>)" href="/edit-instrument/<?php echo e($activity->instrumentID); ?>" class="btn border-success text-success btn-flat btn-rounded btn-icon btn-xs"><i class="icon-folder-plus"></i></a>
                                                        </div>
                                                        <div class="media-body">
                                                            <p><?php echo e($user->name); ?> deleted an image from <?php echo e($activity->legalBodyName); ?>'s <?php echo e($activity->instrumentName); ?></p>
                                                            <div class="media-annotation"><?php echo e($activity_time_ago[$key]); ?></div>
                                                        </div>
                                                        <div class="media-right media-middle">
                                                            <ul class="icons-list">
                                                                <li>
                                                                    <a href="/edit-instrument/<?php echo e($activity->instrumentID); ?>" title="go to instrument (<?php echo e($activity->instrumentName); ?>)"><i class="icon-arrow-right13"></i></a>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </li>
                                        <?php endif; ?>
                                        <?php if($activity->activity == 'You deleted a sound'): ?>   
                                                    <!-- DELETED AUDIO -->
                                                    <li class="media">
                                                        <div class="media-left">
                                                            <a title="go to instrument (<?php echo e($activity->instrumentName); ?>)" href="/edit-instrument/<?php echo e($activity->instrumentID); ?>" class="btn border-success text-success btn-flat btn-rounded btn-icon btn-xs"><i class="icon-folder-plus"></i></a>
                                                        </div>
                                                        <div class="media-body">
                                                            <p><?php echo e($user->name); ?> deleted audio from <?php echo e($activity->legalBodyName); ?>'s <?php echo e($activity->instrumentName); ?></p>
                                                            <div class="media-annotation"><?php echo e($activity_time_ago[$key]); ?></div>
                                                        </div>
                                                        <div class="media-right media-middle">
                                                            <ul class="icons-list">
                                                                <li>
                                                                    <a href="/edit-instrument/<?php echo e($activity->instrumentID); ?>" title="go to instrument (<?php echo e($activity->instrumentName); ?>)"><i class="icon-arrow-right13"></i></a>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </li>
                                        <?php endif; ?>
                                        <?php if($activity->activity == 'You updated an image resource'): ?>   
                                                    <!-- UPDATED IMAGE -->
                                                    <li class="media">
                                                        <div class="media-left">
                                                            <a title="go to instrument (<?php echo e($activity->instrumentName); ?>)" href="/edit-instrument/<?php echo e($activity->instrumentID); ?>" class="btn border-success text-success btn-flat btn-rounded btn-icon btn-xs"><i class="icon-folder-plus"></i></a>
                                                        </div>
                                                        <div class="media-body">
                                                            <p><?php echo e($user->name); ?> updated a <?php echo e($activity->legalBodyName); ?> image resource</p>
                                                              <p> <a title="go to instrument (<?php echo e($activity->instrumentName); ?>)" href="/edit-instrument/<?php echo e($activity->instrumentID); ?>"><?php echo e($activity->instrumentName); ?></a> | <a title="go to image" href="/edit-instrument/<?php echo e($activity->instrumentID); ?>/resource/<?php echo e($activity->resourceID); ?>">Image</a></p>
                                                            <div class="media-annotation"><?php echo e($activity_time_ago[$key]); ?></div>
                                                        </div>
                                                        <div class="media-right media-middle">
                                                            <ul class="icons-list">
                                                                <li>
                                                                    <a href="/edit-instrument/<?php echo e($activity->instrumentID); ?>" title="go to instrument (<?php echo e($activity->instrumentName); ?>)"><i class="icon-arrow-right13"></i></a>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </li>
                                        <?php endif; ?>
                                        <?php if($activity->activity == 'You updated an audio resource'): ?>   
                                                    <!-- UPDATED AUDIO -->
                                                    <li class="media">
                                                        <div class="media-left">
                                                            <a title="go to instrument (<?php echo e($activity->instrumentName); ?>)" href="/edit-instrument/<?php echo e($activity->instrumentID); ?>" class="btn border-success text-success btn-flat btn-rounded btn-icon btn-xs"><i class="icon-folder-plus"></i></a>
                                                        </div>
                                                        <div class="media-body">
                                                            <p><?php echo e($user->name); ?> updated a <?php echo e($activity->legalBodyName); ?> audio resource</p>
                                                              <p> <a title="go to instrument (<?php echo e($activity->instrumentName); ?>)" href="/edit-instrument/<?php echo e($activity->instrumentID); ?>"><?php echo e($activity->instrumentName); ?></a> | <a title="go to audio" href="/edit-instrument/<?php echo e($activity->instrumentID); ?>/resource/<?php echo e($activity->resourceID); ?>">Audio</a></p>
                                                            <div class="media-annotation"><?php echo e($activity_time_ago[$key]); ?></div>
                                                        </div>
                                                        <div class="media-right media-middle">
                                                            <ul class="icons-list">
                                                                <li>
                                                                    <a href="/edit-instrument/<?php echo e($activity->instrumentID); ?>" title="go to instrument (<?php echo e($activity->instrumentName); ?>)"><i class="icon-arrow-right13"></i></a>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </li>
                                        <?php endif; ?>
                                        <?php if($activity->activity == 'You updated a video resource'): ?>   
                                                    <!-- UPDATED VIDEO -->
                                                    <li class="media">
                                                        <div class="media-left">
                                                            <a title="go to instrument (<?php echo e($activity->instrumentName); ?>)" href="/edit-instrument/<?php echo e($activity->instrumentID); ?>" class="btn border-success text-success btn-flat btn-rounded btn-icon btn-xs"><i class="icon-folder-plus"></i></a>
                                                        </div>
                                                        <div class="media-body">
                                                            <p><?php echo e($user->name); ?> updated a <?php echo e($activity->legalBodyName); ?> video resource</p>
                                                              <p> <a title="go to instrument (<?php echo e($activity->instrumentName); ?>)" href="/edit-instrument/<?php echo e($activity->instrumentID); ?>"><?php echo e($activity->instrumentName); ?></a> | <a title="go to video" href="/edit-instrument/<?php echo e($activity->instrumentID); ?>/resource/<?php echo e($activity->resourceID); ?>">Video</a></p>
                                                            <div class="media-annotation"><?php echo e($activity_time_ago[$key]); ?></div>
                                                        </div>
                                                        <div class="media-right media-middle">
                                                            <ul class="icons-list">
                                                                <li>
                                                                    <a href="/edit-instrument/<?php echo e($activity->instrumentID); ?>" title="go to instrument (<?php echo e($activity->instrumentName); ?>)"><i class="icon-arrow-right13"></i></a>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </li>
                                        <?php endif; ?>
                                        <?php if($activity->activity == "You added a new user"): ?>   
                                                    <!-- ADDED A NEW USER -->
                                                    <li class="media">
                                                        <div class="media-left">
                                                            <a title="<?php echo e($user->name); ?> added a new user" class="btn border-warning-400 text-warning-400 btn-flat btn-rounded btn-icon btn-xs"><i class="icon-folder-check"></i></a>
                                                        </div>
                                                        <div class="media-body">
                                                            <p><?php echo e($user->name); ?> added a <a href="/existing-users/edit/<?php echo e($activity->this_userID); ?>" title="go to user">new user</a></p>
                                                            <div class="media-annotation"><?php echo e($activity_time_ago[$key]); ?></div>
                                                        </div>
                                                        <div class="media-right media-middle">
                                                            <ul class="icons-list">
                                                                <li>
                                                                    <a href="/edit-instrument/<?php echo e($activity->instrumentID); ?>" title="go to instrument (<?php echo e($activity->instrumentName); ?>)"><i class="icon-arrow-right13"></i></a>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </li>
                                        <?php endif; ?>
                                        <?php if($activity->activity == "You deleted an admin user"): ?>   
                                                    <!-- DELETED A USER -->
                                                    <li class="media">
                                                        <div class="media-left">
                                                            <a title="<?php echo e($user->name); ?> deleted an admin user (<?php echo e($activity->this_userName); ?>" class="btn border-warning-400 text-warning-400 btn-flat btn-rounded btn-icon btn-xs"><i class="icon-folder-check"></i></a>
                                                        </div>
                                                        <div class="media-body">
                                                            <p><?php echo e($user->name); ?> deleted an admin user (<?php echo e($activity->this_userName); ?>) </p>
                                                            <div class="media-annotation"><?php echo e($activity_time_ago[$key]); ?></div>
                                                        </div>
                                                        <div class="media-right media-middle">
                                                            <ul class="icons-list">
                                                                <li>
                                                                    <a href="/edit-instrument/<?php echo e($activity->instrumentID); ?>" title="go to instrument (<?php echo e($activity->instrumentName); ?>)"><i class="icon-arrow-right13"></i></a>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </li>
                                        <?php endif; ?>
                                        <?php if($activity->activity == "You updated an instrument's production event"): ?>   
                                                    <!-- UPDATED PRODUCTION EVENT -->
                                                    <li class="media">
                                                        <div class="media-left">
                                                            <a title="<?php echo e($user->name); ?> updated a production event" class="btn border-warning-400 text-warning-400 btn-flat btn-rounded btn-icon btn-xs"><i class="icon-folder-check"></i></a>
                                                        </div>
                                                        <div class="media-body">
                                                            <p><?php echo e($user->name); ?> updated a production event</p>
                                                              <p> <a href="/existing-collections/edit/<?php echo e($activity->legalBodyID); ?>"><?php echo e($activity->legalBodyName); ?></a> / <a title="go to instrument (<?php echo e($activity->instrumentName); ?>)" href="/edit-instrument/<?php echo e($activity->instrumentID); ?>"><?php echo e($activity->instrumentName); ?></a></p>
                                                            <div class="media-annotation"><?php echo e($activity_time_ago[$key]); ?></div>
                                                        </div>
                                                        <div class="media-right media-middle">
                                                            <ul class="icons-list">
                                                                <li>
                                                                    <a href="/edit-instrument/<?php echo e($activity->instrumentID); ?>" title="go to instrument (<?php echo e($activity->instrumentName); ?>)"><i class="icon-arrow-right13"></i></a>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </li>
                                        <?php endif; ?>
                                        <?php if($activity->activity == "You updated an instrument event"): ?>   
                                                    <!-- UPDATED EVENT -->
                                                    <li class="media">
                                                        <div class="media-left">
                                                            <a title="<?php echo e($user->name); ?> updated a <?php echo e($activity->eventType); ?> event" class="btn border-warning-400 text-warning-400 btn-flat btn-rounded btn-icon btn-xs"><i class="icon-folder-check"></i></a>
                                                        </div>
                                                        <div class="media-body">
                                                            <p><?php echo e($user->name); ?> updated a <?php echo e($activity->eventType); ?> event</p>
                                                              <p> <a href="/existing-collections/edit/<?php echo e($activity->legalBodyID); ?>"><?php echo e($activity->legalBodyName); ?></a> / <a title="go to instrument (<?php echo e($activity->instrumentName); ?>)" href="/edit-instrument/<?php echo e($activity->instrumentID); ?>"><?php echo e($activity->instrumentName); ?></a></p>
                                                            <div class="media-annotation"><?php echo e($activity_time_ago[$key]); ?></div>
                                                        </div>
                                                        <div class="media-right media-middle">
                                                            <ul class="icons-list">
                                                                <li>
                                                                    <a href="/edit-instrument/<?php echo e($activity->instrumentID); ?>" title="go to instrument (<?php echo e($activity->instrumentName); ?>)"><i class="icon-arrow-right13"></i></a>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </li>
                                        <?php endif; ?>
                                        <?php if($activity->activity == "You added an instrument event"): ?>   
                                                    <!-- ADDED EVENT -->
                                                    <li class="media">
                                                        <div class="media-left">
                                                            <a title="go to instrument (<?php echo e($activity->instrumentName); ?>)" href="/edit-instrument/<?php echo e($activity->instrumentID); ?>" class="btn border-success text-success btn-flat btn-rounded btn-icon btn-xs"><i class="icon-folder-plus"></i></a>
                                                        </div>
                                                        <div class="media-body">
                                                            <p><?php echo e($user->name); ?> added a <?php echo e($activity->eventType); ?> event</p>
                                                              <p> <a href="/existing-collections/edit/<?php echo e($activity->legalBodyID); ?>"><?php echo e($activity->legalBodyName); ?></a> / <a title="go to instrument (<?php echo e($activity->instrumentName); ?>)" href="/edit-instrument/<?php echo e($activity->instrumentID); ?>"><?php echo e($activity->instrumentName); ?></a></p>
                                                            <div class="media-annotation"><?php echo e($activity_time_ago[$key]); ?></div>
                                                        </div>
                                                        <div class="media-right media-middle">
                                                            <ul class="icons-list">
                                                                <li>
                                                                    <a href="/edit-instrument/<?php echo e($activity->instrumentID); ?>" title="go to instrument (<?php echo e($activity->instrumentName); ?>)"><i class="icon-arrow-right13"></i></a>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </li>
                                        <?php endif; ?>
                                        <?php if($activity->activity == "You deleted an event from an instrument"): ?>   
                                                    <!-- DELETED EVENT -->
                                                    <li class="media">
                                                        <div class="media-left">
                                                            <a title="go to instrument (<?php echo e($activity->instrumentName); ?>)" href="/edit-instrument/<?php echo e($activity->instrumentID); ?>" class="btn border-success text-success btn-flat btn-rounded btn-icon btn-xs"><i class="icon-folder-plus"></i></a>
                                                        </div>
                                                        <div class="media-body">
                                                            <p><?php echo e($user->name); ?> deleted an event</p>
                                                              <p> <a href="/existing-collections/edit/<?php echo e($activity->legalBodyID); ?>"><?php echo e($activity->legalBodyName); ?></a> / <a title="go to instrument (<?php echo e($activity->instrumentName); ?>)" href="/edit-instrument/<?php echo e($activity->instrumentID); ?>"><?php echo e($activity->instrumentName); ?></a></p>
                                                            <div class="media-annotation"><?php echo e($activity_time_ago[$key]); ?></div>
                                                        </div>
                                                        <div class="media-right media-middle">
                                                            <ul class="icons-list">
                                                                <li>
                                                                    <a href="/edit-instrument/<?php echo e($activity->instrumentID); ?>" title="go to instrument (<?php echo e($activity->instrumentName); ?>)"><i class="icon-arrow-right13"></i></a>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </li>
                                        <?php endif; ?>
                                        <?php if($activity->activity == 'You deleted an instrument'): ?>   
                                                    <!-- DELETED AN INSTRUMENT -->
                                                    <li class="media">
                                                        <div class="media-left">
                                                            <a href="#" title="<?php echo e($user->name); ?> deleted an instrument" class="btn border-pink text-pink btn-flat btn-rounded btn-icon btn-xs"><i class="icon-folder-remove"></i></a>
                                                        </div>
                                                        <div class="media-body">
                                                            <p><?php echo e($user->name); ?> deleted an instrument</p>
                                                              <p><?php echo e($activity->legalBodyName); ?> / <?php echo e($activity->instrumentName); ?></p>
                                                            <div class="media-annotation"><?php echo e($activity_time_ago[$key]); ?></div>
                                                        </div>
                                                    </li>
                                        <?php endif; ?>
                                        <?php if($activity->activity == 'You updated an instrument'): ?>   
                                                    <!-- UPDATED AN INSTRUMENT -->
                                                    <li class="media">
                                                        <div class="media-left">
                                                            <a href="/edit-instrument/<?php echo e($activity->instrumentID); ?>" title="<?php echo e($user->name); ?> updated an instrument" class="btn border-warning-400 text-warning-400 btn-flat btn-rounded btn-icon btn-xs"><i class="icon-folder-check"></i></a>
                                                        </div>
                                                        <div class="media-body">
                                                            <p><?php echo e($user->name); ?> updated an instrument</p>
                                                            <p><a href="/existing-collections/edit/<?php echo e($activity->legalBodyID); ?>"><?php echo e($activity->legalBodyName); ?></a> / <a href="/edit-instrument/<?php echo e($activity->instrumentID); ?>"><?php echo e($activity->instrumentName); ?></a></p>
                                                            <div class="media-annotation"><?php echo e($activity_time_ago[$key]); ?></div>
                                                        </div>
                                                        <div class="media-right media-middle">
                                                            <ul class="icons-list">
                                                                <li>
                                                                    <a href="#" title="go to instrument"><i class="icon-arrow-right13"></i></a>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </li>
                                        <?php endif; ?>
                                        <?php if($activity->activity == 'You updated a user'): ?>   
                                                    <!-- UPDATED A USER -->
                                                    <li class="media">
                                                        <div class="media-left">
                                                            <a href="#" title="<?php echo e($user->name); ?> updated a user" class="btn border-success text-success btn-flat btn-rounded btn-icon btn-xs"><i class="icon-user-plus"></i></a>
                                                        </div>
                                                        <div class="media-body">
                                                            <p><?php echo e($user->name); ?> updated a user</p>
                                                            <div class="media-annotation"><?php echo e($activity_time_ago[$key]); ?></div>
                                                        </div>
                                                        <div class="media-right media-middle">
                                                            <ul class="icons-list">
                                                                <li>
                                                                    <a href="#" title="go to instrument"><i class="icon-arrow-right13"></i></a>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </li>
                                        <?php endif; ?>
                                        <?php if($activity->activity == 'You deleted a user'): ?>   
                                                    <!-- DELETED A USER -->
                                                    <li class="media">
                                                        <div class="media-left">
                                                            <a href="#" title="<?php echo e($user->name); ?> deleted a user" class="btn border-pink text-pink btn-flat btn-rounded btn-icon btn-xs"><i class="icon-user-minus"></i></a>
                                                        </div>
                                                        
                                                        <div class="media-body">
                                                            <p><?php echo e($user->name); ?> deleted a user</p>
                                                            <div class="media-annotation"><?php echo e($activity_time_ago[$key]); ?></div>
                                                        </div>
                                                        <div class="media-right media-middle">
                                                            <ul class="icons-list">
                                                                <li>
                                                                    <a href="#" title="go to instrument"><i class="icon-arrow-right13"></i></a>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </li>
                                        <?php endif; ?>
                                        <?php if($activity->activity == 'You added an image to an instrument'): ?>   
                                                    <!-- ADDED IMAGE TO INSTRUMENT -->
                                                    <li class="media">
                                                        <div class="media-left">
                                                            <a href="#" title="<?php echo e($user->name); ?> added an image to an instrument" class="btn border-success text-success btn-flat btn-rounded btn-icon btn-xs"><i class="icon-folder-upload"></i></a>
                                                        </div>
                                                        <div class="media-body">
                                                            <p><?php echo e($user->name); ?> added an image to an instrument</p>
                                                            <div class="media-annotation"><?php echo e($activity_time_ago[$key]); ?></div>
                                                        </div>
                                                        <div class="media-right media-middle">
                                                            <ul class="icons-list">
                                                                <li>
                                                                    <a href="#" title="go to instrument"><i class="icon-arrow-right13"></i></a>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </li>
                                        <?php endif; ?>
                                        <?php if($activity->activity == 'You deleted an image from an instrument'): ?>   
                                                    <!-- DELETED IMAGE FROM INSTRUMENT -->
                                                    <li class="media">
                                                        <div class="media-left">
                                                            <a href="#" title="<?php echo e($user->name); ?> deleted an image from an instrument" class="btn border-pink text-pink btn-flat btn-rounded btn-icon btn-xs"><i class="icon-folder-upload"></i></a>
                                                        </div>
                                                        <div class="media-body">
                                                            <p><?php echo e($user->name); ?> deleted an image from an instrument</p>
                                                            <div class="media-annotation"><?php echo e($activity_time_ago[$key]); ?></div>
                                                        </div>
                                                        <div class="media-right media-middle">
                                                            <ul class="icons-list">
                                                                <li>
                                                                    <a href="#" title="go to instrument"><i class="icon-arrow-right13"></i></a>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </li>
                                        <?php endif; ?>
                                        <?php if(strlen(strstr($activity->activity,'You added a new collection'))>0): ?>   
                                                    <!-- ADDED A COLLECTION -->
                                                    <li class="media">
                                                        <div class="media-left">
                                                            <a title="go to collection (<?php echo e($activity->legalBodyName); ?>)" href="/existing-collections/edit/<?php echo e($activity->legalBodyID); ?>" class="btn border-success text-success btn-flat btn-rounded btn-icon btn-xs"><i class="icon-folder-plus4"></i></a>
                                                        </div>
                                                        <div class="media-body">
                                                            <p><?php echo e($user->name); ?> added a new collection</p>
                                                              <p><a title="go to collection (<?php echo e($activity->legalBodyName); ?>)" href="/existing-collections/edit/<?php echo e($activity->legalBodyID); ?>"><?php echo e($activity->legalBodyName); ?></a></p>
                                                            <div class="media-annotation"><?php echo e($activity_time_ago[$key]); ?></div>
                                                        </div>
                                                        <div class="media-right media-middle">
                                                            <ul class="icons-list">
                                                                <li>
                                                                    <a href="/existing-collections/edit/<?php echo e($activity->legalBodyID); ?>" title="go to collection"><i class="icon-arrow-right13"></i></a>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </li>
                                        <?php endif; ?>
                                        <?php if($activity->activity == 'You updated a collection'): ?>   
                                                    <!-- UPDATED A COLLECTION -->
                                                    <li class="media">
                                                        <div class="media-left">
                                                            <a title="<?php echo e($user->name); ?> updated a collection: <?php echo e($activity->legalBodyName); ?>" href="/existing-collections/edit/<?php echo e($activity->legalBodyID); ?>" class="btn border-warning-400 text-warning-400 btn-flat btn-rounded btn-icon btn-xs"><i class="icon-folder-upload3"></i></a>
                                                        </div>
                                                        <div class="media-body">
                                                            <p><?php echo e($user->name); ?> updated a collection</p>
                                                  <?php if($role != "Admin"): ?>
                                                           
                                                            <p><a title="<?php echo e($activity->activity); ?>: <?php echo e($activity->legalBodyName); ?>" href="/existing-collections/edit/<?php echo e($activity->legalBodyID); ?>"><?php echo e($activity->legalBodyName); ?></a></p>
                                                  <?php endif; ?>
                                                  <?php if($role == "Admin"): ?>
                                                            <p><a title="<?php echo e($activity->activity); ?>: <?php echo e($activity->legalBodyName); ?>" href="/legalbody-profile"><?php echo e($activity->legalBodyName); ?></a></p>
                                                  <?php endif; ?>
                                                            <div class="media-annotation"><?php echo e($activity_time_ago[$key]); ?></div>
                                                        </div>
                                                        <div class="media-right media-middle">
                                                            <ul class="icons-list">
                                                                <li>
                                                                    <a title="<?php echo e($activity->activity); ?>: <?php echo e($activity->legalBodyName); ?>" href="/existing-collections/edit/<?php echo e($activity->legalBodyID); ?>"><i class="icon-arrow-right13"></i></a>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </li>
                                        <?php endif; ?>
                                        <?php if($activity->activity == 'You deleted a collection'): ?>   
                                                    <!-- DELETED A COLLECTION -->
                                                    <li class="media">
                                                        <div class="media-left">
                                                            <a title="<?php echo e($user->name); ?> deleted a collection" class="btn border-pink text-pink btn-flat btn-rounded btn-icon btn-xs"><i class="icon-folder-minus4"></i></a>
                                                        </div>
                                                        <div class="media-body">
                                                            <p><?php echo e($user->name); ?> deleted a collection</p>
                                                            <div class="media-annotation"><?php echo e($activity_time_ago[$key]); ?></div>
                                                        </div>
                                                        <div class="media-right media-middle">
                                                            <ul class="icons-list">
                                          
                                                            </ul>
                                                        </div>
                                                    </li>
                                        <?php endif; ?>
                                        <?php if($activity->activity == 'You exported instruments'): ?>   
                                                    <!-- EXPORTED INSTRUMENTS -->
                                                    <li class="media">
                                                        <div class="media-left">
                                                            <a href="#" title="<?php echo e($user->name); ?> exported instruments" class="btn border-primary text-primary btn-flat btn-rounded btn-icon btn-xs"><i class="icon-database-export"></i></a>
                                                        </div>
                                                        <div class="media-body">
                                                            <p><?php echo e($user->name); ?> exported instruments</p>
                                                            <div class="media-annotation"><?php echo e($activity_time_ago[$key]); ?></div>
                                                        </div>
                                                        <div class="media-right media-middle">
                                                            <ul class="icons-list">
                                                                <li>
                                                                    <a href="#" title="go to instrument"><i class="icon-arrow-right13"></i></a>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </li>
                                        <?php endif; ?>
                        <?php endforeach; ?>   
                                    </ul>
                                </div>
                            </div>
                            <!-- /Recent Activity -->      

                                </div>
                        </div>
                    </form>
       <?php endforeach; ?>
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('layouts.loggedin', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>