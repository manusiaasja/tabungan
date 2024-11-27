<?php
include "koneksi.php";

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

// Cek koneksi database
if ($koneksi->connect_error) {
    die("Koneksi gagal: " . $koneksi->connect_error);
}

// Ambil data siswa untuk dropdown (hanya untuk User)
$sql_siswa = "SELECT id_siswa, nama FROM siswa";
$result_siswa = $koneksi->query($sql_siswa);

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $role = $_POST['role'];
    $username = trim($_POST['username']);
    $password = md5(trim($_POST['password'])); // Hash password

    if ($role === 'user') {
        $id_siswa = $_POST['id_siswa'];

        // Masukkan pengguna dengan ID siswa
        $sql = "INSERT INTO user (username, password, role, id_siswa) VALUES (?, ?, ?, ?)";
        $stmt = $koneksi->prepare($sql);
        $stmt->bind_param("sssi", $username, $password, $role, $id_siswa);
    } else {
        // Ambil nama pengguna untuk Admin
        $nama_admin = trim($_POST['nama_admin']);

        // Masukkan admin tanpa ID siswa
        $sql = "INSERT INTO user (username, password, role, nama) VALUES (?, ?, ?, ?)";
        $stmt = $koneksi->prepare($sql);
        $stmt->bind_param("ssss", $username, $password, $role, $nama_admin);
    }

    if ($stmt->execute()) {
        $message = "Pengguna berhasil ditambahkan!";
    } else {
        $message = "Gagal menambahkan pengguna: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Pengguna | Aplikasi Tabungan Siswa</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        /* Styling ... */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            background-color: #f4f4f9;
            color: #333;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .container {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            padding: 20px;
            width: 100%;
            max-width: 500px;
        }

        h2 {
            margin-bottom: 20px;
            color: #4CAF50;
            text-align: center;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        label {
            font-weight: bold;
        }

        input, select {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }

        button {
            padding: 10px;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            color: #fff;
            background-color: #4CAF50;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #45a049;
        }

        .btn-secondary {
            background-color: #555;
        }

        .btn-secondary:hover {
            background-color: #444;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Tambah Pengguna</h2>
        <form method="POST" action="">
            <label for="role">Role:</label>
            <select id="role" name="role" required onchange="toggleFields()">
                <option value="admin">Admin</option>
                <option value="user">User</option>
            </select>

            <div id="adminFields">
                <label for="nama_admin">Nama Admin:</label>
                <input type="text" id="nama_admin" name="nama_admin" required>
            </div>

            <div id="userFields" style="display: none;">
                <label for="id_siswa">ID Siswa:</label>
                <select id="id_siswa" name="id_siswa" required>
                    <option value="" disabled selected>Pilih Siswa</option>
                    <?php while ($row = $result_siswa->fetch_assoc()): ?>
                        <option value="<?php echo htmlspecialchars($row['id_siswa']); ?>">
                            <?php echo htmlspecialchars($row['id_siswa']) . ' - ' . htmlspecialchars($row['nama']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>

            <button type="submit">Tambah Pengguna</button>
            <button type="button" class="btn-secondary" onclick="window.location.href='pengguna.php'">Kembali</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        function toggleFields() {
            const role = document.getElementById('role').value;
            const adminFields = document.getElementById('adminFields');
            const userFields = document.getElementById('userFields');
            const namaAdminField = document.getElementById('nama_admin');

            if (role === 'admin') {
                adminFields.style.display = 'block';
                namaAdminField.setAttribute('required', 'required');
            } else {
                adminFields.style.display = 'none';
                namaAdminField.removeAttribute('required');
                userFields.style.display = 'block'; // Menampilkan field user jika perlu
            }
        }

        <?php if ($message): ?>
        Swal.fire({
            title: "<?php echo strpos($message, 'berhasil') !== false ? 'Berhasil!' : 'Gagal!'; ?>",
            text: "<?php echo $message; ?>",
            icon: "<?php echo strpos($message, 'berhasil') !== false ? 'success' : 'error'; ?>",
            confirmButtonText: 'OK'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'pengguna.php';
            }
        });
        <?php endif; ?>
    </script>
</body>
</html>
