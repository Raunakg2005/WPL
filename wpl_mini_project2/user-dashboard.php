<?php
require_once 'includes/functions.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$user_id = $_SESSION['user_id'];
$bookings = getUserBookings($user_id);
$recommended_movies = getRecommendedMovies($user_id);
$page_title = "My Dashboard";

include 'includes/header.php';
?>

<div class="container py-5">
    <h1 class="mb-4">My Dashboard</h1>
    
    <div class="row">
        <div class="col-lg-8">
            <div class="dashboard-card mb-4">
                <h3 class="dashboard-card-title">My Bookings</h3>
                
                <?php if (empty($bookings)): ?>
                    <div class="alert alert-info">You have no bookings yet.</div>
                <?php else: ?>
                    <?php foreach ($bookings as $booking): ?>
                        <div class="booking-history-item">
                            <div class="booking-history-header">
                                <div class="booking-history-title">
                                    <?php echo $booking['movie_title']; ?>
                                </div>
                                <div class="booking-history-date">
                                    Booked on: <?php echo date('M d, Y', strtotime($booking['booking_date'])); ?>
                                </div>
                            </div>
                            <div class="booking-history-details">
                                <div class="booking-history-detail">
                                    <div class="booking-history-detail-label">Show Date:</div>
                                    <div><?php echo date('l, F d, Y', strtotime($booking['show_date'])); ?></div>
                                </div>
                                <div class="booking-history-detail">
                                    <div class="booking-history-detail-label">Show Time:</div>
                                    <div><?php echo date('h:i A', strtotime($booking['show_time'])); ?></div>
                                </div>
                                <div class="booking-history-detail">
                                    <div class="booking-history-detail-label">Theater:</div>
                                    <div><?php echo $booking['theater_name']; ?> (<?php echo $booking['theater_location']; ?>)</div>
                                </div>
                                <div class="booking-history-detail">
                                    <div class="booking-history-detail-label">Seats:</div>
                                    <div><?php echo $booking['seat_numbers']; ?> (<?php echo $booking['seats_booked']; ?> seats)</div>
                                </div>
                                <div class="booking-history-detail">
                                    <div class="booking-history-detail-label">Total Amount:</div>
                                    <div>$<?php echo number_format($booking['total_amount'], 2); ?></div>
                                </div>
                                <div class="booking-history-detail">
                                    <div class="booking-history-detail-label">Status:</div>
                                    <div>
                                        <?php if ($booking['booking_status'] == 'confirmed'): ?>
                                            <span class="badge bg-success">Confirmed</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Cancelled</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="booking-history-actions">
                                <a href="booking-confirmation.php?id=<?php echo $booking['id']; ?>" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye me-1"></i>View Details
                                </a>
                                <?php if ($booking['booking_status'] == 'confirmed' && strtotime($booking['show_date']) > time()): ?>
                                    <a href="cancel-booking.php?id=<?php echo $booking['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to cancel this booking?')">
                                        <i class="fas fa-times me-1"></i>Cancel Booking
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="dashboard-card mb-4">
                <h3 class="dashboard-card-title">Profile Information</h3>
                
                <?php
                $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                $result = $stmt->get_result();
                $user = $result->fetch_assoc();
                ?>
                
                <div class="profile-info">
                    <div class="mb-3">
                        <strong>Username:</strong> <?php echo $user['username']; ?>
                    </div>
                    <div class="mb-3">
                        <strong>Full Name:</strong> <?php echo $user['full_name']; ?>
                    </div>
                    <div class="mb-3">
                        <strong>Email:</strong> <?php echo $user['email']; ?>
                    </div>
                    <div class="mb-3">
                        <strong>Member Since:</strong> <?php echo date('F d, Y', strtotime($user['created_at'])); ?>
                    </div>
                    <div class="mt-4">
                        <a href="edit-profile.php" class="btn btn-primary">
                            <i class="fas fa-user-edit me-2"></i>Edit Profile
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="dashboard-card">
                <h3 class="dashboard-card-title">Recommended For You</h3>
                
                <?php if (empty($recommended_movies)): ?>
                    <div class="alert alert-info">No recommendations available yet.</div>
                <?php else: ?>
                    <div class="recommended-movies">
                        <?php foreach ($recommended_movies as $movie): ?>
                            <div class="recommended-movie-item mb-3">
                                <div class="row g-0">
                                    <div class="col-4">
                                        <img src="<?php echo $movie['poster']; ?>" alt="<?php echo $movie['title']; ?>" class="img-fluid rounded">
                                    </div>
                                    <div class="col-8 ps-3">
                                        <h6><?php echo $movie['title']; ?></h6>
                                        <div class="small text-muted mb-2">
                                            <i class="fas fa-star text-warning me-1"></i><?php echo $movie['rating']; ?>/10
                                        </div>
                                        <a href="movie-details.php?id=<?php echo $movie['id']; ?>" class="btn btn-sm btn-outline-primary">View Details</a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php
include 'includes/footer.php';
?>
