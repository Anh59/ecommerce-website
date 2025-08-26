<?php

// 1. Brands Table - Bảng thương hiệu/hãng sản xuất
namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateBrandsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'              => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'name'            => ['type' => 'VARCHAR', 'constraint' => 100],
            'slug'            => ['type' => 'VARCHAR', 'constraint' => 100, 'unique' => true],
            'description'     => ['type' => 'TEXT', 'null' => true],
            'logo_url'        => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'website'         => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'country'         => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'is_active'       => ['type' => 'BOOLEAN', 'default' => true],
            'sort_order'      => ['type' => 'INT', 'default' => 0],
            'created_at'      => ['type' => 'DATETIME', 'null' => true],
            'updated_at'      => ['type' => 'DATETIME', 'null' => true],
            'deleted_at'      => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('brands');
    }

    public function down()
    {
        $this->forge->dropTable('brands');
    }
}