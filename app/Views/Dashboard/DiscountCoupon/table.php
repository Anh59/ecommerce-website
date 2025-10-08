<?= $this->extend('Dashboard/layout') ?>
<?= $this->section('content') ?>

<div class="mb-3">
    <button type="button" id="btnAdd" class="btn btn-success" title="Thêm voucher">
        <i class="fas fa-plus"></i> Thêm voucher
    </button>
</div>

<table id="couponsTable" class="display" style="width:100%">
    <thead>
        <tr>
            <th>STT</th>
            <th>Mã voucher</th>
            <th>Loại</th>
            <th>Giá trị</th>
            <th>Đơn tối thiểu</th>
            <th>Sử dụng</th>
            <th>Áp dụng</th>
            <th>Thời gian</th>
            <th>Trạng thái</th>
            <th>Hành động</th>
        </tr>
    </thead>
    <tbody>
        <!-- Dữ liệu sẽ được tải bằng AJAX -->
    </tbody>
</table>

<!-- Modal Form -->
<div class="modal fade" id="couponModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <form id="couponForm" enctype="multipart/form-data">
            <?= csrf_field() ?>
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Thêm / Sửa voucher</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="coupon_id">

                    <div class="row">
                        <!-- Cột trái - Thông tin cơ bản -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">Thông tin cơ bản</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label">Mã voucher *</label>
                                        <div class="input-group">
                                            <input type="text" name="code" id="code" class="form-control" required 
                                                placeholder="VD: SUMMER2024">
                                            <button type="button" class="btn btn-outline-secondary" id="btnAutoCode">
                                                <i class="fa fa-magic"></i>
                                            </button>
                                        </div>
                                        <div class="invalid-feedback"></div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Loại giảm giá *</label>
                                                <select name="type" id="type" class="form-control" required>
                                                    <option value="fixed">Giảm theo số tiền</option>
                                                    <option value="percentage">Giảm theo phần trăm</option>
                                                </select>
                                                <div class="invalid-feedback"></div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Giá trị *</label>
                                                <div class="input-group">
                                                    <input type="number" name="value" id="value" class="form-control" 
                                                        required min="0" step="0.01">
                                                    <span class="input-group-text" id="valueUnit">VND</span>
                                                </div>
                                                <div class="invalid-feedback"></div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Đơn hàng tối thiểu</label>
                                        <div class="input-group">
                                            <input type="number" name="min_order_amount" id="min_order_amount" 
                                                class="form-control" min="0" step="0.01" value="0">
                                            <span class="input-group-text">VND</span>
                                        </div>
                                        <small class="text-muted">Để 0 nếu không có giới hạn</small>
                                        <div class="invalid-feedback"></div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Giới hạn sử dụng</label>
                                        <input type="number" name="usage_limit" id="usage_limit" class="form-control" 
                                            min="1" placeholder="Để trống nếu không giới hạn">
                                        <small class="text-muted">Số lần tối đa có thể sử dụng voucher</small>
                                        <div class="invalid-feedback"></div>
                                    </div>

                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" checked>
                                        <label class="form-check-label" for="is_active">
                                            Kích hoạt voucher
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Cột phải - Cài đặt nâng cao -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">Cài đặt thời gian</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label">Ngày bắt đầu</label>
                                        <input type="datetime-local" name="start_date" id="start_date" class="form-control">
                                        <small class="text-muted">Để trống nếu có hiệu lực ngay</small>
                                        <div class="invalid-feedback"></div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Ngày kết thúc</label>
                                        <input type="datetime-local" name="end_date" id="end_date" class="form-control">
                                        <small class="text-muted">Để trống nếu không có thời hạn</small>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="card mt-3">
                                <div class="card-header">
                                    <h6 class="mb-0">Áp dụng sản phẩm</h6>
                                </div>
                                <div class="card-body">
                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="radio" name="apply_all" id="apply_all_yes" value="1" checked>
                                        <label class="form-check-label" for="apply_all_yes">
                                            Áp dụng cho tất cả sản phẩm
                                        </label>
                                    </div>

                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="radio" name="apply_all" id="apply_all_no" value="0">
                                        <label class="form-check-label" for="apply_all_no">
                                            Áp dụng cho sản phẩm cụ thể
                                        </label>
                                    </div>

                                    <div id="product_selection" style="display: none;">
                                        <label class="form-label">Chọn sản phẩm</label>
                                        <textarea name="product_skus" id="product_skus" class="form-control" rows="3"
                                            placeholder="Nhập SKU sản phẩm, cách nhau bởi dấu phẩy. VD: IPHONE14, SAMSUNGS23, MACBOOK2023"></textarea>
                                        <small class="text-muted">Nhập SKU sản phẩm, cách nhau bởi dấu phẩy</small>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Usage Info (chỉ hiển thị khi edit) -->
                            <div class="card mt-3" id="usage_info" style="display: none;">
                                <div class="card-header">
                                    <h6 class="mb-0">Thông tin sử dụng</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-2">
                                        <strong>Đã sử dụng:</strong> <span id="used_count_display">0</span> lần
                                    </div>
                                    <button type="button" class="btn btn-sm btn-warning" id="btnResetUsage">
                                        <i class="fa fa-refresh"></i> Reset số lần sử dụng
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" id="btnSave" class="btn btn-primary">
                        <i class="fa fa-save"></i> Lưu voucher
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fa fa-times"></i> Đóng
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal Generate Code -->
<div class="modal fade" id="generateCodeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tạo mã voucher tự động</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Tiền tố</label>
                    <input type="text" id="code_prefix" class="form-control" value="COUPON" placeholder="VD: SUMMER">
                </div>
                <div class="mb-3">
                    <label class="form-label">Độ dài mã ngẫu nhiên</label>
                    <input type="number" id="code_length" class="form-control" value="8" min="4" max="20">
                </div>
                <div class="mb-3">
                    <label class="form-label">Mã được tạo:</label>
                    <div class="input-group">
                        <input type="text" id="generated_code" class="form-control" readonly>
                        <button type="button" class="btn btn-secondary" id="btnRefreshCode">
                            <i class="fa fa-refresh"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="btnUseCode">
                    <i class="fa fa-check"></i> Sử dụng mã này
                </button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fa fa-times"></i> Hủy
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
.badge-status {
    font-size: 0.8em;
    padding: 0.4em 0.8em;
}
.coupon-code {
    font-family: monospace;
    font-weight: bold;
    background: #f8f9fa;
    padding: 4px 8px;
    border-radius: 4px;
    border: 1px solid #dee2e6;
}
.usage-progress {
    display: flex;
    align-items: center;
    gap: 8px;
}
.progress {
    flex: 1;
    height: 20px;
}
.progress-label {
    font-size: 0.9em;
    font-weight: 500;
    white-space: nowrap;
}
.type-fixed { background-color: #17a2b8; }
.type-percentage { background-color: #28a745; }
.status-active { background-color: #28a745; }
.status-inactive { background-color: #dc3545; }
.status-expired { background-color: #6c757d; }
.apply-all { background-color: #ffc107; color: #000; }
.apply-specific { background-color: #17a2b8; }
</style>

<script>
$(document).ready(function(){
    // Helper functions
    const csrfToken = () => $('meta[name="csrf-token"]').attr('content');
    const updateToken = (token) => { 
        $('meta[name="csrf-token"]').attr('content', token); 
        $('input[name="<?= csrf_token() ?>"]').val(token); 
    };
    
    const showToast = (type, message) => {
        if (typeof message === 'object') {
            message = Object.values(message).join('<br>');
        }
        toastr[type](message);
    };

    // Format date helper
    const formatDate = (dateString) => {
        if (!dateString) return '';
        const date = new Date(dateString);
        return date.toLocaleDateString('vi-VN') + ' ' + date.toLocaleTimeString('vi-VN', {
            hour: '2-digit',
            minute: '2-digit'
        });
    };

    // Format currency
    const formatCurrency = (amount) => {
        return new Intl.NumberFormat('vi-VN').format(amount) + ' VND';
    };

    // ===== FUNCTION KIỂM TRA TRẠNG THÁI VOUCHER =====
    const getVoucherStatus = (coupon) => {
        // Nếu is_active = 0 → Ngưng
        if (coupon.is_active != 1) {
            return { class: 'status-inactive', text: 'Ngưng' };
        }

        const now = new Date();
        const endDate = coupon.end_date ? new Date(coupon.end_date) : null;
        const usedCount = parseInt(coupon.used_count) || 0;
        const usageLimit = coupon.usage_limit ? parseInt(coupon.usage_limit) : null;

        // Debug log
        console.log('Voucher:', coupon.code, {
            usedCount: usedCount,
            usageLimit: usageLimit,
            comparison: usageLimit ? `${usedCount} >= ${usageLimit}` : 'no limit'
        });

        // Kiểm tra hết hạn theo thời gian
        if (endDate && endDate < now) {
            return { class: 'status-expired', text: 'Hết hạn' };
        }

        // ✅ SỬA: Kiểm tra hết lượt - CHỈ KHI CÓ usage_limit
        if (usageLimit !== null && usedCount >= usageLimit) {
            return { class: 'status-expired', text: 'Hết lượt' };
        }

        // Voucher đang hoạt động bình thường
        return { class: 'status-active', text: 'Hoạt động' };
    };

    // DataTable
    const table = $('#couponsTable').DataTable({
        processing: true,
        serverSide: false, // ✅ Đổi thành false để xử lý ở client
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
            url: "<?= site_url('Dashboard/discount-coupons/list') ?>",
            dataSrc: function(json) {
                if (json?.token) updateToken(json.token);
                // ✅ Debug: In ra data để kiểm tra
                console.log('Coupon Data:', json.data);
                return json.data;
            }
        },
        columns: [
            { 
                data: null, 
                render: (d, t, r, m) => m.row + m.settings._iDisplayStart + 1,
                width: '50px',
                orderable: false
            },
            { 
                data: 'code',
                render: d => `<span class="coupon-code">${d}</span>`,
                width: '150px'
            },
            { 
                data: 'type', 
                render: d => d === 'percentage' ? 
                    '<span class="badge type-percentage">Phần trăm</span>' : 
                    '<span class="badge type-fixed">Số tiền</span>',
                width: '100px'
            },
            { 
                data: null,
                render: (d, t, r) => {
                    if (r.type === 'percentage') {
                        return `<strong>${r.value}%</strong>`;
                    } else {
                        return `<strong>${formatCurrency(r.value)}</strong>`;
                    }
                },
                width: '120px'
            },
            { 
                data: 'min_order_amount',
                render: d => d > 0 ? formatCurrency(d) : '<em class="text-muted">Không</em>',
                width: '120px'
            },
            { 
                data: null,
                render: (d, t, r) => {
                    const used = parseInt(r.used_count) || 0;
                    const limit = r.usage_limit ? parseInt(r.usage_limit) : null;
                    
                    if (limit) {
                        const percentage = (used / limit) * 100;
                        let progressClass = 'bg-success';
                        if (percentage >= 100) progressClass = 'bg-danger';
                        else if (percentage > 80) progressClass = 'bg-warning';
                        else if (percentage > 60) progressClass = 'bg-info';
                        
                        return `
                            <div class="usage-progress">
                                <div class="progress" style="width: 60px;">
                                    <div class="progress-bar ${progressClass}" style="width: ${Math.min(percentage, 100)}%"></div>
                                </div>
                                <span class="progress-label">${used}/${limit}</span>
                            </div>
                        `;
                    } else {
                        return `<span class="badge bg-secondary">${used} / ∞</span>`;
                    }
                },
                width: '120px',
                orderable: false
            },
            { 
                data: 'apply_all',
                render: (d, t, r) => d == 1 ? 
                    '<span class="badge apply-all">Tất cả</span>' : 
                    `<span class="badge apply-specific">Cụ thể (${r.product_count || 0})</span>`,
                width: '100px'
            },
            {
                data: null,
                render: (d, t, r) => {
                    let timeInfo = '';
                    if (r.start_date) {
                        timeInfo += `<small><i class="fa fa-play text-success"></i> ${formatDate(r.start_date)}</small><br>`;
                    }
                    if (r.end_date) {
                        timeInfo += `<small><i class="fa fa-stop text-danger"></i> ${formatDate(r.end_date)}</small>`;
                    }
                    return timeInfo || '<em class="text-muted">Không giới hạn</em>';
                },
                width: '140px',
                orderable: false
            },
            { 
                data: null,
                render: (d, t, r) => {
                    const status = getVoucherStatus(r);
                    return `<span class="badge ${status.class} badge-status">${status.text}</span>`;
                },
                width: '100px'
            },
            { 
                data: 'id', 
                render: (d, t, r) => `
                    <div class="btn-group" role="group">
                        <button class="btn btn-sm btn-primary btn-edit" data-id="${d}" title="Sửa">
                            <i class="fa fa-edit"></i>
                        </button>
                        <button class="btn btn-sm ${r.is_active == 1 ? 'btn-warning' : 'btn-success'} btn-toggle" 
                            data-id="${d}" title="${r.is_active == 1 ? 'Ngưng hoạt động' : 'Kích hoạt'}">
                            <i class="fa ${r.is_active == 1 ? 'fa-pause' : 'fa-play'}"></i>
                        </button>
                        <button class="btn btn-sm btn-info btn-duplicate" data-id="${d}" title="Nhân bản">
                            <i class="fa fa-copy"></i>
                        </button>
                        <button class="btn btn-sm btn-danger btn-delete" data-id="${d}" title="Xóa">
                            <i class="fa fa-trash"></i>
                        </button>
                    </div>
                `,
                width: '150px',
                orderable: false
            }
        ],
        order: [[0, 'asc']]
    });

    // ... Phần còn lại giữ nguyên như code cũ ...
    // (Type change, Apply all, Show add modal, Generate code, etc.)

    // Type change handler
    $('#type').on('change', function() {
        const type = $(this).val();
        const $valueUnit = $('#valueUnit');
        const $valueInput = $('#value');
        
        if (type === 'percentage') {
            $valueUnit.text('%');
            $valueInput.attr('max', '100');
        } else {
            $valueUnit.text('VND');
            $valueInput.removeAttr('max');
        }
    });

    // Apply all change handler
    $('input[name="apply_all"]').on('change', function() {
        const applyAll = $(this).val() === '1';
        $('#product_selection').toggle(!applyAll);
        
        if (applyAll) {
            $('#product_skus').val('').removeClass('is-invalid');
        }
    });

    // Show add modal
    $('#btnAdd').on('click', function(){
        resetForm();
        $('.modal-title').text('Thêm voucher');
        $('#couponModal').modal('show');
        setTimeout(() => $('#code').focus(), 500);
    });

    // Generate code modal
    $('#btnAutoCode').on('click', function(){
        $('#generateCodeModal').modal('show');
        generateNewCode();
    });

    // Generate code functions
    const generateNewCode = () => {
        const prefix = $('#code_prefix').val() || 'COUPON';
        const length = $('#code_length').val() || 8;
        
        $.post("<?= site_url('Dashboard/discount-coupons/generate-code') ?>", {
            '<?= csrf_token() ?>': csrfToken(),
            prefix: prefix,
            length: length
        })
        .done(function(res){
            if (res.token) updateToken(res.token);
            if (res.status === 'success') {
                $('#generated_code').val(res.code);
            }
        });
    };

    $('#btnRefreshCode, #code_prefix, #code_length').on('click change', generateNewCode);

    $('#btnUseCode').on('click', function(){
        const code = $('#generated_code').val();
        if (code) {
            $('#code').val(code);
            $('#generateCodeModal').modal('hide');
            $('#couponModal').modal('show');
        }
    });

    // Reset form function
    const resetForm = () => {
        $('#couponForm')[0].reset();
        $('#coupon_id').val('');
        $('.form-control').removeClass('is-invalid');
        $('.invalid-feedback').text('');
        $('#valueUnit').text('VND');
        $('#product_selection').hide();
        $('#usage_info').hide();
        $('#apply_all_yes').prop('checked', true);
        $('#is_active').prop('checked', true);
        $('#value').removeAttr('max');
    };

    // Clear validation on input
    $('.form-control').on('input change', function() {
        $(this).removeClass('is-invalid');
        $(this).siblings('.invalid-feedback').text('');
    });

    // Edit coupon
    $('#couponsTable').on('click', '.btn-edit', function(){
        const id = $(this).data('id');
        
        $.get(`<?= site_url('Dashboard/discount-coupons') ?>/${id}/edit`)
        .done(function(res){
            if (res.status === 'success') {
                updateToken(res.token);
                fillForm(res.coupon, res.product_skus || []);
                $('.modal-title').text('Sửa voucher');
                $('#couponModal').modal('show');
            } else {
                showToast('error', res.message || 'Lỗi khi tải dữ liệu');
            }
        })
        .fail(() => showToast('error', 'Lỗi kết nối'));
    });

    // Fill form with data
    const fillForm = (coupon, productSkus) => {
        $('#coupon_id').val(coupon.id);
        $('#code').val(coupon.code);
        $('#type').val(coupon.type).trigger('change');
        $('#value').val(coupon.value);
        $('#min_order_amount').val(coupon.min_order_amount);
        $('#usage_limit').val(coupon.usage_limit);
        $('#is_active').prop('checked', coupon.is_active == 1);
        
        // Dates
        if (coupon.start_date) {
            const startDate = new Date(coupon.start_date);
            $('#start_date').val(startDate.toISOString().slice(0, 16));
        }
        if (coupon.end_date) {
            const endDate = new Date(coupon.end_date);
            $('#end_date').val(endDate.toISOString().slice(0, 16));
        }
        
        // Apply all
        if (coupon.apply_all == 1) {
            $('#apply_all_yes').prop('checked', true);
            $('#product_selection').hide();
        } else {
            $('#apply_all_no').prop('checked', true);
            $('#product_selection').show();
            $('#product_skus').val(productSkus.join(', '));
        }
        
        // Usage info
        $('#used_count_display').text(coupon.used_count || 0);
        $('#usage_info').show();
    };

    // Save coupon
    $('#btnSave').on('click', function(){
        const $btn = $(this);
        const form = $('#couponForm')[0];
        const formData = new FormData(form);
        const id = $('#coupon_id').val();
        const url = id ? 
            `<?= site_url('Dashboard/discount-coupons') ?>/${id}/update` : 
            "<?= site_url('Dashboard/discount-coupons/store') ?>";

        // Handle checkbox
        if ($('#is_active').is(':checked')) {
            formData.set('is_active', '1');
        } else {
            formData.set('is_active', '0');
        }

        // Handle apply_all
        const applyAll = $('input[name="apply_all"]:checked').val();
        formData.set('apply_all', applyAll);

        $btn.prop('disabled', true)
            .html('<i class="fa fa-spinner fa-spin"></i> Đang lưu...');

        // Clear previous validation
        $('.form-control').removeClass('is-invalid');
        $('.invalid-feedback').text('');

        $.ajax({
            url: url,
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: { 'X-CSRF-TOKEN': csrfToken() }
        })
        .done(function(res){
            if (res.token) updateToken(res.token);
            
            if (res.status === 'success') {
                $('#couponModal').modal('hide');
                table.ajax.reload(null, false);
                showToast('success', res.message);
            } else {
                if (typeof res.message === 'object') {
                    // Handle validation errors
                    Object.keys(res.message).forEach(field => {
                        $(`#${field}`).addClass('is-invalid');
                        $(`#${field}`).siblings('.invalid-feedback').text(res.message[field]);
                    });
                    showToast('error', 'Vui lòng kiểm tra lại thông tin đã nhập');
                } else {
                    showToast('error', res.message);
                }
            }
        })
        .fail(function(xhr){
            const error = xhr.responseJSON?.message || 'Lỗi hệ thống';
            showToast('error', error);
        })
        .always(function(){
            $btn.prop('disabled', false)
                .html('<i class="fa fa-save"></i> Lưu voucher');
        });
    });

    // Toggle active
    $('#couponsTable').on('click', '.btn-toggle', function(){
        const id = $(this).data('id');
        
        $.post(`<?= site_url('Dashboard/discount-coupons') ?>/${id}/toggle-active`, {
            '<?= csrf_token() ?>': csrfToken()
        })
        .done(function(res){
            if (res.token) updateToken(res.token);
            
            if (res.status === 'success') {
                table.ajax.reload(null, false);
                showToast('success', res.message);
            } else {
                showToast('error', res.message);
            }
        });
    });

    // Delete coupon
    $('#couponsTable').on('click', '.btn-delete', function(){
        const id = $(this).data('id');
        
        if (confirm('Bạn có chắc chắn muốn xóa voucher này?')) {
            $.post(`<?= site_url('Dashboard/discount-coupons') ?>/${id}/delete`, {
                '<?= csrf_token() ?>': csrfToken()
            })
            .done(function(res){
                if (res.token) updateToken(res.token);
                
                if (res.status === 'success') {
                    table.ajax.reload(null, false);
                    showToast('success', res.message);
                } else {
                    showToast('error', res.message);
                }
            });
        }
    });

    // Duplicate coupon
    $('#couponsTable').on('click', '.btn-duplicate', function(){
        const id = $(this).data('id');
        
        $.post(`<?= site_url('Dashboard/discount-coupons') ?>/${id}/duplicate`, {
            '<?= csrf_token() ?>': csrfToken()
        })
        .done(function(res){
            if (res.token) updateToken(res.token);
            
            if (res.status === 'success') {
                table.ajax.reload(null, false);
                showToast('success', res.message);
            } else {
                showToast('error', res.message);
            }
        });
    });

    // Reset usage
    $('#btnResetUsage').on('click', function(){
        const id = $('#coupon_id').val();
        
        if (confirm('Bạn có chắc chắn muốn reset số lần sử dụng voucher này?')) {
            $.post(`<?= site_url('Dashboard/discount-coupons') ?>/${id}/reset-usage`, {
                '<?= csrf_token() ?>': csrfToken()
            })
            .done(function(res){
                if (res.token) updateToken(res.token);
                
                if (res.status === 'success') {
                    $('#used_count_display').text('0');
                    table.ajax.reload(null, false);
                    showToast('success', res.message);
                } else {
                    showToast('error', res.message);
                }
            });
        }
    });
});
</script>
<?= $this->endSection() ?>