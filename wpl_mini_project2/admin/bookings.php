<?php
$page_title = "Admin Dashboard";

require_once '../includes/functions.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('../login.php');
}

$stats = getDashboardStats();

include 'includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <?php include 'includes/sidebar.php'; ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Dashboard</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <div class="btn-group me-2">
                        <button type="button" class="btn btn-sm btn-outline-secondary">Export</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary">Print</button>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle">
                        <i class="fas fa-calendar me-1"></i>
                        This week
                    </button>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6 col-lg-3 mb-4">
                    <div class="card text-white bg-primary h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="card-title">Total Users</h6>
                                    <h2 class="mb-0"><?php echo $stats['total_users']; ?></h2>
                                </div>
                                <div>
                                    <i class="fas fa-users fa-3x opacity-50"></i>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer d-flex align-items-center justify-content-between">
                            <a href="users.php" class="text-white text-decoration-none">View Details</a>
                            <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6 col-lg-3 mb-4">
                    <div class="card text-white bg-success h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="card-title">Total Movies</h6>
                                    <h2 class="mb-0"><?php echo $stats['total_movies']; ?></h2>
                                </div>
                                <div>
                                    <i class="fas fa-film fa-3x opacity-50"></i>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer d-flex align-items-center justify-content-between">
                            <a href="movies.php" class="text-white text-decoration-none">View Details</a>
                            <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6 col-lg-3 mb-4">
                    <div class="card text-white bg-warning h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="card-title">Total Bookings</h6>
                                    <h2 class="mb-0"><?php echo $stats['total_bookings']; ?></h2>
                                </div>
                                <div>
                                    <i class="fas fa-ticket-alt fa-3x opacity-50"></i>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer d-flex align-items-center justify-content-between">
                            <a href="bookings.php" class="text-white text-decoration-none">View Details</a>
                            <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6 col-lg-3 mb-4">
                    <div class="card text-white bg-danger h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="card-title">Total Revenue</h6>
                                    <h2 class="mb-0">$<?php echo number_format($stats['total_revenue'], 2); ?></h2>
                                </div>
                                <div>
                                    <i class="fas fa-dollar-sign fa-3x opacity-50"></i>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer d-flex align-items-center justify-content-between">
                            <a href="reports.php" class="text-white text-decoration-none">View Details</a>
                            <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row mb-4">
                <div class="col-md-8">
                    <div class="card h-100">
                        <div class="card-header">
                            <i class="fas fa-chart-bar me-1"></i>
                            Monthly Bookings
                        </div>
                        <div class="card-body" style="height: 400px; overflow: hidden;">
                            <canvas id="bookingsChart" class="chart-container" style="height: 100%;"></canvas>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card h-100">
                        <div class="card-header">
                            <i class="fas fa-chart-pie me-1"></i>
                            Movie Categories
                        </div>
                        <div class="card-body" style="height: 400px; overflow: hidden;">
                            <canvas id="genresChart" class="chart-container" style="height: 100%;"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-table me-1"></i>
                    Recent Bookings
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>User</th>
                                    <th>Movie</th>
                                    <th>Seats</th>
                                    <th>Amount</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($stats['recent_bookings'] as $booking): ?>
                                    <tr>
                                        <td><?php echo $booking['id']; ?></td>
                                        <td><?php echo $booking['username']; ?></td>
                                        <td><?php echo $booking['movie_title']; ?></td>
                                        <td><?php echo $booking['seats_booked']; ?></td>
                                        <td>$<?php echo number_format($booking['total_amount'], 2); ?></td>
                                        <td><?php echo date('M d, Y', strtotime($booking['booking_date'])); ?></td>
                                        <td>
                                            <?php if ($booking['booking_status'] == 'confirmed'): ?>
                                                <span class="badge bg-success">Confirmed</span>
                                            <?php elseif ($booking['booking_status'] == 'cancelled'): ?>
                                                <span class="badge bg-danger">Cancelled</span>
                                            <?php else: ?>
                                                <span class="badge bg-warning">Pending</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                
                                <?php if (empty($stats['recent_bookings'])): ?>
                                    <tr>
                                        <td colspan="7" class="text-center">No recent bookings found.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="bookings.php" class="btn btn-primary">View All Bookings</a>
                </div>
            </div>
        </main>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Bookings Chart
        const bookingsCtx = document.getElementById('bookingsChart').getContext('2d');
        const bookingsChart = new Chart(bookingsCtx, {
            type: 'bar',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                datasets: [{
                    label: 'Bookings',
                    data: [65, 59, 80, 81, 56, 55, 40, 45, 60, 70, 75, 90],
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
        
        // Genres Chart
        const genresCtx = document.getElementById('genresChart').getContext('2d');
        const genresChart = new Chart(genresCtx, {
            type: 'doughnut',
            data: {
                labels: ['Action', 'Comedy', 'Drama', 'Sci-Fi', 'Horror', 'Romance'],
                datasets: [{
                    data: [30, 20, 15, 10, 15, 10],
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.7)',
                        'rgba(54, 162, 235, 0.7)',
                        'rgba(255, 206, 86, 0.7)',
                        'rgba(75, 192, 192, 0.7)',
                        'rgba(153, 102, 255, 0.7)',
                        'rgba(255, 159, 64, 0.7)'
                    ],
                    borderColor: [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(153, 102, 255, 1)',
                        'rgba(255, 159, 64, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right'
                    }
                }
            }
        });
    });
</script>

<?php
include 'includes/footer.php';
?>