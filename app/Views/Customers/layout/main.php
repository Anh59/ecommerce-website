<?php
// filepath: app/Views/layout/main.php
?>
<!doctype html>
<html lang="zxx">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title><?= isset($title) ? $title : 'Aranoz' ?></title>
    <link rel="icon" href="<?= base_url('aranoz-master/img/favicon.png') ?>">
      <link rel="stylesheet" href="<?= base_url('aranoz-master/css/bootstrap.min.css') ?>">
    <!-- animate CSS -->
    <link rel="stylesheet" href="<?= base_url('aranoz-master/css/animate.css') ?>">
    <!-- owl carousel CSS -->
    <link rel="stylesheet" href="<?= base_url('aranoz-master/css/owl.carousel.min.css') ?>">
    <!-- font awesome CSS -->
    <link rel="stylesheet" href="<?= base_url('aranoz-master/css/all.css') ?>">
    <!-- flaticon CSS -->
    <link rel="stylesheet" href="<?= base_url('aranoz-master/css/flaticon.css') ?>">
    <link rel="stylesheet" href="<?= base_url('aranoz-master/css/themify-icons.css') ?>">
    <!-- font awesome CSS -->
    <link rel="stylesheet" href="<?= base_url('aranoz-master/css/magnific-popup.css') ?>">
    <!-- swiper CSS -->
    <link rel="stylesheet" href="<?= base_url('aranoz-master/css/slick.css') ?>">
    <!-- style CSS -->
    <link rel="stylesheet" href="<?= base_url('aranoz-master/css/style.css') ?>">
</head>
<body>
    <header class="main_menu home_menu">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-12">
                    <nav class="navbar navbar-expand-lg navbar-light">
                        <a class="navbar-brand" href="index.html"> <img src="<?= base_url('aranoz-master/img/logo.png'); ?>" alt="logo"> </a>
                        <button class="navbar-toggler" type="button" data-toggle="collapse"
                            data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                            aria-expanded="false" aria-label="Toggle navigation">
                            <span class="menu_icon"><i class="fas fa-bars"></i></span>
                        </button>

                        <div class="collapse navbar-collapse main-menu-item" id="navbarSupportedContent">
                            <ul class="navbar-nav">
                                <li class="nav-item">
                                    <a class="nav-link" href="index.html">Home</a>
                                </li>
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="blog.html" id="navbarDropdown_1"
                                        role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        Shop
                                    </a>
                                    <div class="dropdown-menu" aria-labelledby="navbarDropdown_1">
                                        <a class="dropdown-item" href="category.html"> shop category</a>
                                        <a class="dropdown-item" href="single-product.html">product details</a>
                                        
                                    </div>
                                </li>
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="blog.html" id="navbarDropdown_3"
                                        role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        pages
                                    </a>
                                    <div class="dropdown-menu" aria-labelledby="navbarDropdown_2">
                                        <a class="dropdown-item" href="login.html"> login</a>
                                        <a class="dropdown-item" href="tracking.html">tracking</a>
                                        <a class="dropdown-item" href="checkout.html">product checkout</a>
                                        <a class="dropdown-item" href="cart.html">shopping cart</a>
                                        <a class="dropdown-item" href="confirmation.html">confirmation</a>
                                        <a class="dropdown-item" href="elements.html">elements</a>
                                    </div>
                                </li>
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="blog.html" id="navbarDropdown_2"
                                        role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        blog
                                    </a>
                                    <div class="dropdown-menu" aria-labelledby="navbarDropdown_2">
                                        <a class="dropdown-item" href="blog.html"> blog</a>
                                        <a class="dropdown-item" href="single-blog.html">Single blog</a>
                                    </div>
                                </li>
                                
                                <li class="nav-item">
                                    <a class="nav-link" href="contact.html">Contact</a>
                                </li>
                            </ul>
                        </div>
                        <div class="hearer_icon d-flex">
                            <a id="search_1" href="javascript:void(0)"><i class="ti-search"></i></a>
                            <a href=""><i class="ti-heart"></i></a>
                            <div class="dropdown cart">
                                <a class="dropdown-toggle" href="#" id="navbarDropdown3" role="button"
                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fas fa-cart-plus"></i>
                                </a>
                                <!-- <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                                    <div class="single_product">
    
                                    </div>
                                </div> -->
                                
                            </div>
                            <div class="dropdown user">
    <?php if (session()->has('user')): ?>
        <!-- Khi đã đăng nhập -->
        <a class="dropdown-toggle" href="#" id="userDropdown" role="button"
           data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="fas fa-user"></i> <?= esc(session('user')['name']) ?>
        </a>
        <div class="dropdown-menu" aria-labelledby="userDropdown">
            <a class="dropdown-item" href="<?= base_url('profile') ?>">My Profile</a>
            <a class="dropdown-item" href="<?= base_url('orders') ?>">My Orders</a>
            <div class="dropdown-divider"></div>
            <a class="dropdown-item" href="<?= base_url('logout') ?>">Logout</a>
        </div>
    <?php else: ?>
        <!-- Khi chưa đăng nhập -->
        <a href="<?= route_to('Customers_sign') ?>">
            <i class="fas fa-user"></i> Login
        </a>
    <?php endif; ?>
