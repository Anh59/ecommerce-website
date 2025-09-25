<?php

use CodeIgniter\Router\RouteCollection;
use App\Controllers\CustomersController;
use App\Controllers\ProfileController;
/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('/about', 'Home::about',['as'=>'home_about']);// trang chủ
$routes->get('/layout', 'Home::layout');
// $routes->get('/login', 'Home::login');
// $routes->get('/blog', 'Home::blog',['as'=>'home_blog']);
$routes->get('/contact', 'Home::contact',['as'=>'home_contact']);
// $routes->get('/single-blog', 'Home::single_blog',['as'=>'home_single_blog']);
// $routes->get('/single-product', 'Home::single_product',['as'=>'home_single_product']);
// $routes->get('/cart', 'Home::cart',['as'=>'home_cart']);
// $routes->get('/checkout', 'Home::checkout',['as'=>'home_checkout']);
// $routes->get('/category', 'Home::category',['as'=>'home_category']);
$routes->get('/tracking', 'Home::tracking',['as'=>'home_tracking']);
$routes->get('/confirmation', 'Home::confirmation',['as'=>'home_confirmation']);
$routes->get('/elements', 'Home::elements',['as'=>'home_elements']);
$routes->get('/feature', 'Home::feature',['as'=>'home_feature']);
// $routes->get('/cart', 'CartController::index',['as'=>'home_cart']);

//bài viết 
$routes->get('blog', 'BlogController::index',['as'=>'blog']);
$routes->get('blog/post/(:segment)', 'BlogController::single/$1');
$routes->get('blog/category/(:segment)', 'BlogController::category/$1');
$routes->post('blog/add-comment', 'BlogController::addComment');
// Thêm các routes sau vào file app/Config/Routes.php
// Single product AJAX (trả JSON)
$routes->get('/single-product/(:segment)', 'SingleProductController::detail/$1', ['as' => 'product_detail']);
$routes->post('api/single-product/wishlist', 'SingleProductController::toggleWishlist', ['as' => 'api_product_wishlist']);
$routes->post('api/single-product/comment', 'SingleProductController::addComment', ['as' => 'api_product_comment']);

$routes->post('api/single-product/buy-now', 'SingleProductController::buyNow', ['as' => 'api_buy_now']);
//
$routes->get('checkout', 'CheckoutController::index');
$routes->post('checkout/process', 'CheckoutController::processOrder', ['as' => 'checkout_process']);
$routes->get('checkout/success/(:segment)', 'CheckoutController::orderSuccess/$1');

$routes->post('apply-coupon', 'CouponController::applyCoupon', ['as' => 'apply_coupon']);
$routes->post('remove-coupon', 'CouponController::removeCoupon', ['as' => 'remove_coupon']);

$routes->get('checkout/momo-callback', 'CheckoutController::momoCallback');
$routes->post('checkout/momo-ipn', 'CheckoutController::momoIPN');
$routes->get('checkout/momo-status/(:num)', 'CheckoutController::checkMomoStatus/$1');
// Category và Product routes
$routes->get('/category', 'TableCategoryController::index', ['as' => 'category']);
$routes->post('api/products/filter', 'TableCategoryController::getProducts', ['as' => 'api_products_filter']);

// Wishlist API routes  
$routes->post('api/wishlist/add', 'TableCategoryController::addToWishlist', ['as' => 'api_wishlist_add']);
$routes->get('api/wishlist/status', 'TableCategoryController::getWishlistStatus', ['as' => 'api_wishlist_status']);
$routes->post('api/wishlist/remove', 'WishlistController::remove', ['as' => 'api_wishlist_remove']);
$routes->post('api/wishlist/move-to-cart', 'WishlistController::moveToCart', ['as' => 'api_wishlist_move_to_cart']);
$routes->post('api/wishlist/clear', 'WishlistController::clear', ['as' => 'api_wishlist_clear']);
$routes->post('api/wishlist/add-multiple', 'WishlistController::addMultiple', ['as' => 'api_wishlist_add_multiple']);
$routes->get('api/wishlist/data', 'WishlistController::getWishlistData', ['as' => 'api_wishlist_data']);

// Wishlist page route
$routes->get('/wishlist', 'WishlistController::index', ['as' => 'wishlist']);

