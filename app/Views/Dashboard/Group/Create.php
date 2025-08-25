<?= $this->extend('Dashboard/layout'); ?>

<?= $this->section('content'); ?>

<div class="card">
    <div class="card-header" style="border-bottom: none;">
        <a href="<?= route_to('Table_Group') ?>" class="btn btn-circle" title="Quay lại">
            <i class="fas fa-arrow-left" style="color: black;"></i>
        </a>
    </div>
    <div class="card-body">
   

        <form action="<?= route_to('Table_Store') ?>" method="post">
            <?= csrf_field() ?>

            <div class="form-group">
                <label for="name">Chức vụ</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>

            <div class="form-group">
                <label for="description">Mô tả chức vụ</label>
                <textarea type="text" class="form-control" id="description" name="description" required></textarea>
            </div>

            <button type="submit" class="btn btn-primary">Tạo</button>
        </form>
    </div>
</div>

<?= $this->endSection(); ?>