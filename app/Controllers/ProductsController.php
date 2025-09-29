<?php
namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ProductModel;
use App\Models\ProductImageModel;
use App\Models\StockMovementModel;
use App\Models\BrandModel;
use App\Models\CategoryModel;

class ProductsController extends BaseController
{
    protected $productModel;
    protected $imageModel;

    public function __construct()
    {
        $this->productModel = new ProductModel();
        $this->imageModel = new ProductImageModel();
        helper(['text','url']);
    }

    // 1. Trang danh sách
    public function index()
    {
        $data['brands'] = (new BrandModel())->where('is_active',1)->findAll();
        $data['categories'] = (new CategoryModel())->where('is_active',1)->findAll();
        return view('Dashboard/product/Table', $data);
    }

    // 2. Lấy data JSON (dùng DataTables)
    public function list()
    {
        $products = $this->productModel
            ->select('products.*, categories.name as category_name, brands.name as brand_name')
            ->join('categories','categories.id = products.category_id','left')
            ->join('brands','brands.id = products.brand_id','left')
            ->findAll();

        return $this->response->setJSON([
            'data' => $products,
            'token' => csrf_hash()
        ]);
    }

    // 3. Lưu mới - ĐÃ SỬA
    public function store()
    {
        $validation = \Config\Services::validation();
        $rules = [
            'name' => 'required|max_length[255]',
            'slug' => 'required|max_length[255]|is_unique[products.slug]',
            'sku'  => 'required|max_length[100]|is_unique[products.sku]',
            'price'=> 'required|numeric|greater_than[0]',
            'category_id' => 'required|integer'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'status' => 'error',
                'errors' => $validation->getErrors(),
                'token' => csrf_hash()
            ]);
        }

        $post = $this->request->getPost();

        // Set default values
        if (!isset($post['is_active'])) $post['is_active'] = 1;
        if (!isset($post['is_featured'])) $post['is_featured'] = 0;
        if (!isset($post['stock_quantity'])) $post['stock_quantity'] = 0;
        if (!isset($post['min_stock_level'])) $post['min_stock_level'] = 0;
        if (!isset($post['stock_status'])) $post['stock_status'] = 'in_stock';

        // Xử lý specifications - PHIÊN BẢN MỚI
        $specifications = [];

        // Thông số kỹ thuật cơ bản
        if (!empty($post['spec_power'])) $specifications['power'] = $post['spec_power'];
        if (!empty($post['spec_capacity'])) $specifications['capacity'] = $post['spec_capacity'];
        if (!empty($post['spec_voltage'])) $specifications['voltage'] = $post['spec_voltage'];
        if (!empty($post['spec_frequency'])) $specifications['frequency'] = $post['spec_frequency'];
        if (!empty($post['spec_screen_size'])) $specifications['screen_size'] = $post['spec_screen_size'];
        if (!empty($post['spec_color'])) $specifications['color'] = $post['spec_color'];

        // Thông số bổ sung từ textarea
        if (!empty($post['spec_other'])) {
            $otherSpecs = explode("\n", $post['spec_other']);
            foreach ($otherSpecs as $spec) {
                $spec = trim($spec);
                if (!empty($spec)) {
                    $parts = explode(':', $spec, 2);
                    if (count($parts) == 2) {
                        $key = trim($parts[0]);
                        $value = trim($parts[1]);
                        if (!empty($key) && !empty($value)) {
                            $specifications[$key] = $value;
                        }
                    }
                }
            }
        }
        
        if (!empty($specifications)) {
            $post['specifications'] = json_encode($specifications, JSON_UNESCAPED_UNICODE);
        }

        // Xử lý dimensions
        $dimensions = [];
        if (!empty($post['dimension_length'])) $dimensions['length'] = (float)$post['dimension_length'];
        if (!empty($post['dimension_width'])) $dimensions['width'] = (float)$post['dimension_width'];
        if (!empty($post['dimension_height'])) $dimensions['height'] = (float)$post['dimension_height'];
        
        if (!empty($dimensions)) {
            $post['dimensions'] = json_encode($dimensions, JSON_UNESCAPED_UNICODE);
        }

        // Xử lý main image
        $mainImage = $this->request->getFile('main_image');
        if ($mainImage && $mainImage->isValid() && !$mainImage->hasMoved()) {
            $newName = $mainImage->getRandomName();
            $mainImage->move(FCPATH . 'uploads/products', $newName);
            $post['main_image'] = 'uploads/products/' . $newName;
        }

        // Clean post data - remove temporary fields
        $fieldsToRemove = [
            'spec_power', 'spec_capacity', 'spec_voltage', 'spec_frequency', 
            'spec_screen_size', 'spec_color', 'spec_other',
            'dimension_length', 'dimension_width', 'dimension_height'
        ];

        foreach ($fieldsToRemove as $field) {
            unset($post[$field]);
        }

        // Set timestamps
        $post['created_at'] = date('Y-m-d H:i:s');
        $post['updated_at'] = date('Y-m-d H:i:s');

        // Lưu product
        try {
            $id = $this->productModel->insert($post);
            
            if (!$id) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Không thể tạo sản phẩm',
                    'errors' => $this->productModel->errors(),
                    'token' => csrf_hash()
                ]);
            }
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Lỗi: ' . $e->getMessage(),
                'token' => csrf_hash()
            ]);
        }

        // Lưu product_images nếu có files multiple
        $files = $this->request->getFiles();
        if (!empty($files['images'])) {
            foreach ($files['images'] as $f) {
                if ($f && $f->isValid()) {
                    $fname = $f->getRandomName();
                    $f->move(FCPATH . 'uploads/products', $fname);
                    $this->imageModel->insert([
                        'product_id' => $id,
                        'image_url'  => 'uploads/products/' . $fname,
                    ]);
                }
            }
        }

        // Stock movement cho stock ban đầu
        if (!empty($post['stock_quantity']) && (int)$post['stock_quantity'] > 0) {
            (new StockMovementModel())->insert([
                'product_id' => $id,
                'type' => 'in',
                'quantity' => (int)$post['stock_quantity'],
                'reason' => 'initial_stock',
                'created_at' => date('Y-m-d H:i:s')
            ]);
        }

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Tạo sản phẩm thành công',
            'token' => csrf_hash()
        ]);
    }

    // 4. Edit (lấy data để fill form)
    public function edit($id)
    {
        $product = $this->productModel->find($id);
        if (!$product) {
            return $this->response->setJSON([
                'status'=>'error',
                'message'=>'Không tìm thấy sản phẩm',
                'token'=>csrf_hash()
            ]);
        }

        // Parse specifications và dimensions
        if (!empty($product['specifications'])) {
            $product['specifications_parsed'] = json_decode($product['specifications'], true);
        }
        if (!empty($product['dimensions'])) {
            $product['dimensions_parsed'] = json_decode($product['dimensions'], true);
        }

        $images = $this->imageModel->where('product_id',$id)->findAll();
        
        return $this->response->setJSON([
            'status'=>'success',
            'product'=>$product,
            'images'=>$images,
            'token'=>csrf_hash()
        ]);
    }

    // 5. Update - ĐÃ SỬA
    public function update($id)
    {
        // Kiểm tra product tồn tại trước
        $oldProduct = $this->productModel->find($id);
        if (!$oldProduct) {
            return $this->response->setJSON([
                'status'=>'error',
                'message'=>'Không tìm thấy sản phẩm',
                'token'=>csrf_hash()
            ]);
        }

        // Validation với exclude current record
        $validation = \Config\Services::validation();
        $rules = [
            'name' => 'required|max_length[255]',
            'slug' => "required|max_length[255]|is_unique[products.slug,id,{$id}]",
            'sku'  => "required|max_length[100]|is_unique[products.sku,id,{$id}]",
            'price'=> 'required|numeric|greater_than[0]',
            'category_id' => 'required|integer'
        ];
        
        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'status'=>'error',
                'errors'=>$validation->getErrors(),
                'token'=>csrf_hash()
            ]);
        }

        $post = $this->request->getPost();

        // Set default values for update
        if (!isset($post['is_active'])) $post['is_active'] = $oldProduct['is_active'];
        if (!isset($post['is_featured'])) $post['is_featured'] = 0;

        // Xử lý specifications - PHIÊN BẢN MỚI
        $specifications = [];

        // Thông số kỹ thuật cơ bản
        if (!empty($post['spec_power'])) $specifications['power'] = $post['spec_power'];
        if (!empty($post['spec_capacity'])) $specifications['capacity'] = $post['spec_capacity'];
        if (!empty($post['spec_voltage'])) $specifications['voltage'] = $post['spec_voltage'];
        if (!empty($post['spec_frequency'])) $specifications['frequency'] = $post['spec_frequency'];
        if (!empty($post['spec_screen_size'])) $specifications['screen_size'] = $post['spec_screen_size'];
        if (!empty($post['spec_color'])) $specifications['color'] = $post['spec_color'];

        // Thông số bổ sung từ textarea
        if (!empty($post['spec_other'])) {
            $otherSpecs = explode("\n", $post['spec_other']);
            foreach ($otherSpecs as $spec) {
                $spec = trim($spec);
                if (!empty($spec)) {
                    $parts = explode(':', $spec, 2);
                    if (count($parts) == 2) {
                        $key = trim($parts[0]);
                        $value = trim($parts[1]);
                        if (!empty($key) && !empty($value)) {
                            $specifications[$key] = $value;
                        }
                    }
                }
            }
        }
        
        if (!empty($specifications)) {
            $post['specifications'] = json_encode($specifications, JSON_UNESCAPED_UNICODE);
        }

        // Xử lý dimensions
        $dimensions = [];
        if (!empty($post['dimension_length'])) $dimensions['length'] = (float)$post['dimension_length'];
        if (!empty($post['dimension_width'])) $dimensions['width'] = (float)$post['dimension_width'];
        if (!empty($post['dimension_height'])) $dimensions['height'] = (float)$post['dimension_height'];
        
        if (!empty($dimensions)) {
            $post['dimensions'] = json_encode($dimensions, JSON_UNESCAPED_UNICODE);
        }

        // Xử lý main image
        $mainImage = $this->request->getFile('main_image');
        if ($mainImage && $mainImage->isValid() && !$mainImage->hasMoved()) {
            // Xóa ảnh cũ nếu có
            if (!empty($oldProduct['main_image']) && file_exists(FCPATH . $oldProduct['main_image'])) {
                unlink(FCPATH . $oldProduct['main_image']);
            }
            
            $newName = $mainImage->getRandomName();
            $mainImage->move(FCPATH . 'uploads/products', $newName);
            $post['main_image'] = 'uploads/products/' . $newName;
        }

        // Xử lý ảnh phụ mới
        $files = $this->request->getFiles();
        if (!empty($files['images'])) {
            foreach ($files['images'] as $f) {
                if ($f && $f->isValid()) {
                    $fname = $f->getRandomName();
                    $f->move(FCPATH . 'uploads/products', $fname);
                    $this->imageModel->insert([
                        'product_id' => $id,
                        'image_url'  => 'uploads/products/' . $fname,
                    ]);
                }
            }
        }

        // Stock movement nếu stock_quantity thay đổi
        if (isset($post['stock_quantity']) && (int)$post['stock_quantity'] != (int)$oldProduct['stock_quantity']) {
            $diff = (int)$post['stock_quantity'] - (int)$oldProduct['stock_quantity'];
            if ($diff != 0) {
                try {
                    (new StockMovementModel())->insert([
                        'product_id' => $id,
                        'type' => $diff > 0 ? 'in' : 'out',
                        'quantity' => abs($diff),
                        'reason' => 'manual_adjustment',
                        'created_at' => date('Y-m-d H:i:s')
                    ]);
                } catch (\Exception $e) {
                    log_message('error', 'Failed to create stock movement: ' . $e->getMessage());
                }
            }
        }

        // Clean post data - remove temporary fields
        $fieldsToRemove = [
            'spec_power', 'spec_capacity', 'spec_voltage', 'spec_frequency', 
            'spec_screen_size', 'spec_color', 'spec_other',
            'dimension_length', 'dimension_width', 'dimension_height'
        ];

        foreach ($fieldsToRemove as $field) {
            unset($post[$field]);
        }

        // Set updated timestamp
        $post['updated_at'] = date('Y-m-d H:i:s');

        try {
            // Sử dụng skipValidation để tránh lỗi validation khi update
            $this->productModel->skipValidation(true)->update($id, $post);
            
            // Verify update thành công
            $updatedProduct = $this->productModel->find($id);
            if (!$updatedProduct) {
                throw new \Exception('Không thể cập nhật sản phẩm');
            }
            
            return $this->response->setJSON([
                'status'=>'success',
                'message'=>'Cập nhật thành công',
                'token'=>csrf_hash()
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Update product error: ' . $e->getMessage());
            return $this->response->setJSON([
                'status'=>'error',
                'message'=>'Lỗi cập nhật: ' . $e->getMessage(),
                'token'=>csrf_hash()
            ]);
        }
    }

    // 6. Delete
    public function delete($id)
    {
        $product = $this->productModel->find($id);
        if (!$product) {
            return $this->response->setJSON([
                'status'=>'error',
                'message'=>'Không tìm thấy sản phẩm',
                'token'=>csrf_hash()
            ]);
        }

        try {
            // Xóa ảnh chính
            if (!empty($product['main_image']) && file_exists(FCPATH . $product['main_image'])) {
                unlink(FCPATH . $product['main_image']);
            }

            // Xóa ảnh phụ
            $images = $this->imageModel->where('product_id', $id)->findAll();
            foreach ($images as $img) {
                if (file_exists(FCPATH . $img['image_url'])) {
                    unlink(FCPATH . $img['image_url']);
                }
            }
            $this->imageModel->where('product_id', $id)->delete();

            // Xóa sản phẩm
            $deleted = $this->productModel->delete($id);
            
            if (!$deleted) {
                throw new \Exception('Không thể xóa sản phẩm');
            }

            return $this->response->setJSON([
                'status'=>'success',
                'message'=>'Xóa thành công',
                'token'=>csrf_hash()
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Delete product error: ' . $e->getMessage());
            return $this->response->setJSON([
                'status'=>'error',
                'message'=>'Lỗi xóa: ' . $e->getMessage(),
                'token'=>csrf_hash()
            ]);
        }
    }

    // 7. Xóa ảnh riêng lẻ
    public function deleteImage($id)
    {
        $img = $this->imageModel->find($id);
        if ($img) {
            // Xóa file
            if (file_exists(FCPATH . $img['image_url'])) {
                unlink(FCPATH . $img['image_url']);
            }
            $this->imageModel->delete($id);
            
            return $this->response->setJSON(['status'=>'success','token'=>csrf_hash()]);
        }
        return $this->response->setJSON(['status'=>'error','message'=>'Không tìm thấy ảnh','token'=>csrf_hash()]);
    }
}