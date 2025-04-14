<?php
// Set page title
$page_title = "Add Promotion";

// Include functions file
require_once '../includes/functions.php';

// Check if user is logged in and is admin
if (!isLoggedIn() || !isAdmin()) {
    redirect('../login.php');
}

// Initialize variables
$title = $code = $description = $discount_type = $discount_value = $start_date = $end_date = "";
$error = $success = "";

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $title = sanitize($_POST['title']);
    $code = sanitize($_POST['code']);
    $description = sanitize($_POST['description']);
    $discount_type = sanitize($_POST['discount_type']);
    $discount_value = floatval($_POST['discount_value']);
    $start_date = sanitize($_POST['start_date']);
    $end_date = sanitize($_POST['end_date']);
    $status = isset($_POST['status']) ? 'active' : 'inactive';
    
    // Validate form data
    if (empty($title) || empty($code) || empty($discount_type) || $discount_value <= 0 || empty($start_date) || empty($end_date)) {
        $error = "Please fill all required fields with valid values.";
    } elseif (strtotime($end_date) < strtotime($start_date)) {
        $error = "End date cannot be earlier than start date.";
    } elseif ($discount_type == 'percentage' && $discount_value > 100) {
        $error = "Percentage discount cannot be greater than 100%.";
    } else {
        // Check if code already exists
        $stmt = $conn->prepare("SELECT id FROM promotions WHERE code = ?");
        $stmt->bind_param("s", $code);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $error = "Promotion code already exists. Please use a different one.";
        } else {
            // Insert promotion
            $stmt = $conn->prepare("
                INSERT INTO promotions (title, code, description, discount_type, discount_value, start_date, end_date, status)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->bind_param("ssssdsss", $title, $code, $description, $discount_type, $discount_value, $start_date, $end_date, $status);
            
            if ($stmt->execute()) {
                $success = "Promotion added successfully!";
                // Clear form data
                $title = $code = $description = $discount_type = $discount_value = $start_date = $end_date = "";
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
                <h1 class="h2">Add New Promotion</h1>
                <a href="promotions.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Promotions
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
                    <i class="fas fa-percentage me-1"></i>
                    Promotion Information
                </div>
                <div class="card-body">
                    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="title" class="form-label required-field">Promotion Title</label>
                                <input type="text" class="form-control" id="title" name="title" value="<?php echo $title; ?>" required>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="code" class="form-label required-field">Promotion Code</label>
                                <input type="text" class="form-control" id="code" name="code" value="<?php echo $code; ?>" required>
                                <div class="form-text">This is the code users will enter to apply the promotion.</div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3"><?php echo $description; ?></textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="discount_type" class="form-label required-field">Discount Type</label>
                                <select class="form-select" id="discount_type" name="discount_type" required>
                                    <option value="">Select Discount Type</option>
                                    <option value="percentage" <?php echo ($discount_type == 'percentage') ? 'selected' : ''; ?>>Percentage (%)</option>
                                    <option value="fixed" <?php echo ($discount_type == 'fixed') ? 'selected' : ''; ?>>Fixed Amount ($)</option>
                                </select>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="discount_value" class="form-label required-field">Discount Value</label>
                                <input type="number" class="form-control" id="discount_value" name="discount_value" value="<?php echo $discount_value; ?>" min="0.01" step="0.01" required>
                                <div class="form-text">For percentage, enter a value between 1 and 100. For fixed amount, enter the dollar value.</div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="start_date" class="form-label required-field">Start Date</label>
                                <input type="date" class="form-control" id="start_date" name="start_date" value="<?php echo $start_date; ?>" required>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="end_date" class="form-label required-field">End Date</label>
                                <input type="date" class="form-control" id="end_date" name="end_date" value="<?php echo $end_date; ?>" required>
                            </div>
                        </div>
                        
                        <div class="mb-3 form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="status" name="status" checked>
                            <label class="form-check-label" for="status">Active</label>
                        </div>
                        
                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Save Promotion
                            </button>
                            <a href="promotions.php" class="btn btn-secondary ms-2">Cancel</a>
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