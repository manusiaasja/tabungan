<?php
include "koneksi.php";

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

// Ambil data siswa dengan saldo tertinggi ke terendah
$sql_siswa = "
    SELECT s.id_siswa, s.nama, s.kelas,
           COALESCE(SUM(CASE WHEN t.jenis_transaksi = 'setor' THEN t.jumlah ELSE 0 END), 0) - 
           COALESCE(SUM(CASE WHEN t.jenis_transaksi = 'tarik' THEN t.jumlah ELSE 0 END), 0) AS saldo
    FROM siswa s
    LEFT JOIN transaksi t ON s.id_siswa = t.id_siswa
    GROUP BY s.id_siswa
    ORDER BY saldo DESC
";
$result_siswa = $koneksi->query($sql_siswa);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaksi | Dashboard Admin Responsif</title>
    <link rel="stylesheet" href="assets/style.css">
    <style>
        .container {
            max-width: 1200px;
            margin: auto;
            overflow-x: auto;
        }
        .form-navigation {
            margin-bottom: 20px;
        }
        .form-navigation button {
            padding: 10px 20px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .form-navigation button:hover {
            opacity: 0.9;
            transform: translateY(-2px);
        }
        h2 {
            font-size: 1.5rem;
            font-weight: 700;
            color: #333;
            border-bottom: 3px solid #4a5568;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .table-responsive {
            overflow-x: auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            background-color: #fff;
            min-width: 600px;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f7fafc;
            font-weight: 700;
        }
        tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        tbody tr:hover {
            background-color: #e2e8f0;
        }
        .details-button {
            background-color: #3182ce;
            color: white;
            padding: 8px 12px;
            border-radius: 6px;
            text-align: center;
            text-decoration: none;
            transition: background-color 0.3s, transform 0.2s;
        }
        .details-button:hover {
            background-color: #2b6cb0;
            transform: translateY(-1px);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="navigation">
            <ul>
                <li><a href="#"><span class="icon"><ion-icon name="logo-apple"></ion-icon></span><span class="title">Tabungan</span></a></li>
                <li><a href="index.php"><span class="icon"><ion-icon name="home-outline"></ion-icon></span><span class="title">Dashboard</span></a></li>
                <li><a href="siswa.php"><span class="icon"><ion-icon name="people-outline"></ion-icon></span><span class="title">Siswa</span></a></li>
                <li><a href="kelas.php"><span class="icon"><ion-icon name="school-outline"></ion-icon></span><span class="title">Kelas</span></a></li>
                <li class="menu-transaksi"><a href="transaksi.php"><span class="icon"><ion-icon name="cash-outline"></ion-icon></span><span class="title">Transaksi</span></a></li>
                <li><a href="pengguna.php" class="active"><span class="icon"><ion-icon name="person-circle-outline"></ion-icon></span><span class="title">Pengguna Sistem</span></a></li>
               <li><a href="logout.php"><span class="icon"><ion-icon name="log-out-outline"></ion-icon></span><span class="title">Sign Out</span></a></li>
            </ul>
        </div>

        <div class="main">
            <div class="topbar">
                <div class="toggle"><ion-icon name="menu-outline"></ion-icon></div>
                <div class="search">
                    <label>
                        <input type="text" placeholder="Search here" class="border rounded px-4 py-2">
                        <ion-icon name="search-outline"></ion-icon>
                    </label>
                </div>
                <div class="user"><img src="assets/imgs/alma.jpeg" alt="User" class="w-10 h-10 rounded-full"></div>
            </div>

            <div class="form-navigation">
                <button onclick="window.location.href='form_setor.php'" class="bg-green-500 text-white">Setoran</button>
                <button onclick="window.location.href='form_tarik.php'" class="bg-red-500 text-white">Penarikan</button>
            </div>

            <h2>Daftar Siswa</h2>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>ID Siswa</th>
                            <th>Nama</th>
                            <th>Kelas</th>
                            <th>Saldo Total</th>
                            <th>Detail</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        while ($row = $result_siswa->fetch_assoc()) {
                            $id_siswa = htmlspecialchars($row['id_siswa']);
                            $nama = htmlspecialchars($row['nama']);
                            $kelas = htmlspecialchars($row['kelas']);
                            $saldo_total = number_format($row['saldo'], 2);

                            echo "<tr>";
                            echo "<td>$id_siswa</td>";
                            echo "<td>$nama</td>";
                            echo "<td>$kelas</td>";
                            echo "<td>Rp $saldo_total</td>";
                            echo "<td><a href='detail_siswa.php?id=$id_siswa' class='details-button'>Detail</a></td>";
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="assets/main.js"></script>
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
</body>
</html>