</div>


                        </div>
                    </nav>
                </div>
            </div>
        </div>
        <div class="search_input" id="search_input_box">
            <div class="container ">
                <form class="d-flex justify-content-between search-inner">
                    <input type="text" class="form-control" id="search_input" placeholder="Search Here">
                    <button type="submit" class="btn"></button>
                    <span class="ti-close" id="close_search" title="Close Search"></span>
                </form>
            </div>
        </div>
    </header>

    <?= $this->renderSection('content') ?>

       <footer class="footer_part">
        <div class="container">
            <div class="row justify-content-around">
                <div class="col-sm-6 col-lg-2">
                    <div class="single_footer_part">
                        <h4>Top Products</h4>
                        <ul class="list-unstyled">
                            <li><a href="">Managed Website</a></li>
                            <li><a href="">Manage Reputation</a></li>
                            <li><a href="">Power Tools</a></li>
                            <li><a href="">Marketing Service</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-2">
                    <div class="single_footer_part">
                        <h4>Quick Links</h4>
                        <ul class="list-unstyled">
                            <li><a href="">Jobs</a></li>
                            <li><a href="">Brand Assets</a></li>
                            <li><a href="">Investor Relations</a></li>
                            <li><a href="">Terms of Service</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-2">
                    <div class="single_footer_part">
                        <h4>Features</h4>
                        <ul class="list-unstyled">
                            <li><a href="">Jobs</a></li>
                            <li><a href="">Brand Assets</a></li>
                            <li><a href="">Investor Relations</a></li>
                            <li><a href="">Terms of Service</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-2">
                    <div class="single_footer_part">
                        <h4>Resources</h4>
                        <ul class="list-unstyled">
                            <li><a href="">Guides</a></li>
                            <li><a href="">Research</a></li>
                            <li><a href="">Experts</a></li>
                            <li><a href="">Agencies</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-4">
                    <div class="single_footer_part">
                        <h4>Newsletter</h4>
                        <p>Heaven fruitful doesn't over lesser in days. Appear creeping
                        </p>
                        <div id="mc_embed_signup">
                            <form target="_blank"
                                action="https://spondonit.us12.list-manage.com/subscribe/post?u=1462626880ade1ac87bd9c93a&amp;id=92a4423d01"
                                method="get" class="subscribe_form relative mail_part">
                                <input type="email" name="email" id="newsletter-form-email" placeholder="Email Address"
                                    class="placeholder hide-on-focus" onfocus="this.placeholder = ''"
                                    onblur="this.placeholder = ' Email Address '">
                                <button type="submit" name="submit" id="newsletter-submit"
                                    class="email_icon newsletter-submit button-contactForm">subscribe</button>
                                <div class="mt-10 info"></div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
        <div class="copyright_part">
            <div class="container">
                <div class="row">
                    <div class="col-lg-8">
                        <div class="copyright_text">
                            <P><!-- Link back to Colorlib can't be removed. Template is licensed under CC BY 3.0. -->
