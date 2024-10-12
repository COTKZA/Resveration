<?php
session_start();

// แสดงข้อความสมัครสมาชิกสำเร็จ
if (isset($_SESSION['success_message'])) {
    echo "<script>alert('" . $_SESSION['success_message'] . "');</script>";
    unset($_SESSION['success_message']); // ลบข้อความหลังจากแสดงแล้ว
}

if (isset($_SESSION['error_message'])) {
    echo "<script>alert('" . $_SESSION['error_message'] . "');</script>";
    unset($_SESSION['error_message']); // ลบข้อความหลังจากแสดงแล้ว
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="./assets/css/login.css">
</head>

<body>
    <div class="container">
        <h2>Login</h2>

        <form action="login.php" method="POST">
            <label for="email">อีเมล์</label>
            <input type="email" id="email" name="email" required>
            <br>
            <label for="password">รหัสผ่าน</label>
            <input type="password" id="password" name="password" required>
            <br>
            <input type="submit" value="Login">
        </form>
        <div>
            <a href="register_page.php" class="register">สมัครสมาชิก?</a>
            <a href="./otp/request_otp.php" class="forgot-password">ลืมรหัสผ่าน?</a>
        </div>
    </div>

</body>

</html>