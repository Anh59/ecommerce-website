<?= $this->extend('Customers/layout/main') ?>

<?= $this->section('content') ?>

<section class="breadcrumb breadcrumb_bg">
    <div class="container text-center">
        <h2>Forgot Password</h2>
        <p>Home <span>-</span> Forgot Password</p>
    </div>
</section>

<section class="login_part padding_top">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8">
                <div class="login_part_form">
                    <div class="login_part_form_iner">
                        <h3>Reset Your Password</h3>
                        <form action="<?= route_to('Customers_processForgotPassword'); ?>" method="post">
                            <?= csrf_field() ?>
                            <div class="form-group">
                                <input type="email" class="form-control" name="email" placeholder="Enter your registered email" required>
                            </div>
                            <button type="submit" class="btn_3">Send OTP</button>
                        </form>
                        <a href="<?= route_to('Customers_sign'); ?>" class="d-block mt-3">Back to Login</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?>
