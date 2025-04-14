<?php
$page_title = "System Settings";

require_once '../includes/functions.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('../login.php');
}

$error = $success = "";

$settings = [];
$result = $conn->query("SELECT * FROM settings");
while ($row = $result->fetch_assoc()) {
    $settings[$row['setting_key']] = $row['setting_value'];
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $site_name = sanitize($_POST['site_name']);
    $site_email = sanitize($_POST['site_email']);
    $site_phone = sanitize($_POST['site_phone']);
    $site_address = sanitize($_POST['site_address']);
    $booking_fee = floatval($_POST['booking_fee']);
    $tax_rate = floatval($_POST['tax_rate']);
    $maintenance_mode = isset($_POST['maintenance_mode']) ? 1 : 0;
    $allow_reviews = isset($_POST['allow_reviews']) ? 1 : 0;
    $auto_approve_reviews = isset($_POST['auto_approve_reviews']) ? 1 : 0;
    
    if (empty($site_name) || empty($site_email)) {
        $error = "Please fill all required fields.";
    } elseif (!filter_var($site_email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } elseif ($booking_fee < 0) {
        $error = "Booking fee cannot be negative.";
    } elseif ($tax_rate < 0 || $tax_rate > 100) {
        $error = "Tax rate must be between 0 and 100.";
    } else {
        $settings_to_update = [
            'site_name' => $site_name,
            'site_email' => $site_email,
            'site_phone' => $site_phone,
            'site_address' => $site_address,
            'booking_fee' => $booking_fee,
            'tax_rate' => $tax_rate,
            'maintenance_mode' => $maintenance_mode,
            'allow_reviews' => $allow_reviews,
            'auto_approve_reviews' => $auto_approve_reviews
        ];
        
        $conn->begin_transaction();
        
        try {
            foreach ($settings_to_update as $key => $value) {
                $stmt = $conn->prepare("SELECT id FROM settings WHERE setting_key = ?");
                $stmt->bind_param("s", $key);
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($result->num_rows > 0) {
                    $stmt = $conn->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = ?");
                    $stmt->bind_param("ss", $value, $key);
                } else {
                    $stmt = $conn->prepare("INSERT INTO settings (setting_key, setting_value) VALUES (?, ?)");
                    $stmt->bind_param("ss", $key, $value);
                }
                
                $stmt->execute();
            }
            
            $conn->commit();
            
            $success = "Settings updated successfully!";
            
            $settings = [];
            $result = $conn->query("SELECT * FROM settings");
            while ($row = $result->fetch_assoc()) {
                $settings[$row['setting_key']] = $row['setting_value'];
            }
        } catch (Exception $e) {
            $conn->rollback();
            
            $error = "Error: " . $e->getMessage();
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
                <h1 class="h2">System Settings</h1>
            </div>
            
            <?php if (!empty($success)): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-cog me-1"></i>
                    General Settings
                </div>
                <div class="card-body">
                    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h5>Site Information</h5>
                                <div class="mb-3">
                                    <label for="site_name" class="form-label required-field">Site Name</label>
                                    <input type="text" class="form-control" id="site_name" name="site_name" value="<?php echo $settings['site_name'] ?? 'Movie Booking System'; ?>" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="site_email" class="form-label required-field">Site Email</label>
                                    <input type="email" class="form-control" id="site_email" name="site_email" value="<?php echo $settings['site_email'] ?? 'info@moviebooking.com'; ?>" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="site_phone" class="form-label">Site Phone</label>
                                    <input type="text" class="form-control" id="site_phone" name="site_phone" value="<?php echo $settings['site_phone'] ?? '+1 (123) 456-7890'; ?>">
                                </div>
                                
                                <div class="mb-3">
                                    <label for="site_address" class="form-label">Site Address</label>
                                    <textarea class="form-control" id="site_address" name="site_address" rows="3"><?php echo $settings['site_address'] ?? '123 Movie Street, Hollywood, CA 90210'; ?></textarea>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <h5>Booking Settings</h5>
                                <div class="mb-3">
                                    <label for="booking_fee" class="form-label">Booking Fee ($)</label>
                                    <input type="number" class="form-control" id="booking_fee" name="booking_fee" value="<?php echo $settings['booking_fee'] ?? '2.00'; ?>" min="0" step="0.01">
                                    <div class="form-text">This fee will be added to each booking.</div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="tax_rate" class="form-label">Tax Rate (%)</label>
                                    <input type="number" class="form-control" id="tax_rate" name="tax_rate" value="<?php echo $settings['tax_rate'] ?? '8.00'; ?>" min="0" max="100" step="0.01">
                                </div>
                                
                                <h5 class="mt-4">System Settings</h5>
                                <div class="mb-3 form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="maintenance_mode" name="maintenance_mode" <?php echo (isset($settings['maintenance_mode']) && $settings['maintenance_mode'] == 1) ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="maintenance_mode">Maintenance Mode</label>
                                    <div class="form-text">When enabled, only administrators can access the site.</div>
                                </div>
                                
                                <div class="mb-3 form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="allow_reviews" name="allow_reviews" <?php echo (!isset($settings['allow_reviews']) || $settings['allow_reviews'] == 1) ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="allow_reviews">Allow User Reviews</label>
                                </div>
                                
                                <div class="mb-3 form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="auto_approve_reviews" name="auto_approve_reviews" <?php echo (isset($settings['auto_approve_reviews']) && $settings['auto_approve_reviews'] == 1) ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="auto_approve_reviews">Auto-Approve Reviews</label>
                                    <div class="form-text">When enabled, user reviews will be automatically approved.</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Save Settings
                            </button>
                            <button type="reset" class="btn btn-secondary ms-2">Reset</button>
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