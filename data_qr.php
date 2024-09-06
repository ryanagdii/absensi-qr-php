<?php
session_start();
if ($_SESSION['role'] != 'superadmin') {
    header('Location: login.php');
    exit();
}
$role = $_SESSION['role'];

include('config.php');

// Pagination Variables
$limit = 5; // Number of entries to show per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Retrieve Total Rows
$totalQuery = "SELECT COUNT(*) as total FROM qr";
$totalResult = $connection->query($totalQuery);
$totalRows = $totalResult->fetch_assoc()['total'];
$totalPages = ceil($totalRows / $limit);

// Retrieve Data with Pagination
$query = "SELECT * FROM qr LIMIT $limit OFFSET $offset";
$result = $connection->query($query);
$qrData = $result->fetch_all(MYSQLI_ASSOC);

$no = $offset + 1;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data QR - Absensi QR Code</title>
    <link rel="icon" type="image" href="img/LogoNRW.png">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.min.css">
    <link href="css/style.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.all.min.js"></script>
</head>
<body>
    <?php include 'layout/navbar.php'; ?>
    <div class="container mt-5">
        <h2>Data QR</h2>
        <p><b>**Note</b><br>Type: 1 adalah qr code check-in, dan Type: 2 adalah qr code check-out</p>
        <p>Klik QR untuk membuka qr</p>
        <table class="table table-striped table-bordered text-center">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kode</th>
                    <th>Type</th>
                    <th>Path</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($qrData as $qr): ?>
                <tr>
                    <td><?php echo $no++; ?></td>
                    <td><?php echo $qr['kode']; ?></td>
                    <td><?php echo $qr['type']; ?></td>
                    <td><a href="<?php echo $qr['path']; ?>" target="_blank"><img src="<?php echo $qr['path']; ?>" width="80" height="80"></a></td>
                    <td>
                        <button class="btn btn-danger" onclick="confirmDelete(<?php echo $qr['id']; ?>)">Hapus</button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Pagination Links -->
        <nav aria-label="Page navigation">
            <ul class="pagination justify-content-center">
                <?php if($page > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?php echo $page-1; ?>" aria-label="Previous">
                            <span aria-hidden="true">&laquo;</span>
                        </a>
                    </li>
                <?php endif; ?>
                <?php for($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?php if($i == $page) echo 'active'; ?>">
                        <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                    </li>
                <?php endfor; ?>
                <?php if($page < $totalPages): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?php echo $page+1; ?>" aria-label="Next">
                            <span aria-hidden="true">&raquo;</span>
                        </a>
                    </li>
                <?php endif; ?>
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
    
    $query = "DELETE FROM qr WHERE id = '$deleteId'";
    if ($connection->query($query)) {
        echo '<script>
            Swal.fire({
                icon: "success",
                title: "Data Terhapus",
                text: "Data berhasil dihapus!",
                timer: 1500
            }).then(function() {
                window.location = "data_qr.php";
            });
        </script>';
    }
}
?>
