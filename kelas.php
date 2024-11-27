<?php
include "koneksi.php";

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

$sql = "SELECT * FROM kelas ORDER BY no ASC"; // Mengurutkan berdasarkan no kelas
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
    <title>Kelas | Dashboard Admin</title>
    <link rel="stylesheet" href="assets/style.css">
    <style>
        .container {
            overflow-x: auto;
            max-width: 1200px; /* Menetapkan lebar maksimum */
            margin: auto; /* Centering container */
        }

        /* Style tombol "Tambah Kelas" */
        .btn-tambah {
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 14px;
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

        /* Style tabel */
        table {
            width: 100%;
            border-collapse: collapse;
            background-color: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
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

        /* Style tombol update dan delete */
        .btn-update, .btn-delete {
            display: inline-flex;
            align-items: center;
            padding: 5px;
            border-radius: 4px;
            color: white;
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

        .action-btns {
            display: flex;
            gap: 5px;
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Navigasi -->
        <div class="navigation">
            <ul>
                <li><a href="#"><span class="icon"><ion-icon name="logo-apple"></ion-icon></span><span class="title">Tabungan</span></a></li>
                <li><a href="index.php"><span class="icon"><ion-icon name="home-outline"></ion-icon></span><span class="title">Dashboard</span></a></li>
                <li><a href="siswa.php"><span class="icon"><ion-icon name="people-outline"></ion-icon></span><span class="title">Siswa</span></a></li>
                <li><a href="kelas.php" class="active"><span class="icon"><ion-icon name="school-outline"></ion-icon></span><span class="title">Kelas</span></a></li>
                <li><a href="transaksi.php"><span class="icon"><ion-icon name="cash-outline"></ion-icon></span><span class="title">Transaksi</span></a></li>
                <li><a href="pengguna.php" class="active"><span class="icon"><ion-icon name="person-circle-outline"></ion-icon></span><span class="title">Pengguna Sistem</span></a></li>
               <li><a href="logout.php"><span class="icon"><ion-icon name="log-out-outline"></ion-icon></span><span class="title">Sign Out</span></a></li>
            </ul>
        </div>

        <!-- Main Content -->
        <div class="main">
            <div class="topbar">
                <div class="toggle"><ion-icon name="menu-outline"></ion-icon></div>
                <div class="search">
                    <label><input type="text" placeholder="Cari di sini"><ion-icon name="search-outline"></ion-icon></label>
                </div>
                <div class="user"><img src="assets/imgs/alma.jpeg" alt=""></div>
            </div>

            <!-- Tombol Tambah Kelas -->
            <div>
                <a href="tambah_kelas.php" class="btn-tambah">Tambah Kelas</a>
            </div>

            <!-- Tabel Kelas -->
            <div>
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Kelas</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($row['no']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['nama_kelas']) . "</td>";
                                echo "<td>
                                    <div class='action-btns'>
                                        <a href='update_kelas.php?id=" . urlencode($row['no']) . "' class='btn-update' title='Update'>
                                            <ion-icon name='create-outline'></ion-icon>
                                        </a>
                                        <a href='delete_kelas.php?id=" . urlencode($row['no']) . "' class='btn-delete' onclick='return confirm(\"Apakah Anda yakin ingin menghapus kelas ini?\")' title='Delete'>
                                            <ion-icon name='trash-outline'></ion-icon>
                                        </a>
                                    </div>
                                </td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='3'>Tidak ada data kelas</td></tr>";
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
        <?php if (isset($_GET['message'])): ?>
            alert('<?php echo htmlspecialchars($_GET['message']); ?>');
        <?php endif; ?>
    </script>
</body>

</html>
