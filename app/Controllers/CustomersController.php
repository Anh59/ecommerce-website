<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\CustomerModel;

class CustomersController extends BaseController
{
    public function register()
    {
        return view('Customers/customers_register');
    }

    public function processRegistration()
    {
        $customerModel = new CustomerModel();
        $validation = \Config\Services::validation();
        
        // Quy tắc xác thực bao gồm mật khẩu mạnh và số điện thoại hợp lệ
        $validation->setRules([
            'name' => 'required',
            'email' => 'required|valid_email|is_unique[customers.email]',
            'phone' => 'required|numeric|min_length[10]|max_length[15]',
            'address' => 'required',
            'password' => 'required|min_length[8]|regex_match[/(?=.*[A-Za-z])(?=.*\d)(?=.*[!@#$%^&*])/]', // Kiểm tra độ mạnh mật khẩu
        ], [
            'password' => [
                'regex_match' => 'Mật khẩu phải có ít nhất 8 ký tự, bao gồm chữ cái, số và ký tự đặc biệt.'
            ],
            'email' => [
                'is_unique' => 'Email đã tồn tại trong hệ thống.'
            ],
            'phone' => [
                'numeric' => 'Số điện thoại chỉ được chứa số.',
                'min_length' => 'Số điện thoại phải có ít nhất 10 số.',
                'max_length' => 'Số điện thoại không được vượt quá 15 số.'
            ]
        ]);

        if (!$this->validate($validation->getRules())) {
            return $this->response->setJSON(['status' => 'error', 'errors' => $this->validator->getErrors()]);
        }
    
        // Generate OTP
        $otp = rand(100000, 999999);
        $otp_expiration = date('Y-m-d H:i:s', strtotime('+5 minutes')); // 5 phút hết hạn
    
        // Lưu dữ liệu khách hàng kèm OTP
        $customerModel->save([
            'name' => $this->request->getPost('name'),
            'email' => $this->request->getPost('email'),
            'phone' => $this->request->getPost('phone'),
            'address' => $this->request->getPost('address'),
            'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'otp' => $otp,
            'otp_expiration' => $otp_expiration,
            'is_verified' => false,
        ]);
    
        // Gửi OTP qua email
        $this->_sendOTP($this->request->getPost('email'), $otp);
    
        return $this->response->setJSON(['status' => 'success', 'message' => 'Đăng ký thành công. Vui lòng kiểm tra email để xác thực OTP.', 'email' => $this->request->getPost('email')]);
    }
    
    public function verifyOTP()
    {
        $customerModel = new CustomerModel();
        $email = $this->request->getPost('email');
        $otp = $this->request->getPost('otp');
    
        $customer = $customerModel->where('email', $email)->first();
    
        if ($customer) {
            if ($customer['otp'] == $otp && strtotime($customer['otp_expiration']) > time()) {
                // OTP hợp lệ, xác thực tài khoản
                $customerModel->update($customer['id'], ['is_verified' => true, 'otp' => null, 'otp_expiration' => null]);
                return $this->response->setJSON(['status' => 'success', 'message' => 'Tài khoản xác thực thành công.']);
            } else if (strtotime($customer['otp_expiration']) <= time()) {
                // OTP hết hạn, xóa tài khoản
                $customerModel->delete($customer['id']);
                return $this->response->setJSON(['status' => 'error', 'message' => 'OTP đã hết hạn. Tài khoản của bạn đã bị xóa.']);
            }
        }
    
        return $this->response->setJSON(['status' => 'error', 'message' => 'OTP không hợp lệ hoặc đã hết hạn.']);
    }

    private function _sendOTP($email, $otp)
    {
        $emailService = \Config\Services::email();
        $emailService->setTo($email);
        $emailService->setSubject('Mã OTP của bạn');
        $emailService->setMessage('Mã OTP của bạn là: ' . $otp);
        if (!$emailService->send()) {
            // Ghi lỗi nếu gửi email thất bại
            log_message('error', 'Gửi email thất bại: ' . $emailService->printDebugger(['headers']));
        }
    }


