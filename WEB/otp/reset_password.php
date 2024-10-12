<?php
session_start(); // เริ่ม session

// แสดงข้อความผิดพลาดจากการลงทะเบียน
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
    <title>Reset Password</title>
    <link rel="stylesheet" href="./../assets/css/otp/reset_password.css">
    <script>
        function validatePasswords() {
            var newPassword = document.getElementById("new_password").value;
            var confirmPassword = document.getElementById("confirm_password").value;

            if (newPassword !== confirmPassword) {
                alert("Passwords do not match. Please try again.");
                return false; // Prevent form submission
            }

            return true; // Allow form submission
        }
    </script>
</head>
<body>
    <div class="container">
        <h2>ลืมรหัสผ่าน</h2>
        <form method="POST" action="update_password.php" onsubmit="return validatePasswords()">
            <input type="hidden" name="email" value="<?php echo htmlspecialchars($_GET['email']); ?>">

            <label for="new_password">รหัสผ่านใหม่</label>
            <input type="password" id="new_password" name="new_password" required>

            <label for="confirm_password">ยืนยันรหัสผ่าน</label>
            <input type="password" id="confirm_password" name="confirm_password" required>

            <button type="submit">รีเซ็ตรหัสผ่าน</button>
        </form>
    </div>
</body>
</html>
