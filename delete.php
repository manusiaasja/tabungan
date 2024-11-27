<?php
include "koneksi.php";

session_start(); // Pastikan session dimulai

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

if (!isset($_GET['id'])) {
    header('Location: siswa.php');
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
    header('Location: siswa.php');
    exit;
}

// Cek apakah siswa memiliki transaksi tabungan
$sql_transaksi = "SELECT * FROM transaksi WHERE id_siswa = ?";
$stmt_transaksi = $koneksi->prepare($sql_transaksi);
$stmt_transaksi->bind_param("s", $id_siswa);
$stmt_transaksi->execute();
$result_transaksi = $stmt_transaksi->get_result();

// Jika siswa memiliki transaksi tabungan
if ($result_transaksi->num_rows > 0) {
    echo "<script>alert('Data siswa tidak dapat dihapus karena masih memiliki tabungan.');</script>";
    echo "<script>window.location.href = 'siswa.php';</script>";
    exit;
}

// Jika siswa tidak memiliki transaksi, hapus siswa
$sql_hapus_siswa = "DELETE FROM siswa WHERE id_siswa = ?";
$stmt_hapus_siswa = $koneksi->prepare($sql_hapus_siswa);
$stmt_hapus_siswa->bind_param("s", $id_siswa);
$stmt_hapus_siswa->execute();

// Redirect ke halaman siswa.php setelah penghapusan berhasil
if ($stmt_hapus_siswa->affected_rows > 0) {
    echo "<script>alert('Data siswa berhasil dihapus.');</script>";
    echo "<script>window.location.href = 'siswa.php';</script>";
} else {
    echo "<script>alert('Gagal menghapus data siswa. Silakan coba lagi.');</script>";
    echo "<script>window.location.href = 'siswa.php';</script>";
}
?>
