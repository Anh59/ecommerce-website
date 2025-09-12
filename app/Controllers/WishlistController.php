<?php

namespace App\Controllers;

use App\Models\WishlistModel;
use App\Models\ProductModel;

class WishlistController extends BaseController
{
    protected $wishlistModel;
    protected $productModel;
    
    public function __construct()
    {
        $this->wishlistModel = new WishlistModel();
        $this->productModel = new ProductModel();
    }

    public function index()
    {
        $session = session();
        $customerId = $session->get('customer_id');

        if (!$customerId) {
            return redirect()->to('/api_Customers/customers_sign')->with('error', 'Vui lòng đăng nhập để xem wishlist');
        }

        // Get wishlist items with pagination
        $perPage = 12;
        $page = $this->request->getGet('page') ?? 1;
        $offset = ($page - 1) * $perPage;

        $wishlistItems = $this->wishlistModel->getWishlistWithProducts($customerId, $perPage, $offset);
        
        // Get total count for pagination
        $totalItems = $this->wishlistModel->where('customer_id', $customerId)->countAllResults();
        
        // Get wishlist statistics
        $stats = $this->wishlistModel->getWishlistStats($customerId);

        // Setup pagination
        $pager = \Config\Services::pager();
        $pager->store('default', $page, $perPage, $totalItems);

        $data = [
            'wishlistItems' => $wishlistItems,
            'stats' => $stats,
            'pager' => $pager,
            'currentPage' => $page,
            'totalItems' => $totalItems,
            'totalPages' => ceil($totalItems / $perPage)
        ];

        return view('Customers/wishlist', $data);
    }

    public function add()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(404);
        }

        $session = session();
        $customerId = $session->get('customer_id');

        if (!$customerId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Vui lòng đăng nhập để thêm vào wishlist',
                'redirect' => '/api_Customers/customers_sign'
            ]);
        }

        $productId = $this->request->getPost('product_id');

        if (!$productId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Sản phẩm không hợp lệ'
            ]);
        }

        // Check if product exists
        $product = $this->productModel->find($productId);
        if (!$product) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Sản phẩm không tồn tại'
            ]);
        }

        // Check if already in wishlist
        $existing = $this->wishlistModel->where([
            'customer_id' => $customerId,
            'product_id' => $productId
        ])->first();

        if ($existing) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Sản phẩm đã có trong wishlist'
            ]);
        }

        // Add to wishlist
        $result = $this->wishlistModel->insert([
            'customer_id' => $customerId,
            'product_id' => $productId,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        if ($result) {
            $wishlistCount = $this->wishlistModel->getWishlistCount($customerId);
            return $this->response->setJSON([
                'success' => true,
                'action' => 'added',
                'message' => 'Đã thêm vào wishlist',
                'wishlist_count' => $wishlistCount
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Không thể thêm vào wishlist'
            ]);
        }
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

        $result = $this->wishlistModel->removeFromWishlist($customerId, $productId);

        if ($result) {
            $newCount = $this->wishlistModel->getWishlistCount($customerId);
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Đã xóa khỏi wishlist',
                'wishlist_count' => $newCount
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Không thể xóa sản phẩm'
            ]);
        }
    }

    public function moveToCart()
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

        $productIds = $this->request->getPost('product_ids');
        $productIds = is_array($productIds) ? $productIds : [$productIds];

        if (empty($productIds)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Chọn ít nhất một sản phẩm'
            ]);
        }

        $movedCount = $this->wishlistModel->moveToCart($customerId, $productIds);

        if ($movedCount > 0) {
            $cartModel = new \App\Models\CartModel();
            $cartCount = $cartModel->getCartCount($customerId);
            $wishlistCount = $this->wishlistModel->getWishlistCount($customerId);

            return $this->response->setJSON([
                'success' => true,
                'message' => "Đã chuyển {$movedCount} sản phẩm vào giỏ hàng",
                'moved_count' => $movedCount,
                'cart_count' => $cartCount,
                'wishlist_count' => $wishlistCount
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Không thể chuyển sản phẩm vào giỏ hàng'
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

        $result = $this->wishlistModel->clearWishlist($customerId);

        if ($result) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Đã xóa toàn bộ wishlist',
                'wishlist_count' => 0
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Không thể xóa wishlist'
            ]);
        }
    }

    public function getWishlistData()
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

        $page = $this->request->getGet('page') ?? 1;
        $perPage = $this->request->getGet('per_page') ?? 12;
        $offset = ($page - 1) * $perPage;

        $wishlistItems = $this->wishlistModel->getWishlistWithProducts($customerId, $perPage, $offset);
        $totalItems = $this->wishlistModel->where('customer_id', $customerId)->countAllResults();
        $stats = $this->wishlistModel->getWishlistStats($customerId);

        return $this->response->setJSON([
            'success' => true,
            'data' => [
                'items' => $wishlistItems,
                'stats' => $stats,
                'pagination' => [
                    'current_page' => $page,
                    'per_page' => $perPage,
                    'total_items' => $totalItems,
                    'total_pages' => ceil($totalItems / $perPage)
                ]
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

        $productIds = $this->request->getPost('product_ids');
        if (!is_array($productIds) || empty($productIds)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Danh sách sản phẩm không hợp lệ'
            ]);
        }

        $added = 0;
        $skipped = 0;

        foreach ($productIds as $productId) {
            // Check if product exists
            $product = $this->productModel->find($productId);
            if (!$product) {
                $skipped++;
                continue;
            }

            // Check if already in wishlist
            $existing = $this->wishlistModel->where([
                'customer_id' => $customerId,
                'product_id' => $productId
            ])->first();

            if ($existing) {
                $skipped++;
                continue;
            }

            // Add to wishlist
            $result = $this->wishlistModel->insert([
                'customer_id' => $customerId,
                'product_id' => $productId,
                'created_at' => date('Y-m-d H:i:s')
            ]);

            if ($result) {
                $added++;
            } else {
                $skipped++;
            }
        }

        $wishlistCount = $this->wishlistModel->getWishlistCount($customerId);

        return $this->response->setJSON([
            'success' => true,
            'message' => "Đã thêm {$added} sản phẩm, bỏ qua {$skipped} sản phẩm",
            'added' => $added,
            'skipped' => $skipped,
            'wishlist_count' => $wishlistCount
        ]);
    }
}