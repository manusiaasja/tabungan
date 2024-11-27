<?php
include "koneksi.php";

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

$message = '';

// Fetch student data for dropdown
$sql_siswa = "SELECT id_siswa, nama FROM siswa";
$result_siswa = $koneksi->query($sql_siswa);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = md5(trim($_POST['password']));
    $confirm_password = md5(trim($_POST['confirm_password']));
    $id_siswa = $_POST['id_siswa'];

    if ($password !== $confirm_password) {
        $message = "Password dan konfirmasi password tidak cocok!";
    } else {
        // Insert new user
        $sql = "INSERT INTO user (username, password, role, id_siswa) VALUES (?, ?, 'user', ?)";
        $stmt = $koneksi->prepare($sql);
        $stmt->bind_param("ssi", $username, $password, $id_siswa);

        if ($stmt->execute()) {
            $message = "User berhasil ditambahkan!";
            header("Location: pengguna.php?message=" . urlencode($message));
            exit;
        } else {
            $message = "Gagal menambahkan user: " . $stmt->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah User | Aplikasi Tabungan Siswa</title>
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
        <h2>Tambah User</h2>
        <form method="POST" action="">
            <label for="id_siswa">ID Siswa:</label>
            <select id="id_siswa" name="id_siswa" required>
                <option value="" disabled selected>Pilih Siswa</option>
                <?php while ($row = $result_siswa->fetch_assoc()): ?>
                    <option value="<?php echo htmlspecialchars($row['id_siswa']); ?>">
                        <?php echo htmlspecialchars($row['id_siswa']) . ' - ' . htmlspecialchars($row['nama']); ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>

            <label for="confirm_password">Konfirmasi Password:</label>
            <input type="password" id="confirm_password" name="confirm_password" required>

            <button type="submit">Tambah User</button>
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
