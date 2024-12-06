let productionTrendChart;

$(document).ready(function() {
    loadProductionSummary();
    loadRecentProduction();
    initializeChart();

    // Form submission handler
    $('#productionForm').on('submit', function(e) {
        e.preventDefault();
        const formData = $(this).serialize();
        
        $.ajax({
            url: 'api/save_production.php',
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    alert('Production recorded successfully!');
                    loadProductionSummary();
                    loadRecentProduction();
                    $(this).trigger('reset');
                } else {
                    alert('Error: ' + response.error);
                }
            },
            error: function(xhr) {
                alert('Error saving production data');
            }
        });
    });

    // Date change handler
    $('#date').on('change', function() {
        loadProductionForDate($(this).val());
    });
});

function loadProductionSummary() {
    $.ajax({
        url: 'api/get_production_summary.php',
        type: 'GET',
        success: function(data) {
            let html = `
                <tr>
                    <td>Small Boxes</td>
                    <td>${data.small_boxes.today}</td>
                    <td>${data.small_boxes.week}</td>
                    <td>${data.small_boxes.month}</td>
                </tr>
                <tr>
                    <td>Big Boxes</td>
                    <td>${data.big_boxes.today}</td>
                    <td>${data.big_boxes.week}</td>
                    <td>${data.big_boxes.month}</td>
                </tr>
            `;
            $('#summaryData').html(html);
        },
        error: function(xhr) {
            console.error('Error loading production summary:', xhr);
            $('#summaryData').html('<tr><td colspan="4">Error loading data</td></tr>');
        }
    });
}

function loadRecentProduction() {
    $.ajax({
        url: 'api/get_recent_production.php',
        type: 'GET',
        success: function(data) {
            let html = '';
            data.forEach(function(record) {
                html += `
                    <tr>
                        <td>${record.date}</td>
                        <td>${record.small_boxes}</td>
                        <td>${record.big_boxes}</td>
                        <td>${record.total_value}</td>
                        <td>
                            <button onclick="editProduction(${record.id})" class="btn btn-sm btn-primary">Edit</button>
                        </td>
                    </tr>
                `;
            });
            $('#recentProduction').html(html);
            updateChart(data);
        },
        error: function(xhr) {
            console.error('Error loading recent production:', xhr);
            $('#recentProduction').html('<tr><td colspan="5">Error loading data</td></tr>');
        }
    });
}

function loadProductionForDate(date) {
    $.ajax({
        url: 'api/get_production.php',
        type: 'GET',
        data: { date: date },
        success: function(data) {
            $('#small_boxes').val(data.small_boxes);
            $('#big_boxes').val(data.big_boxes);
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
            $('#editDate').val(data.date);
            $('#editModal').modal('show');
        }
    });
}

function updateProduction() {
    const data = {
        id: $('#editId').val(),
        small_boxes: $('#editSmallBoxes').val(),
        big_boxes: $('#editBigBoxes').val(),
        date: $('#editDate').val()
    };

    $.ajax({
        url: 'api/update_production.php',
        type: 'POST',
        data: data,
        success: function(response) {
            if (response.success) {
                $('#editModal').modal('hide');
                loadRecentProduction();
                loadProductionSummary();
            } else {
                alert('Error: ' + response.error);
            }
        },
        error: function() {
            alert('Error updating production');
        }
    });
}

function initializeChart() {
    const ctx = document.getElementById('productionChart').getContext('2d');
    productionTrendChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: [],
            datasets: [
                {
                    label: 'Small Boxes',
                    borderColor: 'rgb(75, 192, 192)',
                    data: []
                },
                {
                    label: 'Big Boxes',
                    borderColor: 'rgb(255, 99, 132)',
                    data: []
                }
            ]
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
}

function updateChart(data) {
    if (data && productionTrendChart) {
        productionTrendChart.data.labels = data.dates;
        productionTrendChart.data.datasets[0].data = data.small_boxes;
        productionTrendChart.data.datasets[1].data = data.big_boxes;
        productionTrendChart.update();
    }
}<script src="assets/js/production-new.js"></script>