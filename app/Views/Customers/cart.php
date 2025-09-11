<?= $this->extend('Customers/layout/main') ?>

<?= $this->section('styles') ?>
  <link rel="stylesheet" href="<?= base_url('aranoz-master/css/nice-select.css'); ?>">
  <link rel="stylesheet" href="<?= base_url('aranoz-master/css/price_rangs.css'); ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>

  <!--================Home Banner Area =================-->
  <section class="breadcrumb breadcrumb_bg">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-lg-8">
          <div class="breadcrumb_iner">
            <div class="breadcrumb_iner_item">
              <h2>Giỏ hàng</h2>
              <p>Home <span>-</span> Giỏ hàng</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!--================Cart Area =================-->
  <section class="cart_area padding_top">
    <div class="container">
      <div class="cart_inner">
        <div class="table-responsive">
          <table class="table">
            <thead>
              <tr>
                <th scope="col">Sản phẩm</th>
                <th scope="col">Giá</th>
                <th scope="col">Số lượng</th>
                <th scope="col">Tổng</th>
                <th scope="col">Thao tác</th>
              </tr>
            </thead>
            <tbody id="cart-items">
              <?php if (empty($cartItems)): ?>
              <tr>
                <td colspan="5" class="text-center">Giỏ hàng của bạn đang trống</td>
              </tr>
              <?php else: ?>
                <?php $subtotal = 0; ?>
                <?php foreach ($cartItems as $item): ?>
                <?php 
                  $itemTotal = $item->price * $item->quantity;
                  $subtotal += $itemTotal;
                ?>
                <tr id="cart-item-<?= $item->product_id ?>">
                  <td>
                    <div class="media">
                      <div class="d-flex">
                        <img src="<?= base_url('uploads/products/' . $item->main_image) ?>" alt="<?= $item->name ?>" width="100" />
                      </div>
                      <div class="media-body">
                        <p><?= $item->name ?></p>
                      </div>
                    </div>
                  </td>
                  <td>
                    <h5><?= number_format($item->price, 0, ',', '.') ?>₫</h5>
                  </td>
                  <td>
                    <div class="product_count">
                      <span class="input-number-decrement" onclick="updateQuantity(<?= $item->product_id ?>, <?= $item->quantity - 1 ?>)">
                        <i class="ti-angle-down"></i>
                      </span>
                      <input class="input-number" type="text" value="<?= $item->quantity ?>" min="1" max="10" id="quantity-<?= $item->product_id ?>">
                      <span class="input-number-increment" onclick="updateQuantity(<?= $item->product_id ?>, <?= $item->quantity + 1 ?>)">
                        <i class="ti-angle-up"></i>
                      </span>
                    </div>
                  </td>
                  <td>
                    <h5 id="item-total-<?= $item->product_id ?>"><?= number_format($itemTotal, 0, ',', '.') ?>₫</h5>
                  </td>
                  <td>
                                        <button class="btn btn-sm btn-danger" onclick="removeItem(<?= $item->product_id ?>)">
                      <i class="ti-trash"></i> Xóa
                    </button>
                  </td>
                </tr>
                <?php endforeach; ?>
              <?php endif; ?>
              <tr>
                <td></td>
                <td></td>
                <td>
                  <h5>Tạm tính</h5>
                </td>
                <td>
                  <h5 id="cart-subtotal"><?= number_format($subtotal, 0, ',', '.') ?>₫</h5>
                </td>
                <td></td>
              </tr>
              <tr class="shipping_area">
                <td></td>
                <td></td>
                <td>
                  <h5>Phí vận chuyển</h5>
                </td>
                <td>
                  <div class="shipping_box">
                    <ul class="list">
                      <li>
                        <a href="#">Phí cố định: 30,000₫</a>
                      </li>
                      <li class="active">
                        <a href="#">Miễn phí vận chuyển cho đơn trên 500,000₫</a>
                      </li>
                    </ul>
                  </div>
                </td>
                <td></td>
              </tr>
              <tr>
                <td></td>
                <td></td>
                <td>
                  <h5>Tổng cộng</h5>
                </td>
                <td>
                  <h5 id="cart-total">
                    <?php
                    $shipping = $subtotal >= 500000 ? 0 : 30000;
                    $total = $subtotal + $shipping;
                    echo number_format($total, 0, ',', '.') . '₫';
                    ?>
                  </h5>
                </td>
                <td></td>
              </tr>
            </tbody>
          </table>
          <div class="checkout_btn_inner float-right">
            <a class="btn_1" href="<?= base_url('category') ?>">Tiếp tục mua hàng</a>
            <?php if (!empty($cartItems)): ?>
            <a class="btn_1 checkout_btn_1" href="<?= base_url('checkout') ?>">Thanh toán</a>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </section>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
  <script src="<?= base_url('aranoz-master/js/mail-script.js'); ?>"></script>
  <script src="<?= base_url('aranoz-master/js/stellar.js'); ?>"></script>
  <script src="<?= base_url('aranoz-master/js/price_rangs.js'); ?>"></script>
  
  <script>
  function updateQuantity(productId, newQuantity) {
    if (newQuantity < 1) newQuantity = 1;
    
    $.ajax({
      url: '<?= base_url('cart/update') ?>',
      type: 'POST',
      data: {
        product_id: productId,
        quantity: newQuantity
      },
      success: function(response) {
        if (response.success) {
          $('#quantity-' + productId).val(newQuantity);
          $('#item-total-' + productId).text(response.item_total + '₫');
          $('#cart-subtotal').text(response.subtotal + '₫');
          
          // Tính lại tổng cộng với phí vận chuyển
          const subtotal = parseInt(response.subtotal.replace(/\./g, ''));
          const shipping = subtotal >= 500000 ? 0 : 30000;
          const total = subtotal + shipping;
          $('#cart-total').text(total.toLocaleString('vi-VN') + '₫');
          
          $('#cart-count').text(response.cart_count);
        } else {
          alert(response.message);
        }
      }
    });
  }
  
  function removeItem(productId) {
    if (!confirm('Bạn có chắc muốn xóa sản phẩm này khỏi giỏ hàng?')) return;
    
    $.ajax({
      url: '<?= base_url('cart/remove') ?>',
      type: 'POST',
      data: {
        product_id: productId
      },
      success: function(response) {
        if (response.success) {
          $('#cart-item-' + productId).remove();
          $('#cart-subtotal').text(response.subtotal + '₫');
          
          // Tính lại tổng cộng với phí vận chuyển
          const subtotal = parseInt(response.subtotal.replace(/\./g, ''));
          const shipping = subtotal >= 500000 ? 0 : 30000;
          const total = subtotal + shipping;
          $('#cart-total').text(total.toLocaleString('vi-VN') + '₫');
          
          $('#cart-count').text(response.cart_count);
          
          // Nếu giỏ hàng trống
          if ($('#cart-items tr').length <= 4) { // 4 là số hàng cố định (tạm tính, phí vận chuyển, tổng cộng)
            $('#cart-items').html('<tr><td colspan="5" class="text-center">Giỏ hàng của bạn đang trống</td></tr>');
            $('.checkout_btn_1').hide();
          }
        } else {
          alert(response.message);
        }
      }
    });
  }
  </script>
<?= $this->endSection() ?>