// Legacy wishlist routes for compatibility
$routes->post('wishlist/add', 'WishlistController::add', ['as' => 'wishlist_add']);
$routes->post('wishlist/remove', 'WishlistController::remove', ['as' => 'wishlist_remove']);

// Cart page and API routes  
// Cart page and API routes  
$routes->get('/cart', 'CartController::index', ['as' => 'cart']);
$routes->post('api/cart/add', 'TableCategoryController::addToCart', ['as' => 'api_cart_add']);
$routes->get('api/cart/count', 'CartController::getCartCount', ['as' => 'api_cart_count']);
$routes->post('api/cart/update', 'CartController::updateQuantity', ['as' => 'api_cart_update']);
$routes->post('/cart/update', 'CartController::update', ['as' => 'cart_update']); // For form submission
$routes->post('api/cart/remove', 'CartController::remove', ['as' => 'api_cart_remove']);
$routes->post('api/cart/clear', 'CartController::clear', ['as' => 'api_cart_clear']);
$routes->get('api/cart/validate', 'CartController::validateCart', ['as' => 'api_cart_validate']);
$routes->get('api/cart/data', 'CartController::getCartData', ['as' => 'api_cart_data']);
$routes->get('api/cart/summary', 'CartController::getCartSummary', ['as' => 'api_cart_summary']);
$routes->get('api/cart/widget', 'CartController::getCartWidget', ['as' => 'api_cart_widget']);
$routes->get('/cart/checkout', 'CartController::checkout', ['as' => 'api_cart_checkout']);
$routes->post('api/cart/update-quantity', 'CartController::updateQuantity', ['as' => 'api_cart_update_quantity']);

// NEW: Selected items for checkout routes
$routes->post('api/cart/set-checkout-items', 'CartController::setCheckoutItems', ['as' => 'api_cart_set_checkout_items']);
$routes->get('api/cart/get-checkout-items', 'CartController::getCheckoutItems', ['as' => 'api_cart_get_checkout_items']);

$routes->post('api/cart/clear-expired-buynow', 'CheckoutController::clearExpiredBuyNow', ['as' => 'api_clear_expired_buynow']);
// Trong app/Config/Routes.php
$routes->post('api/cart/update-quantity', 'CartController::updateQuantity', ['as' => 'api_cart_update_quantity']);
// Category filter routes (có thể dùng để SEO friendly URLs)
$routes->get('/category/(:segment)', 'TableCategoryController::index/$1', ['as' => 'category_slug']);
$routes->get('/category/(:segment)/page/(:num)', 'TableCategoryController::index/$1/$2', ['as' => 'category_page']);

// Brand filter routes
$routes->get('/brand/(:segment)', 'TableCategoryController::index', ['as' => 'brand_slug']);

// Search routes
$routes->get('/search', 'TableCategoryController::index', ['as' => 'search']);
$routes->post('api/search/suggestions', 'SearchController::getSuggestions', ['as' => 'api_search_suggestions']);

// Filter routes với parameters
$routes->get('/products', 'TableCategoryController::index', ['as' => 'products']);
$routes->get('/products/category/(:num)', 'TableCategoryController::index', ['as' => 'products_by_category']);

