<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
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
        helper('number');
    }

    public function index()
    {
        $customerId = session()->get('customer_id');
        
        if (!$customerId) {
            return redirect()->to('/login')->with('error', 'Vui lòng đăng nhập để xem giỏ hàng');
        }
        
        $data['cartItems'] = $this->cartModel->getCartWithProducts($customerId);
        $data['title'] = 'Giỏ hàng';
        
        return view('Customers/cart', $data);
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
                'message' => 'Vui lòng đăng nhập để thêm sản phẩm vào giỏ hàng',
                'redirect' => '/login'
            ]);
        }
        
        $productId = $this->request->getPost('product_id');
        $quantity = $this->request->getPost('quantity', 1);
        
        // Kiểm tra sản phẩm tồn tại
        $product = $this->productModel->find($productId);
        if (!$product) {
            return $this->response->setJSON(['success' => false, 'message' => 'Sản phẩm không tồn tại']);
        }
        
        // Kiểm tra số lượng tồn kho
        if ($product->stock_quantity < $quantity) {
            return $this->response->setJSON([
                'success' => false, 
                'message' => 'Số lượng sản phẩm trong kho không đủ'
            ]);
        }
        
        // Thêm vào giỏ hàng
        $result = $this->cartModel->addToCart($customerId, $productId, $quantity, $product->price);
        
        if ($result) {
            $cartCount = $this->cartModel->getCartCount($customerId);
            return $this->response->setJSON([
                'success' => true, 
                'message' => 'Đã thêm sản phẩm vào giỏ hàng',
                'cart_count' => $cartCount
            ]);
        }
        
        return $this->response->setJSON(['success' => false, 'message' => 'Có lỗi xảy ra']);
    }
    
    public function update()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['error' => 'Invalid request']);
        }
        
        $customerId = session()->get('customer_id');
        $productId = $this->request->getPost('product_id');
        $quantity = $this->request->getPost('quantity');
        
        if ($quantity <= 0) {
            return $this->remove();
        }
        
        $result = $this->cartModel->updateCartQuantity($customerId, $productId, $quantity);
        
        if ($result) {
            $cartItem = $this->cartModel->getCartItem($customerId, $productId);
            $cartCount = $this->cartModel->getCartCount($customerId);
            $subtotal = $this->cartModel->getCartSubtotal($customerId);
            
            return $this->response->setJSON([
                'success' => true,
                'item_total' => number_format($cartItem->price * $cartItem->quantity, 0, ',', '.'),
                'subtotal' => number_format($subtotal, 0, ',', '.'),
                'cart_count' => $cartCount
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
        
        $result = $this->cartModel->removeFromCart($customerId, $productId);
        
        if ($result) {
            $cartCount = $this->cartModel->getCartCount($customerId);
            $subtotal = $this->cartModel->getCartSubtotal($customerId);
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Đã xóa sản phẩm khỏi giỏ hàng',
                'subtotal' => number_format($subtotal, 0, ',', '.'),
                'cart_count' => $cartCount
            ]);
        }
        
        return $this->response->setJSON(['success' => false, 'message' => 'Có lỗi xảy ra']);
    }
}
