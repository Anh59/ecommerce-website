<!-- Best Sellers Section - Partial View -->
<section class="product_list best_seller section_padding">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-12">
                <div class="section_tittle text-center">
                    <h2><?= $sectionTitle ?? 'Best Sellers' ?> <span><?= $sectionSubtitle ?? 'shop' ?></span></h2>
                </div>
            </div>
        </div>
        <div class="row align-items-center justify-content-between">
            <div class="col-lg-12">
                <div class="best_product_slider owl-carousel">
                    <?php if (!empty($bestSellers) && is_array($bestSellers)): ?>
                        <?php foreach ($bestSellers as $product): ?>
                            <div class="single_product_item">
                                <a href="<?= route_to('product_detail', $product['slug']) ?>">
                                    <img src="<?= base_url($product['main_image'] ?? 'aranoz-master/img/product/product_1.png') ?>" 
                                         alt="<?= esc($product['name']) ?>">
                                    <div class="single_product_text">
                                        <h4><?= esc($product['name']) ?></h4>
                                        <?php if ($product['sale_price'] && $product['sale_price'] < $product['price']): ?>
                                            <h3 class="text-danger">
                                                <?= number_format($product['sale_price']) ?>₫
                                                <small class="text-muted"><del><?= number_format($product['price']) ?>₫</del></small>
                                            </h3>
                                        <?php else: ?>
                                            <h3><?= number_format($product['price']) ?>₫</h3>
                                        <?php endif; ?>
                                    </div>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <!-- Fallback: Static demo products -->
                        <?php for($i = 1; $i <= 5; $i++): ?>
                            <div class="single_product_item">
                                <img src="<?= base_url("aranoz-master/img/product/product_{$i}.png") ?>" alt="">
                                <div class="single_product_text">
                                    <h4>Best Seller Product <?= $i ?></h4>
                                    <h3><?= number_format(150000 * $i) ?>₫</h3>
                                </div>
                            </div>
                        <?php endfor; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>