            public function login()
        {
            return view('Customers/customers_sign');
        }

        
public function processLogin()
{
    // Lấy dữ liệu đầu vào
    $email = $this->request->getPost('email');
    $password = $this->request->getPost('password');
    
    // Kiểm tra thông tin tài khoản
    $customerModel = new CustomerModel();
    $customer = $customerModel->where('email', $email)->first();

    if ($customer) {
        // Kiểm tra nếu tài khoản đã được xác thực OTP
        if ($customer['is_verified']) {
            // Kiểm tra mật khẩu
            if (password_verify($password, $customer['password'])) {
                // Đăng nhập thành công, lưu thông tin người dùng vào session
                session()->set([
                    'user' => [
                        'id' => $customer['id'],
                        'name' => $customer['name'],
                        'email' => $customer['email'],
                        'phone' => $customer['phone'] ?? '',
                        'address' => $customer['address'] ?? '',
                        'avatar' => $customer['image_url'] ?? ''
                    ],
                    'isLoggedIn' => true,
                    'customer_id' => $customer['id'],
                    'customer_name' => $customer['name'],
                    'customer_avatar' => $customer['image_url'] ?? '',
                ]);

                // Kiểm tra nếu là request AJAX
                if ($this->request->isAJAX()) {
                    return $this->response->setJSON([
                        'status' => 'success', 
                        'message' => 'Đăng nhập thành công!',
                        'redirect_url' => route_to('home_about') // Có thể thay bằng route_to('home') nếu có
                    ]);
                } else {
                    // Nếu không phải AJAX, chuyển hướng trực tiếp
                    session()->setFlashdata('success', 'Đăng nhập thành công!');
                    return redirect()->to(route_to('home_about'));
                }
            } else {
                if ($this->request->isAJAX()) {
                    return $this->response->setJSON(['status' => 'error', 'message' => 'Mật khẩu không đúng.']);
                } else {
                    session()->setFlashdata('error', 'Mật khẩu không đúng.');
                    return redirect()->back();
                }
            }
        } else {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Tài khoản chưa được xác thực. Vui lòng kiểm tra email.']);
            } else {
                session()->setFlashdata('error', 'Tài khoản chưa được xác thực. Vui lòng kiểm tra email.');
                return redirect()->back();
            }
        }
    } else {
        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Email không tồn tại.']);
        } else {
            session()->setFlashdata('error', 'Email không tồn tại.');
            return redirect()->back();
        }
    }
}
        
        
        
        


        public function logout()
        {
            session()->remove(['customer_id', 'customer_name', 'customer_avatar']); // Xóa session
            session()->destroy(); // Hủy session
            return redirect()->route('home_about'); // Chuyển hướng về trang chủ
        }
        

        public function test()
        {
            return view('test');
        }

        public function forgotPassword()
        {
            return view('Customers/customers_forgotpassword'); // Giao diện quên mật khẩu
        }
        
        public function processForgotPassword()
        {
            $customerModel = new CustomerModel();
            $validation = \Config\Services::validation();
        
            $validation->setRules([
                'email' => 'required|valid_email',
            ], [
                'email' => [
                    'valid_email' => 'Email không hợp lệ.'
                ]
            ]);
        
            if (!$this->validate($validation->getRules())) {
                return $this->response->setJSON(['status' => 'error', 'errors' => $this->validator->getErrors()]);
            }
        
            // Kiểm tra xem email có tồn tại không
            $email = $this->request->getPost('email');
            $customer = $customerModel->where('email', $email)->first();
        
            if ($customer) {
                // Generate OTP
                $otp = rand(100000, 999999);
                $otp_expiration = date('Y-m-d H:i:s', strtotime('+5 minutes')); // 5 phút hết hạn
        
                // Cập nhật OTP và thời gian hết hạn vào cơ sở dữ liệu
                $customerModel->update($customer['id'], ['otp' => $otp, 'otp_expiration' => $otp_expiration]);
        
                // Gửi OTP qua email
                $this->_sendOTP($email, $otp);
        
                return $this->response->setJSON(['status' => 'success', 'message' => 'Đã gửi mã OTP đến email của bạn.','email' => $email ]);
            } else {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Email không tồn tại.']);
            }
        }

        public function pass_verifyOTP()
            {
                $customerModel = new CustomerModel();
                $email = $this->request->getPost('email');
                $otp = $this->request->getPost('otp');

                // Tìm tài khoản theo email
                $customer = $customerModel->where('email', $email)->first();

                if ($customer) {
                    // Kiểm tra OTP và thời gian hết hạn
                    if ($customer['otp'] == $otp && strtotime($customer['otp_expiration']) > time()) {
                        return $this->response->setJSON(['status' => 'success', 'message' => 'OTP xác thực thành công.', 'email' => $email]);
                    } else if (strtotime($customer['otp_expiration']) <= time()) {
                        return $this->response->setJSON(['status' => 'error', 'message' => 'OTP đã hết hạn.']);
                    } else {
                        return $this->response->setJSON(['status' => 'error', 'message' => 'OTP không hợp lệ.']);
                    }
                } else {
                    return $this->response->setJSON(['status' => 'error', 'message' => 'Không tìm thấy tài khoản với email này.']);
                }
            }

        public function resetPassword()
            {
                $customerModel = new CustomerModel();
                $email = $this->request->getPost('email');
                $newPassword = $this->request->getPost('new_password');
                $confirmPassword = $this->request->getPost('confirm_password');

                if ($newPassword !== $confirmPassword) {
                    return $this->response->setJSON(['status' => 'error', 'message' => 'Mật khẩu và xác nhận mật khẩu không khớp.']);
                }

                $customer = $customerModel->where('email', $email)->first();
                if ($customer) {
                    // Cập nhật mật khẩu mới
                    $customerModel->update($customer['id'], [
                        'password' => password_hash($newPassword, PASSWORD_DEFAULT),
                        'otp' => null,
                        'otp_expiration' => null // Xóa OTP sau khi cập nhật mật khẩu
                    ]);

                    return $this->response->setJSON(['status' => 'success', 'message' => 'Mật khẩu đã được đặt lại thành công.']);
                }

                return $this->response->setJSON(['status' => 'error', 'message' => 'Không tìm thấy tài khoản.']);
            }

