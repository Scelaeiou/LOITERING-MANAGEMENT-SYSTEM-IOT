<?php
session_start();
include('../esp_api/user_db.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user = $_POST['username'];
    $pass = hash('sha256', $_POST['password']);

    $result = $conn->query("SELECT * FROM admins WHERE username='$user' AND password='$pass'");
    if ($result->num_rows > 0) {
        $_SESSION['admin'] = $user;
        header("Location: index.php");
        exit();
    } else {
        $error = "Invalid username or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Loitering</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <form method="POST">
        <h1>Admin Login</h1>
        <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
        <div class="input-box">
            <input type="text" name="username" required>
            <label>Username</label>
            <ion-icon name="mail-outline"></ion-icon>
        </div>
        <div class="input-box">
            <input type="password" name="password" required>
            <label>Password</label>
            <ion-icon name="lock-closed-outline"></ion-icon>
        </div>
        <div class="checkbox">
            <span>
                <input type="checkbox" id="checkbox-input" name="remember">
                <label for="checkbox-input">Remember Me</label>
            </span>
        </div>
        <input type="submit" class="submit-btn" value="Login">
    </form>

    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
</body>
</html>

