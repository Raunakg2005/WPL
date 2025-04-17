<?php
$page_title = "Home";

require_once 'includes/functions.php';

$trending_movies = getAllMovies(6);

$promotions = getActivePromotions();

include 'includes/header.php';
?>

<section class="hero">
    <div class="hero-overlay"></div>
    <img src="assets/uploads/hero-bg.gif" alt="Movie Theater" class="hero-bg">
    <div class="container hero-content">
        <div class="row">
            <div class="col-lg-8">
                <h1 class="hero-title">Experience the Magic of Cinema</h1>
                <p class="hero-subtitle">Book your tickets online and enjoy the latest movies in theaters near you.</p>
                <div class="hero-btn">
                    <a href="movies.php" class="btn btn-primary btn-lg me-3">Browse Movies</a>
                    <?php if (!isLoggedIn()): ?>
                        <a href="register.php" class="btn btn-outline-light btn-lg">Sign Up</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="section-title">Trending Movies</h2>
            <a href="movies.php" class="btn btn-outline-primary">View All</a>
        </div>
        <div class="row">
            <?php foreach ($trending_movies as $movie): ?>
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
                        <div class="movie-card-overlay">
                            <h5 class="movie-card-overlay-title"><?php echo $movie['title']; ?></h5>
                            <div class="movie-card-overlay-info">
                                <p><?php echo substr($movie['description'], 0, 100) . '...'; ?></p>
                                <p><strong>Director:</strong> <?php echo $movie['director']; ?></p>
                                <p><strong>Duration:</strong> <?php echo $movie['duration']; ?> mins</p>
                            </div>
                            <a href="movie-details.php?id=<?php echo $movie['id']; ?>" class="btn btn-light movie-card-overlay-btn">View Details</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="parallax-section" style="background-image: url('assets/uploads/about-parallax.gif'); background-blend-mode: lighten; background-color: rgba(255, 255, 255, 0.3);">
    <div class="parallax-overlay"></div>
    <div class="container parallax-content">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center">
                <h2 class="mb-4">Experience Movies Like Never Before</h2>
                <p class="mb-4">Immerse yourself in the world of cinema with our state-of-the-art theaters and premium viewing experience.</p>
                <a href="movies.php" class="btn btn-primary btn-lg">Book Now</a>
            </div>
        </div>
    </div>
</section>

<?php if (!empty($promotions)): ?>
<section class="py-5">
    <div class="container">
        <h2 class="section-title mb-4">Special Offers</h2>
        <div class="row">
            <?php foreach ($promotions as $promo): ?>
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <img src="<?php echo $promo['image']; ?>" class="card-img-top" alt="<?php echo $promo['title']; ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo $promo['title']; ?></h5>
                            <p class="card-text"><?php echo $promo['description']; ?></p>
                            <p class="text-muted">Valid until: <?php echo date('F d, Y', strtotime($promo['end_date'])); ?></p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<section class="py-5 bg-light">
    <div class="container">
        <h2 class="section-title text-center mb-5">Why Choose Us</h2>
        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="feature-icon mb-3">
                            <i class="fas fa-ticket-alt fa-3x text-primary"></i>
                        </div>
                        <h4 class="card-title">Easy Booking</h4>
                        <p class="card-text">Book your movie tickets in just a few clicks. No hassle, no waiting in lines.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="feature-icon mb-3">
                            <i class="fas fa-film fa-3x text-primary"></i>
                        </div>
                        <h4 class="card-title">Latest Movies</h4>
                        <p class="card-text">Get access to the latest blockbusters and indie films as soon as they release.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="feature-icon mb-3">
                            <i class="fas fa-thumbs-up fa-3x text-primary"></i>
                        </div>
                        <h4 class="card-title">Personalized Recommendations</h4>
                        <p class="card-text">Discover new movies based on your preferences and viewing history.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card border-0 shadow">
                    <div class="card-body p-5">
                        <h3 class="text-center mb-4">Subscribe to Our Newsletter</h3>
                        <p class="text-center mb-4">Stay updated with the latest movies, promotions, and exclusive offers.</p>
                        <form action="#" method="post" class="row g-3">
                            <div class="col-md-8">
                                <input type="email" class="form-control form-control-lg" placeholder="Your Email Address" required>
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-primary btn-lg w-100">Subscribe</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="modal fade" id="trailerModal" tabindex="-1" aria-labelledby="trailerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
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