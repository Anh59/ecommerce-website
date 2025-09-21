<?php // filepath: app/Views/layout/main.php ?>
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
    
    <!-- Toastr CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    
    <!-- Custom styles section -->
    <?= $this->renderSection('styles') ?>
</head>

<body>
    <header class="main_menu home_menu">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-12">
                    <nav class="navbar navbar-expand-lg navbar-light">
                        <a class="navbar-brand" href="<?= base_url() ?>">
                            <img src="<?= base_url('aranoz-master/img/logo.png'); ?>" alt="logo">
                        </a>
                        <button class="navbar-toggler" type="button" data-toggle="collapse"
                            data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                            aria-expanded="false" aria-label="Toggle navigation">
                            <span class="menu_icon"><i class="fas fa-bars"></i></span>
                        </button>

                        <div class="collapse navbar-collapse main-menu-item" id="navbarSupportedContent">
    <ul class="navbar-nav">
        <!-- Home -->
        <li class="nav-item">
            <a class="nav-link" href="<?= route_to('home_about') ?>">Home</a>
        </li>

        <!-- Shop -->
        <li class="nav-item">
    <a class="nav-link" href="<?= route_to('category') ?>">
        Shop
    </a>
</li>


        <!-- Pages -->
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown_3"
               role="button" data-toggle="dropdown" aria-haspopup="true"
               aria-expanded="false">
                Pages
            </a>
            <div class="dropdown-menu" aria-labelledby="navbarDropdown_3">
                <a class="dropdown-item" href="<?= route_to('Customers_sign') ?>">Login</a>
                <a class="dropdown-item" href="<?= route_to('home_tracking') ?>">Tracking</a>
                <a class="dropdown-item" href="<?= route_to('home_checkout') ?>">Product Checkout</a>
                <a class="dropdown-item" href="<?= route_to('home_cart') ?>">Shopping Cart</a>
                <a class="dropdown-item" href="<?= route_to('home_confirmation') ?>">Confirmation</a>
                <a class="dropdown-item" href="<?= route_to('home_elements') ?>">Elements</a>
            </div>
        </li>

        <!-- Blog -->
              <li class="nav-item">
    <a class="nav-link" href="<?= route_to('blog') ?>">
        Blog
    </a>
</li>

        <!-- Contact -->
        <li class="nav-item">
            <a class="nav-link" href="<?= route_to('home_contact') ?>">Contact</a>
        </li>
    </ul>
</div>

                        <div class="hearer_icon d-flex">
                            <a id="search_1" href="javascript:void(0)"><i class="ti-search"></i></a>
                            <a href="<?= route_to('wishlist') ?>"><i class="ti-heart"></i></a>

                            <div class="cart">
    <a href="<?= route_to('cart') ?>" id="navbarCart">
        <i class="fas fa-cart-plus"></i>
    </a>
</div>

                            <div class="dropdown user">
                                <?php if (session()->has('user')): ?>
                                    <!-- Khi đã đăng nhập -->
                                    <a class="dropdown-toggle" href="#" id="userDropdown" role="button"
                                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fas fa-user"></i>
                                        <?= esc(session('user')['name']) ?>
                                    </a>
                                    <div class="dropdown-menu" aria-labelledby="userDropdown">
                                        <a class="dropdown-item" href="<?= base_url('profile') ?>">My Profile</a>
                                        <a class="dropdown-item" href="<?= base_url('orders') ?>">My Orders</a>
                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item" href="<?= route_to('Customers_logout') ?>">Logout</a>
                                    </div>
                                <?php else: ?>
                                    <!-- Khi chưa đăng nhập -->
                                    <a href="<?= route_to('Customers_sign') ?>">
                                        <i class="fas fa-user"></i>
                                        Login
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
                        <p>Heaven fruitful doesn't over lesser in days. Appear creeping</p>
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

    <!-- jQuery MUST be loaded first -->
    <script src="<?= base_url('aranoz-master/js/jquery-1.12.1.min.js'); ?>"></script>
    
    <!-- Toastr JS - Load after jQuery -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    
    <!-- Toastr Configuration and Functions -->
    <script>
        // Cấu hình Toastr
        toastr.options = {
            "closeButton": true,
            "debug": false,
            "newestOnTop": true,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "preventDuplicates": false,
            "onclick": null,
            "showDuration": "300",
            "hideDuration": "1000",
            "timeOut": "5000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        };

        // Functions for showing messages (tương thích với SweetAlert2)
        function showSuccessMessage(title, message) {
            toastr.success(message, title);
        }

        function showErrorMessage(title, message) {
            toastr.error(message, title);
        }

        function showInfoMessage(title, message) {
            toastr.info(message, title);
        }

        function showWarningMessage(title, message) {
            toastr.warning(message, title);
        }

        // Loading functions để tương thích với login form
        function showLoading() {
            toastr.info("Đang xử lý...", "Vui lòng chờ", {
                timeOut: 0,
                extendedTimeOut: 0,
                closeButton: false,
                progressBar: false
            });
        }

        function hideLoading() {
            toastr.clear();
        }

        // Xử lý flashdata từ session - Execute after DOM is ready
        $(document).ready(function() {
            <?php if (session()->getFlashdata('success')) : ?>
                showSuccessMessage("Thành công", "<?= addslashes(session()->getFlashdata('success')) ?>");
            <?php endif; ?>
            
            <?php if (session()->getFlashdata('error')) : ?>
                showErrorMessage("Thất bại", "<?= addslashes(session()->getFlashdata('error')) ?>");
            <?php endif; ?>

            <?php if (session()->getFlashdata('info')) : ?>
                showInfoMessage("Thông báo", "<?= addslashes(session()->getFlashdata('info')) ?>");
            <?php endif; ?>

            <?php if (session()->getFlashdata('warning')) : ?>
                showWarningMessage("Cảnh báo", "<?= addslashes(session()->getFlashdata('warning')) ?>");
            <?php endif; ?>
        });
    </script>

    <!-- Other JS files - Load after jQuery -->
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
    <!-- custom js - Load last -->
    <script src="<?= base_url('aranoz-master/js/custom.js'); ?>"></script>

    <!-- Custom scripts section -->
    <?= $this->renderSection('scripts') ?>

</body>
</html>