$(document).ready(function() {
    $('#table').DataTable({
    language: {
        "decimal":        "",
        "emptyTable":     "Không có dữ liệu trong bảng",
        "info":           "Hiển thị _START_ đến _END_ trong tổng số _TOTAL_ mục",
        "infoEmpty":      "Hiển thị 0 đến 0 của 0 mục",
        "infoFiltered":   "(được lọc từ tổng số _MAX_ mục)",
        "infoPostFix":    "",
        "thousands":      ",",
        "lengthMenu":     "Hiển thị _MENU_ mục mỗi trang",
        "loadingRecords": "Đang tải...",
        "processing":     "Đang xử lý...",
        "search":         "Tìm kiếm:",
        "zeroRecords":    "Không tìm thấy kết quả phù hợp",
        "paginate": {
            "first":      "Đầu",
            "last":       "Cuối",
            "next":       "›",
            "previous":   "‹"
        },
        "aria": {
            "sortAscending":  ": sắp xếp tăng dần",
            "sortDescending": ": sắp xếp giảm dần"
        }
    }
});

});