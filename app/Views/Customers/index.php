<?= $this->extend('Customers/layout/main') ?>

<?= $this->section('content') ?>

    <!-- banner part start-->
    <section class="banner_part">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-12">
                    <div class="banner_slider owl-carousel">
                        <?php if (!empty($bannerProducts)): ?>
                            <?php foreach ($bannerProducts as $product): ?>
                                <div class="single_banner_slider">
                                    <div class="row">
                                        <div class="col-lg-5 col-md-8">
                                            <div class="banner_text">
                                                <div class="banner_text_iner">
                                                    <h1><?= esc($product['name']) ?></h1>
                                                    <p><?= esc($product['short_description'] ?? 'Sản phẩm chất lượng cao, giá cả hợp lý') ?></p>
                                                    <a href="<?= route_to('product_detail', $product['slug']) ?>" class="btn_2">buy now</a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="banner_img d-none d-lg-block">
                                            <img src="<?= base_url($product['main_image'] ?? 'aranoz-master/img/banner_img.png') ?>" 
                                                 alt="<?= esc($product['name']) ?>">
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <!-- Fallback banner -->
                            <div class="single_banner_slider">
                                <div class="row">
                                    <div class="col-lg-5 col-md-8">
                                        <div class="banner_text">
                                            <div class="banner_text_iner">
                                                <h1>Welcome to Our Shop</h1>
                                                <p>Discover amazing products at great prices</p>
                                                <a href="<?= base_url('/shop') ?>" class="btn_2">shop now</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="banner_img d-none d-lg-block">
                                        <img src="<?= base_url('aranoz-master/img/banner_img.png') ?>" alt="">
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="slider-counter"></div>
                </div>
            </div>
        </div>
    </section>
    <!-- banner part start-->

    <!-- feature_part start-->
    <section class="feature_part padding_top">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="section_tittle text-center">
                        <h2>Featured Category</h2>
                    </div>
                </div>
            </div>
            <div class="row align-items-center justify-content-between">
                <?php if (!empty($featuredCategories)): ?>
                    <?php 
                    $colSizes = ['col-lg-7 col-sm-6', 'col-lg-5 col-sm-6', 'col-lg-5 col-sm-6', 'col-lg-7 col-sm-6'];
                    foreach ($featuredCategories as $index => $category): 
                        $colClass = $colSizes[$index % 4];
                    ?>
                        <div class="<?= $colClass ?>">
                            <div class="single_feature_post_text">
                                <p>Premium Quality</p>
                                <h3><?= esc($category['name']) ?></h3>
                                <a href="<?= base_url('/category/' . $category['slug']) ?>" class="feature_btn">
                                    EXPLORE NOW <i class="fas fa-play"></i>
                                </a>
                                <img src="<?= base_url($category['image_url'] ?? 'aranoz-master/img/feature/feature_' . (($index % 4) + 1) . '.png') ?>" 
                                     alt="<?= esc($category['name']) ?>">
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <!-- Fallback categories -->
                    <div class="col-lg-7 col-sm-6">
                        <div class="single_feature_post_text">
                            <p>Premium Quality</p>
                            <h3>Latest Products</h3>
                            <a href="#" class="feature_btn">EXPLORE NOW <i class="fas fa-play"></i></a>
                            <img src="<?= base_url('aranoz-master/img/feature/feature_1.png') ?>" alt="">
                        </div>
                    </div>
                    <div class="col-lg-5 col-sm-6">
                        <div class="single_feature_post_text">
                            <p>Premium Quality</p>
                            <h3>Best Sellers</h3>
                            <a href="#" class="feature_btn">EXPLORE NOW <i class="fas fa-play"></i></a>
                            <img src="<?= base_url('aranoz-master/img/feature/feature_2.png') ?>" alt="">
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
    <!-- upcoming_event part start-->

 <!-- product_list start-->
