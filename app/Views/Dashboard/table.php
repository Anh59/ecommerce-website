<?= $this->extend('Dashboard/layout'); ?>
<?= $this->section('content'); ?>

<div class="container-fluid">
    <!-- Loading indicator -->
    <div id="loadingIndicator" class="text-center py-5">
        <div class="spinner-border text-primary" role="status">
            <span class="sr-only">Đang tải dữ liệu...</span>
        </div>
        <p class="mt-2">Đang tải dữ liệu dashboard...</p>
    </div>

    <!-- Dashboard Content (sẽ được điền bằng AJAX) -->
    <div id="dashboardContent" style="display: none;">
        <div class="row">
            <!-- THAY ĐỔI: Tổng thu nhập thay vì tổng sản phẩm -->
            <div class="col-lg-3 col-md-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3 id="totalRevenue">0 ₫</h3>
                        <p>Tổng Thu Nhập</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-cash"></i>
                    </div>
                    <a  class="small-box-footer">Chi tiết <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            
            <!-- Sản phẩm đang hoạt động -->
            <div class="col-lg-3 col-md-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3 id="activeProducts">0</h3>
                        <p>Sản Phẩm Đang Bán</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-checkmark-circled"></i>
                    </div>
                    <a href="<?= route_to('Table_products') ?>" class="small-box-footer">Chi tiết <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            
            <!-- Tổng người dùng -->
            <div class="col-lg-3 col-md-6">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3 id="totalUsers">0</h3>
                        <p>Tổng Người Dùng</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-person-stalker"></i>
                    </div>
                    <a href="<?= route_to('Table_Customers') ?>" class="small-box-footer">Chi tiết <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            
            <!-- Tổng đơn hàng -->
            <div class="col-lg-3 col-md-6">
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3 id="totalOrders">0</h3>
                        <p>Tổng Đơn Hàng</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-ios-cart"></i>
                    </div>
                    <a href="<?= route_to('Table_orders') ?>" class="small-box-footer">Chi tiết <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
        </div>

        <!-- Biểu đồ và bảng -->
        <div class="row">
            <!-- Cột trái -->
            <div class="col-lg-6">
                <!-- THAY ĐỔI: Biểu đồ doanh thu theo tháng thay cho trạng thái kho -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h3 class="card-title">Doanh Thu 6 Tháng Gần Nhất</h3>
                    </div>
                    <div class="card-body">
                        <canvas id="revenue-month-chart" style="height: 300px;"></canvas>
                    </div>
                </div>
                
                <!-- Biểu đồ đơn hàng theo ngày -->
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title">Đơn Hàng Theo Ngày</h3>
                        <form id="monthForm" method="get">
                            <label for="month">Chọn Tháng:</label>
                            <input type="month" name="month" id="month" value="<?= date('Y-m') ?>">
                        </form>
                    </div>
                    <div class="card-body">
                        <canvas id="daily-orders-chart" style="height: 300px;"></canvas>
                    </div>
                </div>
            </div>

            <!-- Cột phải -->
            <div class="col-lg-6">
                <!-- Hàng trên: Biểu đồ tròn trạng thái đơn hàng và bảng Top sản phẩm -->
                <div class="row">
                    <!-- Biểu đồ tỉ lệ trạng thái đơn hàng -->
                    <div class="col-lg-6">
                        <div class="card mb-4">
                            <div class="card-header">
                                <h3 class="card-title">Trạng Thái Đơn Hàng</h3>
                            </div>
                            <div class="card-body">
                                <canvas id="order-status-pie-chart" style="height: 250px;"></canvas>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Bảng Top sản phẩm bán chạy -->
                    <div class="col-lg-6">
                        <div class="card mb-4">
                            <div class="card-header">
                                <h3 class="card-title">Top 5 Sản Phẩm Bán Chạy</h3>
                            </div>
                            <div class="card-body">
                                <div id="topProductsTable">
                                    <table class="table table-bordered table-sm">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Tên Sản Phẩm</th>
                                                <th>Đã Bán</th>
                                            </tr>
                                        </thead>
                                        <tbody id="topProductsBody">
                                            <tr>
                                                <td colspan="3" class="text-center">Đang tải...</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Hàng dưới: Bảng sản phẩm sắp hết hàng -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Sản Phẩm Sắp Hết Hàng</h3>
                    </div>
                    <div class="card-body">
                        <div id="lowStockTable">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>SKU</th>
                                        <th>Tên Sản Phẩm</th>
                                        <th>Tồn Kho</th>
                                        <th>Trạng Thái</th>
                                        <th>Thao Tác</th>
                                    </tr>
                                </thead>
                                <tbody id="lowStockBody">
                                    <tr>
                                        <td colspan="5" class="text-center">Đang tải...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Import thư viện jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
