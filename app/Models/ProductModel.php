<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductModel extends Model
{
    protected $table            = 'products';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
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

    // Validation
    protected $validationRules = [
        'name' => 'required|max_length[255]',
        'slug' => 'required|max_length[255]|is_unique[products.slug,id,{id}]',
        'sku'  => 'required|max_length[100]|is_unique[products.sku,id,{id}]',
        'price' => 'required|numeric',
        'category_id' => 'required|integer'
    ];

    protected $validationMessages = [
        'name' => [
            'required' => 'Tên sản phẩm không được để trống',
            'max_length' => 'Tên sản phẩm không được vượt quá 255 ký tự'
        ],
        'slug' => [
            'required' => 'Slug không được để trống',
            'is_unique' => 'Slug đã tồn tại'
        ],
        'sku' => [
            'required' => 'SKU không được để trống',
            'is_unique' => 'SKU đã tồn tại'
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

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = ['beforeInsert'];
    protected $afterInsert    = [];
    protected $beforeUpdate   = ['beforeUpdate'];
    protected $afterUpdate    = ['updateStockStatus'];
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

    protected function updateStockStatus(array $data)
    {
        if (isset($data['id'])) {
            $product = $this->find($data['id']);
            if ($product) {
                $stockStatus = $this->determineStockStatus(
                    $product['stock_quantity'], 
                    $product['min_stock_level']
                );
                
                if ($stockStatus !== $product['stock_status']) {
                    $this->update($data['id'], ['stock_status' => $stockStatus]);
                }
            }
        }
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
                        ->join('brands', 'brands.id = products.brand_id', 'left')
                        ->where('products.deleted_at', null);

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
}