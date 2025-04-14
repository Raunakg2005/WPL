<?php
require_once 'includes/functions.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

if (!isset($_GET['id']) || empty($_GET['id'])) {
    redirect('user-dashboard.php');
}

$booking_id = intval($_GET['id']);

$stmt = $conn->prepare("
    SELECT b.*, s.show_date, s.show_time, s.price, 
           m.title as movie_title, m.poster as movie_poster,
           t.name as theater_name, t.location as theater_location
    FROM bookings b
    JOIN shows s ON b.show_id = s.id
    JOIN movies m ON s.movie_id = m.id
    JOIN theaters t ON s.theater_id = t.id
    WHERE b.id = ? AND b.user_id = ?
");
$stmt->bind_param("ii", $booking_id, $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    $_SESSION['error'] = 'Booking not found.';
    redirect('user-dashboard.php');
}

$booking = $result->fetch_assoc();

$page_title = "Booking Confirmation";

include 'includes/header.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow booking-card">
                <div class="card-header <?php echo $booking['booking_status'] == 'confirmed' ? 'bg-success' : 'bg-danger'; ?> text-white">
                    <h3 class="mb-0">
                        <?php echo $booking['booking_status'] == 'confirmed' ? 'Booking Confirmed!' : 'Booking Cancelled'; ?>
                    </h3>
                </div>
                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <?php if ($booking['booking_status'] == 'confirmed'): ?>
                            <i class="fas fa-check-circle text-success fa-5x mb-3"></i>
                            <h4>Thank you for your booking!</h4>
                            <p>Your booking has been confirmed. Below are your booking details.</p>
                        <?php else: ?>
                            <i class="fas fa-times-circle text-danger fa-5x mb-3"></i>
                            <h4>Your booking has been cancelled.</h4>
                            <p>Unfortunately, this booking is no longer active. Below are the details for your reference.</p>
                        <?php endif; ?>
                    </div>
                    
                    <div class="row mb-4 align-items-center">
                        <div class="col-md-4 text-center">
                            <img src="<?php echo $booking['movie_poster']; ?>" alt="<?php echo $booking['movie_title']; ?>" class="img-fluid rounded movie-poster">
                        </div>
                        <div class="col-md-8">
                            <h5><?php echo $booking['movie_title']; ?></h5>
                            <p>
                                <strong>Date:</strong> <?php echo date('l, F d, Y', strtotime($booking['show_date'])); ?><br>
                                <strong>Time:</strong> <?php echo date('h:i A', strtotime($booking['show_time'])); ?><br>
                                <strong>Theater:</strong> <?php echo $booking['theater_name']; ?> (<?php echo $booking['theater_location']; ?>)<br>
                            </p>
                        </div>
                    </div>
                    
                    <div class="booking-details mb-4">
                        <h5 class="mb-3">Booking Details</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <tr>
                                    <th>Booking ID</th>
                                    <td><?php echo $booking['id']; ?></td>
                                </tr>
                                <tr>
                                    <th>Booking Date</th>
                                    <td><?php echo date('F d, Y h:i A', strtotime($booking['booking_date'])); ?></td>
                                </tr>
                                <tr>
                                    <th>Number of Seats</th>
                                    <td><?php echo $booking['seats_booked']; ?></td>
                                </tr>
                                <tr>
                                    <th>Seat Numbers</th>
                                    <td><?php echo $booking['seat_numbers']; ?></td>
                                </tr>
                                <tr>
                                    <th>Price per Seat</th>
                                    <td>$<?php echo number_format($booking['price'], 2); ?></td>
                                </tr>
                                <tr>
                                    <th>Total Amount</th>
                                    <td>$<?php echo number_format($booking['total_amount'], 2); ?></td>
                                </tr>
                                <tr>
                                    <th>Booking Status</th>
                                    <td>
                                        <?php if ($booking['booking_status'] == 'confirmed'): ?>
                                            <span class="badge bg-success">Confirmed</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Cancelled</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    
                    <div class="text-center action-buttons">
                        <a href="user-dashboard.php" class="btn btn-primary me-2">
                            <i class="fas fa-home me-1"></i> Go to Dashboard
                        </a>
                        <button class="btn btn-outline-secondary me-2" onclick="printTicket()">
                            <i class="fas fa-print me-1"></i> Print Ticket
                        </button>
                        
                        <?php if ($booking['booking_status'] == 'confirmed' && strtotime($booking['show_date']) > time()): ?>
                            <a href="cancel-booking.php?id=<?php echo $booking['id']; ?>" class="btn btn-outline-danger" onclick="return confirm('Are you sure you want to cancel this booking?')">
                                <i class="fas fa-times me-1"></i> Cancel Booking
                            </a>
                        <?php endif; ?>

                        <script>
                            function printTicket() {
                                const originalContent = document.body.innerHTML;
                                const ticketContent = document.querySelector('.card').innerHTML;

                                const printWindow = window.open('', '_blank');
                                printWindow.document.open();
                                printWindow.document.write(`
                                    <html>
                                    <head>
                                        <title>Print Ticket</title>
                                        <style>
                                            body {
                                                font-family: Arial, sans-serif;
                                                margin: 20px;
                                            }
                                            .card-header {
                                                background-color: #28a745;
                                                color: white;
                                                text-align: center;
                                                padding: 10px;
                                                font-size: 18px;
                                            }
                                            .card-body img {
                                                max-width: 150px;
                                                display: block;
                                                margin: 0 auto 10px;
                                            }
                                            table {
                                                width: 100%;
                                                border-collapse: collapse;
                                                margin-top: 20px;
                                            }
                                            table, th, td {
                                                border: 1px solid #ddd;
                                            }
                                            th, td {
                                                padding: 8px;
                                                text-align: left;
                                            }
                                            th {
                                                background-color: #f2f2f2;
                                            }
                                        </style>
                                    </head>
                                    <body>
                                        <div class="card">
                                            ${ticketContent}
                                        </div>
                                    </body>
                                    </html>
                                `);
                                printWindow.document.close();
                                printWindow.print();
                                printWindow.close();
                            }
                        </script>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Custom CSS for the booking confirmation page */
    .booking-card {
        border-radius: 10px;
        overflow: hidden;
        border: none;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1) !important;
    }
    
    .booking-card .card-header {
        padding: 15px;
        font-weight: 600;
    }
    
    .movie-poster {
        max-height: 200px;
        width: auto;
        margin: 0 auto;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }
    
    .booking-details h5 {
        color: #333;
        border-bottom: 2px solid #f0f0f0;
        padding-bottom: 8px;
    }
    
    .table {
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
    }
    
    .table th {
        width: 40%;
        background-color: #f8f9fa;
    }
    
    .action-buttons {
        margin-top: 25px;
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 10px;
    }
    
    .action-buttons .btn {
        padding: 8px 20px;
        border-radius: 5px;
        font-weight: 500;
    }
    
    @media (max-width: 767px) {
        .movie-poster {
            max-height: 180px;
            margin-bottom: 20px;
        }
        
        .action-buttons {
            flex-direction: column;
        }
        
        .action-buttons .btn {
            margin-bottom: 10px;
            width: 100%;
        }
    }
</style>

<?php
include 'includes/footer.php';
?>