<?php
// Set page title
$page_title = "Add Movie";

// Include functions file
require_once '../includes/functions.php';

// Check if user is logged in and is admin
if (!isLoggedIn() || !isAdmin()) {
    redirect('../login.php');
}

// Initialize variables
$title = $description = $release_date = $duration = $genre = $language = $director = $cast = $trailer_url = $rating = $status = "";
$error = $success = "";

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $title = sanitize($_POST['title']);
    $description = sanitize($_POST['description']);
    $release_date = sanitize($_POST['release_date']);
    $duration = intval($_POST['duration']);
    $genre = sanitize($_POST['genre']);
    $language = sanitize($_POST['language']);
    $director = sanitize($_POST['director']);
    $cast = sanitize($_POST['cast']);
    $trailer_url = sanitize($_POST['trailer_url']);
    $rating = floatval($_POST['rating']);
    $status = sanitize($_POST['status']);
    
    // Validate form data
    if (empty($title) || empty($description) || empty($release_date) || $duration <= 0 || empty($genre) || empty($language) || empty($director) || empty($cast)) {
        $error = "Please fill all required fields.";
    } else {
        // Prepare movie data
        $movie_data = [
            'title' => $title,
            'description' => $description,
            'release_date' => $release_date,
            'duration' => $duration,
            'genre' => $genre,
            'language' => $language,
            'director' => $director,
            'cast' => $cast,
            'trailer_url' => $trailer_url,
            'rating' => $rating,
            'status' => $status
        ];
        
        // Save movie without requiring poster
        $result = saveMovie($movie_data, $_FILES['poster'] ?? null);
        
        if ($result['success']) {
            $success = $result['message'];
            // Clear form data
            $title = $description = $release_date = $duration = $genre = $language = $director = $cast = $trailer_url = $rating = $status = "";
        } else {
            $error = $result['message'];
        }
    }
}

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
                <h1 class="h2">Add New Movie</h1>
                <a href="movies.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Movies
                </a>
            </div>
            
            <?php if (!empty($success)): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-film me-1"></i>
                    Movie Information
                </div>
                <div class="card-body">
                    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="title" class="form-label required-field">Title</label>
                                    <input type="text" class="form-control" id="title" name="title" value="<?php echo $title; ?>" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="description" class="form-label required-field">Description</label>
                                    <textarea class="form-control" id="description" name="description" rows="5" required><?php echo $description; ?></textarea>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="release_date" class="form-label required-field">Release Date</label>
                                        <input type="date" class="form-control" id="release_date" name="release_date" value="<?php echo $release_date; ?>" required>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="duration" class="form-label required-field">Duration (minutes)</label>
                                        <input type="number" class="form-control" id="duration" name="duration" value="<?php echo $duration; ?>" min="1" required>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="genre" class="form-label required-field">Genre</label>
                                        <input type="text" class="form-control" id="genre" name="genre" value="<?php echo $genre; ?>" placeholder="e.g. Action, Drama, Comedy" required>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="language" class="form-label required-field">Language</label>
                                        <input type="text" class="form-control" id="language" name="language" value="<?php echo $language; ?>" required>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="director" class="form-label required-field">Director</label>
                                    <input type="text" class="form-control" id="director" name="director" value="<?php echo $director; ?>" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="cast" class="form-label required-field">Cast</label>
                                    <input type="text" class="form-control" id="cast" name="cast" value="<?php echo $cast; ?>" placeholder="e.g. Actor 1, Actor 2, Actor 3" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="trailer_url" class="form-label">Trailer URL (YouTube Embed)</label>
                                    <input type="text" class="form-control" id="trailer_url" name="trailer_url" value="<?php echo $trailer_url; ?>" placeholder="e.g. https://www.youtube.com/embed/abcdefg">
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="rating" class="form-label">Rating (0-10)</label>
                                        <input type="number" class="form-control" id="rating" name="rating" value="<?php echo $rating; ?>" min="0" max="10" step="0.1">
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="status" class="form-label required-field">Status</label>
                                        <select class="form-select" id="status" name="status" required>
                                            <option value="now_showing" <?php echo ($status == 'now_showing') ? 'selected' : ''; ?>>Now Showing</option>
                                            <option value="coming_soon" <?php echo ($status == 'coming_soon') ? 'selected' : ''; ?>>Coming Soon</option>
                                            <option value="archived" <?php echo ($status == 'archived') ? 'selected' : ''; ?>>Archived</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="poster" class="form-label">Poster Image</label>
                                    <div class="file-upload">
                                        <input type="file" class="form-control" id="poster" name="poster" accept="image/*">
                                    </div>
                                    <small class="form-text text-muted">Recommended size: 500x750 pixels</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Save Movie
                            </button>
                            <a href="movies.php" class="btn btn-secondary ms-2">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
</div>

<?php
// Include footer
include 'includes/footer.php';
?>