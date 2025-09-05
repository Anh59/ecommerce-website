<?php

namespace App\Models;

use CodeIgniter\Model;

class BrandModel extends Model
{
    protected $table            = 'brands';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true; // vì có cột deleted_at
    protected $protectFields    = true;

    protected $allowedFields = [
        'name', 'slug', 'description', 'logo_url', 'website', 'country',
        'is_active', 'sort_order', 'created_at', 'updated_at', 'deleted_at'
    ];


    // ✅ Validation rules mặc định (áp dụng cho cả insert/update)
    protected $validationRules = [
    'name'       => 'required|min_length[2]|max_length[100]',
    'website'    => 'required|valid_url',
    'country'    => 'required|max_length[100]',
    'sort_order' => 'required|integer',
    'is_active'  => 'required|in_list[0,1]',
   
];


  protected $validationMessages = [
    'name' => [
        'required'   => 'Tên thương hiệu là bắt buộc',
        'min_length' => 'Tên phải có ít nhất 2 ký tự',
        'max_length' => 'Tên không được quá 100 ký tự',
        'is_unique'  => 'Tên thương hiệu này đã tồn tại'
    ],
    'website' => [
        'required'  => 'Website là bắt buộc',
        'valid_url' => 'Website không đúng định dạng URL',
        'is_unique' => 'Website này đã tồn tại'
    ],
    'country' => [
        'required'  => 'Quốc gia là bắt buộc',
        'max_length'=> 'Tên quốc gia không được quá 100 ký tự',
        'is_unique' => 'Quốc gia này đã tồn tại'
    ],
    'sort_order' => [
        'required'  => 'Thứ tự sắp xếp là bắt buộc',
        'integer'   => 'Thứ tự sắp xếp phải là số',
        'is_unique' => 'Thứ tự sắp xếp đã tồn tại'
    ],
    'is_active' => [
        'required' => 'Trạng thái là bắt buộc',
        'in_list'  => 'Trạng thái không hợp lệ'
    ],
    'logo_url' => [
        'uploaded' => 'Bạn phải chọn logo cho thương hiệu',
        'is_image' => 'File phải là ảnh hợp lệ',
        'max_size' => 'Dung lượng ảnh tối đa 2MB'
    ]
];


    // ✅ Rule riêng cho thêm mới (name phải unique)
public function rulesForInsert()
{
    $rules = $this->validationRules;

    $rules['name']       .= '|is_unique[brands.name]';
    $rules['website']    .= '|is_unique[brands.website]';
    $rules['country']    .= '|is_unique[brands.country]';
    $rules['sort_order'] .= '|is_unique[brands.sort_order]';

    // logo bắt buộc khi thêm mới
    $rules['logo_url'] = 'uploaded[logo_url]|is_image[logo_url]|max_size[logo_url,2048]';

    $messages = $this->validationMessages;
    $messages['name']['is_unique']       = 'Tên thương hiệu này đã tồn tại';
    $messages['website']['is_unique']    = 'Website này đã tồn tại';
    $messages['country']['is_unique']    = 'Quốc gia này đã tồn tại';
    $messages['sort_order']['is_unique'] = 'Thứ tự sắp xếp đã tồn tại';
    $messages['logo_url']['uploaded']    = 'Bạn phải chọn logo cho thương hiệu';

    return [$rules, $messages];
}




    // ✅ Rule riêng cho update (bỏ qua chính nó)
public function rulesForUpdate($id)
{
    $rules = $this->validationRules;
    $rules['name']    .= "|is_unique[brands.name,id,{$id}]";
    $rules['website'] .= "|is_unique[brands.website,id,{$id}]";
    $rules['country'] .= "|is_unique[brands.country,id,{$id}]";
    $rules['sort_order'] .= "|is_unique[brands.sort_order,id,{$id}]";

    // ✅ Bỏ uploaded / is_image khi update, vì logo có thể giữ nguyên
 

    $messages = $this->validationMessages;
    $messages['name']['is_unique']    = 'Tên thương hiệu này đã tồn tại';
    $messages['website']['is_unique'] = 'Website này đã tồn tại';
    $messages['country']['is_unique'] = 'Quốc gia này đã tồn tại';
    $messages['sort_order']['is_unique'] = 'Thứ tự sắp xếp đã tồn tại';

    return [$rules, $messages];
}


    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';
}
