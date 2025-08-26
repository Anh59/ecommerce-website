<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductModel extends Model
{
    protected $table = 'products';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'name','slug','sku','category_id','brand_id','price','sale_price',
        'short_description','description','specifications','main_image',
        'stock_quantity','min_stock_level','stock_status','weight','dimensions',
        'material','origin_country','warranty_period','is_featured','is_active',
        'meta_title','meta_description'
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $useSoftDeletes = true;
    protected $deletedField  = 'deleted_at';
}
