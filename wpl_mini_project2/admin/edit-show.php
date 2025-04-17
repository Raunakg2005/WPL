<?php
$page_title = "Edit Show";

require_once '../includes/functions.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('../login.php');
}

if (!isset($_GET['id']) || empty($_GET['id'])) {
    redirect('shows.php');
}

$show_id = intval($_GET['id']);

$stmt = $conn->prepare("SELECT * FROM shows WHERE id = ?");
$stmt->bind_param("i", $show_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    redirect('shows.php');
}

$show = $result->fetch_assoc();

$movie_id = $show['movie_id'];
$theater_id = $show['theater_id'];
$show_date = $show['show_date'];
$show_time = $show['show_time'];
$price = $show['price'];
$available_seats = $show['available_seats'];
$error = $success = "";

$stmt = $conn->prepare("SELECT id, title FROM movies ORDER BY title");
$stmt->execute();
$result = $stmt->get_result();
$movies = [];
while ($row = $result->fetch_assoc()) {
    $movies[] = $row;
}

$stmt = $conn->prepare("SELECT id, name, location FROM theaters ORDER BY name");
$stmt->execute();
$result = $stmt->get_result();
$theaters = [];
while ($row = $result->fetch_assoc()) {
    $theaters[] = $row;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $movie_id = intval($_POST['movie_id']);
    $theater_id = intval($_POST['theater_id']);
    $show_date = sanitize($_POST['show_date']);
    $show_time = sanitize($_POST['show_time']);
    $price = floatval($_POST['price']);
    $available_seats = intval($_POST['available_seats']);
    
    if ($movie_id <= 0 || $theater_id <= 0 || empty($show_date) || empty($show_time) || $price <= 0 || $available_seats <= 0) {
        $error = "Please fill all required fields with valid values.";
    } else {
        $stmt = $conn->prepare("
            SELECT id FROM shows 
            WHERE movie_id = ? AND theater_id = ? AND show_date = ? AND show_time = ? AND id != ?
        ");
        $stmt->bind_param("iissi", $movie_id, $theater_id, $show_date, $show_time, $show_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $error = "A show with the same movie, theater, date, and time already exists.";
        } else {
            $stmt = $conn->prepare("
                UPDATE shows 
                SET movie_id = ?, theater_id = ?, show_date = ?, show_time = ?, price = ?, available_seats = ?
                WHERE id = ?
            ");
            $stmt->bind_param("iissdii", $movie_id, $theater_id, $show_date, $show_time, $price, $available_seats, $show_id);
            
            if ($stmt->execute()) {
                $success = "Show updated successfully!";
                
                $stmt = $conn->prepare("SELECT * FROM shows WHERE id = ?");
                $stmt->bind_param("i", $show_id);
                $stmt->execute();
                $result = $stmt->get_result();
                $show = $result->fetch_assoc();
            } else {
                $error = "Error: " . $stmt->error;
            }
        }
    }
}

include 'includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <?php include 'includes/sidebar.php'; ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Edit Show</h1>
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
                    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . '?id=' . $show_id); ?>">
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
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="show_date" class="form-label required-field">Show Date</label>
                                <input type="date" class="form-control" id="show_date" name="show_date" value="<?php echo $show_date; ?>" required>
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
                                <i class="fas fa-save me-2"></i>Update Show
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
include 'includes/footer.php';
?>