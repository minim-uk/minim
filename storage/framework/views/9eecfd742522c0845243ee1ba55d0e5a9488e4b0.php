<?php $__env->startSection('title'); ?>
<?php echo e($titleSingle); ?> Preview
<?php $__env->stopSection(); ?>



<?php $__env->startSection('content'); ?>
<h1><?php echo e($legalBodyName); ?> > <?php echo e($titlePreferred); ?></h1>

<?php if(strlen($adminID) > 0): ?>
    <p><img src="/images/users/thumbnails/<?php echo e($creator_avatar); ?>" style="border-radius: 50%; max-width:50px;">

&nbsp;<strong>

        <?php if($creationType == "import"): ?>
        Imported
        <?php endif; ?>

        <?php if($creationType == "created"): ?>
        Created
        <?php endif; ?> 

    </strong>by <?php echo e($creator_name); ?> <?php echo e($instrument_creation_timeago); ?>



        <?php if(strlen($created_at) > 0): ?>
            (<?php echo e($instrument_created_at); ?>)
        <?php endif; ?>

        &nbsp;<span style="font-size:13px;"><a href="/edit-instrument/<?php echo e($instrumentID); ?>">[manage instrument]</a></span>

    </p>
<?php endif; ?>


<?php if($updated_at != "0000-00-00 00:00:00"): ?>
    <p><strong>Last Updated Date:</strong> <?php echo e($updated_at); ?></p>
<?php endif; ?>

<p><strong>Repository:</strong> <?php echo e($repositoryName); ?></p>
<p><strong>Inventory number:</strong> <?php echo e($inventoryNumber); ?></p>

<?php if(strlen($status) > 0): ?>
    <p><strong>Status:</strong> <?php echo e($status); ?></p>
<?php endif; ?>

<?php if(strlen($hornbostelCat) > 0): ?>
    <p><strong>Hornbostel Category:</strong> <?php echo e($hornbostelCat); ?></p>
<?php endif; ?>

<?php if(strlen($tags) > 0): ?>
    <p><strong>Tags:</strong> <?php echo e($tags); ?></p>
<?php endif; ?>

<?php if(strlen($measurementsFreeText) > 0): ?>
    <p><strong>Measurements Free Text:</strong> <?php echo e($measurementsFreeText); ?></p>
<?php endif; ?>

<?php if(strlen($sourceWebsite) > 0): ?>
    <p><strong>Source Website:</strong> <?php echo e($sourceWebsite); ?></p>
<?php endif; ?>

<?php if(count($measurements) > 0): ?>
    <h2 class="preview" class="preview">Specific Measurements</h2 class="preview">
    <?php foreach($measurements AS $measurement): ?>
        <p>

        <?php if(strlen($measurement->type) > 0): ?>
        <strong><?php echo e($measurement->type); ?></strong>:  
        <?php endif; ?>
        
        <?php if(strlen($measurement->unit) > 0): ?>
        <?php echo e($measurement->value); ?> <?php echo e($measurement->unit); ?>

        <?php endif; ?>
        </p>

    <?php endforeach; ?>
<?php endif; ?>

<?php if(strlen($mainDescriptionText) > 0): ?>
<h2 class="preview" class="preview">Main Description</h2 class="preview">
<?php endif; ?>
<?php if(strlen($mainDescriptionType) > 0): ?>
<p><strong>Type</strong>:  <?php echo e($mainDescriptionType); ?></p>
<?php endif; ?>
<?php if(strlen($mainDescriptionSource) > 0): ?>
<p><strong>Source</strong>:  <?php echo e($mainDescriptionSource); ?></p>
<?php endif; ?>
<?php if(strlen($mainDescriptionText) > 0): ?>
<p><strong>Text</strong>:  <?php echo e($mainDescriptionText); ?></p>
<?php endif; ?>

<?php if(count($descriptions) > 0): ?>
    <h2 class="preview" class="preview">Other Description(s)</h2 class="preview">
    <?php foreach($descriptions AS $description): ?>
        <?php if(strlen($description->descriptionType) > 0): ?>
        <p><strong>Type</strong>:  <?php echo e($description->descriptionType); ?></p>
        <?php endif; ?>
        
        <?php if(strlen($description->descriptionTextSource) > 0): ?>
        <p><strong>Source</strong>:  <?php echo e($description->descriptionTextSource); ?></p>
        <?php endif; ?>

        <p><strong>Text</strong>: <?php echo e($description->descriptionText); ?></p>      
    <?php endforeach; ?>
<?php endif; ?>

<?php if(strlen($serialEditionNumbers) > 0): ?>
    <h2 class="preview">Serial Edition Numbers</h2 class="preview">
    <p> <?php echo e($serialEditionNumbers); ?> </p>
