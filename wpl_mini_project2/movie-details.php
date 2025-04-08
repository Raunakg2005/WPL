<?php
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
$page_title = $movie['title'];
include 'includes/header.php';
?>

<div class="container py-5">
    <div class="movie-details">
        <div class="movie-details-header">
            <img src="<?php echo $movie['poster']; ?>" alt="<?php echo $movie['title']; ?>">
            <div class="movie-details-header-overlay">
                <h1 class="movie-details-title"><?php echo $movie['title']; ?></h1>
                <div class="movie-details-info">
                    <div class="movie-details-info-item">
                        <i class="fas fa-calendar-alt"></i>
                        <span><?php echo date('Y', strtotime($movie['release_date'])); ?></span>
                    </div>
                    <div class="movie-details-info-item">
                        <i class="fas fa-clock"></i>
                        <span><?php echo $movie['duration']; ?> mins</span>
                    </div>
                    <div class="movie-details-info-item">
                        <i class="fas fa-film"></i>
                        <span><?php echo $movie['genre']; ?></span>
                    </div>
                    <div class="movie-details-info-item">
                        <i class="fas fa-globe"></i>
                        <span><?php echo $movie['language']; ?></span>
                    </div>
                    <div class="movie-details-rating">
                        <i class="fas fa-star"></i>
                        <span><?php echo $movie['rating']; ?>/10</span>
                    </div>
                </div>
                <?php if (!empty($movie['trailer_url'])): ?>
                    <button class="btn btn-danger mt-3 trailer-btn" data-trailer="<?php echo $movie['trailer_url']; ?>">
                        <i class="fas fa-play me-2"></i>Watch Trailer
                    </button>
                <?php endif; ?>
            </div>
        </div>
        <div class="movie-details-body">
            <div class="row">
                <div class="col-lg-8">
                    <div class="movie-details-section">
                        <h3 class="movie-details-section-title">Synopsis</h3>
                        <p class="movie-details-description"><?php echo $movie['description']; ?></p>
                    </div>
                    
                    <div class="movie-details-section">
                        <h3 class="movie-details-section-title">Cast & Crew</h3>
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <strong>Director:</strong>
                            </div>
                            <div class="col-md-9">
                                <?php echo $movie['director']; ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <strong>Cast:</strong>
                            </div>
                            <div class="col-md-9">
                                <?php echo $movie['cast']; ?>
                            </div>
                        </div>
                    </div>
                    
                    <?php if (isLoggedIn()): ?>
                    <div class="movie-details-section">
                        <h3 class="movie-details-section-title">Rate & Review</h3>
                        <form id="rating-form" action="submit-review.php" method="post">
                            <input type="hidden" name="movie_id" value="<?php echo $movie_id; ?>">
                            <div class="mb-3">
                                <label class="form-label">Your Rating:</label>
                                <div class="rating-stars mb-2">
                                    <?php for ($i = 1; $i <= 10; $i++): ?>
                                        <span class="rating-star" data-value="<?php echo $i; ?>">
                                            <i class="far fa-star"></i>
                                        </span>
                                    <?php endfor; ?>
                                </div>
                                <input type="hidden" name="rating" id="rating" value="0">
                            </div>
                            <div class="mb-3">
                                <label for="comment" class="form-label">Your Review:</label>
                                <textarea class="form-control" id="comment" name="comment" rows="4"></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Submit Review</button>
                        </form>
                    </div>
                    <?php endif; ?>
                </div>
                
                <div class="col-lg-4">
                    <div class="card mb-4">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">Show Times</h5>
                        </div>
                        <div class="card-body">
                            <?php if (empty($shows)): ?>
                                <p class="text-muted">No shows available at the moment.</p>
                            <?php else: ?>
                                <?php 
                                $current_date = '';
                                foreach ($shows as $show): 
                                    $show_date = date('Y-m-d', strtotime($show['show_date']));
                                    if ($show_date != $current_date):
                                        $current_date = $show_date;
                                ?>
                                    <h6 class="mt-3 mb-2"><?php echo date('l, F d, Y', strtotime($show['show_date'])); ?></h6>
                                <?php endif; ?>
                                
                                <div class="show-time-item mb-2">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <span class="badge bg-secondary me-2"><?php echo date('h:i A', strtotime($show['show_time'])); ?></span>
                                            <small><?php echo $show['theater_name']; ?> (<?php echo $show['theater_location']; ?>)</small>
                                        </div>
                                        <div>
                                            <span class="text-success me-2">$<?php echo number_format($show['price'], 2); ?></span>
                                            <?php if (isLoggedIn()): ?>
                                                <a href="booking.php?show_id=<?php echo $show['id']; ?>" class="btn btn-sm btn-outline-primary">Book</a>
                                            <?php else: ?>
                                                <a href="login.php" class="btn btn-sm btn-outline-primary">Login to Book</a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

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
