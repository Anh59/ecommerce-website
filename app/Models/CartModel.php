<?php

namespace App\Models;

use CodeIgniter\Model;

class CartModel extends Model
{
    protected $table            = 'shopping_cart';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;

    protected $allowedFields = [
        'customer_id',
        'product_id',
        'quantity',
        'price',
        'created_at',
        'updated_at'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = '';

    // Validation
    protected $validationRules = [
        'customer_id' => 'required|integer',
        'product_id'  => 'required|integer',
        'quantity'    => 'required|integer|greater_than[0]',
        'price'       => 'required|decimal'
    ];

    protected $validationMessages = [
        'customer_id' => [
            'required' => 'Customer ID là bắt buộc',
            'integer'  => 'Customer ID phải là số'
        ],
        'product_id' => [
            'required' => 'Product ID là bắt buộc',
            'integer'  => 'Product ID phải là số'
        ],
        'quantity' => [
            'required'      => 'Số lượng là bắt buộc',
            'integer'       => 'Số lượng phải là số nguyên',
            'greater_than'  => 'Số lượng phải lớn hơn 0'
        ],
        'price' => [
            'required' => 'Giá là bắt buộc',
            'decimal'  => 'Giá phải là số'
        ]
    ];

    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    /**
     * Get cart items with product details for a customer
     */
    public function getCartWithProducts($customerId)
    {
        return $this->select('shopping_cart.*, products.name, products.slug, products.main_image, products.stock_quantity, products.stock_status, products.weight, categories.name as category_name, brands.name as brand_name')
                    ->join('products', 'products.id = shopping_cart.product_id', 'left')
                    ->join('categories', 'categories.id = products.category_id', 'left')
                    ->join('brands', 'brands.id = products.brand_id', 'left')
                    ->where('shopping_cart.customer_id', $customerId)
                    ->where('products.is_active', 1)
                    ->orderBy('shopping_cart.updated_at', 'DESC')
                    ->findAll();
    }

    /**
     * Get cart count for customer
     */
    public function getCartCount($customerId)
    {
        return $this->where('customer_id', $customerId)->countAllResults();
    }

    /**
     * Get cart total quantity for customer
     */
    public function getCartTotalQuantity($customerId)
    {
        $result = $this->selectSum('quantity')
                      ->where('customer_id', $customerId)
                      ->first();
        
        return $result['quantity'] ?? 0;
    }

    /**
     * Calculate cart totals
     */
    public function getCartTotals($customerId)
    {
        $cartItems = $this->getCartWithProducts($customerId);
        
        $subtotal = 0;
        $totalItems = 0;
        $totalWeight = 0;
        
        foreach ($cartItems as $item) {
            $itemTotal = $item['price'] * $item['quantity'];
            $subtotal += $itemTotal;
            $totalItems += $item['quantity'];
            $totalWeight += ($item['weight'] ?? 0) * $item['quantity'];
        }
        
        // Calculate shipping (example logic)
        $shippingFee = $this->calculateShippingFee($totalWeight, $subtotal);
        
        $total = $subtotal + $shippingFee;
        
        return [
            'subtotal' => $subtotal,
            'shipping_fee' => $shippingFee,
            'total' => $total,
            'total_items' => $totalItems,
            'total_weight' => $totalWeight
        ];
    }

    /**
     * Update cart item quantity
     */
    public function updateQuantity($customerId, $productId, $quantity)
    {
        if ($quantity <= 0) {
            return $this->removeFromCart($customerId, $productId);
        }

        // Check stock availability
        $productModel = new ProductModel();
        $product = $productModel->find($productId);
        
        if (!$product || $product['stock_quantity'] < $quantity) {
            return false;
        }

        return $this->where([
            'customer_id' => $customerId,
            'product_id' => $productId
        ])->set([
            'quantity' => $quantity,
            'updated_at' => date('Y-m-d H:i:s')
        ])->update();
    }

    /**
     * Remove item from cart
     */
    public function removeFromCart($customerId, $productId)
    {
        return $this->where([
            'customer_id' => $customerId,
            'product_id' => $productId
        ])->delete();
    }

    /**
     * Clear entire cart for customer
     */
    public function clearCart($customerId)
    {
        return $this->where('customer_id', $customerId)->delete();
    }

    /**
     * Add or update cart item
     */
    public function addToCart($customerId, $productId, $quantity, $price = null)
{
    // Get product info if price not provided
    if (!$price) {
        $productModel = new ProductModel();
        $product = $productModel->find($productId);
        if (!$product) {
            return false;
        }
        // Sửa lỗi: Kiểm tra nếu sale_price tồn tại và lớn hơn 0
        if (isset($product['sale_price']) && $product['sale_price'] > 0) {
            $price = $product['sale_price'];
        } else {
            $price = $product['price']; // Sử dụng giá gốc nếu không có sale_price
        }
    }

    // Check if item already exists
    $existingItem = $this->where([
        'customer_id' => $customerId,
        'product_id' => $productId
    ])->first();

    if ($existingItem) {
        // Update quantity
        $newQuantity = $existingItem['quantity'] + $quantity;
        return $this->updateQuantity($customerId, $productId, $newQuantity);
    } else {
        // Insert new item
        return $this->insert([
            'customer_id' => $customerId,
            'product_id' => $productId,
            'quantity' => $quantity,
            'price' => $price,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    }
}

    /**
     * Validate cart items (check stock, active products)
     */
    public function validateCart($customerId)
    {
        $cartItems = $this->getCartWithProducts($customerId);
        $issues = [];

        foreach ($cartItems as $item) {
            $issue = [];
            
            // Check if product is still active
            if (!$item['name']) { // Product not found or inactive
                $issue['type'] = 'inactive';
                $issue['message'] = 'Sản phẩm không còn bán';
            }
            // Check stock
            else if ($item['stock_quantity'] < $item['quantity']) {
                $issue['type'] = 'insufficient_stock';
                $issue['message'] = "Chỉ còn {$item['stock_quantity']} sản phẩm trong kho";
                $issue['available_quantity'] = $item['stock_quantity'];
            }
            // Check if out of stock
            else if ($item['stock_status'] === 'out_of_stock') {
                $issue['type'] = 'out_of_stock';
                $issue['message'] = 'Sản phẩm đã hết hàng';
            }

            if (!empty($issue)) {
                $issue['cart_id'] = $item['id'];
                $issue['product_id'] = $item['product_id'];
                $issue['product_name'] = $item['name'];
                $issue['current_quantity'] = $item['quantity'];
                $issues[] = $issue;
            }
        }

        return $issues;
    }

    /**
     * Get cart items for checkout
     */
    public function getCartForCheckout($customerId)
    {
        $cartItems = $this->getCartWithProducts($customerId);
        $validItems = [];
        
        foreach ($cartItems as $item) {
            // Only include valid items
            if ($item['name'] && $item['stock_quantity'] >= $item['quantity'] && $item['stock_status'] !== 'out_of_stock') {
                $validItems[] = $item;
            }
        }
        
        return $validItems;
    }

    /**
     * Clean up old cart items (older than 30 days)
     */
    public function cleanupOldCarts()
    {
        $cutoffDate = date('Y-m-d H:i:s', strtotime('-30 days'));
        return $this->where('updated_at <', $cutoffDate)->delete();
    }

    /**
     * Calculate shipping fee
     */
    private function calculateShippingFee($weight, $subtotal)
    {
        // Example shipping logic
        if ($subtotal >= 500000) { // Free shipping over 500k VND
            return 0;
        }
        
        if ($weight <= 1) { // Under 1kg
            return 30000;
        } elseif ($weight <= 5) { // 1-5kg
            return 50000;
        } else { // Over 5kg
            return 80000;
        }
    }

    /**
     * Merge guest cart with customer cart after login
     */
    public function mergeGuestCart($customerId, $guestCartItems)
    {
        foreach ($guestCartItems as $guestItem) {
            $this->addToCart(
                $customerId, 
                $guestItem['product_id'], 
                $guestItem['quantity'], 
                $guestItem['price']
            );
        }
    }
    
}