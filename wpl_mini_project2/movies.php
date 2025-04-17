<?php
$page_title = "Movies";

require_once 'includes/functions.php';

$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';
$genre = isset($_GET['genre']) ? sanitize($_GET['genre']) : '';
$language = isset($_GET['language']) ? sanitize($_GET['language']) : '';

$genres = getAllGenres();
$languages = getAllLanguages();

$movies = searchMovies($search, $genre, $language);

include 'includes/header.php';
?>

<section class="movies-banner parallax-section" style="background-image: url('assets/uploads/movies-banner.jpg'); background-size: cover; background-position: center; background-attachment: fixed;">
    <div class="container">
        <div class="row">
            <div class="col-md-12 text-center">
                <h1 class="display-4 text-danger">Discover Movies</h1>
                <p class="lead" style="color: #FFD700;">Find the perfect movie for your entertainment</p>
            </div>
        </div>
    </div>
</section>

<section class="py-5 bg-light">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <form method="get" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="row g-3">
                            <div class="col-md-4">
                                <label for="search" class="form-label">Search</label>
                                <input type="text" class="form-control" id="search" name="search" value="<?php echo $search; ?>" placeholder="Search by title, director, or cast">
                            </div>
                            
                            <div class="col-md-3">
                                <label for="genre" class="form-label">Genre</label>
                                <select class="form-select" id="genre" name="genre">
                                    <option value="">All Genres</option>
                                    <?php foreach ($genres as $g): ?>
                                        <option value="<?php echo $g; ?>" <?php echo ($genre == $g) ? 'selected' : ''; ?>><?php echo $g; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="col-md-3">
                                <label for="language" class="form-label">Language</label>
                                <select class="form-select" id="language" name="language">
                                    <option value="">All Languages</option>
                                    <?php foreach ($languages as $l): ?>
                                        <option value="<?php echo $l; ?>" <?php echo ($language == $l) ? 'selected' : ''; ?>><?php echo $l; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-search me-2"></i>Filter
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <?php if (empty($movies)): ?>
            <div class="row">
                <div class="col-md-12 text-center">
                    <div class="alert alert-info">
                        <h4 class="alert-heading">No Movies Found!</h4>
                        <p>Sorry, no movies match your search criteria. Please try different filters.</p>
                        <a href="movies.php" class="btn btn-primary mt-3">View All Movies</a>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="row">
                <?php foreach ($movies as $movie): ?>
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
include 'includes/footer.php';
?>