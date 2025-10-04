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
        'author_id', 'author_name', 'category', 'tags',  
        'status', 'published_at', 'meta_title', 'meta_description', 'view_count', 
        'is_featured', 'reading_time', 'created_at', 'updated_at', 'deleted_at'
    ];

    // Đã xóa slug khỏi validation rules
    protected $validationRules = [
        'title'            => 'required|min_length[5]|max_length[255]',
        'excerpt'          => 'required|min_length[10]|max_length[500]',
        'content'          => 'required|min_length[50]',
        'author_name'      => 'required|min_length[2]|max_length[100]',
        'category'         => 'required|max_length[100]',
        'status'           => 'required|in_list[draft,published,archived]',
        'meta_title'       => 'max_length[255]',
        'meta_description' => 'max_length[500]',
        'reading_time'     => 'permit_empty|is_natural', 
        'is_featured'      => 'permit_empty|in_list[0,1]', 
        'view_count'       => 'permit_empty|is_natural'
    ];

    // Đã xóa slug khỏi validation messages
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

    public function buildValidationRules($isInsert = true, $id = null)
    {
        $rules = $this->validationRules;
        $messages = $this->validationMessages;
        
        // Chỉ giữ is_unique cho title, đã xóa is_unique cho slug
        if ($isInsert) {
            $rules['title'] .= '|is_unique[blog_posts.title]';
        } else {
            $rules['title'] .= "|is_unique[blog_posts.title,id,{$id}]";
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

    /**
     * Generate unique slug from title
     */
    /**
 * Generate unique slug from title (without accents)
 */
public function generateUniqueSlug($title, $id = null)
{
    // Remove accents using iconv
    $slug = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $title);
    
    // Remove any remaining special characters
    $slug = preg_replace("/[^a-zA-Z0-9\s]/", "", $slug);
    
    // Now create URL-friendly slug
    $baseSlug = url_title($slug, '-', true);
    $slug = $baseSlug;
    $counter = 1;

    // Check if slug exists
    while (true) {
        $query = $this->where('slug', $slug);
        if ($id) {
            $query->where('id !=', $id);
        }
        
        $existing = $query->first();
        if (!$existing) {
            break; // Slug is unique
        }
        
        // Add counter to make it unique
        $slug = $baseSlug . '-' . $counter;
        $counter++;
    }

    return $slug;
}

    // Lấy bài viết với thông tin comment count
    public function getPostsWithCommentCount()
    {
        return $this->select('blog_posts.*, COUNT(blog_comments.id) as comment_count')
                   ->join('blog_comments', 'blog_posts.id = blog_comments.post_id AND blog_comments.is_approved = 1', 'left')
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

    // Get post by slug
    public function getPostBySlug($slug)
    {
        return $this->where('slug', $slug)
                    ->where('status', 'published')
                    ->first();
    }

    // Tăng lượt xem
    public function incrementViewCount($id)
    {
        return $this->set('view_count', 'view_count + 1', false)
                   ->where('id', $id)
                   ->update();
    }

    // Tính toán thời gian đọc (ước tính 200 từ/phút)
    public function calculateReadingTime($content)
    {
        $wordCount = str_word_count(strip_tags($content));
        return max(1, ceil($wordCount / 200));
    }
    /**
 * Tự động publish các bài viết archived đã đến hạn
 */
public function autoPublishScheduledPosts()
{
    $now = date('Y-m-d H:i:s');
    
    // Tìm các bài viết archived có published_at <= now
    $posts = $this->where('status', 'archived')
                  ->where('published_at <=', $now)
                  ->where('published_at IS NOT NULL')
                  ->findAll();
    
    if (!empty($posts)) {
        foreach ($posts as $post) {
            $this->update($post['id'], ['status' => 'published']);
            log_message('info', "Auto-published post ID {$post['id']}: {$post['title']}");
        }
    }
    
    return count($posts);
}
    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';
}