<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$role = $_SESSION['role'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Absensi QR Code</title>
    <link rel="icon" type="image" href="img/LogoNRW.png">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.min.css">
    <link href="css/style.css" rel="stylesheet">
</head>
<body>
    <?php include 'layout/navbar.php'; ?>

    <div class="container mt-5">
        <h3>Selamat datang di dashboard!</h3>
    </div>

    <?php include 'layout/script.php'; ?>
</body>
</html>
