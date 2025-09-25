<?php

namespace App\Controllers;

use App\Models\ProductModel;
use App\Models\OrderModel;
use App\Models\OrderItemModel;
use App\Models\CustomerModel;

class DashboardController extends BaseController
{
    protected $productModel;
    protected $orderModel;
    protected $orderItemModel;
    protected $customerModel;

    public function __construct()
    {
        $this->productModel = new ProductModel();
        $this->orderModel = new OrderModel();
        $this->orderItemModel = new OrderItemModel();
        $this->customerModel = new CustomerModel();
    }

    public function index()
    {
        return redirect()->to('Dashboard/table');
    }

    public function table()
    {
        return view('Dashboard/table');
    }

    /**
     * API endpoint để lấy dữ liệu thống kê
     */
    public function getDashboardDataAjax()
    {
        try {
            // THAY ĐỔI: Tổng thu nhập thay vì tổng sản phẩm
            $totalRevenue = $this->getTotalRevenue();
            $activeProducts = $this->productModel->where('is_active', 1)->countAllResults();
            $outOfStockProducts = $this->productModel->where('stock_quantity <=', 0)->countAllResults();
            
            // Thống kê đơn hàng
            $totalOrders = $this->orderModel->countAllResults();
            
            // Thống kê người dùng
            $totalUsers = $this->customerModel->countAllResults();
            $activeUsers = $this->customerModel->where('is_verified', 1)->countAllResults();
            $newUsersThisMonth = $this->customerModel
                ->where('YEAR(created_at)', date('Y'))
                ->where('MONTH(created_at)', date('m'))
                ->countAllResults();
            
            // Lấy tháng hiện tại hoặc tháng được chọn
            $selectedMonth = $this->request->getGet('month') ?? date('Y-m');
            
            // THAY ĐỔI: Đổi biểu đồ trạng thái kho hàng thành biểu đồ doanh thu theo tháng
            $revenueByMonthData = $this->getRevenueByMonthData();
            $dailyOrdersData = $this->getDailyOrdersData($selectedMonth);
            $orderStatusData = $this->getOrderStatusData();
            $topProducts = $this->getTopSellingProducts();
            
            // SỬA: Lấy sản phẩm sắp hết hàng từ bảng products
            $lowStockProducts = $this->getLowStockProducts();

            $data = [
                'success' => true,
                'data' => [
                    'totalRevenue' => $totalRevenue, // THAY ĐỔI: Tổng thu nhập
                    'activeProducts' => $activeProducts,
                    'outOfStockProducts' => $outOfStockProducts,
                    'totalOrders' => $totalOrders,
                    'totalUsers' => $totalUsers,
                    'activeUsers' => $activeUsers,
                    'newUsersThisMonth' => $newUsersThisMonth,
                    'selectedMonth' => $selectedMonth,
                    'revenueByMonthData' => $revenueByMonthData, // THAY ĐỔI: Doanh thu theo tháng
                    'dailyOrdersData' => $dailyOrdersData,
                    'orderStatusData' => $orderStatusData,
                    'topProducts' => $topProducts,
                    'lowStockProducts' => $lowStockProducts
                ]
            ];

            return $this->response->setJSON($data);

        } catch (\Exception $e) {
            log_message('error', 'Dashboard data error: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Lỗi khi tải dữ liệu dashboard',
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * THÊM MỚI: Lấy tổng doanh thu từ các đơn hàng đã giao
     */
    private function getTotalRevenue()
    {
        try {
            $db = \Config\Database::connect();
            
            $query = $db->query("
                SELECT COALESCE(SUM(total_amount), 0) as total_revenue 
                FROM orders 
                WHERE status = 'delivered' 
                AND payment_status = 'paid'
            ");
            
            $result = $query->getRow();
            return $result ? $result->total_revenue : 0;
        } catch (\Exception $e) {
            log_message('error', 'Total revenue error: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * THAY ĐỔI: Lấy doanh thu theo tháng (thay cho trạng thái kho hàng)
     */
    private function getRevenueByMonthData()
    {
        try {
            $db = \Config\Database::connect();
            
            $query = $db->query("
                SELECT 
                    DATE_FORMAT(created_at, '%Y-%m') as month,
                    SUM(total_amount) as monthly_revenue
                FROM orders 
                WHERE status = 'delivered' 
                AND payment_status = 'paid'
                AND created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
                GROUP BY DATE_FORMAT(created_at, '%Y-%m')
                ORDER BY month ASC
            ");
            
            return $query->getResultArray();
        } catch (\Exception $e) {
            log_message('error', 'Revenue by month data error: ' . $e->getMessage());
            return [];
        }
    }

    private function getDailyOrdersData($month)
    {
        try {
            $db = \Config\Database::connect();
            
            $query = $db->query("
                SELECT 
                    DATE(created_at) as date,
                    COUNT(*) as total_orders
                FROM orders 
                WHERE DATE_FORMAT(created_at, '%Y-%m') = ?
                AND status != 'cancelled'
                GROUP BY DATE(created_at)
                ORDER BY date ASC
            ", [$month]);
            
            return $query->getResultArray();
        } catch (\Exception $e) {
            log_message('error', 'Daily orders data error: ' . $e->getMessage());
            return [];
        }
    }

    private function getOrderStatusData()
    {
        try {
            $db = \Config\Database::connect();
            
            $query = $db->query("
                SELECT 
                    status,
                    COUNT(*) as count
                FROM orders 
                GROUP BY status
            ");
            
            $results = $query->getResultArray();
            $statusData = [];
            
            foreach ($results as $row) {
                $statusData[$row['status']] = $row['count'];
            }
            
            return $statusData;
        } catch (\Exception $e) {
            log_message('error', 'Order status data error: ' . $e->getMessage());
            return [];
        }
    }

    private function getTopSellingProducts($limit = 5)
    {
        try {
            $db = \Config\Database::connect();
            
            $query = $db->query("
                SELECT 
                    p.id,
                    p.name,
                    p.sku,
                    COALESCE(SUM(oi.quantity), 0) as total_sold
                FROM products p
                LEFT JOIN order_items oi ON p.id = oi.product_id
                LEFT JOIN orders o ON oi.order_id = o.id
                WHERE (o.status NOT IN ('cancelled') OR o.status IS NULL)
                AND p.is_active = 1
                GROUP BY p.id, p.name, p.sku
                ORDER BY total_sold DESC
                LIMIT ?
            ", [$limit]);
            
            return $query->getResultArray();
        } catch (\Exception $e) {
            log_message('error', 'Top selling products error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * SỬA: Lấy sản phẩm sắp hết hàng từ bảng products
     */
    private function getLowStockProducts($limit = 10)
    {
        try {
            return $this->productModel
                ->select('id, name, sku, stock_quantity, min_stock_level, stock_status')
                ->where('is_active', 1)
                ->where('stock_status', 'low_stock') // Chỉ lấy sản phẩm sắp hết hàng
                ->orWhere('stock_quantity <=', 5) // Hoặc số lượng <= 5
                ->orderBy('stock_quantity', 'ASC')
                ->limit($limit)
                ->findAll();
        } catch (\Exception $e) {
            log_message('error', 'Low stock products error: ' . $e->getMessage());
            return [];
        }
    }
}