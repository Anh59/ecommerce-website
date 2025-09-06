<?php

namespace App\Models;

use CodeIgniter\Model;

class CategoryModel extends Model
{
    protected $table            = 'categories';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true; // vì có cột deleted_at
    protected $protectFields    = true;
    
    protected $allowedFields = [
        'name', 'slug', 'description', 'image_url', 'parent_id', 
        'sort_order', 'is_active', 'created_at', 'updated_at', 'deleted_at'
    ];

    protected $validationRules = [
        'name'        => 'required|min_length[2]|max_length[100]',
        'description' => 'max_length[1000]',
        'parent_id'   => 'permit_empty|integer',
        'sort_order'  => 'required|integer',
        'is_active'   => 'required|in_list[0,1]',
    ];

    protected $validationMessages = [
        'name' => [
            'required'   => 'Tên danh mục là bắt buộc',
            'min_length' => 'Tên phải có ít nhất 2 ký tự',
            'max_length' => 'Tên không được quá 100 ký tự',
            'is_unique'  => 'Tên danh mục này đã tồn tại'
        ],
        'description' => [
            'max_length' => 'Mô tả không được quá 1000 ký tự'
        ],
        'parent_id' => [
            'integer' => 'Danh mục cha không hợp lệ'
        ],
        'sort_order' => [
            'required' => 'Thứ tự sắp xếp là bắt buộc',
            'integer'  => 'Thứ tự sắp xếp phải là số',
            'is_unique' => 'Thứ tự sắp xếp đã tồn tại'
        ],
        'is_active' => [
            'required' => 'Trạng thái là bắt buộc',
            'in_list'  => 'Trạng thái không hợp lệ'
        ],
        'image_url' => [
            'uploaded' => 'Bạn phải chọn ảnh cho danh mục',
            'is_image' => 'File phải là ảnh hợp lệ',
            'max_size' => 'Dung lượng ảnh tối đa 2MB'
        ]
    ];

    // Tối ưu: Gộp chung logic validation với tham số động
    public function buildValidationRules($isInsert = true, $id = null)
    {
        $rules = $this->validationRules;
        $messages = $this->validationMessages;
        
        // Thêm unique rules
        $uniqueFields = ['name', 'sort_order'];
        foreach ($uniqueFields as $field) {
            if ($isInsert) {
                $rules[$field] .= "|is_unique[categories.{$field}]";
            } else {
                $rules[$field] .= "|is_unique[categories.{$field},id,{$id}]";
            }
        }
        
        // Image rules - chỉ bắt buộc khi thêm mới
        if ($isInsert) {
            $rules['image_url'] = 'uploaded[image_url]|is_image[image_url]|max_size[image_url,2048]';
        }
        
        return [$rules, $messages];
    }

    // Giữ lại methods cũ để tương thích
    public function rulesForInsert()
    {
        return $this->buildValidationRules(true);
    }

    public function rulesForUpdate($id)
    {
        return $this->buildValidationRules(false, $id);
    }

    // Lấy danh mục cha (parent_id = null hoặc 0)
    public function getParentCategories()
    {
        return $this->where('parent_id IS NULL OR parent_id = 0')
                   ->where('is_active', 1)
                   ->orderBy('sort_order', 'ASC')
                   ->findAll();
    }

    // Lấy danh mục con theo parent_id
    public function getChildCategories($parentId)
    {
        return $this->where('parent_id', $parentId)
                   ->where('is_active', 1)
                   ->orderBy('sort_order', 'ASC')
                   ->findAll();
    }

    // Lấy tất cả danh mục dạng cây
    public function getCategoriesTree()
    {
        $categories = $this->orderBy('sort_order', 'ASC')->findAll();
        return $this->buildTree($categories);
    }

    private function buildTree($categories, $parentId = null)
    {
        $tree = [];
        foreach ($categories as $category) {
            if ($category['parent_id'] == $parentId) {
                $category['children'] = $this->buildTree($categories, $category['id']);
                $tree[] = $category;
            }
        }
        return $tree;
    }

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';
}