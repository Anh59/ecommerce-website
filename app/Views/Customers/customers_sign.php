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
                        <h2>Login</h2>
                        <p>Home <span>-</span> Login</p>
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
                        <h2>New to our Shop?</h2>
                        <p>Join us today and enjoy exclusive benefits.</p>
                        <a href="<?= route_to('Customers_Register'); ?>" class="btn_3">Create an Account</a>
                    </div>
                </div>
            </div>

            <!-- Form Login -->
            <div class="col-lg-6 col-md-6">
                <div class="login_part_form">
                    <div class="login_part_form_iner">
                        <h3>Welcome Back ! <br>
                            Please Sign in now</h3>

                        <!-- Form login -->
                        <form id="loginForm" class="row contact_form" method="post">
                            <?= csrf_field() ?> <!-- Bảo mật CSRF -->

                            <div class="col-md-12 form-group p_star">
                                <input type="email" class="form-control" id="email" name="email"
                                    placeholder="Email" required>
                            </div>

                            <div class="col-md-12 form-group p_star" style="position: relative;">
                                <input type="password" class="form-control" id="password" name="password" 
                                    placeholder="Password" required>
                                <i class="fa fa-eye" id="togglePassword" 
                                   onclick="password.type = (password.type==='password') ? 'text' : 'password'; 
                                            this.classList.toggle('fa-eye-slash');" 
                                   style="position:absolute; right:15px; top:50%; transform:translateY(-50%); cursor:pointer;">
                                </i>
                            </div>

                            <div class="col-md-12 form-group">
                                <div class="creat_account d-flex align-items-center">
                                    <input type="checkbox" id="remember_me" name="remember_me">
                                    <label for="remember_me">Remember me</label>
                                </div>

                                <button type="submit" class="btn_3">Log In</button>
                                <a class="lost_pass" href="<?= route_to('customes_forgot_password'); ?>">Forget password?</a>
                            </div>
                        </form>

                        <!-- Google Login -->
                        <div class="col-md-12 text-center mt-3">
                            <a href="<?= route_to('google_login'); ?>" class="btn btn-outline-danger">
                                <i class="fab fa-google"></i> Login with Google
                            </a>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!--================login_part end =================-->
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
$(document).ready(function(){
    $('#loginForm').on('submit', function(e){
        e.preventDefault();

        $.ajax({
            url: '<?= route_to("Customers_processLogin") ?>',
            type: 'POST',
            data: $(this).serialize(),
            success: function(response){
                if(response.status === 'success'){
                    sessionStorage.setItem('authToken', response.token);
                    showSuccessMessage("Thành công!", response.message);
                    window.location.href = '<?= route_to("home_about") ?>';
                }else{
                    showErrorMessage("Lỗi", response.message);
                }
            },
            error: function(){
                showErrorMessage("Lỗi", "Xảy ra lỗi trong quá trình xử lý");
            }
        });
    });
});
</script>
<?= $this->endSection() ?>
