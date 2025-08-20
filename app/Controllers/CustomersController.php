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
                            'isLoggedIn' => true, // Cờ xác nhận đăng nhập
                            'customer_id' => $customer['id'],
                            'customer_name' => $customer['name'],
                            'customer_avatar' => $customer['image_url'], // Lưu đường dẫn avatar vào session
                        ]);
        
                        return $this->response->setJSON(['status' => 'success', 'message' => 'Đăng nhập thành công!']);
                    } else {
                        return $this->response->setJSON(['status' => 'error', 'message' => 'Mật khẩu không đúng.']);
                    }
                } else {
                    return $this->response->setJSON(['status' => 'error', 'message' => 'Tài khoản chưa được xác thực. Vui lòng kiểm tra email.']);
                }
            } else {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Email không tồn tại.']);
            }
        }
        
        
        
        


        public function logout()
        {
            session()->remove(['customer_id', 'customer_name', 'customer_avatar']); // Xóa session
            session()->destroy(); // Hủy session
            return redirect()->route('Tour_index');
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


        
}