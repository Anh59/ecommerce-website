<?php

namespace App\Models;

use CodeIgniter\Model;

class CategoryModel extends Model
{
    protected $table            = 'categories';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    
    protected $allowedFields = [
        'name', 'slug', 'description', 'status', 'created_at', 'updated_at'
    ];

    protected $validationRules = [
        'name'        => 'required|min_length[2]|max_length[100]',
        'description' => 'max_length[500]',
        'status'      => 'required|in_list[0,1]',
    ];

    protected $validationMessages = [
        'name' => [
            'required'   => 'Tên danh mục là bắt buộc',
            'min_length' => 'Tên phải có ít nhất 2 ký tự',
            'max_length' => 'Tên không được quá 100 ký tự',
            'is_unique'  => 'Tên danh mục này đã tồn tại'
        ],
        'description' => [
            'max_length' => 'Mô tả không được quá 500 ký tự'
        ],
        'status' => [
            'required' => 'Trạng thái là bắt buộc',
            'in_list'  => 'Trạng thái không hợp lệ'
        ]
    ];

    // Tối ưu: Gộp chung logic validation với tham số động
    public function buildValidationRules($isInsert = true, $id = null)
{
    $rules = $this->validationRules;
    $messages = $this->validationMessages;

    // Thêm unique rules cho name
    if ($isInsert) {
        $rules['name'] .= '|is_unique[categories.name]';
    } else {
        $rules['name'] .= "|is_unique[categories.name,id,{$id}]";
    }

    return [$rules, $messages];
}

public function rulesForInsert()
{
    return $this->buildValidationRules(true);
}

public function rulesForUpdate($id)
{
    return $this->buildValidationRules(false, $id);
}


    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];
}