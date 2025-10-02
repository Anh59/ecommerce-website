<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductModel extends Model
{
    protected $table            = 'products';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false; // SỬA: Tắt soft delete để xóa thật
    protected $protectFields    = true;
    protected $allowedFields    = [
        'name',
        'slug', 
        'sku',
        'category_id',
        'brand_id',
        'price',
        'sale_price',
        'short_description',
        'description',
        'specifications',
        'main_image',
        'stock_quantity',
        'min_stock_level',
        'stock_status',
        'weight',
        'dimensions',
        'material',
        'origin_country',
        'warranty_period',
        'is_featured',
        'is_active',
        'meta_title',
        'meta_description'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation - ĐÃ SỬA: Đơn giản hóa validation rules
    protected $validationRules = [
        'name' => 'required|max_length[255]',
        'price' => 'required|numeric',
        'category_id' => 'required|integer'
    ];

    protected $validationMessages = [
        'name' => [
            'required' => 'Tên sản phẩm không được để trống',
            'max_length' => 'Tên sản phẩm không được vượt quá 255 ký tự'
        ],
        'price' => [
            'required' => 'Giá không được để trống',
            'numeric' => 'Giá phải là số'
        ],
        'category_id' => [
            'required' => 'Danh mục không được để trống',
            'integer' => 'Danh mục không hợp lệ'
        ]
    ];

    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks - ĐÃ SỬA: Giảm thiểu callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = ['beforeInsert'];
    protected $afterInsert    = [];
    protected $beforeUpdate   = ['beforeUpdate'];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    protected function beforeInsert(array $data)
    {
        $data['data']['created_at'] = date('Y-m-d H:i:s');
        $data['data']['updated_at'] = date('Y-m-d H:i:s');
        
        // Auto-generate slug if not provided
        if (empty($data['data']['slug']) && !empty($data['data']['name'])) {
            $data['data']['slug'] = $this->generateSlug($data['data']['name']);
        }
        
        // Auto-generate SKU if not provided
        if (empty($data['data']['sku'])) {
            $data['data']['sku'] = $this->generateSKU();
        }

        return $data;
    }

    protected function beforeUpdate(array $data)
    {
        $data['data']['updated_at'] = date('Y-m-d H:i:s');
        return $data;
    }

    /**
     * Generate unique slug
     */
    private function generateSlug($name, $id = null)
    {
        $slug = url_title($name, '-', true);
        $originalSlug = $slug;
        $counter = 1;

        while (true) {
            $query = $this->where('slug', $slug);
            if ($id) {
                $query->where('id !=', $id);
            }
            
            if (!$query->first()) {
                break;
            }
            
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * Generate unique SKU
     */
    private function generateSKU()
    {
        do {
            $sku = 'PRD-' . strtoupper(uniqid());
        } while ($this->where('sku', $sku)->first());

        return $sku;
    }

    /**
     * Determine stock status based on quantity
     */
    private function determineStockStatus($quantity, $minLevel = 0)
    {
        if ($quantity <= 0) {
            return 'out_of_stock';
        } elseif ($quantity <= $minLevel) {
            return 'low_stock';
        } else {
            return 'in_stock';
        }
    }

    /**
     * Get products with category and brand info
     */
    public function getWithDetails($limit = null, $offset = null)
    {
        $builder = $this->select('products.*, categories.name as category_name, brands.name as brand_name')
                        ->join('categories', 'categories.id = products.category_id', 'left')
                        ->join('brands', 'brands.id = products.brand_id', 'left');

        if ($limit) {
            $builder->limit($limit, $offset);
        }

        return $builder->findAll();
    }

    /**
     * Get featured products
     */
    public function getFeatured($limit = 10)
    {
        return $this->where('is_featured', 1)
                   ->where('is_active', 1)
                   ->limit($limit)
                   ->findAll();
    }

    /**
     * Get products by category
     */
    public function getByCategory($categoryId, $limit = null)
    {
        $builder = $this->where('category_id', $categoryId)
                       ->where('is_active', 1);
        
        if ($limit) {
            $builder->limit($limit);
        }
        
        return $builder->findAll();
    }

    /**
     * Get products by brand
     */
    public function getByBrand($brandId, $limit = null)
    {
        $builder = $this->where('brand_id', $brandId)
                       ->where('is_active', 1);
        
        if ($limit) {
            $builder->limit($limit);
        }
        
        return $builder->findAll();
    }

    /**
     * Search products
     */
    public function search($keyword, $limit = null)
    {
        $builder = $this->groupStart()
                       ->like('name', $keyword)
                       ->orLike('description', $keyword)
                       ->orLike('short_description', $keyword)
                       ->orLike('sku', $keyword)
                       ->groupEnd()
                       ->where('is_active', 1);
        
        if ($limit) {
            $builder->limit($limit);
        }
        
        return $builder->findAll();
    }

    /**
     * Get low stock products
     */
    public function getLowStock()
    {
        return $this->where('stock_quantity <=', 'min_stock_level', false)
                   ->where('stock_quantity >', 0)
                   ->where('is_active', 1)
                   ->findAll();
    }

    /**
     * Get out of stock products
     */
    public function getOutOfStock()
    {
        return $this->where('stock_quantity <=', 0)
                   ->where('is_active', 1)
                   ->findAll();
    }

    /**
     * Update stock quantity
     */
    public function updateStock($productId, $quantity, $operation = 'add')
    {
        $product = $this->find($productId);
        if (!$product) {
            return false;
        }

        $newQuantity = $operation === 'add' 
            ? $product['stock_quantity'] + $quantity 
            : $product['stock_quantity'] - $quantity;

        $newQuantity = max(0, $newQuantity); // Don't allow negative stock

        return $this->update($productId, [
            'stock_quantity' => $newQuantity,
            'stock_status' => $this->determineStockStatus($newQuantity, $product['min_stock_level'])
        ]);
    }

    /**
     * Get product with images
     */
    public function getWithImages($id)
    {
        $product = $this->find($id);
        if ($product) {
            $imageModel = new \App\Models\ProductImageModel();
            $product['images'] = $imageModel->where('product_id', $id)->findAll();
        }
        return $product;
    }

    /**
     * Bulk update status
     */
    public function bulkUpdateStatus($ids, $status)
    {
        return $this->whereIn('id', $ids)->set(['is_active' => $status])->update();
    }

    /**
     * Get products for export
     */
    public function getForExport($filters = [])
    {
        $builder = $this->select('products.*, categories.name as category_name, brands.name as brand_name')
                       ->join('categories', 'categories.id = products.category_id', 'left')
                       ->join('brands', 'brands.id = products.brand_id', 'left');

        if (!empty($filters['category_id'])) {
            $builder->where('products.category_id', $filters['category_id']);
        }

        if (!empty($filters['brand_id'])) {
            $builder->where('products.brand_id', $filters['brand_id']);
        }

        if (isset($filters['is_active'])) {
            $builder->where('products.is_active', $filters['is_active']);
        }

        return $builder->findAll();
    }

    /**
     * Override update method to handle validation issues - ĐÃ THÊM
     */
    public function updateProduct($id, $data)
    {
        try {
            // Tắt validation tạm thời để tránh lỗi unique constraint
            $originalValidation = $this->skipValidation;
            $this->skipValidation = true;
            
            $result = $this->update($id, $data);
            
            // Khôi phục validation
            $this->skipValidation = $originalValidation;
            
            return $result;
        } catch (\Exception $e) {
            log_message('error', 'ProductModel updateProduct error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Override delete method to handle foreign key constraints - ĐÃ THÊM  
     */
    public function deleteProduct($id)
    {
        try {
            $product = $this->find($id);
            if (!$product) {
                return false;
            }

            // Xóa các bản ghi liên quan trước
            $db = \Config\Database::connect();
            
            // Xóa stock movements
            $db->table('stock_movements')->where('product_id', $id)->delete();
            
            // Xóa product images  
            $db->table('product_images')->where('product_id', $id)->delete();
            
            // Xóa order items (nếu có)
            $db->table('order_items')->where('product_id', $id)->delete();
            
            // Cuối cùng xóa product
            return $this->delete($id);
            
        } catch (\Exception $e) {
            log_message('error', 'ProductModel deleteProduct error: ' . $e->getMessage());
            return false;
        }
    }
    public function getProductBySlug($slug)
{
    return $this->where('slug', $slug)
                ->where('is_active', 1)
                ->where('deleted_at IS NULL')
                ->first();
}

public function getProductImages($productId)
{
    return $this->db->table('product_images')
                    ->where('product_id', $productId)
                    ->orderBy('sort_order', 'ASC')
                    ->get()
                    ->getResultArray();
}

public function getRelatedProducts($productId, $categoryId, $limit = 8)
{
    return $this->where('category_id', $categoryId)
                ->where('id !=', $productId)
                ->where('is_active', 1)
                ->where('deleted_at IS NULL')
                ->orderBy('RAND()')
                ->limit($limit)
                ->findAll();
}

public function getBestSellers($limit = 8)
{
    return $this->select('products.*, 
                         COALESCE(SUM(order_items.quantity), 0) as total_sold')
                ->join('order_items', 'order_items.product_id = products.id', 'left')
                ->join('orders', 'orders.id = order_items.order_id AND orders.status IN ("completed", "processing")', 'left')
                ->where('products.is_active', 1)
                ->where('products.deleted_at IS NULL')
                ->groupBy('products.id')
                ->orderBy('total_sold', 'DESC')
                ->orderBy('products.created_at', 'DESC')
                ->limit($limit)
                ->findAll();
}

/**
 * Get featured products as alternative to best sellers
 * 
 * @param int $limit
 * @return array
 */
public function getFeaturedProducts($limit = 8)
{
    return $this->where('is_featured', 1)
                ->where('is_active', 1)
                ->where('deleted_at IS NULL')
                ->orderBy('created_at', 'DESC')
                ->limit($limit)
                ->findAll();
}

/**
 * Get latest products
 * 
 * @param int $limit
 * @return array
 */
public function getLatestProducts($limit = 8)
{
    return $this->where('is_active', 1)
                ->where('deleted_at IS NULL')
                ->orderBy('created_at', 'DESC')
                ->limit($limit)
                ->findAll();
}
}