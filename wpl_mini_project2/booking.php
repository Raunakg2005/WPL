<?php
require_once 'includes/functions.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

if (!isset($_GET['show_id']) || empty($_GET['show_id'])) {
    redirect('movies.php');
}

$show_id = intval($_GET['show_id']);

$show = getShowById($show_id);

if (!$show) {
    redirect('movies.php');
}

$page_title = "Book Tickets - " . $show['movie_title'];

include 'includes/header.php';
?>

<div class="container py-5">
    <h1 class="mb-4">Book Tickets</h1>
    
    <div class="row">
        <div class="col-lg-8">
            <div class="booking-form">
                <h3 class="booking-form-title">Select Seats</h3>
                
                <div class="booking-summary mb-4">
                    <div class="row">
                        <div class="col-md-3">
                            <img src="<?php echo $show['movie_poster']; ?>" alt="<?php echo $show['movie_title']; ?>" class="img-fluid rounded">
                        </div>
                        <div class="col-md-9">
                            <h4><?php echo $show['movie_title']; ?></h4>
                            <p>
                                <strong>Date:</strong> <?php echo date('l, F d, Y', strtotime($show['show_date'])); ?><br>
                                <strong>Time:</strong> <?php echo date('h:i A', strtotime($show['show_time'])); ?><br>
                                <strong>Theater:</strong> <?php echo $show['theater_name']; ?> (<?php echo $show['theater_location']; ?>)<br>
                                <strong>Price:</strong> $<?php echo number_format($show['price'], 2); ?> per seat<br>
                                <strong>Available Seats:</strong> <?php echo $show['available_seats']; ?>
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="seat-selection">
                    <div class="screen mb-4"></div>
                    
                    <div class="seats-container">
                        <?php
                        $rows = ['A', 'B', 'C', 'D', 'E'];
                        $seats_per_row = 10;
                        $total_seats = count($rows) * $seats_per_row;
                        $booked_seats = $total_seats - $show['available_seats'];

                        $booked_seats_array = [];
                        for ($i = 0; $i < $booked_seats; $i++) {
                            $random_row = $rows[array_rand($rows)];
                            $random_seat = rand(1, $seats_per_row);
                            $booked_seats_array[] = $random_row . $random_seat;
                        }

                        foreach ($rows as $row) {
                            echo '<div class="seat-row d-flex align-items-center mb-2">';
                            echo '<div class="seat-row-label me-3">' . $row . '</div>';

                            for ($i = 1; $i <= $seats_per_row; $i++) {
                                $seat_number = $row . $i;
                                $is_booked = in_array($seat_number, $booked_seats_array);
                                $seat_class = $is_booked ? 'seat booked' : 'seat';

                                echo '<div class="' . $seat_class . '" data-seat="' . $seat_number . '" tabindex="0">' . $i . '</div>';
                            }

                            echo '</div>';
                        }
                        ?>
                    </div>
                    
                    <div class="seat-legend mt-4">
                        <div class="seat-legend-item">
                            <div class="seat-legend-color available"></div>
                            <span>Available</span>
                        </div>
                        <div class="seat-legend-item">
                            <div class="seat-legend-color selected"></div>
                            <span>Selected</span>
                        </div>
                        <div class="seat-legend-item">
                            <div class="seat-legend-color booked"></div>
                            <span>Booked</span>
                        </div>
                    </div>
                </div>
                
                <form id="booking-form" action="process-booking.php" method="post">
                    <input type="hidden" name="show_id" value="<?php echo $show_id; ?>">
                    <input type="hidden" name="seat_numbers" id="seat-numbers" value="">
                    <input type="hidden" name="seats_booked" id="seats-booked" value="">
                    <input type="hidden" name="total_amount" id="total-amount" value="">
                    
                    <div class="booking-summary mt-4">
                        <h5 class="booking-summary-title">Booking Summary</h5>
                        <div class="booking-summary-item">
                            <span>Selected Seats:</span>
                            <span id="selected-seats">None</span>
                        </div>
                        <div class="booking-summary-item">
                            <span>Price per Seat:</span>
                            <span>$<?php echo number_format($show['price'], 2); ?></span>
                        </div>
                        <div class="booking-summary-total">
                            <span>Total Amount:</span>
                            <span>$<span id="total-price" data-price="<?php echo $show['price']; ?>">0.00</span></span>
                        </div>
                    </div>
                    
                    <div class="d-grid mt-4">
                        <button type="submit" class="btn btn-primary btn-lg" disabled>Proceed to Payment</button>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Booking Information</h5>
                </div>
                <div class="card-body">
                    <p>Please select your preferred seats from the seating chart. You can select multiple seats.</p>
                    <p>Once you have selected your seats, review your booking summary and proceed to payment.</p>
                    <p>Please note:</p>
                    <ul>
                        <li>Seats marked as booked are already taken.</li>
                        <li>You cannot change your seats after booking.</li>
                        <li>Cancellations are allowed up to 24 hours before the show time.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const seats = document.querySelectorAll('.seat:not(.booked)');
        const selectedSeats = [];
        const seatNumbersInput = document.getElementById('seat-numbers');
        const totalAmountInput = document.getElementById('total-amount');
        const selectedSeatsDisplay = document.getElementById('selected-seats');
        const totalPriceDisplay = document.getElementById('total-price');
        const proceedButton = document.querySelector('button[type="submit"]');
        const pricePerSeat = parseFloat(totalPriceDisplay.dataset.price);

        seats.forEach(seat => {
            seat.addEventListener('click', function () {
                const seatNumber = this.textContent;
                const row = this.parentElement.querySelector('.seat-row-label').textContent;
                const fullSeatNumber = row + seatNumber;

                if (selectedSeats.includes(fullSeatNumber)) {
                    selectedSeats.splice(selectedSeats.indexOf(fullSeatNumber), 1);
                    this.classList.remove('selected');
                } else {
                    selectedSeats.push(fullSeatNumber);
                    this.classList.add('selected');
                }

                seatNumbersInput.value = selectedSeats.join(',');
                totalAmountInput.value = (selectedSeats.length * pricePerSeat).toFixed(2);
                selectedSeatsDisplay.textContent = selectedSeats.length > 0 ? selectedSeats.join(', ') : 'None';
                totalPriceDisplay.textContent = (selectedSeats.length * pricePerSeat).toFixed(2);

                proceedButton.disabled = selectedSeats.length === 0;
            });
        });
    });
</script>

<?php
include 'includes/footer.php';
?>