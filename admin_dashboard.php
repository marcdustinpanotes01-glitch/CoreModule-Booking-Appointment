<?php
include_once("connection/connection.php");
$con = connection();

$sql = "SELECT 
    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_total,
    SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as approved_total,
    SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed_total,
    SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled_total
FROM customer";

$query = $con->query($sql);
$stats = $query->fetch_assoc();

// Store the counts in variables
$pendingCount = $stats['pending_total'];
$approvedCount = $stats['approved_total'];
$completedCount = $stats['completed_total'];
$cancelledCount = $stats['cancelled_total'];

// Display status message if any
if (isset($_SESSION['status_message'])) {
    echo "<div class='alert " . (strpos($_SESSION['status_message'], 'Error') !== false ? 'alert-danger' : 'alert-success') . "'>";
    echo $_SESSION['status_message'];
    echo "</div>";
    unset($_SESSION['status_message']);
}

// Build the SQL query with filters
$sql = "SELECT * FROM customer WHERE 1=1";
$params = array();
$types = "";

// Status filter
if (isset($_GET['status']) && !empty($_GET['status'])) {
    $sql .= " AND status = ?";
    $params[] = $_GET['status'];
    $types .= "s";
}

// Date range filter
if (isset($_GET['date_from']) && !empty($_GET['date_from'])) {
    $sql .= " AND appointment_date >= ?";
    $params[] = $_GET['date_from'];
    $types .= "s";
}

if (isset($_GET['date_to']) && !empty($_GET['date_to'])) {
    $sql .= " AND appointment_date <= ?";
    $params[] = $_GET['date_to'];
    $types .= "s";
}

// Search filter
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $sql .= " AND (first_name LIKE ? OR last_name LIKE ?)";
    $searchTerm = "%" . $_GET['search'] . "%";
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $types .= "ss";
}

// Add the order by clause
$sql .= " ORDER BY appointment_date DESC";

