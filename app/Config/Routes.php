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
$routes->get('/login', 'Home::login');
$routes->get('/blog', 'Home::blog');
$routes->get('/contact', 'Home::contact');
$routes->get('/single-blog', 'Home::single_blog');
$routes->get('/single-product', 'Home::single_product');
$routes->get('/cart', 'Home::cart');
$routes->get('/checkout', 'Home::checkout');
$routes->get('/category', 'Home::category');
$routes->get('/tracking', 'Home::tracking');
$routes->get('/confirmation', 'Home::confirmation');
$routes->get('/elements', 'Home::elements');
$routes->get('/feature', 'Home::feature');



$routes->group('api_Customers',function($routes) {
   

    $routes->get('customers_register','CustomersController::register', ['as' => 'Customers_Register']);
    $routes->post('customers_register','CustomersController::processRegistration', ['as' => 'Customers_processRegistration']);
    $routes->post('customers_verify_otp', 'CustomersController::verifyOTP', ['as' => 'Customers_verifyOTP']);

    $routes->get('customers_sign','CustomersController::login',['as' => 'Customers_sign']);
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