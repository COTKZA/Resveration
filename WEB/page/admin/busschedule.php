<?php
session_start();
if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'admin') {
    header("Location: /WEB/login_page.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ตารางเวลารถบัส</title>
    <link rel="stylesheet" href="./../../assets/css/user.css">
    <style>
        /* สไตล์สำหรับ Sidebar */
body {
            background-color: #ffffff; /* กำหนดพื้นหลังเป็นสีขาว */
            margin: 0; /* ลบระยะขอบ */
            font-family: Arial, sans-serif; /* ฟอนต์ */
            height: 100vh; /* กำหนดความสูงเป็น 100% ของ viewport */
            overflow-x: hidden; /* ป้องกันการเลื่อนแนวนอน */
        }
.sidebar {
            height: 100%;
            width: 300px; /* กว้างของ Sidebar */
            position: fixed; /* วาง Sidebar ติดขอบ */
            right: -350px; /* ซ่อน Sidebar อยู่ด้านขวา */
            background-color: #007bff;
            transition: 0.3s; /* ทำให้มีการเคลื่อนไหวเรียบ */
            padding: 15px;
            z-index: 1; /* ให้อยู่ด้านบนสุด */
            font-size: 36px; /* ขนาดตัวอักษร */
            font-weight: bold; /* ตัวหนา */
        }
    
.sidebar ul li {
    font-size: 36px; /* ขนาดตัวอักษรในลิสต์ */
    font-weight: bold; /* ตัวหนา */
    padding: 10px; /* เพิ่ม padding เพื่อให้ดูเด่นขึ้น */
    transition: background-color 0.3s; /* เพิ่มการเคลื่อนไหวเมื่อชี้ */
}

/* เพิ่มพื้นหลังเมื่อชี้เมาส์ */
.sidebar ul li:hover {
    background-color: #1D1B1BFF; /* เปลี่ยนสีพื้นหลังเมื่อชี้ */
    cursor: pointer; /* เปลี่ยนเป็นรูปมือเพื่อให้ผู้ใช้รู้ว่าคลิกได้ */
    color: white; /* เปลี่ยนสีข้อความให้ชัดเจน */
}

.sidebar.open {
            right: 0; /* แสดง Sidebar เมื่อเปิด */
        }

        /* ปุ่มสำหรับเปิด/ปิด Sidebar */
.toggle-btn {
            font-size: 20px;
            cursor: pointer;
            position: fixed;
            right: 10px; /* วางปุ่มที่ด้านขวา */
            top: 10px;
            z-index: 2; /* ให้อยู่ด้านบนสุด */
            background-color: #007bff; /* สีพื้นหลังของปุ่ม */
            color: white; /* สีข้อความ */
            border: none; /* ไม่มีเส้นขอบ */
            border-radius: 5px; /* มุมกลม */
            width: 40px; /* กว้างของปุ่ม */
            height: 40px; /* สูงของปุ่ม */
            display: flex; /* ใช้ flex เพื่อจัดให้กึ่งกลาง */
            align-items: center; /* จัดให้อยู่กลางแนวตั้ง */
            justify-content: center; /* จัดให้อยู่กลางแนวนอน */
        }

        /* สไตล์สำหรับ Container */
.container {
            display: flex;
            justify-content: center; /* จัดให้อยู่กลางแนวนอน */
            align-items: center; /* จัดให้อยู่กลางแนวตั้ง */
            width: 100%; /* ขยายให้เต็มความกว้าง */
            height: 100%; /* ขยายให้เต็มความสูง */
            padding: 20px; /* เพิ่ม padding เพื่อความสวยงาม */
            background-color: #ffffff; /* สีพื้นหลังของ container */
            box-shadow: none; /* ลบเงา */
        }

        /* สไตล์สำหรับตาราง */
       /* สไตล์สำหรับตาราง */
table {
    width: 100%; /* กำหนดความกว้างของตารางให้เต็มที่ */
    max-width: none; /* จำกัดความกว้างสูงสุดเพื่อป้องกันไม่ให้โตมากเกินไป */
    border-collapse: collapse; /* ร่วมขอบของตาราง */
    text-align: center; /* จัดข้อความใว้ตรงกล */
    margin: 0; /* เพิ่มระยะห่างด้านบนและจัดกลางตาราง */
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); /* เพิ่มเงาให้กับตาราง */
}

th, td {
    border: 1px solid #ddd; /* สีขอบของเซลล์ */
    padding: 20px 25px; /* เพิ่มระยะห่างในเซลล์ (top-bottom, left-right) */
    transition: background-color 0.3s; /* การเคลื่อนไหวเมื่อเปลี่ยนสีพื้นหลัง */
    text-align: center; /* จัดข้อความใว้ตรงกลาง */
}

/* กำหนดความกว้างของคอลัมน์ */
th:nth-child(1),
td:nth-child(1) {
    width: 30%; /* คอลัมน์แรกกว้าง 25% */
}

th:nth-child(2),
td:nth-child(2) {
    width: 40%; /* คอลัมน์ที่สองกว้าง 35% */
}

th:nth-child(3),
td:nth-child(3) {
    width: 15%; /* คอลัมน์ที่สามกว้าง 20% */
}

th:nth-child(4),
td:nth-child(4) {
    width: 15%; /* คอลัมน์ที่สี่กว้าง 20% */
}


th {
    background-color: #007bff; /* สีพื้นหลังของหัวตาราง */
    color: white; /* สีข้อความของหัวตาราง */
    font-weight: bold; /* ทำให้ข้อความหนา */
    text-align: center; /* จัดข้อความกลาง */
}

tr:nth-child(even) {
    background-color: #f9f9f9; /* สีพื้นหลังของแถวที่มีเลขคู่ */
}

tr:hover {
    background-color: #e0e0e0; /* สีพื้นหลังเมื่อชี้เมาส์ไปที่แถว */
    cursor: pointer; /* แสดงเคอร์เซอร์เมื่อชี้ไปที่แถว */
}

/* Responsive styles */
@media (max-width: 768px) {
    table {
        width: 100%; /* ปรับความกว้างของตารางบนหน้าจอเล็ก */
        max-width: none; /* ยกเลิกการจำกัดความกว้างสูงสุด */
    }
}


    </style>
</head>

<body>

    <!-- ปุ่มสำหรับเปิด/ปิด Sidebar -->
    <button class="toggle-btn" id="toggleBtn">&#9776;</button>

    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <h2>Admin</h2>
        <ul>
            <li><a href="admin.php">Admin information</a></li>
            <li><a href="profile.php">ข้อมูลส่วนตัว</a></li>
            <li><a href="view_seats.php">ดูรายละเอียดของผู้ใช้งาน</a></li>
            <li><a href="busschedule.php">ตารางการเดินทาง</a></li>
            <li><a href="/WEB/logout.php">logout</a></li>
        </ul>
    </div>

    <div class="container">
        <div>
            <center>
                <h2>ตารางเดินทาง</h2>
                <div id="loading" class="loading">กำลังโหลดข้อมูล...</div>
                <div id="error" class="error" style="display: none;">เกิดข้อผิดพลาดในการดึงข้อมูล</div>

                <table id="busTable" style="display:none;">
                    <thead>
                        <tr>
                            <th>รถ</th>
                            <th>เส้นทาง</th>
                            <th>ต้นทาง(เวลา)</th>
                            <th>ปลายทาง(เวลา)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- แถวข้อมูลจะถูกเพิ่มที่นี่โดย JavaScript -->
                    </tbody>
                </table>
            </center>
        </div>
    </div>

    <script>
        // ฟังก์ชันสำหรับการเปิด/ปิด Sidebar
        var sidebar = document.getElementById("sidebar");
        var toggleBtn = document.getElementById("toggleBtn");
        var container = document.querySelector(".container");

        toggleBtn.onclick = function() {
            sidebar.classList.toggle("open"); // เพิ่มหรือลบคลาส 'open'

        }
    </script>

    <script>

    // ดึงข้อมูลจาก API และแสดงในตาราง
    fetch('/API/v1/user/bus_schedule.php') // เปลี่ยน URL ให้ถูกต้อง
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            const table = document.getElementById('busTable');
            const tbody = table.querySelector('tbody');
            const loading = document.getElementById('loading');

            // ซ่อนข้อความกำลังโหลด
            loading.style.display = 'none';

            // ตรวจสอบว่ามีข้อมูลหรือไม่
            if (data.length === 0) {
                document.getElementById('error').innerText = 'ไม่พบข้อมูล';
                document.getElementById('error').style.display = 'block';
                return;
            }

            // เพิ่มแถวข้อมูลลงในตาราง
            data.forEach(row => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                <td>${row.bus}</td>
                <td>${row.route}</td>
                <td>${row.origin}</td>
                <td>${row.destination}</td>
            `;
                tbody.appendChild(tr);
            });

            // แสดงตารางเมื่อข้อมูลถูกเพิ่มแล้ว
            table.style.display = 'table';
        })
        .catch(error => {
            console.error('Error fetching data:', error);
            document.getElementById('error').innerText = 'เกิดข้อผิดพลาดในการดึงข้อมูล';
            document.getElementById('error').style.display = 'block';
            document.getElementById('loading').style.display = 'none';
        });
        
    </script>

</body>

</html>