<?= $this->extend('Customers/layout/main') ?>

<?= $this->section('styles') ?>

<?= $this->endSection() ?>

<?= $this->section('content') ?>
    <!-- breadcrumb start-->
    <section class="breadcrumb breadcrumb_bg">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="breadcrumb_iner">
                        <div class="breadcrumb_iner_item">
                            <h2>Blog</h2>
                            <p>Home <span>-</span> Blog</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- breadcrumb start-->

    <!--================Blog Area =================-->
    <section class="blog_area padding_top">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 mb-5 mb-lg-0">
                    <div class="blog_left_sidebar">
                        
                        <!-- Search Form -->
                        <?php if ((isset($searchKeyword) && $searchKeyword) || (isset($currentCategory) && $currentCategory)): ?>
                            <div class="blog-search-form">
                                <div class="alert alert-info">
                                    <i class="ti-search"></i>
                                    <?php if (isset($searchKeyword) && $searchKeyword): ?>
                                        Search results for: <strong>"<?= esc($searchKeyword) ?>"</strong>
                                    <?php endif; ?>
                                    <?php if (isset($currentCategory) && $currentCategory): ?>
                                        Category: <strong><?= esc($currentCategory) ?></strong>
                                    <?php endif; ?>
                                    <?php if (isset($totalResults)): ?>
                                        - <?= $totalResults ?> result(s) found
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Blog Posts -->
                        <?php if (!empty($posts)): ?>
                            <?php foreach ($posts as $post): ?>
                                <article class="blog_item">
                                    <div class="blog_item_img">
                                        <img class="card-img rounded-0" 
                                             src="<?= base_url($post['featured_image'] ?? 'aranoz-master/img/blog/single_blog_1.png') ?>" 
                                             alt="<?= esc($post['image_alt'] ?? $post['title']) ?>"
                                             style="width: 100%;height: 600px; ">
                                        <a href="<?= base_url('blog/post/' . $post['slug']) ?>" class="blog_item_date">
                                            <h3><?= date('j', strtotime($post['published_at'])) ?></h3>
                                            <p><?= date('M', strtotime($post['published_at'])) ?></p>
                                        </a>
                                    </div>

                                    <div class="blog_details">
                                        <a class="d-inline-block" href="<?= base_url('blog/post/' . $post['slug']) ?>">
                                            <h2><?= esc($post['title']) ?></h2>
                                        </a>
                                        <p class="blog-excerpt"><?= esc($post['excerpt']) ?></p>
                                        
                                        <ul class="blog-info-link blog-meta">
                                            <li>
                                                <a href="<?= base_url('blog?category=' . urlencode($post['category'])) ?>">
                                                    <i class="ti-folder"></i> <?= esc($post['category']) ?>
                                                </a>
                                            </li>
                                            <li>
                                                <span><i class="ti-user"></i> <?= esc($post['author_name']) ?></span>
                                            </li>
                                            <li>
                                                <span><i class="ti-eye"></i> <?= number_format($post['view_count']) ?> views</span>
                                            </li>
                                            <li>
                                                <span><i class="ti-time"></i> <?= $post['reading_time'] ?> min read</span>
                                            </li>
                                        </ul>
                                        
                                        <div class="mt-3">
                                            <a href="<?= base_url('blog/post/' . $post['slug']) ?>" class="btn_3">
                                                Read More <i class="ti-arrow-right"></i>
                                            </a>
                                        </div>
                                    </div>
                                </article>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <!-- No Results -->
                            <div class="no-results">
                                <i class="ti-search"></i>
                                <h3>No blog posts found</h3>
                                <p>We couldn't find any posts matching your criteria.</p>
                                <a href="<?= base_url('blog') ?>" class="btn_1">View All Posts</a>
                            </div>
                        <?php endif; ?>

                       <!-- Pagination -->
<?php if (isset($pager) && $pager->getPageCount() > 1): ?>
    <nav class="blog-pagination justify-content-center d-flex">
        <ul class="pagination">
            <!-- Previous Page -->
            <?php if ($pager->getCurrentPage() > 1): ?>
                <li class="page-item">
                    <a href="<?= $pager->getPageURI($pager->getCurrentPage() - 1) ?>" class="page-link" aria-label="Previous">
                        <i class="ti-angle-left"></i>
                    </a>
                </li>
            <?php endif; ?>

            <!-- Page Numbers (Manual loop thay vì foreach links()) -->
            <?php for ($i = 1; $i <= $pager->getPageCount(); $i++): ?>
                <li class="page-item <?= ($i == $pager->getCurrentPage()) ? 'active' : '' ?>">
                    <a href="<?= $pager->getPageURI($i) ?>" class="page-link">
                        <?= $i ?>
                    </a>
                </li>
            <?php endfor; ?>

            <!-- Next Page -->
            <?php if ($pager->getCurrentPage() < $pager->getPageCount()): ?>
                <li class="page-item">
                    <a href="<?= $pager->getPageURI($pager->getCurrentPage() + 1) ?>" class="page-link" aria-label="Next">
                        <i class="ti-angle-right"></i>
                    </a>
                </li>
            <?php endif; ?>
        </ul>
    </nav>
