<?= $this->extend('Customers/layout/main') ?>

<?= $this->section('styles') ?>
<style>
.order-success-container { 
    background: #f8f9fa; 
    min-height: 80vh; 
    padding: 60px 0; 
    display: flex; 
    align-items: center; 
}
.success-card { 
    background: white; 
    border-radius: 15px; 
    padding: 40px; 
    text-align: center; 
    box-shadow: 0 10px 30px rgba(0,0,0,0.1); 
    max-width: 700px; 
    margin: 0 auto; 
}
.success-icon { 
    width: 80px; 
    height: 80px; 
    background: #28a745; 
    border-radius: 50%; 
    display: flex; 
    align-items: center; 
    justify-content: center; 
    margin: 0 auto 20px; 
    animation: pulse 2s infinite; 
}
.success-icon i { 
    font-size: 40px; 
    color: white; 
}
@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.1); }
    100% { transform: scale(1); }
}
.order-details { 
    background: #f8f9fa; 
    border-radius: 8px; 
    padding: 20px; 
    margin: 30px 0; 
    text-align: left;
}
.order-summary-item { 
    display: flex; 
    justify-content: space-between; 
    margin-bottom: 10px; 
}
.order-summary-item:last-child { 
    margin-bottom: 0; 
    border-top: 1px solid #ddd; 
    padding-top: 10px; 
    font-weight: 600; 
}
.order-items { 
    background: #f8f9fa; 
    border-radius: 8px; 
    padding: 20px; 
    margin: 20px 0; 
}
.order-item { 
    display: flex; 
    align-items: center; 
    padding: 10px 0; 
    border-bottom: 1px solid #e9ecef; 
}
.order-item:last-child { border-bottom: none; }
.order-item img { 
    width: 50px; 
    height: 50px; 
    object-fit: cover; 
    border-radius: 4px; 
    margin-right: 15px; 
}
.order-item-info { flex: 1; }
.order-item-name { font-weight: 600; margin-bottom: 5px; }
.order-item-details { font-size: 14px; color: #666; }
.action-buttons { margin-top: 30px; }
.btn-track-order { 
    background: #007bff; 
    color: white; 
    padding: 12px 25px; 
    border: none; 
    border-radius: 5px; 
    margin: 0 10px; 
    text-decoration: none; 
    display: inline-block; 
}
.btn-track-order:hover { 
    background: #0056b3; 
    color: white; 
    text-decoration: none; 
}
.btn-continue-shopping { 
    background: #28a745; 
    color: white; 
    padding: 12px 25px; 
    border: none; 
    border-radius: 5px; 
    margin: 0 10px; 
    text-decoration: none; 
    display: inline-block; 
}
.btn-continue-shopping:hover { 
    background: #218838; 
    color: white; 
    text-decoration: none; 
}
.payment-info { 
    background: #e7f3ff; 
    border: 1px solid #b8daff; 
    border-radius: 5px; 
    padding: 15px; 
    margin: 20px 0; 
}
.coupon-info { 
    background: #d4edda; 
    border: 1px solid #c3e6cb; 
    border-radius: 5px; 
    padding: 15px; 
    margin: 20px 0; 
}
.info-row {
    margin-bottom: 10px;
}
.info-row:last-child {
    margin-bottom: 0;
}
.status-badge {
    padding: 4px 12px;
    border-radius: 15px;
    font-size: 12px;
    font-weight: 600;
}
.status-pending {
    background: #fff3cd;
    color: #856404;
}
.status-processing {
    background: #cce5ff;
    color: #004085;
}
.status-info {
    background: #d1ecf1;
    color: #0c5460;
}
.status-success {
    background: #d4edda;
    color: #155724;
}
.status-danger {
    background: #f8d7da;
    color: #721c24;
}
.coupon-badge {
    background: #28a745;
    color: white;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 600;
    margin-left: 10px;
}
.discount-amount {
    color: #28a745;
    font-weight: 600;
}
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<section class="breadcrumb breadcrumb_bg">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="breadcrumb_iner">
                        <div class="breadcrumb_iner_item">
                            <h2>Thành công</h2>
                            <p>Trang chủ <span>-</span> Thành công</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
<div class="order-success-container">
    <div class="container">
        <div class="success-card">
            <div class="success-icon">
                <i class="ti-check"></i>
            </div>
            
            <h1 class="text-success mb-3">Đặt hàng thành công!</h1>
            <p class="text-muted mb-4">
                Cảm ơn bạn đã đặt hàng tại cửa hàng của chúng tôi. 
                Đơn hàng của bạn đã được ghi nhận và đang được xử lý.
            </p>

            <!-- Order Details -->
            <div class="order-details">
                <h5 class="mb-3">Thông tin đơn hàng</h5>
                <div class="row">
                    <div class="col-md-6">
                        <div class="info-row">
                            <strong>Mã đơn hàng:</strong> 
                            <span class="text-primary"><?= esc($order['order_number']) ?></span>
                        </div>
                        <div class="info-row">
                            <strong>Ngày đặt:</strong> 
                            <?= date('d/m/Y H:i', strtotime($order['created_at'])) ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-row">
                            <strong>Phương thức thanh toán:</strong> 
                            <?php
                            $paymentMethods = [
                                'cod' => 'Thanh toán khi nhận hàng',
                                'momo' => 'Ví MoMo',
                                'bank_transfer' => 'Chuyển khoản ngân hàng'
                            ];
                            echo $paymentMethods[$order['payment_method']] ?? $order['payment_method'];
                            ?>
                        </div>
                        <div class="info-row">
                            <strong>Trạng thái:</strong> 
                            <?php
                            $statusLabels = [
                                'pending' => ['label' => 'Đang xử lý', 'class' => 'status-pending'],
                                'processing' => ['label' => 'Đang xử lý', 'class' => 'status-processing'],
                                'shipped' => ['label' => 'Đang giao', 'class' => 'status-info'],
                                'delivered' => ['label' => 'Đã giao', 'class' => 'status-success'],
                                'cancelled' => ['label' => 'Đã hủy', 'class' => 'status-danger']
                            ];
                            $status = $statusLabels[$order['status']] ?? ['label' => ucfirst($order['status']), 'class' => 'status-pending'];
                            ?>
                            <span class="status-badge <?= $status['class'] ?>"><?= $status['label'] ?></span>
                        </div>
                    </div>
                </div>
                
                <?php if (!empty($order['shipping_method'])): ?>
                    <div class="info-row mt-2">
                        <strong>Phương thức giao hàng:</strong> 
                        <?php
                        $shippingMethods = [
                            'standard' => 'Giao hàng tiêu chuẩn (3-5 ngày)',
                            'express' => 'Giao hàng nhanh (1-2 ngày)',
                            'same_day' => 'Giao trong ngày'
                        ];
                        echo $shippingMethods[$order['shipping_method']] ?? ucfirst($order['shipping_method']);
                        ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- ✅ THÊM: Hiển thị thông tin voucher nếu có -->
            <?php if (!empty($order['coupon_code']) && $order['discount_amount'] > 0): ?>
                <div class="coupon-info">
                    <h6><i class="ti-tag mr-2"></i>Thông tin giảm giá</h6>
                    <div class="text-left">
                        <div class="info-row">
                            <strong>Mã giảm giá:</strong> 
                            <span class="coupon-badge"><?= esc($order['coupon_code']) ?></span>
                        </div>
                        <div class="info-row">
                            <strong>Số tiền giảm:</strong> 
                            <span class="discount-amount">-<?= number_format($order['discount_amount']) ?>₫</span>
                        </div>
                        <div class="info-row">
                            <small class="text-muted">
                                <i class="ti-info-alt mr-1"></i>
                                Voucher đã được áp dụng thành công cho đơn hàng này
                            </small>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Payment Info for Bank Transfer -->
            <?php if ($order['payment_method'] === 'bank_transfer'): ?>
                <div class="payment-info">
                    <h6><i class="ti-info-alt mr-2"></i>Thông tin chuyển khoản</h6>
                    <div class="text-left">
                        <div class="info-row"><strong>Ngân hàng:</strong> Ngân hàng ABC</div>
                        <div class="info-row"><strong>Số tài khoản:</strong> 1234567890</div>
                        <div class="info-row"><strong>Chủ tài khoản:</strong> CONG TY XYZ</div>
                        <div class="info-row">
                            <strong>Nội dung:</strong> <?= esc($order['order_number']) ?>
                        </div>
                        <div class="info-row">
                            <strong>Số tiền:</strong> <?= number_format($order['total_amount']) ?>₫
                        </div>
                    </div>
                    <small class="text-muted mt-2 d-block">
                        Vui lòng chuyển khoản theo đúng nội dung để đơn hàng được xử lý nhanh chóng
                    </small>
                </div>
            <?php endif; ?>

            <!-- Order Items -->
            <?php if (!empty($orderItems)): ?>
                <div class="order-items">
                    <h5 class="mb-3">Sản phẩm đã đặt</h5>
                    <?php foreach ($orderItems as $item): ?>
                        <div class="order-item">
                            <img src="<?= base_url($item['main_image'] ?? 'aranoz-master/img/product/product_1.png') ?>" 
                                 alt="<?= esc($item['product_name']) ?>">
                            <div class="order-item-info">
                                <div class="order-item-name"><?= esc($item['product_name']) ?></div>
                                <div class="order-item-details">
                                    <?= $item['quantity'] ?> × <?= number_format($item['price']) ?>₫
                                    <?php if (!empty($item['product_sku'])): ?>
                                        <span class="text-muted">(SKU: <?= esc($item['product_sku']) ?>)</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="order-item-total font-weight-bold">
                                <?= number_format($item['total']) ?>₫
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <!-- Order Summary -->
            <div class="order-details">
                <h5 class="mb-3">Tổng kết đơn hàng</h5>
                <div class="order-summary-item">
                    <span>Tạm tính:</span>
                    <span><?= number_format($order['subtotal'] + $order['discount_amount']) ?>₫</span>
                </div>
                
                <!-- ✅ THÊM: Hiển thị discount nếu có -->
                <?php if ($order['discount_amount'] > 0): ?>
                    <div class="order-summary-item">
                        <span>Giảm giá:</span>
                        <span class="discount-amount">-<?= number_format($order['discount_amount']) ?>₫</span>
                    </div>
                    <div class="order-summary-item">
                        <span>Tạm tính sau giảm:</span>
                        <span><?= number_format($order['subtotal']) ?>₫</span>
                    </div>
                <?php endif; ?>
                
                <div class="order-summary-item">
                    <span>Phí vận chuyển:</span>
                    <span><?= $order['shipping_fee'] > 0 ? number_format($order['shipping_fee']) . '₫' : 'Miễn phí' ?></span>
                </div>
                <div class="order-summary-item">
                    <span>Tổng cộng:</span>
                    <span class="text-primary"><?= number_format($order['total_amount']) ?>₫</span>
                </div>
            </div>

            <!-- Shipping Address -->
            <?php 
            $shippingAddress = json_decode($order['shipping_address'], true);
            if ($shippingAddress): 
            ?>
                <div class="order-details">
                    <h5 class="mb-3">Địa chỉ giao hàng</h5>
                    <div class="text-left">
                        <div class="info-row">
                            <strong>Người nhận:</strong> <?= esc($shippingAddress['name']) ?>
                        </div>
                        <div class="info-row">
                            <strong>Số điện thoại:</strong> <?= esc($shippingAddress['phone']) ?>
                        </div>
                        <div class="info-row">
                            <strong>Địa chỉ:</strong> <?= esc($shippingAddress['address']) ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Notes -->
            <?php if (!empty($order['notes'])): ?>
                <div class="order-details">
                    <h5 class="mb-3">Ghi chú đơn hàng</h5>
                    <p class="text-left mb-0"><?= nl2br(esc($order['notes'])) ?></p>
                </div>
            <?php endif; ?>

            <!-- Action Buttons -->
            <div class="action-buttons">
                <a href="#" class="btn-track-order">
                    <i class="ti-package mr-2"></i>Theo dõi đơn hàng
                </a>
                <a href="<?= base_url('/products') ?>" class="btn-continue-shopping">
                    <i class="ti-shopping-cart mr-2"></i>Tiếp tục mua sắm
                </a>
            </div>

            <!-- Additional Info -->
            <div class="mt-4">
                <p class="text-muted small">
                    <i class="ti-info-alt mr-1"></i>
                    <?php if ($order['payment_method'] === 'cod'): ?>
                        Đơn hàng của bạn sẽ được giao trong thời gian sớm nhất. Vui lòng chuẩn bị tiền mặt để thanh toán khi nhận hàng.
                    <?php else: ?>
                        Chúng tôi đã gửi email xác nhận đến địa chỉ email của bạn.
                    <?php endif; ?>
                    Nếu bạn có bất kỳ câu hỏi nào, vui lòng liên hệ với chúng tôi qua hotline: 
                    <strong>1900-1234</strong>
                </p>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    // Auto redirect after 30 seconds (optional)
    let autoRedirectTimer = setTimeout(function() {
        if (confirm('Bạn có muốn tiếp tục mua sắm không?')) {
            window.location.href = '<?= base_url('/products') ?>';
        }
    }, 30000);

    // Clear timer if user interacts with the page
    $(document).on('click scroll', function() {
        clearTimeout(autoRedirectTimer);
    });

    console.log('Order success page loaded');
});
</script>
<?= $this->endSection() ?>