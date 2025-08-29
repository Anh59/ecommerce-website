<?= $this->extend('Dashboard/layout') ?>
<?= $this->section('content') ?>

<meta name="csrf-token" content="<?= csrf_hash() ?>">

<div class="mb-3">
    <button id="btnAdd" class="btn btn-success"><i class="fa fa-plus"></i> Thêm sản phẩm</button>
</div>

<table id="productsTable" class="display" style="width:100%">
    <thead>
        <tr>
            <th>STT</th><th>Ảnh</th><th>Tên</th><th>SKU</th><th>Giá</th><th>Kho</th><th>Danh mục</th><th>Brand</th><th>Trạng thái</th><th>Hành động</th>
        </tr>
    </thead>
    <tbody></tbody>
</table>

<!-- Modal Form -->
<div class="modal fade" id="productModal" tabindex="-1">
  <div class="modal-dialog modal-xl">
    <form id="productForm" enctype="multipart/form-data">
      <?= csrf_field() ?>
      <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">Thêm / Sửa sản phẩm</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
            <input type="hidden" name="id" id="product_id">
            
            <!-- Nav tabs -->
            <ul class="nav nav-tabs" id="productTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="basic-tab" data-bs-toggle="tab" data-bs-target="#basic" type="button" role="tab">Thông tin cơ bản</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="specifications-tab" data-bs-toggle="tab" data-bs-target="#specifications" type="button" role="tab">Thông số kỹ thuật</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="images-tab" data-bs-toggle="tab" data-bs-target="#images" type="button" role="tab">Hình ảnh</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="seo-tab" data-bs-toggle="tab" data-bs-target="#seo" type="button" role="tab">SEO</button>
                </li>
            </ul>

            <!-- Tab content -->
            <div class="tab-content mt-3" id="productTabContent">
                
                <!-- Basic Info Tab -->
                <div class="tab-pane fade show active" id="basic" role="tabpanel">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Tên sản phẩm *</label>
                                <input type="text" name="name" id="name" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Slug *</label>
                                <input type="text" name="slug" id="slug" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">SKU *</label>
                                <input type="text" name="sku" id="sku" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Danh mục *</label>
                                <select name="category_id" id="category_id" class="form-control" required>
                                    <option value="">-- Chọn danh mục --</option>
                                    <?php foreach($categories as $c):?>
                                        <option value="<?= $c['id'] ?>"><?= esc($c['name']) ?></option>
                                    <?php endforeach;?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Thương hiệu</label>
                                <select name="brand_id" id="brand_id" class="form-control">
                                    <option value="">-- Chọn thương hiệu --</option>
                                    <?php foreach($brands as $b):?>
                                        <option value="<?= $b['id'] ?>"><?= esc($b['name']) ?></option>
                                    <?php endforeach;?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Trạng thái</label>
                                <select name="is_active" id="is_active" class="form-control">
                                    <option value="1">Hoạt động</option>
                                    <option value="0">Không hoạt động</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Giá bán *</label>
                                <input type="number" name="price" id="price" class="form-control" min="0" step="1000" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Giá khuyến mãi</label>
                                <input type="number" name="sale_price" id="sale_price" class="form-control" min="0" step="1000">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Số lượng tồn kho</label>
                                <input type="number" name="stock_quantity" id="stock_quantity" class="form-control" min="0" value="0">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Mức tồn tối thiểu</label>
                                <input type="number" name="min_stock_level" id="min_stock_level" class="form-control" min="0" value="0">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Trạng thái kho</label>
                                <select name="stock_status" id="stock_status" class="form-control">
                                    <option value="in_stock">Còn hàng</option>
                                    <option value="out_of_stock">Hết hàng</option>
                                    <option value="low_stock">Sắp hết</option>
                                    <option value="pre_order">Đặt trước</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Bảo hành (tháng)</label>
                                <input type="number" name="warranty_period" id="warranty_period" class="form-control" min="0">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Xuất xứ</label>
                                <input type="text" name="origin_country" id="origin_country" class="form-control">
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Mô tả ngắn</label>
                        <textarea name="short_description" id="short_description" class="form-control" rows="3"></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Mô tả chi tiết</label>
                        <textarea name="description" id="description" class="form-control" rows="5"></textarea>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_featured" id="is_featured" value="1">
                            <label class="form-check-label" for="is_featured">
                                Sản phẩm nổi bật
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Specifications Tab -->
                <div class="tab-pane fade" id="specifications" role="tabpanel">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Kích thước</h6>
                            <div class="mb-3">
                                <label class="form-label">Chiều dài (cm)</label>
                                <input type="number" name="dimension_length" id="dimension_length" class="form-control" step="0.1">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Chiều rộng (cm)</label>
                                <input type="number" name="dimension_width" id="dimension_width" class="form-control" step="0.1">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Chiều cao (cm)</label>
                                <input type="number" name="dimension_height" id="dimension_height" class="form-control" step="0.1">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Trọng lượng (kg)</label>
                                <input type="number" name="weight" id="weight" class="form-control" step="0.1">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Chất liệu</label>
                                <input type="text" name="material" id="material" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6>Thông số kỹ thuật</h6>
                            <div class="mb-3">
                                <label class="form-label">Chiều cao (spec)</label>
                                <input type="text" name="spec_height" id="spec_height" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Chiều rộng (spec)</label>
                                <input type="text" name="spec_width" id="spec_width" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Chiều dài (spec)</label>
                                <input type="text" name="spec_length" id="spec_length" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Trọng lượng (spec)</label>
                                <input type="text" name="spec_weight" id="spec_weight" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Chất liệu (spec)</label>
                                <input type="text" name="spec_material" id="spec_material" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Màu sắc</label>
                                <input type="text" name="spec_color" id="spec_color" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Công suất</label>
                                <input type="text" name="spec_power" id="spec_power" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Dung tích</label>
                                <input type="text" name="spec_capacity" id="spec_capacity" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Thông số khác</label>
                                <textarea name="spec_other" id="spec_other" class="form-control" rows="4" placeholder="Mỗi dòng định dạng: Tên: Giá trị"></textarea>
                                <small class="text-muted">Ví dụ: Điện áp: 220V</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Images Tab -->
                <div class="tab-pane fade" id="images" role="tabpanel">
                    <div class="mb-3">
                        <label class="form-label">Ảnh chính</label>
                        <input type="file" name="main_image" id="main_image" class="form-control" accept="image/*">
                        <div id="mainPreview" class="mt-2"></div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Ảnh phụ (chọn nhiều ảnh)</label>
                        <input type="file" name="images[]" id="images" multiple class="form-control" accept="image/*">
                        <div id="imagesPreview" class="mt-2"></div>
                    </div>
                </div>

                <!-- SEO Tab -->
                <div class="tab-pane fade" id="seo" role="tabpanel">
                    <div class="mb-3">
                        <label class="form-label">Meta Title</label>
                        <input type="text" name="meta_title" id="meta_title" class="form-control" maxlength="255">
                        <small class="text-muted">Tối đa 255 ký tự</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Meta Description</label>
                        <textarea name="meta_description" id="meta_description" class="form-control" rows="3" maxlength="500"></textarea>
                        <small class="text-muted">Tối đa 500 ký tự</small>
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

