$(document).ready(function() {
    $('#reportForm').on('submit', function(e) {
        e.preventDefault();
        generateReport();
    });

    $('#exportBtn').on('click', function() {
        exportReport();
    });
});

function generateReport() {
    const data = {
        type: $('#reportType').val(),
        startDate: $('#startDate').val(),
        endDate: $('#endDate').val()
    };

    $.ajax({
        url: 'api/generate_report.php',
        type: 'GET',
        data: data,
        success: function(response) {
            displayReport(response);
        },
        error: function(xhr) {
            alert('Error generating report: ' + xhr.responseText);
        }
    });
}

function displayReport(data) {
    let html = '<table class="table table-striped">';
    
    // Add headers based on report type
    switch($('#reportType').val()) {
        case 'production':
            html += `<thead><tr>
                <th>Date</th>
                <th>Small Boxes</th>
                <th>Big Boxes</th>
                <th>Total Value</th>
            </tr></thead>`;
            break;
        case 'supplies':
            html += `<thead><tr>
                <th>Date</th>
                <th>Staff</th>
                <th>Small Boxes</th>
                <th>Big Boxes</th>
                <th>Value</th>
            </tr></thead>`;
            break;
        // Add other cases...
    }

    html += '<tbody>';
    data.forEach(row => {
        html += '<tr>';
        Object.values(row).forEach(value => {
            html += `<td>${value}</td>`;
        });
        html += '</tr>';
    });
    html += '</tbody></table>';

    $('#reportResults').html(html);
}

function exportReport() {
    const data = {
        type: $('#reportType').val(),
        startDate: $('#startDate').val(),
        endDate: $('#endDate').val(),
        export: true
    };

    window.location.href = `api/generate_report.php?${$.param(data)}`;
} 