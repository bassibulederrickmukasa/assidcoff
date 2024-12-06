$(document).ready(function() {
    loadSystemInfo();

    $('#priceForm').on('submit', function(e) {
        e.preventDefault();
        
        const formData = {
            small_price: $('#smallBoxPrice').val(),
            big_price: $('#bigBoxPrice').val()
        };

        $.ajax({
            url: 'api/update_prices.php',
            type: 'POST',
            data: formData,
            success: function(response) {
                alert('Prices updated successfully!');
            },
            error: function(xhr) {
                alert('Error updating prices: ' + xhr.responseText);
            }
        });
    });

    $('#passwordForm').on('submit', function(e) {
        e.preventDefault();
        
        if ($('#newPassword').val() !== $('#confirmPassword').val()) {
            alert('New passwords do not match!');
            return;
        }

        const formData = {
            current_password: $('#currentPassword').val(),
            new_password: $('#newPassword').val()
        };

        $.ajax({
            url: 'api/change_password.php',
            type: 'POST',
            data: formData,
            success: function(response) {
                alert('Password changed successfully!');
                $('#passwordForm')[0].reset();
            },
            error: function(xhr) {
                alert('Error changing password: ' + xhr.responseText);
            }
        });
    });
});

function loadSystemInfo() {
    $.ajax({
        url: 'api/get_system_info.php',
        type: 'GET',
        success: function(data) {
            $('#dbSize').text(data.db_size);
            $('#totalRecords').text(data.total_records);
            $('#lastBackup').text(data.last_backup || 'Never');
        },
        error: function() {
            $('#dbSize').text('Error loading');
            $('#totalRecords').text('Error loading');
            $('#lastBackup').text('Error loading');
        }
    });
}

function backupDatabase() {
    window.location.href = 'api/backup_database.php';
} 