<?php endif; ?>

<?php if(count($inscriptions) > 0): ?>

    <?php foreach($inscriptions as $key => $inscription): ?>
           
        <?php if(($key == 0) && (strlen($inscription) > 0)): ?>
        <h2 class="preview">Inscriptions</h2>
        <?php endif; ?>

        <?php if(strlen($inscription) > 0): ?>
            <p><strong>&#149;</strong>&nbsp;<?php echo e($inscription); ?></p>
        <?php endif; ?>    

    <?php endforeach; ?>   

<?php endif; ?>

<?php foreach($decorativeElements as $key => $decorativeElement): ?>
    <?php if(($key == 0) && (strlen($decorativeElement) > 0)): ?>
    <h2 class="preview">Decorative Elements</h2>
    <?php endif; ?>
    <?php if(strlen($decorativeElement) > 0): ?>
        <p><strong>&#149;</strong>&nbsp;<?php echo e($decorativeElement); ?></p>
    <?php endif; ?>    
<?php endforeach; ?>   

<h2 class="preview" style="clear:left;">Production Event <span><a href="/production-event/<?php echo e($instrumentID); ?>">[manage production event]</a></span></h2>

<?php if(strlen($productionEventName) > 0): ?>
    <p><strong>Production Event Name:</strong> <?php echo e($productionEventName); ?></p>
<?php endif; ?>

<?php if(strlen($productionEventLocation) > 0): ?>
    <p><strong>Location:</strong> <?php echo e($productionEventLocation); ?></p>
<?php endif; ?>

<?php if(strlen($productionEventNameSource) > 0): ?>
    <p><strong>Production Event Name Source:</strong> <?php echo e($productionEventNameSource); ?></p>
<?php endif; ?>

<?php if(strlen($productionEventDateText) > 0): ?>
    <p><strong>Production Event Date Text:</strong> <?php echo e($productionEventDateText); ?></p>
<?php endif; ?>

<?php if(strlen($productionEventEarliestDate) > 0): ?>
    <p><strong>Earliest Date:</strong> <?php echo e($productionEventEarliestDate); ?></p>
<?php endif; ?>

<?php if(strlen($productionEventLatestDate) > 0): ?>
    <p><strong>Latest Date:</strong> <?php echo e($productionEventLatestDate); ?></p>
<?php endif; ?>

<?php if(strlen($productionEventCulture) > 0): ?>
    <p><strong>Culture:</strong> <?php echo e($productionEventCulture); ?></p>
<?php endif; ?>

<?php if(strlen($productionPeriodName) > 0): ?>
    <p><strong>Period Name:</strong> <?php echo e($productionPeriodName); ?></p>
<?php endif; ?>

<?php if(strlen($productionMaterialsFreeText) > 0): ?>
    <p><strong>Production Materials Free Text:</strong> <?php echo e($productionMaterialsFreeText); ?></p>
<?php endif; ?>

<?php if(strlen($productionMaterials) > 0): ?>
    <p><strong>Specific Materials:</strong> <?php echo e($productionMaterials); ?>

    </p>
<?php endif; ?>

<?php foreach($events as $key => $event): ?>

    <h2 class="preview"><?php echo e($event->eventType); ?> Event <span><a href="/edit-instrument/<?php echo e($instrumentID); ?>/events/<?php echo e($event->eventID); ?>">[manage <?php echo e($event->eventType); ?> event]</a></span></h2>
        
    <?php if(strlen($event->eventName) > 0): ?>
        <p><strong>Event Name: </strong> <?php echo e($event->eventName); ?> </p>
    <?php endif; ?>

    <?php if(strlen($event->location) > 0): ?>
        <p><strong>Location: </strong> <?php echo e($event->location); ?> </p>
    <?php endif; ?>

    <?php if(strlen($event->eventEarliestDate) > 0): ?>
        <p><strong>Earliest Date: </strong> <?php echo e($event->eventEarliestDate); ?> </p>
    <?php endif; ?>

    <?php if(strlen($event->eventLatestDate) > 0): ?>
        <p><strong>Latest Date: </strong> <?php echo e($event->eventLatestDate); ?> </p>
    <?php endif; ?>

    <?php if(strlen($event->eventCulture) > 0): ?>
        <p><strong>Culture: </strong> <?php echo e($event->eventCulture); ?> </p>
    <?php endif; ?>

    <?php if(strlen($event->periodName) > 0): ?>
        <p><strong>Period Name: </strong> <?php echo e($event->periodName); ?> </p>
    <?php endif; ?>

    <?php if(strlen($event->materialsText) > 0): ?>
        <p><strong>Materials Text: </strong> <?php echo e($event->materialsText); ?> </p>
    <?php endif; ?>
    
    <?php if(strlen($event->materials) > 0): ?>
        <p><strong>Specific Materials:</strong> <?php echo e($event->materials); ?>

        </p>
    <?php endif; ?>



