<?php
include "koneksi.php";

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

// Ambil data user
$user_role = $_SESSION['user']['role'];

$sql = "SELECT * FROM siswa";
$result = $koneksi->query($sql);

// Hitung jumlah siswa
$sql_jumlah_siswa = "SELECT COUNT(*) as total FROM siswa";
$result_jumlah_siswa = $koneksi->query($sql_jumlah_siswa);
$data_jumlah_siswa = $result_jumlah_siswa->fetch_assoc();
$jumlah_siswa = $data_jumlah_siswa['total'];

// Hitung total setoran dan penarikan
$sql_total_setoran = "SELECT SUM(jumlah) as total_setoran FROM transaksi WHERE jenis_transaksi='setor'";
$result_total_setoran = $koneksi->query($sql_total_setoran);
$data_total_setoran = $result_total_setoran->fetch_assoc();
$total_setoran = $data_total_setoran['total_setoran'] ?: 0;

$sql_total_penarikan = "SELECT SUM(jumlah) as total_penarikan FROM transaksi WHERE jenis_transaksi='tarik'";
$result_total_penarikan = $koneksi->query($sql_total_penarikan);
$data_total_penarikan = $result_total_penarikan->fetch_assoc();
$total_penarikan = $data_total_penarikan['total_penarikan'] ?: 0;

// Hitung saldo
$total_saldo = $total_setoran - $total_penarikan;

// Ambil data transaksi per bulan
$sql_transaksi_per_bulan = "
    SELECT MONTH(tanggal) as bulan, SUM(jumlah) as total_tabungan 
    FROM transaksi 
    WHERE jenis_transaksi = 'setor'
    GROUP BY MONTH(tanggal)
    ORDER BY MONTH(tanggal) ASC
";

$result_transaksi_per_bulan = $koneksi->query($sql_transaksi_per_bulan);
$bulan = [];
$total_tabungan = [];

// Menyiapkan data bulan dan total tabungan
while ($row = $result_transaksi_per_bulan->fetch_assoc()) {
    $bulan[] = date('F', mktime(0, 0, 0, $row['bulan'], 10)); // Nama bulan
    $total_tabungan[] = $row['total_tabungan'];
}

