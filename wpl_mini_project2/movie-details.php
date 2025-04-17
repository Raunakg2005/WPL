<?php
$page_title = "Movie Details";

require_once 'includes/functions.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    redirect('movies.php');
}

$movie_id = intval($_GET['id']);

$movie = getMovieById($movie_id);

if (!$movie) {
    redirect('movies.php');
}

$shows = getShowsByMovieId($movie_id);

$user_review = null;
if (isLoggedIn()) {
    $user_id = $_SESSION['user_id'];
    
    $stmt = $conn->prepare("
        SELECT * FROM reviews 
        WHERE user_id = ? AND movie_id = ?
    ");
    $stmt->bind_param("ii", $user_id, $movie_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $user_review = $result->fetch_assoc();
    }
}

$stmt = $conn->prepare("
    SELECT r.*, u.username, u.full_name
    FROM reviews r
    JOIN users u ON r.user_id = u.id
    WHERE r.movie_id = ? AND r.status = 'approved'
    ORDER BY r.created_at DESC
");
$stmt->bind_param("i", $movie_id);
$stmt->execute();
$result = $stmt->get_result();
$reviews = [];
while ($row = $result->fetch_assoc()) {
    $reviews[] = $row;
}

$avg_rating = 0;
$total_reviews = count($reviews);
if ($total_reviews > 0) {
    $sum_rating = 0;
    foreach ($reviews as $review) {
        $sum_rating += $review['rating'];
    }
    $avg_rating = $sum_rating / $total_reviews;
}

include 'includes/header.php';
?>

<section class="movie-details-banner parallax-section" style="background-image: url('<?php echo $movie['poster']; ?>');">
    <div class="overlay"></div>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="movie-banner-content">
                    <h1 class="display-4 text-white"><?php echo $movie['title']; ?></h1>
                    <div class="movie-meta text-white">
                        <span><i class="fas fa-star text-warning"></i> <?php echo number_format($movie['rating'], 1); ?>/10</span>
                        <span><i class="fas fa-film"></i> <?php echo $movie['genre']; ?></span>
                        <span><i class="fas fa-language"></i> <?php echo $movie['language']; ?></span>
                        <span><i class="fas fa-clock"></i> <?php echo $movie['duration']; ?> mins</span>
                        <span><i class="fas fa-calendar"></i> <?php echo date('M d, Y', strtotime($movie['release_date'])); ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="card shadow-sm">
                    <img src="<?php echo $movie['poster']; ?>" class="card-img-top" alt="<?php echo $movie['title']; ?>">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $movie['title']; ?></h5>
                        <p class="card-text">
                            <strong>Director:</strong> <?php echo $movie['director']; ?><br>
                            <strong>Cast:</strong> <?php echo $movie['cast']; ?><br>
                            <strong>Genre:</strong> <?php echo $movie['genre']; ?><br>
                            <strong>Language:</strong> <?php echo $movie['language']; ?><br>
                            <strong>Duration:</strong> <?php echo $movie['duration']; ?> mins<br>
                            <strong>Release Date:</strong> <?php echo date('F d, Y', strtotime($movie['release_date'])); ?><br>
                            <strong>Status:</strong> 
                            <?php if ($movie['status'] == 'now_showing'): ?>
                                <span class="badge bg-success">Now Showing</span>
                            <?php elseif ($movie['status'] == 'coming_soon'): ?>
                                <span class="badge bg-warning">Coming Soon</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">Not Available</span>
                            <?php endif; ?>
                        </p>
                        
                        <?php if (!empty($movie['trailer_url'])): ?>
                            <div class="mt-3">
                                <button type="button" class="btn btn-outline-primary w-100" data-bs-toggle="modal" data-bs-target="#trailerModal">
                                    <i class="fas fa-play me-2"></i>Watch Trailer
                                </button>
                            </div>

                            <div class="modal fade" id="trailerModal" tabindex="-1" aria-labelledby="trailerModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="trailerModalLabel">Watch Trailer</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="ratio ratio-16x9">
                                                <iframe src="<?php echo $movie['trailer_url']; ?>" frameborder="0" allowfullscreen></iframe>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- Movie Description and Shows -->
            <div class="col-md-8">
                <div class="card shadow-sm mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">About the Movie</h5>
                    </div>
                    <div class="card-body">
                        <p class="card-text"><?php echo nl2br($movie['description']); ?></p>
                    </div>
                </div>
                
                <?php if ($movie['status'] == 'now_showing'): ?>
                    <div class="card shadow-sm mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Show Times</h5>
                        </div>
                        <div class="card-body">
                            <?php if (empty($shows)): ?>
                                <div class="alert alert-info">
                                    No shows available for this movie at the moment. Please check back later.
                                </div>
                            <?php else: ?>
                                <div class="accordion" id="showsAccordion">
                                    <?php 
                                    $current_date = '';
                                    $date_counter = 0;
                                    foreach ($shows as $show): 
                                        $show_date = date('Y-m-d', strtotime($show['show_date']));
                                        if ($show_date != $current_date) {
                                            $current_date = $show_date;
                                            $date_counter++;
                                    ?>
                                        <?php if ($date_counter > 1): ?>
                                            </div>
                                        </div>
                                    </div>
                                        <?php endif; ?>
                                    
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="heading<?php echo $date_counter; ?>">
                                            <button class="accordion-button <?php echo ($date_counter > 1) ? 'collapsed' : ''; ?>" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?php echo $date_counter; ?>" aria-expanded="<?php echo ($date_counter == 1) ? 'true' : 'false'; ?>" aria-controls="collapse<?php echo $date_counter; ?>">
                                                <?php echo date('l, F d, Y', strtotime($show['show_date'])); ?>
                                            </button>
                                        </h2>
                                        <div id="collapse<?php echo $date_counter; ?>" class="accordion-collapse collapse <?php echo ($date_counter == 1) ? 'show' : ''; ?>" aria-labelledby="heading<?php echo $date_counter; ?>" data-bs-parent="#showsAccordion">
                                            <div class="accordion-body">
                                                <div class="row">
                                    <?php } ?>
                                    
                                    <div class="col-md-6 mb-3">
                                        <div class="card h-100">
                                            <div class="card-body">
                                                <h5 class="card-title"><?php echo $show['theater_name']; ?></h5>
                                                <p class="card-text">
                                                    <small class="text-muted">
                                                        <i class="fas fa-map-marker-alt me-1"></i> <?php echo $show['theater_location']; ?><br>
                                                        <i class="fas fa-clock me-1"></i> <?php echo date('h:i A', strtotime($show['show_time'])); ?><br>
                                                        <i class="fas fa-ticket-alt me-1"></i> Available Seats: <?php echo $show['available_seats']; ?><br>
                                                        <i class="fas fa-dollar-sign me-1"></i> Price: $<?php echo number_format($show['price'], 2); ?>
                                                    </small>
                                                </p>
                                                <?php if ($show['available_seats'] > 0): ?>
                                                    <a href="booking.php?show_id=<?php echo $show['id']; ?>" class="btn btn-primary btn-sm w-100">Book Now</a>
                                                <?php else: ?>
                                                    <button class="btn btn-secondary btn-sm w-100" disabled>Sold Out</button>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <?php endforeach; ?>
                                    
                                    <?php if (!empty($shows)): ?>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
                
                <!-- Reviews Section -->
                <div class="card shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Reviews</h5>
                        <div>
                            <span class="badge bg-primary rounded-pill">
                                <?php echo $total_reviews; ?> <?php echo ($total_reviews == 1) ? 'Review' : 'Reviews'; ?>
                            </span>
                            <?php if ($total_reviews > 0): ?>
                                <span class="ms-2">
                                    <i class="fas fa-star text-warning"></i> 
                                    <?php echo number_format($avg_rating, 1); ?>/10
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php if (isLoggedIn()): ?>
                            <div class="mb-4">
                                <h6>Write a Review</h6>
                                <form method="post" action="submit-review.php" class="review-form">
                                    <input type="hidden" name="movie_id" value="<?php echo $movie_id; ?>">
                                    
                                    <div class="mb-3">
                                        <label for="rating" class="form-label">Your Rating</label>
                                        <div class="rating-stars">
                                            <?php for ($i = 1; $i <= 10; $i++): ?>
                                                <input type="radio" id="rating<?php echo $i; ?>" name="rating" value="<?php echo $i; ?>" <?php echo ($user_review && $user_review['rating'] == $i) ? 'checked' : ''; ?> required>
                                                <label for="rating<?php echo $i; ?>"><i class="far fa-star"></i></label>
                                            <?php endfor; ?>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="comment" class="form-label">Your Review</label>
                                        <textarea class="form-control" id="comment" name="comment" rows="3" required><?php echo ($user_review) ? $user_review['comment'] : ''; ?></textarea>
                                    </div>
                                    
                                    <button type="submit" class="btn btn-primary">
                                        <?php echo ($user_review) ? 'Update Review' : 'Submit Review'; ?>
                                    </button>
                                    
                                    <?php if ($user_review): ?>
                                        <?php if ($user_review['status'] == 'pending'): ?>
                                            <div class="alert alert-warning mt-3">
                                                Your review is pending approval.
                                            </div>
                                        <?php elseif ($user_review['status'] == 'rejected'): ?>
                                            <div class="alert alert-danger mt-3">
                                                Your review was rejected. Please update it to comply with our guidelines.
                                            </div>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </form>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info">
                                <a href="login.php">Login</a> to write a review.
                            </div>
                        <?php endif; ?>
                        
                        <hr>
                        
                        <h6>User Reviews</h6>
                        
                        <?php if (empty($reviews)): ?>
                            <div class="alert alert-light">
                                No reviews yet. Be the first to review this movie!
                            </div>
                        <?php else: ?>
                            <div class="reviews-list">
                                <?php foreach ($reviews as $review): ?>
                                    <div class="review-item mb-3 p-3 border rounded">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <div>
                                                <strong><?php echo $review['full_name']; ?></strong>
                                                <small class="text-muted ms-2"><?php echo date('M d, Y', strtotime($review['created_at'])); ?></small>
                                            </div>
                                            <div class="rating-display">
                                                <?php for ($i = 1; $i <= 10; $i++): ?>
                                                    <?php if ($i <= $review['rating']): ?>
                                                        <i class="fas fa-star text-warning"></i>
                                                    <?php else: ?>
                                                        <i class="far fa-star"></i>
                                                    <?php endif; ?>
                                                <?php endfor; ?>
                                                <span class="ms-2"><?php echo $review['rating']; ?>/10</span>
                                            </div>
                                        </div>
                                        <p class="mb-0"><?php echo nl2br(htmlspecialchars($review['comment'])); ?></p>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
// Include footer
include 'includes/footer.php';
?>