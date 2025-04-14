<?php
// Set page title
$page_title = "About Us";

// Include header
require_once 'includes/header.php';
?>

<div class="container py-5">
    <div class="about-section mb-5">
        <h1 class="about-section-title">About Us</h1>
        <div class="about-content">
            <p>Welcome to <?php echo SITE_NAME; ?>, your premier destination for movie booking and recommendations. We are passionate about cinema and committed to providing you with the best movie-going experience.</p>
            <p>Our platform offers a seamless and convenient way to browse movies, check showtimes, book tickets, and discover new films tailored to your preferences. With our user-friendly interface and personalized recommendations, finding your next favorite movie has never been easier.</p>
            <p>At <?php echo SITE_NAME; ?>, we believe that movies have the power to inspire, entertain, and bring people together. Our mission is to make the joy of cinema accessible to everyone, anytime, anywhere.</p>
        </div>
    </div>
    
    <div class="about-section mb-5">
        <h2 class="about-section-title">Our Story</h2>
        <div class="about-content">
            <p>Founded in 2023, <?php echo SITE_NAME; ?> was born out of a simple idea: to create a platform that makes movie booking hassle-free and enjoyable. What started as a small project has now grown into a comprehensive service trusted by thousands of movie enthusiasts.</p>
            <p>Our journey has been driven by a deep love for cinema and a commitment to innovation. We continuously strive to enhance our platform with new features and improvements based on user feedback and industry trends.</p>
        </div>
    </div>
    
    <div class="about-section mb-5">
        <h2 class="about-section-title">Our Vision</h2>
        <div class="about-content">
            <p>We envision a world where everyone can experience the magic of cinema without barriers. Our goal is to become the leading movie booking and recommendation platform, known for our exceptional user experience, personalized service, and innovative features.</p>
            <p>We are committed to:</p>
            <ul>
                <li>Providing a seamless and intuitive booking experience</li>
                <li>Offering personalized movie recommendations based on user preferences</li>
                <li>Supporting the film industry by promoting diverse and quality cinema</li>
                <li>Continuously improving our platform to meet the evolving needs of our users</li>
            </ul>
        </div>
    </div>
    
    <!-- Parallax Section -->
    <section class="parallax-section mb-5" style="background-image: url('assets/uploads/about-parallax.jpg');">
        <div class="parallax-overlay"></div>
        <div class="container parallax-content">
            <div class="row justify-content-center">
                <div class="col-lg-8 text-center">
                    <h2 class="mb-4">Our Commitment to Excellence</h2>
                    <p class="mb-0">We are dedicated to providing you with the best movie booking experience possible. From the latest blockbusters to indie gems, we've got you covered.</p>
                </div>
            </div>
        </div>
    </section>
    
    <div class="about-section mb-5">
        <h2 class="about-section-title">Our Team</h2>
        <div class="row">
            <div class="col-md-4">
                <div class="team-member">
                    <img src="assets/uploads/team1.jpg" alt="Team Member" class="team-member-img">
                    <h4 class="team-member-name">Raunak Kumar Gupta</h4>
                    <p class="team-member-role">Frontend Developer</p>
                    <div class="team-member-social">
                        <a href="https://www.instagram.com/ohh.itz_rkg/" class="team-member-social-link"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="team-member-social-link"><i class="fab fa-twitter"></i></a>
                        <a href="https://www.linkedin.com/in/raunak-kumar-gupta-7b3503270/" class="team-member-social-link"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="team-member">
                    <img src="assets/uploads/team2.jpg" alt="Team Member" class="team-member-img">
                    <h4 class="team-member-name">Rohan Singh Chauhan</h4>
                    <p class="team-member-role">UI/UX Designer</p>
                    <div class="team-member-social">
                        <a href="#" class="team-member-social-link"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="team-member-social-link"><i class="fab fa-twitter"></i></a>
                        <a href="https://www.linkedin.com/in/rohansinghchauhan/" class="team-member-social-link"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="team-member">
                    <img src="assets/uploads/team3.jpg" alt="Team Member" class="team-member-img">
                    <h4 class="team-member-name">RISHIKESH RAMALINGAM</h4>
                    <p class="team-member-role">Backend developer</p>
                    <div class="team-member-social">
                        <a href="#" class="team-member-social-link"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="team-member-social-link"><i class="fab fa-twitter"></i></a>
                        <a href="https://www.linkedin.com/in/rishikesh-ramalingam-0b9598352/" class="team-member-social-link"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="about-section">
        <h2 class="about-section-title">Our Milestones</h2>
        <div class="timeline">
            <div class="timeline-item">
                <div class="timeline-content">
                    <div class="timeline-date">2023</div>
                    <h4 class="timeline-title">Launch of <?php echo SITE_NAME; ?></h4>
                    <p class="timeline-description">We launched our platform with basic movie booking functionality.</p>
                </div>
            </div>
            <div class="timeline-item">
                <div class="timeline-content">
                    <div class="timeline-date">2023</div>
                    <h4 class="timeline-title">Introduction of Recommendation System</h4>
                    <p class="timeline-description">We implemented a personalized movie recommendation system based on user preferences and booking history.</p>
                </div>
            </div>
            <div class="timeline-item">
                <div class="timeline-content">
                    <div class="timeline-date">2023</div>
                    <h4 class="timeline-title">Mobile App Launch</h4>
                    <p class="timeline-description">We expanded our services with the launch of our mobile app for iOS and Android.</p>
                </div>
            </div>
            <div class="timeline-item">
                <div class="timeline-content">
                    <div class="timeline-date">2024</div>
                    <h4 class="timeline-title">Partnership with Major Theaters</h4>
                    <p class="timeline-description">We established partnerships with major theater chains to offer exclusive deals and promotions.</p>
                </div>
            </div>
            <div class="timeline-item">
                <div class="timeline-content">
                    <div class="timeline-date">Future</div>
                    <h4 class="timeline-title">Global Expansion</h4>
                    <p class="timeline-description">We plan to expand our services globally, bringing the joy of cinema to movie lovers worldwide.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Include footer
include 'includes/footer.php';
?>