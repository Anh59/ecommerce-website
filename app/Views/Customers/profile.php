<?= $this->extend('Customers/layout/main') ?>

<?= $this->section('content') ?>
<section class="breadcrumb breadcrumb_bg">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="breadcrumb_iner">
                        <div class="breadcrumb_iner_item">
                            <h2>Thông tin cá nhân</h2>
                            <p>Trang chủ <span>-</span> Thông tin cá nhân</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
<div class="container mt-5 mb-5">
    <div class="row">
        <!-- Sidebar Navigation -->
        <div class="col-lg-3 col-md-4">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-user"></i> Tài khoản của tôi</h5>
                </div>
                <div class="list-group list-group-flush">
                    <a href="#profile-info" class="list-group-item list-group-item-action active" data-toggle="pill">
                        <i class="fas fa-user-circle"></i> Thông tin cá nhân
                    </a>
                    <a href="#change-password" class="list-group-item list-group-item-action" data-toggle="pill">
                        <i class="fas fa-lock"></i> Đổi mật khẩu
                    </a>
                    <a href="#order-history" class="list-group-item list-group-item-action" data-toggle="pill">
                        <i class="fas fa-shopping-bag"></i> Lịch sử đơn hàng
                    </a>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-lg-9 col-md-8">
            <div class="tab-content">
                
                <!-- Profile Information Tab -->
                <div class="tab-pane fade show active" id="profile-info">
                    <div class="card">
                        <div class="card-header">
                            <h5><i class="fas fa-edit"></i> Thông tin cá nhân</h5>
                        </div>
                        <div class="card-body">
                            <form id="updateProfileForm" enctype="multipart/form-data">
                                <div class="row">
                                    <div class="col-md-4 text-center">
                                        <div class="profile-image-container mb-3">
                                            <img src="<?= $customer['image_url'] ? base_url($customer['image_url']) : base_url('aranoz-master/img/default-avatar.png') ?>" 
                                                 alt="Avatar" class="profile-image" id="profileImage">
                                            <div class="image-overlay">
                                                <i class="fas fa-camera"></i>
                                                <input type="file" id="imageInput" name="image" accept="image/*" style="display: none;">
                                            </div>
                                        </div>
                                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="document.getElementById('imageInput').click()">
                                            <i class="fas fa-upload"></i> Đổi ảnh đại diện
                                        </button>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label for="name"><i class="fas fa-user"></i> Họ và tên</label>
                                            <input type="text" class="form-control" id="name" name="name" 
                                                   value="<?= esc($customer['name']) ?>" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="email"><i class="fas fa-envelope"></i> Email</label>
                                            <input type="email" class="form-control" id="email" name="email" 
                                                   value="<?= esc($customer['email']) ?>" readonly>
                                            <small class="text-muted">Email không thể thay đổi</small>
                                        </div>
                                        <div class="form-group">
                                            <label for="phone"><i class="fas fa-phone"></i> Số điện thoại</label>
                                            <input type="tel" class="form-control" id="phone" name="phone" 
                                                   value="<?= esc($customer['phone']) ?>" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="address"><i class="fas fa-map-marker-alt"></i> Địa chỉ</label>
                                            <textarea class="form-control" id="address" name="address" rows="3" required><?= esc($customer['address']) ?></textarea>
                                        </div>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save"></i> Cập nhật thông tin
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Change Password Tab -->
                <div class="tab-pane fade" id="change-password">
                    <div class="card">
                        <div class="card-header">
                            <h5><i class="fas fa-key"></i> Đổi mật khẩu</h5>
                        </div>
                        <div class="card-body">
                            <form id="changePasswordForm">
                                <div class="form-group">
                                    <label for="current_password"><i class="fas fa-lock"></i> Mật khẩu hiện tại</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="current_password" name="current_password" required>
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('current_password')">
                                                <i class="fas fa-eye" id="current_password_icon"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="new_password"><i class="fas fa-lock"></i> Mật khẩu mới</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="new_password" name="new_password" required>
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('new_password')">
                                                <i class="fas fa-eye" id="new_password_icon"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <small class="text-muted">Mật khẩu phải có ít nhất 8 ký tự, bao gồm chữ cái, số và ký tự đặc biệt</small>
                                </div>
                                <div class="form-group">
                                    <label for="confirm_password"><i class="fas fa-lock"></i> Xác nhận mật khẩu mới</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('confirm_password')">
                                                <i class="fas fa-eye" id="confirm_password_icon"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-warning">
                                    <i class="fas fa-key"></i> Đổi mật khẩu
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Order History Tab -->
                <div class="tab-pane fade" id="order-history">
                    <div class="card">
                        <div class="card-header">
                            <h5><i class="fas fa-history"></i> Lịch sử đơn hàng</h5>
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
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Order Detail Modal -->
<div class="modal fade" id="orderDetailModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Chi tiết đơn hàng</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body" id="orderDetailContent">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('styles') ?>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    // Handle image preview
    $('#imageInput').change(function(e) {
        if (e.target.files && e.target.files[0]) {
            let reader = new FileReader();
            reader.onload = function(e) {
                $('#profileImage').attr('src', e.target.result);
            }
            reader.readAsDataURL(e.target.files[0]);
        }
    });

    // Update Profile Form
    $('#updateProfileForm').submit(function(e) {
        e.preventDefault();
        
        let formData = new FormData(this);
        
        showLoading();
        
        $.ajax({
            url: '<?= route_to("update_profile") ?>',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                hideLoading();
                if (response.status === 'success') {
                    showSuccessMessage('Thành công', response.message);
                    // Update avatar in header if changed
                    if (response.avatar_url) {
                        $('.navbar .fas.fa-user').parent().find('img').attr('src', response.avatar_url);
                    }
                } else {
                    showErrorMessage('Lỗi', response.message || 'Có lỗi xảy ra');
                }
            },
            error: function() {
                hideLoading();
                showErrorMessage('Lỗi', 'Có lỗi xảy ra khi cập nhật thông tin');
            }
        });
    });

    // Change Password Form
    $('#changePasswordForm').submit(function(e) {
        e.preventDefault();
        
        let newPassword = $('#new_password').val();
        let confirmPassword = $('#confirm_password').val();
        
        // Validate password match
        if (newPassword !== confirmPassword) {
            showErrorMessage('Lỗi', 'Mật khẩu mới và xác nhận mật khẩu không khớp');
            return;
        }
        
        // Validate password strength
        let passwordRegex = /^(?=.*[A-Za-z])(?=.*\d)(?=.*[!@#$%^&*])/;
        if (newPassword.length < 8 || !passwordRegex.test(newPassword)) {
            showErrorMessage('Lỗi', 'Mật khẩu phải có ít nhất 8 ký tự, bao gồm chữ cái, số và ký tự đặc biệt');
            return;
        }
        
        showLoading();
        
        $.ajax({
            url: '<?= route_to("change_password") ?>',
            type: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                hideLoading();
                if (response.status === 'success') {
                    showSuccessMessage('Thành công', response.message);
                    $('#changePasswordForm')[0].reset();
                } else {
                    showErrorMessage('Lỗi', response.message || 'Có lỗi xảy ra');
                }
            },
            error: function() {
                hideLoading();
                showErrorMessage('Lỗi', 'Có lỗi xảy ra khi đổi mật khẩu');
            }
        });
    });
});

function togglePassword(fieldId) {
    let field = document.getElementById(fieldId);
    let icon = document.getElementById(fieldId + '_icon');
    
    if (field.type === 'password') {
        field.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        field.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

function viewOrderDetail(orderId) {
    $.ajax({
        url: '<?= base_url("api_Customers/profile/order-detail") ?>/' + orderId,
        type: 'GET',
        success: function(response) {
            $('#orderDetailContent').html(response);
            $('#orderDetailModal').modal('show');
        },
        error: function() {
            showErrorMessage('Lỗi', 'Không thể tải chi tiết đơn hàng');
        }
    });
}

function cancelOrder(orderId) {
    if (confirm('Bạn có chắc chắn muốn hủy đơn hàng này?')) {
        $.ajax({
            url: '<?= base_url("profile/cancel-order") ?>/' + orderId,
            type: 'POST',
            success: function(response) {
                if (response.status === 'success') {
                    showSuccessMessage('Thành công', response.message);
                    location.reload();
                } else {
                    showErrorMessage('Lỗi', response.message);
                }
            },
            error: function() {
                showErrorMessage('Lỗi', 'Không thể hủy đơn hàng');
            }
        });
    }
}
</script>
<?= $this->endSection() ?>