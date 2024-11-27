<?php
include "koneksi.php";


if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

if ($_SESSION['user']['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

// Ambil ID kelas dari URL
$id = isset($_GET['id']) ? $_GET['id'] : 0;

// Query untuk menghapus kelas
$sql = "DELETE FROM kelas WHERE no = '$id'";

$message = '';
$redirect = 'kelas.php'; // Redirect URL

if ($koneksi->query($sql) === TRUE) {
    $message = 'Kelas berhasil dihapus';
} else {
    $message = "Error: " . $koneksi->error;
}

// Tutup koneksi
$koneksi->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <title>Delete Class</title>
</head>
<body>

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