<section class="product_list section_padding">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-12">
                <div class="section_tittle text-center">
                    <h2>awesome <span>shop</span></h2>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="product_list_slider owl-carousel">
                    <?php if (!empty($latestProductsSlide1)): ?>
                        <!-- Slide 1 -->
                        <div class="single_product_list_slider">
                            <div class="row align-items-center justify-content-between">
                                <?php foreach ($latestProductsSlide1 as $product): ?>
                                    <div class="col-lg-3 col-sm-6">
                                        <div class="single_product_item">
                                            <a href="<?= route_to('product_detail', $product['slug']) ?>">
                                                <img src="<?= base_url($product['main_image'] ?? 'aranoz-master/img/product/product_1.png') ?>" 
                                                     alt="<?= esc($product['name']) ?>">
                                            </a>
                                            <div class="single_product_text">
                                                <h4>
                                                    <a href="<?= route_to('product_detail', $product['slug']) ?>">
                                                        <?= esc($product['name']) ?>
                                                    </a>
                                                </h4>
                                                <?php if ($product['sale_price'] && $product['sale_price'] < $product['price']): ?>
                                                    <h3><?= number_format($product['sale_price']) ?>₫</h3>
                                                <?php else: ?>
                                                    <h3><?= number_format($product['price']) ?>₫</h3>
                                                <?php endif; ?>
                                                <a href="#" class="add_cart add-to-cart-btn" data-product-id="<?= $product['id'] ?>">+ add to cart<i class="ti-heart"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($latestProductsSlide2)): ?>
                        <!-- Slide 2 -->
                        <div class="single_product_list_slider">
                            <div class="row align-items-center justify-content-between">
                                <?php foreach ($latestProductsSlide2 as $product): ?>
                                    <div class="col-lg-3 col-sm-6">
                                        <div class="single_product_item">
                                            <a href="<?= route_to('product_detail', $product['slug']) ?>">
                                                <img src="<?= base_url($product['main_image'] ?? 'aranoz-master/img/product/product_1.png') ?>" 
                                                     alt="<?= esc($product['name']) ?>">
                                            </a>
                                            <div class="single_product_text">
                                                <h4>
                                                    <a href="<?= route_to('product_detail', $product['slug']) ?>">
                                                        <?= esc($product['name']) ?>
                                                    </a>
                                                </h4>
                                                <?php if ($product['sale_price'] && $product['sale_price'] < $product['price']): ?>
                                                    <h3><?= number_format($product['sale_price']) ?>₫</h3>
                                                <?php else: ?>
                                                    <h3><?= number_format($product['price']) ?>₫</h3>
                                                <?php endif; ?>
                                                <a href="#" class="add_cart add-to-cart-btn" data-product-id="<?= $product['id'] ?>">+ add to cart<i class="ti-heart"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (empty($latestProductsSlide1) && empty($latestProductsSlide2)): ?>
                        <!-- Fallback -->
                        <div class="single_product_list_slider">
                            <div class="row align-items-center justify-content-between">
                                <?php for($i = 1; $i <= 8; $i++): ?>
                                    <div class="col-lg-3 col-sm-6">
                                        <div class="single_product_item">
                                            <img src="<?= base_url('aranoz-master/img/product/product_' . $i . '.png') ?>" alt="">
                                            <div class="single_product_text">
                                                <h4>Quartz Belt Watch</h4>
                                                <h3>$150.00</h3>
                                                <a href="#" class="add_cart">+ add to cart<i class="ti-heart"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                <?php endfor; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- product_list part start-->

    <!-- awesome_shop start-->
    <section class="our_offer section_padding">
        <div class="container">
            <div class="row align-items-center justify-content-between">
                <div class="col-lg-6 col-md-6">
                    <div class="offer_img">
                        <?php if (!empty($weeklySaleProduct)): ?>
                            <img src="<?= base_url($weeklySaleProduct['main_image'] ?? 'aranoz-master/img/offer_img.png') ?>" 
                                 alt="<?= esc($weeklySaleProduct['name']) ?>">
                        <?php else: ?>
                            <img src="<?= base_url('aranoz-master/img/offer_img.png') ?>" alt="">
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-lg-6 col-md-6">
                    <div class="offer_text">
                        <?php if (!empty($weeklySaleProduct)): ?>
                            <h2><?= esc($weeklySaleProduct['name']) ?><br>
                                <?= number_format($weeklySaleProduct['discount_percent'], 0) ?>% Off</h2>
                        <?php else: ?>
                            <h2>Weekly Sale on<br>60% Off All Products</h2>
                        <?php endif; ?>
                        <div class="date_countdown">
                            <div id="timer">
                                <div id="days" class="date"></div>
                                <div id="hours" class="date"></div>
                                <div id="minutes" class="date"></div>
                                <div id="seconds" class="date"></div>
                            </div>
                        </div>
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="enter email address"
                                aria-label="Recipient's username" aria-describedby="basic-addon2">
                            <div class="input-group-append">
                                <a href="#" class="input-group-text btn_2" id="basic-addon2">book now</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- awesome_shop part start-->

    <!-- Best Sellers - Sử dụng helper hoặc view trực tiếp -->
    <?php if (!empty($bestSellers)): ?>
        <?= view('Customers/best_sellers', [
            'bestSellers' => $bestSellers,
            'sectionTitle' => 'Best Sellers',
            'sectionSubtitle' => 'shop'
        ]) ?>
    <?php else: ?>
        <!-- Fallback Best Sellers section -->
        <section class="product_list best_seller section_padding">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-12">
                        <div class="section_tittle text-center">
                            <h2>Best Sellers <span>shop</span></h2>
                        </div>
                    </div>
                </div>
                <div class="row align-items-center justify-content-between">
                    <div class="col-lg-12">
                        <p class="text-center">No best sellers available yet</p>
                    </div>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <!-- subscribe_area part start-->
    <section class="subscribe_area section_padding">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-7">
                    <div class="subscribe_area_text text-center">
                        <h5>Join Our Newsletter</h5>
                        <h2>Subscribe to get Updated
                            with new offers</h2>
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="enter email address"
                                aria-label="Recipient's username" aria-describedby="basic-addon2">
                            <div class="input-group-append">
                                <a href="#" class="input-group-text btn_2" id="basic-addon2">subscribe now</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--::subscribe_area part end::-->

    <!-- subscribe_area part start-->
    <section class="client_logo padding_top">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-12">
                    <?php if (!empty($topBrands)): ?>
                        <?php foreach ($topBrands as $brand): ?>
                            <div class="single_client_logo">
                                <img src="<?= base_url($brand['logo_url'] ?? 'aranoz-master/img/client_logo/client_logo_1.png') ?>" 
                                     alt="<?= esc($brand['name']) ?>">
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <!-- Fallback brand logos -->
                        <?php for($i = 1; $i <= 10; $i++): ?>
                            <div class="single_client_logo">
                                <img src="<?= base_url('aranoz-master/img/client_logo/client_logo_' . (($i % 5) + 1) . '.png') ?>" alt="">
                            </div>
                        <?php endfor; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
    <!--::subscribe_area part end::-->

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    // Add to cart functionality
    $('.add-to-cart-btn').click(function(e) {
        e.preventDefault();
        var productId = $(this).data('product-id');
        
        $.ajax({
            url: '<?= route_to('api_cart_add') ?>',
            type: 'POST',
            data: {
                product_id: productId,
                quantity: 1,
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

    function showToast(type, message) {
        var toastClass = type === 'success' ? 'alert-success' : 'alert-danger';
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