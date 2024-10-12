<?php
session_start(); // เริ่ม session
if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'user') {
    header("Location: /WEB/login_page.php");
    exit();
}
include './../../connect_db.php';

$email = $_SESSION['email'];

// Update query to mark new seats as viewed
$sql_update_new = "UPDATE reserve_seat SET is_new = 0 WHERE email = ? AND is_new = 1";
$stmt_update_new = $conn->prepare($sql_update_new);

if ($stmt_update_new === false) {
    die("Error preparing statement: " . $conn->error);
}

// Bind the parameters
$stmt_update_new->bind_param("s", $email); // 's' indicates the parameter type is string

// Execute the prepared statement
if (!$stmt_update_new->execute()) {
    die("Error executing query: " . $stmt_update_new->error);
}

// Free the statement
$stmt_update_new->close();

// Close the database connection
$conn->close();
?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservation List</title>
    <link rel="stylesheet" href="./../../assets/css/user.css">
    <style>
    .status-pending {
        color: #FFD900FF;
        /* สีเหลือง สำหรับรอยืนยัน */
    }

    .status-cancelled {
        color: #FF0000FF;
        /* สีแดง สำหรับยกเลิกแล้ว */
    }

    .status-confirmed {
        color: #029916FF;
        /* สีเขียว สำหรับยืนยันแล้ว */
    }
    </style>
</head>

<body>

    <h2>รายการจอง</h2>

    <table>
        <thead>
            <tr>
                <th>วันเดินทาง</th>
                <th>ประเภทการเดินทาง</th>
                <th>รอบเวลาใช้บริการรถตู้</th>
                <th>ที่นั่ง</th>
                <th>สถานะ</th>
                <th>ยกเลิก</th>
            </tr>
        </thead>
        <tbody>
            <?php
            include './../../connect_db.php';

            $email = $_SESSION['email'];

            // Prepare the SQL query
            $sql = "SELECT id, travel_date, travel_type, outbound_travel, return_travel, seats, status FROM reserve_seat WHERE email = ?";
            $stmt = $conn->prepare($sql);

            if ($stmt === false) {
                die("Error preparing statement: " . $conn->error);
            }

            // Bind the parameter
            $stmt->bind_param("s", $email);

            // Execute the prepared statement
            $stmt->execute();

            // Get the result
            $result = $stmt->get_result();

            while ($row = $result->fetch_assoc()) {
                // Determine the correct travel route to display based on the travel type
                $route = '';
                if ($row['travel_type'] === 'เที่ยวเดียว') {
                    $route = htmlspecialchars($row['outbound_travel']);
                } elseif ($row['travel_type'] === 'ไป-กลับ') {
                    $route = htmlspecialchars($row['return_travel']);
                }
                
                // Set text color for status
                $statusClass = '';
                switch (htmlspecialchars($row['status'])) {
                    case 'รอยืนยัน':
                        $statusClass = 'status-pending';
                        break;
                    case 'ยกเลิกแล้ว':
                        $statusClass = 'status-cancelled';
                        break;
                    case 'ยืนยันแล้ว':
                        $statusClass = 'status-confirmed';
                        break;
                }

                echo "<tr>
                    <td>" . htmlspecialchars(date('d-m-Y', strtotime($row['travel_date']))) . "</td>
                    <td>" . htmlspecialchars($row['travel_type']) . "</td>
                    <td>" . $route . "</td>
                    <td>" . htmlspecialchars($row['seats']) . "</td>
                    <td><span class='$statusClass'>" . htmlspecialchars($row['status']) . "</span></td>
                    <td>
                        <button type='button' class='cancel-btn' onclick='showModal(" . $row['id'] . ", \"" . htmlspecialchars(date('Y-m-d', strtotime($row['travel_date']))) . "\", \"" . htmlspecialchars($route) . "\")'>ยกเลิก</button>
                    </td>
                </tr>";
            }

            // Free the statement
            $stmt->close();

            // Close the database connection
            $conn->close();
            ?>
        </tbody>
    </table>

    <!-- Modal for cancellation -->
    <div id="cancelModal"
        style="display:none; position:fixed; top:50%; left:50%; transform:translate(-50%, -50%); background-color:white; border:1px solid #ccc; padding:20px; z-index:1000;">
        <h3>เหตุผลการยกเลิก</h3>
        <textarea id="cancellation_reason" placeholder="กรอกเหตุผลที่นี่..." required></textarea>
        <br><br>
        <button onclick="submitCancellation()">ยืนยันการยกเลิก</button>
        <button onclick="closeModal()">ปิด</button>
    </div>

    <div id="overlay"
        style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:999;">
    </div>

    <script>
    let travelId, travelDate, route;

    function showModal(id, date, r) {
        travelId = id;
        travelDate = date;
        route = r;
        document.getElementById("cancelModal").style.display = "block";
        document.getElementById("overlay").style.display = "block";
    }

    function submitCancellation() {
        const cancellationReason = document.getElementById("cancellation_reason").value;
        if (cancellationReason.trim() === "") {
            alert("กรุณากรอกเหตุผลการยกเลิก");
            return;
        }

        const form = document.createElement("form");
        form.method = "POST";
        form.action = "cancel_reservation.php";

        // Add the hidden inputs
        form.appendChild(createHiddenInput("id", travelId));
        form.appendChild(createHiddenInput("travel_date", travelDate));
        form.appendChild(createHiddenInput("route", route));
        form.appendChild(createHiddenInput("cancellation_reason", cancellationReason));

        document.body.appendChild(form);
        form.submit();
    }

    function createHiddenInput(name, value) {
        const input = document.createElement("input");
        input.type = "hidden";
        input.name = name;
        input.value = value;
        return input;
    }

    function closeModal() {
        document.getElementById("cancelModal").style.display = "none";
        document.getElementById("overlay").style.display = "none";
        document.getElementById("cancellation_reason").value = ""; // ล้างข้อความใน textarea
    }
    </script>

</body>

</html>