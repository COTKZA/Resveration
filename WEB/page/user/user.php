<?php
session_start();
if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'user') {
    header("Location: /WEB/login_page.php");
    exit();
}

// Include your database connection
include './../../connect_db.php';

$email = $_SESSION['email'];

// Query to get the count of new reservations (where is_new = 1)
$sql_new_seats = "SELECT COUNT(*) as new_count FROM reserve_seat WHERE email = ? AND is_new = 1";
$stmt_new_seats = $conn->prepare($sql_new_seats);

if ($stmt_new_seats === false) {
    die("Error preparing statement: " . $conn->error);
}

// Bind parameters
$stmt_new_seats->bind_param("s", $email);

// Execute the prepared statement
$stmt_new_seats->execute();

// Get the result
$result_new_seats = $stmt_new_seats->get_result();

$new_seat_count = 0;
if ($row = $result_new_seats->fetch_assoc()) {
    $new_seat_count = $row['new_count'];
}

// Free the statement
$stmt_new_seats->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sidebar Navigation</title>
    <link rel="stylesheet" href="./../../assets/css/user.css">
</head>

<body>
    <div class="main-content">
        <h1>เนื้อหาหลัก</h1>
        <p>นี่คือพื้นที่สำหรับเนื้อหาหลักของหน้าเว็บ เช่น ข่าวสาร หรือข้อมูลสำคัญอื่น ๆ ที่เกี่ยวข้อง</p>
    </div>

    <!-- Sidebar -->
    <div class="sidebar">
    <h2>user</h2>
    <ul>
        <li><a href="profile.php">ข้อมูลส่วนตัว</a></li>
        <li><a href="seat_system.php">จองที่นั่ง</a></li>
        <li><a href="reservationlist.php">
            รายการสำรองที่นั่ง
            <?php if ($new_seat_count > 0): ?>
                <span class="notification-badge"><?php echo $new_seat_count; ?></span>
            <?php endif; ?>
        </a></li>
        <li><a href="busschedule.php">ตารางการเดินรถ</a></li>
        <li><a href="/WEB/logout.php">logout</a></li>
    </ul>
</div>

</body>

</html>