<?= $this->extend('Dashboard/layout'); ?>
<?= $this->section('content'); ?>

<div class="card">
    <div class="card-header">
        <a href="<?= route_to('Table_Customers') ?>" class="btn btn-circle" title="Quay lại">
            <i class="fas fa-arrow-left"></i>
        </a>
    </div>
    <div class="card-body">
     

        <form action="<?= route_to('Table_Customers_Store') ?>" method="post">
            <?= csrf_field() ?>

            <div class="form-group">
                <label for="name">Tên</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>

            <div class="form-group">
                <label for="phone">Số Điện Thoại</label>
                <input type="text" class="form-control" id="phone" name="phone" required>
            </div>

            <div class="form-group">
                <label for="address">Địa Chỉ</label>
                <input type="text" class="form-control" id="address" name="address">
            </div>

            <button type="submit" class="btn btn-success">Tạo</button>
        </form>
    </div>
</div>

<?= $this->endSection(); ?>