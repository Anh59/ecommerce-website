<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\BlogPostModel;
use App\Models\BlogCommentModel;

class BlogPostController extends BaseController
{
    protected $blogPostModel;
    protected $blogCommentModel;

    public function __construct()
    {
        $this->blogPostModel = new BlogPostModel();
        $this->blogCommentModel = new BlogCommentModel();
    }

    // Danh sách bài viết
    public function index()
    {
        return view('Dashboard/BlogPost/table');
    }

    // Danh sách cho DataTables
   // Danh sách cho DataTables
public function list()
{
    if (!$this->request->isAJAX()) {
        return $this->response->setStatusCode(400);
    }

    // Tự động publish các bài viết đã đến hạn
    $autoPublished = $this->blogPostModel->autoPublishScheduledPosts();
    if ($autoPublished > 0) {
        log_message('info', "Auto-published {$autoPublished} posts");
    }

    $posts = $this->blogPostModel->getPostsWithCommentCount();

    return $this->response->setJSON([
        'status' => 'success',
        'data'   => $posts,
        'token'  => csrf_hash()
    ]);
}

    // Thêm bài viết
    public function store()
    {
        return $this->saveData();
    }

    // Cập nhật bài viết
    public function update($id)
    {
        return $this->saveData($id);
    }

 // FINAL FIX: saveData method với slug được tạo TRƯỚC khi insert
    private function saveData($id = null)
{
    if (!$this->request->isAJAX()) {
        return $this->response->setStatusCode(400);
    }

    $isUpdate = !is_null($id);

    // Lấy dữ liệu từ form
    $data = $this->request->getPost([
        'title', 'excerpt', 'content', 'author_name', 'category', 
        'status', 'meta_title', 'meta_description', 'image_alt',
        'is_featured', 'published_at'
    ]);

    // Tạo slug từ title TRƯỚC khi validate
    if (!empty($data['title'])) {
        $data['slug'] = $this->blogPostModel->generateUniqueSlug($data['title'], $id);
        log_message('debug', 'Generated slug: ' . $data['slug'] . ' from title: ' . $data['title']);
    }

    // Validate với slug đã được tạo
    [$rules, $messages] = $isUpdate 
        ? $this->blogPostModel->rulesForUpdate($id)
        : $this->blogPostModel->rulesForInsert();

    if (!$this->validate($rules, $messages)) {
        return $this->response->setJSON([
            'status' => 'error',
            'message' => $this->validator->getErrors(),
            'token'   => csrf_hash()
        ]);
    }

    // Set default values
    $data['is_featured'] = $data['is_featured'] ?? 0;
    $data['view_count'] = $isUpdate ? null : 0;
    
    // Xử lý meta fields
    if (empty($data['meta_title'])) {
        $data['meta_title'] = $data['title'];
    }
    if (empty($data['meta_description'])) {
        $data['meta_description'] = substr(strip_tags($data['excerpt']), 0, 160);
    }

    // Tính toán reading_time từ content
    if (!empty($data['content'])) {
        $readingTime = $this->blogPostModel->calculateReadingTime($data['content']);
        $data['reading_time'] = (int) $readingTime; // Ép kiểu về integer
    }

    // === XỬ LÝ LOGIC PUBLISHED_AT THEO TRẠNG THÁI ===
    $status = $data['status'];
    $publishedAt = $data['published_at'] ?? null;

    if ($status === 'published') {
        // Nếu status là "published"
        if (empty($publishedAt)) {
            // Không có thời gian → dùng thời gian hiện tại
            $data['published_at'] = date('Y-m-d H:i:s');
        } else {
            // Có thời gian → giữ nguyên (cho phép hẹn giờ xuất bản)
            $data['published_at'] = date('Y-m-d H:i:s', strtotime($publishedAt));
        }
    } elseif ($status === 'archived') {
        // Nếu status là "archived"
        if (empty($publishedAt)) {
            // Không có thời gian → dùng thời gian hiện tại
            $data['published_at'] = date('Y-m-d H:i:s');
        } else {
            // Có thời gian → giữ nguyên (lưu trữ với thời gian được chọn)
            $data['published_at'] = date('Y-m-d H:i:s', strtotime($publishedAt));
        }
    } else {
        // Status là "draft" → không set published_at
        $data['published_at'] = null;
    }

    // Set author_id nếu có session user
    if (session()->has('user_id')) {
        $data['author_id'] = session('user_id');
    }

    // Xử lý upload ảnh
    $this->handleImageUpload($data, $id);

    try {
        // Lưu hoặc cập nhật
        if ($isUpdate) {
            unset($data['view_count']);
            $result = $this->blogPostModel->update($id, $data);
            $message = 'Cập nhật bài viết thành công';
            log_message('debug', 'Updated post ID: ' . $id . ' with slug: ' . $data['slug']);
        } else {
            $result = $this->blogPostModel->insert($data);
            $message = 'Thêm bài viết thành công';
            log_message('debug', 'Inserted new post with ID: ' . $result . ' and slug: ' . $data['slug']);
        }

        if ($result === false) {
            $errors = $this->blogPostModel->errors();
            log_message('error', 'Model validation errors: ' . print_r($errors, true));
            return $this->response->setJSON([
                'status' => 'error',
                'message' => $errors ?: 'Có lỗi xảy ra khi lưu bài viết',
                'token'   => csrf_hash()
            ]);
        }

        return $this->response->setJSON([
            'status' => 'success',
            'message' => $message,
            'token'   => csrf_hash()
        ]);

    } catch (\Exception $e) {
        log_message('error', 'BlogPostController saveData error: ' . $e->getMessage());
        log_message('error', 'Data being saved: ' . print_r($data, true));
        
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Có lỗi hệ thống: ' . $e->getMessage(),
            'token'   => csrf_hash()
        ]);
    }
}

    private function handleImageUpload(&$data, $id = null)
    {
        // Xử lý ảnh đại diện
        $featuredImage = $this->request->getFile('featured_image');
        if ($featuredImage && $featuredImage->isValid() && !$featuredImage->hasMoved()) {
            $newName = $featuredImage->getRandomName();
            $featuredImage->move('uploads/blog/featured', $newName);
            $data['featured_image'] = 'uploads/blog/featured/' . $newName;

            // Xóa ảnh cũ khi update
            if ($id && $old = $this->blogPostModel->find($id)) {
                $this->deleteOldImage($old['featured_image']);
            }
        }

        
    }

    private function deleteOldImage($imagePath)
    {
        if (!empty($imagePath) && file_exists(FCPATH . $imagePath)) {
            unlink(FCPATH . $imagePath);
        }
    }

    // Lấy bài viết để edit
    public function edit($id)
    {
        $post = $this->blogPostModel->find($id);

        if (!$post) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Không tìm thấy bài viết',
                'token' => csrf_hash()
            ]);
        }

        // Decode JSON fields
       

        return $this->response->setJSON([
            'status' => 'success',
            'post' => $post,
            'token' => csrf_hash()
        ]);
    }

    // Xem chi tiết bài viết với comments
    public function view($id)
    {
        $post = $this->blogPostModel->find($id);

        if (!$post) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        // Tăng lượt xem
        $this->blogPostModel->incrementViewCount($id);

        // Lấy comments
        $comments = $this->blogCommentModel->getCommentsByPost($id);

        
      
       

        $data = [
            'post' => $post,
            'comments' => $comments,
            'commentCount' => count($comments)
        ];

        return view('Dashboard/BlogPost/view', $data);
    }

    // Xóa bài viết
    public function delete($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400);
        }

        // Kiểm tra xem có comment nào không
        $commentCount = $this->blogCommentModel->getCommentCountByPost($id, false);
        if ($commentCount > 0) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => "Không thể xóa bài viết này vì có {$commentCount} bình luận liên quan",
                'token' => csrf_hash()
            ]);
        }

        // Lấy thông tin bài viết để xóa ảnh
        $post = $this->blogPostModel->find($id);
        
        if ($this->blogPostModel->delete($id)) {
            // Xóa ảnh liên quan
            if (!empty($post['featured_image'])) {
                $this->deleteOldImage($post['featured_image']);
            }
            
            if (!empty($post['gallery_images'])) {
                $galleryImages = json_decode($post['gallery_images'], true);
                foreach ($galleryImages as $image) {
                    $this->deleteOldImage($image);
                }
            }

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Xóa bài viết thành công',
                'token' => csrf_hash()
            ]);
        }

        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Lỗi khi xóa bài viết',
            'token' => csrf_hash()
        ]);
    }

    // Toggle trạng thái nổi bật
    public function toggleFeatured($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400);
        }

        $post = $this->blogPostModel->find($id);
        if (!$post) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Không tìm thấy bài viết',
                'token' => csrf_hash()
            ]);
        }

        $newStatus = $post['is_featured'] ? 0 : 1;
        $this->blogPostModel->update($id, ['is_featured' => $newStatus]);

        return $this->response->setJSON([
            'status' => 'success',
            'message' => $newStatus ? 'Đã đặt làm bài viết nổi bật' : 'Đã bỏ khỏi bài viết nổi bật',
            'is_featured' => $newStatus,
            'token' => csrf_hash()
        ]);
    }

    // Thay đổi trạng thái bài viết
    public function changeStatus($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400);
        }

        $status = $this->request->getPost('status');
        $validStatuses = ['draft', 'published', 'archived'];

        if (!in_array($status, $validStatuses)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Trạng thái không hợp lệ',
                'token' => csrf_hash()
            ]);
        }

        $updateData = ['status' => $status];
        
        // Nếu chuyển sang published và chưa có published_at
        if ($status === 'published') {
            $post = $this->blogPostModel->find($id);
            if (empty($post['published_at'])) {
                $updateData['published_at'] = date('Y-m-d H:i:s');
            }
        }

        $this->blogPostModel->update($id, $updateData);

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Cập nhật trạng thái thành công',
            'token' => csrf_hash()
        ]);
    }
}