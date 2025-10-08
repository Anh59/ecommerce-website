<?php

namespace App\Models;

use CodeIgniter\Model;

class DiscountCouponModel extends Model
{
    protected $table            = 'discount_coupons';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    
    protected $allowedFields = [
        'code', 'type', 'value', 'min_order_amount', 'usage_limit', 'used_count',
        'apply_all', 'start_date', 'end_date', 'is_active', 'created_at', 'updated_at'
    ];

    protected $validationRules = [
        'code'             => 'required|min_length[3]|max_length[50]|alpha_numeric_punct',
        'type'             => 'required|in_list[percentage,fixed]',
        'value'            => 'required|decimal|greater_than[0]',
        'min_order_amount' => 'permit_empty|decimal|greater_than_equal_to[0]',
        'usage_limit'      => 'permit_empty|integer|greater_than[0]',
        'used_count'       => 'permit_empty|integer|greater_than_equal_to[0]',
        'apply_all'        => 'permit_empty|in_list[0,1]',
        'start_date'       => 'permit_empty|valid_date',
        'end_date'         => 'permit_empty|valid_date',
        'is_active'        => 'required|in_list[0,1]'
    ];

    protected $validationMessages = [
        'code' => [
            'required'           => 'Mã giảm giá là bắt buộc',
            'min_length'         => 'Mã giảm giá phải có ít nhất 3 ký tự',
            'max_length'         => 'Mã giảm giá không được quá 50 ký tự',
            'alpha_numeric_punct' => 'Mã giảm giá chỉ được chứa chữ, số và dấu gạch dưới',
            'is_unique'          => 'Mã giảm giá này đã tồn tại'
        ],
        'type' => [
            'required' => 'Loại giảm giá là bắt buộc',
            'in_list'  => 'Loại giảm giá không hợp lệ'
        ],
        'value' => [
            'required'     => 'Giá trị giảm giá là bắt buộc',
            'decimal'      => 'Giá trị giảm giá phải là số',
            'greater_than' => 'Giá trị giảm giá phải lớn hơn 0'
        ],
        'min_order_amount' => [
            'decimal'                => 'Đơn hàng tối thiểu phải là số',
            'greater_than_equal_to'  => 'Đơn hàng tối thiểu không được âm'
        ],
        'usage_limit' => [
            'integer'      => 'Giới hạn sử dụng phải là số nguyên',
            'greater_than' => 'Giới hạn sử dụng phải lớn hơn 0'
        ],
        'used_count' => [
            'integer'               => 'Số lần đã sử dụng phải là số nguyên',
            'greater_than_equal_to' => 'Số lần đã sử dụng không được âm'
        ],
        'is_active' => [
            'required' => 'Trạng thái là bắt buộc',
            'in_list'  => 'Trạng thái không hợp lệ'
        ]
    ];

    // Custom validation rules
    protected $beforeInsert = ['validateDates', 'validateValue'];
    protected $beforeUpdate = ['validateDates', 'validateValue'];

    // Build validation rules động
    public function buildValidationRules($isInsert = true, $id = null)
    {
        $rules = $this->validationRules;
        $messages = $this->validationMessages;
        
        // Unique rule cho code
        if ($isInsert) {
            $rules['code'] .= '|is_unique[discount_coupons.code]';
        } else {
            $rules['code'] .= "|is_unique[discount_coupons.code,id,{$id}]";
        }
        
        return [$rules, $messages];
    }

    public function rulesForInsert()
    {
        return $this->buildValidationRules(true);
    }

    public function rulesForUpdate($id)
    {
        return $this->buildValidationRules(false, $id);
    }

    // Validate dates
    protected function validateDates(array $data)
    {
        if (!isset($data['data'])) return $data;
        
        $startDate = $data['data']['start_date'] ?? null;
        $endDate = $data['data']['end_date'] ?? null;
        
        if ($startDate && $endDate) {
            if (strtotime($startDate) >= strtotime($endDate)) {
                $this->errors[] = 'Ngày kết thúc phải sau ngày bắt đầu';
                return false;
            }
        }
        
        return $data;
    }

    // Validate percentage value
    protected function validateValue(array $data)
    {
        if (!isset($data['data'])) return $data;
        
        $type = $data['data']['type'] ?? null;
        $value = $data['data']['value'] ?? 0;
        
        if ($type === 'percentage' && $value > 100) {
            $this->errors[] = 'Phần trăm giảm giá không được lớn hơn 100%';
            return false;
        }
        
        return $data;
    }

