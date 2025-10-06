<?php

namespace App\Models;

use CodeIgniter\Model;

class OrderModel extends Model
{
    protected $table = 'orders';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'order_number', 'customer_id', 'status', 'payment_method', 'payment_status','coupon_code',        // THÊM
        'discount_amount',
        'subtotal', 'shipping_fee', 'total_amount',
        'shipping_address', 'billing_address', 'notes', 'tracking_number',
        'shipped_at', 'delivered_at'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation rules
    protected $validationRules = [
        'order_number' => 'required|is_unique[orders.order_number]|max_length[50]',
        'customer_id' => 'required|integer',
        'status' => 'required|in_list[pending,processing,shipped,delivered,cancelled]',
        'payment_method' => 'required|in_list[cod,momo,bank_transfer]',
        'payment_status' => 'required|in_list[pending,paid,failed,refunded]',
        'subtotal' => 'required|decimal',
        'shipping_fee' => 'permit_empty|decimal',
        'discount_amount' => 'permit_empty|decimal',
        'total_amount' => 'required|decimal',
        'shipping_address' => 'required',
         'coupon_code'      => 'permit_empty|max_length[50]',     // THÊM
        'discount_amount'  => 'permit_empty|decimal', 
        'billing_address' => 'required'
    ];

    protected $validationMessages = [
        'order_number' => [
            'required' => 'Mã đơn hàng là bắt buộc',
            'is_unique' => 'Mã đơn hàng đã tồn tại',
            'max_length' => 'Mã đơn hàng không được quá 50 ký tự'
        ],
        'customer_id' => [
            'required' => 'ID khách hàng là bắt buộc',
            'integer' => 'ID khách hàng phải là số nguyên'
        ],
        'status' => [
            'required' => 'Trạng thái đơn hàng là bắt buộc',
            'in_list' => 'Trạng thái đơn hàng không hợp lệ'
        ],
        'payment_method' => [
            'required' => 'Phương thức thanh toán là bắt buộc',
            'in_list' => 'Phương thức thanh toán không hợp lệ'
        ],
        'total_amount' => [
            'required' => 'Tổng tiền là bắt buộc',
            'decimal' => 'Tổng tiền phải là số'
        ]
    ];

    /**
     * Get order by order number
     */
    public function getOrderByNumber($orderNumber, $customerId = null)
    {
        $builder = $this->where('order_number', $orderNumber);
        
        if ($customerId) {
            $builder->where('customer_id', $customerId);
        }
        
        return $builder->first();
    }
// ===== THÊM METHOD MỚI ĐỂ LẤY ĐƠN HÀNG CÓ DÙNG VOUCHER =====
    public function getOrdersWithCoupon($couponCode = null)
    {
        $builder = $this->select('orders.*, customers.name as customer_name, customers.email as customer_email')
                        ->join('customers', 'customers.id = orders.customer_id', 'left')
                        ->where('orders.coupon_code IS NOT NULL');
        
        if ($couponCode) {
            $builder->where('orders.coupon_code', $couponCode);
        }
        
        return $builder->orderBy('orders.created_at', 'DESC')->findAll();
    }

    // Tính tổng discount đã áp dụng
    public function getTotalDiscountByCoupon($couponCode, $startDate = null, $endDate = null)
    {
        $builder = $this->selectSum('discount_amount')
                        ->where('coupon_code', $couponCode)
                        ->where('payment_status', 'paid'); // Chỉ tính đơn đã thanh toán
        
        if ($startDate) {
            $builder->where('created_at >=', $startDate);
        }
        
        if ($endDate) {
            $builder->where('created_at <=', $endDate);
        }
        
        $result = $builder->first();
        return $result['discount_amount'] ?? 0;
    }

    // Đếm số đơn hàng dùng voucher
    public function countOrdersWithCoupon($couponCode, $startDate = null, $endDate = null)
    {
        $builder = $this->where('coupon_code', $couponCode);
        
        if ($startDate) {
            $builder->where('created_at >=', $startDate);
        }
        
        if ($endDate) {
            $builder->where('created_at <=', $endDate);
        }
        
        return $builder->countAllResults();
    }
    /**
     * Get orders for a specific customer
     */
    public function getCustomerOrders($customerId, $limit = null, $status = null)
    {
        $builder = $this->where('customer_id', $customerId);
        
        if ($status) {
            $builder->where('status', $status);
        }
        
        $builder->orderBy('created_at', 'DESC');
        
        if ($limit) {
            $builder->limit($limit);
        }
        
        return $builder->findAll();
    }

    /**
     * Get order with items
     */
    public function getOrderWithItems($orderId)
    {
        $order = $this->find($orderId);
        if ($order) {
            $orderItemModel = new \App\Models\OrderItemModel();
            $order['items'] = $orderItemModel->getOrderItems($orderId);
        }
        return $order;
    }

    /**
     * Get order details with customer info
     */
    public function getOrderDetails($orderId)
    {
        $builder = $this->db->table($this->table . ' o');
        $builder->select('o.*, c.name as customer_name, c.email as customer_email, c.phone as customer_phone');
        $builder->join('customers c', 'c.id = o.customer_id', 'left');
        $builder->where('o.id', $orderId);
        
        return $builder->get()->getRowArray();
    }

    /**
     * Update order status
     */
   /**
 * Update order status
 */
public function updateStatus($orderId, $status, $additionalData = [])
{
    $updateData = array_merge(['status' => $status], $additionalData);
    
    // Add timestamps for specific statuses
    switch ($status) {
        case 'shipped':
            $updateData['shipped_at'] = date('Y-m-d H:i:s');
            break;
        case 'delivered':
            $updateData['delivered_at'] = date('Y-m-d H:i:s');
            break;
    }
    
    // try {
    //     return $this->update($orderId, $updateData);
    // } catch (\Exception $e) {
    //     log_message('error', 'Error updating order status: ' . $e->getMessage());
    //     return false;
    // }
}

    /**
     * Update payment status
     */
    public function updatePaymentStatus($orderId, $paymentStatus)
    {
        return $this->update($orderId, ['payment_status' => $paymentStatus]);
    }

    /**
     * Get orders by status
     */
    public function getOrdersByStatus($status, $limit = null)
    {
        $builder = $this->where('status', $status);
        $builder->orderBy('created_at', 'DESC');
        
        if ($limit) {
            $builder->limit($limit);
        }
        
        return $builder->findAll();
    }

    /**
     * Get daily sales report
     */
    public function getDailySales($date = null)
    {
        if (!$date) {
            $date = date('Y-m-d');
        }
        
        $builder = $this->where('DATE(created_at)', $date);
        $builder->where('status !=', 'cancelled');
        
        return [
            'total_orders' => $builder->countAllResults(false),
            'total_amount' => $builder->selectSum('total_amount')->get()->getRow()->total_amount ?? 0
        ];
    }

    /**
     * Search orders
     */
    public function searchOrders($keyword, $customerId = null, $limit = 20)
    {
        $builder = $this->groupStart()
                        ->like('order_number', $keyword)
                        ->orLike('notes', $keyword)
                        ->groupEnd();
        
        if ($customerId) {
            $builder->where('customer_id', $customerId);
        }
        
        $builder->orderBy('created_at', 'DESC');
        
        if ($limit) {
            $builder->limit($limit);
        }
        
        return $builder->findAll();
    }
}