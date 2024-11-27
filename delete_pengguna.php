<?php
include "koneksi.php";


if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

// Periksa apakah ID pengguna ada di URL
if (!isset($_GET['id'])) {
    header('Location: pengguna.php?message=ID%20pengguna%20tidak%20ditemukan');
    exit;
}

$id_user = $_GET['id'];

// Hapus data pengguna
$sql_delete_user = "DELETE FROM user WHERE id_user = ?";
$stmt_user = $koneksi->prepare($sql_delete_user);
$stmt_user->bind_param("i", $id_user);

// Variabel untuk menyimpan pesan
$message = '';
$redirect = 'pengguna.php'; // Redirect setelah proses berhasil atau gagal

if ($stmt_user->execute()) {
    // Jika berhasil, simpan pesan sukses
    $message = "Pengguna berhasil dihapus!";
} else {
    // Jika gagal, simpan pesan error
    $message = "Error: " . $stmt_user->error;
}

// Menutup statement
$stmt_user->close();
$koneksi->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hapus Pengguna</title>
    <link rel="stylesheet" href="">
</head>
<body>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
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
    </script>
</body>
</html>
