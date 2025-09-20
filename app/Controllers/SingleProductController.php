<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ProductModel;
use App\Models\CategoryModel;
use App\Models\BrandModel;
use App\Models\ProductReviewModel;
use App\Models\ProductCommentModel;
use App\Models\WishlistModel;
use App\Models\CartModel;

class SingleProductController extends BaseController
{
    protected $productModel;
    protected $categoryModel;
    protected $brandModel;
    protected $reviewModel;
    protected $commentModel;
    protected $wishlistModel;
    protected $cartModel;

    public function __construct()
    {
        $this->productModel = new ProductModel();
        $this->categoryModel = new CategoryModel();
        $this->brandModel = new BrandModel();
        $this->reviewModel = new ProductReviewModel();
        $this->commentModel = new ProductCommentModel();
        $this->wishlistModel = new WishlistModel();
        $this->cartModel = new CartModel();
    }

    public function detail($slug = null)
    {
        if (!$slug) {
            return redirect()->to('/shop');
        }

        $product = $this->productModel->getProductBySlug($slug);
        
        if (!$product) {
            return redirect()->to('/shop')->with('error', 'Product not found');
        }

        // Lấy thông tin category và brand
        $category = $this->categoryModel->find($product['category_id']);
        $brand = $this->brandModel->find($product['brand_id']);
        
        // Lấy hình ảnh sản phẩm
        $productImages = $this->productModel->getProductImages($product['id']);
        
        // Lấy đánh giá sản phẩm
        $reviews = $this->reviewModel->getProductReviews($product['id']);
        $reviewStats = $this->reviewModel->getReviewStats($product['id']);
        
        // Lấy bình luận sản phẩm
        $comments = $this->commentModel->getProductComments($product['id']);
        
        // Kiểm tra xem sản phẩm có trong wishlist không
        $isInWishlist = false;
        if (session()->has('customer_id')) {
            $isInWishlist = $this->wishlistModel->isInWishlist(session('customer_id'), $product['id']);
        }
        
        // Lấy sản phẩm liên quan
        $relatedProducts = $this->productModel->getRelatedProducts($product['id'], $product['category_id'], 8);

        // Lấy sản phẩm trước/sau (optional)
        $previousProduct = $this->getPreviousProduct($product['id'], $product['category_id']);
        $nextProduct = $this->getNextProduct($product['id'], $product['category_id']);

        $data = [
            'title' => $product['name'] . ' | Shop Single',
            'product' => $product,
            'category' => $category,
            'brand' => $brand,
            'productImages' => $productImages,
            'reviews' => $reviews,
            'reviewStats' => $reviewStats,
            'comments' => $comments,
            'isInWishlist' => $isInWishlist,
            'relatedProducts' => $relatedProducts,
            'previousProduct' => $previousProduct,
            'nextProduct' => $nextProduct
        ];

        return view('Customers/single-product', $data);
    }

