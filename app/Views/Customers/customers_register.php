<?= $this->extend('Customers/layout/main') ?>

<?= $this->section('content') ?>

<section class="breadcrumb breadcrumb_bg">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="breadcrumb_iner">
                    <div class="breadcrumb_iner_item">
                        <h2>Đăng Ký</h2>
                        <p>Trang chủ <span>-</span> Đăng Ký</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="login_part padding_top">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 col-md-6">
                <div class="login_part_text text-center">
                    <h2>Đã có tài khoản?</h2>
                    <p>Đăng nhập ngay để tiếp tục mua sắm.</p>
                    <a href="<?= route_to('Customers_sign'); ?>" class="btn_3">Đăng Nhập</a>

                    <!-- Social Sign-in Options -->
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

            <div class="col-lg-6 col-md-6">
                <div class="login_part_form">
                    <div class="login_part_form_iner">
                        <!-- Registration Form -->
                        <div id="registerSection">
                            <h3>Tạo Tài Khoản Của Bạn</h3>
                            <form class="row contact_form" id="registerForm"
                                  method="post"
                                  action="<?= base_url('api_Customers/customers_register') ?>"
                                  autocomplete="off" novalidate>
                                <?= csrf_field() ?>

                                <div class="col-md-12 form-group">
                                    <input type="text" name="name" class="form-control" placeholder="Họ và tên" required>
                                </div>
                                <div class="col-md-12 form-group">
                                    <input type="email" name="email" class="form-control" placeholder="Email" required>
                                </div>
                                <div class="col-md-12 form-group">
                                    <input type="text" name="phone" class="form-control" placeholder="Số điện thoại" required>
                                </div>
                                <div class="col-md-12 form-group">
                                    <input type="text" name="address" class="form-control" placeholder="Địa chỉ" required>
                                </div>

                                <!-- Password with toggle eye -->
                                <div class="col-md-12 form-group p_star" style="position: relative;">
                                    <input type="password" class="form-control" id="password" name="password"
                                           placeholder="Mật khẩu " required>
                                    <i class="fa fa-eye" id="togglePassword"
                                       style="position:absolute; right:15px; top:50%; transform:translateY(-50%); cursor:pointer;"></i>
                                </div>

                                <div class="col-md-12 form-group">
                                    <button type="submit" class="btn_3 w-100">Đăng Ký</button>
                                </div>
                            </form>
                        </div>

                        <!-- OTP Verification Form -->
                        <div id="otpSection" style="display:none;">
                            <h3>Xác Thực OTP</h3>
                            <p class="text-center mb-4">Chúng tôi đã gửi mã OTP đến email của bạn. Vui lòng nhập mã bên dưới:</p>
                            <form class="row contact_form" id="otpForm"
                                  method="post"
                                  action="<?= base_url('api_Customers/customers_verify_otp') ?>"
                                  autocomplete="off" novalidate>
                                <?= csrf_field() ?>
                                
                                <div class="col-md-12 form-group">
                                    <input type="text" name="otp" class="form-control" placeholder="Enter 6-digit OTP" maxlength="6" required>
                                    <input type="hidden" name="email">
                                </div>

                                <div class="col-md-12 form-group">
                                    <button type="submit" class="btn_3 w-100">Xác Thực OTP</button>
                                </div>
                                
                                <div class="col-md-12 text-center">
                                    <small class="text-muted">Không nhận được mã? Kiểm tra thư mục spam hoặc thử lại.</small>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- KHÔNG nhúng lại jQuery ở đây để tránh xung đột với layout -->

<script>
// Vanilla JS (không phụ thuộc jQuery)

document.addEventListener('DOMContentLoaded', function () {
    // Toggle show/hide password
    const toggle = document.getElementById('togglePassword');
    const pwd = document.getElementById('password');
    if (toggle && pwd) {
        toggle.addEventListener('click', function () {
            pwd.type = (pwd.type === 'password') ? 'text' : 'password';
            this.classList.toggle('fa-eye-slash');
        });
    }

    // Numeric only for OTP
    const otpInput = document.querySelector('input[name="otp"]');
    if (otpInput) {
        otpInput.addEventListener('input', function () {
            this.value = this.value.replace(/[^0-9]/g, '');
        });
    }

    // Helper: POST form via fetch (x-www-form-urlencoded)
    async function postForm(form) {
        const url = form.getAttribute('action');
        const body = new URLSearchParams(new FormData(form)).toString();

        const res = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body
        });
        // Nếu backend không trả JSON, sẽ throw -> bắt ở catch
        return res.json();
    }

    // Register submit
    const registerForm = document.getElementById('registerForm');
    if (registerForm) {
        registerForm.addEventListener('submit', async function (e) {
            e.preventDefault();

            const btn = registerForm.querySelector('button[type="submit"]');
            const original = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
            btn.disabled = true;

            try {
                const data = await postForm(registerForm);

                if (data.status === 'success') {
                    showSuccessMessage('Success', data.message || 'Registered successfully.');
                    // Chuyển qua OTP
                    document.getElementById('registerSection').style.display = 'none';
                    document.getElementById('otpSection').style.display = 'block';
                    const emailHidden = document.querySelector('#otpForm [name="email"]');
                    if (emailHidden) emailHidden.value = data.email || registerForm.querySelector('[name="email"]')?.value || '';
                } else {
                    let msg = data.message || 'Registration failed';
                    if (data.errors) {
                        // gộp lỗi theo dòng
                        try {
                            msg = Object.values(data.errors).join('\n');
                        } catch (_) {}
                    }
                    showErrorMessage('Error', msg);
                }
            } catch (err) {
                console.error('Register error:', err);
                showErrorMessage('Error', 'An error occurred during registration. Please try again.');
            } finally {
                btn.innerHTML = original;
                btn.disabled = false;
            }
        });
    }

    // OTP submit
    const otpForm = document.getElementById('otpForm');
    if (otpForm) {
        otpForm.addEventListener('submit', async function (e) {
            e.preventDefault();

            const btn = otpForm.querySelector('button[type="submit"]');
            const original = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Verifying...';
            btn.disabled = true;

            try {
                const data = await postForm(otpForm);

                if (data.status === 'success') {
                    showSuccessMessage('Success', data.message || 'OTP verification successful.');
                    setTimeout(function () {
                        // Giữ nguyên URL đích bạn đang dùng
                        window.location.href = '<?= base_url('api_Customers/customers_sign') ?>';
                    }, 1500);
                } else {
                    showErrorMessage('Error', data.message || 'OTP verification failed');
                }
            } catch (err) {
                console.error('OTP error:', err);
                showErrorMessage('Error', 'An error occurred during OTP verification. Please try again.');
            } finally {
                btn.innerHTML = original;
                btn.disabled = false;
            }
        });
    }
});
</script>

<style>
#otpSection {
    animation: fadeIn 0.3s ease-in;
}
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to   { opacity: 1; transform: translateY(0); }
}
.btn_3:disabled {
    opacity: 0.7;
    cursor: not-allowed;
}
/* Social login buttons styling */
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
}
.btn-danger:hover, .btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    text-decoration: none;
    color: white;
}
</style>

<?= $this->endSection() ?>
