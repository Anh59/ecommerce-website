<?= $this->extend('Customers/layout/main') ?>

<?= $this->section('styles') ?>
    <style>
        
        .review-form, .comment-form { display: none; }
        .review-form.active, .comment-form.active { display: block; }
        .rating i { cursor: pointer; }
        .rating i.active { color: #ffc107; }
        .reply-form { margin-left: 50px; margin-top: 10px; display: none; }
        .reply-form.active { display: block; }
        
        /* NEW: Improved Button Layout */
        .card_area { 
            display: flex; 
            flex-direction: column; 
            gap: 15px; 
        }
        
        .top-row { 
            display: flex; 
            align-items: center; 
            justify-content: space-between; 
            gap: 15px; 
        }
        
        .add-cart-wishlist { 
            display: flex; 
            align-items: center; 
            gap: 10px; 
        }
        
        .buy-now-section { 
            width: 100%; 
        }
        
        .btn-buy-now { 
            background: #ff6b35; 
            color: white; 
            border: none; 
            padding: 15px 40px; 
            font-size: 16px; 
            font-weight: 600; 
            text-transform: uppercase; 
            letter-spacing: 1px; 
            transition: all 0.3s ease; 
            width: 100%;
            border-radius: 5px;
        }
        
        .btn-buy-now:hover { 
            background: #e55a2e; 
            color: white; 
            transform: translateY(-2px); 
            box-shadow: 0 5px 15px rgba(255, 107, 53, 0.3); 
        }
        
        .wishlist-btn {
            background: transparent;
            border: 2px solid #ddd;
            padding: 12px 15px;
            border-radius: 5px;
            transition: all 0.3s ease;
        }
        
        .wishlist-btn:hover {
            border-color: #ff0000;
            background: #ff0000;
            color: white;
        }
        
        .wishlist-btn.active {
            border-color: #ff0000;
            background: #ff0000;
            color: white;
        }

        /* Review avatar styling */
        .review_item .media img {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #f0f0f0;
        }

        /* Comment avatar styling */
        .comment_list .review_item .media img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #f0f0f0;
        }

        /* Reply indentation */
        .comment-reply {
            margin-left: 60px;
            padding-left: 20px;
            border-left: 2px solid #f0f0f0;
        }
        
        /* Price styling */
        .sale-price {
            color: #ff6b35;
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 5px;
        }
        
        .original-price {
            text-decoration: line-through;
            color: #999;
            font-size: 18px;
            margin-left: 10px;
        }
        
        .normal-price {
            color: #222;
            font-size: 24px;
            font-weight: 700;
        }
        
        /* NEW: Vertical Thumbnails on Right Side */
        .product_slider_inner {
            display: flex;
            gap: 15px;
        }
        
        .main-carousel-container {
            flex: 1;
        }
        
        .vertical-thumbnails {
            width: 100px;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        
        .product-carousel .carousel-item img {
            width: 100%;
            height: 450px;
            object-fit: cover;
            border-radius: 8px;
        }
        
        .vertical-thumbnails .thumb-item {
            cursor: pointer;
            border: 2px solid transparent;
            border-radius: 5px;
            transition: all 0.3s ease;
            padding: 2px;
        }
        
        .vertical-thumbnails .thumb-item.active {
            border-color: #ff6b35;
        }
        
        .vertical-thumbnails .thumb-item img {
            width: 100%;
            height: 80px;
            object-fit: cover;
            border-radius: 3px;
        }
        
        .vertical-thumbnails .thumb-item:hover {
            border-color: #ff6b35;
        }
        
        .carousel-control-prev,
        .carousel-control-next {
            width: 40px;
            height: 40px;
            background: rgba(0,0,0,0.5);
            border-radius: 50%;
            top: 50%;
            transform: translateY(-50%);
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        
        .product-carousel:hover .carousel-control-prev,
        .product-carousel:hover .carousel-control-next {
            opacity: 1;
        }
        
        .carousel-control-prev {
            left: 15px;
        }
        
        .carousel-control-next {
            right: 15px;
        }
        
        .carousel-indicators {
            bottom: -50px;
        }
        
        .carousel-indicators li {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background-color: #ddd;
        }
        
        .carousel-indicators .active {
            background-color: #ff6b35;
        }
        
        @media (max-width: 768px) {
            .top-row { 
                flex-direction: column; 
                align-items: stretch; 
            }
            .add-cart-wishlist { 
                justify-content: center; 
            }
            
            /* Ẩn thumbnails trên mobile */
            .vertical-thumbnails {
                display: none;
            }
            
            .product-carousel .carousel-item img {
                height: 350px;
            }
            
            .carousel-control-prev,
            .carousel-control-next {
                opacity: 1;
                width: 35px;
                height: 35px;
            }
            
            /* Hiển thị indicators trên mobile */
            .carousel-indicators {
                display: flex;
                bottom: 15px;
            }
            
            /* Price responsive */
            .sale-price {
                font-size: 22px;
            }
            
            .original-price {
                font-size: 16px;
            }
            
            .normal-price {
                font-size: 22px;
            }
        }
        
        @media (max-width: 576px) {
            .product-carousel .carousel-item img {
                height: 300px;
            }
            
            .sale-price {
                font-size: 20px;
            }
            
            .original-price {
                font-size: 14px;
            }
            
            .normal-price {
                font-size: 20px;
            }
        }

    </style>
<?= $this->endSection() ?>

<?= $this->section('content') ?> 
<!-- breadcrumb start-->
<section class="breadcrumb breadcrumb_bg">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="breadcrumb_iner">
                    <div class="breadcrumb_iner_item">
                        <h2>Shop Single</h2>
                        <p>Home <span>-</span> Shop Single</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- breadcrumb start-->

<!--================Single Product Area =================-->
<div class="product_image_area section_padding">
    <div class="container">
        <div class="row s_product_inner justify-content-between">
            <div class="col-lg-7 col-xl-7">
                <div class="product_slider_img">
                    <!-- NEW: Vertical Layout with Thumbnails on Right -->
                    <div class="product_slider_inner">
                        <!-- Main Carousel -->
                        <div class="main-carousel-container">
                            <div id="productCarousel" class="carousel slide product-carousel" data-ride="carousel">
                                <!-- Indicators -->
                                <ol class="carousel-indicators">
                                    <?php if (!empty($productImages)): ?>
                                        <?php foreach ($productImages as $index => $image): ?>
                                            <li data-target="#productCarousel" data-slide-to="<?= $index ?>" 
                                                class="<?= $index === 0 ? 'active' : '' ?>"></li>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <li data-target="#productCarousel" data-slide-to="0" class="active"></li>
                                    <?php endif; ?>
                                </ol>
                                
                                <!-- Slides -->
                                <div class="carousel-inner">
                                    <?php if (!empty($productImages)): ?>
                                        <?php foreach ($productImages as $index => $image): ?>
                                            <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
                                                <img src="<?= base_url($image['image_url']) ?>" 
                                                     class="d-block w-100" 
                                                     alt="<?= esc($product['name']) ?>">
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <div class="carousel-item active">
                                            <img src="<?= base_url('aranoz-master/img/product/single-product/product_1.png') ?>" 
                                                 class="d-block w-100" 
                                                 alt="<?= esc($product['name']) ?>">
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <!-- Controls -->
                                <a class="carousel-control-prev" href="#productCarousel" role="button" data-slide="prev">
                                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                    <span class="sr-only">Previous</span>
                                </a>
                                <a class="carousel-control-next" href="#productCarousel" role="button" data-slide="next">
                                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                    <span class="sr-only">Next</span>
                                </a>
                            </div>
                        </div>
                        
                        <!-- Vertical Thumbnails on Right - Ẩn trên mobile -->
                        <?php if (!empty($productImages) && count($productImages) > 1): ?>
                        <div class="vertical-thumbnails">
                            <?php foreach ($productImages as $index => $image): ?>
                                <div class="thumb-item <?= $index === 0 ? 'active' : '' ?>" 
                                     data-index="<?= $index ?>">
                                    <img src="<?= base_url($image['image_url']) ?>" 
                                         alt="<?= esc($product['name']) ?> - Thumbnail <?= $index + 1 ?>">
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="col-lg-5 col-xl-4">
                <div class="s_product_text">
                    <h5>
                        <?php if (!empty($previousProduct)): ?>
                            <a href="<?= route_to('product_detail', $previousProduct['slug']) ?>">previous</a> <span>|</span>
                        <?php endif; ?>
                        <?php if (!empty($nextProduct)): ?>
                            <a href="<?= route_to('product_detail', $nextProduct['slug']) ?>">next</a>
                        <?php endif; ?>
                    </h5>
                    <h3><?= esc($product['name']) ?></h3>
                    
                    <!-- UPDATED: Price with strike-through for original price -->
                    <?php if ($product['sale_price'] && $product['sale_price'] < $product['price']): ?>
                        <div class="price-section">
                            <h2 class="sale-price"><?= number_format($product['sale_price']) ?>₫</h2>
                            <span class="original-price"><?= number_format($product['price']) ?>₫</span>
                        </div>
                    <?php else: ?>
                        <h2 class="normal-price"><?= number_format($product['price']) ?>₫</h2>
                    <?php endif; ?>
                    
                    <ul class="list">
                        <li>
                            <a class="active" href="#">
                                <span>Category</span> : <?= esc($category['name'] ?? 'Unknown') ?>
                            </a>
                        </li>
                        <li>
                            <a href="#">
                                <span>Brand</span> : <?= esc($brand['name'] ?? 'Unknown') ?>
                            </a>
                        </li>
                        <li>
                            <a href="#">
                                <span>Availability</span> : 
                                <?php if ($product['stock_status'] == 'in_stock'): ?>
                                    <span class="text-success">In Stock</span>
                                <?php elseif ($product['stock_status'] == 'low_stock'): ?>
                                    <span class="text-warning">Low Stock (<?= $product['stock_quantity'] ?> left)</span>
                                <?php else: ?>
                                    <span class="text-danger">Out of Stock</span>
                                <?php endif; ?>
                            </a>
                        </li>
                    </ul>
                    
                    <p><?= esc($product['short_description']) ?></p>
                    
                    <!-- IMPROVED: New Button Layout -->
                    <div class="card_area">
                        <!-- Top Row: Quantity + Add to Cart + Wishlist -->
                        <div class="top-row">
                            <div class="product_count">
                                <span class="inumber-decrement"> <i class="ti-minus"></i></span>
                                <input class="input-number" type="text" value="1" min="1" max="<?= $product['stock_quantity'] ?>" id="quantity">
                                <span class="number-increment"> <i class="ti-plus"></i></span>
                            </div>
                            
                            <div class="add-cart-wishlist">
                                <?php if ($product['stock_status'] != 'out_of_stock'): ?>
                                    <a href="#" class="btn_3 add-to-cart-btn" data-product-id="<?= $product['id'] ?>">
                                        <i class="ti-shopping-cart"></i> 
                                    </a>
                                <?php else: ?>
                                    <a href="#" class="btn_3 disabled">Out of Stock</a>
                                <?php endif; ?>
                                
                                <button class="wishlist-btn <?= $isInWishlist ? 'active' : '' ?>" 
                                        data-product-id="<?= $product['id'] ?>" title="Add to Wishlist">
                                    <i class="ti-heart"></i>
                                </button>
                            </div>
                        </div>
                        
                        <!-- Bottom Row: Buy Now Button -->
                        <?php if ($product['stock_status'] != 'out_of_stock'): ?>
                            <div class="buy-now-section">
                                <button class="btn btn-buy-now buy-now-btn" data-product-id="<?= $product['id'] ?>">
                                    <i class="ti-bolt"></i> Mua ngay
                                </button>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--================End Single Product Area =================-->

<!--================Product Description Area =================-->
<section class="product_description_area">
    <div class="container">
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="description-tab" data-toggle="tab" href="#description" role="tab" 
                   aria-controls="description" aria-selected="true">Description</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="specification-tab" data-toggle="tab" href="#specification" role="tab" 
                   aria-controls="specification" aria-selected="false">Specification</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="comments-tab" data-toggle="tab" href="#comments" role="tab" 
                   aria-controls="comments" aria-selected="false">Comments</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="reviews-tab" data-toggle="tab" href="#reviews" role="tab" 
                   aria-controls="reviews" aria-selected="false">Reviews</a>
            </li>
        </ul>
        
        <div class="tab-content" id="myTabContent">
            <!-- Description Tab -->
            <div class="tab-pane fade show active" id="description" role="tabpanel" aria-labelledby="description-tab">
                <p><?= nl2br(esc($product['description'])) ?></p>
            </div>
            
            <!-- Specification Tab -->
            <div class="tab-pane fade" id="specification" role="tabpanel" aria-labelledby="specification-tab">
                <div class="table-responsive">
                    <table class="table">
                        <tbody>
                            <?php 
                            $specifications = json_decode($product['specifications'] ?? '{}', true);
                            if (!empty($specifications)): 
                                foreach ($specifications as $key => $value): 
                            ?>
                                <tr>
                                    <td><h5><?= esc($key) ?></h5></td>
                                    <td><h5><?= esc($value) ?></h5></td>
                                </tr>
                            <?php 
                                endforeach; 
                            else: 
                            ?>
                                <tr>
                                    <td colspan="2" class="text-center">No specifications available</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Comments Tab -->
            <div class="tab-pane fade" id="comments" role="tabpanel" aria-labelledby="comments-tab">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="comment_list" id="comments-container">
                            <?php if (!empty($comments)): ?>
                                <?php foreach ($comments as $comment): ?>
                                    <div class="review_item">
                                        <div class="media">
                                            <div class="d-flex">
                                                <img src="<?= base_url($comment['customer_image'] ?? 'aranoz-master/img/product/single-product/review-1.png') ?>" 
                                                     alt="<?= esc($comment['customer_name']) ?>" />
                                            </div>
                                            <div class="media-body">
                                                <h4><?= esc($comment['customer_name']) ?></h4>
                                                <h5><?= date('M j, Y', strtotime($comment['created_at'])) ?></h5>
                                                <a class="reply_btn" href="#" data-comment-id="<?= $comment['id'] ?>">Reply</a>
                                            </div>
                                        </div>
                                        <p><?= esc($comment['comment']) ?></p>
                                        
                                        <!-- Display replies -->
                                        <?php if (!empty($comment['replies'])): ?>
                                            <?php foreach ($comment['replies'] as $reply): ?>
                                                <div class="review_item comment-reply">
                                                    <div class="media">
                                                        <div class="d-flex">
                                                            <img src="<?= base_url($reply['customer_image'] ?? 'aranoz-master/img/product/single-product/review-1.png') ?>" 
                                                                 alt="<?= esc($reply['customer_name']) ?>" />
                                                        </div>
                                                        <div class="media-body">
                                                            <h4><?= esc($reply['customer_name']) ?></h4>
                                                            <h5><?= date('M j, Y', strtotime($reply['created_at'])) ?></h5>
                                                        </div>
                                                    </div>
                                                    <p><?= esc($reply['comment']) ?></p>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p class="text-center">No comments yet. Be the first to comment!</p>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="review_box comment-form" id="comment-form">
                            <h4>Post a comment</h4>
                            <form class="row contact_form" id="commentForm" novalidate="novalidate">
                                <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                <input type="hidden" name="parent_id" value="" id="comment-parent-id">
                                
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <textarea class="form-control" name="comment" id="comment-message" rows="4" 
                                                  placeholder="Your comment" required></textarea>
                                    </div>
                                </div>
                                <div class="col-md-12 text-right">
                                    <button type="submit" class="btn_3">Submit Comment</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Reviews Tab - SIMPLIFIED: Only display reviews -->
            <div class="tab-pane fade" id="reviews" role="tabpanel" aria-labelledby="reviews-tab">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="row total_rate">
                            <div class="col-6">
                                <div class="box_total">
                                    <h5>Overall</h5>
                                    <h4><?= number_format($reviewStats['average_rating'] ?? 0, 1) ?></h4>
                                    <h6>(<?= $reviewStats['total_reviews'] ?? 0 ?> Reviews)</h6>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="rating_list">
                                    <h3>Based on <?= $reviewStats['total_reviews'] ?? 0 ?> Reviews</h3>
                                    <ul class="list">
                                        <?php for ($i = 5; $i >= 1; $i--): ?>
                                            <li>
                                                <a href="#"><?= $i ?> Star
                                                    <?php for ($j = 0; $j < 5; $j++): ?>
                                                        <i class="fa fa-star<?= $j < $i ? '' : '-o' ?>"></i>
                                                    <?php endfor; ?>
                                                    <?= $reviewStats['rating_counts'][$i] ?? 0 ?>
                                                </a>
                                            </li>
                                        <?php endfor; ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        
                        <div class="review_list" id="reviews-container">
                            <?php if (!empty($reviews)): ?>
                                <?php foreach ($reviews as $review): ?>
                                    <div class="review_item">
                                        <div class="media">
                                            <div class="d-flex">
                                                <img src="<?= base_url($review['customer_image'] ?? 'aranoz-master/img/product/single-product/review-1.png') ?>" 
                                                     alt="<?= esc($review['customer_name']) ?>" />
                                            </div>
                                            <div class="media-body">
                                                <h4><?= esc($review['customer_name']) ?></h4>
                                                <h5><?= date('M j, Y', strtotime($review['created_at'])) ?></h5>
                                                <div class="rating">
                                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                                        <i class="fa fa-star<?= $i <= $review['rating'] ? '' : '-o' ?>"></i>
                                                    <?php endfor; ?>
                                                </div>
                                            </div>
                                        </div>
                                        <h5><?= esc($review['title']) ?></h5>
                                        <p><?= esc($review['comment']) ?></p>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p class="text-center">No reviews yet.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!--================End Product Description Area =================-->

<!-- Related Products -->

<?= render_best_sellers([
    'limit' => 5,
    'title' => 'Related Products',
    'subtitle' => 'you may also like'
]) ?>
<?= render_best_sellers([
    'limit' => 6,
    'title' => 'New Arrivals',
    'subtitle' => 'latest',
    'type' => 'latest'
]) ?>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    // Bootstrap Carousel functionality
    $('#productCarousel').carousel({
        interval: 5000, // Auto-rotate every 5 seconds
        pause: 'hover',
        wrap: true
    });

    // Thumbnail click handler - chỉ hoạt động trên desktop
    $('.thumb-item').click(function() {
        var index = $(this).data('index');
        $('#productCarousel').carousel(index);
        
        // Update active thumbnail
        $('.thumb-item').removeClass('active');
        $(this).addClass('active');
    });

    // Update active thumbnail when carousel slides - chỉ hoạt động trên desktop
    $('#productCarousel').on('slid.bs.carousel', function (e) {
        var index = $(e.relatedTarget).index();
        $('.thumb-item').removeClass('active');
        $('.thumb-item[data-index="' + index + '"]').addClass('active');
    });

    // Quantity controls
    $('.inumber-decrement').click(function() {
        var quantity = parseInt($('#quantity').val());
        if (quantity > 1) {
            $('#quantity').val(quantity - 1);
        }
    });

    $('.number-increment').click(function() {
        var quantity = parseInt($('#quantity').val());
        var maxQuantity = parseInt($('#quantity').attr('max'));
        if (quantity < maxQuantity) {
            $('#quantity').val(quantity + 1);
        }
    });

    // Add to cart
    $('.add-to-cart-btn').click(function(e) {
        e.preventDefault();
        var productId = $(this).data('product-id');
        var quantity = $('#quantity').val();
        
        $.ajax({
            url: '<?= route_to('api_cart_add') ?>',
            type: 'POST',
            data: {
                product_id: productId,
                quantity: quantity,
                <?= csrf_token() ?>: '<?= csrf_hash() ?>'
            },
            success: function(response) {
                if (response.success) {
                    showToast('success', response.message);
                    updateCartCount(response.cart_count);
                } else {
                    showToast('error', response.message);
                }
            },
            error: function() {
                showToast('error', 'Có lỗi xảy ra, vui lòng thử lại');
            }
        });
    });

    // Buy Now functionality
    $('.buy-now-btn').click(function(e) {
        e.preventDefault();
        
        var productId = $(this).data('product-id');
        var quantity = $('#quantity').val();
        
        if (!quantity || quantity < 1) {
            showToast('error', 'Vui lòng chọn số lượng hợp lệ');
            return;
        }
        
        <?php if (!session()->has('customer_id')): ?>
            showToast('error', 'Vui lòng đăng nhập để mua hàng');
            setTimeout(function() {
                window.location.href = '<?= route_to('Customers_sign') ?>';
            }, 1500);
            return;
        <?php endif; ?>
        
        $(this).prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Processing...');
        
        $.ajax({
            url: '<?= route_to('api_buy_now') ?>',
            type: 'POST',
            data: {
                product_id: productId,
                quantity: quantity,
                action: 'buy_now',
                <?= csrf_token() ?>: '<?= csrf_hash() ?>'
            },
            success: function(response) {
                if (response.success) {
                    showToast('success', 'Redirecting to checkout...');
                    setTimeout(function() {
                        window.location.href = '<?= base_url('/checkout') ?>?buy_now=1&product_id=' + productId;
                    }, 1000);
                } else {
                    showToast('error', response.message);
                    $('.buy-now-btn').prop('disabled', false).html('<i class="ti-bolt"></i> Buy Now');
                }
            },
            error: function() {
                showToast('error', 'Có lỗi xảy ra, vui lòng thử lại');
                $('.buy-now-btn').prop('disabled', false).html('<i class="ti-bolt"></i> Buy Now');
            }
        });
    });

    // Wishlist toggle
    $('.wishlist-btn').click(function(e) {
        e.preventDefault();
        var $btn = $(this);
        var productId = $btn.data('product-id');
        
        $.ajax({
            url: '<?= route_to('api_product_wishlist') ?>',
            type: 'POST',
            data: {
                product_id: productId,
                <?= csrf_token() ?>: '<?= csrf_hash() ?>'
            },
            success: function(response) {
                if (response.success) {
                    if (response.action === 'added') {
                        $btn.addClass('active');
                        showToast('success', response.message);
                    } else {
                        $btn.removeClass('active');
                        showToast('info', response.message);
                    }
                } else {
                    showToast('error', response.message);
                }
            },
            error: function() {
                showToast('error', 'Có lỗi xảy ra, vui lòng thử lại');
            }
        });
    });

    // Comment form submission
    $('#commentForm').submit(function(e) {
        e.preventDefault();
        
        <?php if (!session()->has('customer_id')): ?>
            showToast('error', 'Please login to submit a comment');
            return false;
        <?php endif; ?>
        
        var formData = $(this).serialize();
        
        $.ajax({
            url: '<?= route_to('api_product_comment') ?>',
            type: 'POST',
            data: formData + '&<?= csrf_token() ?>=<?= csrf_hash() ?>',
            success: function(response) {
                if (response.success) {
                    showToast('success', response.message);
                    $('#commentForm')[0].reset();
                    $('#comment-parent-id').val('');
                    
                    // Add new comment to the list
                    addNewComment(response.comment);
                } else {
                    showToast('error', response.message);
                    if (response.errors) {
                        console.error(response.errors);
                    }
                }
            },
            error: function(xhr) {
                showToast('error', 'Có lỗi xảy ra, vui lòng thử lại');
                console.error(xhr.responseText);
            }
        });
    });

    // Reply to comment
    $(document).on('click', '.reply_btn', function(e) {
        e.preventDefault();
        var parentId = $(this).data('comment-id');
        $('#comment-parent-id').val(parentId);
        $('#comment-form').addClass('active');
        $('html, body').animate({
            scrollTop: $('#comment-form').offset().top - 100
        }, 500);
    });

    // Tab click handlers
    $('#comments-tab').click(function() {
        $('#comment-form').addClass('active');
    });

    function addNewComment(comment) {
        var commentHtml = `
            <div class="review_item">
                <div class="media">
                    <div class="d-flex">
                        <img src="${comment.customer_image || '<?= base_url('aranoz-master/img/product/single-product/review-1.png') ?>'}" 
                             alt="${comment.customer_name}" />
                    </div>
                    <div class="media-body">
                        <h4>${comment.customer_name}</h4>
                        <h5>Just now</h5>
                        <a class="reply_btn" href="#" data-comment-id="${comment.id}">Reply</a>
                    </div>
                </div>
                <p>${comment.comment}</p>
            </div>
        `;
        
        $('#comments-container').prepend(commentHtml);
        
        if ($('#comments-container').children().length === 1) {
            $('#comments-container p.text-center').remove();
        }
    }

    function showToast(type, message) {
        var toastClass = type === 'success' ? 'alert-success' : 
                       type === 'error' ? 'alert-danger' : 'alert-info';
        
        var toast = `
            <div class="alert ${toastClass} alert-dismissible fade show position-fixed" 
                 style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
                ${message}
                <button type="button" class="close" data-dismiss="alert">
                    <span>&times;</span>
                </button>
            </div>
        `;
        
        $('body').append(toast);
        
        setTimeout(function() {
            $('.alert').fadeOut(function() {
                $(this).remove();
            });
        }, 3000);
    }

    function updateCartCount(count) {
        $('.cart-count').text(count);
    }
});
</script>
<?= $this->endSection() ?>