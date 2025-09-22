<div class="order-detail">
    <!-- Order Info -->
    <div class="row mb-4">
        <div class="col-md-6">
            <h6><i class="fas fa-receipt"></i> Thông tin đơn hàng</h6>
            <table class="table table-sm table-borderless">
                <tr>
                    <td><strong>Mã đơn hàng:</strong></td>
                    <td><?= esc($order['order_number']) ?></td>
                </tr>
                <tr>
                    <td><strong>Ngày đặt:</strong></td>
                    <td><?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></td>
                </tr>
                <tr>
                    <td><strong>Trạng thái:</strong></td>
                    <td>
                        <?php 
                        $statusClass = '';
                        $statusText = '';
                        switch($order['status']) {
                            case 'pending':
                                $statusClass = 'badge-warning';
                                $statusText = 'Chờ xử lý';
                                break;
                            case 'processing':
                                $statusClass = 'badge-info';
                                $statusText = 'Đang xử lý';
                                break;
                            case 'shipped':
                                $statusClass = 'badge-primary';
                                $statusText = 'Đã gửi hàng';
                                break;
                            case 'delivered':
                                $statusClass = 'badge-success';
                                $statusText = 'Đã giao hàng';
                                break;
                            case 'cancelled':
                                $statusClass = 'badge-danger';
                                $statusText = 'Đã hủy';
                                break;
                        }
                        ?>
                        <span class="badge <?= $statusClass ?>"><?= $statusText ?></span>
                    </td>
                </tr>
                <?php if($order['tracking_number']): ?>
                <tr>
                    <td><strong>Mã vận đơn:</strong></td>
                    <td><?= esc($order['tracking_number']) ?></td>
                </tr>
                <?php endif; ?>
            </table>
        </div>
        <div class="col-md-6">
            <h6><i class="fas fa-credit-card"></i> Thông tin thanh toán</h6>
            <table class="table table-sm table-borderless">
                <tr>
                    <td><strong>Phương thức:</strong></td>
                    <td>
                        <?php 
                        switch($order['payment_method']) {
                            case 'cod':
                                echo 'Thanh toán khi nhận hàng (COD)';
                                break;
                            case 'momo':
                                echo 'MoMo';
                                break;
                            case 'bank_transfer':
                                echo 'Chuyển khoản ngân hàng';
                                break;
                            default:
                                echo ucfirst($order['payment_method']);
                        }
                        ?>
                    </td>
                </tr>
                <tr>
                    <td><strong>Trạng thái thanh toán:</strong></td>
                    <td>
                        <?php 
                        $paymentClass = '';
                        $paymentText = '';
                        switch($order['payment_status']) {
                            case 'pending':
                                $paymentClass = 'badge-warning';
                                $paymentText = 'Chờ thanh toán';
                                break;
                            case 'paid':
                                $paymentClass = 'badge-success';
                                $paymentText = 'Đã thanh toán';
                                break;
                            case 'failed':
                                $paymentClass = 'badge-danger';
                                $paymentText = 'Thất bại';
                                break;
                            case 'refunded':
                                $paymentClass = 'badge-info';
                                $paymentText = 'Đã hoàn tiền';
                                break;
                        }
                        ?>
                        <span class="badge <?= $paymentClass ?>"><?= $paymentText ?></span>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <!-- Shipping Address -->
    <?php if($order['shipping_address']): ?>
    <div class="mb-4">
        <h6><i class="fas fa-shipping-fast"></i> Địa chỉ giao hàng</h6>
        <?php 
        $shippingAddress = json_decode($order['shipping_address'], true);
        if($shippingAddress): ?>
            <div class="card card-body bg-light">
                <strong><?= esc($shippingAddress['name'] ?? '') ?></strong><br>
                <?= esc($shippingAddress['phone'] ?? '') ?><br>
                <?= esc($shippingAddress['address'] ?? '') ?><br>
                <?= esc($shippingAddress['ward'] ?? '') ?>, <?= esc($shippingAddress['district'] ?? '') ?>, <?= esc($shippingAddress['city'] ?? '') ?>
                <?php if(isset($shippingAddress['postal_code'])): ?>
                <br>Mã bưu điện: <?= esc($shippingAddress['postal_code']) ?>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <!-- Order Items -->
    <div class="mb-4">
        <h6><i class="fas fa-box"></i> Sản phẩm đã đặt</h6>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="thead-light">
                    <tr>
                        <th width="80">Ảnh</th>
                        <th>Sản phẩm</th>
                        <th width="100">Đơn giá</th>
                        <th width="80">Số lượng</th>
                        <th width="120">Thành tiền</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($orderItems as $item): ?>
                    <tr>
                        <td>
                            <img src="<?= $item['main_image'] ? base_url($item['main_image']) : base_url('aranoz-master/img/no-image.png') ?>" 
                                 alt="<?= esc($item['product_name']) ?>" 
                                 class="img-fluid" 
                                 style="max-width: 60px; height: 60px; object-fit: cover;">
                        </td>
                        <td>
                            <strong><?= esc($item['product_name']) ?></strong>
                            <?php if($item['product_sku']): ?>
                            <br><small class="text-muted">SKU: <?= esc($item['product_sku']) ?></small>
                            <?php endif; ?>
                        </td>
                        <td><?= number_format($item['price'], 0, ',', '.') ?>đ</td>
                        <td class="text-center"><?= $item['quantity'] ?></td>
                        <td><strong><?= number_format($item['total'], 0, ',', '.') ?>đ</strong></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Order Summary -->
    <div class="row">
        <div class="col-md-6 offset-md-6">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-calculator"></i> Tổng kết đơn hàng</h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <td>Tạm tính:</td>
                            <td class="text-right"><strong><?= number_format($order['subtotal'], 0, ',', '.') ?>đ</strong></td>
                        </tr>
                        <tr>
                            <td>Phí vận chuyển:</td>
                            <td class="text-right"><strong><?= number_format($order['shipping_fee'], 0, ',', '.') ?>đ</strong></td>
                        </tr>
                        <tr class="border-top">
                            <td><strong>Tổng cộng:</strong></td>
                            <td class="text-right"><strong class="text-primary"><?= number_format($order['total_amount'], 0, ',', '.') ?>đ</strong></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Notes -->
    <?php if($order['notes']): ?>
    <div class="mt-4">
        <h6><i class="fas fa-sticky-note"></i> Ghi chú</h6>
        <div class="card card-body bg-light">
            <?= nl2br(esc($order['notes'])) ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Timeline (if applicable) -->
    <?php if($order['shipped_at'] || $order['delivered_at']): ?>
    <div class="mt-4">
        <h6><i class="fas fa-timeline"></i> Lịch sử đơn hàng</h6>
        <div class="timeline">
            <div class="timeline-item">
                <div class="timeline-marker bg-success"></div>
                <div class="timeline-content">
                    <strong>Đặt hàng thành công</strong>
                    <br><small class="text-muted"><?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></small>
                </div>
            </div>
            <?php if($order['shipped_at']): ?>
            <div class="timeline-item">
                <div class="timeline-marker bg-info"></div>
                <div class="timeline-content">
                    <strong>Đã gửi hàng</strong>
                    <br><small class="text-muted"><?= date('d/m/Y H:i', strtotime($order['shipped_at'])) ?></small>
                </div>
            </div>
            <?php endif; ?>
            <?php if($order['delivered_at']): ?>
            <div class="timeline-item">
                <div class="timeline-marker bg-success"></div>
                <div class="timeline-content">
                    <strong>Đã giao hàng</strong>
                    <br><small class="text-muted"><?= date('d/m/Y H:i', strtotime($order['delivered_at'])) ?></small>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #dee2e6;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
}

.timeline-marker {
    position: absolute;
    left: -23px;
    top: 5px;
    width: 16px;
    height: 16px;
    border-radius: 50%;
    border: 3px solid #fff;
    box-shadow: 0 0 0 2px #dee2e6;
}

.timeline-content {
    background: #f8f9fa;
    padding: 10px 15px;
    border-radius: 5px;
    border-left: 3px solid #007bff;
}
</style>