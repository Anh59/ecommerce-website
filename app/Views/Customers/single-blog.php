<?= $this->extend('Customers/layout/main') ?>

<?= $this->section('styles') ?>
<style>
.single-blog-header { margin-bottom: 30px; }
.blog-meta { color: #666; font-size: 14px; margin: 15px 0; }
.blog-meta i { margin-right: 5px; }
.blog-meta span { margin-right: 15px; }
.blog-content { line-height: 1.8; font-size: 16px; }
.blog-content h2, .blog-content h3, .blog-content h4 { margin: 25px 0 15px 0; }
.blog-content p { margin-bottom: 15px; }
.blog-content img { max-width: 100%; height: auto; margin: 20px 0; }
.blog-tags { margin: 30px 0; }
.blog-tag { display: inline-block; background: #f8f9fa; padding: 5px 12px; margin: 5px 5px 5px 0; border-radius: 15px; color: #666; text-decoration: none; font-size: 13px; }
.blog-tag:hover { background: #007bff; color: white; text-decoration: none; }
.blog-navigation { border-top: 1px solid #eee; margin-top: 40px; padding-top: 30px; }
.nav-post { display: flex; align-items: center; text-decoration: none; color: #333; }
.nav-post:hover { text-decoration: none; color: #007bff; }
.nav-post img { width: 60px; height: 60px; object-fit: cover; border-radius: 5px; margin: 0 15px; }
.nav-post-content h5 { margin: 0; font-size: 14px; }
.nav-post-content p { margin: 0; font-size: 12px; color: #666; }
.related-posts { margin-top: 40px; border-top: 1px solid #eee; padding-top: 30px; }
.related-post-item { margin-bottom: 20px; }
.related-post-item img { width: 100px; height: 80px; object-fit: cover; border-radius: 5px; }
.comments-section { margin-top: 40px; border-top: 1px solid #eee; padding-top: 30px; }
.comment-item { margin-bottom: 30px; padding: 20px; background: #f8f9fa; border-radius: 8px; }
.comment-header { display: flex; justify-content-between; align-items: center; margin-bottom: 10px; }
.comment-author { font-weight: 600; color: #333; }
.comment-date { color: #666; font-size: 13px; }
.comment-content { color: #555; line-height: 1.6; }
.comment-reply { margin-left: 40px; margin-top: 20px; }
.reply-btn { color: #007bff; font-size: 13px; cursor: pointer; }
.reply-btn:hover { text-decoration: underline; }
.comment-form { background: #fff; padding: 30px; border: 1px solid #eee; border-radius: 8px; margin-top: 30px; }
.featured-badge { background: #ff6b35; color: white; padding: 3px 8px; border-radius: 12px; font-size: 11px; margin-left: 10px; }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
    <!-- breadcrumb start-->
    <section class="breadcrumb breadcrumb_bg">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="breadcrumb_iner">
                        <div class="breadcrumb_iner_item">
                            <h2>Blog Details</h2>
                            <p>Home <span>-</span> Blog <span>-</span> <?= esc($post['title']) ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- breadcrumb start-->

    <!--================Single Blog Area =================-->
    <section class="blog_area single-post-area padding_top">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 posts-list">
                    <div class="single-post">
                        
                        <!-- Post Header -->
                        <div class="single-blog-header">
                            <div class="feature-img">
                                <img class="img-fluid w-100" 
                                     src="<?= base_url($post['featured_image'] ?? 'aranoz-master/img/blog/single_blog_1.png') ?>" 
                                     alt="<?= esc($post['image_alt'] ?? $post['title']) ?>">
                            </div>
                            
                            <div class="blog_details mt-4">
                                <h2><?= esc($post['title']) ?>
                                    <?php if ($post['is_featured']): ?>
                                        <span class="featured-badge">Featured</span>
                                    <?php endif; ?>
                                </h2>
                                
                                <div class="blog-meta">
                                    <span><i class="ti-user"></i> By <?= esc($post['author_name']) ?></span>
                                    <span><i class="ti-calendar"></i> <?= date('F j, Y', strtotime($post['published_at'])) ?></span>
                                    <span><i class="ti-folder"></i> 
                                        <a href="<?= base_url('blog/category/' . urlencode($post['category'])) ?>" class="text-primary">
                                            <?= esc($post['category']) ?>
                                        </a>
                                    </span>
                                    <span><i class="ti-eye"></i> <?= number_format($post['view_count']) ?> views</span>
                                    <span><i class="ti-time"></i> <?= $post['reading_time'] ?> min read</span>
                                    <span><i class="ti-comment"></i> <?= $commentCount ?> comments</span>
                                </div>
                                
                                <div class="blog-content mt-4">
                                    <?= $post['content'] ?>
                                </div>
                                
                                <!-- Tags -->
                                <?php if (!empty($post['tags'])): ?>
                                    <?php $tags = is_string($post['tags']) ? json_decode($post['tags'], true) : $post['tags']; ?>
                                    <?php if (!empty($tags) && is_array($tags)): ?>
                                        <div class="blog-tags">
                                            <strong>Tags: </strong>
                                            <?php foreach ($tags as $tag): ?>
                                                <a href="<?= base_url('blog/search?keyword=' . urlencode($tag)) ?>" class="blog-tag">
                                                    <?= esc($tag) ?>
                                                </a>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Post Navigation -->
                        <?php if ($previousPost || $nextPost): ?>
                            <div class="blog-navigation">
                                <div class="row">
                                    <div class="col-md-6">
                                        <?php if ($previousPost): ?>
                                            <a href="<?= base_url('blog/post/' . $previousPost['slug']) ?>" class="nav-post">
                                                <div class="nav-direction">
                                                    <i class="ti-arrow-left"></i> Previous Post
                                                </div>
                                                <img src="<?= base_url($previousPost['featured_image'] ?? 'aranoz-master/img/blog/single_blog_1.png') ?>" 
                                                     alt="<?= esc($previousPost['title']) ?>">
                                                <div class="nav-post-content">
                                                    <h5><?= esc(strlen($previousPost['title']) > 50 ? substr($previousPost['title'], 0, 50) . '...' : $previousPost['title']) ?></h5>
                                                    <p><?= date('M j, Y', strtotime($previousPost['published_at'])) ?></p>
                                                </div>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                    <div class="col-md-6 text-right">
                                        <?php if ($nextPost): ?>
                                            <a href="<?= base_url('blog/post/' . $nextPost['slug']) ?>" class="nav-post justify-content-end">
                                                <div class="nav-post-content text-right">
                                                    <h5><?= esc(strlen($nextPost['title']) > 50 ? substr($nextPost['title'], 0, 50) . '...' : $nextPost['title']) ?></h5>
                                                    <p><?= date('M j, Y', strtotime($nextPost['published_at'])) ?></p>
                                                </div>
                                                <img src="<?= base_url($nextPost['featured_image'] ?? 'aranoz-master/img/blog/single_blog_1.png') ?>" 
                                                     alt="<?= esc($nextPost['title']) ?>">
                                                <div class="nav-direction">
                                                    Next Post <i class="ti-arrow-right"></i>
                                                </div>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Related Posts -->
                        <?php if (!empty($relatedPosts)): ?>
                            <div class="related-posts">
                                <h4>Related Posts</h4>
                                <div class="row">
                                    <?php foreach ($relatedPosts as $related): ?>
                                        <div class="col-md-4 related-post-item">
                                            <div class="card border-0">
                                                <img src="<?= base_url($related['featured_image'] ?? 'aranoz-master/img/blog/single_blog_1.png') ?>" 
                                                     class="card-img-top" alt="<?= esc($related['title']) ?>"
                                                     style="height: 200px; object-fit: cover;">
                                                <div class="card-body p-3">
                                                    <h6 class="card-title">
                                                        <a href="<?= base_url('blog/' . $related['slug']) ?>" class="text-dark">
                                                            <?= esc(strlen($related['title']) > 60 ? substr($related['title'], 0, 60) . '...' : $related['title']) ?>
                                                        </a>
                                                    </h6>
                                                    <small class="text-muted">
                                                        <?= date('M j, Y', strtotime($related['published_at'])) ?>
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Comments Section -->
                        <div class="comments-section">
                            <h4>Comments (<?= $commentCount ?>)</h4>
                            
                            <!-- Comments List -->
                            <div class="comments-list mt-4">
                                <?php if (!empty($comments)): ?>
                                    <?php foreach ($comments as $comment): ?>
                                        <?= view('Customers/comment_item', ['comment' => $comment]) ?>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="text-center py-4">
                                        <i class="ti-comment" style="font-size: 3rem; color: #ddd;"></i>
                                        <p class="mt-2 text-muted">No comments yet. Be the first to comment!</p>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Comment Form -->
                            <div class="comment-form">
                                <h5>Leave a Comment</h5>
                                <form id="comment-form">
                                    <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
                                    <input type="hidden" name="parent_id" value="" id="parent-comment-id">
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <input type="text" name="author_name" class="form-control" 
                                                       placeholder="Your Name *" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <input type="email" name="author_email" class="form-control" 
                                                       placeholder="Your Email *" required>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group">
                                        <textarea name="comment" class="form-control" rows="5" 
                                                  placeholder="Your Comment *" required></textarea>
                                    </div>
                                    
                                    <div class="form-group">
                                        <button type="submit" class="btn_1">
                                            <span class="btn-text">Post Comment</span>
                                            <span class="btn-loading" style="display: none;">
                                                <i class="fa fa-spinner fa-spin"></i> Posting...
                                            </span>
                                        </button>
                                        <button type="button" class="btn btn-secondary ml-2" id="cancel-reply" style="display: none;">
                                            Cancel Reply
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="col-lg-4">
                    <div class="blog_right_sidebar">
                        
                        <!-- Search Widget -->
                        <aside class="single_sidebar_widget search_widget">
                            <form action="<?= base_url('blog/search') ?>" method="GET">
                                <div class="form-group">
                                    <div class="input-group mb-3">
                                        <input type="text" name="keyword" class="form-control" 
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
                                            <a href="<?= base_url('blog/category/' . urlencode($category['category'])) ?>" 
                                               class="d-flex <?= $category['category'] === $post['category'] ? 'text-primary font-weight-bold' : '' ?>">
                                                <p><?= esc($category['category']) ?></p>
                                                <p>(<?= $category['post_count'] ?>)</p>
                                            </a>
                                        </li>
                                    <?php endforeach; ?>
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
                                             alt="<?= esc($recentPost['title']) ?>">
                                        <div class="media-body">
                                            <a href="<?= base_url('blog/' . $recentPost['slug']) ?>">
                                                <h3><?= esc(strlen($recentPost['title']) > 40 ? substr($recentPost['title'], 0, 40) . '...' : $recentPost['title']) ?></h3>
                                            </a>
                                            <p><?= date('F j, Y', strtotime($recentPost['published_at'])) ?></p>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </aside>

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
    <!--================Single Blog Area =================-->
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    // Comment form submission
    $('#comment-form').on('submit', function(e) {
        e.preventDefault();
        
        const $form = $(this);
        const $submitBtn = $form.find('button[type="submit"]');
        const $btnText = $submitBtn.find('.btn-text');
        const $btnLoading = $submitBtn.find('.btn-loading');
        
        // Show loading state
        $btnText.hide();
        $btnLoading.show();
        $submitBtn.prop('disabled', true);
        
        const formData = $form.serialize() + '&<?= csrf_token() ?>=<?= csrf_hash() ?>';
        
        $.ajax({
            url: '<?= base_url('blog/add-comment') ?>',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    showToast('success', response.message);
                    
                    // Reset form
                    $form[0].reset();
                    $('#parent-comment-id').val('');
                    $('#cancel-reply').hide();
                    
                    // Reload comments section (or append new comment)
                    setTimeout(() => {
                        location.reload();
                    }, 1000);
                } else {
                    showToast('error', response.message || 'Error posting comment');
                    if (response.errors) {
                        console.error('Validation errors:', response.errors);
                    }
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', error);
                showToast('error', 'Network error. Please try again.');
            },
            complete: function() {
                // Hide loading state
                $btnText.show();
                $btnLoading.hide();
                $submitBtn.prop('disabled', false);
            }
        });
    });

    // Reply to comment
    $(document).on('click', '.reply-btn', function(e) {
        e.preventDefault();
        
        const commentId = $(this).data('comment-id');
        const authorName = $(this).data('author-name');
        
        // Set parent comment ID
        $('#parent-comment-id').val(commentId);
        
        // Update form title
        $('h5', '#comment-form').text(`Reply to ${authorName}`);
        
        // Show cancel button
        $('#cancel-reply').show();
        
        // Scroll to form
        $('html, body').animate({
            scrollTop: $('#comment-form').offset().top - 100
        }, 500);
        
        // Focus on comment textarea
        $('textarea[name="comment"]').focus();
    });

    // Cancel reply
    $('#cancel-reply').on('click', function() {
        // Reset form
        $('#parent-comment-id').val('');
        $('h5', '.comment-form').text('Leave a Comment');
        $(this).hide();
    });

    // Newsletter form
    $('#newsletter-form').on('submit', function(e) {
        e.preventDefault();
        // Add newsletter subscription logic here
        showToast('success', 'Thank you for subscribing to our newsletter!');
    });

    // Social sharing (if you want to add social sharing buttons)
    function sharePost(platform) {
        const url = encodeURIComponent(window.location.href);
        const title = encodeURIComponent('<?= addslashes($post['title']) ?>');
        const text = encodeURIComponent('<?= addslashes(substr(strip_tags($post['excerpt']), 0, 100)) ?>');
        
        let shareUrl = '';
        switch(platform) {
            case 'facebook':
                shareUrl = `https://www.facebook.com/sharer/sharer.php?u=${url}`;
                break;
            case 'twitter':
                shareUrl = `https://twitter.com/intent/tweet?url=${url}&text=${title}`;
                break;
            case 'linkedin':
                shareUrl = `https://www.linkedin.com/sharing/share-offsite/?url=${url}`;
                break;
        }
        
        if (shareUrl) {
            window.open(shareUrl, '_blank', 'width=600,height=400');
        }
    }

    // Toast notification function
    function showToast(type, message) {
        const toastClass = type === 'success' ? 'alert-success' : 'alert-danger';
        const iconClass = type === 'success' ? 'ti-check' : 'ti-close';
        
        const toast = `
            <div class="alert ${toastClass} alert-dismissible fade show position-fixed" 
                 style="top: 20px; right: 20px; z-index: 9999; min-width: 300px; max-width: 400px;">
                <i class="${iconClass}"></i> ${message}
                <button type="button" class="close" data-dismiss="alert">
                    <span>&times;</span>
                </button>
            </div>
        `;
        
        $('body').append(toast);
        
        setTimeout(() => {
            $('.alert').fadeOut(() => $('.alert').remove());
        }, 5000);
    }
});
</script>
<?= $this->endSection() ?>