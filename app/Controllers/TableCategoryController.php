<?php

namespace App\Controllers;

use App\Models\CategoryModel;
use App\Models\ProductModel;
use App\Models\BrandModel;

class TableCategoryController extends BaseController
{
    protected $categoryModel;
    protected $productModel;
    protected $brandModel;
    
    public function __construct()
    {
        $this->categoryModel = new CategoryModel();
        $this->productModel = new ProductModel();
        $this->brandModel = new BrandModel();
    }

    public function index()
    {
        // Lấy parameters từ URL
        $categoryId = $this->request->getGet('category') ?: null;
        $brandId = $this->request->getGet('brand') ?: null;
        $minPrice = $this->request->getGet('min_price') ?: null;
        $maxPrice = $this->request->getGet('max_price') ?: null;
        $color = $this->request->getGet('color') ?: null;
        $sortBy = $this->request->getGet('sort') ?? 'name';
        $perPage = $this->request->getGet('per_page') ?? 9;
        $search = $this->request->getGet('search') ?: null;
        $page = $this->request->getGet('page') ?? 1;

        // Lấy dữ liệu categories và brands
        // $categories = $this->categoryModel->getCategoriesTree();
        $categories = $this->categoryModel->getCategoriesWithProductCount();
        $brands = $this->brandModel->where('is_active', 1)->orderBy('name', 'ASC')->findAll();

        // Build query cho products
        $builder = $this->productModel->select('products.*, categories.name as category_name, brands.name as brand_name')
                                     ->join('categories', 'categories.id = products.category_id', 'left')
                                     ->join('brands', 'brands.id = products.brand_id', 'left')
                                     ->where('products.is_active', 1);

        // Apply filters
        if ($categoryId) {
            $builder->where('products.category_id', $categoryId);
        }

        if ($brandId) {
            $builder->where('products.brand_id', $brandId);
        }

        if ($minPrice) {
            $builder->where('products.price >=', $minPrice);
        }

        if ($maxPrice) {
            $builder->where('products.price <=', $maxPrice);
        }

        if ($color) {
            $builder->like('products.color', $color);
        }

        if ($search) {
            $builder->groupStart()
                   ->like('products.name', $search)
                   ->orLike('products.description', $search)
                   ->orLike('products.short_description', $search)
                   ->groupEnd();
        }

        // Apply sorting
        switch ($sortBy) {
            case 'price_asc':
                $builder->orderBy('products.price', 'ASC');
                break;
            case 'price_desc':
                $builder->orderBy('products.price', 'DESC');
                break;
            case 'name':
            default:
                $builder->orderBy('products.name', 'ASC');
                break;
        }

        // Count total products trước paginate
        $totalProducts = $builder->countAllResults(false);

        // Pagination
        $pager = \Config\Services::pager();
        $products = $builder->paginate($perPage, 'default', $page);

        // Get min and max prices for slider
        $priceRange = $this->productModel->select('MIN(price) as min_price, MAX(price) as max_price')
                                       ->where('is_active', 1)
                                       ->first();

        $data = [
            'products' => $products,
            'categories' => $categories,
            'brands' => $brands,
            'totalProducts' => $totalProducts,
            'currentPage' => $page,
            'totalPages' => $pager->getPageCount(),
            'perPage' => $perPage,
            'minPrice' => $priceRange['min_price'] ?? 0,
            'maxPrice' => $priceRange['max_price'] ?? 1000000,
            'filters' => [
                'category_id' => $categoryId,
                'brand_id' => $brandId,
                'min_price' => $minPrice,
                'max_price' => $maxPrice,
                'color' => $color,
                'sort' => $sortBy,
                'search' => $search
            ]
        ];

        return view('Customers/category', $data);
    }

