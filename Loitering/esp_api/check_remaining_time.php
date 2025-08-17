<?php
header('Content-Type: text/plain');
date_default_timezone_set('Asia/Manila');

$conn = new mysqli("localhost", "root", "", "rfid_db");
if ($conn->connect_error) {
    die("DB connection failed");
}

$uid = $_GET['uid'] ?? '';
if (!$uid) {
    echo "invalid_uid";
    exit;
}

$stmt = $conn->prepare("SELECT remaining_seconds FROM rfid_users WHERE uid = ?");
$stmt->bind_param("s", $uid);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    $remaining = (int)$row['remaining_seconds'];
    if ($remaining <= 0) {
        echo "no_time";
    } else {
        echo $remaining;
    }
} else {
    echo "not_registered";

}

$conn->close();
?>
