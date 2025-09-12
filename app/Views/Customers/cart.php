<?= $this->extend('Customers/layout/main') ?>

<?= $this->section('styles') ?>
<!-- CSS riêng cho cart -->
<link rel="stylesheet" href="<?= base_url('aranoz-master/css/nice-select.css'); ?>">
<link rel="stylesheet" href="<?= base_url('aranoz-master/css/price_rangs.css'); ?>">
<style>
.cart-item-loading { opacity: 0.6; pointer-events: none; }
.cart-errors { background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
.cart-success { background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
.out-of-stock { opacity: 0.7; }
.out-of-stock .product-name { text-decoration: line-through; }
.quantity-controls { display: flex; align-items: center; }
.remove-item { color: #dc3545; cursor: pointer; margin-left: 10px; }
.remove-item:hover { color: #c82333; }
.coupon-section { background: #f8f9fa; padding: 20px; border-radius: 5px; margin: 20px 0; }
.shipping-calculator { background: #f8f9fa; padding: 15px; border-radius: 5px; }
.empty-cart { text-align: center; padding: 60px 20px; }
.empty-cart i { font-size: 4rem; color: #ddd; margin-bottom: 20px; }
.loading-overlay { 
    position: fixed; top: 0; left: 0; width: 100%; height: 100%; 
    background: rgba(255,255,255,0.8); z-index: 9999; display: none; 
}
.loading-content { 
    position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center; 
}
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!--================Home Banner Area =================-->
<!-- breadcrumb start-->
<section class="breadcrumb breadcrumb_bg">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="breadcrumb_iner">
                    <div class="breadcrumb_iner_item">
                        <h2>Cart Products</h2>
                        <p>Home <span>-</span> Cart Products</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- breadcrumb start-->

<!--================Cart Area =================-->
<section class="cart_area padding_top">
    <div class="container">
        <!-- Display Messages -->
        <?php if (session()->getFlashdata('success')): ?>
            <div class="cart-success">
                <?= session()->getFlashdata('success') ?>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="cart-errors">
                <?= session()->getFlashdata('error') ?>
            </div>
        <?php endif; ?>

        <!-- Display Cart Issues -->
        <?php if (!empty($cartIssues)): ?>
            <div class="cart-errors">
                <strong>Giỏ hàng có vấn đề:</strong>
                <ul class="mb-0 mt-2">
                    <?php foreach ($cartIssues as $issue): ?>
                        <li><?= esc($issue['product_name']) ?>: <?= esc($issue['message']) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <div class="cart_inner">
            <?php if (!empty($cartItems)): ?>
                <form id="cart-form" action="<?= route_to('api_cart_update') ?>" method="POST">
                    <?= csrf_field() ?>
                    <div class="table-responsive">
                        <table class="table" id="cart-table">
                            <thead>
                                <tr>
                                    <th scope="col">Product</th>
                                    <th scope="col">Price</th>
                                    <th scope="col">Quantity</th>
                                    <th scope="col">Total</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($cartItems as $item): ?>
                                <tr class="cart-item <?= $item['stock_status'] === 'out_of_stock' ? 'out-of-stock' : '' ?>" 
                                    data-product-id="<?= $item['product_id'] ?>" 
                                    data-cart-id="<?= $item['id'] ?>">
                                    <td>
                                        <div class="media">
                                            <div class="d-flex">
                                                <img src="<?= base_url($item['main_image'] ?? 'aranoz-master/img/product/single-product/cart-1.jpg') ?>" 
                                                     alt="<?= esc($item['name']) ?>" style="width: 80px; height: 80px; object-fit: cover;" />
                                            </div>
                                            <div class="media-body">
                                                <p class="product-name"><?= esc($item['name']) ?></p>
                                                <small class="text-muted">
                                                    <?= esc($item['category_name'] ?? '') ?> 
                                                    <?php if ($item['brand_name']): ?>
                                                        | <?= esc($item['brand_name']) ?>
                                                    <?php endif; ?>
                                                </small>
                                                <?php if ($item['stock_status'] === 'out_of_stock'): ?>
                                                    <div><small class="text-danger">Hết hàng</small></div>
                                                <?php elseif ($item['stock_status'] === 'low_stock'): ?>
                                                    <div><small class="text-warning">Chỉ còn <?= $item['stock_quantity'] ?> sản phẩm</small></div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <h5 class="item-price"><?= number_format($item['price']) ?>₫</h5>
                                    </td>
                                    <td>
                                        <div class="product_count quantity-controls">
                                            <span class="input-number-decrement decrease-qty" data-product-id="<?= $item['product_id'] ?>">
                                                <i class="ti-angle-down"></i>
                                            </span>
                                            <input class="input-number quantity-input" 
                                                   type="number" 
                                                   name="updates[<?= $item['product_id'] ?>]"
                                                   value="<?= $item['quantity'] ?>" 
                                                   min="0" 
                                                   max="<?= $item['stock_quantity'] ?>"
                                                   data-product-id="<?= $item['product_id'] ?>"
                                                   data-price="<?= $item['price'] ?>"
                                                   <?= $item['stock_status'] === 'out_of_stock' ? 'disabled' : '' ?>>
                                            <span class="input-number-increment increase-qty" data-product-id="<?= $item['product_id'] ?>">
                                                <i class="ti-angle-up"></i>
                                            </span>
                                        </div>
                                    </td>
                                    <td>
                                        <h5 class="item-total"><?= number_format($item['price'] * $item['quantity']) ?>₫</h5>
                                    </td>
                                    <td>
                                        <span class="remove-item" 
                                              data-product-id="<?= $item['product_id'] ?>"
                                              title="Xóa sản phẩm">
                                            <i class="ti-trash"></i>
                                        </span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>

                                <tr class="bottom_button">
                                    <td>
                                        <button type="submit" class="btn_1" id="update-cart-btn">Update Cart</button>
                                        <a class="btn btn-outline-danger" href="#" id="clear-cart-btn">Clear Cart</a>
                                    </td>
                                    <td></td>
                                    <td></td>
                                    <td>
                                        <div class="cupon_text float-right">
                                            <a class="btn_1" href="#" id="toggle-coupon">Apply Coupon</a>
                                        </div>
                                    </td>
                                    <td></td>
                                </tr>

                                <!-- Coupon Section -->
                                <tr id="coupon-section" style="display: none;">
                                    <td colspan="5">
                                        <div class="coupon-section">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="input-group">
                                                        <input type="text" class="form-control" id="coupon-code" 
                                                               placeholder="Nhập mã giảm giá">
                                                        <div class="input-group-append">
                                                            <button class="btn btn-primary" type="button" id="apply-coupon-btn">
                                                                Apply
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div id="coupon-info">
                                                        <small class="text-muted">
                                                            Available codes: SAVE10, FLAT50K, FREESHIP
                                                        </small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>

                                <!-- Applied Coupon Display -->
                                <?php 
                                $appliedCoupon = session()->get('applied_coupon');
                                if ($appliedCoupon): 
                                ?>
                                <tr id="applied-coupon-row">
                                    <td></td>
                                    <td></td>
                                    <td><h5>Discount (<?= esc($appliedCoupon['code']) ?>)</h5></td>
                                    <td><h5 class="text-success">-<?= number_format($appliedCoupon['discount']) ?>₫</h5></td>
                                    <td>
                                        <span class="remove-item" id="remove-coupon" title="Xóa mã giảm giá">
                                            <i class="ti-close"></i>
                                        </span>
                                    </td>
                                </tr>
                                <?php endif; ?>

                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td><h5>Subtotal</h5></td>
                                    <td><h5 id="cart-subtotal"><?= number_format($cartTotals['subtotal']) ?>₫</h5></td>
                                    <td></td>
                                </tr>

                                <tr class="shipping_area">
                                    <td></td>
                                    <td></td>
                                    <td><h5>Shipping</h5></td>
                                    <td>
                                        <div class="shipping_box">
                                            <ul class="list">
                                                <?php foreach ($shippingOptions as $key => $option): ?>
                                                <li <?= $key === 'standard' ? 'class="active"' : '' ?>>
                                                    <a href="#" data-shipping="<?= $key ?>" data-price="<?= $option['price'] ?>">
                                                        <?= esc($option['name']) ?>: 
                                                        <?= $option['price'] > 0 ? number_format($option['price']) . '₫' : 'Free' ?>
                                                    </a>
                                                </li>
                                                <?php endforeach; ?>
                                            </ul>
                                            <div class="shipping-calculator">
                                                <h6>Calculate Shipping <i class="fa fa-caret-down" aria-hidden="true"></i></h6>
                                                <select class="shipping_select" id="province-select">
                                                    <option value="">Chọn Tỉnh/Thành phố</option>
                                                    <?php foreach ($provinces as $code => $name): ?>
                                                        <option value="<?= $code ?>"><?= esc($name) ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                                <select class="shipping_select section_bg" id="district-select">
                                                    <option value="">Chọn Quận/Huyện</option>
                                                </select>
                                                <input type="text" id="postal-code" placeholder="Mã bưu điện (không bắt buộc)" />
                                                <a class="btn_1" href="#" id="calculate-shipping">Update Shipping</a>
                                            </div>
                                        </div>
                                    </td>
                                    <td></td>
                                </tr>

                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td><h5>Shipping Fee</h5></td>
                                    <td><h5 id="shipping-fee"><?= number_format($cartTotals['shipping_fee']) ?>₫</h5></td>
                                    <td></td>
                                </tr>

                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td><h4>Total</h4></td>
                                    <td><h4 id="cart-total"><?= number_format($cartTotals['total']) ?>₫</h4></td>
                                    <td></td>
                                </tr>
                            </tbody>
                        </table>
                        
                        <div class="checkout_btn_inner float-right">
                            <a class="btn_1" href="<?= route_to('category') ?>">Continue Shopping</a>
                            <a class="btn_1 checkout_btn_1" href="<?= route_to('api_cart_checkout') ?>" id="checkout-btn">Proceed to Checkout</a>
                        </div>
                    </div>
                </form>

            <?php else: ?>
                <!-- Empty Cart -->
                <div class="empty-cart">
                    <i class="ti-shopping-cart"></i>
                    <h3>Your cart is empty</h3>
                    <p>Start shopping to add items to your cart</p>
                    <a href="<?= route_to('category') ?>" class="btn_1">Start Shopping</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Loading Overlay -->
<div class="loading-overlay" id="loading-overlay">
    <div class="loading-content">
        <i class="fa fa-spinner fa-spin fa-3x text-primary"></i>
        <p class="mt-3">Processing...</p>
    </div>
</div>

<!--================End Cart Area =================-->
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- JS riêng cho cart -->
<script src="<?= base_url('aranoz-master/js/mail-script.js'); ?>"></script>
<script src="<?= base_url('aranoz-master/js/stellar.js'); ?>"></script>
<script src="<?= base_url('aranoz-master/js/price_rangs.js'); ?>"></script>
<!-- custom js -->
<script src="<?= base_url('aranoz-master/js/custom.js'); ?>"></script>

<script>
$(document).ready(function() {
    let isUpdating = false;

    // Quantity controls
    $('.increase-qty').click(function() {
        const productId = $(this).data('product-id');
        const input = $(`.quantity-input[data-product-id="${productId}"]`);
        const currentValue = parseInt(input.val());
        const maxValue = parseInt(input.attr('max'));
        
        if (currentValue < maxValue) {
            input.val(currentValue + 1);
            updateItemTotal(productId);
            
        }
    });

    $('.decrease-qty').click(function() {
        const productId = $(this).data('product-id');
        const input = $(`.quantity-input[data-product-id="${productId}"]`);
        const currentValue = parseInt(input.val());
        
        if (currentValue > 0) {
            input.val(currentValue - 1);
            updateItemTotal(productId);
        }
    });

    // Quantity input change
    $('.quantity-input').on('change', function() {
        const productId = $(this).data('product-id');
        const value = parseInt($(this).val());
        const maxValue = parseInt($(this).attr('max'));
        
        if (value > maxValue) {
            $(this).val(maxValue);
            showToast('warning', `Chỉ có ${maxValue} sản phẩm trong kho`);
        } else if (value < 0) {
            $(this).val(0);
        }
        
        updateItemTotal(productId);
    });

    // Remove item
    $('.remove-item').click(function() {
        const productId = $(this).data('product-id');
        if (confirm('Bạn có chắc muốn xóa sản phẩm này?')) {
            removeItem(productId);
        }
    });

    // Update cart form submission
    $('#cart-form').on('submit', function(e) {
        e.preventDefault();
        if (isUpdating) return;
        
        updateCart();
    });

    // Clear cart
    $('#clear-cart-btn').click(function(e) {
        e.preventDefault();
        if (confirm('Bạn có chắc muốn xóa toàn bộ giỏ hàng?')) {
            clearCart();
        }
    });

    // Toggle coupon section
    $('#toggle-coupon').click(function(e) {
        e.preventDefault();
        $('#coupon-section').toggle();
        $(this).text($('#coupon-section').is(':visible') ? 'Close Coupon' : 'Apply Coupon');
    });

    // Apply coupon
    $('#apply-coupon-btn').click(function() {
        applyCoupon();
    });

    // Remove coupon
    $('#remove-coupon').click(function() {
        removeCoupon();
    });

    // Shipping options
    $('.shipping_box ul li a').click(function(e) {
        e.preventDefault();
        $('.shipping_box ul li').removeClass('active');
        $(this).parent().addClass('active');
        
        const shippingPrice = parseInt($(this).data('price')) || 0;
        updateShippingFee(shippingPrice);
    });

    // Calculate shipping
    $('#calculate-shipping').click(function(e) {
        e.preventDefault();
        calculateShipping();
    });

    // Province change - load districts
    $('#province-select').change(function() {
        loadDistricts($(this).val());
    });

    function updateItemTotal(productId) {
        const input = $(`.quantity-input[data-product-id="${productId}"]`);
        const quantity = parseInt(input.val());
        const price = parseInt(input.data('price'));
        const total = quantity * price;
        
        $(`.cart-item[data-product-id="${productId}"] .item-total`).text(formatCurrency(total) + '₫');
        updateCartTotals();
    }

    function updateCartTotals() {
        let subtotal = 0;
        
        $('.quantity-input').each(function() {
            const quantity = parseInt($(this).val());
            const price = parseInt($(this).data('price'));
            subtotal += quantity * price;
        });
        
        $('#cart-subtotal').text(formatCurrency(subtotal) + '₫');
        
        const shippingFee = parseInt($('#shipping-fee').text().replace(/[^\d]/g, '')) || 0;
        const discount = parseInt($('#applied-coupon-row .text-success').text().replace(/[^\d]/g, '')) || 0;
        const total = subtotal + shippingFee - discount;
        
        $('#cart-total').text(formatCurrency(total) + '₫');
    }

    function removeItem(productId) {
        showLoading();
        
        $.ajax({
            url: '<?= route_to('api_cart_remove') ?>',
            type: 'POST',
            data: {
                product_id: productId,
                <?= csrf_token() ?>: '<?= csrf_hash() ?>'
            },
            success: function(response) {
                if (response.success) {
                    $(`.cart-item[data-product-id="${productId}"]`).fadeOut(function() {
                        $(this).remove();
                        checkEmptyCart();
                        updateCartTotals();
                    });
                    showToast('success', response.message);
                } else {
                    showToast('error', response.message);
                }
            },
            error: function() {
                showToast('error', 'Có lỗi xảy ra, vui lòng thử lại');
            },
            complete: function() {
                hideLoading();
            }
        });
    }

    function updateCart() {
        isUpdating = true;
        showLoading();
        
        const formData = $('#cart-form').serialize();
        
        $.ajax({
            url: '<?= route_to('api_cart_update') ?>',
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    showToast('success', response.message);
                    
                    // Update UI with new data
                    if (response.cart_items) {
                        updateCartDisplay(response.cart_items);
                    }
                    
                    if (response.cart_totals) {
                        updateTotalsDisplay(response.cart_totals);
                    }
                    
                    if (response.errors && response.errors.length > 0) {
                        response.errors.forEach(error => {
                            showToast('warning', error);
                        });
                    }
                } else {
                    showToast('error', response.message);
                }
            },
            error: function() {
                showToast('error', 'Có lỗi xảy ra khi cập nhật giỏ hàng');
            },
            complete: function() {
                isUpdating = false;
                hideLoading();
            }
        });
    }

    function clearCart() {
        showLoading();
        
        $.ajax({
            url: '<?= route_to('api_cart_clear') ?>',
            type: 'POST',
            data: {
                <?= csrf_token() ?>: '<?= csrf_hash() ?>'
            },
            success: function(response) {
                if (response.success) {
                    showToast('success', response.message);
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showToast('error', response.message);
                }
            },
            error: function() {
                showToast('error', 'Có lỗi xảy ra');
            },
            complete: function() {
                hideLoading();
            }
        });
    }

    function applyCoupon() {
        const couponCode = $('#coupon-code').val().trim();
        
        if (!couponCode) {
            showToast('warning', 'Vui lòng nhập mã giảm giá');
            return;
        }
        
        showLoading();
        
        $.ajax({
            url: '<?= route_to('api_cart_apply_promo') ?>',
            type: 'POST',
            data: {
                coupon_code: couponCode,
                <?= csrf_token() ?>: '<?= csrf_hash() ?>'
            },
            success: function(response) {
                if (response.success) {
                    showToast('success', response.message);
                    location.reload(); // Reload to show applied coupon
                } else {
                    showToast('error', response.message);
                }
            },
            error: function() {
                showToast('error', 'Có lỗi xảy ra');
            },
            complete: function() {
                hideLoading();
            }
        });
    }

    function removeCoupon() {
        showLoading();
        
        $.ajax({
            url: '<?= route_to('api_cart_remove_coupon') ?>',
            type: 'POST',
            data: {
                <?= csrf_token() ?>: '<?= csrf_hash() ?>'
            },
            success: function(response) {
                if (response.success) {
                    showToast('success', response.message);
                    location.reload();
                } else {
                    showToast('error', response.message);
                }
            },
            error: function() {
                showToast('error', 'Có lỗi xảy ra');
            },
            complete: function() {
                hideLoading();
            }
        });
    }

    function calculateShipping() {
        const province = $('#province-select').val();
        const district = $('#district-select').val();
        const postalCode = $('#postal-code').val();
        
        if (!province) {
            showToast('warning', 'Vui lòng chọn tỉnh/thành phố');
            return;
        }
        
        showLoading();
        
        $.ajax({
            url: '<?= route_to('api_cart_estimate_shipping') ?>',
            type: 'POST',
            data: {
                city: province,
                district: district,
                postal_code: postalCode,
                <?= csrf_token() ?>: '<?= csrf_hash() ?>'
            },
            success: function(response) {
                if (response.success) {
                    updateShippingFee(response.shipping_fee);
                    showToast('success', 'Đã cập nhật phí vận chuyển. Dự kiến giao hàng: ' + response.estimated_delivery);
                } else {
                    showToast('error', response.message);
                }
            },
            error: function() {
                showToast('error', 'Có lỗi xảy ra khi tính phí vận chuyển');
            },
            complete: function() {
                hideLoading();
            }
        });
    }

    function updateShippingFee(fee) {
        $('#shipping-fee').text(formatCurrency(fee) + '₫');
        updateCartTotals();
    }

    function loadDistricts(provinceCode) {
        // Mock districts data - in real app, this would come from API
        const districts = {
            'HCM': ['Quận 1', 'Quận 2', 'Quận 3', 'Quận 4', 'Quận 5'],
            'HN': ['Ba Đình', 'Hoàn Kiếm', 'Tây Hồ', 'Long Biên', 'Cầu Giấy'],
            'DN': ['Hải Châu', 'Thanh Khê', 'Sơn Trà', 'Ngũ Hành Sơn', 'Liên Chiểu']
        };
        
        const $districtSelect = $('#district-select');
        $districtSelect.empty().append('<option value="">Chọn Quận/Huyện</option>');
        
        if (districts[provinceCode]) {
            districts[provinceCode].forEach(district => {
                $districtSelect.append(`<option value="${district}">${district}</option>`);
            });
        }
    }

    function checkEmptyCart() {
        if ($('.cart-item').length === 0) {
            setTimeout(() => location.reload(), 1000);
        }
    }

    function updateCartDisplay(cartItems) {
        // Update cart items display with new data
        // This is a complex update - for simplicity, we reload the page
        // In production, you might want to update individual elements
    }

    function updateTotalsDisplay(totals) {
        $('#cart-subtotal').text(formatCurrency(totals.subtotal) + '₫');
        $('#shipping-fee').text(formatCurrency(totals.shipping_fee) + '₫');
        $('#cart-total').text(formatCurrency(totals.total) + '₫');
    }

    function showLoading() {
        $('#loading-overlay').show();
    }

    function hideLoading() {
        $('#loading-overlay').hide();
    }

    function showToast(type, message) {
        const toastClass = type === 'success' ? 'alert-success' : 
                          type === 'error' ? 'alert-danger' : 
                          type === 'warning' ? 'alert-warning' : 'alert-info';
        
        const toast = `
            <div class="alert ${toastClass} alert-dismissible fade show position-fixed" 
                 style="top: 20px; right: 20px; z-index: 10000; min-width: 300px; max-width: 400px;">
                <strong>${type.charAt(0).toUpperCase() + type.slice(1)}!</strong> ${message}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        `;
        
        $('body').append(toast);
        
        setTimeout(() => {
            $('.alert').fadeOut(() => $('.alert').remove());
        }, 5000);
    }

    function formatCurrency(amount) {
        return new Intl.NumberFormat('vi-VN').format(amount);
    }

    // Auto-save cart changes (debounced)
    let saveTimeout;
    $('.quantity-input').on('input', function() {
        clearTimeout(saveTimeout);
        saveTimeout = setTimeout(() => {
            if (!isUpdating) {
                updateCart();
            }
        }, 2000);
    });

    // Checkout validation
    $('#checkout-btn').click(function(e) {
        const hasOutOfStock = $('.out-of-stock').length > 0;
        const hasEmptyQuantity = $('.quantity-input').filter(function() {
            return parseInt($(this).val()) === 0;
        }).length > 0;
        
        if (hasOutOfStock) {
            e.preventDefault();
            showToast('error', 'Giỏ hàng có sản phẩm hết hàng. Vui lòng xóa hoặc thay thế.');
            return false;
        }
        
        if (hasEmptyQuantity) {
            e.preventDefault();
            showToast('error', 'Giỏ hàng có sản phẩm với số lượng 0. Vui lòng cập nhật số lượng.');
            return false;
        }
    });
});

</script>

<?= $this->endSection() ?>