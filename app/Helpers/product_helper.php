<?php

if (!function_exists('load_best_sellers')) {
    /**
     * Load best sellers partial view with data
     * 
     * @param array $options Configuration options
     * @return string HTML output
     */
    function load_best_sellers($options = [])
    {
        $productModel = new \App\Models\ProductModel();
        
        $defaults = [
            'limit' => 8,
            'title' => 'Best Sellers',
            'subtitle' => 'shop',
            'type' => 'best_sellers' // best_sellers, featured, latest
        ];
        
        $config = array_merge($defaults, $options);
        
        // Lấy products theo type
        switch ($config['type']) {
            case 'featured':
                $products = $productModel->getFeaturedProducts($config['limit']);
                break;
            case 'latest':
                $products = $productModel->getLatestProducts($config['limit']);
                break;
            case 'best_sellers':
            default:
                $products = $productModel->getBestSellers($config['limit']);
                break;
        }
        
        // Prepare data for view
        $data = [
            'bestSellers' => $products,
            'sectionTitle' => $config['title'],
            'sectionSubtitle' => $config['subtitle']
        ];
        
        return view('Customers/best_sellers', $data);
    }
}

if (!function_exists('render_best_sellers')) {
    /**
     * Echo best sellers partial view
     * 
     * @param array $options
     * @return void
     */
    function render_best_sellers($options = [])
    {
        echo load_best_sellers($options);
    }
}