<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\DiscountCouponModel;

class DiscountCouponController extends BaseController
{
    protected $discountCouponModel;

    public function __construct()
    {
        $this->discountCouponModel = new DiscountCouponModel();
    }

    // Danh sách voucher
    public function index()
    {
        return view('Dashboard/DiscountCoupon/table');
    }

    // Danh sách cho DataTables
    public function list()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400);
        }

        $coupons = $this->discountCouponModel->getCouponsWithProducts();

        return $this->response->setJSON([
            'status' => 'success',
            'data'   => $coupons,
            'token'  => csrf_hash()
        ]);
    }

    // Thêm voucher
    public function store()
    {
        return $this->saveData();
    }

    // Cập nhật voucher
    public function update($id)
    {
        return $this->saveData($id);
    }

    private function saveData($id = null)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400);
        }

        $isUpdate = !is_null($id);
        [$rules, $messages] = $isUpdate 
            ? $this->discountCouponModel->rulesForUpdate($id)
            : $this->discountCouponModel->rulesForInsert();

        if (!$this->validate($rules, $messages)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => $this->validator->getErrors(),
                'token'   => csrf_hash()
            ]);
        }

        // Lấy dữ liệu
        $data = $this->request->getPost([
            'code', 'type', 'value', 'min_order_amount', 'usage_limit', 
            'apply_all', 'start_date', 'end_date', 'is_active'
        ]);

        // Set default values
        $data['used_count'] = $data['used_count'] ?? 0;
        $data['apply_all'] = $data['apply_all'] ?? 0;
        $data['is_active'] = $data['is_active'] ?? 1;
        $data['min_order_amount'] = $data['min_order_amount'] ?: 0;
        
        // Xử lý datetime fields
        $data['start_date'] = !empty($data['start_date']) ? $data['start_date'] : null;
        $data['end_date'] = !empty($data['end_date']) ? $data['end_date'] : null;

        // Validate percentage value
        if ($data['type'] === 'percentage' && $data['value'] > 100) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => ['value' => 'Phần trăm giảm giá không được lớn hơn 100%'],
                'token'   => csrf_hash()
            ]);
        }

        // Validate dates
        if ($data['start_date'] && $data['end_date']) {
            if (strtotime($data['start_date']) >= strtotime($data['end_date'])) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => ['end_date' => 'Ngày kết thúc phải sau ngày bắt đầu'],
                    'token'   => csrf_hash()
                ]);
            }
        }

        // Lưu hoặc cập nhật
        if ($isUpdate) {
            // Khi update, không reset used_count trừ khi có yêu cầu rõ ràng
            if (!$this->request->getPost('reset_usage')) {
                unset($data['used_count']);
            }
            $this->discountCouponModel->update($id, $data);
            $couponId = $id;
            $message = 'Cập nhật voucher thành công';
        } else {
            $couponId = $this->discountCouponModel->insert($data);
            $message = 'Thêm voucher thành công';
        }

        // Trong phương thức saveData()
        // Xử lý products nếu không apply_all
        if (!$data['apply_all']) {
            $skus = $this->request->getPost('product_skus') ?: [];
            if (is_string($skus)) {
                $skus = explode(',', $skus);
            }
            $skus = array_filter(array_map('trim', $skus));
            
            // Chuyển đổi SKUs thành product IDs
            $productModel = new \App\Models\ProductModel();
            $productIds = [];
            
            foreach ($skus as $sku) {
                $product = $productModel->where('sku', $sku)->first();
                if ($product) {
                    $productIds[] = $product['id'];
                }
            }
            
            $this->discountCouponModel->addProducts($couponId, $productIds);
        }

        return $this->response->setJSON([
            'status' => 'success',
            'message' => $message,
            'token'   => csrf_hash()
        ]);
    }

    // Lấy voucher để edit
   // Trong phương thức edit()
