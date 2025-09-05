<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\CategoryModel;

class CategoryController extends BaseController
{
    protected $categoryModel;

    public function __construct()
    {
        $this->categoryModel = new CategoryModel();
    }

    // Danh sách
    public function index()
    {
        $categories = $this->categoryModel->orderBy('name', 'ASC')->findAll();
        return view('Dashboard/Category/table', compact('categories'));
    }

    // Danh sách cho DataTables
    public function list()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400);
        }

        $categories = $this->categoryModel->orderBy('name', 'ASC')->findAll();
        return $this->response->setJSON([
            'status' => 'success',
            'data'   => $categories,
            'token'  => csrf_hash()
        ]);
    }

    // Tối ưu: Gộp chung logic store/update
    public function store()
    {
        return $this->saveData();
    }

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
            ? $this->categoryModel->rulesForUpdate($id)
            : $this->categoryModel->rulesForInsert();

        if (!$this->validate($rules, $messages)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => $this->validator->getErrors(),
                'token'   => csrf_hash()
            ]);
        }

        // Lấy dữ liệu
        $data = $this->request->getPost([
            'name', 'description', 'status'
        ]);
        $data['slug'] = url_title($data['name'], '-', true);

        // Lưu hoặc cập nhật
        if ($isUpdate) {
            $this->categoryModel->update($id, $data);
            $message = 'Cập nhật danh mục thành công';
        } else {
            $this->categoryModel->insert($data);
            $message = 'Thêm danh mục thành công';
        }

        return $this->response->setJSON([
            'status' => 'success',
            'message' => $message,
            'token'   => csrf_hash()
        ]);
    }

    // Lấy 1 danh mục để edit
    public function edit($id)
    {
        $category = $this->categoryModel->find($id);

        if (!$category) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Không tìm thấy danh mục',
                'token' => csrf_hash()
            ]);
        }

        return $this->response->setJSON([
            'status' => 'success',
            'category' => $category,
            'token' => csrf_hash()
        ]);
    }

    // Xóa cứng (vì useSoftDeletes = false)
    public function delete($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400);
        }

        // Kiểm tra xem danh mục có được sử dụng không (nếu có bảng liên quan)
        // Ví dụ: kiểm tra trong bảng products
        // $productModel = new ProductModel();
        // if ($productModel->where('category_id', $id)->countAllResults() > 0) {
        //     return $this->response->setJSON([
        //         'status' => 'error',
        //         'message' => 'Không thể xóa danh mục này vì đang được sử dụng',
        //         'token' => csrf_hash()
        //     ]);
        // }

        $this->categoryModel->delete($id);
        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Xóa danh mục thành công',
            'token' => csrf_hash()
        ]);
    }
}