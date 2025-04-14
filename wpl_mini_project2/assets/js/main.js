/**
 * Main JavaScript file for Movie Booking System
 */

// Wait for the DOM to be fully loaded
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Initialize popovers
    var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });
    
    // Navbar scroll effect
    const navbar = document.querySelector('.navbar');
    if (navbar) {
        window.addEventListener('scroll', function() {
            if (window.scrollY > 50) {
                navbar.classList.add('navbar-scrolled');
            } else {
                navbar.classList.remove('navbar-scrolled');
            }
        });
    }
    
    // Movie filter functionality
    const movieFilter = document.getElementById('movie-filter');
    if (movieFilter) {
        const filterForm = movieFilter.querySelector('form');
        const movieCards = document.querySelectorAll('.movie-card');
        
        filterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(filterForm);
            const searchTerm = formData.get('search').toLowerCase();
            const genre = formData.get('genre');
            const language = formData.get('language');
            
            // Redirect to movies.php with filter parameters
            let url = 'movies.php?';
            if (searchTerm) url += `search=${encodeURIComponent(searchTerm)}&`;
            if (genre) url += `genre=${encodeURIComponent(genre)}&`;
            if (language) url += `language=${encodeURIComponent(language)}`;
            
            window.location.href = url;
        });
    }
    
    // Seat selection functionality
    const seats = document.querySelectorAll('.seat-available');
    const selectedSeatsInput = document.getElementById('selected_seats');
    const totalSeatsInput = document.getElementById('seats_booked');
    const seatNumbersInput = document.getElementById('seat_numbers');
    const totalAmountElement = document.getElementById('total_amount');
    const ticketPriceElement = document.getElementById('ticket_price');

    if (seats.length > 0 && selectedSeatsInput && totalSeatsInput && seatNumbersInput && totalAmountElement && ticketPriceElement) {
        const ticketPrice = parseFloat(ticketPriceElement.textContent);
        
        seats.forEach(function(seat) {
            seat.addEventListener('click', function() {
                this.classList.toggle('seat-selected');
                
                // Update selected seats
                const selectedSeats = document.querySelectorAll('.seat-selected');
                const selectedSeatNumbers = Array.from(selectedSeats).map(seat => seat.textContent.trim());
                
                totalSeatsInput.value = selectedSeats.length;
                seatNumbersInput.value = selectedSeatNumbers.join(', ');
                
                // Update total amount
                const totalAmount = selectedSeats.length * ticketPrice;
                totalAmountElement.textContent = totalAmount.toFixed(2);
                
                // Enable/disable book button
                const bookButton = document.getElementById('book_button');
                if (bookButton) {
                    bookButton.disabled = selectedSeats.length === 0;
                }
            });
        });
    }
    
    // Booking form validation
    const bookingForm = document.getElementById('booking-form');
    if (bookingForm) {
        bookingForm.addEventListener('submit', function(e) {
            const selectedSeats = document.getElementById('seat-numbers').value;
            
            if (!selectedSeats) {
                e.preventDefault();
                alert('Please select at least one seat.');
                return false;
            }
            
            return true;
        });
    }
    
    // Contact form validation
    const contactForm = document.getElementById('contact-form');
    if (contactForm) {
        contactForm.addEventListener('submit', function(e) {
            let isValid = true;
            
            // Get form fields
            const nameInput = document.getElementById('name');
            const emailInput = document.getElementById('email');
            const subjectInput = document.getElementById('subject');
            const messageInput = document.getElementById('message');
            
            // Clear previous error messages
            document.querySelectorAll('.is-invalid').forEach(el => {
                el.classList.remove('is-invalid');
            });
            
            // Validate name
            if (!nameInput.value.trim()) {
                nameInput.classList.add('is-invalid');
                isValid = false;
            }
            
            // Validate email
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailInput.value.trim() || !emailRegex.test(emailInput.value.trim())) {
                emailInput.classList.add('is-invalid');
                isValid = false;
            }
            
            // Validate subject
            if (!subjectInput.value.trim()) {
                subjectInput.classList.add('is-invalid');
                isValid = false;
            }
            
            // Validate message
            if (!messageInput.value.trim()) {
                messageInput.classList.add('is-invalid');
                isValid = false;
            }
            
            if (!isValid) {
                e.preventDefault();
            }
        });
    }
    
    // Registration form validation
    const registerForm = document.getElementById('register-form');
    if (registerForm) {
        registerForm.addEventListener('submit', function(e) {
            let isValid = true;
            
            // Get form fields
            const usernameInput = document.getElementById('username');
            const emailInput = document.getElementById('email');
            const passwordInput = document.getElementById('password');
            const confirmPasswordInput = document.getElementById('confirm-password');
            const fullNameInput = document.getElementById('full-name');
            
            // Clear previous error messages
            document.querySelectorAll('.is-invalid').forEach(el => {
                el.classList.remove('is-invalid');
            });
            
            // Validate username
            if (!usernameInput.value.trim() || usernameInput.value.length < 3) {
                usernameInput.classList.add('is-invalid');
                isValid = false;
            }
            
            // Validate email
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailInput.value.trim() || !emailRegex.test(emailInput.value.trim())) {
                emailInput.classList.add('is-invalid');
                isValid = false;
            }
            
            // Validate password
            if (!passwordInput.value || passwordInput.value.length < 6) {
                passwordInput.classList.add('is-invalid');
                isValid = false;
            }
            
            // Validate confirm password
            if (passwordInput.value !== confirmPasswordInput.value) {
                confirmPasswordInput.classList.add('is-invalid');
                isValid = false;
            }
            
            // Validate full name
            if (!fullNameInput.value.trim()) {
                fullNameInput.classList.add('is-invalid');
                isValid = false;
            }
            
            if (!isValid) {
                e.preventDefault();
            }
        });
    }
    
    // Password toggle
    const passwordToggles = document.querySelectorAll('.password-toggle');
    passwordToggles.forEach(function(toggle) {
        toggle.addEventListener('click', function() {
            const targetId = this.getAttribute('data-target');
            const passwordInput = document.getElementById(targetId);
            const icon = this.querySelector('i');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    });
    
    // Movie rating functionality
    const ratingInputs = document.querySelectorAll('.rating-stars input');
    const ratingLabels = document.querySelectorAll('.rating-stars label');

    ratingInputs.forEach(function(input) {
        input.addEventListener('change', function() {
            const rating = this.value;
            
            ratingLabels.forEach(function(label) {
                const labelFor = label.getAttribute('for');
                const labelRating = labelFor.replace('rating', '');
                
                if (labelRating <= rating) {
                    label.querySelector('i').classList.remove('far');
                    label.querySelector('i').classList.add('fas');
                } else {
                    label.querySelector('i').classList.remove('fas');
                    label.querySelector('i').classList.add('far');
                }
            });
        });
    });
    
    // Parallax effect
    const parallaxElements = document.querySelectorAll('.parallax-section');
    if (parallaxElements.length > 0) {
        window.addEventListener('scroll', function() {
            parallaxElements.forEach(function(section) {
                const scrollPosition = window.pageYOffset;
                const sectionTop = section.offsetTop;
                const sectionHeight = section.offsetHeight;
                
                if (scrollPosition > sectionTop - window.innerHeight && scrollPosition < sectionTop + sectionHeight) {
                    const speed = 0.5; // Adjust the parallax speed
                    const yPos = (scrollPosition - sectionTop) * speed;
                    section.style.backgroundPosition = 'center ' + yPos + 'px';
                }
            });
        });
    }
    
    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            
            const targetId = this.getAttribute('href');
            if (targetId === '#') return;
            
            const targetElement = document.querySelector(targetId);
            if (targetElement) {
                targetElement.scrollIntoView({
                    behavior: 'smooth'
                });
            }
        });
    });
    
    // Animate elements on scroll
    const animateOnScroll = function() {
        const elements = document.querySelectorAll('.animate-on-scroll');
        
        elements.forEach(function(element) {
            const elementPosition = element.getBoundingClientRect().top;
            const windowHeight = window.innerHeight;
            
            if (elementPosition < windowHeight - 50) {
                element.classList.add('animated');
            }
        });
    };

    window.addEventListener('scroll', animateOnScroll);
    animateOnScroll(); // Run once on page load
    
    // Copy promotion code to clipboard
    const promotionCodes = document.querySelectorAll('.promotion-code');

    promotionCodes.forEach(function(code) {
        code.addEventListener('click', function() {
            const textToCopy = this.textContent.trim();
            
            navigator.clipboard.writeText(textToCopy).then(function() {
                // Show tooltip or alert
                alert('Promotion code copied: ' + textToCopy);
            }).catch(function(err) {
                console.error('Could not copy text: ', err);
            });
        });
    });
    
    // Mobile menu toggle
    const mobileMenuToggle = document.querySelector('.navbar-toggler');
    const navbarCollapse = document.querySelector('.navbar-collapse');

    if (mobileMenuToggle && navbarCollapse) {
        document.addEventListener('click', function(event) {
            const isClickInside = navbarCollapse.contains(event.target) || mobileMenuToggle.contains(event.target);
            
            if (!isClickInside && navbarCollapse.classList.contains('show')) {
                mobileMenuToggle.click();
            }
        });
    }
    
    // Movie trailer modal
    const trailerButtons = document.querySelectorAll('.trailer-btn');
    trailerButtons.forEach(button => {
        button.addEventListener('click', function() {
            const trailerUrl = this.getAttribute('data-trailer');
            const trailerModal = new bootstrap.Modal(document.getElementById('trailerModal'));
            const trailerIframe = document.getElementById('trailerIframe');
            
            trailerIframe.src = trailerUrl;
            trailerModal.show();
            
            // Reset iframe src when modal is closed
            document.getElementById('trailerModal').addEventListener('hidden.bs.modal', function() {
                trailerIframe.src = '';
            });
        });
    });
});