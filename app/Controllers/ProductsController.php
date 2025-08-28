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

    // Lấy các field hợp lệ
    $post = $this->request->getPost([
        'name', 'slug', 'sku', 'price', 'sale_price',
        'short_description', 'description',
        'category_id', 'brand_id', 'stock_quantity'
    ]);

    // Set default nếu thiếu
    $post['sale_price']        = $post['sale_price'] ?? null;
    $post['short_description'] = $post['short_description'] ?? null;
    $post['description']       = $post['description'] ?? null;
    $post['stock_quantity']    = $post['stock_quantity'] ?? 0;
    $post['stock_status']      = ($post['stock_quantity'] > 0) ? 'in_stock' : 'out_of_stock';
    $post['created_at']        = date('Y-m-d H:i:s');
    $post['updated_at']        = date('Y-m-d H:i:s');

    // Xử lý main image
    $mainImage = $this->request->getFile('main_image');
    if ($mainImage && $mainImage->isValid() && !$mainImage->hasMoved()) {
        $newName = $mainImage->getRandomName();
        $mainImage->move(FCPATH . 'uploads/products', $newName);
        $post['main_image'] = 'uploads/products/' . $newName;
    }

    // Lưu product
    $id = $this->productModel->insert($post);

    // Lưu ảnh phụ
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

    // Nếu có stock_quantity > 0 thì tạo stock movement
    if (!empty($post['stock_quantity']) && (int)$post['stock_quantity'] > 0) {
        (new StockMovementModel())->insert([
            'product_id' => $id,
            'type'       => 'in',
            'quantity'   => (int)$post['stock_quantity'],
            'reason'     => 'initial_stock'
        ]);
    }

    return $this->response->setJSON([
        'status'  => 'success',
        'message' => 'Tạo sản phẩm thành công',
        'token'   => csrf_hash()
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
    // 5. Update
public function update($id)
{
    $rules = [
        'name' => 'required',
        'slug' => "required|is_unique[products.slug,id,{$id}]",
        'sku'  => "required|is_unique[products.sku,id,{$id}]",
        'price'=> 'required|numeric'
    ];
    if (!$this->validate($rules)) {
        return $this->response->setJSON([
            'status'=>'error',
            'errors'=>\Config\Services::validation()->getErrors(),
            'token'=>csrf_hash()
        ]);
    }

    // Lấy field hợp lệ
    $post = $this->request->getPost([
        'name', 'slug', 'sku', 'price', 'sale_price',
        'short_description', 'description',
        'category_id', 'brand_id', 'stock_quantity'
    ]);

    $post['sale_price']        = $post['sale_price'] ?? null;
    $post['short_description'] = $post['short_description'] ?? null;
    $post['description']       = $post['description'] ?? null;
    $post['stock_quantity']    = $post['stock_quantity'] ?? 0;
    $post['stock_status']      = ($post['stock_quantity'] > 0) ? 'in_stock' : 'out_of_stock';
    $post['updated_at']        = date('Y-m-d H:i:s');

    // Xử lý main image
    $mainImage = $this->request->getFile('main_image');
    if ($mainImage && $mainImage->isValid() && !$mainImage->hasMoved()) {
        $newName = $mainImage->getRandomName();
        $mainImage->move(FCPATH . 'uploads/products', $newName);
        $post['main_image'] = 'uploads/products/' . $newName;
    }

    // Update product
    $this->productModel->update($id, $post);

    // Xử lý ảnh phụ (nếu có upload thêm)
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

    // 👉 Nếu muốn theo dõi thay đổi tồn kho thì ở đây có thể
    // so sánh stock_quantity cũ và mới, rồi insert StockMovementModel

    return $this->response->setJSON([
        'status'=>'success',
        'message'=>'Cập nhật thành công',
        'token'=>csrf_hash()
    ]);
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
