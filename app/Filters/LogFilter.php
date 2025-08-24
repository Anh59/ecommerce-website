<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\RoleModel;
use App\Models\GroupRoleModel;
use App\Models\UserModel;

class LogFilter implements FilterInterface
{
    /**
     * Do whatever processing this filter needs to do.
     * By default it should not return anything during
     * normal execution. However, when an abnormal state
     * is found, it should return an instance of
     * CodeIgniter\HTTP\Response. If it does, script
     * execution will end and that Response will be
     * sent back to the client, allowing for error pages,
     * redirects, etc.
     *
     * @param RequestInterface $request
     * @param array|null       $arguments
     *
     * @return RequestInterface|ResponseInterface|string|void
     */
    public function before(RequestInterface $request, $arguments = null)
    {   
        $session = session();
         // Lấy session hiện tại
     /// Kiểm tra xem người dùng đã đăng nhập hay chưa
        if (!$session->get('user')) {
            // Nếu chưa đăng nhập, chuyển hướng đến trang đăng nhập
            return redirect()->to('/adminlogin');
        } 
       
        $userdata = $session->get('user'); // Lấy thông tin người dùng từ session
    
    
        $userModel = new UserModel();
        $groupRoleModel = new GroupRoleModel();
        $roleModel = new RoleModel();

        $user = $userModel->find($userdata['user_id']); 
        // Lấy thông tin người dùng từ cơ sở dữ liệu
        //dd($user);
        $groupRoles = $groupRoleModel->where('group_id', $user['group_id'])->findAll(); // Lấy tất cả các quyền của nhóm người dùng
        //dd($groupRoles);
        $roleUrls = [];
        foreach ($groupRoles as $groupRole) {
            $role = $roleModel->find($groupRole['role_id']);
            $roleUrls[] = $role['url'];
        }
        //dd($roleUrls);
        // Lấy URL hiện tại
        $currentUrl = $request->getPath();

        
        //dd($currentUrl);
        // if (!in_array($currentUrl, $roleUrls)) {
        //     // Nếu URL hiện tại không nằm trong danh sách quyền, chuyển hướng về trang chủ
        //     return redirect()->back()->with('error', 'Bạn không có quyền truy cập vào trang này.');
        // }
        $hasPermission = false;
        foreach ($roleUrls as $urlPattern) {
            // Chuyển đổi mẫu URL thành regex
            $urlPatternRegex = str_replace(['(:num)', '(:any)'], ['\d+', '.+'], $urlPattern);
            if (preg_match("#^{$urlPatternRegex}$#", $currentUrl)) {
                $hasPermission = true;
                break;
            }
        }

        if (!$hasPermission) {
            // Nếu URL hiện tại không nằm trong danh sách quyền, chuyển hướng về trang chủ
            return redirect()->to('/')->with('error', 'Bạn không có quyền truy cập vào trang này.');
        }


    
    }

    /**
     * Allows After filters to inspect and modify the response
     * object as needed. This method does not allow any way
     * to stop execution of other after filters, short of
     * throwing an Exception or Error.
     *
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     * @param array|null        $arguments
     *
     * @return ResponseInterface|void
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        //
        
    }
}