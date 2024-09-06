<?php
session_start();
if ($_SESSION['role'] != 'superadmin') {
    header('Location: login.php');
    exit();
}

$role = $_SESSION['role'];
include('config.php');

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Jabatan - Absensi QR Code</title>
    <link rel="icon" type="image" href="img/LogoNRW.png">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.min.css">
    <link href="css/style.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.all.min.js"></script>
</head>
<body>
    <?php include 'layout/navbar.php'; ?>
    <div class="container mt-5">
        <h2>Tambah Jabatan</h2>
        <form method="POST" action="">
            <div class="form-group">
                <label for="nama_role">Nama</label>
                <input type="text" class="form-control" id="nama_role" name="nama_role" required>
            </div>
            <button type="submit" class="btn btn-primary">Tambah Jabatan</button>
            <a href="data_role.php" class="btn btn-danger">Cancel</a>
        </form>
    </div>
    <?php include 'layout/script.php'; ?>
</body>
</html>


<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $namaRole = $connection->real_escape_string($_POST['nama_role']);
    
    $query = "INSERT INTO roles (nama) VALUES ('$namaRole')";
    if ($connection->query($query)) {
        echo '<script>
            Swal.fire({
                icon: "success",
                title: "Jabatan Ditambahkan",
                text: "Jabatan baru berhasil ditambahkan!",
                timer: 1500
            }).then(function() {
                window.location = "data_role.php";
            });
        </script>';
    } else {
        echo '<script>
            Swal.fire({
                icon: "error",
                title: "Gagal Menambahkan Jabatan",
                text: "Terjadi kesalahan saat menambahkan jabatan!",
                timer: 1500
            });
        </script>';
    }
}

?>
