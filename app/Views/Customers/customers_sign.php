<?= $this->extend('Customers/layout/main') ?>

<?= $this->section('styles') ?>

<?= $this->endSection() ?>

<?= $this->section('content') ?>
<!-- breadcrumb start-->
<section class="breadcrumb breadcrumb_bg">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="breadcrumb_iner">
                    <div class="breadcrumb_iner_item">
                        <h2>Đăng Nhập</h2>
                        <p>Trang chủ <span>-</span> Đăng Nhập</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- breadcrumb end-->

<!--================login_part Area =================-->
<section class="login_part padding_top">
    <div class="container">
        <div class="row align-items-center">
            <!-- Phần bên trái -->
            <div class="col-lg-6 col-md-6">
                <div class="login_part_text text-center">
                    <div class="login_part_text_iner">
                        <h2>Mới đến cửa hàng của chúng tôi?</h2>
                        <p>Tham gia cùng chúng tôi hôm nay và tận hưởng những lợi ích độc quyền.</p>
                        <a href="<?= route_to('Customers_Register'); ?>" class="btn_3">Tạo tài khoản</a>

                        <!-- Social Sign-in Options - Giống trang đăng ký -->
                        <div style="margin-top: 20px;">
                            <a href="<?= route_to('google_login') ?>" class="btn btn-danger btn-block mb-2">
                                <i class="fab fa-google"></i> Đăng Nhập bằng Google
                            </a>
                            <a href="#" class="btn btn-primary btn-block">
                                <i class="fab fa-facebook-f"></i> Đăng Nhập bằng Facebook
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Login -->
            <div class="col-lg-6 col-md-6">
                <div class="login_part_form">
                    <div class="login_part_form_iner">
                        <h3>Chào mừng bạn trở lại! <br>
                            Vui lòng đăng nhập ngay</h3>

                        <!-- Form login -->
                        <form id="loginForm" class="row contact_form" method="post">
                            <?= csrf_field() ?> <!-- Bảo mật CSRF -->

                            <div class="col-md-12 form-group p_star">
                                <input type="email" class="form-control" id="email" name="email"
                                    placeholder="Email" required>
                                <div class="invalid-feedback"></div>
                            </div>

                            <div class="col-md-12 form-group p_star" style="position: relative;">
                                <input type="password" class="form-control" id="password" name="password" 
                                    placeholder="Mật khẩu" required>
                                <i class="fa fa-eye" id="togglePassword" 
                                   onclick="togglePasswordVisibility()" 
                                   style="position:absolute; right:15px; top:50%; transform:translateY(-50%); cursor:pointer; color: #999;">
                                </i>
                                <div class="invalid-feedback"></div>
                            </div>

                            <div class="col-md-12 form-group">
                                <div class="creat_account d-flex align-items-center">
                                    <input type="checkbox" id="remember_me" name="remember_me">
                                    <label for="remember_me">Ghi nhớ tôi</label>
                                </div>

                                <button type="submit" class="btn_3" id="loginBtn">
                                    <span id="loginBtnText">Đăng Nhập</span>
                                    <span id="loginSpinner" class="d-none">
                                        <i class="fa fa-spinner fa-spin"></i> Đang đăng nhập...
                                    </span>
                                </button>
                                <a class="lost_pass" href="<?= route_to('customes_forgot_password'); ?>">Quên mật khẩu?</a>
                            </div>
                        </form>

                        <!-- Đã chuyển Google Login sang phần bên trái -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!--================login_part end =================-->
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function(){
    $('#googleLoginBtn').on('click', function(e){
        // Hiển thị loading
        const $btn = $(this);
        const originalHtml = $btn.html();
        $btn.html('<i class="fa fa-spinner fa-spin"></i> Đang chuyển hướng...')
            .prop('disabled', true);
        
        // Cho phép chuyển hướng
        setTimeout(() => {
            $btn.html(originalHtml).prop('disabled', false);
        }, 1000);
    });
    // Hàm toggle password visibility
    window.togglePasswordVisibility = function() {
        const passwordField = document.getElementById('password');
        const toggleIcon = document.getElementById('togglePassword');
        
        if (passwordField.type === 'password') {
            passwordField.type = 'text';
            toggleIcon.classList.remove('fa-eye');
            toggleIcon.classList.add('fa-eye-slash');
        } else {
            passwordField.type = 'password';
            toggleIcon.classList.remove('fa-eye-slash');
            toggleIcon.classList.add('fa-eye');
        }
    };

    // Xử lý submit form login
    $('#loginForm').on('submit', function(e){
        e.preventDefault();
        
        // Xóa các thông báo lỗi cũ
        $('.form-control').removeClass('is-invalid');
        $('.invalid-feedback').empty();
        
        // Validate form trước khi gửi
        let email = $('#email').val().trim();
        let password = $('#password').val().trim();
        let hasError = false;

        if (!email) {
            $('#email').addClass('is-invalid');
            $('#email').next('.invalid-feedback').text('Vui lòng nhập email');
            hasError = true;
        } else if (!isValidEmail(email)) {
            $('#email').addClass('is-invalid');
            $('#email').next('.invalid-feedback').text('Email không hợp lệ');
            hasError = true;
        }

        if (!password) {
            $('#password').addClass('is-invalid');
            $('#password').next('.invalid-feedback').text('Vui lòng nhập mật khẩu');
            hasError = true;
        }

        if (hasError) {
            showErrorMessage("Lỗi", "Vui lòng kiểm tra lại thông tin!");
            return;
        }
        
        // Hiển thị loading
        $('#loginBtnText').addClass('d-none');
        $('#loginSpinner').removeClass('d-none');
        $('#loginBtn').prop('disabled', true);
        showLoading();

        $.ajax({
            url: '<?= route_to("Customers_processLogin") ?>',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            timeout: 15000, // 15 seconds timeout
            success: function(response){
                hideLoading();
                resetLoginButton();

                if(response.status === 'success'){
                    // Hiển thị thông báo thành công
                    showSuccessMessage("Đăng nhập thành công!", response.message);
                    
                    // Chờ 2 giây rồi chuyển hướng
                    setTimeout(function(){
                        if(response.redirect_url) {
                            window.location.href = response.redirect_url;
                        } else {
                            // Fallback về trang chủ nếu không có redirect_url
                            window.location.href = '<?= base_url() ?>';
                        }
                    }, 2000);
                } else {
                    // Hiển thị lỗi từ server
                    showErrorMessage("Đăng nhập thất bại!", response.message);
                    
                    // Highlight field có lỗi nếu cần
                    if (response.message.includes('Email')) {
                        $('#email').addClass('is-invalid');
                        $('#email').next('.invalid-feedback').text(response.message);
                    } else if (response.message.includes('Mật khẩu')) {
                        $('#password').addClass('is-invalid');
                        $('#password').next('.invalid-feedback').text(response.message);
                    }
                }
            },
            error: function(xhr, status, error){
                hideLoading();
                resetLoginButton();

                let errorMessage = "Xảy ra lỗi trong quá trình xử lý!";
                
                if (status === 'timeout') {
                    errorMessage = "Kết nối quá chậm, vui lòng thử lại!";
                } else if (xhr.status === 404) {
                    errorMessage = "Không tìm thấy trang xử lý!";
                } else if (xhr.status === 500) {
                    errorMessage = "Lỗi hệ thống, vui lòng thử lại sau!";
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }

                console.log('AJAX Error:', xhr.responseText);
                showErrorMessage("Lỗi kết nối", errorMessage);
            }
        });
    });

    // Hàm reset trạng thái button login
    function resetLoginButton() {
        $('#loginBtnText').removeClass('d-none');
        $('#loginSpinner').addClass('d-none');
        $('#loginBtn').prop('disabled', false);
    }

    // Hàm validate email
    function isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }

    // Xóa lỗi khi người dùng bắt đầu gõ
    $('#email, #password').on('input', function() {
        $(this).removeClass('is-invalid');
        $(this).next('.invalid-feedback').empty();
    });

    // Enter key để submit form
    $('#email, #password').on('keypress', function(e) {
        if (e.which === 13) { // Enter key
            $('#loginForm').submit();
        }
    });

    // Auto focus vào email khi trang load
    $('#email').focus();
});

</script>

<style>
/* Social login buttons styling - Giống trang đăng ký */
.btn-danger, .btn-primary {
    border-radius: 25px;
    padding: 10px 20px;
    font-weight: 500;
    text-decoration: none;
    display: inline-block;
    width: 100%;
    text-align: center;
    margin-bottom: 10px;
    transition: all 0.3s ease;
    border: none;
    color: white !important;
}
.btn-danger:hover, .btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    text-decoration: none;
    color: white;
}
.btn-danger {
    background: linear-gradient(45deg, #ea4335, #d14836);
}
.btn-primary {
    background: linear-gradient(45deg, #1877f2, #166fe5);
}
.btn_3:disabled {
    opacity: 0.7;
    cursor: not-allowed;
}
</style>
<?= $this->endSection() ?>