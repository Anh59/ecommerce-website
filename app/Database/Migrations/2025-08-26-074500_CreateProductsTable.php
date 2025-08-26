<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateProductsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'              => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'name'            => ['type' => 'VARCHAR', 'constraint' => 255],
            'slug'            => ['type' => 'VARCHAR', 'constraint' => 255, 'unique' => true],
            'sku'             => ['type' => 'VARCHAR', 'constraint' => 100, 'unique' => true],
            'category_id'     => ['type' => 'INT', 'unsigned' => true],
            'brand_id'        => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'price'           => ['type' => 'DECIMAL', 'constraint' => '12,0'],
            'sale_price'      => ['type' => 'DECIMAL', 'constraint' => '12,0', 'null' => true],
            'short_description' => ['type' => 'TEXT', 'null' => true],
            'description'     => ['type' => 'LONGTEXT', 'null' => true],
            'specifications'  => ['type' => 'JSON', 'null' => true], // Thông số kỹ thuật dạng JSON
            'main_image'      => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'stock_quantity'  => ['type' => 'INT', 'default' => 0],
            'min_stock_level' => ['type' => 'INT', 'default' => 0], // Mức tồn kho tối thiểu
            'stock_status'    => ['type' => 'ENUM', 'constraint' => ['in_stock', 'out_of_stock', 'low_stock', 'pre_order'], 'default' => 'in_stock'],
            'weight'          => ['type' => 'DECIMAL', 'constraint' => '8,2', 'null' => true],
            'dimensions'      => ['type' => 'JSON', 'null' => true], // {length, width, height}
            'material'        => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true], // Chất liệu (gỗ, nhựa, kim loại...)
            'origin_country'  => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'warranty_period' => ['type' => 'INT', 'null' => true], // Thời gian bảo hành (tháng)
            'is_featured'     => ['type' => 'BOOLEAN', 'default' => false],
            'is_active'       => ['type' => 'BOOLEAN', 'default' => true],
            'meta_title'      => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'meta_description' => ['type' => 'TEXT', 'null' => true],
            'created_at'      => ['type' => 'DATETIME', 'null' => true],
            'updated_at'      => ['type' => 'DATETIME', 'null' => true],
            'deleted_at'      => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('category_id', 'categories', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->addForeignKey('brand_id', 'brands', 'id', 'CASCADE', 'SET NULL');
        $this->forge->createTable('products');
    }

    public function down()
    {
        $this->forge->dropTable('products');
    }
}
