<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\CustomerModel;
use CodeIgniter\HTTP\ResponseInterface;

class GoogleController extends BaseController
{
    protected $customerModel;
    protected $googleClient;

    public function __construct()
    {
        $this->customerModel = new CustomerModel();
        
        // Khởi tạo Google Client
        $this->googleClient = new \Google\Client();
        $this->googleClient->setClientId(getenv('GOOGLE_CLIENT_ID'));
        $this->googleClient->setClientSecret(getenv('GOOGLE_CLIENT_SECRET'));
        $this->googleClient->setRedirectUri(base_url('api_Customers/E-commerce-google-callback'));
        $this->googleClient->addScope('email');
        $this->googleClient->addScope('profile');
    }

    /**
     * Redirect đến Google Login
     */
    public function googleLogin()
    {
        // Lưu URL trước đó để redirect back sau khi login
        $redirectUrl = previous_url() ?? route_to('home_index');
        session()->set('redirect_url', $redirectUrl);
        
        $authUrl = $this->googleClient->createAuthUrl();
        return redirect()->to($authUrl);
    }

    /**
     * Xử lý callback từ Google
     */
    public function googleCallback()
    {
        try {
            if (!$this->request->getGet('code')) {
                throw new \Exception('Không nhận được mã xác thực từ Google');
            }

            // Lấy token từ Google
            $token = $this->googleClient->fetchAccessTokenWithAuthCode($this->request->getGet('code'));
            
            if (isset($token['error'])) {
                throw new \Exception('Lỗi xác thực: ' . $token['error_description']);
            }

            $this->googleClient->setAccessToken($token);

            // Lấy thông tin user từ Google
            $googleService = new \Google\Service\Oauth2($this->googleClient);
            $userInfo = $googleService->userinfo->get();

            // Xử lý đăng nhập/đăng ký
            return $this->handleGoogleUser($userInfo);

        } catch (\Exception $e) {
            log_message('error', 'Google Login Error: ' . $e->getMessage());
            session()->setFlashdata('error', 'Đăng nhập Google thất bại: ' . $e->getMessage());
            return redirect()->to(route_to('Customers_sign'));
        }
    }

    /**
     * Xử lý thông tin user từ Google (KHÔNG cần google_id)
     */
    private function handleGoogleUser($userInfo)
    {
        $email = $userInfo->getEmail();
        $name = $userInfo->getName();
        $picture = $userInfo->getPicture();

        // log_message('debug', 'Processing Google user: ' . $email);

        // Kiểm tra user đã tồn tại bằng email
        $customer = $this->customerModel->where('email', $email)->first();

        if ($customer) {
            // User đã tồn tại - Cập nhật thông tin từ Google
            $updateData = [
                'image_url' => $picture,
                'is_verified' => true, // Đảm bảo tài khoản được xác thực
                'updated_at' => date('Y-m-d H:i:s')
            ];

            // Chỉ cập nhật name nếu chưa có hoặc đang trống
            if (empty($customer['name']) || $customer['name'] === '') {
                $updateData['name'] = $name;
            }

            // Chỉ cập nhật nếu có thay đổi
            if (!empty(array_filter($updateData))) {
                $this->customerModel->update($customer['id'], $updateData);
                // Lấy lại thông tin mới nhất
                $customer = $this->customerModel->find($customer['id']);
            }

            log_message('debug', 'Existing user logged in via Google: ' . $email);
        } else {
            // Tạo user mới với thông tin từ Google
            $customerData = [
                'name' => $name,
                'email' => $email,
                'image_url' => $picture,
                'is_verified' => true, // Google đã xác thực email
                'password' => password_hash(bin2hex(random_bytes(16)), PASSWORD_DEFAULT), // Random password
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];

            $customerId = $this->customerModel->insert($customerData);
            
            if (!$customerId) {
                throw new \Exception('Không thể tạo tài khoản mới: ' . implode(', ', $this->customerModel->errors()));
            }
            
            $customer = $this->customerModel->find($customerId);
            log_message('debug', 'New user created via Google: ' . $email);
        }

        // Đăng nhập user
        $this->loginUser($customer);

        // Chuyển hướng về trang trước đó hoặc trang chủ
        $redirectUrl = session()->get('redirect_url') ?? route_to('home_index');
        session()->remove('redirect_url');

        session()->setFlashdata('success', 'Đăng nhập bằng Google thành công!');
        return redirect()->to($redirectUrl);
    }

    /**
     * Đăng nhập user
     */
    private function loginUser($customer)
    {
        $userData = [
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
            'login_method' => 'google' // Để phân biệt login method nếu cần
        ];

        session()->set($userData);
        
        log_message('debug', 'User logged in: ' . $customer['email']);
    }

    /**
     * API endpoint cho AJAX login (tùy chọn)
     */
    public function getGoogleAuthUrl()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400);
        }

        $authUrl = $this->googleClient->createAuthUrl();
        
        return $this->response->setJSON([
            'status' => 'success',
            'auth_url' => $authUrl
        ]);
    }
}