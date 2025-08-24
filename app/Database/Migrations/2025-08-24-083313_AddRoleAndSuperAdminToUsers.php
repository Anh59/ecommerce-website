<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddRoleAndSuperAdminToUsers extends Migration
{
    public function up()
    {
        $fields = [
            'role' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'default'    => 'user',
                'after'      => 'group_id'
            ],
            'super_admin' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
                'after'      => 'role'
            ],
        ];

        $this->forge->addColumn('users', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('users', ['role', 'super_admin']);
    }
}
