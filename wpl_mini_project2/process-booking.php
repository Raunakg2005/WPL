<?php
require_once 'includes/functions.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    redirect('movies.php');
}

$show_id = isset($_POST['show_id']) ? intval($_POST['show_id']) : 0;
$seat_numbers = isset($_POST['seat_numbers']) ? sanitize($_POST['seat_numbers']) : '';
$seats_booked = isset($_POST['seats_booked']) ? intval($_POST['seats_booked']) : 0;
$total_amount = isset($_POST['total_amount']) ? floatval($_POST['total_amount']) : 0;

error_log("Show ID: " . $show_id);
error_log("Seat Numbers: " . $seat_numbers);
error_log("Seats Booked: " . $seats_booked);
error_log("Total Amount: " . $total_amount);

if ($show_id <= 0 || empty($seat_numbers) || $seats_booked <= 0 || $total_amount <= 0) {
    $_SESSION['error'] = "Invalid booking data. Please try again.";
    redirect('booking.php?show_id=' . $show_id);
}

$available_seats = getAvailableSeats($show_id);
$selected_seats_array = explode(',', $seat_numbers);

if (count($selected_seats_array) > $available_seats) {
    $_SESSION['error'] = "Some of the selected seats are no longer available. Please try again.";
    redirect('booking.php?show_id=' . $show_id);
}

if (areSeatsAlreadyBooked($show_id, $selected_seats_array)) {
    $_SESSION['error'] = "One or more selected seats are already booked. Please choose different seats.";
    redirect('booking.php?show_id=' . $show_id);
}

$user_id = $_SESSION['user_id'];

$result = createBooking($user_id, $show_id, $seats_booked, $seat_numbers, $total_amount);

if ($result['success']) {
    $_SESSION['success'] = "Booking successful! Your booking ID is " . $result['booking_id'];
    redirect('booking-confirmation.php?id=' . $result['booking_id']);
} else {
    $_SESSION['error'] = $result['message'];
    redirect('booking.php?show_id=' . $show_id);
}
?>
