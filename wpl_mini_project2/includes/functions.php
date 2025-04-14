<?php
// Include database configuration
require_once(__DIR__ . '/../config/config.php');

// Function to get all movies
function getAllMovies($limit = null, $status = 'now_showing') {
    global $conn;
    
    $sql = "SELECT * FROM movies WHERE status = ?";
    if ($limit) {
        $sql .= " LIMIT ?";
    }
    
    $stmt = $conn->prepare($sql);
    
    if ($limit) {
        $stmt->bind_param("si", $status, $limit);
    } else {
        $stmt->bind_param("s", $status);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    $movies = [];
    while ($row = $result->fetch_assoc()) {
        $movies[] = $row;
    }
    
    return $movies;
}
function deleteShow($show_id) {
    global $conn;

    $stmt = $conn->prepare("DELETE FROM shows WHERE id = ?");
    $stmt->bind_param("i", $show_id);

    if ($stmt->execute()) {
        return ['success' => true, 'message' => 'Show deleted successfully.'];
    } else {
        return ['success' => false, 'message' => 'Failed to delete the show.'];
    }
}
function updateMovie($movie_data, $poster = null) {
    global $conn;

    // Handle poster upload if provided
    $poster_path = null;
    if ($poster) {
        $target_dir = "../uploads/";
        $poster_path = $target_dir . basename($poster["name"]);
        if (!move_uploaded_file($poster["tmp_name"], $poster_path)) {
            return ['success' => false, 'message' => 'Failed to upload poster.'];
        }
    }

    // Update movie details in the database
    $query = "UPDATE movies SET title = ?, description = ?, release_date = ?, duration = ?, genre = ?, language = ?, director = ?, cast = ?, trailer_url = ?, rating = ?, status = ?";
    if ($poster_path) {
        $query .= ", poster = ?";
    }
    $query .= " WHERE id = ?";

    $stmt = $conn->prepare($query);
    if ($poster_path) {
        $stmt->bind_param("sssssssssdssi", $movie_data['title'], $movie_data['description'], $movie_data['release_date'], $movie_data['duration'], $movie_data['genre'], $movie_data['language'], $movie_data['director'], $movie_data['cast'], $movie_data['trailer_url'], $movie_data['rating'], $movie_data['status'], $poster_path, $movie_data['id']);
    } else {
        $stmt->bind_param("sssssssssdsi", $movie_data['title'], $movie_data['description'], $movie_data['release_date'], $movie_data['duration'], $movie_data['genre'], $movie_data['language'], $movie_data['director'], $movie_data['cast'], $movie_data['trailer_url'], $movie_data['rating'], $movie_data['status'], $movie_data['id']);
    }

    if ($stmt->execute()) {
        return ['success' => true, 'message' => 'Movie updated successfully.'];
    } else {
        return ['success' => false, 'message' => 'Failed to update movie.'];
    }
}
function getBookedSeats($show_id) {
    global $conn;
    $stmt = $conn->prepare("SELECT seat_numbers FROM bookings WHERE show_id = ? AND booking_status = 'confirmed'");
    $stmt->bind_param("i", $show_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $booked_seats = [];
    while ($row = $result->fetch_assoc()) {
        $seats = explode(',', $row['seat_numbers']);
        $booked_seats = array_merge($booked_seats, $seats);
    }
    
    return $booked_seats;
}

function areSeatsAlreadyBooked($show_id, $selected_seats_array) {
    $booked_seats = getBookedSeats($show_id); 
    foreach ($selected_seats_array as $seat) {
        if (in_array($seat, $booked_seats)) {
            return true;
        }
    }
    return false;
}
function getAvailableSeats($show_id) {
    // Replace this with actual logic to fetch available seats from the database
    // For example:
    global $conn;
    $db = $conn;
    $query = $db->prepare("SELECT available_seats FROM shows WHERE id = ?");
    $query->execute([$show_id]);
    $result = $query->fetch();
    return (is_array($result) && isset($result['available_seats'])) ? intval($result['available_seats']) : 0;
}
// Function to get movie by ID
function getMovieById($id) {
    global $conn;
    
    $stmt = $conn->prepare("SELECT * FROM movies WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        return $result->fetch_assoc();
    }
    
    return null;
}

// Function to get shows for a movie
function getShowsByMovieId($movie_id) {
    global $conn;
    
    $stmt = $conn->prepare("
        SELECT s.*, t.name as theater_name, t.location as theater_location 
        FROM shows s
        JOIN theaters t ON s.theater_id = t.id
        WHERE s.movie_id = ? AND s.show_date >= CURDATE()
        ORDER BY s.show_date, s.show_time
    ");
    $stmt->bind_param("i", $movie_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $shows = [];
    while ($row = $result->fetch_assoc()) {
        $shows[] = $row;
    }
    
    return $shows;
}

// Function to get show by ID
function getShowById($id) {
    global $conn;
    
    $stmt = $conn->prepare("
        SELECT s.*, m.title as movie_title, m.poster as movie_poster, 
               t.name as theater_name, t.location as theater_location 
        FROM shows s
        JOIN movies m ON s.movie_id = m.id
        JOIN theaters t ON s.theater_id = t.id
        WHERE s.id = ?
    ");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        return $result->fetch_assoc();
    }
    
    return null;
}

// Function to create a booking
function createBooking($user_id, $show_id, $seats_booked, $seat_numbers, $total_amount) {
    global $conn;
    
    // Start transaction
    $conn->begin_transaction();
    
    try {
        // Check if seats are still available
        $stmt = $conn->prepare("SELECT available_seats FROM shows WHERE id = ? FOR UPDATE");
        $stmt->bind_param("i", $show_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $show = $result->fetch_assoc();
        
        if ($show['available_seats'] < $seats_booked) {
            $conn->rollback();
            return ["success" => false, "message" => "Not enough seats available."];
        }
        
        // Update available seats
        $stmt = $conn->prepare("UPDATE shows SET available_seats = available_seats - ? WHERE id = ?");
        $stmt->bind_param("ii", $seats_booked, $show_id);
        $stmt->execute();
        
        // Create booking
        $stmt = $conn->prepare("
            INSERT INTO bookings (user_id, show_id, seats_booked, seat_numbers, total_amount, payment_status, booking_status)
            VALUES (?, ?, ?, ?, ?, 'completed', 'confirmed')
        ");
        $stmt->bind_param("iiiss", $user_id, $show_id, $seats_booked, $seat_numbers, $total_amount);
        $stmt->execute();
        
        $booking_id = $conn->insert_id;
        
        // Commit transaction
        $conn->commit();
        
        return ["success" => true, "booking_id" => $booking_id];
    } catch (Exception $e) {
        $conn->rollback();
        return ["success" => false, "message" => "Error creating booking: " . $e->getMessage()];
    }
}
function getBookingById($booking_id, $user_id) {
    // Assuming a database connection is available as $conn
    global $conn;

    $stmt = $conn->prepare("SELECT * FROM bookings WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $booking_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    return $result->fetch_assoc();
}
// Function to save booking
function saveBooking($user_id, $show_id, $seat_numbers, $total_amount) {
    global $conn;

    $seats_booked = count(explode(',', $seat_numbers));
    $stmt = $conn->prepare("
        INSERT INTO bookings (user_id, show_id, seat_numbers, seats_booked, total_amount, booking_date, booking_status, payment_status)
        VALUES (?, ?, ?, ?, ?, NOW(), 'confirmed', 'paid')
    ");
    $stmt->bind_param("iisid", $user_id, $show_id, $seat_numbers, $seats_booked, $total_amount);

    if ($stmt->execute()) {
        return ['success' => true, 'booking_id' => $stmt->insert_id];
    } else {
        return ['success' => false, 'message' => 'Failed to save booking.'];
    }
}

// Function to get user bookings
function getUserBookings($user_id) {
    global $conn;
    
    $stmt = $conn->prepare("
        SELECT b.*, s.show_date, s.show_time, m.title as movie_title, m.poster as movie_poster,
               t.name as theater_name, t.location as theater_location
        FROM bookings b
        JOIN shows s ON b.show_id = s.id
        JOIN movies m ON s.movie_id = m.id
        JOIN theaters t ON s.theater_id = t.id
        WHERE b.user_id = ?
        ORDER BY b.booking_date DESC
    ");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $bookings = [];
    while ($row = $result->fetch_assoc()) {
        $bookings[] = $row;
    }
    
    return $bookings;
}

// Function to cancel booking
function cancelBooking($booking_id, $user_id) {
    global $conn;
    
    $conn->begin_transaction();
    
    try {
        $stmt = $conn->prepare("
            SELECT b.*, s.id as show_id, s.show_date
            FROM bookings b
            JOIN shows s ON b.show_id = s.id
            WHERE b.id = ? AND b.user_id = ?
        ");
        $stmt->bind_param("ii", $booking_id, $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows == 0) {
            $conn->rollback();
            return ["success" => false, "message" => "Booking not found."];
        }
        
        $booking = $result->fetch_assoc();
        
        if (strtotime($booking['show_date']) <= time()) {
            $conn->rollback();
            return ["success" => false, "message" => "Cannot cancel past bookings."];
        }
        
        $stmt = $conn->prepare("UPDATE bookings SET booking_status = 'cancelled', payment_status = 'cancelled' WHERE id = ?");
        $stmt->bind_param("i", $booking_id);
        $stmt->execute();
        
        $stmt = $conn->prepare("UPDATE shows SET available_seats = available_seats + ? WHERE id = ?");
        $stmt->bind_param("ii", $booking['seats_booked'], $booking['show_id']);
        $stmt->execute();
        
        $conn->commit();
        
        return ["success" => true, "message" => "Booking cancelled successfully."];
    } catch (Exception $e) {
        $conn->rollback();
        return ["success" => false, "message" => "Error cancelling booking: " . $e->getMessage()];
    }
}

// Function to get recommended movies based on user's booking history
function getRecommendedMovies($user_id, $limit = 5) {
    global $conn;
    
    // Get genres from user's past bookings
    $stmt = $conn->prepare("
        SELECT DISTINCT m.genre
        FROM bookings b
        JOIN shows s ON b.show_id = s.id
        JOIN movies m ON s.movie_id = m.id
        WHERE b.user_id = ?
        LIMIT 3
    ");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $genres = [];
    while ($row = $result->fetch_assoc()) {
        // Split genres (they might be stored as comma-separated values)
        $movie_genres = explode(',', $row['genre']);
        foreach ($movie_genres as $genre) {
            $genre = trim($genre);
            if (!empty($genre) && !in_array($genre, $genres)) {
                $genres[] = $genre;
            }
        }
    }
    
    // If user has no booking history, return popular movies
    if (empty($genres)) {
        $stmt = $conn->prepare("
            SELECT * FROM movies 
            WHERE status = 'now_showing'
            ORDER BY rating DESC
            LIMIT ?
        ");
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $movies = [];
        while ($row = $result->fetch_assoc()) {
            $movies[] = $row;
        }
        
        return $movies;
    }
    
    // Build query to find movies with similar genres
    $sql = "
        SELECT * FROM movies 
        WHERE status = 'now_showing' AND (
    ";
    
    $params = [];
    $types = "";
    
    for ($i = 0; $i < count($genres); $i++) {
        if ($i > 0) {
            $sql .= " OR ";
        }
        $sql .= "genre LIKE ?";
        $params[] = "%" . $genres[$i] . "%";
        $types .= "s";
    }
    
    $sql .= ") ORDER BY rating DESC LIMIT ?";
    $params[] = $limit;
    $types .= "i";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $movies = [];
    while ($row = $result->fetch_assoc()) {
        $movies[] = $row;
    }
    
    return $movies;
}

// Function to get active promotions
function getActivePromotions() {
    global $conn;
    
    $stmt = $conn->prepare("
        SELECT * FROM promotions 
        WHERE is_active = 1 
        AND (start_date <= CURDATE() AND end_date >= CURDATE())
    ");
    $stmt->execute();
    $result = $stmt->get_result();
    
    $promotions = [];
    while ($row = $result->fetch_assoc()) {
        $promotions[] = $row;
    }
    
    return $promotions;
}

// Function to submit contact form
function submitContactForm($name, $email, $subject, $message) {
    global $conn;
    
    $stmt = $conn->prepare("
        INSERT INTO contact_messages (name, email, subject, message)
        VALUES (?, ?, ?, ?)
    ");
    $stmt->bind_param("ssss", $name, $email, $subject, $message);
    
    if ($stmt->execute()) {
        return ["success" => true, "message" => "Your message has been sent successfully."];
    } else {
        return ["success" => false, "message" => "Error sending message: " . $conn->error];
    }
}

// Function to search movies
function searchMovies($keyword, $genre = null, $language = null) {
    global $conn;
    
    $sql = "
        SELECT * FROM movies 
        WHERE (title LIKE ? OR description LIKE ? OR cast LIKE ? OR director LIKE ?)
    ";
    
    $params = ["%$keyword%", "%$keyword%", "%$keyword%", "%$keyword%"];
    $types = "ssss";
    
    if ($genre) {
        $sql .= " AND genre LIKE ?";
        $params[] = "%$genre%";
        $types .= "s";
    }
    
    if ($language) {
        $sql .= " AND language = ?";
        $params[] = $language;
        $types .= "s";
    }
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $movies = [];
    while ($row = $result->fetch_assoc()) {
        $movies[] = $row;
    }
    
    return $movies;
}

// Function to get all genres
function getAllGenres() {
    global $conn;
    
    $stmt = $conn->prepare("SELECT DISTINCT genre FROM movies");
    $stmt->execute();
    $result = $stmt->get_result();
    
    $all_genres = [];
    while ($row = $result->fetch_assoc()) {
        $genres = explode(',', $row['genre']);
        foreach ($genres as $genre) {
            $genre = trim($genre);
            if (!empty($genre) && !in_array($genre, $all_genres)) {
                $all_genres[] = $genre;
            }
        }
    }
    
    sort($all_genres);
    return $all_genres;
}

// Function to get all languages
function getAllLanguages() {
    global $conn;
    
    $stmt = $conn->prepare("SELECT DISTINCT language FROM movies");
    $stmt->execute();
    $result = $stmt->get_result();
    
    $languages = [];
    while ($row = $result->fetch_assoc()) {
        $languages[] = $row['language'];
    }
    
    sort($languages);
    return $languages;
}

// Admin Functions

// Function to get all users
function getAllUsers() {
    global $conn;
    
    $stmt = $conn->prepare("SELECT id, username, email, full_name, created_at, is_admin FROM users");
    $stmt->execute();
    $result = $stmt->get_result();
    
    $users = [];
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
    
    return $users;
}

// Function to get all bookings
function getAllBookings() {
    global $conn;
    
    $stmt = $conn->prepare("
        SELECT b.*, u.username, u.email, s.show_date, s.show_time, 
               m.title as movie_title, t.name as theater_name
        FROM bookings b
        JOIN users u ON b.user_id = u.id
        JOIN shows s ON b.show_id = s.id
        JOIN movies m ON s.movie_id = m.id
        JOIN theaters t ON s.theater_id = t.id
        ORDER BY b.booking_date DESC
    ");
    $stmt->execute();
    $result = $stmt->get_result();
    
    $bookings = [];
    while ($row = $result->fetch_assoc()) {
        $bookings[] = $row;
    }
    
    return $bookings;
}

// Function to add/update movie
function saveMovie($data, $poster = null) {
    global $conn;
    
    // Check if it's an update or new movie
    if (isset($data['id']) && !empty($data['id'])) {
        // Update existing movie
        $sql = "
            UPDATE movies SET 
            title = ?, description = ?, release_date = ?, duration = ?,
            genre = ?, language = ?, director = ?, cast = ?, 
            trailer_url = ?, rating = ?, status = ?
        ";
        
        $params = [
            $data['title'], $data['description'], $data['release_date'], $data['duration'],
            $data['genre'], $data['language'], $data['director'], $data['cast'],
            $data['trailer_url'], $data['rating'], $data['status']
        ];
        $types = "sssississds";
        
        // If new poster is uploaded
        if ($poster && $poster['size'] > 0) {
            $upload_result = uploadFile($poster);
            if ($upload_result['success']) {
                $sql .= ", poster = ?";
                $params[] = $upload_result['file_path'];
                $types .= "s";
            } else {
                return ["success" => false, "message" => $upload_result['message']];
            }
        }
        
        $sql .= " WHERE id = ?";
        $params[] = $data['id'];
        $types .= "i";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param($types, ...$params);
        
        if ($stmt->execute()) {
            return ["success" => true, "message" => "Movie updated successfully."];
        } else {
            return ["success" => false, "message" => "Error updating movie: " . $conn->error];
        }
    } else {
        // Add new movie
        // Poster is required for new movies
        if (!$poster || $poster['size'] == 0) {
            return ["success" => false, "message" => "Poster image is required for new movies."];
        }
        
        // Upload poster
        $upload_result = uploadFile($poster);
        if (!$upload_result['success']) {
            return ["success" => false, "message" => $upload_result['message']];
        }
        
        $stmt = $conn->prepare("
            INSERT INTO movies (title, description, release_date, duration, genre, language, 
                               director, cast, poster, trailer_url, rating, status)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->bind_param("sssississdss", 
            $data['title'], $data['description'], $data['release_date'], $data['duration'],
            $data['genre'], $data['language'], $data['director'], $data['cast'],
            $upload_result['file_path'], $data['trailer_url'], $data['rating'], $data['status']
        );
        
        if ($stmt->execute()) {
            return ["success" => true, "message" => "Movie added successfully.", "id" => $conn->insert_id];
        } else {
            return ["success" => false, "message" => "Error adding movie: " . $conn->error];
        }
    }
}

// Function to delete movie
function deleteMovie($id) {
    global $conn;
    
    // Start transaction
    $conn->begin_transaction();
    
    try {
        // Delete related shows first
        $stmt = $conn->prepare("DELETE FROM shows WHERE movie_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        
        // Delete movie
        $stmt = $conn->prepare("DELETE FROM movies WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        
        // Commit transaction
        $conn->commit();
        
        return ["success" => true, "message" => "Movie deleted successfully."];
    } catch (Exception $e) {
        $conn->rollback();
        return ["success" => false, "message" => "Error deleting movie: " . $e->getMessage()];
    }
}

// Function to add/update promotion
function savePromotion($data, $image = null) {
    global $conn;
    
    // Check if it's an update or new promotion
    if (isset($data['id']) && !empty($data['id'])) {
        // Update existing promotion
        $sql = "
            UPDATE promotions SET 
            title = ?, description = ?, start_date = ?, end_date = ?, is_active = ?
        ";
        
        $params = [
            $data['title'], $data['description'], $data['start_date'], 
            $data['end_date'], $data['is_active']
        ];
        $types = "ssssi";
        
        // If new image is uploaded
        if ($image && $image['size'] > 0) {
            $upload_result = uploadFile($image);
            if ($upload_result['success']) {
                $sql .= ", image = ?";
                $params[] = $upload_result['file_path'];
                $types .= "s";
            } else {
                return ["success" => false, "message" => $upload_result['message']];
            }
        }
        
        $sql .= " WHERE id = ?";
        $params[] = $data['id'];
        $types .= "i";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param($types, ...$params);
        
        if ($stmt->execute()) {
            return ["success" => true, "message" => "Promotion updated successfully."];
        } else {
            return ["success" => false, "message" => "Error updating promotion: " . $conn->error];
        }
    } else {
        // Add new promotion
        // Image is required for new promotions
        if (!$image || $image['size'] == 0) {
            return ["success" => false, "message" => "Image is required for new promotions."];
        }
        
        // Upload image
        $upload_result = uploadFile($image);
        if (!$upload_result['success']) {
            return ["success" => false, "message" => $upload_result['message']];
        }
        
        $stmt = $conn->prepare("
            INSERT INTO promotions (title, description, image, start_date, end_date, is_active)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->bind_param("sssssi", 
            $data['title'], $data['description'], $upload_result['file_path'],
            $data['start_date'], $data['end_date'], $data['is_active']
        );
        
        if ($stmt->execute()) {
            return ["success" => true, "message" => "Promotion added successfully."];
        } else {
            return ["success" => false, "message" => "Error adding promotion: " . $conn->error];
        }
    }
}

// Function to get dashboard statistics
function getDashboardStats() {
    global $conn;
    
    $stats = [];
    
    // Total users
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM users WHERE is_admin = 0");
    $stmt->execute();
    $result = $stmt->get_result();
    $stats['total_users'] = $result->fetch_assoc()['total'];
    
    // Total movies
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM movies");
    $stmt->execute();
    $result = $stmt->get_result();
    $stats['total_movies'] = $result->fetch_assoc()['total'];
    
    // Total bookings
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM bookings");
    $stmt->execute();
    $result = $stmt->get_result();
    $stats['total_bookings'] = $result->fetch_assoc()['total'];
    
    // Revenue
    $stmt = $conn->prepare("SELECT SUM(total_amount) as total FROM bookings WHERE payment_status = 'completed'");
    $stmt->execute();
    $result = $stmt->get_result();
    $stats['total_revenue'] = $result->fetch_assoc()['total'] ?? 0;
    
    // Recent bookings
    $stmt = $conn->prepare("
        SELECT b.*, u.username, m.title as movie_title
        FROM bookings b
        JOIN users u ON b.user_id = u.id
        JOIN shows s ON b.show_id = s.id
        JOIN movies m ON s.movie_id = m.id
        ORDER BY b.booking_date DESC
        LIMIT 5
    ");
    $stmt->execute();
    $result = $stmt->get_result();
    
    $recent_bookings = [];
    while ($row = $result->fetch_assoc()) {
        $recent_bookings[] = $row;
    }
    $stats['recent_bookings'] = $recent_bookings;
    
    return $stats;
}
?>