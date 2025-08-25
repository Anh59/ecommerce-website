<?= $this->extend('Dashboard/layout'); ?>
<?= $this->section('content'); ?>



<div class="mb-3">
    <a href="<?= route_to('Table_Customers_Create') ?>" class="btn btn-success" title="Thêm khách hàng">
        <i class="fas fa-plus"></i> Thêm
    </a>
</div>

<table id="table" class="display" style="width:100%">
    <thead>
        <tr>
            <th>STT</th>
            <th>Tên</th>
            <th>Email</th>
            <th>Số Điện Thoại</th>
            <th>Địa Chỉ</th>
            <th>Chức Năng</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($customers as $index => $customer): ?>
            <tr>
                <td><?= $index + 1 ?></td>
                <td><?= esc($customer['name']) ?></td>
                <td><?= esc($customer['email']) ?></td>
                <td><?= esc($customer['phone']) ?></td>
                <td><?= esc($customer['address']) ?></td>
                <td>
                    <a href="<?= route_to('Table_Customers_Edit', $customer['id']) ?>" class="btn btn-primary btn-sm" title="Sửa">                
                        <i class="fas fa-edit"></i>              
                    </a>
                    <form action="<?= route_to('Table_Customers_Delete', $customer['id']) ?>" method="post" style="display:inline;">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-danger btn-sm" onclick="confirmDelete(event, '<?= route_to('Table_Customers_Delete', $customer['id']) ?>')" title="Xóa">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </form>
                    <form action="<?= route_to('Table_Customers_Lock', $customer['id']) ?>" method="post" style="display:inline;">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-warning btn-sm" onclick="return confirm('Bạn có chắc chắn muốn khóa tài khoản khách hàng này?')" title="Khóa tài khoản">
                            <i class="fas fa-lock"></i> 
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