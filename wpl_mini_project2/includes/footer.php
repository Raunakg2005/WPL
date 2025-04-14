</main>
    
    <!-- Footer -->
    <footer class="footer mt-5 py-4 bg-dark text-white">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h5>About Us</h5>
                    <p>Experience the magic of cinema with our premium movie booking service. We bring the best movies to you.</p>
                </div>
                <div class="col-md-4">
                    <h5>Quick Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="index.php" class="text-white">Home</a></li>
                        <li><a href="movies.php" class="text-white">Movies</a></li>
                        <li><a href="about.php" class="text-white">About Us</a></li>
                        <li><a href="contact.php" class="text-white">Contact Us</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h5>Connect With Us</h5>
                    <div class="social-icons">
                        <a href="#" class="text-white me-2"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="text-white me-2"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-white me-2"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="text-white me-2"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
            </div>
            <hr class="bg-light">
            <div class="row">
                <div class="col-md-6">
                    <p>&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. All Rights Reserved.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p>
                        <a href="#" class="text-white">Privacy Policy</a> | 
                        <a href="#" class="text-white">Terms of Service</a>
                    </p>
                </div>
            </div>
        </div>
    </footer>
    
    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Custom JS -->
    <script src="assets/js/main.js"></script>
    
    <?php if (isset($page_specific_js)): ?>
    <script src="<?php echo $page_specific_js; ?>"></script>
    <?php endif; ?>
    
    <script>
        // Preloader
        $(window).on('load', function() {
            $('#preloader').fadeOut('slow');
        });
    </script>
</body>
</html>