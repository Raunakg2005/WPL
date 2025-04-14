<?php
$page_title = "Admin Profile";

require_once '../includes/functions.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('../login.php');
}

$admin_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT * FROM users WHERE id = ? AND is_admin = 1");
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    redirect('../login.php');
}

$admin = $result->fetch_assoc();

$username = $admin['username'];
$email = $admin['email'];
$full_name = $admin['full_name'];
$error = $success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = sanitize($_POST['email']);
    $full_name = sanitize($_POST['full_name']);
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    if (empty($email) || empty($full_name)) {
        $error = "Please fill all required fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } else {
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $stmt->bind_param("si", $email, $admin_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $error = "Email already exists. Please use a different one.";
        } else {
            if (!empty($current_password) || !empty($new_password) || !empty($confirm_password)) {
                if (empty($current_password)) {
                    $error = "Please enter your current password.";
                } elseif (empty($new_password)) {
                    $error = "Please enter the new password.";
                } elseif (empty($confirm_password)) {
                    $error = "Please confirm the new password.";
                } elseif ($new_password != $confirm_password) {
                    $error = "New passwords do not match.";
                } elseif (strlen($new_password) < 6) {
                    $error = "New password must be at least 6 characters long.";
                } else {
                    if (!password_verify($current_password, $admin['password'])) {
                        $error = "Current password is incorrect.";
                    } else {
                        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                        
                        $stmt = $conn->prepare("UPDATE users SET email = ?, full_name = ?, password = ? WHERE id = ?");
                        $stmt->bind_param("sssi", $email, $full_name, $hashed_password, $admin_id);
                        
                        if ($stmt->execute()) {
                            $success = "Profile updated successfully!";
                            
                            $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
                            $stmt->bind_param("i", $admin_id);
                            $stmt->execute();
                            $result = $stmt->get_result();
                            $admin = $result->fetch_assoc();
                            
                            $username = $admin['username'];
                            $email = $admin['email'];
                            $full_name = $admin['full_name'];
                        } else {
                            $error = "Error: " . $stmt->error;
                        }
                    }
                }
            } else {
                $stmt = $conn->prepare("UPDATE users SET email = ?, full_name = ? WHERE id = ?");
                $stmt->bind_param("ssi", $email, $full_name, $admin_id);
                
                if ($stmt->execute()) {
                    $success = "Profile updated successfully!";
                    
                    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
                    $stmt->bind_param("i", $admin_id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $admin = $result->fetch_assoc();
                    
                    $username = $admin['username'];
                    $email = $admin['email'];
                    $full_name = $admin['full_name'];
                } else {
                    $error = "Error: " . $stmt->error;
                }
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
                <h1 class="h2">Admin Profile</h1>
            </div>
            
            <?php if (!empty($success)): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <i class="fas fa-user me-1"></i>
                            Profile Information
                        </div>
                        <div class="card-body text-center">
                            <div class="mb-3">
                                <img src="https://via.placeholder.com/150" alt="Admin Avatar" class="rounded-circle img-thumbnail">
                            </div>
                            <h5 class="card-title"><?php echo $full_name; ?></h5>
                            <p class="card-text text-muted"><?php echo $username; ?></p>
                            <p class="card-text"><?php echo $email; ?></p>
                            <p class="card-text">
                                <span class="badge bg-success">Administrator</span>
                            </p>
                            <p class="card-text">
                                <small class="text-muted">Member since: <?php echo date('F d, Y', strtotime($admin['created_at'])); ?></small>
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <i class="fas fa-edit me-1"></i>
                            Edit Profile
                        </div>
                        <div class="card-body">
                            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                                <div class="mb-3">
                                    <label for="username" class="form-label">Username</label>
                                    <input type="text" class="form-control" id="username" value="<?php echo $username; ?>" disabled>
                                    <div class="form-text text-muted">Username cannot be changed.</div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="email" class="form-label required-field">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" value="<?php echo $email; ?>" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="full_name" class="form-label required-field">Full Name</label>
                                    <input type="text" class="form-control" id="full_name" name="full_name" value="<?php echo $full_name; ?>" required>
                                </div>
                                
                                <hr class="my-4">
                                
                                <h4 class="mb-3">Change Password (Optional)</h4>
                                
                                <div class="mb-3">
                                    <label for="current_password" class="form-label">Current Password</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="current_password" name="current_password">
                                        <button class="btn btn-outline-secondary password-toggle" type="button" data-target="current_password">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                                
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
                                        <i class="fas fa-save me-2"></i>Update Profile
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<?php
include 'includes/footer.php';
?>