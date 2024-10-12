<?php
session_start(); // เริ่ม session

// Include database connection
include 'connect_db.php';

// Process form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize input
    $first_name = htmlspecialchars($_POST['first_name']);
    $last_name = htmlspecialchars($_POST['last_name']);
    $email = htmlspecialchars($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $phone_number = htmlspecialchars($_POST['phone_number']);
    $employee_id = htmlspecialchars($_POST['employee_id']);
    $office_id = htmlspecialchars($_POST['office_id']);

    // Check if passwords match
    if ($password !== $confirm_password) {
        header("Location: register_page.php?error=passwords_do_not_match");
        exit();
    }

    // Check if email already exists
    $checkEmailSql = "SELECT COUNT(*) AS count FROM accounts WHERE email = ?";
    $checkEmailStmt = $conn->prepare($checkEmailSql);
    $checkEmailStmt->bind_param("s", $email);
    $checkEmailStmt->execute();
    $result = $checkEmailStmt->get_result();
    $row = $result->fetch_assoc();

    if ($row['count'] > 0) {
        $_SESSION['email_use'] = 'การลงทะเบียนล้มเหลว อีเมล์นี้ถูกใช้ไปเเล้ว!!';
        header("Location: register_page.php");
        exit();
    }

    // Prepare the SQL query
    $sql = "INSERT INTO accounts (first_name, last_name, email, password, phone_number, employee_id, office) 
            VALUES (?, ?, ?, ?, ?, ?, (SELECT office_name FROM office_list WHERE id = ?))";

    // Prepare the statement
    $stmt = $conn->prepare($sql);

    // Parameters to bind
    $stmt->bind_param("ssssssi", $first_name, $last_name, $email, $password, $phone_number, $employee_id, $office_id);

    // Execute the query
    if ($stmt->execute()) {
        // บันทึกข้อความสำเร็จใน session
        $_SESSION['success_message'] = 'สมัครสมาชิกสำเร็จแล้ว!';
        header("Location: login_page.php");
        exit();
    } else {
        $_SESSION['error_message'] = 'การลงทะเบียนล้มเหลว กรุณาลองใหม่อีกครั้ง.';
        header("Location: register_page.php");
        exit();
    }

    // Close the statements and the connection
    $stmt->close();
    $checkEmailStmt->close();
    $conn->close();
}
?>
