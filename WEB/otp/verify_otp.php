<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify OTP</title>
    <link rel="stylesheet" href="./../assets/css/otp/verify_otp.css">
</head>
<body>
    <div class="container">
        <h2>ลืมรหัสผ่าน</h2>
        <form method="POST" action="validate_otp.php">
            <input type="hidden" name="email" value="<?php echo htmlspecialchars($_GET['email']); ?>">

            <label for="otp">ใส่รหัส OTP</label>
            <input type="text" name="otp" required>

            <button type="submit">ยืนยัน</button>
        </form>
    </div>
</body>
</html>
