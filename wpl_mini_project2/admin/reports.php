<?php
$page_title = "Reports";

require_once '../includes/functions.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('../login.php');
}

$report_type = isset($_GET['type']) ? sanitize($_GET['type']) : 'sales';
$start_date = isset($_GET['start_date']) ? sanitize($_GET['start_date']) : date('Y-m-d', strtotime('-30 days'));
$end_date = isset($_GET['end_date']) ? sanitize($_GET['end_date']) : date('Y-m-d');

$report_data = [];
$chart_labels = [];
$chart_values = [];

if ($report_type == 'sales') {
    $stmt = $conn->prepare("
        SELECT DATE(b.booking_date) as date, SUM(b.total_amount) as total
        FROM bookings b
        WHERE b.booking_status = 'confirmed'
        AND DATE(b.booking_date) BETWEEN ? AND ?
        GROUP BY DATE(b.booking_date)
        ORDER BY DATE(b.booking_date)
    ");
    $stmt->bind_param("ss", $start_date, $end_date);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $report_data[] = $row;
        $chart_labels[] = date('M d', strtotime($row['date']));
        $chart_values[] = $row['total'];
    }
    
    $stmt = $conn->prepare("
        SELECT SUM(b.total_amount) as total
        FROM bookings b
        WHERE b.booking_status = 'confirmed'
        AND DATE(b.booking_date) BETWEEN ? AND ?
    ");
    $stmt->bind_param("ss", $start_date, $end_date);
    $stmt->execute();
    $result = $stmt->get_result();
    $total_sales = $result->fetch_assoc()['total'] ?? 0;
    
    $stmt = $conn->prepare("
        SELECT COUNT(*) as total
        FROM bookings b
        WHERE b.booking_status = 'confirmed'
        AND DATE(b.booking_date) BETWEEN ? AND ?
    ");
    $stmt->bind_param("ss", $start_date, $end_date);
    $stmt->execute();
    $result = $stmt->get_result();
    $total_bookings = $result->fetch_assoc()['total'] ?? 0;
    
    $avg_sale = ($total_bookings > 0) ? ($total_sales / $total_bookings) : 0;
} elseif ($report_type == 'movies') {
    $stmt = $conn->prepare("
        SELECT m.title, COUNT(b.id) as bookings, SUM(b.total_amount) as revenue
        FROM bookings b
        JOIN shows s ON b.show_id = s.id
        JOIN movies m ON s.movie_id = m.id
        WHERE b.booking_status = 'confirmed'
        AND DATE(b.booking_date) BETWEEN ? AND ?
        GROUP BY m.id
        ORDER BY bookings DESC
    ");
    $stmt->bind_param("ss", $start_date, $end_date);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $report_data[] = $row;
        $chart_labels[] = $row['title'];
        $chart_values[] = $row['bookings'];
    }
} elseif ($report_type == 'theaters') {
    $stmt = $conn->prepare("
        SELECT t.name, t.location, COUNT(b.id) as bookings, SUM(b.total_amount) as revenue
        FROM bookings b
        JOIN shows s ON b.show_id = s.id
        JOIN theaters t ON s.theater_id = t.id
        WHERE b.booking_status = 'confirmed'
        AND DATE(b.booking_date) BETWEEN ? AND ?
        GROUP BY t.id
        ORDER BY bookings DESC
    ");
    $stmt->bind_param("ss", $start_date, $end_date);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $report_data[] = $row;
        $chart_labels[] = $row['name'] . ' (' . $row['location'] . ')';
        $chart_values[] = $row['bookings'];
    }
} elseif ($report_type == 'users') {
    $stmt = $conn->prepare("
        SELECT u.username, u.full_name, COUNT(b.id) as bookings, SUM(b.total_amount) as spent
        FROM bookings b
        JOIN users u ON b.user_id = u.id
        WHERE b.booking_status = 'confirmed'
        AND DATE(b.booking_date) BETWEEN ? AND ?
        GROUP BY u.id
        ORDER BY spent DESC
    ");
    $stmt->bind_param("ss", $start_date, $end_date);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $report_data[] = $row;
        $chart_labels[] = $row['username'];
        $chart_values[] = $row['spent'];
    }
}

include 'includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <?php include 'includes/sidebar.php'; ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Reports</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <div class="btn-group me-2">
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="window.print()">
                            <i class="fas fa-print me-1"></i>Print
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="exportCSV">
                            <i class="fas fa-download me-1"></i>Export
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-filter me-1"></i>
                    Report Filters
                </div>
                <div class="card-body">
                    <form method="get" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="row g-3">
                        <div class="col-md-3">
                            <label for="type" class="form-label">Report Type</label>
                            <select class="form-select" id="type" name="type">
                                <option value="sales" <?php echo ($report_type == 'sales') ? 'selected' : ''; ?>>Sales Report</option>
                                <option value="movies" <?php echo ($report_type == 'movies') ? 'selected' : ''; ?>>Movies Report</option>
                                <option value="theaters" <?php echo ($report_type == 'theaters') ? 'selected' : ''; ?>>Theaters Report</option>
                                <option value="users" <?php echo ($report_type == 'users') ? 'selected' : ''; ?>>Users Report</option>
                            </select>
                        </div>
                        
                        <div class="col-md-3">
                            <label for="start_date" class="form-label">Start Date</label>
                            <input type="date" class="form-control" id="start_date" name="start_date" value="<?php echo $start_date; ?>">
                        </div>
                        
                        <div class="col-md-3">
                            <label for="end_date" class="form-label">End Date</label>
                            <input type="date" class="form-control" id="end_date" name="end_date" value="<?php echo $end_date; ?>">
                        </div>
                        
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-search me-2"></i>Generate Report
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <?php if ($report_type == 'sales'): ?>
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="card text-white bg-primary h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-title">Total Sales</h6>
                                        <h2 class="mb-0">$<?php echo number_format($total_sales, 2); ?></h2>
                                    </div>
                                    <div>
                                        <i class="fas fa-dollar-sign fa-3x opacity-50"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card text-white bg-success h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-title">Total Bookings</h6>
                                        <h2 class="mb-0"><?php echo $total_bookings; ?></h2>
                                    </div>
                                    <div>
                                        <i class="fas fa-ticket-alt fa-3x opacity-50"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card text-white bg-info h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-title">Average Sale</h6>
                                        <h2 class="mb-0">$<?php echo number_format($avg_sale, 2); ?></h2>
                                    </div>
                                    <div>
                                        <i class="fas fa-chart-line fa-3x opacity-50"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-chart-bar me-1"></i>
                    <?php echo ucfirst($report_type); ?> Chart
                </div>
                <div class="card-body">
                    <canvas id="reportChart" class="chart-container"></canvas>
                </div>
            </div>
            
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-table me-1"></i>
                    <?php echo ucfirst($report_type); ?> Report Data
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped datatable" id="reportTable">
                            <thead>
                                <tr>
                                    <?php if ($report_type == 'sales'): ?>
                                        <th>Date</th>
                                        <th>Total Sales</th>
                                    <?php elseif ($report_type == 'movies'): ?>
                                        <th>Movie</th>
                                        <th>Bookings</th>
                                        <th>Revenue</th>
                                    <?php elseif ($report_type == 'theaters'): ?>
                                        <th>Theater</th>
                                        <th>Location</th>
                                        <th>Bookings</th>
                                        <th>Revenue</th>
                                    <?php elseif ($report_type == 'users'): ?>
                                        <th>Username</th>
                                        <th>Full Name</th>
                                        <th>Bookings</th>
                                        <th>Total Spent</th>
                                    <?php endif; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($report_type == 'sales'): ?>
                                    <?php foreach ($report_data as $row): ?>
                                        <tr>
                                            <td><?php echo date('F d, Y', strtotime($row['date'])); ?></td>
                                            <td>$<?php echo number_format($row['total'], 2); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php elseif ($report_type == 'movies'): ?>
                                    <?php foreach ($report_data as $row): ?>
                                        <tr>
                                            <td><?php echo $row['title']; ?></td>
                                            <td><?php echo $row['bookings']; ?></td>
                                            <td>$<?php echo number_format($row['revenue'], 2); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php elseif ($report_type == 'theaters'): ?>
                                    <?php foreach ($report_data as $row): ?>
                                        <tr>
                                            <td><?php echo $row['name']; ?></td>
                                            <td><?php echo $row['location']; ?></td>
                                            <td><?php echo $row['bookings']; ?></td>
                                            <td>$<?php echo number_format($row['revenue'], 2); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php elseif ($report_type == 'users'): ?>
                                    <?php foreach ($report_data as $row): ?>
                                        <tr>
                                            <td><?php echo $row['username']; ?></td>
                                            <td><?php echo $row['full_name']; ?></td>
                                            <td><?php echo $row['bookings']; ?></td>
                                            <td>$<?php echo number_format($row['spent'], 2); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                                
                                <?php if (empty($report_data)): ?>
                                    <tr>
                                        <td colspan="<?php echo ($report_type == 'sales') ? 2 : (($report_type == 'movies') ? 3 : 4); ?>" class="text-center">No data available for the selected period.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        
        const ctx = document.getElementById('reportChart').getContext('2d');
        const reportChart = new Chart(ctx, {
            type: '<?php echo ($report_type == 'sales') ? 'line' : 'bar'; ?>',
            data: {
                labels: <?php echo json_encode($chart_labels); ?>,
                datasets: [{
                    label: '<?php echo ($report_type == 'sales') ? 'Sales' : (($report_type == 'movies' || $report_type == 'theaters') ? 'Bookings' : 'Amount Spent'); ?>',
                    data: <?php echo json_encode($chart_values); ?>,
                    backgroundColor: 'rgba(0, 123, 255, 0.5)',
                    borderColor: 'rgba(0, 123, 255, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
        
        document.getElementById('exportCSV').addEventListener('click', function() {
            const table = document.getElementById('reportTable');
            const rows = table.querySelectorAll('tr');
            let csv = [];
            
            for (let i = 0; i < rows.length; i++) {
                const row = [], cols = rows[i].querySelectorAll('td, th');
                
                for (let j = 0; j < cols.length; j++) {
                    let data = cols[j].innerText.replace(/\$/g, '').replace(/,/g, '');
                    if (data.includes(',')) {
                        data = `"${data}"`;
                    }
                    row.push(data);
                }
                
                csv.push(row.join(','));
            }
            
            const csvString = csv.join('\n');
            const filename = '<?php echo $report_type; ?>_report_<?php echo date('Y-m-d'); ?>.csv';
            
            const blob = new Blob([csvString], { type: 'text/csv;charset=utf-8;' });
            const link = document.createElement('a');
            
            if (navigator.msSaveBlob) { 
                navigator.msSaveBlob(blob, filename);
            } else {
                const url = URL.createObjectURL(blob);
                link.href = url;
                link.download = filename;
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            }
        });
    });
</script>

<?php
include 'includes/footer.php';
?>