<?php
$page_title = "Edit Theater";

require_once '../includes/functions.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('../login.php');
}

if (!isset($_GET['id']) || empty($_GET['id'])) {
    redirect('theaters.php');
}

$theater_id = intval($_GET['id']);

$stmt = $conn->prepare("SELECT * FROM theaters WHERE id = ?");
$stmt->bind_param("i", $theater_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    redirect('theaters.php');
}

$theater = $result->fetch_assoc();

$name = $theater['name'];
$location = $theater['location'];
$capacity = $theater['capacity'];
$facilities = $theater['facilities'];
$status = $theater['status'];
$error = $success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = sanitize($_POST['name']);
    $location = sanitize($_POST['location']);
    $capacity = intval($_POST['capacity']);
    $facilities = sanitize($_POST['facilities']);
    $status = isset($_POST['status']) ? 1 : 0;
    
    if (empty($name) || empty($location) || $capacity <= 0) {
        $error = "Please fill all required fields with valid values.";
    } else {
        $stmt = $conn->prepare("
            UPDATE theaters 
            SET name = ?, location = ?, capacity = ?, facilities = ?, status = ?
            WHERE id = ?
        ");
        $stmt->bind_param("ssissi", $name, $location, $capacity, $facilities, $status, $theater_id);
        
        if ($stmt->execute()) {
            $success = "Theater updated successfully!";
            
            $stmt = $conn->prepare("SELECT * FROM theaters WHERE id = ?");
            $stmt->bind_param("i", $theater_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $theater = $result->fetch_assoc();
            
            $name = $theater['name'];
            $location = $theater['location'];
            $capacity = $theater['capacity'];
            $facilities = $theater['facilities'];
            $status = $theater['status'];
        } else {
            $error = "Error: " . $stmt->error;
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
                <h1 class="h2">Edit Theater</h1>
                <a href="theaters.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Theaters
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
                    <i class="fas fa-building me-1"></i>
                    Theater Information
                </div>
                <div class="card-body">
                    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . '?id=' . $theater_id); ?>">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label required-field">Theater Name</label>
                                <input type="text" class="form-control" id="name" name="name" value="<?php echo $name; ?>" required>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="location" class="form-label required-field">Location</label>
                                <input type="text" class="form-control" id="location" name="location" value="<?php echo $location; ?>" required>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="capacity" class="form-label required-field">Seating Capacity</label>
                                <input type="number" class="form-control" id="capacity" name="capacity" value="<?php echo $capacity; ?>" min="1" required>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="facilities" class="form-label">Facilities</label>
                                <input type="text" class="form-control" id="facilities" name="facilities" value="<?php echo $facilities; ?>">
                                <div class="form-text">Separate multiple facilities with commas (e.g., Dolby Atmos, IMAX, 3D)</div>
                            </div>
                        </div>
                        
                        <div class="mb-3 form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="status" name="status" <?php echo ($status == 1) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="status">Active</label>
                        </div>
                        
                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Update Theater
                            </button>
                            <a href="theaters.php" class="btn btn-secondary ms-2">Cancel</a>
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