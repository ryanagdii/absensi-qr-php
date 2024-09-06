<?php
session_start();
if (!isset($_SESSION['role'])) {
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
    <title>Scan Absensi - QR Code</title>
    <link rel="icon" type="image" href="img/LogoNRW.png">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.min.css">
    <link href="css/style.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.all.min.js"></script>
    <script src="https://unpkg.com/html5-qrcode/html5-qrcode.min.js"></script>
</head>
<body class="">
    <?php include 'layout/navbar.php'; ?>
    <div class="container mt-5 d-flex justify-content-center align-items-center">
        <div>
            <h2 class="text-center ">Scan Absensi</h2>
            <div id="reader" style="width: 100%; height: 300px;"></div>
        </div>
        <script>
            let hasScanned = false; // Flag to prevent re-scanning

            function onScanSuccess(qrCodeMessage) {
                if (hasScanned) return; // Prevent re-scanning

                hasScanned = true; // Set flag to true

                // Create and submit form
                var form = document.createElement("form");
                form.method = "POST";
                form.action = "";

                var hiddenField = document.createElement("input");
                hiddenField.type = "hidden";
                hiddenField.name = "qr_data";
                hiddenField.value = qrCodeMessage;

                form.appendChild(hiddenField);
                document.body.appendChild(form);
                form.submit();


            }

            function onScanError(errorMessage) {
                console.log("Scan error:", errorMessage);
            }

            window.onload = function() {
                // Check URL for status
                const urlParams = new URLSearchParams(window.location.search);
                const status = urlParams.get('status');
                const action = urlParams.get('action');
                const message = urlParams.get('message');

                try {
                    var html5QrcodeScanner = new Html5QrcodeScanner(
                        "reader", { fps: 60, qrbox: 280 }, false);
                    
                    html5QrcodeScanner.render(onScanSuccess, onScanError);
                } catch (error) {
                    console.error("Error initializing QR code scanner:", error);
                    Swal.fire({
                        title: "Initialization Error",
                        text: "An error occurred while initializing the QR code scanner. Check console for details.",
                        icon: "error"
                    });
                }
            }
        </script>
    </div>

    <?php include 'layout/script.php'; ?>
</body>
</html>


<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['qr_data'])) {
    $userId = $_SESSION['user_id'];
    $qrData = $_POST['qr_data'];
    $currentDate = date('Y-m-d');

    // Decode QR data from JSON
    $decodedData = json_decode($qrData, true);

    $qrType = isset($decodedData['type']) ? $decodedData['type'] : '';
    $kode = isset($decodedData['kode']) ? $decodedData['kode'] : '';
    // Determine QR code type
    if ($decodedData['type'] == 1) {
        // Check if check-in data already exists for today
        $query = "SELECT * FROM absensi WHERE user_id = '$userId' AND qr_kode = '$kode'";
        $result = $connection->query($query);
        
        if ($result->num_rows == 0) {
            // Insert new attendance check-in
            $query = "INSERT INTO absensi (user_id, qr_kode, status, waktu_masuk) VALUES ('$userId', '$kode', 'Belum Absensi Keluar', NOW())";
            if ($connection->query($query)) {
                echo "<script>
                    Swal.fire({
                        icon: 'success',
                        title: 'Absensi Masuk Berhasil',
                        text: 'Anda Berhasil Absensi Masuk!'
                    });
                </script>";
            } else {
                echo "<script>
                    Swal.fire({
                        icon: 'error',
                        title: 'Absensi Gagal',
                        text: 'Terjadi kesalahan saat scan absensi!'
                    });
                </script>";
            }
        } else {
            echo "<script>
                    Swal.fire({
                        icon: 'info',
                        title: 'Absensi Sudah Tercatat',
                        text: 'QR Code sudah tercatat!'
                    });
                </script>";
        }
    } elseif ($decodedData['type'] == 2) {
        // Check if check-in data exists for today and is not checked out yet
        $query = "SELECT * FROM absensi WHERE user_id = '$userId' AND qr_kode = '$kode' AND waktu_keluar IS NULL";
        $result = $connection->query($query);
        
        if ($result->num_rows > 0) {
            // Update existing attendance record with check-out time
            $query = "UPDATE absensi SET waktu_keluar = NOW(), status = 'Hadir' WHERE user_id = '$userId' AND qr_kode = '$kode' AND DATE(waktu_masuk) = CURDATE() AND waktu_keluar IS NULL";
            if ($connection->query($query)) {
                echo "<script>
                    Swal.fire({
                        icon: 'success',
                        title: 'Absensi Keluar Berhasil',
                        text: 'Anda Berhasil Absensi Keluar!'
                    });
                </script>";
            } else {
                echo "<script>
                    Swal.fire({
                        icon: 'error',
                        title: 'Absensi Gagal',
                        text: 'Terjadi kesalahan saat scan absensi!'
                    });
                </script>";
            }
        } else {
            echo "<script>
                    Swal.fire({
                        icon: 'info',
                        title: 'Absensi Sudah Tercatat',
                        text: 'QR Code sudah tercatat!'
                    });
                </script>";
        }
    } else {
        echo "<script>
                    Swal.fire({
                        icon: 'error',
                        title: 'QR Code invalid',
                        text: 'QR code tidak valid atau tidak dikenali!'
                    });
                </script>";
    }
}

?>


