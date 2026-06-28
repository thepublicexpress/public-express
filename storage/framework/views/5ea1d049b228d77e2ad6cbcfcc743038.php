

<?php $__env->startSection('title', ($district->name ?? 'जिला') . ' - द पब्लिक एक्सप्रेस'); ?>

<?php $__env->startSection('content'); ?>
<div class="container my-4">
    <h3 class="border-start border-danger border-4 ps-3 mb-4 text-dark font-weight-bold">
        जिला: <?php echo e($district->name ?? 'स्थानीय ख़बरें'); ?>

    </h3>

    <div class="row">
        <?php if(isset($news) && $news->count() > 0): ?>
            <?php $__currentLoopData = $news; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="col-md-4 mb-4">
                <div class="card h-100 shadow-sm border-0 rounded-3 overflow-hidden">
                    <?php if($item->image): ?>
                        <img src="<?php echo e(asset('storage/' . $item->image)); ?>" class="card-img-top" alt="<?php echo e($item->title); ?>" style="height: 200px; object-fit: cover;">
                    <?php else: ?>
                        <img src="https://via.placeholder.com/400x200" class="card-img-top" alt="Default Image">
                    <?php endif; ?>
                    <div class="card-body">
                        <h5 class="card-title text-dark font-weight-bold" style="font-size: 1.1rem;"><?php echo e(\Illuminate\Support\Str::limit($item->title, 60)); ?></h5>
                        <p class="card-text text-muted small"><?php echo e(\Illuminate\Support\Str::limit(strip_tags($item->content), 100)); ?></p>
                        <a href="<?php echo e(route('news.show', $item->slug ?? $item->id)); ?>" class="btn btn-sm btn-danger rounded-pill px-3">पूरी ख़बर पढ़ें</a>
                    </div>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php else: ?>
            <div class="col-md-12 text-center py-5">
                <h5 class="text-muted">इस जिले में अभी कोई ख़बर उपलब्ध नहीं है।</h5>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\public-express\resources\views/news/district.blade.php ENDPATH**/ ?>