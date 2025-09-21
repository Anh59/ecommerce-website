<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\BlogPostModel;
use App\Models\BlogCommentModel;

class BlogController extends BaseController
{
    protected $blogPostModel;
    protected $blogCommentModel;

    public function __construct()
    {
        $this->blogPostModel = new BlogPostModel();
        $this->blogCommentModel = new BlogCommentModel();
    }

    /**
     * Display blog list page
     */
    public function index()
    {
        $perPage = 5;
        $page = (int) ($this->request->getVar('page') ?? 1);
        
        // Get published posts with pagination
        $posts = $this->blogPostModel
            ->where('status', 'published')
            ->orderBy('published_at', 'DESC')
            ->paginate($perPage, 'default', $page);

        // Get pagination links
        $pager = $this->blogPostModel->pager;

        // Get featured posts for sidebar
        $featuredPosts = $this->blogPostModel->getFeaturedPosts(4);

        // Get categories with post count
        $categories = $this->getCategories();

        // Get recent posts
        $recentPosts = $this->blogPostModel
            ->where('status', 'published')
            ->orderBy('published_at', 'DESC')
            ->limit(4)
            ->findAll();

        $data = [
            'title' => 'Blog - Latest News & Articles',
            'posts' => $posts,
            'pager' => $pager,
            'featuredPosts' => $featuredPosts,
            'categories' => $categories,
            'recentPosts' => $recentPosts,
            'currentPage' => $page,
            'totalPages' => $pager->getPageCount()
        ];

        return view('Customers/blog', $data);
    }

    /**
     * Display single blog post
     */
    public function single($slug)
    {
        // Get post by slug
        $post = $this->blogPostModel
            ->where('slug', $slug)
            ->where('status', 'published')
            ->first();

        if (!$post) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        // Increment view count
        $this->blogPostModel->incrementViewCount($post['id']);

        // Get comments
        $comments = $this->blogCommentModel->getCommentsByPost($post['id']);

        // Get comment count
        $commentCount = $this->blogCommentModel->getCommentCountByPost($post['id']);

        // Get related posts (same category)
        $relatedPosts = $this->blogPostModel
            ->where('category', $post['category'])
            ->where('id !=', $post['id'])
            ->where('status', 'published')
            ->orderBy('published_at', 'DESC')
            ->limit(3)
            ->findAll();

        // Get categories for sidebar
        $categories = $this->getCategories();

        // Get recent posts for sidebar
        $recentPosts = $this->blogPostModel
            ->where('status', 'published')
            ->where('id !=', $post['id'])
            ->orderBy('published_at', 'DESC')
            ->limit(4)
            ->findAll();

        // Get previous and next posts
        $previousPost = $this->blogPostModel
            ->where('published_at <', $post['published_at'])
            ->where('status', 'published')
            ->orderBy('published_at', 'DESC')
            ->first();

        $nextPost = $this->blogPostModel
            ->where('published_at >', $post['published_at'])
            ->where('status', 'published')
            ->orderBy('published_at', 'ASC')
            ->first();

        $data = [
            'title' => $post['meta_title'] ?: $post['title'],
            'metaDescription' => $post['meta_description'] ?: $post['excerpt'],
            'post' => $post,
            'comments' => $comments,
            'commentCount' => $commentCount,
            'relatedPosts' => $relatedPosts,
            'categories' => $categories,
            'recentPosts' => $recentPosts,
            'previousPost' => $previousPost,
            'nextPost' => $nextPost
        ];

        return view('Customers/single-blog', $data);
    }

