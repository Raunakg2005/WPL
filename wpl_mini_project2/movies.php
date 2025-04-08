<?php
$page_title = "Movies";
require_once 'includes/functions.php';

$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';
$genre = isset($_GET['genre']) ? sanitize($_GET['genre']) : '';
$language = isset($_GET['language']) ? sanitize($_GET['language']) : '';

if (!empty($search) || !empty($genre) || !empty($language)) {
    $movies = searchMovies($search, $genre, $language);
} else {
    $movies = getAllMovies(null);
}

$all_genres = getAllGenres();
$all_languages = getAllLanguages();

include 'includes/header.php';
?>

<div class="container py-5">
    <h1 class="mb-4">Movies</h1>
    
    <div class="card mb-4" id="movie-filter">
        <div class="card-body">
            <h5 class="card-title mb-3">Filter Movies</h5>
            <form method="get" action="movies.php"></form>
                <div class="row g-3">
                    <div class="col-md-6"></div>
                        <input type="text" class="form-control" name="search" placeholder="Search by title, director, cast..." value="<?php echo $search; ?>">
                    </div>
                    <div class="col-md-2">
                        <select class="form-select" name="genre">
                            <option value="">All Genres</option>
                            <?php foreach ($all_genres as $g): ?>
                                <option value="<?php echo $g; ?>" <?php echo ($genre == $g) ? 'selected' : ''; ?>><?php echo $g; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select class="form-select" name="language">
                            <option value="">All Languages</option>
                            <?php foreach ($all_languages as $l): ?>
                                <option value="<?php echo $l; ?>" <?php echo ($language == $l) ? 'selected' : ''; ?>><?php echo $l; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">Filter</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <?php if (empty($movies)): ?>
        <div class="alert alert-info">No movies found matching your criteria.</div>
    <?php else: ?>
        <div class="row">
            <?php foreach ($movies as $movie): ?>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="movie-card"></div>
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
