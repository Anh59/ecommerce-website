<?= $this->extend('Customers/layout/main') ?>

<?= $this->section('styles') ?>
  <link rel="stylesheet" href="<?= base_url('aranoz-master/css/nice-select.css'); ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>

  <!--================Home Banner Area =================-->
  <section class="breadcrumb breadcrumb_bg">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-lg-8">
          <div class="breadcrumb_iner">
            <div class="breadcrumb_iner_item">
              <h2>Sản phẩm yêu thích</h2>
              <p>Home <span>-</span> Sản phẩm yêu thích</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!--================Wishlist Area =================-->
  <section class="cart_area padding_top">
    <div class="container">
      <div class="wishlist_inner">
        <div class="table-responsive">
          <table class="table">
            <thead>
              <tr>
                <th scope="col">Sản phẩm</th>
                <th scope="col">Giá</th>
                <th scope="col">Trạng thái</th>
                <th scope="col">Thao tác</th>
              </tr>
            </thead>
            <tbody id="wishlist-items">
              <?php if (empty($wishlistItems)): ?>
              <tr>
                <td colspan="4" class="text-center">Danh sách yêu thích của bạn đang trống</td>
              </tr>
              <?php else: ?>
                <?php foreach ($wishlistItems as $item): ?>
                <tr id="wishlist-item-<?= $item->product_id ?>">
                  <td>
                    <div class="media">
                      <div class="d-flex">
                        <img src="<?= base_url('uploads/products/' . $item->main_image) ?>" alt="<?= $item->name ?>" width="100" />
                      </div>
                      <div class="media-body">
                        <h4><a href="<?= base_url('product/' . $item->slug) ?>"><?= $item->name ?></a></h4>
                      </div>
                    </div>
                  </td>
                  <td>
                    <h5>
                      <?php if ($item->sale_price && $item->sale_price < $item->price): ?>
                        <span class="text-danger"><?= number_format($item->sale_price, 0, ',', '.') ?>₫</span>
                        <span class="text-muted text-decoration-line-through"><?= number_format($item->price, 0, ',', '.') ?>₫</span>
                      <?php else: ?>
                        <?= number_format($item->price, 0, ',', '.') ?>₫
                      <?php endif; ?>
                    </h5>
                  </td>
                  <td>
                    <h5>
                      <?php if ($item->stock_status == 'in_stock'): ?>
                        <span class="text-success">Còn hàng</span>
                      <?php elseif ($item->stock_status == 'out_of_stock'): ?>
                        <span class="text-danger">Hết hàng</span>
                      <?php elseif ($item->stock_status == 'pre_order'): ?>
                        <span class="text-warning">Đặt trước</span>
                      <?php else: ?>
                        <span class="text-info">Liên hệ</span>
                      <?php endif; ?>
                    </h5>
                  </td>
                  <td>
                    <div class="d-flex flex-column">
                      <button class="btn btn-sm btn-primary mb-2 add-to-cart" data-product-id="<?= $item->product_id ?>">
                        <i class="ti-shopping-cart"></i> Thêm vào giỏ
                      </button>
                      <button class="btn btn-sm btn-danger" onclick="removeFromWishlist(<?= $item->product_id ?>)">
                        <i class="ti-trash"></i> Xóa
                      </button>
                    </div>
                  </td>
                </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
          <div class="checkout_btn_inner float-right">
            <a class="btn_1" href="<?= base_url('category') ?>">Tiếp tục mua hàng</a>
          </div>
        </div>
      </div>
    </div>
  </section>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
  <script>
  function removeFromWishlist(productId) {
    if (!confirm('Bạn có chắc muốn xóa sản phẩm này khỏi danh sách yêu thích?')) return;
    
    $.ajax({
      url: '<?= base_url('wishlist/remove') ?>',
      type: 'POST',
      data: {
        product_id: productId
      },
      success: function(response) {
        if (response.success) {
          $('#wishlist-item-' + productId).remove();
          $('#wishlist-count').text(response.wishlist_count);
          
          // Nếu wishlist trống
          if ($('#wishlist-items tr').length === 1) {
            $('#wishlist-items').html('<tr><td colspan="4" class="text-center">Danh sách yêu thích của bạn đang trống</td></tr>');
          }
        } else {
          alert(response.message);
        }
      }
    });
  }
  
  // Thêm vào giỏ hàng từ wishlist
  $(document).on('click', '.add-to-cart', function(e) {
    e.preventDefault();
    const productId = $(this).data('product-id');
    
    $.ajax({
      url: '<?= base_url('cart/add') ?>',
      type: 'POST',
      data: { product_id: productId },
      success: function(response) {
        if (response.success) {
          alert(response.message);
          $('#cart-count').text(response.cart_count);
        } else {
          if (response.redirect) {
            window.location.href = response.redirect;
          } else {
            alert(response.message);
          }
        }
      }
    });
  });
  </script>
<?= $this->endSection() ?>