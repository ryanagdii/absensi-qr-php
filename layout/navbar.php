<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <a class="navbar-brand" href="dashboard.php"><img src="./img/LogoNRW.png" width="50" heigth="50" />Dashboard</a>
    <!-- Navbar Toggler Button (Mobile) -->
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <!-- Collapsible Content -->
    <div class="collapse navbar-collapse" id="navbarNavDropdown">
        <ul class="navbar-nav ml-auto">
            <?php if ($role != 'superadmin'): ?>
            <li class="nav-item">
                <a class="nav-link" href="scan_absensi.php">Scan Absensi</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="profile.php">Profile</a>
            </li>
            <?php elseif ($role == 'superadmin'): ?>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuData" role="button"
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Data
                </a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuData">
                    <a class="dropdown-item" href="data_anggota.php">Anggota</a>
                    <a class="dropdown-item" href="data_role.php">Jabatan</a>
                    <a class="dropdown-item" href="data_qr.php">QR Code</a>
                </div>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" role="button"
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Absensi
                </a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                    <a class="dropdown-item" href="buat_absensi.php">Buat Absensi</a>
                    <a class="dropdown-item" href="data_absensi.php">Data Absensi</a>
                    <a class="dropdown-item" href="report_absensi.php">Report Absensi</a>
                </div>
            </li>
            <?php endif; ?>
            <li class="nav-item">
                <a class="nav-link" href="logout.php">Logout</a>
            </li>
        </ul>
    </div>
</nav>
