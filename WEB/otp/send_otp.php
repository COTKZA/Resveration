<?php
// Database connection
include './../connect_db.php';

// Load Composer's autoloader if using PHPMailer
require './../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Set timezone to Thailand
date_default_timezone_set('Asia/Bangkok');

// Function to generate OTP
function generateOTP($length = 6) {
    $otp = '';
    for ($i = 0; $i < $length; $i++) {
        $otp .= rand(0, 9);
    }
    return $otp;
}

// Function to send OTP via email
function sendOTP($email, $otp) {
    $mail = new PHPMailer(true);
    
    try {
        // Server settings for Gmail SMTP
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'cotkgtasa123@gmail.com'; // Your Gmail address
        $mail->Password   = 'xchotywagrkhjiuw'; // Your Gmail password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Set charset to support Thai
        $mail->CharSet = 'UTF-8';

        // Recipients
        $mail->setFrom('cotkgtasa123@gmail.com', 'ADMIN');
        $mail->addAddress($email);
        
        // Email content
        $mail->isHTML(true);
        $mail->Subject = 'รีเซ็ตรหัสผ่าน OTP';
        $mail->Body    = "รหัส OTP ของคุณคือ: <b>$otp</b><br>โค้ดนี้จะหมดอายุใน 5 นาที";
        
        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}

// Handle OTP request
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];

    // Check if email exists
    $query = "SELECT * FROM accounts WHERE email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Generate OTP
        $otp = generateOTP();
        $expiration = date('Y-m-d H:i:s', strtotime('+5 minutes'));

        // Store OTP in the database
        $insert_query = "INSERT INTO otp_requests (email, otp, expiration) VALUES (?, ?, ?)";
        $insert_stmt = $conn->prepare($insert_query);
        $insert_stmt->bind_param("sss", $email, $otp, $expiration);
        
        if ($insert_stmt->execute()) {
            // Send OTP to user's email
            if (sendOTP($email, $otp)) {
                // Redirect to OTP verification page
                header("Location: verify_otp.php?email=" . urlencode($email));
                exit();
            } else {
                echo "Failed to send OTP. Please try again.";
            }
        } else {
            echo "Failed to store OTP in the database.";
        }
    } else {
        echo "Email not found.";
    }
}
?>
