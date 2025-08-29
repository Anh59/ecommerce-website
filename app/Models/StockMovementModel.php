<?php

namespace App\Models;

use CodeIgniter\Model;

class StockMovementModel extends Model
{
    protected $table            = 'stock_movements';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'product_id',
        'type',
        'quantity',
        'reason',
        'reference_id',
        'reference_type',
        'notes',
        'user_id'
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
        'type' => 'required|in_list[in,out]',
        'quantity' => 'required|integer|greater_than[0]',
        'reason' => 'required|max_length[255]'
    ];

    protected $validationMessages = [
        'product_id' => [
            'required' => 'Product ID is required',
            'integer' => 'Product ID must be an integer'
        ],
        'type' => [
            'required' => 'Movement type is required',
            'in_list' => 'Movement type must be either "in" or "out"'
        ],
        'quantity' => [
            'required' => 'Quantity is required',
            'integer' => 'Quantity must be an integer',
            'greater_than' => 'Quantity must be greater than 0'
        ],
        'reason' => [
            'required' => 'Reason is required',
            'max_length' => 'Reason cannot exceed 255 characters'
        ]
    ];

    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = ['beforeInsert'];
    protected $afterInsert    = ['updateProductStock'];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    protected function beforeInsert(array $data)
    {
        $data['data']['created_at'] = date('Y-m-d H:i:s');
        $data['data']['updated_at'] = date('Y-m-d H:i:s');

        // Set user_id if available in session
        if (session()->has('user_id')) {
            $data['data']['user_id'] = session('user_id');
        }

        return $data;
    }

    protected function updateProductStock(array $data)
    {
        if (isset($data['id'])) {
            $movement = $this->find($data['id']);
            if ($movement) {
                $productModel = new \App\Models\ProductModel();
                $operation = $movement['type'] === 'in' ? 'add' : 'subtract';
                $productModel->updateStock($movement['product_id'], $movement['quantity'], $operation);
            }
        }
        return $data;
    }

    /**
     * Get movements by product
     */
    public function getByProduct($productId, $limit = null)
    {
        $builder = $this->where('product_id', $productId)
                       ->orderBy('created_at', 'DESC');
        
        if ($limit) {
            $builder->limit($limit);
        }
        
        return $builder->findAll();
    }

    /**
     * Get movements with product info
     */
    public function getWithProductInfo($limit = null, $offset = null)
    {
        $builder = $this->select('stock_movements.*, products.name as product_name, products.sku, users.username')
                        ->join('products', 'products.id = stock_movements.product_id', 'left')
                        ->join('users', 'users.id = stock_movements.user_id', 'left')
                        ->orderBy('stock_movements.created_at', 'DESC');

        if ($limit) {
            $builder->limit($limit, $offset);
        }

        return $builder->findAll();
    }

    /**
     * Get movements by date range
     */
    public function getByDateRange($startDate, $endDate, $productId = null)
    {
        $builder = $this->where('created_at >=', $startDate)
                       ->where('created_at <=', $endDate);

        if ($productId) {
            $builder->where('product_id', $productId);
        }

        return $builder->orderBy('created_at', 'DESC')->findAll();
    }

    /**
     * Get movements by type
     */
    public function getByType($type, $limit = null)
    {
        $builder = $this->where('type', $type)
                       ->orderBy('created_at', 'DESC');
        
        if ($limit) {
            $builder->limit($limit);
        }
        
        return $builder->findAll();
    }

    /**
     * Get movements by reason
     */
    public function getByReason($reason, $limit = null)
    {
        $builder = $this->where('reason', $reason)
                       ->orderBy('created_at', 'DESC');
        
        if ($limit) {
            $builder->limit($limit);
        }
        
        return $builder->findAll();
    }

    /**
     * Create stock adjustment
     */
    public function createAdjustment($productId, $currentStock, $newStock, $reason = 'manual_adjustment', $notes = null)
    {
        $difference = $newStock - $currentStock;
        
        if ($difference == 0) {
            return true; // No change needed
        }

        $type = $difference > 0 ? 'in' : 'out';
        $quantity = abs($difference);

        return $this->insert([
            'product_id' => $productId,
            'type' => $type,
            'quantity' => $quantity,
            'reason' => $reason,
            'notes' => $notes
        ]);
    }

    /**
     * Create initial stock
     */
    public function createInitialStock($productId, $quantity, $notes = null)
    {
        return $this->insert([
            'product_id' => $productId,
            'type' => 'in',
            'quantity' => $quantity,
            'reason' => 'initial_stock',
            'notes' => $notes
        ]);
    }

    /**
     * Create purchase stock
     */
    public function createPurchase($productId, $quantity, $referenceId = null, $notes = null)
    {
        return $this->insert([
            'product_id' => $productId,
            'type' => 'in',
            'quantity' => $quantity,
            'reason' => 'purchase',
            'reference_id' => $referenceId,
            'reference_type' => 'purchase_order',
            'notes' => $notes
        ]);
    }

    /**
     * Create sale stock movement
     */
    public function createSale($productId, $quantity, $referenceId = null, $notes = null)
    {
        return $this->insert([
            'product_id' => $productId,
            'type' => 'out',
            'quantity' => $quantity,
            'reason' => 'sale',
            'reference_id' => $referenceId,
            'reference_type' => 'order',
            'notes' => $notes
        ]);
    }

    /**
     * Create return stock movement
     */
    public function createReturn($productId, $quantity, $referenceId = null, $notes = null)
    {
        return $this->insert([
            'product_id' => $productId,
            'type' => 'in',
            'quantity' => $quantity,
            'reason' => 'return',
            'reference_id' => $referenceId,
            'reference_type' => 'return_order',
            'notes' => $notes
        ]);
    }

    /**
     * Get stock summary by product
     */
    public function getStockSummary($productId)
    {
        $totalIn = $this->where('product_id', $productId)
                       ->where('type', 'in')
                       ->selectSum('quantity')
                       ->first();

        $totalOut = $this->where('product_id', $productId)
                        ->where('type', 'out')
                        ->selectSum('quantity')
                        ->first();

        return [
            'total_in' => $totalIn['quantity'] ?? 0,
            'total_out' => $totalOut['quantity'] ?? 0,
            'current_stock' => ($totalIn['quantity'] ?? 0) - ($totalOut['quantity'] ?? 0)
        ];
    }

    /**
     * Get recent movements
     */
    public function getRecent($limit = 10)
    {
        return $this->select('stock_movements.*, products.name as product_name, products.sku')
                   ->join('products', 'products.id = stock_movements.product_id', 'left')
                   ->orderBy('stock_movements.created_at', 'DESC')
                   ->limit($limit)
                   ->findAll();
    }

    /**
     * Get movements statistics
     */
    public function getStatistics($startDate = null, $endDate = null)
    {
        $builder = $this;
        
        if ($startDate && $endDate) {
            $builder = $builder->where('created_at >=', $startDate)
                             ->where('created_at <=', $endDate);
        }

        $totalIn = $builder->where('type', 'in')->selectSum('quantity')->first();
        $totalOut = $builder->where('type', 'out')->selectSum('quantity')->first();

        return [
            'total_movements' => $this->countAllResults(false),
            'total_in' => $totalIn['quantity'] ?? 0,
            'total_out' => $totalOut['quantity'] ?? 0,
            'net_movement' => ($totalIn['quantity'] ?? 0) - ($totalOut['quantity'] ?? 0)
        ];
    }
}