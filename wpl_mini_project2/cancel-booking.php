<?php
require_once 'includes/functions.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

if (!isset($_GET['id']) || empty($_GET['id'])) {
    redirect('user-dashboard.php');
}

$booking_id = intval($_GET['id']);
$user_id = $_SESSION['user_id'];
$result = cancelBooking($booking_id, $user_id);

if ($result['success']) {
    $_SESSION['success'] = $result['message'];
} else {
    $_SESSION['error'] = $result['message'];
}

redirect('user-dashboard.php');
?>
