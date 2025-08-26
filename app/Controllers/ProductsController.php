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

    // 3. Lưu mới
    public function store()
    {
        $validation = \Config\Services::validation();
        $rules = [
            'name' => 'required',
            'slug' => 'required|is_unique[products.slug]',
            'sku'  => 'required|is_unique[products.sku]',
            'price'=> 'required|numeric'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'status' => 'error',
                'errors' => $validation->getErrors(),
                'token' => csrf_hash()
            ]);
        }

        $post = $this->request->getPost();

        // Xử lý main image (nếu có)
        $mainImage = $this->request->getFile('main_image');
        if ($mainImage && $mainImage->isValid() && !$mainImage->hasMoved()) {
            $newName = $mainImage->getRandomName();
            $mainImage->move(FCPATH . 'uploads/products', $newName);
            $post['main_image'] = 'uploads/products/' . $newName;
        }

        // Lưu product
        $id = $this->productModel->insert($post);

        // Lưu product_images nếu có files multiple (name="images[]")
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

        // Nếu có stock_quantity, tạo stock movement
        if (!empty($post['stock_quantity']) && (int)$post['stock_quantity'] > 0) {
            (new StockMovementModel())->insert([
                'product_id' => $id,
                'type' => 'in',
                'quantity' => (int)$post['stock_quantity'],
                'reason' => 'initial_stock'
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
            return $this->response->setJSON(['status'=>'error','message'=>'Không tìm thấy sản phẩm','token'=>csrf_hash()]);
        }
        $images = $this->imageModel->where('product_id',$id)->findAll();
        return $this->response->setJSON(['status'=>'success','product'=>$product,'images'=>$images,'token'=>csrf_hash()]);
    }

    // 5. Update
    public function update($id)
    {
        // Validation: unique slug/sku ngoại trừ chính nó
        $rules = [
            'name' => 'required',
            'slug' => "required|is_unique[products.slug,id,{$id}]",
            'sku'  => "required|is_unique[products.sku,id,{$id}]",
            'price'=> 'required|numeric'
        ];
        if (!$this->validate($rules)) {
            return $this->response->setJSON(['status'=>'error','errors'=>\Config\Services::validation()->getErrors(),'token'=>csrf_hash()]);
        }

        $post = $this->request->getPost();

        // Xử lý main image (nếu có)
        $mainImage = $this->request->getFile('main_image');
        if ($mainImage && $mainImage->isValid() && !$mainImage->hasMoved()) {
            $newName = $mainImage->getRandomName();
            $mainImage->move(FCPATH . 'uploads/products', $newName);
            $post['main_image'] = 'uploads/products/' . $newName;
        }

        $this->productModel->update($id, $post);

        // Nếu stock_quantity thay đổi thì thêm stock_movement (tùy logic của bạn)
        // VD: nếu muốn track diff -> đọc product cũ rồi insert movement dựa trên change

        return $this->response->setJSON(['status'=>'success','message'=>'Cập nhật thành công','token'=>csrf_hash()]);
    }

    // 6. Delete (POST)
    public function delete($id)
    {
        $this->productModel->delete($id);
        // (Bạn có thể xóa file ảnh nếu muốn)
        return $this->response->setJSON(['status'=>'success','message'=>'Xóa thành công','token'=>csrf_hash()]);
    }

    // 7. Xóa ảnh riêng lẻ
    public function deleteImage($id)
    {
        $img = $this->imageModel->find($id);
        if ($img) {
            $this->imageModel->delete($id);
            // unlink file ở public nếu muốn
            return $this->response->setJSON(['status'=>'success','token'=>csrf_hash()]);
        }
        return $this->response->setJSON(['status'=>'error','message'=>'Không tìm thấy ảnh','token'=>csrf_hash()]);
    }
}
