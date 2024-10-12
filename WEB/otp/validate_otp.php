<?php
include './../connect_db.php'; // Include database connection

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $otp = $_POST['otp'];

    // Validate OTP
    $query = "SELECT * FROM otp_requests WHERE email = ? AND otp = ? AND expiration > NOW() AND used = 0";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $email, $otp);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Mark OTP as used
        $update_query = "UPDATE otp_requests SET used = 1 WHERE email = ? AND otp = ?";
        $update_stmt = $conn->prepare($update_query);
        $update_stmt->bind_param("ss", $email, $otp);
        $update_stmt->execute();

        // Redirect to reset password page
        header("Location: reset_password.php?email=" . urlencode($email));
        exit();
    } else {
        // Invalid OTP or OTP expired
        echo "<script>alert('รหัส OTP ไม่ถูกต้อง หรือ รหัส OTP หมดอายุแล้ว'); window.history.back();</script>";
        exit();
    }

    $stmt->close(); // Close statement
    $update_stmt->close(); // Close update statement
}

$conn->close(); // Close database connection
?>
