<div class="card">
    <div class="card-header">
        <h5><i class="fas fa-history"></i> Lịch sử đặt hàng</h5>
    </div>
    <div class="card-body">
        <?php if (!empty($orders)): ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th>Mã đơn hàng</th>
                            <th>Ngày đặt</th>
                            <th>Tổng tiền</th>
                            <th>Trạng thái</th>
                            <th>Thanh toán</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): ?>
                        <tr>
                            <td><strong><?= esc($order['order_number']) ?></strong></td>
                            <td><?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></td>
                            <td><strong><?= number_format($order['total_amount'], 0, ',', '.') ?>đ</strong></td>
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
                                    default:
                                        $statusClass = 'badge-secondary';
                                        $statusText = ucfirst($order['status']);
                                }
                                ?>
                                <span class="badge <?= $statusClass ?>"><?= $statusText ?></span>
                            </td>
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
                                    default:
                                        $paymentClass = 'badge-secondary';
                                        $paymentText = ucfirst($order['payment_status']);
                                }
                                ?>
                                <span class="badge <?= $paymentClass ?>"><?= $paymentText ?></span>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary" onclick="viewOrderDetail(<?= $order['id'] ?>)">
                                    <i class="fas fa-eye"></i> Chi tiết
                                </button>
                                <?php if($order['status'] == 'pending'): ?>
                                <button class="btn btn-sm btn-outline-danger" onclick="cancelOrder(<?= $order['id'] ?>)">
                                    <i class="fas fa-times"></i> Hủy
                                </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <?php if (isset($pager)): ?>
            <div class="d-flex justify-content-center mt-3">
                <?= $pager->links() ?>
            </div>
            <?php endif; ?>
            
        <?php else: ?>
            <div class="text-center py-4">
                <i class="fas fa-shopping-bag fa-3x text-muted mb-3"></i>
                <h5>Chưa có đơn hàng nào</h5>
                <p class="text-muted">Bạn chưa có đơn hàng nào. Hãy bắt đầu mua sắm ngay!</p>
                <a href="<?= route_to('category') ?>" class="btn btn-primary">
                    <i class="fas fa-shopping-cart"></i> Mua sắm ngay
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>