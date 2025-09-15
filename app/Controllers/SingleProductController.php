<?php

namespace App\Controllers;

use App\Models\ProductModel;
use App\Models\ProductImageModel;
use App\Models\CategoryModel;
use App\Models\BrandModel;
use App\Models\ReviewModel;

class SingleProductController extends BaseController
{
    protected $productModel;
    protected $productImageModel;
    protected $categoryModel;
    protected $brandModel;
    protected $reviewModel;

    public function __construct()
    {
        $this->productModel = new ProductModel();
        $this->productImageModel = new ProductImageModel();
        $this->categoryModel = new CategoryModel();
        $this->brandModel = new BrandModel();
        $this->reviewModel = new ReviewModel();
    }

    public function index($productId = null, $slug = null)
    {
        // Nếu có slug, tìm theo slug, không thì tìm theo ID
        if ($slug) {
            $product = $this->productModel->where('slug', $slug)->first();
        } elseif ($productId) {
            $product = $this->productModel->find($productId);
        } else {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Product not found');
        }

        if (!$product || !$product['is_active']) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Product not found');
        }

        // Lấy thông tin category và brand
        $category = null;
        $brand = null;
        
        if ($product['category_id']) {
            $category = $this->categoryModel->find($product['category_id']);
        }
        
        if ($product['brand_id']) {
            $brand = $this->brandModel->find($product['brand_id']);
        }

        // Lấy các hình ảnh sản phẩm
        $productImages = $this->productImageModel->where('product_id', $product['id'])
                                                 ->orderBy('sort_order', 'ASC')
                                                 ->findAll();

        // Nếu không có hình ảnh phụ, sử dụng hình ảnh chính
        if (empty($productImages) && !empty($product['main_image'])) {
            $productImages = [
                [
                    'id' => 0,
                    'image_url' => $product['main_image'],
                    'alt_text' => $product['name'],
                    'is_main' => 1
                ]
            ];
        }

        // Lấy reviews cho sản phẩm
        $reviews = $this->getProductReviews($product['id']);
        $reviewSummary = $this->getReviewSummary($product['id']);

        // Lấy sản phẩm liên quan (cùng category)
        $relatedProducts = $this->getRelatedProducts($product['id'], $product['category_id'], 5);

        // Lấy sản phẩm bán chạy
        $bestSellers = $this->getBestSellerProducts(5);

        // Tăng view count (optional)
        $this->incrementViewCount($product['id']);

        $data = [
            'product' => $product,
            'category' => $category,
            'brand' => $brand,
            'productImages' => $productImages,
            'reviews' => $reviews,
            'reviewSummary' => $reviewSummary,
            'relatedProducts' => $relatedProducts,
            'bestSellers' => $bestSellers,
            'metaTitle' => $product['meta_title'] ?: $product['name'],
            'metaDescription' => $product['meta_description'] ?: $product['short_description']
        ];

