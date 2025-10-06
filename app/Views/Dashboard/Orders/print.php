<?= $this->extend('Dashboard/layout') ?>
<?= $this->section('content') ?>

<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12 text-center">
            <h2 class="mb-1">HÓA ĐƠN BÁN HÀNG</h2>
            <p class="mb-0 text-muted">Mã đơn: <strong class="text-dark"><?= $order['order_number'] ?></strong></p>
            <p class="mb-0 text-muted">Ngày đặt: <?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></p>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="mb-0 fw-bold"><i class="fas fa-user me-2"></i>Thông tin khách hàng</h6>
                </div>
                <div class="card-body">
                    <?php if ($order['customer_id']): ?>
                        <p class="mb-1"><strong>Tên:</strong> <?= $customer['name'] ?? 'N/A' ?></p>
                        <p class="mb-1"><strong>Điện thoại:</strong> <?= $customer['phone'] ?? 'N/A' ?></p>
                        <p class="mb-0"><strong>Email:</strong> <?= $customer['email'] ?? 'N/A' ?></p>
                    <?php else: ?>
                        <p class="mb-0 text-muted">Khách vãng lai</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="mb-0 fw-bold"><i class="fas fa-receipt me-2"></i>Thông tin đơn hàng</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <p class="mb-2"><strong>Trạng thái:</strong></p>
                            <p class="mb-2"><strong>Thanh toán:</strong></p>
                            <p class="mb-0"><strong>Phương thức:</strong></p>
                        </div>
                        <div class="col-6">
                            <p class="mb-2">
                                <span class="badge bg-<?= getStatusBadge($order['status']) ?>">
                                    <?= getStatusText($order['status']) ?>
                                </span>
                            </p>
                            <p class="mb-2">
                                <span class="badge bg-<?= getPaymentStatusBadge($order['payment_status']) ?>">
                                    <?= getPaymentStatusText($order['payment_status']) ?>
                                </span>
                            </p>
                            <p class="mb-0"><?= getPaymentMethodText($order['payment_method']) ?></p>
                        </div>
                    </div>
                    
                    <!-- ✅ THÊM: Thông tin voucher gọn gàng -->
                    <?php if (!empty($order['coupon_code']) && $order['discount_amount'] > 0): ?>
                    <hr class="my-2">
                    <div class="row">
                        <div class="col-6">
                            <p class="mb-1"><strong>Mã giảm giá:</strong></p>
                            <p class="mb-0"><strong>Giảm giá:</strong></p>
                        </div>
                        <div class="col-6">
                            <p class="mb-1">
                                <span class="badge bg-success"><?= $order['coupon_code'] ?></span>
                            </p>
                            <p class="mb-0 text-success fw-bold">-<?= number_format($order['discount_amount']) ?> VND</p>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($order['tracking_number']): ?>
                    <hr class="my-2">
                    <p class="mb-0"><strong>Mã vận chuyển:</strong> <?= $order['tracking_number'] ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="mb-0 fw-bold"><i class="fas fa-boxes me-2"></i>Chi tiết sản phẩm</h6>
                </div>
                <div class="card-body p-0">
                    <table class="table table-bordered mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="5%" class="text-center">STT</th>
                                <th width="45%">Sản phẩm</th>
                                <th width="15%" class="text-center">SKU</th>
                                <th width="15%" class="text-end">Đơn giá</th>
                                <th width="10%" class="text-center">SL</th>
                                <th width="10%" class="text-end">Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($order['items'] as $index => $item): ?>
                            <tr>
                                <td class="text-center"><?= $index + 1 ?></td>
                                <td><?= $item['product_name'] ?></td>
                                <td class="text-center"><small class="text-muted"><?= $item['product_sku'] ?></small></td>
                                <td class="text-end"><?= number_format($item['price']) ?> VND</td>
                                <td class="text-center"><?= $item['quantity'] ?></td>
                                <td class="text-end fw-bold"><?= number_format($item['total']) ?> VND</td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot class="table-light">
                            <?php 
                            $originalSubtotal = $order['subtotal'] + ($order['discount_amount'] ?? 0);
                            ?>
                            <tr>
                                <td colspan="5" class="text-end fw-bold">Tạm tính:</td>
                                <td class="text-end fw-bold"><?= number_format($originalSubtotal) ?> VND</td>
                            </tr>
                            
                            <!-- ✅ THÊM: Dòng giảm giá gọn gàng -->
                            <?php if (!empty($order['coupon_code']) && $order['discount_amount'] > 0): ?>
                            <tr>
                                <td colspan="5" class="text-end text-success">
                                    Giảm giá <span class="badge bg-success"><?= $order['coupon_code'] ?></span>:
                                </td>
                                <td class="text-end text-success fw-bold">
                                    -<?= number_format($order['discount_amount']) ?> VND
                                </td>
                            </tr>
                            <tr>
                                <td colspan="5" class="text-end">Tạm tính sau giảm giá:</td>
                                <td class="text-end fw-bold"><?= number_format($order['subtotal']) ?> VND</td>
                            </tr>
                            <?php endif; ?>
                            
                            <?php if ($order['shipping_fee'] > 0): ?>
                            <tr>
                                <td colspan="5" class="text-end">Phí vận chuyển:</td>
                                <td class="text-end"><?= number_format($order['shipping_fee']) ?> VND</td>
                            </tr>
                            <?php endif; ?>
                            
                            <tr class="table-active">
                                <td colspan="5" class="text-end fw-bold fs-6">TỔNG CỘNG:</td>
                                <td class="text-end fw-bold fs-6"><?= number_format($order['total_amount']) ?> VND</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="mb-0 fw-bold"><i class="fas fa-truck me-2"></i>Địa chỉ giao hàng</h6>
                </div>
                <div class="card-body">
                    <?php
                    $shippingAddress = json_decode($order['shipping_address'], true);
                    if ($shippingAddress && is_array($shippingAddress)):
                    ?>
                        <p class="mb-1"><strong><?= $shippingAddress['name'] ?? '' ?></strong></p>
                        <p class="mb-1 text-muted"><?= $shippingAddress['phone'] ?? '' ?></p>
                        <p class="mb-1"><?= $shippingAddress['address'] ?? '' ?></p>
                        <p class="mb-0 text-muted">
                            <?= $shippingAddress['ward'] ?? '' ?>, 
                            <?= $shippingAddress['district'] ?? '' ?>, 
                            <?= $shippingAddress['city'] ?? '' ?>
                        </p>
                        <?php if (!empty($shippingAddress['postal_code'])): ?>
                        <p class="mb-0 text-muted">Mã bưu điện: <?= $shippingAddress['postal_code'] ?></p>
                        <?php endif; ?>
                    <?php else: ?>
                        <p class="text-muted mb-0">Không có thông tin địa chỉ</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="mb-0 fw-bold"><i class="fas fa-credit-card me-2"></i>Địa chỉ thanh toán</h6>
                </div>
                <div class="card-body">
                    <?php
                    $billingAddress = json_decode($order['billing_address'], true);
                    if ($billingAddress && is_array($billingAddress)):
                    ?>
                        <p class="mb-1"><strong><?= $billingAddress['name'] ?? '' ?></strong></p>
                        <p class="mb-1 text-muted"><?= $billingAddress['phone'] ?? '' ?></p>
                        <p class="mb-1"><?= $billingAddress['address'] ?? '' ?></p>
                        <p class="mb-0 text-muted">
                            <?= $billingAddress['ward'] ?? '' ?>, 
                            <?= $billingAddress['district'] ?? '' ?>, 
                            <?= $billingAddress['city'] ?? '' ?>
                        </p>
                        <?php if (!empty($billingAddress['postal_code'])): ?>
                        <p class="mb-0 text-muted">Mã bưu điện: <?= $billingAddress['postal_code'] ?></p>
                        <?php endif; ?>
                    <?php else: ?>
                        <p class="text-muted mb-0">Không có thông tin địa chỉ</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <?php if (!empty($order['notes'])): ?>
    <div class="row mt-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="mb-0 fw-bold"><i class="fas fa-sticky-note me-2"></i>Ghi chú đơn hàng</h6>
                </div>
                <div class="card-body">
                    <p class="mb-0"><?= nl2br(htmlspecialchars($order['notes'])) ?></p>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <div class="row mt-4">
        <div class="col-12 text-center">
            <button class="btn btn-primary px-4" onclick="window.print()">
                <i class="fas fa-print me-2"></i> In hóa đơn
            </button>
            <a href="<?= site_url('Dashboard/orders') ?>" class="btn btn-outline-secondary px-4">
                <i class="fas fa-arrow-left me-2"></i> Quay lại
            </a>
        </div>
    </div>
