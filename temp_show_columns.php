<?php
$mysqli = mysqli_connect('localhost','root','','financetracker_bengkel');
if (!$mysqli) { echo 'connect failed: '.mysqli_connect_error(); exit(1); }
$tables = ['pelanggan','transaksi'];
foreach ($tables as $table) {
    echo "TABLE $table:\n";
    $result = mysqli_query($mysqli, "SHOW COLUMNS FROM $table");
    if (!$result) { echo 'query failed: '.mysqli_error($mysqli)."\n"; continue; }
    while ($row = mysqli_fetch_assoc($result)) {
        echo $row['Field'].' '.$row['Type'].' '.($row['Null']=='NO'?'NOT NULL':'NULL').' '.($row['Key']?($row['Key']=='PRI'?'PRIMARY':$row['Key']):'')."\n";
    }
    echo "\n";
}
