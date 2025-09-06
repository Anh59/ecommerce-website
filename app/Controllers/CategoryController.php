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
        $categories = $this->categoryModel->orderBy('sort_order', 'ASC')->findAll();
        return view('Dashboard/Category/table', compact('categories'));
    }

    // Danh sách cho DataTables
    public function list()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400);
        }

        $categories = $this->categoryModel
            ->select('categories.*, parent.name as parent_name')
            ->join('categories as parent', 'categories.parent_id = parent.id', 'left')
            ->orderBy('categories.sort_order', 'ASC')
            ->findAll();

        return $this->response->setJSON([
            'status' => 'success',
            'data'   => $categories,
            'token'  => csrf_hash()
        ]);
    }

    // Lấy danh sách danh mục cha cho dropdown
    public function getParentCategories()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400);
        }

        $parents = $this->categoryModel->getParentCategories();
        return $this->response->setJSON([
            'status' => 'success',
            'data'   => $parents,
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
            'name', 'description', 'parent_id', 'sort_order', 'is_active'
        ]);
        
        // Xử lý parent_id (nếu là 0 thì set null)
        $data['parent_id'] = empty($data['parent_id']) ? null : $data['parent_id'];
        
        // Tạo slug từ name
        $data['slug'] = url_title($data['name'], '-', true);

        // Xử lý upload image
        $this->handleImageUpload($data, $id);

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

    private function handleImageUpload(&$data, $id = null)
    {
        $file = $this->request->getFile('image_url');
        
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName();
            $file->move('uploads/categories', $newName);
            $data['image_url'] = 'uploads/categories/' . $newName;

            // Xóa ảnh cũ khi update
            if ($id && $old = $this->categoryModel->find($id)) {
                $this->deleteOldImage($old['image_url']);
            }
        }
    }

    private function deleteOldImage($imagePath)
    {
        if (!empty($imagePath) && file_exists(FCPATH . $imagePath)) {
            unlink(FCPATH . $imagePath);
        }
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

        // Lấy danh sách parent categories (trừ chính nó và con cháu của nó)
        $parentCategories = $this->categoryModel
            ->where('id !=', $id)
            ->where('parent_id IS NULL OR parent_id = 0')
            ->where('is_active', 1)
            ->orderBy('sort_order', 'ASC')
            ->findAll();

        return $this->response->setJSON([
            'status' => 'success',
            'category' => $category,
            'parentCategories' => $parentCategories,
            'token' => csrf_hash()
        ]);
    }

    // Xóa mềm
    public function delete($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400);
        }

        // Kiểm tra xem có danh mục con không
        $childCount = $this->categoryModel->where('parent_id', $id)->countAllResults();
        if ($childCount > 0) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Không thể xóa danh mục này vì còn danh mục con',
                'token' => csrf_hash()
            ]);
        }

        // Kiểm tra xem danh mục có được sử dụng không (trong bảng products chẳng hạn)
        // Uncomment nếu có bảng products
        /*
        $productModel = new \App\Models\ProductModel();
        if ($productModel->where('category_id', $id)->countAllResults() > 0) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Không thể xóa danh mục này vì đang được sử dụng trong sản phẩm',
                'token' => csrf_hash()
            ]);
        }
        */

        $this->categoryModel->delete($id); // Xóa mềm
        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Xóa danh mục thành công',
            'token' => csrf_hash()
        ]);
    }
}