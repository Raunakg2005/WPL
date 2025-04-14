<nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-dark sidebar collapse offcanvas-md offcanvas-start">
    <div class="offcanvas-header d-md-none">
        <h5 class="offcanvas-title text-white">Admin Menu</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" data-bs-target="#sidebarMenu" aria-label="Close"></button>
    </div>
    <div class="position-sticky pt-3">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'dashboard.php') ? 'active' : ''; ?>" href="dashboard.php">
                    <i class="fas fa-tachometer-alt me-2"></i>
                    Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'movies.php' || basename($_SERVER['PHP_SELF']) == 'add-movie.php' || basename($_SERVER['PHP_SELF']) == 'edit-movie.php') ? 'active' : ''; ?>" href="movies.php">
                    <i class="fas fa-film me-2"></i>
                    Movies
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'shows.php' || basename($_SERVER['PHP_SELF']) == 'add-show.php' || basename($_SERVER['PHP_SELF']) == 'edit-show.php') ? 'active' : ''; ?>" href="shows.php">
                    <i class="fas fa-calendar-alt me-2"></i>
                    Shows
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'theaters.php' || basename($_SERVER['PHP_SELF']) == 'add-theater.php' || basename($_SERVER['PHP_SELF']) == 'edit-theater.php') ? 'active' : ''; ?>" href="theaters.php">
                    <i class="fas fa-building me-2"></i>
                    Theaters
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'bookings.php' || basename($_SERVER['PHP_SELF']) == 'booking-details.php') ? 'active' : ''; ?>" href="bookings.php">
                    <i class="fas fa-ticket-alt me-2"></i>
                    Bookings
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'users.php' || basename($_SERVER['PHP_SELF']) == 'add-user.php' || basename($_SERVER['PHP_SELF']) == 'edit-user.php') ? 'active' : ''; ?>" href="users.php">
                    <i class="fas fa-users me-2"></i>
                    Users
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'promotions.php' || basename($_SERVER['PHP_SELF']) == 'add-promotion.php' || basename($_SERVER['PHP_SELF']) == 'edit-promotion.php') ? 'active' : ''; ?>" href="promotions.php">
                    <i class="fas fa-percentage me-2"></i>
                    Promotions
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'messages.php' || basename($_SERVER['PHP_SELF']) == 'view-message.php') ? 'active' : ''; ?>" href="messages.php">
                    <i class="fas fa-envelope me-2"></i>
                    Messages
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'reviews.php') ? 'active' : ''; ?>" href="reviews.php">
                    <i class="fas fa-star me-2"></i>
                    Reviews
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'reports.php') ? 'active' : ''; ?>" href="reports.php">
                    <i class="fas fa-chart-bar me-2"></i>
                    Reports
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'profile.php') ? 'active' : ''; ?>" href="profile.php">
                    <i class="fas fa-user-circle me-2"></i>
                    Profile
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'settings.php') ? 'active' : ''; ?>" href="settings.php">
                    <i class="fas fa-cog me-2"></i>
                    Settings
                </a>
            </li>
        </ul>
    </div>
</nav>