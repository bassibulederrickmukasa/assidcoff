$(document).ready(function() {
    $('#userForm').on('submit', function(e) {
        e.preventDefault();
        
        const formData = {
            username: $('#username').val(),
            password: $('#password').val(),
            role: $('#role').val()
        };

        $.ajax({
            url: 'api/add_user.php',
            type: 'POST',
            data: formData,
            success: function(response) {
                alert('User added successfully!');
                location.reload();
            },
            error: function(xhr) {
                alert('Error adding user: ' + xhr.responseText);
            }
        });
    });

    $('#staffForm').on('submit', function(e) {
        e.preventDefault();
        
        const formData = {
            name: $('#staffName').val(),
            contact: $('#contact').val(),
            role: $('#staffRole').val()
        };

        $.ajax({
            url: 'api/add_staff.php',
            type: 'POST',
            data: formData,
            success: function(response) {
                alert('Staff member added successfully!');
                location.reload();
            },
            error: function(xhr) {
                alert('Error adding staff member: ' + xhr.responseText);
            }
        });
    });
});

function deleteUser(userId) {
    if (confirm('Are you sure you want to delete this user?')) {
        $.ajax({
            url: 'api/delete_user.php',
            type: 'POST',
            data: { id: userId },
            success: function(response) {
                alert('User deleted successfully!');
                location.reload();
            },
            error: function(xhr) {
                alert('Error deleting user: ' + xhr.responseText);
            }
        });
    }
}

function deleteStaff(staffId) {
    if (confirm('Are you sure you want to delete this staff member?')) {
        $.ajax({
            url: 'api/delete_staff.php',
            type: 'POST',
            data: { id: staffId },
            success: function(response) {
                alert('Staff member deleted successfully!');
                location.reload();
            },
            error: function(xhr) {
                alert('Error deleting staff member: ' + xhr.responseText);
            }
        });
    }
} 