    /**
     * Add comment to blog post
     */
    public function addComment()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(404);
        }

        $validation = \Config\Services::validation();
        $validation->setRules([
            'post_id' => 'required|integer',
            'author_name' => 'required|min_length[2]|max_length[100]',
            'author_email' => 'required|valid_email|max_length[100]',
            'comment' => 'required|min_length[5]|max_length[1000]',
            'parent_id' => 'permit_empty|integer'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validation->getErrors()
            ]);
        }

        $data = [
            'post_id' => $this->request->getPost('post_id'),
            'author_name' => $this->request->getPost('author_name'),
            'author_email' => $this->request->getPost('author_email'),
            'comment' => $this->request->getPost('comment'),
            'parent_id' => $this->request->getPost('parent_id') ?: null,
            'is_approved' => 1, // Auto approve for now
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        // Add customer_id if logged in
        if (session()->has('customer_id')) {
            $data['customer_id'] = session('customer_id');
        }

        try {
            $commentId = $this->blogCommentModel->insert($data);
            
            if ($commentId) {
                // Get the newly created comment
                $newComment = $this->blogCommentModel->find($commentId);
                
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Bình luận đã được thêm thành công',
                    'comment' => $newComment
                ]);
            }
        } catch (\Exception $e) {
            log_message('error', 'Add comment error: ' . $e->getMessage());
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Có lỗi xảy ra khi thêm bình luận'
        ]);
    }

    /**
     * Search blog posts
     */
    public function search()
    {
        $keyword = $this->request->getVar('keyword') ?? '';
        $category = $this->request->getVar('category') ?? '';
        $perPage = 5;
        $page = (int) ($this->request->getVar('page') ?? 1);

        $builder = $this->blogPostModel->where('status', 'published');

        // Search by keyword
        if (!empty($keyword)) {
            $builder->groupStart()
                   ->like('title', $keyword)
                   ->orLike('excerpt', $keyword)
                   ->orLike('content', $keyword)
                   ->groupEnd();
        }

        // Filter by category
        if (!empty($category)) {
            $builder->where('category', $category);
        }

        $posts = $builder->orderBy('published_at', 'DESC')
                        ->paginate($perPage, 'default', $page);

        $pager = $this->blogPostModel->pager;

        // Get sidebar data
        $featuredPosts = $this->blogPostModel->getFeaturedPosts(4);
        $categories = $this->getCategories();
        $recentPosts = $this->blogPostModel
            ->where('status', 'published')
            ->orderBy('published_at', 'DESC')
            ->limit(4)
            ->findAll();

        $data = [
            'title' => 'Search Results - Blog',
            'posts' => $posts,
            'pager' => $pager,
            'featuredPosts' => $featuredPosts,
            'categories' => $categories,
            'recentPosts' => $recentPosts,
            'searchKeyword' => $keyword,
            'searchCategory' => $category,
            'currentPage' => $page,
            'totalPages' => $pager->getPageCount(),
            'totalResults' => $pager->getTotal()
        ];

        return view('Customers/blog', $data);
    }

    /**
     * Get categories with post count
     */
    private function getCategories()
    {
        $categories = $this->blogPostModel
            ->select('category, COUNT(*) as post_count')
            ->where('status', 'published')
            ->groupBy('category')
            ->findAll();

        return $categories;
    }

    /**
     * Get posts by category
     */
    public function category($category)
    {
        $perPage = 5;
        $page = (int) ($this->request->getVar('page') ?? 1);
        
        $posts = $this->blogPostModel
            ->where('category', urldecode($category))
            ->where('status', 'published')
            ->orderBy('published_at', 'DESC')
            ->paginate($perPage, 'default', $page);

        $pager = $this->blogPostModel->pager;

        // Get sidebar data
        $featuredPosts = $this->blogPostModel->getFeaturedPosts(4);
        $categories = $this->getCategories();
        $recentPosts = $this->blogPostModel
            ->where('status', 'published')
            ->orderBy('published_at', 'DESC')
            ->limit(4)
            ->findAll();

        $data = [
            'title' => 'Category: ' . urldecode($category) . ' - Blog',
            'posts' => $posts,
            'pager' => $pager,
            'featuredPosts' => $featuredPosts,
            'categories' => $categories,
            'recentPosts' => $recentPosts,
            'currentCategory' => urldecode($category),
            'currentPage' => $page,
            'totalPages' => $pager->getPageCount()
        ];

        return view('Customers/blog', $data);
    }
}