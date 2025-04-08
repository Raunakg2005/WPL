<?php
$page_title = "Movie Recommendations";
require_once 'includes/functions.php';

if (isLoggedIn()) {
    $user_id = $_SESSION['user_id'];
    $recommended_movies = getRecommendedMovies($user_id, 12);
} else {
    $stmt = $conn->prepare("
        SELECT * FROM movies 
        WHERE status = 'now_showing'
        ORDER BY rating DESC
        LIMIT 12
    ");
    $stmt->execute();
    $result = $stmt->get_result();
    $recommended_movies = [];
    while ($row = $result->fetch_assoc()) {
        $recommended_movies[] = $row;
    }
}

include 'includes/header.php';
?>

<div class="container py-5">
    <h1 class="mb-4">Movie Recommendations</h1>
    
    <?php if (!isLoggedIn()): ?>
        <div class="alert alert-info mb-4">
            <i class="fas fa-info-circle me-2"></i>
            Sign in to get personalized movie recommendations based on your preferences and booking history.
            <a href="login.php" class="alert-link">Login now</a> or <a href="register.php" class="alert-link">create an account</a>.
        </div>
    <?php else: ?>
        <p class="lead mb-4">Based on your preferences and booking history, we think you'll enjoy these movies:</p>
    <?php endif; ?>
    
    <?php if (empty($recommended_movies)): ?>
        <div class="alert alert-warning">No recommendations available at the moment.</div>
    <?php else: ?>
        <div class="row">
            <?php foreach ($recommended_movies as $movie): ?>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="movie-card">
                        <img src="<?php echo $movie['poster']; ?>" alt="<?php echo $movie['title']; ?>" class="movie-card-img">
                        <div class="movie-card-body">
                            <h5 class="movie-card-title"><?php echo $movie['title']; ?></h5>
                            <div class="movie-card-info">
                                <span><?php echo $movie['genre']; ?></span>
                                <div class="movie-card-rating">
                                    <i class="fas fa-star"></i>
                                    <span><?php echo $movie['rating']; ?></span>
                                </div>
                            </div>
                            <a href="movie-details.php?id=<?php echo $movie['id']; ?>" class="btn btn-primary w-100">View Details</a>
                        </div>
                        <div class="movie-card-overlay"></div>
                            <h5 class="movie-card-overlay-title"><?php echo $movie['title']; ?></h5>
                            <div class="movie-card-overlay-info"></div>
                                <p><?php echo substr($movie['description'], 0, 100) . '...'; ?></p>
                                <p><strong>Director:</strong> <?php echo $movie['director']; ?></p>
                                <p><strong>Duration:</strong> <?php echo $movie['duration']; ?> mins</p>
                            </div>
                            <a href="movie-details.php?id=<?php echo $movie['id']; ?>" class="btn btn-light movie-card-overlay-btn">View Details</a>
                            <?php if (!empty($movie['trailer_url'])): ?>
                                <button class="btn btn-danger movie-card-overlay-btn trailer-btn" data-trailer="<?php echo $movie['trailer_url']; ?>">
                                    <i class="fas fa-play me-2"></i>Watch Trailer
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    
    <div class="card mt-5">
        <div class="card-header bg-primary text-white">
            <h3 class="mb-0">How Our Recommendations Work</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 mb-4 mb-md-0">
                    <div class="text-center">
                        <i class="fas fa-history fa-3x text-primary mb-3"></i>
                        <h4>Booking History</h4>
                        <p>We analyze your past bookings to understand what types of movies you enjoy watching.</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4 mb-md-0">
                    <div class="text-center">
                        <i class="fas fa-thumbs-up fa-3x text-primary mb-3"></i>
                        <h4>Ratings & Reviews</h4>
                        <p>Your ratings and reviews help us better understand your preferences and tastes.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="text-center">
                        <i class="fas fa-magic fa-3x text-primary mb-3"></i>
                        <h4>Smart Algorithm</h4>
                        <p>Our recommendation engine uses advanced algorithms to suggest movies you're likely to enjoy.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="trailerModal" tabindex="-1" aria-labelledby="trailerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content"></div>
            <div class="modal-header">
                <h5 class="modal-title" id="trailerModalLabel">Movie Trailer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="ratio ratio-16x9">
                    <iframe id="trailerIframe" src="/placeholder.svg" title="Movie Trailer" allowfullscreen></iframe>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
include 'includes/footer.php';
?>
