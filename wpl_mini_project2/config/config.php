<?php
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'movie_booking');

$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$conn->set_charset("utf8");

define('SITE_NAME', 'Movie Booking System');
define('SITE_URL', 'http://localhost/movie-booking');
define('ADMIN_EMAIL', 'admin@moviebooking.com');

session_start();

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1;
}

function redirect($url) {
    header("Location: $url");
    exit;
}

function sanitize($data) {
    global $conn;
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    $data = $conn->real_escape_string($data);
    return $data;
}

function displayError($message) {
    return "<div class='alert alert-danger'>{$message}</div>";
}

function displaySuccess($message) {
    return "<div class='alert alert-success'>{$message}</div>";
}

function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function uploadFile($file, $directory = 'assets/uploads/') {
    if (!file_exists($directory)) {
        mkdir($directory, 0777, true);
    }
    
    $target_dir = $directory;
    $file_extension = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
    $new_filename = generateRandomString() . "." . $file_extension;
    $target_file = $target_dir . $new_filename;
    
    $check = getimagesize($file["tmp_name"]);
    if($check === false) {
        return ["success" => false, "message" => "File is not an image."];
    }
    
    if ($file["size"] > 5000000) {
        return ["success" => false, "message" => "File is too large. Max 5MB allowed."];
    }
    
    if($file_extension != "jpg" && $file_extension != "png" && $file_extension != "jpeg" && $file_extension != "gif" ) {
        return ["success" => false, "message" => "Only JPG, JPEG, PNG & GIF files are allowed."];
    }
    
    if (move_uploaded_file($file["tmp_name"], $target_file)) {
        return ["success" => true, "file_path" => $target_file];
    } else {
        return ["success" => false, "message" => "There was an error uploading your file."];
    }
}

function getAvailableSeats($show_id) {
    global $conn;
    $stmt = $conn->prepare("SELECT available_seats FROM shows WHERE id = ?");
    $stmt->bind_param("i", $show_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row ? intval($row['available_seats']) : 0;
}

function areSeatsAlreadyBooked($show_id, $seat_numbers) {
    global $conn;
    $seat_numbers_placeholder = implode(',', array_fill(0, count($seat_numbers), '?'));
    $query = "
        SELECT COUNT(*) as conflict_count 
        FROM bookings 
        WHERE show_id = ? AND FIND_IN_SET(seat_numbers, ?)
    ";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("is", $show_id, implode(',', $seat_numbers));
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row['conflict_count'] > 0;
}
?>
