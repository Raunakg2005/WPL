<?php
require_once __DIR__ . '/../config/config.php';
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

function createBooking($user_id, $show_id, $seats_booked, $seat_numbers, $total_amount) {
    global $conn;
    
    $conn->begin_transaction();
    
    try {
        $stmt = $conn->prepare("SELECT available_seats FROM shows WHERE id = ? FOR UPDATE");
        $stmt->bind_param("i", $show_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $show = $result->fetch_assoc();
        
        if ($show['available_seats'] < $seats_booked) {
            $conn->rollback();
            return ["success" => false, "message" => "Not enough seats available."];
        }
        
        $stmt = $conn->prepare("UPDATE shows SET available_seats = available_seats - ? WHERE id = ?");
        $stmt->bind_param("ii", $seats_booked, $show_id);
        $stmt->execute();
        
        $stmt = $conn->prepare("
            INSERT INTO bookings (user_id, show_id, seats_booked, seat_numbers, total_amount, payment_status, booking_status)
            VALUES (?, ?, ?, ?, ?, 'completed', 'confirmed')
        ");
        $stmt->bind_param("iiiss", $user_id, $show_id, $seats_booked, $seat_numbers, $total_amount);
        $stmt->execute();
        
        $booking_id = $conn->insert_id;
        
        $conn->commit();
        
        return ["success" => true, "booking_id" => $booking_id];
    } catch (Exception $e) {
        $conn->rollback();
        return ["success" => false, "message" => "Error creating booking: " . $e->getMessage()];
    }
}

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

function getRecommendedMovies($user_id, $limit = 5) {
    global $conn;
    
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
        $movie_genres = explode(',', $row['genre']);
        foreach ($movie_genres as $genre) {
            $genre = trim($genre);
            if (!empty($genre) && !in_array($genre, $genres)) {
                $genres[] = $genre;
            }
        }
    }
    
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

function saveMovie($data, $poster = null) {
    global $conn;
    
    if (isset($data['id']) && !empty($data['id'])) {
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
        if (!$poster || $poster['size'] == 0) {
            return ["success" => false, "message" => "Poster image is required for new movies."];
        }
        
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

function deleteMovie($id) {
    global $conn;
    
    $conn->begin_transaction();
    
    try {
        $stmt = $conn->prepare("DELETE FROM shows WHERE movie_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        
        $stmt = $conn->prepare("DELETE FROM movies WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        
        $conn->commit();
        
        return ["success" => true, "message" => "Movie deleted successfully."];
    } catch (Exception $e) {
        $conn->rollback();
        return ["success" => false, "message" => "Error deleting movie: " . $e->getMessage()];
    }
}

function savePromotion($data, $image = null) {
    global $conn;
    
    if (isset($data['id']) && !empty($data['id'])) {
        $sql = "
            UPDATE promotions SET 
            title = ?, description = ?, start_date = ?, end_date = ?, is_active = ?
        ";
        
        $params = [
            $data['title'], $data['description'], $data['start_date'], 
            $data['end_date'], $data['is_active']
        ];
        $types = "ssssi";
        
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
        if (!$image || $image['size'] == 0) {
            return ["success" => false, "message" => "Image is required for new promotions."];
        }
        
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

function getDashboardStats() {
    global $conn;
    
    $stats = [];
    
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM users WHERE is_admin = 0");
    $stmt->execute();
    $result = $stmt->get_result();
    $stats['total_users'] = $result->fetch_assoc()['total'];
    
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM movies");
    $stmt->execute();
    $result = $stmt->get_result();
    $stats['total_movies'] = $result->fetch_assoc()['total'];
    
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM bookings");
    $stmt->execute();
    $result = $stmt->get_result();
    $stats['total_bookings'] = $result->fetch_assoc()['total'];
    
    $stmt = $conn->prepare("SELECT SUM(total_amount) as total FROM bookings WHERE payment_status = 'completed'");
    $stmt->execute();
    $result = $stmt->get_result();
    $stats['total_revenue'] = $result->fetch_assoc()['total'] ?? 0;
    
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