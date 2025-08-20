<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCustomersTable extends Migration
{
    public function up()
    {
        //
        $this->forge->addField([
            'id'              => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'name'            => ['type' => 'VARCHAR', 'constraint' => 100],
            'email'           => ['type' => 'VARCHAR', 'constraint' => 100, 'unique' => true],
            'phone'           => ['type' => 'VARCHAR', 'constraint' => 20],
            'address'         => ['type' => 'VARCHAR', 'constraint' => 255],
            'password'        => ['type' => 'VARCHAR', 'constraint' => 255],
            'image_url'       => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'otp'             => ['type' => 'VARCHAR', 'constraint' => 10, 'null' => true],
            'otp_expiration'  => ['type' => 'DATETIME', 'null' => true],
            'is_verified'     => ['type' => 'BOOLEAN', 'default' => false],
            'created_at'      => ['type' => 'DATETIME', 'null' => true],
            'updated_at'      => ['type' => 'DATETIME', 'null' => true],
            'deleted_at'      => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('customers');
    }

    public function down()
    {
        //
        $this->forge->dropTable('customers');
    }
}
