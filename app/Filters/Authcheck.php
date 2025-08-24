<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class AuthCheck implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Kiểm tra nếu người dùng chưa đăng nhập
        if (!session()->get('isLoggedIn')) {
            return redirect()->to(base_url('api_Customers/customers_sign'))->with('error', 'Bạn cần đăng nhập để truy cập.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Không cần làm gì sau khi xử lý
    }
}