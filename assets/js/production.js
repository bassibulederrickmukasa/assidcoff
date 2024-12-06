let productionTrendChart;

$(document).ready(function() {
    loadProductionSummary();
    loadRecentProduction();
    initializeChart();

    // Set max date to today
    $('#date').attr('max', new Date().toISOString().split('T')[0]);

    $('#productionForm').on('submit', function(e) {
        e.preventDefault();
        
        const formData = {
            date: $('#date').val(), // This will be YYYY-MM-DD format
            small_boxes: parseInt($('#smallBoxes').val()) || 0,
            big_boxes: parseInt($('#bigBoxes').val()) || 0
        };

        // Validate inputs
        if (formData.small_boxes < 0 || formData.big_boxes < 0) {
            alert('Box quantities cannot be negative');
            return;
        }

        // Show loading state
        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.text();
        submitBtn.prop('disabled', true).text('Saving...');

        $.ajax({
            url: 'api/save_production.php',
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    alert('Production saved successfully!');
                    loadProductionSummary();
                    loadRecentProduction();
                    updateChart();
                } else {
                    alert('Error: ' + (response.error || 'Unknown error occurred'));
                }
            },
            error: function(xhr) {
                console.error('Error:', xhr);
                alert('Error saving production: ' + (xhr.responseText || 'Unknown error occurred'));
            },
            complete: function() {
                submitBtn.prop('disabled', false).text(originalText);
            }
        });
    });

    $('#date').on('change', function() {
        loadProductionForDate($(this).val());
    });
});

function loadProductionSummary() {
    $.ajax({
        url: 'api/get_production_summary.php',
        type: 'GET',
        success: function(data) {
            if (!data.today || !data.week || !data.month) {
                console.error('Invalid data format:', data);
                $('#summaryData').html('<tr><td colspan="4" class="text-center text-danger">Error: Invalid data format</td></tr>');
                return;
            }

            $('#summaryData').html(`
                <tr>
                    <td>Small Boxes</td>
                    <td>${data.today.small_boxes}</td>
                    <td>${data.week.small_boxes}</td>
                    <td>${data.month.small_boxes}</td>
                </tr>
                <tr>
                    <td>Big Boxes</td>
                    <td>${data.today.big_boxes}</td>
                    <td>${data.week.big_boxes}</td>
                    <td>${data.month.big_boxes}</td>
                </tr>
            `);
        },
        error: function(xhr) {
            console.error('Error loading summary:', xhr);
            $('#summaryData').html('<tr><td colspan="4" class="text-center text-danger">Error loading data</td></tr>');
        }
    });
}

function loadRecentProduction() {
    $('#recentProduction').html('<tr><td colspan="5" class="text-center">Loading...</td></tr>');
    
    $.ajax({
        url: 'api/get_recent_production.php',
        type: 'GET',
        success: function(response) {
            console.log('Response:', response); // Debug log
            
            // Check if response is valid
            if (typeof response === 'string') {
                try {
                    response = JSON.parse(response);
                } catch (e) {
                    console.error('Failed to parse response:', e);
                    $('#recentProduction').html('<tr><td colspan="5" class="text-center text-danger">Error: Invalid response format</td></tr>');
                    return;
                }
            }

            // Handle error responses
            if (!response.success) {
                console.error('Server error:', response.error);
                $('#recentProduction').html('<tr><td colspan="5" class="text-center text-danger">Error: ' + (response.error || 'Unknown error') + '</td></tr>');
                return;
            }

            // Validate data array
            if (!response.data || !Array.isArray(response.data)) {
                console.error('Invalid data format:', response);
                $('#recentProduction').html('<tr><td colspan="5" class="text-center text-danger">Error: Invalid data format</td></tr>');
                return;
            }

            const data = response.data;
            if (data.length === 0) {
                $('#recentProduction').html('<tr><td colspan="5" class="text-center">No production records found</td></tr>');
                return;
            }

            let html = '';
            data.forEach(function(record) {
                html += `
                    <tr>
                        <td>${formatDate(record.date)}</td>
                        <td>${record.small_boxes}</td>
                        <td>${record.big_boxes}</td>
                        <td>${formatCurrency(record.total_value)}</td>
                        <td>
                            <button class="btn btn-sm btn-primary" onclick="editProduction(${record.id})">
                                Edit
                            </button>
                        </td>
                    </tr>
                `;
            });
            $('#recentProduction').html(html);
        },
        error: function(xhr) {
            console.error('Error loading recent production:', xhr);
            $('#recentProduction').html('<tr><td colspan="5" class="text-center text-danger">Error loading data</td></tr>');
        }
    });
}

function loadProductionForDate(date) {
    $.ajax({
        url: 'api/get_production.php',
        type: 'GET',
        data: { date: date },
        success: function(data) {
            $('#smallBoxes').val(data.small_boxes || 0);
            $('#bigBoxes').val(data.big_boxes || 0);
        },
        error: function(xhr) {
            console.error('Error loading production for date:', xhr);
            alert('Error loading production data for selected date');
        }
    });
}

function editProduction(id) {
    $.ajax({
        url: 'api/get_production.php',
        type: 'GET',
        data: { id: id },
        success: function(data) {
            $('#editId').val(id);
            $('#editSmallBoxes').val(data.small_boxes);
            $('#editBigBoxes').val(data.big_boxes);
            $('#editProductionModal').modal('show');
        },
        error: function(xhr) {
            console.error('Error loading production:', xhr);
            alert('Error loading production data');
        }
    });
}

function updateProduction() {
    const data = {
        id: $('#editId').val(),
        small_boxes: parseInt($('#editSmallBoxes').val()) || 0,
        big_boxes: parseInt($('#editBigBoxes').val()) || 0
    };

    // Validate inputs
    if (data.small_boxes < 0 || data.big_boxes < 0) {
        alert('Box quantities cannot be negative');
        return;
    }

    $.ajax({
        url: 'api/update_production.php',
        type: 'POST',
        data: data,
        success: function(response) {
            if (response.success) {
                $('#editProductionModal').modal('hide');
                loadProductionSummary();
                loadRecentProduction();
                updateChart();
            } else {
                alert('Error: ' + (response.error || 'Unknown error occurred'));
            }
        },
        error: function(xhr) {
            console.error('Error updating production:', xhr);
            alert('Error updating production: ' + (xhr.responseText || 'Unknown error occurred'));
        }
    });
}

function initializeChart() {
    const ctx = document.getElementById('productionChart').getContext('2d');
    productionTrendChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: [],
            datasets: [{
                label: 'Small Boxes',
                borderColor: 'rgb(75, 192, 192)',
                data: [],
                tension: 0.1
            }, {
                label: 'Big Boxes',
                borderColor: 'rgb(255, 99, 132)',
                data: [],
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    }
                }
            },
            plugins: {
                legend: {
                    position: 'top'
                }
            }
        }
    });
    updateChart();
}

function updateChart() {
    $.ajax({
        url: 'api/get_production_trend.php',
        type: 'GET',
        success: function(data) {
            if (!data.dates || !data.small_boxes || !data.big_boxes) {
                console.error('Invalid chart data format:', data);
                return;
            }
            
            productionTrendChart.data.labels = data.dates;
            productionTrendChart.data.datasets[0].data = data.small_boxes;
            productionTrendChart.data.datasets[1].data = data.big_boxes;
            productionTrendChart.update();
        },
        error: function(xhr) {
            console.error('Error updating chart:', xhr);
        }
    });
}

function formatDate(dateStr) {
    const date = new Date(dateStr);
    return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
}

function formatCurrency(amount) {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'UGX',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    }).format(amount);
}