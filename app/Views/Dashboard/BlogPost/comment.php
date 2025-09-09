<?= $this->extend('Dashboard/layout') ?>
<?= $this->section('content') ?>

<meta name="csrf-token" content="<?= csrf_hash() ?>">

<div class="mb-3 d-flex justify-content-between align-items-center">
    <div>
        <h4><i class="fa fa-comments"></i> Quản lý bình luận</h4>
    </div>
    <div class="btn-group">
        <button id="btnShowAll" class="btn btn-secondary">
            <i class="fa fa-list"></i> Tất cả
        </button>
        <button id="btnShowPending" class="btn btn-warning">
            <i class="fa fa-clock-o"></i> Chờ duyệt
        </button>
        <button id="btnShowApproved" class="btn btn-success">
            <i class="fa fa-check"></i> Đã duyệt
        </button>
    </div>
</div>

<table id="commentsTable" class="display" style="width:100%">
    <thead>
        <tr>
            <th>STT</th>
            <th>Bài viết</th>
            <th>Người bình luận</th>
            <th>Email</th>
            <th>Nội dung</th>
            <th>Loại</th>
            <th>Trạng thái</th>
            <th>Ngày bình luận</th>
            <th>Hành động</th>
        </tr>
    </thead>
    <tbody></tbody>
</table>

