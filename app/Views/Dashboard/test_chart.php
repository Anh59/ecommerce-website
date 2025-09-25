<?= $this->extend('Dashboard/layout'); ?>
<?= $this->section('content'); ?>

<div class="content-wrapper">
    <div class="container-fluid">
        <h1>Test Chart.js</h1>
        
        <div class="row">
            <div class="col-md-6">
                <canvas id="testChart" width="400" height="200"></canvas>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<script>
console.log('=== TEST CHART.JS ===');
console.log('Chart available:', typeof Chart !== 'undefined');

if (typeof Chart !== 'undefined') {
    console.log('Chart version:', Chart.version);
    
    const ctx = document.getElementById('testChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Red', 'Blue', 'Yellow', 'Green', 'Purple', 'Orange'],
            datasets: [{
                label: 'Test Data',
                data: [12, 19, 3, 5, 2, 3],
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
    console.log('✅ Test chart created successfully');
} else {
    console.error('❌ Chart.js not available');
}
</script>
<?= $this->endSection(); ?>