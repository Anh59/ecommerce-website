<?= $this->extend('Dashboard/layout') ?>
<?= $this->section('content') ?>

<meta name="csrf-token" content="<?= csrf_hash() ?>">

<div class="mb-3">
    <button id="btnAdd" class="btn btn-success"><i class="fa fa-plus"></i> Thêm sản phẩm</button>
</div>

<table id="productsTable" class="display" style="width:100%">
    <thead>
        <tr>
            <th>STT</th><th>Tên</th><th>SKU</th><th>Giá</th><th>Kho</th><th>Danh mục</th><th>Brand</th><th>Hành động</th>
        </tr>
    </thead>
    <tbody></tbody>
</table>

<!-- Modal Form -->
<div class="modal fade" id="productModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <form id="productForm" enctype="multipart/form-data">
      <?= csrf_field() ?>
      <div class="modal-content">
        <div class="modal-header"><h5 class="modal-title">Thêm / Sửa sản phẩm</h5></div>
        <div class="modal-body">
            <input type="hidden" name="id" id="product_id">
            <div class="mb-3">
                <label>Tên</label>
                <input type="text" name="name" id="name" class="form-control">
            </div>
            <div class="mb-3">
                <label>Slug</label>
                <input type="text" name="slug" id="slug" class="form-control">
            </div>
            <div class="mb-3">
                <label>SKU</label>
                <input type="text" name="sku" id="sku" class="form-control">
            </div>
            <div class="mb-3 row">
                <div class="col">
                    <label>Giá</label>
                    <input type="text" name="price" id="price" class="form-control">
                </div>
                <div class="col">
                    <label>Kho</label>
                    <input type="number" name="stock_quantity" id="stock_quantity" class="form-control" value="0">
                </div>
            </div>

            <div class="mb-3">
                <label>Danh mục</label>
                <select name="category_id" id="category_id" class="form-control">
                    <option value="">-- Chọn --</option>
                    <?php foreach($categories as $c):?>
                        <option value="<?= $c['id'] ?>"><?= esc($c['name']) ?></option>
                    <?php endforeach;?>
                </select>
            </div>

            <div class="mb-3">
                <label>Brand</label>
                <select name="brand_id" id="brand_id" class="form-control">
                    <option value="">-- Chọn --</option>
                    <?php foreach($brands as $b):?>
                        <option value="<?= $b['id'] ?>"><?= esc($b['name']) ?></option>
                    <?php endforeach;?>
                </select>
            </div>

            <div class="mb-3">
                <label>Main image</label>
                <input type="file" name="main_image" id="main_image" class="form-control">
                <div id="mainPreview" class="mt-2"></div>
            </div>

            <div class="mb-3">
                <label>Ảnh phụ (multiple)</label>
                <input type="file" name="images[]" id="images" multiple class="form-control">
                <div id="imagesPreview" class="mt-2"></div>
            </div>

            <div class="mb-3">
                <label>Mô tả ngắn</label>
                <textarea name="short_description" id="short_description" class="form-control"></textarea>
            </div>

        </div>
        <div class="modal-footer">
            <button type="button" id="btnSave" class="btn btn-primary">Lưu</button>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- include jQuery, DataTables, Bootstrap JS -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
