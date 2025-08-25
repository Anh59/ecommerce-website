<?= $this->extend('Dashboard/layout'); ?>
<?= $this->section('content'); ?>

<div class="card">
    <div class="card-header" style="border-bottom: none;">
        <a href="<?= route_to('Table_Group') ?>" class="btn btn-circle" title="Quay lại">
            <i class="fas fa-arrow-left" style="color: black;"></i>
        </a>
    </div>
    <div class="card-body">
    
        <form action="<?= route_to('Group_update', $group['id']) ?>" method="post">
            <?= csrf_field() ?>
            <div class="form-group">
                <label for="name">Tên Chức vụ:</label>
                <input type="text" id="name" name="name" class="form-control" value="<?= esc($group['name']) ?>" required>
            </div>
            <div class="form-group">
                <label for="description">Mô tả:</label>
                <textarea id="description" name="description" class="form-control" required><?= esc($group['description']) ?></textarea>
            </div>
            <button type="submit" class="btn btn-success">Cập nhật</button>
        </form>
    </div>
</div>

<?= $this->endSection(); ?>