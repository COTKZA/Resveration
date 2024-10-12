<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

include './../../api_config.php';

$request_method = $_SERVER["REQUEST_METHOD"];

switch ($request_method) {
    case 'GET':
        if (isset($_GET['id'])) {
            getReservation($conn, $_GET['id']);
        } else {
            getReservations($conn);
        }
        break;
    case 'POST':
        createReservation($conn);
        break;
    case 'PUT':
        updateReservation($conn, $_GET['id']);
        break;
    case 'DELETE':
        deleteReservation($conn, $_GET['id']);
        break;
    default:
        http_response_code(405);
        echo json_encode(array("message" => "Method ไม่รองรับ"));
}

function getReservations($conn) {
    $sql = "SELECT * FROM reserve_seat";
    $stmt = $conn->query($sql);
    
    $data = array();
    
    while ($row = $stmt->fetch_assoc()) {
        $data[] = $row;
    }

    if (empty($data)) {
        http_response_code(404);
        echo json_encode(array("message" => "ไม่พบข้อมูล"));
    } else {
        http_response_code(200);
        echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
}

function getReservation($conn, $id) {
    $sql = "SELECT * FROM reserve_seat WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $reservation = $result->fetch_assoc();
    
    if ($reservation) {
        echo json_encode($reservation, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    } else {
        http_response_code(404);
        echo json_encode(array("message" => "ไม่พบข้อมูล"));
    }
}

function createReservation($conn) {
    $data = json_decode(file_get_contents("php://input"), true);
    $sql = "INSERT INTO reserve_seat (first_name, last_name, email, travel_datetime, travel_time, travel_type, route, seats, travel_reason, phone_number, office, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssssssss",
        $data['first_name'],
        $data['last_name'],
        $data['email'],
        $data['travel_datetime'],
        $data['travel_time'],
        $data['travel_type'],
        $data['route'],
        $data['seats'],
        $data['travel_reason'],
        $data['phone_number'],
        $data['office'],
        $data['status']
    );

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'id' => $conn->insert_id]);
    } else {
        echo json_encode(['status' => 'error']);
    }
}

function updateReservation($conn, $id) {
    $data = json_decode(file_get_contents("php://input"), true);
    $sql = "UPDATE reserve_seat SET first_name = ?, last_name = ?, email = ?, travel_datetime = ?, travel_time = ?, travel_type = ?, route = ?, seats = ?, travel_reason = ?, phone_number = ?, office = ?, status = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssssssssi",
        $data['first_name'],
        $data['last_name'],
        $data['email'],
        $data['travel_datetime'],
        $data['travel_time'],
        $data['travel_type'],
        $data['route'],
        $data['seats'],
        $data['travel_reason'],
        $data['phone_number'],
        $data['office'],
        $data['status'],
        $id
    );

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error']);
    }
}

function deleteReservation($conn, $id) {
    $sql = "DELETE FROM reserve_seat WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success']);
    } else {
        http_response_code(404);
        echo json_encode(array("message" => "ไม่พบข้อมูล"));
    }
}
?>
