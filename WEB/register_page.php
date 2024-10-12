<?php
session_start(); // เริ่ม session

// แสดงข้อความผิดพลาดจากการลงทะเบียน
if (isset($_SESSION['error_message'])) {
    echo "<script>alert('" . $_SESSION['error_message'] . "');</script>";
    unset($_SESSION['error_message']); // ลบข้อความหลังจากแสดงแล้ว
}

if (isset($_SESSION['email_use'])) {
    echo "<script>alert('" . $_SESSION['email_use'] . "');</script>";
    unset($_SESSION['email_use']); // ลบข้อความหลังจากแสดงแล้ว
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <link rel="stylesheet" href="./assets/css/register.css">
    <script>
    function validatePasswords() {
        var password = document.getElementById("password").value;
        var confirmPassword = document.getElementById("confirm_password").value;

        if (password !== confirmPassword) {
            alert("รหัสผ่านไม่ตรงกัน!");
            return false;
        }
        return true;
    }

    function validatePhoneNumber() {
        var phoneNumber = document.getElementById("phone_number").value;
        var phonePattern = /^\d{10}$/;

        if (!phonePattern.test(phoneNumber)) {
            alert("Phone number must be exactly 10 digits.");
            return false;
        }
        return true;
    }

    function validateForm() {
        return validatePasswords() && validatePhoneNumber();
    }
    </script>
</head>

<body>
    <div class="container">
        <h2>Register</h2>
        <form method="POST" action="register.php" onsubmit="return validatePasswords()">
            <div class="grid-item">
                <label for="first_name">ชื่อ</label>
                <input type="text" id="first_name" name="first_name" required>

                <label for="last_name">นามสกุล</label>
                <input type="text" id="last_name" name="last_name" required>

                <label for="email">อีเมล์</label>
                <input type="email" id="email" name="email" required>

                <label for="password">รหัสผ่าน</label>
                <input type="password" id="password" name="password" required>

                <label for="confirm_password">ยืนยันรหัสผ่าน</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>
            <div class="grid-item">
                <label for="phone_number">เบอร์โทร</label>
                <input type="text" id="phone_number" name="phone_number" required maxlength="10" pattern="\d{10}"
                    title="Phone number must be exactly 10 digits.">

                <label for="employee_id">รหัสพนักงาน</label>
                <input type="text" id="employee_id" name="employee_id" required>

                <label for="office">สำนังาน หรือ หน่วยงานที่ท่านสังกัด</label>
                <select id="office_id" name="office_id" required>
                    <option value="">เลือกสำนักงานหรือหน่วยงาน</option>
                    <!-- Add office options here -->
                </select>
            </div>

            <input type="submit" value="Register">
        </form>
    </div>

    <script>
    // Function to fetch office list from the API and populate the dropdown
    async function loadOfficeList() {
        try {
            const response = await fetch('/API/v1/user/office_list.php');
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            const data = await response.json();

            const officeSelect = document.getElementById('office_id');

            // Remove existing options
            officeSelect.innerHTML = '<option value="">เลือกสำนักงานหรือหน่วยงาน</option>';

            // Add new options from API data
            data.forEach(office => {
                const option = document.createElement('option');
                option.value = office.id;
                option.textContent = office.office_name;
                officeSelect.appendChild(option);
            });
        } catch (error) {
            console.error('Error fetching office list:', error);
        }
    }

    // Call loadOfficeList function on page load
    window.onload = loadOfficeList;
    </script>
</body>

</html>