<?= $this->extend('Dashboard/layout'); ?>
<?= $this->section('content'); ?>

<div class="mb-3">
    <button class="btn btn-success" onclick="openBrandModal()">+ Thêm Brand</button>
</div>

<table id="brandTable" class="display">
    <thead>
        <tr>
            <th>ID</th>
            <th>Tên</th>
            <th>Mô tả</th>
            <th>Trạng thái</th>
            <th>Chức năng</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($brands as $brand): ?>
            <tr>
                <td><?= $brand['id'] ?></td>
                <td><?= esc($brand['name']) ?></td>
                <td><?= esc($brand['description']) ?></td>
                <td><?= $brand['status'] ?></td>
                <td>
                    <button class="btn btn-primary" onclick="editBrand(<?= $brand['id'] ?>, '<?= esc($brand['name']) ?>', '<?= esc($brand['description']) ?>', '<?= $brand['status'] ?>')">Sửa</button>
                    <button class="btn btn-danger" onclick="deleteBrand(<?= $brand['id'] ?>)">Xóa</button>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<script>
function openBrandModal() {
    let name = prompt("Nhập tên brand:");
    if(name) {
        $.post("<?= route_to('Table_Brand_Store') ?>", {
            name: name,
            description: '',
            status: 1,
            <?= csrf_token() ?>: "<?= csrf_hash() ?>"
        }, function(res) {
            if(res.status === 'success') location.reload();
        }, 'json');
    }
}

function editBrand(id, name, description, status) {
    let newName = prompt("Sửa tên brand:", name);
    if(newName) {
        $.post("<?= base_url('Dashboard/brands/update') ?>/" + id, {
            name: newName,
            description: description,
            status: status,
            <?= csrf_token() ?>: "<?= csrf_hash() ?>"
        }, function(res) {
            if(res.status === 'success') location.reload();
        }, 'json');
    }
}

function deleteBrand(id) {
    if(confirm("Bạn có chắc muốn xóa?")) {
        $.post("<?= base_url('Dashboard/brands/delete') ?>/" + id, {
            <?= csrf_token() ?>: "<?= csrf_hash() ?>"
        }, function(res) {
            if(res.status === 'success') location.reload();
        }, 'json');
    }
}
</script>

<?= $this->endSection(); ?>
