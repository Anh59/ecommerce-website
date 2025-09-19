<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\DiscountCouponModel;
use App\Models\ProductModel;

class CouponController extends BaseController
{
    protected $discountCouponModel;
    protected $productModel;

    public function __construct()
    {
        $this->discountCouponModel = new DiscountCouponModel();
        $this->productModel = new ProductModel();
    }

    public function applyCoupon()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400);
        }

        $session = session();
        $customerId = $session->get('customer_id');
        
        if (!$customerId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Vui lòng đăng nhập để sử dụng mã giảm giá'
            ]);
        }

        $couponCode = $this->request->getPost('coupon_code');
        $orderAmount = $this->request->getPost('order_amount', FILTER_VALIDATE_FLOAT) ?: 0;
        $productIds = $this->request->getPost('product_ids') ?: [];

        // Validate coupon
        $result = $this->discountCouponModel->validateCoupon($couponCode, $orderAmount, $productIds);
        
        if (!$result['valid']) {
            return $this->response->setJSON([
                'success' => false,
                'message' => $result['message']
            ]);
        }

        $coupon = $result['coupon'];
        $discountAmount = $this->discountCouponModel->calculateDiscount($coupon, $orderAmount);

        // Save coupon to session
        $session->set('applied_coupon', [
            'code' => $coupon['code'],
            'type' => $coupon['type'],
            'value' => $coupon['value'],
            'discount' => $discountAmount,
            'free_shipping' => false // You can extend this based on your coupon logic
        ]);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Áp dụng mã giảm giá thành công',
            'discount' => $discountAmount
        ]);
    }

    public function removeCoupon()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400);
        }

        $session = session();
        $session->remove('applied_coupon');

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Đã xóa mã giảm giá'
        ]);
    }
}