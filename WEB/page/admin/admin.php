<?php
session_start();
if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'admin') {
    header("Location: /WEB/login_page.php");
    exit();
}

include './../../connect_db.php'; // Include the database connection

// Retrieve user data from the database by email
$email = $_SESSION['email'];
$sql = "SELECT * FROM accounts WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email); // Bind the email parameter

// Execute the query
$stmt->execute();

// Get the result
$result = $stmt->get_result();
$user_data = $result->fetch_assoc(); // Fetch user data as an associative array

if ($user_data === null) {
    echo json_encode(['status' => 'error', 'message' => 'User not found']);
    exit();
}

// Close the database connection
$stmt->close(); // Close the statement
$conn->close(); // Close the database connection
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <title>หน้า Admin</title>
    <link rel="stylesheet" href="./../../assets/css/admin/stlye.css">
    <link rel="stylesheet" href="./../../assets/css/user/profile.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }

        .profile-container {
            max-width: 500px;
            margin: auto;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            padding: 30px;
            text-align: center;
        }

        .profile-image {
            width: 240px;
            height: 240px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid #000000FF;
            margin-bottom: 20px;
        }

        .welcome-text {
            font-size: 36px;
            margin-top: 15px;
            color: #0E2F52;
        }

        .user-info {
            font-size: 22px;
            margin: 20px 0;
            color: #555555;
        }

        .user-info strong {
            color: #007bff;
        }

        .nav-links {
            margin-top: 30px;
            display: flex;
            justify-content: center;
            gap: 20px;
        }

        .nav-links a {
            padding: 10px 15px;
            border: 2px solid #007bff;
            border-radius: 5px;
            background-color: #007bff;
            color: #ffffff;
            text-decoration: none;
            font-weight: bold;
            transition: background-color 0.3s, color 0.3s;
        }

        .nav-links a:hover {
            background-color: #0056b3;
            color: #ffffff;
        }

        /* Responsive design */
        @media (max-width: 600px) {
            .profile-container {
                width: 90%;
            }

            .welcome-text {
                font-size: 28px;
            }

            .user-info {
                font-size: 20px;
            }
        }
    </style>
</head>

<body>
<?php
        // Set the directory where profile images are stored
        $profileImageDir = './../../images/';

        // Check if the user has a profile image filename stored in the database
        if (!empty($user_data['profile_image'])) {
            // Generate the full path to the profile image
            $profileImagePath = $profileImageDir . basename($user_data['profile_image']);
            
            // Check if the file exists
            if (file_exists($profileImagePath)) {
                $imagePath = $profileImagePath;
            } else {
                // If the file doesn't exist, use the default profile image
                $imagePath = './../../assets/img/default_profile.jpg';
            }
        } else {
            // Use the default profile image if none is set
            $imagePath = './../../assets/img/default_profile.jpg';
        }
        ?>

<div class="profile-container">
    <img src="<?php echo htmlspecialchars($imagePath); ?>" alt="Profile Image" class="profile-image">
    <p class="welcome-text">Admin Info</p>
    <div class="user-info">
        <p><strong>ชื่อ:</strong> <span id="user-first-name"><?php echo htmlspecialchars($user_data['first_name']); ?></span></p>
        <p><strong>นามสกุล:</strong> <span id="user-last-name"><?php echo htmlspecialchars($user_data['last_name']); ?></span></p>
    </div>
    <div class="nav-links">
        <a href="view_seats.php">ดูรายละเอียดของผู้ใช้ในระบบ</a>
        <a href="busschedule.php">ตารางการเดินทาง</a>
    </div>
</div>

</body>
</html>