</div>

<style>
@media print {
    .btn { display: none; }
    .card { 
        border: 1px solid #dee2e6 !important;
        box-shadow: none !important;
    }
    .card-header { 
        background: #f8f9fa !important;
        border-bottom: 1px solid #dee2e6;
    }
    body { 
        font-size: 12px; 
        background: white !important;
        color: #000 !important;
    }
    .container-fluid {
        padding: 0;
        margin: 0;
    }
    .text-success {
        color: #198754 !important;
    }
    .bg-success {
        background-color: #198754 !important;
        color: white !important;
    }
    .table-light {
        background-color: #f8f9fa !important;
    }
    .table-active {
        background-color: #e9ecef !important;
    }
}

/* ✅ Style cho màn hình */
.card {
    border-radius: 8px;
}
.card-header {
    border-radius: 8px 8px 0 0 !important;
    font-size: 0.9rem;
}
.table th {
    border-bottom: 2px solid #dee2e6;
    font-size: 0.85rem;
}
.table td {
    font-size: 0.85rem;
    vertical-align: middle;
}
.badge {
    font-size: 0.75em;
    padding: 0.35em 0.65em;
}
.btn {
    border-radius: 6px;
    font-weight: 500;
}
</style>

<?php
// Helper functions
function getStatusText($status) {
    $statusMap = [
        'pending' => 'Chờ xử lý',
        'processing' => 'Đang xử lý',
        'shipped' => 'Đang giao',
        'delivered' => 'Đã giao',
        'cancelled' => 'Đã hủy'
    ];
    return $statusMap[$status] ?? $status;
}

function getStatusBadge($status) {
    $badgeMap = [
        'pending' => 'warning',
        'processing' => 'info',
        'shipped' => 'primary',
        'delivered' => 'success',
        'cancelled' => 'danger'
    ];
    return $badgeMap[$status] ?? 'secondary';
}

function getPaymentStatusText($status) {
    $statusMap = [
        'pending' => 'Chờ thanh toán',
        'paid' => 'Đã thanh toán',
        'failed' => 'Thất bại',
        'refunded' => 'Đã hoàn tiền'
    ];
    return $statusMap[$status] ?? $status;
}

function getPaymentStatusBadge($status) {
    $badgeMap = [
        'pending' => 'warning',
        'paid' => 'success',
        'failed' => 'danger',
        'refunded' => 'secondary'
    ];
    return $badgeMap[$status] ?? 'secondary';
}

function getPaymentMethodText($method) {
    $methodMap = [
        'cod' => 'COD',
        'momo' => 'Ví MoMo',
        'bank_transfer' => 'Chuyển khoản'
    ];
    return $methodMap[$method] ?? $method;
}
?>

<?= $this->endSection() ?>