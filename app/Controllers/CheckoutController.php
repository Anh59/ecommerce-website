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

        // Xác định loại checkout với độ ưu tiên đúng
        $checkoutType = 'cart';
        $checkoutItems = [];

        // Ưu tiên 1: Items được chọn từ giỏ hàng
        $selectedItems = $session->get('checkout_selected_items');
        if ($selectedItems && !empty($selectedItems)) {
            $checkoutType = 'selected';
            $checkoutItems = $selectedItems;
            
            // Clear buy_now_mode nếu tồn tại để tránh xung đột
            if ($session->get('buy_now_mode')) {
                $session->remove('buy_now_mode');
            }
        }
        // Ưu tiên 2: Chế độ mua ngay
        else {
            $buyNowMode = $session->get('buy_now_mode');
            if ($buyNowMode && isset($buyNowMode['product_id'])) {
                $checkoutType = 'buy_now';
                
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
        
        // Ưu tiên 3: Tất cả items trong giỏ hàng
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
                    'brand_name' => $item['brand_name'] ?? '',
                    'sku' => $item['sku'] ?? ''
                ];
            }
        }

        // Validate items
        $validatedItems = $this->validateCheckoutItems($checkoutItems);
        if (!$validatedItems['valid']) {
            return redirect()->to('/cart')
                           ->with('error', 'Có sản phẩm trong đơn hàng không hợp lệ: ' . implode(', ', $validatedItems['errors']));
        }

        // Tính toán tổng tiền
        $subtotal = array_sum(array_map(function($item) {
            return $item['quantity'] * $item['price'];
        }, $checkoutItems));

        // Lấy options vận chuyển và tính phí
        $shippingOptions = $this->getShippingOptions();
        $shippingFee = $this->calculateShippingFee($subtotal, $customer);
        
        // Áp dụng coupon nếu có
        $appliedCoupon = $session->get('applied_coupon');
        $discount = 0;
        if ($appliedCoupon) {
            $discount = $appliedCoupon['discount'] ?? 0;
            if ($appliedCoupon['free_shipping'] ?? false) {
                $shippingFee = 0;
            }
        }

        // Tính tổng cộng
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
            // Lấy items để xử lý
            $checkoutItems = $this->getCheckoutItemsForProcessing($customerId);
            
            if (empty($checkoutItems)) {
                $db->transRollback();
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Không có sản phẩm nào để thanh toán'
                ]);
            }

            // Validate items lần nữa (chỉ kiểm tra, không trừ kho)
            $validatedItems = $this->validateCheckoutItems($checkoutItems);
            if (!$validatedItems['valid']) {
                $db->transRollback();
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Có sản phẩm không hợp lệ: ' . implode(', ', $validatedItems['errors'])
                ]);
            }

            // Tính toán tổng tiền
            $subtotal = array_sum(array_map(function($item) {
                return $item['quantity'] * $item['price'];
            }, $checkoutItems));

            $shippingMethod = $this->request->getPost('shipping_method');
            $shippingOptions = $this->getShippingOptions();
            $shippingFee = isset($shippingOptions[$shippingMethod]) 
                ? $shippingOptions[$shippingMethod]['price'] 
                : 30000;

            // Miễn phí vận chuyển cho đơn hàng trên 500k
            if ($subtotal >= 500000) {
                $shippingFee = 0;
            }

            // Xử lý voucher
            $appliedCoupon = $session->get('applied_coupon');
            $discountAmount = 0;
            $couponCode = null;
            
            if ($appliedCoupon) {
                $discountAmount = $appliedCoupon['discount'] ?? 0;
                $couponCode = $appliedCoupon['code'] ?? null;
                
                if ($appliedCoupon['free_shipping'] ?? false) {
                    $shippingFee = 0;
                }
            }

            // Tính tổng cộng cuối cùng
            $finalSubtotal = $subtotal - $discountAmount;
            $total = $finalSubtotal + $shippingFee;

            // Chuẩn bị địa chỉ giao hàng
            $shippingAddressData = [
                'name' => $this->request->getPost('shipping_name'),
                'phone' => $this->request->getPost('shipping_phone'),
                'address' => $this->request->getPost('shipping_address'),
                'ward' => '',
                'district' => '',
                'city' => '',
                'postal_code' => ''
            ];

            $paymentMethod = $this->request->getPost('payment_method');
            
            // Xác định trạng thái thanh toán ban đầu
            $initialPaymentStatus = 'pending';
            
            // Tạo order - KHÔNG trừ kho ở bước này
            $orderData = [
                'customer_id' => $customerId,
                'order_number' => $this->generateOrderNumber(),
                'status' => 'pending', // Đơn hàng mới, chờ xác nhận
                'payment_method' => $paymentMethod,
                'payment_status' => $initialPaymentStatus,
                'subtotal' => $finalSubtotal,
                'shipping_fee' => $shippingFee,
                'total_amount' => $total,
                'coupon_code' => $couponCode,
                'discount_amount' => $discountAmount,
                'shipping_address' => json_encode($shippingAddressData),
                'billing_address' => json_encode($shippingAddressData),
                'notes' => $this->request->getPost('notes'),
                'shipping_method' => $shippingMethod,
                'tracking_number' => null,
                'shipped_at' => null,
                'delivered_at' => null,
                'paid_at' => null // Chưa thanh toán
            ];

            $orderId = $this->orderModel->insert($orderData);

            if (!$orderId) {
                $errors = $this->orderModel->errors();
                $db->transRollback();
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Không thể tạo đơn hàng: ' . implode(', ', $errors)
                ]);
            }

            // Tạo order items - CHỈ lưu thông tin, không trừ kho
            foreach ($checkoutItems as $item) {
                $orderItemData = [
                    'order_id' => $orderId,
                    'product_id' => $item['product_id'],
                    'product_name' => $item['name'],
                    'product_sku' => $item['sku'] ?? '',
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'total' => $item['quantity'] * $item['price']
                ];

                $this->orderItemModel->insert($orderItemData);
                
                // QUAN TRỌNG: KHÔNG trừ kho ở đây - sẽ trừ khi admin xác nhận đơn hàng
            }

            // Tăng số lần sử dụng voucher
            if ($couponCode) {
                $coupon = $this->discountCouponModel->where('code', $couponCode)->first();
                if ($coupon) {
                    $this->discountCouponModel->incrementUsage($coupon['id']);
                }
            }

            $db->transComplete();

            if ($db->transStatus() === FALSE) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Có lỗi xảy ra trong quá trình tạo đơn hàng'
                ]);
            }

            // Clear dữ liệu checkout (giỏ hàng, session)
            $this->clearCheckoutData($customerId);

            // Xử lý thanh toán nếu là MoMo
            $paymentResult = $this->processPayment($orderId, $orderData);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Đặt hàng thành công! Đơn hàng đang chờ xác nhận.',
                'order_id' => $orderId,
                'order_number' => $orderData['order_number'],
                'total_amount' => $total,
                'payment_method' => $orderData['payment_method'],
                'payment_result' => $paymentResult,
                'discount_applied' => $discountAmount
            ]);

        } catch (\Exception $e) {
            $db->transRollback();
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Có lỗi xảy ra trong quá trình đặt hàng: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Xử lý thanh toán
     */
    private function processPayment($orderId, $orderData)
    {
        $paymentMethod = $orderData['payment_method'];
        
        switch ($paymentMethod) {
            case 'cod':
                return [
                    'status' => 'success',
                    'message' => 'Đơn hàng đã được tạo thành công. Bạn sẽ thanh toán khi nhận hàng.'
                ];
                
            case 'momo':
                $momoResult = $this->processMomoPayment($orderId, $orderData);
                return $momoResult;
                
            case 'bank_transfer':
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

    /**
     * Xử lý thanh toán MoMo
     */
    public function processMomoPayment($orderId, $orderData)
    {
        try {
            $session = session();
            $customerId = $session->get('customer_id');
            
            $requestId = time() . '_' . $customerId . '_' . $orderId;
            
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

            $paymentResult = $this->momoService->createPayment($momoData);

            if ($paymentResult['success']) {
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
                return [
                    'status' => 'failed',
                    'message' => 'Không thể khởi tạo thanh toán MoMo: ' . $paymentResult['message']
                ];
            }

        } catch (\Exception $e) {
            return [
                'status' => 'failed',
                'message' => 'Lỗi xử lý thanh toán MoMo: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Callback từ MoMo
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
            // QUAN TRỌNG: Thanh toán MoMo thành công - CẬP NHẬT payment_status thành 'paid'
            // NHƯNG vẫn giữ order_status là 'pending' (chờ admin xác nhận)
            // VÀ KHÔNG trừ kho ở đây
            $this->updateOrderPaymentStatus($realOrderId, 'paid', 'Thanh toán MoMo thành công');
            
            // Clear cart data
            $customerId = $session->get('customer_id');
            $this->clearCheckoutData($customerId);
            
            $session->setFlashdata('success', 'Thanh toán MoMo thành công! Đơn hàng đang chờ xác nhận.');
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

            // QUAN TRỌNG: IPN - Cập nhật payment_status thành 'paid'
            // NHƯNG KHÔNG trừ kho, vẫn giữ order_status là 'pending'
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

    /**
     * Cập nhật trạng thái thanh toán đơn hàng
     */
    private function updateOrderPaymentStatus($orderId, $status, $notes = '')
    {
        $updateData = ['payment_status' => $status];
        
        if ($status === 'paid') {
            $updateData['paid_at'] = date('Y-m-d H:i:s');
            // QUAN TRỌNG: Khi thanh toán thành công, TIỀN ĐÃ VỀ TÀI KHOẢN
            // Nhưng vẫn KHÔNG trừ kho ở đây
        }
        
        if ($notes) {
            // Thêm ghi chú vào trường notes
            $order = $this->orderModel->find($orderId);
            $currentNotes = $order['notes'] ?? '';
            $updateData['notes'] = $currentNotes . "\n[Payment] " . $notes;
        }

        return $this->orderModel->update($orderId, $updateData);
    }

    /**
     * Lấy checkout items để xử lý với độ ưu tiên đúng
     */
    private function getCheckoutItemsForProcessing($customerId)
    {
        $session = session();
        $checkoutItems = [];

        // Ưu tiên 1: Items được chọn từ giỏ hàng
        $selectedItems = $session->get('checkout_selected_items');
        if ($selectedItems && !empty($selectedItems)) {
            return $selectedItems;
        }

        // Ưu tiên 2: Chế độ mua ngay
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

        // Ưu tiên 3: Tất cả items trong giỏ hàng
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

    /**
     * Validate checkout items - CHỈ kiểm tra, không trừ kho
     */
    private function validateCheckoutItems($items)
    {
        $errors = [];
        $validItems = [];

        foreach ($items as $item) {
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

            // QUAN TRỌNG: Chỉ kiểm tra tồn kho, không trừ
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

    /**
     * Clear checkout data sau khi đặt hàng thành công
     */
    private function clearCheckoutData($customerId)
    {
        $session = session();

        $selectedItems = $session->get('checkout_selected_items');
        $buyNowMode = $session->get('buy_now_mode');

        // Ưu tiên 1: Xóa selected items
        if ($selectedItems && !empty($selectedItems)) {
            $session->remove('checkout_selected_items');
        }
        // Ưu tiên 2: Xóa buy now mode
        else if ($buyNowMode) {
            $session->remove('buy_now_mode');
        }
        // Ưu tiên 3: Xóa toàn bộ giỏ hàng
        else {
            $this->cartModel->clearCart($customerId);
        }
        
        // Luôn xóa coupon sau khi đặt hàng thành công
        $session->remove('applied_coupon');
    }

    /**
     * Helper methods
     */
    private function generateOrderNumber()
    {
        $prefix = 'DH';
        $date = date('Ymd');
        $random = sprintf('%04d', mt_rand(1, 9999));
        return $prefix . $date . $random;
    }

    private function calculateShippingFee($subtotal, $customer = null)
    {
        if ($subtotal >= 500000) {
            return 0;
        }
        return 30000;
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

    private function getOrderNumber($orderId)
    {
        $order = $this->orderModel->find($orderId);
        return $order ? $order['order_number'] : null;
    }

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

    private function getShippingOptions()
    {
        return [
            'standard' => [
                'name' => 'Giao hàng tiêu chuẩn',
                'price' => 30000,
                'time' => '3-5 ngày làm việc',
                'description' => 'Giao hàng tiêu chuẩn'
            ],
            'express' => [
                'name' => 'Giao hàng nhanh',
                'price' => 50000,
                'time' => '1-2 ngày làm việc',
                'description' => 'Giao hàng nhanh'
            ],
            'same_day' => [
                'name' => 'Giao trong ngày',
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
                'available' => true
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

    /**
     * Clear expired buy_now sessions
     */
    public function clearExpiredBuyNow()
    {
        $session = session();
        $buyNowMode = $session->get('buy_now_mode');
        
        if ($buyNowMode && isset($buyNowMode['timestamp'])) {
            $thirtyMinutesAgo = time() - (30 * 60);
            if ($buyNowMode['timestamp'] < $thirtyMinutesAgo) {
                $session->remove('buy_now_mode');
                return true;
            }
        }
        
        return false;
    }
}