<!-- Include CSS & JS -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<style>
.product-image { width: 50px; height: 50px; object-fit: cover; border-radius: 4px; }
.preview-image { width: 100px; height: 100px; object-fit: cover; border-radius: 4px; margin: 5px; position: relative; display: inline-block; }
.delete-image-btn { position: absolute; top: -5px; right: -5px; }
.nav-tabs .nav-link.active { background-color: #007bff; color: white; }
</style>

<script>
$(document).ready(function(){
    // CSRF token functions
    function csrfToken() { return $('meta[name="csrf-token"]').attr('content'); }
    function updateToken(token) { 
        $('meta[name="csrf-token"]').attr('content', token); 
        $('input[name="<?= csrf_token() ?>"]').val(token); 
    }

    // Auto-generate slug from name
    $('#name').on('input', function(){
        let name = $(this).val();
        let slug = name.toLowerCase()
            .replace(/[^a-z0-9\s-]/g, '')
            .replace(/\s+/g, '-')
            .replace(/-+/g, '-')
            .trim('-');
        $('#slug').val(slug);
    });

    // DataTable initialization
    var table = $('#productsTable').DataTable({
        processing: true,
        ajax: {
            url: "<?= site_url('Dashboard/products/list') ?>",
            dataSrc: 'data',
            complete: function(xhr) {
                var json = xhr.responseJSON;
                if (json && json.token) updateToken(json.token);
            }
        },
        columns: [
            { data: null, render: function (data, type, row, meta) { return meta.row + 1; } },
            { 
                data: 'main_image', 
                render: function(d) { 
                    return d ? `<img src="<?= base_url() ?>/${d}" class="product-image">` : '<i class="fa fa-image text-muted"></i>'; 
                }
            },
            { data: 'name' },
            { data: 'sku' },
            { 
                data: 'price', 
                render: function(d) { 
                    return new Intl.NumberFormat('vi-VN', {style: 'currency', currency: 'VND'}).format(d);
                }
            },
            { data: 'stock_quantity' },
            { data: 'category_name' },
            { data: 'brand_name' },
            { 
                data: 'is_active', 
                render: function(d) { 
                    return d == 1 ? '<span class="badge bg-success">Hoạt động</span>' : '<span class="badge bg-danger">Không hoạt động</span>'; 
                }
            },
            { 
                data: 'id', 
                render: function(d){ 
                    return `<div class="btn-group" role="group">
                                <button class="btn btn-sm btn-primary btn-edit" data-id="${d}" title="Sửa">
                                    <i class="fa fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-danger btn-delete" data-id="${d}" title="Xóa">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </div>`;
                }
            }
        ],
        order: [[0, 'desc']],
        pageLength: 25,
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/vi.json'
        }
    });

    // Show add modal
    $('#btnAdd').on('click', function(){
        $('#productForm')[0].reset();
        $('#product_id').val('');
        $('#mainPreview, #imagesPreview').html('');
        $('.modal-title').text('Thêm sản phẩm');
        $('#productModal').modal('show');
        // Switch to basic tab
        $('#basic-tab').tab('show');
    });

    // Edit product
    $('#productsTable').on('click', '.btn-edit', function(){
        let id = $(this).data('id');
        $.get("<?= site_url('Dashboard/products') ?>/" + id + "/edit", function(res){
            if(res.status === 'success'){
                updateToken(res.token);
                const p = res.product;
                
                // Fill basic info
                $('#product_id').val(p.id);
                $('#name').val(p.name);
                $('#slug').val(p.slug);
                $('#sku').val(p.sku);
                $('#price').val(p.price);
                $('#sale_price').val(p.sale_price);
                $('#stock_quantity').val(p.stock_quantity);
                $('#min_stock_level').val(p.min_stock_level);
                $('#stock_status').val(p.stock_status);
                $('#category_id').val(p.category_id);
                $('#brand_id').val(p.brand_id);
                $('#is_active').val(p.is_active);
                $('#warranty_period').val(p.warranty_period);
                $('#origin_country').val(p.origin_country);
                $('#weight').val(p.weight);
                $('#material').val(p.material);
                $('#short_description').val(p.short_description);
                $('#description').val(p.description);
                $('#meta_title').val(p.meta_title);
                $('#meta_description').val(p.meta_description);
                $('#is_featured').prop('checked', p.is_featured == 1);

                // Fill dimensions
                if (p.dimensions_parsed) {
                    $('#dimension_length').val(p.dimensions_parsed.length);
                    $('#dimension_width').val(p.dimensions_parsed.width);
                    $('#dimension_height').val(p.dimensions_parsed.height);
                }

                // Fill specifications
                if (p.specifications_parsed) {
                    const specs = p.specifications_parsed;
                    $('#spec_height').val(specs.height);
                    $('#spec_width').val(specs.width);
                    $('#spec_length').val(specs.length);
                    $('#spec_weight').val(specs.weight);
                    $('#spec_material').val(specs.material);
                    $('#spec_color').val(specs.color);
                    $('#spec_power').val(specs.power);
                    $('#spec_capacity').val(specs.capacity);
                    
                    // Other specs
                    let otherSpecs = [];
                    const mainSpecs = ['height', 'width', 'length', 'weight', 'material', 'color', 'power', 'capacity'];
                    for (let key in specs) {
                        if (!mainSpecs.includes(key)) {
                            otherSpecs.push(key + ': ' + specs[key]);
                        }
                    }
                    $('#spec_other').val(otherSpecs.join('\n'));
                }

                // Show main image
                if(p.main_image) {
                    $('#mainPreview').html(`
                        <div class="preview-image">
                            <img src="<?= base_url() ?>/${p.main_image}" width="100" height="100" style="object-fit: cover;">
                        </div>
                    `);
                }
                
                // Show additional images
                let html = '';
                res.images.forEach(img => {
                    html += `
                        <div class="preview-image">
                            <img src="<?= base_url() ?>/${img.image_url}" width="100" height="100" style="object-fit: cover;" />
                            <button type="button" class="btn btn-sm btn-danger delete-image-btn delete-image" data-id="${img.id}">
                                <i class="fa fa-times"></i>
                            </button>
                        </div>`;
                });
                $('#imagesPreview').html(html);
                
                $('.modal-title').text('Sửa sản phẩm');
                $('#productModal').modal('show');
            } else {
                alert(res.message || 'Lỗi khi tải dữ liệu');
            }
        }, 'json').fail(function(){
            alert('Lỗi hệ thống khi tải dữ liệu');
        });
    });

    // Delete image
    $(document).on('click', '.delete-image', function(){
        if (!confirm('Xóa ảnh này?')) return;
        let id = $(this).data('id');
        let $this = $(this);
        
        $.post("<?= site_url('Dashboard/products/images') ?>/" + id + "/delete", {
            _method:'POST', 
            '<?= csrf_token() ?>': csrfToken()
        }, function(res){
            if(res.token) updateToken(res.token);
            if(res.status === 'success') { 
                $this.closest('.preview-image').remove();
                toastr.success('Xóa ảnh thành công');
            } else {
                alert(res.message || 'Lỗi khi xóa ảnh');
            }
        }, 'json').fail(function(){
            alert('Lỗi hệ thống');
        });
    });

    // Save product (create/update)
    $('#btnSave').on('click', function(){
        let form = $('#productForm')[0];
        let formData = new FormData(form);
        let id = $('#product_id').val();
        let url = id ? "<?= site_url('Dashboard/products') ?>/" + id + "/update" : "<?= site_url('Dashboard/products/store') ?>";
        
        // Show loading
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
                    $('#productModal').modal('hide');
                    table.ajax.reload(null, false);
                    toastr.success(res.message || 'Lưu thành công');
                } else if (res.status === 'error') {
                    let msg = '';
                    if (res.errors) {
                        for (let k in res.errors) {
                            msg += res.errors[k] + "\n";
                        }
                    } else {
                        msg = res.message || 'Có lỗi xảy ra';
                    }
                    alert(msg);
                }
            },
            error: function(xhr, status, error){
                console.error('AJAX Error:', error);
                alert('Lỗi hệ thống: ' + error);
            },
            complete: function(){
                $('#btnSave').prop('disabled', false).html('<i class="fa fa-save"></i> Lưu');
            }
        });
    });

    // Delete product
    $('#productsTable').on('click', '.btn-delete', function(){
        if(!confirm('Bạn có chắc muốn xóa sản phẩm này?\nHành động này không thể hoàn tác!')) return;
        
        let id = $(this).data('id');
        let $btn = $(this);
        
        $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i>');
        
        $.post("<?= site_url('Dashboard/products') ?>/" + id + "/delete", {
            '<?= csrf_token() ?>': csrfToken()
        }, function(res){
            if (res.token) updateToken(res.token);
            if (res.status === 'success') {
                table.ajax.reload(null, false);
                toastr.success('Xóa thành công');
            } else {
                alert(res.message || 'Lỗi khi xóa');
            }
        }, 'json').fail(function(){
            alert('Lỗi hệ thống');
        }).always(function(){
            $btn.prop('disabled', false).html('<i class="fa fa-trash"></i>');
        });
    });

    // Preview main image
    $('#main_image').on('change', function(){
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#mainPreview').html(`
                    <div class="preview-image">
                        <img src="${e.target.result}" width="100" height="100" style="object-fit: cover;">
                    </div>
                `);
            }
            reader.readAsDataURL(file);
        }
    });

    // Preview multiple images
    $('#images').on('change', function(){
        const files = this.files;
        let html = '';
        
        for (let i = 0; i < files.length; i++) {
            const reader = new FileReader();
            reader.onload = function(e) {
                html += `
                    <div class="preview-image">
                        <img src="${e.target.result}" width="100" height="100" style="object-fit: cover;">
                    </div>
                `;
                $('#imagesPreview').html(html);
            }
            reader.readAsDataURL(files[i]);
        }
    });

    // Form validation
    $('#productForm').on('submit', function(e){
        e.preventDefault();
        $('#btnSave').click();
    });
});
</script>

<?= $this->endSection() ?>