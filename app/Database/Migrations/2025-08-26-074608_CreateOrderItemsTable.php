<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateOrderItemsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'              => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'order_id'        => ['type' => 'INT', 'unsigned' => true],
            'product_id'      => ['type' => 'INT', 'unsigned' => true],
            'product_name'    => ['type' => 'VARCHAR', 'constraint' => 255],
            'product_sku'     => ['type' => 'VARCHAR', 'constraint' => 100],
            'quantity'        => ['type' => 'INT'],
            'price'           => ['type' => 'DECIMAL', 'constraint' => '12,0'],
            'total'           => ['type' => 'DECIMAL', 'constraint' => '12,0'],
            'created_at'      => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('order_id', 'orders', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('product_id', 'products', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->createTable('order_items');
    }

    public function down()
    {
        $this->forge->dropTable('order_items');
    }
}
