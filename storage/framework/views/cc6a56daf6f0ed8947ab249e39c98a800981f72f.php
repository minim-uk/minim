<?php $__env->startSection('content'); ?>

            <?php echo $__env->yieldContent('demo'); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script type="text/javascript">
<?php echo $__env->yieldContent('js'); ?>
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.loggedin', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>