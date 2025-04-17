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
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-3 mb-4">
                    <div class="card text-white bg-primary">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="card-title">Total Users</h5>
                                    <h2 class="display-4"><?php echo $stats['total_users']; ?></h2>
                                </div>
                                <i class="fas fa-users fa-3x opacity-50"></i>
                            </div>
                        </div>
                        <div class="card-footer d-flex align-items-center justify-content-between">
                            <a href="users.php" class="text-white text-decoration-none">View Details</a>
                            <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3 mb-4">
                    <div class="card text-white bg-success">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="card-title">Total Movies</h5>
                                    <h2 class="display-4"><?php echo $stats['total_movies']; ?></h2>
                                </div>
                                <i class="fas fa-film fa-3x opacity-50"></i>
                            </div>
                        </div>
                        <div class="card-footer d-flex align-items-center justify-content-between">
                            <a href="movies.php" class="text-white text-decoration-none">View Details</a>
                            <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3 mb-4">
                    <div class="card text-white bg-warning">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="card-title">Total Bookings</h5>
                                    <h2 class="display-4"><?php echo $stats['total_bookings']; ?></h2>
                                </div>
                                <i class="fas fa-ticket-alt fa-3x opacity-50"></i>
                            </div>
                        </div>
                        <div class="card-footer d-flex align-items-center justify-content-between">
                            <a href="bookings.php" class="text-white text-decoration-none">View Details</a>
                            <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3 mb-4">
                    <div class="card text-white bg-danger">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="card-title">Total Revenue</h5>
                                    <h2 class="display-4">$<?php echo number_format($stats['total_revenue'], 2); ?></h2>
                                </div>
                                <i class="fas fa-dollar-sign fa-3x opacity-50"></i>
                            </div>
                        </div>
                        <div class="card-footer d-flex align-items-center justify-content-between">
                            <a href="bookings.php" class="text-white text-decoration-none">View Details</a>
                            <div class="small text-white"><i class="fas fa-angle-right"></i></div>
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
                                            <?php else: ?>
                                                <span class="badge bg-danger">Cancelled</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer small text-muted">
                    <a href="bookings.php" class="btn btn-primary btn-sm">View All Bookings</a>
                </div>
            </div>
        </main>
    </div>
</div>

<?php
include 'includes/footer.php';
?>