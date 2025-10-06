<?php

namespace App\Controllers;

use App\Models\CartModel;
use App\Models\ProductModel;
use App\Models\CustomerModel;
use App\Models\OrderModel;
use App\Models\OrderItemModel;
use App\Models\DiscountCouponModel; 
use App\Libraries\MomoService; 
class CheckoutController extends BaseController
{
    protected $cartModel;
    protected $productModel;
    protected $customerModel;
    protected $orderModel;
    protected $orderItemModel;
     protected $momoService;
       protected $discountCouponModel;
    public function __construct()
    {
        $this->cartModel = new CartModel();
        $this->productModel = new ProductModel();
        $this->customerModel = new CustomerModel();
        $this->orderModel = new OrderModel();
        $this->orderItemModel = new OrderItemModel();
        $this->momoService = new MomoService(); 
         $this->discountCouponModel = new DiscountCouponModel();
    }

    public function index()
    {
        $session = session();
        $customerId = $session->get('customer_id');

        if (!$customerId) {
            return redirect()->to('/api_Customers/customers_sign')
                           ->with('error', 'Vui lòng đăng nhập để thanh toán');
        }

        // Lấy thông tin khách hàng
        $customer = $this->customerModel->find($customerId);
        if (!$customer) {
            return redirect()->to('/api_Customers/customers_sign')
                           ->with('error', 'Thông tin tài khoản không hợp lệ');
        }

        // FIXED: Determine checkout type with correct priority
        $checkoutType = 'cart'; // Default
        $checkoutItems = [];

        log_message('debug', 'CheckoutController - Session data: ' . json_encode([
            'buy_now_mode' => $session->get('buy_now_mode'),
            'checkout_selected_items' => $session->get('checkout_selected_items') ? 'exists' : 'not_exists'
        ]));

        // Priority 1: Check for selected items from cart (HIGHEST PRIORITY)
        $selectedItems = $session->get('checkout_selected_items');
        if ($selectedItems && !empty($selectedItems)) {
            log_message('debug', 'CheckoutController - Using selected items from cart');
            $checkoutType = 'selected';
            $checkoutItems = $selectedItems;
            
            // Clear buy_now_mode if exists to avoid conflicts
            if ($session->get('buy_now_mode')) {
                $session->remove('buy_now_mode');
                log_message('debug', 'CheckoutController - Cleared buy_now_mode due to selected items');
            }
        }
        // Priority 2: Check for buy now mode (only if no selected items)
        else {
            $buyNowMode = $session->get('buy_now_mode');
            if ($buyNowMode && isset($buyNowMode['product_id'])) {
                log_message('debug', 'CheckoutController - Using buy now mode');
                $checkoutType = 'buy_now';
                
                // Get single product for buy now
                $product = $this->productModel->find($buyNowMode['product_id']);
                if ($product) {
                    $price = !empty($product['sale_price']) && $product['sale_price'] > 0 
                        ? $product['sale_price'] 
                        : $product['price'];
                    
                    $checkoutItems = [[
                        'product_id' => $product['id'],
                        'name' => $product['name'],
                        'quantity' => $buyNowMode['quantity'],
                        'price' => $price,
                        'total' => $price * $buyNowMode['quantity'],
                        'main_image' => $product['main_image'],
                        'slug' => $product['slug'],
                        'sku' => $product['sku'] ?? ''
                    ]];
                }
            }
        }
        
        // Priority 3: Fallback to all cart items
        if (empty($checkoutItems)) {
            log_message('debug', 'CheckoutController - Using all cart items as fallback');
            $checkoutType = 'cart';
            $cartItems = $this->cartModel->getCartWithProducts($customerId);
            
            if (empty($cartItems)) {
                return redirect()->to('/cart')
                               ->with('error', 'Giỏ hàng trống. Vui lòng thêm sản phẩm trước khi thanh toán');
            }

            foreach ($cartItems as $item) {
                $checkoutItems[] = [
                    'product_id' => $item['product_id'],
                    'name' => $item['name'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'total' => $item['price'] * $item['quantity'],
                    'main_image' => $item['main_image'],
                    'slug' => $item['slug'],
                    'category_name' => $item['category_name'] ?? '',
                    'brand_name' => $item['brand_name'] ?? '',
                    'sku' => $item['sku'] ?? ''
                ];
            }
        }

        log_message('debug', 'CheckoutController - Final checkout type: ' . $checkoutType . ', Items count: ' . count($checkoutItems));

        // Validate all checkout items
        $validatedItems = $this->validateCheckoutItems($checkoutItems);
        if (!$validatedItems['valid']) {
            return redirect()->to('/cart')
                           ->with('error', 'Có sản phẩm trong đơn hàng không hợp lệ: ' . implode(', ', $validatedItems['errors']));
        }

        // Calculate totals
        $subtotal = array_sum(array_map(function($item) {
            return $item['quantity'] * $item['price'];
        }, $checkoutItems));

        // Get shipping options and calculate shipping fee
        $shippingOptions = $this->getShippingOptions();
        $shippingFee = $this->calculateShippingFee($subtotal, $customer);
        
        // Apply coupon if any (simplified - no database integration yet)
        $appliedCoupon = $session->get('applied_coupon');
        $discount = 0;
        if ($appliedCoupon) {
            $discount = $appliedCoupon['discount'] ?? 0;
            if ($appliedCoupon['free_shipping'] ?? false) {
                $shippingFee = 0;
            }
        }

        // Calculate total (subtotal already includes discount, so total = subtotal + shipping)
        $total = $subtotal - $discount + $shippingFee;

        $data = [
            'title' => 'Checkout - Complete Your Order',
            'customer' => $customer,
            'checkoutType' => $checkoutType,
            'checkoutItems' => $checkoutItems,
            'orderSummary' => [
                'subtotal' => $subtotal,
                'shipping_fee' => $shippingFee,
                'discount' => $discount,
                'total' => $total,
                'total_items' => count($checkoutItems),
                'total_quantity' => array_sum(array_column($checkoutItems, 'quantity'))
            ],
            'shippingOptions' => $shippingOptions,
            'paymentMethods' => $this->getPaymentMethods(),
            'appliedCoupon' => $appliedCoupon,
            'defaultShipping' => $this->getDefaultShippingAddress($customer)
        ];

        return view('Customers/checkout-test', $data);
    }

    // FIXED: Updated clearCheckoutData method with correct priority
    private function clearCheckoutData($customerId)
    {
        $session = session();

        // Determine what to clear based on what was used for checkout
        $selectedItems = $session->get('checkout_selected_items');
        $buyNowMode = $session->get('buy_now_mode');

        log_message('debug', 'clearCheckoutData - Selected items: ' . ($selectedItems ? 'exists' : 'none') . 
                             ', Buy now mode: ' . ($buyNowMode ? 'exists' : 'none'));

        // Priority 1: If we had selected items, only clear those (don't clear entire cart)
        if ($selectedItems && !empty($selectedItems)) {
            log_message('debug', 'clearCheckoutData - Clearing selected items, keeping cart');
            $session->remove('checkout_selected_items');
            
            // Optionally remove the selected items from cart
            // Uncomment this if you want to remove purchased items from cart
            /*
            foreach ($selectedItems as $item) {
                $this->cartModel->removeFromCart($customerId, $item['product_id']);
            }
            */
        }
        // Priority 2: If we had buy now mode, clear it
        else if ($buyNowMode) {
            log_message('debug', 'clearCheckoutData - Clearing buy now mode');
            $session->remove('buy_now_mode');
            
            // For buy now, we typically don't want to clear the entire cart
            // since buy now is separate from cart items
        }
        // Priority 3: Clear entire cart (fallback case)
        else {
            log_message('debug', 'clearCheckoutData - Clearing entire cart');
            $this->cartModel->clearCart($customerId);
        }
        
        // Always clear coupon after successful order
        $session->remove('applied_coupon');
        
        log_message('debug', 'clearCheckoutData - Cleanup completed');
    }

    // Add new method to clear expired buy_now sessions
    public function clearExpiredBuyNow()
    {
        $session = session();
        $buyNowMode = $session->get('buy_now_mode');
        
        if ($buyNowMode && isset($buyNowMode['timestamp'])) {
            // Clear buy_now_mode if older than 30 minutes
            $thirtyMinutesAgo = time() - (30 * 60);
            if ($buyNowMode['timestamp'] < $thirtyMinutesAgo) {
                $session->remove('buy_now_mode');
                log_message('debug', 'Cleared expired buy_now_mode session');
                return true;
            }
        }
        
        return false;
    }

  public function processOrder()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(404);
        }

