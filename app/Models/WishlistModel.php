<?php
namespace App\Models;

use CodeIgniter\Model;

class WishlistModel extends Model
{
    protected $table = 'wishlist';
    protected $primaryKey = 'id';
    protected $allowedFields = ['customer_id', 'product_id'];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';

    public function addToWishlist($customerId, $productId)
    {
        return $this->insert([
            'customer_id' => $customerId,
            'product_id' => $productId,
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }
    
    public function getWishlistWithProducts($customerId)
    {
        $db = \Config\Database::connect();
        
        return $db->table('wishlist as w')
                 ->select('w.*, p.name, p.slug, p.main_image, p.price, p.sale_price, p.stock_status')
                 ->join('products as p', 'p.id = w.product_id')
                 ->where('w.customer_id', $customerId)
                 ->where('p.deleted_at', null)
                 ->where('p.is_active', 1)
                 ->get()
                 ->getResult();
    }
    
    public function getWishlistCount($customerId)
    {
        return $this->where('customer_id', $customerId)
                    ->countAllResults();
    }
    
    public function isInWishlist($customerId, $productId)
    {
        return $this->where('customer_id', $customerId)
                   ->where('product_id', $productId)
                   ->countAllResults() > 0;
    }
    
    public function removeFromWishlist($customerId, $productId)
    {
        return $this->where('customer_id', $customerId)
                   ->where('product_id', $productId)
                   ->delete();
    }
}