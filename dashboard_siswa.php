<?php
include "koneksi.php";

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'user') {
    header('Location: login.php');
    exit;
}

// Ambil ID siswa dari sesi
$id_siswa = $_SESSION['user']['id_siswa'];

// Query untuk mengambil detail siswa
$sql = "SELECT * FROM siswa WHERE id_siswa = ?";
$stmt = $koneksi->prepare($sql);
$stmt->bind_param("i", $id_siswa);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $siswa = $result->fetch_assoc();
    $nama_siswa = $siswa['nama'];
} else {
    $error = "Data siswa tidak ditemukan!";
    $nama_siswa = '';
}

// Query untuk mengambil riwayat transaksi siswa
$sql_transaksi = "SELECT * FROM transaksi WHERE id_siswa = ? ORDER BY tanggal ASC";
$stmt_transaksi = $koneksi->prepare($sql_transaksi);
$stmt_transaksi->bind_param("i", $id_siswa);
$stmt_transaksi->execute();
$result_transaksi = $stmt_transaksi->get_result();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Siswa | Aplikasi Tabungan Siswa</title>
    <link rel="stylesheet" href="assets/style.css">
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var namaSiswa = "<?php echo htmlspecialchars($nama_siswa); ?>";
            if (namaSiswa) {
                Swal.fire({
                    title: 'Selamat Datang!',
                    text: 'Di tabungan ' + namaSiswa,
                    icon: 'success',
                    confirmButtonText: 'OK'
                });
            }
        });
    </script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f7fa;
            color: #333;
        }

        .siswa-detail {
            background: #fff;
            padding: 15px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .profil-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 15px;
        }

        .profil-item {
            background: #e3f2fd;
            padding: 15px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
            transition: background 0.3s;
        }

        .profil-item ion-icon {
            margin-right: 10px;
            font-size: 24px;
            color: #4CAF50;
        }

        .profil-item:hover {
            background: #bbdefb;
        }

        .table-responsive {
            overflow-x: auto; /* Enable horizontal scroll */
            margin-top: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        th, td {
            padding: 8px; /* Mengurangi padding untuk membuat tabel lebih kecil */
            text-align: left;
            border-bottom: 1px solid #ddd;
            min-width: 100px; /* Lebar kolom lebih sempit */
        }

        th {
            background-color: #f7fafc;
            font-weight: bold;
            color: #2c3e50; /* Warna lebih gelap untuk judul kolom */
            font-size: 14px; /* Ukuran font lebih kecil */
        }

        td {
            color: #7f8c8d; /* Warna lebih lembut untuk teks */
            font-size: 13px; /* Ukuran font lebih kecil untuk isi tabel */
        }

        .profil-item strong {
            color: #2c3e50;
            font-size: 14px;
            font-weight: 600;
        }

        .total-transactions {
            margin-top: 20px;
            background: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .total-transactions h3 {
            font-size: 18px;
            font-weight: bold;
            color: #28a745; /* Hijau untuk "Total Transaksi" */
        }

        .total-transactions p {
            font-size: 16px;
            color: #7f8c8d;
        }

        .error {
            color: red;
            font-size: 14px;
            margin-top: 10px;
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="navigation">
            <ul>
                <li><a href="#"><span class="icon"><ion-icon name="logo-apple"></ion-icon></span><span class="title">TabungKu</span></a></li>
                <li><a href="dashboard_siswa.php" class="active"><span class="icon"><ion-icon name="wallet-outline"></ion-icon></span><span class="title">Tabungan</span></a></li>
                <li><a href="logout.php"><span class="icon"><ion-icon name="log-out-outline"></ion-icon></span><span class="title">Sign Out</span></a></li>
            </ul>
        </div>
        <div class="main">
            <div class="topbar">
                <div class="toggle">
                    <ion-icon name="menu-outline"></ion-icon>
                </div>
                <div class="search">
                    <label>
                        <input type="text" placeholder="Cari di sini">
                        <ion-icon name="search-outline"></ion-icon>
                    </label>
                </div>
                <div class="user">
                    <img src="assets/imgs/alma.jpeg" alt="">
                </div>
            </div>
            <h2>Dashboard Siswa</h2>
            <?php if (isset($error)): ?>
                <p class="error"><?php echo htmlspecialchars($error); ?></p>
            <?php endif; ?>

            <?php if (isset($siswa)): ?>
                <div class="siswa-detail">
                    <h2>Profil Siswa</h2>
                    <div class="profil-grid">
                        <div class="profil-item">
                            <ion-icon name="person-outline"></ion-icon>
                            <strong>Nama:</strong> <?php echo htmlspecialchars($siswa['nama']); ?>
                        </div>
                        <div class="profil-item">
                            <ion-icon name="calendar-outline"></ion-icon>
                            <strong>Tanggal Lahir:</strong> <?php echo htmlspecialchars($siswa['tanggal_lahir']); ?>
                        </div>
                        <div class="profil-item">
                            <ion-icon name="school-outline"></ion-icon>
                            <strong>Kelas:</strong> <?php echo htmlspecialchars($siswa['kelas']); ?>
                        </div>
                        <div class="profil-item">
                            <ion-icon name="home-outline"></ion-icon>
                            <strong>Alamat:</strong> <?php echo htmlspecialchars($siswa['alamat']); ?>
                        </div>
                        <div class="profil-item">
                            <ion-icon name="<?php echo htmlspecialchars($siswa['jenis_kelamin']) === 'L' ? 'gender-male-outline' : 'gender-female-outline'; ?>"></ion-icon>
                            <strong>Jenis Kelamin:</strong> <?php echo htmlspecialchars($siswa['jenis_kelamin']); ?>
                        </div>
                        <div class="profil-item">
                            <ion-icon name="id-card-outline"></ion-icon>
                            <strong>NISN:</strong> <?php echo htmlspecialchars($siswa['nisn']); ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <h2>Riwayat Transaksi</h2>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>No Referensi</th>
                            <th>Tanggal</th>
                            <th>Waktu</th>
                            <th>Setoran</th>
                            <th>Penarikan</th>
                            <th>Saldo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $saldo = 0;
                        $total_setoran = 0;
                        $total_penarikan = 0;

                        if ($result_transaksi->num_rows > 0):
                            while ($transaksi = $result_transaksi->fetch_assoc()):
                                if ($transaksi['jenis_transaksi'] === 'setor') {
                                    $saldo += $transaksi['jumlah'];
                                    $total_setoran += $transaksi['jumlah'];
                                } elseif ($transaksi['jenis_transaksi'] === 'tarik') {
                                    $saldo -= $transaksi['jumlah'];
                                    $total_penarikan += $transaksi['jumlah'];
                                }
                                ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($transaksi['no_referensi']); ?></td>
                                    <td><?php echo htmlspecialchars(date('d-m-Y', strtotime($transaksi['tanggal']))); ?></td>
                                    <td><?php echo htmlspecialchars(date('H:i:s', strtotime($transaksi['tanggal']))); ?></td>
                                    <td><?php echo $transaksi['jenis_transaksi'] === 'setor' ? number_format($transaksi['jumlah'], 2) : '-'; ?></td>
                                    <td><?php echo $transaksi['jenis_transaksi'] === 'tarik' ? number_format($transaksi['jumlah'], 2) : '-'; ?></td>
                                    <td><?php echo 'Rp. ' . number_format($saldo, 2); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="error">Tidak ada riwayat transaksi.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="total-transactions">
                <h3>Total Transaksi</h3>
                <p>Total Setoran: Rp. <?php echo number_format($total_setoran, 2); ?></p>
                <p>Total Penarikan: Rp. <?php echo number_format($total_penarikan, 2); ?></p>
                <p>Saldo Akhir: Rp. <?php echo number_format($saldo, 2); ?></p>
            </div>
        </div>
    </div>

    <script src="assets/main.js"></script>
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    <script>
        <?php if (isset($_GET['message'])): ?>
            alert('<?php echo htmlspecialchars($_GET['message']); ?>');
        <?php endif; ?>
    </script>
</body>
</html>