// Routes cho Wishlist
// $routes->get('wishlist', 'WishlistController::index', ['as' => 'wishlist']);
// $routes->post('wishlist/add', 'WishlistController::add', ['as' => 'wishlist_add']);
// $routes->post('wishlist/remove', 'WishlistController::remove', ['as' => 'wishlist_remove']);
// Xây các trang website cho khách hàng
// Xử lý phần API cho khách hàng
$routes->group('api_Customers',function($routes) {
   

    $routes->get('customers_register','CustomersController::register', ['as' => 'Customers_Register']);
    $routes->post('customers_register','CustomersController::processRegistration', ['as' => 'Customers_processRegistration']);
    $routes->post('customers_verify_otp', 'CustomersController::verifyOTP', ['as' => 'Customers_verifyOTP']);

    $routes->get('customers_sign','CustomersController::login',['as' => 'Customers_sign']);// đăng nhập
    $routes->post('customers_sign','CustomersController::processLogin',['as' => 'Customers_processLogin']);

    $routes->get('customers_logout','CustomersController::logout',['as' => 'Customers_logout']);
    $routes->get('testEmail', 'CustomersController::testEmail', ['as' => 'testEmail']);
    

    $routes->get('customers_forgot_password', 'CustomersController::forgotPassword',['as' => 'customes_forgot_password']);
    $routes->post('customers_forgot_password', 'CustomersController::processForgotPassword',['as'=>'Customers_processForgotPassword']);
    $routes->post('customers_pass_verify_otp', 'CustomersController::pass_verifyOTP',['as'=>'Customers_processPassVerifyOTP']);
    $routes->post('customers_reset_password', 'CustomersController::resetPassword',['as' => 'Customers_resetPassword']);

    $routes->get('google_login', 'GoogleController::googleLogin', ['as' => 'google_login']);
    $routes->get('google_callback', 'GoogleController::googleCallback', ['as' => 'google_callback']);
    
    $routes->get('profile', 'CustomersController::profile', ['as' => 'profile']);
    $routes->post('profile/update', 'CustomersController::updateProfile', ['as' => 'update_profile']);
    $routes->post('profile/change-password', 'CustomersController::changePassword', ['as' => 'change_password']);
    $routes->get('profile/order-detail/(:num)', 'CustomersController::orderDetail/$1');
    $routes->post('profile/cancel-order/(:num)', 'CustomersController::cancelOrder/$1',['as'=>'cancel_order']);

$routes->post('profile/submit-review', 'CustomersController::submitReview', ['as' => 'submit_review']);
    $routes->get('profile/view-review/(:num)/(:num)', 'CustomersController::viewReview/$1/$2', ['as' => 'view_review']);
    // $routes->group('Manager',  ['filter' => 'authCheck'],function($routes) {
    //     $routes->get('profile','Profilecontroller::profile', ['as' => 'profile']);
    //     $routes->get('personal', 'ProfileController::personal', ['as' => 'personal']);


    //     $routes->get('change_password','Profilecontroller::change_password', ['as' => 'change_password']);
    //     $routes->post('changePassword', 'ProfileController::changePassword', ['as' => 'changePassword']);

    //     $routes->get('changePersonalInfo', 'ProfileController::changePersonalInfo', ['as' => 'changePersonalInfo']);
        
    //     $routes->post('updatePersonalInfo', 'ProfileController::updatePersonalInfo',['as' => 'updatePersonalInfo']);
    //     $routes->get('verifyChangeEmailOTP', 'ProfileController::verifyChangeEmailOTP', ['as' => 'verifyChangeEmailOTP']);
    //     $routes->post('verifyChangeEmailOTP', 'ProfileController::handleVerifyChangeEmailOTP', ['as' => 'handleVerifyChangeEmailOTP']);



    //     $routes->get('order','ProfileController::order', ['as' => 'order']);
    //     $routes->get('detail_order/(:num)', 'ProfileController::detail_order/$1', ['as' => 'detail_order']);
    //     $routes->get('history_order','ProfileController::history_order', ['as' => 'history_order']);
    //     $routes->get('detail_history_order/(:num)', 'ProfileController::detail_history_order/$1', ['as' => 'detail_history_order']);
    //     $routes->get('history_order/delete/(:num)', 'ProfileController::delete_order/$1', ['as' => 'delete_order']);//xoá đơn hàng
    //     $routes->get('history_order/reorder/(:num)', 'ProfileController::reorder/$1', ['as' => 'reorder']);//đặt lại đơn hàng
    //     $routes->post('cancel/(:num)', 'ProfileController::cancelOrder/$1', ['as' => 'cancel_order']);//huỷ đơn hàng đã đặt
    //     $routes->get('reviews/(:num)', 'ProfileController::reviews/$1',['as'=>'reviews']); // Hiển thị trang đánh giá
    //     $routes->post('reviews/submit/(:num)', 'ProfileController::submitReview/$1',['as'=>'submitReview']); // Xử lý gửi đánh giá

    // });
  
});

// Xử lý phần Dashboard
$routes->get('/Dashboard', 'Home::Dashboard');
$routes->get('errors','Home::Errors');

$routes->get('adminlogin', 'UserController::loginForm',['as'=>'adminlogin']);
$routes->post('login', 'UserController::login');
$routes->get('adminregister', 'UserController::registerForm',['as' => 'adminregister']);
$routes->post('register', 'UserController::register');
$routes->get('logout', 'UserController::logout');

