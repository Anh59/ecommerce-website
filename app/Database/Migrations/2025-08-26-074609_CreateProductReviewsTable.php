<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateProductReviewsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'              => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'product_id'      => ['type' => 'INT', 'unsigned' => true],
            'customer_id'     => ['type' => 'INT', 'unsigned' => true],
            'order_id'        => ['type' => 'INT', 'unsigned' => true], // Chỉ khách đã mua mới được review
            'rating'          => ['type' => 'TINYINT', 'constraint' => 1], // 1-5 stars
            'title'           => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'comment'         => ['type' => 'TEXT', 'null' => true],
            'is_verified'     => ['type' => 'BOOLEAN', 'default' => true], // Đã mua hàng
            'is_approved'     => ['type' => 'BOOLEAN', 'default' => false],
            'helpful_count'   => ['type' => 'INT', 'default' => 0],
            'created_at'      => ['type' => 'DATETIME', 'null' => true],
            'updated_at'      => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['product_id', 'customer_id', 'order_id']);
        $this->forge->addForeignKey('product_id', 'products', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('customer_id', 'customers', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('order_id', 'orders', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('product_reviews');
    }

    public function down()
    {
        $this->forge->dropTable('product_reviews');
    }
}
