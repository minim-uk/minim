 <style>
a.delete {
    color:red;
}
a.delete:hover {
    color:red;
    font-weight: bold;
}
</style>


<script>
$('body').on( 'click', 'button.preview', function () {
                $('#modal_remote').on('show.bs.modal', function(instrumentID) {
                    $(this).find('.modal-body').load('/preview-instrument/' + <?php echo e($instrumentID); ?>, function() {
                });
            });
           $('#modal_remote').modal('show');
    });
</script>

                    <!-- Resources For This Instrument -->
                    <div class="col-lg-4">





               <!-- Instrument Status -->
               <div class="panel panel-flat">
                <div class="panel-heading">
                    <h6><a href="/edit-instrument/<?php echo e($instrumentID); ?>">

                    <?php if($page == "editinstrument"): ?>  
                      <strong>
                    <?php endif; ?>

                    <?php echo e(ucfirst(trans($titleSingle))); ?>


                    <?php if($page == "editinstrument"): ?>  
                      </strong>
                    <?php endif; ?>

                    </a> | Status</h6>

                    <?php if(sizeof($rights) > 0): ?>
                       <p>&#149;&nbsp;This instrument has <?php echo e(sizeof($rights)); ?> set(s) of rights information. <a href="/edit-instrument/<?php echo e($instrumentID); ?>/rights">

                         <?php if($page == "instrumentrights"): ?>
                         <strong> 
                         <?php endif; ?>  

                           Manage Rights Information

                         <?php if($page == "instrumentrights"): ?>
                         </strong>
                         <?php endif; ?>  

                       </a>.</p>
                    <?php else: ?>   
                      <p>&#149;&nbsp;This instrument has no rights information. <a href="/edit-instrument/<?php echo e($instrumentID); ?>/rights">

                      <?php if($page == "instrumentrights"): ?>  
                      <strong>   
                      <?php endif; ?>

                          Add Rights Information

                      <?php if($page == "instrumentrights"): ?>  
                      </strong>   
                      <?php endif; ?>

                      </a>.</p>
                    <?php endif; ?>  



                </div>

                <div class="panel-body">
                    <div class="content-group-xs" id="bullets"></div>

                    <ul class="media-list">
                        <li class="media">

                            <div class="media-body">


                                  <!-- <p style="margin-top:-30px;"><?php echo e($status); ?></p> -->


<?php if($insimage != "none"): ?>

  <p style="margin-top: -30px;">&#149; This instrument has at least one image, and can be <a href="#">put live</a>.</p>

<?php endif; ?>

<?php if($insimage == "none"): ?>

  <p style="margin-top: -30px;">&#149; This instrument can not be put live yet, as it does not have at least one image.</p>

