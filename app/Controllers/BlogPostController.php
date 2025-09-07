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
    public function list()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400);
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

    private function saveData($id = null)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400);
        }

        $isUpdate = !is_null($id);
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

        // Lấy dữ liệu
        $data = $this->request->getPost([
            'title', 'excerpt', 'content', 'author_name', 'category', 
            'status', 'meta_title', 'meta_description', 'image_alt',
            'is_featured', 'published_at'
        ]);

        // Xử lý tags
        $tags = $this->request->getPost('tags');
        if ($tags) {
            $data['tags'] = is_string($tags) ? explode(',', $tags) : $tags;
        }

        // Xử lý published_at
        if ($data['status'] === 'published' && empty($data['published_at'])) {
            $data['published_at'] = date('Y-m-d H:i:s');
        }

        // Set author_id nếu có session user
        if (session()->has('user_id')) {
            $data['author_id'] = session('user_id');
        }

        // Xử lý upload ảnh
        $this->handleImageUpload($data, $id);

        // Lưu hoặc cập nhật
        if ($isUpdate) {
            $this->blogPostModel->update($id, $data);
            $message = 'Cập nhật bài viết thành công';
        } else {
            $this->blogPostModel->insert($data);
            $message = 'Thêm bài viết thành công';
        }

        return $this->response->setJSON([
            'status' => 'success',
            'message' => $message,
            'token'   => csrf_hash()
        ]);
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

        // Xử lý gallery images
        $galleryFiles = $this->request->getFiles();
        if (isset($galleryFiles['gallery_images'])) {
            $galleryImages = [];
            foreach ($galleryFiles['gallery_images'] as $file) {
                if ($file->isValid() && !$file->hasMoved()) {
                    $newName = $file->getRandomName();
                    $file->move('uploads/blog/gallery', $newName);
                    $galleryImages[] = 'uploads/blog/gallery/' . $newName;
                }
            }
            if (!empty($galleryImages)) {
                $data['gallery_images'] = $galleryImages;
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
        if (!empty($post['tags'])) {
            $post['tags'] = json_decode($post['tags'], true);
        }
        if (!empty($post['gallery_images'])) {
            $post['gallery_images'] = json_decode($post['gallery_images'], true);
        }

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

        // Decode JSON fields
        if (!empty($post['tags'])) {
            $post['tags'] = json_decode($post['tags'], true);
        }
        if (!empty($post['gallery_images'])) {
            $post['gallery_images'] = json_decode($post['gallery_images'], true);
        }

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