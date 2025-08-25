<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\{UserModel, GroupModel, RoleModel};

class TableUserController extends BaseController
{
    public function tableuser()
    {
        $userModel = new UserModel();
        $groupModel = new GroupModel();
        $roleModel = new RoleModel();

        // Lấy tất cả người dùng và nhóm
        $data['users'] = $userModel->findAll();
        $data['groups'] = $groupModel->findAll();
        
        // Cập nhật breadcrumb và pageTitle
        $data['pageTitle'] = 'Danh Sách Tài khoản nội bộ';  // Tiêu đề trang
        $data['breadcrumb'] = [
            ['title' => 'Trang chủ', 'url' => route_to('Dashboard_table')],
            ['title' => 'Tài khoản nội bộ', 'url' => route_to('Table_User')],
        ];

        return view('Dashboard/User/Table', $data);
    }

    public function changeUserGroup()
    {
        $userModel = new UserModel();
        $userId = $this->request->getPost('user_id');
        $groupId = $this->request->getPost('group_id');

        $user = $userModel->find($userId);
        if ($user) {
            $user['group_id'] = $groupId;
            if ($userModel->save($user)) {
                // Cập nhật session nếu người dùng hiện tại được thay đổi nhóm
                $session = session();
                if ($session->get('user_id') == $userId) {
                    $session->set('group_id', $groupId);
                }
                return $this->response->setJSON(['status' => 'success']);
            }
        }
        return $this->response->setJSON(['status' => 'error']);
    }

    public function deleteUser($userId)
    {
        $userModel = new UserModel();
        $user = $userModel->find($userId);
    
        if ($user) {
            if ($userModel->delete($userId)) {
                return redirect()->back()->with('success', 'Xóa tài khoản user thành công');
            } else {
                return redirect()->back()->with('error', 'Xóa tài khoản user thất bại');
            }
        } else {
            return redirect()->back()->with('error', 'User không tồn tại');
        }
    }

    public function editUser($userId)
    {
        $userModel = new UserModel();
        $groupModel = new GroupModel();
        $user = $userModel->find($userId);
        
        if (!$user) {
            return redirect()->route('Table_User')->with('error', 'User không tồn tại');
        }

        // Cập nhật breadcrumb và pageTitle
        $data['user'] = $user;
        $data['groups'] = $groupModel->findAll();
        $data['pageTitle'] = 'Chỉnh Sửa Tài khoản nội bộ';  // Tiêu đề trang
        $data['breadcrumb'] = [
            ['title' => 'Trang chủ', 'url' => route_to('Dashboard_table')],
            ['title' => 'Tài khoản nội bộ', 'url' => route_to('Table_User')],
            ['title' => 'Chỉnh Sửa Tài khoản nội bộ', 'url' => route_to('Table_User_Edit', $userId)],
        ];

        return view('Dashboard/User/Edit', $data);
    }

    public function updateUser($userId)
    {
        $userModel = new UserModel();
        $user = $userModel->find($userId);

        if ($user) {
            $postData = $this->request->getPost();
            $user['username'] = $postData['username'];
            $user['email'] = $postData['email'];
            $user['group_id'] = $postData['group_id'];

            if ($userModel->save($user)) {
                return redirect()->route('Table_User')->with('success', 'Cập nhật người dùng thành công');
            } else {
                return redirect()->route('Table_User')->with('error', 'Cập nhật người dùng thất bại');
            }
        } else {
            return redirect()->back()->with('error', 'Người dùng không tồn tại');
        }
    }

    public function create()
    {
        $groupModel = new GroupModel();
        $data['groups'] = $groupModel->findAll();
        
        // Cập nhật breadcrumb và pageTitle
        $data['pageTitle'] = 'Tạo Tài khoản nội bộ Mới';  // Tiêu đề trang
        $data['breadcrumb'] = [
            ['title' => 'Trang chủ', 'url' => route_to('Dashboard_table')],
            ['title' => 'Tài khoản nội bộ', 'url' => route_to('Table_User')],
            ['title' => 'Tạo Tài khoản nội bộ', 'url' => route_to('Table_User_Create')],
        ];

        return view('Dashboard/User/Create', $data);
    }

    public function store()
    {
        $userModel = new UserModel();
        $postData = $this->request->getPost();

        $newUser = [
            'username' => $postData['username'],
            'email' => $postData['email'],
            'group_id' => $postData['group_id'],
            'password' => password_hash('1', PASSWORD_DEFAULT), 
            'super_admin' => false // or other default value
        ];

        if ($userModel->insert($newUser)) {
            return redirect()->route('Table_User')->with('success', 'Tạo người dùng mới thành công');
        } else {
            return redirect()->back()->with('error', 'Tạo người dùng mới thất bại');
        }
    }
}