// Prepare and execute the statement
$stmt = $con->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$appointments = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="css/admin/admin_styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="nav-bar">
        <div class="nav-container">
            <a href="admin_dashboard.php" class="nav-link active">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>
            <a href="reports.php" class="nav-link">
                <i class="fas fa-chart-bar"></i> Reports
            </a>
        </div>
    </nav>

    <div class="admin-container">
        <h1>Admin Dashboard</h1>
        
        <!-- Statistics Cards -->
        <div class="stats-grid">
            <div class="stat-card pending">
                <h3>Pending</h3>
                <div class="stat-number"><?php echo $pendingCount; ?></div>
            </div>
            
            <div class="stat-card approved">
                <h3>Approved</h3>
                <div class="stat-number"><?php echo $approvedCount; ?></div>
            </div>
            
            <div class="stat-card completed">
                <h3>Completed</h3>
                <div class="stat-number"><?php echo $completedCount; ?></div>
            </div>
            
            <div class="stat-card cancelled">
                <h3>Cancelled</h3>
                <div class="stat-number"><?php echo $cancelledCount; ?></div>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="filter-container">
            <form method="GET" class="filter-form">
                <div class="filter-group">
                    <label for="status">Status</label>
                    <select name="status" id="status" class="filter-select">
                        <option value="">All Status</option>
                        <option value="pending" <?php echo isset($_GET['status']) && $_GET['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                        <option value="approved" <?php echo isset($_GET['status']) && $_GET['status'] == 'approved' ? 'selected' : ''; ?>>Approved</option>
                        <option value="completed" <?php echo isset($_GET['status']) && $_GET['status'] == 'completed' ? 'selected' : ''; ?>>Completed</option>
                        <option value="cancelled" <?php echo isset($_GET['status']) && $_GET['status'] == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                    </select>
                </div>

                <div class="filter-group">
                    <label for="date_from">Date From</label>
                    <input type="date" id="date_from" name="date_from" class="filter-input" 
                           value="<?php echo isset($_GET['date_from']) ? htmlspecialchars($_GET['date_from']) : ''; ?>">
                </div>

                <div class="filter-group">
                    <label for="date_to">Date To</label>
                    <input type="date" id="date_to" name="date_to" class="filter-input"
                           value="<?php echo isset($_GET['date_to']) ? htmlspecialchars($_GET['date_to']) : ''; ?>">
                </div>

                <div class="filter-group">
                    <label for="search">Search Clients</label>
                    <input type="text" id="search" name="search" class="filter-input" placeholder="Search by name..."
                           value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                </div>

                <div class="filter-buttons">
                    <button type="submit" class="filter-btn apply">
                        <i class="fas fa-filter"></i> Apply Filters
                    </button>
                    <a href="admin_dashboard.php" class="filter-btn reset">
                        <i class="fas fa-undo"></i> Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- Appointments Table -->
        <div class="table-container">
            <h2>Appointment List</h2>
            <table class="appointment-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($appointments->num_rows > 0): ?>
                        <?php while($row = $appointments->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $row['id']; ?></td>
                                
                                <td><button type="button" class="view-btn" onclick="showDetails(this)" 
                                        data-name="<?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?>"
                                        data-address="<?php echo htmlspecialchars($row['address']); ?>"
                                        data-phone="<?php echo htmlspecialchars($row['phone_number']); ?>"
                                        data-date="<?php echo htmlspecialchars($row['appointment_date']); ?>"
                                        data-time="<?php echo htmlspecialchars($row['appointment_time']); ?>"
                                        data-service="<?php echo htmlspecialchars($row['service_type']); ?>"
                                        data-type="<?php echo htmlspecialchars($row['appointment_type']); ?>"
                                        data-status="<?php echo htmlspecialchars($row['status']); ?>">
                                        <i class="fas fa-eye"></i> View
                                    </button><?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['appointment_date']); ?></td>
                                <td><?php echo htmlspecialchars($row['appointment_time']); ?></td>
                                <td><?php echo htmlspecialchars($row['status']); ?></td>
                                <td>
                                    <form action="update_status.php" method="POST" style="display: inline-block;">
                                        <input type="hidden" name="customer_id" value="<?php echo $row['id']; ?>">
                                        <?php if($row['status'] === 'cancelled' && $row['cancelled_by'] === 'user'): ?>
                                            <div class="status-cancelled">Cancelled by user</div>
                                        <?php else: ?>
                                            <select name="new_status" class="status-select">
                                                <option value="pending" <?php echo ($row['status'] == 'pending') ? 'selected' : ''; ?>>Pending</option>
                                                <option value="approved" <?php echo ($row['status'] == 'approved') ? 'selected' : ''; ?>>Approved</option>
                                                <option value="completed" <?php echo ($row['status'] == 'completed') ? 'selected' : ''; ?>>Completed</option>
                                                <option value="cancelled" <?php echo ($row['status'] == 'cancelled') ? 'selected' : ''; ?>>Cancelled</option>
                                            </select>
                                            <button type="submit" class="update-btn">Update</button>
                                        <?php endif; ?>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="no-data">No appointments found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal -->
    <div id="detailsModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Appointment Details</h2>
                <span class="close-modal">&times;</span>
            </div>
            <div class="modal-body">
                <div class="detail-grid">
                    <div class="detail-item">
                        <i class="fas fa-user"></i>
                        <span class="detail-label">Full Name:</span>
                        <span id="modal-name" class="detail-value"></span>
                    </div>
                    <div class="detail-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <span class="detail-label">Address:</span>
                        <span id="modal-address" class="detail-value"></span>
                    </div>
                    <div class="detail-item">
                        <i class="fas fa-phone"></i>
                        <span class="detail-label">Phone Number:</span>
                        <span id="modal-phone" class="detail-value"></span>
                    </div>
                    <div class="detail-item">
                        <i class="fas fa-calendar"></i>
                        <span class="detail-label">Date:</span>
                        <span id="modal-date" class="detail-value"></span>
                    </div>
                    <div class="detail-item">
                        <i class="fas fa-clock"></i>
                        <span class="detail-label">Time:</span>
                        <span id="modal-time" class="detail-value"></span>
                    </div>
                    <div class="detail-item">
                        <i class="fas fa-tools"></i>
                        <span class="detail-label">Service:</span>
                        <span id="modal-service" class="detail-value"></span>
                    </div>
                    <div class="detail-item">
                        <i class="fas fa-home"></i>
                        <span class="detail-label">Type:</span>
                        <span id="modal-type" class="detail-value"></span>
                    </div>
                    <div class="detail-item">
                        <i class="fas fa-info-circle"></i>
                        <span class="detail-label">Status:</span>
                        <span id="modal-status" class="detail-value"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    const modal = document.getElementById('detailsModal');
    const closeBtn = document.querySelector('.close-modal');

    function showDetails(btn) {
        // Get data from button attributes
        const name = btn.getAttribute('data-name');
        const address = btn.getAttribute('data-address');
        const phone = btn.getAttribute('data-phone');
        const date = btn.getAttribute('data-date');
        const time = btn.getAttribute('data-time');
        const service = btn.getAttribute('data-service');
        const type = btn.getAttribute('data-type');
        const status = btn.getAttribute('data-status');

        // Set modal content
        document.getElementById('modal-name').textContent = name;
        document.getElementById('modal-address').textContent = address;
        document.getElementById('modal-phone').textContent = phone;
        document.getElementById('modal-date').textContent = date;
        document.getElementById('modal-time').textContent = time;
        document.getElementById('modal-service').textContent = service;
        document.getElementById('modal-type').textContent = type;
        document.getElementById('modal-status').textContent = status;

        // Show modal
        modal.style.display = 'block';
    }

    // Close modal when clicking (X)
    closeBtn.onclick = function() {
        modal.style.display = 'none';
    }

    // Close modal when clicking outside
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = 'none';
        }
    }
    </script>
</body>
</html>