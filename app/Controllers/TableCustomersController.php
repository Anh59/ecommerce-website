<?php

namespace App\Controllers;

use App\Models\CustomerModel;
use CodeIgniter\Controller;

class TableCustomersController extends Controller
{
    protected $customerModel;

    public function __construct()
    {
        $this->customerModel = new CustomerModel();
    }

    public function table()
    {
        $data['customers'] = $this->customerModel->findAll();
        
        // Cập nhật breadcrumb và pageTitle
        $data['pageTitle'] = 'Danh Sách Khách Hàng'; // Tiêu đề trang
        $data['breadcrumb'] = [
            ['title' => 'Thống kê', 'url' => route_to('Dashboard_table')],
            ['title' => 'Khách Hàng', 'url' => route_to('Table_Customers')],
        ];

        return view('Dashboard/Customer/table', $data);
    }

    public function create()
    {
        // Cập nhật breadcrumb và pageTitle
        $data['pageTitle'] = 'Thêm Khách Hàng Mới'; // Tiêu đề trang
        $data['breadcrumb'] = [
            ['title' => 'Thống kê', 'url' => route_to('Dashboard_table')],
            ['title' => 'Khách Hàng', 'url' => route_to('Table_Customers')],
            ['title' => 'Thêm Khách Hàng', 'url' => route_to('Table_Customers_Create')],
        ];

        return view('Dashboard/Customer/Create', $data);
    }

    public function store()
    {
        // Validate input if needed
        $this->customerModel->save($this->request->getPost());

        return redirect()
            ->to(route_to('Table_Customers'))
            ->with('success', 'Khách hàng đã được thêm thành công.');
    }

    public function edit($id)
    {
        $data['customer'] = $this->customerModel->find($id);

        if (!$data['customer']) {
            return redirect()
                ->to(route_to('Table_Customers'))
                ->with('error', 'Không tìm thấy khách hàng.');
        }

        // Cập nhật breadcrumb và pageTitle
        $data['pageTitle'] = 'Chỉnh Sửa Khách Hàng'; // Tiêu đề trang
        $data['breadcrumb'] = [
            ['title' => 'Thống kê', 'url' => route_to('Dashboard_table')],
            ['title' => 'Khách Hàng', 'url' => route_to('Table_Customers')],
            ['title' => 'Chỉnh Sửa Khách Hàng', 'url' => route_to('Table_Customers_Edit', $id)],
        ];

        return view('Dashboard/Customer/Edit', $data);
    }

    public function update($id)
    {
        // Validate input if needed
        $this->customerModel->update($id, $this->request->getPost());

        return redirect()
            ->to(route_to('Table_Customers'))
            ->with('success', 'Thông tin khách hàng đã được cập nhật thành công.');
    }

    public function delete($id)
    {
        $this->customerModel->delete($id);

        return redirect()
            ->to(route_to('Table_Customers'))
            ->with('success', 'Khách hàng đã được xóa thành công.');
    }

    public function lockCustomer($id)
    {
        $customer = $this->customerModel->find($id);

        if ($customer) {
            // Cập nhật trạng thái "locked"
            $this->customerModel->update($id, ['status' => 'locked']);

            // Gửi email thông báo khóa tài khoản
            $email = \Config\Services::email();
            $email->setFrom('your-email@example.com', 'Travel Service Management');
            $email->setTo($customer['email']);
            $email->setSubject('Thông báo khóa tài khoản');
            $email->setMessage("Chào {$customer['name']},\n\nTài khoản của bạn đã bị khóa do vi phạm quy định. Vui lòng liên hệ với chúng tôi để biết thêm chi tiết.");

            if ($email->send()) {
                return redirect()
                    ->to(route_to('Table_Customers'))
                    ->with('success', 'Tài khoản đã được khóa và email thông báo đã được gửi.');
            } else {
                return redirect()
                    ->to(route_to('Table_Customers'))
                    ->with('error', 'Tài khoản đã được khóa nhưng không thể gửi email thông báo.');
            }
        } else {
            return redirect()
                ->to(route_to('Table_Customers'))
                ->with('error', 'Không tìm thấy khách hàng.');
        }
    }
}