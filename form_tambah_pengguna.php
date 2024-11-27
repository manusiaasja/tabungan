<?php
include "koneksi.php";

session_start();

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    // Menambahkan pengguna baru ke tabel user
    $sql = "INSERT INTO user (username, password, role) VALUES (?, ?, ?)";
    $stmt = $koneksi->prepare($sql);
    $stmt->bind_param("sss", $username, $password, $role);
    
    if ($stmt->execute()) {
        $message = "Pengguna berhasil ditambahkan!";
    } else {
        $message = "Gagal menambahkan pengguna.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Tambah Pengguna | Dashboard Admin</title>
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
        <h2>Form Tambah Pengguna</h2>
        <form action="form_tambah_pengguna.php" method="post">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>

            <label for="role">Level:</label>
            <select id="role" name="role" required>
                <option value="" disabled selected>Pilih Level</option>
                <option value="admin">Admin</option>
                <option value="user">User</option>
            </select>

            <button type="submit">Tambah Pengguna</button>
            <button type="button" class="btn-secondary" onclick="window.location.href='pengguna.php'">Kembali</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <?php if (isset($message)): ?>
    <script>
        Swal.fire({
            title: <?php echo strpos($message, 'berhasil') !== false ? '"Berhasil!"' : '"Gagal!"'; ?>,
            text: "<?php echo $message; ?>",
            icon: <?php echo strpos($message, 'berhasil') !== false ? '"success"' : '"error"'; ?>,
            confirmButtonText: 'OK'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'pengguna.php';
            }
        });
    </script>
    <?php endif; ?>
</body>
</html>
