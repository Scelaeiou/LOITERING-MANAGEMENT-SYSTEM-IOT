<?php
$conn = new mysqli("localhost", "root", "", "rfid_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle delete action
if (isset($_GET['delete_id'])) {
    $deleteId = intval($_GET['delete_id']);
    $conn->query("DELETE FROM history WHERE id = $deleteId");
    header("Location: history.php");
    exit;
}
if (isset($_POST['delete_all'])) {
    $conn->query("DELETE FROM history");
    header("Location: history.php");
    exit;
}

// Filters
$day = $_GET['day'] ?? '';
$uid = $_GET['uid'] ?? '';

$sql = "SELECT * FROM history WHERE 1";
if ($day) {
    $sql .= " AND day = '" . $conn->real_escape_string($day) . "'";
}
if ($uid) {
    $sql .= " AND uid = '" . $conn->real_escape_string($uid) . "'";
}
$sql .= " ORDER BY id DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>RFID History</title>
  <style>/* Container and Layout */
.container {
    display: flex;
    min-height: 100vh;
    background-color: #f1f5f9;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

/* Sidebar */
.sidebar {
    width: 220px;
    background-color:rgb(131, 9, 9);
    color: white;
    padding: 20px;
    display: flex;
    flex-direction: column;
}

.logo {
    font-size: 1.6rem;
    font-weight: bold;
    margin-bottom: 30px;
    text-align: center;
}

.nav a {
    color: white;
    text-decoration: none;
    margin: 10px 0;
    padding: 10px;
    display: block;
    border-radius: 8px;
    transition: background-color 0.2s;
}

.nav a:hover {
    background-color:rgb(223, 18, 28);
}

/* Main Area */
.main {
    flex: 1;
    padding: 40px;
    background-color: #f8fafc;
}

/* Card Style */
.card {
    background-color: white;
    padding: 30px;
    border-radius: 15px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
}

/* Heading */
.card h2 {
    margin-bottom: 25px;
    color: #1e293b;
}

/* Filter Form */
.filter-form {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    align-items: center;
    margin-bottom: 25px;
}

.filter-form label {
    font-weight: 500;
    display: flex;
    flex-direction: column;
    font-size: 14px;
}

.filter-form input {
    padding: 8px 10px;
    margin-top: 4px;
    border: 1px solid #cbd5e1;
    border-radius: 6px;
    font-size: 14px;
    background-color: #ffffff;
    color: #1e293b;
}

/* Buttons */
.btn {
    padding: 8px 16px;
    border: none;
    border-radius: 6px;
    font-size: 14px;
    cursor: pointer;
    font-weight: 500;
    transition: background-color 0.2s;
}

.btn.blue {
    background-color:rgba(246, 59, 68, 0.8);
    color: white;
}

.btn.blue:hover {
    background-color:rgb(99, 0, 0);
}

.btn.gray {
    background-color: #94a3b8;
    color: white;
}

.btn.gray:hover {
    background-color: #64748b;
}

/* Table Styling */
.history-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 15px;
    background-color: #ffffff;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}

.history-table th, .history-table td {
    padding: 14px 18px;
    text-align: center;
    border-bottom: 1px solid #e2e8f0;
    font-size: 14px;
}

.history-table th {
    background-color: #f1f5f9;
    color: #334155;
    font-weight: 600;
}

.history-table tr:hover {
    background-color: #f8fafc;
}

.history-table td {
    color: #475569;
}

/* Additional Styles for the History Table (Specific to `history.php`) */
.history-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}

.history-table th, .history-table td {
    border: 1px solid #ccc;
    padding: 8px;
    text-align: center;
}

.filter-form {
    display: flex;
    gap: 15px;
    margin-bottom: 20px;
    flex-wrap: wrap;
}

.filter-form input {
    padding: 5px;
}

.btn-group {
    display: flex;
    gap: 8px;
}
</style>
</head>
<body>
  <div class="container">
    <aside class="sidebar">
      <div class="logo">ESP32 Dashboard</div>
      <nav class="nav">
        <a href="home.php">🏠 Dashboard</a>
        <a href="control.php">🎛️ Control</a>
        <a href="register.php">ℹ️ Register Area </a>
      </nav>
    </aside>

    <main class="main">
      <section class="card">
        <h2>RFID Scan History</h2>

        <form method="GET" class="filter-form">
          <label>Day: <input type="date" name="day" value="<?= htmlspecialchars($day) ?>"></label>
          <label>UID: <input type="text" name="uid" placeholder="Enter UID" value="<?= htmlspecialchars($uid) ?>"></label>
          <button type="submit" class="btn blue">Filter</button>
          <a href="history.php" class="btn gray">Reset</a>
        </form>

        <form method="POST">
          <button type="submit" name="delete_all" class="btn red" onclick="return confirm('Delete all records?')">Delete All</button>
        </form>

        <table class="history-table">
          <thead>
            <tr>
              <th>ID</th>
              <th>UID</th>
              <th>Table</th>
              <th>Tap#</th>
              <th>Date</th>
              <th>Time</th>
              <th>Start Time</th>
              <th>End Time</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
              <tr>
                <td><?= $row['id'] ?></td>
                <td><?= htmlspecialchars($row['uid']) ?></td>
                <td><?= $row['table_id']??'—'?></td>
                <td><?= $row['tap_number'] ?? '—' ?></td>
                <td><?= $row['day'] ?></td>
                <td><?= $row['time'] ?></td>
                <td><?= $row['start_time'] ?? '—' ?></td>
                <td><?= $row['end_time'] ?? '—' ?></td>
                <td>
                  <a href="?delete_id=<?= $row['id'] ?>" class="btn red" onclick="return confirm('Delete this record?')">Delete</a>
                </td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </section>
    </main>
  </div>
</body>
</html>
