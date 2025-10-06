<?= $this->extend('Dashboard/layout') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Quản lý đơn hàng</h4>
    <div class="d-flex gap-2">
        <div class="dropdown">
            <button class="btn btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                <i class="fas fa-filter"></i> Lọc trạng thái
            </button>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item filter-status" data-status="all" href="#">Tất cả đơn hàng</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item filter-status" data-status="pending" href="#">Chờ xử lý</a></li>
                <li><a class="dropdown-item filter-status" data-status="processing" href="#">Đang xử lý</a></li>
                <li><a class="dropdown-item filter-status" data-status="shipped" href="#">Đang giao</a></li>
                <li><a class="dropdown-item filter-status" data-status="delivered" href="#">Đã giao</a></li>
                <li><a class="dropdown-item filter-status" data-status="cancelled" href="#">Đã hủy</a></li>
            </ul>
        </div>
        <div class="dropdown">
            <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                <i class="fas fa-money-bill"></i> Thanh toán
            </button>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item filter-payment" data-payment="all" href="#">Tất cả</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item filter-payment" data-payment="pending" href="#">Chờ thanh toán</a></li>
                <li><a class="dropdown-item filter-payment" data-payment="paid" href="#">Đã thanh toán</a></li>
                <li><a class="dropdown-item filter-payment" data-payment="failed" href="#">Thanh toán thất bại</a></li>
                <li><a class="dropdown-item filter-payment" data-payment="refunded" href="#">Đã hoàn tiền</a></li>
            </ul>
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-body">
        <div class="row">
            <div class="col-md-3">
                <div class="input-group">
                    <input type="text" id="searchInput" class="form-control" placeholder="Tìm kiếm đơn hàng...">
                    <button class="btn btn-primary" id="btnSearch">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
            <div class="col-md-4">
                <input type="date" id="dateFilter" class="form-control" value="<?= date('Y-m-d') ?>">
            </div>
            <div class="col-md-2">
                <button class="btn btn-outline-secondary w-100" id="btnResetFilters">
                    <i class="fas fa-refresh"></i> Reset
                </button>
            </div>
        </div>
    </div>
</div>

<table id="ordersTable" class="display" style="width:100%">
    <thead>
        <tr>
            <th>Mã đơn</th>
            <th>Khách hàng</th>
            <th>Ngày đặt</th>
            <th>Số lượng</th>
            <th>Tổng tiền</th>
            <th>Giảm giá</th>
            <th>Trạng thái</th>
            <th>Thanh toán</th>
            <th>Phương thức</th>
            <th>Hành động</th>
        </tr>
    </thead>
    <tbody>
        <!-- Dữ liệu sẽ được tải bằng AJAX -->
    </tbody>
</table>

