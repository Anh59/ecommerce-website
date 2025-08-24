<?= $this->extend('Dashboard/layout'); ?>
<?= $this->section('content'); ?>

<div class="card">
    <div class="card-header">
        <a href="<?= route_to('Table_User') ?>" class="btn btn-circle" title="Quay lại">
            <i class="fas fa-arrow-left"></i>
        </a>
    </div>
    <div class="card-body">
   

        <?php if (session()->has('success')): ?>
            <div class="alert alert-success"><?= session()->get('success') ?></div>
        <?php endif; ?>
        <?php if (session()->has('error')): ?>
            <div class="alert alert-danger"><?= session()->get('error') ?></div>
        <?php endif; ?>

        <form action="<?= route_to('Table_User_Update', $user['id']) ?>" method="post">
            <?= csrf_field() ?>
            <div class="form-group">
                <label for="username">Tên người dùng</label>
                <input type="text" name="username" id="username" class="form-control" value="<?= esc($user['username']) ?>" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" class="form-control" value="<?= esc($user['email']) ?>" required>
            </div>
            <div class="form-group">
                <label for="group_id">Chức vụ</label>
                <select name="group_id" id="group_id" class="form-control" required>
                    <?php foreach ($groups as $group): ?>
                        <option value="<?= $group['id'] ?>" <?= ($group['id'] == $user['group_id']) ? 'selected' : '' ?>>
                            <?= esc($group['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
        </form>
    </div>
</div>

<?= $this->endSection(); ?>