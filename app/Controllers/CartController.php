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

    /**
     * FIXED: Phương thức updateQuantity - xử lý đúng quantity = 0
     */
    public function updateQuantity()
    {
        // Bật debug
        log_message('info', 'updateQuantity called');
        log_message('info', 'POST data: ' . json_encode($this->request->getPost()));
        
        if (!$this->request->isAJAX()) {
            log_message('error', 'Not AJAX request');
            return $this->response->setStatusCode(404);
        }

        $session = session();
        $customerId = $session->get('customer_id');

        if (!$customerId) {
            log_message('error', 'No customer ID in session');
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Vui lòng đăng nhập'
            ]);
        }

        $productId = (int)$this->request->getPost('product_id');
        $quantity = (int)$this->request->getPost('quantity');

        log_message('info', "Processing: customer={$customerId}, product={$productId}, quantity={$quantity}");

        if (!$productId || $quantity < 0) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Thông tin không hợp lệ'
            ]);
        }

        try {
            // Nếu quantity = 0, xóa sản phẩm
            if ($quantity === 0) {
                $result = $this->cartModel->removeFromCart($customerId, $productId);
                if ($result) {
                    $cartTotals = $this->cartModel->getCartTotals($customerId);
                    return $this->response->setJSON([
                        'success' => true,
                        'message' => 'Đã xóa sản phẩm khỏi giỏ hàng',
                        'action' => 'removed',
                        'cart_count' => $cartTotals['total_items'],
                        'subtotal' => $cartTotals['subtotal'],
                        'total' => $cartTotals['total']
                    ]);
                } else {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Không thể xóa sản phẩm'
                    ]);
                }
            }

            // Check stock
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

            // Update quantity
            $result = $this->cartModel->updateQuantity($customerId, $productId, $quantity);

            if ($result) {
                $cartTotals = $this->cartModel->getCartTotals($customerId);

                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Đã cập nhật số lượng',
                    'action' => 'updated',
                    'cart_count' => $cartTotals['total_items'],
                    'subtotal' => $cartTotals['subtotal'],
                    'total' => $cartTotals['total']
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Không thể cập nhật số lượng'
                ]);
            }

        } catch (\Exception $e) {
            log_message('error', 'updateQuantity error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Có lỗi hệ thống: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * NEW: Set selected items for checkout - FIXED to clear buy_now_mode
     */
    public function setCheckoutItems()
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

        $selectedItems = $this->request->getPost('selected_items');
        
        if (!$selectedItems || !is_array($selectedItems)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Vui lòng chọn ít nhất một sản phẩm'
            ]);
        }

        // FIXED: Clear buy_now_mode when user selects items from cart
        if ($session->get('buy_now_mode')) {
            $session->remove('buy_now_mode');
            log_message('debug', 'CartController - Cleared buy_now_mode due to cart selection');
        }

        // Validate selected items exist in cart and are available
        $cartItems = $this->cartModel->getCartWithProducts($customerId);
        $validItems = [];
        
        foreach ($selectedItems as $productId) {
            foreach ($cartItems as $cartItem) {
                if ($cartItem['product_id'] == $productId) {
                    // Check if item is available
                    if ($cartItem['stock_status'] === 'out_of_stock') {
                        return $this->response->setJSON([
                            'success' => false,
                            'message' => "Sản phẩm {$cartItem['name']} đã hết hàng"
                        ]);
                    }
                    
                    if ($cartItem['stock_quantity'] < $cartItem['quantity']) {
                        return $this->response->setJSON([
                            'success' => false,
                            'message' => "Sản phẩm {$cartItem['name']} không đủ số lượng trong kho"
                        ]);
                    }
                    
                    $validItems[] = [
                        'product_id' => $cartItem['product_id'],
                        'name' => $cartItem['name'],
                        'quantity' => $cartItem['quantity'],
                        'price' => $cartItem['price'],
                        'total' => $cartItem['price'] * $cartItem['quantity'],
                        'main_image' => $cartItem['main_image'],
                        'slug' => $cartItem['slug'],
                        'sku' => $cartItem['sku'] ?? '',
                        'category_name' => $cartItem['category_name'] ?? '',
                        'brand_name' => $cartItem['brand_name'] ?? ''
                    ];
                    break;
                }
            }
        }

        if (empty($validItems)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Không tìm thấy sản phẩm hợp lệ'
            ]);
        }

        // Store selected items in session
        $session->set('checkout_selected_items', $validItems);
        
        log_message('debug', 'CartController - Set checkout_selected_items: ' . count($validItems) . ' items');
        
        return $this->response->setJSON([
            'success' => true,
            'message' => 'Đã chuẩn bị thanh toán cho ' . count($validItems) . ' sản phẩm',
            'selected_count' => count($validItems)
        ]);
    }

    /**
     * NEW: Get selected items for checkout
     */
    public function getCheckoutItems()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(404);
        }

        $session = session();
        $selectedItems = $session->get('checkout_selected_items');

        if (!$selectedItems) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Không có sản phẩm nào được chọn'
            ]);
        }

        // Calculate totals for selected items
        $subtotal = 0;
        foreach ($selectedItems as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }

        return $this->response->setJSON([
            'success' => true,
            'data' => [
                'items' => $selectedItems,
                'subtotal' => $subtotal,
                'count' => count($selectedItems)
            ]
        ]);
    }

    public function index()
    {
        $session = session();
        $customerId = $session->get('customer_id');

        $data = [
            'cartItems' => [],
            'cartTotals' => [
                'subtotal' => 0,
                'total' => 0,
                'total_items' => 0,
                'total_weight' => 0
            ],
            'cartIssues' => []
        ];

        if ($customerId) {
            $data['cartItems'] = $this->cartModel->getCartWithProducts($customerId);
            $data['cartTotals'] = $this->cartModel->getCartTotals($customerId);
            $data['cartIssues'] = $this->cartModel->validateCart($customerId);
        }

        return view('Customers/cart', $data);
    }

    /**
     * FIXED: Xử lý update quantity trong batch
     */
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
        $removed = 0;
        $errors = [];

        foreach ($updates as $productId => $quantity) {
            $quantity = (int)$quantity;
            $productId = (int)$productId;

            if ($quantity < 0) continue;

            // FIXED: Nếu quantity = 0 thì xóa sản phẩm
            if ($quantity == 0) {
                if ($this->cartModel->removeFromCart($customerId, $productId)) {
                    $removed++;
                }
                continue;
            }

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

        if ($this->request->isAJAX()) {
            $cartTotals = $this->cartModel->getCartTotals($customerId);
            $cartItems = $this->cartModel->getCartWithProducts($customerId);
            
            $totalChanges = $updated + $removed;
            $message = '';
            if ($updated > 0) $message .= "Đã cập nhật {$updated} sản phẩm. ";
            if ($removed > 0) $message .= "Đã xóa {$removed} sản phẩm. ";
            if ($totalChanges == 0) $message = 'Không có sản phẩm nào được cập nhật.';
            
            return $this->response->setJSON([
                'success' => $totalChanges > 0,
                'message' => trim($message),
                'errors' => $errors,
                'cart_totals' => $cartTotals,
                'cart_items' => $cartItems,
                'updated_count' => $updated,
                'removed_count' => $removed
            ]);
        }

        $totalChanges = $updated + $removed;
        $message = '';
        if ($updated > 0) $message .= "Đã cập nhật {$updated} sản phẩm. ";
        if ($removed > 0) $message .= "Đã xóa {$removed} sản phẩm. ";
        if ($totalChanges == 0) $message = 'Không có sản phẩm nào được cập nhật.';
        
        if (!empty($errors)) {
            $message .= ' Có một số lỗi: ' . implode(', ', $errors);
        }

        return redirect()->back()->with('success', $message);
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

        $productId = (int)$this->request->getPost('product_id');

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

    public function checkout()
    {
        $session = session();
        $customerId = $session->get('customer_id');

        if (!$customerId) {
            return redirect()->to('/api_Customers/customers_sign')
                           ->with('error', 'Vui lòng đăng nhập để thanh toán');
        }

        // FIXED: Check priority - selected items first, then buy_now, then all cart
        $selectedItems = $session->get('checkout_selected_items');
        $buyNowMode = $session->get('buy_now_mode');
        
        // Priority 1: Selected items from cart
        if ($selectedItems && !empty($selectedItems)) {
            log_message('debug', 'checkout() - Processing selected items from cart');
            
            // Validate selected items are still available
            foreach ($selectedItems as $item) {
                $product = $this->productModel->find($item['product_id']);
                if (!$product || !$product['is_active'] || $product['stock_quantity'] < $item['quantity']) {
                    $session->remove('checkout_selected_items');
                    return redirect()->to('/cart')
                                   ->with('error', 'Có sản phẩm trong danh sách đã chọn không còn khả dụng. Vui lòng kiểm tra lại.');
                }
            }
        }
        // Priority 2: Buy now mode (only if no selected items)
        else if ($buyNowMode && isset($buyNowMode['product_id'])) {
            log_message('debug', 'checkout() - Processing buy now mode');
            
            // Validate buy now product
            $product = $this->productModel->find($buyNowMode['product_id']);
            if (!$product || !$product['is_active'] || $product['stock_quantity'] < $buyNowMode['quantity']) {
                $session->remove('buy_now_mode');
                return redirect()->to('/cart')
                               ->with('error', 'Sản phẩm mua ngay không còn khả dụng.');
            }
        }
        // Priority 3: All cart items (fallback)
        else {
            log_message('debug', 'checkout() - Processing all cart items');
            
            $cartItems = $this->cartModel->getCartWithProducts($customerId);
            if (empty($cartItems)) {
                return redirect()->to('/cart')
                               ->with('error', 'Giỏ hàng trống. Vui lòng thêm sản phẩm trước khi thanh toán');
            }
        }

        return redirect()->to('/checkout');
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
            'items' => $formattedItems,
            'total_items_in_cart' => count($cartItems)
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
            'total' => $cartTotals['total'],
            'items' => $summaryItems
        ]);
    }
}