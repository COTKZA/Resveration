<?php
session_start();
if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'user') {
    header("Location: /WEB/login_page.php");
    exit();
}

include './../../../connect_db.php'; // Ensure this connects to your MySQL database

$email = $_SESSION['email'];
$sql = "SELECT first_name, last_name, phone_number, office FROM accounts WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email); // Bind the email parameter
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    die("ไม่พบข้อมูลผู้ใช้");
}

$firstName = $user['first_name'];
$lastName = $user['last_name'];
$phoneNumber = $user['phone_number'];
$office = $user['office'];

// Retrieve reservation details
$travelDate = $_POST['travelDate'] ?? '';
$travelType = trim($_POST['travelType'] ?? '');
$travelReason = $_POST['travelReason'] ?? '';
$route = $_POST['route'] ?? '';
$seats = $_POST['seats'] ?? ''; // This will be a JSON string

// Decode the JSON string to get an array of selected seats
$seatsArray = json_decode($seats, true);

// Check if the decoded array is valid
if (json_last_error() !== JSON_ERROR_NONE || !is_array($seatsArray)) {
    die("ข้อมูลที่นั่งไม่ถูกต้อง");
}

if (empty($travelType) || !in_array($travelType, ['เที่ยวเดียว', 'ไป-กลับ'])) {
    die("ค่าประเภทรอบเดินทางไม่ถูกต้อง");
}

// Determine whether to set the outbound and return travel routes
$outboundTravel = $travelType === 'เที่ยวเดียว' ? $route : '';  // If one-way, set outbound travel
$returnTravel = $travelType === 'ไป-กลับ' ? $route : '';  // If round-trip, set return travel

// Loop through each selected seat and insert it into the database
try {
    foreach ($seatsArray as $seat) {
        if (!is_numeric($seat)) {
            continue; // Skip if seat is not a valid number
        }

        // Insert each seat as a separate row
        $sqlInsert = "INSERT INTO reserve_seat (first_name, last_name, email, travel_date, travel_type, outbound_travel, return_travel, seats, travel_reason, phone_number, office, status, status_seats) 
                      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'รอยืนยัน', '1')";

        $stmtInsert = $conn->prepare($sqlInsert);
        if (!$stmtInsert) {
            throw new Exception($conn->error);
        }

        $stmtInsert->bind_param(
            "sssssssssss",
            $firstName,
            $lastName,
            $email,
            $travelDate,
            $travelType,
            $outboundTravel,   // Insert outbound travel for one-way trip
            $returnTravel,     // Insert return travel for round trip
            $seat,             // Insert individual seat number
            $travelReason,
            $phoneNumber,
            $office
        );

        if (!$stmtInsert->execute()) {
            throw new Exception($stmtInsert->error);
        }
    }

    // Set success message in session
    $_SESSION['success_message'] = 'จองที่นั่งสำเร็จแล้ว!';
    header("Location: /WEB/page/user/seat_system.php");
    exit();
} catch (Exception $e) {
    die("เกิดข้อผิดพลาดในการบันทึกข้อมูล: " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'));
}

// Close the prepared statements
$stmt->close();
$conn->close();
?>
