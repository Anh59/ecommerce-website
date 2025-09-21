<!-- Comment Item Partial -->
<div class="comment-item" id="comment-<?= $comment['id'] ?>">
    <div class="comment-header">
        <div class="comment-author-info">
            <span class="comment-author"><?= esc($comment['author_name']) ?></span>
            <?php if ($comment['customer_id']): ?>
                <span class="badge badge-primary badge-sm ml-1">Verified</span>
            <?php endif; ?>
        </div>
        <div class="comment-actions">
            <span class="comment-date"><?= date('M j, Y \a\t g:i A', strtotime($comment['created_at'])) ?></span>
            <span class="reply-btn ml-3" data-comment-id="<?= $comment['id'] ?>" data-author-name="<?= esc($comment['author_name']) ?>">
                <i class="ti-back-left"></i> Reply
            </span>
        </div>
    </div>
    
    <div class="comment-content">
        <?= nl2br(esc($comment['comment'])) ?>
    </div>
    
    <!-- Replies -->
    <?php if (!empty($comment['replies'])): ?>
        <div class="comment-replies mt-3">
            <?php foreach ($comment['replies'] as $reply): ?>
                <div class="comment-reply">
                    <?= view('Customers/partials/comment_item', ['comment' => $reply]) ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>