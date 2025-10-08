<?= $this->extend('Customers/layout/main') ?>

<?= $this->section('styles') ?>
    <link rel="stylesheet" href="<?= base_url('aranoz-master/css/price_rangs.css'); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ion-rangeslider/2.3.1/css/ion.rangeSlider.min.css"/>
    <style>
    /* Wishlist & Cart Styles */
    .wishlist-btn { 
        position: relative;
        background: none;
        border: none;
        padding: 5px;
        cursor: pointer;
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s ease;
    }
    
    .product-item:hover .wishlist-btn {
        opacity: 1;
        visibility: visible;
    }
    
    .wishlist-btn.active i { color: #ff0000 !important; }
    .loading { opacity: 0.6; pointer-events: none; }
    .product-item { transition: all 0.3s ease; }
    .out-of-stock { opacity: 0.7; }
    .out-of-stock .add_cart { 
        background: #ccc !important; 
        cursor: not-allowed; 
        pointer-events: none;
    }
    .cart-count {
        position: absolute;
        top: -8px;
        right: -8px;
        background: #ff6b35;
        color: white;
        border-radius: 50%;
        width: 18px;
        height: 18px;
        font-size: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    /* Dropdown styles - Giữ lại cần thiết */
    .nice-select {
        background-color: #fff;
        border-radius: 0px;
        border: 1px solid #eeeeee;
        cursor: pointer;
        display: block;
        font-family: "Poppins", sans-serif;
        font-size: 14px;
        height: 40px;
        line-height: 40px;
        padding-left: 20px;
        padding-right: 40px;
        position: relative;
        text-align: left !important;
        transition: all 0.2s ease-in-out;
        width: auto;
        margin-left: 10px;
    }

    .nice-select:hover { border-color: #dbdbdb; }
    .nice-select:active, .nice-select.open, .nice-select:focus { border-color: #999; }

    .nice-select:after {
        content: "\f0d7";
        font: normal normal normal 14px/1 FontAwesome;
        border: none;
        color: #555555;
        margin-top: -6px;
        right: 20px;
        position: absolute;
        top: 50%;
    }

    .nice-select.open:after { transform: rotate(180deg); }

    .nice-select .list {
        background-color: #fff;
        border-radius: 0px;
        box-shadow: 0 0 0 1px rgba(68, 68, 68, 0.11);
        opacity: 0;
        overflow: hidden;
        padding: 0;
        pointer-events: none;
        position: absolute;
        top: 100%;
        left: 0;
        transform: scale(0.75) translateY(-21px);
        transition: all 0.2s cubic-bezier(0.5, 0, 0, 1.25), opacity 0.15s ease-out;
        z-index: 9;
        width: 100%;
    }

    .nice-select.open .list {
        opacity: 1;
        pointer-events: auto;
        transform: scale(1) translateY(0);
    }

    .nice-select .option {
        cursor: pointer;
        line-height: 40px;
        list-style: none;
        min-height: 40px;
        padding-left: 20px;
        padding-right: 20px;
        text-align: left;
        transition: all 0.2s;
    }

    .nice-select .option:hover { background-color: #f6f6f6; }
    .nice-select .option.selected { font-weight: bold; }
    
    /* Filter active state */
    .category-filter.active, .brand-filter.active {
        color: #ff6b35 !important;
        font-weight: bold;
    }
    
    /* Product item styles */
    .single_product_item {
        position: relative;
        height: 100%;
        display: flex;
        flex-direction: column;
    }
    
    .single_product_item img {
        width: 100%;
        height: 250px;
        object-fit: cover;
        object-position: center;
    }
    
    .product-item {
        height: 100%;
        display: flex;
        flex-direction: column;
    }
    
    .single_product_text {
        flex-grow: 1;
        display: flex;
        flex-direction: column;
        padding: 32px 0;
        background-color: #fff;
        transition: 0.5s;
    }

    .single_product_item:hover .single_product_text {
        padding: 32px 32px;
    }
    
    .price-section {
        margin: 10px 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .sale-price {
        color: #ff6b35;
        font-weight: bold;
    }
    
    .original-price {
        text-decoration: line-through;
        color: red;
        font-size: 0.9em;
    }
    
    .product-actions {
        margin-top: auto;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    /* Product name and price always visible */
    .single_product_text h4 {
        font-weight: 700;
        font-size: 18px;
        margin-bottom: 14px;
    }

    .single_product_text h4 a {
        color: #2a2a2a;
        text-decoration: none;
        transition: 0.3s;
    }

    .single_product_text h4 a:hover {
        color: #ff3368;
    }

    .single_product_text h3 {
        font-weight: 300;
        font-size: 18px;
    }

    /* Add to cart button - hidden by default, show on hover */
    .single_product_text .add_cart {
        color: #ff3368;
        text-transform: uppercase;
        font-size: 18px;
        font-weight: 500;
        display: block;
        margin-top: 10px;
        opacity: 0;
        visibility: hidden;
        transition: 0.5s;
        text-decoration: none;
        flex-grow: 1;
        margin-right: 10px;
    }

    .single_product_item:hover .single_product_text .add_cart {
        opacity: 1;
        visibility: visible;
    }

    .add_cart i {
        float: right;
        font-size: 18px;
        line-height: 26px;
        color: #000;
    }
    
    .stock-indicator {
        position: absolute;
        top: 10px;
        left: 10px;
        background: #ff6b35;
        color: white;
        padding: 3px 8px;
        border-radius: 3px;
        font-size: 12px;
        font-weight: bold;
    }
    
    .sale-badge {
        position: absolute;
        top: 10px;
        right: 10px;
        background: #ff0000;
        color: white;
        padding: 3px 8px;
        border-radius: 3px;
        font-size: 12px;
        font-weight: bold;
    }
    
    .out-of-stock .single_product_text { opacity: 0.7; }
    
    .filter-actions { margin-top: 15px; }
    
    /* Range slider styles */
    .irs--flat .irs-bar { background: #ff6b35; }
    .irs--flat .irs-from, .irs--flat .irs-to, .irs--flat .irs-single {
        background: #ff6b35;
    }
    .irs--flat .irs-handle>i:first-child { background: #ff6b35; }

    /* Improve product height consistency */
    .latest_product_inner .col-lg-4,
    .latest_product_inner .col-sm-6 {
        display: flex;
        align-items: stretch;
    }

    /* Hover effects */
    .single_product_item:hover {
        box-shadow: 0px 10px 20px 0px rgba(0, 23, 51, 0.09);
    }

    /* Toast notifications */
    .alert.position-fixed {
        top: 20px;
        right: 20px;
        z-index: 9999;
        min-width: 300px;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

    <!--================Home Banner Area =================-->
    <section class="breadcrumb breadcrumb_bg">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="breadcrumb_iner">
                        <div class="breadcrumb_iner_item">
                            <h2>Danh mục sản phẩm</h2>
                            <p>Trang chủ <span>-</span> Danh mục sản phẩm</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- breadcrumb start-->

    <!--================Category Product Area =================-->
    <section class="cat_product_area section_padding">
        <div class="container">
            <div class="row">
                <div class="col-lg-3">
                    <div class="left_sidebar_area">
                        <aside class="left_widgets p_filter_widgets">
                            <div class="l_w_title">
                                <h3>Danh mục</h3>
                            </div>
                            <div class="widgets_inner">
                                <ul class="list">
                                    <?php foreach ($categories as $category): ?>
                                    <li>
                                        <a href="#" class="category-filter <?= ($filters['category_id'] == $category['id']) ? 'active' : '' ?>" 
                                           data-category="<?= $category['id'] ?>">
                                            <?= esc($category['name']) ?>
                                        </a>
                                        <span>(<?= $category['product_count'] ?? 0 ?>)</span>
                                    </li>
                                    <?php if (!empty($category['children'])): ?>
                                        <?php foreach ($category['children'] as $child): ?>
                                        <li style="margin-left: 20px;">
                                            <a href="#" class="category-filter <?= ($filters['category_id'] == $child['id']) ? 'active' : '' ?>" 
                                               data-category="<?= $child['id'] ?>">
                                                - <?= esc($child['name']) ?>
                                            </a>
                                            <span>(<?= $child['product_count'] ?? 0 ?>)</span>
                                        </li>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                            <div class="filter-actions">
                                <button class="btn btn-sm btn-outline-secondary clear-filters">Xóa bộ lọc danh mục</button>
                            </div>
                        </aside>

                        <aside class="left_widgets p_filter_widgets">
                            <div class="l_w_title">
                                <h3>Thương hiệu</h3>
                            </div>
                            <div class="widgets_inner">
                                <ul class="list">
                                    <?php foreach ($brands as $brand): ?>
                                    <li>
                                        <a href="#" class="brand-filter <?= ($filters['brand_id'] == $brand['id']) ? 'active' : '' ?>" 
                                           data-brand="<?= $brand['id'] ?>">
                                            <?= esc($brand['name']) ?>
                                        </a>
                                    </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                            <div class="filter-actions">
                                <button class="btn btn-sm btn-outline-secondary clear-filters">Xóa bộ lọc thương hiệu</button>
                            </div>
                        </aside>

                        <aside class="left_widgets p_filter_widgets price_rangs_aside">
                            <div class="l_w_title">
                                <h3>Giá</h3>
                            </div>
                            <div class="widgets_inner">
                                <div class="range_item">
                                    <input type="text" class="js-range-slider" value="" />
                                    <div class="d-flex">
                                        <div class="price_text">
                                            <p>Giá :</p>
                                        </div>
                                        <div class="price_value d-flex justify-content-center">
                                            <input type="text" class="js-input-from" id="min_price" readonly />
                                            <span>đến</span>
                                            <input type="text" class="js-input-to" id="max_price" readonly />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="filter-actions">
                                <button class="btn btn-sm btn-outline-secondary clear-filters">Xóa bộ lọc giá</button>
                            </div>
                        </aside>
                    </div>
                </div>
                <div class="col-lg-9">
                   <div class="row">
                        <div class="col-lg-12">
                            <div class="product_top_bar d-flex justify-content-between align-items-center">
                                <div class="single_product_menu">
                                    <p><span id="product-count"><?= number_format($totalProducts) ?></span> Sản phẩm</p>
                                </div>
                                <div class="single_product_menu d-flex">
                                    <h5>Sắp xếp theo : </h5>
                                    <div class="left_dorp">
                                        <div class="nice-select sorting" tabindex="0">
                                            <span class="current" id="sort-current">
                                                <?= $filters['sort'] == 'price_asc' ? 'Giá: Thấp đến Cao' : 
                                                   ($filters['sort'] == 'price_desc' ? 'Giá: Cao đến Thấp' : 'Tên') ?>
                                            </span>
                                            <ul class="list">
                                                <li data-value="name" class="option <?= $filters['sort'] == 'name' ? 'selected' : '' ?>">Tên</li>
                                                <li data-value="price_asc" class="option <?= $filters['sort'] == 'price_asc' ? 'selected' : '' ?>">Giá: Thấp đến Cao</li>
                                                <li data-value="price_desc" class="option <?= $filters['sort'] == 'price_desc' ? 'selected' : '' ?>">Giá: Cao đến Thấp</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="single_product_menu d-flex">
                                    <h5>Hiển thị :</h5>
                                    <div class="left_dorp">
                                        <div class="nice-select show" tabindex="0">
                                            <span class="current" id="show-current"><?= $perPage ?></span>
                                            <ul class="list">
                                                <li data-value="9" class="option <?= $perPage == 9 ? 'selected' : '' ?>">9</li>
                                                <li data-value="18" class="option <?= $perPage == 18 ? 'selected' : '' ?>">18</li>
                                                <li data-value="36" class="option <?= $perPage == 36 ? 'selected' : '' ?>">36</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="single_product_menu d-flex">
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="search-input" placeholder="Tìm kiếm sản phẩm..." 
                                               value="<?= esc($filters['search']) ?>"
                                               aria-describedby="inputGroupPrepend">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="inputGroupPrepend">
                                                <i class="ti-search"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="products-container">
                        <div class="row align-items-stretch latest_product_inner"> <!-- Thay align-items-center bằng align-items-stretch để chiều cao đồng đều -->
                            <?php foreach ($products as $product): ?>
                            <div class="col-lg-4 col-sm-6">
                                <div class="single_product_item product-item <?= $product['stock_status'] == 'out_of_stock' ? 'out-of-stock' : '' ?>" 
                                     data-product-id="<?= $product['id'] ?>">
                                    <img src="<?= base_url($product['main_image'] ?? 'aranoz-master/img/product/product_1.png') ?>" alt="<?= esc($product['name']) ?>">
                                    <div class="single_product_text">
                                        <h4>
    <a href="<?= route_to('product_detail', $product['slug']) ?>">
        <?= esc($product['name']) ?>
    </a>
</h4>

                                        <div class="price-section">
                                            <?php if ($product['sale_price'] && $product['sale_price'] < $product['price']): ?>
                                                <h3 class="sale-price"><?= number_format($product['sale_price']) ?>₫</h3>
                                                <span class="original-price"><?= number_format($product['price']) ?>₫</span>
                                            <?php else: ?>
                                                <h3><?= number_format($product['price']) ?>₫</h3>
                                            <?php endif; ?>
                                        </div>
                                        <div class="product-actions">
                                            <a href="#" class="add_cart add-to-cart-btn" data-product-id="<?= $product['id'] ?>">
                                                <?= $product['stock_status'] == 'out_of_stock' ? 'Hết hàng' : '+ Thêm giỏ hàng' ?>
                                            </a>
                                            <button class="wishlist-btn" data-product-id="<?= $product['id'] ?>" title="Add to Wishlist">
                                                <i class="ti-heart"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <?php if ($product['stock_status'] == 'low_stock'): ?>
                                        <div class="stock-indicator">Only <?= $product['stock_quantity'] ?> left!</div>
                                    <?php endif; ?>
                                    <?php if ($product['sale_price'] && $product['sale_price'] < $product['price']): ?>
                                        <div class="sale-badge">-<?= round((1 - $product['sale_price'] / $product['price']) * 100) ?>%</div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- Pagination -->
                        <div class="row">
                            <div class="col-lg-12">
                                <div id="pagination-container">
                                    <?php if ($totalPages > 1): ?>
                                    <div class="pageination">
                                        <nav aria-label="Page navigation example">
                                            <ul class="pagination justify-content-center">
                                                <?php if ($currentPage > 1): ?>
                                                <li class="page-item">
                                                    <a class="page-link" href="#" data-page="<?= $currentPage - 1 ?>" aria-label="Previous">
                                                        <i class="ti-angle-double-left"></i>
                                                    </a>
                                                </li>
                                                <?php endif; ?>
                                                
                                                <?php 
                                                $startPage = max(1, $currentPage - 2);
                                                $endPage = min($totalPages, $startPage + 4);
                                                if ($endPage - $startPage < 4) {
                                                    $startPage = max(1, $endPage - 4);
                                                }
                                                for ($i = $startPage; $i <= $endPage; $i++): 
                                                    $activeClass = $i == $currentPage ? 'active' : '';
                                                ?>
                                                <li class="page-item <?= $activeClass ?>">
                                                    <a class="page-link" href="#" data-page="<?= $i ?>"><?= $i ?></a>
                                                </li>
                                                <?php endfor; ?>
                                                
                                                <?php if ($currentPage < $totalPages): ?>
                                                <li class="page-item">
                                                    <a class="page-link" href="#" data-page="<?= $currentPage + 1 ?>" aria-label="Next">
                                                        <i class="ti-angle-double-right"></i>
                                                    </a>
                                                </li>
                                                <?php endif; ?>
                                            </ul>
                                        </nav>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Loading overlay -->
                    <div id="loading-overlay" style="display: none;">
                        <div class="text-center py-5">
                            <i class="fa fa-spinner fa-spin fa-3x"></i>
                            <p>Loading products...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--================End Category Product Area =================-->

    <!-- Best Sellers Section -->
    <!-- Best Sellers -->
<?= render_best_sellers([
    'limit' => 8,
    'title' => 'Sản phẩm bán chạy',
    'subtitle' => 'Cửa hàng',
    'type' => 'best_sellers'
]) ?>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
    <script src="<?= base_url('aranoz-master/js/stellar.js'); ?>"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ion-rangeslider/2.3.1/js/ion.rangeSlider.min.js"></script>
    
    <script>
$(document).ready(function() {
    let currentFilters = {
        category_id: <?= $filters['category_id'] ? $filters['category_id'] : 'null' ?>,
        brand_id: <?= $filters['brand_id'] ? $filters['brand_id'] : 'null' ?>,
        min_price: <?= $filters['min_price'] ? $filters['min_price'] : 'null' ?>,
        max_price: <?= $filters['max_price'] ? $filters['max_price'] : 'null' ?>,
        sort_by: '<?= $filters['sort'] ?? 'name' ?>',
        per_page: <?= $perPage ?>,
        page: <?= $currentPage ?? 1 ?>,
        search: '<?= $filters['search'] ?? '' ?>'
    };

    let wishlistItems = [];
    let isWishlistLoaded = false;

    // Initialize price range slider
    function initPriceRangeSlider() {
        let minPrice = <?= $filters['min_price'] ?? $minPrice ?? 0 ?>;
        let maxPrice = <?= $filters['max_price'] ?? $maxPrice ?? 1000000 ?>;
        
        $(".js-range-slider").ionRangeSlider({
            type: "double",
            min: 0,
            max: <?= $maxPrice ?? 1000000 ?>,
            from: minPrice,
            to: maxPrice,
            grid: true,
            prefix: "₫",
            onFinish: function(data) {
                $('#min_price').val(data.from);
                $('#max_price').val(data.to);
                currentFilters.min_price = data.from;
                currentFilters.max_price = data.to;
                currentFilters.page = 1;
                loadProducts();
            }
        });
    }

    // Load wishlist status on page load - FIX: Đợi load xong mới update UI
    function loadWishlistStatus() {
        return $.ajax({
            url: '<?= route_to('api_wishlist_status') ?>',
            type: 'GET',
            success: function(response) {
                if (response.wishlist) {
                    wishlistItems = response.wishlist.map(id => parseInt(id));
                    isWishlistLoaded = true;
                    updateWishlistButtons();
                }
            },
            error: function() {
                console.log('Failed to load wishlist status');
                isWishlistLoaded = true;
            }
        });
    }

    // Initialize - FIX: Đợi wishlist load xong
    loadWishlistStatus().then(function() {
        initPriceRangeSlider();
        initializeFromURL();
    });

    // Category filter click
    $('.category-filter').click(function(e) {
        e.preventDefault();
        let categoryId = $(this).data('category');
        
        if (currentFilters.category_id == categoryId) {
            currentFilters.category_id = null;
            $(this).removeClass('active');
        } else {
            $('.category-filter').removeClass('active');
            $(this).addClass('active');
            currentFilters.category_id = categoryId;
        }
        
        currentFilters.page = 1;
        loadProducts();
    });

    // Brand filter click
    $('.brand-filter').click(function(e) {
        e.preventDefault();
        let brandId = $(this).data('brand');
        
        if (currentFilters.brand_id == brandId) {
            currentFilters.brand_id = null;
            $(this).removeClass('active');
        } else {
            $('.brand-filter').removeClass('active');
            $(this).addClass('active');
            currentFilters.brand_id = brandId;
        }
        
        currentFilters.page = 1;
        loadProducts();
    });

    // Sort change
    $(document).on('click', '.sorting .option', function() {
        currentFilters.sort_by = $(this).data('value');
        $('.sorting .current').text($(this).text());
        currentFilters.page = 1;
        loadProducts();
    });

    // Per page change
    $(document).on('click', '.show .option', function() {
        currentFilters.per_page = $(this).data('value');
        $('.show .current').text($(this).text());
        currentFilters.page = 1;
        loadProducts();
    });

    // Search functionality
    let searchTimeout;
    $('#search-input').on('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
            currentFilters.search = $('#search-input').val();
            currentFilters.page = 1;
            loadProducts();
        }, 500);
    });

    // Clear filters
    $(document).on('click', '.clear-filters', function() {
        let filterType = $(this).closest('aside').find('.l_w_title h3').text();
        
        if (filterType.includes('Category')) {
            currentFilters.category_id = null;
            $('.category-filter').removeClass('active');
        } else if (filterType.includes('Brand')) {
            currentFilters.brand_id = null;
            $('.brand-filter').removeClass('active');
        } else if (filterType.includes('Price')) {
            currentFilters.min_price = null;
            currentFilters.max_price = null;
            
            let slider = $(".js-range-slider").data("ionRangeSlider");
            slider.update({
                from: 0,
                to: <?= $maxPrice ?? 1000000 ?>
            });
            
            $('#min_price').val('0');
            $('#max_price').val('<?= $maxPrice ?? 1000000 ?>');
        }
        
        currentFilters.page = 1;
        loadProducts();
    });

    // Add to cart functionality
    $(document).on('click', '.add-to-cart-btn', function(e) {
        e.preventDefault();
        let productId = $(this).data('product-id');
        let $btn = $(this);
        
        if ($btn.hasClass('loading') || $btn.closest('.out-of-stock').length) return;
        
        $btn.addClass('loading').text('Adding...');
        
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
            },
            complete: function() {
                $btn.removeClass('loading').text('+ Thêm giỏ hàng');
            }
        });
    });

    // Wishlist functionality
    $(document).on('click', '.wishlist-btn', function(e) {
        e.preventDefault();
        let productId = parseInt($(this).data('product-id'));
        let $btn = $(this);
        
        if ($btn.hasClass('loading')) return;
        
        $btn.addClass('loading');
        
        $.ajax({
            url: '<?= route_to('api_wishlist_add') ?>',
            type: 'POST',
            data: {
                product_id: productId,
                <?= csrf_token() ?>: '<?= csrf_hash() ?>'
            },
            success: function(response) {
                if (response.success) {
                    if (response.action === 'added') {
                        $btn.addClass('active');
                        wishlistItems.push(productId);
                        showToast('success', response.message);
                    } else {
                        $btn.removeClass('active');
                        wishlistItems = wishlistItems.filter(id => id !== productId);
                        showToast('info', response.message);
                    }
                    updateWishlistCount();
                } else {
                    showToast('error', response.message);
                }
            },
            error: function() {
                showToast('error', 'Có lỗi xảy ra, vui lòng thử lại');
            },
            complete: function() {
                $btn.removeClass('loading');
            }
        });
    });

    // Pagination click handler
    $(document).on('click', '.pagination a', function(e) {
        e.preventDefault();
        let page = $(this).data('page');
        if (page) {
            currentFilters.page = parseInt(page);
            loadProducts();
        }
    });

    // Load products via AJAX
    function loadProducts() {
        $('#loading-overlay').show();
        $('#products-container').addClass('loading');

        $.ajax({
            url: '<?= route_to('api_products_filter') ?>',
            type: 'POST',
            data: {
                ...currentFilters,
                <?= csrf_token() ?>: '<?= csrf_hash() ?>'
            },
            success: function(response) {
                if (response.success) {
                    updateProductCount(response.total);

                    let currentPage = parseInt(response.page);
                    let totalPages = parseInt(response.total_pages);

                    // Build products HTML
                    let productsHtml = '<div class="row align-items-stretch latest_product_inner">';
                    
                    if (response.products && response.products.length > 0) {
                        response.products.forEach(function(product) {
                            let outOfStockClass = product.stock_status === 'out_of_stock' ? 'out-of-stock' : '';
                            let addToCartText = product.stock_status === 'out_of_stock' ? 'Hết hàng' : '+ Thêm giỏ hàng';
                            let stockIndicator = '';
                            
                            if (product.stock_status === 'low_stock') {
                                stockIndicator = `<div class="stock-indicator">Only ${product.stock_quantity} left!</div>`;
                            }

                            // FIX: Kiểm tra sale_price > 0 VÀ < price
                            let saleBadge = '';
                            let priceSection = '';
                            
                            if (product.sale_price && product.sale_price > 0 && product.sale_price < product.price) {
                                let discountPercent = Math.round((1 - product.sale_price / product.price) * 100);
                                saleBadge = `<div class="sale-badge">-${discountPercent}%</div>`;
                                priceSection = `
                                    <h3 class="sale-price">${formatCurrency(product.sale_price)}₫</h3>
                                    <span class="original-price">${formatCurrency(product.price)}₫</span>
                                `;
                            } else {
                                priceSection = `<h3>${formatCurrency(product.price)}₫</h3>`;
                            }

                            // FIX: Thêm link vào product detail
                            let productUrl = `<?= site_url('single-product/') ?>${product.slug || product.id}`;

                            productsHtml += `
                                <div class="col-lg-4 col-sm-6">
                                    <div class="single_product_item product-item ${outOfStockClass}" data-product-id="${product.id}">
                                        <img src="${product.main_image || '<?= base_url('aranoz-master/img/product/product_1.png') ?>'}" 
                                             alt="${escapeHtml(product.name)}">
                                        <div class="single_product_text">
                                            <h4>
                                                <a href="${productUrl}">${escapeHtml(product.name)}</a>
                                            </h4>
                                            <div class="price-section">${priceSection}</div>
                                            <div class="product-actions">
                                                <a href="#" class="add_cart add-to-cart-btn" data-product-id="${product.id}">
                                                    ${addToCartText}
                                                </a>
                                                <button class="wishlist-btn" data-product-id="${product.id}" title="Add to Wishlist">
                                                    <i class="ti-heart"></i>
                                                </button>
                                            </div>
                                        </div>
                                        ${stockIndicator}
                                        ${saleBadge}
                                    </div>
                                </div>
                            `;
                        });
                    } else {
                        productsHtml += `
                            <div class="col-12">
                                <div class="text-center py-5">
                                    <h4>Không tìm thấy sản phẩm nào</h4>
                                    <p>Hãy thử thay đổi bộ lọc hoặc từ khóa tìm kiếm</p>
                                </div>
                            </div>
                        `;
                    }
                    
                    productsHtml += '</div>';

                    // Build pagination HTML
                    let paginationHtml = '';
                    if (totalPages > 1) {
                        paginationHtml = `
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="pageination">
                                        <nav aria-label="Page navigation example">
                                            <ul class="pagination justify-content-center">
                        `;
                        
                        if (currentPage > 1) {
                            paginationHtml += `<li class="page-item"><a class="page-link" href="#" data-page="${currentPage - 1}" aria-label="Previous"><i class="ti-angle-double-left"></i></a></li>`;
                        }
                        
                        let startPage = Math.max(1, currentPage - 2);
                        let endPage = Math.min(totalPages, startPage + 4);
                        
                        if (endPage - startPage < 4) {
                            startPage = Math.max(1, endPage - 4);
                        }
                        
                        for (let i = startPage; i <= endPage; i++) {
                            let activeClass = i === currentPage ? 'active' : '';
                            paginationHtml += `<li class="page-item ${activeClass}"><a class="page-link" href="#" data-page="${i}">${i}</a></li>`;
                        }
                        
                        if (currentPage < totalPages) {
                            paginationHtml += `<li class="page-item"><a class="page-link" href="#" data-page="${currentPage + 1}" aria-label="Next"><i class="ti-angle-double-right"></i></a></li>`;
                        }
                        
                        paginationHtml += `
                                            </ul>
                                        </nav>
                                    </div>
                                </div>
                            </div>
                        `;
                    }

                    // Update entire container
                    $('#products-container').html(productsHtml + paginationHtml);

                    updateWishlistButtons();
                } else {
                    showToast('error', 'Không thể tải sản phẩm');
                }
            },
            error: function() {
                showToast('error', 'Có lỗi xảy ra khi tải sản phẩm');
            },
            complete: function() {
                $('#loading-overlay').hide();
                $('#products-container').removeClass('loading');
            }
        });
    }

    // FIX: Đảm bảo wishlist buttons được update đúng
    function updateWishlistButtons() {
        if (!isWishlistLoaded) return;
        
        $('.wishlist-btn').each(function() {
            let productId = parseInt($(this).data('product-id'));
            if (wishlistItems.includes(productId)) {
                $(this).addClass('active');
            } else {
                $(this).removeClass('active');
            }
        });
    }

    function updateWishlistCount() {
        $('.wishlist-count').text(wishlistItems.length);
    }

    function updateCartCount(count) {
        $('.cart-count').text(count);
    }

    function updateProductCount(total) {
        $('#product-count').text(formatNumber(total));
    }

    function showToast(type, message) {
        let toastClass = type === 'success' ? 'alert-success' : 
                       type === 'error' ? 'alert-danger' : 'alert-info';
        
        let toast = `
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

    function formatCurrency(amount) {
        return new Intl.NumberFormat('vi-VN').format(amount);
    }

    function formatNumber(num) {
        return new Intl.NumberFormat('vi-VN').format(num);
    }

    function escapeHtml(text) {
        let map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.replace(/[&<>"']/g, function(m) { return map[m]; });
    }

    function initializeFromURL() {
        let urlParams = new URLSearchParams(window.location.search);
        
        if (urlParams.get('category')) {
            currentFilters.category_id = parseInt(urlParams.get('category'));
            $(`.category-filter[data-category="${currentFilters.category_id}"]`).addClass('active');
        }
        
        if (urlParams.get('brand')) {
            currentFilters.brand_id = parseInt(urlParams.get('brand'));
            $(`.brand-filter[data-brand="${currentFilters.brand_id}"]`).addClass('active');
        }
        
        if (urlParams.get('search')) {
            currentFilters.search = urlParams.get('search');
            $('#search-input').val(currentFilters.search);
        }
        
        if (urlParams.get('sort')) {
            currentFilters.sort_by = urlParams.get('sort');
            let sortText = currentFilters.sort_by === 'price_asc' ? 'Price: Low to High' : 
                          currentFilters.sort_by === 'price_desc' ? 'Price: High to Low' : 'Name';
            $('.sorting .current').text(sortText);
        }
        
        if (urlParams.get('per_page')) {
            currentFilters.per_page = parseInt(urlParams.get('per_page'));
            $('.show .current').text(currentFilters.per_page);
        }

        if (urlParams.get('page')) {
            currentFilters.page = parseInt(urlParams.get('page'));
        }
    }
});
    </script>
<?= $this->endSection() ?>