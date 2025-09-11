<?php
namespace App\Models;

use CodeIgniter\Model;

class CartModel extends Model
{
    protected $table = 'shopping_cart';
    protected $primaryKey = 'id';
    protected $allowedFields = ['customer_id', 'product_id', 'quantity', 'price'];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    public function addToCart($customerId, $productId, $quantity, $price)
    {
        // Kiểm tra sản phẩm đã có trong giỏ hàng chưa
        $existingItem = $this->where('customer_id', $customerId)
                            ->where('product_id', $productId)
                            ->first();
        
        if ($existingItem) {
            // Nếu đã có, cập nhật số lượng
            return $this->update($existingItem->id, [
                'quantity' => $existingItem->quantity + $quantity,
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        } else {
            // Nếu chưa có, thêm mới
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
    
    public function getCartWithProducts($customerId)
    {
        $db = \Config\Database::connect();
        
        return $db->table('shopping_cart as sc')
                 ->select('sc.*, p.name, p.slug, p.main_image, p.stock_quantity, p.stock_status')
                 ->join('products as p', 'p.id = sc.product_id')
                 ->where('sc.customer_id', $customerId)
                 ->where('p.deleted_at', null)
                 ->get()
                 ->getResult();
    }
    
    public function getCartCount($customerId)
    {
        return $this->where('customer_id', $customerId)
                    ->countAllResults();
    }
    
    public function getCartSubtotal($customerId)
    {
        $items = $this->where('customer_id', $customerId)->findAll();
        $subtotal = 0;
        
        foreach ($items as $item) {
            $subtotal += $item->price * $item->quantity;
        }
        
        return $subtotal;
    }
    
    public function updateCartQuantity($customerId, $productId, $quantity)
    {
        $item = $this->where('customer_id', $customerId)
                    ->where('product_id', $productId)
                    ->first();
        
        if ($item) {
            return $this->update($item->id, [
                'quantity' => $quantity,
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        }
        
        return false;
    }
    
    public function removeFromCart($customerId, $productId)
    {
        return $this->where('customer_id', $customerId)
                   ->where('product_id', $productId)
                   ->delete();
    }
    
    public function getCartItem($customerId, $productId)
    {
        return $this->where('customer_id', $customerId)
                   ->where('product_id', $productId)
                   ->first();
    }
    
    public function clearCart($customerId)
    {
        return $this->where('customer_id', $customerId)->delete();
    }
}