<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class InitSeeder extends Seeder
{
    public function run()
    {
        // --- Nhóm quyền ---
        $groups = [
            ['group_id' => 1, 'group_name' => 'Admin'],
            ['group_id' => 2, 'group_name' => 'Editor'],
            ['group_id' => 3, 'group_name' => 'Viewer'],
        ];
        $this->db->table('groups')->insertBatch($groups);

        // --- Các quyền (role) ---
        $roles = [
            ['role_id' => 1, 'name' => 'Dashboard access', 'url' => 'Dashboard_table'],
            ['role_id' => 2, 'name' => 'Manage Groups', 'url' => 'Table_Group'],
            ['role_id' => 3, 'name' => 'Manage Roles', 'url' => 'Table_Role'],
            ['role_id' => 4, 'name' => 'Manage GroupRole', 'url' => 'Table_GroupRole'],
            ['role_id' => 5, 'name' => 'Manage Permissions', 'url' => 'Table_Permissions'],
            ['role_id' => 6, 'name' => 'Manage Users', 'url' => 'Table_User'],
        ];
        $this->db->table('roles')->insertBatch($roles);

        // --- Gán quyền cho từng nhóm ---
        $groupRoles = [
            // Admin full quyền
            ['group_id' => 1, 'role_id' => 1],
            ['group_id' => 1, 'role_id' => 2],
            ['group_id' => 1, 'role_id' => 3],
            ['group_id' => 1, 'role_id' => 4],
            ['group_id' => 1, 'role_id' => 5],
            ['group_id' => 1, 'role_id' => 6],

            // Editor (Dashboard + Group)
            ['group_id' => 2, 'role_id' => 1],
            ['group_id' => 2, 'role_id' => 2],

            // Viewer (chỉ Dashboard)
            ['group_id' => 3, 'role_id' => 1],
        ];
        $this->db->table('group_roles')->insertBatch($groupRoles);

        // --- Users mẫu ---
        $users = [
            [
                'user_id' => 1,
                'username' => 'superadmin',
                // Dùng password_hash để mã hoá
                'password' => password_hash('123456', PASSWORD_DEFAULT),
                'group_id' => 1,
                'super_admin' => 1
            ],
            [
                'user_id' => 2,
                'username' => 'admin',
                'password' => password_hash('123456', PASSWORD_DEFAULT),
                'group_id' => 1,
                'super_admin' => 0
            ],
            [
                'user_id' => 3,
                'username' => 'editor',
                'password' => password_hash('123456', PASSWORD_DEFAULT),
                'group_id' => 2,
                'super_admin' => 0
            ],
            [
                'user_id' => 4,
                'username' => 'viewer',
                'password' => password_hash('123456', PASSWORD_DEFAULT),
                'group_id' => 3,
                'super_admin' => 0
            ],
        ];
        $this->db->table('users')->insertBatch($users);
    }
}
