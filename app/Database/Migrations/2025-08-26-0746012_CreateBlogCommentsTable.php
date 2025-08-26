<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateBlogCommentsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'              => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'post_id'         => ['type' => 'INT', 'unsigned' => true],
            'customer_id'     => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'parent_id'       => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'author_name'     => ['type' => 'VARCHAR', 'constraint' => 100],
            'author_email'    => ['type' => 'VARCHAR', 'constraint' => 100],
            'comment'         => ['type' => 'TEXT'],
            'is_approved'     => ['type' => 'BOOLEAN', 'default' => false],
            'created_at'      => ['type' => 'DATETIME', 'null' => true],
            'updated_at'      => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('post_id', 'blog_posts', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('customer_id', 'customers', 'id', 'CASCADE', 'SET NULL');
        $this->forge->addForeignKey('parent_id', 'blog_comments', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('blog_comments');
    }

    public function down()
    {
        $this->forge->dropTable('blog_comments');
    }
}
