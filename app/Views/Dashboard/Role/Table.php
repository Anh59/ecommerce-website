<?= $this->extend('Dashboard/layout'); ?>
<?= $this->section('content'); ?>

<form action="<?= route_to('Table_Role_Create') ?>" method="get" style="display:inline;">
    <button type="submit" class="btn btn-success" title="Thêm Role">
        <i class="fas fa-plus"></i> Thêm
    </button>
</form>

<table id="table" class="display" style="width:100%">
    <thead>
        <tr>
            <th>STT</th>
            <th>ĐƯỜNG DẪN</th>
            <th>MÔ TẢ</th>
            <th>CHỨC NĂNG</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($roles as $role): ?>
        <tr>
            <td><?= $role['id'] ?></td>
            <td><?= esc($role['url']) ?></td>
            <td><?= esc($role['description']) ?></td>
            <td>
                <a href="<?= route_to('Table_Role_Edit', $role['id']) ?>" class="btn btn-primary" title="Sửa">
                    <i class="fas fa-edit"></i>
                </a>
                <form action="<?= route_to('Table_Role_Delete', $role['id']) ?>" method="post" style="display:inline;">
                    <?= csrf_field() ?>
                    <button type="button" class="btn btn-danger" onclick="confirmDelete(event, '<?= route_to('Table_Role_Delete', $role['id']) ?>')" title="Xóa">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdn.datatables.net/v/dt/jqc-1.12.4/dt-2.0.7/b-3.0.2/sl-2.0.2/datatables.min.js"></script>
<script src="<?= base_url('js/datatable.js') ?>"></script>

<?= $this->endSection(); ?>