    // ===== SỬA: Lấy coupon với thông tin products và CAST kiểu dữ liệu =====
    public function getCouponsWithProducts()
    {
        $coupons = $this->select('discount_coupons.*, 
                            COUNT(discount_coupon_products.product_id) as product_count')
                   ->join('discount_coupon_products', 'discount_coupons.id = discount_coupon_products.coupon_id', 'left')
                   ->groupBy('discount_coupons.id')
                   ->orderBy('discount_coupons.created_at', 'DESC')
                   ->findAll();
        
        // ✅ CAST các trường số về đúng kiểu để JavaScript xử lý chính xác
        foreach ($coupons as &$coupon) {
            $coupon['used_count'] = (int) ($coupon['used_count'] ?? 0);
            $coupon['usage_limit'] = $coupon['usage_limit'] ? (int) $coupon['usage_limit'] : null;
            $coupon['is_active'] = (int) ($coupon['is_active'] ?? 0);
            $coupon['apply_all'] = (int) ($coupon['apply_all'] ?? 0);
            $coupon['product_count'] = (int) ($coupon['product_count'] ?? 0);
            $coupon['value'] = (float) ($coupon['value'] ?? 0);
            $coupon['min_order_amount'] = (float) ($coupon['min_order_amount'] ?? 0);
        }
        
        return $coupons;
    }

    // Lấy coupon đang hoạt động
    public function getActiveCoupons()
    {
        return $this->where('is_active', 1)
                   ->where('(start_date IS NULL OR start_date <= NOW())')
                   ->where('(end_date IS NULL OR end_date >= NOW())')
                   ->where('(usage_limit IS NULL OR used_count < usage_limit)')
                   ->orderBy('created_at', 'DESC')
                   ->findAll();
    }

    // Kiểm tra coupon có hợp lệ không
    public function validateCoupon($code, $orderAmount = 0, $productSkus = [])
    {
        $coupon = $this->where('code', $code)->first();
        
        if (!$coupon) {
            return ['valid' => false, 'message' => 'Mã giảm giá không tồn tại'];
        }
        
        if (!$coupon['is_active']) {
            return ['valid' => false, 'message' => 'Mã giảm giá không còn hoạt động'];
        }
        
        // Kiểm tra thời gian
        $now = date('Y-m-d H:i:s');
        if ($coupon['start_date'] && $coupon['start_date'] > $now) {
            return ['valid' => false, 'message' => 'Mã giảm giá chưa có hiệu lực'];
        }
        
        if ($coupon['end_date'] && $coupon['end_date'] < $now) {
            return ['valid' => false, 'message' => 'Mã giảm giá đã hết hạn'];
        }
        
        // ✅ SỬA: Kiểm tra giới hạn sử dụng với cast về int
        $usedCount = (int) ($coupon['used_count'] ?? 0);
        $usageLimit = $coupon['usage_limit'] ? (int) $coupon['usage_limit'] : null;
        
        if ($usageLimit !== null && $usedCount >= $usageLimit) {
            return ['valid' => false, 'message' => 'Mã giảm giá đã hết lượt sử dụng'];
        }
        
        // Kiểm tra đơn hàng tối thiểu
        if ($coupon['min_order_amount'] > 0 && $orderAmount < $coupon['min_order_amount']) {
            return ['valid' => false, 'message' => 'Đơn hàng tối thiểu ' . number_format($coupon['min_order_amount']) . ' VND'];
        }
        
        // Kiểm tra áp dụng sản phẩm
        if (!$coupon['apply_all'] && !empty($productSkus)) {
            $couponProductIds = $this->getCouponProducts($coupon['id']);
            
            // Chuyển đổi SKUs thành product IDs
            $productModel = new \App\Models\ProductModel();
            $productIds = [];
            
            foreach ($productSkus as $sku) {
                $product = $productModel->where('sku', $sku)->first();
                if ($product) {
                    $productIds[] = $product['id'];
                }
            }
            
            $validProducts = array_intersect($productIds, $couponProductIds);
            
            if (empty($validProducts)) {
                return ['valid' => false, 'message' => 'Mã giảm giá không áp dụng cho sản phẩm này'];
            }
        }
        
        return ['valid' => true, 'coupon' => $coupon];
    }

    // Tính số tiền giảm giá
    public function calculateDiscount($coupon, $orderAmount)
    {
        if ($coupon['type'] === 'percentage') {
            return min($orderAmount * ($coupon['value'] / 100), $orderAmount);
        } else {
            return min($coupon['value'], $orderAmount);
        }
    }

    // Tăng số lần sử dụng
    public function incrementUsage($couponId)
    {
         $coupon = $this->find($couponId);
        return $this->update($couponId, [
            'used_count' => ($coupon['used_count'] ?? 0) + 1
        ]);
    }

    // Lấy danh sách sản phẩm của coupon
    public function getCouponProducts($couponId)
    {
        $db = \Config\Database::connect();
        $result = $db->table('discount_coupon_products')
                    ->where('coupon_id', $couponId)
                    ->get()
                    ->getResultArray();
        
        return array_column($result, 'product_id');
    }

    // Lấy danh sách SKU sản phẩm của coupon
    public function getCouponProductSkus($couponId)
    {
        $db = \Config\Database::connect();
        $result = $db->table('discount_coupon_products dcp')
                    ->select('p.sku')
                    ->join('products p', 'p.id = dcp.product_id')
                    ->where('dcp.coupon_id', $couponId)
                    ->get()
                    ->getResultArray();
        
        return array_column($result, 'sku');
    }

    // Thêm sản phẩm vào coupon
    public function addProducts($couponId, $productIds)
    {
        $db = \Config\Database::connect();
        
        // Xóa sản phẩm cũ
        $db->table('discount_coupon_products')->where('coupon_id', $couponId)->delete();
        
        // Thêm sản phẩm mới
        if (!empty($productIds)) {
            $data = [];
            foreach ($productIds as $productId) {
                $data[] = [
                    'coupon_id' => $couponId,
                    'product_id' => $productId
                ];
            }
            $db->table('discount_coupon_products')->insertBatch($data);
        }
        
        return true;
    }

    // Lấy coupon sắp hết hạn
    public function getExpiringCoupons($days = 7)
    {
        return $this->where('is_active', 1)
                   ->where('end_date IS NOT NULL')
                   ->where("end_date BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL {$days} DAY)")
                   ->findAll();
    }

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}