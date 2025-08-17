<?php
// Set timezone
date_default_timezone_set('Asia/Manila');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "rfid_db";

// Create DB connection
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query to retrieve timers with last_seen
$query = "SELECT table_id, start_time, end_time, last_seen FROM timers";
$result = $conn->query($query);

$timers = [];

while ($row = $result->fetch_assoc()) {
    $timers[$row['table_id']] = [
        'start_time' => $row['start_time'] ? strtotime($row['start_time']) : null,
        'end_time'   => $row['end_time'] ? strtotime($row['end_time']) : null,
        'last_seen'  => $row['last_seen'] ? strtotime($row['last_seen']) : null
    ];
}

$serverTime = time();

header('Content-Type: application/json');
echo json_encode([
    'timers' => $timers,
    'server_time' => $serverTime
]);

$conn->close();
