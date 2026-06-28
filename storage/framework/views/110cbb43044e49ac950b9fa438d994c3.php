

<?php $__env->startSection('title', $category->display_name . ' - द पब्लिक एक्सप्रेस'); ?>

<?php $__env->startSection('content'); ?>
<div class="container">
    <div class="row">
        <div class="col-12">
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb bg-white p-3 rounded shadow-sm">
                    <li class="breadcrumb-item"><a href="<?php echo e(url('/')); ?>" class="text-decoration-none">होम</a></li>
                    <li class="breadcrumb-item active"><?php echo e($category->display_name); ?></li>
                </ol>
            </nav>

            <!-- Category Header -->
            <div class="bg-white p-4 rounded shadow-sm mb-4 border-left-4" style="border-left: 4px solid <?php echo e($category->color ?? '#c62828'); ?>;">
                <h1 class="h2 fw-bold mb-0">
                    <?php echo e($category->icon ?? '📰'); ?> <?php echo e($category->display_name); ?>

                </h1>
                <p class="text-muted small mt-1"><?php echo e($category->description ?? $category->display_name . ' की ताज़ा खबरें'); ?></p>
            </div>

            <!-- News Grid -->
            <?php if($news->count() > 0): ?>
                <div class="row g-4">
                    <?php $__currentLoopData = $news; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="col-md-6 col-lg-4">
                            <div class="card h-100 shadow-sm hover-shadow transition">
                                <?php if($item->featured_image): ?>
                                    <img src="<?php echo e(url('/serve-image/' . urlencode($item->featured_image))); ?>" 
                                         class="card-img-top" 
                                         style="height:200px; object-fit:cover;"
                                         alt="<?php echo e($item->title); ?>"
                                         onerror="this.src='<?php echo e(asset('images/default-news.jpg')); ?>'">
                                <?php else: ?>
                                    <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height:200px;">
                                        <span class="display-1 text-muted">📰</span>
                                    </div>
                                <?php endif; ?>
                                <div class="card-body">
                                    <h5 class="card-title fw-bold">
                                        <a href="<?php echo e(route('news.show', $item->slug)); ?>" class="text-decoration-none text-dark hover-text-brand">
                                            <?php echo e(Str::limit($item->title, 70)); ?>

                                        </a>
                                    </h5>
                                    <p class="card-text text-muted small"><?php echo e(Str::limit($item->summary ?? $item->body, 100)); ?></p>
                                </div>
                                <div class="card-footer bg-transparent border-top d-flex justify-content-between align-items-center">
                                    <span class="text-muted small"><i class="far fa-clock"></i> <?php echo e($item->created_at->diffForHumans()); ?></span>
                                    <span class="text-muted small"><i class="far fa-eye"></i> <?php echo e(number_format($item->views ?? 0)); ?></span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>

                <!-- Pagination -->
                <div class="mt-4 d-flex justify-content-center">
                    <?php echo e($news->links()); ?>

                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-newspaper fa-3x text-muted mb-3"></i>
                    <p class="text-muted">इस श्रेणी में अभी कोई खबर नहीं है।</p>
                    <a href="<?php echo e(url('/')); ?>" class="btn btn-primary">होम पेज पर जाएं</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
    .hover-shadow:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.1) !important;
        transition: all 0.3s ease;
    }
    .hover-text-brand:hover {
        color: #c62828 !important;
    }
    .border-left-4 {
        border-left-width: 4px !important;
    }
    .transition {
        transition: all 0.3s ease;
    }
</style>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\public-express\resources\views/category/show.blade.php ENDPATH**/ ?>