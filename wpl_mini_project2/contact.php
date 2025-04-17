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
        $stmt = $conn->prepare("INSERT INTO messages (name, email, subject, message, status, created_at) VALUES (?, ?, ?, ?, 'unread', NOW())");
        $stmt->bind_param("ssss", $name, $email, $subject, $message);

        if ($stmt->execute()) {
            $success = "Your message has been sent successfully!";
            $name = $email = $subject = $message = "";
        } else {
            $error = "Error: " . $stmt->error;
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
                        <p>KJSCE, Vidyavihar, Mumbai, Maharashtra, India</p>
                    </div>
                </div>
                
                <div class="contact-info-item">
                    <div class="contact-info-icon">
                        <i class="fas fa-phone-alt"></i>
                    </div>
                    <div class="contact-info-content">
                        <h5>Phone Number</h5>
                        <p>+91 9004575153</p>
                    </div>
                </div>
                
                <div class="contact-info-item">
                    <div class="contact-info-icon">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <div class="contact-info-content">
                        <h5>Email Address</h5>
                        <p>raunak.gupta@gmail.com</p>
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
                    <a href="https://www.instagram.com/ohh.itz_rkg/" class="contact-social-link"><i class="fab fa-instagram"></i></a>
                    <a href="https://www.linkedin.com/in/raunak-kumar-gupta-7b3503270/" class="contact-social-link"><i class="fab fa-linkedin-in"></i></a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="map-section mt-5">
        <h3 class="mb-4">Find Us on Map</h3>
        <div class="ratio ratio-21x9">
            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3770.7926506301715!2d72.89735127350376!3d19.07285205207469!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3be7c627a20bcaa9%3A0xb2fd3bcfeac0052a!2sK.%20J.%20Somaiya%20College%20of%20Engineering!5e0!3m2!1sen!2sin!4v1744798782085!5m2!1sen!2sin" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>
    </div>
</div>

<?php
include 'includes/footer.php';
?>