<?php
// Set page title
$page_title = "Booking Details";

// Include functions file
require_once '../includes/functions.php';

// Check if user is logged in and is admin
if (!isLoggedIn() || !isAdmin()) {
    redirect('../login.php');
}

// Check if booking ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    redirect('bookings.php');
}

// Get booking ID
$booking_id = intval($_GET['id']);

// Get booking details
$stmt = $conn->prepare("
    SELECT b.*, u.username, u.email, u.full_name,
           s.show_date, s.show_time, s.price,
           m.title as movie_title, m.poster as movie_poster,
           t.name as theater_name, t.location as theater_location
    FROM bookings b
    JOIN users u ON b.user_id = u.id
    JOIN shows s ON b.show_id = s.id
    JOIN movies m ON s.movie_id = m.id
    JOIN theaters t ON s.theater_id = t.id
    WHERE b.id = ?
");
$stmt->bind_param("i", $booking_id);
$stmt->execute();
$result = $stmt->get_result();

// If booking not found, redirect to bookings page
if ($result->num_rows == 0) {
    redirect('bookings.php');
}

// Get booking data
$booking = $result->fetch_assoc();

// Include header
include 'includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <?php include 'includes/sidebar.php'; ?>
        
        <!-- Main Content -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Booking Details</h1>
                <a href="bookings.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Bookings
                </a>
            </div>
            
            <div class="row">
                <div class="col-md-8">
                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-info-circle me-1"></i>
                            Booking Information
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <strong>Booking ID:</strong>
                                </div>
                                <div class="col-md-8">
                                    #<?php echo $booking['id']; ?>
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <strong>Booking Date:</strong>
                                </div>
                                <div class="col-md-8">
                                    <?php echo date('F d, Y h:i A', strtotime($booking['booking_date'])); ?>
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <strong>Booking Status:</strong>
                                </div>
                                <div class="col-md-8">
                                    <?php if ($booking['booking_status'] == 'confirmed'): ?>
                                        <span class="badge bg-success">Confirmed</span>
                                    <?php elseif ($booking['booking_status'] == 'cancelled'): ?>
                                        <span class="badge bg-danger">Cancelled</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning">Pending</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <strong>Payment Status:</strong>
                                </div>
                                <div class="col-md-8">
                                    <?php if ($booking['payment_status'] == 'paid'): ?>
                                        <span class="badge bg-success">Paid</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning">Pending</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <strong>Total Amount:</strong>
                                </div>
                                <div class="col-md-8">
                                    $<?php echo number_format($booking['total_amount'], 2); ?>
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <strong>Seats Booked:</strong>
                                </div>
                                <div class="col-md-8">
                                    <?php echo $booking['seats_booked']; ?> seats
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <strong>Seat Numbers:</strong>
                                </div>
                                <div class="col-md-8">
                                    <?php echo $booking['seat_numbers']; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-user me-1"></i>
                            Customer Information
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <strong>Name:</strong>
                                </div>
                                <div class="col-md-8">
                                    <?php echo $booking['full_name']; ?>
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <strong>Username:</strong>
                                </div>
                                <div class="col-md-8">
                                    <?php echo $booking['username']; ?>
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <strong>Email:</strong>
                                </div>
                                <div class="col-md-8">
                                    <?php echo $booking['email']; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-calendar-alt me-1"></i>
                            Show Information
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <strong>Movie:</strong>
                                </div>
                                <div class="col-md-8">
                                    <?php echo $booking['movie_title']; ?>
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <strong>Theater:</strong>
                                </div>
                                <div class="col-md-8">
                                    <?php echo $booking['theater_name']; ?> (<?php echo $booking['theater_location']; ?>)
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <strong>Show Date:</strong>
                                </div>
                                <div class="col-md-8">
                                    <?php echo date('F d, Y', strtotime($booking['show_date'])); ?>
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <strong>Show Time:</strong>
                                </div>
                                <div class="col-md-8">
                                    <?php echo date('h:i A', strtotime($booking['show_time'])); ?>
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <strong>Ticket Price:</strong>
                                </div>
                                <div class="col-md-8">
                                    $<?php echo number_format($booking['price'], 2); ?> per seat
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-film me-1"></i>
                            Movie Poster
                        </div>
                        <div class="card-body text-center">
                            <img src="../<?php echo $booking['movie_poster']; ?>" alt="<?php echo $booking['movie_title']; ?>" class="movie-poster-lg mb-3">
                            <h5><?php echo $booking['movie_title']; ?></h5>
                        </div>
                    </div>
                    
                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-cog me-1"></i>
                            Actions
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <?php if ($booking['booking_status'] == 'pending'): ?>
                                    <a href="bookings.php?action=confirm&id=<?php echo $booking['id']; ?>" class="btn btn-success">
                                        <i class="fas fa-check me-2"></i>Confirm Booking
                                    </a>
                                <?php endif; ?>
                                
                                <?php if ($booking['booking_status'] != 'cancelled'): ?>
                                    <a href="bookings.php?action=cancel&id=<?php echo $booking['id']; ?>" class="btn btn-warning">
                                        <i class="fas fa-ban me-2"></i>Cancel Booking
                                    </a>
                                <?php endif; ?>
                                
                                <a href="bookings.php?action=delete&id=<?php echo $booking['id']; ?>" class="btn btn-danger btn-delete">
                                    <i class="fas fa-trash me-2"></i>Delete Booking
                                </a>
                                
                                <a href="mailto:<?php echo $booking['email']; ?>" class="btn btn-primary">
                                    <i class="fas fa-envelope me-2"></i>Email Customer
                                </a>
                                
                                <button class="btn btn-secondary" onclick="window.print()">
                                    <i class="fas fa-print me-2"></i>Print Booking
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<?php
// Include footer
include 'includes/footer.php';
?>