<?php
session_start();
if ($_SESSION['role'] != 'superadmin') {
    header('Location: login.php');
    exit();
}
$role = $_SESSION['role'];
include('config.php');

require 'vendor/autoload.php';

use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Label\Label;
use Endroid\QrCode\Label\Font\NotoSans;
use Endroid\QrCode\Logo\Logo;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Writer\ValidationException;

function tanggal_indo($tanggal, $cetak_hari = false)
{
    $hari = array(
        1 => 'Senin',
        'Selasa',
        'Rabu',
        'Kamis',
        'Jumat',
        'Sabtu',
        'Minggu'
    );

    $bulan = array(
        1 => 'Januari',
        'Februari',
        'Maret',
        'April',
        'Mei',
        'Juni',
        'Juli',
        'Agustus',
        'September',
        'Oktober',
        'November',
        'Desember'
    );

    $split = explode('-', $tanggal);
    $tgl_indo = $split[2] . ' ' . $bulan[(int)$split[1]] . ' ' . $split[0];

    if ($cetak_hari) {
        $num = date('N', strtotime($tanggal));
        return $hari[$num] . ', ' . $tgl_indo;
    }
    return $tgl_indo;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tanggal = $connection->real_escape_string($_POST['tanggal']);
    $kode = rand(10000, 99999);

    $formattedDate = tanggal_indo($tanggal, true);

    $checkinData = json_encode(["type" => 1, "kode" => $kode]);
    $checkoutData = json_encode(["type" => 2, "kode" => $kode]);

    $checkinQrCode = new QrCode($checkinData);
    $checkoutQrCode = new QrCode($checkoutData);

    $writer = new PngWriter();
    $qrcodesDir = __DIR__ . "/qrcodes";
    
    if (!is_dir($qrcodesDir)) {
        mkdir($qrcodesDir, 0777, true);
    }

    $checkinQrCodeFile = "checkin_$tanggal-$kode.png";
    $checkoutQrCodeFile = "checkout_$tanggal-$kode.png";

    $checkinQrCodePath = "$qrcodesDir/$checkinQrCodeFile";
    $checkoutQrCodePath = "$qrcodesDir/$checkoutQrCodeFile";

    $logo = Logo::create(__DIR__.'/img/LogoNRW.png')
    ->setResizeToWidth(80)
    ;

    $labelCheckin = Label::create('Check-in '.$formattedDate)
    ->setTextColor(new Color(0, 0, 0))
    ->setFont(new NotoSans(13))
    ;

    $labelCheckout = Label::create('Check-out '.$formattedDate)
    ->setTextColor(new Color(0, 0, 0))
    ->setFont(new NotoSans(13))
    ;
    
    $writer->write($checkinQrCode, $logo, $labelCheckin)->saveToFile($checkinQrCodePath);
    $writer->write($checkoutQrCode, $logo, $labelCheckout)->saveToFile($checkoutQrCodePath);

    $checkinQrCodeDbPath = "qrcodes/$checkinQrCodeFile";
    $checkoutQrCodeDbPath = "qrcodes/$checkoutQrCodeFile";

    $stmt = $connection->prepare("INSERT INTO qr (kode, type, path) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $kode, $type, $path);

    $type = 1;
    $path = $checkinQrCodeDbPath;
    $stmt->execute();

    $type = 2;
    $path = $checkoutQrCodeDbPath;
    $stmt->execute();

    $stmt->close();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Attendance - QR Code</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.min.css">
    <link rel="icon" type="image" href="img/LogoNRW.png">
    <link href="css/style.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.all.min.js"></script>
</head>

<body>
    <?php include 'layout/navbar.php'; ?>
    <div class="container mt-5">
        <h2>Buat Absensi</h2>
        <form method="POST" action="">
            <div class="form-group">
                <label for="tanggal">Date</label>
                <input type="date" class="form-control" id="tanggal" name="tanggal" required>
            </div>
            <button type="submit" class="btn btn-primary">Generate QR Code</button>
        </form>

        <?php if (isset($tanggal)): ?>
        <h3 class="mt-3">QR Code Absensi</h3>
        <div class="row mt-3 mb-5">
            <div class="col-md-6 text-center">
                <!-- <h5 class="">Check-In <br><?php echo $formattedDate; ?></h5> -->
                <img src="qrcodes/checkin_<?php echo htmlspecialchars($tanggal); ?>-<?php echo $kode; ?>.png"
                    alt="Check-In QR Code" class="img-fluid">
            </div>
            <div class="col-md-6 text-center">
                <!-- <h5 class="text-center">Check-Out <br><?php echo $formattedDate; ?></h5> -->
                <img src="qrcodes/checkout_<?php echo htmlspecialchars($tanggal); ?>-<?php echo $kode; ?>.png"
                    alt="Check-Out QR Code" class="img-fluid">
            </div>
        </div>
        <?php endif; ?>
    </div>

    <?php include 'layout/script.php'; ?>
</body>

</html>
