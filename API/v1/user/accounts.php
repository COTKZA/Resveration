<?php
// ตั้งค่า header เพื่ออนุญาตการเข้าถึงจากภายนอกและระบุประเภทของข้อมูลที่ส่งออกเป็น JSON
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

// รวมการตั้งค่า API และการเชื่อมต่อฐานข้อมูล
include './../../api_config.php';

// ตรวจสอบการเชื่อมต่อฐานข้อมูล
if (!$conn) {
    http_response_code(500); // ตั้งค่า HTTP response เป็น 500 ถ้าเชื่อมต่อไม่ได้
    echo json_encode(array("message" => "ไม่สามารถเชื่อมต่อกับฐานข้อมูล"));
    die();
}

// ตรวจสอบ method ที่ใช้
$request_method = $_SERVER["REQUEST_METHOD"];

// สร้างฟังก์ชันสำหรับแต่ละ HTTP method
switch($request_method) {
    case 'GET':
        // ดึงข้อมูลจากฐานข้อมูล
        getAccounts();
        break;
    case 'POST':
        // เรียกใช้ฟังก์ชันสำหรับการเพิ่มข้อมูล (ยังไม่ใช้งานในตัวอย่างนี้)
        http_response_code(405); // method not allowed
        echo json_encode(array("message" => "Method POST ยังไม่ได้รับการรองรับ"));
        break;
    case 'PUT':
        // เรียกใช้ฟังก์ชันสำหรับการแก้ไขข้อมูล (ยังไม่ใช้งานในตัวอย่างนี้)
        http_response_code(405); 
        echo json_encode(array("message" => "Method PUT ยังไม่ได้รับการรองรับ"));
        break;
    case 'DELETE':
        // เรียกใช้ฟังก์ชันสำหรับการลบข้อมูล (ยังไม่ใช้งานในตัวอย่างนี้)
        http_response_code(405); 
        echo json_encode(array("message" => "Method DELETE ยังไม่ได้รับการรองรับ"));
        break;
    default:
        http_response_code(405); 
        echo json_encode(array("message" => "Method ไม่ถูกต้อง"));
        break;
}

// ฟังก์ชันสำหรับดึงข้อมูล accounts
function getAccounts() {
    global $conn;
    
    // คำสั่ง SQL สำหรับดึงข้อมูล
    $sql = "SELECT * FROM accounts";
    $stmt = $conn->prepare($sql);

    // ตรวจสอบการเรียกใช้คำสั่ง SQL
    if (!$stmt->execute()) {
        http_response_code(500); // ตั้งค่า HTTP response เป็น 500 ถ้ามีข้อผิดพลาด SQL
        echo json_encode(array("message" => "ข้อผิดพลาดในการเรียกใช้คำสั่ง SQL"));
        die();
    }

    // สร้าง array สำหรับเก็บข้อมูล
    $result = $stmt->get_result();
    $data = array();

    // ดึงข้อมูลแต่ละแถวจาก result set และเพิ่มลงใน array
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }

    if (empty($data)) {
        // ไม่มีข้อมูลในฐานข้อมูล
        http_response_code(404); // ไม่พบข้อมูล
        echo json_encode(array("message" => "ไม่พบข้อมูล"));
    } else {
        // ส่งข้อมูลเป็น JSON
        http_response_code(200); // สำเร็จ
        echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    // ปิดการเชื่อมต่อฐานข้อมูล
    $stmt->close();
    $conn->close();
}
?>
