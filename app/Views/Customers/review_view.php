<div class="review-content">
    <h6><i class="fas fa-star"></i> Đánh giá của bạn</h6>
    <div class="card card-body bg-light">
        <div class="mb-2">
            <strong>Điểm đánh giá:</strong>
            <div class="star-rating">
                <?php for ($i = 5; $i >= 1; $i--): ?>
                    <i class="fas fa-star <?= $i <= $review['rating'] ? 'text-warning' : 'text-muted' ?>"></i>
                <?php endfor; ?>
            </div>
        </div>
        <div class="mb-2">
            <strong>Tiêu đề:</strong> <?= esc($review['title']) ?>
        </div>
        <div class="mb-2">
            <strong>Nhận xét:</strong>
            <p><?= nl2br(esc($review['comment'])) ?></p>
        </div>
        <small class="text-muted">Đánh giá vào: <?= date('d/m/Y H:i', strtotime($review['created_at'])) ?></small>
    </div>
</div>