<?= $this->extend('Dashboard/layout') ?>
<?= $this->section('content') ?>

<meta name="csrf-token" content="<?= csrf_hash() ?>">

<div class="mb-3">
    <button id="btnAdd" class="btn btn-success">
        <i class="fa fa-plus"></i> Thêm danh mục
    </button>
</div>

<table id="categoriesTable" class="display" style="width:100%">
    <thead>
        <tr>
            <th>STT</th>
            <th>Tên danh mục</th>
            <th>Slug</th>
            <th>Mô tả</th>
            <th>Trạng thái</th>
            <th>Ngày tạo</th>
            <th>Hành động</th>
        </tr>
    </thead>
    <tbody></tbody>
</table>

<!-- Modal Form -->
<div class="modal fade" id="categoryModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form id="categoryForm">
            <?= csrf_field() ?>
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Thêm / Sửa danh mục</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="category_id">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Tên danh mục *</label>
                                <input type="text" name="name" id="name" class="form-control" required>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Trạng thái</label>
                                <select name="status" id="status" class="form-control">
                                    <option value="1">Hoạt động</option>
                                    <option value="0">Không hoạt động</option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Mô tả</label>
                                <textarea name="description" id="description" class="form-control" rows="4" placeholder="Mô tả danh mục (tối đa 500 ký tự)" maxlength="500"></textarea>
                                <small class="text-muted">
                                    <span id="charCount">0</span>/500 ký tự
                                </small>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" id="btnSave" class="btn btn-primary">
                        <i class="fa fa-save"></i> Lưu
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fa fa-times"></i> Đóng
                    </button>
                </div>
            </div>
        </form>
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
.text-truncate-desc {
    max-width: 200px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.badge-status {
    font-size: 0.8em;
    padding: 0.4em 0.8em;
}
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

    // DataTable
    const table = $('#categoriesTable').DataTable({
        processing: true,
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
            url: "<?= site_url('Dashboard/categories/list') ?>",
            dataSrc: 'data',
            complete: function(xhr) {
                const json = xhr.responseJSON;
                if (json?.token) updateToken(json.token);
            }
        },
        columns: [
            { data: null, render: (d, t, r, m) => m.row + 1, width: '50px' },
            { 
                data: 'name',
                render: (d, t, r) => `<strong>${d}</strong>`
            },
            { 
                data: 'slug',
                render: d => `<code class="text-muted">${d}</code>`
            },
            { 
                data: 'description',
                render: d => {
                    if (!d) return '<em class="text-muted">Chưa có mô tả</em>';
                    return d.length > 50 
                        ? `<span title="${d}" class="text-truncate-desc">${d.substring(0, 50)}...</span>`
                        : d;
                }
            },
            { 
                data: 'status', 
                render: d => d == 1 ? 
                    '<span class="badge bg-success badge-status">Hoạt động</span>' : 
                    '<span class="badge bg-danger badge-status">Ngưng</span>',
                width: '100px'
            },
            {
                data: 'created_at',
                render: d => formatDate(d),
                width: '150px'
            },
            { 
                data: 'id', 
                render: d => `
                    <div class="btn-group" role="group">
                        <button class="btn btn-sm btn-primary btn-edit" data-id="${d}" title="Sửa">
                            <i class="fa fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-danger btn-delete" data-id="${d}" title="Xóa">
                            <i class="fa fa-trash"></i>
                        </button>
                    </div>
                `,
                width: '100px',
                orderable: false
            }
        ],
        order: [[1, 'asc']] // Sắp xếp theo tên
    });

    // Character counter for description
    $('#description').on('input', function() {
        const current = $(this).val().length;
        $('#charCount').text(current);
        
        if (current > 450) {
            $('#charCount').addClass('text-warning');
        } else if (current > 480) {
            $('#charCount').addClass('text-danger').removeClass('text-warning');
        } else {
            $('#charCount').removeClass('text-warning text-danger');
        }
    });

    // Show add modal
    $('#btnAdd').on('click', function(){
        resetForm();
        $('.modal-title').text('Thêm danh mục');
        $('#categoryModal').modal('show');
        setTimeout(() => $('#name').focus(), 500);
    });

    // Reset form function
    const resetForm = () => {
        $('#categoryForm')[0].reset();
        $('#category_id').val('');
        $('.form-control').removeClass('is-invalid');
        $('.invalid-feedback').text('');
        $('#charCount').text('0').removeClass('text-warning text-danger');
    };

    // Clear validation on input
    $('.form-control').on('input', function() {
        $(this).removeClass('is-invalid');
        $(this).siblings('.invalid-feedback').text('');
    });

    // Edit category
    $('#categoriesTable').on('click', '.btn-edit', function(){
        const id = $(this).data('id');
        
        $.get(`<?= site_url('Dashboard/categories') ?>/${id}/edit`)
        .done(function(res){
            if (res.status === 'success') {
                updateToken(res.token);
                fillForm(res.category);
                $('.modal-title').text('Sửa danh mục');
                $('#categoryModal').modal('show');
                setTimeout(() => $('#name').focus(), 500);
            } else {
                showToast('error', res.message || 'Lỗi khi tải dữ liệu');
            }
        })
        .fail(() => showToast('error', 'Lỗi kết nối'));
    });

    // Fill form with data
    const fillForm = (category) => {
        $('#category_id').val(category.id);
        $('#name').val(category.name);
        $('#description').val(category.description || '');
        $('#status').val(category.status);
        
        // Update character count
        $('#charCount').text((category.description || '').length);
    };

    // Save category
    $('#btnSave').on('click', function(){
        const $btn = $(this);
        const form = $('#categoryForm')[0];
        const formData = new FormData(form);
        const id = $('#category_id').val();
        const url = id ? 
            `<?= site_url('Dashboard/categories') ?>/${id}/update` : 
            "<?= site_url('Dashboard/categories/store') ?>";

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
                $('#categoryModal').modal('hide');
                table.ajax.reload(null, false);
                showToast('success', res.message);
            } else {
                if (typeof res.message === 'object') {
                    // Handle validation errors
                    Object.keys(res.message).forEach(field => {
                        $(`#${field}`).addClass('is-invalid');
                        $(`#${field}`).siblings('.invalid-feedback').text(res.message[field]);
                    });
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
                .html('<i class="fa fa-save"></i> Lưu');
        });
    });

    // Delete category
    $('#categoriesTable').on('click', '.btn-delete', function(){
        const $row = $(this).closest('tr');
        const categoryName = table.row($row).data().name;
        
        if (!confirm(`Bạn có chắc muốn xóa danh mục "${categoryName}"?\n\nLưu ý: Thao tác này không thể hoàn tác!`)) return;
        
        const id = $(this).data('id');
        
        $.post(`<?= site_url('Dashboard/categories') ?>/${id}/delete`, {
            '<?= csrf_token() ?>': csrfToken()
        })
        .done(function(res){
            if (res.token) updateToken(res.token);
            
            if (res.status === 'success') {
                table.ajax.reload(null, false);
                showToast('success', res.message || 'Xóa thành công');
            } else {
                showToast('error', res.message);
            }
        })
        .fail(() => showToast('error', 'Lỗi khi xóa'));
    });

    // Reset when modal closes
    $('#categoryModal').on('hidden.bs.modal', resetForm);

    // Enter key submit
    $('#categoryForm').on('keypress', function(e) {
        if (e.which === 13 && !e.shiftKey) {
            e.preventDefault();
            $('#btnSave').click();
        }
    });
});
</script>

<?= $this->endSection() ?>