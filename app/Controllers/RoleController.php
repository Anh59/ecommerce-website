<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\RoleModel;

class RoleController extends BaseController
{
    public function table()
    {
        $roleModel = new RoleModel();

        $data = [
            'roles' => $roleModel->findAll(),
            'pageTitle' => 'Danh Sách Quyền',
            'breadcrumb' => [
                ['title' => 'Home', 'url' => route_to('Dashboard_table')],
                ['title' => 'Quản Lý Quyền', 'url' => route_to('Table_Role')],
            ],
            'success' => session()->getFlashdata('success'),
            'error' => session()->getFlashdata('error'),
        ];

        return view('Dashboard/Role/table', $data);
    }

    public function create()
    {
        $data = [
            'pageTitle' => 'Thêm Quyền Mới',
            'breadcrumb' => [
                ['title' => 'Home', 'url' => route_to('Dashboard_table')],
                ['title' => 'Quản Lý Quyền', 'url' => route_to('Table_Role')],
                ['title' => 'Thêm Quyền', 'url' => route_to('Table_Role_Create')],
            ],
            'success' => session()->getFlashdata('success'),
            'error' => session()->getFlashdata('error'),
        ];

        return view('Dashboard/Role/create', $data);
    }

    public function store()
    {
        $roleModel = new RoleModel();

        $url = $this->request->getPost('url');
        $description = $this->request->getPost('description');

        // Kiểm tra quyền trùng lặp
        $existing = $roleModel
            ->where('url', $url)
            ->orWhere('description', $description)
            ->first();

        if ($existing) {
            if ($existing['url'] === $url) {
                session()->setFlashdata('error', 'Quyền này đã tồn tại.');
            } elseif ($existing['description'] === $description) {
                session()->setFlashdata('error', 'Mô tả quyền không được trùng lặp.');
            }
            return redirect()->back()->withInput();
        }

        $data = [
            'url' => $url,
            'description' => $description,
        ];

        if ($roleModel->insert($data)) {
            session()->setFlashdata('success', 'Thêm mới quyền thành công.');
        } else {
            session()->setFlashdata('error', 'Thêm mới quyền thất bại.');
        }

        return redirect()->route('Table_Role');
    }

    public function edit($id)
    {
        $roleModel = new RoleModel();
        $role = $roleModel->find($id);
    
        if (!$role) {
            session()->setFlashdata('error', 'Quyền không tồn tại.');
            return redirect()->route('Table_Role');
        }
    
        $data = [
            'roles' => $role, // Đảm bảo truyền dữ liệu 'roles'
            'pageTitle' => 'Chỉnh Sửa Quyền',
            'breadcrumb' => [
                ['title' => 'Home', 'url' => route_to('Dashboard_table')],
                ['title' => 'Quản Lý Quyền', 'url' => route_to('Table_Role')],
                ['title' => 'Chỉnh Sửa Quyền', 'url' => route_to('Table_Role_Edit', $id)],
            ],
        ];
    
        return view('Dashboard/Role/edit', $data);
    }
    

    public function update($id)
    {
        $roleModel = new RoleModel();

        $data = [
            'url' => $this->request->getPost('url'),
            'description' => $this->request->getPost('description'),
        ];

        if ($roleModel->update($id, $data)) {
            session()->setFlashdata('success', 'Cập nhật quyền thành công.');
        } else {
            session()->setFlashdata('error', 'Cập nhật quyền thất bại.');
        }

        return redirect()->route('Table_Role');
    }

    public function delete($id)
    {
        $roleModel = new RoleModel();

        if ($roleModel->delete($id)) {
            session()->setFlashdata('success', 'Xóa quyền thành công.');
        } else {
            session()->setFlashdata('error', 'Xóa quyền thất bại.');
        }

        return redirect()->route('Table_Role');
    }
}