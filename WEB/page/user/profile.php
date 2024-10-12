<?php
session_start();
include './../../connect_db.php';

// ตรวจสอบว่า session มีข้อมูลอีเมลหรือไม่
if (!isset($_SESSION['email'])) {
    header("Location: /WEB/login_page.php");
    exit();
}

// ตรวจสอบว่า user มี role ถูกต้องหรือไม่
if ($_SESSION['role'] !== 'user') {
    echo json_encode(['status' => 'error', 'message' => 'Access denied']);
    exit();
}

// ดึงข้อมูลผู้ใช้จากฐานข้อมูลตามอีเมล
$email = $_SESSION['email'];
$sql = "SELECT * FROM accounts WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo json_encode(['status' => 'error', 'message' => 'User not found']);
    exit();
}

$user_data = $result->fetch_assoc();

// Query to get the count of new reservations (where is_new = 1)
$sql_new_seats = "SELECT COUNT(*) as new_count FROM reserve_seat WHERE email = ? AND is_new = 1";
$stmt_new_seats = $conn->prepare($sql_new_seats);
$stmt_new_seats->bind_param("s", $email);
$stmt_new_seats->execute();
$result_new_seats = $stmt_new_seats->get_result();

$new_seat_count = 0;
if ($row = $result_new_seats->fetch_assoc()) {
    $new_seat_count = $row['new_count'];
}

// ปิดการเชื่อมต่อฐานข้อมูล
$stmt_new_seats->close();
$conn->close();
?>

<?php
include './../../connect_db.php';
// Retrieve office list from the database
$officeListQuery = "SELECT id, office_name FROM office_list";
$officeResult = $conn->query($officeListQuery);

