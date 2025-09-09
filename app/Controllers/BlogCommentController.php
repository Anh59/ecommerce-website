<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\BlogCommentModel;
use App\Models\BlogPostModel;

class BlogCommentController extends BaseController
{
    protected $blogCommentModel;
    protected $blogPostModel;

    public function __construct()
    {
        $this->blogCommentModel = new BlogCommentModel();
        $this->blogPostModel = new BlogPostModel();
    }

    // Danh sách bình luận
    public function index()
    {
        return view('Dashboard/BlogPost/comment');
    }

    // Danh sách cho DataTables
    public function list()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400);
        }

        $comments = $this->blogCommentModel
                        ->select('blog_comments.*, blog_posts.title as post_title, blog_posts.slug as post_slug')
                        ->join('blog_posts', 'blog_comments.post_id = blog_posts.id')
                        ->orderBy('blog_comments.created_at', 'DESC')
                        ->findAll();

        return $this->response->setJSON([
            'status' => 'success',
            'data'   => $comments,
            'token'  => csrf_hash()
        ]);
    }

    // Thêm bình luận (từ frontend)
    public function store()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400);
        }

        if (!$this->validate($this->blogCommentModel->getValidationRules())) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => $this->validator->getErrors(),
                'token'   => csrf_hash()
            ]);
        }

        $data = $this->request->getPost([
            'post_id', 'parent_id', 'author_name', 'author_email', 'comment'
        ]);

        // Set customer_id nếu user đã đăng nhập
        if (session()->has('customer_id')) {
            $data['customer_id'] = session('customer_id');
        }

        // Mặc định cần duyệt
        $data['is_approved'] = 0;

        if ($this->blogCommentModel->insert($data)) {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Bình luận của bạn đã được gửi và đang chờ duyệt',
                'token'   => csrf_hash()
            ]);
        }

        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Lỗi khi gửi bình luận',
            'token'   => csrf_hash()
        ]);
    }

    // Duyệt bình luận
    public function approve($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400);
        }

        if ($this->blogCommentModel->approveComment($id)) {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Đã duyệt bình luận',
                'token'   => csrf_hash()
            ]);
        }

        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Lỗi khi duyệt bình luận',
            'token'   => csrf_hash()
        ]);
    }

    // Từ chối bình luận
    public function reject($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400);
        }

        if ($this->blogCommentModel->rejectComment($id)) {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Đã từ chối bình luận',
                'token'   => csrf_hash()
            ]);
        }

        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Lỗi khi từ chối bình luận',
            'token'   => csrf_hash()
        ]);
    }

    // Xóa bình luận
    public function delete($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400);
        }

        if ($this->blogCommentModel->delete($id)) {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Đã xóa bình luận',
                'token'   => csrf_hash()
            ]);
        }

        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Lỗi khi xóa bình luận',
            'token'   => csrf_hash()
        ]);
    }

    // Lấy bình luận chưa duyệt
    public function pending()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400);
        }

        $comments = $this->blogCommentModel->getPendingComments();

        return $this->response->setJSON([
            'status' => 'success',
            'data'   => $comments,
            'token'  => csrf_hash()
        ]);
    }

    // Toggle trạng thái duyệt
    public function toggleApprove($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400);
        }

        $comment = $this->blogCommentModel->find($id);
        if (!$comment) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Không tìm thấy bình luận',
                'token'   => csrf_hash()
            ]);
        }

        $newStatus = $comment['is_approved'] ? 0 : 1;
        $this->blogCommentModel->update($id, ['is_approved' => $newStatus]);

        return $this->response->setJSON([
            'status' => 'success',
            'message' => $newStatus ? 'Đã duyệt bình luận' : 'Đã hủy duyệt bình luận',
            'is_approved' => $newStatus,
            'token' => csrf_hash()
        ]);
    }
}