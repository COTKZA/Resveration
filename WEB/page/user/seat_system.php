<?php
session_start(); // เริ่ม session
if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'user') {
    header("Location: /WEB/login_page.php");
    exit();
}

// แสดงข้อความสมัครสมาชิกสำเร็จ
if (isset($_SESSION['success_message'])) {
    echo "<script>alert('" . $_SESSION['success_message'] . "');</script>";
    unset($_SESSION['success_message']); // ลบข้อความหลังจากแสดงแล้ว
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

// Bind the parameter
$stmt_new_seats->bind_param("s", $email);

// Execute the prepared statement
$stmt_new_seats->execute();

// Get the result
$result_new_seats = $stmt_new_seats->get_result();

// Fetch the count of new seats
$new_seat_count = 0;
if ($row = $result_new_seats->fetch_assoc()) {
    $new_seat_count = $row['new_count'];
}

// Free the statement
$stmt_new_seats->close();

// Close the database connection if not needed further
$conn->close();
?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เลือกการเดินทาง</title>
    <link rel="stylesheet" href="./../../assets/css/user.css">
    <style>
        .hidden {
            display: none;
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
        <h1>ระบบจองที่นั่ง</h1>
        <form id="travelForm">
            <div class="form-group">
                <label for="travelDate">วันที่เดินทาง:</label>
                <input type="date" id="travelDate" name="travelDate" required>
            </div>

            <div class="form-group">
                <label for="travelType">ประเภทรอบเดินทาง:</label>
                <select id="travelType" name="travelType" required>
                    <option value="">-- กรุณาเลือกประเภทรอบเดินทาง --</option>
                    <option value="เที่ยวเดียว">เที่ยวเดียว</option>
                    <option value="ไป-กลับ">ไป-กลับ</option>
                </select>
            </div>

            <div id="single-trip-options" class="form-group hidden">
                <label for="route-select">เลือกเส้นทาง (หรือพิมพ์ใหม่):</label>
                <select id="route-select">
                    <option value="">-- กรุณาเลือกเส้นทาง --</option>
                    <option value="other">พิมพ์เส้นทางใหม่</option>
                </select>
                <div class="form-group hidden" id="customRouteInput">
                    <input type="text" id="customRoute" name="customRoute" placeholder="ระบุเส้นทางที่ต้องการ">
                </div>
            </div>

            <div id="round-trip-text" class="form-group hidden">
                <label for="roundTripText">กรุณาพิมพ์เส้นทาง:</label>
                <input type="text" id="roundTripText" name="roundTripText" placeholder="ระบุเส้นทางที่ต้องการ">
            </div>

            <div class="form-group">
                <label for="travelReason">เหตุผลในการเดินทาง:</label>
                <select id="travelReason" name="travelReason" required>
                    <option value="ประชุม">ประชุม</option>
                    <option value="ติดต่อ">ติดต่อ</option>
                    <option value="สอน">สอน</option>
                    <option value="other">อื่นๆ (กรุณาระบุ)</option>
                </select>
            </div>

            <div class="form-group hidden" id="customReasonInput">
                <input type="text" id="customReason" name="customReason" placeholder="ระบุเหตุผลที่ต้องการ">
            </div>

            <button type="submit">ค้นหา</button>
        </form>
    </div>

    <script>
        async function fetchRoutes() {
            try {
                const response = await fetch('/API/v1/user/bus_schedule.php');
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                const data = await response.json();
                if (Array.isArray(data)) {
                    populateRouteSelect(data);
                } else {
                    console.error("ไม่พบข้อมูล");
                }
            } catch (error) {
                console.error('ข้อผิดพลาดในการดึงข้อมูล:', error);
            }
        }

        function populateRouteSelect(routes) {
            const select = document.getElementById('route-select');
            select.innerHTML = '<option value="">-- กรุณาเลือกเส้นทาง --</option>';
            routes.forEach(route => {
                const option = document.createElement('option');
                option.value = route.route;
                option.textContent = route.route;
                select.appendChild(option);
            });
            const customOption = document.createElement('option');
            customOption.value = 'other';
            customOption.textContent = 'พิมพ์เส้นทางใหม่';
            select.appendChild(customOption);
        }

        document.getElementById('travelType').addEventListener('change', function () {
            const travelType = this.value;
            const singleTripOptions = document.getElementById('single-trip-options');
            const roundTripText = document.getElementById('round-trip-text');

            if (travelType === 'เที่ยวเดียว') {
                singleTripOptions.classList.remove('hidden');
                roundTripText.classList.add('hidden');
                fetchRoutes();
            } else if (travelType === 'ไป-กลับ') {
                singleTripOptions.classList.add('hidden');
                roundTripText.classList.remove('hidden');
            } else {
                singleTripOptions.classList.add('hidden');
                roundTripText.classList.add('hidden');
            }
        });

        document.getElementById('route-select').addEventListener('change', function () {
            const routeSelect = this.value;
            const customRouteInput = document.getElementById('customRouteInput');
            customRouteInput.classList.toggle('hidden', routeSelect !== 'other');
        });

        document.getElementById('travelReason').addEventListener('change', function () {
            const reasonSelect = this.value;
            const customReasonInput = document.getElementById('customReasonInput');
            customReasonInput.classList.toggle('hidden', reasonSelect !== 'other');
        });

        document.getElementById('travelForm').addEventListener('submit', function (event) {
            event.preventDefault();
            const travelDate = document.getElementById('travelDate').value;
            const travelType = document.getElementById('travelType').value;
            const travelReason = document.getElementById('travelReason').value;
            let route = '';

            if (travelType === 'เที่ยวเดียว') {
                const selectedRoute = document.getElementById('route-select').value;
                route = selectedRoute === 'other' ? document.getElementById('customRoute').value : selectedRoute;
            } else if (travelType === 'ไป-กลับ') {
                route = document.getElementById('roundTripText').value;
            }

            if (!route) {
                alert('กรุณาระบุเส้นทาง');
                return;
            }

            let reason = travelReason;
            if (travelReason === 'other') {
                reason = document.getElementById('customReason').value;
            }

            if (!reason) {
                alert('กรุณาระบุเหตุผลในการเดินทาง');
                return;
            }

            // เปลี่ยนเส้นทางไปหน้าที่นั่ง
            window.location.href = `seats.php?date=${encodeURIComponent(travelDate)}&type=${encodeURIComponent(travelType)}&reason=${encodeURIComponent(reason)}&route=${encodeURIComponent(route)}`;
        });
    </script>

</body>

</html>
