<?php
session_start();
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Methods: GET, POST');
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
        // Change to check for exact date match
        $sql .= " AND travel_date = ?";
    }

    $stmt = $conn->prepare($sql);
    
    if ($start_date) {
        $stmt->bind_param("s", $start_date);
    }

    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $data = array();
        while ($row = $result->fetch_assoc()) {
            // Format the travel date before adding it to the response
            $row['travel_date'] = date('d-m-Y', strtotime($row['travel_date']));
            $data[] = $row;
        }
        http_response_code(200);
        echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE); // Return data as JSON
    } else {
        http_response_code(404);
        echo json_encode(array("message" => "ไม่พบข้อมูล")); // No data found message
    }

    $stmt->close();
    $conn->close(); // Close the database connection
}

// Function to update reservation status
function updateReservationStatus() {
    global $conn;

    // Get the input data from the request
    $input = json_decode(file_get_contents("php://input"), true);

    // Validate the input
    if (!isset($input['id']) || !isset($input['status'])) {
        http_response_code(400);
        echo json_encode(array("message" => "ข้อมูลไม่ถูกต้อง")); // Invalid data message
        return;
    }

    // Extract the ID and status from the input
    $id = $input['id'];
    $status = $input['status'];

    // Prepare the SQL statement to update the status
    $sql = "UPDATE reserve_seat SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $status, $id);

    // Execute the query
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
?>
