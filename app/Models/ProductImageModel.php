<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductImageModel extends Model
{
    protected $table            = 'product_images';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'product_id',
        'image_url',
        'alt_text',
        'sort_order',
        'is_primary'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules = [
        'product_id' => 'required|integer',
        'image_url' => 'required|max_length[500]'
    ];

    protected $validationMessages = [
        'product_id' => [
            'required' => 'Product ID is required',
            'integer' => 'Product ID must be an integer'
        ],
        'image_url' => [
            'required' => 'Image URL is required',
            'max_length' => 'Image URL cannot exceed 500 characters'
        ]
    ];

    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
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
        // Set sort order if not provided
        if (empty($data['data']['sort_order'])) {
            $maxOrder = $this->where('product_id', $data['data']['product_id'])
                           ->selectMax('sort_order')
                           ->first();
            $data['data']['sort_order'] = ($maxOrder['sort_order'] ?? 0) + 1;
        }

        $data['data']['created_at'] = date('Y-m-d H:i:s');
        $data['data']['updated_at'] = date('Y-m-d H:i:s');

        return $data;
    }

    protected function beforeUpdate(array $data)
    {
        $data['data']['updated_at'] = date('Y-m-d H:i:s');
        return $data;
    }

    /**
     * Get images by product ID, ordered by sort_order
     */
    public function getByProduct($productId)
    {
        return $this->where('product_id', $productId)
                   ->orderBy('sort_order', 'ASC')
                   ->findAll();
    }

    /**
     * Get primary image for product
     */
    public function getPrimaryImage($productId)
    {
        return $this->where('product_id', $productId)
                   ->where('is_primary', 1)
                   ->first();
    }

    /**
     * Set primary image
     */
    public function setPrimary($imageId, $productId)
    {
        // Remove primary status from all images of this product
        $this->where('product_id', $productId)
             ->set(['is_primary' => 0])
             ->update();

        // Set this image as primary
        return $this->update($imageId, ['is_primary' => 1]);
    }

    /**
     * Update sort order
     */
    public function updateSortOrder($imageId, $sortOrder)
    {
        return $this->update($imageId, ['sort_order' => $sortOrder]);
    }

    /**
     * Delete all images for a product
     */
    public function deleteByProduct($productId)
    {
        return $this->where('product_id', $productId)->delete();
    }

    /**
     * Bulk delete images
     */
    public function bulkDelete($imageIds)
    {
        return $this->whereIn('id', $imageIds)->delete();
    }

    /**
     * Reorder images for a product
     */
    public function reorderImages($productId, $imageIds)
    {
        $sortOrder = 1;
        foreach ($imageIds as $imageId) {
            $this->update($imageId, [
                'sort_order' => $sortOrder,
                'updated_at' => date('Y-m-d H:i:s')
            ]);
            $sortOrder++;
        }
        return true;
    }

    /**
     * Get images count by product
     */
    public function getImageCount($productId)
    {
        return $this->where('product_id', $productId)->countAllResults();
    }

    /**
     * Get all images with product info
     */
    public function getWithProductInfo($limit = null, $offset = null)
    {
        $builder = $this->select('product_images.*, products.name as product_name, products.sku')
                        ->join('products', 'products.id = product_images.product_id', 'left')
                        ->orderBy('product_images.product_id', 'ASC')
                        ->orderBy('product_images.sort_order', 'ASC');

        if ($limit) {
            $builder->limit($limit, $offset);
        }

        return $builder->findAll();
    }

    /**
     * Get unused images (orphaned)
     */
    public function getUnusedImages()
    {
        return $this->select('product_images.*')
                   ->join('products', 'products.id = product_images.product_id', 'left')
                   ->where('products.id IS NULL')
                   ->findAll();
    }
}