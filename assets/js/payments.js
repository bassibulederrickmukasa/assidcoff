$(document).ready(function() {
    loadPaymentHistory();

    // Update summary when month changes
    // Removed this block as per the new code structure

    $('#paymentForm').on('submit', function(e) {
        e.preventDefault();
        
        // Get form values directly from form elements
        const manager_id = document.getElementById('manager').value;
        const amount = document.getElementById('amount').value;
        const payment_date = document.getElementById('payment_date').value;
        const notes = document.getElementById('notes').value;
        
        // Debug log
        console.log('Submitting form with values:', {
            manager_id,
            amount,
            payment_date,
            notes
        });

        // Basic validation
        if (!manager_id) {
            alert('Please select a manager');
            return;
        }
        if (!amount || amount <= 0) {
            alert('Please enter a valid amount');
            return;
        }
        if (!payment_date) {
            alert('Please select a payment date');
            return;
        }

        // Create form data
        const formData = new FormData();
        formData.append('manager_id', manager_id);
        formData.append('amount', amount);
        formData.append('payment_date', payment_date);
        formData.append('notes', notes);

        // Show loading state
        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.text();
        submitBtn.prop('disabled', true).text('Processing...');

        // Use fetch API instead of jQuery AJAX
        fetch('api/save_payment.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            console.log('Server response:', data);
            if (data.success) {
                alert('Payment recorded successfully!');
                document.getElementById('paymentForm').reset();
                loadPaymentHistory();
                window.location.reload();
            } else {
                alert('Error: ' + (data.error || 'Unknown error occurred'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error recording payment: ' + error.message);
        })
        .finally(() => {
            submitBtn.prop('disabled', false).text(originalText);
        });
    });
});

function loadPaymentHistory() {
    $('#paymentHistory').html('<tr><td colspan="5" class="text-center">Loading...</td></tr>');
    
    fetch('api/get_payment_history.php')
        .then(response => response.json())
        .then(response => {
            if (!response.success || !Array.isArray(response.data)) {
                $('#paymentHistory').html(
                    '<tr><td colspan="5" class="text-center text-danger">' + 
                    (response.error || 'Error loading payment history') + 
                    '</td></tr>'
                );
                return;
            }

            const data = response.data;
            if (data.length === 0) {
                $('#paymentHistory').html(
                    '<tr><td colspan="5" class="text-center">No payments recorded</td></tr>'
                );
                return;
            }

            let html = '';
            data.forEach(function(payment) {
                html += `
                    <tr>
                        <td>${formatDate(payment.payment_date)}</td>
                        <td>${formatCurrency(payment.amount)}</td>
                        <td>${payment.manager_name}</td>
                        <td>${payment.recorded_by}</td>
                        <td>${payment.notes || ''}</td>
                    </tr>
                `;
            });
            $('#paymentHistory').html(html);
        })
        .catch(error => {
            console.error('Error loading payment history:', error);
            $('#paymentHistory').html(
                '<tr><td colspan="5" class="text-center text-danger">Error loading data</td></tr>'
            );
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

// Export function - make it globally accessible
window.exportPayments = function(format) {
    // Show loading state
    const btn = event.target.closest('button');
    const originalText = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Exporting...';

    // Trigger download
    fetch(`api/export_payments.php?format=${format}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Export failed');
            }
            return response.blob();
        })
        .then(blob => {
            // Create download link
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `payment_report.${format}`;
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(url);
            a.remove();
        })
        .catch(error => {
            alert('Error exporting file: ' + error.message);
        })
        .finally(() => {
            // Restore button state
            btn.disabled = false;
            btn.innerHTML = originalText;
        });
};