<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateOrdersTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'              => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'order_number'    => ['type' => 'VARCHAR', 'constraint' => 50, 'unique' => true],
            'customer_id'     => ['type' => 'INT', 'unsigned' => true],
            'status'          => ['type' => 'ENUM', 'constraint' => ['pending', 'processing', 'shipped', 'delivered', 'cancelled'], 'default' => 'pending'],
            'payment_method'  => ['type' => 'ENUM', 'constraint' => ['cod', 'momo', 'bank_transfer'], 'default' => 'cod'],
            'payment_status'  => ['type' => 'ENUM', 'constraint' => ['pending', 'paid', 'failed', 'refunded'], 'default' => 'pending'],
            'subtotal'        => ['type' => 'DECIMAL', 'constraint' => '12,0'],
            'shipping_fee'    => ['type' => 'DECIMAL', 'constraint' => '12,0', 'default' => 0],
            'total_amount'    => ['type' => 'DECIMAL', 'constraint' => '12,0'],
            'shipping_address' => ['type' => 'JSON'], // Địa chỉ giao hàng
            'billing_address' => ['type' => 'JSON', 'null' => true], // Địa chỉ thanh toán
            'notes'           => ['type' => 'TEXT', 'null' => true],
            'tracking_number' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'shipped_at'      => ['type' => 'DATETIME', 'null' => true],
            'delivered_at'    => ['type' => 'DATETIME', 'null' => true],
            'created_at'      => ['type' => 'DATETIME', 'null' => true],
            'updated_at'      => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('customer_id', 'customers', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->createTable('orders');
    }

    public function down()
    {
        $this->forge->dropTable('orders');
    }
}