<?php endforeach; ?>

<?php if(count($images) > 0): ?>
    <h2 class="preview" style="margin-top: 20px;">Images <span><a href="/edit-instrument/<?php echo e($instrumentID); ?>/images">[manage images]</a></h2 class="preview">
<?php else: ?>
    <h2 class="preview" style="margin-top: 10px;">Images</h2 class="preview">
    <p>This instrument currently has no images. <a href="/edit-instrument/<?php echo e($instrumentID); ?>/add-resource">[add images]</a>
<?php endif; ?>

<?php if(count($images) > 0): ?>
        <?php foreach($images AS $image): ?>
        <div style="float:left; margin-right: 10px; border:1px #ddd solid; padding:10px;width:30%; width:300px; height:330px; margin-bottom:10px;">
            
            <center><a href="/edit-instrument/<?php echo e($instrumentID); ?>/resource/<?php echo e($image->resourceID); ?>"><img src="/instrument_resources/images/<?php echo e($legalBodyMDAcode); ?>/thumbnails/<?php echo e($image->resourceFileName); ?>" alt="" style="max-width: 250px; max-height: 250px;"/></a></center>


        <?php if(strlen($image->resourceCaption) > 0): ?>
                <p style="margin-top:20px;"><strong>Caption: </strong><?php echo e(str_limit($image->resourceCaption, $limit = 43, $end = '...')); ?></p>
        <?php else: ?>
                <p style="margin-top:20px;"><strong>Caption: </strong>none.</p>
        <?php endif; ?>

        </div>    
        <?php endforeach; ?>
<?php endif; ?>



<?php if(count($videos) > 0): ?>
    <h2 class="preview" style="clear:left;">Video <span><a href="/edit-instrument/<?php echo e($instrumentID); ?>/video">[manage video]</a></span></h2 class="preview">

<?php foreach($videos AS $video): ?>

<div style="float:left; border:1px #ddd solid; padding:10px; width:30%; width:340px; height:240px; margin-right: 10px; margin-bottom:10px;">

    <div style="float:left; margin-right:50px; background-color: #fff;">
    <div style="width:250px; float:left; margin-right: 30px; margin-bottom: 10px;">
             <!--<h4><?php echo e($video->resourceFileName); ?></h4>-->
              <video id="<?php echo e($video->resourceFileName); ?>" class="video-js" controls preload="auto" width="320" height="132"
              poster="/instrument_resources/images/<?php echo e($legalBodyMDAcode); ?>/<?php echo e($image->resourceFileName); ?>" data-setup="{}">

                <source src="/instrument_resources/video/<?php echo e($legalBodyMDAcode); ?>/<?php echo e($video->resourceFileName); ?>" type='video/webm'> -->

                <?php /* SAME VIDEO OF DIFFERENT TYPE FOR FALLBACKS ON WEB PLAY MAY TO BE GROUPED PER VID BY NAME */ ?>
                <p class="vjs-no-js">
                  To view this video please enable JavaScript, and consider upgrading to a web browser that
                  <a href="http://videojs.com/html5-video-support/" target="_blank">supports HTML5 video</a>
                </p>
              </video>

            <?php if(strlen($video->resourceCaption) > 0): ?>
                    <p style="margin-top:10px;"><strong>Caption: </strong><?php echo e(str_limit($video->resourceCaption, $limit = 38, $end = '...')); ?></p>
            <?php else: ?>
                    <p style="margin-top:10px;"><strong>Caption: </strong>none.</p>
            <?php endif; ?>

</div>
    </div>  
    </div>   
<?php endforeach; ?>

    <script src="http://vjs.zencdn.net/5.10.4/video.js"></script>

<?php else: ?>
<div style="clear:left; float:left; margin-right: 10px;">
    <h2 class="preview" style="margin-top: 10px;">Video</h2>
    <p>This instrument currently has no video. <a href="/edit-instrument/<?php echo e($instrumentID); ?>/add-resource">[add video]</a></p>
</p>    
<?php endif; ?>


<?php if(count($sounds) > 0): ?>
    <h2 class="preview" style="margin-top: 10px; clear:both">Audio <span><a href="/edit-instrument/<?php echo e($instrumentID); ?>/audio">[manage audio]</a></span></h2 class="preview">

    <!-- DO THE PLAYLIST HERE -->
   <?php foreach($sounds AS $sound): ?>

