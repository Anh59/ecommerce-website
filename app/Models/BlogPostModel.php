<?php

namespace App\Models;

use CodeIgniter\Model;

class BlogPostModel extends Model
{
    protected $table            = 'blog_posts';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    
    protected $allowedFields = [
        'title', 'slug', 'excerpt', 'content', 'featured_image', 'image_alt', 
        'author_id', 'author_name', 'category',  
        'status', 'published_at', 'meta_title', 'meta_description', 'view_count', 
        'is_featured', 'reading_time', 'created_at', 'updated_at', 'deleted_at'
    ];

    protected $validationRules = [
        'title'            => 'required|min_length[5]|max_length[255]',
        'excerpt'          => 'required|min_length[10]|max_length[500]',
        'content'          => 'required|min_length[50]',
        'author_name'      => 'required|min_length[2]|max_length[100]',
        'category'         => 'required|max_length[100]',
        'status'           => 'required|in_list[draft,published,archived]',
        'meta_title'       => 'max_length[255]',
        'meta_description' => 'max_length[500]',
        'reading_time'     => 'permit_empty|integer|greater_than[0]',
        'is_featured'      => 'permit_empty|in_list[0,1]', 
        'view_count'       => 'permit_empty|integer'
    ];

    protected $validationMessages = [
        'title' => [
            'required'   => 'Tiêu đề bài viết là bắt buộc',
            'min_length' => 'Tiêu đề phải có ít nhất 5 ký tự',
            'max_length' => 'Tiêu đề không được quá 255 ký tự',
            'is_unique'  => 'Tiêu đề này đã tồn tại'
        ],
        'excerpt' => [
            'required'   => 'Tóm tắt bài viết là bắt buộc',
            'min_length' => 'Tóm tắt phải có ít nhất 10 ký tự',
            'max_length' => 'Tóm tắt không được quá 500 ký tự'
        ],
        'content' => [
            'required'   => 'Nội dung bài viết là bắt buộc',
            'min_length' => 'Nội dung phải có ít nhất 50 ký tự'
        ],
        'author_name' => [
            'required'   => 'Tên tác giả là bắt buộc',
            'min_length' => 'Tên tác giả phải có ít nhất 2 ký tự',
            'max_length' => 'Tên tác giả không được quá 100 ký tự'
        ],
        'category' => [
            'required'   => 'Danh mục là bắt buộc',
            'max_length' => 'Danh mục không được quá 100 ký tự'
        ],
        'status' => [
            'required' => 'Trạng thái là bắt buộc',
            'in_list'  => 'Trạng thái không hợp lệ'
        ],
        'featured_image' => [
            'uploaded' => 'Ảnh đại diện là bắt buộc',
            'is_image' => 'File phải là ảnh hợp lệ',
            'max_size' => 'Dung lượng ảnh tối đa 5MB'
        ]
    ];

    // Build validation rules động
    public function buildValidationRules($isInsert = true, $id = null)
    {
        $rules = $this->validationRules;
        $messages = $this->validationMessages;
        
        // Unique rules cho title và slug
        if ($isInsert) {
            $rules['title'] .= '|is_unique[blog_posts.title]';
            $rules['slug'] = 'is_unique[blog_posts.slug]';
        } else {
            $rules['title'] .= "|is_unique[blog_posts.title,id,{$id}]";
            $rules['slug'] = "is_unique[blog_posts.slug,id,{$id}]";
        }
        
        // Featured image - chỉ bắt buộc khi thêm mới
        if ($isInsert) {
            $rules['featured_image'] = 'uploaded[featured_image]|is_image[featured_image]|max_size[featured_image,5120]';
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

    // Lấy bài viết với thông tin comment count
    public function getPostsWithCommentCount()
    {
        return $this->select('blog_posts.*, COUNT(blog_comments.id) as comment_count')
                   ->join('blog_comments', 'blog_posts.id = blog_comments.post_id', 'left')
                   ->groupBy('blog_posts.id')
                   ->orderBy('blog_posts.created_at', 'DESC')
                   ->findAll();
    }

    // Lấy bài viết theo trạng thái
    public function getPostsByStatus($status = 'published')
    {
        return $this->where('status', $status)
                   ->orderBy('published_at', 'DESC')
                   ->findAll();
    }

    // Lấy bài viết nổi bật
    public function getFeaturedPosts($limit = 5)
    {
        return $this->where('is_featured', 1)
                   ->where('status', 'published')
                   ->orderBy('published_at', 'DESC')
                   ->limit($limit)
                   ->findAll();
    }

    // Tăng lượt xem
    public function incrementViewCount($id)
    {
        $this->where('id', $id)->increment('view_count');
    }

    // Tính toán thời gian đọc (ước tính 200 từ/phút)
    public function calculateReadingTime($content)
    {
        $wordCount = str_word_count(strip_tags($content));
        return max(1, ceil($wordCount / 200));
    }

    // Callback trước khi insert
    protected function beforeInsert(array $data)
    {
        $data = $this->prepareData($data);
        return $data;
    }

    // Callback trước khi update
    protected function beforeUpdate(array $data)
    {
        $data = $this->prepareData($data);
        return $data;
    }

    private function prepareData(array $data)
    {
        if (isset($data['data']['title'])) {
            // Tạo slug từ title
            $data['data']['slug'] = url_title($data['data']['title'], '-', true);
            
            // Tự động tạo meta_title nếu chưa có
            if (empty($data['data']['meta_title'])) {
                $data['data']['meta_title'] = $data['data']['title'];
            }
        }

        if (isset($data['data']['content'])) {
            // Tính toán thời gian đọc nếu chưa có
            if (!isset($data['data']['reading_time']) || empty($data['data']['reading_time'])) {
                $data['data']['reading_time'] = $this->calculateReadingTime($data['data']['content']);
            }
        }

        // Set default values
        if (!isset($data['data']['view_count'])) {
            $data['data']['view_count'] = 0;
        }
        
        if (!isset($data['data']['is_featured'])) {
            $data['data']['is_featured'] = 0;
        };

 

       


        return $data;
    }

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';
}