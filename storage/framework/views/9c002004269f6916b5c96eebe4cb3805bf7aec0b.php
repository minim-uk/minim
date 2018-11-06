<?php $__env->startSection('title'); ?>
Dashboard
<?php $__env->stopSection(); ?>



<?php $__env->startSection('breadcrumbs'); ?>
    <li class="active"><i class="icon-home2 position-left"></i>Dashboard</li>
<?php $__env->stopSection(); ?>



<?php $__env->startSection('content'); ?>    

<!--
learn vr
https://livierickson.com/blog/6-questions-to-ask-before-diving-into-vr-development/
https://www.quora.com/I-want-to-be-a-virtual-reality-developer-From-where-can-I-start-What-are-the-best-learning-materials
-->

                <!-- Content area -->
                <div class="content">

                    <!-- Dashboard content -->
                    <div class="row">
                        <div class="col-lg-8">

                            <!-- <div class="panel panel-flat" style="min-height: 254px;"> -->
                            
                            <div class="panel panel-flat" style="min-height: 114px;">

                            <!-- welcome back -->    
                            <p style="font-size:21px; padding-top:20px;padding-left:30px;padding-right:20px;">Welcome back, <?php echo e(Auth::user()->name); ?>.</p>
                            <?php /*  <p style="padding-top:6px;padding-left:30px;padding-right:20px;">You last logged in on <?php echo e(Session::get('last_login')); ?>. If in doubt, <a href="#">change password</a>.</p> */ ?>

                            <p style="padding-top:6px;padding-left:30px;padding-right:20px;">You'll be logged out after 45 minutes of inactivity. Please do not use multiple windows.</p>
                            <?php /*  <p>Compatible Browsers: IE9, IE10, IE11, Firefox, Safari,Opera, Chrome.</p> */ ?>

