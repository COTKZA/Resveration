<?php
session_start();
session_unset(); // ลบค่าทุกตัวแปร session
session_destroy(); // ทำลาย session
header('Location: login_page.php?message=logout_successful'); // Redirect ไปยังหน้า login
exit();
?>
