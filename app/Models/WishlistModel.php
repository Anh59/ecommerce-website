<?php

namespace App\Models;

use CodeIgniter\Model;

class WishlistModel extends Model
{
    protected $table            = 'wishlist';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;

    protected $allowedFields = [
        'customer_id',
        'product_id',
        'created_at'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = '';
    protected $deletedField  = '';

    // Validation
    protected $validationRules = [
        'customer_id' => 'required|integer',
        'product_id'  => 'required|integer'
    ];

    protected $validationMessages = [
        'customer_id' => [
            'required' => 'Customer ID là bắt buộc',
            'integer'  => 'Customer ID phải là số'
        ],
        'product_id' => [
            'required' => 'Product ID là bắt buộc',
            'integer'  => 'Product ID phải là số'
        ]
    ];

    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    /**
     * Get wishlist items with product details for a customer (updated method)
     */
    public function getWishlistWithProducts($customerId, $limit = null, $offset = null)
    {
        $builder = $this->select('wishlist.*, products.name, products.slug, products.price, products.sale_price, products.main_image, products.stock_status, products.stock_quantity, categories.name as category_name, brands.name as brand_name')
                        ->join('products', 'products.id = wishlist.product_id', 'left')
                        ->join('categories', 'categories.id = products.category_id', 'left')
                        ->join('brands', 'brands.id = products.brand_id', 'left')
                        ->where('wishlist.customer_id', $customerId)
                        ->where('products.is_active', 1)
                        ->orderBy('wishlist.created_at', 'DESC');

        if ($limit) {
            $builder->limit($limit, $offset);
        }

        return $builder->findAll();
    }

    /**
     * Add to wishlist method (for compatibility with existing code)
     */
    public function addToWishlist($customerId, $productId)
    {
        return $this->insert([
            'customer_id' => $customerId,
            'product_id' => $productId,
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Check if product is in customer's wishlist
     */
    public function isInWishlist($customerId, $productId)
    {
        return $this->where([
            'customer_id' => $customerId,
            'product_id' => $productId
        ])->first() !== null;
    }

    /**
     * Get wishlist count for customer
     */
    public function getWishlistCount($customerId)
    {
        return $this->where('customer_id', $customerId)->countAllResults();
    }

    /**
     * Remove product from wishlist
     */
    public function removeFromWishlist($customerId, $productId)
    {
        return $this->where([
            'customer_id' => $customerId,
            'product_id' => $productId
        ])->delete();
    }

    /**
     * Clear all wishlist items for customer
     */
    public function clearWishlist($customerId)
    {
        return $this->where('customer_id', $customerId)->delete();
    }

    /**
     * Get popular wishlist products
     */
    public function getPopularWishlistProducts($limit = 10)
    {
        return $this->select('products.*, COUNT(wishlist.product_id) as wishlist_count')
                    ->join('products', 'products.id = wishlist.product_id', 'left')
                    ->where('products.is_active', 1)
                    ->groupBy('wishlist.product_id')
                    ->orderBy('wishlist_count', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }

    /**
     * Move wishlist items to cart
     */
    public function moveToCart($customerId, $productIds = [])
    {
        $cartModel = new CartModel();
        $productModel = new ProductModel();
        
        // Get wishlist items
        $builder = $this->where('customer_id', $customerId);
        if (!empty($productIds)) {
            $builder->whereIn('product_id', $productIds);
        }
        $wishlistItems = $builder->findAll();

        $movedCount = 0;
        foreach ($wishlistItems as $item) {
            // Get product info
            $product = $productModel->find($item['product_id']);
            if (!$product || !$product['is_active'] || $product['stock_quantity'] <= 0) {
                continue;
            }

            // Check if already in cart
            $existingCartItem = $cartModel->where([
                'customer_id' => $customerId,
                'product_id' => $item['product_id']
            ])->first();

            if ($existingCartItem) {
                // Update quantity in cart
                $newQuantity = $existingCartItem['quantity'] + 1;
                if ($newQuantity <= $product['stock_quantity']) {
                    $cartModel->update($existingCartItem['id'], [
                        'quantity' => $newQuantity,
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);
                    $movedCount++;
                }
            } else {
                // Add to cart
                $cartModel->insert([
                    'customer_id' => $customerId,
                    'product_id' => $item['product_id'],
                    'quantity' => 1,
                    'price' => $product['sale_price'] ?? $product['price'],
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
                $movedCount++;
            }

            // Remove from wishlist
            $this->delete($item['id']);
        }

        return $movedCount;
    }

    /**
     * Get wishlist statistics for customer
     */
    public function getWishlistStats($customerId)
    {
        $wishlistItems = $this->getWishlistWithProducts($customerId);
        
        $totalItems = count($wishlistItems);
        $totalValue = 0;
        $availableItems = 0;
        $outOfStockItems = 0;

        foreach ($wishlistItems as $item) {
            $price = $item['sale_price'] ?? $item['price'];
            $totalValue += $price;

            if ($item['stock_status'] === 'in_stock') {
                $availableItems++;
            } else {
                $outOfStockItems++;
            }
        }

        return [
            'total_items' => $totalItems,
            'total_value' => $totalValue,
            'available_items' => $availableItems,
            'out_of_stock_items' => $outOfStockItems
        ];
    }
}