<?php endif; ?>



                                  <?php if($page != "editresource"): ?>  
                                  <button class='preview'>Preview Instrument</button>
                                  <button class='delete' onclick="location.href='/delete-instrument/<?php echo e($instrumentID); ?>';">Delete It</button>  
                                  <?php endif; ?>

                             </div>      

                           </ul> 
                       </div>
                   </div>
                   <!-- /Instrument Status -->







  <?php if(isset($resource->resourceType)): ?>                               

                        <div class="panel panel-flat">
                            <div class="panel-heading">
                                <h6 class="panel-title">Rights Information For This Resource</h6>
                            </div>
                            <div class="panel-body">
                                <ul class="media-list">
                                    <li class="media">
                                        <div class="media-left media-middle">

                                           <?php if($insimage == "none"): ?>

                                           <a class="btn border-warning-400 text-warning-400 btn-flat btn-rounded btn-icon btn-xs" href="/edit-instrument/<?php echo e($instrumentID); ?>/images" title="Images for this instrument">
                                            <i class="icon-warning22"></i>
                                        </a>

                                        <?php endif; ?>

                                        <?php if($insimage != "none"): ?>

                                        <a href="/edit-instrument/<?php echo e($instrumentID); ?>/images" title="Manage images for <?php echo e($legalBodyName); ?> / <?php echo e($titlePreferred); ?>">
                                            <img src="/instrument_resources/images/<?php echo e($legalBodyMDAcode); ?>/thumbnails/<?php echo e($insimage); ?>" class="img-circle img-xs" alt="">
                                        </a>

                                        <?php endif; ?>
                                    </a>
                                </div>
                                <div class="media-body">
                                
                                    <?php if(sizeof($resourceRights) > 0): ?>
                                       <p>&#149;&nbsp;This <?php echo e($resource->resourceType); ?> has <?php echo e(sizeof($resourceRights)); ?> set(s) of rights information. <a href="/edit-instrument/<?php echo e($instrumentID); ?>/resource/<?php echo e($resource->resourceID); ?>/rights"></p><p style="margin-top:10px;">

                                         <?php if($page == "resourcerights"): ?>
                                         <strong>
                                         <?php endif; ?>

                                            Manage Rights information

                                         <?php if($page == "resourcerights"): ?>
                                         </strong>
                                         <?php endif; ?>
                                      </a>.</p>
                                    

                                    <?php else: ?>   
                                      <p>&#149;&nbsp;This <?php echo e($resource->resourceType); ?> has no rights information. <a href="/edit-instrument/<?php echo e($instrumentID); ?>/resource/<?php echo e($resource->resourceID); ?>/rights"></p><p style="margin-top:10px;">

                                         <?php if($page == "resourcerights"): ?>
                                         <strong>
                                         <?php endif; ?>  

                                           Add Rights Information

                                         <?php if($page == "resourcerights"): ?>
                                         </strong>
                                         <?php endif; ?>  

                                      </a>.</p>
                                    <?php endif; ?>  
                                            
                               </div>
                           </li>
                       </ul>         
                   </div>
               </div>
               <!-- /Rights For This Resource -->

