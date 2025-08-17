<?php
date_default_timezone_set('Asia/Manila');

$conn = new mysqli("localhost", "root", "", "rfid_db");

if ($conn->connect_error) {
    http_response_code(500);
    echo "DB error";
    exit;
}

$table_id = intval($_GET['table'] ?? 0);
if ($table_id > 0) {
    $stmt = $conn->prepare("UPDATE timers SET last_seen = NOW() WHERE table_id = ?");
    $stmt->bind_param("i", $table_id);
    $stmt->execute();
    echo "Status updated";
} else {
    echo "Invalid table ID";
}
$conn->close();
