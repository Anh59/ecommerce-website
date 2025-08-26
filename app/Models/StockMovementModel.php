<?php

namespace App\Models;

use CodeIgniter\Model;

class StockMovementModel extends Model
{
    protected $table = 'stock_movements';
    protected $primaryKey = 'id';
    protected $allowedFields = ['product_id','type','quantity','reason','reference_id','reference_type','notes','created_by','created_at'];
    public $timestamps = false;
}
