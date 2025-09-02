<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateBlogPostsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'              => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'title'           => ['type' => 'VARCHAR', 'constraint' => 255],
            'slug'            => ['type' => 'VARCHAR', 'constraint' => 255, 'unique' => true],
            'excerpt'         => ['type' => 'TEXT', 'null' => true],
            'content'         => ['type' => 'LONGTEXT'],
            'featured_image'  => ['type' => 'VARCHAR', 'constraint' => 255], // Bắt buộc phải có ảnh
            'image_alt'       => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true], // Alt text cho SEO
            'gallery_images'  => ['type' => 'JSON', 'null' => true], // Nhiều ảnh phụ dạng JSON array
            'author_id'       => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'author_name'     => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true], // Tên tác giả
            'category'        => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'tags'            => ['type' => 'JSON', 'null' => true], // ["tag1", "tag2", "tag3"]
            'status'          => ['type' => 'ENUM', 'constraint' => ['draft', 'published', 'archived'], 'default' => 'draft'],
            'published_at'    => ['type' => 'DATETIME', 'null' => true],
            'meta_title'      => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'meta_description' => ['type' => 'TEXT', 'null' => true],
            'view_count'      => ['type' => 'INT', 'default' => 0],
            'is_featured'     => ['type' => 'BOOLEAN', 'default' => false], // Bài viết nổi bật
            'reading_time'    => ['type' => 'INT', 'null' => true], // Thời gian đọc (phút)
            'created_at'      => ['type' => 'DATETIME', 'null' => true],
            'updated_at'      => ['type' => 'DATETIME', 'null' => true],
            'deleted_at'      => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('blog_posts');
    }

    public function down()
    {
        $this->forge->dropTable('blog_posts');
    }
}