// Thêm các method này vào CustomersController.php

/**
/**
     * Hiển thị trang profile
     */
    public function profile()
    {
        if (!session()->has('user')) {
            session()->setFlashdata('error', 'Vui lòng đăng nhập để truy cập trang này.');
            return redirect()->to(route_to('Customers_sign'));
        }

        $customerModel = new CustomerModel();
        $orderModel = new \App\Models\OrderModel();
        
        $customerId = session('user')['id'];
        
        $customer = $customerModel->find($customerId);
        
        if (!$customer) {
            session()->setFlashdata('error', 'Không tìm thấy thông tin tài khoản.');
            return redirect()->to(route_to('home_about'));
        }
        
        $orders = $orderModel->where('customer_id', $customerId)
                           ->orderBy('created_at', 'DESC')
                           ->paginate(10);
        
        $data = [
            'title' => 'Trang cá nhân - ' . $customer['name'],
            'customer' => $customer,
            'orders' => $orders,
            'pager' => $orderModel->pager
        ];
        
        return view('Customers/profile', $data);
    }

    /**
     * Cập nhật thông tin profile
     */
    public function updateProfile()
    {
        if (!session()->has('user')) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Vui lòng đăng nhập.']);
        }

        $customerModel = new CustomerModel();
        $validation = \Config\Services::validation();
        $customerId = session('user')['id'];

        $validation->setRules([
            'name' => 'required|min_length[2]|max_length[100]',
            'phone' => 'required|numeric|min_length[10]|max_length[15]',
            'address' => 'required|min_length[10]|max_length[255]',
            'image' => 'permit_empty|max_size[image,2048]|is_image[image]'
        ]);

        if (!$this->validate($validation->getRules())) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Dữ liệu không hợp lệ.',
                'errors' => $this->validator->getErrors()
            ]);
        }

        $updateData = [
            'name'       => $this->request->getPost('name'),
            'phone'      => $this->request->getPost('phone'),
            'address'    => $this->request->getPost('address'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $imageFile = $this->request->getFile('image');
        if ($imageFile && $imageFile->isValid() && !$imageFile->hasMoved()) {
            $uploadPath = FCPATH . 'uploads/customers/';
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }

            $fileName = $customerId . '_' . time() . '.' . $imageFile->getExtension();

            if ($imageFile->move($uploadPath, $fileName)) {
                $customer = $customerModel->find($customerId);
                if ($customer && !empty($customer['image_url'])) {
                    $oldImagePath = FCPATH . $customer['image_url'];
                    if (is_file($oldImagePath)) {
                        unlink($oldImagePath);
                    }
                }

                $updateData['image_url'] = 'uploads/customers/' . $fileName;
            }
        }

        if ($customerModel->update($customerId, $updateData)) {
            $updatedCustomer = $customerModel->find($customerId);

            session()->set('user', [
                'id'      => $updatedCustomer['id'],
                'name'    => $updatedCustomer['name'],
                'email'   => $updatedCustomer['email'],
                'phone'   => $updatedCustomer['phone'],
                'address' => $updatedCustomer['address'],
                'avatar'  => $updatedCustomer['image_url'] ?? ''
            ]);

            $response = [
                'status'  => 'success',
                'message' => 'Cập nhật thông tin thành công!'
            ];

            if (isset($updateData['image_url'])) {
                $response['avatar_url'] = base_url($updateData['image_url']);
            }

            return $this->response->setJSON($response);
        }

        return $this->response->setJSON(['status' => 'error', 'message' => 'Có lỗi xảy ra khi cập nhật thông tin.']);
    }

    /**
     * Đổi mật khẩu
     */
    public function changePassword()
    {
        if (!session()->has('user')) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Vui lòng đăng nhập.']);
        }

        $customerModel = new CustomerModel();
        $validation = \Config\Services::validation();
        $customerId = session('user')['id'];
        
        $validation->setRules([
            'current_password' => 'required',
            'new_password' => 'required|min_length[8]|regex_match[/(?=.*[A-Za-z])(?=.*\d)(?=.*[!@#$%^&*])/]',
            'confirm_password' => 'required|matches[new_password]'
        ], [
            'current_password' => [
                'required' => 'Vui lòng nhập mật khẩu hiện tại.'
            ],
            'new_password' => [
                'required' => 'Vui lòng nhập mật khẩu mới.',
                'min_length' => 'Mật khẩu mới phải có ít nhất 8 ký tự.',
                'regex_match' => 'Mật khẩu phải có ít nhất 8 ký tự, bao gồm chữ cái, số và ký tự đặc biệt.'
            ],
            'confirm_password' => [
                'required' => 'Vui lòng xác nhận mật khẩu mới.',
                'matches' => 'Mật khẩu xác nhận không khớp với mật khẩu mới.'
            ]
        ]);

        if (!$this->validate($validation->getRules())) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Dữ liệu không hợp lệ.', 'errors' => $this->validator->getErrors()]);
        }

        $customer = $customerModel->find($customerId);
        if (!$customer) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Không tìm thấy thông tin tài khoản.']);
        }

        if (!password_verify($this->request->getPost('current_password'), $customer['password'])) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Mật khẩu hiện tại không đúng.']);
        }

        $updateData = [
            'password' => password_hash($this->request->getPost('new_password'), PASSWORD_DEFAULT),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        if ($customerModel->update($customerId, $updateData)) {
            return $this->response->setJSON(['status' => 'success', 'message' => 'Đổi mật khẩu thành công!']);
        } else {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Có lỗi xảy ra khi đổi mật khẩu.']);
        }
    }

    /**
     * Xem chi tiết đơn hàng
     */
    public function orderDetail($orderId)
    {
        log_message('debug', 'OrderDetail called with ID: ' . $orderId);
        
        if (!session()->has('user')) {
            log_message('debug', 'User not logged in');
            return $this->response->setStatusCode(401)->setBody('<div class="alert alert-danger">Vui lòng đăng nhập để xem thông tin này.</div>');
        }

        try {
            $orderModel = new \App\Models\OrderModel();
            $orderItemModel = new \App\Models\OrderItemModel();
            $customerId = session('user')['id'];
            
            log_message('debug', 'Customer ID: ' . $customerId);
            
            $order = $orderModel->where('id', $orderId)
                              ->where('customer_id', $customerId)
                              ->first();
            
            log_message('debug', 'Order found: ' . ($order ? 'Yes' : 'No'));
            
            if (!$order) {
                return $this->response->setStatusCode(404)->setBody('<div class="alert alert-danger">Không tìm thấy đơn hàng hoặc bạn không có quyền xem đơn hàng này.</div>');
            }
            
            $orderItems = $orderItemModel->getOrderItems($orderId);
            
            log_message('debug', 'Order items count: ' . count($orderItems));
            
            $data = [
                'order' => $order,
                'orderItems' => $orderItems
            ];
            
            return view('Customers/order_detail_modal', $data);
            
        } catch (\Exception $e) {
            log_message('error', 'Error in orderDetail: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setBody('<div class="alert alert-danger">Có lỗi xảy ra: ' . $e->getMessage() . '</div>');
        }
    }

    /**
     * Hủy đơn hàng
     */
    public function cancelOrder($orderId)
    {
        if (!session()->has('user')) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Vui lòng đăng nhập.']);
        }

        $orderModel = new \App\Models\OrderModel();
        $customerId = session('user')['id'];
        
        $order = $orderModel->where('id', $orderId)
                          ->where('customer_id', $customerId)
                          ->first();
        
        if (!$order) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Không tìm thấy đơn hàng.']);
        }
        
        if ($order['status'] !== 'pending') {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Không thể hủy đơn hàng này.']);
        }
        
        $updateData = [
            'status' => 'cancelled',
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        if ($orderModel->update($orderId, $updateData)) {
            return $this->response->setJSON(['status' => 'success', 'message' => 'Hủy đơn hàng thành công!']);
        } else {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Có lỗi xảy ra khi hủy đơn hàng.']);
        }
    }

    /**
     * Gửi đánh giá sản phẩm
     */
    public function submitReview()
    {
        if (!session()->has('user')) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Vui lòng đăng nhập.']);
        }

        $validation = \Config\Services::validation();
        $reviewModel = new \App\Models\ProductReviewModel();
        $orderModel = new \App\Models\OrderModel();
        $customerId = session('user')['id'];

        $validation->setRules([
            'order_id' => 'required|numeric',
            'product_id' => 'required|numeric',
            'rating' => 'required|numeric|greater_than[0]|less_than[6]',
            'title' => 'required|min_length[3]|max_length[100]',
            'comment' => 'required|min_length[10]|max_length[1000]'
        ]);

        if (!$this->validate($validation->getRules())) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Dữ liệu không hợp lệ.',
                'errors' => $this->validator->getErrors()
            ]);
        }

        $orderId = $this->request->getPost('order_id');
        $productId = $this->request->getPost('product_id');

        // Kiểm tra đơn hàng có tồn tại và thuộc về khách hàng
        $order = $orderModel->where('id', $orderId)
                           ->where('customer_id', $customerId)
                           ->where('status', 'delivered')
                           ->first();

        if (!$order) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Đơn hàng không hợp lệ hoặc chưa được giao.']);
        }

        // Kiểm tra sản phẩm có trong đơn hàng
        $orderItemModel = new \App\Models\OrderItemModel();
        $item = $orderItemModel->where('order_id', $orderId)
                              ->where('product_id', $productId)
                              ->first();

        if (!$item) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Sản phẩm không thuộc đơn hàng này.']);
        }

        // Kiểm tra xem đã đánh giá chưa
        $existingReview = $reviewModel->where([
            'order_id' => $orderId,
            'product_id' => $productId,
            'customer_id' => $customerId
        ])->first();

        if ($existingReview) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Bạn đã đánh giá sản phẩm này rồi.']);
        }

        $reviewData = [
            'order_id' => $orderId,
            'product_id' => $productId,
            'customer_id' => $customerId,
            'rating' => $this->request->getPost('rating'),
            'title' => $this->request->getPost('title'),
            'comment' => $this->request->getPost('comment'),
            'is_verified' => 1 // Xác minh vì đây là khách hàng đã mua hàng
        ];

        if ($reviewModel->insert($reviewData)) {
            return $this->response->setJSON(['status' => 'success', 'message' => 'Đánh giá đã được gửi thành công!']);
        } else {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Có lỗi xảy ra khi gửi đánh giá.']);
        }
    }

    /**
     * Xem đánh giá sản phẩm
     */
    public function viewReview($orderId, $productId)
    {
        if (!session()->has('user')) {
            return $this->response->setStatusCode(401)->setBody('<div class="alert alert-danger">Vui lòng đăng nhập để xem thông tin này.</div>');
        }

        $reviewModel = new \App\Models\ProductReviewModel();
        $customerId = session('user')['id'];

        $review = $reviewModel->where([
            'order_id' => $orderId,
            'product_id' => $productId,
            'customer_id' => $customerId
        ])->first();

        if (!$review) {
            return $this->response->setStatusCode(404)->setBody('<div class="alert alert-danger">Không tìm thấy đánh giá.</div>');
        }

        $data = [
            'review' => $review
        ];

        return view('Customers/review_view', $data);
    }
        
}