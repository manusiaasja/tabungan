<?php
include "koneksi.php";

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

// Mengambil data siswa untuk dropdown
$sql_siswa = "
    SELECT s.id_siswa, s.nama,
           COALESCE(SUM(CASE WHEN t.jenis_transaksi = 'setor' THEN t.jumlah ELSE 0 END), 0) - 
           COALESCE(SUM(CASE WHEN t.jenis_transaksi = 'tarik' THEN t.jumlah ELSE 0 END), 0) AS saldo
    FROM siswa s
    LEFT JOIN transaksi t ON s.id_siswa = t.id_siswa
    GROUP BY s.id_siswa
";
$result_siswa = $koneksi->query($sql_siswa);

$saldo = 0; // Default saldo

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_siswa = $_POST['id_siswa'];
    $jumlah = str_replace(['Rp ', '.', ','], '', $_POST['jumlah']);
    $tanggal = $_POST['tanggal'];

    // Menghitung saldo siswa berdasarkan transaksi
    $sql_saldo = "
        SELECT COALESCE(SUM(CASE WHEN jenis_transaksi = 'setor' THEN jumlah ELSE 0 END), 0) - 
               COALESCE(SUM(CASE WHEN jenis_transaksi = 'tarik' THEN jumlah ELSE 0 END), 0) AS saldo 
        FROM transaksi 
        WHERE id_siswa = ?
    ";
    $stmt_saldo = $koneksi->prepare($sql_saldo);
    $stmt_saldo->bind_param("s", $id_siswa);
    $stmt_saldo->execute();
    $result_saldo = $stmt_saldo->get_result();
    $row_saldo = $result_saldo->fetch_assoc();
    $saldo = $row_saldo['saldo'];

    // Validasi jumlah penarikan
    if ($jumlah > $saldo) {
        $message = "Saldo tidak cukup untuk melakukan penarikan.";
    } else {
        // Menghasilkan nomor referensi
        $tanggal_format = date('Ymd', strtotime($tanggal));
        $sql_max = "SELECT COUNT(*) as total FROM transaksi WHERE tanggal = ?";
        $stmt_max = $koneksi->prepare($sql_max);
        $stmt_max->bind_param("s", $tanggal);
        $stmt_max->execute();
        $result_max = $stmt_max->get_result();
        $row_max = $result_max->fetch_assoc();
        $urutan = str_pad($row_max['total'] + 1, 3, '0', STR_PAD_LEFT);
        $no_referensi = $tanggal_format . "_TAR_" . $urutan;

        // Menambahkan transaksi tarik ke tabel transaksi
        $sql = "INSERT INTO transaksi (id_siswa, jumlah, jenis_transaksi, tanggal, waktu, no_referensi) VALUES (?, ?, 'tarik', ?, NOW(), ?)";
        $stmt = $koneksi->prepare($sql);
        $stmt->bind_param("ssss", $id_siswa, $jumlah, $tanggal, $no_referensi);
        
        if ($stmt->execute()) {
            $message = "Penarikan berhasil ditambahkan!";
        } else {
            $message = "Gagal menambahkan penarikan.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Penarikan | Dashboard Admin</title>
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
        .saldo-container {
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 10px;
            text-align: center;
            font-weight: bold;
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
        <h2>Form Penarikan</h2>
        <form action="form_tarik.php" method="post">
            <label for="id_siswa">ID Siswa:</label>
            <select id="id_siswa" name="id_siswa" required>
                <option value="" disabled selected>Pilih Siswa</option>
                <?php while ($row = $result_siswa->fetch_assoc()): ?>
                    <option value="<?php echo htmlspecialchars($row['id_siswa']); ?>" data-saldo="<?php echo $row['saldo']; ?>">
                        <?php echo htmlspecialchars($row['id_siswa']) . ' - ' . htmlspecialchars($row['nama']); ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <label>Saldo:</label>
            <div class="saldo-container" id="saldo">Rp 0.00</div>

            <label for="tanggal">Tanggal:</label>
            <input type="date" id="tanggal" name="tanggal" required>

            <label for="jumlah">Jumlah:</label>
            <input type="text" id="jumlah" name="jumlah" required oninput="formatRupiah(this, 'Rp ')">

            <button type="submit">Tambah Penarikan</button>
            <button type="button" class="btn-secondary" onclick="window.location.href='transaksi.php'">Kembali</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function formatRupiah(angka, prefix) {
            let number_string = angka.value.replace(/[^,\d]/g, '').toString(),
                split = number_string.split(','),
                sisa = split[0].length % 3,
                rupiah = split[0].substr(0, sisa),
                ribuan = split[0].substr(sisa).match(/\d{3}/gi);
            
            if (ribuan) {
                separator = sisa ? '.' : '';
                rupiah += separator + ribuan.join('.');
            }

            rupiah = split[1] !== undefined ? rupiah + ',' + split[1] : rupiah;
            angka.value = prefix === undefined ? rupiah : (rupiah ? 'Rp ' + rupiah : '');
        }

        document.getElementById('id_siswa').addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const saldo = selectedOption.getAttribute('data-saldo');
            document.getElementById('saldo').innerText = 'Rp ' + parseFloat(saldo).toFixed(2);
        });
    </script>

    <?php if (isset($message)): ?>
    <script>
        Swal.fire({
            title: <?php echo strpos($message, 'berhasil') !== false ? '"Berhasil!"' : '"Gagal!"'; ?>,
            text: "<?php echo $message; ?>",
            icon: <?php echo strpos($message, 'berhasil') !== false ? '"success"' : '"error"'; ?>,
            confirmButtonText: 'OK'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'transaksi.php';
            }
        });
    </script>
    <?php endif; ?>
</body>
</html>
