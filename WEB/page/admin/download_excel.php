<?php
session_start();
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

// Set a dynamic filename based on whether a start date is provided
$startDate = isset($_GET['start_date']) ? $_GET['start_date'] : null;
if ($startDate) {
    // Format the date for the filename (e.g., 2024-09-27 to 2024-09-27)
    $formattedDate = date('d-m-Y', strtotime($startDate));
    header('Content-Disposition: attachment; filename="reservations_' . $formattedDate . '.xlsx"');
} else {
    header('Content-Disposition: attachment; filename="reservations.xlsx"');
}

// Include PhpSpreadsheet
require './../../vendor/autoload.php'; // Adjust the path as needed

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Include your database connection
include './../../connect_db.php'; // Ensure this connects to your MySQL database

// Fetch reservations based on the start date, or all reservations if no date is provided
if ($startDate) {
    // Prepare and execute the query to fetch filtered reservations
    $sql = "SELECT * FROM reserve_seat WHERE travel_date = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $startDate); // Bind the parameter
    $stmt->execute();
    $result = $stmt->get_result(); // Get the result set
} else {
    // Fetch all reservations
    $sql = "SELECT * FROM reserve_seat";
    $result = $conn->query($sql); // Execute the query directly
}

// Check if the query execution was successful
if ($result === false) {
    die(json_encode(['status' => 'error', 'message' => 'Query execution failed: ' . $conn->error]));
}

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Reservations');

// Set the headers
$headers = [
    'Travel Date', 'First Name', 'Last Name', 'Email',
    'Travel Type', 'Outbound', 'Return', 'Seats', 'Contact', 'Office', 'Reason for Travel', 'Status'
];
$sheet->fromArray($headers, NULL, 'A1');

// Populate the data
$rowIndex = 2;
while ($row = $result->fetch_assoc()) {
    // Format the travel date
    $travelDate = !empty($row['travel_date']) ? date('d-m-Y', strtotime($row['travel_date'])) : '-'; 
    $data = [
        $travelDate, 
        $row['first_name'], 
        $row['last_name'], 
        $row['email'], 
        $row['travel_type'], 
        !empty($row['outbound_travel']) ? $row['outbound_travel'] : '-', 
        !empty($row['return_travel']) ? $row['return_travel'] : '-', 
        $row['seats'], 
        $row['phone_number'], 
        $row['office'], 
        $row['travel_reason'], 
        $row['status']
    ];
    $sheet->fromArray($data, NULL, 'A' . $rowIndex++);
}

// Save the spreadsheet and output it to the browser
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit();
?>
