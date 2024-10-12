<?php
session_start();
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

// Include your database connection
include './../../api_config.php'; // Adjust the path as needed

// Check database connection
if (!$conn) {
    http_response_code(500);
    echo json_encode(array("message" => "ไม่สามารถเชื่อมต่อกับฐานข้อมูล")); // Database connection error
    die();
}

// Handle request method
$request_method = $_SERVER["REQUEST_METHOD"];

if ($request_method == 'GET') {
    getReservations(); // Function to handle GET requests
} elseif ($request_method == 'POST') {
    updateReservationStatus(); // Function to handle POST requests
} elseif ($request_method == 'DELETE') {
    deleteReservation(); // Function to handle DELETE requests
} elseif ($request_method == 'PUT') {
    updateReservation(); // Function to handle PUT requests
} else {
    http_response_code(405);
    echo json_encode(array("message" => "Method ไม่รองรับ")); // Method not allowed message
}

// Function to fetch reservations based on start date
function getReservations() {
    global $conn;

    $start_date = isset($_GET['start_date']) ? $_GET['start_date'] : null;
    
    $sql = "SELECT * FROM reserve_seat WHERE 1=1";
    
    if ($start_date) {
        $sql .= " AND travel_date = ?";
    }

    $stmt = $conn->prepare($sql);
    
    if ($start_date) {
        $stmt->bind_param("s", $start_date);
    }

    $stmt->execute();
    $result = $stmt->get_result();
    
    $data = array();
    while ($row = $result->fetch_assoc()) {
        // Format the travel date before adding it to the response
        $row['travel_date'] = date('d-m-Y', strtotime($row['travel_date'])); // Format travel_date
        $data[] = $row;
    }

    if (empty($data)) {
        http_response_code(404);
        echo json_encode(array("message" => "ไม่พบข้อมูล")); // No data found message
    } else {
        http_response_code(200);
        echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE); // Return data as JSON
    }

    $stmt->close(); // Close the statement
    $conn->close(); // Close the database connection
}

// Function to update reservation status
function updateReservationStatus() {
    global $conn;

    $input = json_decode(file_get_contents("php://input"), true);

    if (!isset($input['id']) || !isset($input['status'])) {
        http_response_code(400);
        echo json_encode(array("message" => "ข้อมูลไม่ถูกต้อง")); // Invalid data message
        return;
    }

    $id = $input['id'];
    $status = $input['status'];

    $sql = "UPDATE reserve_seat SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $status, $id);

    if ($stmt->execute()) {
        if ($stmt->affected_rows === 0) {
            http_response_code(404);
            echo json_encode(array("message" => "ไม่พบรายการที่ต้องการอัปเดต")); // Not found message
        } else {
            http_response_code(200);
            echo json_encode(array("message" => "อัปเดตสถานะเรียบร้อยแล้ว")); // Successful update message
        }
    } else {
        http_response_code(500);
        echo json_encode(array("message" => "ข้อผิดพลาดในการอัปเดตสถานะ")); // Update status error message
    }

    $stmt->close();
    $conn->close();
}

// Function to delete a reservation
function deleteReservation() {
    global $conn;

    $input = json_decode(file_get_contents("php://input"), true);

    if (!isset($input['id'])) {
        http_response_code(400);
        echo json_encode(array("message" => "ข้อมูลไม่ถูกต้อง")); // Invalid data message
        return;
    }

    $id = $input['id'];

    $sql = "DELETE FROM reserve_seat WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        if ($stmt->affected_rows === 0) {
            http_response_code(404);
            echo json_encode(array("message" => "ไม่พบรายการที่ต้องการลบ")); // Not found message
        } else {
            http_response_code(200);
            echo json_encode(array("message" => "ลบรายการเรียบร้อยแล้ว")); // Successful delete message
        }
    } else {
        http_response_code(500);
        echo json_encode(array("message" => "ข้อผิดพลาดในการลบรายการ")); // Delete error message
    }

    $stmt->close();
    $conn->close();
}

// Function to update a reservation
function updateReservation() {
    global $conn;

    $input = json_decode(file_get_contents("php://input"), true);

    if (!isset($input['id']) || !isset($input['travel_date']) || !isset($input['first_name']) ||
        !isset($input['last_name']) || !isset($input['email']) || !isset($input['travel_type']) || 
        !isset($input['outbound_travel']) || !isset($input['return_travel']) || !isset($input['seats']) || 
        !isset($input['phone_number']) || !isset($input['office']) || !isset($input['travel_reason'])) {

        http_response_code(400);
        echo json_encode(array("message" => "ข้อมูลไม่ถูกต้อง")); // Invalid data message
        return;
    }

    $id = $input['id'];
    $travel_date = $input['travel_date'];
    $first_name = $input['first_name'];
    $last_name = $input['last_name'];
    $email = $input['email'];
    $travel_type = $input['travel_type'];
    $outbound_travel = $input['outbound_travel'];
    $return_travel = $input['return_travel'];
    $seats = $input['seats'];
    $phone_number = $input['phone_number'];
    $office = $input['office'];
    $travel_reason = $input['travel_reason'];

    $sql = "UPDATE reserve_seat 
            SET travel_date = ?, first_name = ?, last_name = ?, email = ?, 
                travel_type = ?, outbound_travel = ?, return_travel = ?, 
                seats = ?, phone_number = ?, office = ?, travel_reason = ? 
            WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssssssssi", $travel_date, $first_name, $last_name, $email, $travel_type,
                      $outbound_travel, $return_travel, $seats, $phone_number, $office, $travel_reason, $id);

    if ($stmt->execute()) {
        if ($stmt->affected_rows === 0) {
            http_response_code(404);
            echo json_encode(array("message" => "ไม่พบรายการที่ต้องการอัปเดต")); // Not found message
        } else {
            http_response_code(200);
            echo json_encode(array("message" => "อัปเดตข้อมูลเรียบร้อยแล้ว")); // Successful update message
        }
    } else {
        http_response_code(500);
        echo json_encode(array("message" => "ข้อผิดพลาดในการอัปเดตข้อมูล")); // Update error message
    }

    $stmt->close();
    $conn->close();
}
?>