<?php endif; ?> 





                        <div class="panel panel-flat">
                            <div class="panel-heading">
                                <h6 class="panel-title">Resources For This Instrument</h6>
                            </div>
                            <div class="panel-body">
                                <ul class="media-list">
                                    <li class="media">
                                        <div class="media-left media-middle">

                                           <?php if($insimage == "none"): ?>

                                           <a class="btn border-warning-400 text-warning-400 btn-flat btn-rounded btn-icon btn-xs" href="/edit-instrument/<?php echo e($instrumentID); ?>/images" title="Images for this instrument">
                                            <i class="icon-warning22"></i>
                                        </a>

                                        <?php endif; ?>

                                        <?php if($insimage != "none"): ?>

                                        <a href="/edit-instrument/<?php echo e($instrumentID); ?>/images" title="Manage images for <?php echo e($legalBodyName); ?> / <?php echo e($titlePreferred); ?>">
                                            <img src="/instrument_resources/images/<?php echo e($legalBodyMDAcode); ?>/thumbnails/<?php echo e($insimage); ?>" class="img-circle img-xs" alt="">
                                        </a>

                                        <?php endif; ?>
                                    </a>
                                </div>
                                <div class="media-body">
                                    <p>
                                       <?php if($page == "editimages"): ?>  
                                       <strong>
                                           <?php endif; ?>
                                           <a title="Manage images for <?php echo e($legalBodyName); ?> / <?php echo e($titlePreferred); ?>" href="/edit-instrument/<?php echo e($instrumentID); ?>/images"><span style="font-size:17px;"><?php echo e($imageCount); ?></span></a> images, 
                                           <?php if($page == "editimages"): ?>  
                                       </strong>
                                       <?php endif; ?>

                                       <?php if($page == "editaudio"): ?>  
                                       <strong>
                                           <?php endif; ?>
                                           <a title="Manage audio tracks for <?php echo e($legalBodyName); ?> / <?php echo e($titlePreferred); ?>" href="/edit-instrument/<?php echo e($instrumentID); ?>/audio"><span style="font-size:17px;"><?php echo e($audioCount); ?></span></a> audio tracks, 
                                           <?php if($page == "editaudio"): ?>  
                                       </strong>
                                       <?php endif; ?>

                                       <?php if($page == "editvideo"): ?>  
                                       <strong>
                                           <?php endif; ?>
                                           <a href="/edit-instrument/<?php echo e($instrumentID); ?>/video" title="Manage video for <?php echo e($legalBodyName); ?> / <?php echo e($titlePreferred); ?>"><span style="font-size:17px;"><?php echo e($videoCount); ?></span></a> videos.
                                           <?php if($page == "editvideo"): ?>  
                                       </strong>
                                       <?php endif; ?>

                                   </p> 
                               </div>
                               <?php if($page == "addresource"): ?>  
                               <strong>
                                   <?php endif; ?>

                                   <p style="margin-left: 54px;"><a title="Add resource to <?php echo e($legalBodyName); ?> / <?php echo e($titlePreferred); ?>" href="/edit-instrument/<?php echo e($instrumentID); ?>/add-resource">Add Resource</a></p>

                                   <?php if($page == "addresource"): ?>  
                               </strong>
                               <?php endif; ?>

                           </li>
                       </ul>         
                   </div>
               </div>
               <!-- /Resources For This Instrument -->








               <!-- Events For This Instrument -->
               <div class="panel panel-flat">
                <div class="panel-heading">
                    <h6 class="panel-title">Events For This Instrument</h6>
                </div>

                <div class="panel-body">
                    <div class="content-group-xs" id="bullets"></div>

                    <ul class="media-list">
                        <li class="media">

                            <div class="media-body">

                                <p style="margin-top: -20px;">

                                   <?php if($page == "productionevent" || $eventType == "Production"): ?>  
                                   <strong>
                                   <?php endif; ?>

                                       <a title="Manage production event for <?php echo e($legalBodyName); ?> / <?php echo e($titlePreferred); ?>" href="/production-event/<?php echo e($instrumentID); ?>"><span style="font-size:17px;">Production</span></a> <span>

                                      <span style="font-weight: normal;">[ 
                                       <?php if($page == "eventmaterials" && $eventType == "Production"): ?>  
                                       <strong>
                                       <?php endif; ?>

                                       <a href="/production-event/<?php echo e($instrumentID); ?>/materials">materials</a>

                                       <?php if($page == "eventmaterials" && $eventType == "Production"): ?>  
                                       </strong>
                                       <?php endif; ?>
                                      ]</span>

                                   <?php if($page == "productionevent" || $eventType == "Production"): ?>  
                                   </strong>
                                   <?php endif; ?>

                                   <?php if(sizeof($prod_event_actors) ==0): ?>
                                   <p style="margin-left:30px;">There are no actors</p>
                                   <?php endif; ?>

                                   <?php foreach($prod_event_actors as $key => $prodactor): ?>  
                                   <p style="margin-left:30px;">


                                  <?php if($prodactor->eventActorID == $actorID): ?>
                                  <strong>
                                  <?php endif; ?>

                                   <a href="/production-event/<?php echo e($instrumentID); ?>/actor/<?php echo e($prodactor->eventActorID); ?>"><?php echo e($prodactor->eventActorName); ?></a>


                                  <?php if($prodactor->eventActorID == $actorID): ?>
                                  </strong>
                                  <?php endif; ?>

                                  

                     | <a class="delete" href="/production-event/delete-actor/<?php echo e($prodactor->eventActorID); ?>/ins/<?php echo e($instrumentID); ?>" title="IMMEDIATELY delete this production event actor">Delete Actor</a></p>
                                  





                                   <?php endforeach; ?>

                                   <p style="margin-left:30px;"><a href="/production-event/<?php echo e($instrumentID); ?>/add-actor">

                                   <?php if(isset($eventType) && ($eventType == "Production") && ($page == "addactor")): ?>
                                   <strong> 
                                   <?php endif; ?> 
                                   Add Actor
                       
                                   <?php if(isset($eventType) && ($eventType == "Production") && ($page == "addactor")): ?>
                                   </strong> 
                                   <?php endif; ?> 
                                   </a></p>





                                   <?php foreach($events as $key => $event): ?>     
                                   <p>
                                       <?php if($event->eventID == $eventID): ?> 
                                       <strong>
                                           <?php endif; ?>

                                           <a title="Manage <?php echo e($event->eventType); ?> event for <?php echo e($legalBodyName); ?> / <?php echo e($titlePreferred); ?>" href="/edit-instrument/<?php echo e($instrumentID); ?>/events/<?php echo e($event->eventID); ?>"><span style="font-size:17px;"><?php echo e($event->eventType); ?></span></a> 



                                      <span style="font-weight: normal;">[ 
                                       <?php if($page == "eventmaterials" && $eventID == $event->eventID ): ?>  
                                       <strong>
                                       <?php endif; ?>

                                       <a href="/edit-instrument/<?php echo e($instrumentID); ?>/events/<?php echo e($event->eventID); ?>/materials">materials</a>

                                       <?php if($page == "eventmaterials" && $eventID == $event->eventID ): ?>  
                                       </strong>
                                       <?php endif; ?>
                                      ]</span>

         






                                           


                                       <?php if($event->eventID == $eventID): ?> 
                                       </strong>
                                           <?php endif; ?>



                                           <a class="delete" href="/delete-event/<?php echo e($event->eventID); ?>/ins/<?php echo e($instrumentID); ?>" title="IMMEDIATELY delete this event and ALL of the actors belonging to it">Delete Event</a>

                                  
                                   <?php foreach($other_event_actors[$event->eventID] as $key => $otheractor): ?>  
                                   <p style="margin-left:30px;">

                                  <?php if($otheractor->eventActorID == $actorID): ?>
                                  <strong>
                                  <?php endif; ?>

                                   <a href="/events/<?php echo e($event->eventID); ?>/actor/<?php echo e($otheractor->eventActorID); ?>"><?php echo e($otheractor->eventActorName); ?></a>

                                  <?php if($otheractor->eventActorID == $actorID): ?>
                                  </strong>
                                  <?php endif; ?>
                                  


                                    | <a title="IMMEDIATELY delete this <?php echo e($event->eventType); ?> event actor" class="delete" href="/delete-actor/<?php echo e($otheractor->eventActorID); ?>/event/<?php echo e($event->eventID); ?>">Delete Actor</a></p>
                                   <?php endforeach; ?>

                                           <?php if($event->eventID == $eventID): ?> 
                                       </strong>
                                       <?php endif; ?>
                                     </p>



                                   <?php if(sizeof($other_event_actors[$event->eventID]) == 0): ?>
                                   <p style="margin-left:30px;">There are no actors</p>
                                   <?php endif; ?>

                                   <?php if($eventID == $event->eventID && $page == "addactor"): ?>
                                   <strong>
                                   <?php endif; ?>
                                   <p style="margin-left:30px;"><a href="/edit-instrument/<?php echo e($instrumentID); ?>/events/<?php echo e($event->eventID); ?>/add-actor">Add Actor</a></p>
                                   <?php if($eventID == $event->eventID): ?>
                                   </strong>
                                   <?php endif; ?>


                                      

                                       <?php endforeach; ?>        

                                   </div>


                                   <?php if($page == "addevent"): ?>  
                                   <strong>
                                       <?php endif; ?>

                                       <p><a title="Add event to <?php echo e($legalBodyName); ?> / <?php echo e($titlePreferred); ?>" href="/edit-instrument/<?php echo e($instrumentID); ?>/add-event">Add Event</a></p>

                                       <?php if($page == "addevent"): ?>  
                                   </strong>
                                   <?php endif; ?>

                               </li>
                           </ul> 
                       </div>
                   </div>
                   <!-- /Events For This Instrument-->




                   <!-- Recent Activity -->
                   <div class="panel panel-flat">
                    <div class="panel-heading">
                        <h6 class="panel-title">Activity For This Instrument</h6>
                    </div>

                    <div class="panel-body">
                        <div class="content-group-xs" id="bullets"></div>

                        <ul class="media-list" style="margin-top: -14px;">


                         <!-- ACTIVITY LOOP HERE -->




                         <!-- CREATION ACTIVITY -->
                         <li class="media">

                            <div class="media-left media-middle">
                                <a title="<?php echo e($creator_name); ?> created this collection" style="margin-top:-20px;"><img src="/images/users/thumbnails/<?php echo e($creator_avatar); ?>" class="img-circle img-xs" alt=""></a>
                            </div>



                            <div class="media-body">
                                <p><?php echo e($creator_name); ?> <?php if($creationType == "import"): ?> imported <?php endif; ?> <?php if($creationType == "created"): ?> created <?php endif; ?> this instrument</p>
                                <div class="media-annotation"><?php echo e($instrument_creation_timeago); ?></div>
                            </div>
                        </li>



                    </ul>
                </div>
            </div>
                            <!-- /Recent Activity -->