<!-- Modal chi tiết đơn hàng -->
<div class="modal fade" id="orderModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Chi tiết đơn hàng: <span id="orderNumberDisplay"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-8">
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="mb-0">Sản phẩm</h6>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table" id="orderItemsTable">
                                        <thead>
                                            <tr>
                                                <th>Sản phẩm</th>
                                                <th>SKU</th>
                                                <th>Đơn giá</th>
                                                <th>Số lượng</th>
                                                <th>Thành tiền</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- Items will be populated by JS -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="mb-0">Thông tin đơn hàng</h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <strong>Trạng thái:</strong>
                                    <select class="form-select mt-1" id="orderStatus">
                                        <option value="pending">Chờ xử lý</option>
                                        <option value="processing">Đang xử lý</option>
                                        <option value="shipped">Đang giao</option>
                                        <option value="delivered">Đã giao</option>
                                        <option value="cancelled">Đã hủy</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <strong>Thanh toán:</strong>
                                    <select class="form-select mt-1" id="paymentStatus">
                                        <option value="pending">Chờ thanh toán</option>
                                        <option value="paid">Đã thanh toán</option>
                                        <option value="failed">Thanh toán thất bại</option>
                                        <option value="refunded">Đã hoàn tiền</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <strong>Mã theo dõi:</strong>
                                    <input type="text" class="form-control mt-1" id="trackingNumber" placeholder="Nhập mã vận chuyển">
                                </div>
                                <div class="mb-3">
                                    <strong>Ghi chú:</strong>
                                    <textarea class="form-control mt-1" id="orderNotes" rows="3"></textarea>
                                </div>
                            </div>
                        </div>
                        
                        <!-- ✅ THÊM: Thông tin voucher -->
                        <div class="card mb-4" id="couponInfoCard" style="display: none;">
                            <div class="card-header bg-success text-white">
                                <h6 class="mb-0"><i class="fas fa-tag me-2"></i>Thông tin giảm giá</h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-2">
                                    <strong>Mã giảm giá:</strong>
                                    <span class="badge bg-success ms-2" id="couponCodeDisplay"></span>
                                </div>
                                <div class="mb-2">
                                    <strong>Số tiền giảm:</strong>
                                    <span class="text-success fw-bold" id="discountAmountDisplay"></span>
                                </div>
                                <small class="text-muted">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Voucher đã được áp dụng thành công
                                </small>
                            </div>
                        </div>
                        
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0">Tổng cộng</h6>
                            </div>
                            <div class="card-body">
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Tạm tính:</span>
                                    <span id="subtotalAmount">0 VND</span>
                                </div>
                                
                                <!-- ✅ THÊM: Dòng giảm giá -->
                                <div class="d-flex justify-content-between mb-2" id="discountRow" style="display: none !important;">
                                    <span>Giảm giá:</span>
                                    <span class="text-success" id="discountAmount">-0 VND</span>
                                </div>
                                
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Phí vận chuyển:</span>
                                    <span id="shippingFee">0 VND</span>
                                </div>
                                <hr>
                                <div class="d-flex justify-content-between mb-2 fw-bold">
                                    <span>Tổng cộng:</span>
                                    <span id="totalAmount">0 VND</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0">Địa chỉ giao hàng</h6>
                            </div>
                            <div class="card-body" id="shippingAddress">
                                <!-- Shipping address will be populated by JS -->
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0">Địa chỉ thanh toán</h6>
                            </div>
                            <div class="card-body" id="billingAddress">
                                <!-- Billing address will be populated by JS -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="btnSaveOrder">
                    <i class="fas fa-save"></i> Cập nhật
                </button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i> Đóng
                </button>
            </div>
        </div>
    </div>
</div>

