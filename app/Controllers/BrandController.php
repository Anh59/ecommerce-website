<?php

namespace App\Controllers;

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
        $brands = $this->brandModel->orderBy('sort_order', 'ASC')->findAll();

        return view('Dashboard/Brand/table', [
            'brands' => $brands
        ]);
    }
    // Danh sách cho DataTables
    public function list()
    {
        if ($this->request->isAJAX()) {
            $brands = $this->brandModel->orderBy('sort_order', 'ASC')->findAll();

            return $this->response->setJSON([
                'status' => 'success',
                'data'   => $brands,   // DataTables cần key "data"
                'token'  => csrf_hash()
            ]);
        }
    }

    // Thêm mới
    public function store()
{
    if ($this->request->isAJAX()) {
        $data = [
            'name'        => $this->request->getPost('name'),
            'slug'        => url_title($this->request->getPost('name'), '-', true),
            'description' => $this->request->getPost('description'),
            'website'     => $this->request->getPost('website'),
            'country'     => $this->request->getPost('country'),
            'is_active'   => $this->request->getPost('is_active') ?? 1,
            'sort_order'  => $this->request->getPost('sort_order') ?? 0,
        ];

        // Xử lý upload logo (nếu có)
        $file = $this->request->getFile('logo_url');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName();
            $file->move('uploads/brand', $newName);
            $data['logo_url'] = 'uploads/brand/' . $newName;
        }

        $this->brandModel->insert($data);

        return $this->response->setJSON(['status' => 'success', 'message' => 'Thêm thương hiệu thành công']);
    }
}
    // Lấy 1 thương hiệu để edit
    public function edit($id)
{
    $brand = $this->brandModel->find($id);

    if (!$brand) {
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Không tìm thấy thương hiệu',
            'token' => csrf_hash()
        ]);
    }

    return $this->response->setJSON([
        'status' => 'success',
        'brand' => $brand,
        'token' => csrf_hash()
    ]);
}


    // Cập nhật
    public function update($id)
{
    if ($this->request->isAJAX()) {
        $data = [
            'name'        => $this->request->getPost('name'),
            'slug'        => url_title($this->request->getPost('name'), '-', true),
            'description' => $this->request->getPost('description'),
            'website'     => $this->request->getPost('website'),
            'country'     => $this->request->getPost('country'),
            'is_active'   => $this->request->getPost('is_active') ?? 1,
            'sort_order'  => $this->request->getPost('sort_order') ?? 0,
        ];

        // Xử lý upload logo mới (nếu có)
        $file = $this->request->getFile('logo_url');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName();
            $file->move('uploads/brand', $newName);
            $data['logo_url'] = 'uploads/brand/' . $newName;

            // Xóa logo cũ (nếu có)
            $old = $this->brandModel->find($id);
            if ($old && !empty($old['logo_url']) && file_exists(FCPATH . $old['logo_url'])) {
                unlink(FCPATH . $old['logo_url']);
            }
        }

        $this->brandModel->update($id, $data);

        return $this->response->setJSON(['status' => 'success', 'message' => 'Cập nhật thương hiệu thành công']);
    }
}


    // Xóa mềm
    public function delete($id)
    {
        if ($this->request->isAJAX()) {
            $this->brandModel->delete($id); // vì có useSoftDeletes = true
            return $this->response->setJSON(['status' => 'success']);
        }
    }
}