// Buat array untuk bulan-bulan yang hilang
$all_bulan = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
foreach ($all_bulan as $bln) {
    if (!in_array($bln, $bulan)) {
        $bulan[] = $bln;
        $total_tabungan[] = 0; // Menambahkan nilai 0 jika tidak ada data untuk bulan tersebut
    }
}

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Aplikasi Tabungan Siswa</title>
    <link rel="stylesheet" href="assets/style.css">
    <style>
        table {
            width: 100%;
            max-width: 100%;
            overflow-x: auto;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: #fff;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        th,
        td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #f7fafc;
            font-weight: bold;
            color: #333;
        }

        tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tbody tr:hover {
            background-color: #e2e8f0;
        }

        .cardBox {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            margin: 20px 0;
        }

        .card {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 5px;
            margin: 10px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            transition: transform 0.2s;
            flex: 1 1 calc(48% - 20px);
            height: 80px;
            box-sizing: border-box;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .numbers {
            font-size: 10px;
            font-weight: bold;
            margin-right: 5px;
        }

        .cardName {
            font-size: 8px;
            color: #555;
        }

        .iconBx {
            font-size: 12px;
            color: #4CAF50;
            flex-shrink: 0;
            margin-left: 5px;
        }

        @media (max-width: 600px) {
            .card {
                flex: 1 1 calc(100% - 20px);
            }
        }

        @media (min-width: 601px) {
            .card {
                flex: 1 1 calc(48% - 20px);
            }
        }

        @media (min-width: 901px) {
            .card {
                flex: 1 1 calc(23% - 20px);
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="navigation">
            <ul>
                <li>
                    <a href="#">
                        <span class="icon">
                            <ion-icon name="logo-apple"></ion-icon>
                        </span>
                        <span class="title">Tabungan</span>
                    </a>
                </li>
                <li>
                    <a href="index.php" class="active">
                        <span class="icon">
                            <ion-icon name="home-outline"></ion-icon>
                        </span>
                        <span class="title">Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="siswa.php">
                        <span class="icon">
                            <ion-icon name="people-outline"></ion-icon>
                        </span>
                        <span class="title">Siswa</span>
                    </a>
                </li>
                <li>
                    <a href="kelas.php">
                        <span class="icon">
                            <ion-icon name="school-outline"></ion-icon>
                        </span>
                        <span class="title">Kelas</span>
                    </a>
                </li>
                <li>
                    <a href="transaksi.php">
                        <span class="icon">
                            <ion-icon name="cash-outline"></ion-icon>
                        </span>
                        <span class="title">Transaksi</span>
                    </a>
                </li>
                <li>
                    <a href="pengguna.php">
                        <span class="icon">
                            <ion-icon name="person-circle-outline"></ion-icon>
                        </span>
                        <span class="title">Pengguna Sistem</span>
                    </a>
                </li>

                <li>
                    <a href="logout.php">
                        <span class="icon">
                            <ion-icon name="log-out-outline"></ion-icon>
                        </span>
                        <span class="title">Keluar</span>
                    </a>
                </li>
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

            <div class="cardBox">
                <div class="card">
                    <div>
                        <div class="numbers"><?php echo $jumlah_siswa; ?></div>
                        <div class="cardName">Jumlah Siswa</div>
                    </div>
                    <div class="iconBx">
                        <ion-icon name="people-outline"></ion-icon>
                    </div>
                </div>

                <div class="card">
                    <div>
                        <div class="numbers"><?php echo number_format($total_setoran, 2, ',', '.'); ?></div>
                        <div class="cardName">Total Setoran</div>
                    </div>
                    <div class="iconBx">
                        <ion-icon name="arrow-up-circle-outline"></ion-icon>
                    </div>
                </div>

                <div class="card">
                    <div>
                        <div class="numbers"><?php echo number_format($total_penarikan, 2, ',', '.'); ?></div>
                        <div class="cardName">Total Penarikan</div>
                    </div>
                    <div class="iconBx">
                        <ion-icon name="arrow-down-circle-outline"></ion-icon>
                    </div>
                </div>

                <div class="card">
                    <div>
                        <div class="numbers"><?php echo number_format($total_saldo, 2, ',', '.'); ?></div>
                        <div class="cardName">Total Saldo</div>
                    </div>
                    <div class="iconBx">
                        <ion-icon name="cash-outline"></ion-icon>
                    </div>
                </div>
            </div>

            <!-- Grafik Batang -->
            <div class="chart-container">
                <canvas id="tabunganChart" width="400" height="200"></canvas>
            </div>

            <div>
                <table>
                    <thead>
                        <tr>
                            <th colspan="2">Profil Sekolah</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>NPSN</td>
                            <td>70006539</td>
                        </tr>
                        <tr>
                            <td>Nama</td>
                            <td>MAKN ENDE</td>
                        </tr>
                        <tr>
                            <td>Status Sekolah</td>
                            <td>NEGERI</td>
                        </tr>
                        <tr>
                            <td>Alamat Jalan</td>
                            <td>Jl. Raya Ende-Bajawa Km 21 RT RW</td>
                        </tr>
                        <tr>
                            <td>Kepala Sekolah</td>
                            <td>ABDUL WAHAB</td>
                        </tr>
                        <tr>
                            <td>Akreditasi</td>
                            <td>A</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/ionicons@5.5.2/dist/ionicons.js"></script>
    <script>
        const toggle = document.querySelector(".toggle");
        const navigation = document.querySelector(".navigation");
        const main = document.querySelector(".main");

        toggle.onclick = function () {
            navigation.classList.toggle("active");
            main.classList.toggle("active");
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Data dari PHP ke JavaScript
        const bulan = <?php echo json_encode($bulan); ?>;
        const totalTabungan = <?php echo json_encode($total_tabungan); ?>;

        const ctx = document.getElementById('tabunganChart').getContext('2d');
        const tabunganChart = new Chart(ctx, {
            type: 'bar', // Jenis grafik: Batang
            data: {
                labels: bulan, // Nama bulan
                datasets: [{
                    label: 'Total Tabungan',
                    data: totalTabungan, // Data jumlah tabungan per bulan
                    backgroundColor: 'rgba(75, 192, 192, 0.2)', // Warna latar belakang batang
                    borderColor: 'rgba(75, 192, 192, 1)', // Warna border batang
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true // Sumbu Y dimulai dari 0
                    }
                }
            }
        });
    </script>

</body>

</html>
