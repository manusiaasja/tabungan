<?php
include "koneksi.php";

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

if (!isset($_GET['id'])) {
    header('Location: transaksi.php');
    exit;
}

$id_siswa = $_GET['id'];

// Ambil data siswa
$sql_siswa = "SELECT * FROM siswa WHERE id_siswa = ?";
$stmt = $koneksi->prepare($sql_siswa);
$stmt->bind_param("s", $id_siswa);
$stmt->execute();
$result_siswa = $stmt->get_result();
$siswa = $result_siswa->fetch_assoc();

// Cek jika data siswa ditemukan
if (!$siswa) {
    header('Location: transaksi.php');
    exit;
}

// Ambil riwayat transaksi
$sql_transaksi = "SELECT * FROM transaksi WHERE id_siswa = ? ORDER BY tanggal ASC";
$stmt_transaksi = $koneksi->prepare($sql_transaksi);
$stmt_transaksi->bind_param("s", $id_siswa);
$stmt_transaksi->execute();
$result_transaksi = $stmt_transaksi->get_result();

// Hitung saldo, total setoran, dan total penarikan
$saldo = 0;
$total_setoran = 0;
$total_penarikan = 0;

if ($result_transaksi->num_rows > 0) {
    while ($transaksi = $result_transaksi->fetch_assoc()) {
        if ($transaksi['jenis_transaksi'] == 'setor') {
            $total_setoran += $transaksi['jumlah'];
            $saldo += $transaksi['jumlah'];
        } elseif ($transaksi['jenis_transaksi'] == 'tarik') {
            $total_penarikan += $transaksi['jumlah'];
            $saldo -= $transaksi['jumlah'];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Tabungan Siswa</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print {
                display: none; /* Hide buttons during printing */
            }
            body {
                margin: 0;
                padding: 20px;
                font-family: Arial, sans-serif;
                background: none; /* Remove background for print */
            }
            h1, h2 {
                page-break-after: avoid;
            }
            table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 20px;
            }
            th, td {
                border: 1px solid #000;
                padding: 8px;
                text-align: left;
            }
            th {
                background-color: #f0f0f0;
            }
            /* Ensure the total balance is well formatted */
            .total {
                font-weight: bold;
                font-size: 1.2em;
            }
        }
        .table-responsive {
            overflow-x: auto;
        }
    </style>
</head>
<body class="bg-gray-100 p-4 md:p-8">
    <div class="bg-white p-6 rounded-lg shadow-lg">
        <h1 class="text-2xl font-bold mb-4">Detail Tabungan Siswa</h1>
        <div class="flex flex-col md:flex-row md:justify-between mb-6 no-print">
            <div class="flex flex-col md:flex-row md:space-x-4">
                <a href="transaksi.php" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 transition mb-2 md:mb-0 flex items-center justify-center">Kembali</a>
                <button onclick="window.print()" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition">Cetak</button>
            </div>
        </div>
        <div class="mb-4">
            <div class="flex flex-col md:flex-row md:space-x-4 mb-2">
                <span class="font-semibold">Nama</span>
                <span>: <?php echo htmlspecialchars($siswa['nama']); ?></span>
            </div>
            <div class="flex flex-col md:flex-row md:space-x-4 mb-2">
                <span class="font-semibold">Kelas</span>
                <span>: <?php echo htmlspecialchars($siswa['kelas']); ?></span>
            </div>
            <div class="flex flex-col md:flex-row md:space-x-4 mb-2">
                <span class="font-semibold">Alamat</span>
                <span>: <?php echo htmlspecialchars($siswa['alamat']); ?></span>
            </div>
        </div>

        <div class="mt-4 bg-gray-100 p-4 rounded">
            <h2 class="font-semibold">Total Transaksi</h2>
            <p>Total Setoran: Rp. <?php echo number_format($total_setoran, 0, ',', '.'); ?></p>
            <p>Total Penarikan: Rp. <?php echo number_format($total_penarikan, 0, ',', '.'); ?></p>
            <p class="total">Saldo Akhir: Rp. <?php echo number_format($saldo, 0, ',', '.'); ?></p>
        </div>

        <div class="table-responsive mt-4">
            <table class="min-w-full bg-white border border-gray-200">
                <thead>
                    <tr>
                        <th class="py-2 px-4 border-b">No</th>
                        <th class="py-2 px-4 border-b">No Referensi</th>
                        <th class="py-2 px-4 border-b">Tanggal</th>
                        <th class="py-2 px-4 border-b">Waktu</th>
                        <th class="py-2 px-4 border-b">Setoran</th>
                        <th class="py-2 px-4 border-b">Penarikan</th>
                        <th class="py-2 px-4 border-b">Saldo</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 1;
                    $result_transaksi->data_seek(0);
                    while ($transaksi = $result_transaksi->fetch_assoc()) {
                        $setoran = ($transaksi['jenis_transaksi'] == 'setor') ? $transaksi['jumlah'] : 0;
                        $penarikan = ($transaksi['jenis_transaksi'] == 'tarik') ? $transaksi['jumlah'] : 0;

                        $saldo_format = number_format($saldo, 0, ',', '.');
                        $tanggal = date('d-m-Y', strtotime($transaksi['tanggal']));
                        $waktu = date('H:i:s', strtotime($transaksi['waktu']));
                        $no_referensi = htmlspecialchars($transaksi['no_referensi']);
                    ?>
                    <tr>
                        <td class="py-2 px-4 border-b"><?php echo $no++; ?></td>
                        <td class="py-2 px-4 border-b"><?php echo $no_referensi; ?></td>
                        <td class="py-2 px-4 border-b"><?php echo $tanggal; ?></td>
                        <td class="py-2 px-4 border-b"><?php echo $waktu; ?></td>
                        <td class="py-2 px-4 border-b"><?php echo number_format($setoran, 0, ',', '.'); ?></td>
                        <td class="py-2 px-4 border-b"><?php echo number_format($penarikan, 0, ',', '.'); ?></td>
                        <td class="py-2 px-4 border-b"><?php echo 'Rp. ' . $saldo_format; ?></td>
                    </tr>
                    <?php 
                        } 
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
