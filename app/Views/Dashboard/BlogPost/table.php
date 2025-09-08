<?= $this->extend('Dashboard/layout') ?>
<?= $this->section('content') ?>

<meta name="csrf-token" content="<?= csrf_hash() ?>">

<div class="mb-3">
    <button id="btnAdd" class="btn btn-success">
        <i class="fa fa-plus"></i> Thêm bài viết
    </button>
</div>

<table id="blogPostsTable" class="display" style="width:100%">
    <thead>
        <tr>
            <th>STT</th>
            <th>Ảnh</th>
            <th>Tiêu đề</th>
            <th>Tác giả</th>
            <th>Danh mục</th>
            <th>Trạng thái</th>
            <th>Nổi bật</th>
            <th>Lượt xem</th>
            <th>Bình luận</th>
            <th>Ngày xuất bản</th>
            <th>Hành động</th>
        </tr>
    </thead>
    <tbody></tbody>
</table>

<!-- Modal Form -->
<div class="modal fade" id="blogPostModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <form id="blogPostForm" enctype="multipart/form-data">
            <?= csrf_field() ?>
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Thêm / Sửa bài viết</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="post_id">

                    <div class="row">
                        <!-- Cột trái - Thông tin chính -->
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label class="form-label">Tiêu đề bài viết *</label>
                                <input type="text" name="title" id="title" class="form-control" required>
                                <div class="invalid-feedback"></div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Tóm tắt bài viết *</label>
                                <textarea name="excerpt" id="excerpt" class="form-control" rows="3" required 
                                    placeholder="Tóm tắt ngắn gọn về bài viết (tối đa 500 ký tự)" maxlength="500"></textarea>
                                <small class="text-muted">
                                    <span id="excerptCount">0</span>/500 ký tự
                                </small>
                                <div class="invalid-feedback"></div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Nội dung bài viết *</label>
                                <textarea name="content" id="content" class="form-control" rows="12" required 
                                    placeholder="Nội dung chi tiết của bài viết"></textarea>
                                <div class="invalid-feedback"></div>
                            </div>

                            <!-- SEO Settings -->
                            <div class="card mt-3">
                                <div class="card-header">
                                    <h6 class="mb-0">Cài đặt SEO</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label">Tiêu đề SEO</label>
                                        <input type="text" name="meta_title" id="meta_title" class="form-control" 
                                            placeholder="Để trống sẽ dùng tiêu đề bài viết">
                                        <div class="invalid-feedback"></div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Mô tả SEO</label>
                                        <textarea name="meta_description" id="meta_description" class="form-control" 
                                            rows="2" maxlength="500" placeholder="Mô tả ngắn cho SEO"></textarea>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Cột phải - Cài đặt và ảnh -->
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">Cài đặt bài viết</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label">Tác giả *</label>
                                        <input type="text" name="author_name" id="author_name" class="form-control" required>
                                        <div class="invalid-feedback"></div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Danh mục *</label>
                                        <input type="text" name="category" id="category" class="form-control" required>
                                        <div class="invalid-feedback"></div>
                                    </div>

                                   

                                    <div class="mb-3">
                                        <label class="form-label">Trạng thái *</label>
                                        <select name="status" id="status" class="form-control" required>
                                            <option value="draft">Nháp</option>
                                            <option value="published">Đã xuất bản</option>
                                            <option value="archived">Lưu trữ</option>
                                        </select>
                                        <div class="invalid-feedback"></div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Ngày xuất bản</label>
                                        <input type="datetime-local" name="published_at" id="published_at" class="form-control">
                                        <small class="text-muted">Để trống sẽ dùng thời gian hiện tại</small>
                                    </div>

                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="is_featured" id="is_featured" value="1">
                                        <label class="form-check-label" for="is_featured">
                                            Bài viết nổi bật
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Ảnh đại diện -->
                            <div class="card mt-3">
                                <div class="card-header">
                                    <h6 class="mb-0">Ảnh đại diện *</h6>
                                </div>
                                <div class="card-body">
                                    <input type="file" name="featured_image" id="featured_image" class="form-control" accept="image/*">
                                    <small class="text-muted">Chọn ảnh (tối đa 5MB)</small>
                                    <div class="invalid-feedback"></div>
                                    
                                    <div class="mb-3 mt-2">
                                        <label class="form-label">Alt text cho ảnh</label>
                                        <input type="text" name="image_alt" id="image_alt" class="form-control" 
                                            placeholder="Mô tả ảnh cho SEO">
                                    </div>

                                    <div id="featuredImagePreview" class="mt-2">
                                        <!-- Preview image will be shown here -->
                                    </div>
                                </div>
                            </div>

                            
                           
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" id="btnSave" class="btn btn-primary">
                        <i class="fa fa-save"></i> Lưu bài viết
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
.post-image { 
    width: 60px; 
    height: 60px; 
    object-fit: cover; 
    border-radius: 8px;
    border: 1px solid #ddd;
}
.preview-image { 
    width: 100%; 
    max-width: 200px;
    height: 150px; 
    object-fit: cover; 
    border-radius: 8px; 
    border: 2px dashed #ddd;
    display: block;
    margin: 10px auto;
}
.gallery-thumb {
    width: 60px;
    height: 60px;
    object-fit: cover;
    border-radius: 4px;
    margin: 2px;
    border: 1px solid #ddd;
}
.text-truncate-title {
    max-width: 250px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.badge-status {
    font-size: 0.8em;
    padding: 0.4em 0.8em;
}
.status-draft { background-color: #6c757d; }
.status-published { background-color: #28a745; }
.status-archived { background-color: #ffc107; color: #000; }

.tag-item {
    display: inline-block;
    background: #e9ecef;
    padding: 2px 8px;
    margin: 2px;
    border-radius: 12px;
    font-size: 0.8em;
    color: #495057;
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
    const table = $('#blogPostsTable').DataTable({
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
            url: "<?= site_url('Dashboard/blog-posts/list') ?>",
            dataSrc: 'data',
            complete: function(xhr) {
                const json = xhr.responseJSON;
                if (json?.token) updateToken(json.token);
            }
        },
        columns: [
            { data: null, render: (d, t, r, m) => m.row + 1, width: '50px' },
            { 
                data: 'featured_image', 
                render: d => d ? 
                    `<img src="<?= base_url() ?>/${d}" class="post-image" alt="featured image">` : 
                    '<i class="fa fa-image text-muted fa-2x"></i>',
                width: '80px',
                orderable: false
            },
            { 
                data: 'title',
                
            },
            { data: 'author_name' },
            { 
                data: 'category',
                render: d => `<span class="badge bg-info">${d}</span>`
            },
            { 
                data: 'status', 
                render: d => {
                    const statusMap = {
                        'draft': '<span class="badge status-draft">Nháp</span>',
                        'published': '<span class="badge status-published">Đã xuất bản</span>',
                        'archived': '<span class="badge status-archived">Lưu trữ</span>'
                    };
                    return statusMap[d] || d;
                },
                width: '120px'
            },
            { 
                data: 'is_featured', 
                render: d => d == 1 ? 
                    '<i class="fa fa-star text-warning" title="Nổi bật"></i>' : 
                    '<i class="fa fa-star-o text-muted" title="Thường"></i>',
                width: '80px'
            },
            { 
                data: 'view_count',
                render: d => `<span class="badge bg-secondary">${d || 0}</span>`,
                width: '80px'
            },
            { 
                data: 'comment_count',
                render: d => `<span class="badge bg-primary">${d || 0}</span>`,
                width: '80px'
            },
            {
                data: 'published_at',
                render: d => d ? `<small>${formatDate(d)}</small>` : '<em class="text-muted">Chưa xuất bản</em>',
                width: '120px'
            },
            { 
                data: 'id', 
                render: (d, t, r) => `
                    <div class="btn-group" role="group">
                        <button class="btn btn-sm btn-info btn-view" data-id="${d}" title="Xem chi tiết">
                            <i class="fa fa-eye"></i>
                        </button>
                        <button class="btn btn-sm btn-primary btn-edit" data-id="${d}" title="Sửa">
                            <i class="fa fa-edit"></i>
                        </button>
                        <button class="btn btn-sm ${r.is_featured == 1 ? 'btn-warning' : 'btn-outline-warning'} btn-featured" 
                            data-id="${d}" title="Toggle nổi bật">
                            <i class="fa fa-star"></i>
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
        order: [[9, 'desc']] // Sắp xếp theo ngày tạo
    });

    // Character counters
    $('#excerpt').on('input', function() {
        const current = $(this).val().length;
        $('#excerptCount').text(current);
    });

    // Show add modal
    $('#btnAdd').on('click', function(){
        resetForm();
        $('.modal-title').text('Thêm bài viết');
        // Set default author name from session if available
        if (typeof userSession !== 'undefined' && userSession.name) {
            $('#author_name').val(userSession.name);
        }
        $('#blogPostModal').modal('show');
        setTimeout(() => $('#title').focus(), 500);
    });

    // Reset form function
    const resetForm = () => {
        $('#blogPostForm')[0].reset();
        $('#post_id').val('');
        $('.form-control').removeClass('is-invalid');
        $('.invalid-feedback').text('');
        $('#excerptCount').text('0');
        $('#featuredImagePreview, #galleryPreview').empty();
        $('#is_featured').prop('checked', false);
    };

    // Clear validation on input
    $('.form-control').on('input change', function() {
        $(this).removeClass('is-invalid');
        $(this).siblings('.invalid-feedback').text('');
    });

    // Preview featured image
    $('#featured_image').on('change', function(){
        const file = this.files[0];
        if (file) {
            if (file.size > 5242880) { // 5MB
                showToast('error', 'Dung lượng ảnh không được quá 5MB');
                $(this).val('');
                return;
            }
            
            const reader = new FileReader();
            reader.onload = e => {
                $('#featuredImagePreview').html(`
                    <div class="text-center">
                        <img src="${e.target.result}" class="preview-image" alt="Preview">
                        <div class="mt-2">
                            <small class="text-muted">Ảnh đại diện</small>
                        </div>
                    </div>
                `);
            };
            reader.readAsDataURL(file);
        }
    });

  

    // Auto-fill meta title from title
    $('#title').on('blur', function() {
        const title = $(this).val();
        if (title && !$('#meta_title').val()) {
            $('#meta_title').val(title);
        }
    });

    // View post
    $('#blogPostsTable').on('click', '.btn-view', function(){
        const id = $(this).data('id');
        window.open(`<?= site_url('Dashboard/blog-posts') ?>/${id}/view`, '_blank');
    });

    // Edit post
    $('#blogPostsTable').on('click', '.btn-edit', function(){
        const id = $(this).data('id');
        
        $.get(`<?= site_url('Dashboard/blog-posts') ?>/${id}/edit`)
        .done(function(res){
            if (res.status === 'success') {
                updateToken(res.token);
                fillForm(res.post);
                $('.modal-title').text('Sửa bài viết');
                $('#blogPostModal').modal('show');
            } else {
                showToast('error', res.message || 'Lỗi khi tải dữ liệu');
            }
        })
        .fail(() => showToast('error', 'Lỗi kết nối'));
    });

    // Fill form with data
    const fillForm = (post) => {
        $('#post_id').val(post.id);
        $('#title').val(post.title);
        $('#excerpt').val(post.excerpt || '');
        $('#content').val(post.content || '');
        $('#author_name').val(post.author_name);
        $('#category').val(post.category);
        $('#status').val(post.status);
        $('#meta_title').val(post.meta_title || '');
        $('#meta_description').val(post.meta_description || '');
        $('#image_alt').val(post.image_alt || '');
        $('#is_featured').prop('checked', post.is_featured == 1);
        
     
        
        // Published at
        if (post.published_at) {
            const date = new Date(post.published_at);
            const formattedDate = date.toISOString().slice(0, 16);
            $('#published_at').val(formattedDate);
        }
        
        // Update character count
        $('#excerptCount').text((post.excerpt || '').length);
        
        // Show current featured image
        if (post.featured_image) {
            $('#featuredImagePreview').html(`
                <div class="text-center">
                    <img src="<?= base_url() ?>/${post.featured_image}" class="preview-image" alt="Current image">
                    <div class="mt-2">
                        <small class="text-muted">Ảnh hiện tại</small>
                    </div>
                </div>
            `);
        }
        
     
    };

    // Toggle featured
    $('#blogPostsTable').on('click', '.btn-featured', function(){
        const id = $(this).data('id');
        const $btn = $(this);
        
        $.post(`<?= site_url('Dashboard/blog-posts') ?>/${id}/toggle-featured`, {
            '<?= csrf_token() ?>': csrfToken()
        })
        .done(function(res){
            if (res.token) updateToken(res.token);
            
            if (res.status === 'success') {
                showToast('success', res.message);
                if (res.is_featured) {
                    $btn.removeClass('btn-outline-warning').addClass('btn-warning');
                } else {
                    $btn.removeClass('btn-warning').addClass('btn-outline-warning');
                }
            } else {
                showToast('error', res.message);
            }
        })
        .fail(() => showToast('error', 'Lỗi khi cập nhật'));
    });

    // Save post
    $('#btnSave').on('click', function(){
        const $btn = $(this);
        const form = $('#blogPostForm')[0];
        const formData = new FormData(form);
        const id = $('#post_id').val();
        const url = id ? 
            `<?= site_url('Dashboard/blog-posts') ?>/${id}/update` : 
            "<?= site_url('Dashboard/blog-posts/store') ?>";

        // Xử lý checkbox is_featured
        if ($('#is_featured').is(':checked')) {
            formData.set('is_featured', '1');
        } else {
            formData.set('is_featured', '0');
        }



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
                $('#blogPostModal').modal('hide');
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
                .html('<i class="fa fa-save"></i> Lưu bài viết');
        });
    });

    // Delete post
    $('#blogPostsTable').on('click', '.btn-delete', function(){
        const $row = $(this).closest('tr');
        const postData = table.row($row).data();
        const postTitle = postData.title;
        
        if (!confirm(`Bạn có chắc muốn xóa bài viết "${postTitle}"?\n\nLưu ý: Thao tác này sẽ xóa mềm bài viết!`)) return;
        
        const id = $(this).data('id');
        
        $.post(`<?= site_url('Dashboard/blog-posts') ?>/${id}/delete`, {
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
    $('#blogPostModal').on('hidden.bs.modal', resetForm);

    // Enter key submit (except in textarea)
    $('#blogPostForm').on('keypress', function(e) {
        if (e.which === 13 && e.target.tagName !== 'TEXTAREA') {
            e.preventDefault();
            $('#btnSave').click();
        }
    });

    // Status change handler
    $('#status').on('change', function() {
        const status = $(this).val();
        if (status === 'published' && !$('#published_at').val()) {
            const now = new Date();
            const formattedDate = now.toISOString().slice(0, 16);
            $('#published_at').val(formattedDate);
        }
    });
});
</script>

<?= $this->endSection() ?>