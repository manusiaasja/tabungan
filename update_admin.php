<?php
include "koneksi.php";

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

$message = '';
$id_user = $_GET['id'] ?? null;

if ($id_user) {
    $sql = "SELECT username, nama FROM user WHERE id_user = ? AND role = 'admin'";
    $stmt = $koneksi->prepare($sql);
    $stmt->bind_param("i", $id_user);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        die("Admin tidak ditemukan.");
    }

    $admin_data = $result->fetch_assoc();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = !empty($_POST['password']) ? md5(trim($_POST['password'])) : $admin_data['password'];
    $confirm_password = !empty($_POST['confirm_password']) ? md5(trim($_POST['confirm_password'])) : $admin_data['password'];
    $nama_admin = trim($_POST['nama_admin']);

    if ($password !== $confirm_password) {
        $message = "Password dan konfirmasi password tidak cocok!";
    } else {
        $sql = "UPDATE user SET username = ?, password = ?, nama = ? WHERE id_user = ?";
        $stmt = $koneksi->prepare($sql);
        $stmt->bind_param("sssi", $username, $password, $nama_admin, $id_user);

        if ($stmt->execute()) {
            $message = "Admin berhasil diupdate!";
            header("Location: pengguna.php?message=" . urlencode($message));
            exit;
        } else {
            $message = "Gagal mengupdate admin: " . $stmt->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Admin | Aplikasi Tabungan Siswa</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
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
        <h2>Update Admin</h2>
        <form method="POST" action="">
            <label for="nama_admin">Nama Admin:</label>
            <input type="text" id="nama_admin" name="nama_admin" value="<?php echo htmlspecialchars($admin_data['nama']); ?>" required>

            <label for="username">Username:</label>
            <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($admin_data['username']); ?>" required>

            <label for="password">Password (Kosongkan jika tidak ingin diubah):</label>
            <input type="password" id="password" name="password">

            <label for="confirm_password">Konfirmasi Password:</label>
            <input type="password" id="confirm_password" name="confirm_password">

            <button type="submit">Update Admin</button>
            <button type="button" class="btn-secondary" onclick="window.location.href='pengguna.php'">Kembali</button>
        </form>

        <?php if ($message): ?>
            <script>
                Swal.fire({
                    title: '<?php echo strpos($message, 'berhasil') !== false ? 'Berhasil!' : 'Gagal!'; ?>',
                    text: "<?php echo $message; ?>",
                    icon: '<?php echo strpos($message, 'berhasil') !== false ? 'success' : 'error'; ?>',
                    confirmButtonText: 'OK'
                });
            </script>
        <?php endif; ?>
    </div>
</body>
</html>
