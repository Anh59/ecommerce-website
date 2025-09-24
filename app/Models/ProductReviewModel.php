<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductReviewModel extends Model
{
    protected $table = 'product_reviews';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'order_id', 'product_id', 'customer_id', 'rating', 'title', 'comment', 'is_verified'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    public function getProductReviews($productId, $limit = null)
    {
        $builder = $this->select('product_reviews.*, customers.name as customer_name, customers.image_url as customer_image')
                       ->join('customers', 'customers.id = product_reviews.customer_id')
                       ->where('product_reviews.product_id', $productId)
                       ->orderBy('product_reviews.created_at', 'DESC');
        
        if ($limit) {
            $builder->limit($limit);
        }
        
        return $builder->findAll();
    }

    public function getReviewStats($productId)
    {
        $reviews = $this->where('product_id', $productId)->findAll();
        
        if (empty($reviews)) {
            return [
                'total_reviews' => 0,
                'average_rating' => 0,
                'rating_counts' => [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0]
            ];
        }

        $totalReviews = count($reviews);
        $totalRating = array_sum(array_column($reviews, 'rating'));
        $averageRating = $totalRating / $totalReviews;

        $ratingCounts = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0];
        foreach ($reviews as $review) {
            $ratingCounts[$review['rating']]++;
        }

        return [
            'total_reviews' => $totalReviews,
            'average_rating' => $averageRating,
            'rating_counts' => $ratingCounts
        ];
    }
}