<?php
session_start();
if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'user') {
    header("Location: /WEB/login_page.php");
    exit();
}

include './../../connect_db.php'; // Make sure this connects to your MySQL database

$email = $_SESSION['email'];

// Query to get the count of new reservations (where is_new = 1)
$sql_new_seats = "SELECT COUNT(*) as new_count FROM reserve_seat WHERE email = ? AND is_new = 1";

// Prepare the statement
$stmt_new_seats = $conn->prepare($sql_new_seats);

// Bind the parameters
$stmt_new_seats->bind_param("s", $email); // 's' indicates the type is string

// Execute the statement
if (!$stmt_new_seats->execute()) {
    die("Query execution failed: " . $stmt_new_seats->error);
}

// Get the result
$result = $stmt_new_seats->get_result();
$new_seat_count = 0;

if ($row = $result->fetch_assoc()) {
    $new_seat_count = $row['new_count'];
}

// Free the statement
$stmt_new_seats->close(); // Close the prepared statement
$conn->close(); // Close the connection
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ตารางเวลารถบัส</title>
    <link rel="stylesheet" href="./../../assets/css/user.css">
</head>

<body>

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

    <div class="container">
        <center><h2>ตารางเดินทาง </h2></center>

        <div id="loading" class="loading">กำลังโหลดข้อมูล...</div>
        <div id="error" class="error" style="display: none;">เกิดข้อผิดพลาดในการดึงข้อมูล</div>

        <table id="busTable" style="display:none;">
            <thead>
                <tr>
                    <th>รถ</th>
                    <th>เส้นทาง</th>
                    <th>ต้นทาง(เวลา)</th>
                    <th>ปลายทาง(เวลา)</th>
                </tr>
            </thead>
            <tbody>
                <!-- แถวข้อมูลจะถูกเพิ่มที่นี่โดย JavaScript -->
            </tbody>
        </table>
    </div>


    <script>
    // ดึงข้อมูลจาก API และแสดงในตาราง
    fetch('/API/v1/user/bus_schedule.php') // เปลี่ยน URL ให้ถูกต้อง
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            const table = document.getElementById('busTable');
            const tbody = table.querySelector('tbody');
            const loading = document.getElementById('loading');

            // ซ่อนข้อความกำลังโหลด
            loading.style.display = 'none';

            // ตรวจสอบว่ามีข้อมูลหรือไม่
            if (data.length === 0) {
                document.getElementById('error').innerText = 'ไม่พบข้อมูล';
                document.getElementById('error').style.display = 'block';
                return;
            }

            // เพิ่มแถวข้อมูลลงในตาราง
            data.forEach(row => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                <td>${row.bus}</td>
                <td>${row.route}</td>
                <td>${row.origin}</td>
                <td>${row.destination}</td>
            `;
                tbody.appendChild(tr);
            });

            // แสดงตารางเมื่อข้อมูลถูกเพิ่มแล้ว
            table.style.display = 'table';
        })
        .catch(error => {
            console.error('Error fetching data:', error);
            document.getElementById('error').innerText = 'เกิดข้อผิดพลาดในการดึงข้อมูล';
            document.getElementById('error').style.display = 'block';
            document.getElementById('loading').style.display = 'none';
        });
    </script>

</body>

</html>