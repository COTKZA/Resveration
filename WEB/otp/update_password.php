<?php
session_start(); // Start session

include './../connect_db.php'; // Include database connection

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Check if the passwords match
    if ($new_password === $confirm_password) {
        // Update the password in the database
        $update_query = "UPDATE accounts 
                         SET password = ?, 
                             last_password_change = NOW() 
                         WHERE email = ?";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param("ss", $new_password, $email);

        // Check if the query was successful
        if ($stmt->execute()) {
            $_SESSION['success_message'] = 'เปลี่ยนรหัสผ่านสำเร็จเเล้ว';
            header("Location: /WEB/login_page.php"); // Redirect to login page
            exit(); // Always call exit after header redirection
        } else {
            echo "Error updating password: " . $stmt->error; // Display error if query fails
        }

        $stmt->close(); // Close statement
    } else {
        echo "Passwords do not match."; // Display error if passwords do not match
    }
}

$conn->close(); // Close database connection
?>
