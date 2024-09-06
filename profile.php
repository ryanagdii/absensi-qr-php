<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$role = $_SESSION['role'];
$userId = $_SESSION['user_id'];
include('config.php');



// Retrieve User Data
$query = "SELECT * FROM users WHERE id = '$userId'";
$result = $connection->query($query);
$userData = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil - Absensi QR Code</title>
    <link rel="icon" type="image" href="img/LogoNRW.png">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.all.min.js"></script>
</head>
<body>
    <?php include 'layout/navbar.php'; ?>
    <div class="container mt-5">
        <h2>Profil</h2>
        <form method="POST" action="">
            <div class="form-group">
                <label for="nama_lengkap">Nama Lengkap</label>
                <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" value="<?php echo $userData['nama_lengkap']; ?>" required>
            </div>
            <div class="form-group">
                <label for="email">Username</label>
                <input type="text" class="form-control" id="username" name="username" value="<?php echo $userData['username']; ?>" required>
            </div>
            <div class="form-group">
                <label for="password">Password <small>(Kosongkan jika tidak ingin mengubah)</small></label>
                <input type="password" class="form-control" id="password" name="password">
            </div>
            <div class="form-group">
                <label for="role">Jabatan <small>(Hubungi Administrator jika ingin merubah jabatan!)</small></label>
                <input type="text" class="form-control" id="role" name="role" value="<?php echo $userData['role']; ?>" disabled>
            </div>
            <button type="submit" name="update_profile" class="btn btn-primary">Ubah</button>
        </form>
    </div>
    <?php include 'layout/script.php'; ?>
</body>
</html>

<?php
    // Handle Update Request
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
    $newNamaLengkap = $connection->real_escape_string($_POST['nama_lengkap']);
    $newUsername = $connection->real_escape_string($_POST['username']);
    $newPassword = !empty($_POST['password']) ? md5($_POST['password']) : null;

    $query = "UPDATE users SET nama_lengkap = '$newNamaLengkap', username = '$newUsername'";

    if ($newPassword) {
        $query .= ", password = '$newPassword'";
    }

    $query .= " WHERE id = '$userId'";

    if ($connection->query($query)) {
        echo '<script>
            Swal.fire({
                icon: "success",
                title: "Profil Diperbarui",
                text: "Data profil berhasil diperbarui!"
            }).then(function() {
                window.location = "profile.php";
            });
        </script>';
    } else {
        echo '<script>
            Swal.fire({
                icon: "error",
                title: "Error",
                text: "Terjadi kesalahan saat memperbarui data: ' . $connection->error . '"
            });
        </script>';
    }
}

?>