$routes->group('Dashboard', function (RouteCollection $routes) {// ['filter' => 'Perermissions'], đã bỏ filter dòng này nếu sau có lỗi check lại

    // login
    $routes->get('table', 'DashboardController::table', ['as' => 'Dashboard_table', 'filter' => 'Perermissions:Dashboard_table']);
$routes->get('getDashboardDataAjax', 'DashboardController::getDashboardDataAjax');
    // Group
    $routes->group('Group',  function (RouteCollection $routes) {
        $routes->get('table-group', 'GroupController::table', ['as' => 'Table_Group', 'filter' => 'Perermissions:Table_Group']);
        $routes->get('table-create', 'GroupController::create', ['as' => 'Table_Create', 'filter' => 'Perermissions:Table_Create']);
        $routes->post('table-store', 'GroupController::store', ['as' => 'Table_Store', 'filter' => 'Perermissions:Table_Store']);
        $routes->post('group-update/(:num)', 'GroupController::update/$1', ['as' => 'Group_update', 'filter' => 'Perermissions:Group_update']);
        $routes->get('group-edit/(:num)', 'GroupController::edit/$1', ['as' => 'Group_edit', 'filter' => 'Perermissions:Group_edit']);
        $routes->post('group-delete/(:num)', 'GroupController::delete/$1', ['as' => 'Group_delete', 'filter' => 'Perermissions:Group_delete']);
    });
    // Role
    $routes->group('Role', function (RouteCollection $routes) {  
        $routes->get('table-role', 'RoleController::table', ['as' => 'Table_Role', 'filter' => 'Perermissions:Table_Role']);
        $routes->get('table-role-create', 'RoleController::create', ['as' => 'Table_Role_Create', 'filter' => 'Perermissions:Table_Role_Create']);
        $routes->post('table-role-store', 'RoleController::store', ['as' => 'Table_Role_Store', 'filter' => 'Perermissions:Table_Role_Store']);
        $routes->get('table-role-edit/(:num)', 'RoleController::edit/$1', ['as' => 'Table_Role_Edit', 'filter' => 'Perermissions:Table_Role_Edit']);
        $routes->post('table-role-update/(:num)', 'RoleController::update/$1', ['as' => 'Table_Role_Update', 'filter' => 'Perermissions:Table_Role_Update']);
        $routes->post('table-role-delete/(:num)', 'RoleController::delete/$1', ['as' => 'Table_Role_Delete', 'filter' => 'Perermissions:Table_Role_Delete']);
    });

    // Group_Role
    $routes->group('Group_Role', function (RouteCollection $routes) {
        $routes->get('table-groupRole', 'GroupRoleController::table', ['as' => 'Table_GroupRole', 'filter' => 'Perermissions:Table_GroupRole']);
        $routes->get('table-groupRole-create', 'GroupRoleController::create', ['as' => 'Table_GroupRole_Create', 'filter' => 'Perermissions:Table_GroupRole_Create']);
        $routes->post('table-groupRole-store', 'GroupRoleController::store', ['as' => 'Table_GroupRole_Store', 'filter' => 'Perermissions:Table_GroupRole_Store']);
        $routes->get('table-groupRole-edit/(:num)', 'GroupRoleController::edit/$1', ['as' => 'Table_GroupRole_Edit', 'filter' => 'Perermissions:Table_GroupRole_Edit']);
        $routes->post('table-groupRole-update/(:num)', 'GroupRoleController::update/$1', ['as' => 'Table_GroupRole_Update', 'filter' => 'Perermissions:Table_GroupRole_Update']);
        $routes->post('table-groupRole-delete/(:num)', 'GroupRoleController::delete/$1', ['as' => 'Table_GroupRole_Delete', 'filter' => 'Perermissions:Table_GroupRole_Delete']);
    });

    // Permissions
    $routes->group('permissions', function (RouteCollection $routes) {
        $routes->get('table-permissions', 'PermissionsController::table', ['as' => 'Table_Permissions', 'filter' => 'Perermissions:Table_Permissions']);
        $routes->get('tableuser_list', 'PermissionsController::tableuser_list', ['as' => 'Table_User_List', 'filter' => 'Perermissions:Table_User_List']);
        $routes->get('fetch-table-updates', 'PermissionsController::fetchTableUpdates', ['as' => 'Fetch_Table_Updates']);

    });

    // User
    $routes->group('User', function (RouteCollection $routes) {
         $routes->get('table-user', 'TableUserController::tableuser', ['as' => 'Table_User', 'filter' => 'Perermissions:Table_User']);
         $routes->post('change_user_group', 'TableUserController::changeUserGroup', ['as' => 'change_user_group']);
         $routes->get('table-user-create', 'TableUserController::create', ['as' => 'Table_User_Create', 'filter' => 'Perermissions:Table_User_Create']);
         $routes->post('table-user-store', 'TableUserController::store', ['as' => 'Table_User_Store', 'filter' => 'Perermissions:Table_User_Store']);
         $routes->get('table-user-edit/(:num)', 'TableUserController::editUser/$1', ['as' => 'Table_User_Edit', 'filter' => 'Perermissions:Table_User_Edit']);
         $routes->post('table-user-update/(:num)', 'TableUserController::updateUser/$1', ['as' => 'Table_User_Update', 'filter' => 'Perermissions:Table_User_Update']);
         $routes->post('table-user-delete/(:num)', 'TableUserController::deleteUser/$1', ['as' => 'Table_User_Delete', 'filter' => 'Perermissions:Table_User_Delete']);
    });
    $routes->group('Customers', function (RouteCollection $routes) {
        $routes->get('table-customers', 'TableCustomersController::table', ['as' => 'Table_Customers', 'filter' => 'Perermissions:Table_Customers']);
        $routes->get('table-customers-create', 'TableCustomersController::create', ['as' => 'Table_Customers_Create', 'filter' => 'Perermissions:Table_Customers_Create']);
        $routes->post('table-customers-store', 'TableCustomersController::store', ['as' => 'Table_Customers_Store', 'filter' => 'Perermissions:Table_Customers_Store']);
        $routes->get('table-customers-edit/(:num)', 'TableCustomersController::edit/$1', ['as' => 'Table_Customers_Edit', 'filter' => 'Perermissions:Table_Customers_Edit']);
        $routes->post('table-customers-update/(:num)', 'TableCustomersController::update/$1', ['as' => 'Table_Customers_Update', 'filter' => 'Perermissions:Table_Customers_Update']);
        $routes->post('table-customers-delete/(:num)', 'TableCustomersController::delete/$1', ['as' => 'Table_Customers_Delete', 'filter' => 'Perermissions:Table_Customers_Delete']);
        //$routes->post('table-customers-lock/(:num)', 'TableCustomersController::lockCustomer/$1', ['as' => 'Table_Customers_Lock', 'filter' => 'Perermissions:Table_Customers_Lock']);
    });

    // Brand
    $routes->get('brands', 'BrandController::index', ['as' => 'Table_Brand', 'filter' => 'Perermissions:Table_Brand']);
    $routes->post('brands/store', 'BrandController::store', ['as' => 'Table_Brand_Store']);
    $routes->get('brands/(:num)/edit', 'BrandController::edit/$1', ['as' => 'Table_Brand_Edit', 'filter' => 'Perermissions:Table_Brand_Edit']);
    $routes->post('brands/(:num)/update', 'BrandController::update/$1', ['as' => 'Table_Brand_Update']);
    $routes->post('brands/(:num)/delete', 'BrandController::delete/$1', ['as' => 'Table_Brand_Delete']);
    $routes->get('brands/list', 'BrandController::list');


    // Category
    $routes->get('categories', 'CategoryController::index', ['as' => 'Table_categories', 'filter' => 'Perermissions:Table_categories']);
    $routes->get('categories/list', 'CategoryController::list');
    $routes->post('categories/store', 'CategoryController::store');
    $routes->get('categories/getParentCategories', 'CategoryController::getParentCategories');
    $routes->get('categories/(:num)/edit', 'CategoryController::edit/$1');
    $routes->post('categories/(:num)/update', 'CategoryController::update/$1');
    $routes->post('categories/(:num)/delete', 'CategoryController::delete/$1');

    // Blog Posts
    $routes->get('blog-posts', 'BlogPostController::index', ['as' => 'Table_blog_posts', 'filter' => 'Perermissions:Table_blog_posts']);
    $routes->get('blog-posts/list', 'BlogPostController::list');
    $routes->post('blog-posts/store', 'BlogPostController::store');
    $routes->get('blog-posts/(:num)/edit', 'BlogPostController::edit/$1');
    $routes->post('blog-posts/(:num)/update', 'BlogPostController::update/$1');
    $routes->post('blog-posts/(:num)/delete', 'BlogPostController::delete/$1');
    $routes->get('blog-posts/(:num)/view', 'BlogPostController::view/$1');
    $routes->post('blog-posts/(:num)/toggle-featured', 'BlogPostController::toggleFeatured/$1');
    $routes->post('blog-posts/(:num)/change-status', 'BlogPostController::changeStatus/$1');

    // Blog Comments
    $routes->get('blog-comments', 'BlogCommentController::index', ['as' => 'Table_blog_comments', 'filter' => 'Perermissions:Table_blog_comments']);
    $routes->get('blog-comments/list', 'BlogCommentController::list');
    $routes->post('blog-comments/store', 'BlogCommentController::store');
    $routes->post('blog-comments/(:num)/approve', 'BlogCommentController::approve/$1');
    $routes->post('blog-comments/(:num)/reject', 'BlogCommentController::reject/$1');
    $routes->post('blog-comments/(:num)/delete', 'BlogCommentController::delete/$1');
    $routes->get('blog-comments/pending', 'BlogCommentController::pending');
    $routes->post('blog-comments/(:num)/toggle-approve', 'BlogCommentController::toggleApprove/$1');
    // Products
        // $routes->get('products', 'ProductsController::index', ['as' => 'Table_products', 'filter' => 'Perermissions:Table_products']);
    $routes->get('products', 'ProductsController::index',['as' => 'Table_products', 'filter' => 'Perermissions:Table_products']);
    $routes->get('products/list', 'ProductsController::list');
    $routes->post('products/store', 'ProductsController::store');
    $routes->get('products/(:num)/edit', 'ProductsController::edit/$1');
    $routes->post('products/(:num)/update', 'ProductsController::update/$1');
    $routes->post('products/(:num)/delete', 'ProductsController::delete/$1');
    $routes->post('products/images/(:num)/delete', 'ProductsController::deleteImage/$1');
    
    $routes->get('discount-coupons', 'DiscountCouponController::index', ['as' => 'Table_discount_coupons', 'filter' => 'Perermissions:Table_discount_coupons']);
    $routes->get('discount-coupons/list', 'DiscountCouponController::list');
    $routes->post('discount-coupons/store', 'DiscountCouponController::store');
    $routes->get('discount-coupons/(:num)/edit', 'DiscountCouponController::edit/$1');
    $routes->post('discount-coupons/(:num)/update', 'DiscountCouponController::update/$1');
    $routes->post('discount-coupons/(:num)/delete', 'DiscountCouponController::delete/$1');
    $routes->post('discount-coupons/(:num)/toggle-active', 'DiscountCouponController::toggleActive/$1');
    $routes->post('discount-coupons/(:num)/reset-usage', 'DiscountCouponController::resetUsage/$1');
    $routes->post('discount-coupons/(:num)/duplicate', 'DiscountCouponController::duplicate/$1');
    $routes->post('discount-coupons/validate-code', 'DiscountCouponController::validateCode');
    $routes->post('discount-coupons/generate-code', 'DiscountCouponController::generateCode');
    $routes->get('discount-coupons/(:num)/usage-report', 'DiscountCouponController::usageReport/$1');
    // Alternative routes for consistency
    // $routes->match(['get', 'post'], 'products/create', 'ProductsController::store');
    // $routes->match(['get', 'post'], 'products/edit/(:num)', 'ProductsController::edit/$1');
    // $routes->match(['post', 'put'], 'products/update/(:num)', 'ProductsController::update/$1');
    // $routes->match(['post', 'delete'], 'products/delete/(:num)', 'ProductsController::delete/$1');

    // (Tương tự thêm routes cho posts, invoices, reviews...)
    $routes->get('orders', 'OrderController::index', ['as' => 'Table_orders', 'filter' => 'Perermissions:Table_orders']);
    $routes->get('orders/list', 'OrderController::list');
    $routes->get('orders/(:num)/details', 'OrderController::details/$1');
    $routes->post('orders/(:num)/update', 'OrderController::update/$1');
    $routes->get('orders/(:num)/print', 'OrderController::print/$1');
    $routes->get('orders/stats', 'OrderController::stats');
    $routes->get('orders/export', 'OrderController::export');



});