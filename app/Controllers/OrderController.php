<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\OrderModel;
use App\Models\OrderItemModel;

class OrderController extends BaseController
{
    protected $orderModel;
    protected $orderItemModel;

    public function __construct()
    {
        $this->orderModel = new OrderModel();
        $this->orderItemModel = new OrderItemModel();
    }

    // Danh sách đơn hàng
    public function index()
    {
        return view('Dashboard/Orders/table');
    }

    // Danh sách cho DataTables
    public function list()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400);
        }

        $status = $this->request->getGet('status') ?? 'all';
        $paymentStatus = $this->request->getGet('payment_status') ?? 'all';
        $search = $this->request->getGet('search') ?? '';
        $date = $this->request->getGet('date') ?? date('Y-m-d');

        // Build query
        $builder = $this->orderModel->select('orders.*, 
            customers.name as customer_name, 
            customers.email as customer_email, 
            customers.phone as customer_phone,
            (SELECT COUNT(*) FROM order_items WHERE order_items.order_id = orders.id) as total_items')
           ->join('customers', 'customers.id = orders.customer_id', 'left');

        // Filter by status
        if ($status !== 'all') {
            $builder->where('orders.status', $status);
        }

        // Filter by payment status
        if ($paymentStatus !== 'all') {
            $builder->where('orders.payment_status', $paymentStatus);
        }

        // Filter by date
        if ($date) {
            $builder->where('DATE(orders.created_at)', $date);
        }

        // Search
        if (!empty($search)) {
            $builder->groupStart()
                   ->like('orders.order_number', $search)
                   ->orLike('customers.name', $search)
                   ->orLike('customers.phone', $search)
                   ->orLike('orders.notes', $search)
                   ->groupEnd();
        }

        $orders = $builder->orderBy('orders.created_at', 'DESC')
                         ->findAll();

        return $this->response->setJSON([
            'status' => 'success',
            'data'   => $orders,
            'token'  => csrf_hash()
        ]);
    }

    // Chi tiết đơn hàng
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

    // Cập nhật đơn hàng
   // Cập nhật đơn hàng
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

    $data = $this->request->getPost([
        'status', 'payment_status', 'tracking_number', 'notes'
    ]);

    // Additional data for specific status changes
    $additionalData = [];
    if ($data['status'] === 'shipped' && $order['status'] !== 'shipped') {
        $additionalData['shipped_at'] = date('Y-m-d H:i:s');
    } elseif ($data['status'] === 'delivered' && $order['status'] !== 'delivered') {
        $additionalData['delivered_at'] = date('Y-m-d H:i:s');
    }

    try {
        if ($this->orderModel->update($id, array_merge($data, $additionalData))) {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Cập nhật đơn hàng thành công',
                'token' => csrf_hash()
            ]);
        } else {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Lỗi khi cập nhật đơn hàng',
                'errors' => $this->orderModel->errors(),
                'token' => csrf_hash()
            ]);
        }
    } catch (\Exception $e) {
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Lỗi hệ thống: ' . $e->getMessage(),
            'token' => csrf_hash()
        ]);
    }
}

    // In đơn hàng
  // Trong phương thức print() của OrderController
public function print($id)
{
    $order = $this->orderModel->getOrderWithItems($id);
    
    if (!$order) {
        throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
    }

    // Lấy thông tin khách hàng nếu có
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

    // Thống kê đơn hàng
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

    // Xuất Excel đơn hàng
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

        $orders = $builder->orderBy('orders.created_at', 'DESC')
                         ->findAll();

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

    private function getStatusText($status)
    {
        $statusMap = [
            'pending' => 'Chờ xử lý',
            'processing' => 'Đang xử lý',
            'shipped' => 'Đang giao',
            'delivered' => 'Đã giao',
            'cancelled' => 'Đã hủy'
        ];
        return $statusMap[$status] ?? $status;
    }

    private function getPaymentStatusText($status)
    {
        $statusMap = [
            'pending' => 'Chờ thanh toán',
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