<?php
session_start();
if ($_SESSION['role'] != 'superadmin') {
    header('Location: login.php');
    exit();
}
$role = $_SESSION['role'];

include('config.php');



// Retrieve Data
$query = "SELECT * FROM roles";
$result = $connection->query($query);
$jabatanData = $result->fetch_all(MYSQLI_ASSOC);

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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.min.css">
    <link href="css/style.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.all.min.js"></script>
</head>
<body>
    <?php include 'layout/navbar.php'; ?>
    <div class="container mt-5">
        <h2>Data Jabatan</h2>
        <a href="tambah_jabatan.php" class="btn btn-primary mb-3">Tambah Jabatan</a>
        <table class="table table-striped table-bordered text-center">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($jabatanData as $data): ?>
                <tr>
                    <td><?php echo $no++; ?></td>
                    <td class=""><?php echo $data['nama']; ?></td>
                    <td>
                        <button class="btn btn-warning" onclick="editData(<?php echo $data['id']; ?>, '<?php echo $data['nama']; ?>')">Edit</button>
                        <button class="btn btn-danger" onclick="confirmDelete(<?php echo $data['id']; ?>)">Hapus</button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
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

    function editData(id, namaRole) {
        Swal.fire({
            title: 'Edit Data Jabatan',
            html: `
                <input id="editNamaRole" class="swal2-input" value="${namaRole}" placeholder="Nama Jabatan">
            `,
            focusConfirm: false,
            preConfirm: () => {
                const namaRole = Swal.getPopup().querySelector('#editNamaRole').value
                if (!namaRole) {
                    Swal.showValidationMessage('Nama Jabatan harus diisi')
                }
                return { namaRole: namaRole }
            },
            showCancelButton: true,
            cancelButtonColor: '#d33',
            cancelButtonText: 'Batal',
            confirmButtonText: 'Simpan'
        }).then((result) => {
            if (result.isConfirmed) {
                var form = document.createElement("form");
                form.method = "POST";
                form.action = "";

                var hiddenIdField = document.createElement("input");
                hiddenIdField.type = "hidden";
                hiddenIdField.name = "edit_id";
                hiddenIdField.value = id;

                var hiddenNameField = document.createElement("input");
                hiddenNameField.type = "hidden";
                hiddenNameField.name = "namaRole";
                hiddenNameField.value = result.value.namaRole;

                form.appendChild(hiddenIdField);
                form.appendChild(hiddenNameField);
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
    
    $query = "DELETE FROM roles WHERE id = '$deleteId'";
    if ($connection->query($query)) {
        echo '<script>
            Swal.fire({
                icon: "success",
                title: "Data Terhapus",
                text: "Data berhasil dihapus!",
                timer: 1500
            }).then(function() {
                window.location = "data_role.php";
            });
        </script>';
    }
}

// Handle Edit Request
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_id'])) {
    $editId = $connection->real_escape_string($_POST['edit_id']);
    $newNama = $connection->real_escape_string($_POST['namaRole']);
    
    $query = "UPDATE roles SET nama = '$newNama' WHERE id = '$editId'";
    if ($connection->query($query)) {
        echo '<script>
            Swal.fire({
                icon: "success",
                title: "Data Diperbarui",
                text: "Data berhasil diperbarui!",
                timer: 1500
            }).then(function() {
                window.location = "data_role.php";
            });
        </script>';
    }
}
?>
