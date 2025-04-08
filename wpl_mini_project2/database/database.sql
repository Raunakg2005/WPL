
CREATE DATABASE IF NOT EXISTS movie_booking;
USE movie_booking;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    is_admin TINYINT(1) DEFAULT 0
);

-- Movies table
CREATE TABLE IF NOT EXISTS movies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    release_date DATE,
    duration INT COMMENT 'Duration in minutes',
    genre VARCHAR(100),
    language VARCHAR(50),
    director VARCHAR(100),
    cast TEXT,
    poster VARCHAR(255) COMMENT 'Path to poster image',
    trailer_url VARCHAR(255),
    rating DECIMAL(3,1) DEFAULT 0,
    status ENUM('now_showing', 'coming_soon', 'archived') DEFAULT 'now_showing',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Theaters table
CREATE TABLE IF NOT EXISTS theaters (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    location VARCHAR(255),
    total_seats INT DEFAULT 100,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Shows table (movie showtimes)
CREATE TABLE IF NOT EXISTS shows (
    id INT AUTO_INCREMENT PRIMARY KEY,
    movie_id INT,
    theater_id INT,
    show_date DATE NOT NULL,
    show_time TIME NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    available_seats INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (movie_id) REFERENCES movies(id) ON DELETE CASCADE,
    FOREIGN KEY (theater_id) REFERENCES theaters(id) ON DELETE CASCADE
);

-- Bookings table
CREATE TABLE IF NOT EXISTS bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    show_id INT,
    booking_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    seats_booked INT NOT NULL,
    seat_numbers VARCHAR(255) NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    payment_status ENUM('pending', 'completed', 'cancelled') DEFAULT 'pending',
    booking_status ENUM('confirmed', 'cancelled') DEFAULT 'confirmed',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (show_id) REFERENCES shows(id) ON DELETE CASCADE
);

-- Reviews table
CREATE TABLE IF NOT EXISTS reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    movie_id INT,
    rating INT NOT NULL CHECK (rating BETWEEN 1 AND 10),
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (movie_id) REFERENCES movies(id) ON DELETE CASCADE
);

-- Promotions table
CREATE TABLE IF NOT EXISTS promotions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    image VARCHAR(255),
    start_date DATE,
    end_date DATE,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Contact messages table
CREATE TABLE IF NOT EXISTS contact_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    subject VARCHAR(255),
    message TEXT NOT NULL,
    is_read TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO users (username, email, password, full_name, is_admin) 
VALUES ('admin', 'admin@moviebooking.com', '$2y$10$8WxhJz0q.Y9BsHM9bNJJZ.XS9YQCCo1KE7XvIXxvZY5vWWNn6Qrjm', 'Admin User', 1);

INSERT INTO users (username, email, password, full_name) 
VALUES ('user', 'user@example.com', '$2y$10$Nt0RHKh7MhXxQYD.3IOO2.Nqr7rJILg/3QWmgJFOFHoOgmgKyjNtO', 'Regular User');

INSERT INTO movies (title, description, release_date, duration, genre, language, director, cast, poster, trailer_url, rating) VALUES
('Inception', 'A thief who steals corporate secrets through the use of dream-sharing technology is given the inverse task of planting an idea into the mind of a C.E.O.', '2010-07-16', 148, 'Sci-Fi, Action', 'English', 'Christopher Nolan', 'Leonardo DiCaprio, Joseph Gordon-Levitt, Ellen Page', 'assets/uploads/inception.jpg', 'https://www.youtube.com/embed/YoHD9XEInc0', 8.8),
('The Shawshank Redemption', 'Two imprisoned men bond over a number of years, finding solace and eventual redemption through acts of common decency.', '1994-09-23', 142, 'Drama', 'English', 'Frank Darabont', 'Tim Robbins, Morgan Freeman', 'assets/uploads/shawshank.jpg', 'https://www.youtube.com/embed/6hB3S9bIaco', 9.3),
('The Dark Knight', 'When the menace known as the Joker wreaks havoc and chaos on the people of Gotham, Batman must accept one of the greatest psychological and physical tests of his ability to fight injustice.', '2008-07-18', 152, 'Action, Crime, Drama', 'English', 'Christopher Nolan', 'Christian Bale, Heath Ledger, Aaron Eckhart', 'assets/uploads/dark_knight.jpg', 'https://www.youtube.com/embed/EXeTwQWrcwY', 9.0),
('Pulp Fiction', 'The lives of two mob hitmen, a boxer, a gangster and his wife, and a pair of diner bandits intertwine in four tales of violence and redemption.', '1994-10-14', 154, 'Crime, Drama', 'English', 'Quentin Tarantino', 'John Travolta, Uma Thurman, Samuel L. Jackson', 'assets/uploads/pulp_fiction.jpg', 'https://www.youtube.com/embed/s7EdQ4FqbhY', 8.9),
('Avengers: Endgame', 'After the devastating events of Avengers: Infinity War, the universe is in ruins. With the help of remaining allies, the Avengers assemble once more in order to reverse Thanos actions and restore balance to the universe.', '2019-04-26', 181, 'Action, Adventure, Drama', 'English', 'Anthony Russo, Joe Russo', 'Robert Downey Jr., Chris Evans, Mark Ruffalo', 'assets/uploads/movie2.jpg', 'https://www.youtube.com/embed/TcMBFSGVi1c', 8.4);

-- Sample theaters
INSERT INTO theaters (name, location, total_seats) VALUES
('Cineplex', 'Downtown', 120),
('MovieMax', 'Westside Mall', 100),
('FilmHouse', 'Eastside Plaza', 80);

-- Sample shows
INSERT INTO shows (movie_id, theater_id, show_date, show_time, price, available_seats) VALUES
(1, 1, CURDATE(), '14:00:00', 12.50, 120),
(1, 1, CURDATE(), '18:30:00', 15.00, 120),
(1, 2, CURDATE(), '19:00:00', 14.00, 100),
(2, 1, CURDATE(), '15:30:00', 12.50, 120),
(2, 3, CURDATE(), '20:00:00', 13.50, 80),
(3, 2, CURDATE(), '16:00:00', 14.00, 100),
(3, 3, CURDATE(), '21:30:00', 13.50, 80),
(4, 1, CURDATE() + INTERVAL 1 DAY, '14:30:00', 12.50, 120),
(5, 2, CURDATE() + INTERVAL 1 DAY, '17:00:00', 14.00, 100);

-- Sample promotions
INSERT INTO promotions (title, description, image, start_date, end_date, is_active) VALUES
('Weekend Special', 'Get 20% off on all movie tickets this weekend!', 'assets/uploads/promo1.jpg', CURDATE(), CURDATE() + INTERVAL 7 DAY, 1),
('Student Discount', 'Students get 15% off on all shows with valid ID', 'assets/uploads/promo2.jpg', CURDATE(), CURDATE() + INTERVAL 30 DAY, 1);