if (!$officeResult) {
    die("Error: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <title>ข้อมูลส่วนตัว</title>
    <link rel="stylesheet" href="./../../assets/css/user/profile.css">
    <link rel="stylesheet" href="./../../assets/css/user.css">
</head>

<body>
    <!-- Sidebar -->
    <div class="sidebar">
    <h2>user</h2>
    <ul>
        <li><a href="profile.php">ข้อมูลส่วนตัว</a></li>
        <li><a href="seat_system.php">จองที่นั่ง</a></li>
        <li><a href="reservationlist.php">
            รายการสำรองที่นั่ง
            <?php if ($new_seat_count > 0): ?>
                <span class="notification-badge"><?php echo $new_seat_count; ?></span>
            <?php endif; ?>
        </a></li>
        <li><a href="busschedule.php">ตารางการเดินรถ</a></li>
        <li><a href="/WEB/logout.php">logout</a></li>
    </ul>
</div>

    <div class="main-content">
        <h1>ข้อมูลส่วนตัว</h1>

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

        <!-- Add an image element to display the profile image -->
        <div id="user-info">
            <img src="<?php echo htmlspecialchars($imagePath); ?>" alt="Profile Image" class="profile-image">
            <p><strong>ชื่อ:</strong> <span
                    id="user-first-name"><?php echo htmlspecialchars($user_data['first_name']); ?></span></p>
            <p><strong>นามสกุล:</strong> <span
                    id="user-last-name"><?php echo htmlspecialchars($user_data['last_name']); ?></span></p>
            <p><strong>อีเมล:</strong> <span id="user-email"><?php echo htmlspecialchars($user_data['email']); ?></span>
            </p>
            <p><strong>เบอร์โทร:</strong> <span
                    id="user-phone-number"><?php echo htmlspecialchars($user_data['phone_number']); ?></span></p>
            <div class="info-row">
                <div class="left-side">
                    <p><strong>รหัสพนักงาน:</strong> <span
                            id="user-employee_id"><?php echo htmlspecialchars($user_data['employee_id']); ?></span></p>
                </div>
                <div class="right-side">
                    <p><strong>สำนักงาน หรือหน่วยงานที่ท่านสังกัด:</strong> <span
                            id="user-office"><?php echo htmlspecialchars($user_data['office']); ?></span></p>
                </div>
            </div>
            <button id="edit-button">แก้ไขข้อมูล</button>
        </div>

        <!-- Popup for editing profile -->
        <div class="popup" id="edit-popup">
            <h2>แก้ไขข้อมูลส่วนตัว</h2>
            <form id="profile-form" action="./php/update_profile.php" method="post" enctype="multipart/form-data">
                <label for="profile_image">รูปโปรไฟล์:</label>
                <input type="file" id="profile_image" name="profile_image" accept="image/*">
                <br>
                <label for="first_name">ชื่อ:</label>
                <input type="text" id="first_name" name="first_name"
                    value="<?php echo htmlspecialchars($user_data['first_name']); ?>" required
                    autocomplete="given-name">
                <br>
                <label for="last_name">นามสกุล:</label>
                <input type="text" id="last_name" name="last_name"
                    value="<?php echo htmlspecialchars($user_data['last_name']); ?>" required
                    autocomplete="family-name">
                <br>
                <label for="email">อีเมล:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user_data['email']); ?>"
                    required autocomplete="email">
                <br>
                <label for="phone_number">เบอร์โทร:</label>
                <input type="text" id="phone_number" name="phone_number"
                    value="<?php echo htmlspecialchars($user_data['phone_number']); ?>" autocomplete="tel"
                    maxlength="10" pattern="\d{10}" title="Please enter a 10-digit phone number">
                <br>
                <label for="employee_id">รหัสพนักงาน:</label>
                <input type="text" id="employee_id" name="employee_id"
                    value="<?php echo htmlspecialchars($user_data['employee_id']); ?>"
                    autocomplete="organization-title">
                <br>
                <label for="office">สำนักงาน หรือหน่วยงานที่ท่านสังกัด:</label>
                <select id="office_id" name="office_id" autocomplete="organization" size="1" onclick="expandOptions()">
                    <option value="">Select Office</option>
                    <?php
                    // Assuming $officeResult is fetched from MySQL like this:
                    $officeListQuery = "SELECT id, office_name FROM office_list";
                    $officeResult = $conn->query($officeListQuery);

                    // Check if the query was successful
                    if (!$officeResult) {
                        die("Error: " . $conn->error);
                    }

                    // Loop through the office options
                    while ($row = $officeResult->fetch_assoc()) {
                        $selected = ($row['office_name'] == $user_data['office']) ? 'selected' : '';
                        echo '<option value="' . htmlspecialchars($row['office_name']) . '" ' . $selected . '>' . htmlspecialchars($row['office_name']) . '</option>';
                    }
                    ?>
                </select>

                <br>
                <button type="submit">บันทึก</button>
                <button type="button" id="close-popup">ปิด</button>
            </form>
        </div>

        <div class="popup-overlay" id="popup-overlay"></div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('profile-form');

        form.addEventListener('submit', function(e) {
            e.preventDefault(); // ป้องกันการส่งฟอร์มแบบปกติ

            const formData = new FormData(form);

            fetch('./php/update_profile.php', { // เปลี่ยนเป็นเส้นทางของไฟล์ไปที่เซิร์ฟเวอร์
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        // ปรับปรุงข้อมูลในหน้าจอ
                        document.getElementById('user-first-name').textContent = formData.get(
                            'first_name');
                        document.getElementById('user-last-name').textContent = formData.get(
                            'last_name');
                        document.getElementById('user-email').textContent = formData.get('email');
                        document.getElementById('user-phone-number').textContent = formData.get(
                            'phone_number');
                        document.getElementById('user-employee_id').textContent = formData.get(
                            'employee_id');
                        document.getElementById('user-office').textContent = formData.get(
                            'office_id');

                        // Update profile image if changed
                        const imageInput = document.getElementById('profile_image');
                        if (imageInput.files.length > 0) {
                            const reader = new FileReader();
                            reader.onload = function(e) {
                                document.querySelector('#user-info img').src = e.target.result;
                            }
                            reader.readAsDataURL(imageInput.files[0]);
                        }

                        // ปิด popup
                        document.getElementById('edit-popup').classList.remove('active');
                        document.getElementById('popup-overlay').classList.remove('active');

                        alert('ข้อมูลถูกอัปเดตเรียบร้อยแล้ว');
                    } else {
                        alert('เกิดข้อผิดพลาด: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        });

        const editButton = document.getElementById('edit-button');
        const closeButton = document.getElementById('close-popup');
        const overlay = document.getElementById('popup-overlay');

        editButton.addEventListener('click', function() {
            document.getElementById('edit-popup').classList.add('active');
            overlay.classList.add('active');
        });

        closeButton.addEventListener('click', function() {
            document.getElementById('edit-popup').classList.remove('active');
            overlay.classList.remove('active');
        });

        overlay.addEventListener('click', function() {
            document.getElementById('edit-popup').classList.remove('active');
            overlay.classList.remove('active');
        });
    });
    </script>

    <script>
    // กำหนดขนาดไฟล์สูงสุดเป็น 2MB (2 * 1024 * 1024 bytes)
    const maxFileSize = 2 * 1024 * 1024;

    // จับเหตุการณ์เมื่อมีการเลือกไฟล์
    document.getElementById('profile_image').addEventListener('change', function() {
        const file = this.files[0];

        // ตรวจสอบว่ามีไฟล์ถูกเลือก และขนาดไฟล์เกินกำหนดหรือไม่
        if (file && file.size > maxFileSize) {
            alert('ขนาดไฟล์เกินกำหนด (2MB). กรุณาเลือกไฟล์ที่มีขนาดเล็กกว่านี้.');
            // ล้างค่าของ input file เพื่อป้องกันการส่งไฟล์ที่เกินขนาด
            this.value = '';
        }
    });
    </script>

    <script>
    const selectElement = document.getElementById('office_id');

    function expandOptions() {
        // ขยายตัวเลือกเมื่อคลิก
        selectElement.size = 5;
    }

    selectElement.addEventListener('focus', expandOptions);

    selectElement.addEventListener('change', function() {
        // เมื่อเลือกตัวเลือก ลดขนาดกลับไปแสดงรายการเดียว
        selectElement.size = 1;
    });

    selectElement.addEventListener('dblclick', function() {
        // ลดขนาดกลับเมื่อดับเบิ้ลคลิก
        selectElement.size = 1;
    });

    // ลดขนาดกลับเมื่อคลิกนอก select
    document.addEventListener('click', function(event) {
        if (!selectElement.contains(event.target)) {
            selectElement.size = 1;
        }
    });
    </script>
</body>

</html>