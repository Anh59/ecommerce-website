<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ProductModel;
use App\Models\CategoryModel;
use App\Models\BrandModel;
use App\Models\ProductReviewModel;
use App\Models\ProductCommentModel;
use App\Models\WishlistModel;

class SingleProductController extends BaseController
{
    protected $productModel;
    protected $categoryModel;
    protected $brandModel;
    protected $reviewModel;
    protected $commentModel;
    protected $wishlistModel;

    public function __construct()
    {
        $this->productModel = new ProductModel();
        $this->categoryModel = new CategoryModel();
        $this->brandModel = new BrandModel();
        $this->reviewModel = new ProductReviewModel();
        $this->commentModel = new ProductCommentModel();
        $this->wishlistModel = new WishlistModel();
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
            'relatedProducts' => $relatedProducts
        ];

        return view('Customers/single-product', $data);
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
}