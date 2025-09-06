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
            <th>Ảnh</th>
            <th>Tên danh mục</th>
            <th>Slug</th>
            <th>Danh mục cha</th>
            <th>Sắp xếp</th>
            <th>Trạng thái</th>
            <th>Ngày tạo</th>
            <th>Hành động</th>
        </tr>
    </thead>
    <tbody></tbody>
</table>

<!-- Modal Form -->
<div class="modal fade" id="categoryModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <form id="categoryForm" enctype="multipart/form-data">
            <?= csrf_field() ?>
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Thêm / Sửa danh mục</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="category_id">

                    <div class="row">
                        <div class="col-md-8">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Tên danh mục *</label>
                                        <input type="text" name="name" id="name" class="form-control" required>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Danh mục cha</label>
                                        <select name="parent_id" id="parent_id" class="form-control">
                                            <option value="">-- Danh mục gốc --</option>
                                        </select>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Thứ tự sắp xếp</label>
                                        <input type="number" name="sort_order" id="sort_order" class="form-control" value="0" min="0">
                                        <div class="invalid-feedback"></div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Trạng thái</label>
                                        <select name="is_active" id="is_active" class="form-control">
                                            <option value="1">Hoạt động</option>
                                            <option value="0">Không hoạt động</option>
                                        </select>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Mô tả</label>
                                <textarea name="description" id="description" class="form-control" rows="4" placeholder="Mô tả danh mục (tối đa 1000 ký tự)" maxlength="1000"></textarea>
                                <small class="text-muted">
                                    <span id="charCount">0</span>/1000 ký tự
                                </small>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Ảnh danh mục</label>
                                <input type="file" name="image_url" id="image" class="form-control" accept="image/*">
                                <small class="text-muted">Chọn ảnh (tối đa 2MB)</small>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div id="imagePreview" class="mt-2">
                                <!-- Preview image will be shown here -->
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
.category-image { 
    width: 60px; 
    height: 60px; 
    object-fit: cover; 
    border-radius: 8px;
    border: 1px solid #ddd;
}
.preview-image { 
    width: 100%; 
    max-width: 200px;
    height: 200px; 
    object-fit: cover; 
    border-radius: 8px; 
    border: 2px dashed #ddd;
    display: block;
    margin: 10px auto;
}
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
.parent-category {
    color: #0066cc;
    font-weight: 500;
}
.child-category {
    color: #666;
    font-style: italic;
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

    // Load parent categories for dropdown
    const loadParentCategories = () => {
        $.get("<?= site_url('Dashboard/categories/getParentCategories') ?>")
        .done(function(res) {
            if (res.status === 'success') {
                updateToken(res.token);
                const $select = $('#parent_id');
                $select.find('option:not(:first)').remove(); // Keep first option
                
                res.data.forEach(function(category) {
                    $select.append(`<option value="${category.id}">${category.name}</option>`);
                });
            }
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
                data: 'image_url', 
                render: d => d ? 
                    `<img src="<?= base_url() ?>/${d}" class="category-image" alt="category image">` : 
                    '<i class="fa fa-image text-muted fa-2x"></i>',
                width: '80px',
                orderable: false
            },
            { 
                data: 'name',
                render: (d, t, r) => {
                    const prefix = r.parent_id ? '└─ ' : '';
                    const cssClass = r.parent_id ? 'child-category' : 'parent-category';
                    return `<span class="${cssClass}">${prefix}<strong>${d}</strong></span>`;
                }
            },
            { 
                data: 'slug',
                render: d => `<code class="text-muted small">${d}</code>`
            },
            {
                data: 'parent_name',
                render: d => d ? `<span class="badge bg-info">${d}</span>` : '<em class="text-muted">Danh mục gốc</em>'
            },
            { 
                data: 'sort_order',
                render: d => `<span class="badge bg-secondary">${d}</span>`,
                width: '80px'
            },
            { 
                data: 'is_active', 
                render: d => d == 1 ? 
                    '<span class="badge bg-success badge-status">Hoạt động</span>' : 
                    '<span class="badge bg-danger badge-status">Ngưng</span>',
                width: '100px'
            },
            {
                data: 'created_at',
                render: d => `<small>${formatDate(d)}</small>`,
                width: '120px'
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
        order: [[5, 'asc']] // Sắp xếp theo sort_order
    });

    // Character counter for description
    $('#description').on('input', function() {
        const current = $(this).val().length;
        $('#charCount').text(current);
        
        if (current > 800) {
            $('#charCount').addClass('text-warning');
        } else if (current > 950) {
            $('#charCount').addClass('text-danger').removeClass('text-warning');
        } else {
            $('#charCount').removeClass('text-warning text-danger');
        }
    });

    // Show add modal
    $('#btnAdd').on('click', function(){
        resetForm();
        loadParentCategories();
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
        $('#imagePreview').empty();
        $('#parent_id').find('option:not(:first)').remove();
    };

    // Clear validation on input
    $('.form-control').on('input change', function() {
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
                
                // Load parent categories first
                const $select = $('#parent_id');
                $select.find('option:not(:first)').remove();
                
                res.parentCategories.forEach(function(category) {
                    $select.append(`<option value="${category.id}">${category.name}</option>`);
                });
                
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
        $('#parent_id').val(category.parent_id || '');
        $('#sort_order').val(category.sort_order);
        $('#is_active').val(category.is_active);
        
        // Update character count
        $('#charCount').text((category.description || '').length);
        
        // Show current image
        if (category.image_url) {
            $('#imagePreview').html(`
                <div class="text-center">
                    <img src="<?= base_url() ?>/${category.image_url}" class="preview-image" alt="Current image">
                    <div class="mt-2">
                        <small class="text-muted">Ảnh hiện tại</small>
                    </div>
                </div>
            `);
        }
    };

    // Preview image
    $('#image').on('change', function(){
        const file = this.files[0];
        if (file) {
            // Validate file size (2MB)
            if (file.size > 2048000) {
                showToast('error', 'Dung lượng ảnh không được quá 2MB');
                $(this).val('');
                return;
            }
            
            const reader = new FileReader();
            reader.onload = e => {
                $('#imagePreview').html(`
                    <div class="text-center">
                        <img src="${e.target.result}" class="preview-image" alt="Preview">
                        <div class="mt-2">
                            <small class="text-muted">Ảnh mới</small>
                        </div>
                    </div>
                `);
            };
            reader.readAsDataURL(file);
        }
    });

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
        
        if (!confirm(`Bạn có chắc muốn xóa danh mục "${categoryName}"?\n\nLưu ý: Thao tác này sẽ xóa mềm danh mục!`)) return;
        
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

    // Enter key submit (except in textarea)
    $('#categoryForm').on('keypress', function(e) {
        if (e.which === 13 && e.target.tagName !== 'TEXTAREA') {
            e.preventDefault();
            $('#btnSave').click();
        }
    });

    // Auto-generate sort order
    $('#name').on('blur', function() {
        if (!$('#category_id').val() && !$('#sort_order').val()) {
            // For new category, set sort_order to next available number
            const currentMax = Math.max(...table.data().toArray().map(row => parseInt(row.sort_order) || 0));
            $('#sort_order').val(currentMax + 1);
        }
    });
});
</script>

<?= $this->endSection() ?>