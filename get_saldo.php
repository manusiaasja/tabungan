<?php
include "koneksi.php";

if (isset($_GET['id'])) {
    $id_siswa = $_GET['id'];

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

    echo json_encode(['saldo' => $row_saldo['saldo']]);
}
