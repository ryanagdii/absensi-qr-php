<?php
session_start();
if ($_SESSION['role'] != 'superadmin') {
    header('Location: login.php');
    exit();
}
$role = $_SESSION['role'];
include('config.php');

 // Dapatkan ID dari URL
$editId = $connection->real_escape_string($_GET['id']);

// Ambil data user berdasarkan ID
$query = "SELECT * FROM users WHERE id = '$editId'";
$result = $connection->query($query);

$data = $result->fetch_assoc();

// Ambil daftar role dari database
$roleQuery = "SELECT * FROM roles";
$roleResult = $connection->query($roleQuery);
$jabatan = $roleResult->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Anggota - Absensi QR Code</title>
    <link rel="icon" type="image" href="img/LogoNRW.png">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.all.min.js" async></script>
</head>
<body>
    <?php include 'layout/navbar.php'; ?>
    <div class="container mt-5">
        <h2>Edit Anggota</h2>
        <form method="POST">
            <div class="form-group">
                <label for="nama_lengkap">Nama Lengkap</label>
                <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" value="<?php echo $data['nama_lengkap']; ?>" required>
            </div>
            <div class="form-group">
                <label for="role">Jabatan</label>
                <select class="form-control" id="role" name="role" required>
                    <?php foreach ($jabatan as $jb): ?>
                        <option value="<?php echo $jb['nama']; ?>" <?php if ($jb['nama'] == $data['role']) echo 'selected'; ?>>
                            <?php echo $jb['nama']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="data_anggota.php" class="btn btn-secondary">Kembali</a>
        </form>
    </div>

    <?php include 'layout/script.php'; ?>
</body>
</html>

<?php

if ($result->num_rows == 0) {
    echo '<script>
        Swal.fire({
            icon: "error",
            title: "Error",
            text: "Data tidak ditemukan!"
        }).then(function() {
            window.location = "data_anggota.php";
        });
    </script>';
    exit();
}



// Tangani pembaruan data
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $newNamaLengkap = $connection->real_escape_string($_POST['nama_lengkap']);
    $newRole = $connection->real_escape_string($_POST['role']);

    $query = "UPDATE users SET nama_lengkap = '$newNamaLengkap', role = '$newRole' WHERE id = '$editId'";
    if ($connection->query($query)) {
        echo '<script>
            Swal.fire({
                icon: "success",
                title: "Data Diperbarui",
                text: "Data berhasil diperbarui!"
            }).then(function() {
                window.location = "data_anggota.php";
            });
        </script>';
    }
}
?>