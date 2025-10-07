<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\OrderModel;
use App\Models\OrderItemModel;
use App\Models\ProductModel;

class OrderController extends BaseController
{
    protected $orderModel;
    protected $orderItemModel;
    protected $productModel;

    public function __construct()
    {
        $this->orderModel = new OrderModel();
        $this->orderItemModel = new OrderItemModel();
        $this->productModel = new ProductModel();
    }

    // ... các method khác giữ nguyên (index, list, details, print, stats, export) ...

    public function index()
    {
        return view('Dashboard/Orders/table');
    }

    public function list()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400);
        }

        $status = $this->request->getGet('status') ?? 'all';
        $paymentStatus = $this->request->getGet('payment_status') ?? 'all';
        $search = $this->request->getGet('search') ?? '';
        $date = $this->request->getGet('date') ?? date('Y-m-d');

        $builder = $this->orderModel->select('orders.*, 
            customers.name as customer_name, 
            customers.email as customer_email, 
            customers.phone as customer_phone,
            (SELECT COUNT(*) FROM order_items WHERE order_items.order_id = orders.id) as total_items')
           ->join('customers', 'customers.id = orders.customer_id', 'left');

        if ($status !== 'all') {
            $builder->where('orders.status', $status);
        }

        if ($paymentStatus !== 'all') {
            $builder->where('orders.payment_status', $paymentStatus);
        }

        if ($date) {
            $builder->where('DATE(orders.created_at)', $date);
        }

        if (!empty($search)) {
            $builder->groupStart()
                   ->like('orders.order_number', $search)
                   ->orLike('customers.name', $search)
                   ->orLike('customers.phone', $search)
                   ->orLike('orders.notes', $search)
                   ->groupEnd();
        }

        $orders = $builder->orderBy('orders.created_at', 'DESC')->findAll();

        return $this->response->setJSON([
            'status' => 'success',
            'data'   => $orders,
            'token'  => csrf_hash()
        ]);
    }

    public function details($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400);
        }

        $order = $this->orderModel->getOrderWithItems($id);

        if (!$order) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Không tìm thấy đơn hàng',
                'token' => csrf_hash()
            ]);
        }

        return $this->response->setJSON([
            'status' => 'success',
            'order' => $order,
            'token' => csrf_hash()
        ]);
    }

    /**
     * ===== LOGIC CẬP NHẬT ĐƠN HÀNG THEO QUY TRÌNH MỚI =====
     */
    public function update($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400);
        }

        $order = $this->orderModel->find($id);
        if (!$order) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Không tìm thấy đơn hàng',
                'token' => csrf_hash()
            ]);
        }

        $oldStatus = $order['status'];
        $newStatus = $this->request->getPost('status');
        $newPaymentStatus = $this->request->getPost('payment_status');
        $trackingNumber = $this->request->getPost('tracking_number');
        $notes = $this->request->getPost('notes');

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // ===== XỬ LÝ CHUYỂN TRẠNG THÁI =====
            $statusChangeResult = $this->handleStatusChange($id, $oldStatus, $newStatus, $order);
            
            if (!$statusChangeResult['success']) {
                $db->transRollback();
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => $statusChangeResult['message'],
                    'token' => csrf_hash()
                ]);
            }

            // ===== CẬP NHẬT DỮ LIỆU ĐƠN HÀNG =====
            $updateData = [
                'status' => $newStatus,
                'payment_status' => $newPaymentStatus,
                'tracking_number' => $trackingNumber,
                'notes' => $notes
            ];

            // Thêm timestamps cho các trạng thái cụ thể
            if ($newStatus === 'shipped' && $oldStatus !== 'shipped') {
                $updateData['shipped_at'] = date('Y-m-d H:i:s');
            } elseif ($newStatus === 'delivered' && $oldStatus !== 'delivered') {
                $updateData['delivered_at'] = date('Y-m-d H:i:s');
                
                // Tự động cập nhật payment_status = paid nếu COD
                if ($order['payment_method'] === 'cod' && $order['payment_status'] === 'pending') {
                    $updateData['payment_status'] = 'paid';
                }
            }

            $this->orderModel->update($id, $updateData);

            $db->transComplete();

            if ($db->transStatus() === FALSE) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Lỗi khi cập nhật đơn hàng',
                    'token' => csrf_hash()
                ]);
            }

            log_message('info', "Order #{$order['order_number']} status changed: {$oldStatus} → {$newStatus}");

            return $this->response->setJSON([
                'status' => 'success',
                'message' => $statusChangeResult['message'],
                'token' => csrf_hash()
            ]);

        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'Error updating order: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Lỗi hệ thống: ' . $e->getMessage(),
                'token' => csrf_hash()
            ]);
        }
    }

    /**
     * Xử lý logic chuyển trạng thái đơn hàng
     */
    private function handleStatusChange($orderId, $oldStatus, $newStatus, $order)
    {
        // Nếu không đổi trạng thái
        if ($oldStatus === $newStatus) {
            return ['success' => true, 'message' => 'Cập nhật đơn hàng thành công'];
        }

        // ===== PENDING → PROCESSING (XÁC NHẬN ĐƠN, TRỪ KHO) =====
        if ($oldStatus === 'pending' && $newStatus === 'processing') {
            $deductResult = $this->deductStock($orderId);
            if (!$deductResult['success']) {
                return [
                    'success' => false,
                    'message' => 'Không thể xác nhận đơn: ' . $deductResult['message']
                ];
            }
            return [
                'success' => true,
                'message' => 'Đã xác nhận đơn hàng và trừ kho thành công'
            ];
        }

        // ===== PROCESSING → SHIPPED (CHUYỂN CHO SHIPPER) =====
        if ($oldStatus === 'processing' && $newStatus === 'shipped') {
            return [
                'success' => true,
                'message' => 'Đơn hàng đã được giao cho đơn vị vận chuyển'
            ];
        }

        // ===== SHIPPED → DELIVERED (GIAO THÀNH CÔNG) =====
        if ($oldStatus === 'shipped' && $newStatus === 'delivered') {
            return [
                'success' => true,
                'message' => 'Đơn hàng đã được giao thành công'
            ];
        }

        // ===== HỦY ĐƠN HÀNG =====
        if ($newStatus === 'cancelled') {
            // Nếu đã xác nhận (đã trừ kho) → hoàn kho
            if (in_array($oldStatus, ['processing', 'shipped'])) {
                $restoreResult = $this->restoreStock($orderId);
                if (!$restoreResult['success']) {
                    return [
                        'success' => false,
                        'message' => 'Không thể hủy đơn: ' . $restoreResult['message']
                    ];
                }
                return [
                    'success' => true,
                    'message' => 'Đã hủy đơn hàng và hoàn lại kho thành công'
                ];
            }
            
            // Nếu chưa xác nhận (pending) → hủy trực tiếp
            return [
                'success' => true,
                'message' => 'Đã hủy đơn hàng'
            ];
        }

        // ===== KHÔNG CHO PHÉP CHUYỂN NGƯỢC LẠI =====
        $statusFlow = ['pending', 'processing', 'shipped', 'delivered'];
        $oldIndex = array_search($oldStatus, $statusFlow);
        $newIndex = array_search($newStatus, $statusFlow);

        if ($oldIndex !== false && $newIndex !== false && $newIndex < $oldIndex) {
            return [
                'success' => false,
                'message' => 'Không thể chuyển đơn hàng từ trạng thái "' . $this->getStatusText($oldStatus) . 
                            '" về "' . $this->getStatusText($newStatus) . '"'
            ];
        }

        return ['success' => true, 'message' => 'Cập nhật trạng thái đơn hàng thành công'];
    }

    /**
     * Trừ kho khi xác nhận đơn (pending → processing)
     */
    private function deductStock($orderId)
    {
        $orderItems = $this->orderItemModel->getOrderItems($orderId);
        
        if (empty($orderItems)) {
            return ['success' => false, 'message' => 'Không tìm thấy sản phẩm trong đơn hàng'];
        }

        $errors = [];

        foreach ($orderItems as $item) {
            $product = $this->productModel->find($item['product_id']);
            
            if (!$product) {
                $errors[] = "Sản phẩm {$item['product_name']} không tồn tại";
                continue;
            }

            // Kiểm tra tồn kho
            if ($product['stock_quantity'] < $item['quantity']) {
                $errors[] = "Sản phẩm {$item['product_name']} không đủ hàng (còn {$product['stock_quantity']}, cần {$item['quantity']})";
                continue;
            }

            // Trừ kho
            $newStock = $product['stock_quantity'] - $item['quantity'];
            $stockStatus = $this->determineStockStatus($newStock, $product['min_stock_level'] ?? 0);
            
            $this->productModel->update($item['product_id'], [
                'stock_quantity' => $newStock,
                'stock_status' => $stockStatus
            ]);

            log_message('info', "Deducted stock for product #{$item['product_id']}: {$item['quantity']} units. New stock: {$newStock}");
        }

        if (!empty($errors)) {
            return ['success' => false, 'message' => implode(', ', $errors)];
        }

        return ['success' => true, 'message' => 'Trừ kho thành công'];
    }

    /**
     * Hoàn kho khi hủy đơn đã xác nhận
     */
    private function restoreStock($orderId)
    {
        $orderItems = $this->orderItemModel->getOrderItems($orderId);
        
        if (empty($orderItems)) {
            return ['success' => false, 'message' => 'Không tìm thấy sản phẩm trong đơn hàng'];
        }

        foreach ($orderItems as $item) {
            $product = $this->productModel->find($item['product_id']);
            
            if (!$product) {
                log_message('warning', "Product #{$item['product_id']} not found when restoring stock");
                continue;
            }

            // Hoàn kho
            $newStock = $product['stock_quantity'] + $item['quantity'];
            $stockStatus = $this->determineStockStatus($newStock, $product['min_stock_level'] ?? 0);
            
            $this->productModel->update($item['product_id'], [
                'stock_quantity' => $newStock,
                'stock_status' => $stockStatus
            ]);

            log_message('info', "Restored stock for product #{$item['product_id']}: {$item['quantity']} units. New stock: {$newStock}");
        }

        return ['success' => true, 'message' => 'Hoàn kho thành công'];
    }

    /**
     * Xác định trạng thái tồn kho
     */
    private function determineStockStatus($quantity, $minLevel = 0)
    {
        if ($quantity <= 0) {
            return 'out_of_stock';
        } elseif ($quantity <= $minLevel) {
            return 'low_stock';
        } else {
            return 'in_stock';
        }
    }

    /**
     * Lấy text hiển thị của trạng thái
     */
    private function getStatusText($status)
    {
        $statusMap = [
            'pending' => 'Chờ xử lý',
            'processing' => 'Xác nhận',
            'shipped' => 'Đang giao',
            'delivered' => 'Đã giao',
            'cancelled' => 'Đã hủy'
        ];
        return $statusMap[$status] ?? $status;
    }

    // ... các method khác giữ nguyên (print, stats, export, etc.) ...

    public function print($id)
    {
        $order = $this->orderModel->getOrderWithItems($id);
        
        if (!$order) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $customer = null;
        if ($order['customer_id']) {
            $customerModel = new \App\Models\CustomerModel();
            $customer = $customerModel->find($order['customer_id']);
        }

        return view('Dashboard/Orders/print', [
            'order' => $order,
            'customer' => $customer
        ]);
    }

    public function stats()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400);
        }

        $period = $this->request->getGet('period') ?? 'today';

        $stats = [
            'total_orders' => 0,
            'total_revenue' => 0,
            'pending_orders' => 0,
            'processing_orders' => 0
        ];

        switch ($period) {
            case 'today':
                $stats = $this->getTodayStats();
                break;
            case 'week':
                $stats = $this->getWeekStats();
                break;
            case 'month':
                $stats = $this->getMonthStats();
                break;
        }

        return $this->response->setJSON([
            'status' => 'success',
            'stats' => $stats,
            'token' => csrf_hash()
        ]);
    }

    private function getTodayStats()
    {
        $today = date('Y-m-d');
        
        $builder = $this->orderModel->where('DATE(created_at)', $today)
                                   ->where('status !=', 'cancelled');

        $totalOrders = $builder->countAllResults(false);
        $totalRevenue = $builder->selectSum('total_amount')->get()->getRow()->total_amount ?? 0;

        $pendingOrders = $this->orderModel->where('DATE(created_at)', $today)
                                         ->where('status', 'pending')
                                         ->countAllResults();

        $processingOrders = $this->orderModel->where('DATE(created_at)', $today)
                                            ->where('status', 'processing')
                                            ->countAllResults();

        return [
            'total_orders' => $totalOrders,
            'total_revenue' => $totalRevenue,
            'pending_orders' => $pendingOrders,
            'processing_orders' => $processingOrders
        ];
    }

    private function getWeekStats()
    {
        $startOfWeek = date('Y-m-d', strtotime('monday this week'));
        $endOfWeek = date('Y-m-d', strtotime('sunday this week'));
        
        $builder = $this->orderModel->where('created_at >=', $startOfWeek)
                                   ->where('created_at <=', $endOfWeek . ' 23:59:59')
                                   ->where('status !=', 'cancelled');

        $totalOrders = $builder->countAllResults(false);
        $totalRevenue = $builder->selectSum('total_amount')->get()->getRow()->total_amount ?? 0;

        $pendingOrders = $this->orderModel->where('created_at >=', $startOfWeek)
                                         ->where('created_at <=', $endOfWeek . ' 23:59:59')
                                         ->where('status', 'pending')
                                         ->countAllResults();

        $processingOrders = $this->orderModel->where('created_at >=', $startOfWeek)
                                            ->where('created_at <=', $endOfWeek . ' 23:59:59')
                                            ->where('status', 'processing')
                                            ->countAllResults();

        return [
            'total_orders' => $totalOrders,
            'total_revenue' => $totalRevenue,
            'pending_orders' => $pendingOrders,
            'processing_orders' => $processingOrders
        ];
    }

    private function getMonthStats()
    {
        $startOfMonth = date('Y-m-01');
        $endOfMonth = date('Y-m-t');
        
        $builder = $this->orderModel->where('created_at >=', $startOfMonth)
                                   ->where('created_at <=', $endOfMonth . ' 23:59:59')
                                   ->where('status !=', 'cancelled');

        $totalOrders = $builder->countAllResults(false);
        $totalRevenue = $builder->selectSum('total_amount')->get()->getRow()->total_amount ?? 0;

        $pendingOrders = $this->orderModel->where('created_at >=', $startOfMonth)
                                         ->where('created_at <=', $endOfMonth . ' 23:59:59')
                                         ->where('status', 'pending')
                                         ->countAllResults();

        $processingOrders = $this->orderModel->where('created_at >=', $startOfMonth)
                                            ->where('created_at <=', $endOfMonth . ' 23:59:59')
                                            ->where('status', 'processing')
                                            ->countAllResults();

        return [
            'total_orders' => $totalOrders,
            'total_revenue' => $totalRevenue,
            'pending_orders' => $pendingOrders,
            'processing_orders' => $processingOrders
        ];
    }

    public function export()
    {
        $startDate = $this->request->getGet('start_date');
        $endDate = $this->request->getGet('end_date');
        $status = $this->request->getGet('status');

        $builder = $this->orderModel->select('orders.*, customers.name as customer_name, customers.phone as customer_phone')
                                   ->join('customers', 'customers.id = orders.customer_id', 'left');

        if ($startDate) {
            $builder->where('DATE(orders.created_at) >=', $startDate);
        }

        if ($endDate) {
            $builder->where('DATE(orders.created_at) <=', $endDate);
        }

        if ($status && $status !== 'all') {
            $builder->where('orders.status', $status);
        }

        $orders = $builder->orderBy('orders.created_at', 'DESC')->findAll();

        // Tạo file Excel
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header
        $sheet->setCellValue('A1', 'Mã đơn hàng');
        $sheet->setCellValue('B1', 'Khách hàng');
        $sheet->setCellValue('C1', 'Số điện thoại');
        $sheet->setCellValue('D1', 'Ngày đặt');
        $sheet->setCellValue('E1', 'Tổng tiền');
        $sheet->setCellValue('F1', 'Trạng thái');
        $sheet->setCellValue('G1', 'Thanh toán');
        $sheet->setCellValue('H1', 'Phương thức');

        // Data
        $row = 2;
        foreach ($orders as $order) {
            $sheet->setCellValue('A' . $row, $order['order_number']);
            $sheet->setCellValue('B' . $row, $order['customer_name']);
            $sheet->setCellValue('C' . $row, $order['customer_phone']);
            $sheet->setCellValue('D' . $row, $order['created_at']);
            $sheet->setCellValue('E' . $row, $order['total_amount']);
            $sheet->setCellValue('F' . $row, $this->getStatusText($order['status']));
            $sheet->setCellValue('G' . $row, $this->getPaymentStatusText($order['payment_status']));
            $sheet->setCellValue('H' . $row, $this->getPaymentMethodText($order['payment_method']));
            $row++;
        }

        // Auto size columns
        foreach (range('A', 'H') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Output
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="orders_export.xlsx"');
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output');
        exit;
    }

    private function getPaymentStatusText($status)
    {
        $statusMap = [
            'pending' => 'Chưa thanh toán',
            'paid' => 'Đã thanh toán',
            'failed' => 'Thất bại',
            'refunded' => 'Đã hoàn tiền'
        ];
        return $statusMap[$status] ?? $status;
    }

    private function getPaymentMethodText($method)
    {
        $methodMap = [
            'cod' => 'COD',
            'momo' => 'Momo',
            'bank_transfer' => 'Chuyển khoản'
        ];
        return $methodMap[$method] ?? $method;
    }
}