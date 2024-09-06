<?php
session_start();
if ($_SESSION['role'] != 'superadmin') {
    header('Location: login.php');
    exit();
}

include('config.php');
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=report_absensi.xls");

// Query to fetch data from absensi and join with users to get name and role
$query = "SELECT absensi.*, users.nama_lengkap AS nama_lengkap, users.role 
          FROM absensi 
          JOIN users ON absensi.user_id = users.id";
$result = $connection->query($query);
$absensiData = $result->fetch_all(MYSQLI_ASSOC);
$no = 1;
?>

<table border="1">
    <tr>
        <th>No</th>
        <th>Nama</th>
        <th>Jabatan</th>
        <th>Status</th>
        <th>Waktu Masuk</th>
        <th>Waktu Keluar</th>
    </tr>
    <?php foreach ($absensiData as $data): ?>
    <tr>
        <td><?php echo $no++; ?></td>
        <td><?php echo $data['nama_lengkap']; ?></td>
        <td><?php echo $data['role']; ?></td>
        <td><?php echo $data['status']; ?></td>
        <td><?php echo $data['waktu_masuk']; ?></td>
        <td><?php echo $data['waktu_keluar']; ?></td>
    </tr>
    <?php endforeach; ?>
</table>
