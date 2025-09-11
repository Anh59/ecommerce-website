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
        $customerId = session()->get('customer_id');
        
        if (!$customerId) {
            return redirect()->route('Customers_sign')->with('error', 'Vui lòng đăng nhập để xem danh sách yêu thích');
        }
        
        $data['wishlistItems'] = $this->wishlistModel->getWishlistWithProducts($customerId);
        $data['title'] = 'Sản phẩm yêu thích';
        
        return view('Customers/wishlist', $data);
    }
    
    public function add()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['error' => 'Invalid request']);
        }
        
        $customerId = session()->get('customer_id');
        if (!$customerId) {
            return $this->response->setJSON([
                'success' => false, 
                'message' => 'Vui lòng đăng nhập để thêm sản phẩm vào danh sách yêu thích',
                'redirect' => '/login'
            ]);
        }
        
        $productId = $this->request->getPost('product_id');
        
        // Kiểm tra sản phẩm tồn tại
        $product = $this->productModel->find($productId);
        if (!$product) {
            return $this->response->setJSON(['success' => false, 'message' => 'Sản phẩm không tồn tại']);
        }
        
        // Kiểm tra đã có trong wishlist chưa
        if ($this->wishlistModel->isInWishlist($customerId, $productId)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Sản phẩm đã có trong danh sách yêu thích']);
        }
        
        // Thêm vào wishlist
        $result = $this->wishlistModel->addToWishlist($customerId, $productId);
        
        if ($result) {
            $wishlistCount = $this->wishlistModel->getWishlistCount($customerId);
            return $this->response->setJSON([
                'success' => true, 
                'message' => 'Đã thêm vào danh sách yêu thích',
                'wishlist_count' => $wishlistCount
            ]);
        }
        
        return $this->response->setJSON(['success' => false, 'message' => 'Có lỗi xảy ra']);
    }
    
    public function remove()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['error' => 'Invalid request']);
        }
        
        $customerId = session()->get('customer_id');
        $productId = $this->request->getPost('product_id');
        
        $result = $this->wishlistModel->removeFromWishlist($customerId, $productId);
        
        if ($result) {
            $wishlistCount = $this->wishlistModel->getWishlistCount($customerId);
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Đã xóa khỏi danh sách yêu thích',
                'wishlist_count' => $wishlistCount
            ]);
        }
        
        return $this->response->setJSON(['success' => false, 'message' => 'Có lỗi xảy ra']);
    }
}