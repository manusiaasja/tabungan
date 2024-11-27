<?php
include "koneksi.php";

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

// Ambil data kelas untuk dropdown saat update
$sql_kelas = "SELECT * FROM kelas";
$result_kelas = $koneksi->query($sql_kelas);

// Cek query pencarian
$query = isset($_GET['query']) ? $koneksi->real_escape_string($_GET['query']) : '';
$sql = "SELECT * FROM siswa WHERE nisn LIKE '%$query%' OR nama LIKE '%$query%' ORDER BY id_siswa ASC";
$result = $koneksi->query($sql);

if ($result === FALSE) {
    die("Query error: " . $koneksi->error);
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Siswa | Dashboard Admin Responsif</title>
    <link rel="stylesheet" href="assets/style.css">
    <style>
        .container {
            max-width: 1200px; /* Atur lebar maksimum */
            margin: auto;
            overflow-x: auto;
        }

        .btn-tambah {
            padding: 10px 20px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            transition: all 0.3s ease;
            color: white;
            text-decoration: none;
            background-color: #48bb78;
            display: inline-block;
            margin-bottom: 10px;
        }

        .btn-tambah:hover {
            background-color: #38a169;
        }

        .table-wrapper {
            overflow-x: auto; /* Mengizinkan scroll horizontal */
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background-color: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            table-layout: auto; /* Menyesuaikan ukuran tabel */
            min-width: 600px; /* Lebar minimum tabel */
        }

        th,
        td {
            padding: 10px 12px; /* Mengurangi padding sedikit untuk kompak */
            text-align: left;
            border-bottom: 1px solid #ddd;
            word-wrap: break-word; /* Memungkinkan teks panjang untuk dibungkus */
            font-size: 14px; /* Menyesuaikan ukuran font */
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

        .btn-update,
        .btn-delete {
            padding: 5px 10px;
            border-radius: 5px;
            color: white;
            font-size: 14px; /* Ukuran font pada tombol */
        }

        .btn-update {
            background-color: #007bff;
        }

        .btn-update:hover {
            background-color: #0056b3;
        }

        .btn-delete {
            background-color: #dc3545;
        }

        .btn-delete:hover {
            background-color: #c82333;
        }

        .btn-update ion-icon,
        .btn-delete ion-icon {
            font-size: 18px;
        }

        @media (max-width: 768px) {
            table {
                font-size: 12px; /* Mengurangi ukuran font untuk layar kecil */
            }

            th, td {
                padding: 8px 10px; /* Mengurangi padding untuk layar kecil */
            }

            .btn-update,
            .btn-delete {
                padding: 4px 8px; /* Ukuran tombol lebih kecil */
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="navigation">
            <ul>
                <li><a href="#"><span class="icon"><ion-icon name="logo-apple"></ion-icon></span><span class="title">Tabungan</span></a></li>
                <li><a href="index.php"><span class="icon"><ion-icon name="home-outline"></ion-icon></span><span class="title">Dashboard</span></a></li>
                <li><a href="siswa.php" class="active"><span class="icon"><ion-icon name="people-outline"></ion-icon></span><span class="title">Siswa</span></a></li>
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
                    <form action="" method="GET">
                        <label>
                            <input type="text" name="query" placeholder="Cari di sini" value="<?php echo htmlspecialchars($query); ?>">
                            <ion-icon name="search-outline"></ion-icon>
                        </label>
                    </form>
                </div>
                <div class="user"><img src="assets/imgs/alma.jpeg" alt=""></div>
            </div>

            <div>
                <a href="tambah.php" class="btn-tambah">Tambah Siswa</a>
            </div>

            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>ID Siswa</th>
                            <th>NISN</th>
                            <th>Nama</th>
                            <th>Tanggal Lahir</th>
                            <th>Kelas</th>
                            <th>Alamat</th>
                            <th>Jenis Kelamin</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($row['id_siswa']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['nisn']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['nama']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['tanggal_lahir']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['kelas']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['alamat']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['jenis_kelamin']) . "</td>";
                                echo "<td>
                                    <div style='display: flex; gap: 5px;'>
                                        <a href='update.php?id=" . urlencode($row['id_siswa']) . "' class='btn-update' title='Update'>
                                            <ion-icon name='create-outline'></ion-icon>
                                        </a>
                                        <a href='delete.php?id=" . urlencode($row['id_siswa']) . "' class='btn-delete' onclick='return confirm(\"Apakah Anda yakin?\")' title='Delete'>
                                            <ion-icon name='trash-outline'></ion-icon>
                                        </a>
                                    </div>
                                </td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='8'>Tidak ada data siswa</td></tr>";
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
    <script>
        // Menampilkan alert jika ada pesan
        <?php if (isset($_GET['message'])): ?>
            alert('<?php echo htmlspecialchars($_GET['message']); ?>');
        <?php endif; ?>
    </script>
</body>

</html>
