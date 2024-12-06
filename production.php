<?php
require_once 'config/config.php';
session_start();
require_once 'config/database.php';
require_once 'includes/security.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Production Management - <?php echo SYSTEM_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        #debugPanel {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            max-height: 300px;
            overflow-y: auto;
            background-color: #f8f9fa;
            border-top: 1px solid #dee2e6;
            padding: 10px;
            display: none;
        }
    </style>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="card-title">Production Records</h5>
                            <div class="d-flex align-items-center">
                                <div class="search-box me-2">
                                    <input type="text" class="form-control" placeholder="Search..." id="searchInput">
                                </div>
                                <div class="btn-group me-2">
                                    <button type="button" class="btn btn-outline-primary filter-btn" data-filter="today">Today</button>
                                    <button type="button" class="btn btn-outline-primary filter-btn" data-filter="week">This Week</button>
                                    <button type="button" class="btn btn-outline-primary filter-btn" data-filter="month">This Month</button>
                                    <button type="button" class="btn btn-outline-primary filter-btn" data-filter="all">All</button>
                                </div>
                                <?php if ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'staff'): ?>
                                <button class="btn btn-primary ms-2" data-bs-toggle="modal" data-bs-target="#addProductionModal">
                                    <i class="fas fa-plus"></i> Add Production
                                </button>
                                <?php endif; ?>
                                <button class="btn btn-secondary ms-2" id="toggleDebugBtn">
                                    <i class="fas fa-bug"></i> Debug
                                </button>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th><input type="checkbox" class="form-check-input" id="selectAll"></th>
                                        <th>Production ID</th>
                                        <th>Staff Name</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Date</th>
                                        <th>Small Boxes</th>
                                        <th>Big Boxes</th>
                                        <th>Type</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="productionRecords">
                                    <tr>
                                        <td colspan="11" class="text-center">Loading production records...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div class="entries-info">
                                Showing <span id="startEntry">1</span> to <span id="endEntry">10</span> of <span id="totalEntries">0</span> entries
                            </div>
                            <nav aria-label="Page navigation">
                                <ul class="pagination mb-0">
                                    <li class="page-item">
                                        <a class="page-link" href="#" onclick="previousPage()">Previous</a>
                                    </li>
                                    <li class="page-item active"><a class="page-link" href="#">1</a></li>
                                    <li class="page-item">
                                        <a class="page-link" href="#" onclick="nextPage()">Next</a>
                                    </li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Debug Panel -->
    <div id="debugPanel" class="text-dark">
        <h6>Debug Information</h6>
        <pre id="debugContent" class="small"></pre>
    </div>

    <!-- Add Production Modal -->
    <div class="modal fade" id="addProductionModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Production Record</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="productionForm">
                        <div class="mb-3">
                            <label class="form-label">Date</label>
                            <input type="date" class="form-control" id="date" name="date" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Small Boxes</label>
                            <input type="number" class="form-control" id="smallBoxes" name="small_boxes" min="0" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Big Boxes</label>
                            <input type="number" class="form-control" id="bigBoxes" name="big_boxes" min="0" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="saveProduction()">Save Record</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    let currentPage = 1;
    let currentFilter = 'today';
    let debugMode = false;

    $(document).ready(function() {
        // Set default date to today
        $('#date').val(new Date().toISOString().split('T')[0]);

        // Initialize with current date and today's filter
        loadProduction();
        
        // Add event listener for filter changes
        $('.filter-btn').on('click', function() {
            currentFilter = $(this).data('filter');
            currentPage = 1;
            loadProduction();
        });

        // Add event listener for search
        $('#searchInput').on('keyup', function() {
            currentPage = 1;
            loadProduction();
        });

        // Debug toggle
        $('#toggleDebugBtn').on('click', function() {
            debugMode = !debugMode;
            $('#debugPanel').toggle(debugMode);
        });
    });

    function loadProduction() {
        const searchQuery = $('#searchInput').val();
        
        $.ajax({
            url: 'api/get_production.php',
            method: 'GET',
            data: {
                page: currentPage,
                limit: 10,
                filter: currentFilter,
                search: searchQuery
            },
            success: function(response) {
                // Comprehensive logging
                console.log('FULL API Response:', JSON.stringify(response, null, 2)); 
                
                // Update debug panel if in debug mode
                if (debugMode) {
                    $('#debugContent').text(JSON.stringify(response, null, 2));
                    $('#debugPanel').show();
                }
                
                if (response.success) {
                    if (response.data && response.data.length > 0) {
                        displayProduction(response.data);
                        updatePagination(response.pagination);
                    } else {
                        $('#productionRecords').html(`
                            <tr>
                                <td colspan="11" class="text-center text-warning">
                                    No production records found. 
                                    <br>Check filter or add a new record.
                                </td>
                            </tr>
                        `);
                    }
                } else {
                    $('#productionRecords').html(`
                        <tr>
                            <td colspan="11" class="text-center text-danger">
                                ${response.message || 'Error retrieving records'}
                            </td>
                        </tr>
                    `);
                    console.error('Response Error:', response);
                }
            },
            error: function(xhr, status, error) {
                console.error('Complete API Error:', {
                    status: status,
                    error: error,
                    responseText: xhr.responseText
                });
                
                try {
                    const errorResponse = JSON.parse(xhr.responseText);
                    $('#productionRecords').html(`
                        <tr>
                            <td colspan="11" class="text-center text-danger">
                                ${errorResponse.message || 'Unexpected error loading production records'}
                            </td>
                        </tr>
                    `);
                } catch {
                    $('#productionRecords').html(`
                        <tr>
                            <td colspan="11" class="text-center text-danger">
                                Unexpected error loading production records
                            </td>
                        </tr>
                    `);
                }
            }
        });
    }

    function displayProduction(records) {
        const tbody = $('#productionRecords');
        tbody.empty();
        
        records.forEach(record => {
            const row = `
                <tr>
                    <td><input type="checkbox" class="form-check-input production-checkbox"></td>
                    <td>#${record.id}</td>
                    <td>${record.staff_name || '-'}</td>
                    <td>${record.email || '-'}</td>
                    <td>${record.phone || '-'}</td>
                    <td>${record.date}</td>
                    <td>${record.small_boxes}</td>
                    <td>${record.big_boxes}</td>
                    <td>
                        <span class="badge ${record.type === 'Mixed' ? 'bg-purple' : 
                                          record.type === 'Small' ? 'bg-primary' : 
                                          'bg-success'}">${record.type}</span>
                    </td>
                    <td>
                        <span class="badge ${record.status === 'Completed' ? 'bg-success' : 'bg-warning'}">
                            ${record.status}
                        </span>
                    </td>
                    <td>
                        <div class="btn-group">
                            <button class="btn btn-sm btn-outline-primary" onclick="viewProduction(${record.id})">
                                <i class="fas fa-eye"></i>
                            </button>
                            ${record.can_edit ? `
                            <button class="btn btn-sm btn-outline-warning" onclick="editProduction(${record.id})">
                                <i class="fas fa-edit"></i>
                            </button>
                            ` : ''}
                        </div>
                    </td>
                </tr>
            `;
            tbody.append(row);
        });
    }

    function updatePagination(pagination) {
        if (!pagination) {
            console.warn('No pagination data provided');
            return;
        }

        const page = pagination.page || 1;
        const total_pages = pagination.total_pages || 1;
        const total = pagination.total || 0;
        
        const start = ((page - 1) * 10) + 1;
        const end = Math.min(page * 10, total);
        
        $('#startEntry').text(start);
        $('#endEntry').text(end);
        $('#totalEntries').text(total);
        
        const paginationUl = $('.pagination');
        paginationUl.empty();
        
        paginationUl.append(`
            <li class="page-item ${page === 1 ? 'disabled' : ''}">
                <a class="page-link" href="#" onclick="changePage(${page - 1})">Previous</a>
            </li>
        `);
        
        for (let i = 1; i <= total_pages; i++) {
            if (i === page || (i === 1) || (i === total_pages) || (i >= page - 1 && i <= page + 1)) {
                paginationUl.append(`
                    <li class="page-item ${i === page ? 'active' : ''}">
                        <a class="page-link" href="#" onclick="changePage(${i})">${i}</a>
                    </li>
                `);
            } else if (i === page - 2 || i === page + 2) {
                paginationUl.append('<li class="page-item disabled"><a class="page-link">...</a></li>');
            }
        }
        
        paginationUl.append(`
            <li class="page-item ${page === total_pages ? 'disabled' : ''}">
                <a class="page-link" href="#" onclick="changePage(${page + 1})">Next</a>
            </li>
        `);
    }

    function changePage(page) {
        currentPage = page;
        loadProduction();
    }

    function viewProduction(id) {
        // Implement view production details
        alert('View production details for ID: ' + id);
    }

    function editProduction(id) {
        // Implement edit production
        alert('Edit production record ID: ' + id);
    }

    function saveProduction() {
        const date = $('#date').val();
        const smallBoxes = $('#smallBoxes').val();
        const bigBoxes = $('#bigBoxes').val();

        if (!date || !smallBoxes || !bigBoxes) {
            alert('Please fill in all fields');
            return;
        }

        $.ajax({
            url: 'api/save_production.php',
            method: 'POST',
            data: {
                date: date,
                small_boxes: smallBoxes,
                big_boxes: bigBoxes
            },
            success: function(response) {
                if (response.success) {
                    $('#addProductionModal').modal('hide');
                    $('#productionForm')[0].reset();
                    loadProduction();
                    alert('Production record added successfully');
                } else {
                    alert(response.message || 'Error adding production record');
                }
            },
            error: function(xhr, status, error) {
                console.error('Save Error:', error);
                alert('Error saving production record');
            }
        });
    }
    </script>
</body>
</html>