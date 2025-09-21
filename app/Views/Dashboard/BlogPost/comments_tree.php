<?php if (!empty($comments)): ?>
    <?php foreach ($comments as $comment): ?>
        <div class="comment-item <?= $comment['is_approved'] == 0 ? 'comment-pending' : 'comment-approved' ?>">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <div>
                    <strong><?= esc($comment['author_name']) ?></strong>
                    <?php if ($comment['customer_id']): ?>
                        <span class="badge bg-primary">Khách hàng</span>
                    <?php endif; ?>
                    <br>
                    <small class="comment-meta">
                        <i class="fa fa-envelope"></i> <?= esc($comment['author_email']) ?> |
                        <i class="fa fa-clock-o"></i> <?= date('d/m/Y H:i', strtotime($comment['created_at'])) ?>
                    </small>
                </div>
                <div class="comment-actions">
                    <?php if ($comment['is_approved'] == 0): ?>
                        <button class="btn btn-sm btn-success btn-comment-action" 
                                onclick="approveComment(<?= $comment['id'] ?>)">
                            <i class="fa fa-check"></i> Duyệt
                        </button>
                    <?php else: ?>
                        <button class="btn btn-sm btn-warning btn-comment-action" 
                                onclick="rejectComment(<?= $comment['id'] ?>)">
                            <i class="fa fa-ban"></i> Ẩn
                        </button>
                    <?php endif; ?>
                    <button class="btn btn-sm btn-danger btn-comment-action" 
                            onclick="deleteComment(<?= $comment['id'] ?>)">
                        <i class="fa fa-trash"></i> Xóa
                    </button>
                </div>
            </div>
            
            <div class="comment-content">
                <?= nl2br(esc($comment['comment'])) ?>
            </div>

            <!-- Hiển thị replies nếu có -->
            <?php if (!empty($comment['replies'])): ?>
                <div class="comment-replies mt-3">
                    <?= view('Dashboard/BlogPost/comments_tree', ['comments' => $comment['replies']]) ?>
                </div>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
<?php endif; ?>