<?php
$page_title = "Contact Us";
require_once 'includes/functions.php';

$name = $email = $subject = $message = "";
$error = $success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = sanitize($_POST['name']);
    $email = sanitize($_POST['email']);
    $subject = sanitize($_POST['subject']);
    $message = sanitize($_POST['message']);
    
    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        $error = "Please fill all required fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } else {
        $result = submitContactForm($name, $email, $subject, $message);
        
        if ($result['success']) {
            $success = $result['message'];
            $name = $email = $subject = $message = "";
        } else {
            $error = $result['message'];
        }
    }
}

include 'includes/header.php';
?>

<div class="container py-5">
    <h1 class="mb-4">Contact Us</h1>
    
    <div class="row">
        <div class="col-lg-8">
            <div class="contact-form">
                <h3 class="mb-4">Get in Touch</h3>
                
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <?php if (!empty($success)): ?>
                    <div class="alert alert-success"><?php echo $success; ?></div>
                <?php endif; ?>
                
                <form id="contact-form" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">Your Name</label>
                            <input type="text" class="form-control" id="name" name="name" value="<?php echo $name; ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Your Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?php echo $email; ?>" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="subject" class="form-label">Subject</label>
                        <input type="text" class="form-control" id="subject" name="subject" value="<?php echo $subject; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="message" class="form-label">Message</label>
                        <textarea class="form-control" id="message" name="message" rows="5" required><?php echo $message; ?></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Send Message</button>
                </form>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="contact-info">
                <h3 class="mb-4">Contact Information</h3>
                
                <div class="contact-info-item">
                    <div class="contact-info-icon">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <div class="contact-info-content">
                        <h5>Our Location</h5>
                        <p>123 Movie Street, Cinema City, CA 12345, USA</p>
                    </div>
                </div>
                
                <div class="contact-info-item">
                    <div class="contact-info-icon">
                        <i class="fas fa-phone-alt"></i>
                    </div>
                    <div class="contact-info-content">
                        <h5>Phone Number</h5>
                        <p>+1 (123) 456-7890</p>
                    </div>
                </div>
                
                <div class="contact-info-item">
                    <div class="contact-info-icon">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <div class="contact-info-content">
                        <h5>Email Address</h5>
                        <p><?php echo ADMIN_EMAIL; ?></p>
                    </div>
                </div>
                
                <div class="contact-info-item">
                    <div class="contact-info-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="contact-info-content">
                        <h5>Working Hours</h5>
                        <p>Monday - Friday: 9:00 AM - 10:00 PM<br>
                        Saturday - Sunday: 10:00 AM - 11:00 PM</p>
                    </div>
                </div>
                
                <div class="contact-social">
                    <a href="#" class="contact-social-link"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="contact-social-link"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="contact-social-link"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="contact-social-link"><i class="fab fa-linkedin-in"></i></a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="map-section mt-5">
        <h3 class="mb-4">Find Us on Map</h3>
        <div class="ratio ratio-21x9"></div>
            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3022.215266754809!2d-73.98784492426385!3d40.75790937138223!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x89c25855c6480299%3A0x55194ec5a1ae072e!2sTimes%20Square!5e0!3m2!1sen!2sus!4v1710341987654!5m2!1sen!2sus" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>
    </div>
</div>

<?php
include 'includes/footer.php';
?>
