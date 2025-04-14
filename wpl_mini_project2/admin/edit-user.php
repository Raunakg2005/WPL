<?php
// Set page title
$page_title = "Edit User";

// Include functions file
require_once '../includes/functions.php';

// Check if user is logged in and is admin
if (!isLoggedIn() || !isAdmin()) {
    redirect('../login.php');
}

// Check if user ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    redirect('users.php');
}

// Get user ID
$user_id = intval($_GET['id']);

// Get user details
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ? AND is_admin = 0");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// If user not found, redirect to users page
if ($result->num_rows == 0) {
    redirect('users.php');
}

// Get user data
$user = $result->fetch_assoc();

// Initialize variables
$username = $user['username'];
$email = $user['email'];
$full_name = $user['full_name'];
$status = $user['status'];
$error = $success = "";

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $email = sanitize($_POST['email']);
    $full_name = sanitize($_POST['full_name']);
    $status = isset($_POST['status']) ? 1 : 0;
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validate form data
    if (empty($email) || empty($full_name)) {
        $error = "Please fill all required fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } else {
        // Check if email already exists (excluding current user)
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $stmt->bind_param("si", $email, $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $error = "Email already exists. Please use a different one.";
        } else {
            // Check if password is being changed
            if (!empty($new_password) || !empty($confirm_password)) {
                // Validate new password
                if (empty($new_password)) {
                    $error = "Please enter the new password.";
                } elseif (empty($confirm_password)) {
                    $error = "Please confirm the new password.";
                } elseif ($new_password != $confirm_password) {
                    $error = "New passwords do not match.";
                } elseif (strlen($new_password) < 6) {
                    $error = "New password must be at least 6 characters long.";
                } else {
                    // Hash new password
                    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                    
                    // Update user with new password
                    $stmt = $conn->prepare("UPDATE users SET email = ?, full_name = ?, password = ?, status = ? WHERE id = ?");
                    $stmt->bind_param("sssis", $email, $full_name, $hashed_password, $status, $user_id);
                    
                    if ($stmt->execute()) {
                        $success = "User updated successfully!";
                    } else {
                        $error = "Error: " . $stmt->error;
                    }
                }
            } else {
                // Update user without changing password
                $stmt = $conn->prepare("UPDATE users SET email = ?, full_name = ?, status = ? WHERE id = ?");
                $stmt->bind_param("ssis", $email, $full_name, $status, $user_id);
                
                if ($stmt->execute()) {
                    $success = "User updated successfully!";
                } else {
                    $error = "Error: " . $stmt->error;
                }
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
                <h1 class="h2">Edit User</h1>
                <a href="users.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Users
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
                    <i class="fas fa-user-edit me-1"></i>
                    User Information
                </div>
                <div class="card-body">
                    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . '?id=' . $user_id); ?>">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control" id="username" value="<?php echo $username; ?>" disabled>
                                <div class="form-text text-muted">Username cannot be changed.</div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label required-field">Email</label>
                                <input type="email" class="form-control" id="email" name="email" value="<?php echo $email; ?>" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="full_name" class="form-label required-field">Full Name</label>
                            <input type="text" class="form-control" id="full_name" name="full_name" value="<?php echo $full_name; ?>" required>
                        </div>
                        
                        <div class="mb-3 form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="status" name="status" <?php echo ($status == 1) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="status">Active</label>
                        </div>
                        
                        <hr class="my-4">
                        
                        <h4 class="mb-3">Change Password (Optional)</h4>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="new_password" class="form-label">New Password</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="new_password" name="new_password">
                                    <button class="btn btn-outline-secondary password-toggle" type="button" data-target="new_password">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                <div class="form-text">Password must be at least 6 characters long.</div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="confirm_password" class="form-label">Confirm New Password</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="confirm_password" name="confirm_password">
                                    <button class="btn btn-outline-secondary password-toggle" type="button" data-target="confirm_password">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Update User
                            </button>
                            <a href="users.php" class="btn btn-secondary ms-2">Cancel</a>
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