<?php /* 
                            <!-- Quick stats boxes -->                   
                            <div style="padding:20px;">
                                    <div class="col-lg-4">
                                         <!-- Total Instruments -->
                                        <div class="panel bg-teal-400">
                                            <div class="panel-body">
                                                <div class="heading-elements">
                                                    <span class="heading-text badge bg-teal-800">+100%</span>
                                                </div>
                                                <h3 class="no-margin">853</h3>
                                                Total instruments
                                                <div class="text-muted text-size-small">853 in last month</div>
                                            </div>
                                            <div class="container-fluid">
                                                <div id="members-online"></div>
                                            </div>
                                        </div>
                                        <!-- /Total Instrumentse -->
                                    </div>
                                    <div class="col-lg-4">
                                       <!-- Instruments Status -->
                                        <div class="panel bg-pink-400">
                                            <div class="panel-body">
                                                <div class="heading-elements">
                                                    <ul class="icons-list">
                                                        <li><a data-action="reload"></a></li>
                                                    </ul>
                                                </div>
                                                <h3 class="no-margin">12.4%</h3>
                                                with missing data
                                                <div class="text-muted text-size-small">106 in last month</div>
                                            </div>

                                            <div id="server-load"></div>
                                        </div>
                                        <!-- /Instruments Status -->
                                    </div>
                                    <div class="col-lg-4">
                                        <!-- Collections -->
                                        <div class="panel bg-blue-400">
                                            <div class="panel-body">
                                                <div class="heading-elements">
                                                    <ul class="icons-list">
                                                        <li><a data-action="reload"></a></li>
                                                    </ul>
                                                </div>
                                                <h3 class="no-margin">2</h3>
                                                Collections
                                                <div class="text-muted text-size-small">2 new in last month</div>
                                            </div>
                                            <div id="today-revenue"></div>
                                        </div>
                                        <!-- /Collections -->
                                    </div>
                                </div>
                               <!-- /quick stats boxes -->
*/ ?>


                           </div>
                           <!-- close panel -->



                       </div>
                        <div class="col-lg-4">
                            <!-- Recent Activity -->
                            <div class="panel panel-flat">
                                <div class="panel-heading">
                                    <h6 class="panel-title">Your Recent Activity</h6>
                                </div>
                                <div class="panel-body">
                                    <div class="content-group-xs" id="bullets"></div>
                                    <?php if(count($admin_user_activity) < 1): ?>
                                        <?php /* This user has no activity*/ ?>
                                        <p style="margin-top:-20px;">You currently have no activity.</p>
                                    <?php endif; ?>
                                    <ul class="media-list">
                                    <?php foreach($admin_user_activity as $key => $activity): ?>
                                       <?php if($activity->type == 'admin_import'): ?>    
                                         <!-- IMPORTED -->
                                            <li class="media">
                                                <div class="media-left">
                                                    <a title="<?php echo e($activity->activity); ?>" class="btn border-teal-400 text-teal btn-flat btn-rounded btn-icon btn-xs"><i class="icon-user-lock"></i></a>
                                                </div>
                                                <div class="media-body">
                                                    <p>You <?php echo e($activity->activity); ?> to <a href="/existing-collections/edit/<?php echo e($activity->legalBodyID); ?>"><?php echo e($activity->legalBodyName); ?></a></p>
                                                    <div class="media-annotation"><?php echo e($activity_time_ago[$key]); ?></div>
                                                </div>
                                                <div class="media-right media-middle">
                                                    <ul class="icons-list">
                                                        <li>
                                                            <a href="/existing-collections/edit/<?php echo e($activity->legalBodyID); ?>"><i class="icon-arrow-right13"></i></a>
                                                        </li>
                                                    </ul>
                                                </div>
                                             </li>    
                                       <?php endif; ?>
                                       <?php if($activity->activity == 'You changed your password'): ?>        
                                            <!-- CHANGED PASSWORD -->
                                            <li class="media">
                                                <div class="media-left">
                                                    <a title="<?php echo e($activity->activity); ?>" class="btn border-teal-400 text-teal btn-flat btn-rounded btn-icon btn-xs"><i class="icon-user-lock"></i></a>
                                                </div>
                                                <div class="media-body">
                                                    <p><?php echo e($activity->activity); ?></p>
                                                    <div class="media-annotation"><?php echo e($activity_time_ago[$key]); ?></div>
                                                </div>
                                             </li>
                                        <?php endif; ?>
                                        <?php if($activity->activity == 'You updated your account details'): ?>        
                                                    <!-- UPDATED ACCOUNT DETAILS -->
                                                    <li class="media">
                                                        <div class="media-left">
                                                            <a title="<?php echo e($activity->activity); ?>" class="btn border-warning-400 text-warning-400 btn-flat btn-rounded btn-icon btn-xs"><i class="icon-folder-check"></i></a>
                                                        </div>
                                                        <div class="media-body">
                                                            <p><?php echo e($activity->activity); ?></p>
                                                            <div class="media-annotation"><?php echo e($activity_time_ago[$key]); ?></div>
                                                        </div>
                                                    </li>
                                        <?php endif; ?>
                                        <?php if($activity->activity == 'You logged in'): ?>        
                                                    <!-- LOGGED IN -->
                                                    <li class="media">
                                                        <div class="media-left">
                                                            <a title="<?php echo e($activity->activity); ?>" class="btn border-teal-400 text-teal btn-flat btn-rounded btn-icon btn-xs"><i class="icon-user-plus"></i></a>
                                                        </div>
                                                        <div class="media-body">
                                                            <p><?php echo e($activity->activity); ?></p>
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
                                                            <a title="<?php echo e($activity->activity); ?>" class="btn border-primary text-primary btn-flat btn-rounded btn-icon btn-xs"><i class="icon-user-minus"></i></a>
                                                        </div>
                                                        <div class="media-body">
                                                            <p><?php echo e($activity->activity); ?></p>
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
                                                    <!-- ADDED INSTRUMENT -->
                                                    <li class="media">
                                                        <div class="media-left">
                                                            <a title="go to instrument (<?php echo e($activity->instrumentName); ?>)" href="/edit-instrument/<?php echo e($activity->instrumentID); ?>" class="btn border-success text-success btn-flat btn-rounded btn-icon btn-xs"><i class="icon-folder-plus"></i></a>
                                                        </div>
                                                        <div class="media-body">
                                                            <p>You created an instrument</p>
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
                                                            <p>You added an actor to an instrument's event</p>
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
                                                    <!-- ADDED AN IMAGE -->
                                                    <li class="media">
                                                        <div class="media-left">
                                                            <a title="go to instrument (<?php echo e($activity->instrumentName); ?>)" href="/edit-instrument/<?php echo e($activity->instrumentID); ?>" class="btn border-success text-success btn-flat btn-rounded btn-icon btn-xs"><i class="icon-folder-plus"></i></a>
                                                        </div>
                                                        <div class="media-body">
                                                            <p>You added an image to <?php echo e($activity->legalBodyName); ?>'s</p>
                                                              <p> <a title="go to instrument (<?php echo e($activity->instrumentName); ?>)" href="/edit-instrument/<?php echo e($activity->instrumentID); ?>"><?php echo e($activity->instrumentName); ?></a> | <a title="go to image" href="/edit-instrument/<?php echo e($activity->instrumentID); ?>/resource/<?php echo e($activity->resourceID); ?>"><?php echo e($activity->resourceName); ?></a></p>
                                                            <div class="media-annotation"><?php echo e($activity_time_ago[$key]); ?></div>
                                                        </div>
                                                        <div class="media-right media-middle">
                                                            <ul class="icons-list">
                                                                <li>
                                                                    <a href="/edit-instrument/<?php echo e($activity->instrumentID); ?>/resource/<?php echo e($activity->resourceID); ?>" title="go to image"><i class="icon-arrow-right13"></i></a>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </li>
                                        <?php endif; ?>
                                        <?php if($activity->activity == 'You added a video'): ?>   
                                                    <!-- ADDED A VIDEO -->
                                                    <li class="media">
                                                        <div class="media-left">
                                                            <a title="go to instrument (<?php echo e($activity->instrumentName); ?>)" href="/edit-instrument/<?php echo e($activity->instrumentID); ?>" class="btn border-success text-success btn-flat btn-rounded btn-icon btn-xs"><i class="icon-folder-plus"></i></a>
                                                        </div>
                                                        <div class="media-body">
                                                            <p>You added a video to <?php echo e($activity->legalBodyName); ?>'s</p>
                                                              <p> <a title="go to instrument (<?php echo e($activity->instrumentName); ?>)" href="/edit-instrument/<?php echo e($activity->instrumentID); ?>"><?php echo e($activity->instrumentName); ?></a> | <a title="go to video" href="/edit-instrument/<?php echo e($activity->instrumentID); ?>/resource/<?php echo e($activity->resourceID); ?>"><?php echo e($activity->resourceName); ?></a></p>
                                                            <div class="media-annotation"><?php echo e($activity_time_ago[$key]); ?></div>
                                                        </div>
                                                        <div class="media-right media-middle">
                                                            <ul class="icons-list">
                                                                <li>
                                                                    <a href="/edit-instrument/<?php echo e($activity->instrumentID); ?>/resource/<?php echo e($activity->resourceID); ?>" title="go to video"><i class="icon-arrow-right13"></i></a>
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
                                                            <p>You added audio to <?php echo e($activity->legalBodyName); ?>'s</p>
                                                              <p> <a title="go to instrument (<?php echo e($activity->instrumentName); ?>)" href="/edit-instrument/<?php echo e($activity->instrumentID); ?>"><?php echo e($activity->instrumentName); ?></a> | <a title="go to audio" href="/edit-instrument/<?php echo e($activity->instrumentID); ?>/resource/<?php echo e($activity->resourceID); ?>"><?php echo e($activity->resourceName); ?></a></p>
                                                            <div class="media-annotation"><?php echo e($activity_time_ago[$key]); ?></div>
                                                        </div>
                                                        <div class="media-right media-middle">
                                                            <ul class="icons-list">
                                                                <li>
                                                                    <a href="/edit-instrument/<?php echo e($activity->instrumentID); ?>/resource/<?php echo e($activity->resourceID); ?>" title="go to audio"><i class="icon-arrow-right13"></i></a>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </li>
                                        <?php endif; ?>
                                        <?php if($activity->activity == 'You deleted a video'): ?>   
                                                    <!-- DELETED A VIDEO-->
                                                    <li class="media">
                                                        <div class="media-left">
                                                            <a title="go to instrument (<?php echo e($activity->instrumentName); ?>)" href="/edit-instrument/<?php echo e($activity->instrumentID); ?>" class="btn border-success text-success btn-flat btn-rounded btn-icon btn-xs"><i class="icon-folder-plus"></i></a>
                                                        </div>
                                                        <div class="media-body">
                                                            <p>You deleted a video from <?php echo e($activity->legalBodyName); ?>'s <?php echo e($activity->instrumentName); ?></p>
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
                                                    <!-- DELETED AN IMAGE-->
                                                    <li class="media">
                                                        <div class="media-left">
                                                            <a title="go to instrument (<?php echo e($activity->instrumentName); ?>)" href="/edit-instrument/<?php echo e($activity->instrumentID); ?>" class="btn border-success text-success btn-flat btn-rounded btn-icon btn-xs"><i class="icon-folder-plus"></i></a>
                                                        </div>
                                                        <div class="media-body">
                                                            <p>You deleted an image from <?php echo e($activity->legalBodyName); ?>'s <?php echo e($activity->instrumentName); ?></p>
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
                                                            <p>You deleted audio from <?php echo e($activity->legalBodyName); ?>'s <?php echo e($activity->instrumentName); ?></p>
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
                                                    <!-- UPDATED AN IMAGE -->
                                                    <li class="media">
                                                        <div class="media-left">
                                                            <a title="go to instrument (<?php echo e($activity->instrumentName); ?>)" href="/edit-instrument/<?php echo e($activity->instrumentID); ?>" class="btn border-success text-success btn-flat btn-rounded btn-icon btn-xs"><i class="icon-folder-plus"></i></a>
                                                        </div>
                                                        <div class="media-body">
                                                            <p>You updated a <?php echo e($activity->legalBodyName); ?> image resource</p>
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
                                                            <p>You updated a <?php echo e($activity->legalBodyName); ?> audio resource</p>
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
                                                            <p>You updated a <?php echo e($activity->legalBodyName); ?> video resource</p>
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
                                                            <a title="<?php echo e($activity->activity); ?>" class="btn border-warning-400 text-warning-400 btn-flat btn-rounded btn-icon btn-xs"><i class="icon-folder-check"></i></a>
                                                        </div>
                                                        <div class="media-body">
                                                            <p>You added a <a href="/existing-users/edit/<?php echo e($activity->this_userID); ?>" title="go to user">new user</a></p>
                                                            <div class="media-annotation"><?php echo e($activity_time_ago[$key]); ?></div>
                                                        </div>

                                                        <div class="media-right media-middle">
                                                            <ul class="icons-list">
                                                                <li>
                                                                    <a href="/existing-users/edit/<?php echo e($activity->this_userID); ?>"><i class="icon-arrow-right13"></i></a>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </li>
                                        <?php endif; ?>
                                        <?php if($activity->activity == "You deleted an admin user"): ?>   
                                                    <!-- DELETED A USER -->
                                                    <li class="media">
                                                        <div class="media-left">
                                                            <a title="<?php echo e($activity->activity); ?>" class="btn border-warning-400 text-warning-400 btn-flat btn-rounded btn-icon btn-xs"><i class="icon-folder-check"></i></a>
                                                        </div>
                                                        <div class="media-body">
                                                            <p>You deleted an admin user (<?php echo e($activity->this_userName); ?>) </p>
                                                            <div class="media-annotation"><?php echo e($activity_time_ago[$key]); ?></div>
                                                        </div>
                                                    </li>
                                        <?php endif; ?>
                                        <?php if($activity->activity == "You updated an instrument's production event"): ?>   
                                                    <!-- UPDATED PRODUCTION EVENT -->
                                                    <li class="media">
                                                        <div class="media-left">
                                                            <a title="<?php echo e($activity->activity); ?>" class="btn border-warning-400 text-warning-400 btn-flat btn-rounded btn-icon btn-xs"><i class="icon-folder-check"></i></a>
                                                        </div>
                                                        <div class="media-body">
                                                            <p>You updated a production event</p>
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
                                                            <a title="<?php echo e($activity->activity); ?>" class="btn border-warning-400 text-warning-400 btn-flat btn-rounded btn-icon btn-xs"><i class="icon-folder-check"></i></a>
                                                        </div>
                                                        <div class="media-body">
                                                            <p><?php echo e($activity->eventType); ?> event updated</p>
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
                                                            <p><?php echo e($activity->eventType); ?> event addded</p>
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
                                                            <p>You deleted an event</p>
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
                                                    <!-- DELETED INSTRUMENT -->
                                                    <li class="media">
                                                        <div class="media-left">
                                                            <a href="#" title="<?php echo e($activity->activity); ?>" class="btn border-pink text-pink btn-flat btn-rounded btn-icon btn-xs"><i class="icon-folder-remove"></i></a>
                                                        </div>
                                                        <div class="media-body">
                                                            <p><?php echo e($activity->activity); ?></p>
                                                              <p><?php echo e($activity->legalBodyName); ?> / <?php echo e($activity->instrumentName); ?></p>
                                                            <div class="media-annotation"><?php echo e($activity_time_ago[$key]); ?></div>
                                                        </div>
                                                    </li>
                                        <?php endif; ?>
                                        <?php if($activity->activity == 'You updated an instrument'): ?>   
                                                    <!-- UPDATED INSTRUMENT -->
                                                    <li class="media">
                                                        <div class="media-left">
                                                            <a href="/edit-instrument/<?php echo e($activity->instrumentID); ?>" title="<?php echo e($activity->activity); ?>" class="btn border-warning-400 text-warning-400 btn-flat btn-rounded btn-icon btn-xs"><i class="icon-folder-check"></i></a>
                                                        </div>
                                                        <div class="media-body">
                                                            <p><?php echo e($activity->activity); ?></p>
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
                                                    <!-- UPDATED USER -->
                                                    <li class="media">
                                                        <div class="media-left">
                                                            <a href="#" title="<?php echo e($activity->activity); ?>" class="btn border-success text-success btn-flat btn-rounded btn-icon btn-xs"><i class="icon-user-plus"></i></a>
                                                        </div>
                                                        <div class="media-body">
                                                            <p><?php echo e($activity->activity); ?></p>
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
                                                    <!-- DELETED USER -->
                                                    <li class="media">
                                                        <div class="media-left">
                                                            <a href="#" title="<?php echo e($activity->activity); ?>" class="btn border-pink text-pink btn-flat btn-rounded btn-icon btn-xs"><i class="icon-user-minus"></i></a>
                                                        </div>
                                                        <div class="media-body">
                                                            <p><?php echo e($activity->activity); ?></p>
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
                                                            <a href="#" title="<?php echo e($activity->activity); ?>" class="btn border-success text-success btn-flat btn-rounded btn-icon btn-xs"><i class="icon-folder-upload"></i></a>
                                                        </div>
                                                        <div class="media-body">
                                                            <p><?php echo e($activity->activity); ?></p>
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
                                                            <a href="#" title="<?php echo e($activity->activity); ?>" class="btn border-pink text-pink btn-flat btn-rounded btn-icon btn-xs"><i class="icon-folder-upload"></i></a>
                                                        </div>
                                                        <div class="media-body">
                                                            <p><?php echo e($activity->activity); ?></p>
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
                                                    <!-- ADDED COLLECTION -->
                                                    <li class="media">
                                                        <div class="media-left">
                                                            <a title="go to collection (<?php echo e($activity->legalBodyName); ?>)" href="existing-collections/edit/<?php echo e($activity->legalBodyID); ?>" class="btn border-success text-success btn-flat btn-rounded btn-icon btn-xs"><i class="icon-folder-plus4"></i></a>
                                                        </div>
                                                        <div class="media-body">
                                                            <p><?php echo e($activity->activity); ?></p>
                                                              <p><a title="go to collection (<?php echo e($activity->legalBodyName); ?>)" href="existing-collections/edit/<?php echo e($activity->legalBodyID); ?>"><?php echo e($activity->legalBodyName); ?></a></p>
                                                            <div class="media-annotation"><?php echo e($activity_time_ago[$key]); ?></div>
                                                        </div>
                                                        <div class="media-right media-middle">
                                                            <ul class="icons-list">
                                                                <li>
                                                                    <a href="existing-collections/edit/<?php echo e($activity->legalBodyID); ?>" title="go to collection"><i class="icon-arrow-right13"></i></a>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </li>
                                        <?php endif; ?>
                                        <?php if($activity->activity == 'You updated a collection'): ?>   
                                                    <!-- UPDATED A COLLECTION -->
                                                    <li class="media">
                                                        <div class="media-left">
                                                            <a title="<?php echo e($activity->activity); ?>: <?php echo e($activity->legalBodyName); ?>" href="/existing-collections/edit/<?php echo e($activity->legalBodyID); ?>" class="btn border-warning-400 text-warning-400 btn-flat btn-rounded btn-icon btn-xs"><i class="icon-folder-upload3"></i></a>
                                                        </div>
                                                        <div class="media-body">
                                                            <p><?php echo e($activity->activity); ?></p>
                                                            
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
                                                            <a title="<?php echo e($activity->activity); ?>" class="btn border-pink text-pink btn-flat btn-rounded btn-icon btn-xs"><i class="icon-folder-minus4"></i></a>
                                                        </div>
                                                        <div class="media-body">
                                                            <p><?php echo e($activity->activity); ?></p>
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
                                                            <a href="#" title="<?php echo e($activity->activity); ?>" class="btn border-primary text-primary btn-flat btn-rounded btn-icon btn-xs"><i class="icon-database-export"></i></a>
                                                        </div>
                                                        <div class="media-body">
                                                            <p><?php echo e($activity->activity); ?></p>
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
                        </div><!-- end righthand side container -->
                    </div>
                  </div><!-- /dashboard content -->
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('layouts.loggedin', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>