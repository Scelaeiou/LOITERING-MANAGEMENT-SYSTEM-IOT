<?php
date_default_timezone_set('Asia/Manila');

// DB connection
$conn = new mysqli("localhost", "root", "", "rfid_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$uid = $_GET['uid'] ?? null;
$table = $_GET['table'] ?? null;

if (!$uid || !$table) {
    echo "Missing parameters";
    exit;
}

// Check if tag is registered
$stmt = $conn->prepare("SELECT remaining_seconds FROM rfid_users WHERE uid = ?");
$stmt->bind_param("s", $uid);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo "Tag not registered";
    exit;
}

$row = $result->fetch_assoc();
$remaining = (int)$row['remaining_seconds'];

if ($remaining <= 0) {
    echo "No time left";
    exit;
}

// Deduct time (15 minutes = 900 seconds)
$deduct = 900;
if ($remaining < $deduct) $deduct = $remaining;
$new_remaining = $remaining - $deduct;

// Update user's remaining time
$update = $conn->prepare("UPDATE rfid_users SET remaining_seconds = ? WHERE uid = ?");
$update->bind_param("is", $new_remaining, $uid);
$update->execute();

// ✅ Now update the MySQL timers table for this table number
$table_id = (int)$table;

// Add time to the timer, depending on whether it's active
$stmt = $conn->prepare("
    UPDATE timers 
    SET end_time = CASE 
        WHEN end_time > NOW() THEN DATE_ADD(end_time, INTERVAL ? SECOND)
        ELSE DATE_ADD(NOW(), INTERVAL ? SECOND)
    END 
    WHERE table_id = ?
");
$stmt->bind_param("iii", $deduct, $deduct, $table_id);
$stmt->execute();

// --- Log to history ---
$day = date('Y-m-d');
$time = date('H:i:s');

// Get current timer times from database
$timerStmt = $conn->prepare("SELECT start_time, end_time FROM timers WHERE table_id = ?");
$timerStmt->bind_param("i", $table_id);
$timerStmt->execute();
$timerResult = $timerStmt->get_result();
$timerRow = $timerResult->fetch_assoc();
$start_time = $timerRow['start_time'];
$end_time = $timerRow['end_time'];

// Get the current tap number for this UID
$tapStmt = $conn->prepare("SELECT COUNT(*) AS tap_count FROM history WHERE uid = ?");
$tapStmt->bind_param("s", $uid);
$tapStmt->execute();
$tapResult = $tapStmt->get_result();
$tapRow = $tapResult->fetch_assoc();
$tap_number = $tapRow['tap_count'] + 1;

// Insert into history
$logStmt = $conn->prepare("INSERT INTO history (uid, table_id, tap_number, day, time, start_time, end_time) VALUES (?, ?, ?, ?, ?, ?, ?)");
$logStmt->bind_param("siissss", $uid, $table_id, $tap_number, $day, $time, $start_time, $end_time);
$logStmt->execute();

echo "Time added and Logged to History";
?>