<!-- CSS & JS -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<style>
.comment-content {
    max-width: 300px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.comment-content-full {
    white-space: normal;
    word-wrap: break-word;
    max-width: 300px;
    display: none;
}
.badge-status {
    font-size: 0.8em;
    padding: 0.4em 0.8em;
}
.comment-pending { background-color: #ffc107; color: #000; }
.comment-approved { background-color: #28a745; }
.comment-rejected { background-color: #dc3545; }
.post-link {
    color: #0066cc;
    text-decoration: none;
    font-weight: 500;
}
.post-link:hover {
    text-decoration: underline;
}
.email-text {
    font-family: monospace;
    font-size: 0.9em;
    color: #666;
}
.comment-type-member {
    background-color: #17a2b8;
}
.comment-type-guest {
    background-color: #6c757d;
}
.comment-type-reply {
    background-color: #fd7e14;
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
    const table = $('#commentsTable').DataTable({
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
            url: "<?= site_url('Dashboard/blog-comments/list') ?>",
            dataSrc: 'data',
            complete: function(xhr) {
                const json = xhr.responseJSON;
                if (json?.token) updateToken(json.token);
            }
        },
        columns: [
            { data: null, render: (d, t, r, m) => m.row + 1, width: '50px' },
            { 
                data: 'post_title',
                render: (d, t, r) => `
                    <a href="<?= site_url('Dashboard/blog-posts') ?>/${r.post_id}/view" 
                       class="post-link" target="_blank" title="Xem bài viết">
                        ${d}
                    </a>
                    <br><small class="text-muted">Slug: ${r.post_slug}</small>
                `,
                width: '200px'
            },
            { 
                data: 'author_name',
                render: d => `<strong>${d}</strong>`
            },
            { 
                data: 'author_email',
                render: d => `<span class="email-text">${d}</span>`,
                width: '150px'
            },
            { 
                data: 'comment',
                render: d => {
                    const shortComment = d.length > 100 ? d.substring(0, 100) + '...' : d;
                    return `
                        <div class="comment-content" title="${d.replace(/"/g, '&quot;')}">${shortComment}</div>
                        <div class="comment-content-full">${d}</div>
                        ${d.length > 100 ? '<a href="#" class="btn-expand-comment" style="font-size: 0.8em;">Xem thêm</a>' : ''}
                    `;
                },
                width: '250px',
                orderable: false
            },
            { 
                data: null,
                render: (d, t, r) => {
                    let typeClass = 'comment-type-guest';
                    let typeText = 'Khách';
                    
                    if (r.customer_id) {
                        typeClass = 'comment-type-member';
                        typeText = 'Thành viên';
                    }
                    
                    if (r.parent_id) {
                        typeClass = 'comment-type-reply';
                        typeText = 'Trả lời';
                    }
                    
                    return `<span class="badge ${typeClass}">${typeText}</span>`;
                },
                width: '100px'
            },
            { 
                data: 'is_approved', 
                render: d => d == 1 ? 
                    '<span class="badge comment-approved badge-status">Đã duyệt</span>' : 
                    '<span class="badge comment-pending badge-status">Chờ duyệt</span>',
                width: '100px'
            },
            {
                data: 'created_at',
                render: d => `<small>${formatDate(d)}</small>`,
                width: '120px'
            },
            { 
                data: 'id', 
                render: (d, t, r) => {
                    let actions = `
                        <div class="btn-group" role="group">
                            <button class="btn btn-sm btn-info btn-view-post" data-post-id="${r.post_id}" title="Xem bài viết">
                                <i class="fa fa-eye"></i>
                            </button>
                    `;
                    
                    if (r.is_approved == 0) {
                        actions += `
                            <button class="btn btn-sm btn-success btn-approve" data-id="${d}" title="Duyệt">
                                <i class="fa fa-check"></i>
                            </button>
                        `;
                    } else {
                        actions += `
                            <button class="btn btn-sm btn-warning btn-reject" data-id="${d}" title="Hủy duyệt">
                                <i class="fa fa-times"></i>
                            </button>
                        `;
                    }
                    
                    actions += `
                            <button class="btn btn-sm btn-danger btn-delete" data-id="${d}" title="Xóa">
                                <i class="fa fa-trash"></i>
                            </button>
                        </div>
                    `;
                    
                    return actions;
                },
                width: '120px',
                orderable: false
            }
        ],
        order: [[7, 'desc']] // Sắp xếp theo ngày tạo
    });

    // Filter buttons
    $('#btnShowAll').on('click', function() {
        table.column(6).search('').draw();
        $('.btn-group .btn').removeClass('active');
        $(this).addClass('active');
    });

    $('#btnShowPending').on('click', function() {
        table.column(6).search('Chờ duyệt').draw();
        $('.btn-group .btn').removeClass('active');
        $(this).addClass('active');
    });

    $('#btnShowApproved').on('click', function() {
        table.column(6).search('Đã duyệt').draw();
        $('.btn-group .btn').removeClass('active');
        $(this).addClass('active');
    });

    // Expand/collapse comment
    $('#commentsTable').on('click', '.btn-expand-comment', function(e) {
        e.preventDefault();
        const $row = $(this).closest('td');
        const $short = $row.find('.comment-content');
        const $full = $row.find('.comment-content-full');
        
        if ($full.is(':visible')) {
            $full.hide();
            $short.show();
            $(this).text('Xem thêm');
        } else {
            $short.hide();
            $full.show();
            $(this).text('Thu gọn');
        }
    });

    // View post
    $('#commentsTable').on('click', '.btn-view-post', function(){
        const postId = $(this).data('post-id');
        window.open(`<?= site_url('Dashboard/blog-posts') ?>/${postId}/view`, '_blank');
    });

    // Approve comment
    $('#commentsTable').on('click', '.btn-approve', function(){
        const id = $(this).data('id');
        
        $.post(`<?= site_url('Dashboard/blog-comments') ?>/${id}/approve`, {
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
        })
        .fail(() => showToast('error', 'Lỗi khi duyệt bình luận'));
    });

    // Reject comment
    $('#commentsTable').on('click', '.btn-reject', function(){
        const id = $(this).data('id');
        
        if (!confirm('Bạn có chắc muốn hủy duyệt bình luận này?')) return;
        
        $.post(`<?= site_url('Dashboard/blog-comments') ?>/${id}/reject`, {
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
        })
        .fail(() => showToast('error', 'Lỗi khi từ chối bình luận'));
    });

    // Delete comment
    $('#commentsTable').on('click', '.btn-delete', function(){
        if (!confirm('Bạn có chắc muốn xóa bình luận này?')) return;
        
        const id = $(this).data('id');
        
        $.post(`<?= site_url('Dashboard/blog-comments') ?>/${id}/delete`, {
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

    // Set default filter to show all
    $('#btnShowAll').addClass('active');
});
</script>

<?= $this->endSection() ?>