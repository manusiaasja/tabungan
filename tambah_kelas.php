<?php
include "koneksi.php";

// Cek apakah user sudah login dan memiliki hak akses admin
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

if ($_SESSION['user']['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

$message = '';
$redirect = '';
$alertType = ''; // Tipe alert akan diisi sesuai kondisi

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_kelas = $_POST['kelas'];

    // Validasi input
    $nama_kelas = $koneksi->real_escape_string($nama_kelas);

    // Cek apakah nama kelas sudah ada
    $checkSql = "SELECT * FROM kelas WHERE nama_kelas = '$nama_kelas'";
    $checkResult = $koneksi->query($checkSql);

    if ($checkResult->num_rows > 0) {
        // Jika kelas sudah ada
        $message = "Kelas dengan nama '$nama_kelas' sudah ada!"; // Pesan kesalahan
        $alertType = "error"; // Tipe alert untuk SweetAlert
    } else {
        // Query untuk menyimpan data
        $sql = "INSERT INTO kelas (nama_kelas) VALUES ('$nama_kelas')";
        if ($koneksi->query($sql) === TRUE) {
            // Jika berhasil ditambahkan
            $message = "Kelas berhasil ditambahkan!";
            $redirect = "kelas.php"; // Halaman yang akan diarahkan setelah sukses
            $alertType = "success"; // Tipe alert untuk SweetAlert
        } else {
            // Jika terjadi kesalahan saat penyimpanan
            $message = "Error: " . $koneksi->error; // Pesan kesalahan lainnya
            $alertType = "error"; // Tipe alert untuk SweetAlert
        }
    }

    // Tutup koneksi
    $koneksi->close();
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Kelas | Dashboard Admin</title>
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
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .container {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
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

        .form-group {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }

        label {
            font-weight: bold;
            margin-bottom: 5px;
        }

        input {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
            width: 100%;
        }

        .button-container {
            display: flex;
            justify-content: space-between;
        }

        button,
        .btn-secondary {
            padding: 10px;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            color: #fff;
            background-color: #4CAF50;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover,
        .btn-secondary:hover {
            background-color: #45a049;
        }

        .btn-secondary {
            background-color: #555;
        }

        .btn-secondary:hover {
            background-color: #444;
        }

        .btn-secondary:active {
            background-color: #333;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>Tambah Kelas</h2>
        <form method="post" action="">
            <div class="form-group">
                <label for="kelas">Nama Kelas:</label>
                <input type="text" id="kelas" name="kelas" placeholder="Kelas" required>
            </div>

            <div class="button-container">
                <button type="submit">Simpan</button>
                <a href="kelas.php" class="btn-secondary">Batal</a>
            </div>
        </form>
    </div>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        <?php if ($message): ?>
            Swal.fire({
                title: <?php echo $alertType === 'success' ? '"Berhasil!"' : '"Gagal!"'; ?>,
                text: <?php echo json_encode($message); ?>,
                icon: '<?php echo $alertType; ?>',
                confirmButtonText: 'OK'
            }).then((result) => {
                if (result.isConfirmed) {
                    <?php if ($redirect): ?>
                        window.location.href = '<?php echo $redirect; ?>';
                    <?php endif; ?>
                }
            });
        <?php endif; ?>
    </script>
</body>

</html>