    /**
     * FIXED: Buy Now API - Improved session management
     */
    public function buyNow()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request']);
        }

        // Kiểm tra đăng nhập
        if (!session()->has('customer_id')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Please login to buy']);
        }

        $validation = \Config\Services::validation();
        $validation->setRules([
            'product_id' => 'required|numeric',
            'quantity' => 'required|numeric|greater_than[0]',
            'action' => 'required|in_list[buy_now]'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid data',
                'errors' => $validation->getErrors()
            ]);
        }

        $productId = (int)$this->request->getPost('product_id');
        $quantity = (int)$this->request->getPost('quantity');
        $customerId = session('customer_id');

        try {
            // Kiểm tra sản phẩm
            $product = $this->productModel->find($productId);
            if (!$product || !$product['is_active']) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Product not found or inactive'
                ]);
            }

            // Kiểm tra stock
            if ($product['stock_status'] === 'out_of_stock' || $product['stock_quantity'] < $quantity) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Not enough stock. Available: ' . $product['stock_quantity']
                ]);
            }

            // Tính giá sản phẩm (sale_price nếu có, không thì price)
            $price = !empty($product['sale_price']) && $product['sale_price'] > 0 
                ? $product['sale_price'] 
                : $product['price'];

            // FIXED: Clear any existing checkout_selected_items to avoid conflicts
            session()->remove('checkout_selected_items');
            
            // FIXED: Set buy_now_mode with timestamp for expiration
            session()->set('buy_now_mode', [
                'product_id' => $productId,
                'quantity' => $quantity,
                'price' => $price,
                'timestamp' => time()
            ]);

            log_message('debug', 'SingleProduct - Set buy_now_mode for product: ' . $productId . ', quantity: ' . $quantity);

            // Optionally also add to cart for inventory tracking
            // But don't clear entire cart for buy now
            $existingCartItem = $this->cartModel->getCartItem($customerId, $productId);
            
            if ($existingCartItem) {
                // Update quantity
                $result = $this->cartModel->updateQuantity($customerId, $productId, $quantity);
            } else {
                // Add to cart
                $result = $this->cartModel->addToCart($customerId, $productId, $quantity, $price);
            }

            if ($result) {
                $cartTotals = $this->cartModel->getCartTotals($customerId);

                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Product ready for checkout',
                    'buy_now' => true,
                    'cart_totals' => $cartTotals,
                    'product' => [
                        'id' => $product['id'],
                        'name' => $product['name'],
                        'price' => $price,
                        'quantity' => $quantity
                    ]
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Failed to prepare product for checkout'
                ]);
            }

        } catch (\Exception $e) {
            log_message('error', 'Buy Now error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'System error occurred: ' . $e->getMessage()
            ]);
        }
    }

    public function addReview()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request']);
        }

        // Kiểm tra đăng nhập
        if (!session()->has('customer_id')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Please login to review']);
        }

        $validation = \Config\Services::validation();
        $validation->setRules([
            'product_id' => 'required|numeric',
            'rating' => 'required|numeric|greater_than[0]|less_than[6]',
            'title' => 'required|min_length[5]|max_length[255]',
            'comment' => 'required|min_length[10]'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validation->getErrors()
            ]);
        }

        $data = [
            'product_id' => $this->request->getPost('product_id'),
            'customer_id' => session('customer_id'),
            'rating' => $this->request->getPost('rating'),
            'title' => $this->request->getPost('title'),
            'comment' => $this->request->getPost('comment'),
            'is_verified' => true
        ];

        try {
            $this->reviewModel->insert($data);
            
            // Cập nhật thống kê đánh giá
            $reviewStats = $this->reviewModel->getReviewStats($data['product_id']);
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Review added successfully',
                'reviewStats' => $reviewStats
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to add review: ' . $e->getMessage()
            ]);
        }
    }

    public function addComment()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request']);
        }

        // Kiểm tra đăng nhập
        if (!session()->has('customer_id')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Please login to comment']);
        }

        $validation = \Config\Services::validation();
        $validation->setRules([
            'product_id' => 'required|numeric',
            'comment' => 'required|min_length[5]|max_length[1000]',
            'parent_id' => 'permit_empty|numeric'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validation->getErrors()
            ]);
        }

        $data = [
            'product_id' => $this->request->getPost('product_id'),
            'customer_id' => session('customer_id'),
            'parent_id' => $this->request->getPost('parent_id') ?: null,
            'comment' => $this->request->getPost('comment')
        ];

        try {
            $commentId = $this->commentModel->insert($data);
            $newComment = $this->commentModel->getCommentWithCustomer($commentId);
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Comment added successfully',
                'comment' => $newComment
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to add comment: ' . $e->getMessage()
            ]);
        }
    }

    public function toggleWishlist()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request']);
        }

        if (!session()->has('customer_id')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Please login to add to wishlist']);
        }

        $productId = $this->request->getPost('product_id');
        $customerId = session('customer_id');

        try {
            $isInWishlist = $this->wishlistModel->isInWishlist($customerId, $productId);
            
            if ($isInWishlist) {
                // Xóa khỏi wishlist
                $this->wishlistModel->removeFromWishlist($customerId, $productId);
                return $this->response->setJSON([
                    'success' => true,
                    'action' => 'removed',
                    'message' => 'Removed from wishlist'
                ]);
            } else {
                // Thêm vào wishlist
                $this->wishlistModel->addToWishlist($customerId, $productId);
                return $this->response->setJSON([
                    'success' => true,
                    'action' => 'added',
                    'message' => 'Added to wishlist'
                ]);
            }
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to update wishlist: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get previous product in same category
     */
    private function getPreviousProduct($currentId, $categoryId)
    {
        return $this->productModel
            ->where('category_id', $categoryId)
            ->where('id <', $currentId)
            ->where('is_active', 1)
            ->orderBy('id', 'DESC')
            ->first();
    }

    /**
     * Get next product in same category
     */
    private function getNextProduct($currentId, $categoryId)
    {
        return $this->productModel
            ->where('category_id', $categoryId)
            ->where('id >', $currentId)
            ->where('is_active', 1)
            ->orderBy('id', 'ASC')
            ->first();
    }
}