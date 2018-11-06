<?php if(count($errors) > 0): ?>
    <div class="row">
        <div class="col-lg-12">
            <div class="panel registration-form" style="border:0;">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-lg-12">
                            <p style="color:#d84315; font-weight:bold;">Form Error</p>
                            <ul>
                                <?php foreach($errors->all() as $error): ?>
                                       <li style="color:#d84315;"><?php echo e($error); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div> 
                </div>    
            </div>        
        </div> 
    </div>
<?php endif; ?>    