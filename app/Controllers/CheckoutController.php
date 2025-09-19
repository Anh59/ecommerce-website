<?php

namespace App\Controllers;

use App\Models\CartModel;
use App\Models\ProductModel;
use App\Models\CustomerModel;
use App\Models\OrderModel;
use App\Models\OrderItemModel;

class CheckoutController extends BaseController
{
    protected $cartModel;
    protected $productModel;
    protected $customerModel;
    protected $orderModel;
    protected $orderItemModel;
    
    public function __construct()
    {
        $this->cartModel = new CartModel();
        $this->productModel = new ProductModel();
        $this->customerModel = new CustomerModel();
        $this->orderModel = new OrderModel();
        $this->orderItemModel = new OrderItemModel();
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

        // Determine checkout type and get items
        $checkoutType = 'cart'; // Default
        $checkoutItems = [];

        // Check for buy now mode
        $buyNowMode = $session->get('buy_now_mode');
        if ($buyNowMode && isset($buyNowMode['product_id'])) {
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
                    'slug' => $product['slug']
                ]];
            }
        }
        
        // Check for selected items from cart
        $selectedItems = $session->get('checkout_selected_items');
        if ($selectedItems && isset($selectedItems['items'])) {
            $checkoutType = 'selected';
            $checkoutItems = $selectedItems['items'];
        }
        
        // Fallback to all cart items
        if (empty($checkoutItems)) {
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
                    'brand_name' => $item['brand_name'] ?? ''
                ];
            }
        }

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
                : 30000; // Default standard shipping

            // Free shipping for orders over 500k
            if ($subtotal >= 500000) {
                $shippingFee = 0;
            }

            // Apply coupon discount (simplified)
            $appliedCoupon = $session->get('applied_coupon');
            $discountAmount = 0;
            if ($appliedCoupon) {
                $discountAmount = $appliedCoupon['discount'] ?? 0;
                if ($appliedCoupon['free_shipping'] ?? false) {
                    $shippingFee = 0;
                }
            }

            // Calculate final total: subtotal - discount + shipping
            $finalSubtotal = $subtotal - $discountAmount;
            $total = $finalSubtotal + $shippingFee;

            // Prepare shipping address JSON
            $shippingAddressData = [
                'name' => $this->request->getPost('shipping_name'),
                'phone' => $this->request->getPost('shipping_phone'),
                'address' => $this->request->getPost('shipping_address'),
                'ward' => '', // Để trống vì không có trong form
                'district' => '', // Để trống vì không có trong form  
                'city' => '', // Để trống vì không có trong form
                'postal_code' => '' // Để trống vì không có trong form
            ];

            // Create order - CHỈ SỬ DỤNG CÁC TRƯỜNG CÓ TRONG DATABASE
            $orderData = [
                'customer_id' => $customerId,
                'order_number' => $this->generateOrderNumber(),
                'status' => 'pending',
                'payment_method' => $this->request->getPost('payment_method'),
                'payment_status' => 'pending',
                'subtotal' => $finalSubtotal, // Subtotal sau khi đã trừ discount
                'shipping_fee' => $shippingFee,
                'total_amount' => $total,
                'shipping_address' => json_encode($shippingAddressData),
                'billing_address' => json_encode($shippingAddressData), // Same as shipping
                'notes' => $this->request->getPost('notes'),
                'tracking_number' => null,
                'shipped_at' => null,
                'delivered_at' => null
            ];

            // Debug: Log order data
            log_message('debug', 'Order data: ' . print_r($orderData, true));

            $orderId = $this->orderModel->insert($orderData);

            if (!$orderId) {
                // Get more detailed error from model
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

                // Debug: Log order item data
                log_message('debug', 'Order item ' . $index . ': ' . print_r($orderItemData, true));

                $insertResult = $this->orderItemModel->insert($orderItemData);
                if (!$insertResult) {
                    // Get detailed error from model
                    $itemErrors = $this->orderItemModel->errors();
                    log_message('error', 'Order item creation failed for item ' . $index . '. Validation errors: ' . print_r($itemErrors, true));
                    log_message('error', 'Failed order item data: ' . print_r($orderItemData, true));
                    
                    $db->transRollback();
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Không thể tạo chi tiết đơn hàng cho sản phẩm: ' . $item['name'] . '. Lỗi: ' . implode(', ', $itemErrors)
                    ]);
                }

                log_message('debug', 'Order item created successfully for product: ' . $item['name']);

                // Update product stock
                $product = $this->productModel->find($item['product_id']);
                if ($product) {
                    $newStock = max(0, $product['stock_quantity'] - $item['quantity']);
                    $stockStatus = $this->determineStockStatus($newStock, $product['min_stock_level'] ?? 0);
                    
                    $updateResult = $this->productModel->update($item['product_id'], [
                        'stock_quantity' => $newStock,
                        'stock_status' => $stockStatus
                    ]);
                    
                    if (!$updateResult) {
                        log_message('warning', 'Failed to update stock for product ID: ' . $item['product_id']);
                    }
                }
            }

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

    private function getCheckoutItemsForProcessing($customerId)
    {
        $session = session();
        $checkoutItems = [];

        // Priority 1: Buy now mode
        $buyNowMode = $session->get('buy_now_mode');
        if ($buyNowMode && isset($buyNowMode['product_id'])) {
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

        // Priority 2: Selected items
        $selectedItems = $session->get('checkout_selected_items');
        if ($selectedItems && isset($selectedItems['items'])) {
            return $selectedItems['items'];
        }

        // Priority 3: All cart items
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

    private function clearCheckoutData($customerId)
    {
        $session = session();

        // Clear buy now mode
        if ($session->get('buy_now_mode')) {
            $session->remove('buy_now_mode');
        }

        // Clear selected items
        if ($session->get('checkout_selected_items')) {
            $session->remove('checkout_selected_items');
            return; // Don't clear cart for selected items checkout
        }

        // Clear entire cart for normal checkout
        $this->cartModel->clearCart($customerId);
        
        // Clear coupon
        $session->remove('applied_coupon');
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
                return [
                    'status' => 'pending',
                    'message' => 'Chức năng thanh toán MoMo đang được phát triển'
                ];
                
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
                'available' => false // Tạm thời tắt
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