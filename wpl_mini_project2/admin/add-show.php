<?php
// Set page title
$page_title = "Add Show";

// Include functions file
require_once '../includes/functions.php';

// Check if user is logged in and is admin
if (!isLoggedIn() || !isAdmin()) {
    redirect('../login.php');
}

// Initialize variables
$movie_id = $theater_id = $show_date = $show_time = $price = $available_seats = "";
$error = $success = "";

// Get all movies
$stmt = $conn->prepare("SELECT id, title FROM movies WHERE status = 'now_showing' ORDER BY title");
$stmt->execute();
$result = $stmt->get_result();
$movies = [];
while ($row = $result->fetch_assoc()) {
    $movies[] = $row;
}

// Get all theaters
$stmt = $conn->prepare("SELECT id, name, location FROM theaters ORDER BY name");
$stmt->execute();
$result = $stmt->get_result();
$theaters = [];
while ($row = $result->fetch_assoc()) {
    $theaters[] = $row;
}

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $movie_id = intval($_POST['movie_id']);
    $theater_id = intval($_POST['theater_id']);
    $show_date = sanitize($_POST['show_date']);
    $show_time = sanitize($_POST['show_time']);
    $price = floatval($_POST['price']);
    $available_seats = intval($_POST['available_seats']);
    
    // Validate form data
    if ($movie_id <= 0 || $theater_id <= 0 || empty($show_date) || empty($show_time) || $price <= 0 || $available_seats <= 0) {
        $error = "Please fill all required fields with valid values.";
    } else {
        // Check if show already exists
        $stmt = $conn->prepare("
            SELECT id FROM shows 
            WHERE movie_id = ? AND theater_id = ? AND show_date = ? AND show_time = ?
        ");
        $stmt->bind_param("iiss", $movie_id, $theater_id, $show_date, $show_time);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $error = "A show with the same movie, theater, date, and time already exists.";
        } else {
            // Insert new show
            $stmt = $conn->prepare("
                INSERT INTO shows (movie_id, theater_id, show_date, show_time, price, available_seats)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $stmt->bind_param("iissdi", $movie_id, $theater_id, $show_date, $show_time, $price, $available_seats);
            
            if ($stmt->execute()) {
                $success = "Show added successfully!";
                // Clear form data
                $movie_id = $theater_id = $show_date = $show_time = $price = $available_seats = "";
            } else {
                $error = "Error: " . $stmt->error;
            }
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
                <h1 class="h2">Add New Show</h1>
                <a href="shows.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Shows
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
                    <i class="fas fa-calendar-alt me-1"></i>
                    Show Information
                </div>
                <div class="card-body">
                    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="movie_id" class="form-label required-field">Movie</label>
                                <select class="form-select" id="movie_id" name="movie_id" required>
                                    <option value="">Select Movie</option>
                                    <?php foreach ($movies as $movie): ?>
                                        <option value="<?php echo $movie['id']; ?>" <?php echo ($movie_id == $movie['id']) ? 'selected' : ''; ?>>
                                            <?php echo $movie['title']; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if (empty($movies)): ?>
                                    <div class="form-text text-danger">No movies available. Please add movies first.</div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="theater_id" class="form-label required-field">Theater</label>
                                <select class="form-select" id="theater_id" name="theater_id" required>
                                    <option value="">Select Theater</option>
                                    <?php foreach ($theaters as $theater): ?>
                                        <option value="<?php echo $theater['id']; ?>" <?php echo ($theater_id == $theater['id']) ? 'selected' : ''; ?>>
                                            <?php echo $theater['name']; ?> (<?php echo $theater['location']; ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if (empty($theaters)): ?>
                                    <div class="form-text text-danger">No theaters available. Please add theaters first.</div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="show_date" class="form-label required-field">Show Date</label>
                                <input type="date" class="form-control" id="show_date" name="show_date" value="<?php echo $show_date; ?>" min="<?php echo date('Y-m-d'); ?>" required>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="show_time" class="form-label required-field">Show Time</label>
                                <input type="time" class="form-control" id="show_time" name="show_time" value="<?php echo $show_time; ?>" required>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="price" class="form-label required-field">Ticket Price ($)</label>
                                <input type="number" class="form-control" id="price" name="price" value="<?php echo $price; ?>" min="0.01" step="0.01" required>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="available_seats" class="form-label required-field">Available Seats</label>
                                <input type="number" class="form-control" id="available_seats" name="available_seats" value="<?php echo $available_seats; ?>" min="1" required>
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Save Show
                            </button>
                            <a href="shows.php" class="btn btn-secondary ms-2">Cancel</a>
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