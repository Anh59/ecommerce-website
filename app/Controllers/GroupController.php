<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\GroupModel;

class GroupController extends BaseController
{
    public function table()
    {
        $model = new GroupModel();

        $data = [
            'groups' => $model->findAll(),
            'pageTitle' => 'Danh Sách chức vụ',
            'breadcrumb' => [
                ['title' => 'Thống kê', 'url' => route_to('Dashboard_table')],
                ['title' => 'Quản Lý chức vụ', 'url' => route_to('Table_Group')],
            ],
            'success' => session()->getFlashdata('success'),
            'error' => session()->getFlashdata('error'),
        ];

        return view('Dashboard/group/table', $data);
    }

    public function create()
    {
        $data = [
            'pageTitle' => 'Thêm chức vụ Mới',
            'breadcrumb' => [
                ['title' => 'Thống kê', 'url' => route_to('Dashboard_table')],
                ['title' => 'Quản Lý chức vụ', 'url' => route_to('Table_Group')],
                ['title' => 'Thêm chức vụ', 'url' => route_to('Table_Create')],
            ],
            'success' => session()->getFlashdata('success'),
            'error' => session()->getFlashdata('error'),
        ];
        // dd($data);
        return view('Dashboard/group/create', $data);
    }

    public function store()
    {
        $model = new GroupModel();

        $data = [
            'name' => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),
        ];

        if ($model->save($data)) {
            session()->setFlashdata('success', 'Tạo nhóm mới thành công.');
        } else {
            session()->setFlashdata('error', 'Tạo nhóm mới thất bại.');
        }

        return redirect()->route('Table_Group');
    }

    public function edit($id)
    {
        $model = new GroupModel();
        $group = $model->find($id);

        if (!$group) {
            return redirect()->route('Table_Group')->with('error', 'chức vụ không tồn tại.');
        }

        $data = [
            'group' => $group,
            'pageTitle' => 'Chỉnh Sửa chức vụ',
            'breadcrumb' => [
                ['title' => 'Thống kê', 'url' => route_to('Dashboard_table')],
                ['title' => 'Quản Lý chức vụ', 'url' => route_to('Table_Group')],
                ['title' => 'Chỉnh Sửa chức vụ', 'url' => route_to('Group_edit', $id)],
            ],
            'success' => session()->getFlashdata('success'),
            'error' => session()->getFlashdata('error'),
        ];

        return view('Dashboard/group/edit', $data);
    }

    public function update($id)
    {
        $model = new GroupModel();

        $data = [
            'name' => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),
        ];

        if ($model->update($id, $data)) {
            session()->setFlashdata('success', 'Cập nhật nhóm thành công.');
        } else {
            session()->setFlashdata('error', 'Cập nhật nhóm thất bại.');
        }

        return redirect()->route('Table_Group');
    }

    public function delete($id)
    {
        $model = new GroupModel();

        if ($model->delete($id)) {
            session()->setFlashdata('success', 'Xóa nhóm thành công.');
        } else {
            session()->setFlashdata('error', 'Xóa nhóm thất bại.');
        }

        return redirect()->route('Table_Group');
    }
}