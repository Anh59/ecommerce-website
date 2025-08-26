<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateShoppingCartTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'              => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'customer_id'     => ['type' => 'INT', 'unsigned' => true],
            'product_id'      => ['type' => 'INT', 'unsigned' => true],
            'quantity'        => ['type' => 'INT', 'default' => 1],
            'price'           => ['type' => 'DECIMAL', 'constraint' => '12,0'],
            'created_at'      => ['type' => 'DATETIME', 'null' => true],
            'updated_at'      => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['customer_id', 'product_id']);
        $this->forge->addForeignKey('customer_id', 'customers', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('product_id', 'products', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('shopping_cart');
    }

    public function down()
    {
        $this->forge->dropTable('shopping_cart');
    }
}
