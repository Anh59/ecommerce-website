<?= $this->extend('Dashboard/layout') ?>
<?= $this->section('content') ?>

<meta name="csrf-token" content="<?= csrf_hash() ?>">

<div class="mb-3">
    <button id="btnAdd" class="btn btn-success">
        <i class="fa fa-plus"></i> Thêm thương hiệu
    </button>
</div>

<table id="brandsTable" class="display" style="width:100%">
    <thead>
        <tr>
            <th>STT</th>
            <th>Logo</th>
            <th>Tên</th>
            <th>Website</th>
            <th>Quốc gia</th>
            <th>Kích hoạt</th>
            <th>Sắp xếp</th>
            <th>Hành động</th>
        </tr>
    </thead>
    <tbody></tbody>
</table>

<!-- Modal Form -->
<div class="modal fade" id="brandModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <form id="brandForm" enctype="multipart/form-data">
      <?= csrf_field() ?>
      <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">Thêm / Sửa thương hiệu</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
            <input type="hidden" name="id" id="brand_id">

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Tên thương hiệu *</label>
                        <input type="text" name="name" id="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Website</label>
                        <input type="text" name="website" id="website" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Quốc gia</label>
                        <input type="text" name="country" id="country" class="form-control">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Logo</label>
                        <input type="file" name="logo_url" id="logo" class="form-control" accept="image/*">
                        <div id="logoPreview" class="mt-2"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kích hoạt</label>
                        <select name="is_active" id="is_active" class="form-control">
                            <option value="1">Hoạt động</option>
                            <option value="0">Không hoạt động</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Sắp xếp</label>
                        <input type="number" name="sort_order" id="sort_order" class="form-control" value="0">
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
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<style>
.logo-image { width: 50px; height: 50px; object-fit: cover; border-radius: 4px; }
.preview-image { width: 100px; height: 100px; object-fit: cover; border-radius: 4px; margin: 5px; }
</style>

<script>
$(document).ready(function(){
    function csrfToken() { return $('meta[name="csrf-token"]').attr('content'); }
    function updateToken(token) { 
        $('meta[name="csrf-token"]').attr('content', token); 
        $('input[name="<?= csrf_token() ?>"]').val(token); 
    }

    // DataTable
    var table = $('#brandsTable').DataTable({
        processing: true,
        ajax: {
            url: "<?= site_url('Dashboard/brands/list') ?>",
            dataSrc: 'data',
            complete: function(xhr) {
                var json = xhr.responseJSON;
                if (json && json.token) updateToken(json.token);
            }
        },
        columns: [
            { data: null, render: (d,t,r,m)=> m.row + 1 },
            { data: 'logo_url', render: d => d ? `<img src="<?= base_url() ?>/${d}" class="logo-image">` : '<i class="fa fa-image text-muted"></i>' },
            { data: 'name' },
            { data: 'website' },
            { data: 'country' },
            { data: 'is_active', render: d => d == 1 ? '<span class="badge bg-success">✔</span>' : '<span class="badge bg-danger">✘</span>' },
            { data: 'sort_order' },
            { data: 'id', render: d => `
                <div class="btn-group">
                    <button class="btn btn-sm btn-primary btn-edit" data-id="${d}"><i class="fa fa-edit"></i></button>
                    <button class="btn btn-sm btn-danger btn-delete" data-id="${d}"><i class="fa fa-trash"></i></button>
                </div>
            `}
        ]
    });

    // Show add modal
    $('#btnAdd').on('click', function(){
        $('#brandForm')[0].reset();
        $('#brand_id').val('');
        $('#logoPreview').html('');
        $('.modal-title').text('Thêm thương hiệu');
        $('#brandModal').modal('show');
    });

    // Edit brand
    $('#brandsTable').on('click', '.btn-edit', function(){
        let id = $(this).data('id');
        $.get("<?= site_url('Dashboard/brands') ?>/" + id + "/edit", function(res){
            if(res.status === 'success'){
                updateToken(res.token);
                const b = res.brand;
                $('#brand_id').val(b.id);
                $('#name').val(b.name);
                $('#website').val(b.website);
                $('#country').val(b.country);
                $('#is_active').val(b.is_active);
                $('#sort_order').val(b.sort_order);

                if(b.logo_url){
                    $('#logoPreview').html(`<div class="preview-image"><img src="<?= base_url() ?>/${b.logo_url}" width="100" height="100" style="object-fit: cover;"></div>`);
                }

                $('.modal-title').text('Sửa thương hiệu');
                $('#brandModal').modal('show');
            } else {
                alert(res.message || 'Lỗi khi tải dữ liệu');
            }
        }, 'json');
    });

    // Save brand
    $('#btnSave').on('click', function(){
        let form = $('#brandForm')[0];
        let formData = new FormData(form);
        let id = $('#brand_id').val();
        let url = id ? "<?= site_url('Dashboard/brands') ?>/" + id + "/update" : "<?= site_url('Dashboard/brands/store') ?>";

        $(this).prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Đang lưu...');

        $.ajax({
            url: url,
            method: 'POST',
            data: formData,
            enctype: 'multipart/form-data',
            processData: false,
            contentType: false,
            headers: {'X-CSRF-TOKEN': csrfToken()},
            success: function(res){
                if (res.token) updateToken(res.token);
                if (res.status === 'success') {
                    $('#brandModal').modal('hide');
                    table.ajax.reload(null, false);
                    toastr.success(res.message || 'Lưu thành công');
                } else {
                    alert(res.message || 'Có lỗi xảy ra');
                }
            },
            error: function(xhr, status, error){
                alert('Lỗi hệ thống: ' + error);
            },
            complete: function(){
                $('#btnSave').prop('disabled', false).html('<i class="fa fa-save"></i> Lưu');
            }
        });
    });

    // Delete brand
    $('#brandsTable').on('click', '.btn-delete', function(){
        if(!confirm('Bạn có chắc muốn xóa thương hiệu này?')) return;
        let id = $(this).data('id');
        $.post("<?= site_url('Dashboard/brands') ?>/" + id + "/delete", {
            '<?= csrf_token() ?>': csrfToken()
        }, function(res){
            if (res.token) updateToken(res.token);
            if (res.status === 'success') {
                table.ajax.reload(null, false);
                toastr.success('Xóa thành công');
            } else {
                alert(res.message || 'Lỗi khi xóa');
            }
        }, 'json');
    });

    // Preview logo
    $('#logo').on('change', function(){
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#logoPreview').html(`<div class="preview-image"><img src="${e.target.result}" width="100" height="100" style="object-fit: cover;"></div>`);
            }
            reader.readAsDataURL(file);
        }
    });

    // Reset khi đóng modal
    $('#brandModal').on('hidden.bs.modal', function(){
        $('#logo').val('');
        $('#logoPreview').html('');
    });
});
</script>

<?= $this->endSection() ?>
