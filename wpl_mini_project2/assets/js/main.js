document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });
    
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
            
            let url = 'movies.php?';
            if (searchTerm) url += `search=${encodeURIComponent(searchTerm)}&`;
            if (genre) url += `genre=${encodeURIComponent(genre)}&`;
            if (language) url += `language=${encodeURIComponent(language)}`;
            
            window.location.href = url;
        });
    }
    
    const seatSelection = document.querySelector('.seat-selection');
    if (seatSelection) {
        const seats = seatSelection.querySelectorAll('.seat:not(.booked)');
        const selectedSeatsElement = document.getElementById('selected-seats');
        const totalPriceElement = document.getElementById('total-price');
        const seatNumbersInput = document.getElementById('seat-numbers');
        const seatsBookedInput = document.getElementById('seats-booked');
        const totalAmountInput = document.getElementById('total-amount');
        const submitButton = document.querySelector('button[type="submit"]');

        let ticketPrice = parseFloat(totalPriceElement.dataset.price);
        let selectedSeats = [];

        seats.forEach(seat => {
            seat.addEventListener('click', function () {
                const seatNumber = this.parentElement.querySelector('.seat-row-label').textContent + this.textContent;

                if (this.classList.contains('selected')) {
                    this.classList.remove('selected');
                    selectedSeats = selectedSeats.filter(s => s !== seatNumber);
                } else {
                    this.classList.add('selected');
                    selectedSeats.push(seatNumber);
                }

                seatNumbersInput.value = selectedSeats.join(',');
                seatsBookedInput.value = selectedSeats.length;
                totalAmountInput.value = (selectedSeats.length * ticketPrice).toFixed(2);
                selectedSeatsElement.textContent = selectedSeats.length > 0 ? selectedSeats.join(', ') : 'None';
                totalPriceElement.textContent = (selectedSeats.length * ticketPrice).toFixed(2);
                submitButton.disabled = selectedSeats.length === 0;
            });
        });
    }
    
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
    
    const contactForm = document.getElementById('contact-form');
    if (contactForm) {
        contactForm.addEventListener('submit', function(e) {
            let isValid = true;
            
            const nameInput = document.getElementById('name');
            const emailInput = document.getElementById('email');
            const subjectInput = document.getElementById('subject');
            const messageInput = document.getElementById('message');
            
            document.querySelectorAll('.is-invalid').forEach(el => {
                el.classList.remove('is-invalid');
            });
            
            if (!nameInput.value.trim()) {
                nameInput.classList.add('is-invalid');
                isValid = false;
            }
            
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailInput.value.trim() || !emailRegex.test(emailInput.value.trim())) {
                emailInput.classList.add('is-invalid');
                isValid = false;
            }
            
            if (!subjectInput.value.trim()) {
                subjectInput.classList.add('is-invalid');
                isValid = false;
            }
            
            if (!messageInput.value.trim()) {
                messageInput.classList.add('is-invalid');
                isValid = false;
            }
            
            if (!isValid) {
                e.preventDefault();
            }
        });
    }
    
    const registerForm = document.getElementById('register-form');
    if (registerForm) {
        registerForm.addEventListener('submit', function(e) {
            let isValid = true;
            
            const usernameInput = document.getElementById('username');
            const emailInput = document.getElementById('email');
            const passwordInput = document.getElementById('password');
            const confirmPasswordInput = document.getElementById('confirm-password');
            const fullNameInput = document.getElementById('full-name');
            
            document.querySelectorAll('.is-invalid').forEach(el => {
                el.classList.remove('is-invalid');
            });
            
            if (!usernameInput.value.trim() || usernameInput.value.length < 3) {
                usernameInput.classList.add('is-invalid');
                isValid = false;
            }
            
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailInput.value.trim() || !emailRegex.test(emailInput.value.trim())) {
                emailInput.classList.add('is-invalid');
                isValid = false;
            }
            
            if (!passwordInput.value || passwordInput.value.length < 6) {
                passwordInput.classList.add('is-invalid');
                isValid = false;
            }
            
            if (passwordInput.value !== confirmPasswordInput.value) {
                confirmPasswordInput.classList.add('is-invalid');
                isValid = false;
            }
            
            if (!fullNameInput.value.trim()) {
                fullNameInput.classList.add('is-invalid');
                isValid = false;
            }
            
            if (!isValid) {
                e.preventDefault();
            }
        });
    }
    
    const passwordToggles = document.querySelectorAll('.password-toggle');
    passwordToggles.forEach(toggle => {
        toggle.addEventListener('click', function() {
            const passwordInput = document.getElementById(this.getAttribute('data-target'));
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                this.innerHTML = '<i class="fas fa-eye-slash"></i>';
            } else {
                passwordInput.type = 'password';
                this.innerHTML = '<i class="fas fa-eye"></i>';
            }
        });
    });
    
    const ratingForm = document.getElementById('rating-form');
    if (ratingForm) {
        const ratingStars = ratingForm.querySelectorAll('.rating-star');
        const ratingInput = document.getElementById('rating');
        
        ratingStars.forEach(star => {
            star.addEventListener('click', function() {
                const value = parseInt(this.getAttribute('data-value'));
                ratingInput.value = value;
                
                ratingStars.forEach(s => {
                    const starValue = parseInt(s.getAttribute('data-value'));
                    if (starValue <= value) {
                        s.classList.add('active');
                    } else {
                        s.classList.remove('active');
                    }
                });
            });
        });
    }
    
    const parallaxElements = document.querySelectorAll('.parallax-section');
    if (parallaxElements.length > 0) {
        window.addEventListener('scroll', function() {
            parallaxElements.forEach(element => {
                const distance = window.scrollY;
                element.style.backgroundPositionY = `${distance * 0.5}px`;
            });
        });
    }
    
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            
            const targetId = this.getAttribute('href');
            if (targetId === '#') return;
            
            const targetElement = document.querySelector(targetId);
            if (targetElement) {
                window.scrollTo({
                    top: targetElement.offsetTop - 70,
                    behavior: 'smooth'
                });
            }
        });
    });
    
    const animateElements = document.querySelectorAll('.animate-on-scroll');
    if (animateElements.length > 0) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animated');
                }
            });
        }, { threshold: 0.1 });
        
        animateElements.forEach(element => {
            observer.observe(element);
        });
    }
    
    const trailerButtons = document.querySelectorAll('.trailer-btn');
    trailerButtons.forEach(button => {
        button.addEventListener('click', function() {
            const trailerUrl = this.getAttribute('data-trailer');
            const trailerModal = new bootstrap.Modal(document.getElementById('trailerModal'));
            const trailerIframe = document.getElementById('trailerIframe');
            
            trailerIframe.src = trailerUrl;
            trailerModal.show();
            
            document.getElementById('trailerModal').addEventListener('hidden.bs.modal', function() {
                trailerIframe.src = '';
            });
        });
    });
});
