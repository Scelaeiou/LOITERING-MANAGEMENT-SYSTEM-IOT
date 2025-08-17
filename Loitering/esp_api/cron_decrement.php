<?php
$conn = new mysqli("localhost", "root", "", "rfid_db");
$conn->query("UPDATE timer SET time_left = GREATEST(0, time_left - 1)");
$conn->close();
?>