public function edit($id)
{
    $coupon = $this->discountCouponModel->find($id);

    if (!$coupon) {
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Không tìm thấy voucher',
            'token' => csrf_hash()
        ]);
    }

    // Lấy danh sách SKU sản phẩm của coupon
    $productSkus = $this->discountCouponModel->getCouponProductSkus($id);

    return $this->response->setJSON([
        'status' => 'success',
        'coupon' => $coupon,
        'product_skus' => $productSkus, // Đổi từ product_ids thành product_skus
        'token' => csrf_hash()
    ]);
}

    // Xóa voucher
    public function delete($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400);
        }

        // Kiểm tra xem voucher có đang được sử dụng không
        $coupon = $this->discountCouponModel->find($id);
        if (!$coupon) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Không tìm thấy voucher',
                'token' => csrf_hash()
            ]);
        }

        if ($coupon['used_count'] > 0) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Không thể xóa voucher đã được sử dụng. Hãy ngưng hoạt động thay vì xóa.',
                'token' => csrf_hash()
            ]);
        }

        if ($this->discountCouponModel->delete($id)) {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Xóa voucher thành công',
                'token' => csrf_hash()
            ]);
        }

        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Lỗi khi xóa voucher',
            'token' => csrf_hash()
        ]);
    }

    // Toggle trạng thái hoạt động
    public function toggleActive($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400);
        }

        $coupon = $this->discountCouponModel->find($id);
        if (!$coupon) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Không tìm thấy voucher',
                'token' => csrf_hash()
            ]);
        }

        $newStatus = $coupon['is_active'] ? 0 : 1;
        $this->discountCouponModel->update($id, ['is_active' => $newStatus]);

        return $this->response->setJSON([
            'status' => 'success',
            'message' => $newStatus ? 'Đã kích hoạt voucher' : 'Đã ngưng hoạt động voucher',
            'is_active' => $newStatus,
            'token' => csrf_hash()
        ]);
    }

    // Reset số lần sử dụng
    public function resetUsage($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400);
        }

        $coupon = $this->discountCouponModel->find($id);
        if (!$coupon) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Không tìm thấy voucher',
                'token' => csrf_hash()
            ]);
        }

        $this->discountCouponModel->update($id, ['used_count' => 0]);

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Đã reset số lần sử dụng',
            'token' => csrf_hash()
        ]);
    }

    // Nhân bản voucher
    public function duplicate($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400);
        }

        $originalCoupon = $this->discountCouponModel->find($id);
        if (!$originalCoupon) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Không tìm thấy voucher',
                'token' => csrf_hash()
            ]);
        }

        // Tạo mã mới
        $newCode = $originalCoupon['code'] . '_COPY_' . time();
        
        // Dữ liệu voucher mới
        $newCoupon = $originalCoupon;
        unset($newCoupon['id']);
        $newCoupon['code'] = $newCode;
        $newCoupon['used_count'] = 0;
        $newCoupon['is_active'] = 0; // Tạm ngưng để admin kiểm tra
        
        $newId = $this->discountCouponModel->insert($newCoupon);

        // Copy products nếu có
        if (!$originalCoupon['apply_all']) {
            $productIds = $this->discountCouponModel->getCouponProducts($id);
            $this->discountCouponModel->addProducts($newId, $productIds);
        }

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Đã nhân bản voucher với mã: ' . $newCode,
            'new_code' => $newCode,
            'token' => csrf_hash()
        ]);
    }

    // Validate voucher code (cho frontend)
    public function validateCode()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400);
        }

        $code = $this->request->getPost('code');
        $orderAmount = $this->request->getPost('order_amount', FILTER_VALIDATE_FLOAT) ?: 0;
        $productIds = $this->request->getPost('product_ids') ?: [];

        if (is_string($productIds)) {
            $productIds = explode(',', $productIds);
        }

        $result = $this->discountCouponModel->validateCoupon($code, $orderAmount, $productIds);

        if ($result['valid']) {
            $discountAmount = $this->discountCouponModel->calculateDiscount($result['coupon'], $orderAmount);
            
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Mã giảm giá hợp lệ',
                'coupon' => $result['coupon'],
                'discount_amount' => $discountAmount,
                'token' => csrf_hash()
            ]);
        } else {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => $result['message'],
                'token' => csrf_hash()
            ]);
        }
    }

    // Báo cáo sử dụng voucher
    public function usageReport($id)
    {
        // Tùy chỉnh theo nhu cầu báo cáo
        $coupon = $this->discountCouponModel->find($id);
        
        if (!$coupon) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        // Lấy lịch sử sử dụng từ bảng orders hoặc order_items
        // Code này cần tùy chỉnh theo cấu trúc database thực tế
        
        $data = [
            'coupon' => $coupon,
            'usage_data' => [] // Dữ liệu sử dụng chi tiết
        ];

        return view('Dashboard/DiscountCoupon/usage_report', $data);
    }

    // Generate voucher code tự động
    public function generateCode()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400);
        }

        $prefix = $this->request->getPost('prefix') ?: 'COUPON';
        $length = $this->request->getPost('length') ?: 8;
        
        do {
            $code = $prefix . '_' . strtoupper(substr(md5(time() . rand()), 0, $length));
            $exists = $this->discountCouponModel->where('code', $code)->first();
        } while ($exists);

        return $this->response->setJSON([
            'status' => 'success',
            'code' => $code,
            'token' => csrf_hash()
        ]);
    }
}