<?php

namespace App\Models;

use CodeIgniter\Model;

class OrderItemModel extends Model
{
    protected $table = 'order_items';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'order_id', 'product_id', 'product_name', 'product_sku',
        'quantity', 'price', 'total'
    ];
    protected $useTimestamps = false;



    public function getOrderItems($orderId)
    {
        return $this->select('order_items.*, products.main_image, products.slug')
                   ->join('products', 'products.id = order_items.product_id', 'left')
                   ->where('order_id', $orderId)
                   ->findAll();
    }
}
