<?php
include "koneksi.php";

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

// Ambil data pengguna berdasarkan ID
$id_user = $_GET['id'];
$sql = "SELECT u.username, u.role, u.id_siswa, s.nama, u.password FROM user u LEFT JOIN siswa s ON u.id_siswa = s.id_siswa WHERE u.id_user = ?";
$stmt = $koneksi->prepare($sql);
$stmt->bind_param("i", $id_user);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $role = $_POST['role'];
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $hashed_password = $user['password']; // Default ke password saat ini

    // Jika password baru diisi, lakukan hashing
    if (!empty($password)) {
        $hashed_password = md5($password);
    }

    if ($role === 'user') {
        $id_siswa = $_POST['id_siswa'];
        // Update pengguna dengan ID siswa
        $sql = "UPDATE user SET username = ?, password = ?, role = ?, id_siswa = ? WHERE id_user = ?";
        $stmt = $koneksi->prepare($sql);
        $stmt->bind_param("ssiii", $username, $hashed_password, $role, $id_siswa, $id_user);
    } else {
        // Update admin tanpa ID siswa
        $sql = "UPDATE user SET username = ?, password = ?, role = ?, id_siswa = NULL WHERE id_user = ?";
        $stmt = $koneksi->prepare($sql);
        $stmt->bind_param("ssii", $username, $hashed_password, $role, $id_user);
    }

    if ($stmt->execute()) {
        header("Location: pengguna.php?message=Update berhasil!");
        exit;
    } else {
        $message = "Gagal mengupdate pengguna: " . $stmt->error;
    }
}

$sql_siswa = "SELECT id_siswa, nama FROM siswa";
$result_siswa = $koneksi->query($sql_siswa);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Pengguna | Aplikasi Tabungan Siswa</title>
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
        <h2>Update Pengguna</h2>
        <form method="POST" action="">
            <label for="role">Role:</label>
            <select id="role" name="role" required onchange="toggleFields()">
                <option value="admin" <?php echo $user['role'] === 'admin' ? 'selected' : ''; ?>>Admin</option>
                <option value="user" <?php echo $user['role'] === 'user' ? 'selected' : ''; ?>>User</option>
            </select>

            <div id="adminFields" style="<?php echo $user['role'] === 'admin' ? 'block' : 'none'; ?>">
                <label for="nama_admin">Nama Admin:</label>
                <input type="text" id="nama_admin" name="nama_admin" required value="<?php echo htmlspecialchars($user['nama'] ?? ''); ?>">
            </div>

            <div id="userFields" style="<?php echo $user['role'] === 'user' ? 'block' : 'none'; ?>">
                <label for="id_siswa">ID Siswa:</label>
                <select id="id_siswa" name="id_siswa" required>
                    <option value="" disabled>Pilih Siswa</option>
                    <?php while ($row = $result_siswa->fetch_assoc()): ?>
                        <option value="<?php echo htmlspecialchars($row['id_siswa']); ?>" <?php echo $user['id_siswa'] == $row['id_siswa'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($row['id_siswa']) . ' - ' . htmlspecialchars($row['nama']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required value="<?php echo htmlspecialchars($user['username']); ?>">

            <label for="password">Password:</label>
            <input type="text" id="password" name="password" placeholder="Masukkan password baru jika ingin mengganti" value="">

            <button type="submit">Update Pengguna</button>
            <button type="button" class="btn-secondary" onclick="window.location.href='pengguna.php'">Kembali</button>
        </form>
    </div>

    <script>
        function toggleFields() {
            const role = document.getElementById('role').value;
            const adminFields = document.getElementById('adminFields');
            const userFields = document.getElementById('userFields');

            if (role === 'admin') {
                adminFields.style.display = 'block';
                userFields.style.display = 'none';
            } else {
                adminFields.style.display = 'none';
                userFields.style.display = 'block';
            }
        }

        // Jalankan fungsi untuk menampilkan field yang benar
        toggleFields();
    </script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>
</html>