        return view('Customers/single-product', $data);
    }

    private function getProductReviews($productId, $limit = 10)
    {
        return $this->reviewModel->select('reviews.*, customers.first_name, customers.last_name')
                                ->join('customers', 'customers.id = reviews.customer_id')
                                ->where('reviews.product_id', $productId)
                                ->where('reviews.status', 'approved')
                                ->orderBy('reviews.created_at', 'DESC')
                                ->limit($limit)
                                ->findAll();
    }

    private function getReviewSummary($productId)
    {
        $db = \Config\Database::connect();
        
        $query = $db->query("
            SELECT 
                COUNT(*) as total_reviews,
                AVG(rating) as average_rating,
                SUM(CASE WHEN rating = 5 THEN 1 ELSE 0 END) as five_star,
                SUM(CASE WHEN rating = 4 THEN 1 ELSE 0 END) as four_star,
                SUM(CASE WHEN rating = 3 THEN 1 ELSE 0 END) as three_star,
                SUM(CASE WHEN rating = 2 THEN 1 ELSE 0 END) as two_star,
                SUM(CASE WHEN rating = 1 THEN 1 ELSE 0 END) as one_star
            FROM reviews 
            WHERE product_id = ? AND status = 'approved'
        ", [$productId]);

        $result = $query->getRow('array');
        
        if ($result && $result['total_reviews'] > 0) {
            $result['average_rating'] = round($result['average_rating'], 1);
        } else {
            $result = [
                'total_reviews' => 0,
                'average_rating' => 0,
                'five_star' => 0,
                'four_star' => 0,
                'three_star' => 0,
                'two_star' => 0,
                'one_star' => 0
            ];
        }

        return $result;
    }

    private function getRelatedProducts($currentProductId, $categoryId, $limit = 5)
    {
        if (!$categoryId) {
            return [];
        }

        return $this->productModel->where('category_id', $categoryId)
                                 ->where('id !=', $currentProductId)
                                 ->where('is_active', 1)
                                 ->where('stock_quantity >', 0)
                                 ->orderBy('RAND()')
                                 ->limit($limit)
                                 ->findAll();
    }

    private function getBestSellerProducts($limit = 5)
    {
        // Giả sử có bảng order_items để tính best seller
        $db = \Config\Database::connect();
        
        $query = $db->query("
            SELECT p.*, SUM(oi.quantity) as total_sold
            FROM products p
            LEFT JOIN order_items oi ON p.id = oi.product_id
            LEFT JOIN orders o ON oi.order_id = o.id
            WHERE p.is_active = 1 
            AND p.stock_quantity > 0
            AND (o.status IS NULL OR o.status = 'completed')
            GROUP BY p.id
            ORDER BY total_sold DESC, p.created_at DESC
            LIMIT ?
        ", [$limit]);

        $result = $query->getResult('array');
        
        // Nếu không có dữ liệu order, lấy featured products
        if (empty($result)) {
            $result = $this->productModel->where('is_featured', 1)
                                        ->where('is_active', 1)
                                        ->where('stock_quantity >', 0)
                                        ->limit($limit)
                                        ->findAll();
        }

        return $result;
    }

    private function incrementViewCount($productId)
    {
        // Tăng view count cho sản phẩm (optional feature)
        $db = \Config\Database::connect();
        $db->query("UPDATE products SET view_count = IFNULL(view_count, 0) + 1 WHERE id = ?", [$productId]);
    }

    // API method để lấy thông tin sản phẩm qua AJAX
    public function getProductInfo()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(404);
        }

        $productId = $this->request->getPost('product_id');
        
        if (!$productId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Product ID is required'
            ]);
        }

        $product = $this->productModel->find($productId);
        
        if (!$product) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Product not found'
            ]);
        }

        // Lấy hình ảnh
        $images = $this->productImageModel->where('product_id', $productId)
                                         ->orderBy('sort_order', 'ASC')
                                         ->findAll();

        return $this->response->setJSON([
            'success' => true,
            'product' => $product,
            'images' => $images
        ]);
    }

    // Add review
    public function addReview()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(404);
        }

        $session = session();
        $customerId = $session->get('customer_id');

        if (!$customerId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Vui lòng đăng nhập để đánh giá sản phẩm'
            ]);
        }

        $productId = $this->request->getPost('product_id');
        $rating = $this->request->getPost('rating');
        $comment = $this->request->getPost('comment');
        $title = $this->request->getPost('title');

        // Validate input
        if (!$productId || !$rating || $rating < 1 || $rating > 5) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Thông tin đánh giá không hợp lệ'
            ]);
        }

        // Check if customer already reviewed this product
        $existingReview = $this->reviewModel->where([
            'customer_id' => $customerId,
            'product_id' => $productId
        ])->first();

        if ($existingReview) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Bạn đã đánh giá sản phẩm này rồi'
            ]);
        }

        // Check if customer bought this product (optional)
        $orderModel = new \App\Models\OrderModel();
        $hasPurchased = $orderModel->select('orders.id')
                                  ->join('order_items', 'order_items.order_id = orders.id')
                                  ->where('orders.customer_id', $customerId)
                                  ->where('order_items.product_id', $productId)
                                  ->where('orders.status', 'completed')
                                  ->first();

        if (!$hasPurchased) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Bạn cần mua sản phẩm này trước khi đánh giá'
            ]);
        }

        // Add review
        $reviewData = [
            'customer_id' => $customerId,
            'product_id' => $productId,
            'rating' => $rating,
            'title' => $title,
            'comment' => $comment,
            'status' => 'pending', // Requires admin approval
            'created_at' => date('Y-m-d H:i:s')
        ];

        if ($this->reviewModel->insert($reviewData)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Đánh giá của bạn đã được gửi và đang chờ duyệt'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Có lỗi xảy ra, vui lòng thử lại'
            ]);
        }
    }
}