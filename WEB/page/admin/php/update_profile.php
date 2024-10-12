<?php
session_start();
include './../../../connect_db.php'; // Include database connection

// Check if session has email
if (!isset($_SESSION['email'])) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
    exit();
}

// Check if admin has the correct role
if ($_SESSION['role'] !== 'admin') {
    echo json_encode(['status' => 'error', 'message' => 'Access denied']);
    exit();
}

// Get data from the form
$first_name = isset($_POST['first_name']) ? trim($_POST['first_name']) : '';
$last_name = isset($_POST['last_name']) ? trim($_POST['last_name']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$phone_number = isset($_POST['phone_number']) ? trim($_POST['phone_number']) : '';
$employee_id = isset($_POST['employee_id']) ? trim($_POST['employee_id']) : '';
$office_name = isset($_POST['office_id']) ? trim($_POST['office_id']) : ''; // Use office_name instead of office_id

// Validate required fields
if (empty($first_name) || empty($last_name) || empty($email)) {
    echo json_encode(['status' => 'error', 'message' => 'Required fields are missing']);
    exit();
}

// Sanitize input to prevent SQL Injection
$email = filter_var($email, FILTER_SANITIZE_EMAIL);
$phone_number = filter_var($phone_number, FILTER_SANITIZE_STRING);
$employee_id = filter_var($employee_id, FILTER_SANITIZE_STRING);
$office_name = filter_var($office_name, FILTER_SANITIZE_STRING);

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid email format']);
    exit();
}

// Handle image upload
$imagePath = null;
if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
    $imageTmpName = $_FILES['profile_image']['tmp_name'];
    $imageName = basename($_FILES['profile_image']['name']);
    $uploadDir = './../../../images/'; // Adjust to match actual directory location

    // Check if the upload directory exists, if not create it
    if (!is_dir($uploadDir)) {
        if (!mkdir($uploadDir, 0755, true)) {
            echo json_encode(['status' => 'error', 'message' => 'Failed to create upload directory']);
            exit();
        }
    }

    // Validate uploaded file type (only allow jpg, jpeg, png)
    $allowedExtensions = ['jpg', 'jpeg', 'png'];
    $fileExtension = strtolower(pathinfo($imageName, PATHINFO_EXTENSION));
    if (!in_array($fileExtension, $allowedExtensions)) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid file type. Only JPG, JPEG, PNG allowed.']);
        exit();
    }

    // Check file size (example: limit file size to 2MB)
    $maxFileSize = 2 * 1024 * 1024; // 2MB
    if ($_FILES['profile_image']['size'] > $maxFileSize) {
        echo json_encode(['status' => 'error', 'message' => 'File size exceeds the limit of 2MB.']);
        exit();
    }

    // Create a new file name to prevent overwriting
    $newFileName = uniqid('profile_', true) . '.' . $fileExtension;
    $imagePath = $uploadDir . $newFileName;

    // Move uploaded file to designated directory
    if (!move_uploaded_file($imageTmpName, $imagePath)) {
        echo json_encode(['status' => 'error', 'message' => 'Failed to upload image']);
        exit();
    }

    // Store the image path to save in the database
    $imagePath = 'images/' . $newFileName;
}

// Update database information
// Create SQL statement, including profile_image only if an image was uploaded
if ($imagePath) {
    $sql = "UPDATE accounts SET first_name = ?, last_name = ?, email = ?, phone_number = ?, employee_id = ?, office = ?, profile_image = ? WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssss", $first_name, $last_name, $email, $phone_number, $employee_id, $office_name, $imagePath, $_SESSION['email']);
} else {
    $sql = "UPDATE accounts SET first_name = ?, last_name = ?, email = ?, phone_number = ?, employee_id = ?, office = ? WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssss", $first_name, $last_name, $email, $phone_number, $employee_id, $office_name, $_SESSION['email']);
}

// Execute the statement
if (!$stmt->execute()) {
    echo json_encode(['status' => 'error', 'message' => 'Update failed: ' . $stmt->error]);
    exit();
}

// Update session email if update is successful
$_SESSION['email'] = $email;

// Close the database connection
$stmt->close(); // Close statement
$conn->close(); // Close database connection

// Return response as JSON
echo json_encode(['status' => 'success', 'redirect' => '/WEB/page/user/profile.php']);
?>