<?php endif; ?>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="col-lg-4">
                    <div class="blog_right_sidebar">
                        
                        <!-- Search Widget -->
                        <aside class="single_sidebar_widget search_widget">
                            <form action="<?= base_url('blog') ?>" method="GET">
                                <div class="form-group">
                                    <div class="input-group mb-3">
                                        <input type="text" name="keyword" class="form-control" 
                                               value="<?= esc($searchKeyword ?? '') ?>"
                                               placeholder='Search Posts'
                                               onfocus="this.placeholder = ''"
                                               onblur="this.placeholder = 'Search Posts'">
                                        <div class="input-group-append">
                                            <button class="btn" type="submit"><i class="ti-search"></i></button>
                                        </div>
                                    </div>
                                </div>
                                <button class="button rounded-0 primary-bg text-white w-100 btn_1" type="submit">
                                    Search
                                </button>
                            </form>
                        </aside>

                        <!-- Categories Widget -->
                        <aside class="single_sidebar_widget post_category_widget">
                            <h4 class="widget_title">Categories</h4>
                            <ul class="list cat-list">
                                <?php if (!empty($categories)): ?>
                                    <?php foreach ($categories as $category): ?>
                                        <li>
                                            <a href="<?= base_url('blog?category=' . urlencode($category['category'])) ?>" class="d-flex category-link">
                                                <p><?= esc($category['category']) ?></p>
                                                <p>(<?= $category['post_count'] ?>)</p>
                                            </a>
                                        </li>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <li><p>No categories found</p></li>
                                <?php endif; ?>
                            </ul>
                        </aside>

                        <!-- Recent Posts Widget -->
                        <aside class="single_sidebar_widget popular_post_widget">
                            <h3 class="widget_title">Recent Posts</h3>
                            <?php if (!empty($recentPosts)): ?>
                                <?php foreach ($recentPosts as $recentPost): ?>
                                    <div class="media post_item">
                                        <img src="<?= base_url($recentPost['featured_image'] ?? 'aranoz-master/img/post/post_1.png') ?>" 
                                             alt="<?= esc($recentPost['title']) ?>"
                                             style="width: 80px; height: 60px; object-fit: cover;">
                                        <div class="media-body">
                                            <a href="<?= base_url('blog/post/' . $recentPost['slug']) ?>">
                                                <h3><?= esc(strlen($recentPost['title']) > 40 ? substr($recentPost['title'], 0, 40) . '...' : $recentPost['title']) ?></h3>
                                            </a>
                                            <p><?= date('F j, Y', strtotime($recentPost['published_at'])) ?></p>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </aside>

                        <!-- Featured Posts Widget -->
                        <?php if (!empty($featuredPosts)): ?>
                            <aside class="single_sidebar_widget popular_post_widget">
                                <h3 class="widget_title">Featured Posts</h3>
                                <?php foreach ($featuredPosts as $featured): ?>
                                    <div class="media post_item">
                                        <img src="<?= base_url($featured['featured_image'] ?? 'aranoz-master/img/post/post_1.png') ?>" 
                                             alt="<?= esc($featured['title']) ?>"
                                             style="width: 80px; height: 60px; object-fit: cover;">
                                        <div class="media-body">
                                            <a href="<?= base_url('blog/post/' . $featured['slug']) ?>">
                                                <h3><?= esc(strlen($featured['title']) > 40 ? substr($featured['title'], 0, 40) . '...' : $featured['title']) ?></h3>
                                            </a>
                                            <p><?= date('F j, Y', strtotime($featured['published_at'])) ?></p>
                                            <small class="text-warning">
                                                <i class="ti-star"></i> Featured
                                            </small>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </aside>
                        <?php endif; ?>

                        <!-- Newsletter Widget -->
                        <aside class="single_sidebar_widget newsletter_widget">
                            <h4 class="widget_title">Newsletter</h4>
                            <form action="#" method="POST" id="newsletter-form">
                                <div class="form-group">
                                    <input type="email" name="email" class="form-control" 
                                           onfocus="this.placeholder = ''"
                                           onblur="this.placeholder = 'Enter email'" 
                                           placeholder='Enter email' required>
                                </div>
                                <button class="button rounded-0 primary-bg text-white w-100 btn_1" type="submit">
                                    Subscribe
                                </button>
                            </form>
                        </aside>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--================Blog Area =================-->
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    // Newsletter form submission
    $('#newsletter-form').on('submit', function(e) {
        e.preventDefault();
        // Add newsletter subscription logic here
        showToast('success', 'Thank you for subscribing to our newsletter!');
    });

    function showToast(type, message) {
        const toastClass = type === 'success' ? 'alert-success' : 'alert-danger';
        const toast = `
            <div class="alert ${toastClass} alert-dismissible fade show position-fixed" 
                 style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
                ${message}
                <button type="button" class="close" data-dismiss="alert">
                    <span>&times;</span>
                </button>
            </div>
        `;
        $('body').append(toast);
        setTimeout(() => $('.alert').fadeOut(() => $('.alert').remove()), 3000);
    }
});
</script>
<?= $this->endSection() ?>