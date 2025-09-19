<?= $this->extend('Dashboard/layout') ?>
<?= $this->section('content') ?>

<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12 text-center">
            <h2>HÓA ĐƠN BÁN HÀNG</h2>
            <p class="mb-0">Mã đơn: <strong><?= $order['order_number'] ?></strong></p>
            <p>Ngày đặt: <?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></p>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Thông tin khách hàng</h5>
                </div>
                <div class="card-body">
                    <?php if ($order['customer_id']): ?>
                        <p class="mb-1"><strong>Tên:</strong> <?= $customer['name'] ?? 'N/A' ?></p>
                        <p class="mb-1"><strong>Điện thoại:</strong> <?= $customer['phone'] ?? 'N/A' ?></p>
                        <p class="mb-1"><strong>Email:</strong> <?= $customer['email'] ?? 'N/A' ?></p>
                    <?php else: ?>
                        <p class="mb-0 text-muted">Khách vãng lai</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Thông tin đơn hàng</h5>
                </div>
                <div class="card-body">
                    <p class="mb-1"><strong>Trạng thái:</strong> 
                        <span class="badge bg-<?= getStatusBadge($order['status']) ?>">
                            <?= getStatusText($order['status']) ?>
                        </span>
                    </p>
                    <p class="mb-1"><strong>Thanh toán:</strong> 
                        <span class="badge bg-<?= getPaymentStatusBadge($order['payment_status']) ?>">
                            <?= getPaymentStatusText($order['payment_status']) ?>
                        </span>
                    </p>
                    <p class="mb-1"><strong>Phương thức:</strong> <?= getPaymentMethodText($order['payment_method']) ?></p>
                    <?php if ($order['tracking_number']): ?>
                    <p class="mb-0"><strong>Mã vận chuyển:</strong> <?= $order['tracking_number'] ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Sản phẩm</h5>
                </div>
                <div class="card-body p-0">
                    <table class="table table-bordered mb-0">
                        <thead>
                            <tr>
                                <th>STT</th>
                                <th>Sản phẩm</th>
                                <th>SKU</th>
                                <th>Đơn giá</th>
                                <th>Số lượng</th>
                                <th>Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($order['items'] as $index => $item): ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <td><?= $item['product_name'] ?></td>
                                <td><?= $item['product_sku'] ?></td>
                                <td class="text-end"><?= number_format($item['price']) ?> VND</td>
                                <td class="text-center"><?= $item['quantity'] ?></td>
                                <td class="text-end"><?= number_format($item['total']) ?> VND</td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="5" class="text-end fw-bold">Tạm tính:</td>
                                <td class="text-end fw-bold"><?= number_format($order['subtotal']) ?> VND</td>
                            </tr>
                            <?php if ($order['shipping_fee'] > 0): ?>
                            <tr>
                                <td colspan="5" class="text-end">Phí vận chuyển:</td>
                                <td class="text-end"><?= number_format($order['shipping_fee']) ?> VND</td>
                            </tr>
                            <?php endif; ?>
                            
                            <tr>
                                <td colspan="5" class="text-end fw-bold">Tổng cộng:</td>
                                <td class="text-end fw-bold"><?= number_format($order['total_amount']) ?> VND</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Địa chỉ giao hàng</h5>
                </div>
                <div class="card-body">
                    <?php
                    $shippingAddress = json_decode($order['shipping_address'], true);
                    if ($shippingAddress && is_array($shippingAddress)):
                    ?>
                        <p class="mb-1"><strong><?= $shippingAddress['name'] ?? '' ?></strong></p>
                        <p class="mb-1"><?= $shippingAddress['phone'] ?? '' ?></p>
                        <p class="mb-1"><?= $shippingAddress['address'] ?? '' ?></p>
                        <p class="mb-0">
                            <?= $shippingAddress['ward'] ?? '' ?>, 
                            <?= $shippingAddress['district'] ?? '' ?>, 
                            <?= $shippingAddress['city'] ?? '' ?>
                        </p>
                        <?php if (!empty($shippingAddress['postal_code'])): ?>
                        <p class="mb-0">Mã bưu điện: <?= $shippingAddress['postal_code'] ?></p>
                        <?php endif; ?>
                    <?php else: ?>
                        <p class="text-muted">Không có thông tin địa chỉ</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Địa chỉ thanh toán</h5>
                </div>
                <div class="card-body">
                    <?php
                    $billingAddress = json_decode($order['billing_address'], true);
                    if ($billingAddress && is_array($billingAddress)):
                    ?>
                        <p class="mb-1"><strong><?= $billingAddress['name'] ?? '' ?></strong></p>
                        <p class="mb-1"><?= $billingAddress['phone'] ?? '' ?></p>
                        <p class="mb-1"><?= $billingAddress['address'] ?? '' ?></p>
                        <p class="mb-0">
                            <?= $billingAddress['ward'] ?? '' ?>, 
                            <?= $billingAddress['district'] ?? '' ?>, 
                            <?= $billingAddress['city'] ?? '' ?>
                        </p>
                        <?php if (!empty($billingAddress['postal_code'])): ?>
                        <p class="mb-0">Mã bưu điện: <?= $billingAddress['postal_code'] ?></p>
                        <?php endif; ?>
                    <?php else: ?>
                        <p class="text-muted">Không có thông tin địa chỉ</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <?php if (!empty($order['notes'])): ?>
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Ghi chú đơn hàng</h5>
                </div>
                <div class="card-body">
                    <?= nl2br(htmlspecialchars($order['notes'])) ?>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <div class="row mt-4">
        <div class="col-12 text-center">
            <button class="btn btn-primary" onclick="window.print()">
                <i class="fas fa-print"></i> In hóa đơn
            </button>
            <a href="<?= site_url('Dashboard/orders') ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Quay lại
            </a>
        </div>
    </div>
</div>

<style>
@media print {
    .btn { display: none; }
    .card { border: 1px solid #000; }
    .card-header { 
        background: #f8f9fa !important;
        border-bottom: 1px solid #000;
    }
    body { 
        font-size: 12px; 
        background: white !important;
    }
    .container-fluid {
        padding: 0;
        margin: 0;
    }
}
.badge {
    font-size: 0.85em;
    padding: 0.35em 0.65em;
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
        'cod' => 'COD (Thanh toán khi nhận hàng)',
        'momo' => 'Ví MoMo',
        'bank_transfer' => 'Chuyển khoản ngân hàng'
    ];
    return $methodMap[$method] ?? $method;
}
?>

<?= $this->endSection() ?>