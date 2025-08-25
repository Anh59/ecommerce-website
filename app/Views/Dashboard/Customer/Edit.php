<?= $this->extend('Dashboard/layout'); ?>
<?= $this->section('content'); ?>

<div class="card">
    <div class="card-header">
        <a href="<?= route_to('Table_Customers') ?>" class="btn btn-circle" title="Quay lại">
            <i class="fas fa-arrow-left"></i>
        </a>
    </div>
    <div class="card-body">
        

        <form action="<?= route_to('Table_Customers_Update', $customer['id']) ?>" method="post">
            <?= csrf_field() ?>

            <div class="form-group">
                <label for="name">Tên</label>
                <input type="text" class="form-control" id="name" name="name" value="<?= esc($customer['name']) ?>" required>
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="<?= esc($customer['email']) ?>" required>
            </div>

            <div class="form-group">
                <label for="phone">Số Điện Thoại</label>
                <input type="text" class="form-control" id="phone" name="phone" value="<?= esc($customer['phone']) ?>" required>
            </div>

            <div class="form-group">
                <label for="address">Địa Chỉ</label>
                <input type="text" class="form-control" id="address" name="address" value="<?= esc($customer['address']) ?>">
            </div>

            <button type="submit" class="btn btn-primary">Lưu Lại</button>
        </form>
    </div>
</div>

<?= $this->endSection(); ?>