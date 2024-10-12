<?php
// ตั้งค่า header เพื่อระบุว่าเป็น REST API และอนุญาตให้เข้าถึงจากภายนอก
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

// รวมการตั้งค่า API และการเชื่อมต่อฐานข้อมูล
include './../../api_config.php';

// ตรวจสอบการเชื่อมต่อฐานข้อมูล
if (!$conn) {
    // ตั้งค่า HTTP response code 500 เมื่อไม่สามารถเชื่อมต่อฐานข้อมูลได้
    http_response_code(500);
    echo json_encode(array("message" => "ไม่สามารถเชื่อมต่อกับฐานข้อมูล"));
    die();
}

// ตรวจสอบ method ที่ใช้
$request_method = $_SERVER["REQUEST_METHOD"];

// ตรวจสอบว่ามีการใช้ method GET เท่านั้น
if($request_method == 'GET') {
    // เรียกใช้ฟังก์ชันในการดึงข้อมูล bus_schedule
    getOfficeList();
} else {
    // ตั้งค่า HTTP response code 405 Method Not Allowed สำหรับ method อื่นๆ
    http_response_code(405);
    echo json_encode(array("message" => "Method ไม่รองรับ"));
}

// ฟังก์ชันสำหรับดึงข้อมูลจาก bus_schedule
function getOfficeList() {
    global $conn;

    // คำสั่ง SQL สำหรับดึงข้อมูลจากตาราง bus_schedule
    $sql = "SELECT * FROM bus_schedule";
    $stmt = $conn->prepare($sql);

    // ตรวจสอบการเรียกใช้คำสั่ง SQL
    if (!$stmt->execute()) {
        // ตั้งค่า HTTP response code 500 เมื่อมีข้อผิดพลาดในการดึงข้อมูล
        http_response_code(500);
        echo json_encode(array("message" => "ข้อผิดพลาดในการเรียกใช้คำสั่ง SQL"));
        die();
    }

    // สร้าง array สำหรับเก็บข้อมูลที่ดึงมา
    $result = $stmt->get_result();
    $data = array();

    // ดึงข้อมูลแต่ละแถวจาก result set และเพิ่มลงใน array
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }

    // ตรวจสอบว่ามีข้อมูลหรือไม่
    if (empty($data)) {
        // ถ้าไม่มีข้อมูล ตั้งค่า HTTP response code 404 ไม่พบข้อมูล
        http_response_code(404);
        echo json_encode(array("message" => "ไม่พบข้อมูล"));
    } else {
        // ถ้ามีข้อมูล ส่งข้อมูลเป็น JSON พร้อมกับตั้งค่า HTTP response code 200 สำเร็จ
        http_response_code(200);
        echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    // ปิดการเชื่อมต่อฐานข้อมูล
    $stmt->close();
    $conn->close();
}
?>
