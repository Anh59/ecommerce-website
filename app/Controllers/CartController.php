<?php

namespace App\Controllers;

use App\Models\CartModel;
use App\Models\ProductModel;

class CartController extends BaseController
{
    protected $cartModel;
    protected $productModel;
    
    public function __construct()
    {
        $this->cartModel = new CartModel();
        $this->productModel = new ProductModel();
    }

    public function getCartCount()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(404);
        }

        $session = session();
        $customerId = $session->get('customer_id');

        if (!$customerId) {
            return $this->response->setJSON(['cart_count' => 0]);
        }

        $cartCount = $this->cartModel->getCartTotalQuantity($customerId);

        return $this->response->setJSON([
            'success' => true,
            'cart_count' => $cartCount
        ]);
    }

    public function updateQuantity()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(404);
        }

        $session = session();
        $customerId = $session->get('customer_id');

        if (!$customerId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Vui lòng đăng nhập'
            ]);
        }

        $productId = $this->request->getPost('product_id');
        $quantity = (int)$this->request->getPost('quantity');

        if (!$productId || $quantity < 0) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Thông tin không hợp lệ'
            ]);
        }

        // If quantity is 0, remove item
        if ($quantity === 0) {
            return $this->remove();
        }

        // Check product availability
        $product = $this->productModel->find($productId);
        if (!$product || !$product['is_active']) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Sản phẩm không tồn tại'
            ]);
        }

        if ($product['stock_quantity'] < $quantity) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Không đủ hàng trong kho. Chỉ còn ' . $product['stock_quantity'] . ' sản phẩm'
            ]);
        }

        $result = $this->cartModel->updateQuantity($customerId, $productId, $quantity);

        if ($result) {
            $cartItems = $this->cartModel->getCartWithProducts($customerId);
            $cartTotals = $this->cartModel->getCartTotals($customerId);

            // Format items for summary (limit to essential info)
            $summaryItems = [];
            foreach ($cartItems as $item) {
                $summaryItems[] = [
                    'id' => $item['id'],
                    'product_id' => $item['product_id'],
                    'name' => $item['name'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'total' => $item['price'] * $item['quantity'],
                    'image' => $item['main_image']
                ];
            }

            return $this->response->setJSON([
                'success' => true,
                'cart_count' => $cartTotals['total_items'],
                'subtotal' => $cartTotals['subtotal'],
                'total' => $cartTotals['total'],
                'shipping_fee' => $cartTotals['shipping_fee'],
                'items' => $summaryItems
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Không thể cập nhật số lượng'
            ]);
        }
    }

    public function applyPromoCode()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(404);
        }

        $session = session();
        $customerId = $session->get('customer_id');

        if (!$customerId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Vui lòng đăng nhập'
            ]);
        }

        $promoCode = $this->request->getPost('promo_code');
        
        if (!$promoCode) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Vui lòng nhập mã khuyến mãi'
            ]);
        }

        // TODO: Implement promo code logic
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Mã khuyến mãi không hợp lệ hoặc đã hết hạn'
        ]);
    }

    public function estimateShipping()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(404);
        }

        $session = session();
        $customerId = $session->get('customer_id');

        if (!$customerId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Vui lòng đăng nhập'
            ]);
        }

        $address = [
            'city' => $this->request->getPost('city'),
            'district' => $this->request->getPost('district'),
            'ward' => $this->request->getPost('ward')
        ];

        if (!$address['city']) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Vui lòng chọn tỉnh/thành phố'
            ]);
        }

        $cartTotals = $this->cartModel->getCartTotals($customerId);
        
        $shippingFee = $this->calculateShippingByAddress($address, $cartTotals);

        return $this->response->setJSON([
            'success' => true,
            'shipping_fee' => $shippingFee,
            'total' => $cartTotals['subtotal'] + $shippingFee,
            'estimated_delivery' => $this->estimateDeliveryTime($address)
        ]);
    }

    private function calculateShippingByAddress($address, $cartTotals)
    {
        $baseShipping = 30000;
        
        if ($cartTotals['subtotal'] >= 500000) {
            return 0;
        }

        $remoteCities = ['An Giang', 'Cà Mau', 'Kiên Giang', 'Bạc Liêu'];
        if (in_array($address['city'], $remoteCities)) {
            $baseShipping += 20000;
        }

        return $baseShipping;
    }

    private function estimateDeliveryTime($address)
    {
        $fastDeliveryCities = ['Hồ Chí Minh', 'Hà Nội', 'Đà Nẵng'];
        
        if (in_array($address['city'], $fastDeliveryCities)) {
            return '1-2 ngày làm việc';
        } else {
            return '3-5 ngày làm việc';
        }
    }

    public function index()
    {
        $session = session();
        $customerId = $session->get('customer_id');

        $data = [
            'cartItems' => [],
            'cartTotals' => [
                'subtotal' => 0,
                'shipping_fee' => 0,
                'total' => 0,
                'total_items' => 0,
                'total_weight' => 0
            ],
            'cartIssues' => [],
            'shippingOptions' => $this->getShippingOptions(),
            'provinces' => $this->getProvinces()
        ];

        if ($customerId) {
            $data['cartItems'] = $this->cartModel->getCartWithProducts($customerId);
            $data['cartTotals'] = $this->cartModel->getCartTotals($customerId);
            $data['cartIssues'] = $this->cartModel->validateCart($customerId);
        }

        return view('Customers/cart', $data);
    }

    public function update()
    {
        $session = session();
        $customerId = $session->get('customer_id');

        if (!$customerId) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Vui lòng đăng nhập'
                ]);
            }
            return redirect()->to('/api_Customers/customers_sign')->with('error', 'Vui lòng đăng nhập');
        }

        $updates = $this->request->getPost('updates');
        
        if (!$updates || !is_array($updates)) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Dữ liệu cập nhật không hợp lệ'
                ]);
            }
            return redirect()->back()->with('error', 'Dữ liệu cập nhật không hợp lệ');
        }

        $updated = 0;
        $errors = [];

        foreach ($updates as $productId => $quantity) {
            $quantity = (int)$quantity;
            $productId = (int)$productId;

            if ($quantity < 0) continue;

            if ($quantity == 0) {
                if ($this->cartModel->removeFromCart($customerId, $productId)) {
                    $updated++;
                }
            } else {
                $product = $this->productModel->find($productId);
                if (!$product || !$product['is_active']) {
                    $errors[] = "Sản phẩm ID {$productId} không tồn tại";
                    continue;
                }

                if ($product['stock_quantity'] < $quantity) {
                    $errors[] = "Sản phẩm {$product['name']} chỉ còn {$product['stock_quantity']} trong kho";
                    continue;
                }

                if ($this->cartModel->updateQuantity($customerId, $productId, $quantity)) {
                    $updated++;
                } else {
                    $errors[] = "Không thể cập nhật sản phẩm {$product['name']}";
                }
            }
        }

        if ($this->request->isAJAX()) {
            $cartTotals = $this->cartModel->getCartTotals($customerId);
            $cartItems = $this->cartModel->getCartWithProducts($customerId);
            
            return $this->response->setJSON([
                'success' => $updated > 0,
                'message' => $updated > 0 ? "Đã cập nhật {$updated} sản phẩm" : 'Không có sản phẩm nào được cập nhật',
                'errors' => $errors,
                'cart_totals' => $cartTotals,
                'cart_items' => $cartItems,
                'updated_count' => $updated
            ]);
        }

        $message = $updated > 0 ? "Đã cập nhật {$updated} sản phẩm" : 'Không có sản phẩm nào được cập nhật';
        if (!empty($errors)) {
            $message .= '. Có một số lỗi: ' . implode(', ', $errors);
        }

        return redirect()->back()->with('success', $message);
    }

    private function getShippingOptions()
    {
        return [
            'standard' => [
                'name' => 'Standard Shipping',
                'price' => 30000,
                'time' => '3-5 ngày làm việc'
            ],
            'express' => [
                'name' => 'Express Shipping', 
                'price' => 50000,
                'time' => '1-2 ngày làm việc'
            ],
            'same_day' => [
                'name' => 'Same Day Delivery',
                'price' => 80000,
                'time' => 'Trong ngày'
            ],
            'free' => [
                'name' => 'Free Shipping (Đơn hàng trên 500k)',
                'price' => 0,
                'time' => '3-7 ngày làm việc'
            ]
        ];
    }

    private function getProvinces()
    {
        return [
            'HCM' => 'TP. Hồ Chí Minh',
            'HN' => 'Hà Nội', 
            'DN' => 'Đà Nẵng',
            'CT' => 'Cần Thơ',
            'HP' => 'Hải Phòng',
            'BD' => 'Bình Dương',
            'DNA' => 'Đồng Nai'
        ];
    }

    public function applyCoupon()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(404);
        }

        $session = session();
        $customerId = $session->get('customer_id');

        if (!$customerId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Vui lòng đăng nhập'
            ]);
        }

        $couponCode = trim($this->request->getPost('coupon_code'));

        if (!$couponCode) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Vui lòng nhập mã giảm giá'
            ]);
        }

        $validCoupons = [
            'SAVE10' => ['type' => 'percent', 'value' => 10, 'min_order' => 100000],
            'FLAT50K' => ['type' => 'fixed', 'value' => 50000, 'min_order' => 200000],
            'FREESHIP' => ['type' => 'freeship', 'value' => 0, 'min_order' => 0]
        ];

        $cartTotals = $this->cartModel->getCartTotals($customerId);

        if (!isset($validCoupons[$couponCode])) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Mã giảm giá không hợp lệ hoặc đã hết hạn'
            ]);
        }

        $coupon = $validCoupons[$couponCode];

        if ($cartTotals['subtotal'] < $coupon['min_order']) {
            return $this->response->setJSON([
                'success' => false,
                'message' => "Đơn hàng tối thiểu " . number_format($coupon['min_order']) . "đ để sử dụng mã này"
            ]);
        }

        $discount = 0;
        $freeShipping = false;

        switch ($coupon['type']) {
            case 'percent':
                $discount = ($cartTotals['subtotal'] * $coupon['value']) / 100;
                break;
            case 'fixed':
                $discount = $coupon['value'];
                break;
            case 'freeship':
                $freeShipping = true;
                break;
        }

        $session->set('applied_coupon', [
            'code' => $couponCode,
            'discount' => $discount,
            'free_shipping' => $freeShipping
        ]);

        $newTotal = $cartTotals['subtotal'] - $discount;
        $shippingFee = $freeShipping ? 0 : $cartTotals['shipping_fee'];
        $finalTotal = $newTotal + $shippingFee;

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Áp dụng mã giảm giá thành công',
            'discount' => $discount,
            'free_shipping' => $freeShipping,
            'new_subtotal' => $newTotal,
            'shipping_fee' => $shippingFee,
            'final_total' => $finalTotal
        ]);
    }

    public function removeCoupon()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(404);
        }

        $session = session();
        $session->remove('applied_coupon');

        $customerId = $session->get('customer_id');
        if ($customerId) {
            $cartTotals = $this->cartModel->getCartTotals($customerId);
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Đã xóa mã giảm giá',
                'cart_totals' => $cartTotals
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Đã xóa mã giảm giá'
        ]);
    }

    public function getCartWidget()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(404);
        }

        $session = session();
        $customerId = $session->get('customer_id');

        if (!$customerId) {
            return $this->response->setJSON([
                'success' => true,
                'cart_count' => 0,
                'cart_total' => 0,
                'items' => []
            ]);
        }

        $cartItems = $this->cartModel->getCartWithProducts($customerId);
        $cartTotals = $this->cartModel->getCartTotals($customerId);

        $widgetItems = array_slice($cartItems, 0, 5);
        $formattedItems = [];

        foreach ($widgetItems as $item) {
            $formattedItems[] = [
                'id' => $item['id'],
                'product_id' => $item['product_id'],
                'name' => $item['name'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
                'total' => $item['price'] * $item['quantity'],
                'image' => $item['main_image'],
                'slug' => $item['slug']
            ];
        }

        return $this->response->setJSON([
            'success' => true,
            'cart_count' => $cartTotals['total_items'],
            'cart_total' => $cartTotals['total'],
            'subtotal' => $cartTotals['subtotal'],
            'shipping_fee' => $cartTotals['shipping_fee'],
            'items' => $formattedItems,
            'total_items_in_cart' => count($cartItems)
        ]);
    }

    public function checkout()
    {
        $session = session();
        $customerId = $session->get('customer_id');

        if (!$customerId) {
            return redirect()->to('/api_Customers/customers_sign')
                           ->with('error', 'Vui lòng đăng nhập để thanh toán');
        }

        $cartItems = $this->cartModel->getCartWithProducts($customerId);
        
        if (empty($cartItems)) {
            return redirect()->to('/cart')
                           ->with('error', 'Giỏ hàng trống. Vui lòng thêm sản phẩm trước khi thanh toán');
        }

        $issues = $this->cartModel->validateCart($customerId);
        if (!empty($issues)) {
            $errorMessages = array_map(function($issue) {
                return $issue['product_name'] . ': ' . $issue['message'];
            }, $issues);
            
            return redirect()->to('/cart')
                           ->with('error', 'Giỏ hàng có vấn đề: ' . implode(', ', $errorMessages));
        }

        return redirect()->to('/checkout');
    }

    public function remove()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(404);
        }

        $session = session();
        $customerId = $session->get('customer_id');

        if (!$customerId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Vui lòng đăng nhập'
            ]);
        }

        $productId = $this->request->getPost('product_id');

        if (!$productId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Sản phẩm không hợp lệ'
            ]);
        }

        $result = $this->cartModel->removeFromCart($customerId, $productId);

        if ($result) {
            $cartCount = $this->cartModel->getCartCount($customerId);
            $cartTotals = $this->cartModel->getCartTotals($customerId);
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Đã xóa sản phẩm khỏi giỏ hàng',
                'cart_count' => $cartCount,
                'cart_totals' => $cartTotals
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Không thể xóa sản phẩm'
            ]);
        }
    }

    public function clear()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(404);
        }

        $session = session();
        $customerId = $session->get('customer_id');

        if (!$customerId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Vui lòng đăng nhập'
            ]);
        }

        $result = $this->cartModel->clearCart($customerId);

        if ($result) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Đã xóa toàn bộ giỏ hàng',
                'cart_count' => 0
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Không thể xóa giỏ hàng'
            ]);
        }
    }

    public function validateCart()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(404);
        }

        $session = session();
        $customerId = $session->get('customer_id');

        if (!$customerId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Vui lòng đăng nhập'
            ]);
        }

        $issues = $this->cartModel->validateCart($customerId);

        return $this->response->setJSON([
            'success' => true,
            'issues' => $issues,
            'has_issues' => !empty($issues)
        ]);
    }

    public function getCartData()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(404);
        }

        $session = session();
        $customerId = $session->get('customer_id');

        if (!$customerId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Vui lòng đăng nhập'
            ]);
        }

        $cartItems = $this->cartModel->getCartWithProducts($customerId);
        $cartTotals = $this->cartModel->getCartTotals($customerId);
        $issues = $this->cartModel->validateCart($customerId);

        return $this->response->setJSON([
            'success' => true,
            'data' => [
                'items' => $cartItems,
                'totals' => $cartTotals,
                'issues' => $issues,
                'item_count' => count($cartItems)
            ]
        ]);
    }

    public function addMultiple()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(404);
        }

        $session = session();
        $customerId = $session->get('customer_id');

        if (!$customerId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Vui lòng đăng nhập'
            ]);
        }

        $items = $this->request->getPost('items');
        if (!is_array($items) || empty($items)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Danh sách sản phẩm không hợp lệ'
            ]);
        }

        $added = 0;
        $errors = [];

        foreach ($items as $item) {
            $productId = $item['product_id'] ?? null;
            $quantity = (int)($item['quantity'] ?? 1);

            if (!$productId || $quantity <= 0) {
                $errors[] = "Sản phẩm ID {$productId} không hợp lệ";
                continue;
            }

            $product = $this->productModel->find($productId);
            if (!$product || !$product['is_active']) {
                $errors[] = "Sản phẩm ID {$productId} không tồn tại";
                continue;
            }

            if ($product['stock_quantity'] < $quantity) {
                $errors[] = "Sản phẩm {$product['name']} không đủ hàng";
                continue;
            }

            $price = $product['sale_price'] ?? $product['price'];
            $result = $this->cartModel->addToCart($customerId, $productId, $quantity, $price);

            if ($result) {
                $added++;
            } else {
                $errors[] = "Không thể thêm sản phẩm {$product['name']} vào giỏ hàng";
            }
        }

        $cartCount = $this->cartModel->getCartCount($customerId);
        $cartTotals = $this->cartModel->getCartTotals($customerId);

        return $this->response->setJSON([
            'success' => $added > 0,
            'message' => "Đã thêm {$added} sản phẩm vào giỏ hàng",
            'added' => $added,
            'errors' => $errors,
            'cart_count' => $cartCount,
            'cart_totals' => $cartTotals
        ]);
    }

    public function getCartSummary()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(404);
        }

        $session = session();
        $customerId = $session->get('customer_id');

        if (!$customerId) {
            return $this->response->setJSON([
                'cart_count' => 0,
                'subtotal' => 0,
                'items' => []
            ]);
        }

        $cartItems = $this->cartModel->getCartWithProducts($customerId);
        $cartTotals = $this->cartModel->getCartTotals($customerId);

        $summaryItems = [];
        foreach ($cartItems as $item) {
            $summaryItems[] = [
                'id' => $item['id'],
                'product_id' => $item['product_id'],
                'name' => $item['name'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
                'total' => $item['price'] * $item['quantity'],
                'image' => $item['main_image']
            ];
        }

        return $this->response->setJSON([
            'cart_count' => $cartTotals['total_items'],
            'subtotal' => $cartTotals['subtotal'],
            'shipping_fee' => $cartTotals['shipping_fee'],
            'total' => $cartTotals['total'],
            'items' => $summaryItems
        ]);
    }
}