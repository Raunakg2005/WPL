<?php
$page_title = "All Bookings";
require_once '../includes/functions.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('../login.php');
}

$bookings = getAllBookings();
include 'includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <?php include 'includes/sidebar.php'; ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
            <h1 class="h2">All Bookings</h1>
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-table me-1"></i>
                    Bookings List
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>User</th>
                                    <th>Movie</th>
                                    <th>Theater</th>
                                    <th>Seats</th>
                                    <th>Amount</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($bookings as $booking): ?>
                                    <tr>
                                        <td><?php echo $booking['id']; ?></td>
                                        <td><?php echo $booking['username']; ?></td>
                                        <td><?php echo $booking['movie_title']; ?></td>
                                        <td><?php echo $booking['theater_name']; ?></td>
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
            </div>
        </main>
    </div>
</div>

<?php
include 'includes/footer.php';
?>