<!-- CSS & JS -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<style>
.order-status {
    font-size: 0.8em;
    padding: 0.4em 0.8em;
    border-radius: 4px;
}
.status-pending { background-color: #ffc107; color: #000; }
.status-processing { background-color: #17a2b8; }
.status-shipped { background-color: #007bff; }
.status-delivered { background-color: #28a745; }
.status-cancelled { background-color: #dc3545; }
.payment-status {
    font-size: 0.8em;
    padding: 0.4em 0.8em;
    border-radius: 4px;
}
.payment-pending { background-color: #ffc107; color: #000; }
.payment-paid { background-color: #28a745; }
.payment-failed { background-color: #dc3545; }
.payment-refunded { background-color: #6c757d; }
.order-number {
    font-family: monospace;
    font-weight: bold;
}
.coupon-badge {
    background: #28a745;
    color: white;
    padding: 2px 6px;
    border-radius: 4px;
    font-size: 11px;
    font-weight: 600;
}
.discount-amount {
    color: #28a745;
    font-weight: 600;
}
</style>

<script>
$(document).ready(function(){
    // Helper functions
    const csrfToken = () => $('meta[name="csrf-token"]').attr('content');
    const updateToken = (token) => { 
        $('meta[name="csrf-token"]').attr('content', token); 
    };
    
    const showToast = (type, message) => {
        if (typeof message === 'object') {
            message = Object.values(message).join('<br>');
        }
        toastr[type](message);
    };

    // Format currency
    const formatCurrency = (amount) => {
        return new Intl.NumberFormat('vi-VN').format(amount) + ' VND';
    };

    // Format date
    const formatDate = (dateString) => {
        if (!dateString) return '';
        const date = new Date(dateString);
        return date.toLocaleDateString('vi-VN') + ' ' + date.toLocaleTimeString('vi-VN', {
            hour: '2-digit',
            minute: '2-digit'
        });
    };

    // DataTable
    let currentStatus = 'all';
    let currentPaymentStatus = 'all';
    let currentSearch = '';
    let currentDate = '<?= date('Y-m-d') ?>';

    const table = $('#ordersTable').DataTable({
        processing: true,
        serverSide: true,
        language: { 
            processing: 'Đang tải dữ liệu...',
            search: 'Tìm kiếm:',
            lengthMenu: 'Hiển thị _MENU_ mục',
            info: 'Hiển thị _START_ đến _END_ của _TOTAL_ mục',
            paginate: {
                first: 'Đầu',
                last: 'Cuối',
                next: 'Tiếp',
                previous: 'Trước'
            }
        },
        ajax: {
            url: "<?= site_url('Dashboard/orders/list') ?>",
            data: function(d) {
                d.status = currentStatus;
                d.payment_status = currentPaymentStatus;
                d.search = currentSearch;
                d.date = currentDate;
            },
            dataSrc: 'data',
            complete: function(xhr) {
                const json = xhr.responseJSON;
                if (json?.token) updateToken(json.token);
            }
        },
        columns: [
            { 
                data: 'order_number',
                render: d => `<span class="order-number">${d}</span>`,
                width: '120px'
            },
            { 
                data: null,
                render: (d, t, r) => {
                    return `
                        <div>
                            <strong>${r.customer_name || 'Khách vãng lai'}</strong><br>
                            <small class="text-muted">${r.customer_phone || ''}</small>
                        </div>
                    `;
                },
                width: '150px'
            },
            { 
                data: 'created_at',
                render: d => formatDate(d),
                width: '140px'
            },
            { 
                data: 'total_items',
                render: d => `<span class="badge bg-secondary">${d} sản phẩm</span>`,
                width: '100px'
            },
            { 
                data: 'total_amount',
                render: d => `<strong>${formatCurrency(d)}</strong>`,
                width: '120px'
            },
            { 
                // ✅ THÊM: Cột giảm giá
                data: null,
                render: (d, t, r) => {
                    if (r.coupon_code && r.discount_amount > 0) {
                        return `
                            <div>
                                <span class="coupon-badge">${r.coupon_code}</span><br>
                                <small class="discount-amount">-${formatCurrency(r.discount_amount)}</small>
                            </div>
                        `;
                    }
                    return '<span class="text-muted">Không có</span>';
                },
                width: '120px'
            },
            { 
                data: 'status',
                render: d => `<span class="badge order-status status-${d}">${getStatusText(d)}</span>`,
                width: '100px'
            },
            { 
                data: 'payment_status',
                render: d => `<span class="badge payment-status payment-${d}">${getPaymentStatusText(d)}</span>`,
                width: '120px'
            },
            { 
                data: 'payment_method',
                render: d => getPaymentMethodText(d),
                width: '120px'
            },
            { 
                data: 'id',
                render: (d, t, r) => `
                    <div class="btn-group" role="group">
                        <button class="btn btn-sm btn-primary btn-view" data-id="${d}" title="Xem chi tiết">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="btn btn-sm btn-info btn-print" data-id="${d}" title="In đơn hàng">
                            <i class="fas fa-print"></i>
                        </button>
                    </div>
                `,
                width: '100px',
                orderable: false
            }
        ],
        order: [[2, 'desc']]
    });

    // Helper functions for status texts
    function getStatusText(status) {
        const statusMap = {
            'pending': 'Chờ xử lý',
            'processing': 'Đang xử lý',
            'shipped': 'Đang giao',
            'delivered': 'Đã giao',
            'cancelled': 'Đã hủy'
        };
        return statusMap[status] || status;
    }

    function getPaymentStatusText(status) {
        const statusMap = {
            'pending': 'Chờ thanh toán',
            'paid': 'Đã thanh toán',
            'failed': 'Thất bại',
            'refunded': 'Đã hoàn tiền'
        };
        return statusMap[status] || status;
    }

    function getPaymentMethodText(method) {
        const methodMap = {
            'cod': 'COD',
            'momo': 'Momo',
            'bank_transfer': 'Chuyển khoản'
        };
        return methodMap[method] || method;
    }

    // Filter handlers
    $('.filter-status').on('click', function(e) {
        e.preventDefault();
        currentStatus = $(this).data('status');
        table.ajax.reload();
    });

    $('.filter-payment').on('click', function(e) {
        e.preventDefault();
        currentPaymentStatus = $(this).data('payment');
        table.ajax.reload();
    });

    // Search handler
    $('#btnSearch').on('click', function() {
        currentSearch = $('#searchInput').val();
        table.ajax.reload();
    });

    $('#searchInput').on('keypress', function(e) {
        if (e.which === 13) {
            currentSearch = $(this).val();
            table.ajax.reload();
        }
    });

    // Date filter
    $('#dateFilter').on('change', function() {
        currentDate = $(this).val();
        table.ajax.reload();
    });

    // Reset filters
    $('#btnResetFilters').on('click', function() {
        currentStatus = 'all';
        currentPaymentStatus = 'all';
        currentSearch = '';
        currentDate = '<?= date('Y-m-d') ?>';
        $('#searchInput').val('');
        $('#dateFilter').val('<?= date('Y-m-d') ?>');
        table.ajax.reload();
    });

    // View order details
    $('#ordersTable').on('click', '.btn-view', function(){
        const orderId = $(this).data('id');
        $('#orderModal').data('order-id', orderId);
        loadOrderDetails(orderId);
    });

    // Load order details
    function loadOrderDetails(orderId) {
        console.log('Loading order details for ID:', orderId);
        $.get(`<?= site_url('Dashboard/orders') ?>/${orderId}/details`)
        .done(function(res){
            if (res.status === 'success') {
                updateToken(res.token);
                displayOrderDetails(res.order);
                $('#orderModal').modal('show');
            } else {
                showToast('error', res.message || 'Lỗi khi tải chi tiết đơn hàng');
            }
        })
        .fail(function(xhr, status, error) {
            console.error('Error loading order details:', error);
            showToast('error', 'Lỗi kết nối khi tải chi tiết đơn hàng');
        });
    }

    // Display order details in modal
    function displayOrderDetails(order) {
        $('#orderNumberDisplay').text(order.order_number);
        $('#orderStatus').val(order.status);
        $('#paymentStatus').val(order.payment_status);
        $('#trackingNumber').val(order.tracking_number || '');
        $('#orderNotes').val(order.notes || '');
        
        // ✅ THÊM: Hiển thị thông tin voucher
        if (order.coupon_code && order.discount_amount > 0) {
            $('#couponInfoCard').show();
            $('#couponCodeDisplay').text(order.coupon_code);
            $('#discountAmountDisplay').text('-' + formatCurrency(order.discount_amount));
        } else {
            $('#couponInfoCard').hide();
        }
        
        // Display order items
        const $itemsTable = $('#orderItemsTable tbody');
        $itemsTable.empty();
        
        if (order.items && order.items.length > 0) {
            order.items.forEach(item => {
                $itemsTable.append(`
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                ${item.main_image ? 
                                    `<img src="${item.main_image}" alt="${item.product_name}" 
                                        style="width: 50px; height: 50px; object-fit: cover; margin-right: 10px;">` : 
                                    `<div style="width: 50px; height: 50px; background: #f8f9fa; 
                                        display: flex; align-items: center; justify-content: center; 
                                        margin-right: 10px;">
                                        <i class="fas fa-image text-muted"></i>
                                    </div>`
                                }
                                <div>
                                    <strong>${item.product_name}</strong>
                                    ${item.slug ? `<br><small class="text-muted">/${item.slug}</small>` : ''}
                                </div>
                            </div>
                        </td>
                        <td>${item.product_sku}</td>
                        <td>${formatCurrency(item.price)}</td>
                        <td>${item.quantity}</td>
                        <td><strong>${formatCurrency(item.total)}</strong></td>
                    </tr>
                `);
            });
        }
        
        // ✅ CẬP NHẬT: Hiển thị tổng cộng với thông tin giảm giá
        const subtotal = parseFloat(order.subtotal) || 0;
        const discount = parseFloat(order.discount_amount) || 0;
        const shipping = parseFloat(order.shipping_fee) || 0;
        const total = parseFloat(order.total_amount) || 0;
        
        // Tính subtotal trước giảm giá
        const originalSubtotal = subtotal + discount;
        
        $('#subtotalAmount').text(formatCurrency(originalSubtotal));
        
        // Hiển thị dòng giảm giá nếu có
        if (discount > 0) {
            $('#discountRow').show();
            $('#discountAmount').text('-' + formatCurrency(discount));
        } else {
            $('#discountRow').hide();
        }
        
        $('#shippingFee').text(formatCurrency(shipping));
        $('#totalAmount').text(formatCurrency(total));
        
        // Display addresses
        $('#shippingAddress').html(formatAddress(order.shipping_address));
        $('#billingAddress').html(formatAddress(order.billing_address));
    }

    // Format address
    function formatAddress(address) {
        if (typeof address === 'string') {
            try {
                address = JSON.parse(address);
            } catch (e) {
                return `<pre>${address}</pre>`;
            }
        }
        
        if (typeof address === 'object') {
            return `
                <div>
                    <strong>${address.full_name || ''}</strong><br>
                    ${address.phone ? `<small>${address.phone}</small><br>` : ''}
                    ${address.address || ''}<br>
                    ${address.ward ? `${address.ward}, ` : ''}
                    ${address.district ? `${address.district}, ` : ''}
                    ${address.province || ''}
                </div>
            `;
        }
        
        return `<pre>${address}</pre>`;
    }

    // Save order changes
    $('#btnSaveOrder').on('click', function(){
        const orderId = $('#orderModal').data('order-id');
        console.log('Saving order with ID:', orderId);
        
        if (!orderId) {
            showToast('error', 'Không tìm thấy ID đơn hàng');
            return;
        }
        
        const status = $('#orderStatus').val();
        const paymentStatus = $('#paymentStatus').val();
        const trackingNumber = $('#trackingNumber').val();
        const notes = $('#orderNotes').val();
        
        const $btn = $(this);
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Đang lưu...');
        
        console.log('Sending update data:', {
            status: status,
            payment_status: paymentStatus,
            tracking_number: trackingNumber,
            notes: notes
        });
        
        $.ajax({
            url: `<?= site_url('Dashboard/orders') ?>/${orderId}/update`,
            method: 'POST',
            data: {
                '<?= csrf_token() ?>': csrfToken(),
                status: status,
                payment_status: paymentStatus,
                tracking_number: trackingNumber,
                notes: notes
            },
            dataType: 'json'
        })
        .done(function(res){
            console.log('Update response:', res);
            if (res.token) updateToken(res.token);
            
            if (res.status === 'success') {
                $('#orderModal').modal('hide');
                table.ajax.reload(null, false);
                showToast('success', res.message);
            } else {
                let errorMessage = res.message || 'Có lỗi xảy ra';
                if (res.errors) {
                    errorMessage += '<br>' + Object.values(res.errors).join('<br>');
                }
                showToast('error', errorMessage);
            }
        })
        .fail(function(xhr, status, error){
            console.error('AJAX Error:', status, error);
            console.error('Response:', xhr.responseText);
            
            let errorMessage = 'Lỗi kết nối: ';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage += response.message || xhr.statusText;
            } else {
                errorMessage += xhr.statusText || error;
            }
            showToast('error', errorMessage);
        })
        .always(function(){
            $btn.prop('disabled', false).html('<i class="fas fa-save"></i> Cập nhật');
        });
    });

    // Print order
    $('#ordersTable').on('click', '.btn-print', function(){
        const orderId = $(this).data('id');
        window.open(`<?= site_url('Dashboard/orders') ?>/${orderId}/print`, '_blank');
    });
});
</script>
<?= $this->endSection() ?>