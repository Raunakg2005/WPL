<?php
require_once 'includes/functions.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

if (!isset($_GET['id']) || empty($_GET['id'])) {
    redirect('user-dashboard.php');
}

$booking_id = intval($_GET['id']);

$stmt = $conn->prepare("
    SELECT b.*, s.show_date, s.show_time, s.price, 
           m.title as movie_title, m.poster as movie_poster,
           t.name as theater_name, t.location as theater_location
    FROM bookings b
    JOIN shows s ON b.show_id = s.id
    JOIN movies m ON s.movie_id = m.id
    JOIN theaters t ON s.theater_id = t.id
    WHERE b.id = ? AND b.user_id = ?
");
$stmt->bind_param("ii", $booking_id, $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    redirect('user-dashboard.php');
}

$booking = $result->fetch_assoc();

$page_title = "Booking Confirmation";

include 'includes/header.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header bg-success text-white">
                    <h3 class="mb-0">Booking Confirmed!</h3>
                </div>
                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <?php if ($booking['booking_status'] == 'confirmed'): ?>
                            <i class="fas fa-check-circle text-success fa-5x mb-3"></i>
                            <h4>Thank you for your booking!</h4>
                            <p>Your booking has been confirmed. Below are your booking details.</p>
                        <?php else: ?>
                            <i class="fas fa-times-circle text-danger fa-5x mb-3"></i>
                            <h4>Your booking has been cancelled.</h4>
                            <p>Unfortunately, this booking is no longer active. Below are the details for your reference.</p>
                        <?php endif; ?>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-md-4"></div>
                            <img src="<?php echo $booking['movie_poster']; ?>" alt="<?php echo $booking['movie_title']; ?>" class="img-fluid rounded">
                        </div>
                        <div class="col-md-8">
                            <h5><?php echo $booking['movie_title']; ?></h5>
                            <p>
                                <strong>Date:</strong> <?php echo date('l, F d, Y', strtotime($booking['show_date'])); ?><br>
                                <strong>Time:</strong> <?php echo date('h:i A', strtotime($booking['show_time'])); ?><br>
                                <strong>Theater:</strong> <?php echo $booking['theater_name']; ?> (<?php echo $booking['theater_location']; ?>)<br>
                            </p>
                        </div>
                    </div>
                    
                    <div class="booking-details mb-4">
                        <h5 class="mb-3">Booking Details</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <tr>
                                    <th>Booking ID</th>
                                    <td><?php echo $booking['id']; ?></td>
                                </tr>
                                <tr>
                                    <th>Booking Date</th>
                                    <td><?php echo date('F d, Y h:i A', strtotime($booking['booking_date'])); ?></td>
                                </tr>
                                <tr>
                                    <th>Number of Seats</th>
                                    <td><?php echo $booking['seats_booked']; ?></td>
                                </tr>
                                <tr>
                                    <th>Seat Numbers</th>
                                    <td><?php echo $booking['seat_numbers']; ?></td>
                                </tr>
                                <tr>
                                    <th>Price per Seat</th>
                                    <td>$<?php echo number_format($booking['price'], 2); ?></td>
                                </tr>
                                <tr>
                                    <th>Total Amount</th>
                                    <td>$<?php echo number_format($booking['total_amount'], 2); ?></td>
                                </tr>
                                <tr>
                                    <th>Payment Status</th>
                                    <td>
                                        <span class="badge bg-success">
                                            <?php echo ucfirst($booking['payment_status']); ?>
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Booking Status</th>
                                    <td>
                                        <?php if ($booking['booking_status'] == 'confirmed'): ?>
                                            <span class="badge bg-success">Confirmed</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Cancelled</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    
                    <div class="text-center">
                        <a href="user-dashboard.php" class="btn btn-primary me-2">Go to Dashboard</a>
                        <button class="btn btn-outline-secondary" onclick="window.print()">
                            <i class="fas fa-print me-2"></i>Print Ticket
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
include 'includes/footer.php';
?>
