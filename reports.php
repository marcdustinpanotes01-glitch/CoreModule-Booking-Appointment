<?php
include_once("connection/connection.php");
$con = connection();

// Initialize filter variables
$service_filter = isset($_GET['service']) ? $_GET['service'] : '';
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';
$date_from = isset($_GET['date_from']) ? $_GET['date_from'] : '';
$date_to = isset($_GET['date_to']) ? $_GET['date_to'] : '';

// Build the WHERE clause based on filters
$where_clause = "WHERE 1=1";
$params = array();
$types = "";

if (!empty($service_filter)) {
    $where_clause .= " AND service_type = ?";
    $params[] = $service_filter;
    $types .= "s";
}

if (!empty($status_filter)) {
    $where_clause .= " AND status = ?";
    $params[] = $status_filter;
    $types .= "s";
}

if (!empty($date_from)) {
    $where_clause .= " AND appointment_date >= ?";
    $params[] = $date_from;
    $types .= "s";
}

if (!empty($date_to)) {
    $where_clause .= " AND appointment_date <= ?";
    $params[] = $date_to;
    $types .= "s";
}

// Get total appointments and statistics with filters
$sql = "SELECT 
    COUNT(*) as total_appointments,
    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_total,
    SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as approved_total,
    SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed_total,
    SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled_total
FROM customer " . $where_clause;

// Prepare and execute the query with filters
$stmt = $con->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
$stats = $result->fetch_assoc();

// Get appointments by month for the current year
$sql = "SELECT 
    MONTH(appointment_date) as month,
    COUNT(*) as total
FROM customer
WHERE YEAR(appointment_date) = YEAR(CURRENT_DATE)
GROUP BY MONTH(appointment_date)
ORDER BY month";

$monthly_query = $con->query($sql);
$monthly_data = array_fill(1, 12, 0); // Initialize all months with 0

while ($row = $monthly_query->fetch_assoc()) {
    $monthly_data[$row['month']] = $row['total'];
}

$monthly_json = json_encode(array_values($monthly_data));

// Get status distribution
$status_data = [
    $stats['pending_total'],
    $stats['approved_total'],
    $stats['completed_total'],
    $stats['cancelled_total']
];
$status_json = json_encode($status_data);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports - Admin Dashboard</title>
    <link rel="stylesheet" href="css/admin/reports.css">
    <link rel="stylesheet" href="css/admin/admin_styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="nav-bar">
        <div class="nav-container">
            <a href="admin_dashboard.php" class="nav-link">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>
            <a href="reports.php" class="nav-link active">
                <i class="fas fa-chart-bar"></i> Reports
            </a>
        </div>
    </nav>

    <div class="reports-container">
        <h1>Reports Dashboard</h1>
        
        <!-- Filter Form -->
        <div class="report-card">
            <form method="GET" class="filter-form">
                <div class="filter-grid">
                    <div class="filter-item">
                        <label for="service">Service Type:</label>
                        <select name="service" id="service">
                            <option value="">All Services</option>
                            <option value="Calibration" <?php echo $service_filter == 'Calibration' ? 'selected' : ''; ?>>Calibration</option>
                            <option value="Egg Hatching" <?php echo $service_filter == 'Egg Hatching' ? 'selected' : ''; ?>>Egg Hatching</option>
                            <option value="Repair" <?php echo $service_filter == 'Repair' ? 'selected' : ''; ?>>Repair</option>
                        </select>
                    </div>
                    <div class="filter-item">
                        <label for="status">Status:</label>
                        <select name="status" id="status">
                            <option value="">All Status</option>
                            <option value="completed" <?php echo $status_filter == 'completed' ? 'selected' : ''; ?>>Completed</option>
                            <option value="cancelled" <?php echo $status_filter == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                            <option value="pending" <?php echo $status_filter == 'pending' ? 'selected' : ''; ?>>Pending</option>
                            <option value="approved" <?php echo $status_filter == 'approved' ? 'selected' : ''; ?>>Approved</option>
                        </select>
                    </div>
                    <div class="filter-item">
                        <label for="date_from">Date From:</label>
                        <input type="date" id="date_from" name="date_from" value="<?php echo $date_from; ?>">
                    </div>
                    <div class="filter-item">
                        <label for="date_to">Date To:</label>
                        <input type="date" id="date_to" name="date_to" value="<?php echo $date_to; ?>">
                    </div>
                    <div class="filter-item filter-buttons-container">
                        <button type="submit" class="filter-button">
                            <i class="fas fa-filter"></i>
                            Apply Filters
                        </button>
                        <a href="reports.php" class="reset-button">
                            <i class="fas fa-undo"></i>
                            Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <div class="report-card">
            <h2>Total Appointments</h2>
            <div class="total-appointments">
                <?php echo $stats['total_appointments']; ?>
            </div>
            <div class="status-summary">
                <div class="status-item pending">
                    <span class="status-label">Pending:</span>
                    <span class="status-value"><?php echo $stats['pending_total']; ?></span>
                </div>
                <div class="status-item approved">
                    <span class="status-label">Approved:</span>
                    <span class="status-value"><?php echo $stats['approved_total']; ?></span>
                </div>
                <div class="status-item completed">
                    <span class="status-label">Completed:</span>
                    <span class="status-value"><?php echo $stats['completed_total']; ?></span>
                </div>
                <div class="status-item cancelled">
                    <span class="status-label">Cancelled:</span>
                    <span class="status-value"><?php echo $stats['cancelled_total']; ?></span>
                </div>
            </div>
        </div>

        <div class="chart-container">
            <div class="report-card">
                <h2>Monthly Appointments (<?php echo date('Y'); ?>)</h2>
                <canvas id="monthlyChart"></canvas>
            </div>
            
            <div class="report-card">
                <h2>Appointment Status Distribution</h2>
                <canvas id="statusChart"></canvas>
            </div>
        </div>
    </div>

    <script>
        // Monthly Appointments Chart
        const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
        new Chart(monthlyCtx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                datasets: [{
                    label: 'Number of Appointments',
                    data: <?php echo $monthly_json; ?>,
                    borderColor: '#4CAF50',
                    tension: 0.1,
                    fill: false
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: 'Monthly Appointments Distribution'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });

        // Status Distribution Chart
        const statusCtx = document.getElementById('statusChart').getContext('2d');
        new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: ['Pending', 'Approved', 'Completed', 'Cancelled'],
                datasets: [{
                    data: <?php echo $status_json; ?>,
                    backgroundColor: [
                        '#FFC107', // Pending - Yellow
                        '#2196F3', // Approved - Blue
                        '#4CAF50', // Completed - Green
                        '#F44336'  // Cancelled - Red
                    ]
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: 'Appointment Status Distribution'
                    }
                }
            }
        });
    </script>
</body>
</html>