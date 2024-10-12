<?php
session_start();
include './../../connect_db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $cancellation_reason = $_POST['cancellation_reason'];

    // Ensure the input is encoded in UTF-8
    $cancellation_reason = htmlspecialchars($cancellation_reason, ENT_QUOTES, 'UTF-8');

    // SQL query using prepared statement
    $sql = "UPDATE reserve_seat SET status = 'ยกเลิกแล้ว', status_seats = '0', reason_cancellation = ? WHERE id = ?";

    // Prepare the statement
    $stmt = $conn->prepare($sql);

    // Bind the parameters
    $stmt->bind_param("si", $cancellation_reason, $id); // 's' for string, 'i' for integer

    // Execute the statement
    if (!$stmt->execute()) {
        die("Error executing query: " . $stmt->error);
    }

    // Redirect back to reservation list
    header("Location: /WEB/page/user/reservationlist.php");
    exit();
}

// Close the statement and connection (optional, as PHP will close them automatically at the end of the script)
$stmt->close();
$conn->close();
?>
