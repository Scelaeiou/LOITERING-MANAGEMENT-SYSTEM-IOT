<?php
header('Content-Type: text/plain');
date_default_timezone_set('Asia/Manila');

$conn = new mysqli("localhost", "root", "", "rfid_db");
if ($conn->connect_error) {
    die("0"); // 0 seconds left on error
}

$table = isset($_GET['table']) ? intval($_GET['table']) : 0;
if ($table < 1 || $table > 6) {
    echo "0"; exit;
}

$stmt = $conn->prepare("SELECT end_time FROM timers WHERE table_id = ?");
$stmt->bind_param("i", $table);
$stmt->execute();
$stmt->bind_result($end_time_str);
if (!$stmt->fetch()) {
    echo "0"; // no timer => expired
    $stmt->close();
    $conn->close();
    exit;
}
$stmt->close();

$end_time = strtotime($end_time_str);
$now = time();
$remaining = max(0, $end_time - $now);

echo $remaining;

$conn->close();
?>
