<?php
session_start();
$_SESSION['user_id'] = 1;
error_reporting(E_ALL);
ini_set('display_errors', 1);

ob_start();
require 'CRUD/create.php';
$output = ob_get_clean();

if (strpos($output, 'error') !== false || strpos($output, 'Error') !== false || strpos($output, 'ERROR') !== false) {
    echo "Error found in output\n";
    echo substr($output, 0, 2000);
} else {
    echo "No obvious errors in output\n";
}
?>
