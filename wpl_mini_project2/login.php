<?php
// Set page title
$page_title = "Login";

// Include functions file
require_once 'includes/functions.php';

// Check if user is already logged in
if (isLoggedIn()) {
    redirect('index.php');
}

// Initialize variables
$username = $password = "";
$error = "";

// Process login form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $username = sanitize($_POST['username']);
    $password = $_POST['password'];
    
    // Validate form data
    if (empty($username) || empty($password)) {
        $error = "Please enter both username and password.";
    } else {
        // Check if username exists
        $stmt = $conn->prepare("SELECT id, username, password, full_name, is_admin FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();
            
            // Verify password
            if (password_verify($password, $user['password'])) {
                // Password is correct, start a new session
                session_start();
                
                // Store data in session variables
                $_SESSION["user_id"] = $user['id'];
                $_SESSION["username"] = $user['username'];
                $_SESSION["full_name"] = $user['full_name'];
                $_SESSION["is_admin"] = $user['is_admin'];
                
                // Redirect user to appropriate page
                if ($user['is_admin']) {
                    redirect('admin/dashboard.php');
                } else {
                    redirect('user-dashboard.php');
                }
            } else {
                $error = "Invalid password.";
            }
        } else {
            $error = "Username not found.";
        }
    }
}

// Include header
include 'includes/header.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-body p-5">
                    <h2 class="text-center mb-4">Login</h2>
                    
                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>
                    
                    <form id="login-form" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="username" name="username" value="<?php echo $username; ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="password" name="password" required>
                                <button class="btn btn-outline-secondary password-toggle" type="button" data-target="password">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="remember-me" name="remember-me">
                            <label class="form-check-label" for="remember-me">Remember me</label>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">Login</button>
                        </div>
                    </form>
                    
                    <div class="text-center mt-4">
                        <p>Don't have an account? <a href="register.php">Register here</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Include footer
include 'includes/footer.php';
?>