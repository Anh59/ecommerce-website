<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateStockMovementsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'              => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'product_id'      => ['type' => 'INT', 'unsigned' => true],
            'type'            => ['type' => 'ENUM', 'constraint' => ['in', 'out', 'adjustment'], 'default' => 'in'],
            'quantity'        => ['type' => 'INT'],
            'reason'          => ['type' => 'VARCHAR', 'constraint' => 100], // Lý do: purchase, sale, return, damage
            'reference_id'    => ['type' => 'INT', 'unsigned' => true, 'null' => true], // ID đơn hàng hoặc phiếu nhập
            'reference_type'  => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true], // order, purchase_order
            'notes'           => ['type' => 'TEXT', 'null' => true],
            'created_by'      => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'created_at'      => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('product_id', 'products', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('stock_movements');
    }

    public function down()
    {
        $this->forge->dropTable('stock_movements');
    }
}
