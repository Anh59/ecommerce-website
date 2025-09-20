<?= $this->extend('Customers/layout/main') ?>

<?= $this->section('styles') ?>
<!-- CSS riêng cho cart -->
<link rel="stylesheet" href="<?= base_url('aranoz-master/css/nice-select.css'); ?>">
<style>
.cart-item-loading { opacity: 0.6; pointer-events: none; }
.cart-errors { background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
.cart-success { background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
.out-of-stock { opacity: 0.7; }
.out-of-stock .product-name { text-decoration: line-through; }

.empty-cart { text-align: center; padding: 60px 20px; }
.empty-cart i { font-size: 4rem; color: #ddd; margin-bottom: 20px; }
.loading-overlay { 
    position: fixed; top: 0; left: 0; width: 100%; height: 100%; 
    background: rgba(255,255,255,0.8); z-index: 9999; display: none; 
}
.loading-content { 
    position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center; 
}

/* Checkbox styles */
.product-checkbox {
    transform: scale(1.2);
    margin-right: 10px;
}

.select-all-section {
    background: #f8f9fa;
    padding: 15px 20px;
    border-radius: 5px;
    margin-bottom: 20px;
    border-left: 4px solid #007bff;
}

.select-all-checkbox {
    transform: scale(1.3);
    margin-right: 10px;
}

.selected-summary {
    background: #e3f2fd;
    padding: 15px;
    border-radius: 5px;
    margin: 20px 0;
    border-left: 4px solid #2196f3;
}

.checkout-section {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 5px;
    margin-top: 20px;
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
                        <h2>Shopping Cart</h2>
                        <p>Home <span>-</span> Shopping Cart</p>
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
                
                <!-- Select All Section -->
                <div class="select-all-section">
                    <label class="d-flex align-items-center mb-0">
                        <input type="checkbox" id="select-all" class="select-all-checkbox">
                        <strong>Chọn tất cả sản phẩm (<span id="total-items"><?= count($cartItems) ?></span>)</strong>
                    </label>
                </div>

                <!-- Selected Items Summary -->
                <div class="selected-summary" id="selected-summary" style="display: none;">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <strong>Đã chọn: <span id="selected-count">0</span> sản phẩm</strong>
                        </div>
                        <div class="col-md-6 text-right">
                            <strong>Tổng tiền: <span id="selected-total">0₫</span></strong>
                        </div>
                    </div>
                </div>

                <form id="cart-form" action="<?= route_to('cart_update') ?>" method="POST">
                    <?= csrf_field() ?>
                    <div class="table-responsive">
                        <table class="table" id="cart-table">
                            <thead>
                                <tr>
                                    <th scope="col" width="50">
                                        <input type="checkbox" id="header-select-all" class="product-checkbox">
                                    </th>
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
                                        <input type="checkbox" 
                                               class="product-checkbox item-checkbox" 
                                               data-product-id="<?= $item['product_id'] ?>"
                                               data-price="<?= $item['price'] ?>"
                                               data-quantity="<?= $item['quantity'] ?>"
                                               value="<?= $item['product_id'] ?>"
                                               <?= $item['stock_status'] === 'out_of_stock' ? 'disabled' : '' ?>>
                                    </td>
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
                                        <h5 class="item-price" data-price="<?= $item['price'] ?>"><?= number_format($item['price']) ?>₫</h5>
                                    </td>
                                    <td>
                                        <div class="product_count quantity-controls">
                                            <span class="input-number-decrement decrease-qty" data-product-id="<?= $item['product_id'] ?>">
                                                <i class="ti-minus"></i>
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
                                                <i class="ti-plus"></i>
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
                                    <td colspan="2">
                                        <button type="submit" class="btn_1" id="update-cart-btn">Update Cart</button>
                                        <a class="btn btn-outline-danger" href="#" id="clear-cart-btn">Clear Cart</a>
                                    </td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>

                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td><h5>Cart Total</h5></td>
                                    <td><h5 id="cart-total"><?= number_format($cartTotals['subtotal']) ?>₫</h5></td>
                                    <td></td>
                                </tr>
                            </tbody>
                        </table>
                        
                        <!-- Checkout Section -->
                        <div class="checkout-section">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <div class="selected-info">
                                        <h5>Thông tin đặt hàng:</h5>
                                        <p class="mb-1">Sản phẩm đã chọn: <strong><span id="checkout-selected-count">0</span></strong></p>
                                        <p class="mb-0">Tổng thanh toán: <strong><span id="checkout-selected-total">0₫</span></strong></p>
                                    </div>
                                </div>
                                <div class="col-md-6 text-right">
                                    <a class="btn_1 mr-3" href="<?= route_to('category') ?>">Continue Shopping</a>
                                    <button class="btn_1 checkout_btn_1" id="checkout-selected-btn" disabled>
                                        Mua hàng (<span id="checkout-btn-count">0</span>)
                                    </button>
                                </div>
                            </div>
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

<!-- Toast Container -->
<div class="toast-container" id="toast-container"></div>

<!--================End Cart Area =================-->
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- JS riêng cho cart -->
<script src="<?= base_url('aranoz-master/js/stellar.js'); ?>"></script>
<script src="<?= base_url('aranoz-master/js/custom.js'); ?>"></script>

<script>
$(document).ready(function() {
    let isUpdating = false;

    function debugLog(message) {
        console.log('[CART DEBUG] ' + message);
    }
    
    // ============= CHECKBOX FUNCTIONALITY =============
    
    // Select All functionality
    $('#select-all, #header-select-all').on('change', function() {
        const isChecked = $(this).is(':checked');
        $('.item-checkbox:not(:disabled)').prop('checked', isChecked);
        
        // Sync both select all checkboxes
        $('#select-all, #header-select-all').prop('checked', isChecked);
        
        updateSelectedSummary();
    });

    // Individual checkbox change
    $(document).on('change', '.item-checkbox', function() {
        updateSelectedSummary();
        
        // Update select all checkbox state
        const totalCheckboxes = $('.item-checkbox:not(:disabled)').length;
        const checkedCheckboxes = $('.item-checkbox:not(:disabled):checked').length;
        
        $('#select-all, #header-select-all').prop('checked', totalCheckboxes === checkedCheckboxes);
    });

    function updateSelectedSummary() {
        const selectedItems = $('.item-checkbox:checked');
        const selectedCount = selectedItems.length;
        let selectedTotal = 0;

        selectedItems.each(function() {
            const price = parseInt($(this).data('price'));
            const quantity = parseInt($(this).data('quantity'));
            selectedTotal += price * quantity;
        });

        // Update summary display
        $('#selected-count').text(selectedCount);
        $('#selected-total').text(formatCurrency(selectedTotal) + '₫');
        
        // Update checkout section
        $('#checkout-selected-count').text(selectedCount);
        $('#checkout-selected-total').text(formatCurrency(selectedTotal) + '₫');
        $('#checkout-btn-count').text(selectedCount);

        // Show/hide summary and enable/disable checkout button
        if (selectedCount > 0) {
            $('#selected-summary').show();
            $('#checkout-selected-btn').prop('disabled', false).removeClass('btn-secondary').addClass('checkout_btn_1');
        } else {
            $('#selected-summary').hide();
            $('#checkout-selected-btn').prop('disabled', true).removeClass('checkout_btn_1').addClass('btn-secondary');
        }

        debugLog(`Selected: ${selectedCount} items, Total: ${selectedTotal}`);
    }

    // Checkout selected items
    $('#checkout-selected-btn').on('click', function(e) {
        e.preventDefault();
        
        const selectedItems = $('.item-checkbox:checked');
        if (selectedItems.length === 0) {
            showToast('warning', 'Vui lòng chọn ít nhất một sản phẩm');
            return;
        }

        // Check for out of stock items
        let hasOutOfStock = false;
        selectedItems.each(function() {
            const $row = $(this).closest('.cart-item');
            if ($row.hasClass('out-of-stock')) {
                hasOutOfStock = true;
                return false;
            }
        });

        if (hasOutOfStock) {
            showToast('error', 'Có sản phẩm hết hàng trong danh sách đã chọn');
            return;
        }

        // Collect selected product IDs
        const selectedProductIds = [];
        selectedItems.each(function() {
            selectedProductIds.push($(this).val());
        });

        // Store selected items in session and redirect to checkout
        $.ajax({
            url: '<?= base_url() ?>api/cart/set-checkout-items',
            type: 'POST',
            data: {
                selected_items: selectedProductIds,
                '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
            },
            success: function(response) {
                if (response.success) {
                    window.location.href = '<?= route_to('api_cart_checkout') ?>';
                } else {
                    showToast('error', response.message);
                }
            },
            error: function() {
                showToast('error', 'Có lỗi xảy ra khi chuẩn bị thanh toán');
            }
        });
    });
    
    // ============= QUANTITY CONTROLS =============
    
    $('.increase-qty, .decrease-qty, .quantity-input').addClass('cart-fixed');
    
    debugLog('Cart quantity fix initialized');

    // Increase quantity
    $(document).on('click', '.increase-qty.cart-fixed', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        if (isUpdating) return;
        
        const $row = $(this).closest('.cart-item');
        const productId = $row.data('product-id');
        const $input = $row.find('.quantity-input');
        const maxValue = parseInt($input.attr('max')) || 999;
        
        debugLog(`Increase clicked for product ${productId}`);
        
        $.ajax({
            url: '<?= base_url() ?>api/cart/data',
            type: 'GET',
            success: function(response) {
                if (response.success && response.data.items) {
                    const item = response.data.items.find(i => i.product_id == productId);
                    if (item) {
                        const currentQuantity = parseInt(item.quantity);
                        
                        if (currentQuantity < maxValue) {
                            const newQuantity = currentQuantity + 1;
                            debugLog(`Server says ${currentQuantity}, increasing to ${newQuantity}`);
                            updateQuantityServer(productId, newQuantity);
                        } else {
                            showToast('warning', `Chỉ có ${maxValue} sản phẩm trong kho`);
                        }
                    }
                }
            }
        });
    });

    // Decrease quantity
    $(document).on('click', '.decrease-qty.cart-fixed', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        if (isUpdating) return;
        
        const $row = $(this).closest('.cart-item');
        const productId = $row.data('product-id');
        
        debugLog(`Decrease clicked for product ${productId}`);
        
        $.ajax({
            url: '<?= base_url() ?>api/cart/data',
            type: 'GET',
            success: function(response) {
                if (response.success && response.data.items) {
                    const item = response.data.items.find(i => i.product_id == productId);
                    if (item) {
                        const currentQuantity = parseInt(item.quantity);
                        
                        if (currentQuantity > 1) {
                            const newQuantity = currentQuantity - 1;
                            debugLog(`Server says ${currentQuantity}, decreasing to ${newQuantity}`);
                            updateQuantityServer(productId, newQuantity);
                        } else if (currentQuantity === 1) {
                            if (confirm('Bạn có muốn xóa sản phẩm này khỏi giỏ hàng không?')) {
                                updateQuantityServer(productId, 0);
                            }
                        }
                    }
                }
            }
        });
    });

    // Input change
    $(document).on('change', '.quantity-input.cart-fixed', function(e) {
        if (isUpdating) return;
        
        const $input = $(this);
        const productId = $input.data('product-id');
        let value = parseInt($input.val()) || 0;
        const maxValue = parseInt($input.attr('max')) || 999;
        
        debugLog(`Input changed: product=${productId}, value=${value}`);
        
        if (value < 0) {
            value = 1;
            $input.val(1);
        } else if (value > maxValue) {
            value = maxValue;
            $input.val(maxValue);
            showToast('warning', `Chỉ có ${maxValue} sản phẩm trong kho`);
        }
        
        if (value === 0) {
            if (confirm('Bạn có muốn xóa sản phẩm này khỏi giỏ hàng không?')) {
                updateQuantityServer(productId, 0);
            } else {
                $input.val(1);
            }
        } else {
            updateQuantityServer(productId, value);
        }
    });

    function updateQuantityServer(productId, quantity) {
        if (isUpdating) return;
        
        isUpdating = true;
        debugLog(`Updating server: product=${productId}, quantity=${quantity}`);
        
        showLoading();
        
        $.ajax({
            url: '<?= base_url() ?>api/cart/update-quantity',
            type: 'POST',
            data: {
                product_id: productId,
                quantity: quantity,
                '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
            },
            success: function(response) {
                debugLog('Server response: ' + JSON.stringify(response));
                
                if (response.success) {
                    if (response.action === 'removed') {
                        $(`.cart-item[data-product-id="${productId}"]`).fadeOut(function() {
                            $(this).remove();
                            checkEmptyCart();
                            updateSelectedSummary();
                        });
                        showToast('success', response.message);
                    } else {
                        const $input = $(`.quantity-input[data-product-id="${productId}"]`);
                        $input.val(quantity);
                        
                        // Update checkbox data-quantity
                        $(`.item-checkbox[data-product-id="${productId}"]`).attr('data-quantity', quantity);
                        
                        updateItemDisplay(productId, quantity);
                        updateSelectedSummary();
                        showToast('success', response.message);
                    }
                    
                    updateTotalsFromResponse(response);
                } else {
                    showToast('error', response.message);
                }
            },
            error: function(xhr, status, error) {
                debugLog(`Error: ${status} - ${error}`);
                showToast('error', 'Có lỗi xảy ra: ' + error);
            },
            complete: function() {
                isUpdating = false;
                hideLoading();
            }
        });
    }

    function updateItemDisplay(productId, quantity) {
        const $row = $(`.cart-item[data-product-id="${productId}"]`);
        const $input = $row.find('.quantity-input');
        const price = parseInt($input.data('price')) || 0;
        const total = quantity * price;

        $input.val(quantity);
        $row.find('.item-total').text(formatCurrency(total) + '₫');
    }

    function updateTotalsFromResponse(response) {
        if (response.total !== undefined) {
            $('#cart-total').text(formatCurrency(response.subtotal) + '₫');
        }
    }

    // ============= OTHER FUNCTIONS =============

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

    function removeItem(productId) {
        showLoading();
        
        $.ajax({
            url: '<?= route_to('api_cart_remove') ?>',
            type: 'POST',
            data: {
                product_id: productId,
                '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
            },
            success: function(response) {
                if (response.success) {
                    $(`.cart-item[data-product-id="${productId}"]`).fadeOut(function() {
                        $(this).remove();
                        checkEmptyCart();
                        updateSelectedSummary();
                    });
                    
                    if (response.cart_totals) {
                        updateTotalsDisplay(response.cart_totals);
                    }
                    
                    showToast('success', response.message);
                } else {
                    showToast('error', response.message);
                }
            },
            error: function() {
                showToast('error', 'Có lỗi xảy ra khi xóa sản phẩm');
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
            url: '<?= route_to('cart_update') ?>',
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    showToast('success', response.message);
                    
                    if (response.cart_totals) {
                        updateTotalsDisplay(response.cart_totals);
                    }
                    
                    if (response.errors && response.errors.length > 0) {
                        response.errors.forEach(error => {
                            showToast('warning', error);
                        });
                    }
                    
                    if (response.removed_count > 0) {
                        setTimeout(() => location.reload(), 2000);
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
                '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
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

    function checkEmptyCart() {
        if ($('.cart-item').length === 0) {
            setTimeout(() => location.reload(), 1000);
        }
    }

    function updateTotalsDisplay(totals) {
        $('#cart-total').text(formatCurrency(totals.subtotal) + '₫');
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
            <div class="alert ${toastClass} alert-dismissible fade show" 
                 style="position: fixed; top: 20px; right: 20px; z-index: 10000; min-width: 300px; max-width: 400px;">
                <strong>${type.charAt(0).toUpperCase() + type.slice(1)}!</strong> ${message}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        `;
        
        $('body').append(toast);
        
        setTimeout(() => {
            $('.alert').first().fadeOut(() => $('.alert').first().remove());
        }, 5000);
    }

    function formatCurrency(amount) {
        return new Intl.NumberFormat('vi-VN').format(amount);
    }

    // Initialize selected summary on page load
    updateSelectedSummary();

    // Sync quantities from server on page load
    $.ajax({
        url: '<?= base_url() ?>api/cart/data',
        type: 'GET',
        success: function(response) {
            if (response.success && response.data.items) {
                response.data.items.forEach(item => {
                    const $input = $(`.quantity-input[data-product-id="${item.product_id}"]`);
                    if ($input.length) {
                        $input.val(item.quantity);
                        // Update checkbox data-quantity
                        $(`.item-checkbox[data-product-id="${item.product_id}"]`).attr('data-quantity', item.quantity);
                        debugLog(`Synced product ${item.product_id}: ${item.quantity}`);
                    }
                });
                debugLog('All quantities synced from server on page load');
                updateSelectedSummary();
            }
        },
        error: function() {
            debugLog('Failed to sync quantities from server on page load');
        }
    });

    debugLog('Enhanced Cart with Checkboxes JavaScript initialized');
});
</script>

<?= $this->endSection() ?>