<?php
include "koneksi.php";

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

$sql = "
    SELECT u.id_user, u.username, u.role, s.nama 
    FROM user u
    LEFT JOIN siswa s ON u.id_siswa = s.id_siswa
    ORDER BY u.id_user ASC
"; 
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
    <title>Pengguna Sistem | Dashboard Admin</title>
    <link rel="stylesheet" href="assets/style.css">
    <style>
        .container {
            max-width: 1200px; 
            margin: auto; 
        }
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
        .table-responsive {
            overflow-x: auto; /* Membuat tabel bisa di-scroll */
            margin-top: 20px;
        }
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
        <div class="navigation">
            <ul>
                <li><a href="#"><span class="icon"><ion-icon name="logo-apple"></ion-icon></span><span class="title">Tabungan</span></a></li>
                <li><a href="index.php"><span class="icon"><ion-icon name="home-outline"></ion-icon></span><span class="title">Dashboard</span></a></li>
                <li><a href="siswa.php"><span class="icon"><ion-icon name="people-outline"></ion-icon></span><span class="title">Siswa</span></a></li>
                <li><a href="kelas.php"><span class="icon"><ion-icon name="school-outline"></ion-icon></span><span class="title">Kelas</span></a></li>
                <li><a href="transaksi.php"><span class="icon"><ion-icon name="cash-outline"></ion-icon></span><span class="title">Transaksi</span></a></li>
                <li><a href="pengguna.php" class="active"><span class="icon"><ion-icon name="person-circle-outline"></ion-icon></span><span class="title">Pengguna Sistem</span></a></li>
                <li><a href="logout.php"><span class="icon"><ion-icon name="log-out-outline"></ion-icon></span><span class="title">Keluar</span></a></li>
            </ul>
        </div>

        <div class="main">
            <div class="topbar">
                <div class="toggle"><ion-icon name="menu-outline"></ion-icon></div>
                <div class="search">
                    <label><input type="text" placeholder="Cari di sini"><ion-icon name="search-outline"></ion-icon></label>
                </div>
                <div class="user"><img src="assets/imgs/alma.jpeg" alt=""></div>
            </div>

            <div>
                <a href="tambah_user.php" class="btn-tambah">Tambah User</a>
                <a href="tambah_admin.php" class="btn-tambah">Tambah Admin</a>
            </div>

            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Username</th>
                            <th>Password</th>
                            <th>Level</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($result->num_rows > 0) {
                            $no = 1;
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($no) . "</td>";
                                echo "<td>" . htmlspecialchars($row['role'] === 'user' ? $row['nama'] : 'Admin') . "</td>";
                                echo "<td>" . htmlspecialchars($row['username']) . "</td>";
                                echo "<td><ion-icon name='lock-closed-outline'></ion-icon></td>";
                                echo "<td>" . htmlspecialchars($row['role']) . "</td>";
                                echo "<td>
                                    <div class='action-btns'>
                                        <a href='update_" . ($row['role'] === 'admin' ? "admin" : "user") . ".php?id=" . urlencode($row['id_user']) . "' class='btn-update' title='Update'>
                                            <ion-icon name='create-outline'></ion-icon>
                                        </a>
                                        <a href='delete_pengguna.php?id=" . urlencode($row['id_user']) . "' class='btn-delete' onclick='return confirm(\"Apakah Anda yakin ingin menghapus pengguna ini?\")' title='Delete'>
                                            <ion-icon name='trash-outline'></ion-icon>
                                        </a>
                                    </div>
                                </td>";
                                echo "</tr>";
                                $no++;
                            }
                        } else {
                            echo "<tr><td colspan='6'>Tidak ada data pengguna</td></tr>";
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
