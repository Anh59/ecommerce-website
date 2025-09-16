<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductReviewModel extends Model
{
    protected $table = 'product_reviews';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'product_id', 'customer_id', 'order_id', 'rating', 'title', 
        'comment', 'is_verified', 'helpful_count', 'created_at', 'updated_at'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    public function getProductReviews($productId)
    {
        return $this->select('product_reviews.*, customers.name as customer_name, customers.image_url as customer_image')
                    ->join('customers', 'customers.id = product_reviews.customer_id')
                    ->where('product_reviews.product_id', $productId)
                    ->where('product_reviews.is_verified', 1)
                    ->orderBy('product_reviews.created_at', 'DESC')
                    ->findAll();
    }

    public function getReviewStats($productId)
    {
        $stats = [
            'total_reviews' => 0,
            'average_rating' => 0,
            'rating_counts' => [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0]
        ];
        
        $reviews = $this->where('product_id', $productId)
                        ->where('is_verified', 1)
                        ->findAll();
        
        if (!empty($reviews)) {
            $stats['total_reviews'] = count($reviews);
            
            $totalRating = 0;
            foreach ($reviews as $review) {
                $totalRating += $review['rating'];
                if (isset($stats['rating_counts'][$review['rating']])) {
                    $stats['rating_counts'][$review['rating']]++;
                }
            }
            
            $stats['average_rating'] = $totalRating / $stats['total_reviews'];
        }
        
        return $stats;
    }

    public function getReviewById($reviewId)
    {
        return $this->find($reviewId);
    }

    public function getCustomerReviewForProduct($customerId, $productId)
    {
        return $this->where('customer_id', $customerId)
                    ->where('product_id', $productId)
                    ->first();
    }
}