<?= $this->extend('Dashboard/layout'); ?>
<?= $this->section('content'); ?>



<div class="mb-3">
    <a href="<?= route_to('Table_User_Create') ?>" class="btn btn-success" title="Thêm tài khoản">
        <i class="fas fa-plus"></i> Thêm
    </a>
</div>

<table id="table" class="display" style="width:100%">
    <thead>
        <tr>
            <th>STT</th>
            <th>TÊN</th>
            <th>EMAIL</th>
            <th>CHỨC VỤ HIỆN TẠI</th>
            <th>CHỨC NĂNG</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($users as $user): ?>
            <?php if ($user['super_admin'] != 1): ?>
                <tr>
                    <td><?= $user['id'] ?></td>
                    <td><?= esc($user['username']) ?></td>
                    <td><?= esc($user['email']) ?></td>
                    <td>
                        <select class="form-control" onchange="changeUserGroup(<?= $user['id'] ?>, this.value)">
                            <?php foreach ($groups as $group): ?>
                                <option value="<?= $group['id'] ?>" <?= ($group['id'] == $user['group_id']) ? 'selected' : '' ?>>
                                    <?= esc($group['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                    <td>
                        <a href="<?= route_to('Table_User_Edit', $user['id']) ?>" class="btn btn-primary" title="Sửa">
                            <i class="fas fa-edit"></i>
                        </a>
                        <button class="btn btn-danger" onclick="confirmDelete(event, '<?= route_to('Table_User_Delete', $user['id']) ?>')" title="Xóa">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </td>
                </tr>
            <?php endif; ?>
        <?php endforeach; ?>
    </tbody>
</table>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script> 
<script src="https://cdn.datatables.net/v/dt/jqc-1.12.4/dt-2.0.7/b-3.0.2/sl-2.0.2/datatables.min.js"></script>
<script src="<?= base_url('js/datatable.js') ?>"></script>
<script>
function changeUserGroup(userId, groupId) {
    $.ajax({
        url: '<?= route_to('change_user_group') ?>',
        type: 'POST',
        data: {
            user_id: userId,
            group_id: groupId,
            <?= csrf_token() ?>: '<?= csrf_hash() ?>'
        },
        success: function(response) {
            if (response.status === 'success') {
                alert('Cập nhật nhóm thành công!');
            } else {
                alert('Lỗi xảy ra khi cập nhật nhóm.');
            }
        },
        error: function() {
            alert('Lỗi xảy ra khi gửi yêu cầu.');
        }
    });
}
</script>

<?= $this->endSection(); ?>