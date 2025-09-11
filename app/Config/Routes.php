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
$routes->get('/blog', 'Home::blog',['as'=>'home_blog']);
$routes->get('/contact', 'Home::contact',['as'=>'home_contact']);
$routes->get('/single-blog', 'Home::single_blog',['as'=>'home_single_blog']);
$routes->get('/single-product', 'Home::single_product',['as'=>'home_single_product']);
$routes->get('/cart', 'Home::cart',['as'=>'home_cart']);
$routes->get('/checkout', 'Home::checkout',['as'=>'home_checkout']);
// $routes->get('/category', 'Home::category',['as'=>'home_category']);
$routes->get('/tracking', 'Home::tracking',['as'=>'home_tracking']);
$routes->get('/confirmation', 'Home::confirmation',['as'=>'home_confirmation']);
$routes->get('/elements', 'Home::elements',['as'=>'home_elements']);
$routes->get('/feature', 'Home::feature',['as'=>'home_feature']);


// Routes cho Wishlist
$routes->get('wishlist', 'WishlistController::index', ['as' => 'wishlist']);
$routes->post('wishlist/add', 'WishlistController::add', ['as' => 'wishlist_add']);
$routes->post('wishlist/remove', 'WishlistController::remove', ['as' => 'wishlist_remove']);
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
    
    $routes->group('Manager',  ['filter' => 'authCheck'],function($routes) {
        $routes->get('profile','Profilecontroller::profile', ['as' => 'profile']);
        $routes->get('personal', 'ProfileController::personal', ['as' => 'personal']);


        $routes->get('change_password','Profilecontroller::change_password', ['as' => 'change_password']);
        $routes->post('changePassword', 'ProfileController::changePassword', ['as' => 'changePassword']);

        $routes->get('changePersonalInfo', 'ProfileController::changePersonalInfo', ['as' => 'changePersonalInfo']);
        
        $routes->post('updatePersonalInfo', 'ProfileController::updatePersonalInfo',['as' => 'updatePersonalInfo']);
        $routes->get('verifyChangeEmailOTP', 'ProfileController::verifyChangeEmailOTP', ['as' => 'verifyChangeEmailOTP']);
        $routes->post('verifyChangeEmailOTP', 'ProfileController::handleVerifyChangeEmailOTP', ['as' => 'handleVerifyChangeEmailOTP']);



        $routes->get('order','ProfileController::order', ['as' => 'order']);
        $routes->get('detail_order/(:num)', 'ProfileController::detail_order/$1', ['as' => 'detail_order']);
        $routes->get('history_order','ProfileController::history_order', ['as' => 'history_order']);
        $routes->get('detail_history_order/(:num)', 'ProfileController::detail_history_order/$1', ['as' => 'detail_history_order']);
        $routes->get('history_order/delete/(:num)', 'ProfileController::delete_order/$1', ['as' => 'delete_order']);//xoá đơn hàng
        $routes->get('history_order/reorder/(:num)', 'ProfileController::reorder/$1', ['as' => 'reorder']);//đặt lại đơn hàng
        $routes->post('cancel/(:num)', 'ProfileController::cancelOrder/$1', ['as' => 'cancel_order']);//huỷ đơn hàng đã đặt
        $routes->get('reviews/(:num)', 'ProfileController::reviews/$1',['as'=>'reviews']); // Hiển thị trang đánh giá
        $routes->post('reviews/submit/(:num)', 'ProfileController::submitReview/$1',['as'=>'submitReview']); // Xử lý gửi đánh giá

    });
  
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




});