<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateProductCommentsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'              => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'product_id'      => ['type' => 'INT', 'unsigned' => true],
            'customer_id'     => ['type' => 'INT', 'unsigned' => true],
            'parent_id'       => ['type' => 'INT', 'unsigned' => true, 'null' => true], // Reply to comment
            'comment'         => ['type' => 'TEXT'],
            'is_approved'     => ['type' => 'BOOLEAN', 'default' => false],
            'created_at'      => ['type' => 'DATETIME', 'null' => true],
            'updated_at'      => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('product_id', 'products', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('customer_id', 'customers', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('parent_id', 'product_comments', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('product_comments');
    }

    public function down()
    {
        $this->forge->dropTable('product_comments');
    }
}
