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

// Get the search query from the form, if any
$search_query = isset($_GET['search']) ? $connection->real_escape_string($_GET['search']) : '';

// Modify the query to include search functionality
$where_clause = $search_query ? "WHERE absensi.user_id LIKE '%$search_query%' OR absensi.qr_data LIKE '%$search_query%' OR users.nama_lengkap LIKE '%$search_query%' OR users.role LIKE '%$search_query%'" : '';

// Query to get the total number of records matching the search query
$total_query = "SELECT COUNT(*) as total FROM absensi 
                JOIN users ON absensi.user_id = users.id 
                $where_clause";
$total_result = $connection->query($total_query);
$total_row = $total_result->fetch_assoc();
$total_items = $total_row['total'];

// Calculate the offset for the query
$offset = ($current_page - 1) * $items_per_page;

// Query to get the data for the current page and search query
$query = "SELECT absensi.*, users.nama_lengkap, users.role FROM absensi 
          JOIN users ON absensi.user_id = users.id 
          $where_clause 
          LIMIT $offset, $items_per_page";
$result = $connection->query($query);
$absensiData = $result->fetch_all(MYSQLI_ASSOC);

// Calculate total pages
$total_pages = ceil($total_items / $items_per_page);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Absensi - Absensi QR Code</title>
    <link rel="icon" type="image" href="img/LogoNRW.png">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body>
    <?php include 'layout/navbar.php'; ?>
    <div class="container mt-5">
        <h2>Data Absensi</h2>

        <!-- Search Form -->
        <form method="GET" class="form-inline mb-3">
            <input type="text" name="search" class="form-control mr-2" placeholder="Search..." value="<?php echo htmlspecialchars($search_query); ?>">
            <button type="submit" class="btn btn-primary">Search</button>
        </form>

        <!-- Display Total Records -->
        <p>Total records found: <?php echo $total_items; ?></p>

        <!-- Data Table -->
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>User ID</th>
                    <th>Nama</th>
                    <th>Jabatan</th>
                    <th>Waktu Masuk</th>
                    <th>Waktu Keluar</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($absensiData) > 0): ?>
                    <?php foreach ($absensiData as $data): ?>
                    <tr>
                        <td><?php echo $data['id']; ?></td>
                        <td><?php echo $data['user_id']; ?></td>
                        <td><?php echo $data['nama_lengkap']; ?></td>
                        <td><?php echo $data['role']; ?></td>
                        <td><?php echo $data['waktu_masuk']; ?></td>
                        <td><?php echo $data['waktu_keluar']; ?></td>
                        <td><?php echo $data['status']; ?></td>
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
                    <a class="page-link" href="?page=<?php echo $current_page - 1; ?>&search=<?php echo urlencode($search_query); ?>" aria-label="Previous">
                        <span aria-hidden="true">&laquo;</span>
                    </a>
                </li>
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <li class="page-item <?php if ($current_page == $i) echo 'active'; ?>">
                    <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search_query); ?>"><?php echo $i; ?></a>
                </li>
                <?php endfor; ?>
                <li class="page-item <?php if ($current_page >= $total_pages) echo 'disabled'; ?>">
                    <a class="page-link" href="?page=<?php echo $current_page + 1; ?>&search=<?php echo urlencode($search_query); ?>" aria-label="Next">
                        <span aria-hidden="true">&raquo;</span>
                    </a>
                </li>
            </ul>
        </nav>
    </div>

    <?php include 'layout/script.php'; ?>
</body>
</html>
