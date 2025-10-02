<?php

namespace App\Controllers;
use App\Models\ProductModel;
use App\Models\CategoryModel;
use App\Models\BrandModel;
class Home extends BaseController
{
    protected $productModel;
    protected $categoryModel;
    protected $brandModel;

    public function __construct()
    {
        $this->productModel = new ProductModel();
        $this->categoryModel = new CategoryModel();
        $this->brandModel = new BrandModel();
        helper(['product', 'url']);
    }

    public function index()
    {
        $data = [];

        // 1. Banner Slides - Lấy 3 sản phẩm featured ngẫu nhiên
        $data['bannerProducts'] = $this->productModel
            ->where('is_featured', 1)
            ->where('is_active', 1)
            ->where('deleted_at IS NULL')
            ->orderBy('RAND()')
            ->limit(3)
            ->findAll();

        // 2. Featured Categories - Lấy 4 danh mục parent có sản phẩm nhiều nhất
        $data['featuredCategories'] = $this->categoryModel
            ->select('categories.*, COUNT(products.id) as product_count')
            ->join('products', 'products.category_id = categories.id AND products.is_active = 1', 'left')
            ->where('categories.is_active', 1)
            ->where('categories.deleted_at IS NULL')
            ->where('categories.parent_id IS NULL OR categories.parent_id = 0')
            ->groupBy('categories.id')
            ->orderBy('product_count', 'DESC')
            ->limit(4)
            ->findAll();

        // 3. Awesome Shop - Sản phẩm mới nhất (2 slides x 8 sản phẩm = 16)
        $data['latestProducts'] = $this->productModel
            ->where('is_active', 1)
            ->where('deleted_at IS NULL')
            ->orderBy('created_at', 'DESC')
            ->limit(16)
            ->findAll();

        // Chia thành 2 slides
        $data['latestProductsSlide1'] = array_slice($data['latestProducts'], 0, 8);
        $data['latestProductsSlide2'] = array_slice($data['latestProducts'], 8, 8);

        // 4. Weekly Sale - Sản phẩm có sale_price cao nhất
        $data['weeklySaleProduct'] = $this->productModel
            ->select('products.*, 
                     ((price - sale_price) / price * 100) as discount_percent')
            ->where('sale_price IS NOT NULL')
            ->where('sale_price > 0')
            ->where('sale_price < price')
            ->where('is_active', 1)
            ->where('deleted_at IS NULL')
            ->orderBy('discount_percent', 'DESC')
            ->first();

        // 5. Best Sellers - Sản phẩm bán chạy
        $data['bestSellers'] = $this->productModel->getBestSellers(8);

        // 6. Brands - Top brands
        $data['topBrands'] = $this->brandModel
            ->where('is_active', 1)
            ->where('deleted_at IS NULL')
            ->orderBy('sort_order', 'ASC')
            ->limit(10)
            ->findAll();

        return view('Customers/index', $data);
    }
    public function layout(): string{
        return view('Customers/layout/main');
    }
    public function login(): string{
        return view('Customers/login');
    }
    public function blog(): string
    {
        return view('Customers/blog');
    }
    public function contact(): string{
        return view('Customers/contact');
    }
    public function single_blog(): string
    {
        return view('Customers/single-blog');
    
    }
    public function single_product(): string
    {
        return view('Customers/single-product');
    }

    public function cart(): string
    {
        return view('Customers/cart');
    }
    public function checkout(): string
    {
        return view('Customers/checkout');
    }
    public function category(): string
    {
        return view('Customers/category');
    }
    public function tracking(): string
    {
        return view('Customers/tracking');
    }
    public function confirmation(): string
    {
        return view('Customers/confirmation');
    }
    public function elements(): string{
        return view('Customers/elements');
    }
    public function feature(): string
    {
        return view('Customers/feature');
    }
    public function Dashboard(): string
    {
        return view('Dashboard/layout');
    }
    public function Errors(): string
    {
        return view('Dashboard/errors');
    }
}