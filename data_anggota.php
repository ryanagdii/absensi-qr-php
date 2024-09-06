<?php
session_start();
if ($_SESSION['role'] != 'superadmin') {
    header('Location: login.php');
    exit();
}
$role = $_SESSION['role'];

include('config.php');

// Pagination settings
$recordsPerPage = 10; // Number of records to display per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1; // Current page number
$offset = ($page - 1) * $recordsPerPage; // Calculate offset

// Retrieve Data with Pagination
$query = "SELECT * FROM users LIMIT $recordsPerPage OFFSET $offset";
$result = $connection->query($query);
$anggotaData = $result->fetch_all(MYSQLI_ASSOC);

// Get total records for pagination calculation
$totalQuery = "SELECT COUNT(*) AS total FROM users";
$totalResult = $connection->query($totalQuery);
$totalRecords = $totalResult->fetch_assoc()['total'];
$totalPages = ceil($totalRecords / $recordsPerPage);


// Retrieve Data
$query = "SELECT * FROM users";
$result = $connection->query($query);
$anggotaData = $result->fetch_all(MYSQLI_ASSOC);

$no = 1;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Anggota - Absensi QR Code</title>
    <link rel="icon" type="image" href="img/LogoNRW.png">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.all.min.js"></script>
</head>
<body>
    <?php include 'layout/navbar.php'; ?>
    <div class="container mt-5">
        <h2>Data Anggota</h2>
        <a href="tambah_anggota.php" class="btn btn-primary mb-3">Tambah Anggota</a>
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Jabatan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($anggotaData as $data): ?>
                <tr>
                    <td><?php echo $no++; ?></td>
                    <td><?php echo $data['nama_lengkap']; ?></td>
                    <td><?php echo $data['role']; ?></td>
                    <td>
                        <a href="edit_anggota.php?id=<?php echo $data['id']; ?>" class="btn btn-warning">Edit</a>
                        <button class="btn btn-danger" onclick="confirmDelete(<?php echo $data['id']; ?>)">Hapus</button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Pagination Links -->
        <nav aria-label="Page navigation">
            <ul class="pagination justify-content-center">
                <li class="page-item <?php if($page <= 1) echo 'disabled'; ?>">
                    <a class="page-link" href="?page=<?php echo $page-1; ?>">Previous</a>
                </li>
                <?php for($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?php if($i == $page) echo 'active'; ?>">
                        <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                    </li>
                <?php endfor; ?>
                <li class="page-item <?php if($page >= $totalPages) echo 'disabled'; ?>">
                    <a class="page-link" href="?page=<?php echo $page+1; ?>">Next</a>
                </li>
            </ul>
        </nav>
    </div>

    <script>
    function confirmDelete(id) {
        Swal.fire({
            title: "Apakah kamu yakin?",
            text: "Data yang sudah dihapus tidak dapat dikembalikan!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
            confirmButtonText: "Ya, hapus!",
            cancelButtonText: "Batal"
        }).then((result) => {
            if (result.isConfirmed) {
                var form = document.createElement("form");
                form.method = "POST";
                form.action = "";

                var hiddenField = document.createElement("input");
                hiddenField.type = "hidden";
                hiddenField.name = "delete_id";
                hiddenField.value = id;

                form.appendChild(hiddenField);
                document.body.appendChild(form);
                form.submit();
            }
        });
    }
    </script>

    <?php include 'layout/script.php'; ?>
</body>
</html>


<?php
// Handle Delete Request
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_id'])) {
    $deleteId = $connection->real_escape_string($_POST['delete_id']);
    
    $query = "DELETE FROM users WHERE id = '$deleteId'";
    if ($connection->query($query)) {
        echo '<script>
            Swal.fire({
                icon: "success",
                title: "Data Terhapus",
                text: "Data berhasil dihapus!"
            }).then(function() {
                window.location = "data_anggota.php";
            });
        </script>';
    }
}

?>
