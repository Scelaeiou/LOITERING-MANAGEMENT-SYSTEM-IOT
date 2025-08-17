<?php

$filename = "latest_uid.txt";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $uid = strtoupper(trim($_POST["uid"]));
    file_put_contents($filename, $uid);
    echo "UID saved";
} else {
    if (file_exists($filename)) {
        echo trim(file_get_contents($filename));
    } else {
        echo "";
    }
}
?>