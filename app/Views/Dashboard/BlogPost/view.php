<?= $this->extend('Dashboard/layout') ?>
<?= $this->section('content') ?>

<meta name="csrf-token" content="<?= csrf_hash() ?>">

<div class="row">
    <div class="col-md-12">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="<?= site_url('Dashboard/blog-posts') ?>">
                        <i class="fa fa-list"></i> Danh sách bài viết
                    </a>
                </li>
                <li class="breadcrumb-item active">Chi tiết bài viết</li>
            </ol>
        </nav>

        <!-- Post Header -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Chi tiết bài viết</h4>
                <div class="btn-group">
                    <a href="<?= site_url('Dashboard/blog-posts/' . $post['id'] . '/edit') ?>" 
                       class="btn btn-primary btn-sm">
                        <i class="fa fa-edit"></i> Sửa bài viết
                    </a>
                    <button type="button" class="btn btn-danger btn-sm" onclick="deletePost(<?= $post['id'] ?>)">
                        <i class="fa fa-trash"></i> Xóa
                    </button>
                </div>
            </div>
        </div>

        <!-- Post Content -->
        <div class="card mt-3">
            <div class="card-body">
                <!-- Post Meta Info -->
                <div class="row mb-4">
                    <div class="col-md-8">
                        <h1 class="post-title"><?= esc($post['title']) ?></h1>
                        <p class="text-muted mb-2">
                            <i class="fa fa-user"></i> Tác giả: <strong><?= esc($post['author_name']) ?></strong> |
                            <i class="fa fa-calendar"></i> Ngày xuất bản: 
                            <strong><?= $post['published_at'] ? date('d/m/Y H:i', strtotime($post['published_at'])) : 'Chưa xuất bản' ?></strong> |
                            <i class="fa fa-eye"></i> Lượt xem: <strong><?= number_format($post['view_count'] ?? 0) ?></strong>
                        </p>
                        
                        <!-- Status and Category -->
                        <div class="mb-3">
                            <?php
                            $statusColors = [
                                'draft' => 'secondary',
                                'published' => 'success',
                                'archived' => 'warning'
                            ];
                            $statusTexts = [
                                'draft' => 'Nháp',
                                'published' => 'Đã xuất bản', 
                                'archived' => 'Lưu trữ'
                            ];
                            ?>
                            <span class="badge bg-<?= $statusColors[$post['status']] ?> me-2">
                                <?= $statusTexts[$post['status']] ?>
                            </span>
                            <span class="badge bg-info me-2">
                                <i class="fa fa-folder"></i> <?= esc($post['category']) ?>
                            </span>
                            <?php if ($post['is_featured']): ?>
                            <span class="badge bg-warning">
                                <i class="fa fa-star"></i> Nổi bật
                            </span>
                            <?php endif; ?>
                        </div>

                        <!-- Tags -->
                        <?php if (!empty($post['tags'])): ?>
                        <div class="mb-3">
                            <strong>Tags:</strong>
                            <?php foreach ($post['tags'] as $tag): ?>
                            <span class="badge bg-light text-dark me-1">#<?= esc($tag) ?></span>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>

                        <!-- Reading Time -->
                        <?php if (!empty($post['reading_time'])): ?>
                        <div class="mb-3">
                            <i class="fa fa-clock-o"></i> Thời gian đọc: <strong><?= $post['reading_time'] ?> phút</strong>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="col-md-4">
                        <!-- Featured Image -->
                        <?php if (!empty($post['featured_image'])): ?>
                        <div class="featured-image text-center">
                            <img src="<?= base_url($post['featured_image']) ?>" 
                                 class="img-fluid rounded shadow-sm" 
                                 alt="<?= esc($post['image_alt'] ?? $post['title']) ?>"
                                 style="max-height: 300px; object-fit: cover;">
                            <?php if (!empty($post['image_alt'])): ?>
                            <small class="text-muted d-block mt-2">Alt: <?= esc($post['image_alt']) ?></small>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <hr>

                <!-- Post Excerpt -->
                <?php if (!empty($post['excerpt'])): ?>
                <div class="alert alert-info">
                    <h5><i class="fa fa-info-circle"></i> Tóm tắt</h5>
                    <p class="mb-0"><?= nl2br(esc($post['excerpt'])) ?></p>
                </div>
                <?php endif; ?>

                <!-- Post Content -->
                <div class="post-content">
                    <h5><i class="fa fa-file-text"></i> Nội dung bài viết</h5>
                    <div class="content-body">
                        <?= $post['content'] ?>
                    </div>
                </div>

                <!-- Gallery Images -->
                <?php if (!empty($post['gallery_images'])): ?>
                <hr>
                <div class="gallery-section">
                    <h5><i class="fa fa-images"></i> Thư viện ảnh</h5>
                    <div class="row">
                        <?php foreach ($post['gallery_images'] as $index => $image): ?>
                        <div class="col-md-3 col-sm-4 col-6 mb-3">
                            <a href="<?= base_url($image) ?>" data-bs-toggle="modal" data-bs-target="#imageModal" 
                               onclick="showImageModal('<?= base_url($image) ?>', '<?= "Gallery Image " . ($index + 1) ?>')">
                                <img src="<?= base_url($image) ?>" class="img-fluid rounded shadow-sm gallery-thumb" 
                                     alt="Gallery Image <?= $index + 1 ?>"
                                     style="height: 150px; width: 100%; object-fit: cover; cursor: pointer;">
                            </a>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- SEO Information -->
                <?php if (!empty($post['meta_title']) || !empty($post['meta_description'])): ?>
                <hr>
                <div class="seo-section">
                    <h5><i class="fa fa-search"></i> Thông tin SEO</h5>
                    <div class="card bg-light">
                        <div class="card-body">
                            <?php if (!empty($post['meta_title'])): ?>
                            <p><strong>Meta Title:</strong> <?= esc($post['meta_title']) ?></p>
                            <?php endif; ?>
                            <?php if (!empty($post['meta_description'])): ?>
                            <p><strong>Meta Description:</strong> <?= esc($post['meta_description']) ?></p>
                            <?php endif; ?>
                            <p><strong>URL Slug:</strong> <code><?= esc($post['slug']) ?></code></p>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Comments Section -->
        <div class="card mt-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fa fa-comments"></i> Bình luận 
                    <span class="badge bg-primary"><?= $commentCount ?></span>
                </h5>
                <button type="button" class="btn btn-sm btn-secondary" onclick="refreshComments()">
                    <i class="fa fa-refresh"></i> Làm mới
                </button>
            </div>
            <div class="card-body">
                <?php if (empty($comments)): ?>
                <div class="text-center py-4">
                    <i class="fa fa-comment-o fa-3x text-muted mb-3"></i>
                    <p class="text-muted">Chưa có bình luận nào cho bài viết này.</p>
                </div>
                <?php else: ?>
                <div id="comments-container">
                    <?= $this->include('Dashboard/BlogPost/comments_tree', ['comments' => $comments]) ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Image Modal -->
