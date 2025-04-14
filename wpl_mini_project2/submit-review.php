<?php
require_once 'includes/functions.php';

// Ensure the user is logged in
if (!isLoggedIn()) {
    redirect('login.php');
}

// Validate form data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $movie_id = intval($_POST['movie_id']);
    $rating = intval($_POST['rating']);
    $comment = trim($_POST['comment']);
    $user_id = $_SESSION['user_id'];

    // Check if required fields are provided
    if (empty($movie_id) || empty($rating) || empty($comment)) {
        redirect("movie-details.php?id=$movie_id&error=missing_fields");
    }

    // Check if the movie exists
    $movie = getMovieById($movie_id);
    if (!$movie) {
        redirect("movies.php?error=invalid_movie");
    }

    // Check if the user has already submitted a review for this movie
    global $conn;
    $stmt = $conn->prepare("
        SELECT id FROM reviews 
        WHERE user_id = ? AND movie_id = ?
    ");
    $stmt->bind_param("ii", $user_id, $movie_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Update existing review
        $stmt = $conn->prepare("
            UPDATE reviews 
            SET rating = ?, comment = ?, status = 'pending', updated_at = NOW() 
            WHERE user_id = ? AND movie_id = ?
        ");
        $stmt->bind_param("isii", $rating, $comment, $user_id, $movie_id);
    } else {
        // Insert new review
        $stmt = $conn->prepare("
            INSERT INTO reviews (user_id, movie_id, rating, comment, status, created_at, updated_at) 
            VALUES (?, ?, ?, ?, 'pending', NOW(), NOW())
        ");
        $stmt->bind_param("iiis", $user_id, $movie_id, $rating, $comment);
    }

    if ($stmt->execute()) {
        redirect("movie-details.php?id=$movie_id&success=review_submitted");
    } else {
        redirect("movie-details.php?id=$movie_id&error=review_failed");
    }
} else {
    redirect('movies.php');
}
?>
