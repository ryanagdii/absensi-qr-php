<?php
// Database configuration

setlocale(LC_TIME, 'id_ID.utf8');
$host = 'localhost';
$dbname = 'absensi_db';
$username = 'root';
$password = '';
$connection = new mysqli($host, $username, $password, $dbname);

if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}
?>
