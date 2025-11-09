<?php

session_start();

if (!isset($_SESSION["ssLoginPOS"])) {
    header("location: ../auth/login.php");
    exit();
}

require "../config/config.php";
require "../config/functions.php";
require "../module/mode-customer.php";

$id = $_GET['id'];
$sql = "DELETE FROM tbl_relasi WHERE id_relasi = $id AND tipe = 'CUSTOMER'";
if (mysqli_query($koneksi, $sql)) {
    echo "<script>document.location.href = 'data-customer.php?msg=deleted';</script>";
} else {
    echo "<script>document.location.href = 'data-customer.php?msg=aborted';</script>";
}