<?php

session_start();

if (!isset($_SESSION["ssLoginPOS"])) {
    header("location: ../auth/login.php");
    exit();
}

require "../config/config.php";
require "../config/functions.php";
require "../module/mode-supplier.php";

$id = $_GET['id'];

// Pastikan hanya hapus SUPPLIER
$sql = "DELETE FROM tbl_relasi WHERE id_relasi = $id AND tipe = 'SUPPLIER'";
if (mysqli_query($koneksi, $sql)) {
    echo "<script>document.location.href = 'data-supplier.php?msg=deleted';</script>";
} else {
    echo "<script>document.location.href = 'data-supplier.php?msg=aborted';</script>";
}