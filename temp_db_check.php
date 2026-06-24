<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'financetracker_bengkel';
$mysqli = mysqli_connect($host, $user, $pass, $db);
if (!$mysqli) {
    echo 'connect failed: ' . mysqli_connect_error();
    exit(1);
}
$result = mysqli_query($mysqli, 'SHOW COLUMNS FROM pelanggan');
if (!$result) {
    echo 'query failed: ' . mysqli_error($mysqli);
    exit(1);
}
while ($row = mysqli_fetch_assoc($result)) {
    echo $row['Field'] . ' ' . $row['Type'] . PHP_EOL;
}
