<?php
// DB Connection
$conn = new mysqli("localhost", "root", "", "rfid_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = "";
$uid = "";
$minutes = "";

// Handle form submission for registering or reloading time
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $uid = strtoupper(trim($_POST['uid']));
    $minutes = intval($_POST['minutes']);
    $secondsToAdd = $minutes * 60;

    if ($uid && $secondsToAdd > 0) {
        // Check if UID exists
        $stmt = $conn->prepare("SELECT * FROM rfid_users WHERE uid = ?");
        $stmt->bind_param("s", $uid);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Update existing user time
            $update = $conn->prepare("UPDATE rfid_users SET remaining_seconds = remaining_seconds + ? WHERE uid = ?");
            $update->bind_param("is", $secondsToAdd, $uid);
            $update->execute();
            $message = "⏫ Time reloaded for UID: $uid";
        } else {
            // Insert new user
            $insert = $conn->prepare("INSERT INTO rfid_users (uid, remaining_seconds) VALUES (?, ?)");
            $insert->bind_param("si", $uid, $secondsToAdd);
            $insert->execute();
            $message = "✅ UID: $uid registered successfully!";
        }
    } else {
        $message = "⚠️ Please enter a valid UID and time.";
    }
}

// Filter/search handling
$search = $_GET['search'] ?? "";
$search_sql = $search ? "WHERE uid LIKE '%$search%'" : "";
$result = $conn->query("SELECT * FROM rfid_users $search_sql ORDER BY id DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>RFID Card Registration & Log</title>
<style> :root { --bg-color: #f9fafb; --text-color: #333; --card-bg: #ffffff; --table-head-bg: #edf2f7; --button-bg: #3182ce; --button-hover: #2b6cb0; --message-bg: #e6fffa; --message-border: #b2f5ea; --border-color: #e2e8f0; } @media (prefers-color-scheme: dark) { :root { --bg-color: #1a202c; --text-color: #e2e8f0; --card-bg: #2d3748; --table-head-bg: #4a5568; --button-bg: #4299e1; --button-hover: #3182ce; --message-bg: #22543d; --message-border: #38a169; --border-color: #4a5568; } } body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; padding: 20px; background: var(--bg-color); color: var(--text-color); transition: background 0.3s ease, color 0.3s ease; } h2 { font-size: 28px; color: var(--button-bg); margin-bottom: 20px; } h3 { font-size: 22px; color: var(--text-color); margin-top: 40px; margin-bottom: 10px; } input, button, select { padding: 12px; margin: 6px 0; width: 100%; box-sizing: border-box; border: 1px solid var(--border-color); border-radius: 6px; font-size: 16px; background: var(--card-bg); color: var(--text-color); transition: background 0.3s ease, color 0.3s ease, border 0.3s ease; } button { background-color: var(--button-bg); color: white; font-weight: bold; border: none; cursor: pointer; transition: background-color 0.3s ease; } button:hover { background-color: var(--button-hover); } .form-section { background: var(--card-bg); padding: 20px; border-radius: 8px; box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1); margin-bottom: 30px; transition: background 0.3s ease, color 0.3s ease; } .message { margin-top: 15px; font-weight: bold; color: #38a169; background: var(--message-bg); padding: 10px; border: 1px solid var(--message-border); border-radius: 6px; animation: fadeIn 0.5s ease-in-out; } .filter { margin-top: 20px; display: flex; gap: 10px; flex-wrap: wrap; } .filter form { flex: 1 1 250px; } table { border-collapse: collapse; width: 100%; margin-top: 20px; background: var(--card-bg); box-shadow: 0 1px 3px rgba(0,0,0,0.1); border-radius: 6px; overflow: hidden; transition: background 0.3s ease; } th, td { padding: 12px; text-align: center; border-bottom: 1px solid var(--border-color); transition: background 0.3s ease; } th { background-color: var(--table-head-bg); font-weight: 600; } tr:hover { background-color: rgba(0, 0, 0, 0.05); } .back-button { margin-top: 20px; display: inline-block; padding: 10px 20px; background: var(--table-head-bg); color: var(--text-color); text-decoration: none; border-radius: 6px; font-weight: 500; transition: background-color 0.3s ease; } .back-button:hover { background: var(--border-color); } @keyframes fadeIn { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } } @media (max-width: 768px) { .filter { flex-direction: column; } table, thead, tbody, th, td, tr { display: block; } thead { display: none; } tr { margin-bottom: 10px; border: 1px solid var(--border-color); border-radius: 6px; background: var(--card-bg); } td { text-align: left; padding-left: 50%; position: relative; } td::before { content: attr(data-label); position: absolute; left: 15px; font-weight: bold; } } </style>
</head>
<body>

    <h2>📇 RFID Card Registration</h2>

    <div class="form-section">
        <form method="POST">
            <label>RFID UID:</label>
            <input type="text" name="uid" placeholder="Enter UID..." required>

            <label>Time to Add (minutes):</label>
            <input type="number" name="minutes" placeholder="e.g. 50" min="1" required>

            <button type="submit">Register / Reload Time</button>
        </form>

        <?php if ($message): ?>
            <div class="message"><?= $message ?></div>
        <?php endif; ?>
    </div>

    <div class="filter">
        <form method="GET" style="flex: 1;">
            <input type="text" name="search" placeholder="Search UID..." value="<?= htmlspecialchars($search) ?>">
            <button type="submit">Search</button>
        </form>
        <a href="home.php" class="back-button">⬅ Back to Home</a>
    </div>

    <h3>🕒 Registered RFID Users</h3>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>UID</th>
                <th>Remaining Time (seconds)</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><?= $row['uid'] ?></td>
                        <td><?= $row['remaining_seconds'] ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="3">No RFID users found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

</body>
</html>
