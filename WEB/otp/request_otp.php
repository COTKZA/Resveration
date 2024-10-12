<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request OTP</title>
    <link rel="stylesheet" href="./../assets/css/otp/request_otp.css">
</head>
<body>
    <div class="container">
        <h2>ลืมรหัสผ่าน</h2>
        <form method="POST" action="send_otp.php">
            <label for="email">อีเมล์</label>
            <input type="email" name="email" required>
            <button type="submit">ส่งรหัส OTP</button>
        </form>
    </div>
</body>
</html>