$(document).ready(function(){
    // CSRF token meta
    function csrfToken() { return $('meta[name="csrf-token"]').attr('content'); }
    function updateToken(token) { $('meta[name="csrf-token"]').attr('content', token); $('input[name="<?= csrf_token() ?>"]').val(token); }

    // DataTable
    var table = $('#productsTable').DataTable({
        ajax: {
            url: "<?= site_url('Dashboard/products/list') ?>",
            dataSrc: 'data',
            complete: function(xhr) {
                var json = xhr.responseJSON;
                if (json && json.token) updateToken(json.token);
            }
        },
        columns: [
            { data: null, render: function (data, type, row, meta) { return meta.row+1; } },
            { data: 'name' }, { data: 'sku' },
            { data: 'price', render: $.fn.dataTable.render.number( ',', '.', 0, '' ) },
            { data: 'stock_quantity' }, { data: 'category_name' }, { data: 'brand_name' },
            { data: 'id', render: function(d){ 
                return `<button class="btn btn-sm btn-primary btn-edit" data-id="${d}"><i class="fa fa-edit"></i></button>
                        <button class="btn btn-sm btn-danger btn-delete" data-id="${d}"><i class="fa fa-trash"></i></button>`;
            } }
        ]
    });

    // Show add modal
    $('#btnAdd').on('click', function(){
        $('#productForm')[0].reset();
        $('#product_id').val('');
        $('#mainPreview, #imagesPreview').html('');
        $('#productModal').modal('show');
    });

    // Edit
    $('#productsTable').on('click', '.btn-edit', function(){
        let id = $(this).data('id');
        $.get("<?= site_url('Dashboard/products') ?>/" + id + "/edit", function(res){
            if(res.status === 'success'){
                updateToken(res.token);
                const p = res.product;
                $('#product_id').val(p.id);
                $('#name').val(p.name);
                $('#slug').val(p.slug);
                $('#sku').val(p.sku);
                $('#price').val(p.price);
                $('#stock_quantity').val(p.stock_quantity);
                $('#category_id').val(p.category_id);
                $('#brand_id').val(p.brand_id);
                $('#short_description').val(p.short_description);
                if(p.main_image) $('#mainPreview').html(`<img src="<?= base_url() ?>/${p.main_image}" width="120">`);
                // images
                let html = '';
                res.images.forEach(img => {
                    html += `<div class="d-inline-block me-2">
                                <img src="<?= base_url() ?>/${img.image_url}" width="80" />
                                <button data-id="${img.id}" class="btn btn-sm btn-danger delete-image">x</button>
                             </div>`;
                });
                $('#imagesPreview').html(html);
                $('#productModal').modal('show');
            } else {
                alert(res.message || 'Lỗi');
            }
        }, 'json');
    });

    // Delete image
    $('#imagesPreview').on('click', '.delete-image', function(){
        if (!confirm('Xóa ảnh này?')) return;
        let id = $(this).data('id');
        $.post("<?= site_url('Dashboard/products/images') ?>/" + id + "/delete", {_method:'POST', '<?= csrf_token() ?>': csrfToken()}, function(res){
            if(res.token) updateToken(res.token);
            if(res.status === 'success') { table.ajax.reload(null,false); $('#imagesPreview').find(`[data-id="${id}"]`).closest('div').remove(); }
        }, 'json');
    });

    // Save (create/update)
    $('#btnSave').on('click', function(){
        let form = $('#productForm')[0];
        let formData = new FormData(form);
        let id = $('#product_id').val();
        let url = id ? "<?= site_url('Dashboard/products') ?>/" + id + "/update" : "<?= site_url('Dashboard/products/store') ?>";
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
                    $('#productModal').modal('hide');
                    table.ajax.reload(null,false);
                    alert(res.message);
                } else if (res.status === 'error') {
                    // show validation errors (res.errors)
                    let msg = '';
                    if (res.errors) {
                        for (let k in res.errors) msg += res.errors[k] + "\n";
                    } else msg = res.message || 'Có lỗi';
                    alert(msg);
                }
            },
            error: function(){
                alert('Lỗi hệ thống');
            }
        });
    });

    // Delete product
    $('#productsTable').on('click', '.btn-delete', function(){
        if(!confirm('Bạn có chắc muốn xóa sản phẩm này?')) return;
        let id = $(this).data('id');
        $.post("<?= site_url('Dashboard/products') ?>/" + id + "/delete", {'<?= csrf_token() ?>': csrfToken()}, function(res){
            if (res.token) updateToken(res.token);
            if (res.status === 'success') table.ajax.reload(null,false);
            else alert(res.message || 'Lỗi');
        }, 'json');
    });
});
</script>

<?= $this->endSection() ?>
