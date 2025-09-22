<?= $this->extend('Customers/layout/main') ?>

<?= $this->section('styles') ?>
<style>
.checkout-container { background: #f8f9fa; min-height: 100vh; padding: 40px 0; }
.checkout-step { background: white; border-radius: 8px; padding: 25px; margin-bottom: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
.step-header { border-bottom: 1px solid #eee; padding-bottom: 15px; margin-bottom: 20px; }
.step-number { 
    display: inline-flex; 
    align-items: center; 
    justify-content: center; 
    width: 30px; 
    height: 30px; 
    background: #007bff; 
    color: white; 
    border-radius: 50%; 
    margin-right: 10px; 
    font-weight: 600; 
}
.order-summary { background: white; border-radius: 8px; padding: 20px; position: sticky; top: 20px; }
.order-item { display: flex; align-items: center; padding: 10px 0; border-bottom: 1px solid #f0f0f0; }
.order-item:last-child { border-bottom: none; }
.order-item img { width: 60px; height: 60px; object-fit: cover; border-radius: 4px; margin-right: 15px; }
.order-item-info { flex: 1; }
.order-item-name { font-weight: 600; margin-bottom: 5px; }
.order-item-price { color: #666; font-size: 14px; }
.payment-method { border: 2px solid #e9ecef; border-radius: 8px; padding: 15px; margin-bottom: 10px; cursor: pointer; transition: all 0.3s; }
.payment-method:hover { border-color: #007bff; }
.payment-method.active { border-color: #007bff; background: #f8f9ff; }
.payment-method input[type="radio"] { margin-right: 10px; }
.payment-method.disabled { opacity: 0.5; cursor: not-allowed; }
.shipping-method { border: 1px solid #ddd; border-radius: 5px; padding: 10px; margin-bottom: 10px; cursor: pointer; }
.shipping-method.active { border-color: #007bff; background: #f8f9ff; }
.order-total { background: #f8f9fa; padding: 15px; border-radius: 5px; margin-top: 20px; }
.btn-place-order { 
    background: #28a745; 
    color: white; 
    border: none; 
    padding: 15px 30px; 
    font-size: 16px; 
    font-weight: 600; 
    border-radius: 5px; 
    width: 100%; 
}
.btn-place-order:hover { background: #218838; color: white; }
.btn-place-order:disabled { background: #6c757d; cursor: not-allowed; }
.loading-spinner { display: none; }
.checkout-type-badge { 
    background: #007bff; 
    color: white; 
    padding: 4px 8px; 
    border-radius: 12px; 
    font-size: 12px; 
    margin-left: 10px; 
}
.loading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(255, 255, 255, 0.9);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 9999;
}
.loading-content {
    text-align: center;
}
.is-invalid {
    border-color: #dc3545 !important;
}
.alert-dismissible {
    position: relative;
    padding-right: 4rem;
}
.alert .close {
    position: absolute;
    top: 0;
    right: 0;
    padding: 0.75rem 1.25rem;
    color: inherit;
}
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<section class="breadcrumb breadcrumb_bg">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="breadcrumb_iner">
                        <div class="breadcrumb_iner_item">
                            <h2>Thanh toán</h2>
                            <p>Trang chủ<span>-</span> Thanh toán</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
<div class="checkout-container">
    <div class="container">
        <div class="row">
            <!-- Checkout Form -->
            <div class="col-lg-8">
                <form id="checkout-form">
                    <?= csrf_field() ?>

                    <!-- Step 1: Shipping Information -->
                    <div class="checkout-step">
                        <div class="step-header">
                            <h4>
                                <span class="step-number">1</span>
                                Thông tin giao hàng
                                <?php if ($checkoutType !== 'cart'): ?>
                                    <span class="checkout-type-badge">
                                        <?= $checkoutType === 'buy_now' ? 'Mua ngay' : 'Sản phẩm đã chọn' ?>
                                    </span>
                                <?php endif; ?>
                            </h4>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="shipping_name">Họ và tên *</label>
                                    <input type="text" class="form-control" id="shipping_name" name="shipping_name" 
                                           value="<?= esc($defaultShipping['name']) ?>" required>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="shipping_phone">Số điện thoại *</label>
                                    <input type="tel" class="form-control" id="shipping_phone" name="shipping_phone" 
                                           value="<?= esc($defaultShipping['phone']) ?>" required>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="shipping_address">Địa chỉ *</label>
                            <textarea class="form-control" id="shipping_address" name="shipping_address" 
                                      rows="3" required><?= esc($defaultShipping['address']) ?></textarea>
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="form-group">
                            <label for="notes">Ghi chú đơn hàng</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3" 
                                      placeholder="Ghi chú về đơn hàng, ví dụ: thời gian hay chỉ dẫn địa điểm giao hàng chi tiết hơn."></textarea>
                        </div>
                    </div>

                    <!-- Step 2: Shipping Method -->
                    <div class="checkout-step">
                        <div class="step-header">
                            <h4><span class="step-number">2</span>Phương thức giao hàng</h4>
                        </div>
                        
                        <?php foreach ($shippingOptions as $key => $option): ?>
                            <div class="shipping-method <?= $key === 'standard' ? 'active' : '' ?>">
                                <label class="mb-0 w-100" style="cursor: pointer;">
                                    <input type="radio" name="shipping_method" value="<?= $key ?>" 
                                           <?= $key === 'standard' ? 'checked' : '' ?>>
                                    <strong><?= esc($option['name']) ?></strong> 
                                    <span class="float-right text-primary font-weight-bold">
                                        <?= $option['price'] > 0 ? number_format($option['price']) . '₫' : 'Miễn phí' ?>
                                    </span>
                                    <div class="text-muted small mt-1">
                                        <?= esc($option['description']) ?> - <?= esc($option['time']) ?>
                                    </div>
                                </label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                            
                    <!-- Step 3: Payment Method -->
                    <div class="checkout-step">
                        <div class="step-header">
                            <h4><span class="step-number">3</span>Phương thức thanh toán</h4>
                        </div>

                        <?php foreach ($paymentMethods as $key => $method): ?>
                            <div class="payment-method <?= $key === 'cod' ? 'active' : '' ?> <?= !$method['available'] ? 'disabled' : '' ?>">
                                <label class="mb-0 w-100" style="cursor: pointer;">
                                    <input type="radio" name="payment_method" value="<?= $key ?>" 
                                           <?= $key === 'cod' ? 'checked' : '' ?>
                                           <?= !$method['available'] ? 'disabled' : '' ?>>
                                    <i class="<?= esc($method['icon']) ?> mr-2"></i>
                                    <strong><?= esc($method['name']) ?></strong>
                                    <div class="text-muted small mt-1">
                                        <?= esc($method['description']) ?>
                                        <?php if (!$method['available']): ?>
                                            <span class="text-warning">(Sắp ra mắt)</span>
                                        <?php endif; ?>
                                    </div>
                                </label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                </form>
            </div>

            <!-- Order Summary -->
            <div class="col-lg-4">
                
                <div class="order-summary">
                    <h5 class="mb-3">Đơn hàng của bạn</h5>
                    
                    <!-- Order Items -->
                    <div class="order-items">
                        <?php foreach ($checkoutItems as $item): ?>
                            <div class="order-item">
                                <img src="<?= base_url($item['main_image'] ?? 'aranoz-master/img/product/product_1.png') ?>" 
                                     alt="<?= esc($item['name']) ?>">
                                <div class="order-item-info">
                                    <div class="order-item-name"><?= esc($item['name']) ?></div>
                                    <div class="order-item-price">
                                        <?= number_format($item['price']) ?>₫ × <?= $item['quantity'] ?>
                                    </div>
                                </div>
                                <div class="order-item-total font-weight-bold">
                                    <?= number_format($item['price'] * $item['quantity']) ?>₫
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Coupon Section -->
                    <?php if ($appliedCoupon): ?>
                        <div class="mt-3 p-2 bg-success text-white rounded">
                            <small>
                                <i class="ti-check mr-1"></i>
                                Mã giảm giá "<?= esc($appliedCoupon['code']) ?>" đã được áp dụng
                                <button type="button" class="btn btn-sm btn-link text-white p-0 ml-2" id="remove-coupon">
                                    <i class="ti-close"></i>
                                </button>
                            </small>
                        </div>
                    <?php else: ?>
                        <div class="coupon-section mt-3">
                            <div class="input-group">
                                <input type="text" class="form-control" id="coupon-code" placeholder="Nhập mã giảm giá">
                                <div class="input-group-append">
                                    <button class="btn btn-outline-primary" type="button" id="apply-coupon">
                                        <i class="ti-tag"></i> Áp dụng
                                    </button>
                                </div>
                            </div>
                            <div id="coupon-message" class="mt-1 small"></div>
                        </div>
                    <?php endif; ?>

                    <!-- Order Total -->
                    <div class="order-total">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Tạm tính (<?= $orderSummary['total_quantity'] ?> sản phẩm):</span>
                            <span><?= number_format($orderSummary['subtotal']) ?>₫</span>
                        </div>
                        
                        <?php if ($orderSummary['discount'] > 0): ?>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Giảm giá:</span>
                                <span class="text-success">-<?= number_format($orderSummary['discount']) ?>₫</span>
                            </div>
                        <?php endif; ?>
                        
                        <div class="d-flex justify-content-between mb-2">
                            <span>Phí vận chuyển:</span>
                            <span id="checkout-shipping-fee">
                                <?= $orderSummary['shipping_fee'] > 0 ? number_format($orderSummary['shipping_fee']) . '₫' : 'Miễn phí' ?>
                            </span>
                        </div>
                        
                        <hr>
                        <div class="d-flex justify-content-between">
                            <strong>Tổng cộng:</strong>
                            <strong class="text-primary" id="checkout-total"><?= number_format($orderSummary['total']) ?>₫</strong>
                        </div>
                    </div>

                    <!-- Place Order Button -->
                    <button type="submit" form="checkout-form" class="btn-place-order mt-3" id="place-order-btn">
                        <span class="loading-spinner">
                            <i class="fa fa-spinner fa-spin mr-2"></i>
                        </span>
                        Đặt hàng
                    </button>

                    <!-- Order Info -->
                    <div class="mt-3 text-center">
                        <small class="text-muted">
                            Bằng việc đặt hàng, bạn đồng ý với 
                            <a href="#" class="text-primary">Điều khoản sử dụng</a> của chúng tôi
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Loading Overlay -->
<div class="loading-overlay" id="loading-overlay" style="display: none;">
    <div class="loading-content">
        <i class="fa fa-spinner fa-spin fa-3x text-primary"></i>
        <p class="mt-3">Đang xử lý đơn hàng...</p>
    </div>
</div>

<!-- Toast Container -->
<div class="toast-container" id="toast-container" style="position: fixed; top: 20px; right: 20px; z-index: 10000;"></div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    let isProcessing = false;
    let shippingOptions = <?= json_encode($shippingOptions) ?>;
    let orderSummary = <?= json_encode($orderSummary) ?>;

    // Helper functions
    const csrfToken = () => $('meta[name="csrf-token"]').attr('content') || $('input[name="<?= csrf_token() ?>"]').val();
    const updateToken = (token) => { 
        $('meta[name="csrf-token"]').attr('content', token); 
        $('input[name="<?= csrf_token() ?>"]').val(token); 
    };

    // Payment method selection
    $('.payment-method:not(.disabled)').click(function() {
        if ($(this).hasClass('disabled')) return;
        
        $('.payment-method').removeClass('active');
        $(this).addClass('active');
        $(this).find('input[type="radio"]').prop('checked', true);
    });

    // Shipping method selection
    $('.shipping-method').click(function() {
        $('.shipping-method').removeClass('active');
        $(this).addClass('active');
        $(this).find('input[type="radio"]').prop('checked', true);
        
        // Update shipping fee
        updateShippingFee();
    });

    // Form validation
    function clearFieldErrors() {
        $('.form-control').removeClass('is-invalid');
        $('.invalid-feedback').text('');
    }

    function showFieldError(fieldName, message) {
        const field = $(`#${fieldName}, input[name="${fieldName}"], textarea[name="${fieldName}"]`);
        field.addClass('is-invalid');
        field.siblings('.invalid-feedback').text(message);
    }

    // Form submission
    $('#checkout-form').submit(function(e) {
        e.preventDefault();
        
        if (isProcessing) return;
        
        if (!validateCheckoutForm()) {
            return;
        }
        
        processOrder();
    });

    function validateCheckoutForm() {
        clearFieldErrors();
        
        const requiredFields = [
            { name: 'shipping_name', label: 'Họ và tên' },
            { name: 'shipping_phone', label: 'Số điện thoại' },
            { name: 'shipping_address', label: 'Địa chỉ' }
        ];
        
        let isValid = true;
        let firstInvalidField = null;
        
        // Check required fields
        requiredFields.forEach(field => {
            const $field = $(`#${field.name}`);
            const value = $field.val().trim();
            
            if (!value) {
                showFieldError(field.name, `${field.label} là bắt buộc`);
                isValid = false;
                
                if (!firstInvalidField) {
                    firstInvalidField = $field;
                }
            }
        });
        
        // Validate phone number
        const phone = $('#shipping_phone').val().trim();
        if (phone && (phone.length < 10 || phone.length > 15 || !/^[0-9+\-\s]+$/.test(phone))) {
            showFieldError('shipping_phone', 'Số điện thoại không hợp lệ');
            isValid = false;
            if (!firstInvalidField) {
                firstInvalidField = $('#shipping_phone');
            }
        }
        
        // Check if payment method is selected
        if (!$('input[name="payment_method"]:checked').length) {
            showToast('error', 'Vui lòng chọn phương thức thanh toán');
            isValid = false;
        }
        
        // Check if shipping method is selected
        if (!$('input[name="shipping_method"]:checked').length) {
            showToast('error', 'Vui lòng chọn phương thức giao hàng');
            isValid = false;
        }
        
        if (!isValid) {
            showToast('error', 'Vui lòng điền đầy đủ thông tin bắt buộc');
            if (firstInvalidField) {
                firstInvalidField.focus();
            }
        }
        
        return isValid;
    }

    function processOrder() {
        isProcessing = true;
        
        $('#place-order-btn').prop('disabled', true);
        $('.loading-spinner').show();
        $('#loading-overlay').show();
        
        const formData = $('#checkout-form').serialize();
        
        $.ajax({
            url: '<?= route_to('checkout_process') ?>',
            type: 'POST',
            data: formData,
            dataType: 'json',
            timeout: 30000, // 30 seconds timeout
            success: function(response) {
                if (response.success) {
                    showToast('success', response.message);
                    
                    // Handle different payment methods
                    if (response.payment_result) {
                        if (response.payment_result.status === 'redirect') {
                            // Redirect to payment gateway
                            window.location.href = response.payment_result.redirect_url;
                            return;
                        }
                    }
                    
                    // For COD or successful payments, redirect to success page
                    setTimeout(() => {
                        const successUrl = '<?= base_url('/checkout/success/') ?>' + response.order_number;
                        window.location.href = successUrl;
                    }, 1500);
                    
                } else {
                    showToast('error', response.message || 'Có lỗi xảy ra trong quá trình đặt hàng');
                    
                    if (response.errors) {
                        Object.keys(response.errors).forEach(field => {
                            showFieldError(field, response.errors[field]);
                        });
                    }
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', {
                    status: xhr.status,
                    statusText: xhr.statusText,
                    responseText: xhr.responseText
                });
                
                let message = 'Có lỗi xảy ra trong quá trình đặt hàng';
                
                if (xhr.status === 422) {
                    // Validation errors
                    try {
                        const errorResponse = JSON.parse(xhr.responseText);
                        if (errorResponse.errors) {
                            Object.keys(errorResponse.errors).forEach(field => {
                                showFieldError(field, errorResponse.errors[field]);
                            });
                        }
                        message = errorResponse.message || 'Dữ liệu không hợp lệ';
                    } catch (e) {
                        message = 'Dữ liệu không hợp lệ';
                    }
                } else if (xhr.status === 500) {
                    message = 'Lỗi máy chủ. Vui lòng thử lại sau';
                } else if (xhr.status === 0 || status === 'timeout') {
                    message = 'Kết nối bị gián đoạn. Vui lòng kiểm tra mạng và thử lại';
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }
                
                showToast('error', message);
            },
            complete: function() {
                isProcessing = false;
                $('#place-order-btn').prop('disabled', false);
                $('.loading-spinner').hide();
                $('#loading-overlay').hide();
            }
        });
    }

    function updateShippingFee() {
        const selectedShipping = $('input[name="shipping_method"]:checked').val();
        
        if (shippingOptions[selectedShipping]) {
            const fee = shippingOptions[selectedShipping].price;
            const subtotal = orderSummary.subtotal;
            const discount = orderSummary.discount;
            
            // Apply free shipping rule
            let actualFee = fee;
            if (subtotal >= 500000) {
                actualFee = 0;
            }
            
            // Update shipping fee display
            const feeText = actualFee > 0 ? formatCurrency(actualFee) + '₫' : 'Miễn phí';
            $('#checkout-shipping-fee').text(feeText);
            
            // Update total
            const newTotal = subtotal - discount + actualFee;
            $('#checkout-total').text(formatCurrency(newTotal) + '₫');
            
            // Update orderSummary for future calculations
            orderSummary.shipping_fee = actualFee;
            orderSummary.total = newTotal;
        }
    }

    function showToast(type, message) {
        const toastClass = type === 'success' ? 'alert-success' : 
                          type === 'error' ? 'alert-danger' : 
                          type === 'warning' ? 'alert-warning' : 'alert-info';
        
        const iconClass = type === 'success' ? 'ti-check' : 
                         type === 'error' ? 'ti-close' : 
                         type === 'warning' ? 'ti-alert' : 'ti-info';
        
        const toast = `
            <div class="alert ${toastClass} alert-dismissible fade show" 
                 style="min-width: 300px; max-width: 400px; margin-bottom: 10px;">
                <i class="${iconClass} mr-2"></i>
                <strong>${type.charAt(0).toUpperCase() + type.slice(1)}!</strong> ${message}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        `;
        
        $('#toast-container').append(toast);
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            $('#toast-container .alert').first().fadeOut(() => {
                $('#toast-container .alert').first().remove();
            });
        }, 5000);
    }

    function formatCurrency(amount) {
        return new Intl.NumberFormat('vi-VN').format(amount);
    }

    // Apply coupon functionality (if routes exist)
    $('#apply-coupon').click(function() {
        const couponCode = $('#coupon-code').val().trim();
        
        if (!couponCode) {
            showToast('error', 'Vui lòng nhập mã giảm giá');
            return;
        }
        
        const $button = $(this);
        $button.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Đang xử lý...');
        
        // Check if apply_coupon route exists
        const applyCouponUrl = '<?= route_to("apply_coupon") ?? "" ?>';
        if (!applyCouponUrl) {
            showToast('warning', 'Chức năng mã giảm giá chưa được kích hoạt');
            $button.prop('disabled', false).html('<i class="ti-tag"></i> Áp dụng');
            return;
        }
        
        $.post(applyCouponUrl, {
            '<?= csrf_token() ?>': csrfToken(),
            coupon_code: couponCode,
            order_amount: orderSummary.subtotal,
            product_ids: <?= json_encode(array_column($checkoutItems, 'product_id')) ?>
        })
        .done(function(response) {
            if (response.token) updateToken(response.token);
            
            if (response.success) {
                showToast('success', response.message);
                setTimeout(() => location.reload(), 1000);
            } else {
                showToast('error', response.message);
                $('#coupon-message').html('<div class="text-danger small">' + response.message + '</div>');
            }
        })
        .fail(function(xhr) {
            let message = 'Có lỗi xảy ra khi áp dụng mã giảm giá';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                message = xhr.responseJSON.message;
            }
            showToast('error', message);
        })
        .always(function() {
            $button.prop('disabled', false).html('<i class="ti-tag"></i> Áp dụng');
        });
    });

    // Remove coupon functionality
    $('#remove-coupon').click(function() {
        const removeCouponUrl = '<?= route_to("remove_coupon") ?? "" ?>';
        if (!removeCouponUrl) {
            showToast('warning', 'Chức năng mã giảm giá chưa được kích hoạt');
            return;
        }
        
        $.post(removeCouponUrl, {
            '<?= csrf_token() ?>': csrfToken()
        })
        .done(function(response) {
            if (response.token) updateToken(response.token);
            
            if (response.success) {
                showToast('success', response.message);
                setTimeout(() => location.reload(), 1000);
            }
        })
        .fail(function() {
            showToast('error', 'Có lỗi xảy ra khi xóa mã giảm giá');
        });
    });

    // Input validation on typing
    $('#shipping_name').on('input', function() {
        const value = $(this).val().trim();
        if (value.length >= 2) {
            $(this).removeClass('is-invalid');
            $(this).siblings('.invalid-feedback').text('');
        }
    });

    $('#shipping_phone').on('input', function() {
        const value = $(this).val().trim();
        const phoneRegex = /^[0-9+\-\s]+$/;
        
        if (value.length >= 10 && value.length <= 15 && phoneRegex.test(value)) {
            $(this).removeClass('is-invalid');
            $(this).siblings('.invalid-feedback').text('');
        }
    });

    $('#shipping_address').on('input', function() {
        const value = $(this).val().trim();
        if (value.length >= 10) {
            $(this).removeClass('is-invalid');
            $(this).siblings('.invalid-feedback').text('');
        }
    });

    // Confirm navigation away if form is being filled
    let formModified = false;
    $('#checkout-form input, #checkout-form textarea').on('input change', function() {
        formModified = true;
    });

    $(window).on('beforeunload', function() {
        if (formModified && !isProcessing) {
            return 'Bạn có chắc chắn muốn rời khỏi trang? Thông tin đã nhập sẽ bị mất.';
        }
    });

    console.log('Checkout form initialized successfully');
});
</script>
<?= $this->endSection() ?>