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
$dataJual = getData("SELECT t.*, r.nama AS relasi_nama FROM tbl_transaksi t JOIN tbl_relasi r ON r.id_relasi = t.id_relasi WHERE t.tipe_transaksi = 'JUAL' AND t.tgl_transaksi BETWEEN '$tgl1' AND '$tgl2'");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Penjualan</title>
</head>

<body>
    <div style="text-align: center;">
        <h2 style="margin-bottom: -15px;">
            Rekap Laporan Penjualan
        </h2>
        <h2 style="margin-bottom: 15px;">
            SnackinajaPOS
        </h2>
    </div>
    <table>
        <thead>
            <tr>
                <td colspan="5" style="height: 5px;">
                    <hr style="margin-bottom: 2px; margin-left: -5px;" , size="3" , color="grey">
                </td>
            </tr>
            <tr>
                <th>No</th>
                <th style="120px">Tgl Penjualan</th>
                <th style="120px">ID Penjualan</th>
                <th style="300px">Customer</th>
                <th>Total Penjualan</th>
            </tr>
            <tr>
                <td colspan="5" style="height: 5px;">
                    <hr style="margin-bottom: 2px; margin-left: -5px; margin-top: 1px;" , size="3" , color="grey">
                </td>
            </tr>
        </thead>
        <tbody>
            <?php
            $no = 1;
            foreach ($dataJual as $jual) { ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= date('d-m-Y', strtotime($jual['tgl_transaksi'])) ?></td>
                    <td><?= $jual['no_transaksi'] ?></td>
                    <td><?= $jual['relasi_nama'] ?></td>
                    <td>Rp. <?= number_format($jual['total'], 0, ',', '.') ?></td>
                </tr>
            <?php } ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="5" style="height: 5px;">
                    <hr style="margin-bottom: 2px; margin-left: -5px; margin-top: 1px;" , size="3" , color="grey">
                </td>
            </tr>
        </tfoot>
    </table>
</body>
<script>
    window.print();
</script>
</html>