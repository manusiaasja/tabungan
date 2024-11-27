<?php
include "koneksi.php";

if ($koneksi->connect_error) {
    die("Koneksi gagal: " . $koneksi->connect_error);
}

$message = '';
$redirect = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil data dari formulir
    $nisn = $_POST['nisn'];
    $nama = $_POST['nama'];
    $tanggal_lahir = $_POST['tanggal_lahir'];
    $kelas = $_POST['kelas'];
    $alamat = $_POST['alamat'];
    $jenis_kelamin = $_POST['jenis_kelamin'];
    
    // Validasi input (sanitasi input)
    $nisn = $koneksi->real_escape_string($nisn);
    $nama = $koneksi->real_escape_string($nama);
    $tanggal_lahir = $koneksi->real_escape_string($tanggal_lahir);
    $alamat = $koneksi->real_escape_string($alamat);
    $jenis_kelamin = $koneksi->real_escape_string($jenis_kelamin);
    
    // Query untuk menyimpan data
    $sql = "INSERT INTO siswa (nisn, nama, tanggal_lahir, kelas, alamat, jenis_kelamin, created_at)
            VALUES ('$nisn', '$nama', '$tanggal_lahir', '$kelas', '$alamat', '$jenis_kelamin', NOW())";

    if ($koneksi->query($sql) === TRUE) {
        $message = "Data siswa berhasil ditambahkan!";
        $redirect = "siswa.php"; // Halaman yang akan diarahkan setelah sukses
    } else {
        $message = "Error: " . $koneksi->error;
    }
}

// Ambil data kelas untuk dropdown tanpa duplikasi dan terurut
$sql_kelas = "SELECT DISTINCT nama_kelas FROM kelas ORDER BY nama_kelas ASC";
$result_kelas = $koneksi->query($sql_kelas);

// Tutup koneksi setelah semua query selesai
$koneksi->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Siswa</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        /* Styling */
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

        input, select, textarea {
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

        button, .btn-secondary {
            padding: 10px;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            color: #fff;
            background-color: #4CAF50;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover, .btn-secondary:hover {
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
        <h2>Tambah Data Siswa</h2>
        <form method="post" action="">
            <div class="form-group">
                <label for="nisn">NISN:</label>
                <input type="text" id="nisn" name="nisn" required>
            </div>

            <div class="form-group">
                <label for="nama">Nama:</label>
                <input type="text" id="nama" name="nama" required>
            </div>

            <div class="form-group">
                <label for="kelas">Kelas:</label>
                <select id="kelas" name="kelas" required>
                    <option value="">Pilih Kelas</option>
                    <?php if ($result_kelas): ?>
                        <?php while ($row = $result_kelas->fetch_assoc()): ?>
                            <option value="<?php echo htmlspecialchars($row['nama_kelas']); ?>">
                                <?php echo htmlspecialchars($row['nama_kelas']); ?>
                            </option>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="alamat">Alamat:</label>
                <textarea id="alamat" name="alamat" required></textarea>
            </div>

            <div class="form-group">
                <label for="tanggal_lahir">Tanggal Lahir:</label>
                <input type="date" id="tanggal_lahir" name="tanggal_lahir" required>
            </div>

            <div class="form-group">
                <label for="jenis_kelamin">Jenis Kelamin:</label>
                <select id="jenis_kelamin" name="jenis_kelamin" required>
                    <option value="">Pilih Jenis Kelamin</option>
                    <option value="Laki-laki">Laki-laki</option>
                    <option value="Perempuan">Perempuan</option>
                </select>
            </div>

            <div class="button-container">
                <button type="submit">Tambah Siswa</button>
                <a href="siswa.php" class="btn-secondary">Kembali</a>
            </div>
        </form>
    </div>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        <?php if ($message): ?>
            Swal.fire({
                title: <?php echo strpos($message, 'Error') === false ? '"Berhasil!"' : '"Gagal!"'; ?>,
                text: <?php echo json_encode($message); ?>,
                icon: <?php echo strpos($message, 'Error') === false ? '"success"' : '"error"'; ?>,
                confirmButtonText: 'OK'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '<?php echo $redirect; ?>';
                }
            });
        <?php endif; ?>
    </script>
</body>
</html>
