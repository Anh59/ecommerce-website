<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index(): string
    {
        return view('welcome_message');
    }
    public function about(): string
    {
        return view('Customers/index');
    }
    public function layout(): string{
        return view('Customers/layout/main');
    }
    public function login(): string{
        return view('Customers/login');
    }
    public function blog(): string
    {
        return view('Customers/blog');
    }
    public function contact(): string{
        return view('Customers/contact');
    }
    public function single_blog(): string
    {
        return view('Customers/single-blog');
    
    }
    public function single_product(): string
    {
        return view('Customers/single-product');
    }

    public function cart(): string
    {
        return view('Customers/cart');
    }
    public function checkout(): string
    {
        return view('Customers/checkout');
    }
    public function category(): string
    {
        return view('Customers/category');
    }
    public function tracking(): string
    {
        return view('Customers/tracking');
    }
    public function confirmation(): string
    {
        return view('Customers/confirmation');
    }
    public function elements(): string{
        return view('Customers/elements');
    }
    public function feature(): string
    {
        return view('Customers/feature');
    }
    public function Dashboard(): string
    {
        return view('Dashboard/layout');
    }
}