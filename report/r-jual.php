<?php
session_start();
if (!isset($_SESSION["ssLoginPOS"])) {
    header("location: ../auth/login.php");
    exit();
}

require "../config/config.php";
require "../config/functions.php";

$tgl1 = $_GET['tgl1'];
$tgl2 = $_GET['tgl2'];

$dataJual = getData("
    SELECT p.*, c.nama
    FROM tbl_penjualan p
    LEFT JOIN tbl_customer c ON p.id_customer = c.id_customer
    WHERE p.tgl_jual BETWEEN '$tgl1' AND '$tgl2'
    ORDER BY p.tgl_jual ASC
");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Laporan Penjualan</title>
</head>

<body>
    <div style="text-align:center;">
        <h2>Rekap Laporan Penjualan</h2>
        <h3>Snackinaja POS</h3>
    </div>

    <table width="100%">
        <thead>
            <tr><td colspan="5"><hr></td></tr>
            <tr>
                <th>No</th>
                <th>Tgl Penjualan</th>
                <th>No Transaksi</th>
                <th>Customer</th>
                <th>Total</th>
            </tr>
            <tr><td colspan="5"><hr></td></tr>
        </thead>

        <tbody>
        <?php $no = 1; foreach ($dataJual as $d) { ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= date('d-m-Y', strtotime($d['tgl_jual'])) ?></td>
                <td><?= $d['no_jual'] ?></td>
                <td><?= $d['nama'] ?></td>
                <td>Rp <?= number_format($d['total'], 0, ',', '.') ?></td>
            </tr>
        <?php } ?>
        </tbody>

        <tfoot>
            <tr><td colspan="5"><hr></td></tr>
        </tfoot>
    </table>

    <script> window.print(); </script>
</body>
</html>
