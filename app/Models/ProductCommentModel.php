<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductCommentModel extends Model
{
    protected $table = 'product_comments';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'product_id', 'customer_id', 'parent_id', 'comment', 'created_at', 'updated_at'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    public function getProductComments($productId)
    {
        $comments = $this->select('product_comments.*, customers.name as customer_name, customers.image_url as customer_image')
                         ->join('customers', 'customers.id = product_comments.customer_id')
                         ->where('product_comments.product_id', $productId)
                         ->where('product_comments.parent_id IS NULL')
                         ->orderBy('product_comments.created_at', 'DESC')
                         ->findAll();
        
        foreach ($comments as &$comment) {
            $comment['replies'] = $this->getCommentReplies($comment['id']);
        }
        
        return $comments;
    }

    public function getCommentReplies($parentId)
    {
        return $this->select('product_comments.*, customers.name as customer_name, customers.image_url as customer_image')
                    ->join('customers', 'customers.id = product_comments.customer_id')
                    ->where('product_comments.parent_id', $parentId)
                    ->orderBy('product_comments.created_at', 'ASC')
                    ->findAll();
    }

    public function getCommentWithCustomer($commentId)
    {
        return $this->select('product_comments.*, customers.name as customer_name, customers.image_url as customer_image')
                    ->join('customers', 'customers.id = product_comments.customer_id')
                    ->where('product_comments.id', $commentId)
                    ->first();
    }

    public function getCommentById($commentId)
    {
        return $this->find($commentId);
    }
}