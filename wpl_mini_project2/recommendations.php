<?php
// Set page title
$page_title = "Recommended Movies";

// Include functions file
require_once 'includes/functions.php';

// Check if user is logged in
if (isLoggedIn()) {
    $user_id = $_SESSION['user_id'];
    $recommended_movies = getRecommendedMovies($user_id, 12);
} else {
    // For non-logged in users, show popular movies
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

// Include header
include 'includes/header.php';
?>

<!-- Recommendations Banner Section -->
<section class="movies-banner parallax-section" style="background-image: url('assets/uploads/movies-banner.jpg'); background-size: cover; background-position: center; background-attachment: fixed;">
    <div class="container">
        <div class="row">
            <div class="col-md-12 text-center">
                <h1 class="display-4 text-danger">Recommended For You</h1>
                <p class="lead text-light">
                    <?php echo isLoggedIn() 
                        ? "<span class='text-info'>Movies tailored to your taste based on your booking history</span>" 
                        : "Sign in to get personalized recommendations"; ?>
                </p>
                <?php if (!isLoggedIn()): ?>
                    <a href="login.php" class="btn btn-light mt-3">Login Now</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- Recommendations Section -->
<section class="py-5">
    <div class="container">
        <?php if (empty($recommended_movies)): ?>
            <div class="row">
                <div class="col-md-12 text-center">
                    <div class="alert alert-info">
                        <h4 class="alert-heading">No Recommendations Yet!</h4>
                        <p>
                            <?php echo isLoggedIn() 
                                ? "We don't have enough data to make personalized recommendations for you yet. Book some movies to get started!" 
                                : "Sign in to see personalized recommendations or browse popular movies."; ?>
                        </p>
                        <a href="movies.php" class="btn btn-primary mt-3">Browse Movies</a>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="row">
                <?php foreach ($recommended_movies as $movie): ?>
                    <div class="col-md-4 col-lg-3 mb-4">
                        <div class="card movie-card h-100 shadow-sm">
                            <div class="movie-poster">
                                <img src="<?php echo $movie['poster']; ?>" class="card-img-top" alt="<?php echo $movie['title']; ?>">
                                <div class="movie-rating">
                                    <span><i class="fas fa-star text-warning"></i> <?php echo number_format($movie['rating'], 1); ?></span>
                                </div>
                                <div class="movie-overlay">
                                    <a href="movie-details.php?id=<?php echo $movie['id']; ?>" class="btn btn-primary btn-sm">View Details</a>
                                </div>
                            </div>
                            <div class="card-body">
                                <h5 class="card-title movie-title"><?php echo $movie['title']; ?></h5>
                                <p class="card-text movie-info">
                                    <small class="text-muted">
                                        <i class="fas fa-film me-1"></i> <?php echo $movie['genre']; ?><br>
                                        <i class="fas fa-language me-1"></i> <?php echo $movie['language']; ?><br>
                                        <i class="fas fa-clock me-1"></i> <?php echo $movie['duration']; ?> mins
                                    </small>
                                </p>
                            </div>
                            <div class="card-footer bg-transparent border-top-0">
                                <a href="movie-details.php?id=<?php echo $movie['id']; ?>" class="btn btn-outline-primary btn-sm w-100">Book Now</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php
// Include footer
include 'includes/footer.php';
?>
