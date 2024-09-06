<?php
session_start();
if ($_SESSION['role'] != 'superadmin') {
    header('Location: login.php');
    exit();
}
$role = $_SESSION['role'];

include('config.php');

// Set the number of items per page
$items_per_page = 10;

// Get the current page number from the query string, defaulting to page 1 if not set
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

// Calculate the offset for the query
$offset = ($current_page - 1) * $items_per_page;

// Query to get the total number of records
$total_query = "
    SELECT COUNT(*) as total 
    FROM absensi 
    JOIN users ON absensi.user_id = users.id
";
$total_result = $connection->query($total_query);
$total_row = $total_result->fetch_assoc();
$total_items = $total_row['total'];

// Query to get the data for the current page
$query = "
    SELECT absensi.*, users.nama_lengkap, users.role 
    FROM absensi 
    JOIN users ON absensi.user_id = users.id
    LIMIT $offset, $items_per_page
";
$result = $connection->query($query);
$absensiData = $result->fetch_all(MYSQLI_ASSOC);

// Calculate total pages
$total_pages = ceil($total_items / $items_per_page);

$no = $offset + 1;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report Absensi - Absensi QR Code</title>
    <link rel="icon" type="image" href="img/LogoNRW.png">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body>
    <?php include 'layout/navbar.php'; ?>
    <div class="container mt-5">
        <h2>Report Absensi</h2>
        <a href="export_excel.php" class="btn btn-success mt-3 mb-3">Export ke Excel</a>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>No</th>
                    <th>User ID</th>
                    <th>Nama Lengkap</th>
                    <th>Jabatan</th>
                    <th>Status</th>
                    <th>Waktu Masuk</th>
                    <th>Waktu Keluar</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($absensiData) > 0): ?>
                    <?php foreach ($absensiData as $data): ?>
                    <tr>
                        <td><?php echo $no++; ?></td>
                        <td><?php echo htmlspecialchars($data['user_id']); ?></td>
                        <td><?php echo htmlspecialchars($data['nama_lengkap']); ?></td>
                        <td><?php echo htmlspecialchars($data['role']); ?></td>
                        <td><?php echo htmlspecialchars($data['status']); ?></td>
                        <td><?php echo htmlspecialchars($data['waktu_masuk']); ?></td>
                        <td><?php echo htmlspecialchars($data['waktu_keluar']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center">No records found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Pagination -->
        <nav aria-label="Page navigation">
            <ul class="pagination justify-content-center">
                <li class="page-item <?php if ($current_page <= 1) echo 'disabled'; ?>">
                    <a class="page-link" href="?page=<?php echo $current_page - 1; ?>" aria-label="Previous">
                        <span aria-hidden="true">&laquo;</span>
                    </a>
                </li>
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <li class="page-item <?php if ($current_page == $i) echo 'active'; ?>">
                    <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                </li>
                <?php endfor; ?>
                <li class="page-item <?php if ($current_page >= $total_pages) echo 'disabled'; ?>">
                    <a class="page-link" href="?page=<?php echo $current_page + 1; ?>" aria-label="Next">
                        <span aria-hidden="true">&raquo;</span>
                    </a>
                </li>
            </ul>
        </nav>
    </div>

    <?php include 'layout/script.php'; ?>
</body>
</html>
