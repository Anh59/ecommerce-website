<?= $this->extend('Dashboard/layout'); ?>
<?= $this->section('content'); ?>

<div class="card">
    <div class="card-header" style="border-bottom: none;">
        <a href="<?= route_to('Table_Role') ?>" class="btn btn-circle" title="Quay lại">
            <i class="fas fa-arrow-left" style="color: black;"></i>
        </a>
    </div>
    <div class="card-body">
        <form action="<?= route_to('Table_Role_Update', $roles['id']) ?>" method="post">
            <?= csrf_field() ?>
            <div class="form-group">
                <label for="url">Đường Dẫn:</label>
                <input type="text" id="url" name="url" class="form-control" value="<?= esc($roles['url']) ?>" required>
            </div>
            <div class="form-group">
                <label for="description">Mô Tả:</label>
                <textarea id="description" name="description" class="form-control" required><?= esc($roles['description']) ?></textarea>
            </div>
            <button type="submit" class="btn btn-success">Cập nhật</button>
        </form>
    </div>
</div>

<?= $this->endSection(); ?>