        $session = session();
        $customerId = $session->get('customer_id');

        if (!$customerId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Vui lòng đăng nhập để thanh toán'
            ]);
        }

        // Validate form data
        $validation = \Config\Services::validation();
        $validation->setRules([
            'payment_method' => 'required|in_list[cod,momo,bank_transfer]',
            'shipping_method' => 'required|in_list[standard,express,same_day]',
            'shipping_address' => 'required|min_length[10]',
            'shipping_phone' => 'required|min_length[10]|max_length[15]',
            'shipping_name' => 'required|min_length[2]|max_length[100]',
            'notes' => 'permit_empty|max_length[500]'
        ], [
            'payment_method' => [
                'required' => 'Vui lòng chọn phương thức thanh toán',
                'in_list' => 'Phương thức thanh toán không hợp lệ'
            ],
            'shipping_method' => [
                'required' => 'Vui lòng chọn phương thức giao hàng',
                'in_list' => 'Phương thức giao hàng không hợp lệ'
            ],
            'shipping_name' => [
                'required' => 'Vui lòng nhập họ tên người nhận',
                'min_length' => 'Họ tên phải có ít nhất 2 ký tự',
                'max_length' => 'Họ tên không được quá 100 ký tự'
            ],
            'shipping_phone' => [
                'required' => 'Vui lòng nhập số điện thoại',
                'min_length' => 'Số điện thoại phải có ít nhất 10 số',
                'max_length' => 'Số điện thoại không được quá 15 số'
            ],
            'shipping_address' => [
                'required' => 'Vui lòng nhập địa chỉ giao hàng',
                'min_length' => 'Địa chỉ phải có ít nhất 10 ký tự'
            ]
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Thông tin không hợp lệ',
                'errors' => $validation->getErrors()
            ]);
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Get checkout items
            $checkoutItems = $this->getCheckoutItemsForProcessing($customerId);
            
            if (empty($checkoutItems)) {
                $db->transRollback();
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Không có sản phẩm nào để thanh toán'
                ]);
            }

            log_message('debug', 'processOrder - Processing ' . count($checkoutItems) . ' items');

            // Validate items again
            $validatedItems = $this->validateCheckoutItems($checkoutItems);
            if (!$validatedItems['valid']) {
                $db->transRollback();
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Có sản phẩm không hợp lệ: ' . implode(', ', $validatedItems['errors'])
                ]);
            }

            // Get customer info
            $customer = $this->customerModel->find($customerId);

            // Calculate order totals
            $subtotal = array_sum(array_map(function($item) {
                return $item['quantity'] * $item['price'];
            }, $checkoutItems));

            $shippingMethod = $this->request->getPost('shipping_method');
            $shippingOptions = $this->getShippingOptions();
            $shippingFee = isset($shippingOptions[$shippingMethod]) 
                ? $shippingOptions[$shippingMethod]['price'] 
                : 30000;

            // Free shipping for orders over 500k
            if ($subtotal >= 500000) {
                $shippingFee = 0;
            }

            // ===== XỬ LÝ VOUCHER - BƯỚC 1: LẤY THÔNG TIN =====
            $appliedCoupon = $session->get('applied_coupon');
            $discountAmount = 0;
            $couponCode = null;
            
            if ($appliedCoupon) {
                $discountAmount = $appliedCoupon['discount'] ?? 0;
                $couponCode = $appliedCoupon['code'] ?? null;
                
                if ($appliedCoupon['free_shipping'] ?? false) {
                    $shippingFee = 0;
                }
                
                log_message('debug', "Applying coupon: {$couponCode}, Discount: {$discountAmount}");
            }
            // ===== HẾT BƯỚC 1 =====

            // Calculate final total
            $finalSubtotal = $subtotal - $discountAmount;
            $total = $finalSubtotal + $shippingFee;

            // Prepare shipping address JSON
            $shippingAddressData = [
                'name' => $this->request->getPost('shipping_name'),
                'phone' => $this->request->getPost('shipping_phone'),
                'address' => $this->request->getPost('shipping_address'),
                'ward' => '',
                'district' => '',
                'city' => '',
                'postal_code' => ''
            ];

            // ===== TẠO ORDER VỚI THÔNG TIN VOUCHER =====
            $orderData = [
                'customer_id' => $customerId,
                'order_number' => $this->generateOrderNumber(),
                'status' => 'pending',
                'payment_method' => $this->request->getPost('payment_method'),
                'payment_status' => 'pending',
                'subtotal' => $finalSubtotal,
                'shipping_fee' => $shippingFee,
                'total_amount' => $total,
                'coupon_code' => $couponCode,        // THÊM DÒNG NÀY
                'discount_amount' => $discountAmount, // THÊM DÒNG NÀY
                'shipping_address' => json_encode($shippingAddressData),
                'billing_address' => json_encode($shippingAddressData),
                'notes' => $this->request->getPost('notes'),
                'tracking_number' => null,
                'shipped_at' => null,
                'delivered_at' => null
            ];

            $orderId = $this->orderModel->insert($orderData);

            if (!$orderId) {
                $errors = $this->orderModel->errors();
                log_message('error', 'Order creation failed. Validation errors: ' . print_r($errors, true));
                
                $db->transRollback();
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Không thể tạo đơn hàng: ' . implode(', ', $errors)
                ]);
            }

            log_message('debug', 'Order created with ID: ' . $orderId);

            // Create order items
            foreach ($checkoutItems as $index => $item) {
                $orderItemData = [
                    'order_id' => $orderId,
                    'product_id' => $item['product_id'],
                    'product_name' => $item['name'],
                    'product_sku' => $item['sku'] ?? '',
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'total' => $item['quantity'] * $item['price']
                ];

                $insertResult = $this->orderItemModel->insert($orderItemData);
                if (!$insertResult) {
                    $itemErrors = $this->orderItemModel->errors();
                    log_message('error', 'Order item creation failed for item ' . $index . '. Validation errors: ' . print_r($itemErrors, true));
                    
                    $db->transRollback();
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Không thể tạo chi tiết đơn hàng cho sản phẩm: ' . $item['name'] . '. Lỗi: ' . implode(', ', $itemErrors)
                    ]);
                }

                // Update product stock
                $product = $this->productModel->find($item['product_id']);
                if ($product) {
                    $newStock = max(0, $product['stock_quantity'] - $item['quantity']);
                    $stockStatus = $this->determineStockStatus($newStock, $product['min_stock_level'] ?? 0);
                    
                    $this->productModel->update($item['product_id'], [
                        'stock_quantity' => $newStock,
                        'stock_status' => $stockStatus
                    ]);
                }
            }

            // ===== XỬ LÝ VOUCHER - BƯỚC 2: TĂNG USED_COUNT =====
            if ($couponCode) {
                $coupon = $this->discountCouponModel->where('code', $couponCode)->first();
                
                if ($coupon) {
                    // Tăng số lần sử dụng
                    $updateResult = $this->discountCouponModel->incrementUsage($coupon['id']);
                    
                    if ($updateResult) {
                        log_message('info', "Coupon '{$couponCode}' used_count incremented for order {$orderId}");
                    } else {
                        log_message('error', "Failed to increment used_count for coupon '{$couponCode}'");
                    }
                } else {
                    log_message('warning', "Coupon '{$couponCode}' not found in database when processing order {$orderId}");
                }
            }
            // ===== HẾT BƯỚC 2 =====

            $db->transComplete();

            if ($db->transStatus() === FALSE) {
                log_message('error', 'Transaction failed during order processing');
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Có lỗi xảy ra trong quá trình tạo đơn hàng'
                ]);
            }
            
            log_message('info', 'Order processed successfully. Order ID: ' . $orderId . ', Order Number: ' . $orderData['order_number']);

            // Clear cart/session data based on checkout type
            $this->clearCheckoutData($customerId);

            // Process payment if needed
            $paymentResult = $this->processPayment($orderId, $orderData);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Đặt hàng thành công!',
                'order_id' => $orderId,
                'order_number' => $orderData['order_number'],
                'total_amount' => $total,
                'payment_method' => $orderData['payment_method'],
                'payment_result' => $paymentResult,
                'discount_applied' => $discountAmount
            ]);

        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'Checkout process error: ' . $e->getMessage() . "\nStack trace: " . $e->getTraceAsString());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Có lỗi xảy ra trong quá trình đặt hàng: ' . $e->getMessage()
            ]);
        }
    }

    // FIXED: Updated getCheckoutItemsForProcessing with correct priority
    private function getCheckoutItemsForProcessing($customerId)
    {
        $session = session();
        $checkoutItems = [];

        // Priority 1: Selected items from cart (HIGHEST PRIORITY)
        $selectedItems = $session->get('checkout_selected_items');
        if ($selectedItems && !empty($selectedItems)) {
            log_message('debug', 'getCheckoutItemsForProcessing - Using selected items');
            return $selectedItems;
        }

        // Priority 2: Buy now mode (only if no selected items)
        $buyNowMode = $session->get('buy_now_mode');
        if ($buyNowMode && isset($buyNowMode['product_id'])) {
            log_message('debug', 'getCheckoutItemsForProcessing - Using buy now mode');
            $product = $this->productModel->find($buyNowMode['product_id']);
            if ($product) {
                $price = !empty($product['sale_price']) && $product['sale_price'] > 0 
                    ? $product['sale_price'] 
                    : $product['price'];
                
                return [[
                    'product_id' => $product['id'],
                    'name' => $product['name'],
                    'quantity' => $buyNowMode['quantity'],
                    'price' => $price,
                    'sku' => $product['sku'] ?? ''
                ]];
            }
        }

        // Priority 3: All cart items (fallback)
        log_message('debug', 'getCheckoutItemsForProcessing - Using all cart items');
        $cartItems = $this->cartModel->getCartWithProducts($customerId);
        foreach ($cartItems as $item) {
            $checkoutItems[] = [
                'product_id' => $item['product_id'],
                'name' => $item['name'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
                'sku' => $item['sku'] ?? ''
            ];
        }

        return $checkoutItems;
    }

    // Rest of the methods remain the same...
    private function validateCheckoutItems($items)
    {
        $errors = [];
        $validItems = [];

        foreach ($items as $item) {
            // Check if product_id exists
            if (!isset($item['product_id'])) {
                $errors[] = 'Thiếu ID sản phẩm';
                continue;
            }

            $product = $this->productModel->find($item['product_id']);
            
            if (!$product) {
                $errors[] = $item['name'] . ' không còn tồn tại';
                continue;
            }

            if (!$product['is_active']) {
                $errors[] = $item['name'] . ' đã ngừng kinh doanh';
                continue;
            }

            if ($product['stock_status'] === 'out_of_stock' || $product['stock_quantity'] < $item['quantity']) {
                $errors[] = $item['name'] . ' không đủ hàng (còn ' . $product['stock_quantity'] . ')';
                continue;
            }

            $validItems[] = $item;
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'items' => $validItems
        ];
    }

    private function generateOrderNumber()
    {
        $prefix = 'DH';
        $date = date('Ymd');
        $random = sprintf('%04d', mt_rand(1, 9999));
        return $prefix . $date . $random;
    }

    private function calculateShippingFee($subtotal, $customer = null)
    {
        // Free shipping for orders over 500k
        if ($subtotal >= 500000) {
            return 0;
        }

        return 30000; // Standard shipping fee
    }

    private function determineStockStatus($quantity, $minLevel = 0)
    {
        if ($quantity <= 0) {
            return 'out_of_stock';
        } elseif ($quantity <= $minLevel) {
            return 'low_stock';
        } else {
            return 'in_stock';
        }
    }
    //Xử lý thanh toán MoMo
     
   /**
 * Xử lý thanh toán MoMo - SỬA LẠI METHOD NÀY
 */
/**
 * Xử lý thanh toán MoMo - SỬA LẠI HOÀN TOÀN
 */
public function processMomoPayment($orderId, $orderData)
{
    try {
        $session = session();
        $customerId = $session->get('customer_id');
        
        // Tạo request ID duy nhất
        $requestId = time() . '_' . $customerId . '_' . $orderId;
        
        // Lấy config
        $config = $this->momoService->getConfig();
        
        log_message('debug', 'Momo Config: ' . json_encode($config));
        
        $momoData = [
            'request_id' => $requestId,
            'order_id' => $orderId . '_' . time(),
            'order_number' => $orderData['order_number'],
            'amount' => intval($orderData['total_amount']),
            'order_info' => 'Thanh toán đơn hàng ' . $orderData['order_number'],
            'return_url' => base_url('checkout/momo-callback'),
            'ipn_url' => base_url('checkout/momo-ipn'),
            'customer_id' => $customerId
        ];

        log_message('debug', 'Momo Payment Data: ' . json_encode($momoData));

        $paymentResult = $this->momoService->createPayment($momoData);

        log_message('debug', 'Momo Payment Result: ' . json_encode($paymentResult));

        if ($paymentResult['success']) {
            // Lưu thông tin thanh toán vào session
            $session->set('momo_payment_' . $orderId, [
                'request_id' => $requestId,
                'momo_order_id' => $momoData['order_id'],
                'order_id' => $orderId,
                'amount' => $momoData['amount'],
                'timestamp' => time()
            ]);

            return [
                'status' => 'redirect',
                'message' => 'Đang chuyển hướng đến MoMo...',
                'redirect_url' => $paymentResult['payment_url']
            ];
        } else {
            // NẾU MOMO TRẢ VỀ LỖI, KHÔNG ĐƯỢC COI LÀ THÀNH CÔNG
            // Cập nhật trạng thái đơn hàng thành failed
            $this->updateOrderPaymentStatus($orderId, 'failed', 'MoMo error: ' . $paymentResult['message']);
            
            return [
                'status' => 'failed', // QUAN TRỌNG: phải là 'failed'
                'message' => 'Không thể khởi tạo thanh toán MoMo: ' . $paymentResult['message']
            ];
        }

    } catch (\Exception $e) {
        log_message('error', 'Momo Payment Processing Error: ' . $e->getMessage());
        
        // Cập nhật trạng thái đơn hàng thành failed
        $this->updateOrderPaymentStatus($orderId, 'failed', 'MoMo exception: ' . $e->getMessage());
        
        return [
            'status' => 'failed',
            'message' => 'Lỗi xử lý thanh toán MoMo: ' . $e->getMessage()
        ];
    }
}

    /**
     * Callback từ MoMo sau khi thanh toán
     */
    public function momoCallback()
    {
        $request = $this->request;
        $session = session();
        
        $resultCode = $request->getGet('resultCode');
        $orderId = $request->getGet('orderId');
        $message = $request->getGet('message');

        log_message('debug', 'Momo Callback: ' . json_encode($request->getGet()));

        if (!$orderId) {
            $session->setFlashdata('error', 'Thông tin đơn hàng không hợp lệ');
            return redirect()->to('/checkout');
        }

        // Extract order ID từ orderId của MoMo (format: orderId_timestamp)
        $parts = explode('_', $orderId);
        $realOrderId = $parts[0];

        if ($resultCode == 0) {
            // Thanh toán thành công
            $this->updateOrderPaymentStatus($realOrderId, 'paid', 'Thanh toán MoMo thành công');
            
            // Clear cart data
            $customerId = $session->get('customer_id');
            $this->clearCheckoutData($customerId);
            
            $session->setFlashdata('success', 'Thanh toán MoMo thành công!');
            return redirect()->to('/checkout/success/' . $this->getOrderNumber($realOrderId));
        } else {
            // Thanh toán thất bại
            $errorMessage = $this->getMomoErrorMessage($resultCode);
            $this->updateOrderPaymentStatus($realOrderId, 'failed', $errorMessage);
            
            $session->setFlashdata('error', 'Thanh toán MoMo thất bại: ' . $errorMessage);
            return redirect()->to('/checkout?error=momo_failed&order_id=' . $realOrderId);
        }
    }

    /**
     * IPN (Instant Payment Notification) từ MoMo
     */
    public function momoIPN()
    {
        $request = $this->request;
        
        // Nhận dữ liệu JSON từ MoMo
        $json = $request->getJSON(true);
        if (!$json) {
            // Fallback: thử đọc raw input
            $rawInput = $request->getBody();
            $json = json_decode($rawInput, true);
        }

        log_message('debug', 'Momo IPN Received: ' . json_encode($json));

        if (!$json) {
            log_message('error', 'Momo IPN: Invalid JSON data');
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Invalid JSON']);
        }

        // Xác minh chữ ký
        if (!$this->momoService->verifyIPN($json)) {
            log_message('error', 'Momo IPN Signature Verification Failed');
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Invalid signature']);
        }

        if ($json['resultCode'] == 0) {
            // Extract order ID
            $parts = explode('_', $json['orderId']);
            $orderId = $parts[0];

            // Cập nhật trạng thái đơn hàng
            $this->updateOrderPaymentStatus($orderId, 'paid', 
                'IPN: Thanh toán MoMo thành công. TransId: ' . $json['transId']);

            log_message('info', 'Momo IPN: Order ' . $orderId . ' paid successfully');
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'IPN processed successfully'
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Payment not successful'
        ]);
    }

    /**
     * Kiểm tra trạng thái thanh toán MoMo
     */
    public function checkMomoStatus($orderId)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(404);
        }

        $session = session();
        $paymentInfo = $session->get('momo_payment_' . $orderId);

        if (!$paymentInfo) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Không tìm thấy thông tin thanh toán'
            ]);
        }

        $statusResult = $this->momoService->checkTransactionStatus(
            $paymentInfo['momo_order_id'],
            $paymentInfo['request_id']
        );

        if ($statusResult && $statusResult['resultCode'] == 0) {
            // Thanh toán thành công
            $this->updateOrderPaymentStatus($orderId, 'paid', 
                'Thanh toán MoMo thành công. TransId: ' . $statusResult['transId']);

            $session->remove('momo_payment_' . $orderId);

            return $this->response->setJSON([
                'success' => true,
                'paid' => true,
                'message' => 'Thanh toán thành công'
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'paid' => false,
            'message' => 'Chưa thanh toán'
        ]);
    }
 private function updateOrderPaymentStatus($orderId, $status, $notes = '')
    {
        $updateData = ['payment_status' => $status];
        
        if ($status === 'paid') {
            $updateData['paid_at'] = date('Y-m-d H:i:s');
        }
        
        if ($notes) {
            // Thêm ghi chú vào trường notes
            $order = $this->orderModel->find($orderId);
            $currentNotes = $order['notes'] ?? '';
            $updateData['notes'] = $currentNotes . "\n[Payment] " . $notes;
        }

        return $this->orderModel->update($orderId, $updateData);
    }
