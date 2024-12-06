$(document).ready(function() {
    loadSupplies();

    $('#supplyForm').on('submit', function(e) {
        e.preventDefault();
        
        const formData = {
            date: $('#date').val(),
            staff_id: $('#staff').val(),
            small_boxes: $('#smallBoxes').val(),
            big_boxes: $('#bigBoxes').val()
        };

        $.ajax({
            url: 'api/save_supply.php',
            type: 'POST',
            data: formData,
            success: function(response) {
                console.log('Save response:', response);
                if (response.success) {
                    alert('Supply recorded successfully!');
                    loadSupplies();
                    $('#supplyForm')[0].reset();
                } else {
                    alert('Error: ' + (response.message || 'Failed to save supply'));
                }
            },
            error: function(xhr) {
                console.error('Save error:', xhr);
                alert('Error recording supply: ' + xhr.responseText);
            }
        });
    });
});

function loadSupplies() {
    const date = $('#date').val() || new Date().toISOString().split('T')[0];
    console.log('Loading supplies for date:', date);
    
    $.ajax({
        url: 'api/get_supplies.php',
        type: 'GET',
        data: { date: date },
        success: function(response) {
            console.log('Load response:', response);
            
            if (!response || typeof response !== 'object') {
                console.error('Invalid response format:', response);
                $('#supplyList').html('Error: Invalid response from server');
                return;
            }

            if (!response.success) {
                console.error('API error:', response.message);
                $('#supplyList').html('Error: ' + (response.message || 'Failed to load supplies'));
                return;
            }

            const data = response.data || {};
            console.log('Data:', data);
            
            const supplies = Array.isArray(data.supplies) ? data.supplies : [];
            const totals = data.totals || {};
            
            console.log('Supplies:', supplies);
            console.log('Totals:', totals);

            let html = `
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Staff</th>
                                <th>Small Boxes</th>
                                <th>Big Boxes</th>
                                <th>Value</th>
                            </tr>
                        </thead>
                        <tbody>
            `;
            
            if (supplies.length === 0) {
                html += '<tr><td colspan="4" class="text-center">No supplies found</td></tr>';
            } else {
                supplies.forEach(supply => {
                    html += `
                        <tr>
                            <td>${supply.staff_name || 'N/A'}</td>
                            <td>${supply.small_boxes || 0}</td>
                            <td>${supply.big_boxes || 0}</td>
                            <td>UGX ${supply.total_value || 0}</td>
                        </tr>
                    `;
                });

                // Add totals row
                html += `
                    <tr class="table-info">
                        <th>Total</th>
                        <th>${totals.total_small_boxes || 0}</th>
                        <th>${totals.total_big_boxes || 0}</th>
                        <th>UGX ${totals.total_value || 0}</th>
                    </tr>
                `;
            }

            html += `
                        </tbody>
                    </table>
                </div>
            `;
            
            $('#supplyList').html(html);
        },
        error: function(xhr) {
            console.error('Load error:', xhr);
            const errorMsg = xhr.responseJSON?.message || xhr.responseText || 'Failed to load supplies';
            $('#supplyList').html('Error: ' + errorMsg);
        }
    });
}