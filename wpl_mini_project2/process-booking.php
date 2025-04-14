<?php
require_once 'includes/functions.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $show_id = intval($_POST['show_id']);
    $seat_numbers = $_POST['seat_numbers'];
    $total_amount = floatval($_POST['total_amount']);
    $user_id = $_SESSION['user_id'];

    // Save booking to the database
    $result = saveBooking($user_id, $show_id, $seat_numbers, $total_amount);

    if ($result['success']) {
        redirect('booking-confirmation.php?id=' . $result['booking_id']);
    } else {
        $_SESSION['error'] = $result['message'];
        redirect('booking.php?show_id=' . $show_id);
    }
} else {
    redirect('movies.php');
}
?>