private function getOrderNumber($orderId)
    {
        $order = $this->orderModel->find($orderId);
        return $order ? $order['order_number'] : null;
    }

    /**
     * Lấy thông báo lỗi từ MoMo
     */
    private function getMomoErrorMessage($resultCode)
    {
        $errors = [
            '0' => 'Thành công',
            '1001' => 'Merchant không tồn tại',
            '1002' => 'Dữ liệu không hợp lệ',
            '1003' => 'Không đủ số dư',
            '1004' => 'Giao dịch bị từ chối',
            '1005' => 'Lỗi hệ thống',
            '1006' => 'Giao dịch đang được xử lý',
            '1007' => 'Giao dịch bị hủy',
            '1008' => 'Số tiền không hợp lệ',
            '1009' => 'Mã đơn hàng đã tồn tại',
            '1010' => 'Xác thực thất bại',
            '1011' => 'Phiên bản không hợp lệ',
            '1012' => 'Hạn mức vượt quá quy định',
            '1013' => 'Giao dịch bị khóa',
            '1014' => 'Số lần thử vượt quá giới hạn',
            '1015' => 'Token không hợp lệ',
            '1016' => 'Đối tác không được hỗ trợ',
            '1017' => 'Chữ ký không hợp lệ',
            '1018' => 'Người dùng hủy giao dịch',
            '1019' => 'Thời gian thanh toán đã hết',
            '1020' => 'Giao dịch không tồn tại'
        ];

        return $errors[$resultCode] ?? 'Lỗi không xác định (' . $resultCode . ')';
    }
    private function processPayment($orderId, $orderData)
    {
        $paymentMethod = $orderData['payment_method'];
        
        switch ($paymentMethod) {
            case 'cod':
                // Cash on delivery - no additional processing needed
                return [
                    'status' => 'success',
                    'message' => 'Đơn hàng đã được tạo thành công. Bạn sẽ thanh toán khi nhận hàng.'
                ];
                
            case 'momo':
                // MoMo payment - currently disabled
               $momoResult = $this->processMomoPayment($orderId, $orderData);
            
            // QUAN TRỌNG: Nếu MoMo trả về failed, không được clear cart
            if ($momoResult['status'] === 'failed') {
                // KHÔNG clear cart ở đây, để người dùng thử lại
                log_message('debug', 'MoMo payment failed, keeping cart data');
            }
            
            return $momoResult;
                
            case 'bank_transfer':
                // Bank transfer
                return [
                    'status' => 'pending',
                    'message' => 'Vui lòng chuyển khoản theo thông tin đã cung cấp',
                    'bank_info' => [
                        'bank_name' => 'Ngân hàng ABC',
                        'account_number' => '1234567890',
                        'account_name' => 'CONG TY XYZ'
                    ]
                ];
                
            default:
                return [
                    'status' => 'failed',
                    'message' => 'Phương thức thanh toán không hỗ trợ'
                ];
        }
    }

    private function getShippingOptions()
    {
        return [
            'standard' => [
                'name' => 'Standard Delivery',
                'price' => 30000,
                'time' => '3-5 ngày làm việc',
                'description' => 'Giao hàng tiêu chuẩn'
            ],
            'express' => [
                'name' => 'Express Delivery',
                'price' => 50000,
                'time' => '1-2 ngày làm việc',
                'description' => 'Giao hàng nhanh'
            ],
            'same_day' => [
                'name' => 'Same Day Delivery',
                'price' => 80000,
                'time' => 'Trong ngày',
                'description' => 'Giao hàng trong ngày (chỉ TP.HCM, HN, DN)'
            ]
        ];
    }

    private function getPaymentMethods()
    {
        return [
            'cod' => [
                'name' => 'Thanh toán khi nhận hàng (COD)',
                'description' => 'Thanh toán bằng tiền mặt khi nhận được hàng',
                'icon' => 'ti-money',
                'available' => true
            ],
            'momo' => [
                'name' => 'Ví MoMo',
                'description' => 'Thanh toán qua ví điện tử MoMo',
                'icon' => 'ti-mobile',
                'available' => true // Tạm thời tắt
            ],
            'bank_transfer' => [
                'name' => 'Chuyển khoản ngân hàng',
                'description' => 'Chuyển khoản qua ngân hàng',
                'icon' => 'ti-credit-card',
                'available' => true
            ]
        ];
    }

    private function getDefaultShippingAddress($customer)
    {
        return [
            'name' => $customer['name'] ?? '',
            'phone' => $customer['phone'] ?? '',
            'address' => $customer['address'] ?? ''
        ];
    }

    public function orderSuccess($orderNumber = null)
    {
        $session = session();
        $customerId = $session->get('customer_id');

        if (!$customerId || !$orderNumber) {
            return redirect()->to('/')->with('error', 'Đơn hàng không tồn tại');
        }

        $order = $this->orderModel->getOrderByNumber($orderNumber, $customerId);
        
        if (!$order) {
            return redirect()->to('/')->with('error', 'Đơn hàng không tồn tại');
        }

        $orderItems = $this->orderItemModel->getOrderItems($order['id']);

        $data = [
            'title' => 'Đặt hàng thành công - ' . $orderNumber,
            'order' => $order,
            'orderItems' => $orderItems
        ];

        return view('Customers/order-success', $data);
    }
}