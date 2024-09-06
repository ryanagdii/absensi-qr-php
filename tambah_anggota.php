<?php
session_start();
if ($_SESSION['role'] != 'superadmin') {
    header('Location: login.php');
    exit();
}

include('config.php');

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
    <title>Tambah Anggota - Absensi QR Code</title>
    <link rel="icon" type="image" href="img/LogoNRW.png">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.min.css">
    <link href="css/style.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.all.min.js"></script>
</head>
<body>
    <div class="container mt-5">
        <h2>Tambah Anggota</h2>
        <form method="POST">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="nama_lengkap">Nama Lengkap</label>
                <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" required>
            </div>
            <div class="form-group">
                <label for="jabatan">Jabatan</label>
                <select class="form-control" id="jabatan" name="jabatan" required>
                    <?php foreach ($jabatan as $jb): ?>
                        <option value="<?php echo $jb['nama']; ?>">
                            <?php echo $jb['nama']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Tambah Anggota</button>
            <a href="data_anggota.php" class="btn btn-danger">Cancel</a>
        </form>
    </div>

</body>
</html>

<?php
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $connection->real_escape_string($_POST['username']);
    $password = md5($connection->real_escape_string($_POST['password']));
    $nama_lengkap = $connection->real_escape_string($_POST['nama_lengkap']);
    $role = $connection->real_escape_string($_POST['jabatan']);
    
    $query = "INSERT INTO users (username, password, nama_lengkap, role) VALUES ('$username', '$password', '$nama_lengkap', '$role')";
    if ($connection->query($query)) {
        echo '<script>
            Swal.fire({
                icon: "success",
                title: "Anggota Ditambahkan",
                text: "Anggota baru berhasil ditambahkan!"
            }).then(function() {
                window.location = "data_anggota.php";
            });
        </script>';
    } else {
        echo '<script>
            Swal.fire({
                icon: "error",
                title: "Gagal Menambahkan Anggota",
                text: "Terjadi kesalahan saat menambahkan anggota!"
            });
        </script>';
    }
}

?>