<div style="float:left; margin-right: 10px; border:1px #ddd solid; padding:10px;width:30%; width:300px; height:200px; margin-bottom:10px;">
        <div style="width:250px; float:left; margin-right: 10px; margin-bottom: 10px;" id="player<?php echo e($sound->resourceFileName); ?>" class="aplayer">
        </div>
 
        <script type="text/javascript" src="<?php echo asset('/js/admin/plugins/audioplayer/APlayer.min.js')?>"></script>
        <script type="text/javascript">   
        var ap4 = new APlayer({
            element: document.getElementById('player<?php echo e($sound->resourceFileName); ?>'),
            narrow: false,
            autoplay: false,
            showlrc: false,
            mutex: true,
            theme: '#ad7a86',
            music: [
                    {
                        title: '<?php echo e($sound->resourceFileName); ?>',
                        author: '<?php echo e($repositoryName); ?>',
                        url: '/instrument_resources/sound/<?php echo e($legalBodyMDAcode); ?>/<?php echo e($sound->resourceFileName); ?>',
                     
                        pic: '/instrument_resources/images/<?php echo e($legalBodyMDAcode); ?>/<?php echo e($image->resourceFileName); ?>'
                      
                    },
            ]
        });
        ap4.init();
        </script>

<?php if(strlen($sound->resourceCaption) > 0): ?>
        <p style="float:left;"><strong>Caption: </strong><?php echo e(str_limit($sound->resourceCaption, $limit = 34, $end = '...')); ?></p>
<?php else: ?>
        <p style="margin-top:10px; float:left;"><strong>Caption: </strong>none.</p>
<?php endif; ?>
</div>
</div>

    <?php endforeach; ?>    

<?php else: ?>
<div style="clear:left; float:left; margin-right: 10px;">
    <h2 class="preview" style="margin-top: 10px;">Audio</h2>
    <p>This instrument currently has no audio. <a href="/edit-instrument/<?php echo e($instrumentID); ?>/add-resource">[add audio]</a></p>
</div>
<?php endif; ?>



<?php if(count($rights) > 0): ?>
<br clear="both"/>
<hr/>

    <h2 class="preview" style="margin-top: 10px; color:gray;">Rights Information For This Instrument <span><a href="/edit-instrument/<?php echo e($instrumentID); ?>/rights">[manage rights information]</a></span></h2>

     <?php foreach($rights AS $rights): ?>
         <?php if(strlen($rights->rightsType) > 0): ?> <p style="color:gray;"><strong>Rights Type: </strong><?php echo e($rights->rightsType); ?></p> <?php endif; ?>
         <?php if(strlen($rights->rightsEarliestDate) > 0): ?> <p style="margin-top:-10px; color:gray;"><strong>Rights Earliest Date: </strong><?php echo e($rights->rightsEarliestDate); ?></p> <?php endif; ?>
         <?php if(strlen($rights->rightsLatestDate) > 0): ?> <p style="margin-top:-10px; color:gray;"><strong>Rights Latest Date: </strong><?php echo e($rights->rightsLatestDate); ?></p> <?php endif; ?>
         <?php if(strlen($rights->rightsHolderName) > 0): ?> <p style="margin-top:-10px; color:gray;"><strong>Rights Holder Name: </strong><?php echo e($rights->rightsHolderName); ?></p> <?php endif; ?>
         <?php if(strlen($rights->rightsHolderID) > 0): ?> <p style="margin-top:-10px; color:gray;"><strong>Rights Holder ID: </strong><?php echo e($rights->rightsHolderID); ?></p> <?php endif; ?>
         <?php if(strlen($rights->rightsHolderWebsite) > 0): ?> <p style="margin-top:-10px; color:gray;"><strong>Rights Holder Website: </strong><?php echo e($rights->rightsHolderWebsite); ?></p> <?php endif; ?>
         <?php if(strlen($rights->rightsCreditLine) > 0): ?> <p style="margin-top:-10px; color:gray;"><strong>Rights CreditLine: </strong><small><?php echo e($rights->rightsCreditLine); ?></small></p> <?php endif; ?>

     <?php endforeach; ?>

<?php else: ?>
<div style="clear:left; float:left; margin-right: 10px;">
    <h2 class="preview" style="margin-top: 10px; color:gray;">Rights Information For This Instrument</h2>
    <p style="color:gray;">This instrument currently has no rights information. <span><a href="/edit-instrument/<?php echo e($instrumentID); ?>/rights">[add rights information]</a></span></p>
</p>   
<?php endif; ?>

<?php $__env->stopSection(); ?> 
<?php echo $__env->make('layouts.instrumentpreview', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>