// Biến toàn cục để lưu chart instances
let revenueChart, ordersChart, statusChart;

// Hàm khởi tạo dashboard
function initializeDashboard() {
    console.log('Initializing dashboard...');
    
    // Load dữ liệu ban đầu
    loadDashboardData();

    // Xử lý thay đổi tháng
    document.getElementById('month').addEventListener('change', function() {
        console.log('Month changed to:', this.value);
        loadDashboardData();
    });

    // Auto-refresh mỗi 5 phút
    setInterval(function() {
        console.log('Auto-refreshing dashboard...');
        loadDashboardData();
    }, 300000);
}

function loadDashboardData() {
    const month = document.getElementById('month').value;
    
    const url = '<?= site_url("Dashboard/getDashboardDataAjax") ?>' + (month ? '?month=' + month : '');
    
    console.log('Fetching data from:', url);

    // Hiển thị loading
    document.getElementById('loadingIndicator').style.display = 'block';
    document.getElementById('dashboardContent').style.display = 'none';

    // Sử dụng fetch API
    fetch(url)
        .then(response => {
            console.log('Response status:', response.status);
            if (!response.ok) {
                throw new Error('Network response was not ok: ' + response.status);
            }
            return response.json();
        })
        .then(data => {
            console.log('Data received:', data);
            if (data.success) {
                updateDashboard(data.data);
                document.getElementById('loadingIndicator').style.display = 'none';
                document.getElementById('dashboardContent').style.display = 'block';
            } else {
                showError('Lỗi khi tải dữ liệu: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Fetch error:', error);
            showError('Lỗi kết nối: ' + error.message);
        });
}

function updateDashboard(data) {
    console.log('Updating dashboard with data:', data);
    
    // THAY ĐỔI: Cập nhật tổng thu nhập với định dạng tiền tệ
    document.getElementById('totalRevenue').textContent = formatCurrency(data.totalRevenue || 0);
    document.getElementById('activeProducts').textContent = data.activeProducts || 0;
    document.getElementById('totalUsers').textContent = data.totalUsers || 0;
    document.getElementById('totalOrders').textContent = data.totalOrders || 0;

    // Cập nhật biểu đồ
    updateCharts(data);

    // Cập nhật bảng top products
    updateTopProductsTable(data.topProducts);

    // Cập nhật bảng low stock products
    updateLowStockTable(data.lowStockProducts);
}

function updateCharts(data) {
    console.log('Updating charts...');
    
    // Hủy các chart cũ nếu tồn tại
    if (revenueChart) revenueChart.destroy();
    if (ordersChart) ordersChart.destroy();
    if (statusChart) statusChart.destroy();

    // THAY ĐỔI: Biểu đồ doanh thu theo tháng
    const revenueLabels = data.revenueByMonthData.map(item => {
        const [year, month] = item.month.split('-');
        return `Tháng ${month}/${year}`;
    });
    const revenueValues = data.revenueByMonthData.map(item => item.monthly_revenue || 0);

    const revenueCtx = document.getElementById('revenue-month-chart').getContext('2d');
    revenueChart = new Chart(revenueCtx, {
        type: 'bar',
        data: {
            labels: revenueLabels,
            datasets: [{
                label: 'Doanh Thu (VND)',
                data: revenueValues,
                backgroundColor: '#28a745',
                borderColor: '#28a745',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return formatCurrency(value);
                        }
                    }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'Doanh thu: ' + formatCurrency(context.parsed.y);
                        }
                    }
                }
            }
        }
    });

    // Biểu đồ đơn hàng theo ngày
    const orderLabels = data.dailyOrdersData.map(item => item.date);
    const orderValues = data.dailyOrdersData.map(item => item.total_orders);

    const ordersCtx = document.getElementById('daily-orders-chart').getContext('2d');
    ordersChart = new Chart(ordersCtx, {
        type: 'line',
        data: {
            labels: orderLabels,
            datasets: [{
                label: 'Số Đơn Hàng',
                data: orderValues,
                borderColor: '#4e73df',
                backgroundColor: 'rgba(78, 115, 223, 0.1)',
                fill: true,
                tension: 0.3
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Biểu đồ tròn trạng thái đơn hàng
    const statusLabels = [];
    const statusValues = [];
    const statusColors = {
        'pending': '#ffc107',
        'processing': '#17a2b8', 
        'shipped': '#fd7e14',
        'delivered': '#28a745',
        'cancelled': '#dc3545'
    };

    const statusNames = {
        'pending': 'Chờ Xử Lý',
        'processing': 'Đang Xử Lý',
        'shipped': 'Đã Gửi',
        'delivered': 'Đã Giao',
        'cancelled': 'Đã Hủy'
    };

    Object.keys(data.orderStatusData).forEach(status => {
        statusLabels.push(statusNames[status] || status);
        statusValues.push(data.orderStatusData[status]);
    });

    const statusCtx = document.getElementById('order-status-pie-chart').getContext('2d');
    statusChart = new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: statusLabels,
            datasets: [{
                data: statusValues,
                backgroundColor: Object.keys(data.orderStatusData).map(status => statusColors[status] || '#6c757d'),
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
}

function updateTopProductsTable(products) {
    const tbody = document.getElementById('topProductsBody');
    tbody.innerHTML = '';

    if (products && products.length > 0) {
        products.forEach((product, index) => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${index + 1}</td>
                <td>${escapeHtml(product.name)}</td>
                <td><span class="badge badge-success">${product.total_sold || 0}</span></td>
            `;
            tbody.appendChild(row);
        });
    } else {
        tbody.innerHTML = '<tr><td colspan="3" class="text-center">Chưa có dữ liệu</td></tr>';
    }
}

function updateLowStockTable(products) {
    const tbody = document.getElementById('lowStockBody');
    tbody.innerHTML = '';

    if (products && products.length > 0) {
        products.forEach(product => {
            const statusClass = getStockStatusClass(product.stock_status);
            const statusText = getStockStatusText(product.stock_status);
            
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${escapeHtml(product.sku)}</td>
                <td>${escapeHtml(product.name)}</td>
                <td>
                    <span class="badge ${product.stock_quantity <= 0 ? 'badge-danger' : 'badge-warning'}">
                        ${product.stock_quantity}
                    </span>
                </td>
                <td>
                    <span class="badge ${statusClass}">${statusText}</span>
                </td>
                <td>
                    <a href="<?= site_url('admin/products/edit/') ?>${product.id}" 
                       class="btn btn-sm btn-primary" title="Chỉnh sửa">
                        <i class="fas fa-edit"></i>
                    </a>
                </td>
            `;
            tbody.appendChild(row);
        });
    } else {
        tbody.innerHTML = '<tr><td colspan="5" class="text-center">Không có sản phẩm nào sắp hết hàng</td></tr>';
    }
}

// THÊM MỚI: Hàm định dạng tiền tệ
function formatCurrency(amount) {
    return new Intl.NumberFormat('vi-VN', {
        style: 'currency',
        currency: 'VND'
    }).format(amount);
}

function getStockStatusClass(status) {
    switch(status) {
        case 'out_of_stock': return 'badge-danger';
        case 'low_stock': return 'badge-warning';
        case 'in_stock': return 'badge-success';
        default: return 'badge-secondary';
    }
}

function getStockStatusText(status) {
    switch(status) {
        case 'out_of_stock': return 'Hết hàng';
        case 'low_stock': return 'Sắp hết';
        case 'in_stock': return 'Còn hàng';
        default: return 'Không xác định';
    }
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function showError(message) {
    const loadingIndicator = document.getElementById('loadingIndicator');
    loadingIndicator.innerHTML = `
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-triangle"></i> ${message}
            <button class="btn btn-sm btn-primary ml-2" onclick="loadDashboardData()">Thử lại</button>
        </div>
    `;
}

// Khởi tạo dashboard khi trang load xong
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, initializing dashboard...');
    initializeDashboard();
});
</script>

<?= $this->endSection(); ?>