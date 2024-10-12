<?php
session_start();
include './../../../connect_db.php'; // Ensure this connects to your MySQL database

// ตรวจสอบว่า session มีข้อมูลอีเมลหรือไม่
if (!isset($_SESSION['email'])) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
    exit();
}

// ตรวจสอบว่า user มี role ถูกต้องหรือไม่
if ($_SESSION['role'] !== 'user') {
    echo json_encode(['status' => 'error', 'message' => 'Access denied']);
    exit();
}

// ดึงข้อมูลที่ส่งมาจากฟอร์ม
$first_name = isset($_POST['first_name']) ? trim($_POST['first_name']) : '';
$last_name = isset($_POST['last_name']) ? trim($_POST['last_name']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$phone_number = isset($_POST['phone_number']) ? trim($_POST['phone_number']) : '';
$employee_id = isset($_POST['employee_id']) ? trim($_POST['employee_id']) : '';
$office_name = isset($_POST['office_id']) ? trim($_POST['office_id']) : ''; // ใช้ office_name แทน office_id

// ตรวจสอบข้อมูลที่ต้องการ
if (empty($first_name) || empty($last_name) || empty($email)) {
    echo json_encode(['status' => 'error', 'message' => 'Required fields are missing']);
    exit();
}

// ป้องกันการโจมตี SQL Injection โดยใช้ prepared statements
$email = filter_var($email, FILTER_SANITIZE_EMAIL);
$phone_number = filter_var($phone_number, FILTER_SANITIZE_STRING);
$employee_id = filter_var($employee_id, FILTER_SANITIZE_STRING);
$office_name = filter_var($office_name, FILTER_SANITIZE_STRING);

// ตรวจสอบอีเมลว่าถูกต้องหรือไม่
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid email format']);
    exit();
}

// จัดการอัปโหลดภาพ
$imagePath = null;
if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
    $imageTmpName = $_FILES['profile_image']['tmp_name'];
    $imageName = basename($_FILES['profile_image']['name']);
    $uploadDir = './../../../images/'; // ปรับให้ตรงกับตำแหน่งไดเรกทอรีจริง

    // ตรวจสอบว่ามีไดเรกทอรีที่เก็บภาพหรือไม่ ถ้าไม่มีก็สร้างใหม่
    if (!is_dir($uploadDir)) {
        if (!mkdir($uploadDir, 0755, true)) {
            echo json_encode(['status' => 'error', 'message' => 'Failed to create upload directory']);
            exit();
        }
    }

    // ตรวจสอบชนิดไฟล์ที่อัปโหลด (อนุญาตเฉพาะไฟล์ jpg, jpeg, png)
    $allowedExtensions = ['jpg', 'jpeg', 'png'];
    $fileExtension = strtolower(pathinfo($imageName, PATHINFO_EXTENSION));
    if (!in_array($fileExtension, $allowedExtensions)) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid file type. Only JPG, JPEG, PNG allowed.']);
        exit();
    }

    // ตรวจสอบขนาดไฟล์ (ตัวอย่าง: จำกัดขนาดไฟล์ไม่เกิน 2MB)
    $maxFileSize = 2 * 1024 * 1024; // 2MB
    if ($_FILES['profile_image']['size'] > $maxFileSize) {
        echo json_encode(['status' => 'error', 'message' => 'File size exceeds the limit of 2MB.']);
        exit();
    }

    // สร้างชื่อไฟล์ใหม่เพื่อป้องกันการเขียนทับไฟล์ที่มีชื่อเดียวกัน
    $newFileName = uniqid('profile_', true) . '.' . $fileExtension;
    $imagePath = $uploadDir . $newFileName;

    // ย้ายไฟล์ที่อัปโหลดไปยังไดเรกทอรีที่กำหนด
    if (!move_uploaded_file($imageTmpName, $imagePath)) {
        echo json_encode(['status' => 'error', 'message' => 'Failed to upload image']);
        exit();
    }

    // เก็บพาธของรูปภาพเพื่อบันทึกในฐานข้อมูล
    // คุณอาจต้องปรับพาธให้สัมพันธ์กับโครงสร้างเว็บไซต์ของคุณ
    $imagePath = 'images/' . $newFileName;
}

// อัปเดตข้อมูลในฐานข้อมูล
// สร้างคำสั่ง SQL โดยเพิ่ม profile_image เฉพาะเมื่อมีการอัปโหลดภาพ
if ($imagePath) {
    $sql = "UPDATE accounts SET first_name = ?, last_name = ?, email = ?, phone_number = ?, employee_id = ?, office = ?, profile_image = ? WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssss", $first_name, $last_name, $email, $phone_number, $employee_id, $office_name, $imagePath, $_SESSION['email']);
} else {
    $sql = "UPDATE accounts SET first_name = ?, last_name = ?, email = ?, phone_number = ?, employee_id = ?, office = ? WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssss", $first_name, $last_name, $email, $phone_number, $employee_id, $office_name, $_SESSION['email']);
}

// Execute the statement
if (!$stmt->execute()) {
    echo json_encode(['status' => 'error', 'message' => 'Update failed: ' . $stmt->error]);
    exit();
}

// อัปเดตอีเมลในเซสชันถ้าการอัปเดตสำเร็จ
$_SESSION['email'] = $email;

// ปิดการเชื่อมต่อฐานข้อมูล
$stmt->close(); // Close the statement
$conn->close(); // Close the connection

// ส่งข้อมูลกลับเป็น JSON
echo json_encode(['status' => 'success', 'redirect' => '/WEB/page/user/profile.php']);
?>