<div class="modal fade" id="imageModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="imageModalTitle">Xem ảnh</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <img id="modalImage" src="" class="img-fluid rounded" alt="">
            </div>
        </div>
    </div>
</div>

<!-- CSS -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<style>
.post-title {
    color: #2c3e50;
    font-weight: 600;
    line-height: 1.3;
}

.content-body {
    font-size: 16px;
    line-height: 1.8;
    color: #444;
}

.content-body img {
    max-width: 100%;
    height: auto;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    margin: 15px 0;
}

.content-body p {
    margin-bottom: 1.2em;
    text-align: justify;
}

.content-body h1, .content-body h2, .content-body h3, .content-body h4, .content-body h5, .content-body h6 {
    margin-top: 2em;
    margin-bottom: 1em;
    color: #2c3e50;
}

.gallery-thumb:hover {
    transform: scale(1.05);
    transition: transform 0.2s ease;
}

.comment-item {
    border-left: 3px solid #e9ecef;
    padding-left: 15px;
    margin-bottom: 20px;
}

.comment-reply {
    margin-left: 30px;
    border-left: 2px solid #dee2e6;
}

.comment-meta {
    color: #6c757d;
    font-size: 0.9em;
}

.comment-content {
    margin-top: 8px;
    line-height: 1.6;
}

.comment-actions {
    margin-top: 10px;
}

.btn-comment-action {
    font-size: 0.8em;
    padding: 2px 8px;
    margin-right: 5px;
}

.comment-pending {
    background-color: #fff3cd;
    border-left-color: #ffc107;
}

.comment-approved {
    background-color: #d1edff;
    border-left-color: #0d6efd;
}
</style>

<script>
function showImageModal(imageSrc, title) {
    $('#modalImage').attr('src', imageSrc);
    $('#imageModalTitle').text(title);
}

function deletePost(id) {
    if (!confirm('Bạn có chắc muốn xóa bài viết này?')) return;
    
    $.post(`<?= site_url('Dashboard/blog-posts') ?>/${id}/delete`, {
        '<?= csrf_token() ?>': $('meta[name="csrf-token"]').attr('content')
    })
    .done(function(res) {
        if (res.status === 'success') {
            toastr.success(res.message);
            setTimeout(() => {
                window.location.href = '<?= site_url('Dashboard/blog-posts') ?>';
            }, 1500);
        } else {
            toastr.error(res.message);
        }
    })
    .fail(() => toastr.error('Lỗi khi xóa bài viết'));
}

function refreshComments() {
    location.reload();
}

function approveComment(id) {
    $.post(`<?= site_url('Dashboard/blog-comments') ?>/${id}/approve`, {
        '<?= csrf_token() ?>': $('meta[name="csrf-token"]').attr('content')
    })
    .done(function(res) {
        if (res.status === 'success') {
            toastr.success(res.message);
            refreshComments();
        } else {
            toastr.error(res.message);
        }
    })
    .fail(() => toastr.error('Lỗi khi duyệt bình luận'));
}

function rejectComment(id) {
    if (!confirm('Bạn có chắc muốn từ chối bình luận này?')) return;
    
    $.post(`<?= site_url('Dashboard/blog-comments') ?>/${id}/reject`, {
        '<?= csrf_token() ?>': $('meta[name="csrf-token"]').attr('content')
    })
    .done(function(res) {
        if (res.status === 'success') {
            toastr.success(res.message);
            refreshComments();
        } else {
            toastr.error(res.message);
        }
    })
    .fail(() => toastr.error('Lỗi khi từ chối bình luận'));
}

function deleteComment(id) {
    if (!confirm('Bạn có chắc muốn xóa bình luận này?')) return;
    
    $.post(`<?= site_url('Dashboard/blog-comments') ?>/${id}/delete`, {
        '<?= csrf_token() ?>': $('meta[name="csrf-token"]').attr('content')
    })
    .done(function(res) {
        if (res.status === 'success') {
            toastr.success(res.message);
            refreshComments();
        } else {
            toastr.error(res.message);
        }
    })
    .fail(() => toastr.error('Lỗi khi xóa bình luận'));
}
</script>

<?= $this->endSection() ?>