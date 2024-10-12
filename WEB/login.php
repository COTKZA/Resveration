<?php
include 'connect_db.php';

session_start(); // เริ่ม session ที่นี่

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // ตรวจสอบว่าผู้ใช้มีอยู่หรือไม่
    $sql = "SELECT * FROM accounts WHERE email = ?";
    
    // Prepare the statement for MySQL
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);  // "s" stands for string
    
    // Execute the query
    $stmt->execute();
    
    // Get the result
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Fetch user data
        $row = $result->fetch_assoc();

        // ตรวจสอบรหัสผ่าน (you should hash passwords for security, plain text is not recommended)
        if ($password === $row['password']) {
            // บันทึกข้อมูลผู้ใช้ใน session
            $_SESSION['email'] = $row['email'];
            $_SESSION['role'] = $row['role'];

            // ตรวจสอบ role และเปลี่ยนเส้นทางไปยังหน้าที่เหมาะสม
            if ($row['role'] === 'admin') {
                header("Location: /WEB/page/admin/admin.php");
                exit();
            } elseif ($row['role'] === 'user') {
                header("Location: /WEB/page/user/user.php");
                exit();
            } elseif ($row['role'] === 'driver') {
                header("Location: /WEB/page/driver/driver.php");
                exit();
            } else {
                $_SESSION['error_message'] = 'Invalid role';
                header("Location: login_page.php");
                exit();
            }
        } else {
            $_SESSION['error_message'] = 'รหัสผ่านไม่ถูกต้องหรืออีเมล์ไม่ถูกต้อง';
            header("Location: login_page.php");
            exit();
        }
    } else {
        $_SESSION['error_message'] = 'ไม่พบผู้ใช้';
        header("Location: login_page.php");
        exit();
    }

    // Close the statement
    $stmt->close();
}
?>