Copyright &copy;<script>document.write(new Date().getFullYear());</script> All rights reserved | This template is made with <i class="ti-heart" aria-hidden="true"></i> by <a href="https://colorlib.com" target="_blank">Colorlib</a>
<!-- Link back to Colorlib can't be removed. Template is licensed under CC BY 3.0. --></P>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="footer_icon social_icon">
                            <ul class="list-unstyled">
                                <li><a href="#" class="single_social_icon"><i class="fab fa-facebook-f"></i></a></li>
                                <li><a href="#" class="single_social_icon"><i class="fab fa-twitter"></i></a></li>
                                <li><a href="#" class="single_social_icon"><i class="fas fa-globe"></i></a></li>
                                <li><a href="#" class="single_social_icon"><i class="fab fa-behance"></i></a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </footer>
<!-- Thư viện SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
    //thông báo thành công - thất bại
    function showSuccessMessage(title, message) {
        Swal.fire({
            icon: 'success',
            title: title,
            text: message,
            confirmButtonText: 'OK'
        });
    }

    function showErrorMessage(title, message) {
        Swal.fire({
            icon: 'error',
            title: title,
            text: message,
            confirmButtonText: 'OK'
        });
    }

    // Xử lý flashdata từ session
    <?php if (session()->getFlashdata('success')) : ?>
        showSuccessMessage("Thành công", "<?= session()->getFlashdata('success') ?>");
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')) : ?>
        showErrorMessage("Thất bại", "<?= session()->getFlashdata('error') ?>");
    <?php endif; ?>
    </script>
     <script src="<?= base_url('aranoz-master/js/jquery-1.12.1.min.js'); ?>"></script>
    <!-- popper js -->
    <script src="<?= base_url('aranoz-master/js/popper.min.js'); ?>"></script>
    <!-- bootstrap js -->
    <script src="<?= base_url('aranoz-master/js/bootstrap.min.js'); ?>"></script>
    <!-- easing js -->
    <script src="<?= base_url('aranoz-master/js/jquery.magnific-popup.js'); ?>"></script>
    <!-- swiper js -->
    <script src="<?= base_url('aranoz-master/js/swiper.min.js'); ?>"></script>
    <!-- swiper js -->
    <script src="<?= base_url('aranoz-master/js/masonry.pkgd.js'); ?>"></script>
    <!-- particles js -->
    <script src="<?= base_url('aranoz-master/js/owl.carousel.min.js'); ?>"></script>
    <script src="<?= base_url('aranoz-master/js/jquery.nice-select.min.js'); ?>"></script>
    <!-- slick js -->
    <script src="<?= base_url('aranoz-master/js/slick.min.js'); ?>"></script>
    <script src="<?= base_url('aranoz-master/js/jquery.counterup.min.js'); ?>"></script>
    <script src="<?= base_url('aranoz-master/js/waypoints.min.js'); ?>"></script>
    <script src="<?= base_url('aranoz-master/js/contact.js'); ?>"></script>
    <script src="<?= base_url('aranoz-master/js/jquery.ajaxchimp.min.js'); ?>"></script>
    <script src="<?= base_url('aranoz-master/js/jquery.form.js'); ?>"></script>
    <script src="<?= base_url('aranoz-master/js/jquery.validate.min.js'); ?>"></script>
    <script src="<?= base_url('aranoz-master/js/mail-script.js'); ?>"></script>
    <!-- custom js -->
    <script src="<?= base_url('aranoz-master/js/custom.js'); ?>"></script>
    <!-- ... các js khác ... -->
</body>
</html>
