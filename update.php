<?php
include "koneksi.php";

if (!isset($_SESSION['user'])) {
    header('location:login.php');
    exit;
}

$message = '';  // Pesan alert
$redirect = ''; // URL redirect setelah proses berhasil

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Fetch existing data
    $stmt = $koneksi->prepare("SELECT * FROM siswa WHERE id_siswa = ?");
    $stmt->bind_param("s", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();
    $stmt->close();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get updated values
    $id = $_POST['id_siswa'];
    $nisn = $_POST['nisn'];
    $nama = $_POST['nama'];
    $tanggal_lahir = $_POST['tanggal_lahir'];
    $kelas = $_POST['kelas'];
    $alamat = $_POST['alamat'];
    $jenis_kelamin = $_POST['jenis_kelamin'];

    // Update query with prepared statements
    $stmt = $koneksi->prepare("UPDATE siswa SET
        nisn = ?, nama = ?, tanggal_lahir = ?, kelas = ?, alamat = ?, jenis_kelamin = ? WHERE id_siswa = ?");

    $stmt->bind_param("sssssss", $nisn, $nama, $tanggal_lahir, $kelas, $alamat, $jenis_kelamin, $id);

    if ($stmt->execute()) {
        $message = "Data siswa berhasil diperbarui!";
        $redirect = "siswa.php"; // Redirect to the main page after success
    } else {
        $message = "Error: " . $stmt->error;
    }
    $stmt->close();
}

// Ambil data kelas untuk dropdown
$sql_kelas = "SELECT DISTINCT nama_kelas FROM kelas ORDER BY nama_kelas ASC";
$result_kelas = $koneksi->query($sql_kelas);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Data Siswa</title>
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
        <h2>Update Data Siswa</h2>
        <form method="post" action="">
            <input type="hidden" name="id_siswa" value="<?php echo htmlspecialchars($data['id_siswa']); ?>">
            <div class="form-group">
                <label for="nisn">NISN:</label>
                <input type="text" id="nisn" name="nisn" value="<?php echo htmlspecialchars($data['nisn']); ?>" required>
            </div>
            <div class="form-group">
                <label for="nama">Nama:</label>
                <input type="text" id="nama" name="nama" value="<?php echo htmlspecialchars($data['nama']); ?>" required>
            </div>
            <div class="form-group">
                <label for="kelas">Kelas:</label>
                <select id="kelas" name="kelas" required>
                    <option value="">Pilih Kelas</option>
                    <?php while ($row = $result_kelas->fetch_assoc()): ?>
                        <option value="<?php echo htmlspecialchars($row['nama_kelas']); ?>" <?php if ($row['nama_kelas'] == $data['kelas']) echo 'selected'; ?>>
                            <?php echo htmlspecialchars($row['nama_kelas']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="alamat">Alamat:</label>
                <textarea id="alamat" name="alamat" required><?php echo htmlspecialchars($data['alamat']); ?></textarea>
            </div>
            <div class="form-group">
                <label for="tanggal_lahir">Tanggal Lahir:</label>
                <input type="date" id="tanggal_lahir" name="tanggal_lahir" value="<?php echo htmlspecialchars($data['tanggal_lahir']); ?>" required>
            </div>
            <div class="form-group">
                <label for="jenis_kelamin">Jenis Kelamin:</label>
                <select id="jenis_kelamin" name="jenis_kelamin" required>
                    <option value="Laki-laki" <?php if ($data['jenis_kelamin'] == 'Laki-laki') echo 'selected'; ?>>Laki-laki</option>
                    <option value="Perempuan" <?php if ($data['jenis_kelamin'] == 'Perempuan') echo 'selected'; ?>>Perempuan</option>
                </select>
            </div>
            <div class="button-container">
                <button type="submit">Perbarui Siswa</button>
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
