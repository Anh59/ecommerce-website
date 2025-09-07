<?php

namespace App\Models;

use CodeIgniter\Model;

class BlogCommentModel extends Model
{
    protected $table            = 'blog_comments';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    
    protected $allowedFields = [
        'post_id', 'customer_id', 'parent_id', 'author_name', 
        'author_email', 'comment', 'is_approved', 'created_at', 'updated_at'
    ];

    protected $validationRules = [
        'post_id'      => 'required|integer',
        'author_name'  => 'required|min_length[2]|max_length[100]',
        'author_email' => 'required|valid_email|max_length[100]',
        'comment'      => 'required|min_length[5]|max_length[1000]',
        'parent_id'    => 'permit_empty|integer',
        'customer_id'  => 'permit_empty|integer',
        'is_approved'  => 'in_list[0,1]'
    ];

    protected $validationMessages = [
        'post_id' => [
            'required' => 'ID bài viết là bắt buộc',
            'integer'  => 'ID bài viết không hợp lệ'
        ],
        'author_name' => [
            'required'   => 'Tên người bình luận là bắt buộc',
            'min_length' => 'Tên phải có ít nhất 2 ký tự',
            'max_length' => 'Tên không được quá 100 ký tự'
        ],
        'author_email' => [
            'required'    => 'Email là bắt buộc',
            'valid_email' => 'Email không hợp lệ',
            'max_length'  => 'Email không được quá 100 ký tự'
        ],
        'comment' => [
            'required'   => 'Nội dung bình luận là bắt buộc',
            'min_length' => 'Bình luận phải có ít nhất 5 ký tự',
            'max_length' => 'Bình luận không được quá 1000 ký tự'
        ]
    ];

    // Lấy comment theo bài viết với cấu trúc cây (parent-child)
    public function getCommentsByPost($postId, $approved = true)
    {
        $query = $this->where('post_id', $postId);
        
        if ($approved) {
            $query->where('is_approved', 1);
        }
        
        $comments = $query->orderBy('created_at', 'ASC')->findAll();
        
        return $this->buildCommentTree($comments);
    }

    // Xây dựng cấu trúc cây comment
    private function buildCommentTree($comments, $parentId = null)
    {
        $tree = [];
        foreach ($comments as $comment) {
            if ($comment['parent_id'] == $parentId) {
                $comment['replies'] = $this->buildCommentTree($comments, $comment['id']);
                $tree[] = $comment;
            }
        }
        return $tree;
    }

    // Lấy số lượng comment theo bài viết
    public function getCommentCountByPost($postId, $approved = true)
    {
        $query = $this->where('post_id', $postId);
        
        if ($approved) {
            $query->where('is_approved', 1);
        }
        
        return $query->countAllResults();
    }

    // Lấy comment chưa được duyệt
    public function getPendingComments()
    {
        return $this->select('blog_comments.*, blog_posts.title as post_title')
                   ->join('blog_posts', 'blog_comments.post_id = blog_posts.id')
                   ->where('blog_comments.is_approved', 0)
                   ->orderBy('blog_comments.created_at', 'DESC')
                   ->findAll();
    }

    // Duyệt comment
    public function approveComment($id)
    {
        return $this->update($id, ['is_approved' => 1]);
    }

    // Từ chối comment
    public function rejectComment($id)
    {
        return $this->update($id, ['is_approved' => 0]);
    }

    // Lấy comment gần đây
    public function getRecentComments($limit = 10, $approved = true)
    {
        $query = $this->select('blog_comments.*, blog_posts.title as post_title, blog_posts.slug as post_slug')
                      ->join('blog_posts', 'blog_comments.post_id = blog_posts.id');
        
        if ($approved) {
            $query->where('blog_comments.is_approved', 1);
        }
        
        return $query->orderBy('blog_comments.created_at', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}