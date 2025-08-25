<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\{RoleModel, UserModel, GroupModel, GroupRoleModel};

class GroupRoleController extends BaseController
{
    public function table()
    {
        $groupModel = new GroupModel();
        $roleModel = new RoleModel();
        $groupRoleModel = new GroupRoleModel();

        $groups = $groupModel->findAll();
        $roles = $roleModel->findAll();
        $groupRoles = $groupRoleModel->findAll();

        $data = [
            'groups' => $groups,
            'roles' => $roles,
            'groupRoles' => $groupRoles,
            'pageTitle' => 'Danh Sách Phân quyền',
            'breadcrumb' => [
                ['title' => 'Thống kê', 'url' => route_to('Dashboard_table')],
                ['title' => 'Quản Lý Phân quyền', 'url' => route_to('Table_GroupRole')],
            ],
        ];

        return view('Dashboard/Group_Role/table', $data);
    }

    public function edit($id)
    {
        $groupModel = new GroupModel();
        $roleModel = new RoleModel();
        $groupRoleModel = new GroupRoleModel();
        $userModel = new UserModel();

        $group = $groupModel->find($id);
        $roles = $roleModel->findAll();
        $groupRoles = $groupRoleModel->where('group_id', $id)->findAll();
        $currentUser = $userModel->find(session()->get('user')['user_id']);

        if (!$group) {
            return redirect()
                ->route('Table_GroupRole')
                ->with('error', 'Nhóm không tồn tại.');
        }

        $data = [
            'group' => $group,
            'roles' => $roles,
            'groupRoles' => $groupRoles,
            'currentUser' => $currentUser,
            'pageTitle' => 'Chỉnh Sửa Phân quyền',
            'breadcrumb' => [
                ['title' => 'Thống kê', 'url' => route_to('Dashboard_table')],
                ['title' => 'Quản Lý Phân quyền', 'url' => route_to('Table_GroupRole')],
                ['title' => 'Chỉnh Sửa Phân quyền', 'url' => route_to('Table_GroupRole_Edit', $id)],
            ],
        ];

        return view('Dashboard/Group_Role/edit', $data);
    }

    public function update($id)
    {
        $groupRoleModel = new GroupRoleModel();
        $roleIds = $this->request->getPost('roles');

        if (!$roleIds) {
            session()->setFlashdata('error', 'Vui lòng chọn ít nhất một quyền.');
            return redirect()->route('Table_GroupRole_Edit', $id);
        }

        // Xóa các quyền hiện tại
        $groupRoleModel->where('group_id', $id)->delete();

        // Thêm các quyền mới
        foreach ($roleIds as $roleId) {
            $groupRoleModel->insert(['group_id' => $id, 'role_id' => $roleId]);
        }

        session()->setFlashdata('success', 'Quyền nhóm đã được cập nhật thành công.');
        return redirect()->route('Table_GroupRole');
    }

    public function delete($id)
    {
        $groupModel = new GroupModel();
        $groupRoleModel = new GroupRoleModel();

        // Xóa tất cả các quyền liên quan đến nhóm
        $groupRoleModel->where('group_id', $id)->delete();

        // Xóa nhóm
        if ($groupModel->delete($id)) {
            session()->setFlashdata('success', 'Nhóm và các quyền liên quan đã được xóa thành công.');
        } else {
            session()->setFlashdata('error', 'Không thể xóa nhóm.');
        }

        return redirect()->route('Table_GroupRole');
    }

    public function create()
    {
        $groupModel = new GroupModel();
        $roleModel = new RoleModel();

        $data = [
            'roles' => $roleModel->findAll(),
            'groups' => $groupModel->findAll(),
            'pageTitle' => 'Thêm Phân quyền Mới',
            'breadcrumb' => [
                ['title' => 'Thống kê', 'url' => route_to('Dashboard_table')],
                ['title' => 'Quản Lý Phân quyền', 'url' => route_to('Table_GroupRole')],
                ['title' => 'Thêm Nhóm', 'url' => route_to('Table_GroupRole_Create')],
            ],
        ];

        return view('Dashboard/Group_Role/create', $data);
    }

    public function store()
    {
        $groupRoleModel = new GroupRoleModel();
        $groupId = $this->request->getPost('group_id');
        $roleIds = $this->request->getPost('roles');

        if (!$groupId || !$roleIds) {
            session()->setFlashdata('error', 'Vui lòng điền đầy đủ thông tin.');
            return redirect()->route('Table_GroupRole_Create');
        }

        foreach ($roleIds as $roleId) {
            $groupRoleModel->insert(['group_id' => $groupId, 'role_id' => $roleId]);
        }

        session()->setFlashdata('success', 'Nhóm và quyền đã được thêm thành công.');
        return redirect()->route('Table_GroupRole');
    }
}