    public function getProducts()
{
    if (!$this->request->isAJAX()) {
        return $this->response->setStatusCode(404);
    }

    $categoryId = $this->request->getPost('category_id');
    $brandId = $this->request->getPost('brand_id');
    $minPrice = $this->request->getPost('min_price');
    $maxPrice = $this->request->getPost('max_price');
    $color = $this->request->getPost('color');
    $sortBy = $this->request->getPost('sort_by') ?? 'name';
    $page = $this->request->getPost('page') ?? 1;
    $perPage = $this->request->getPost('per_page') ?? 9;
    $search = $this->request->getPost('search');

    $builder = $this->productModel->select('products.*, categories.name as category_name, brands.name as brand_name')
                                 ->join('categories', 'categories.id = products.category_id', 'left')
                                 ->join('brands', 'brands.id = products.brand_id', 'left')
                                 ->where('products.is_active', 1);

    // Apply filters
    if ($categoryId && $categoryId !== 'null') {
        $builder->where('products.category_id', $categoryId);
    }

    if ($brandId && $brandId !== 'null') {
        $builder->where('products.brand_id', $brandId);
    }

    if ($minPrice && $minPrice !== 'null') {
        $builder->where('products.price >=', $minPrice);
    }

    if ($maxPrice && $maxPrice !== 'null') {
        $builder->where('products.price <=', $maxPrice);
    }

    if ($color && $color !== 'null') {
        $builder->like('products.color', $color);
    }

    if ($search && $search !== 'null') {
        $builder->groupStart()
               ->like('products.name', $search)
               ->orLike('products.description', $search)
               ->orLike('products.short_description', $search)
               ->groupEnd();
    }

    // Apply sorting
    switch ($sortBy) {
        case 'price_asc':
            $builder->orderBy('products.price', 'ASC');
            break;
        case 'price_desc':
            $builder->orderBy('products.price', 'DESC');
            break;
        case 'name':
        default:
            $builder->orderBy('products.name', 'ASC');
            break;
    }

    // Sử dụng paginate thay vì limit
    $totalProducts = $builder->countAllResults(false);
    $pager = \Config\Services::pager();
    $products = $builder->paginate($perPage, 'default', $page);

    $response = [
        'success' => true,
        'products' => $products,
        'total' => $totalProducts,
        'page' => $page,
        'per_page' => $perPage,
        'total_pages' => $pager->getPageCount()
    ];

    return $this->response->setJSON($response);
}

    public function addToWishlist()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(404);
        }

        $session = session();
        $customerId = $session->get('customer_id');

        if (!$customerId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Vui lòng đăng nhập để thêm vào wishlist'
            ]);
        }

        $productId = $this->request->getPost('product_id');

        if (!$productId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Sản phẩm không hợp lệ'
            ]);
        }

        // Check if product exists
        $product = $this->productModel->find($productId);
        if (!$product) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Sản phẩm không tồn tại'
            ]);
        }

        $wishlistModel = new \App\Models\WishlistModel();

        // Check if already in wishlist
        $existing = $wishlistModel->where([
            'customer_id' => $customerId,
            'product_id' => $productId
        ])->first();

        if ($existing) {
            // Remove from wishlist
            $wishlistModel->delete($existing['id']);
            return $this->response->setJSON([
                'success' => true,
                'action' => 'removed',
                'message' => 'Đã xóa khỏi wishlist'
            ]);
        } else {
            // Add to wishlist
            $wishlistModel->insert([
                'customer_id' => $customerId,
                'product_id' => $productId,
                'created_at' => date('Y-m-d H:i:s')
            ]);

            return $this->response->setJSON([
                'success' => true,
                'action' => 'added',
                'message' => 'Đã thêm vào wishlist'
            ]);
        }
    }

    public function getWishlistStatus()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(404);
        }

        $session = session();
        $customerId = $session->get('customer_id');

        if (!$customerId) {
            return $this->response->setJSON(['wishlist' => []]);
        }

        $wishlistModel = new \App\Models\WishlistModel();
        $wishlistItems = $wishlistModel->where('customer_id', $customerId)
                                      ->findColumn('product_id');

        return $this->response->setJSON([
            'wishlist' => $wishlistItems ?? []
        ]);
    }

    public function addToCart()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(404);
        }

        $session = session();
        $customerId = $session->get('customer_id');

        if (!$customerId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Vui lòng đăng nhập để thêm vào giỏ hàng'
            ]);
        }

        $productId = $this->request->getPost('product_id');
        $quantity = $this->request->getPost('quantity') ?? 1;

        if (!$productId || $quantity < 1) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Thông tin sản phẩm không hợp lệ'
            ]);
        }

        // Check product exists and in stock
        $product = $this->productModel->find($productId);
        if (!$product || !$product['is_active']) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Sản phẩm không tồn tại hoặc đã ngừng bán'
            ]);
        }

        if ($product['stock_quantity'] < $quantity) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Không đủ hàng trong kho'
            ]);
        }

        $cartModel = new \App\Models\CartModel();

        // Check if product already in cart
        $existingItem = $cartModel->where([
            'customer_id' => $customerId,
            'product_id' => $productId
        ])->first();

        if ($existingItem) {
            // Update quantity
            $newQuantity = $existingItem['quantity'] + $quantity;
            if ($newQuantity > $product['stock_quantity']) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Không đủ hàng trong kho'
                ]);
            }

            $cartModel->update($existingItem['id'], [
                'quantity' => $newQuantity,
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        } else {
            // Add new item
            $cartModel->insert([
                'customer_id' => $customerId,
                'product_id' => $productId,
                'quantity' => $quantity,
                'price' => ($product['sale_price'] > 0) ? $product['sale_price'] : $product['price'],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        }

        // Get cart count
        $cartCount = $cartModel->where('customer_id', $customerId)->countAllResults();

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Đã thêm vào giỏ hàng',
            'cart_count' => $cartCount
        ]);
    }
}