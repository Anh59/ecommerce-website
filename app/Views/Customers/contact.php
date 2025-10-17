<?php // filepath: app/Views/contact.php ?>
<?= $this->extend('Customers/layout/main') ?>

<?= $this->section('styles') ?>
<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.css" />
<style>
    #map {
        height: 480px;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    .leaflet-popup-content-wrapper {
        border-radius: 8px;
    }
    .contact-info {
        transition: transform 0.3s ease;
    }
    .contact-info:hover {
        transform: translateY(-5px);
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
                        <h2>Liên hệ</h2>
                        <p>Trang chủ <span>-</span> Liên hệ</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- breadcrumb end-->

<!-- ================ contact section start ================= -->
<section class="contact-section padding_top">
    <div class="container">
        <!-- Map Section -->
        <div class="d-none d-sm-block mb-5 pb-4">
            <div id="map"></div>
        </div>

        <div class="row">
            <div class="col-12">
                <h2 class="contact-title">Liên hệ với chúng tôi</h2>
            </div>
            
            <!-- Contact Form -->
            <div class="col-lg-8">
                <form class="form-contact contact_form" action="<?= base_url('contact/send') ?>" method="post" id="contactForm">
                    <?= csrf_field() ?>
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <textarea class="form-control w-100" name="message" id="message" cols="30" rows="9"
                                    onfocus="this.placeholder = ''" onblur="this.placeholder = 'Nhập tin nhắn'"
                                    placeholder='Nhập tin nhắn' required></textarea>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <input class="form-control" name="name" id="name" type="text" 
                                    onfocus="this.placeholder = ''"
                                    onblur="this.placeholder = 'Nhập tên của bạn'" 
                                    placeholder='Nhập tên của bạn' required>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <input class="form-control" name="email" id="email" type="email" 
                                    onfocus="this.placeholder = ''"
                                    onblur="this.placeholder = 'Nhập địa chỉ email'" 
                                    placeholder='Nhập địa chỉ email' required>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <input class="form-control" name="subject" id="subject" type="text" 
                                    onfocus="this.placeholder = ''"
                                    onblur="this.placeholder = 'Nhập tiêu đề'" 
                                    placeholder='Nhập tiêu đề' required>
                            </div>
                        </div>
                    </div>
                    <div class="form-group mt-3">
                        <button type="submit" class="btn_3 button-contactForm">Gửi tin nhắn</button>
                    </div>
                </form>
            </div>
            
            <!-- Contact Info -->
            <div class="col-lg-4">
                <div class="media contact-info">
                    <span class="contact-info__icon"><i class="ti-home"></i></span>
                    <div class="media-body">
                        <h3>Địa chỉ văn phòng</h3>
                        <p>Nam Định, Ninh Bình, Việt Nam</p>
                    </div>
                </div>
                <div class="media contact-info">
                    <span class="contact-info__icon"><i class="ti-tablet"></i></span>
                    <div class="media-body">
                        <h3>+84 (123) 456 789</h3>
                        <p>Thứ 2 - Thứ 6: 9h - 18h</p>
                    </div>
                </div>
                <div class="media contact-info">
                    <span class="contact-info__icon"><i class="ti-email"></i></span>
                    <div class="media-body">
                        <h3>support@example.com</h3>
                        <p>Gửi câu hỏi của bạn bất cứ lúc nào!</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- ================ contact section end ================= -->

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- Leaflet JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js"></script>

<script>
    // Khởi tạo bản đồ Leaflet
    $(document).ready(function() {
        // Tọa độ Nam Định, Việt Nam
        var latitude = 20.4389;
        var longitude = 106.1621;
        
        // Khởi tạo map
        var map = L.map('map').setView([latitude, longitude], 13);
        
        // Thêm tile layer từ OpenStreetMap
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
            maxZoom: 19
        }).addTo(map);
        
        // Thêm marker với popup
        var marker = L.marker([latitude, longitude]).addTo(map);
        marker.bindPopup('<b>Vị trí của chúng tôi</b><br>Nam Định, Ninh Bình, Việt Nam').openPopup();
        
        // Custom icon (tùy chọn)
        var customIcon = L.icon({
            iconUrl: '<?= base_url('aranoz-master/img/marker-icon.png') ?>',
            iconSize: [25, 41],
            iconAnchor: [12, 41],
            popupAnchor: [1, -34],
            shadowSize: [41, 41]
        });
        
        // Có thể thay đổi icon của marker
        // marker.setIcon(customIcon);
    });
    
    // Xử lý form validation
    $('#contactForm').on('submit', function(e) {
        var isValid = true;
        
        // Validate các trường
        $(this).find('[required]').each(function() {
            if ($(this).val().trim() === '') {
                isValid = false;
                $(this).addClass('is-invalid');
            } else {
                $(this).removeClass('is-invalid');
            }
        });
        
        // Validate email
        var email = $('#email').val();
        var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (email && !emailRegex.test(email)) {
            isValid = false;
            $('#email').addClass('is-invalid');
        }
        
        if (!isValid) {
            e.preventDefault();
            showErrorMessage('Lỗi', 'Vui lòng điền đầy đủ thông tin hợp lệ');
            return false;
        }
        
        // Hiển thị loading khi submit
        showLoading();
    });
    
    // Xóa invalid class khi người dùng nhập
    $('[required], #email').on('input', function() {
        $(this).removeClass('is-invalid');
    });
</script>
<?= $this->endSection() ?>