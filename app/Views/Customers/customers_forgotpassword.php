<?= $this->extend('Customers/layout/main') ?>

<?= $this->section('content') ?>

<section class="breadcrumb breadcrumb_bg">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="breadcrumb_iner">
                    <div class="breadcrumb_iner_item">
                        <h2>Quên Mật Khẩu</h2>
                        <p>Trang chủ <span>-</span> Quên Mật Khẩu</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="login_part padding_top">
    <div class="container">
        <div class="row align-items-center justify-content-center">
            <div class="col-lg-6 col-md-8">
                <div class="login_part_form">
                    <div class="login_part_form_iner">

                        <!-- Nhập email -->
                        <div id="emailSection">
                            <h3>Đặt Lại Mật Khẩu Của Bạn</h3>
                            <p class="mb-4">Nhập email của bạn để nhận mã OTP.</p>
                            <form id="emailForm" class="row contact_form"
                                  method="post"
                                  action="<?= base_url('api_Customers/customers_forgot_password') ?>"
                                  autocomplete="off" novalidate>
                                <?= csrf_field() ?>
                                <div class="col-md-12 form-group">
                                    <input type="email" name="email" class="form-control" placeholder="Your Email" required>
                                </div>
                                <div class="col-md-12 form-group">
                                    <button type="submit" class="btn_3 w-100">Gửi OTP</button>
                                </div>
                            </form>
                        </div>

                        <!-- Nhập OTP -->
                        <div id="otpSection" style="display:none;">
                            <h3>Xác Thực OTP</h3>
                            <p class="mb-4">Chúng tôi đã gửi mã đến email của bạn. Vui lòng nhập mã bên dưới:</p>
                            <form id="otpForm" class="row contact_form"
                                  method="post"
                                  action="<?= base_url('api_Customers/customers_pass_verify_otp') ?>"
                                  autocomplete="off" novalidate>
                                <?= csrf_field() ?>
                                <div class="col-md-12 form-group">
                                    <input type="text" name="otp" maxlength="6" class="form-control" placeholder="Enter 6-digit OTP" required>
                                    <input type="hidden" name="email">
                                </div>
                                <div class="col-md-12 form-group">
                                    <button type="submit" class="btn_3 w-100">Xác Thực OTP</button>
                                </div>
                            </form>
                        </div>

                        <!-- Reset mật khẩu -->
                        <div id="resetPasswordSection" style="display:none;">
                            <h3>Đặt Lại Mật Khẩu</h3>
                            <form id="resetPasswordForm" class="row contact_form"
                                  method="post"
                                  action="<?= base_url('api_Customers/customers_reset_password') ?>"
                                  autocomplete="off" novalidate>
                                <?= csrf_field() ?>
                                <div class="col-md-12 form-group">
                                    <input type="password" name="new_password" id="new_password" class="form-control" placeholder="New Password" required>
                                </div>
                                <div class="col-md-12 form-group">
                                    <input type="password" name="confirm_password" id="confirm_password" class="form-control" placeholder="Confirm Password" required>
                                </div>
                                <input type="hidden" name="email">
                                <div class="col-md-12 form-group">
                                    <button type="submit" class="btn_3 w-100">Đặt Lại</button>
                                </div>
                            </form>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


<script>
document.addEventListener('DOMContentLoaded', function () {

    // Helper function POST form via fetch
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
        return res.json();
    }

    // --- B1: Nhập email ---
    const emailForm = document.getElementById('emailForm');
    if (emailForm) {
        emailForm.addEventListener('submit', async function (e) {
            e.preventDefault();

            const btn = emailForm.querySelector('button[type="submit"]');
            const original = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
            btn.disabled = true;

            try {
                const data = await postForm(emailForm);
                if (data.status === 'success') {
                    showSuccessMessage('Success', data.message);
                    document.getElementById('emailSection').style.display = 'none';
                    document.getElementById('otpSection').style.display = 'block';
                    // Truyền email sang form OTP
                    document.querySelector('#otpForm [name="email"]').value = data.email;
                } else {
                    showErrorMessage('Error', data.message || 'Email not found.');
                }
            } catch (err) {
                console.error(err);
                showErrorMessage('Error', 'Server error. Please try again.');
            } finally {
                btn.innerHTML = original;
                btn.disabled = false;
            }
        });
    }

    // --- B2: Xác thực OTP ---
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
                    showSuccessMessage('Success', data.message);
                    document.getElementById('otpSection').style.display = 'none';
                    document.getElementById('resetPasswordSection').style.display = 'block';
                    // Truyền email sang form reset password
                    document.querySelector('#resetPasswordForm [name="email"]').value = data.email;
                } else {
                    showErrorMessage('Error', data.message || 'OTP invalid or expired.');
                }
            } catch (err) {
                console.error(err);
                showErrorMessage('Error', 'Server error. Please try again.');
            } finally {
                btn.innerHTML = original;
                btn.disabled = false;
            }
        });
    }

    // --- B3: Reset mật khẩu ---
    const resetForm = document.getElementById('resetPasswordForm');
    if (resetForm) {
        resetForm.addEventListener('submit', async function (e) {
            e.preventDefault();

            const btn = resetForm.querySelector('button[type="submit"]');
            const original = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Resetting...';
            btn.disabled = true;

            try {
                const data = await postForm(resetForm);
                if (data.status === 'success') {
                    showSuccessMessage('Success', data.message);
                    setTimeout(() => {
                        window.location.href = '<?= base_url('api_Customers/customers_sign') ?>';
                    }, 1500);
                } else {
                    showErrorMessage('Error', data.message || 'Reset password failed.');
                }
            } catch (err) {
                console.error(err);
                showErrorMessage('Error', 'Server error. Please try again.');
            } finally {
                btn.innerHTML = original;
                btn.disabled = false;
            }
        });
    }

});
</script>

<style>
#otpSection, #resetPasswordSection {
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
</style>

<?= $this->endSection() ?>
