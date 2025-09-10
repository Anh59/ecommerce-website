<?php
namespace App\Database\Migrations;
use CodeIgniter\Database\Migration;
class CreateDiscountCoupons extends Migration
{
    public function up()
    {
        // Bảng discount_coupons
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'code' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'unique'     => true,
            ],
            'type' => [
                'type'       => 'ENUM',
                'constraint' => ['percentage', 'fixed'],
                'default'    => 'fixed',
            ],
            'value' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => 0.00,
            ],
            'min_order_amount' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
                'default'    => 0.00,
            ],
            'usage_limit' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
            ],
            'used_count' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
            ],
            'apply_all' => [
                'type'       => 'BOOLEAN',
                'default'    => true,
            ],
            'start_date' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'end_date' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'is_active' => [
                'type'       => 'BOOLEAN',
                'default'    => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('discount_coupons');
        // Bảng discount_coupon_products (liên kết coupon với product)
        $this->forge->addField([
            'coupon_id' => [
                'type'     => 'INT',
                'unsigned' => true,
            ],
            'product_id' => [
                'type'     => 'INT',
                'unsigned' => true,
            ],
        ]);
        $this->forge->addKey(['coupon_id', 'product_id'], true);
        $this->forge->addForeignKey('coupon_id', 'discount_coupons', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('product_id', 'products', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('discount_coupon_products');
    }
    public function down()
    {
        $this->forge->dropTable('discount_coupon_products', true);
        $this->forge->dropTable('discount_coupons', true);
    }
}