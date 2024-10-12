<?php
session_start();
if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'user') {
    header("Location: /WEB/login_page.php");
    exit();
}

include './../../connect_db.php'; // Include your database connection file

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Get parameters from the URL
$travelDate = $_GET['date'] ?? '';
$travelType = $_GET['type'] ?? '';
$route = $_GET['route'] ?? '';
$reason = $_GET['reason'] ?? '';

// Fetch seat statuses based on travel date and route
function fetchSeatStatuses($travelDate, $route) {
    global $conn;
    $sql = "SELECT seats, status_seats FROM reserve_seat WHERE travel_date = ? AND (outbound_travel = ? OR return_travel = ?)";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        die("Error preparing statement: " . $conn->error);
    }

    // Bind parameters
    $stmt->bind_param("sss", $travelDate, $route, $route);

    // Execute the prepared statement
    $stmt->execute();

    // Get the result
    $result = $stmt->get_result();

    $seatsStatus = [];
    while ($row = $result->fetch_assoc()) {
        $seatsStatus[$row['seats']] = $row['status_seats'];
    }

    // Free the statement
    $stmt->close();
    
    return $seatsStatus;
}

// Fetching seat statuses
$seatsStatus = fetchSeatStatuses($travelDate, $route);
?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เลือกที่นั่ง</title>
    <link rel="stylesheet" href="./../../assets/css/user.css">
    <style>
        .seat {
            width: 50px;
            height: 50px;
            display: inline-block;
            margin: 5px;
            text-align: center;
            line-height: 50px; /* Center text vertically */
            cursor: pointer;
        }

        .available {
            background-color: green; /* Available seats */
        }

        .unavailable {
            background-color: grey; /* Unavailable seats */
            cursor: not-allowed; /* Indicate that these seats cannot be clicked */
        }

        .selected {
            background-color: yellow; /* Selected seats */
        }
    </style>
</head>

<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <h2>user</h2>
        <ul>
            <li><a href="profile.php">ข้อมูลส่วนตัว</a></li>
            <li><a href="seat_system.php">จองที่นั่ง</a></li>
            <li><a href="reservationlist.php">รายการสำรองที่นั่ง</a></li>
            <li><a href="busschedule.php">ตารางการเดินรถ</a></li>
            <li><a href="/WEB/logout.php">logout</a></li>
        </ul>
    </div>
    
    <div class="container">
        <h1>เลือกที่นั่ง</h1>

        <div class="seat-container">
            <?php
            for ($i = 1; $i <= 10; $i++) {
                $status = isset($seatsStatus[$i]) ? $seatsStatus[$i] : '0'; // Default to available
                $class = $status === '1' ? 'seat unavailable' : 'seat available';
                echo "<div class='$class' data-seat='$i'>$i</div>";
            }
            ?>
        </div>

        <form id="reservationForm" method="POST" action="./php/confirm_seats.php">
            <input type="hidden" name="travelDate" value="<?php echo htmlspecialchars($travelDate); ?>">
            <input type="hidden" name="travelType" value="<?php echo htmlspecialchars($travelType); ?>">
            <input type="hidden" name="travelReason" value="<?php echo htmlspecialchars($reason); ?>">
            <input type="hidden" name="route" value="<?php echo htmlspecialchars($route); ?>">
            <input type="hidden" name="seats" id="selectedSeats" value="">

            <button type="submit">ยืนยันการจอง</button>
        </form>
    </div>

    <script>
        const availableSeats = document.querySelectorAll('.available');
        const selectedSeatsInput = document.getElementById('selectedSeats');
        let selectedSeats = [];

        availableSeats.forEach(seat => {
            seat.addEventListener('click', function() {
                const seatNumber = this.dataset.seat;

                if (selectedSeats.includes(seatNumber)) {
                    selectedSeats = selectedSeats.filter(s => s !== seatNumber);
                    this.classList.remove('selected'); // Remove selected class
                } else {
                    selectedSeats.push(seatNumber);
                    this.classList.add('selected'); // Add selected class
                }

                selectedSeatsInput.value = JSON.stringify(selectedSeats); // Update hidden input
            });
        });
    </script>

</body>

</html>
