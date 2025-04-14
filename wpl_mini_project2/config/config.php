<?php
// Database configuration
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'movie_booking');

// Attempt to connect to MySQL database
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set character set
$conn->set_charset("utf8");

// Site configuration
define('SITE_NAME', 'Movie Booking System');
define('SITE_URL', 'http://localhost/movie-booking');
define('ADMIN_EMAIL', 'admin@moviebooking.com');

// Session configuration
session_start();

// Function to check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Function to check if user is admin
function isAdmin() {
    return isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1;
}

// Function to redirect
function redirect($url) {
    header("Location: $url");
    exit;
}

// Function to sanitize input data
function sanitize($data) {
    global $conn;
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    $data = $conn->real_escape_string($data);
    return $data;
}

// Function to display error message
function displayError($message) {
    return "<div class='alert alert-danger'>{$message}</div>";
}

// Function to display success message
function displaySuccess($message) {
    return "<div class='alert alert-success'>{$message}</div>";
}

// Function to generate random string
function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

// Function to upload file
function uploadFile($file, $directory = 'assets/uploads/') {
    // Check if directory exists, if not create it
    if (!file_exists($directory)) {
        mkdir($directory, 0777, true);
    }
    
    $target_dir = $directory;
    $file_extension = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
    $new_filename = generateRandomString() . "." . $file_extension;
    $target_file = $target_dir . $new_filename;
    
    // Check if file is an actual image
    $check = getimagesize($file["tmp_name"]);
    if($check === false) {
        return ["success" => false, "message" => "File is not an image."];
    }
    
    // Check file size (limit to 5MB)
    if ($file["size"] > 5000000) {
        return ["success" => false, "message" => "File is too large. Max 5MB allowed."];
    }
    
    // Allow certain file formats
    if($file_extension != "jpg" && $file_extension != "png" && $file_extension != "jpeg" && $file_extension != "gif" ) {
        return ["success" => false, "message" => "Only JPG, JPEG, PNG & GIF files are allowed."];
    }
    
    // Try to upload file
    if (move_uploaded_file($file["tmp_name"], $target_file)) {
        return ["success" => true, "file_path" => $target_file];
    } else {
        return ["success" => false, "message" => "There was an error uploading your file."];
    }
}
?>