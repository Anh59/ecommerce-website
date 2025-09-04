<?php

namespace App\Controllers\Dashboard;

use App\Controllers\BaseController;
use App\Models\BrandModel;

class BrandController extends BaseController
{
    protected $brandModel;

    public function __construct()
    {
        $this->brandModel = new BrandModel();
    }

    // Danh sách
    public function index()
    {
        $brands = $this->brandModel->findAll();

        return view('Dashboard/Brands/table', [
            'brands' => $brands
        ]);
    }

    // Thêm mới
    public function store()
    {
        if ($this->request->isAJAX()) {
            $data = [
                'name'        => $this->request->getPost('name'),
                'slug'        => url_title($this->request->getPost('name'), '-', true),
                'description' => $this->request->getPost('description'),
                'status'      => $this->request->getPost('status'),
            ];

            $this->brandModel->insert($data);

            return $this->response->setJSON(['status' => 'success']);
        }
    }

    // Cập nhật
    public function update($id)
    {
        if ($this->request->isAJAX()) {
            $data = [
                'name'        => $this->request->getPost('name'),
                'slug'        => url_title($this->request->getPost('name'), '-', true),
                'description' => $this->request->getPost('description'),
                'status'      => $this->request->getPost('status'),
            ];

            $this->brandModel->update($id, $data);

            return $this->response->setJSON(['status' => 'success']);
        }
    }

    // Xóa
    public function delete($id)
    {
        if ($this->request->isAJAX()) {
            $this->brandModel->delete($id);
            return $this->response->setJSON(['status' => 'success']);
        }
    }
}
