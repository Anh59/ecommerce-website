<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateInvoicesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'              => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'invoice_number'  => ['type' => 'VARCHAR', 'constraint' => 50, 'unique' => true],
            'order_id'        => ['type' => 'INT', 'unsigned' => true, 'unique' => true],
            'customer_id'     => ['type' => 'INT', 'unsigned' => true],
            'invoice_date'    => ['type' => 'DATE'],
            'due_date'        => ['type' => 'DATE', 'null' => true],
            'subtotal'        => ['type' => 'DECIMAL', 'constraint' => '12,0'],
            'tax_amount'      => ['type' => 'DECIMAL', 'constraint' => '12,0', 'default' => 0],
            'discount_amount' => ['type' => 'DECIMAL', 'constraint' => '12,0', 'default' => 0],
            'shipping_fee'    => ['type' => 'DECIMAL', 'constraint' => '12,0', 'default' => 0],
            'total_amount'    => ['type' => 'DECIMAL', 'constraint' => '12,0'],
            'status'          => ['type' => 'ENUM', 'constraint' => ['draft', 'sent', 'paid', 'overdue', 'cancelled'], 'default' => 'draft'],
            'notes'           => ['type' => 'TEXT', 'null' => true],
            'terms'           => ['type' => 'TEXT', 'null' => true],
            'created_by'      => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'created_at'      => ['type' => 'DATETIME', 'null' => true],
            'updated_at'      => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('order_id', 'orders', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('customer_id', 'customers', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->createTable('invoices');
    }

    public function down()
    {
        $this->forge->dropTable('invoices');
    }
}
