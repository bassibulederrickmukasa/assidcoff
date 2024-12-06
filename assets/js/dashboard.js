let productionChart, paymentChart;

$(document).ready(function() {
    initializeCharts();
    loadDashboardData();
    
    // Refresh data every 5 minutes
    setInterval(loadDashboardData, 300000);
});

function initializeCharts() {
    // Production Chart
    const productionCtx = document.getElementById('productionChart').getContext('2d');
    productionChart = new Chart(productionCtx, {
        type: 'line',
        data: {
            labels: [],
            datasets: [{
                label: 'Small Boxes',
                borderColor: '#36a2eb',
                data: []
            }, {
                label: 'Big Boxes',
                borderColor: '#ff6384',
                data: []
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

    // Payment Chart
    const paymentCtx = document.getElementById('paymentChart').getContext('2d');
    paymentChart = new Chart(paymentCtx, {
        type: 'bar',
        data: {
            labels: [],
            datasets: [{
                label: 'Daily Payments (UGX)',
                backgroundColor: '#4bc0c0',
                data: []
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'UGX ' + value.toLocaleString();
                        }
                    }
                }
            }
        }
    });
}

function loadDashboardData() {
    $.ajax({
        url: 'api/dashboard_data.php',
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            if (data.error) {
                console.error('Server error:', data.error);
                return;
            }
            try {
                updateDashboardCards(data.today);
                updateCharts(data.trends);
                if (data.system) {
                    updateSystemOverview(data.system);
                }
            } catch (e) {
                console.error('Error processing data:', e);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error loading dashboard data:', {
                status: status,
                error: error,
                response: xhr.responseText
            });
        }
    });
}

function updateDashboardCards(data) {
    if (!data) return;
    
    $('#smallBoxes').text(data.production?.small_boxes || 0);
    $('#bigBoxes').text(data.production?.big_boxes || 0);
    $('#smallStock').text(data.stock?.small_boxes || 0);
    $('#bigStock').text(data.stock?.big_boxes || 0);
    $('#smallSupplies').text(data.supplies?.small_boxes || 0);
    $('#bigSupplies').text(data.supplies?.big_boxes || 0);
    $('#todayPayments').text((data.payments?.amount || 0).toLocaleString());
    $('#currentBalance').text((data.payments?.balance || 0).toLocaleString());
}

function updateCharts(trends) {
    if (!trends) return;
    
    // Update Production Chart
    if (productionChart) {
        productionChart.data.labels = trends.dates || [];
        productionChart.data.datasets[0].data = trends.small_boxes || [];
        productionChart.data.datasets[1].data = trends.big_boxes || [];
        productionChart.update();
    }

    // Update Payment Chart
    if (paymentChart) {
        paymentChart.data.labels = trends.dates || [];
        paymentChart.data.datasets[0].data = trends.payments || [];
        paymentChart.update();
    }
}

function updateSystemOverview(data) {
    if (!data) return;
    
    $('#totalUsers').text(data.total_users || 0);
    $('#totalRevenue').text('UGX ' + (data.total_revenue || 0).toLocaleString());
    $('#activeStaff').text(data.active_staff || 0);
} 